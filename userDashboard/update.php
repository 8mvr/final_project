<?php

session_start();

if($_SESSION["role"]!=="admin"){
    header("Location:dashboard.php");
    exit();
}
if(!isset($_SESSION["email"])){
    header("Location:../logout.php");
    exit();
}

require_once __DIR__."/../vendor/autoload.php";
require_once __DIR__."/upload_photo.php";
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
    $status=$_POST["status"];

    try{
        // New upload replaces the photo; otherwise keep the existing one
        $photo_url=save_uploaded_photo();
        if($photo_url===null){
            $existing=$petModel->ViewbyID($id);
            $photo_url=$existing["photo_url"] ?? "";
        }
        $petModel->update($name,$species,$breed,$age,$sex,$description,$photo_url,$status,$id);
        header("Location:dashboard.php");
    }
    catch(PDOException | RuntimeException $e){
        echo $e->getMessage();
    }
}

?>