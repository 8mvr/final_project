<?php

session_start();

if(!isset($_SESSION["email"])){
    header("Location:../logout.php");
    exit();
}

require_once __DIR__."/../vendor/autoload.php";
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
</head>
<body>
    <h1>Update Pet</h1>
    <form action="update.php" method="POST" enctype="multipart/form-data">
        <fieldset <?php if($_SESSION["role"]!=="admin") echo "disabled"; ?> style="border:0;padding:0">
            <input type="hidden" name="id" value="<?php echo $pet["petid"] ?>">
            Pet Name: <input type="text" name="name" value="<?php echo $pet["name"] ?>"><br>
            Species: <input type="text" name="species" value="<?php echo $pet["species"] ?>"><br>
            Breed: <input type="text" name="breed" value="<?php echo $pet["breed"] ?>"><br>
            Age: <input type="number" name="age" value="<?php echo $pet["age"] ?>"><br>
            Sex: <select name="sex">
                <option value="Male" <?php if($pet["sex"]==="Male") echo "selected"; ?>>Male</option>
                <option value="Female" <?php if($pet["sex"]==="Female") echo "selected"; ?>>Female</option>
            </select><br>
            Description: <input type="text" name="description" value="<?php echo $pet["description"] ?>"><br>
            <?php if($pet["photo_url"]){ ?>
                <img src="Images/<?php echo htmlspecialchars($pet["photo_url"]) ?>" width="120" alt=""><br>
            <?php } ?>
            Photo: <input type="file" name="photo" accept="image/*"><br>
            Status: <select name="status">
                <?php foreach(["available","adopted","fostered"] as $s){ ?>
                    <option value="<?php echo $s ?>" <?php if($pet["status"]===$s) echo "selected"; ?>><?php echo ucfirst($s) ?></option>
                <?php } ?>
            </select><br>
            <?php if($_SESSION["role"]==="admin"){ ?>
                <input type="submit" value="Update" name="update">
            <?php } ?>
        </fieldset>
    </form>
</body>
</html>