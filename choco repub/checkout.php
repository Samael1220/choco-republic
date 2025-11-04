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
        p.image,
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
  <div class="checkout-container">
    <!-- Sidebar Progress -->
    <div class="checkout-sidebar">
      <div class="sidebar-header">
        <h2>Checkout Process</h2>
      </div>
      <div class="progress-steps">
        <div class="step completed">
          <div class="step-marker">
            <span>1</span>
          </div>
          <div class="step-info">
            <span class="step-title">Shopping Cart</span>
            <span class="step-desc">Review items</span>
          </div>
        </div>
        <div class="step active">
          <div class="step-marker">
            <span>2</span>
          </div>
          <div class="step-info">
            <span class="step-title">Checkout</span>
            <span class="step-desc">Payment & confirm</span>
          </div>
        </div>
        <div class="step">
          <div class="step-marker">
            <span>3</span>
          </div>
          <div class="step-info">
            <span class="step-title">Confirmation</span>
            <span class="step-desc">Order complete</span>
          </div>
        </div>
      </div>
      
      <div class="order-summary-sidebar">
        <h3>Order Summary</h3>
        <div class="summary-items">
          <?php foreach ($cartItems as $ci): ?>
            <div class="summary-item">
              <img src="/choco_repub/images/<?= !empty($ci['image']) ? htmlspecialchars(basename($ci['image'])) : 'default-product.png' ?>" 
                   alt="<?= htmlspecialchars($ci['name']) ?>">
              <div class="item-info">
                <span class="item-name"><?= htmlspecialchars($ci['name']) ?></span>
                <span class="item-qty">Qty: <?= (int)$ci['quantity'] ?></span>
              </div>
              <span class="item-price">₱<?= number_format((float)$ci['price'] * (int)$ci['quantity'], 2) ?></span>
            </div>
          <?php endforeach; ?>
        </div>
        <div class="summary-total">
          <div class="total-line">
            <span>Subtotal</span>
            <span>₱<?= number_format($total, 2) ?></span>
          </div>
          <div class="total-line">
            <span>Shipping</span>
            <span>Free</span>
          </div>
          <div class="total-line grand">
            <span>Total</span>
            <span>₱<?= number_format($total, 2) ?></span>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    
    <!-- Main Content -->
<div class="checkout-main">
  <?php if ($error): ?>
    <div class="alert-message error">
      <div class="alert-icon">⚠️</div>
      <div class="alert-text">
        <strong>Error:</strong> <?= htmlspecialchars($error) ?>
      </div>
    </div>
  <?php endif; ?>

  <?php if (empty($cartItems)): ?>
    <div class="empty-state">
      <div class="empty-icon">🛒</div>
      <h3>Your cart is empty</h3>
      <p>Add some delicious chocolates to your cart first!</p>
      <a href="products.php" class="btn btn-primary">Continue Shopping</a>
    </div>
  <?php else: ?>
    <div class="checkout-content">
      <header class="checkout-header">
        <h1>Complete Your Order</h1>
        <p>Review your details and confirm purchase</p>
      </header>

      <form method="post" class="checkout-form">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrfToken()) ?>">
        
        <div class="form-section">
          <div class="section-title">
            <h2>Payment Method</h2>
           
          </div>
          
          <div class="payment-options">
            <div class="payment-option">
              <input type="radio" name="payment_method" value="Cash on Delivery" id="cod" checked>
              <label for="cod" class="payment-card">
                <div class="payment-header">
                  <div class="payment-icon">💵</div>
                  <div class="payment-info">
                    <span class="payment-name">Cash on Delivery</span>
                    <span class="payment-desc">Pay when you receive your order</span>
                  </div>
                  <div class="radio-indicator"></div>
                </div>
              </label>
            </div>
            
            <div class="payment-option disabled">
              <input type="radio" name="payment_method" value="Credit Card" id="card" disabled>
              <label for="card" class="payment-card">
                <div class="payment-header">
                  <div class="payment-icon">💳</div>
                  <div class="payment-info">
                    <span class="payment-name">Credit/Debit Card</span>
                    <span class="payment-desc">Coming soon</span>
                  </div>
                  <div class="radio-indicator"></div>
                </div>
              </label>
            </div>
          </div>
        </div>

        <div class="trust-badges">
          <div class="trust-item">
            <span class="trust-icon">🔒</span>
            <div class="trust-text">
              <strong>Secure Checkout</strong>
              <span>Your data is protected</span>
            </div>
          </div>
          <div class="trust-item">
            <span class="trust-icon">🚚</span>
            <div class="trust-text">
              <strong>Free Delivery</strong>
              <span>On all orders</span>
            </div>
          </div>
          <div class="trust-item">
            <span class="trust-icon">↩️</span>
            <div class="trust-text">
              <strong>Easy Returns</strong>
              <span>30-day policy</span>
            </div>
          </div>
        </div>

        <div class="action-section">
          <button type="submit" class="order-btn">
            <span class="order-text">
              <span class="main-text">Place Your Order</span>
              <span class="sub-text">Total: ₱<?= number_format($total, 2) ?></span>
            </span>
            <span class="btn-arrow">→</span>
          </button>
         
        </div>
      </form>
    </div>
  <?php endif; ?>

  </div>
</section>