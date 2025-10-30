<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/db.php';
requireLogin();
include __DIR__ . '/includes/header.php';

$userId = (int)$_SESSION['user_id'];

$orders = [];
$stmt = $conn->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC');
$stmt->bind_param('i', $userId);
$stmt->execute();
$res = $stmt->get_result();
while ($o = $res->fetch_assoc()) {
    $orders[] = $o;
}


?>
  <link rel="stylesheet" href="orders.css" />

    <section class="orders-section">
      <h2>My Orders</h2>
      <?php if (isset($_GET['placed'])): ?>
        <p class="order-success">Your order has been placed successfully.</p>
      <?php endif; ?>
      <?php if (empty($orders)): ?>
        <p class="order-empty">You have no orders yet.</p>
      <?php else: ?>
        <div class="orders-grid">
          <?php foreach ($orders as $order): ?>
            <div class="order-card">
              <div class="order-info">
                <div class="order-id-status">
                  <span class="order-id">#<?= (int)$order['id'] ?></span>
                  <span class="order-status <?= htmlspecialchars($order['status']) ?>">
                    <?= htmlspecialchars(ucwords($order['status'])) ?>
                  </span>
                </div>
                <div class="order-meta">
                  <span class="order-date">Placed: <?= htmlspecialchars($order['order_date']) ?></span>
                  <span class="order-total">₱<?= number_format((float)$order['total_amount'], 2) ?></span>
                </div>
              </div>
              <ul class="order-items">
                <?php
                  $oi = $conn->prepare('SELECT product_id, product_name, quantity, price FROM order_items WHERE order_id = ?');
                  $oid = (int)$order['id'];
                  $oi->bind_param('i', $oid);
                  $oi->execute();
                  $items = $oi->get_result();
                  while ($it = $items->fetch_assoc()):
                    $imgPath = !empty($it['product_id']) ? $conn->query("SELECT image FROM products WHERE id = " . (int)$it['product_id'])->fetch_assoc()['image'] ?? '' : '';
                    $imgPath = !empty($imgPath) ? 'images/' . basename($imgPath) : 'images/0.png';
                ?>
                <li class="order-item">
                  <span class="item-thumb"><img src="<?= htmlspecialchars($imgPath) ?>" alt="<?= htmlspecialchars($it['product_name']) ?>" /></span>
                  <span class="item-name"><?= htmlspecialchars($it['product_name']) ?></span>
                  <span class="item-mult">× <?= (int)$it['quantity'] ?></span>
                  <span class="item-total">₱<?= number_format((float)$it['price'] * (int)$it['quantity'], 2) ?></span>
                </li>
                <?php endwhile; ?>
              </ul>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>



