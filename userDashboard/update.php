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

require_once __DIR__ . "/../vendor/autoload.php";
use app\Models\Pet;

$petModel = new Pet();
if (isset($_POST["update"]) || isset($_POST["save_pet"])) {
    $id = $_POST["id"] ?? $_POST["pet_id"];
    $name = $_POST["name"];
    $species = $_POST["species"];
    $breed = $_POST["breed"];
    $age = $_POST["age"];
    $sex = $_POST["sex"];
    $description = $_POST["description"];
    $photo_url = $_POST["photo_url"];
    $status = $_POST["status"];

    try {
        $petModel->update($name, $species, $breed, $age, $sex, $description, $photo_url, $status, $id);
        header("Location: dashboard.php");
        exit();
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}

$id = $_GET['id'] ?? ($_POST['id'] ?? 1);
header("Location: dashboard.php?edit_id=" . urlencode($id));
exit();
