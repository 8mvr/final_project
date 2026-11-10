<?php

session_start();

if(!isset($_SESSION["email"])){
    header("Location:../logout.php");
    exit();
}

require_once __DIR__."/../vendor/autoload.php";
use app\Models\Pet;

$petModel=new Pet();
$id=$_GET["id"];
if(isset($_GET["id"])){
    try{
        $pet=$petModel->ViewbyID($id);
    }
    catch(PDOException $e){
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
    <form action="update.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $pet["petid"] ?>">
        Pet Name: <input type="text" name="name" value="<?php echo $pet["name"] ?>"><br>
        Species: <input type="text" name="species" value="<?php echo $pet["species"] ?>"><br>
        Breed: <input type="text" name="breed" value="<?php echo $pet["breed"] ?>"><br>
        Age: <input type="number" name="age" value="<?php echo $pet["age"] ?>"><br>
        Sex: <input type="text" name="sex" value="<?php echo $pet["sex"] ?>"><br>
        Description: <input type="text" name="description" value="<?php echo $pet["description"] ?>"><br>
        Photo_url: <input type="text" name="photo_url" value="<?php echo $pet["photo_url"] ?>"><br>
        Status: <input type="text" name="status" value="<?php echo $pet["status"] ?>"><br>
        <input type="submit" value="Update" name="update">
    </form>
</body>
</html>