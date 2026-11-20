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

$petModel=new Pet();
$id=$_GET["id"];
if(isset($_GET["id"])){
    try{
        $petModel->delete($id);
        header("Location:dashboard.php");
    }
    catch(PDOException $e){
        echo $e->getMessage();
    }
}

?>