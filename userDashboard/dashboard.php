<?php

session_start();

if(!isset($_SESSION["email"])){
    header("Location:../logout.php");
    exit();
}

require_once __DIR__."/../vendor/autoload.php";
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
</head>
<body>
    Hello <?php echo $_SESSION["email"]; ?>
    <br>
    <?php if($_SESSION["role"]==="admin"){ ?>
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
            <th>Photo URL</th>
            <th>Status</th>
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
            <?php if($pet["photo_url"]){ ?>
                <img src="Images/<?php echo htmlspecialchars($pet["photo_url"]) ?>" width="80" alt="">
            <?php } else { echo "No photo"; } ?>
            </td>
            <td><?php echo $pet["status"] ?></td>
            <td>
                <?php if ($_SESSION["role"] === "admin") { ?>
                    <a href="viewbyid.php?id=<?php echo $pet["petid"] ?>">Edit</a>
                    <a href="delete.php?id=<?php echo $pet["petid"] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                <?php } else { ?>
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