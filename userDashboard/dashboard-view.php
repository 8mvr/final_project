<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#24483e">
    <title>Pet dashboard | Hearth</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="header.css">
    <script src="dashboard.js" defer></script>
</head>
<body class="dashboard-page" id="top">
    <?php require __DIR__ . "/header.php"; ?>

    <main class="main-content">
        <?php
            $heroPet = null;
            foreach ($pets as $pet) {
                if (trim((string) ($pet["photo_url"] ?? "")) !== "") {
                    $heroPet = $pet;
                    break;
                }
            }
        ?>
        <section class="hero" aria-labelledby="page-title">
            <div class="hero-copy">
                <p class="eyebrow">Find a companion</p>
                <h1 id="page-title">Give a pet a loving home.</h1>
                <p class="intro-copy">Meet the animals in our care and find the friend who fits your life.</p>
                <a class="button hero-button" href="#pet-list">Meet our pets</a>
            </div>
            <div class="hero-image">
                <?php if ($heroPet !== null) { ?>
                    <img src="Images/<?= $escape($heroPet["photo_url"]) ?>" alt="<?= $escape($heroPet["name"] ?? "Pet available for adoption") ?>">
                <?php } else { ?>
                    <span>Photo coming soon</span>
                <?php } ?>
            </div>
        </section>

        <section class="pets-panel" id="pet-list" aria-labelledby="pets-title">
            <div class="panel-heading">
                <h2 id="pets-title">Pets in our care</h2>
                <?php if (!$loadError) { ?>
                    <span class="pet-count" id="pet-count" aria-live="polite"><?= count($pets) ?> <?= count($pets) === 1 ? "pet" : "pets" ?></span>
                <?php } ?>
            </div>

            <?php if ($loadError) { ?>
                <div class="error-state" role="alert">
                    <p>We couldn't load the pet list right now. Please try again later.</p>
                </div>
            <?php } elseif (empty($pets)) { ?>
                <div class="empty-state">
                    <h3>No pets have been added yet</h3>
                    <p>Please check back soon for pets looking for a home.</p>
                </div>
            <?php } else { ?>
                <div class="filters" role="search" aria-label="Filter pets">
                    <label class="sr-only" for="pet-search">Search by name, species, or breed</label>
                    <input class="filter-control" id="pet-search" type="search" placeholder="Search by name, species, or breed">
                    <label class="sr-only" for="status-filter">Filter by status</label>
                    <select class="filter-control" id="status-filter">
                        <option value="">All statuses</option>
                        <?php foreach ($statuses as $status) { ?>
                            <option value="<?= $escape($status) ?>"><?= $escape($status) ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="pet-grid" id="pet-grid">
                    <?php foreach ($pets as $pet) {
                        $name = $pet["name"] ?? "";
                        $species = $pet["species"] ?? "";
                        $breed = $pet["breed"] ?? "";
                        $status = $pet["status"] ?? "";
                        $searchText = implode(" ", [$name, $species, $breed]);
                        $hasPhoto = trim((string) ($pet["photo_url"] ?? "")) !== "";
                    ?>
                        <article class="pet-card" data-search="<?= $escape($searchText) ?>" data-status="<?= $escape($status) ?>">
                            <div class="pet-photo">
                                <?php if ($hasPhoto) { ?>
                                    <img data-pet-photo src="Images/<?= $escape($pet["photo_url"]) ?>" alt="<?= $escape($name) ?>">
                                <?php } ?>
                                <span class="photo-placeholder" <?= $hasPhoto ? "hidden" : "" ?>>Photo coming soon</span>
                            </div>
                            <div class="pet-details">
                                <div class="pet-title-row">
                                    <h3><?= $escape($name) ?></h3>
                                    <?php if (trim((string) $status) !== "") { ?>
                                        <span class="status"><?= $escape($status) ?></span>
                                    <?php } ?>
                                </div>
                                <p class="pet-meta"><?= $escape($species) ?><?php if (trim((string) $breed) !== "") { ?> &middot; <?= $escape($breed) ?><?php } ?><?php if (isset($pet["age"]) && $pet["age"] !== "") { ?> &middot; <?= $escape($pet["age"]) ?> yrs<?php } ?><?php if (trim((string) ($pet["sex"] ?? "")) !== "") { ?> &middot; <?= $escape($pet["sex"]) ?><?php } ?></p>
                                <p class="pet-description"><?= $escape($pet["description"] ?? "") ?></p>
                                <div class="pet-actions">
                                    <?php if ($isAdmin) { ?>
                                        <a class="text-link" href="viewbyid.php?id=<?= rawurlencode((string) ($pet["petid"] ?? "")) ?>">Edit details</a>
                                        <a class="text-link text-link-danger" href="delete.php?id=<?= rawurlencode((string) ($pet["petid"] ?? "")) ?>" onclick="return confirm('Are you sure you want to delete this pet?')">Delete</a>
                                    <?php } else { ?>
                                        <a class="text-link" href="viewbyid.php?id=<?= rawurlencode((string) ($pet["petid"] ?? "")) ?>">View details</a>
                                    <?php } ?>
                                </div>
                            </div>
                        </article>
                    <?php } ?>
                </div>
                <div class="empty-state" id="no-matches" hidden>
                    <h3>No pets match those filters</h3>
                    <p>Try a different search or status.</p>
                </div>
            <?php } ?>
        </section>
    </main>

    <?php require __DIR__ . "/footer.php"; ?>
</body>
</html>
