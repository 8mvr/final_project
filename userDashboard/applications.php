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

use app\Models\Application;
use app\Models\Authentication;

function e($value){
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

$appModel = new Application();
$authModel = new Authentication();
$message = "";
$error = "";

if(($_POST["action"] ?? "") === "review"){
    $kind = $_POST["kind"] ?? "";
    $id = (int)($_POST["id"] ?? 0);
    $status = $_POST["status"] ?? "";

    if(!in_array($status, ["Approved", "Rejected"], true)){
        $error = "Invalid review status.";
    }elseif($status === "Approved" && in_array($kind, ["adoption", "foster"], true) && $id > 0){
        header("Location:placements.php?application_kind=".urlencode($kind)."&application_id=".$id);
        exit();
    }else{
        try{
            $admin = $authModel->findByEmail($_SESSION["email"]);
            $reviewedBy = (int)($admin["userid"] ?? 0);

            if($kind === "adoption"){
                $appModel->reviewAdoption($id, $status, $reviewedBy);
            }elseif($kind === "foster"){
                $appModel->reviewFoster($id, $status, $reviewedBy);
            }else{
                $error = "Invalid application type.";
            }

            if($error === ""){
                $message = "Application $status successfully.";
            }
        }catch(PDOException $ex){
            $error = $ex->getMessage();
        }
    }
}

try{
    $adoptions = $appModel->allAdoptions();
    $fosters = $appModel->allFosters();
}catch(PDOException $ex){
    $adoptions = [];
    $fosters = [];
    $error = $ex->getMessage();
}

$applications = [];
foreach($adoptions as $application){
    $applications[] = [
        "kind" => "adoption",
        "id" => (int)$application["adoptionid"],
        "reference" => "ADO-".$application["adoptionid"],
        "full_name" => $application["full_name"],
        "email" => $application["email"],
        "pet_name" => $application["pet_name"],
        "photo_url" => $application["photo_url"],
        "application_date" => $application["application_date"],
        "appointment_datetime" => $application["appointment_datetime"] ?? null,
        "status" => $application["status"] ?: "Pending",
        "reason" => $application["reason"] ?? "",
        "reviewed_by_name" => $application["reviewed_by_name"] ?? null
    ];
}
foreach($fosters as $application){
    $applications[] = [
        "kind" => "foster",
        "id" => (int)$application["fosterid"],
        "reference" => "FOS-".$application["fosterid"],
        "full_name" => $application["full_name"],
        "email" => $application["email"],
        "pet_name" => $application["pet_name"],
        "photo_url" => $application["photo_url"],
        "application_date" => $application["application_date"],
        "appointment_datetime" => $application["appointment_datetime"] ?? null,
        "status" => $application["status"] ?: "Pending",
        "reason" => "Foster period: ".($application["planned_start_date"] ?: "?")." to ".($application["planned_end_date"] ?: "?"),
        "reviewed_by_name" => $application["reviewed_by_name"] ?? null
    ];
}

usort($applications, fn($a, $b) => strcmp($b["application_date"] ?: "", $a["application_date"] ?: ""));

$type = $_GET["type"] ?? "all";
if(!in_array($type, ["all", "adoption", "foster"], true)){ $type = "all"; }
$statusFilter = $_GET["status"] ?? "all";
if(!in_array($statusFilter, ["all", "Pending", "Approved", "Rejected"], true)){ $statusFilter = "all"; }

$applications = array_values(array_filter($applications, function($application) use ($type, $statusFilter){
    $typeMatch = $type === "all" || $application["kind"] === $type;
    $statusMatch = $statusFilter === "all" || strcasecmp($application["status"], $statusFilter) === 0;
    return $typeMatch && $statusMatch;
}));

$pendingCount = count(array_filter($applications, fn($app) => strcasecmp($app["status"], "Pending") === 0));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applications · Hearth</title>
    <link rel="stylesheet" href="../assets/css/pet.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="admin-layout">
    <?php require __DIR__."/../includes/admin_navbar.php"; ?>
    <main class="main applications-main">
        <header class="top applications-top">
            <div>
                <h1>Applications</h1>
                <p>Review adoption and foster applications submitted by users.</p>
            </div>
            <span class="applications-count"><?php echo $pendingCount; ?> pending</span>
        </header>

        <?php if($message !== ""){ ?><p class="okmsg"><?php echo e($message); ?></p><?php } ?>
        <?php if($error !== ""){ ?><p class="notice"><?php echo e($error); ?></p><?php } ?>

        <section class="card applications-card">
            <div class="applications-toolbar">
                <div class="applications-tabs">
                    <a class="<?php echo $type === "all" ? "active" : ""; ?>" href="applications.php?status=<?php echo urlencode($statusFilter); ?>">All</a>
                    <a class="<?php echo $type === "adoption" ? "active" : ""; ?>" href="applications.php?type=adoption&amp;status=<?php echo urlencode($statusFilter); ?>">Adoption</a>
                    <a class="<?php echo $type === "foster" ? "active" : ""; ?>" href="applications.php?type=foster&amp;status=<?php echo urlencode($statusFilter); ?>">Foster</a>
                </div>
                <form class="applications-filter" method="GET">
                    <input type="hidden" name="type" value="<?php echo e($type); ?>">
                    <select name="status" aria-label="Filter application status">
                        <option value="all" <?php echo $statusFilter === "all" ? "selected" : ""; ?>>All statuses</option>
                        <option value="Pending" <?php echo $statusFilter === "Pending" ? "selected" : ""; ?>>Pending</option>
                        <option value="Approved" <?php echo $statusFilter === "Approved" ? "selected" : ""; ?>>Approved</option>
                        <option value="Rejected" <?php echo $statusFilter === "Rejected" ? "selected" : ""; ?>>Rejected</option>
                    </select>
                    <button type="submit" class="btn-light btn-sm">Apply</button>
                </form>
            </div>

            <?php if(empty($applications)){ ?>
                <div class="applications-empty">No applications match these filters.</div>
            <?php }else{ foreach($applications as $application){
                $src = photo_src($application["photo_url"]);
                $statusClass = strtolower($application["status"]);
            ?>
                <article class="application-row">
                    <div class="application-photo"><?php if($src){ ?><img src="<?php echo e($src); ?>" alt="<?php echo e($application["pet_name"]); ?>"><?php } ?></div>
                    <div class="application-details">
                        <div class="application-heading">
                            <div>
                                <strong><?php echo e($application["full_name"]); ?></strong>
                                <span><?php echo e($application["email"]); ?></span>
                            </div>
                            <span class="application-status <?php echo e($statusClass); ?>"><?php echo e($application["status"]); ?></span>
                        </div>
                        <p class="application-meta"><?php echo e($application["reference"]); ?> · <?php echo e($application["pet_name"]); ?> · <?php echo e($application["kind"]); ?> application</p>
                        <?php if(!empty($application["appointment_datetime"])){ ?><p class="application-meta">Visit appointment: <?php echo e(date("M j, Y \\a\\t g:i A", strtotime($application["appointment_datetime"]))); ?></p><?php } ?>
                        <div class="application-reason"><?php echo e($application["reason"]); ?></div>
                        <?php if(strcasecmp($application["status"], "Pending") === 0){ ?>
                            <div class="application-actions">
                                <form method="POST">
                                    <input type="hidden" name="action" value="review">
                                    <input type="hidden" name="kind" value="<?php echo e($application["kind"]); ?>">
                                    <input type="hidden" name="id" value="<?php echo (int)$application["id"]; ?>">
                                    <input type="hidden" name="status" value="Approved">
                                    <button type="submit" class="btn-dark btn-sm">Approve</button>
                                </form>
                                <form method="POST">
                                    <input type="hidden" name="action" value="review">
                                    <input type="hidden" name="kind" value="<?php echo e($application["kind"]); ?>">
                                    <input type="hidden" name="id" value="<?php echo (int)$application["id"]; ?>">
                                    <input type="hidden" name="status" value="Rejected">
                                    <button type="submit" class="btn-light danger btn-sm">Reject</button>
                                </form>
                            </div>
                        <?php } ?>
                    </div>
                </article>
            <?php }} ?>
        </section>
    </main>
</div>
</body>
</html>
