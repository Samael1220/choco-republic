<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/db.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf'] ?? '')) {
        http_response_code(400);
        echo 'Invalid CSRF token';
        exit();
    }
    $userId = (int)$_SESSION['user_id'];
    $productId = (int)($_POST['product_id'] ?? 0);
    $quantity = max(1, (int)($_POST['quantity'] ?? 1));

    // Ensure product exists and has stock
    $stmt = $conn->prepare('SELECT stock FROM products WHERE id = ?');
    $stmt->bind_param('i', $productId);
    $stmt->execute();
    $prod = $stmt->get_result()->fetch_assoc();
    if (!$prod) {
        header('Location: /products.php');
        exit();
    }

    // Upsert into cart
    $stmt = $conn->prepare('SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?');
    $stmt->bind_param('ii', $userId, $productId);
    $stmt->execute();
    $existing = $stmt->get_result()->fetch_assoc();
    if ($existing) {
        $newQty = $existing['quantity'] + $quantity;
        $stmt = $conn->prepare('UPDATE cart SET quantity = ? WHERE id = ?');
        $stmt->bind_param('ii', $newQty, $existing['id']);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare('INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)');
        $stmt->bind_param('iii', $userId, $productId, $quantity);
        $stmt->execute();
    }
}

header('Location: cart.php');
exit();
?>