<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/db.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrf($_POST['csrf'] ?? '')) {
    header('Location: cart.php');
    exit();
}

$userId = (int)$_SESSION['user_id'];

// Remove single item
if (isset($_POST['remove'])) {
    $cartId = (int)$_POST['remove'];
    $stmt = $conn->prepare('DELETE FROM cart WHERE id = ? AND user_id = ?');
    $stmt->bind_param('ii', $cartId, $userId);
    $stmt->execute();
    header('Location: cart.php');
    exit();
}

// Update quantities (only if "Update Cart" button pressed)
if (isset($_POST['action']) && $_POST['action'] === 'update') {
    $qtyMap = $_POST['qty'] ?? [];
    foreach ($qtyMap as $cartId => $qty) {
        $cartId = (int)$cartId;
        $qty = max(1, (int)$qty);
        $stmt = $conn->prepare('UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?');
        $stmt->bind_param('iii', $qty, $cartId, $userId);
        $stmt->execute();
    }
}

header('Location: cart.php');
exit();
?>
