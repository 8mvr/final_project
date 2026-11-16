<?php
// comment
require_once __DIR__ . '/session.php';
$currentPage = basename($_SERVER['PHP_SELF'] ?? 'index.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' · Hearth' : 'Hearth · Pet Adoption & Foster'; ?></title>
    <link rel="stylesheet" href="<?php echo isset($pathToRoot) ? $pathToRoot : ''; ?>style.css">
    <link rel="icon" type="image/svg+xml" href="<?php echo isset($pathToRoot) ? $pathToRoot : ''; ?>assets/images/paw-print.svg">
</head>
<body>
    <header class="hearth-nav">
        <a href="<?php echo isset($pathToRoot) ? $pathToRoot : ''; ?>index.php" class="brand">
            <img src="<?php echo isset($pathToRoot) ? $pathToRoot : ''; ?>assets/images/paw-print.svg" alt="Hearth" class="brand-icon">
            <span class="brand-text">hearth</span>
        </a>

        <nav class="nav-links">
            <a href="<?php echo isset($pathToRoot) ? $pathToRoot : ''; ?>index.php" class="<?php echo $currentPage === 'index.php' ? 'active' : ''; ?>">Home</a>
            <a href="<?php echo isset($pathToRoot) ? $pathToRoot : ''; ?>pets.php" class="<?php echo in_array($currentPage, ['pets.php', 'pet-profile.php']) ? 'active' : ''; ?>">Find a pet</a>
            <a href="<?php echo isset($pathToRoot) ? $pathToRoot : ''; ?>index.php#how-it-works">How adoption works</a>
            <a href="<?php echo isset($pathToRoot) ? $pathToRoot : ''; ?>index.php#foster">Become a foster</a>
            <a href="<?php echo isset($pathToRoot) ? $pathToRoot : ''; ?>index.php#story">Our story</a>
        </nav>

        <div class="nav-actions">
            <?php if (isset($_SESSION['email'])): ?>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'staff'): ?>
                    <a href="<?php echo isset($pathToRoot) ? $pathToRoot : ''; ?>userDashboard/dashboard.php" class="btn-nav btn-nav-primary">
                        Staff Console
                    </a>
                <?php else: ?>
                    <a href="<?php echo isset($pathToRoot) ? $pathToRoot : ''; ?>my-applications.php" class="btn-nav">
                        My applications
                    </a>
                <?php endif; ?>
                <a href="<?php echo isset($pathToRoot) ? $pathToRoot : ''; ?>logout.php" class="btn-nav" title="Sign out">
                    Sign out
                </a>
            <?php else: ?>
                <a href="<?php echo isset($pathToRoot) ? $pathToRoot : ''; ?>my-applications.php" class="btn-nav">
                    My applications
                </a>
                <a href="<?php echo isset($pathToRoot) ? $pathToRoot : ''; ?>login.php" class="btn-nav btn-nav-primary">
                    Sign in
                </a>
            <?php endif; ?>
        </div>
    </header>
