<?php

session_start();

if (!isset($_SESSION["email"], $_SESSION["role"])) {
    header("Location:../logout.php");
    exit();
}

require_once __DIR__ . "/../vendor/autoload.php";

use app\Models\Pet;

$pets = [];
$loadError = false;

try {
    $petModel = new Pet();
    $pets = $petModel->getAll();
} catch (PDOException $e) {
    error_log($e->getMessage());
    $loadError = true;
}

$escape = static function ($value): string {
    return htmlspecialchars((string) ($value ?? ""), ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
};
$isAdmin = $_SESSION["role"] === "admin";
$statuses = [];
foreach ($pets as $pet) {
    $status = trim((string) ($pet["status"] ?? ""));
    if ($status !== "" && !in_array($status, $statuses, true)) {
        $statuses[] = $status;
    }
}
sort($statuses, SORT_NATURAL | SORT_FLAG_CASE);

require __DIR__ . "/dashboard-view.php";
