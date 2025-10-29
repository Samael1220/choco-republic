<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/db.php';
requireLogin();

$userId = (int)$_SESSION['user_id'];

$orders = [];
$stmt = $conn->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC');
$stmt->bind_param('i', $userId);
$stmt->execute();
$res = $stmt->get_result();
while ($o = $res->fetch_assoc()) {
    $orders[] = $o;
}

include __DIR__ . '/includes/header.php';
?>

    <section class="products">
      <h2>My Orders</h2>
      <?php if (isset($_GET['placed'])): ?>
        <p style="color:green;">Your order has been placed successfully.</p>
      <?php endif; ?>
      <?php if (empty($orders)): ?>
        <p>You have no orders yet.</p>
      <?php else: ?>
        <?php foreach ($orders as $order): ?>
          <div style="border:1px solid #ddd; padding:12px; margin-bottom:12px; border-radius:8px;">
            <div style="display:flex; justify-content:space-between;">
              <div>
                <strong>Order #<?= (int)$order['id'] ?></strong> — <?= htmlspecialchars($order['status']) ?>
              </div>
              <div>
                ₱<?= number_format((float)$order['total_amount'], 2) ?>
              </div>
            </div>
            <div style="font-size:12px; color:#666;">Placed: <?= htmlspecialchars($order['order_date']) ?></div>
            <?php
              $oi = $conn->prepare('SELECT product_name, quantity, price FROM order_items WHERE order_id = ?');
              $oid = (int)$order['id'];
              $oi->bind_param('i', $oid);
              $oi->execute();
              $items = $oi->get_result();
            ?>
            <ul>
              <?php while ($it = $items->fetch_assoc()): ?>
                <li><?= htmlspecialchars($it['product_name']) ?> × <?= (int)$it['quantity'] ?> — ₱<?= number_format((float)$it['price'] * (int)$it['quantity'], 2) ?></li>
              <?php endwhile; ?>
            </ul>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </section>

<?php include __DIR__ . '/includes/footer.php'; ?>

