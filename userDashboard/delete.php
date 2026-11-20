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
use app\Models\Pet;
use app\upload;

$petModel=new Pet();
$id=$_GET["id"];
if(isset($_GET["id"])){
    try{
        $pet=$petModel->ViewbyID($id);
        $petModel->delete($id);
        if($pet) Upload::delete($pet["photo_url"]);
        header("Location:dashboard.php");
        exit();
    }
    catch(PDOException $e){
        echo $e->getMessage();
    }
}

?>