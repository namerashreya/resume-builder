<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}
$show_success = false;
if (isset($_SESSION['login_success'])) {
    $show_success = true;
    unset($_SESSION['login_success']);
}
$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
$name = isset($_SESSION['name']) ? $_SESSION['name'] : 'User';
// Placeholder stats
$resume_count = 1; // You can fetch from DB later
$last_edited = 'Today'; // You can fetch from DB later
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Resume Builder</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <div class="greeting">
                Welcome, <?php echo htmlspecialchars($name); ?>!
                <?php if ($is_admin) echo '<span class="admin-badge">Admin</span>'; ?>
            </div>
            <?php if ($show_success) echo '<div class="success-msg">Login Successful!</div>'; ?>
        </div>
        <div class="quick-stats">
            <div class="stat-card">
                <i class="fa-solid fa-file-lines"></i><br>
                <strong><?php echo $resume_count; ?></strong> Resume(s)
            </div>
            <div class="stat-card">
                <i class="fa-solid fa-clock"></i><br>
                Last Edited: <strong><?php echo $last_edited; ?></strong>
            </div>
        </div>
        <div class="features-grid">
            <a href="build_resume.php" class="feature-card">
                <span class="feature-icon"><i class="fa-solid fa-plus"></i></span>
                <span class="feature-title">Build Resume</span>
                <span class="feature-desc">Start creating a new professional resume.</span>
            </a>
            <a href="edit_resume.php" class="feature-card">
                <span class="feature-icon"><i class="fa-solid fa-pen-to-square"></i></span>
                <span class="feature-title">Edit Resume</span>
                <span class="feature-desc">Update your existing resume details.</span>
            </a>
            <a href="delete_resume.php" class="feature-card">
                <span class="feature-icon"><i class="fa-solid fa-trash"></i></span>
                <span class="feature-title">Delete Resume</span>
                <span class="feature-desc">Remove a resume you no longer need.</span>
            </a>
            <a href="resume_list.php" class="feature-card">
                <span class="feature-icon"><i class="fa-solid fa-eye"></i></span>
                <span class="feature-title">View Resumes</span>
                <span class="feature-desc">See all your created resumes.</span>
            </a>
            <a href="templates.php" class="feature-card">
                <span class="feature-icon"><i class="fa-solid fa-palette"></i></span>
                <span class="feature-title">Choose Template</span>
                <span class="feature-desc">Select from 15+ ATS-friendly resume designs.</span>
            </a>
            <?php if ($is_admin) { ?>
            <a href="admin.php" class="feature-card">
                <span class="feature-icon"><i class="fa-solid fa-user-shield"></i></span>
                <span class="feature-title">Admin Panel</span>
                <span class="feature-desc">Manage users and resumes as admin.</span>
            </a>
            <?php } ?>
        </div>
        <a href="logout.php" class="logout-link"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>
</body>
</html> 

