<?php

require_once __DIR__."/../vendor/autoload.php";
use app\Models\Authentication;

$message="";
$full_name="";
$email="";
if(isset($_POST["submit"])){
    $full_name=trim($_POST["full_name"] ?? "");
    $email=trim($_POST["email"] ?? "");
    $password=$_POST["password"] ?? "";
    $confirmPassword=$_POST["confirm_password"] ?? "";

    if($full_name === "" || $email === "" || $password === ""){
        $message="All fields are required.";
    }elseif($password !== $confirmPassword){
        $message="Passwords do not match.";
    }else{
        $auth=new Authentication();
        $message=$auth->signup($full_name, $email, $password);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>
    <form action="signup.php" method="POST">   
        <div class="signup-container">
            <h1>SIGN UP</h1>
            <p>Fill up the form</p>

            <label for="full_name"><b>Full Name</b></label>
            <p>First Name and Last Name</p>
            <input type="text" name="full_name" value="<?php echo htmlspecialchars($full_name, ENT_QUOTES, 'UTF-8'); ?>" autocomplete="name" required>

            <label for="email"><b>Email Address</b></label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" autocomplete="email" required>

            <label for="password"><b>Password</b></label>
            <input type="password" name="password" autocomplete="new-password" required>

            <label for="confirm_password"><b>Confirm Password</b></label>
            <input type="password" name="confirm_password" autocomplete="new-password" required>

            <input type="submit" name="submit" value="Create Account">
            <p class="login-link">Already have an account? <a href="login.php">Login</a></p>

            <?php

            if($message!==""){
                echo "<p role=\"alert\">" . htmlspecialchars($message, ENT_QUOTES, "UTF-8") . "</p>";
            }

            ?>
        </div>
    </form>
</body>
</html>