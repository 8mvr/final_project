<?php

session_start();

if (!isset($_SESSION["email"])) {
    header("Location:../auth/logout.php");
    exit();
}
if (($_SESSION["role"] ?? "") !== "admin") {
    header("Location:dashboard.php");
    exit();
}

require_once __DIR__ . "/../vendor/autoload.php";

use app\Models\Pet;

$id = $_GET["id"] ?? null;
if ($id === null) {
    header("Location:admin.php");
    exit();
}
$id = (int)$id;

try {
    (new Pet())->deleteWithRelations($id);

    header("Location:admin.php?deleted=1");
} catch (PDOException $e) {
    header("Location:admin.php?err=" . urlencode($e->getMessage()));
}
exit();