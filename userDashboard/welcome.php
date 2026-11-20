<?php

session_start();

if (!isset($_SESSION["email"], $_SESSION["role"])) {
    header("Location:../logout.php");
    exit();
}

require_once __DIR__ . "/../vendor/autoload.php";

use app\Models\Pet;

$featuredPet = null;
$photoUrl = "16c25d7d4e5e5ae0.jpg";
$featuredName = "Your future companion";

try {
    $pets = (new Pet())->getAll();
    foreach ($pets as $pet) {
        $candidate = trim((string) ($pet["photo_url"] ?? ""));
        if ($candidate !== "" && file_exists(__DIR__ . "/Images/" . basename($candidate))) {
            $featuredPet = $pet;
            $photoUrl = basename($candidate);
            $featuredName = trim((string) ($pet["name"] ?? "")) ?: $featuredName;
            break;
        }
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
}

$escape = static function ($value): string {
    return htmlspecialchars((string) ($value ?? ""), ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#24483e">
    <title>Hearth | Pet Adoption</title>
    <link rel="stylesheet" href="welcome.css">
</head>
<body class="welcome-page">
    <main class="welcome-shell">
        <section class="welcome-photo" aria-label="A pet currently in the Hearth system">
            <img src="Images/<?= $escape($photoUrl) ?>" alt="<?= $escape($featuredName) ?> available through Hearth">
            <div class="welcome-photo-caption">
                <span>Hearth</span>
                <p>Every adoption starts with a small moment of trust.</p>
            </div>
        </section>

        <section class="welcome-content" aria-labelledby="welcome-title">
            <p class="welcome-eyebrow">Pet adoption and foster coordination</p>
            <h1 id="welcome-title">Find a friend.<br>Change a life.</h1>
            <?php if ($featuredPet !== null) { ?>
                <p class="welcome-featured">Featured pet: <strong><?= $escape($featuredName) ?></strong></p>
            <?php } ?>
            <p class="welcome-copy">Meet the pets in our care, explore their profiles, and take the next step in a responsible adoption or foster journey.</p>

            <div class="welcome-actions">
                <a class="welcome-button welcome-button-primary" href="dashboard.php">View pets <span aria-hidden="true">&rarr;</span></a>
                <a class="welcome-button welcome-button-secondary" href="../logout.php">Sign out</a>
            </div>

            <p class="welcome-note">Helping pets find safe, lasting homes.</p>
        </section>
    </main>
</body>
</html>
