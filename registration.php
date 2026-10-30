<?php
    if(isset($_POST['create'])){
        echo"user submitted";
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
    <form action="registration.php" method="POST">   
        <div class="container">
            <div class="row">
                <div class="col-sm-3">
                    <h1>Registration</h1>
                    <p>Fill up the form</p>

                    <label for="fullname"><b>Full Name</b></label>
                    <label for="">First Name, Last Name</label>
                    <input class="form-control" name="fullname" required>

                    <label for="password"><b>Password</b></label>
                    <input class="form-control" type="password" name="password" required>

                    <label for="email"><b>Email Address</b></label>
                    <input class="form-control" type="email" name="email" required>

                    <input type="submit" name="create" value="Sign Up">
                </div>
            </div>
        </div>
    </form>
</body>
</html>