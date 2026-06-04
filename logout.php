<?php
<<<<<<< HEAD
/**
 * Logout
 * Ordnance Factory Varangaon – Inventory Management System
 *
 * Destroys the current session and redirects to the login page.
 */
require_once 'includes/db.php';

session_unset();
session_destroy();

header("Location: login.php");
exit();
=======
require_once 'includes/db.php';

// Destroy session
session_start();
session_unset();
session_destroy();

// Redirect to login with logout message
header("Location: login.php?logged_out=1");
exit();
?>
>>>>>>> 8e508c9b963c8e29112b5e5a4ab939b3626529ab
