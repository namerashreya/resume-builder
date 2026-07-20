<?php
session_start();

// Unset all session variables
$_SESSION = [];

// If a session cookie exists, delete it on the client
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}

// Destroy the session data on the server
session_destroy();

// Regenerate a fresh session ID to prevent fixation on next request
session_start();
session_regenerate_id(true);

// Optional: flash message after logout
$_SESSION['logout_success'] = 'You have been logged out successfully.';

header('Location: login.php');
exit;
?>


