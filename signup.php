<?php

require_once __DIR__."/vendor/autoload.php";
use app\Models\Authentication;

$message="";
if(isset($_POST["submit"])){
    $full_name=$_POST["full_name"];
    $email=$_POST["email"];
    $password=$_POST["password"];

    $auth=new Authentication();
    $message=$auth->signup($full_name, $email, $password);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <!-- <link rel="stylesheet" href="style.css"> -->
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

            <input type="submit" name="submit" value="Create Account">
            <p class="login-link">Already have an account? <a href="login.php">Login</a></p>

            <?php

            if($message!==""){
                echo"<p>$message</p>";
            }

            ?>
        </div>
    </form>
</body>
</html>