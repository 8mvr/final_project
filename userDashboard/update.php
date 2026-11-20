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
if(isset($_POST["update"])){
    $id=$_POST["id"];
    $name=$_POST["name"];
    $species=$_POST["species"];
    $breed=$_POST["breed"];
    $age=$_POST["age"];
    $sex=$_POST["sex"];
    $description=$_POST["description"];
    // $photo_url=$_POST["photo_url"];
    $status=$_POST["status"];

    try{
        $old=$petModel->ViewbyID($id);
        $new=Upload::save($_FILES["photo"]);
        $photo_url=$new ?? $old["photo_url"];   // keep old photo if none chosen

        $petModel->update($name,$species,$breed,$age,$sex,$description,$photo_url,$status,$id);
        if($new) Upload::delete($old["photo_url"]);   // remove the replaced file
        header("Location:dashboard.php");
        exit();
    }
    catch(PDOException $e){
        echo $e->getMessage();
    }
}

?>