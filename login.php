<?php
// comment
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/vendor/autoload.php';
use app\Models\Authentication;

$message = "";
$selectedRole = isset($_POST["role"]) ? $_POST["role"] : "applicant";

if (isset($_POST["submit"])) {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $selectedRole = $_POST["role"] ?? "applicant";

    $auth = new Authentication();
    $message = $auth->login($email, $password, $selectedRole);

    if ($message === "Login Successful") {
        $_SESSION["email"] = $email;
        $userRole = $auth->getRole($email);
        if ($userRole) {
            $_SESSION["role"] = $userRole;
        }
        if ($selectedRole === "staff" || (isset($_SESSION["role"]) && ($_SESSION["role"] === "staff" || $_SESSION["role"] === "admin"))) {
            header("Location: userDashboard/dashboard.php");
        } else {
            header("Location: index.php");
        }
        exit();
    }
}

$pageTitle = "Sign In";
require_once __DIR__ . '/includes/header.php';
?>

<main class="auth-page">
    <div class="auth-layout">
        <!-- Welcome Left Panel -->
        <div class="auth-panel">
            <img src="assets/images/signin-welcome.png" alt="Welcome to Hearth" class="panel-img">
            <div class="panel-card">
                <span class="eyebrow">Every home starts with a hello</span>
                <h2 class="title">Apply, visit, and follow every step from one place.</h2>
                <p class="desc">Create an account to submit adoption or foster applications, book a visit, and track your status.</p>
            </div>
        </div>

        <!-- Auth Card -->
        <div class="auth-card">
            <span class="eyebrow">Welcome back</span>
            <h1 class="heading">Sign in to Hearth</h1>
            <p class="subheading">Track your applications, schedule visits, and keep up with the pets you’ve saved.</p>

            <form action="login.php" method="POST" style="display: flex; flex-direction: column; gap: 20px; width: 100%;">
                <!-- Role Toggle -->
                <div class="role-toggle">
                    <button type="button" class="role-btn <?php echo $selectedRole === 'applicant' ? 'active' : ''; ?>" onclick="setRole('applicant')">
                        Applicant
                    </button>
                    <button type="button" class="role-btn <?php echo $selectedRole === 'staff' ? 'active' : ''; ?>" onclick="setRole('staff')">
                        Shelter staff
                    </button>
                    <input type="hidden" name="role" id="roleInput" value="<?php echo htmlspecialchars($selectedRole); ?>">
                </div>

                <!-- Error Alert Banner -->
                <?php if ($message !== ""): ?>
                    <div class="alert-banner">
                        <img src="assets/images/icon-alert.svg" alt="Alert" class="alert-icon">
                        <span><?php echo htmlspecialchars($message); ?></span>
                    </div>
                <?php endif; ?>

                <!-- Email Field -->
                <div class="form-field">
                    <label class="form-label" for="email">Email address</label>
                    <div class="input-wrapper">
                        <img src="assets/images/icon-mail.svg" alt="" class="input-icon">
                        <input type="email" id="email" name="email" placeholder="alex.rivera@example.com" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                    </div>
                </div>

                <!-- Password Field -->
                <div class="form-field">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-wrapper">
                        <img src="assets/images/icon-lock.svg" alt="" class="input-icon">
                        <input type="password" id="password" name="password" placeholder="••••••••••" required>
                    </div>
                </div>

                <!-- Options -->
                <div class="form-options">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember" checked>
                        <span>Keep me signed in</span>
                    </label>
                    <a href="#" class="forgot-link">Forgot password?</a>
                </div>

                <!-- Submit Button -->
                <button type="submit" name="submit" class="btn btn-primary" style="width: 100%; height: 50px;">
                    <span>Sign in</span>
                    <img src="assets/images/icon-arrow-right.svg" alt="" style="width: 16px; height: 16px; filter: brightness(0) invert(1);">
                </button>

                <div class="form-divider"></div>

                <div class="form-footer-prompt">
                    <span>New to Hearth?</span>
                    <a href="signup.php">Create an account</a>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
function setRole(role) {
    document.getElementById('roleInput').value = role;
    const buttons = document.querySelectorAll('.role-btn');
    buttons.forEach(btn => btn.classList.remove('active'));
    if (role === 'applicant') {
        buttons[0].classList.add('active');
    } else {
        buttons[1].classList.add('active');
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
