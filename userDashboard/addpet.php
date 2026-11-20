<?php

session_start();

if (!isset($_SESSION["email"], $_SESSION["role"])) {
    header("Location:../logout.php");
    exit();
}
if ($_SESSION["role"] !== "admin") {
    header("Location:dashboard.php");
    exit();
}

require_once __DIR__ . "/../vendor/autoload.php";

use app\Models\Pet;
use app\Upload;

$error = "";
$values = [
    "name" => "",
    "species" => "",
    "breed" => "",
    "age" => "",
    "sex" => "",
    "description" => "",
    "status" => ""
];

if (isset($_POST["submit"])) {
    foreach ($values as $field => $_) {
        $values[$field] = trim((string) ($_POST[$field] ?? ""));
    }

    try {
        $photoUrl = Upload::save($_FILES["photo"]);
        $petModel = new Pet();
        $petModel->insert(
            $_SESSION["email"],
            $values["name"],
            $values["species"],
            $values["breed"],
            $values["age"] === "" ? null : (int) $values["age"],
            $values["sex"],
            $values["description"],
            $photoUrl ?? "",
            $values["status"]
        );
        header("Location:dashboard.php");
        exit();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
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
    <title>Add a pet | Hearth</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="addpet.css">
</head>
<body class="dashboard-page" id="top">
    <header class="site-header">
        <div class="header-inner">
            <a class="brand" href="dashboard.php" aria-label="Hearth pet dashboard">
                <span class="brand-mark" aria-hidden="true">h</span>
                <span>hearth</span>
            </a>
            <div class="header-actions">
                <span class="welcome">Welcome, <?= $escape($_SESSION["email"]) ?></span>
                <a class="button button-secondary" href="dashboard.php">Back to dashboard</a>
            </div>
        </div>
    </header>

    <main class="main-content form-main">
        <section class="intro form-intro">
            <p class="eyebrow">Pet administration</p>
            <h1>Add a pet.</h1>
            <p class="intro-copy">Create a profile to help each pet find the right home.</p>
        </section>

        <section class="form-panel" aria-labelledby="add-pet-title">
            <div class="panel-heading">
                <h2 id="add-pet-title">Pet details</h2>
            </div>

            <?php if ($error !== "") { ?>
                <div class="error-state form-error" role="alert">
                    <p><?= $escape($error) ?></p>
                </div>
            <?php } ?>

            <form class="pet-form" method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-field">
                        <label for="pet-name">Pet name</label>
                        <input id="pet-name" type="text" name="name" value="<?= $escape($values["name"]) ?>" required>
                    </div>
                    <div class="form-field">
                        <label for="pet-species">Species</label>
                        <input id="pet-species" type="text" name="species" value="<?= $escape($values["species"]) ?>" placeholder="Dog, cat, rabbit..." required>
                    </div>
                    <div class="form-field">
                        <label for="pet-breed">Breed</label>
                        <input id="pet-breed" type="text" name="breed" value="<?= $escape($values["breed"]) ?>" required>
                    </div>
                    <div class="form-field">
                        <label for="pet-age">Age</label>
                        <input id="pet-age" type="number" name="age" value="<?= $escape($values["age"]) ?>" min="0" max="50" required>
                    </div>
                    <div class="form-field">
                        <label for="pet-sex">Sex</label>
                        <select id="pet-sex" name="sex" required>
                            <option value="">Select sex</option>
                            <option value="Male" <?= $values["sex"] === "Male" ? "selected" : "" ?>>Male</option>
                            <option value="Female" <?= $values["sex"] === "Female" ? "selected" : "" ?>>Female</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label for="pet-status">Status</label>
                        <input id="pet-status" type="text" name="status" value="<?= $escape($values["status"]) ?>" placeholder="Available, Fostered..." required>
                    </div>
                    <div class="form-field form-field-wide">
                        <label for="pet-description">Description</label>
                        <textarea id="pet-description" name="description" rows="5" required><?= $escape($values["description"]) ?></textarea>
                    </div>
                    <div class="form-field form-field-wide">
                        <label for="pet-photo">Photo</label>
                        <div class="file-input">
                            <input id="pet-photo" type="file" name="photo" accept="image/jpeg,image/png,image/webp">
                            <span>JPG, PNG or WEBP up to 2 MB</span>
                        </div>
                    </div>
                </div>
                <div class="form-actions">
                    <a class="button button-secondary" href="dashboard.php">Cancel</a>
                    <button class="button" type="submit" name="submit" value="1">Add pet</button>
                </div>
            </form>
        </section>
    </main>

    <footer class="site-footer">
        <div class="footer-inner">
            <p>A little care, a lasting connection.</p>
            <a href="../logout.php">Log out</a>
        </div>
    </footer>
</body>
</html>