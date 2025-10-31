<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/db.php';
requireLogin();
include __DIR__ . '/includes/header.php';

$userId = (int)$_SESSION['user_id'];

// Fetch cart
$stmt = $conn->prepare("
    SELECT 
        c.id AS cart_id,
        p.id AS product_id,
        p.name,
        p.price,
        p.stock,
        p.image,       -- ✅ Add this line
        c.quantity
    FROM cart c
    JOIN products p ON p.id = c.product_id
    WHERE c.user_id = ?
");
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();

$cartItems = [];
$total = 0.0;

while ($row = $result->fetch_assoc()) {
    $line = (float)$row['price'] * (int)$row['quantity'];
    $total += $line;
    $cartItems[] = $row;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf'] ?? '')) {
        $error = 'Invalid CSRF token.';
    } elseif (empty($cartItems)) {
        $error = 'Your cart is empty.';
    } else {
        $conn->begin_transaction();
        try {
            $payment = $_POST['payment_method'] ?? 'Cash on Delivery';
            $stmt = $conn->prepare('INSERT INTO orders (user_id, total_amount, status, payment_method) VALUES (?, ?, "pending", ?)');
            $stmt->bind_param('ids', $userId, $total, $payment);
            $stmt->execute();
            $orderId = $conn->insert_id;

            $oi = $conn->prepare('INSERT INTO order_items (order_id, product_id, product_name, quantity, price) VALUES (?, ?, ?, ?, ?)');
            foreach ($cartItems as $ci) {
                $pn = $ci['name'];
                $qty = (int)$ci['quantity'];
                $price = (float)$ci['price'];
                $pid = (int)$ci['product_id'];
                $oi->bind_param('iisid', $orderId, $pid, $pn, $qty, $price);
                $oi->execute();

                // decrement stock
                $upd = $conn->prepare('UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?');
                $upd->bind_param('iii', $qty, $pid, $qty);
                $upd->execute();
                if ($conn->affected_rows === 0) {
                    throw new Exception('Insufficient stock for ' . $pn);
                }
            }

            // clear cart
            $del = $conn->prepare('DELETE FROM cart WHERE user_id = ?');
            $del->bind_param('i', $userId);
            $del->execute();

            $conn->commit();
            header('Location: orders.php?placed=1');
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            $error = $e->getMessage();
        }
    }
}
?>
<link rel="stylesheet" href="checkout.css">

<section class="checkout-section">
  <h2>Checkout</h2>

  <?php if ($error): ?>
    <p class="checkout-error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <?php if (empty($cartItems)): ?>
    <p class="checkout-empty">Your cart is empty.</p>
  <?php else: ?>
    <div class="checkout-summary">
      <?php foreach ($cartItems as $ci): ?>
        <div class="checkout-item-row">
          <div class="ci-thumb">
  <img src="/choco_repub/images/<?= !empty($ci['image']) ? htmlspecialchars(basename($ci['image'])) : 'default-product.png' ?>" 
     alt="<?= htmlspecialchars($ci['name']) ?>">
</div>
          <div class="ci-main">
            <span class="ci-title"><?= htmlspecialchars($ci['name']) ?></span>
            <span class="ci-meta">Qty: <?= (int)$ci['quantity'] ?></span>
          </div>
          <div class="ci-price">₱<?= number_format((float)$ci['price'] * (int)$ci['quantity'], 2) ?></div>
        </div>
      <?php endforeach; ?>

      <div class="checkout-total-row">
        <span class="ct-title">Total:</span>
        <span class="ct-price">₱<?= number_format($total, 2) ?></span>
      </div>
    </div>

    <form method="post" class="checkout-form">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrfToken()) ?>">
      
      <label class="checkout-label" for="payment_method">Payment Method</label>
      <select id="payment_method" name="payment_method" required>
        <option value="Cash on Delivery">Cash on Delivery</option>
      </select>

      <div class="checkout-submit-row">
        <button type="submit" class="cta-btn">Place Order</button>
      </div>
    </form>
  <?php endif; ?>
</section>


