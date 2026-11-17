<?php
// comment
require_once __DIR__ . '/../includes/session.php';

$id = $_GET['id'] ?? 1;
header("Location: ../pet-profile.php?id=" . urlencode($id));
exit();
