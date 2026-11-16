<?php
// comment
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/vendor/autoload.php';
use app\Models\Authentication;

$message = "";
$error = "";

if (isset($_POST["submit"])) {
    $first_name = trim($_POST["first_name"] ?? "");
    $last_name = trim($_POST["last_name"] ?? "");
    $full_name = trim($_POST["full_name"] ?? ($first_name . " " . $last_name));
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $auth = new Authentication();
        $res = $auth->signup($full_name, $email, $password, $phone);
        if ($res === "Success") {
            header("Location: login.php");
            exit();
        } else {
            $error = $res;
        }
    }
}

$pageTitle = "Create Account";
require_once __DIR__ . '/includes/header.php';
?>

<main class="auth-page">
    <div class="auth-layout" style="align-items: flex-start;">
        <!-- Welcome Left Panel -->
        <div class="auth-panel" style="height: 780px;">
            <img src="assets/images/signup-welcome.png" alt="Join Hearth" class="panel-img">
            <div class="panel-card" style="bottom: 24px;">
                <span class="eyebrow">Join Hearth</span>
                <h2 class="title">One account for every step of the journey.</h2>
                <p class="desc">Browse pets, apply to adopt or foster, book visits, and see where every application stands.</p>
            </div>
        </div>

        <!-- Register Card -->
        <div class="auth-card" style="max-width: 620px;">
            <span class="eyebrow">Create account</span>
            <h1 class="heading">Let’s get you set up</h1>
            <p class="subheading">It takes about two minutes. You’ll use this account to apply and to keep track of your applications.</p>

            <form action="signup.php" method="POST" style="display: flex; flex-direction: column; gap: 18px; width: 100%;">
                <!-- Error Banner -->
                <?php if ($error !== ""): ?>
                    <div class="alert-banner">
                        <img src="assets/images/icon-alert.svg" alt="Alert" class="alert-icon">
                        <span><?php echo htmlspecialchars($error); ?></span>
                    </div>
                <?php endif; ?>

                <!-- Name Row -->
                <div class="form-row">
                    <div class="form-field">
                        <label class="form-label" for="first_name">First name</label>
                        <div class="input-wrapper">
                            <input type="text" id="first_name" name="first_name" placeholder="Alex" value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>" required>
                        </div>
                    </div>
                    <div class="form-field">
                        <label class="form-label" for="last_name">Last name</label>
                        <div class="input-wrapper">
                            <input type="text" id="last_name" name="last_name" placeholder="Rivera" value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>" required>
                        </div>
                    </div>
                </div>

                <!-- Email Field -->
                <div class="form-field">
                    <label class="form-label" for="email">Email address</label>
                    <div class="input-wrapper">
                        <img src="assets/images/icon-mail.svg" alt="" class="input-icon">
                        <input type="email" id="email" name="email" placeholder="alex.rivera@example.com" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                    </div>
                </div>

                <!-- Phone Field -->
                <div class="form-field">
                    <label class="form-label" for="phone">Phone number</label>
                    <div class="input-wrapper">
                        <img src="assets/images/icon-phone.svg" alt="" class="input-icon">
                        <input type="tel" id="phone" name="phone" placeholder="(503) 555-0148" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                    </div>
                </div>

                <!-- Password Row -->
                <div class="form-row">
                    <div class="form-field">
                        <label class="form-label" for="password">Password</label>
                        <div class="input-wrapper">
                            <img src="assets/images/icon-lock.svg" alt="" class="input-icon">
                            <input type="password" id="password" name="password" placeholder="••••••••••" required>
                        </div>
                        <span class="field-hint">At least 6 characters</span>
                    </div>
                    <div class="form-field">
                        <label class="form-label" for="confirm_password">Confirm password</label>
                        <div class="input-wrapper">
                            <img src="assets/images/icon-lock.svg" alt="" class="input-icon">
                            <input type="password" id="confirm_password" name="confirm_password" placeholder="••••••••••" required>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" name="submit" class="btn btn-primary" style="width: 100%; height: 50px; margin-top: 8px;">
                    <span>Create account</span>
                    <img src="assets/images/icon-arrow-right.svg" alt="" style="width: 16px; height: 16px; filter: brightness(0) invert(1);">
                </button>

                <div class="form-divider"></div>

                <div class="form-footer-prompt">
                    <span>Already have an account?</span>
                    <a href="login.php">Sign in</a>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
