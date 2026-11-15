<?php
$id = $_GET['id'] ?? 1;
header("Location: ../pet-profile.php?id=" . urlencode($id));
exit();
