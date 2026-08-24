<?php
session_start();
require_once 'db.php';

$email = $code = $new_password = $confirm_password = '';
$email_err = $code_err = $new_password_err = $confirm_password_err = $success_msg = $error_msg = '';

if (!isset($_SESSION['reset_step'])) {
    $_SESSION['reset_step'] = 1;
}

// Step 1: Enter email
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['reset_step'] == 1) {
    if (empty(trim($_POST['email']))) {
        $email_err = 'Please enter your email.';
    } else {
        $email = trim($_POST['email']);
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows == 1) {
            // Email exists, generate code
            $_SESSION['reset_email'] = $email;
            $_SESSION['reset_code'] = rand(100000, 999999);
            $_SESSION['reset_step'] = 2;
        } else {
            $email_err = 'No account found with that email.';
        }
        $stmt->close();
    }
}
// Step 2: Enter code and new password
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['reset_step'] == 2) {
    $code = trim($_POST['code']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);
    if (empty($code)) {
        $code_err = 'Please enter the reset code.';
    } elseif ($code != $_SESSION['reset_code']) {
        $code_err = 'Invalid reset code.';
    }
    if (empty($new_password)) {
        $new_password_err = 'Please enter a new password.';
    } elseif (strlen($new_password) < 6) {
        $new_password_err = 'Password must be at least 6 characters.';
    }
    if (empty($confirm_password)) {
        $confirm_password_err = 'Please confirm your new password.';
    } elseif ($new_password !== $confirm_password) {
        $confirm_password_err = 'Passwords do not match.';
    }
    if (empty($code_err) && empty($new_password_err) && empty($confirm_password_err)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $hashed_password, $_SESSION['reset_email']);
        if ($stmt->execute()) {
            $success_msg = 'Password reset successful! <a href=\'login.php\'>Login here</a>.';
            unset($_SESSION['reset_email'], $_SESSION['reset_code'], $_SESSION['reset_step']);
        } else {
            $error_msg = 'Something went wrong. Please try again.';
        }
        $stmt->close();
    }
}
// Reset process if user visits page fresh
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    unset($_SESSION['reset_email'], $_SESSION['reset_code'], $_SESSION['reset_step']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | Resume Builder</title>
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>
    <div class="auth-container">
        <h2>Forgot Password</h2>
        <?php if ($success_msg) { echo '<div class="success-msg">' . $success_msg . '</div>'; } ?>
        <?php if ($error_msg) { echo '<div class="error-msg">' . $error_msg . '</div>'; } ?>
        <?php if (!isset($_SESSION['reset_step']) || $_SESSION['reset_step'] == 1) { ?>
            <form action="" method="post" autocomplete="off">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
                    <span class="error-msg"><?php echo $email_err; ?></span>
                </div>
                <button type="submit">Send Reset Code</button>
                <p class="switch-link"><a href="login.php">Back to Login</a></p>
            </form>
        <?php } elseif ($_SESSION['reset_step'] == 2) { ?>
            <div class="success-msg">Your reset code is: <strong><?php echo $_SESSION['reset_code']; ?></strong></div>
            <form action="" method="post" autocomplete="off">
                <div class="form-group">
                    <label>Reset Code</label>
                    <input type="text" name="code" value="<?php echo htmlspecialchars($code); ?>">
                    <span class="error-msg"><?php echo $code_err; ?></span>
                </div>
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password">
                    <span class="error-msg"><?php echo $new_password_err; ?></span>
                </div>
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password">
                    <span class="error-msg"><?php echo $confirm_password_err; ?></span>
                </div>
                <button type="submit">Reset Password</button>
                <p class="switch-link"><a href="login.php">Back to Login</a></p>
            </form>
        <?php } ?>
    </div>
</body>
</html> 