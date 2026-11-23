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
$pet = $petid > 0 ? (new Pet())->ViewbyID($petid) : false;
if(!$pet){
    header("Location:dashboard.php");
    exit();
}
$petStatus = strtolower(trim((string)$pet["status"]));
if(!in_array($petStatus, ["available", "foster"], true)){
    header("Location:viewbyid.php?id=".$petid);
    exit();
}

$error = "";
$submitted = false;
$appointmentInput = $_POST["appointment_datetime"] ?? "";
if(($_POST["action"] ?? "") === "submit_foster"){
    $startDate = $_POST["start_date"] ?? "";
    $endDate = $_POST["end_date"] ?? "";
    $appointment = DateTime::createFromFormat("!Y-m-d\TH:i", $appointmentInput);
    $dateErrors = DateTime::getLastErrors();
    $auth = new Authentication();
    $user = $auth->findByEmail($_SESSION["email"]);

    if($startDate === "" || $endDate === ""){
        $error = "Please choose both a start and end date.";
    }elseif($endDate < $startDate){
        $error = "The end date must be after the start date.";
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
            (new Application())->insertFoster(
                $petid,
                (int)$user["userid"],
                $startDate,
                $endDate,
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
    <title>Foster application · Hearth</title>
    <link rel="stylesheet" href="../assets/css/pet.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<?php require __DIR__."/../includes/navbar.php"; ?>
<main class="application-page">
    <?php if($submitted){ ?>
        <section class="app-success">
            <h1>Foster application submitted</h1>
            <p>Your foster request for <?php echo e($pet["name"]); ?> is pending review.</p>
            <p>Visit appointment: <?php echo e($appointment->format("M j, Y \\a\\t g:i A")); ?></p>
            <a class="btn-back" href="dashboard.php">Back to pets</a>
        </section>
    <?php }else{ ?>
    <a class="application-back" href="viewbyid.php?id=<?php echo (int)$petid; ?>">Back to <?php echo e($pet["name"]); ?>'s profile</a>
        <header class="application-page-head">
            <h1>Open your home for a little while</h1>
            <p>Foster families give pets a calm place to decompress, heal, or wait for adoption.</p>
        </header>

        <?php if($error !== ""){ ?><p class="notice" role="alert"><?php echo e($error); ?></p><?php } ?>

        <div class="application-layout">
            <form class="application-form" method="POST">
                <input type="hidden" name="action" value="submit_foster">
                <input type="hidden" name="petid" value="<?php echo (int)$petid; ?>">
                <h2>Foster plan</h2>

                <fieldset class="application-choice application-choice-grid"><legend>Type of foster care</legend><label><input type="radio" name="care_type" value="Short-term" checked> Short-term</label><label><input type="radio" name="care_type" value="Medical"> Medical recovery</label><label><input type="radio" name="care_type" value="Uncertain"> Until adapted</label></fieldset>

                <fieldset class="application-choice application-choice-grid"><legend>Animals in your home</legend><label><input type="checkbox" name="animals[]" value="Dogs" checked> Dogs</label><label><input type="checkbox" name="animals[]" value="Cats"> Cats</label><label><input type="checkbox" name="animals[]" value="Small pets"> Small pets</label><label><input type="checkbox" name="animals[]" value="None"> No other pets</label></fieldset>

                <label>Appointment date and time<input type="datetime-local" name="appointment_datetime" min="<?php echo e(date("Y-m-d\\TH:i")); ?>" value="<?php echo e($appointmentInput); ?>" required></label>

                <div class="application-grid">
                    <label>Foster start date<input type="date" name="start_date" required></label>
                    <label>Foster end date<input type="date" name="end_date" required></label>
                </div>

                <div class="application-grid">
                    <label>Type of home<select name="home_type"><option>Apartment</option><option>House</option><option>Townhouse</option><option>Condo</option></select></label>
                    <label>Pets allowed at home<select name="pets_allowed"><option>Yes, with notice</option><option>Yes</option><option>No</option></select></label>
                </div>
                
                <label class="application-reason-field">Why do you want to foster <?php echo e($pet["name"]); ?>?<textarea name="reason" rows="4"></textarea></label>

                <div class="application-form-actions"><button type="submit" class="btn-dark">Submit foster application</button></div>
            </form>

            <aside class="application-aside">
                <div class="application-pet-card">
                    <?php if($src){ ?><img src="<?php echo e($src); ?>" alt="<?php echo e($pet["name"]); ?>"><?php } ?>
                    <div>
                        <strong><?php echo e($pet["name"]); ?></strong>
                        <span><?php echo e($pet["breed"]); ?> · <?php echo (int)$pet["age"]; ?> <?php echo (int)$pet["age"] === 1 ? "year old" : "years old"; ?> · <?php echo e($pet["sex"]); ?></span>
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
