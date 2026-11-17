<?php
// comment
require_once __DIR__ . '/vendor/autoload.php';
use app\Models\Pet;

$petModel = new Pet();

$species = $_GET['species'] ?? 'All animals';
$status = $_GET['status'] ?? 'All statuses';
$search = $_GET['search'] ?? '';
$lookingTo = $_GET['looking'] ?? 'all';

// Map looking to status
if ($lookingTo === 'adopt') {
    $status = 'Available';
} elseif ($lookingTo === 'foster') {
    $status = 'Foster welcome';
}

$pets = $petModel->getAll($species, $status, $search);

$pageTitle = "Find a Pet";
require_once __DIR__ . '/includes/header.php';
?>

<main>
    <div class="browse-hero">
        <span class="hero-eyebrow">Your kind of company</span>
        <h1 class="browse-title">A little hello. A lifelong connection.</h1>
        <p class="hero-desc">Meet the pets ready for their next chapter. Find the personality that fits your everyday.</p>

        <!-- Search Bar -->
        <form action="pets.php" method="GET" class="browse-searchbar">
            <img src="assets/images/icon-search.svg" alt="" style="width: 20px; height: 20px;">
            <input type="text" name="search" placeholder="Search by name, breed, or a little of both" value="<?php echo htmlspecialchars($search); ?>">
            <?php if (!empty($species) && $species !== 'All animals'): ?>
                <input type="hidden" name="species" value="<?php echo htmlspecialchars($species); ?>">
            <?php endif; ?>
            <button type="submit" class="btn btn-primary btn-sm">
                Search pets
            </button>
        </form>
    </div>

    <div class="browse-workspace">
        <!-- Filter Sidebar -->
        <aside class="filter-sidebar">
            <div class="filter-header">
                <span class="filter-title">Filters</span>
                <a href="pets.php" class="filter-reset">Reset all</a>
            </div>

            <!-- Looking To Filter -->
            <div class="filter-group">
                <span class="filter-group-title">Looking to</span>
                <label class="filter-option">
                    <input type="radio" name="filter_looking" value="all" <?php echo $lookingTo === 'all' ? 'checked' : ''; ?> onchange="applyFilter('looking', this.value)">
                    <span>Adopt or foster</span>
                </label>
                <label class="filter-option">
                    <input type="radio" name="filter_looking" value="adopt" <?php echo $lookingTo === 'adopt' ? 'checked' : ''; ?> onchange="applyFilter('looking', this.value)">
                    <span>Adopt</span>
                </label>
                <label class="filter-option">
                    <input type="radio" name="filter_looking" value="foster" <?php echo $lookingTo === 'foster' ? 'checked' : ''; ?> onchange="applyFilter('looking', this.value)">
                    <span>Foster</span>
                </label>
            </div>

            <!-- Species Filter -->
            <div class="filter-group">
                <span class="filter-group-title">Species</span>
                <label class="filter-option">
                    <input type="radio" name="filter_species" value="All animals" <?php echo $species === 'All animals' ? 'checked' : ''; ?> onchange="applyFilter('species', this.value)">
                    <span>All animals</span>
                </label>
                <label class="filter-option">
                    <input type="radio" name="filter_species" value="Dog" <?php echo $species === 'Dog' ? 'checked' : ''; ?> onchange="applyFilter('species', this.value)">
                    <span>Dogs</span>
                </label>
                <label class="filter-option">
                    <input type="radio" name="filter_species" value="Cat" <?php echo $species === 'Cat' ? 'checked' : ''; ?> onchange="applyFilter('species', this.value)">
                    <span>Cats</span>
                </label>
                <label class="filter-option">
                    <input type="radio" name="filter_species" value="Rabbit" <?php echo $species === 'Rabbit' ? 'checked' : ''; ?> onchange="applyFilter('species', this.value)">
                    <span>Small animals</span>
                </label>
            </div>

            <!-- Age Filter -->
            <div class="filter-group">
                <span class="filter-group-title">Age</span>
                <label class="filter-option">
                    <input type="radio" name="filter_age" value="any" checked>
                    <span>Any age</span>
                </label>
                <label class="filter-option">
                    <input type="radio" name="filter_age" value="puppy">
                    <span>Under 1 year</span>
                </label>
                <label class="filter-option">
                    <input type="radio" name="filter_age" value="young">
                    <span>1–3 years</span>
                </label>
                <label class="filter-option">
                    <input type="radio" name="filter_age" value="mature">
                    <span>4+ years</span>
                </label>
            </div>
        </aside>

        <!-- Results Grid -->
        <section class="browse-results">
            <?php if (empty($pets)): ?>
                <div style="background: var(--bg-card); border: 1px solid var(--border-light); border-radius: 14px; padding: 48px; text-align: center;">
                    <h3 style="font-family: var(--font-serif); font-size: 24px; color: var(--text-dark); margin-bottom: 8px;">No pets match your criteria</h3>
                    <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 20px;">Try adjusting your filters or search keywords.</p>
                    <a href="pets.php" class="btn btn-secondary">Clear all filters</a>
                </div>
            <?php else: ?>
                <div class="pets-grid">
                    <?php foreach ($pets as $pet): ?>
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
                                        } elseif (stripos($pet['status'], 'in foster') !== false) {
                                            $statusClass = 'badge-in-foster';
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
                                        Apply
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>
</main>

<script>
function applyFilter(key, value) {
    const url = new URL(window.location.href);
    url.searchParams.set(key, value);
    window.location.href = url.toString();
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
