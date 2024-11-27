<?php
// session_reset.php

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])) {
    // Clear all session data and destroy the session
    $_SESSION = [];
    session_destroy();
}
?>
