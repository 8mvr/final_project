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
$message = "";
$messageType = "";

try{
    $users = $auth->getAllUsers();
}catch(PDOException $ex){
    $users = [];
    $message = $ex->getMessage();
    $messageType = "notice";
}

$q = trim((string)($_GET["q"] ?? ""));
$filteredUsers = array_values(array_filter($users, function($user) use ($q){
    $haystack = strtolower($user["full_name"]." ".$user["email"]);
    return $q === "" || str_contains($haystack, strtolower($q));
}));

$fullName = $_SESSION["email"];
$counts = ["total" => count($users), "admin" => 0, "user" => 0];
foreach($users as $user){
    if(strcasecmp($user["email"], $_SESSION["email"]) === 0){
        $fullName = $user["full_name"] ?: $_SESSION["email"];
    }

    $role = strtolower(trim($user["role"]));
    $counts[in_array($role, ["admin", "user"], true) ? $role : "user"]++;
}
$firstName = explode(" ", trim($fullName))[0];
$hour = (int)date("G");
$greeting = $hour < 12 ? "Good morning" : ($hour < 18 ? "Good afternoon" : "Good evening");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User management · Hearth</title>
    <link rel="stylesheet" href="../assets/css/pet.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="admin-layout">
    <?php require __DIR__."/../includes/admin_navbar.php"; ?>
    <main class="main users-main">
        <header class="top">
            <div>
                <h1><?php echo e($greeting); ?>, <?php echo e(strtoupper($firstName)); ?></h1>
            </div>
        </header>

        <?php if(isset($_GET["done"])){ ?><p class="okmsg"><?php echo e($_GET["done"]); ?></p><?php } ?>
        <?php if(isset($_GET["err"])){ ?><p class="notice"><?php echo e($_GET["err"]); ?></p><?php } ?>
        <?php if($message !== ""){ ?><p class="<?php echo e($messageType); ?>"><?php echo e($message); ?></p><?php } ?>

        <section class="stats users-stats" aria-label="User account summary">
            <div class="stat"><span>Total users</span><b><?php echo $counts["total"]; ?></b></div>
            <div class="stat"><span>Administrators</span><b><?php echo $counts["admin"]; ?></b></div>
            <div class="stat"><span>Regular users</span><b><?php echo $counts["user"]; ?></b></div>
        </section>

        <section class="card users-card">
            <div class="card-head">
                <h2>Manage all users</h2>
            </div>

            <form class="users-toolbar" method="GET">
                <label class="users-search">
                    <span class="sr-only">Search by name or email</span>
                    <input type="search" name="q" value="<?php echo e($q); ?>" placeholder="Search by name or email">
                </label>
                <button type="submit" class="btn-dark btn-sm">Apply</button>
            </form>

            <div class="table-wrap users-table-wrap">
                <table class="admin-table users-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if(empty($filteredUsers)){ ?>
                        <tr><td colspan="4" class="empty">No users match your filters.</td></tr>
                    <?php }else{ foreach($filteredUsers as $user){
                        $role = strtolower(trim($user["role"]));
                        $role = in_array($role, ["admin", "user"], true) ? $role : "user";
                    ?>
                        <tr>
                            <td><strong><?php echo e($user["full_name"]); ?></strong></td>
                            <td><?php echo e($user["email"]); ?></td>
                            <td><span class="badge <?php echo $role === "admin" ? "" : "foster"; ?>"><?php echo $role === "admin" ? "Administrator" : "User"; ?></span></td>
                            <td>
                                <div class="user-actions">
                                    <a class="btn-light btn-sm" href="users_edit.php?id=<?php echo (int)$user["userid"]; ?>">Edit</a>
                                    <a class="btn-light danger btn-sm" href="users_delete.php?id=<?php echo (int)$user["userid"]; ?>" onclick="return confirm('Delete <?php echo e($user["full_name"]); ?>? This cannot be undone.')">Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php }} ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>
</body>
</html>