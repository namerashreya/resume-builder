<?php
session_start();
require_once 'db.php';
$email = $password = '';
$email_err = $password_err = $login_err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate email
    if (empty(trim($_POST['email']))) {
        $email_err = 'Please enter your email.';
    } else {
        $email = trim($_POST['email']);
    }
    // Validate password
    if (empty(trim($_POST['password']))) {
        $password_err = 'Please enter your password.';
    } else {
        $password = trim($_POST['password']);
    }
    // Check credentials
    if (empty($email_err) && empty($password_err)) {
        $sql = "SELECT id, name, email, password, role FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows == 1) {
            $stmt->bind_result($id, $name, $email_db, $hashed_password, $role);
            $stmt->fetch();
            if (password_verify($password, $hashed_password)) {
                // Password is correct, start session
                $_SESSION['loggedin'] = true;
                $_SESSION['id'] = $id;
                $_SESSION['name'] = $name;
                $_SESSION['email'] = $email_db;
                $_SESSION['role'] = $role;
                $_SESSION['login_success'] = true;
                header('Location: dashboard.php');
                exit;
            } else {
                $login_err = 'Invalid email or password.';
            }
        } else {
            $login_err = 'Invalid email or password.';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Resume Builder</title>
    <link rel="stylesheet" href="assets/css/auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="auth-container">
        <h2>Login</h2>
        <?php if (isset($_SESSION['logout_success'])) { echo '<div class="success-msg"><i class="fa fa-check-circle"></i> ' . $_SESSION['logout_success'] . '</div>'; unset($_SESSION['logout_success']); } ?>
        <?php if ($login_err) echo '<div class="error-msg">' . $login_err . '</div>'; ?>
        <form action="" method="post" autocomplete="off">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <span class="error-msg"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password">
                <span class="error-msg"><?php echo $password_err; ?></span>
            </div>
            <button type="submit">Login</button>
            <p class="switch-link">Don't have an account? <a href="signup.php">Sign Up</a></p>
            <p class="switch-link"><a href="forgot_password.php">Forgot Password?</a></p>
        </form>
    </div>
</body>
</html> 