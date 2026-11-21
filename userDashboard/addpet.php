<?php

session_start();

if(!isset($_SESSION["email"])){
    header("Location:../auth/logout.php");
    exit();
}
if($_SESSION["role"]!=="admin"){
    header("Location:dashboard.php");
    exit();
}

require_once __DIR__."/../vendor/autoload.php";
require_once __DIR__."/../includes/upload_photo.php";
use app\Models\Pet;

$petModel=new Pet();
$error="";
if(isset($_POST["submit"])){
    $name=$_POST["name"];
    $species=$_POST["species"];
    $breed=$_POST["breed"];
    $age=$_POST["age"];
    $sex=$_POST["sex"];
    $description=$_POST["description"];
    $status=$_POST["status"];

    try{
        $photo_url=save_uploaded_photo() ?? "";
        $petModel->insert($_SESSION["email"],$name,$species,$breed,$age,$sex,$description,$photo_url,$status);

        header("Location:admin.php?page=pets");
    }
    catch(PDOException | RuntimeException $e){
        $error=$e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add a pet · Hearth</title>
    <link rel="stylesheet" href="../assets/css/pet.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="admin-layout">
    <?php require __DIR__."/../includes/admin_navbar.php"; ?>
    <main class="page">
        <div class="page-head">
            <h1>Add a pet</h1>
        </div>

        <?php if($error !== ""){ ?>
            <p class="notice"><?php echo htmlspecialchars($error) ?></p>
        <?php } ?>

        <form class="pet-form" method="POST" enctype="multipart/form-data">
            <div class="field">
                <label for="name">Pet name</label>
                <input type="text" id="name" name="name" placeholder="e.g. Biscuit" required>
            </div>

            <div class="field-row">
                <div class="field">
                    <label for="species">Species</label>
                    <input type="text" id="species" name="species" placeholder="Dog, Cat…" required>
                </div>
                <div class="field">
                    <label for="breed">Breed</label>
                    <input type="text" id="breed" name="breed" placeholder="e.g. Aspin">
                </div>
            </div>

            <div class="field-row">
                <div class="field">
                    <label for="age">Age (years)</label>
                    <input type="number" id="age" name="age" min="0" placeholder="0">
                </div>
                <div class="field">
                    <label for="sex">Sex</label>
                    <select id="sex" name="sex">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
            </div>

            <div class="field">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4" placeholder="Personality, health notes, what kind of home suits them…"></textarea>
            </div>

            <div class="field">
                <label for="photo">Photo</label>
                <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/gif,image/webp">
                <small class="hint">JPG, PNG, GIF or WEBP · max 5 MB</small>
            </div>

            <div class="field">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="available">Available</option>
                    <option value="foster">Foster</option>
                </select>
            </div>

            <div class="form-actions">
                <input type="submit" value="Add pet" name="submit">
                <a class="btn-cancel" href="admin.php">Cancel</a>
            </div>
        </form>
    </main>
</div>
    <?php require __DIR__."/../includes/footer.php"; ?>
</body>
</html>
