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

use app\Models\Placement;
use app\Models\Authentication;
use app\Models\Application;

function e($value){
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

$placementModel = new Placement();
$authModel = new Authentication();
$appModel = new Application();
$message = isset($_GET["recorded"]) ? "Placement and appointment recorded successfully." : "";
$error = "";
$selectedApplicationKind = $_POST["application_kind"] ?? $_GET["application_kind"] ?? "";
$selectedApplicationId = (int)($_POST["application_id"] ?? $_GET["application_id"] ?? 0);
$selectedApplication = null;
$appointmentInput = $_POST["appointment_datetime"] ?? "";

if($selectedApplicationId > 0){
    try{
        $selectedApplication = $appModel->placementApplicationDetails($selectedApplicationKind, $selectedApplicationId);
        if(!$selectedApplication){
            $error = "Application not found or no longer available for placement.";
        }
    }catch(PDOException $ex){
        $error = $ex->getMessage();
    }
}
if($_SERVER["REQUEST_METHOD"] !== "POST" && $selectedApplication && !empty($selectedApplication["appointment_datetime"])){
    $appointmentTimestamp = strtotime($selectedApplication["appointment_datetime"]);
    if($appointmentTimestamp !== false && $appointmentTimestamp > time()){
        $appointmentInput = date("Y-m-d\\TH:i", $appointmentTimestamp);
    }
}
$showPlacementForm = $selectedApplication !== null || ($_POST["action"] ?? "") === "create";

if(($_POST["action"] ?? "") === "create"){
    $userid = (int)($_POST["userid"] ?? 0);
    $petid = (int)($_POST["petid"] ?? 0);
    $placementtype = $_POST["placementtype"] ?? "Adoption";
    $startDate = $_POST["start_date"] ?? "";
    $endDate = $_POST["end_date"] ?? "";
    $status = $_POST["status"] ?? "Active";
    $appointment = DateTime::createFromFormat("!Y-m-d\TH:i", $appointmentInput);
    $dateErrors = DateTime::getLastErrors();

    $admin = $authModel->findByEmail($_SESSION["email"]);
    $recordedBy = (int)($admin["userid"] ?? 0);

    if($userid <= 0 || $petid <= 0){
        $error = "Select both a person and a pet.";
    }elseif(
        !$appointment
        || ($dateErrors !== false && ($dateErrors["warning_count"] > 0 || $dateErrors["error_count"] > 0))
        || $appointment->format("Y-m-d\TH:i") !== $appointmentInput
    ){
        $error = "Please choose a valid appointment date and time.";
    }elseif($appointment <= new DateTime()){
        $error = "The appointment date and time must be in the future.";
    }elseif($selectedApplicationId > 0 && (
        !$selectedApplication
        || $userid !== (int)$selectedApplication["userid"]
        || $petid !== (int)$selectedApplication["petid"]
        || strcasecmp($placementtype, $selectedApplication["placementtype"]) !== 0
    )){
        $error = "The placement details must match the approved application.";
    }else{
        try{
            $adoptionId = $selectedApplicationKind === "adoption" ? $selectedApplicationId : 0;
            $fosterId = $selectedApplicationKind === "foster" ? $selectedApplicationId : 0;
            $placementModel->insert(
                $userid,
                $petid,
                $adoptionId,
                $fosterId,
                $placementtype,
                $startDate,
                $endDate,
                $status,
                $recordedBy,
                $appointment->format("Y-m-d H:i:s")
            );
            if($selectedApplicationId > 0){
                header("Location:placements.php?recorded=1");
                exit();
            }
            $message = "Placement and appointment recorded successfully.";
        }catch(PDOException | RuntimeException $ex){
            $error = $ex->getMessage();
        }
    }
}

if(($_POST["action"] ?? "") === "delete"){
    try{
        $placementModel->delete((int)$_POST["placementid"]);
        $message = "Placement deleted successfully.";
    }catch(PDOException $ex){
        $error = $ex->getMessage();
    }
}

try{
    $placements = $placementModel->getAll();
    $options = $placementModel->options();
}catch(PDOException $ex){
    $placements = [];
    $options = ["users" => [], "pets" => []];
    $error = $ex->getMessage();
}

$filter = $_GET["type"] ?? "all";
if(!in_array($filter, ["all", "Adoption", "Foster"], true)){ $filter = "all"; }

$filtered = array_values(array_filter($placements, function($placement) use ($filter){
    return $filter === "all" || strcasecmp((string)$placement["placementtype"], $filter) === 0;
}));

$totalPlacements = count($placements);
$adoptions = count(array_filter($placements, fn($p) => strcasecmp((string)$p["placementtype"], "Adoption") === 0));
$active = count(array_filter($placements, fn($p) => strcasecmp((string)$p["status"], "Active") === 0));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Placement records · Hearth</title>
    <link rel="stylesheet" href="../assets/css/pet.css">
    <link rel="stylesheet" href="../assets/css/admin.css?v=3">
</head>
<body>
<div class="admin-layout">
    <?php require __DIR__."/../includes/admin_navbar.php"; ?>
    <main class="main placements-main">
        <header class="top placements-top">
            <div>
                <h1>Placement records</h1>
            </div>
        </header>

        <?php if($message !== ""){ ?><p class="okmsg"><?php echo e($message); ?></p><?php } ?>
        <?php if($error !== ""){ ?><p class="notice"><?php echo e($error); ?></p><?php } ?>

        <form class="placement-form" id="placementForm" method="POST" <?php echo $showPlacementForm ? "" : "hidden"; ?>>
            <input type="hidden" name="action" value="create">
            <input type="hidden" name="application_kind" value="<?php echo e($selectedApplicationKind); ?>">
            <input type="hidden" name="application_id" value="<?php echo $selectedApplicationId; ?>">
            <div class="placement-form-grid">
                <label>Person<select name="userid" required><option value="">Select user</option><?php foreach($options["users"] as $user){ ?><option value="<?php echo (int)$user["userid"]; ?>" <?php echo (int)($selectedApplication["userid"] ?? $_POST["userid"] ?? 0) === (int)$user["userid"] ? "selected" : ""; ?>><?php echo e($user["full_name"]); ?></option><?php } ?></select></label>
                <label>Pet<select name="petid" required><option value="">Select pet</option><?php foreach($options["pets"] as $pet){ ?><option value="<?php echo (int)$pet["petid"]; ?>" <?php echo (int)($selectedApplication["petid"] ?? $_POST["petid"] ?? 0) === (int)$pet["petid"] ? "selected" : ""; ?>><?php echo e($pet["name"]); ?></option><?php } ?></select></label>
                <label>Type<select name="placementtype"><option <?php echo ($selectedApplication["placementtype"] ?? $_POST["placementtype"] ?? "Adoption") === "Adoption" ? "selected" : ""; ?>>Adoption</option><option <?php echo ($selectedApplication["placementtype"] ?? $_POST["placementtype"] ?? "") === "Foster" ? "selected" : ""; ?>>Foster</option></select></label>
                <label>Start date<input type="date" name="start_date" value="<?php echo e($_POST["start_date"] ?? ($selectedApplication["start_date"] ?? "")); ?>"></label>
                <label>End date<input type="date" name="end_date" value="<?php echo e($_POST["end_date"] ?? ($selectedApplication["end_date"] ?? "")); ?>"></label>
                <label>Appointment date and time<input type="datetime-local" name="appointment_datetime" min="<?php echo e(date("Y-m-d\\TH:i")); ?>" value="<?php echo e($appointmentInput); ?>" required></label>
                <label>Status<select name="status"><option <?php echo ($_POST["status"] ?? "Active") === "Active" ? "selected" : ""; ?>>Active</option><option <?php echo ($_POST["status"] ?? "") === "Scheduled" ? "selected" : ""; ?>>Scheduled</option><option <?php echo ($_POST["status"] ?? "") === "Completed" ? "selected" : ""; ?>>Completed</option><option <?php echo ($_POST["status"] ?? "") === "Returned" ? "selected" : ""; ?>>Returned</option></select></label>
            </div>
            <div class="form-actions"><input type="submit" value="Save placement"><?php if($selectedApplicationId > 0){ ?><a class="btn-light" href="applications.php">Cancel</a><?php }else{ ?><button type="button" class="btn-light" onclick="document.getElementById('placementForm').hidden=true;">Cancel</button><?php } ?></div>
        </form>

        <section class="stats">
            <div class="stat"><span>Total placements</span><b><?php echo $totalPlacements; ?></b></div>
            <div class="stat"><span>Active placements</span><b><?php echo $active; ?></b></div>
            <div class="stat"><span>Follow-ups due this week</span><b>0</b></div>
            <div class="stat"><span>Completed placements</span><b><?php echo count(array_filter($placements, fn($p) => strcasecmp((string)$p["status"], "Completed") === 0)); ?></b></div>
        </section>

        <section class="card placements-card">
            <div class="placements-toolbar">
                <div class="placements-tabs" aria-label="Placement type">
                    <a class="<?php echo $filter === "all" ? "active" : ""; ?>" href="placements.php">All records</a>
                    <a class="<?php echo $filter === "Adoption" ? "active" : ""; ?>" href="placements.php?type=Adoption">Adoptions</a>
                    <a class="<?php echo $filter === "Foster" ? "active" : ""; ?>" href="placements.php?type=Foster">Foster</a>
                </div>
            </div>

            <div class="table-wrap placements-table-wrap">
                <table class="placements-table">
                    <thead><tr><th>Record</th><th>Pet</th><th>Placed with</th><th>Type</th><th>Placed on</th><th>Follow-up</th><th>Appointment</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php if(empty($filtered)){ ?>
                        <tr><td colspan="8" class="empty">No placement records yet.</td></tr>
                    <?php }else{ foreach($filtered as $placement){
                        $src = photo_src($placement["photo_url"] ?? null);
                        $status = $placement["status"] ?: "Active";
                        $source = $placement["record_source"] ?? "placement";
                        $recordId = (int)$placement["placementid"];
                        $recordLabel = $source === "adoption" ? "ADO-".abs($recordId) : ($source === "foster" ? "FOS-".abs($recordId) : "PL-".str_pad((string)$recordId, 4, "0", STR_PAD_LEFT));
                    ?>
                        <tr>
                            <td><strong><?php echo e($recordLabel); ?></strong></td>
                            <td><div class="placement-pet"><span class="placement-photo"><?php if($src){ ?><img src="<?php echo e($src); ?>" alt=""><?php } ?></span><strong><?php echo e($placement["pet_name"]); ?></strong></div></td>
                            <td><?php echo e($placement["full_name"]); ?></td>
                            <td><span class="placement-type <?php echo strcasecmp($placement["placementtype"], "Foster") === 0 ? "foster" : ""; ?>"><?php echo e($placement["placementtype"] ?: "Adoption"); ?></span></td>
                            <td><?php echo $placement["start_date"] ? e(date("M j, Y", strtotime($placement["start_date"]))) : "—"; ?></td>
                            <td><?php echo $placement["end_date"] ? e(date("M j, Y", strtotime($placement["end_date"]))) : "—"; ?></td>
                            <td><?php echo !empty($placement["appointment_datetime"]) ? e(date("M j, Y g:i A", strtotime($placement["appointment_datetime"]))) : "—"; ?></td>
                            <td><span class="placement-status <?php echo strtolower($status); ?>"><?php echo e($status); ?></span></td>
                        </tr>
                    <?php }} ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>
</body>
</html>