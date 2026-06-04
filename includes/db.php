<?php
<<<<<<< HEAD
/**
 * Database Configuration & Core Helpers
 * Ordnance Factory Varangaon – Inventory Management System v3.0
 *
 * Functions:
 *   getDBConnection()   – returns a mysqli connection
 *   requireLogin()      – guard for any authenticated page
 *   requireAdmin()      – guard for admin-only pages
 *   requireOperator()   – guard for operator pages
 *   isAdmin()           – true if current session is admin
 *   logAction()         – writes audit entry to inventory_history
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ordnance_ims');

if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Strict',
    ]);
}

function getDBConnection(): mysqli {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        http_response_code(503);
        die('<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>DB Error</title>
        <style>body{font-family:system-ui,sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;background:#f1f5f9;margin:0}
        .box{background:#fff;padding:40px;border-radius:12px;max-width:480px;border:1px solid #e2e8f0;text-align:center}
        h2{color:#dc2626;margin-bottom:12px}p{color:#64748b;font-size:14px;line-height:1.6}
        code{background:#f1f5f9;padding:2px 6px;border-radius:4px;font-size:13px}</style></head>
        <body><div class="box"><h2>Database Connection Failed</h2>
        <p>' . htmlspecialchars($conn->connect_error) . '</p>
        <p>Verify credentials in <code>includes/db.php</code>.</p></div></body></html>');
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}

/** Redirect to login if no session at all */
function requireLogin(): void {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
=======
// ============================================
// Database Configuration
// Ordnance Factory Varangaon - IMS
// ============================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // Change to your MySQL username
define('DB_PASS', '');           // Change to your MySQL password
define('DB_NAME', 'ordnance_ims');

function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die('<div style="color:red; font-family:Arial; padding:20px;">
            <h3>Database Connection Failed</h3>
            <p>' . $conn->connect_error . '</p>
            <p>Please check your database configuration in <b>includes/db.php</b></p>
        </div>');
    }
    $conn->set_charset("utf8");
    return $conn;
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Auth check function
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
>>>>>>> 8e508c9b963c8e29112b5e5a4ab939b3626529ab
        exit();
    }
}

<<<<<<< HEAD
/** Redirect non-admins away */
function requireAdmin(): void {
    requireLogin();
    if (($_SESSION['role'] ?? '') !== 'admin') {
        header('Location: operator_dashboard.php');
        exit();
    }
}

/** Redirect admins away from operator pages */
function requireOperator(): void {
    requireLogin();
    if (($_SESSION['role'] ?? '') === 'admin') {
        header('Location: home.php');
        exit();
    }
}

/** Returns true if the logged-in user is admin */
function isAdmin(): bool {
    return ($_SESSION['role'] ?? '') === 'admin';
}

/**
 * Writes an audit entry into inventory_history.
 */
function logAction(
    mysqli $conn,
    int    $product_id,
    string $product_name,
    string $product_code,
    string $action_type,
    int    $old_qty,
    int    $new_qty,
    string $changed_by,
    string $remarks = ''
): void {
    $stmt = $conn->prepare(
        "INSERT INTO inventory_history
         (product_id, product_name, product_code, action_type, old_quantity, new_quantity, changed_by, remarks)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param('isssiiis',
        $product_id, $product_name, $product_code,
        $action_type, $old_qty, $new_qty, $changed_by, $remarks);
    $stmt->execute();
    $stmt->close();
}
=======
// Log inventory action
function logAction($conn, $product_id, $product_name, $product_code, $action_type, $old_qty, $new_qty, $changed_by, $remarks = '') {
    $stmt = $conn->prepare("INSERT INTO inventory_history (product_id, product_name, product_code, action_type, old_quantity, new_quantity, changed_by, remarks) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssiiis", $product_id, $product_name, $product_code, $action_type, $old_qty, $new_qty, $changed_by, $remarks);
    $stmt->execute();
    $stmt->close();
}
?>
>>>>>>> 8e508c9b963c8e29112b5e5a4ab939b3626529ab
