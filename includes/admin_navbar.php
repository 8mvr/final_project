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
        <!-- <a href="addpet.php" class="<?php echo $current === "addpet.php" ? "active" : "" ?>" <?php echo $current === "addpet.php" ? 'aria-current="page"' : "" ?>>Add pet</a> -->
    </nav>
    <div class="admin-me">
        <strong><?php echo htmlspecialchars($fullName); ?></strong>
        <a href="../auth/logout.php">Log out</a>
    </div>
</aside>
