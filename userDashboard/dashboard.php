<?php
// comment
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
<body>
    Hello <?php echo $_SESSION["email"]; ?>
    <br>
    <?php if($_SESSION["role"]==="admin"){ ?>
    <a href="addpet.php">Add pet</a>
    <?php } ?>
    <table border="1">
        <tr>
            <th>Name</th>
            <th>Species</th>
            <th>Breed</th>
            <th>Age</th>
            <th>Sex</th>
            <th>Description</th>
            <th>Photo URL</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>

        <?php
        
        if(empty($pets)){
    
        ?>
        <tr>
            <td colspan="10">No available Pet.</td>
        </tr>
        <?php }else{
            foreach($pets as $pet){
        ?>
        <tr>
            <td><?php echo $pet["name"] ?></td>
            <td><?php echo $pet["species"] ?></td>
            <td><?php echo $pet["breed"] ?></td>
            <td><?php echo $pet["age"] ?></td>
            <td><?php echo $pet["sex"] ?></td>
            <td><?php echo $pet["description"] ?></td>
            <td><?php echo $pet["photo_url"] ?></td>
            <td><?php echo $pet["status"] ?></td>
            <td>
                <a href="viewbyid.php?id=<?php echo $pet["petid"] ?>">View</a>
                <?php if($_SESSION["role"]==="admin"){ ?>
                    <a href="delete.php?id=<?php echo $pet["petid"] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                <?php } ?>
            </td>
        </tr>
        <?php }} ?>
    </table>
    <br>
    <a href="../logout.php">Logout</a>
</body>
</html>
