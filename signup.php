<?php
require_once "app/database.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $full_name = trim($_POST["full_name"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $role = "user";

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $created_at = date("Y-m-d H:i:s");

    try{
        $stmt = $pdo->prepare("INSERT INTO user (full_name, email, password, role, created_at) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$full_name, $email, $hashed_password, $role, $created_at]);
        
        echo "Registration successful! You can now login.";
    }
    catch(PDOException $e){
        if($e->getCode() == 23000){
            echo "Error: Email is already registered.";
        }else{
            echo "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <form action="signup.php" method="POST">   
        <div class="signup-container">
            <h1>SIGN UP</h1>
            <p>Fill up the form</p>

            <label for="full_name"><b>Full Name</b></label>
            <p>First Name and Last Name</p>
            <input type="text" name="full_name" required>

            <label for="email"><b>Email Address</b></label>
            <input type="email" name="email" required>

            <label for="password"><b>Password</b></label>
            <input type="password" name="password" required>
            <!-- <label for="password"><b>Password</b></label>
            <input type="password" name="password" required> -->

            <input type="submit" name="create" value="Create Account">
            <p class="login-link">Already have an account? <a href="login.php">Log In</a></p>
        </div>
    </form>
</body>
</html>