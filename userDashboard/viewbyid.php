<?php

session_start();

if(!isset($_SESSION["email"])){
    header("Location:../logout.php");
    exit();
}

require_once __DIR__."/../vendor/autoload.php";
require_once __DIR__."/upload_photo.php";
use app\Models\Pet;

$petModel=new Pet();
$id = $_GET["id"] ?? null;
if (isset($id)) {
    try {
        $pet = $petModel->ViewbyID($id);
    }
    catch (PDOException $e) {
        echo $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Pet</title>
    <link rel="stylesheet" href="../pets.css">
</head>
<body>
    <h1><?php echo $_SESSION["role"]==="admin" ? "Edit Pet" : "View Pet"; ?></h1>
    <form action="update.php" method="POST" enctype="multipart/form-data">
        <fieldset <?php if($_SESSION["role"]!=="admin") echo "disabled"; ?> style="border:0;padding:0">
            <input type="hidden" name="id" value="<?php echo $pet["petid"] ?>">
            Pet Name: <input type="text" name="name" value="<?php echo $pet["name"] ?>"><br>
            Species: <input type="text" name="species" value="<?php echo $pet["species"] ?>"><br>
            Breed: <input type="text" name="breed" value="<?php echo $pet["breed"] ?>"><br>
            Age: <input type="number" name="age" value="<?php echo $pet["age"] ?>"><br>
            Sex: <select name="sex">
                <option value="male" <?php if($pet["sex"]==="Male") echo "selected"; ?>>Male</option>
                <option value="female" <?php if($pet["sex"]==="Female") echo "selected"; ?>>Female</option>
            </select><br>
            Description: <input type="text" name="description" value="<?php echo $pet["description"] ?>"><br>
            <?php $src = photo_src($pet["photo_url"]); ?>
            <?php if($src){ ?>
                <img src="<?php echo htmlspecialchars($src) ?>" alt="<?php echo htmlspecialchars($pet["name"]) ?>" width="200"><br>
            <?php } ?>
            Photo: <input type="file" name="photo" accept="image/jpeg,image/png,image/gif,image/webp"><br>
            Status: <select name="status">
                <option value="Available" <?php echo $pet["status"] ?>>Available</option>
                <option value="Foster" <?php echo $pet["status"] ?>>Foster</option>
                <option value="Not Available" <?php echo $pet["status"] ?>>Not Available</option>
            </select><br>
            <?php if($_SESSION["role"]==="admin"){ ?>
                <input type="submit" value="Update" name="update">
            <?php } ?>
        </fieldset>
    </form>
</body>
</html>