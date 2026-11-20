<?php

session_start();

if(!isset($_SESSION["email"])){
    header("Location:../logout.php");
    exit();
}

require_once __DIR__."/../vendor/autoload.php";
require_once __DIR__."/upload_photo.php";
use app\Models\Pet;

$petModel=new Pet();
$pets=$petModel->getAll();
try{

}
catch(PDOException $e){
    echo $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Adoption and Foster Coordination</title>
    <link rel="stylesheet" href="../pet.css">
</head>
<body>
    Hello <?php echo $_SESSION["email"]; ?>
    <br>
    <?php if ($_SESSION["role"] == "admin") { ?>
    <a href="addpet.php">Add pet</a>
    <?php } ?>
    <table border="1">
        <tr>
            <th>Name</th>
            <th>Species</th>
            <th>Breed</th>
            <th>Age</th>
            <th>Sex</th>
            <th>Description</th>
            <th>Photo</th>
            <th>Status</th>
            <!-- <th>Created By</th> -->
            <th>Actions</th>
        </tr>

        <?php
        
        if(empty($pets)){
    
        ?>
        <tr>
            <td colspan="10">No available Pet.</td>
        </tr>
        <?php }else{
            foreach($pets as $pet){
        ?>
        <tr>
            <td><?php echo $pet["name"] ?></td>
            <td><?php echo $pet["species"] ?></td>
            <td><?php echo $pet["breed"] ?></td>
            <td><?php echo $pet["age"] ?></td>
            <td><?php echo $pet["sex"] ?></td>
            <td><?php echo $pet["description"] ?></td>
            <td>
                <?php $src = photo_src($pet["photo_url"]); ?>
                <?php if($src){ ?>
                    <img src="<?php echo htmlspecialchars($src) ?>" alt="<?php echo htmlspecialchars($pet["name"]) ?>" width="100" style="height:100px;object-fit:cover;">
                <?php }else{ ?>
                    No photo
                <?php } ?>
            </td>
            <td><?php echo $pet["status"] ?></td>
            <td>
                <?php if($_SESSION["role"]==="admin"){ ?>
                    <a href="viewbyid.php?id=<?php echo $pet["petid"] ?>">Edit</a>
                    <a href="delete.php?id=<?php echo $pet["petid"] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                <?php }else{ ?>
                    <a href="viewbyid.php?id=<?php echo $pet["petid"] ?>">View</a>
                <?php } ?>
            </td>
        </tr>
        <?php }} ?>
    </table>
    <br>
    <a href="../logout.php">Logout</a>
</body>
</html>