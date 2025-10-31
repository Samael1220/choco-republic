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
    <section class="products">
      <h2>Manage Orders</h2>
      <table style="width:100%; border-collapse: collapse;">
        <tr>
          <th align="left">Order</th>
          <th>Customer</th>
          <th>Total</th>
          <th>Status</th>
          <th>Placed</th>
          <th></th>
        </tr>
        <?php while ($o = $orders->fetch_assoc()): ?>
          <tr>
            <td>#<?= (int)$o['id'] ?></td>
            <td align="center"><?= htmlspecialchars($o['customer']) ?></td>
            <td align="center">₱<?= number_format((float)$o['total_amount'], 2) ?></td>
            <td align="center"><?= htmlspecialchars($o['status']) ?></td>
            <td align="center"><?= htmlspecialchars($o['order_date']) ?></td>
            <td align="right">
              <details>
                <summary>View / Update</summary>
                <?php
                  $oi = $conn->prepare('SELECT product_name, quantity, price FROM order_items WHERE order_id = ?');
                  $oid = (int)$o['id'];
                  $oi->bind_param('i', $oid);
                  $oi->execute();
                  $items = $oi->get_result();
                ?>
                <ul>
                  <?php while ($it = $items->fetch_assoc()): ?>
                    <li><?= htmlspecialchars($it['product_name']) ?> × <?= (int)$it['quantity'] ?> — ₱<?= number_format((float)$it['price'] * (int)$it['quantity'], 2) ?></li>
                  <?php endwhile; ?>
                </ul>
                <form method="post">
                  <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrfToken()) ?>">
                  <input type="hidden" name="order_id" value="<?= (int)$o['id'] ?>">
                  <select name="status">
                    <option value="pending" <?= $o['status']==='pending'?'selected':'' ?>>pending</option>
                    <option value="completed" <?= $o['status']==='completed'?'selected':'' ?>>completed</option>
                    <option value="cancelled" <?= $o['status']==='cancelled'?'selected':'' ?>>cancelled</option>
                  </select>
                  <button type="submit">Update</button>
                </form>
              </details>
            </td>
          </tr>
        <?php endwhile; ?>
      </table>
    </section>

<?php include __DIR__ . '/../includes/footer.php'; ?>

