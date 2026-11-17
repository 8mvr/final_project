<?php
// comment
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/vendor/autoload.php';
use app\Database;

$userName = $_SESSION['full_name'] ?? 'Alex Rivera';
$userEmail = $_SESSION['email'] ?? 'alex.rivera@example.com';

// Mock applications list matching Figma design
$applications = [
    [
        "id" => "APP-1042",
        "pet_name" => "Maple",
        "pet_photo" => "assets/images/pet-maple.png",
        "type" => "Adoption",
        "status" => "Under review",
        "date" => "Nov 3, 2026",
        "appointment" => "Meet & greet booked for Wednesday, Nov 18 at 10:45 AM · 18 Willow Lane",
        "current_step" => 2
    ],
    [
        "id" => "APP-1038",
        "pet_name" => "Milo",
        "pet_photo" => "assets/images/pet-milo.png",
        "type" => "Adoption",
        "status" => "Approved",
        "date" => "Oct 28, 2026",
        "appointment" => "Home trial arranged for Saturday, Nov 14 at 2:00 PM",
        "current_step" => 3
    ],
    [
        "id" => "FST-2019",
        "pet_name" => "Luna",
        "pet_photo" => "assets/images/pet-luna.png",
        "type" => "Foster",
        "status" => "Under review",
        "date" => "Nov 6, 2026",
        "appointment" => "Coordinator phone interview pending",
        "current_step" => 2
    ]
];

$pageTitle = "My Applications";
require_once __DIR__ . '/includes/header.php';
?>

<main class="section-wrap" style="padding-top: 48px;">
    <!-- Page Heading -->
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 28px;">
        <div>
            <span class="section-eyebrow">Signed in as <?php echo htmlspecialchars($userName); ?></span>
            <h1 style="font-family: var(--font-serif); font-size: 48px; color: var(--text-dark); margin: 6px 0 10px;">
                My applications
            </h1>
            <p style="color: var(--text-muted); font-size: 15px;">
                Follow each application from first hello to homecoming, and manage your visits.
            </p>
        </div>
        <a href="pets.php" class="btn btn-secondary">
            <span>Find another pet</span>
            <img src="assets/images/icon-arrow-right.svg" alt="" style="width: 14px; height: 14px;">
        </a>
    </div>

    <!-- Filter Tabs -->
    <div style="display: flex; gap: 10px; align-items: center; margin-bottom: 24px;">
        <div style="background: var(--text-dark); color: #fff; padding: 8px 18px; border-radius: 100px; font-size: 12px; font-weight: 600; cursor: pointer;">
            All applications · <?php echo count($applications); ?>
        </div>
        <div style="background: #fff; border: 1px solid var(--border-light); color: var(--text-heading); padding: 8px 18px; border-radius: 100px; font-size: 12px; font-weight: 600; cursor: pointer;">
            Adoption · 2
        </div>
        <div style="background: #fff; border: 1px solid var(--border-light); color: var(--text-heading); padding: 8px 18px; border-radius: 100px; font-size: 12px; font-weight: 600; cursor: pointer;">
            Foster · 1
        </div>
        <div style="margin-left: auto; font-size: 12px; color: var(--text-muted);">
            Sort: Most recent
        </div>
    </div>

    <!-- Application List -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
        <?php foreach ($applications as $app): ?>
            <div style="background: var(--bg-card); border: 1px solid var(--border-light); border-radius: 14px; padding: 24px; display: flex; gap: 24px; align-items: flex-start; box-shadow: var(--shadow-sm);">
                <img src="<?php echo htmlspecialchars($app['pet_photo']); ?>" alt="<?php echo htmlspecialchars($app['pet_name']); ?>" style="width: 140px; height: 140px; border-radius: 12px; object-fit: cover; flex-shrink: 0;" onerror="this.src='assets/images/pet-maple.png'">

                <div style="flex: 1; display: flex; flex-direction: column; gap: 14px;">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <h2 style="font-family: var(--font-serif); font-size: 28px; color: var(--text-dark);">
                                <?php echo htmlspecialchars($app['pet_name']); ?>
                            </h2>
                            <span class="badge" style="background: #ecebe4; color: var(--text-muted);">
                                <?php echo htmlspecialchars($app['type']); ?>
                            </span>
                        </div>
                        <span class="badge badge-review">
                            <?php echo htmlspecialchars($app['status']); ?>
                        </span>
                    </div>

                    <p style="font-size: 12px; color: var(--text-muted);">
                        Application <?php echo htmlspecialchars($app['id']); ?> · Submitted <?php echo htmlspecialchars($app['date']); ?>
                    </p>

                    <!-- Status Step Tracker -->
                    <div style="display: flex; align-items: center; gap: 10px; max-width: 600px;">
                        <!-- Step 1 -->
                        <div style="width: 20px; height: 20px; border-radius: 50%; background: var(--text-dark); display: flex; align-items: center; justify-content: center;">
                            <img src="assets/images/icon-check.svg" alt="" style="width: 11px; height: 11px; filter: brightness(0) invert(1);">
                        </div>
                        <span style="font-size: 12px; font-weight: 600; color: var(--text-heading);">Submitted</span>
                        
                        <div style="flex: 1; height: 2px; background: var(--text-dark);"></div>

                        <!-- Step 2 -->
                        <div style="width: 20px; height: 20px; border-radius: 50%; border: 2px solid var(--text-dark); background: #fff; display: flex; align-items: center; justify-content: center;">
                            <div style="width: 8px; height: 8px; border-radius: 50%; background: var(--text-dark);"></div>
                        </div>
                        <span style="font-size: 12px; font-weight: 600; color: var(--text-heading);">Under review</span>

                        <div style="flex: 1; height: 2px; background: var(--border-light);"></div>

                        <!-- Step 3 -->
                        <div style="width: 20px; height: 20px; border-radius: 50%; border: 1px solid var(--border-light); background: #fff;"></div>
                        <span style="font-size: 12px; color: var(--text-muted);">Decision</span>
                    </div>

                    <!-- Next Step Info Box -->
                    <?php if (!empty($app['appointment'])): ?>
                        <div style="background: var(--bg-subtle); border-radius: 10px; padding: 12px 16px; display: flex; align-items: center; gap: 12px;">
                            <img src="assets/images/icon-cal.svg" alt="" style="width: 16px; height: 16px;">
                            <span style="flex: 1; font-size: 12px; color: var(--text-heading);">
                                <?php echo htmlspecialchars($app['appointment']); ?>
                            </span>
                            <button class="btn btn-secondary btn-sm" style="height: 32px; font-size: 11px;">
                                Reschedule
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
