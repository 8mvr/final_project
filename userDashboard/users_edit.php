<?php

session_start();

if(!isset($_SESSION["email"])){
    header("Location:../auth/logout.php");
    exit();
}
if(($_SESSION["role"] ?? "") !== "admin"){
    header("Location:dashboard.php");
    exit();
}

require_once __DIR__."/../vendor/autoload.php";

use app\Models\Authentication;

function e($value){
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

$auth = new Authentication();
$userid = (int)($_GET["id"] ?? $_POST["userid"] ?? 0);
$user = $userid > 0 ? $auth->findById($userid) : false;
$error = "";

if(!$user){
    header("Location:users.php?err=".urlencode("User not found."));
    exit();
}

if(($_POST["action"] ?? "") === "update_user"){
    $fullName = trim($_POST["full_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $role = $_POST["role"] ?? "user";

    if($fullName === "" || $email === ""){
        $error = "Full name and email are required.";
    }elseif(filter_var($email, FILTER_VALIDATE_EMAIL) === false){
        $error = "Enter a valid email address.";
    }elseif(!$auth->updateUser($userid, $fullName, $email, $role)){
        $error = "The email is already in use or the role is invalid.";
    }else{
        header("Location:users.php?done=".urlencode("User updated successfully."));
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit user · Hearth</title>
    <link rel="stylesheet" href="../assets/css/pet.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="admin-layout">
    <?php require __DIR__."/../includes/admin_navbar.php"; ?>
    <main class="page">
        <div class="page-head">
            <h1>Edit user</h1>
            <p>Update account details and access level.</p>
        </div>

        <?php if($error !== ""){ ?><p class="notice" role="alert"><?php echo e($error); ?></p><?php } ?>

        <form class="pet-form" method="POST">
            <input type="hidden" name="action" value="update_user">
            <input type="hidden" name="userid" value="<?php echo (int)$userid; ?>">

            <div class="field">
                <label for="full_name">Full name</label>
                <input type="text" id="full_name" name="full_name" value="<?php echo e($_POST["full_name"] ?? $user["full_name"]); ?>" required>
            </div>

            <div class="field">
                <label for="email">Email address</label>
                <input type="email" id="email" name="email" value="<?php echo e($_POST["email"] ?? $user["email"]); ?>" required>
            </div>

            <div class="field">
                <label for="role">Role</label>
                <select id="role" name="role">
                    <option value="admin" <?php echo (($_POST["role"] ?? $user["role"]) === "admin") ? "selected" : ""; ?>>Administrator</option>
                    <option value="user" <?php echo (($_POST["role"] ?? $user["role"]) === "user") ? "selected" : ""; ?>>User</option>
                </select>
            </div>

            <div class="form-actions">
                <input type="submit" value="Save changes">
                <a class="btn-cancel" href="users.php">Cancel</a>
            </div>
        </form>
    </main>
</div>
</body>
</html>
