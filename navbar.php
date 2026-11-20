<?php
// Shared navigation bar. Expects session_start() to have been called already.
$isAdmin = ($_SESSION["role"] ?? "") === "admin";
$current = basename($_SERVER["SCRIPT_NAME"]);
?>
<header class="navbar">
    <a class="brand" href="dashboard.php">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
            <circle cx="6" cy="9" r="1.8"/><circle cx="11" cy="5.5" r="1.8"/><circle cx="17" cy="7" r="1.8"/>
            <path d="M7 17c0-3 2.5-5 5-5s5 2 5 5c0 2-1.5 3-3 3-1 0-1.3-.5-2-.5s-1 .5-2 .5c-1.5 0-3-1-3-3z"/>
        </svg>
        hearth
    </a>
    <nav class="nav-links">
        <a href="dashboard.php" class="<?php echo $current === "dashboard.php" ? "active" : "" ?>">Find a pet</a>
        <?php if ($isAdmin) { ?>
            <a href="addpet.php" class="<?php echo $current === "addpet.php" ? "active" : "" ?>">Add a pet</a>
        <?php } ?>
    </nav>
    <div class="nav-user">
        <span class="nav-email"><?php echo htmlspecialchars($_SESSION["email"]) ?></span>
        <a href="../logout.php" class="nav-btn">Logout</a>
    </div>
</header>