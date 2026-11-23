<?php

session_start();

if(!isset($_SESSION["email"])){
    header("Location:../auth/logout.php");
    exit();
}

require_once __DIR__."/../vendor/autoload.php";
require_once __DIR__."/../includes/upload_photo.php";

use app\Models\Application;
use app\Models\Authentication;
use app\Models\Pet;

function e($value){
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

$petid = (int)($_GET["petid"] ?? $_POST["petid"] ?? 0);
$petModel = new Pet();
$pet = $petid > 0 ? $petModel->ViewbyID($petid) : false;
if(!$pet){
    header("Location:dashboard.php");
    exit();
}
if(strtolower(trim((string)$pet["status"])) !== "available"){
    header("Location:viewbyid.php?id=".$petid);
    exit();
}

$error = "";
$submitted = false;
$appointmentInput = $_POST["appointment_datetime"] ?? "";
if(($_POST["action"] ?? "") === "submit_adoption"){
    $reason = trim($_POST["reason"] ?? "");
    $appointment = DateTime::createFromFormat("!Y-m-d\TH:i", $appointmentInput);
    $dateErrors = DateTime::getLastErrors();
    $auth = new Authentication();
    $user = $auth->findByEmail($_SESSION["email"]);

    if($reason === ""){
        $error = "Please tell us why Maple would be a good fit for your home.";
    }elseif(
        !$appointment
        || ($dateErrors !== false && ($dateErrors["warning_count"] > 0 || $dateErrors["error_count"] > 0))
        || $appointment->format("Y-m-d\TH:i") !== $appointmentInput
    ){
        $error = "Please choose a valid appointment date and time.";
    }elseif($appointment <= new DateTime()){
        $error = "The appointment date and time must be in the future.";
    }elseif(!$user){
        $error = "Your account could not be found.";
    }else{
        try{
            (new Application())->insertAdoption(
                $petid,
                (int)$user["userid"],
                $reason,
                $appointment->format("Y-m-d H:i:s")
            );
            $submitted = true;
        }catch(PDOException $ex){
            $error = $ex->getMessage();
        }
    }
}

$src = photo_src($pet["photo_url"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adoption application · Hearth</title>
    <link rel="stylesheet" href="../assets/css/pet.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<?php require __DIR__."/../includes/navbar.php"; ?>
<main class="application-page">
    <?php if($submitted){ ?>
        <section class="app-success">
            <h1>Application submitted</h1>
            <p>Your adoption application for <?php echo e($pet["name"]); ?> is pending review.</p>
            <p>Visit appointment: <?php echo e($appointment->format("M j, Y \\a\\t g:i A")); ?></p>
            <a class="btn-back" href="dashboard.php">Back to pets</a>
        </section>
    <?php }else{ ?>
        <a class="application-back" href="viewbyid.php?id=<?php echo (int)$petid; ?>">Back to <?php echo e($pet["name"]); ?>'s profile</a>
        <header class="application-page-head">
            <h1>Tell us about your home</h1>
            <p>A few honest answers help us find the right match for you and <?php echo e($pet["name"]); ?>.</p>
        </header>
        
        <?php if($error !== ""){ ?><p class="notice" role="alert"><?php echo e($error); ?></p><?php } ?>

        <div class="application-layout">
            <form class="application-form" method="POST">
                <input type="hidden" name="action" value="submit_adoption">
                <input type="hidden" name="petid" value="<?php echo (int)$petid; ?>">
                <h2>Your home</h2>

                <div class="application-grid">
                    <label>Type of home<select name="home_type"><option>House</option><option>Apartment</option><option>Condo</option><option>Townhouse</option></select></label>
                    <label>Do you own or rent?<select name="ownership"><option>Own</option><option>Rent</option></select></label>
                </div>

                <fieldset class="application-choice application-choice-grid"><legend>Do you have a yard or pets?</legend><label><input type="radio" name="yard" value="Yes"> Yes</label><label><input type="radio" name="yard" value="No" checked> No</label><label><input type="radio" name="yard" value="Unsure"> Not sure</label></fieldset>

                <div class="application-grid">
                    <label>People in household<input type="number" name="household" min="1" value="1"></label>
                    <label>Other pets in home<select name="other_pets"><option>None</option><option>Dog(s)</option><option>Cat(s)</option><option>Dogs and cats</option></select></label>
                </div>

                <fieldset class="application-choice application-choice-grid"><legend>Is your home fenced?</legend><label><input type="radio" name="fenced" value="Yes"> Yes</label><label><input type="radio" name="fenced" value="No" checked> No</label></fieldset>

                <label>Appointment date and time<input type="datetime-local" name="appointment_datetime" min="<?php echo e(date("Y-m-d\\TH:i")); ?>" value="<?php echo e($appointmentInput); ?>" required></label>

                <label class="application-reason-field">Why would <?php echo e($pet["name"]); ?> be a good match?<textarea name="reason" rows="5" required><?php echo e($_POST["reason"] ?? ""); ?></textarea></label>

                <div class="application-form-actions"><button type="submit" class="btn-dark">Submit application</button></div>
            </form>

            <aside class="application-aside">
                <div class="application-pet-card">
                    <?php if($src){ ?>
                        <img src="<?php echo e($src); ?>" alt="<?php echo e($pet["name"]); ?>">
                    <?php } ?>
                    <div>
                        <strong><?php echo e($pet["name"]); ?></strong>
                        <span>
                            <?php echo e($pet["breed"]); ?> ·
                            <?php echo (int)$pet["age"]; ?>
                            <?php echo (int)$pet["age"] === 1 ? "year old" : "years old"; ?> ·
                            <?php echo e($pet["sex"]); ?>
                        </span>
                        <small><?php echo e($pet["status"]); ?></small>
                    </div>
                </div>
            </aside>
        </div>
    <?php } ?>
</main>
<?php require __DIR__."/../includes/footer.php"; ?>
</body>
</html>
