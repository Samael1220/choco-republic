<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../db.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrf($_POST['csrf'] ?? '')) {
    header('Location: /admin/products.php');
    exit();
}

$id = (int)($_POST['id'] ?? 0);
$name = trim($_POST['name'] ?? '');
$price = (float)($_POST['price'] ?? 0);
$stock = (int)($_POST['stock'] ?? 0);
$description = trim($_POST['description'] ?? '');
$imagePath = '';

// Handle image upload if provided
if (!empty($_FILES['image']['name'])) {
    $uploadDir = '/images/';
    $baseDir = realpath(__DIR__ . '/..');
    $targetDir = $baseDir . $uploadDir;
    if (!is_dir($targetDir)) {
        @mkdir($targetDir, 0777, true);
    }
    $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9_\-.]/', '_', $_FILES['image']['name']);
    $targetPath = $targetDir . $filename;
    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
        $imagePath = $uploadDir . $filename;
    }
}

if ($id) {
    if ($imagePath) {
        $stmt = $conn->prepare('UPDATE products SET name=?, price=?, stock=?, description=?, image=? WHERE id=?');
        $stmt->bind_param('sdissi', $name, $price, $stock, $description, $imagePath, $id);
    } else {
        $stmt = $conn->prepare('UPDATE products SET name=?, price=?, stock=?, description=? WHERE id=?');
        $stmt->bind_param('sdisi', $name, $price, $stock, $description, $id);
    }
    $stmt->execute();
} else {
    $stmt = $conn->prepare('INSERT INTO products (name, price, stock, description, image) VALUES (?, ?, ?, ?, ?)');
    $stmt->bind_param('sdiss', $name, $price, $stock, $description, $imagePath);
    $stmt->execute();
}

header('Location: products.php');
exit();
?>