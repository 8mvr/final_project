<?php
// comment
require_once __DIR__ . '/vendor/autoload.php';
use app\Models\Pet;

$petModel = new Pet();
$id = $_GET['id'] ?? 1;
$pet = $petModel->ViewbyID($id);

if (!$pet) {
    header("Location: pets.php");
    exit();
}

$allPets = $petModel->getAll();
$relatedPets = array_values(array_filter($allPets, fn($p) => $p['petid'] != $pet['petid']));
$relatedPets = array_slice($relatedPets, 0, 3);

$pageTitle = htmlspecialchars($pet['name']) . " · Pet Profile";
require_once __DIR__ . '/includes/header.php';
?>

<main class="section-wrap" style="padding-top: 30px;">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="pets.php" style="display: flex; align-items: center; gap: 6px;">
            <img src="assets/images/icon-arrow-left.svg" alt="" style="width: 14px; height: 14px;">
            <span>Find a pet</span>
        </a>
        <span>/</span>
        <span><?php echo htmlspecialchars($pet['species']); ?></span>
        <span>/</span>
        <span style="color: var(--text-heading); font-weight: 600;"><?php echo htmlspecialchars($pet['name']); ?></span>
    </div>

    <!-- Main Pet Intro Section -->
    <div class="profile-intro">
        <!-- Gallery Left -->
        <div class="profile-gallery">
            <div class="profile-portrait">
                <img src="<?php echo htmlspecialchars($pet['photo_url']); ?>" alt="<?php echo htmlspecialchars($pet['name']); ?>" onerror="this.src='assets/images/pet-maple.png'">
            </div>
            <div class="profile-gallery-note">
                <span><?php echo htmlspecialchars($pet['name']); ?>, enjoying the quiet moments in the shelter garden.</span>
                <div style="cursor: pointer;" title="Save to favorites">
                    <img src="assets/images/icon-heart.svg" alt="" style="width: 20px; height: 20px;">
                </div>
            </div>
        </div>

        <!-- Details Right -->
        <div class="profile-details">
            <div style="display: flex; gap: 8px;">
                <span class="badge badge-available"><?php echo htmlspecialchars($pet['status']); ?></span>
                <span class="badge badge-foster">Foster welcome</span>
            </div>

            <span class="section-eyebrow">A gentle companion</span>
            <h1 class="profile-name">Hello, I’m <?php echo htmlspecialchars($pet['name']); ?>.</h1>
            <p class="profile-facts">
                <?php echo htmlspecialchars($pet['breed']); ?> · <?php echo htmlspecialchars($pet['age']); ?> <?php echo $pet['age'] == 1 ? 'year' : 'years'; ?> · <?php echo htmlspecialchars($pet['sex']); ?>
            </p>

            <p class="profile-bio">
                <?php echo nl2br(htmlspecialchars($pet['description'])); ?>
            </p>

            <div class="tags-row">
                <span class="badge badge-available">Gentle</span>
                <span class="badge badge-available">People-loving</span>
                <span class="badge badge-available">Easygoing</span>
            </div>

            <div class="profile-actions">
                <a href="apply.php?id=<?php echo $pet['petid']; ?>&type=adopt" class="btn btn-primary" style="height: 52px; font-size: 15px;">
                    <span>Apply to adopt <?php echo htmlspecialchars($pet['name']); ?></span>
                    <img src="assets/images/icon-arrow-right.svg" alt="" style="width: 16px; height: 16px; filter: brightness(0) invert(1);">
                </a>

                <a href="apply.php?id=<?php echo $pet['petid']; ?>&type=foster" class="btn btn-secondary" style="height: 52px; font-size: 15px;">
                    Offer <?php echo htmlspecialchars($pet['name']); ?> a foster home
                </a>

                <p style="font-size: 11px; color: var(--text-muted); line-height: 1.5; margin-top: 4px;">
                    Applying starts a conversation, not a commitment. Our team reviews every application before planning a meet-and-greet.
                </p>
            </div>

            <div style="display: flex; align-items: center; gap: 9px; font-size: 13px; color: var(--text-muted); margin-top: 8px;">
                <img src="assets/images/icon-map-pin.svg" alt="" style="width: 17px; height: 17px;">
                <span>Hearth Portland · Visits by appointment</span>
            </div>
        </div>
    </div>

    <!-- Story and Next Steps -->
    <div style="display: flex; gap: 64px; align-items: flex-start; margin-top: 48px; border-top: 1px solid var(--border-light); padding-top: 48px;">
        <div style="flex: 1; display: flex; flex-direction: column; gap: 32px;">
            <div>
                <h2 style="font-family: var(--font-serif); font-size: 34px; color: var(--text-heading); margin-bottom: 14px;">
                    A little more about me
                </h2>
                <p style="font-size: 15px; color: var(--text-muted); line-height: 1.7;">
                    <?php echo htmlspecialchars($pet['name']); ?> takes a moment to get to know new places, but once settled, their affectionate and playful side shines. They like calm walks, a predictable daily routine, and curling up nearby while you read or work. Offer a little patience, and they will offer a lifetime of loyalty and warmth.
                </p>
            </div>

            <!-- Home Preferences -->
            <div>
                <h3 style="font-size: 18px; font-weight: 600; color: var(--text-heading); margin-bottom: 16px;">Home preferences</h3>
                <div class="pref-grid">
                    <div class="pref-card">
                        <img src="assets/images/icon-sun.svg" alt="" style="width: 24px; height: 24px;">
                        <h4 class="pref-title">Daily routine</h4>
                        <p class="pref-desc">Enjoys moderate daily walks and relaxed afternoons sleeping by a sunny window.</p>
                    </div>
                    <div class="pref-card">
                        <img src="assets/images/icon-house.svg" alt="" style="width: 24px; height: 24px;">
                        <h4 class="pref-title">Living space</h4>
                        <p class="pref-desc">Adapts well to houses or apartments with respectful, caring family members.</p>
                    </div>
                </div>
            </div>

            <!-- Team Note -->
            <div style="background: var(--bg-card); border: 1px solid var(--border-light); border-radius: 14px; padding: 24px;">
                <span class="section-eyebrow">A note from the shelter team</span>
                <p style="font-family: var(--font-serif); font-size: 18px; font-style: italic; color: var(--text-heading); line-height: 1.6; margin: 10px 0;">
                    "<?php echo htmlspecialchars($pet['name']); ?> is an absolute joy to care for. Whoever welcomes them will have a devoted companion for years to come."
                </p>
                <span style="font-size: 12px; color: var(--text-muted);">— Elena, Hearth Animal Care Specialist</span>
            </div>
        </div>

        <!-- Next Steps Card -->
        <div style="width: 440px; flex-shrink: 0;" class="timeline-card">
            <h3 style="font-family: var(--font-serif); font-size: 26px; color: var(--text-dark);">Next steps</h3>
            
            <div class="timeline-step">
                <span class="timeline-num">1</span>
                <div>
                    <h4 class="timeline-step-title">Submit your application</h4>
                    <p class="timeline-step-desc">Tell us a bit about your lifestyle, home, and schedule.</p>
                </div>
            </div>

            <div class="timeline-step">
                <span class="timeline-num">2</span>
                <div>
                    <h4 class="timeline-step-title">Match conversation</h4>
                    <p class="timeline-step-desc">A brief chat with our coordinator to answer questions.</p>
                </div>
            </div>

            <div class="timeline-step">
                <span class="timeline-num">3</span>
                <div>
                    <h4 class="timeline-step-title">Meet in person</h4>
                    <p class="timeline-step-desc">Spend quiet time together in our shelter meet-and-greet suites.</p>
                </div>
            </div>

            <a href="apply.php?id=<?php echo $pet['petid']; ?>&type=adopt" class="btn btn-primary" style="width: 100%; height: 50px;">
                Start application
            </a>
        </div>
    </div>

    <!-- Other Companions Grid -->
    <div style="margin-top: 64px; border-top: 1px solid var(--border-light); padding-top: 48px;">
        <div class="section-header">
            <div>
                <span class="section-eyebrow">Other companions</span>
                <h2 class="section-title">More friends ready to meet you</h2>
            </div>
            <a href="pets.php" class="btn btn-secondary btn-sm">
                View all pets
                <img src="assets/images/icon-arrow-right.svg" alt="" style="width: 14px; height: 14px;">
            </a>
        </div>

        <div class="pets-grid">
            <?php foreach ($relatedPets as $rel): ?>
                <div class="pet-card">
                    <div class="pet-card-image">
                        <img src="<?php echo htmlspecialchars($rel['photo_url']); ?>" alt="<?php echo htmlspecialchars($rel['name']); ?>" onerror="this.src='assets/images/pet-maple.png'">
                        <div class="pet-card-heart" title="Save to favorites">
                            <img src="assets/images/icon-heart.svg" alt="" style="width: 16px; height: 16px;">
                        </div>
                    </div>
                    <div class="pet-card-content">
                        <div class="pet-card-header">
                            <h3 class="pet-card-name"><?php echo htmlspecialchars($rel['name']); ?></h3>
                            <span class="badge badge-available"><?php echo htmlspecialchars($rel['status']); ?></span>
                        </div>
                        <p class="pet-card-facts">
                            <?php echo htmlspecialchars($rel['breed'] . ' · ' . $rel['age'] . ' yrs · ' . $rel['sex']); ?>
                        </p>
                        <div class="pet-card-footer">
                            <a href="pet-profile.php?id=<?php echo $rel['petid']; ?>" class="pet-card-link">
                                View profile
                                <img src="assets/images/icon-arrow-right.svg" alt="" style="width: 14px; height: 14px;">
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
