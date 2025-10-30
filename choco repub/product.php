<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/db.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

include __DIR__ . '/includes/header.php';
?>

<?php if ($product): ?>
<section class="product-page">
    <div class="product-container">
        <?php 
        $imgPath = !empty($product['image']) ? 'images/' . basename($product['image']) : 'images/0.png'; 
        ?>
        <div class="product-image">
            <img src="<?= htmlspecialchars($imgPath) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
        </div>

        <div class="product-details">
            <h2><?= htmlspecialchars($product['name']) ?></h2>
            <p class="price">₱<?= number_format((float)$product['price'], 2) ?></p>

            <?php if (!empty($product['description'])): ?>
                <p class="description"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
            <?php endif; ?>

            <?php if ((int)$product['stock'] > 0): ?>
            <form method="post" action="cart_add.php">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrfToken()) ?>">
                <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                <label for="quantity">Quantity:</label>
                <input type="number" name="quantity" id="quantity" min="1" max="<?= (int)$product['stock'] ?>" value="1">
                <button type="submit">Add to Cart</button>
            </form>
            <?php else: ?>
                <p class="out-of-stock">Out of stock</p>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php else: ?>
<p style="padding:20px;">Product not found.</p>
<?php endif; ?>



<style>
/* Single Product Page Styling */
.product-page {
    max-width: 1000px;
    margin: 40px auto;
    padding: 0 20px;
}

.product-container {
    display: flex;
    flex-wrap: wrap;
    gap: 30px;
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.product-image {
    flex: 1 1 300px;
    text-align: center;
}

.product-image img {
    max-width: 100%;
    height: auto;
    border-radius: 12px;
    object-fit: cover;
}

.product-details {
    flex: 1 1 300px;
}

.product-details h2 {
    color: #5a2d0c;
    margin-bottom: 15px;
    font-size: 2rem;
}

.product-details .price {
    color: #5a2d0c;
    font-weight: 700;
    font-size: 1.5rem;
    margin-bottom: 15px;
}

.product-details .description {
    margin-bottom: 20px;
    line-height: 1.5;
}

.product-details form {
    display: flex;
    align-items: center;
    gap: 10px;
}

.product-details input[type="number"] {
    width: 60px;
    padding: 6px;
    border-radius: 6px;
    border: 1px solid #ccc;
}

.product-details button {
    background: #5a2d0c;
    color: #fff;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}

.product-details button:hover {
    background: #43210a;
}

.product-details .out-of-stock {
    color: red;
    font-weight: 700;
    font-size: 1.2rem;
}

/* Responsive */
@media (max-width: 768px) {
    .product-container {
        flex-direction: column;
        align-items: center;
    }

    .product-details {
        text-align: center;
    }
}
</style>
