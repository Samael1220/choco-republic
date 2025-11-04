<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../db.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST['csrf'] ?? '')) {
    $orderId = (int)($_POST['order_id'] ?? 0);
    $status = $_POST['status'] ?? 'pending';
    if (in_array($status, ['pending','completed','cancelled'], true)) {
        $stmt = $conn->prepare('UPDATE orders SET status = ? WHERE id = ?');
        $stmt->bind_param('si', $status, $orderId);
        $stmt->execute();
    }
    header('Location: orders.php');
    exit();
}

$orders = $conn->query('SELECT o.*, u.name as customer FROM orders o JOIN users u ON u.id=o.user_id ORDER BY order_date DESC');

include __DIR__ . '/../includes/headerForA.php';
?>
<link rel="stylesheet" href="orders.css" />

<section class="admin-orders-section">
  <h2>Manage Orders</h2>

  <?php if ($orders->num_rows === 0): ?>
    <p class="no-orders">No orders found.</p>
  <?php else: ?>
    <div class="orders-grid">
      <?php while ($o = $orders->fetch_assoc()): ?>
        <div class="order-card">
          <div class="order-header">
            <span class="order-id">#<?= (int)$o['id'] ?></span>
            <span class="order-status <?= htmlspecialchars($o['status']) ?>">
              <?= htmlspecialchars(ucwords($o['status'])) ?>
            </span>
          </div>

          <p class="customer-name">👤 <?= htmlspecialchars($o['customer']) ?></p>
          <p class="order-meta">
            📅 <?= htmlspecialchars($o['order_date']) ?> — 💰 ₱<?= number_format((float)$o['total_amount'], 2) ?>
          </p>

          <ul class="order-items">
            <?php
              $oi = $conn->prepare('SELECT product_name, quantity, price FROM order_items WHERE order_id = ?');
              $oid = (int)$o['id'];
              $oi->bind_param('i', $oid);
              $oi->execute();
              $items = $oi->get_result();
              while ($it = $items->fetch_assoc()):
            ?>
            <li class="order-item">
              <span class="item-name"><?= htmlspecialchars($it['product_name']) ?></span>
              <span class="item-qty">× <?= (int)$it['quantity'] ?></span>
              <span class="item-total">₱<?= number_format((float)$it['price'] * (int)$it['quantity'], 2) ?></span>
            </li>
            <?php endwhile; ?>
          </ul>

          <form method="post" class="status-form">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrfToken()) ?>">
            <input type="hidden" name="order_id" value="<?= (int)$o['id'] ?>">
            <select name="status">
              <option value="pending" <?= $o['status']==='pending'?'selected':'' ?>>Pending</option>
              <option value="completed" <?= $o['status']==='completed'?'selected':'' ?>>Completed</option>
              <option value="cancelled" <?= $o['status']==='cancelled'?'selected':'' ?>>Cancelled</option>
            </select>
            <button type="submit">Update</button>
          </form>
        </div>
      <?php endwhile; ?>
    </div>
  <?php endif; ?>
</section>
