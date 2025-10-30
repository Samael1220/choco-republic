<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../db.php';
requireAdmin();

$stats = [
  'products' => 0,
  'orders' => 0,
  'pending' => 0,
  'users' => 0,
];

$stats['products'] = (int)$conn->query('SELECT COUNT(*) c FROM products')->fetch_assoc()['c'];
$stats['orders'] = (int)$conn->query('SELECT COUNT(*) c FROM orders')->fetch_assoc()['c'];
$stats['pending'] = (int)$conn->query("SELECT COUNT(*) c FROM orders WHERE status='pending'")->fetch_assoc()['c'];
$stats['users'] = (int)$conn->query('SELECT COUNT(*) c FROM users')->fetch_assoc()['c'];

include __DIR__ . '/../includes/headerForA.php';
?>
    <link rel="stylesheet" href="aindex.css" />
<section class="products">
  <h2>Admin Dashboard</h2>

  <div class="stats-grid">
    <div class="stat-card">
      <strong>Products</strong>
      <div><?= (int)$stats['products'] ?></div>
    </div>
    <div class="stat-card">
      <strong>Orders</strong>
      <div><?= (int)$stats['orders'] ?></div>
    </div>
    <div class="stat-card">
      <strong>Pending Orders</strong> 
      <div><?= (int)$stats['pending'] ?></div>
    </div>
    <div class="stat-card">
      <strong>Users</strong>
      <div><?= (int)$stats['users'] ?></div>
    </div>
  </div>

  <div class="links-row">
    <a href="products.php">Manage Products</a>
    <a href="orders.php">Manage Orders</a>
  </div>
</section>




