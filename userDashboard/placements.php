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

function e($value){
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

$placementModel = new Placement();
$authModel = new Authentication();
$message = "";
$error = "";

if(($_POST["action"] ?? "") === "create"){
    $userid = (int)($_POST["userid"] ?? 0);
    $petid = (int)($_POST["petid"] ?? 0);
    $placementtype = $_POST["placementtype"] ?? "Adoption";
    $startDate = $_POST["start_date"] ?? "";
    $endDate = $_POST["end_date"] ?? "";
    $status = $_POST["status"] ?? "Active";

    $admin = $authModel->findByEmail($_SESSION["email"]);
    $recordedBy = (int)($admin["userid"] ?? 0);

    if($userid <= 0 || $petid <= 0){
        $error = "Select both a person and a pet.";
    }else{
        try{
            $placementModel->insert($userid, $petid, 0, 0, $placementtype, $startDate, $endDate, $status, $recordedBy);
            $message = "Placement recorded successfully.";
        }catch(PDOException $ex){
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
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="admin-layout">
    <?php require __DIR__."/../includes/admin_navbar.php"; ?>
    <main class="main placements-main">
        <header class="top placements-top">
            <div>
                <h1>Placement records</h1>
                <p>Every adoption and foster placement, with follow-up dates.</p>
            </div>
            <button class="btn-dark" type="button" onclick="document.getElementById('placementForm').hidden=false;document.getElementById('placementForm').scrollIntoView({behavior:'smooth'});">Record placement</button>
        </header>

        <?php if($message !== ""){ ?><p class="okmsg"><?php echo e($message); ?></p><?php } ?>
        <?php if($error !== ""){ ?><p class="notice"><?php echo e($error); ?></p><?php } ?>

        <form class="placement-form" id="placementForm" method="POST" hidden>
            <input type="hidden" name="action" value="create">
            <div class="placement-form-grid">
                <label>Person<select name="userid" required><option value="">Select user</option><?php foreach($options["users"] as $user){ ?><option value="<?php echo (int)$user["userid"]; ?>"><?php echo e($user["full_name"]); ?></option><?php } ?></select></label>
                <label>Pet<select name="petid" required><option value="">Select pet</option><?php foreach($options["pets"] as $pet){ ?><option value="<?php echo (int)$pet["petid"]; ?>"><?php echo e($pet["name"]); ?></option><?php } ?></select></label>
                <label>Type<select name="placementtype"><option>Adoption</option><option>Foster</option></select></label>
                <label>Start date<input type="date" name="start_date"></label>
                <label>End date<input type="date" name="end_date"></label>
                <label>Status<select name="status"><option>Active</option><option>Scheduled</option><option>Completed</option><option>Returned</option></select></label>
            </div>
            <div class="form-actions"><input type="submit" value="Save placement"><button type="button" class="btn-light" onclick="document.getElementById('placementForm').hidden=true;">Cancel</button></div>
        </form>

        <section class="placement-stats">
            <div class="placement-stat"><span>Active placements</span><b><?php echo $active; ?></b><small>Adoptions and foster placements</small></div>
            <div class="placement-stat"><span>Follow-ups due this week</span><b>0</b><small>No follow-up dates recorded</small></div>
            <div class="placement-stat"><span>Completed placements</span><b><?php echo count(array_filter($placements, fn($p) => strcasecmp((string)$p["status"], "Completed") === 0)); ?></b><small><?php echo $adoptions; ?> adoption records</small></div>
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
                    <thead><tr><th>Record</th><th>Pet</th><th>Placed with</th><th>Type</th><th>Placed on</th><th>Follow-up</th><th>Status</th><th>Actions</th></tr></thead>
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
                            <td><span class="placement-status <?php echo strtolower($status); ?>"><?php echo e($status); ?></span></td>
                            <td><a class="btn-light btn-sm" href="placements.php?view=<?php echo (int)$placement["placementid"]; ?>">View</a></td>
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
