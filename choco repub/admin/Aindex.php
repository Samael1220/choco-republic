<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../db.php';
requireAdmin();

// Fetch stats
$stats = [
  'products' => (int)$conn->query('SELECT COUNT(*) c FROM products')->fetch_assoc()['c'],
  'orders' => (int)$conn->query('SELECT COUNT(*) c FROM orders')->fetch_assoc()['c'],
  'pending' => (int)$conn->query("SELECT COUNT(*) c FROM orders WHERE status='pending'")->fetch_assoc()['c'],
  'users' => (int)$conn->query('SELECT COUNT(*) c FROM users')->fetch_assoc()['c']
];

// Recent Orders
$recentOrders = $conn->query("
  SELECT o.id, u.name AS user_name, o.total_amount, o.status, o.order_date
  FROM orders o
  JOIN users u ON o.user_id = u.id
  ORDER BY o.order_date DESC
  LIMIT 5
");

// Top Products (by total quantity sold)
$topProducts = $conn->query("
  SELECT p.name, SUM(oi.quantity) AS total_sold
  FROM order_items oi
  JOIN products p ON oi.product_id = p.id
  GROUP BY oi.product_id
  ORDER BY total_sold DESC
  LIMIT 5
");

include __DIR__ . '/../includes/headerForA.php';
?>
<link rel="stylesheet" href="adindex.css" />

<section class="admin-dashboard">
  <h2>Admin Dashboard</h2>

  <!-- Stats Overview -->
  <div class="stats-grid">
    <div class="stat-card">
      <i class="fas fa-box"></i>
      <strong>Products</strong>
      <div><?= $stats['products'] ?></div>
    </div>
    <div class="stat-card">
      <i class="fas fa-shopping-cart"></i>
      <strong>Orders</strong>
      <div><?= $stats['orders'] ?></div>
    </div>
    <div class="stat-card pending">
      <i class="fas fa-clock"></i>
      <strong>Pending Orders</strong>
      <div><?= $stats['pending'] ?></div>
    </div>
    <div class="stat-card">
      <i class="fas fa-users"></i>
      <strong>Users</strong>
      <div><?= $stats['users'] ?></div>
    </div>
  </div>

  <!-- Quick Links -->
  <div class="links-row">
    <a href="products.php" class="btn">Manage Products</a>
    <a href="orders.php" class="btn">Manage Orders</a>
  </div>

  <!-- Recent Orders -->
  <div class="dashboard-section">
    <h3>Recent Orders</h3>
    <table class="dashboard-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Customer</th>
          <th>Total</th>
          <th>Status</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $recentOrders->fetch_assoc()): ?>
          <tr>
            <td>#<?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['user_name']) ?></td>
            <td>₱<?= number_format($row['total_amount'], 2) ?></td>
            <td class="status <?= $row['status'] ?>"><?= ucfirst($row['status']) ?></td>
            <td><?= date('M d, Y', strtotime($row['order_date'])) ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <!-- Top Products -->
  <div class="dashboard-section">
    <h3>Top Selling Products</h3>
    <ul class="top-products">
      <?php while ($p = $topProducts->fetch_assoc()): ?>
        <li>
          <span><?= htmlspecialchars($p['name']) ?></span>
          <span class="sold">Sold: <?= $p['total_sold'] ?></span>
        </li>
      <?php endwhile; ?>
    </ul>
  </div>
</section>
