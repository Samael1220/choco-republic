<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../db.php';
requireAdmin();

$id = (int)($_GET['id'] ?? 0);
$product = [ 'name' => '', 'price' => '0.00', 'description' => '', 'stock' => 0, 'image' => '' ];
if ($id) {
    $stmt = $conn->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $p = $stmt->get_result()->fetch_assoc();
    if ($p) { $product = $p; }
}

include __DIR__ . '/../includes/headerForA.php';
?>
<link rel="stylesheet" href="product_form.css">

    <section class="products">
      <h2><?= $id ? 'Edit' : 'Add' ?> Product</h2>
      <form action="product_save.php" method="post" enctype="multipart/form-data" style="max-width:560px;">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrfToken()) ?>">
        <input type="hidden" name="id" value="<?= (int)$id ?>">
        <div>
          <label>Name</label>
          <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
        </div>
        <div>
          <label>Price</label>
          <input type="number" name="price" step="0.01" value="<?= htmlspecialchars($product['price']) ?>" required>
        </div>
        <div>
          <label>Stock</label>
          <input type="number" name="stock" min="0" value="<?= (int)$product['stock'] ?>" required>
        </div>
        <div>
          <label>Description</label>
          <textarea name="description" rows="4"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
        </div>
        <div>
    <label>Image</label>

    <?php if (!empty($product['image'])): ?>
        <?php
        // Make path relative to project folder
        $imgPath = ltrim($product['image'], '/');

        // If file doesn't exist in the images folder, use placeholder
        if (!file_exists(__DIR__ . '/../images/' . basename($imgPath))) {
            $imgPath = 'images/placeholder.png';
        } else {
            // Ensure path points to images folder
            $imgPath = 'images/' . basename($imgPath);
        }
        ?>
        <div>
            <img src="<?= htmlspecialchars($imgPath) ?>" alt="<?= htmlspecialchars($product['name'] ?? '') ?>" style="height:60px; border-radius:4px;">
        </div>
    <?php endif; ?>

    <input type="file" name="image">
</div>
        <div style="margin-top:8px;">
          <button type="submit">Save</button>
          <a href="products.php">Cancel</a>
        </div>
      </form>
    </section>

<?php include __DIR__ . '/../includes/footer.php'; ?>

