<?php
require_once __DIR__."/../vendor/autoload.php";

use app\Models\Authentication;

$current = basename($_SERVER["SCRIPT_NAME"]);
$fullName = $_SESSION["email"];
try{
    $signedInUser = (new Authentication())->findByEmail($_SESSION["email"]);
    if($signedInUser){ $fullName = $signedInUser["full_name"] ?: $_SESSION["email"]; }
}catch(PDOException $ex){ }
?>
<aside class="admin-side">
    <a class="admin-logo" href="admin.php">Hearth</a>
    <p class="admin-side-label">Administrator</p>
    <nav class="admin-side-nav" aria-label="Admin navigation">
        <a href="admin.php" class="<?php echo $current === "admin.php" ? "active" : "" ?>" <?php echo $current === "admin.php" ? 'aria-current="page"' : "" ?>>Dashboard</a>
        <a href="applications.php" class="<?php echo $current === "applications.php" ? "active" : "" ?>" <?php echo $current === "applications.php" ? 'aria-current="page"' : "" ?>>Applications</a>
        <a href="">Appointments</a>
        <a href="placements.php" class="<?php echo $current === "placements.php" ? "active" : "" ?>" <?php echo $current === "placements.php" ? 'aria-current="page"' : "" ?>>Placements</a>
        <a href="users.php" class="<?php echo $current === "users.php" ? "active" : "" ?>" <?php echo $current === "users.php" ? 'aria-current="page"' : "" ?>>Users</a>
    </nav>
    <div class="admin-me">
        <strong><?php echo htmlspecialchars($fullName); ?></strong>
        <a href="../auth/logout.php">Log out</a>
    </div>
</aside>