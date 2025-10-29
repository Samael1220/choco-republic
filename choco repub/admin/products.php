<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../db.php';
requireAdmin();

$res = $conn->query('SELECT * FROM products ORDER BY created_at DESC');
$products = [];
while ($row = $res->fetch_assoc()) { $products[] = $row; }

include __DIR__ . '/../includes/header.php';
?>

    <section class="products">
      <h2>Manage Products</h2>
      <a href="product_form.php" style="display:inline-block; margin:8px 0;">+ Add Product</a>
      <table style="width:100%; border-collapse: collapse;">
        <tr>
          <th align="left">Name</th>
          <th>Price</th>
          <th>Stock</th>
          <th>Image</th>
          <th></th>
        </tr>
        <?php foreach ($products as $p): ?>
          <tr>
            <td><?= htmlspecialchars($p['name']) ?></td>
            <td align="center">₱<?= number_format((float)$p['price'], 2) ?></td>
            <td align="center"><?= (int)$p['stock'] ?></td>
            <td align="center">
    <?php if (!empty($p['image'])): ?>
        <?php
        // Only the filename
        $filename = basename($p['image']);
        $imgPath = "../images/$filename"; // go up one level to reach 'images' folder
        ?>
        <img src="<?= htmlspecialchars($imgPath) ?>" alt="<?= htmlspecialchars($p['name']) ?>" style="height:40px;">
    <?php endif; ?>
</td>
            <td align="right">
              <a href="product_form.php?id=<?= (int)$p['id'] ?>">Edit</a>
              |
              <a href="product_delete.php?id=<?= (int)$p['id'] ?>" onclick="return confirm('Delete this product?')">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    </section>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<style>
  /* Admin Products Page */

body {
    font-family: 'Poppins', sans-serif;
    background-color: #f9f1f0;
    color: #333;
    line-height: 1.6;
    padding: 0;
    margin: 0;
}

.products {
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px;
}

.products h2 {
    color: #5a2d0c;
    font-size: 2rem;
    margin-bottom: 20px;
    text-align: center;
}

.products a {
    background-color: #5a2d0c;
    color: #fff;
    text-decoration: none;
    padding: 8px 12px;
    border-radius: 6px;
    font-weight: 600;
    transition: background 0.2s;
}

.products a:hover {
    background-color: #43210a;
}

/* Table Styling */
.products table {
    width: 100%;
    border-collapse: collapse;
    background-color: #fff;
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    border-radius: 8px;
    overflow: hidden;
}

.products th, .products td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.products th {
    background-color: #5a2d0c;
    color: #fff;
    font-weight: 600;
}

.products tr:nth-child(even) {
    background-color: #f5f0ec;
}

.products tr:hover {
    background-color: #f1e3d9;
}

.products img {
    border-radius: 4px;
}

/* Action Links */
.products td a {
    color: #5a2d0c;
    text-decoration: none;
    font-weight: 600;
    margin: 0 4px;
}

.products td a:hover {
    text-decoration: underline;
}

/* Responsive Table */
@media (max-width: 768px) {
    .products table, .products thead, .products tbody, .products th, .products td, .products tr {
        display: block;
    }

    .products tr {
        margin-bottom: 15px;
        border-bottom: 2px solid #ccc;
    }

    .products td {
        padding-left: 50%;
        text-align: right;
        position: relative;
    }

    .products td::before {
        content: attr(data-label);
        position: absolute;
        left: 15px;
        width: 45%;
        padding-right: 10px;
        text-align: left;
        font-weight: 600;
        color: #5a2d0c;
    }

    .products th {
        display: none;
    }

    .products img {
        height: auto;
        max-width: 100px;
    }
}

</style>