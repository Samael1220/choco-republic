<?php
require_once __DIR__ . '/auth.php';
?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="header.css" />
<header class="site-header">
  <nav class="navbar">
    <div class="brand">
      <a href="index.php" class="logo">🍫 Chocolate Republic</a>
    </div>

    <input type="checkbox" id="nav-toggle" class="nav-toggle" aria-label="Toggle navigation" />
    <label for="nav-toggle" class="hamburger" aria-hidden="true">
      <span></span>
      <span></span>
      <span></span>
    </label>

    <ul class="nav-links">
      <li><a href="index.php" class="active">Home</a></li>
      <li><a href="products.php">Products</a></li>
      <?php if (isLoggedIn()): ?>
        <li><a href="orders.php">My Orders</a></li>
        <?php if (isAdmin()): ?>
          <li><a href="Aindex.php">Admin</a></li>
        <?php endif; ?>
        <li><a href="cart.php" class="cart-link">Cart</a></li>
        <li><a href="logout.php" class="logout-link">Logout</a></li>
      <?php else: ?>
        <li><a href="login.php" id="login-btn" class="login-btn">Login</a></li>
      <?php endif; ?>
    </ul>
  </nav>
</header>



