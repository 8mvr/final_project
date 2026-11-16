<?php
// comment
require_once __DIR__ . '/vendor/autoload.php';
use app\Models\Pet;

$pageTitle = "Good Company. A Fresh Start.";
require_once __DIR__ . '/includes/header.php';

$petModel = new Pet();
$pets = $petModel->getAll();
// Take first 6 for homepage
$featuredPets = array_slice($pets, 0, 6);
?>

<main>
    <!-- Welcome Hero -->
    <section class="hero-section">
        <div class="hero-content">
            <span class="hero-eyebrow">Good company. A fresh start.</span>
            <h1 class="hero-title">Their next chapter starts with you.</h1>
            <p class="hero-desc">
                A familiar face at the door. A little more love at home. Meet the pets ready to become part of your everyday.
            </p>
            <div class="hero-actions">
                <a href="pets.php" class="btn btn-primary">
                    Find your companion
                    <img src="assets/images/icon-arrow-up-right.svg" alt="" style="width: 16px; height: 16px; filter: brightness(0) invert(1);">
                </a>
                <a href="#foster" class="btn btn-secondary">
                    Explore fostering
                </a>
            </div>
            <div class="hero-reassurance">
                <img src="assets/images/icon-heart-handshake.svg" alt="" style="width: 18px; height: 18px;">
                <span>Thoughtful matches. Real people. Support along the way.</span>
            </div>
        </div>

        <div class="hero-image-wrap">
            <a href="pet-profile.php?id=1">
                <img src="assets/images/hero-maple.png" alt="Maple Beagle" class="hero-portrait">
            </a>

            <div class="hero-pet-card">
                <div class="hero-pet-title">
                    <span>Meet Maple</span>
                    <a href="pet-profile.php?id=1">
                        <img src="assets/images/icon-arrow-up-right.svg" alt="" style="width: 20px; height: 20px;">
                    </a>
                </div>
                <p class="hero-pet-desc">3 years · Beagle mix · Professional cuddler</p>
            </div>

            <div class="hero-badge-sun">
                <img src="assets/images/icon-sun.svg" alt="" style="width: 16px; height: 16px;">
                <span>A brighter everyday</span>
            </div>
        </div>
    </section>

    <!-- Value Props Banner -->
    <section class="approach-banner">
        <div class="approach-item">
            <img src="assets/images/paw-print.svg" alt="" class="approach-icon">
            <span>Every personality belongs</span>
        </div>
        <div class="approach-item">
            <img src="assets/images/icon-house.svg" alt="" class="approach-icon">
            <span>Adopt for a lifetime. Foster for a while.</span>
        </div>
        <div class="approach-item">
            <img src="assets/images/icon-heart-handshake.svg" alt="" class="approach-icon">
            <span>A caring team, at every step</span>
        </div>
    </section>

    <!-- Available Pets Grid -->
    <section class="section-wrap">
        <div class="section-header">
            <div>
                <span class="section-eyebrow">Ready for a home</span>
                <h2 class="section-title">Companions looking for family</h2>
            </div>
            <a href="pets.php" class="btn btn-secondary btn-sm">
                View all pets
                <img src="assets/images/icon-arrow-right.svg" alt="" style="width: 14px; height: 14px;">
            </a>
        </div>

        <div class="pets-grid">
            <?php foreach ($featuredPets as $pet): ?>
                <div class="pet-card">
                    <div class="pet-card-image">
                        <img src="<?php echo htmlspecialchars($pet['photo_url']); ?>" alt="<?php echo htmlspecialchars($pet['name']); ?>" onerror="this.src='assets/images/pet-maple.png'">
                        <div class="pet-card-heart" title="Save to favorites">
                            <img src="assets/images/icon-heart.svg" alt="Heart" style="width: 16px; height: 16px;">
                        </div>
                    </div>
                    <div class="pet-card-content">
                        <div class="pet-card-header">
                            <h3 class="pet-card-name"><?php echo htmlspecialchars($pet['name']); ?></h3>
                            <?php 
                                $statusClass = 'badge-available';
                                if (stripos($pet['status'], 'foster') !== false) {
                                    $statusClass = 'badge-foster';
                                }
                            ?>
                            <span class="badge <?php echo $statusClass; ?>">
                                <?php echo htmlspecialchars($pet['status']); ?>
                            </span>
                        </div>
                        <p class="pet-card-facts">
                            <?php echo htmlspecialchars($pet['breed'] . ' · ' . $pet['age'] . ' ' . ($pet['age'] == 1 ? 'yr' : 'yrs') . ' · ' . $pet['sex']); ?>
                        </p>
                        <p class="pet-card-desc">
                            <?php echo htmlspecialchars(mb_strimwidth($pet['description'], 0, 95, '...')); ?>
                        </p>
                        <div class="pet-card-footer">
                            <a href="pet-profile.php?id=<?php echo $pet['petid']; ?>" class="pet-card-link">
                                View profile
                                <img src="assets/images/icon-arrow-right.svg" alt="" style="width: 14px; height: 14px;">
                            </a>
                            <a href="apply.php?id=<?php echo $pet['petid']; ?>&type=adopt" class="btn btn-nav btn-sm">
                                Adopt
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- How Adoption Works -->
    <section id="how-it-works" style="background-color: #fffefa; border-top: 1px solid var(--border-light); border-bottom: 1px solid var(--border-light); padding: 72px 64px;">
        <div style="max-width: var(--max-width); margin: 0 auto;">
            <div style="text-align: center; max-width: 600px; margin: 0 auto 56px;">
                <span class="section-eyebrow">How adoption works</span>
                <h2 class="section-title">A clear, supportive path home</h2>
                <p style="color: var(--text-muted); font-size: 15px; margin-top: 12px;">
                    We make the adoption journey thoughtful, honest, and joyful — for both you and your future pet.
                </p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px;">
                <div style="background: var(--bg-page); border: 1px solid var(--border-light); border-radius: 14px; padding: 28px;">
                    <span style="font-size: 32px; font-family: var(--font-serif); color: var(--text-dark); display: block; margin-bottom: 14px;">01</span>
                    <h3 style="font-size: 17px; font-weight: 600; color: var(--text-heading); margin-bottom: 8px;">Find your match</h3>
                    <p style="font-size: 13px; color: var(--text-muted); line-height: 1.5;">Browse pets and learn their unique personalities, quirks, and home preferences.</p>
                </div>
                <div style="background: var(--bg-page); border: 1px solid var(--border-light); border-radius: 14px; padding: 28px;">
                    <span style="font-size: 32px; font-family: var(--font-serif); color: var(--text-dark); display: block; margin-bottom: 14px;">02</span>
                    <h3 style="font-size: 17px; font-weight: 600; color: var(--text-heading); margin-bottom: 8px;">Apply online</h3>
                    <p style="font-size: 13px; color: var(--text-muted); line-height: 1.5;">Fill out our simple application to tell us about your daily lifestyle and home setup.</p>
                </div>
                <div style="background: var(--bg-page); border: 1px solid var(--border-light); border-radius: 14px; padding: 28px;">
                    <span style="font-size: 32px; font-family: var(--font-serif); color: var(--text-dark); display: block; margin-bottom: 14px;">03</span>
                    <h3 style="font-size: 17px; font-weight: 600; color: var(--text-heading); margin-bottom: 8px;">Meet & connect</h3>
                    <p style="font-size: 13px; color: var(--text-muted); line-height: 1.5;">Book an in-person visit at our center or with the foster family in a relaxed setting.</p>
                </div>
                <div style="background: var(--bg-page); border: 1px solid var(--border-light); border-radius: 14px; padding: 28px;">
                    <span style="font-size: 32px; font-family: var(--font-serif); color: var(--text-dark); display: block; margin-bottom: 14px;">04</span>
                    <h3 style="font-size: 17px; font-weight: 600; color: var(--text-heading); margin-bottom: 8px;">Welcome home</h3>
                    <p style="font-size: 13px; color: var(--text-muted); line-height: 1.5;">Complete adoption paperwork, receive veterinary records, and head home together.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Foster Feature Banner -->
    <section id="foster" class="section-wrap">
        <div style="background: var(--bg-subtle); border: 1px solid var(--border-light); border-radius: 22px; padding: 48px; display: flex; align-items: center; gap: 48px;">
            <div style="flex: 1;">
                <span class="section-eyebrow">Become a foster caregiver</span>
                <h2 style="font-family: var(--font-serif); font-size: 38px; color: var(--text-dark); line-height: 1.2; margin: 12px 0 16px;">
                    A temporary home.<br>A permanent impact.
                </h2>
                <p style="color: var(--text-muted); font-size: 15px; line-height: 1.6; margin-bottom: 24px;">
                    Fostering gives pets a peaceful place to decompress while awaiting adoption. Hearth provides all food, medical care, and supplies — you just provide the love and warmth.
                </p>
                <div style="display: flex; gap: 14px;">
                    <a href="apply.php?type=foster" class="btn btn-primary">
                        Apply to foster
                    </a>
                    <a href="pets.php?filter=foster" class="btn btn-secondary">
                        See foster-ready pets
                    </a>
                </div>
            </div>
            <div style="width: 440px; height: 320px; border-radius: 16px; overflow: hidden; flex-shrink: 0; box-shadow: var(--shadow-sm);">
                <img src="assets/images/foster-home.png" alt="Foster at home" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
        </div>
    </section>

    <!-- Our Story Section -->
    <section id="story" style="background: var(--bg-card); border-top: 1px solid var(--border-light); padding: 72px 64px;">
        <div style="max-width: var(--max-width); margin: 0 auto; display: flex; align-items: center; gap: 64px;">
            <div style="width: 480px; height: 380px; border-radius: 20px; overflow: hidden; flex-shrink: 0; box-shadow: var(--shadow-sm);">
                <img src="assets/images/together.png" alt="Together with companion" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <div style="flex: 1;">
                <span class="section-eyebrow">Our Story & Mission</span>
                <h2 style="font-family: var(--font-serif); font-size: 38px; color: var(--text-heading); margin: 12px 0 16px;">
                    Every home starts with a hello.
                </h2>
                <p style="color: var(--text-muted); font-size: 15px; line-height: 1.7; margin-bottom: 16px;">
                    Hearth was founded on the belief that animal shelters should feel like community sanctuaries, not stressful holding pens. We pair transparent behavioral assessments with attentive, compassionate matchmaking.
                </p>
                <p style="color: var(--text-muted); font-size: 15px; line-height: 1.7;">
                    Whether you are adopting your first pet, opening your living room to a foster dog, or volunteering your weekend, we are here with guidance at every chapter.
                </p>
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
