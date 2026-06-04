<?php
/**
 * Delete Product (Soft Delete)
 * Ordnance Factory Varangaon – Inventory Management System
 *
 * Sets product status to 'deleted' and logs the action in audit history.
 * Redirects back to dashboard with a status message.
 */
require_once 'includes/db.php';
requireAdmin(); // Admin-only page

$id = intval($_GET['id'] ?? 0);

if (!$id) {
    header("Location: home.php");
    exit();
}

$conn = getDBConnection();

// Fetch product before marking as deleted
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND status='active'");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

<<<<<<< HEAD
if (!$product) {
    $conn->close();
    header("Location: home.php");
    exit();
}

// Soft delete: mark status as 'deleted'
$del = $conn->prepare("UPDATE products SET status='deleted' WHERE id=?");
$del->bind_param("i", $id);
$del->execute();
$del->close();

// Log the deletion in audit history
logAction(
    $conn,
    $product['id'],
    $product['product_name'],
    $product['product_code'],
    'DELETE',
    $product['quantity'],
    0,
    $_SESSION['username'],
    'Product removed from inventory (soft delete)'
);

$conn->close();
header("Location: home.php?success=3");
exit();
=======
if ($product) {
    // Soft delete
    $del = $conn->prepare("UPDATE products SET status='deleted' WHERE id=?");
    $del->bind_param("i", $id);
    $del->execute();
    $del->close();

    // Log the action
    logAction($conn, $id, $product['product_name'], $product['product_code'],
              'DELETE', $product['quantity'], 0, $_SESSION['username'], 'Product deleted');
}

$conn->close();
header("Location: home.php?success=3");
exit();
?>
>>>>>>> 8e508c9b963c8e29112b5e5a4ab939b3626529ab
