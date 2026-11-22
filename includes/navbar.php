<?php
// Shared navigation bar. Expects session_start() to have been called already.
$isAdmin = ($_SESSION["role"] ?? "") === "admin";
$current = basename($_SERVER["SCRIPT_NAME"]);
?>
<header class="navbar">
    <a class="brand" href="<?php echo $isAdmin ? "admin.php" : "dashboard.php" ?>">
        Hearth
    </a>
    <nav class="nav-links">
        <?php if ($isAdmin) { ?>
            <a href="admin.php" class="<?php echo $current === "admin.php" ? "active" : "" ?>">Dashboard</a>
            <a href="addpet.php" class="<?php echo $current === "addpet.php" ? "active" : "" ?>">Add pet</a>
        <?php } else { ?>
            <a href="dashboard.php" class="<?php echo $current === "dashboard.php" ? "active" : "" ?>">Find a pet</a>
            <a href="dashboard.php">My applications</a>
        <?php } ?>
    </nav>
    <div class="nav-user">
        <span class="nav-email"><?php echo htmlspecialchars($_SESSION["email"]) ?></span>
        <a href="../auth/logout.php" class="nav-btn">Logout</a>
    </div>
</header>   