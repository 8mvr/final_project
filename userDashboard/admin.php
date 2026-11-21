<?php

session_start();

if(!isset($_SESSION["email"])){
    header("Location:../auth/logout.php");
    exit();
}
if(($_SESSION["role"] ?? "") !== "admin"){
    header("Location:dashboard.php");
    exit();
}

require_once __DIR__."/../vendor/autoload.php";
require_once __DIR__."/../includes/upload_photo.php";
use app\Models\Authentication;
use app\Models\Pet;

function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, "UTF-8"); }

// Only the dashboard and pet management page are available in the admin panel.
$page = "home";

$pets = [];
$error = "";
try{
    $pets = (new Pet())->getAll();
}
catch(PDOException $ex){
    $error = $ex->getMessage();
}

// Who is signed in
$fullName = $_SESSION["email"];
try{
    $signedInUser = (new Authentication())->findByEmail($_SESSION["email"]);
    if($signedInUser){ $fullName = $signedInUser["full_name"] ?: $_SESSION["email"]; }
}catch(PDOException $ex){ }
$firstName = explode(" ", trim($fullName))[0];
$hour = (int)date("G");
$greeting = $hour < 12 ? "Good morning" : ($hour < 18 ? "Good afternoon" : "Good evening");

// Numbers for the cards
$total = count($pets);
$count = ["available" => 0, "foster" => 0, "not available" => 0];
foreach($pets as $p){
    $s = strtolower(trim($p["status"]));
    if(isset($count[$s])){ $count[$s]++; }
}
usort($pets, fn($a, $b) => $b["petid"] <=> $a["petid"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff dashboard · Hearth</title>
    <link rel="stylesheet" href="../assets/css/pet.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="admin-layout">
<?php require __DIR__."/../includes/admin_navbar.php"; ?>
<main class="main">
        <!-- ================= HOME / DASHBOARD ================= -->
        <header class="top">
            <div>
                <h1><?php echo $greeting ?>, <?php echo e($firstName) ?></h1>
            </div>
        </header>

        <?php if($error !== ""){ ?><p class="notice"><?php echo e($error) ?></p><?php } ?>
        <?php if(isset($_GET["deleted"])){ ?><p class="okmsg">Pet deleted successfully.</p><?php } ?>
        <?php if(isset($_GET["added"])){ ?><p class="okmsg">Pet added successfully.</p><?php } ?>
        <?php if(isset($_GET["updated"])){ ?><p class="okmsg">Pet updated successfully.</p><?php } ?>
        <?php if(isset($_GET["err"]) && $_GET["err"] !== "missing"){ ?><p class="notice"><?php echo e($_GET["err"]) ?></p><?php } ?>
        <?php if(isset($_GET["err"]) && $_GET["err"] === "missing"){ ?><p class="notice">Pet name and species are required.</p><?php } ?>

        <section class="stats">
            <div class="stat"><span>Available pets</span><b><?php echo $count["available"] ?></b><small>of <?php echo $total ?> total pets</small></div>
            <div class="stat"><span>In foster care</span><b><?php echo $count["foster"] ?></b></div>
            <div class="stat"><span>Not available</span><b><?php echo $count["not available"] ?></b></div>
        </section>

        <!-- ---------- Manage all pets (Add / Edit / Delete) ---------- -->
        <section class="card" id="manage">
            <div class="card-head">
                <h2>Manage all pets</h2>
                <a class="btn-dark btn-sm" href="addpet.php">Add pet</a>
            </div>

            <div class="table-wrap">
                <table>
                    <tr>
                        <th>Photo</th>
                        <th>Pet</th>
                        <th>Species</th>
                        <th>Breed</th>
                        <th>Age</th>
                        <th>Sex</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    <?php if(empty($pets)){ ?>
                        <tr><td colspan="8" class="empty">No pets yet.</td></tr>
                    <?php }else{ foreach($pets as $p){ $src = photo_src($p["photo_url"]); ?>
                        <tr>
                            <td><?php if($src){ ?><img class="thumb" src="<?php echo e($src) ?>" alt=""><?php }else{ ?><span class="thumb"></span><?php } ?></td>
                            <td><strong><?php echo e($p["name"]) ?></strong></td>
                            <td><?php echo e($p["species"]) ?></td>
                            <td><?php echo e($p["breed"]) ?></td>
                            <td><?php echo (int)$p["age"] ?> <?php echo (int)$p["age"] === 1 ? "yr" : "yrs" ?></td>
                            <td><?php echo e($p["sex"]) ?></td>
                            <td><span class="badge <?php echo e(str_replace(" ", "-", strtolower($p["status"]))) ?>"><?php echo e($p["status"]) ?></span></td>
                            <td>
                                <a class="btn-light" href="viewbyid.php?id=<?php echo (int)$p["petid"] ?>">Edit</a>
                                <a class="btn-light danger" href="delete.php?id=<?php echo (int)$p["petid"] ?>" onclick="return confirm('Delete <?php echo e($p["name"]) ?>? This cannot be undone.')">Delete</a>
                            </td>
                        </tr>
                    <?php }} ?>
                </table>
            </div>
        </section>
</main>
</div>
</body>
</html>