<?php

session_start();

if(!isset($_SESSION["email"])){
    header("Location:../auth/logout.php");
    exit();
}

require_once __DIR__."/../vendor/autoload.php";
require_once __DIR__."/../includes/upload_photo.php";
use app\Models\Pet;

$petModel=new Pet();
$id = isset($_GET["id"])? (int)$_GET["id"] : 0;
if ($id <= 0) {
    header("Location:admin.php");
    exit();
}
try {
    $pet = $petModel->ViewbyID($id);
}
catch (PDOException $e) {
    $pet = null;
}
if (!$pet) {
    header("Location:admin.php?err=" . urlencode("Pet not found."));
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Pet</title>
    <link rel="stylesheet" href="../assets/css/pet.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    
<?php if ($_SESSION["role"] === "admin") { ?>

    <!-- ADMIN EDIT FORM -->
    <div class="admin-layout">
    <?php require __DIR__."/../includes/admin_navbar.php"; ?>
    <main class="page">
        <div class="page-head">
            <h1>Edit pet</h1>
            <p>Update <?php echo htmlspecialchars($pet["name"]) ?>'s details. Changes are visible to users right away.</p>
        </div>

        <form class="pet-form" action="update.php" method="POST" enctype="multipart/form-data" autocomplete="off">
            <input type="hidden" name="id" value="<?php echo $pet["petid"] ?>">

            <div class="field-row">
                <div class="field">
                    <label for="name">Pet name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($pet["name"]) ?>" required>
                </div>
                <div class="field">
                    <label for="species">Species</label>
                    <input type="text" id="species" name="species" value="<?php echo htmlspecialchars($pet["species"]) ?>" required>
                </div>
            </div>

            <div class="field-row">
                <div class="field">
                    <label for="breed">Breed</label>
                    <input type="text" id="breed" name="breed" value="<?php echo htmlspecialchars($pet["breed"]) ?>">
                </div>
                <div class="field">
                    <label for="age">Age (years)</label>
                    <input type="number" id="age" name="age" min="0" value="<?php echo (int)$pet["age"] ?>">
                </div>
            </div>

            <div class="field-row">
                <div class="field">
                    <label for="sex">Sex</label>
                    <select id="sex" name="sex">
                        <option value="Male" <?php if($pet["sex"]==="Male") echo "selected"; ?>>Male</option>
                        <option value="Female" <?php if($pet["sex"]==="Female") echo "selected"; ?>>Female</option>
                    </select>
                </div>
                <div class="field">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="Available" <?php if(strcasecmp($pet["status"],"Available")===0) echo "selected"; ?>>Available</option>
                        <option value="Foster" <?php if(strcasecmp($pet["status"],"Foster")===0) echo "selected"; ?>>Foster</option>
                        <option value="Not Available" <?php if(strcasecmp($pet["status"],"Not Available")===0) echo "selected"; ?>>Not Available</option>
                    </select>
                </div>
            </div>

            <div class="field">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($pet["description"]) ?></textarea>
            </div>

            <?php $src = photo_src($pet["photo_url"]); ?>
            <div class="field">
                <label>Current photo</label>
                <?php if($src){ ?>
                    <img class="edit-photo" src="<?php echo htmlspecialchars($src) ?>" alt="">
                <?php }else{ ?>
                    <p class="hint">No photo yet.</p>
                <?php } ?>
                <label for="photo" style="margin-top:10px">Replace photo</label>
                <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/gif,image/webp">
                <small class="hint">Leave empty to keep the current photo · JPG, PNG, GIF, WEBP · max 5 MB</small>
            </div>

            <div class="form-actions">
                <input type="submit" value="Save changes" name="update">
                <a class="btn-cancel" href="admin.php">Cancel</a>
            </div>
        </form>
    </main>
    </div>

<?php } else { ?>

    <!-- PET PROFILE FOR REGULAR USERS -->
     <?php require __DIR__."/../includes/navbar.php"; ?>
    <div class="pd">

        <?php $src = photo_src($pet["photo_url"]); ?>

        <section class="pd-hero">

            <figure class="pd-photo">
                <div class="pd-photo-frame">
                    <?php if($src) { ?>
                        <img src="<?php echo htmlspecialchars($src); ?>"
                             alt="<?php echo htmlspecialchars($pet["name"]); ?>">
                    <?php } ?>
                </div>
            </figure>

            <div class="pd-info">

                <p class="pd-eyebrow">MEET YOUR NEW FRIEND</p>

                <h1>Hello, I'm <?php echo htmlspecialchars($pet["name"]); ?>.</h1>

                <p class="pd-meta">
                    <?php echo htmlspecialchars($pet["breed"]); ?>
                    · <?php echo htmlspecialchars($pet["age"]); ?>
                    <?php echo (int)$pet["age"] === 1 ? "year old" : "years old"; ?>
                    · <?php echo htmlspecialchars($pet["sex"]); ?>
                </p>

                <div class="pd-badges">
                    <span class="pd-badge <?php echo strtolower($pet["status"]) === "available" ? "pd-badge-green" : "pd-badge-grey"; ?>">
                        <?php echo htmlspecialchars($pet["status"]); ?>
                    </span>
                </div>

                <?php if(strtolower($pet["status"]) === "available") { ?>
                    <div class="pd-actions">
                        <a class="pd-btn pd-btn-primary" href="adoption_application.php?petid=<?php echo (int)$pet["petid"]; ?>">Apply to adopt</a>
                        <a class="pd-btn pd-btn-secondary" href="foster_application.php?petid=<?php echo (int)$pet["petid"]; ?>">Offer a foster home</a>
                    </div>
                <?php } else { ?>
                    <p class="pd-unavailable">This pet is currently unavailable.</p>
                <?php } ?>

            </div>
        </section>

        <section class="pd-lower">
            <div>
                <h2>A little more about me</h2>
                <p><?php echo htmlspecialchars($pet["description"]); ?></p>
            </div>
        </section>

    </div>
    <br>

<?php } ?>
</main>
    <?php if ($_SESSION["role"] === "admin") {
        // require __DIR__."/../includes/footer.php";
    }else{ 
        require __DIR__."/../includes/footer.php";
    } ?>
    
</body>
</html>