<?php

session_start();

if(!isset($_SESSION["email"])){
    header("Location:../logout.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Adoption and Foster Coordination</title>
</head>
<body>
    Hello <?php echo $_SESSION["email"]; ?>
    <br>
    <br>
    <a href="../logout.php">Logout</a>
</body>
</html>