<?php
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
