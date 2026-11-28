<?php

session_start();

if(!isset($_SESSION["email"])){
    header("Location:../auth/logout.php");
    exit();
}
if(($_SESSION["role"] ?? "") !== "admin"){
    header("Location:dashboard.php");
    exit();
}

require_once __DIR__."/../vendor/autoload.php";

use app\Models\Authentication;

$auth = new Authentication();
$userid = (int)($_GET["id"] ?? 0);
$user = $userid > 0 ? $auth->findById($userid) : false;
$signedInUser = $auth->findByEmail($_SESSION["email"]);

if(!$user){
    header("Location:users.php?err=".urlencode("User not found."));
    exit();
}
if($signedInUser && (int)$signedInUser["userid"] === $userid){
    header("Location:users.php?err=".urlencode("You cannot delete your own account."));
    exit();
}

try{
    $auth->deleteUser($userid);
    header("Location:users.php?done=".urlencode("User deleted successfully."));
}catch(PDOException $ex){
    header("Location:users.php?err=".urlencode($ex->getMessage()));
}
exit();
