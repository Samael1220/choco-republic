<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/db.php';
requireLogin();

$userId = (int)$_SESSION['user_id'];

// Fetch cart
$stmt = $conn->prepare("SELECT c.id as cart_id, p.id as product_id, p.name, p.price, p.stock, c.quantity
                         FROM cart c JOIN products p ON p.id = c.product_id WHERE c.user_id = ?");
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

include __DIR__ . '/includes/header.php';
?>

    <section class="products">
      <h2>Checkout</h2>
      <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
      <?php endif; ?>
      <?php if (empty($cartItems)): ?>
        <p>Your cart is empty.</p>
      <?php else: ?>
        <table style="width:100%; border-collapse: collapse;">
          <tr>
            <th align="left">Product</th>
            <th>Qty</th>
            <th align="right">Price</th>
          </tr>
          <?php foreach ($cartItems as $ci): ?>
            <tr>
              <td><?= htmlspecialchars($ci['name']) ?></td>
              <td align="center"><?= (int)$ci['quantity'] ?></td>
              <td align="right">₱<?= number_format((float)$ci['price'] * (int)$ci['quantity'], 2) ?></td>
            </tr>
          <?php endforeach; ?>
          <tr>
            <td colspan="2" align="right"><strong>Total</strong></td>
            <td align="right"><strong>₱<?= number_format($total, 2) ?></strong></td>
          </tr>
        </table>

        <form method="post" style="margin-top: 12px;">
          <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrfToken()) ?>">
          <label>Payment Method
            <select name="payment_method">
              <option value="Cash on Delivery">Cash on Delivery</option>
            </select>
          </label>
          <div style="margin-top: 8px;">
            <button type="submit">Place Order</button>
          </div>
        </form>
      <?php endif; ?>
    </section>

<?php include __DIR__ . '/includes/footer.php'; ?>

