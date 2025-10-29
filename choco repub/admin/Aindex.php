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

include __DIR__ . '/../includes/header.php';
?>

    <section class="products">
      <h2>Admin Dashboard</h2>
      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px,1fr)); gap:12px;">
        <div style="border:1px solid #ddd; border-radius:8px; padding:12px;">
          <strong>Products</strong>
          <div><?= (int)$stats['products'] ?></div>
        </div>
        <div style="border:1px solid #ddd; border-radius:8px; padding:12px;">
          <strong>Orders</strong>
          <div><?= (int)$stats['orders'] ?></div>
        </div>
        <div style="border:1px solid #ddd; border-radius:8px; padding:12px;">
          <strong>Pending Orders</strong>
          <div><?= (int)$stats['pending'] ?></div>
        </div>
        <div style="border:1px solid #ddd; border-radius:8px; padding:12px;">
          <strong>Users</strong>
          <div><?= (int)$stats['users'] ?></div>
        </div>
      </div>
      <div style="margin-top:16px; display:flex; gap:8px;">
        <a href="/admin/products.php">Manage Products</a>
        <a href="/admin/orders.php">Manage Orders</a>
      </div>
    </section>

<?php include __DIR__ . '/../includes/footer.php'; ?>

