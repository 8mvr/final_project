<?php

$db_server = "localhost";
$db_user = "root";
$db_pass = "maverick";
$db_name = "petadoptiondb";
$conn = "";

try{
    $conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);
}
catch(mysqli_sql_exception){
    // echo"couldn't connect <br>";
}
if($conn){
    // echo"connected <br>";
}

?>