<?php
    // Logout Code Here
    session_start();

// Destroy the session and log the user out
session_destroy();

// Redirect back to the login page
header("Location: index.php");
exit();
?>