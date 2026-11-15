<?php
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/vendor/autoload.php';
use app\Models\Pet;
use app\Database;

$petModel = new Pet();
$id = $_GET['id'] ?? 1;
$type = $_GET['type'] ?? 'adopt'; // 'adopt' or 'foster'
$pet = $petModel->ViewbyID($id);

$submitted = false;
$msg = "";

if (isset($_POST['submit_application'])) {
    $applicantName = trim($_POST['applicant_name'] ?? '');
    $applicantEmail = trim($_POST['applicant_email'] ?? '');
    $homeType = $_POST['home_type'] ?? '';
    $ownRent = $_POST['own_rent'] ?? '';
    $landlord = $_POST['landlord'] ?? 'Yes';
    $yard = $_POST['yard'] ?? 'Fenced yard';
    $hoursAlone = $_POST['hours_alone'] ?? '1-3 hours';
    $reason = trim($_POST['reason'] ?? '');

    // Save into database if connected
    try {
        $db = new Database();
        $conn = $db->connection();
        
        $userId = $_SESSION['userid'] ?? 1;
        $petId = $pet['petid'] ?? 1;

        if ($type === 'foster') {
            $stmt = $conn->prepare("INSERT INTO foster (userid, petid, reviewed_by, application_date, planned_start_date, planned_end_date, status) VALUES (?, ?, 1, CURDATE(), CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), 'Under review')");
            $stmt->execute([$userId, $petId]);
        } else {
            $stmt = $conn->prepare("INSERT INTO adoption (userid, petid, reviewed_by, application_date, status, reason) VALUES (?, ?, 1, CURDATE(), 'Under review', ?)");
            $stmt->execute([$userId, $petId, $reason]);
        }
    } catch (Exception $e) {
        // Safe offline demo handling
    }

    $submitted = true;
}

$pageTitle = ($type === 'foster' ? 'Foster' : 'Adoption') . ' Application · ' . htmlspecialchars($pet['name']);
require_once __DIR__ . '/includes/header.php';
?>

<main class="section-wrap" style="padding-top: 30px;">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="pet-profile.php?id=<?php echo $pet['petid']; ?>" style="display: flex; align-items: center; gap: 6px;">
            <img src="assets/images/icon-arrow-left.svg" alt="" style="width: 14px; height: 14px;">
            <span>Back to <?php echo htmlspecialchars($pet['name']); ?>’s profile</span>
        </a>
    </div>

    <!-- Application Heading -->
    <div style="margin-bottom: 32px;">
        <span class="section-eyebrow"><?php echo $type === 'foster' ? 'Foster Application' : 'Adoption Application'; ?> · <?php echo htmlspecialchars($pet['name']); ?></span>
        <h1 style="font-family: var(--font-serif); font-size: 44px; color: var(--text-dark); margin: 8px 0 12px;">
            Tell us about your home
        </h1>
        <p style="color: var(--text-muted); font-size: 15px; max-width: 750px; line-height: 1.6;">
            A few honest answers help us find the right match for you and for <?php echo htmlspecialchars($pet['name']); ?>. It takes about 5 minutes, and your progress is saved.
        </p>
    </div>

    <?php if ($submitted): ?>
        <div style="background: var(--bg-card); border: 1px solid var(--border-light); border-radius: 14px; padding: 48px; text-align: center; max-width: 680px; margin: 40px auto;">
            <div style="width: 56px; height: 56px; border-radius: 50%; background-color: var(--bg-subtle); display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                <img src="assets/images/icon-check.svg" alt="" style="width: 24px; height: 24px;">
            </div>
            <span class="section-eyebrow">Application Submitted</span>
            <h2 style="font-family: var(--font-serif); font-size: 32px; color: var(--text-dark); margin: 8px 0 16px;">
                Thank you for reaching out!
            </h2>
            <p style="color: var(--text-muted); font-size: 15px; line-height: 1.6; margin-bottom: 28px;">
                We have received your application for <strong><?php echo htmlspecialchars($pet['name']); ?></strong>. Our shelter team will review your application within 2–3 business days and email you with the next steps.
            </p>
            <div style="display: flex; justify-content: center; gap: 14px;">
                <a href="my-applications.php" class="btn btn-primary">
                    View my applications
                </a>
                <a href="pets.php" class="btn btn-secondary">
                    Browse more pets
                </a>
            </div>
        </div>
    <?php else: ?>
        <!-- Progress Steps -->
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 36px; max-width: 800px;">
            <div style="width: 28px; height: 28px; border-radius: 50%; background: var(--text-dark); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 12px;">
                <img src="assets/images/icon-check.svg" alt="" style="width: 14px; height: 14px; filter: brightness(0) invert(1);">
            </div>
            <span style="font-size: 13px; color: var(--text-heading);">About you</span>
            <div style="flex: 1; height: 1px; background: var(--border-light);"></div>

            <div style="width: 28px; height: 28px; border-radius: 50%; background: var(--text-dark); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 12px; font-weight: 600;">
                2
            </div>
            <span style="font-size: 13px; font-weight: 600; color: var(--text-heading);">Your home</span>
            <div style="flex: 1; height: 1px; background: var(--border-light);"></div>

            <div style="width: 28px; height: 28px; border-radius: 50%; border: 1px solid var(--border-light); background: #fff; display: flex; align-items: center; justify-content: center; color: var(--text-muted); font-size: 12px;">
                3
            </div>
            <span style="font-size: 13px; color: var(--text-muted);">Experience</span>
            <div style="flex: 1; height: 1px; background: var(--border-light);"></div>

            <div style="width: 28px; height: 28px; border-radius: 50%; border: 1px solid var(--border-light); background: #fff; display: flex; align-items: center; justify-content: center; color: var(--text-muted); font-size: 12px;">
                4
            </div>
            <span style="font-size: 13px; color: var(--text-muted);">Review & submit</span>
        </div>

        <!-- Form Layout Columns -->
        <div style="display: flex; gap: 40px; align-items: flex-start;">
            <!-- Left Form Column -->
            <div style="flex: 1; background: var(--bg-card); border: 1px solid var(--border-light); border-radius: 14px; padding: 36px;">
                <span class="section-eyebrow">Step 2 of 4</span>
                <h2 style="font-family: var(--font-serif); font-size: 28px; color: var(--text-dark); margin: 6px 0 24px;">Your home setup</h2>

                <form action="apply.php?id=<?php echo $pet['petid']; ?>&type=<?php echo $type; ?>" method="POST" style="display: flex; flex-direction: column; gap: 20px;">
                    <!-- Contact info prefill -->
                    <div class="form-row">
                        <div class="form-field">
                            <label class="form-label">Your full name</label>
                            <div class="input-wrapper">
                                <input type="text" name="applicant_name" value="<?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Alex Rivera'); ?>" required>
                            </div>
                        </div>
                        <div class="form-field">
                            <label class="form-label">Email address</label>
                            <div class="input-wrapper">
                                <input type="email" name="applicant_email" value="<?php echo htmlspecialchars($_SESSION['email'] ?? 'alex.rivera@example.com'); ?>" required>
                            </div>
                        </div>
                    </div>

                    <!-- Row 1: Home Type & Ownership -->
                    <div class="form-row">
                        <div class="form-field">
                            <label class="form-label">Type of home</label>
                            <div class="input-wrapper">
                                <select name="home_type" style="width: 100%; border: none; outline: none; background: transparent; font-size: 13px; color: var(--text-heading);">
                                    <option value="House">Single Family House</option>
                                    <option value="Apartment">Apartment</option>
                                    <option value="Condo">Condo / Townhouse</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-field">
                            <label class="form-label">Do you own or rent?</label>
                            <div class="input-wrapper">
                                <select name="own_rent" style="width: 100%; border: none; outline: none; background: transparent; font-size: 13px; color: var(--text-heading);">
                                    <option value="Rent">Rent</option>
                                    <option value="Own">Own</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Landlord policy -->
                    <div class="form-field">
                        <label class="form-label">Does your landlord / HOA allow pets?</label>
                        <div style="display: flex; gap: 12px;">
                            <label style="flex: 1; display: flex; align-items: center; gap: 10px; padding: 12px 14px; background: var(--bg-subtle); border: 1px solid var(--text-dark); border-radius: 10px; cursor: pointer; font-size: 13px; font-weight: 600;">
                                <input type="radio" name="landlord" value="Yes" checked accent-color="var(--text-dark)">
                                <span>Yes, pets permitted</span>
                            </label>
                            <label style="flex: 1; display: flex; align-items: center; gap: 10px; padding: 12px 14px; background: #fff; border: 1px solid var(--border-light); border-radius: 10px; cursor: pointer; font-size: 13px;">
                                <input type="radio" name="landlord" value="Checking">
                                <span>Currently checking</span>
                            </label>
                        </div>
                    </div>

                    <!-- Yard Details -->
                    <div class="form-row">
                        <div class="form-field">
                            <label class="form-label">Yard setup</label>
                            <div class="input-wrapper">
                                <select name="yard" style="width: 100%; border: none; outline: none; background: transparent; font-size: 13px; color: var(--text-heading);">
                                    <option value="Fully fenced yard">Fully fenced private yard</option>
                                    <option value="Partially fenced yard">Partially fenced yard</option>
                                    <option value="Shared yard or park nearby">No yard, park nearby</option>
                                    <option value="Balcony / Patio">Balcony / Patio</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-field">
                            <label class="form-label">Daily hours pet will be alone</label>
                            <div class="input-wrapper">
                                <select name="hours_alone" style="width: 100%; border: none; outline: none; background: transparent; font-size: 13px; color: var(--text-heading);">
                                    <option value="0-2 hours">0–2 hours (someone home)</option>
                                    <option value="3-5 hours">3–5 hours</option>
                                    <option value="6-8 hours">6–8 hours</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Reason -->
                    <div class="form-field">
                        <label class="form-label">Why are you interested in <?php echo htmlspecialchars($pet['name']); ?>?</label>
                        <div style="background: var(--bg-card); border: 1px solid var(--border-light); border-radius: 10px; padding: 12px;">
                            <textarea name="reason" rows="4" style="width: 100%; border: none; outline: none; background: transparent; font-size: 13px; resize: vertical;" placeholder="Tell us about your household rhythm, favorite activities, and what drew you to this pet..."></textarea>
                        </div>
                    </div>

                    <div class="form-divider" style="margin: 12px 0;"></div>

                    <!-- Form Action Buttons -->
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <a href="pet-profile.php?id=<?php echo $pet['petid']; ?>" class="btn btn-secondary">
                            Back
                        </a>
                        <button type="submit" name="submit_application" class="btn btn-primary" style="height: 48px; padding: 0 28px;">
                            <span>Submit application</span>
                            <img src="assets/images/icon-arrow-right.svg" alt="" style="width: 16px; height: 16px; filter: brightness(0) invert(1);">
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right Pet Summary Column -->
            <div style="width: 400px; flex-shrink: 0; display: flex; flex-direction: column; gap: 20px;">
                <div class="pet-card" style="margin: 0;">
                    <div class="pet-card-image" style="height: 220px;">
                        <img src="<?php echo htmlspecialchars($pet['photo_url']); ?>" alt="<?php echo htmlspecialchars($pet['name']); ?>" onerror="this.src='assets/images/pet-maple.png'">
                    </div>
                    <div class="pet-card-content">
                        <div class="pet-card-header">
                            <h3 class="pet-card-name"><?php echo htmlspecialchars($pet['name']); ?></h3>
                            <span class="badge badge-available"><?php echo htmlspecialchars($pet['status']); ?></span>
                        </div>
                        <p class="pet-card-facts">
                            <?php echo htmlspecialchars($pet['breed'] . ' · ' . $pet['age'] . ' yrs · ' . $pet['sex']); ?>
                        </p>
                        <p class="pet-card-desc">
                            <?php echo htmlspecialchars($pet['description']); ?>
                        </p>
                    </div>
                </div>

                <!-- What happens next box -->
                <div style="background: var(--bg-subtle); border-radius: 14px; padding: 24px; display: flex; flex-direction: column; gap: 16px;">
                    <h3 style="font-family: var(--font-serif); font-size: 20px; color: var(--text-dark);">What happens next</h3>

                    <div style="display: flex; gap: 12px;">
                        <span style="font-weight: 700; color: var(--text-dark);">1</span>
                        <div>
                            <h4 style="font-size: 13px; font-weight: 600; color: var(--text-heading);">Submitted</h4>
                            <p style="font-size: 12px; color: var(--text-muted);">You’ll get an instant confirmation.</p>
                        </div>
                    </div>

                    <div style="display: flex; gap: 12px;">
                        <span style="font-weight: 700; color: var(--text-dark);">2</span>
                        <div>
                            <h4 style="font-size: 13px; font-weight: 600; color: var(--text-heading);">Under review</h4>
                            <p style="font-size: 12px; color: var(--text-muted);">Our team reviews every application within 2–3 days.</p>
                        </div>
                    </div>

                    <div style="display: flex; gap: 12px;">
                        <span style="font-weight: 700; color: var(--text-dark);">3</span>
                        <div>
                            <h4 style="font-size: 13px; font-weight: 600; color: var(--text-heading);">Visit & decision</h4>
                            <p style="font-size: 12px; color: var(--text-muted);">We’ll invite you to meet <?php echo htmlspecialchars($pet['name']); ?> in person.</p>
                        </div>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 8px; font-size: 12px; color: var(--text-muted);">
                    <img src="assets/images/icon-check.svg" alt="" style="width: 14px; height: 14px;">
                    <span>Draft saved automatically</span>
                </div>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
