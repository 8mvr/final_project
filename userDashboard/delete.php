<?php
session_start();
if (!isset($_SESSION["email"])) {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . '/../vendor/autoload.php';
use app\Models\Pet;

if (isset($_GET['id'])) {
    $petModel = new Pet();
    $petModel->delete($_GET['id']);
}

header("Location: dashboard.php");
exit();
