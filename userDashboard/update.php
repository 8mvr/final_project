<?php

session_start();

if(!isset($_SESSION["email"])){
    header("Location:../auth/logout.php");
    exit();
}
if(($_SESSION["role"] ?? "")!=="admin"){
    header("Location:dashboard.php");
    exit();
}

require_once __DIR__."/../vendor/autoload.php";
require_once __DIR__."/../includes/upload_photo.php";
use app\Models\Pet;

$petModel=new Pet();
if(isset($_POST["update"])){
    $id=(int)($_POST["id"] ?? 0);
    $name=trim($_POST["name"] ?? "");
    $species=trim($_POST["species"] ?? "");
    $breed=trim($_POST["breed"] ?? "");
    $age=(int)($_POST["age"] ?? 0);
    $sex=$_POST["sex"] ?? "Male";
    $description=trim($_POST["description"] ?? "");
    $status=$_POST["status"] ?? "Available";

    if($id <= 0 || $name === "" || $species === ""){
        header("Location:admin.php?err=" . urlencode("Pet name and species are required."));
        exit();
    }

    try{
        // New upload replaces the photo; otherwise keep the existing one
        $photo_url=save_uploaded_photo();
        if($photo_url===null){
            $existing=$petModel->ViewbyID($id);
            $photo_url=$existing["photo_url"] ?? "";
        }
        $petModel->update($name,$species,$breed,$age,$sex,$description,$photo_url,$status,$id);
        header("Location:admin.php?updated=1");
    }
    catch(PDOException | RuntimeException $e){
        echo $e->getMessage();
    }
}

?>