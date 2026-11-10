<?php

session_start();

if(!isset($_SESSION["email"])){
    header("Location:../logout.php");
    exit();
}

require_once __DIR__."/../vendor/autoload.php";
use app\Models\Pet;

$petModel=new Pet();
if(isset($_POST["update"])){
    $id=$_POST["id"];
    $name=$_POST["name"];
    $species=$_POST["species"];
    $breed=$_POST["breed"];
    $age=$_POST["age"];
    $sex=$_POST["sex"];
    $description=$_POST["description"];
    $photo_url=$_POST["photo_url"];
    $status=$_POST["status"];

    try{
        echo $petModel->update($name,$species,$breed,$age,$sex,$description,$photo_url,$status,$id);
        header("Location:dashboard.php");
    }
    catch(PDOException $e){
        echo $e->getMessage();
    }
}

?>