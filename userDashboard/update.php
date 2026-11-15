<?php
session_start();
if (!isset($_SESSION["email"])) {
    header("Location: ../login.php");
    exit();
}

$id = $_GET['id'] ?? ($_POST['id'] ?? 1);
header("Location: dashboard.php?edit_id=" . urlencode($id));
exit();
