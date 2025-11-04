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
  <div class="products-header">
    <h2>All Chocolates</h2>
    <form method="get" class="search-form">
      <input type="text" name="q" placeholder="Search sweet treats..." value="<?= htmlspecialchars($q) ?>">
      <button type="submit" class="btn primary">Search</button>
    </form>
  </div>

  <?php if (empty($products)): ?>
    <p class="no-results">No products found.</p>
  <?php else: ?>
    <div class="product-grid">
      <?php foreach ($products as $p): 
        $imgPath = !empty($p['image']) ? 'images/' . basename($p['image']) : 'images/0.png';
      ?>
      <div class="product-card">
        <a href="product.php?id=<?= (int)$p['id'] ?>">
          <div class="img-wrap">
            <img src="<?= htmlspecialchars($imgPath) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
          </div>
          <p><?= htmlspecialchars($p['name']) ?></p>
        </a>
        <p class="price">₱<?= number_format((float)$p['price'], 2) ?></p>
        <form method="post" action="cart_add.php" class="add-form">
          <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrfToken()) ?>">
          <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
          <input type="number" name="quantity" min="1" value="1">
          <button type="submit" class="btn small">Add to Cart</button>
        </form>
      </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>



<style>
    body{
         font-family: "Poppins", sans-serif;
    }
   
/* ===== Products Page ===== */
.products-page {
  max-width: 1200px;
  margin: 50px auto;
  padding: 0 20px 50px;
  text-align: center;
}

.products-header {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-bottom: 24px;
}

.products-page h2 {
  font-size: 2rem;
  color: #4e342e;
  margin-bottom: 14px;
}

.search-form {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
  justify-content: center;
}

.search-form input[type="text"] {
  padding: 10px 14px;
  border-radius: 999px;
  border: 1px solid #d6c5bb;
  outline: none;
  width: 250px;
  background: #fff8f3;
  color: #4e342e;
  font-family: "Poppins", sans-serif;
}

.search-form input::placeholder {
  color: #9e7b6b;
}

.btn {
  display: inline-block;
  padding: 10px 16px;
  border-radius: 999px;
  text-decoration: none;
  font-weight: 600;
  border: none;
  cursor: pointer;
}

.btn.primary {
  background: #5a2d0c;
  color: #fff;
}

.btn.primary:hover {
  background: #43210a;
}

.btn.small {
  padding: 8px 12px;
  background: #5a2d0c;
  color: #fff;
}

.btn.small:hover {
  background: #43210a;
}

/* ===== Product Grid ===== */
.product-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 32px;
  justify-items: center;
}

.product-card {
  background: #ffffff;
  border-radius: 16px;
  padding: 18px;
  text-align: center;
  box-shadow: 0 10px 22px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s, box-shadow 0.2s;
}

.product-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 16px 28px rgba(0, 0, 0, 0.14);
}

.img-wrap {
  width: 220px;
  height: 220px;
  margin: 0 auto 12px;
  border-radius: 12px;
  background: #fff8f3;
  display: grid;
  place-items: center;
  overflow: hidden;
}

.product-card img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  display: block;
}

.product-card p,a  {
  margin: 6px 0;
  font-weight: 700;
  color: #4e342e;
  font-size: 1rem;
  text-decoration: none;
}

.price {
  color: #5a2d0c;
  font-weight: 800;
  margin-top: 6px;
}

.add-form {
  display: flex;
  gap: 8px;
  justify-content: center;
  align-items: center;
  margin-top: 8px;
}

.add-form input[type="number"] {
  width: 64px;
  padding: 6px;
  border-radius: 8px;
  border: 1px solid #d6c5bb;
}

/* ===== No Results ===== */
.no-results {
  font-size: 1.1rem;
  color: #6d4c41;
  background: #fff8f3;
  padding: 16px 20px;
  border-radius: 12px;
  display: inline-block;
  box-shadow: 0 6px 14px rgba(0,0,0,0.08);
}

/* ===== Responsive ===== */
@media (max-width: 768px) {
  .product-grid {
    grid-template-columns: 1fr;
  }

  .search-form input[type="text"] {
    width: 80%;
  }
}
</style>
