<header class="site-header">
    <div class="header-inner">
        <a class="brand" href="welcome.php" aria-label="Hearth welcome page">
            <span class="brand-mark" aria-hidden="true">&#9825;</span>
            <span>Hearth</span>
        </a>
        <nav class="main-nav" aria-label="Primary navigation">
            <a class="nav-link <?= basename($_SERVER["PHP_SELF"]) === "welcome.php" ? "is-active" : "" ?>" href="welcome.php" <?= basename($_SERVER["PHP_SELF"]) === "welcome.php" ? 'aria-current="page"' : "" ?>>Home</a>
            <a class="nav-link <?= basename($_SERVER["PHP_SELF"]) === "dashboard.php" ? "is-active" : "" ?>" href="dashboard.php" <?= basename($_SERVER["PHP_SELF"]) === "dashboard.php" ? 'aria-current="page"' : "" ?>>Find a pet</a>
            <a class="nav-link" href="#about">Become a fosterer</a>
        </nav>
        <div class="header-actions">
            <?php if ($isAdmin) { ?>
                <a class="button" href="addpet.php">Add a pet</a>
            <?php } ?>
            <a class="applications-button" href="#pet-list">My applications</a>
        </div>
    </div>
</header>
