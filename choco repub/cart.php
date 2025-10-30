<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/db.php';
requireLogin();
include __DIR__ . '/includes/header.php';

$userId = (int)$_SESSION['user_id'];

$sql = "SELECT c.id as cart_id, p.id as product_id, p.name, p.price, p.image, c.quantity
        FROM cart c JOIN products p ON p.id = c.product_id
        WHERE c.user_id = ? ORDER BY c.added_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
$items = [];
$total = 0.0;
while ($row = $result->fetch_assoc()) {
    $row['line_total'] = (float)$row['price'] * (int)$row['quantity'];
    $total += $row['line_total'];
    $items[] = $row;
}


?>

<section class="cart-section">
    <h2>Your Cart</h2>
    <?php if (empty($items)): ?>
        <p class="empty">Your cart is empty.</p>
    <?php else: ?>
        <form method="post" action="cart_update.php" class="cart-form">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrfToken()) ?>">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($items as $it): ?>
                    <tr>
                        <td class="product-info">
                            <img src="<?= htmlspecialchars($it['image'] ? 'images/' . basename($it['image']) : 'images/0.png') ?>" alt="<?= htmlspecialchars($it['name']) ?>" />
                            <p href="/product.php?id=<?= (int)$it['product_id'] ?>"><?= htmlspecialchars($it['name']) ?></p>
                        </td>
                        <td>₱<?= number_format((float)$it['price'], 2) ?></td>
                        <td>
                            <input 
                                type="number" 
                                min="1" 
                                name="qty[<?= (int)$it['cart_id'] ?>]" 
                                value="<?= (int)$it['quantity'] ?>" 
                                onkeydown="return event.key === 'ArrowUp' || event.key === 'ArrowDown' || event.key === 'Tab';"
                            />
                        </td>
                        <td>₱<?= number_format($it['line_total'], 2) ?></td>
                        <td>
                            <button type="submit" name="remove" value="<?= (int)$it['cart_id'] ?>" class="remove-btn">Remove</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" align="right"><strong>Grand Total</strong></td>
                        <td colspan="2">₱<?= number_format($total, 2) ?></td>
                    </tr>
                </tfoot>
            </table>
            <div class="cart-actions">
                <button type="submit" name="action" value="update" class="update-btn">Update Cart</button>
                <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
            </div>
        </form>
    <?php endif; ?>
</section>

<style>
.cart-section {
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px;
    font-family: 'Poppins', sans-serif;
}

.cart-section h2 {
    text-align: center;
    color: #5a2d0c;
    font-size: 2rem;
    margin-bottom: 20px;
}

.empty {
    text-align: center;
    color: #777;
    font-size: 1.2rem;
}

.cart-table {
    width: 100%;
    border-collapse: collapse;
    background-color: #fff;
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    border-radius: 8px;
    overflow: hidden;
}

.cart-table th, .cart-table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.cart-table th {
    background-color: #5a2d0c;
    color: #fff;
}

.cart-table tbody tr:nth-child(even) {
    background-color: #f5f0ec;
}

.cart-table tbody tr:hover {
    background-color: #f1e3d9;
}

.product-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.product-info img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 6px;
    border: 1px solid #ccc;
}

input[type="number"] {
    width: 60px;
    padding: 6px;
    border-radius: 6px;
    border: 1px solid #ccc;
    text-align: center;
}

input[type="number"]:focus {
    outline: none;
    border-color: #5a2d0c;
}

button.update-btn, .remove-btn {
    background-color: #5a2d0c;
    color: #fff;
    border: none;
    padding: 8px 12px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
}

button.update-btn:hover, .remove-btn:hover {
    background-color: #43210a;
}

.cart-actions {
    margin-top: 12px;
    display: flex;
    gap: 12px;
}

.checkout-btn {
    padding: 8px 12px;
    background: #5a2d0c;
    color: #fff;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 600;
    text-align: center;
}

.checkout-btn:hover {
    background: #43210a;
}
</style>
