<?php

session_start();

if(!isset($_SESSION["email"])){
    header("Location:../auth/logout.php");
    exit();
}
if(($_SESSION["role"] ?? "")==="admin"){
    header("Location:admin.php");
    exit();
}

require_once __DIR__."/../vendor/autoload.php";
require_once __DIR__."/../includes/upload_photo.php";
use app\Models\Pet;

$pets=[];
$error="";
try{
    $petModel=new Pet();
    $pets=$petModel->getAll();
}
catch(PDOException $e){
    $error="We couldn't load the pets right now. Please try again in a moment.";
}

// Newest first (petid is auto-increment).
// usort($pets, function($a,$b){ return $b["petid"] <=> $a["petid"]; });

$q = trim((string)($_GET["q"] ?? ""));
$availInput = $_GET["avail"] ?? "all";
$ageInput = $_GET["age"] ?? "all";
$sexInput = $_GET["sex"] ?? "all";
$avail = in_array($availInput, ["all", "yes", "no"], true) ? $availInput : "all";
$age = in_array($ageInput, ["all", "under1", "1-5", "6plus"], true) ? $ageInput : "all";
$sex = in_array($sexInput, ["all", "female", "male"], true) ? $sexInput : "all";

$qLow = mb_strtolower($q);
$pets = array_values(array_filter($pets, function($pet) use ($qLow, $avail, $age, $sex) {
    $status = strtolower(trim($pet["status"]));
    $isAvailable = $status === "available";
    $petAge = (int)$pet["age"];
    $petAgeGroup = $petAge < 1 ? "under1" : ($petAge <= 5 ? "1-5" : "6plus");
    $petSex = strtolower(trim($pet["sex"]));
    $searchText = mb_strtolower($pet["name"] . " " . $pet["breed"] . " " . $pet["species"]);

    return ($qLow === "" || str_contains($searchText, $qLow))
        && ($avail === "all" || ($avail === "yes" ? $isAvailable : !$isAvailable))
        && ($age === "all" || $age === $petAgeGroup)
        && ($sex === "all" || $sex === $petSex);
}));

function e($value){
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

function age_label($age){
    $age=(int)$age;
    if($age<1) return "Under 1 year";
    return $age." ".($age===1 ? "year" : "years");
}

function age_group($age){
    $age=(int)$age;
    if($age<1) return "under1";
    if($age<=5) return "1-5";
    return "6plus";
}

$total=count($pets);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find a pet | Hearth</title>
    <link rel="stylesheet" href="../assets/css/pet.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
    <?php require __DIR__."/../includes/navbar.php"; ?>

    <main class="page dash">
        <section class="hero">
            <form class="search" id="searchForm" role="search" method="GET">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                <input type="hidden" name="avail" value="<?php echo e($avail) ?>">
                <input type="hidden" name="age" value="<?php echo e($age) ?>">
                <input type="hidden" name="sex" value="<?php echo e($sex) ?>">
                <input type="search" name="q" value="<?php echo e($q) ?>" placeholder="Search by name, breed, or a little of both" aria-label="Search pets" autocomplete="off">
                <button type="submit" class="search-btn" id="searchBtn">Search pets</button>
            </form>
        </section>

        <?php if($error!==""){ ?>
            <div class="notice"><?php echo e($error) ?></div>
        <?php } ?>

        <div class="layout">
            <!-- ---------- Filters ---------- -->
            <aside class="filters" aria-label="Filters">
                <div class="filters-head">
                    <h2>Filters</h2>
                </div>

                <fieldset class="f-group">
                    <legend>Availability</legend>
                    <form method="GET">
                        <input type="hidden" name="q" value="<?php echo e($q) ?>">
                        <input type="hidden" name="age" value="<?php echo e($age) ?>">
                        <input type="hidden" name="sex" value="<?php echo e($sex) ?>">
                        <button type="submit" name="avail" value="all" class="check filter-choice"><span class="box<?php echo $avail === "all" ? " active" : "" ?>"></span>All pets</button>
                        <button type="submit" name="avail" value="yes" class="check filter-choice"><span class="box<?php echo $avail === "yes" ? " active" : "" ?>"></span>Available</button>
                        <button type="submit" name="avail" value="no" class="check filter-choice"><span class="box<?php echo $avail === "no" ? " active" : "" ?>"></span>Not available</button>
                    </form>
                </fieldset>

                <fieldset class="f-group">
                    <legend>Age</legend>
                    <form method="GET">
                        <input type="hidden" name="q" value="<?php echo e($q) ?>">
                        <input type="hidden" name="avail" value="<?php echo e($avail) ?>">
                        <input type="hidden" name="sex" value="<?php echo e($sex) ?>">
                        <button type="submit" name="age" value="all" class="check filter-choice"><span class="box<?php echo $age === "all" ? " active" : "" ?>"></span>Any age</button>
                        <button type="submit" name="age" value="under1" class="check filter-choice"><span class="box<?php echo $age === "under1" ? " active" : "" ?>"></span>Under 1 year</button>
                        <button type="submit" name="age" value="1-5" class="check filter-choice"><span class="box<?php echo $age === "1-5" ? " active" : "" ?>"></span>1&ndash;5 years</button>
                        <button type="submit" name="age" value="6plus" class="check filter-choice"><span class="box<?php echo $age === "6plus" ? " active" : "" ?>"></span>6 years &amp; up</button>
                    </form>
                </fieldset>

                <fieldset class="f-group">
                    <legend>Sex</legend>
                    <form method="GET">
                        <input type="hidden" name="q" value="<?php echo e($q) ?>">
                        <input type="hidden" name="avail" value="<?php echo e($avail) ?>">
                        <input type="hidden" name="age" value="<?php echo e($age) ?>">
                        <button type="submit" name="sex" value="all" class="check filter-choice"><span class="box<?php echo $sex === "all" ? " active" : "" ?>"></span>Any</button>
                        <button type="submit" name="sex" value="female" class="check filter-choice"><span class="box<?php echo $sex === "female" ? " active" : "" ?>"></span>Female</button>
                        <button type="submit" name="sex" value="male" class="check filter-choice"><span class="box<?php echo $sex === "male" ? " active" : "" ?>"></span>Male</button>
                    </form>
                </fieldset>
            </aside>

            <!-- ---------- Results ---------- -->
            <section class="results">
                <div class="grid" id="petGrid">
                    <?php foreach($pets as $i=>$pet){
                        $src=photo_src($pet["photo_url"]);
                        $status=strtolower(trim($pet["status"]));
                        $isAvailable=($status==="available");
                        $search=strtolower($pet["name"]." ".$pet["breed"]." ".$pet["species"]);
                    ?>
                    <article class="card"
                        data-avail="<?php echo $isAvailable ? "yes" : "no" ?>"
                        data-agegroup="<?php echo age_group($pet["age"]) ?>"
                        data-sex="<?php echo e(strtolower(trim($pet["sex"]))) ?>"
                        data-search="<?php echo e($search) ?>">
                        <a class="card-link" href="viewbyid.php?id=<?php echo (int)$pet["petid"] ?>">
                            <div class="photo">
                                <?php if($src){ ?>
                                    <img src="<?php echo e($src) ?>" alt="<?php echo e($pet["name"]) ?>" loading="lazy">
                                <?php }else{ ?>
                                    <div class="no-photo">No photo</div>
                                <?php } ?>
                            </div>
                            <div class="card-body">
                                <div class="card-top">
                                    <h3><?php echo e($pet["name"]) ?></h3>
                                    <span class="pill <?php echo $isAvailable ? "" : "pill-foster" ?>"><?php echo $isAvailable ? "Available" : "Not available" ?></span>
                                </div>
                                <p class="meta"><?php echo e($pet["breed"]) ?> &middot; <?php echo e(age_label($pet["age"])) ?> &middot; <?php echo e($pet["sex"]) ?></p>
                                <p class="desc"><?php echo e($pet["description"]) ?></p>
                            </div>
                        </a>
                    </article>
                    <?php } ?>
                </div>

                <?php if(empty($pets)){ ?>
                <div class="empty" id="emptyState">
                    <h3>No pets match</h3>
                </div>
                <?php } ?>
            </section>
        </div>
    </main>

    <?php require __DIR__."/../includes/footer.php"; ?>

</body>
</html>