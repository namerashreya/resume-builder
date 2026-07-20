<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['id'];
$resume_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$resume_id) {
    header('Location: resume_list.php');
    exit;
}

// Check if resume belongs to user and delete it
$sql = "DELETE FROM resumes WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $resume_id, $user_id);

if ($stmt->execute()) {
    $_SESSION['delete_success'] = 'Resume deleted successfully!';
} else {
    $_SESSION['delete_error'] = 'Failed to delete resume.';
}

$stmt->close();
header('Location: resume_list.php');
exit;
?> 