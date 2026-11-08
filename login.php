<?php

session_start();
require_once __DIR__."/vendor/autoload.php";
use app\Models\Authentication;

$message="";
if(isset($_POST["submit"])){
    $email=$_POST["email"];
    $password=$_POST["password"];

    $auth=new Authentication();
    $message=$auth->login($email, $password);
    if($message=="Login Successful"){
        header("Location:./userDashboard/dashboard.php");
        $_SESSION["email"]=$email;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <form action="login.php" method="POST">   
        <div class="login-container">
            <h1>WELCOME</h1>
            <p>Enter your Credentials</p>

            <label for="email"><b>Email Address</b></label>
            <input type="email" name="email" required>

            <label for="password"><b>Password</b></label>
            <input type="password" name="password" required>

            <input type="submit" name="submit" value="LogIn">
            <p class="signup-link">Don't have an account? <a href="signup.php">SignUp</a></p>

            <?php

            if($message!==""){
                echo"<p>$message</p>";
            }

            ?>
        </div>
    </form>
</body>
</html>