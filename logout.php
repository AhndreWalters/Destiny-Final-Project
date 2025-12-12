<?php
session_start();

// Store some data for feedback if needed
$username = $_SESSION['username'] ?? 'Guest';

// Destroy all session data
$_SESSION = array();

// Delete the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to home page with logout message
header("Location: index.php?logout=1&user=" . urlencode($username));
exit;
?>