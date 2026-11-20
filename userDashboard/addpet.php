<?php

session_start();

if(!isset($_SESSION["email"])){
    header("Location:../logout.php");
    exit();
}

require_once __DIR__."/../vendor/autoload.php";
use app\Models\Pet;
use app\upload;

$petModel=new Pet();
if(isset($_POST["submit"])){
    $name=$_POST["name"];
    $species=$_POST["species"];
    $breed=$_POST["breed"];
    $age=$_POST["age"];
    $sex=$_POST["sex"];
    $description=$_POST["description"];
    $photo_url=$_POST["photo_url"];
    $status=$_POST["status"];

    try{
        $petModel->insert($_SESSION["email"],$name,$species,$breed,$age,$sex,$description,$photo_url,$status);

        header("Location:dashboard.php");
        exit();
    }
    catch(Exception $e){
        echo $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Add Pet</h1>
    <form method="POST" enctype="multipart/form-data">
        Pet Name: <input type="text" name="name"><br>
        Species: <input type="text" name="species"><br>
        Breed: <input type="text" name="breed"><br>
        Age: <input type="number" name="age"><br>
        Sex: <input type="text" name="sex"><br>
        Description: <input type="text" name="description"><br>
        Photo: <input type="file" name="photo" accept="image/*"><br>
        Status: <input type="text" name="status"><br>
        <input type="submit" value="Add" name="submit">
    </form>
</body>
</html>