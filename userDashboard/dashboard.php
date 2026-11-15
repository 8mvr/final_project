<?php
require_once __DIR__ . '/../includes/session.php';

// Check if user is logged in
if (!isset($_SESSION["email"])) {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . '/../vendor/autoload.php';
use app\Models\Pet;

$petModel = new Pet();

// Handle deletion if requested directly
if (isset($_GET['delete_id'])) {
    $petModel->delete($_GET['delete_id']);
    header("Location: dashboard.php");
    exit();
}

// Handle Add or Update form submit
$actionMsg = "";
if (isset($_POST['save_pet'])) {
    $petId = $_POST['pet_id'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $species = trim($_POST['species'] ?? 'Dog');
    $breed = trim($_POST['breed'] ?? '');
    $age = intval($_POST['age'] ?? 1);
    $sex = trim($_POST['sex'] ?? 'Female');
    $description = trim($_POST['description'] ?? '');
    $photo_url = trim($_POST['photo_url'] ?? '');
    $status = trim($_POST['status'] ?? 'Available');

    if (empty($photo_url)) {
        $photo_url = "assets/images/pet-maple.png";
    }

    if (!empty($petId)) {
        $res = $petModel->update($name, $species, $breed, $age, $sex, $description, $photo_url, $status, $petId);
        $actionMsg = "Pet updated successfully.";
    } else {
        $res = $petModel->insert($_SESSION["email"], $name, $species, $breed, $age, $sex, $description, $photo_url, $status);
        $actionMsg = "Pet added successfully.";
    }
}

// Filters & Search
$search = $_GET['search'] ?? '';
$speciesFilter = $_GET['species'] ?? 'All species';
$statusFilter = $_GET['status'] ?? 'All statuses';

$allPets = $petModel->getAll();
$pets = $petModel->getAll(
    $speciesFilter === 'All species' ? null : $speciesFilter,
    $statusFilter === 'All statuses' ? null : $statusFilter,
    $search
);

$totalCount = count($allPets);
$availableCount = count(array_filter($allPets, fn($p) => stripos($p['status'], 'available') !== false));

// Selected pet for edit panel (defaults to first pet or requested pet)
$editPetId = $_GET['edit_id'] ?? ($pets[0]['petid'] ?? 1);
$editPet = $petModel->ViewbyID($editPetId);
$isAdding = isset($_GET['action']) && $_GET['action'] === 'add';

$userName = $_SESSION['full_name'] ?? 'Priya Natarajan';
$userInitials = strtoupper(substr($userName, 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Pets · Hearth Shelter Console</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" type="image/svg+xml" href="../assets/images/paw-print.svg">
</head>
<body style="background-color: var(--bg-page);">
    <div class="dashboard-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <a href="../index.php" class="brand">
                <img src="../assets/images/paw-print.svg" alt="Hearth" class="brand-icon" style="filter: brightness(0) invert(1);">
                <span class="brand-text">hearth</span>
            </a>

            <div class="sidebar-nav">
                <span class="sidebar-role-label">Shelter staff</span>

                <a href="dashboard.php" class="sidebar-item">
                    <img src="../assets/images/icon-dash.svg" alt="" class="item-icon">
                    <span>Dashboard</span>
                </a>

                <a href="#applications" class="sidebar-item">
                    <img src="../assets/images/icon-clip.svg" alt="" class="item-icon">
                    <span>Applications</span>
                    <span class="badge-count item-count">12</span>
                </a>

                <a href="dashboard.php" class="sidebar-item active">
                    <img src="../assets/images/icon-paw.svg" alt="" class="item-icon">
                    <span>Pets</span>
                </a>

                <a href="#appointments" class="sidebar-item">
                    <img src="../assets/images/icon-cal.svg" alt="" class="item-icon">
                    <span>Appointments</span>
                </a>

                <a href="#placements" class="sidebar-item">
                    <img src="../assets/images/icon-file.svg" alt="" class="item-icon">
                    <span>Placements</span>
                </a>

                <a href="#users" class="sidebar-item">
                    <img src="../assets/images/icon-users.svg" alt="" class="item-icon">
                    <span>Users</span>
                </a>
            </div>

            <div class="sidebar-spacer"></div>

            <div class="sidebar-user">
                <div class="user-avatar"><?php echo htmlspecialchars($userInitials); ?></div>
                <div class="user-info">
                    <span class="user-name"><?php echo htmlspecialchars($userName); ?></span>
                    <a href="../logout.php" class="user-action">Sign out</a>
                </div>
            </div>
        </aside>

        <!-- Main Dashboard Content -->
        <main class="dashboard-main">
            <!-- Header -->
            <div class="dashboard-header">
                <div>
                    <h1 class="dashboard-title">Pets</h1>
                    <p class="dashboard-subtitle">
                        <?php echo $totalCount; ?> pets in your care · <?php echo $availableCount; ?> available for adoption or foster
                    </p>
                </div>
                <a href="dashboard.php?action=add" class="btn btn-primary">
                    <span>Add pet</span>
                    <img src="../assets/images/icon-plus.svg" alt="" style="width: 16px; height: 16px; filter: brightness(0) invert(1);">
                </a>
            </div>

            <?php if (!empty($actionMsg)): ?>
                <div class="alert-banner" style="background: #e6f4ea; border-color: #ceead6; color: #137333;">
                    <img src="../assets/images/icon-check.svg" alt="" style="width: 16px; height: 16px;">
                    <span><?php echo htmlspecialchars($actionMsg); ?></span>
                </div>
            <?php endif; ?>

            <!-- Table & Drawer Columns -->
            <div class="dashboard-layout-cols">
                <!-- Table Column -->
                <div class="dashboard-table-col">
                    <!-- Filters & Search Bar -->
                    <form action="dashboard.php" method="GET" class="table-filter-bar">
                        <div class="table-search-input">
                            <img src="../assets/images/icon-search.svg" alt="" style="width: 16px; height: 16px;">
                            <input type="text" name="search" placeholder="Search by name or breed" value="<?php echo htmlspecialchars($search); ?>">
                        </div>

                        <div class="table-select">
                            <select name="species" onchange="this.form.submit()">
                                <option value="All species" <?php echo $speciesFilter === 'All species' ? 'selected' : ''; ?>>All species</option>
                                <option value="Dog" <?php echo $speciesFilter === 'Dog' ? 'selected' : ''; ?>>Dog</option>
                                <option value="Cat" <?php echo $speciesFilter === 'Cat' ? 'selected' : ''; ?>>Cat</option>
                                <option value="Rabbit" <?php echo $speciesFilter === 'Rabbit' ? 'selected' : ''; ?>>Rabbit</option>
                            </select>
                            <img src="../assets/images/icon-chevd.svg" alt="" style="width: 14px; height: 14px;">
                        </div>

                        <div class="table-select">
                            <select name="status" onchange="this.form.submit()">
                                <option value="All statuses" <?php echo $statusFilter === 'All statuses' ? 'selected' : ''; ?>>All statuses</option>
                                <option value="Available" <?php echo $statusFilter === 'Available' ? 'selected' : ''; ?>>Available</option>
                                <option value="In foster" <?php echo $statusFilter === 'In foster' ? 'selected' : ''; ?>>In foster</option>
                                <option value="Adopted" <?php echo $statusFilter === 'Adopted' ? 'selected' : ''; ?>>Adopted</option>
                            </select>
                            <img src="../assets/images/icon-chevd.svg" alt="" style="width: 14px; height: 14px;">
                        </div>
                    </form>

                    <!-- Table Card -->
                    <div class="data-table-card">
                        <div class="table-head">
                            <div class="col-pet">Pet</div>
                            <div class="col-species">Species</div>
                            <div class="col-breed">Breed</div>
                            <div class="col-age">Age</div>
                            <div class="col-status">Status</div>
                            <div class="col-actions"></div>
                        </div>

                        <?php if (empty($pets)): ?>
                            <div style="padding: 32px; text-align: center; color: var(--text-muted); font-size: 14px;">
                                No pets match the selected filter.
                            </div>
                        <?php else: ?>
                            <?php foreach ($pets as $p): ?>
                                <div class="table-row">
                                    <div class="col-pet">
                                        <img src="../<?php echo htmlspecialchars($p['photo_url']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" class="pet-thumb" onerror="this.src='../assets/images/pet-maple.png'">
                                        <span class="pet-name-cell"><?php echo htmlspecialchars($p['name']); ?></span>
                                    </div>
                                    <div class="col-species"><?php echo htmlspecialchars($p['species']); ?></div>
                                    <div class="col-breed"><?php echo htmlspecialchars($p['breed']); ?></div>
                                    <div class="col-age"><?php echo htmlspecialchars($p['age']); ?> yrs</div>
                                    <div class="col-status">
                                        <?php 
                                            $stClass = 'badge-available';
                                            if (stripos($p['status'], 'in foster') !== false) {
                                                $stClass = 'badge-in-foster';
                                            } elseif (stripos($p['status'], 'foster') !== false) {
                                                $stClass = 'badge-foster';
                                            } elseif (stripos($p['status'], 'adopted') !== false) {
                                                $stClass = 'badge-adopted';
                                            }
                                        ?>
                                        <span class="badge <?php echo $stClass; ?>">
                                            <?php echo htmlspecialchars($p['status']); ?>
                                        </span>
                                    </div>
                                    <div class="col-actions">
                                        <a href="dashboard.php?edit_id=<?php echo $p['petid']; ?>" class="btn-icon-sq" title="Edit pet">
                                            <img src="../assets/images/icon-pen.svg" alt="Edit" style="width: 14px; height: 14px;">
                                        </a>
                                        <a href="dashboard.php?delete_id=<?php echo $p['petid']; ?>" class="btn-icon-sq btn-danger" onclick="return confirm('Are you sure you want to remove <?php echo htmlspecialchars($p['name']); ?>?')" title="Delete pet">
                                            <img src="../assets/images/icon-trash.svg" alt="Delete" style="width: 14px; height: 14px;">
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <!-- Table Pagination Footer -->
                        <div class="table-pagination">
                            <span>Showing 1–<?php echo count($pets); ?> of <?php echo $totalCount; ?> pets</span>
                            <div style="display: flex; gap: 8px;">
                                <button class="btn-icon-sq" disabled style="opacity: 0.5;">
                                    <img src="../assets/images/icon-chevl.svg" alt="Previous" style="width: 14px; height: 14px;">
                                </button>
                                <button class="btn-icon-sq" disabled style="opacity: 0.5;">
                                    <img src="../assets/images/icon-chevr.svg" alt="Next" style="width: 14px; height: 14px;">
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side Edit / Add Pet Panel -->
                <aside class="drawer-panel">
                    <div class="drawer-header">
                        <h2 class="drawer-title"><?php echo $isAdding ? 'Add pet' : 'Edit pet'; ?></h2>
                        <a href="dashboard.php" class="btn-icon-sq" title="Close">
                            <img src="../assets/images/icon-x.svg" alt="Close" style="width: 14px; height: 14px;">
                        </a>
                    </div>

                    <form action="dashboard.php" method="POST" style="display: flex; flex-direction: column; gap: 16px;">
                        <input type="hidden" name="pet_id" value="<?php echo $isAdding ? '' : htmlspecialchars($editPet['petid'] ?? ''); ?>">

                        <!-- Photo Section -->
                        <div class="drawer-photo-section">
                            <img id="photoPreview" src="../<?php echo htmlspecialchars($editPet['photo_url'] ?? 'assets/images/pet-maple.png'); ?>" alt="Photo" class="drawer-photo-preview" onerror="this.src='../assets/images/pet-maple.png'">
                            <div style="display: flex; flex-direction: column; gap: 6px;">
                                <span class="form-label">Profile photo</span>
                                <input type="text" name="photo_url" id="photoUrlInput" value="<?php echo htmlspecialchars($editPet['photo_url'] ?? 'assets/images/pet-maple.png'); ?>" placeholder="assets/images/pet-maple.png" style="font-size: 11px; padding: 6px 10px; border: 1px solid var(--border-light); border-radius: 6px;" oninput="document.getElementById('photoPreview').src = '../' + this.value;">
                            </div>
                        </div>

                        <!-- Name and Species Row -->
                        <div class="form-row">
                            <div class="form-field">
                                <label class="form-label">Name</label>
                                <div class="input-wrapper" style="height: 40px;">
                                    <input type="text" name="name" value="<?php echo $isAdding ? '' : htmlspecialchars($editPet['name'] ?? ''); ?>" required placeholder="Maple">
                                </div>
                            </div>
                            <div class="form-field">
                                <label class="form-label">Species</label>
                                <div class="input-wrapper" style="height: 40px;">
                                    <select name="species" style="width: 100%; border: none; outline: none; background: transparent; font-size: 13px;">
                                        <option value="Dog" <?php echo (!$isAdding && ($editPet['species'] ?? '') === 'Dog') ? 'selected' : ''; ?>>Dog</option>
                                        <option value="Cat" <?php echo (!$isAdding && ($editPet['species'] ?? '') === 'Cat') ? 'selected' : ''; ?>>Cat</option>
                                        <option value="Rabbit" <?php echo (!$isAdding && ($editPet['species'] ?? '') === 'Rabbit') ? 'selected' : ''; ?>>Rabbit</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Breed and Age Row -->
                        <div class="form-row">
                            <div class="form-field">
                                <label class="form-label">Breed</label>
                                <div class="input-wrapper" style="height: 40px;">
                                    <input type="text" name="breed" value="<?php echo $isAdding ? '' : htmlspecialchars($editPet['breed'] ?? ''); ?>" placeholder="Beagle mix" required>
                                </div>
                            </div>
                            <div class="form-field">
                                <label class="form-label">Age (years)</label>
                                <div class="input-wrapper" style="height: 40px;">
                                    <input type="number" name="age" value="<?php echo $isAdding ? '1' : htmlspecialchars($editPet['age'] ?? '1'); ?>" min="0" max="30" required>
                                </div>
                            </div>
                        </div>

                        <!-- Sex and Status Row -->
                        <div class="form-row">
                            <div class="form-field">
                                <label class="form-label">Sex</label>
                                <div class="input-wrapper" style="height: 40px;">
                                    <select name="sex" style="width: 100%; border: none; outline: none; background: transparent; font-size: 13px;">
                                        <option value="Female" <?php echo (!$isAdding && ($editPet['sex'] ?? '') === 'Female') ? 'selected' : ''; ?>>Female</option>
                                        <option value="Male" <?php echo (!$isAdding && ($editPet['sex'] ?? '') === 'Male') ? 'selected' : ''; ?>>Male</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-field">
                                <label class="form-label">Status</label>
                                <div class="input-wrapper" style="height: 40px;">
                                    <select name="status" style="width: 100%; border: none; outline: none; background: transparent; font-size: 13px;">
                                        <option value="Available" <?php echo (!$isAdding && ($editPet['status'] ?? '') === 'Available') ? 'selected' : ''; ?>>Available</option>
                                        <option value="In foster" <?php echo (!$isAdding && ($editPet['status'] ?? '') === 'In foster') ? 'selected' : ''; ?>>In foster</option>
                                        <option value="Foster welcome" <?php echo (!$isAdding && ($editPet['status'] ?? '') === 'Foster welcome') ? 'selected' : ''; ?>>Foster welcome</option>
                                        <option value="Adopted" <?php echo (!$isAdding && ($editPet['status'] ?? '') === 'Adopted') ? 'selected' : ''; ?>>Adopted</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Description Field -->
                        <div class="form-field">
                            <label class="form-label">Description</label>
                            <div style="background: var(--bg-card); border: 1px solid var(--border-light); border-radius: 10px; padding: 10px;">
                                <textarea name="description" rows="3" style="width: 100%; border: none; outline: none; background: transparent; font-size: 13px; resize: vertical;" placeholder="Personality, background, daily habits..."><?php echo $isAdding ? '' : htmlspecialchars($editPet['description'] ?? ''); ?></textarea>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div style="display: flex; gap: 10px; margin-top: 10px;">
                            <a href="dashboard.php" class="btn btn-secondary" style="flex: 1; height: 42px;">
                                Cancel
                            </a>
                            <button type="submit" name="save_pet" class="btn btn-primary" style="flex: 1; height: 42px;">
                                <?php echo $isAdding ? 'Add pet' : 'Save changes'; ?>
                            </button>
                        </div>
                    </form>
                </aside>
            </div>
        </main>
    </div>
</body>
</html>
