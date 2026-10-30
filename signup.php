<?php
require_once "database.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullname = trim($_POST["fullname"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $role = "user";
    
    $createdat = date("Y-m-d H:i:s");

    try{
        $stmt = $pdo->prepare("INSERT INTO user (fullname, email, password, role, createdat) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$fullname, $email, $password, $role, $createdat]);
        
        echo "Registration successful! You can now login.";
    }
    catch(PDOException $e){
        if($e->getCode() == 23000){
            echo "Error: Email is already registered.";
        }
        else{
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
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
</head>
<body>
    <form action="" method="POST">   
        <div class="container mt-5">
            <div class="row">
                <div class="col-sm-4 offset-sm-4">
                    <h1>Registration</h1>
                    <p>Fill up the form</p>

                    <hr class="mb-3">
                    <label for="fullname"><b>Full Name</b></label>
                    <p>First Name and Last Name</p>
                    <input class="form-control" name="fullname" required>

                    <label for="password"><b>Password</b></label>
                    <input class="form-control" type="password" name="password" required>

                    <label for="email"><b>Email Address</b></label>
                    <input class="form-control" type="email" name="email" required>

                    <hr class="mb-3">
                    <input class="btn btn-primary w-100" type="submit" name="create" value="Sign Up">
                    <p class="mt-3 text-center">Already have an account? <a href="login.php">Log In</a></p>
                </div>
            </div>
        </div>
    </form>
</body>
</html>