<?php

session_start();

if(!isset($_SESSION["email"])){
    header("Location:../auth/logout.php");
    exit();
}
if(($_SESSION["role"] ?? "") === "admin"){
    header("Location:admin.php");
    exit();
}

require_once __DIR__."/../vendor/autoload.php";
require_once __DIR__."/../includes/upload_photo.php";

use app\Models\Application;
use app\Models\Authentication;

function e($value){
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

$user = (new Authentication())->findByEmail($_SESSION["email"]);
if(!$user){
    header("Location:../auth/logout.php");
    exit();
}

$appModel = new Application();
try{
    $adoptions = $appModel->getAdoptionsByUser((int)$user["userid"]);
    $fosters = $appModel->getFostersByUser((int)$user["userid"]);
}catch(PDOException $ex){
    $adoptions = [];
    $fosters = [];
}

$applications = [];
foreach($adoptions as $application){
    $applications[] = [
        "kind" => "Adoption",
        "id" => (int)$application["adoptionid"],
        "pet_name" => $application["pet_name"],
        "breed" => $application["breed"],
        "photo_url" => $application["photo_url"],
        "application_date" => $application["application_date"],
        "status" => $application["status"] ?: "Pending",
        "detail" => $application["reason"] ?: "Adoption application"
    ];
}
foreach($fosters as $application){
    $applications[] = [
        "kind" => "Foster",
        "id" => (int)$application["fosterid"],
        "pet_name" => $application["pet_name"],
        "breed" => $application["breed"],
        "photo_url" => $application["photo_url"],
        "application_date" => $application["application_date"],
        "status" => $application["status"] ?: "Pending",
        "detail" => "Foster period: ".($application["planned_start_date"] ?: "?")." to ".($application["planned_end_date"] ?: "?")
    ];
}

usort($applications, fn($a, $b) => strcmp($b["application_date"] ?: "", $a["application_date"] ?: ""));

$type = $_GET["type"] ?? "all";
if(!in_array($type, ["all", "adoption", "foster"], true)){ $type = "all"; }
$shown = array_values(array_filter($applications, fn($application) => $type === "all" || strcasecmp($application["kind"], $type) === 0));

function statusClass($status){
    $status = strtolower((string)$status);
    if($status === "approved") return "st-approved";
    if($status === "rejected") return "st-rejected";
    if($status === "under review") return "st-review";
    return "st-submitted";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My applications · Hearth</title>
    <link rel="stylesheet" href="../assets/css/pet.css">
</head>
<body>
<?php require __DIR__."/../includes/navbar.php"; ?>
<main class="page myapps-page">
    <div class="page-head"><h1>My applications</h1></div>

    <div class="apps-toolbar">
        <div class="apps-tabs">
            <a class="apps-tab <?php echo $type === "all" ? "active" : ""; ?>" href="myapplications.php">All applications</a>
            <a class="apps-tab <?php echo $type === "adoption" ? "active" : ""; ?>" href="myapplications.php?type=adoption">Adoptions</a>
            <a class="apps-tab <?php echo $type === "foster" ? "active" : ""; ?>" href="myapplications.php?type=foster">Fosters</a>
        </div>
    </div>

    <?php if(empty($shown)){ ?>
        <div class="app-success"><h1>No applications yet</h1><p>Find a pet and submit an adoption or foster application.</p><a class="btn-back" href="dashboard.php">Find a pet</a></div>
    <?php }else{ foreach($shown as $application){
        $src = photo_src($application["photo_url"]);
        $status = $application["status"];
        $normalizedStatus = strtolower($status);
        $rejected = $normalizedStatus === "rejected";
        $approved = $normalizedStatus === "approved";
        $underReview = in_array($normalizedStatus, ["under review", "approved", "rejected"], true);
    ?>
        <article class="app-card">
            <div class="app-photo"><?php if($src){ ?><img src="<?php echo e($src); ?>" alt="<?php echo e($application["pet_name"]); ?>"><?php }else{ ?><span class="app-photo-empty">No photo</span><?php } ?></div>
            <div class="app-body">
                <div class="app-top">
                    <h3><?php echo e($application["pet_name"]); ?></h3>
                    <span class="app-type"><?php echo e($application["kind"]); ?></span>
                    <span class="app-status <?php echo statusClass($status); ?>"><?php echo e($status); ?></span>
                </div>
                <p class="app-sub"><?php echo e($application["breed"]); ?> · Submitted <?php echo $application["application_date"] ? e(date("M j, Y", strtotime($application["application_date"]))) : ""; ?></p>

                <div class="app-stages">
                    <span class="stage done"><i>1</i> Submitted</span>
                    <span class="stage-line <?php echo $underReview ? "done" : ""; ?>"></span>
                    <span class="stage <?php echo $underReview ? "done" : ""; ?>"><i>2</i> Under review</span>
                    <span class="stage-line <?php echo $approved || $rejected ? "done" : ""; ?>"></span>
                    <span class="stage <?php echo $approved ? "done" : ($rejected ? "rejected" : ""); ?>"><i>3</i> <?php echo $rejected ? "Rejected" : ($approved ? "Approved" : "Decision"); ?></span>
                </div>

                <div class="app-note"><p><?php echo e($application["detail"]); ?></p></div>
            </div>
        </article>
    <?php }} ?>
</main>
<?php require __DIR__."/../includes/footer.php"; ?>
</body>
</html>
