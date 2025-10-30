<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../db.php';
requireAdmin();

$res = $conn->query('SELECT * FROM products ORDER BY created_at DESC');
$products = [];
while ($row = $res->fetch_assoc()) { $products[] = $row; }

include __DIR__ . '/../includes/headerForA.php';
?>

  

<section class="products">
  <div class="products-header">
    <h2>🍫 Manage Products</h2>
    <a href="product_form.php" class="add-btn">+ Add Product</a>
  </div>

  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Price</th>
          <th>Stock</th>
          <th>Image</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($products as $p): ?>
          <tr>
            <td data-label="Name"><?= htmlspecialchars($p['name']) ?></td>
            <td data-label="Price" align="center">₱<?= number_format((float)$p['price'], 2) ?></td>
            <td data-label="Stock" align="center"><?= (int)$p['stock'] ?></td>
            <td data-label="Image" align="center">
              <?php if (!empty($p['image'])): ?>
                <?php
                  $filename = basename($p['image']);
                  $imgPath = "../images/$filename";
                ?>
                <img src="<?= htmlspecialchars($imgPath) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
              <?php endif; ?>
            </td>
            <td data-label="Actions" align="center">
              <a href="product_form.php?id=<?= (int)$p['id'] ?>" class="edit-btn">Edit</a>
              <a href="product_delete.php?id=<?= (int)$p['id'] ?>" class="delete-btn" onclick="return confirm('Delete this product?')">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>


<style>
:root {
  --accent: #5a2d0c;       /* chocolate */
  --accent-dark: #3d1f07;  /* darker chocolate */
  --light-bg: #f9f4ef;     /* creamy beige */
  --table-bg: #ffffff;
  --text: #2e2e2e;
}

/* Base Layout */
body {
  font-family: 'Poppins', sans-serif;
  background-color: var(--light-bg);
  color: var(--text);
  margin: 0;
  padding: 0;
}

.products {
  max-width: 1100px;
  margin: 50px auto;
  padding: 20px;
}

/* Header */
.products-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 25px;
}

.products h2 {
  font-size: 1.8rem;
  font-weight: 600;
  color: var(--accent);
}

.add-btn {
  background: var(--accent);
  color: #fff;
  text-decoration: none;
  padding: 10px 16px;
  border-radius: 8px;
  font-weight: 600;
  transition: 0.2s ease;
  box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
}

.add-btn:hover {
  background: var(--accent-dark);
}

/* Table Container */
.table-wrapper {
  background: var(--table-bg);
  border-radius: 12px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.08);
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
  border-radius: 12px;
  overflow: hidden;
}

thead {
  background-color: var(--accent);
  color: #fff;
  font-weight: 600;
}

th, td {
  padding: 14px 18px;
  text-align: left;
}

tr:nth-child(even) {
  background-color: #f8eee7;
}

tr:hover {
  background-color: #f4e1d2;
}

img {
  height: 45px;
  border-radius: 6px;
  object-fit: cover;
}

/* Buttons */
.edit-btn, .delete-btn {
  text-decoration: none;
  font-weight: 600;
  padding: 6px 12px;
  border-radius: 6px;
  margin: 0 3px;
  transition: all 0.2s ease;
}

.edit-btn {
  background: #f4e1d2;
  color: var(--accent);
}

.edit-btn:hover {
  background: #e0c4a8;
}

.delete-btn {
  background: #ffe5e5;
  color: #b32626;
}

.delete-btn:hover {
  background: #ffc7c7;
}

/* Responsive */
@media (max-width: 768px) {
  table, thead, tbody, th, td, tr {
    display: block;
  }

  tr {
    margin-bottom: 15px;
    border-bottom: 1px solid #ddd;
  }

  td {
    padding-left: 50%;
    text-align: right;
    position: relative;
  }

  td::before {
    content: attr(data-label);
    position: absolute;
    left: 15px;
    width: 45%;
    text-align: left;
    font-weight: 600;
    color: var(--accent-dark);
  }

  th {
    display: none;
  }
}
</style>


