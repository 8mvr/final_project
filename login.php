<?php
session_start();
require_once "database.php";

$error_message = "";

if($_SERVER["REQUEST_METHOD"] === "POST"){
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    try{
        $stmt = $pdo->prepare("SELECT * FROM user WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if($user && password_verify($password, $user['password'])){

            $_SESSION['userid'] = $user['userid'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            header("Location: dashboard.php");
            exit();
        }
        else{
            $error_message = "Invalid email or password.";
        }
    }catch(PDOException $e){
        $error_message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
</head>
<body>
    <form action="login.php" method="POST">   
        <div class="container mt-5">
            <div class="row">
                <div class="col-sm-4 offset-sm-4">
                    <h1>Login</h1>
                    <p>Enter your credentials</p>

                    <hr class="mb-3">

                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo htmlspecialchars($error_message); ?>
                        </div>
                    <?php endif; ?>

                    <label for="email"><b>Email Address</b></label>
                    <input class="form-control mb-3" type="email" name="email" required>

                    <label for="password"><b>Password</b></label>
                    <input class="form-control mb-3" type="password" name="password" required>

                    <hr class="mb-3">
                    <input class="btn btn-primary w-100" type="submit" name="login" value="Log In">
                    
                    <p class="mt-3 text-center">Don't have an account? <a href="signup.php">Sign Up</a></p>
                </div>
            </div>
        </div>
    </form>
</body>
</html>