<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/db.php';

include __DIR__ . '/includes/header.php';

$q = trim($_GET['q'] ?? '');

$sql = "SELECT id, name, price, image FROM products";
if ($q !== '') {
    $stmt = $conn->prepare($sql . " WHERE name LIKE CONCAT('%', ?, '%') ORDER BY name ASC");
    $stmt->bind_param('s', $q);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($sql . " ORDER BY name ASC");
}

$products = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}


?>

<section class="products-page">
    <h2>Products</h2>
    <form method="get" class="search-form">
        <input type="text" name="q" placeholder="Search products" value="<?= htmlspecialchars($q) ?>">
        <button type="submit">Search</button>
    </form>

    <div class="product-grid">
        <?php foreach ($products as $p): 
            $imgPath = !empty($p['image']) ? 'images/' . basename($p['image']) : 'images/0.png';
        ?>
        <div class="product-card">
            <a href="product.php?id=<?= (int)$p['id'] ?>">
                <img src="<?= htmlspecialchars($imgPath) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                <p><?= htmlspecialchars($p['name']) ?></p>
            </a>
            <p class="price">₱<?= number_format((float)$p['price'], 2) ?></p>
            <form method="post" action="cart_add.php">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrfToken()) ?>">
                <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                <input type="number" name="quantity" min="1" value="1">
                <button type="submit">Add to Cart</button>
            </form>
        </div>
        <?php endforeach; ?>
    </div>
</section>



<style>
/* Products Page Styling */
.products-page {
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px;
}

.products-page h2 {
    text-align: center;
    color: #5a2d0c;
    margin-bottom: 20px;
    font-size: 2rem;
}

.search-form {
    text-align: center;
    margin-bottom: 30px;
}

.search-form input[type="text"] {
    width: 200px;
    padding: 6px 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
    margin-right: 5px;
}

.search-form button {
    background: #5a2d0c;
    color: #fff;
    border: none;
    padding: 6px 12px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    transition: background 0.2s;
}

.search-form button:hover {
    background: #43210a;
}

.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
}

.product-card {
    background: #fff;
    padding: 15px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    text-align: center;
    transition: transform 0.2s, box-shadow 0.2s;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 25px rgba(0,0,0,0.2);
}

.product-card img {
    max-width: 100%;
    height: 180px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 10px;
}

.product-card p {
    margin: 5px 0;
}

.product-card .price {
    color: #5a2d0c;
    font-weight: 700;
    margin: 10px 0;
}

.product-card input[type="number"] {
    width: 60px;
    padding: 6px;
    border-radius: 6px;
    border: 1px solid #ccc;
    margin-right: 5px;
}

.product-card button {
    background: #5a2d0c;
    color: #fff;
    border: none;
    padding: 8px 12px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
}

.product-card button:hover {
    background: #43210a;
}

/* Responsive */
@media (max-width: 768px) {
    .product-grid {
        grid-template-columns: 1fr;
    }
}
</style>
