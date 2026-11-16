<?php
// comment
require_once __DIR__ . '/../includes/session.php';

if (!isset($_SESSION["email"])) {
    header("Location: ../logout.php");
    exit();
}

$isAdmin = isset($_SESSION["role"]) && in_array($_SESSION["role"], ["admin", "staff"]);
if (!$isAdmin) {
    header("Location: dashboard.php");
    exit();
}

require_once __DIR__ . '/../vendor/autoload.php';
use app\Models\Pet;

$petModel = new Pet();
$id = $_GET["id"] ?? ($_GET["delete_id"] ?? null);

if ($id) {
    try {
        $petModel->delete($id);
    } catch (PDOException $e) {
        // error handling
    }
}

header("Location: dashboard.php");
exit();
