<?php
require_once __DIR__ . '/../includes/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Chocolate Republic</title>
    <link rel="stylesheet" href="/index.css" />
  </head>
  <body>
    <header>
      <nav class="navbar">
        <ul>
          <li><a href="index.php" class="active">Home</a></li>
          <li><a href="products.php">Products</a></li>
          <li><a href="about.html">About Us</a></li>
          <?php if (isLoggedIn()): ?>
            <li><a href="orders.php">My Orders</a></li>
            <?php if (isAdmin()): ?>
              <li><a href="Aindex.php">Admin</a></li>
            <?php endif; ?>
            <li><a href="cart.php">Cart</a></li>
            <li><a href="logout.php">Logout</a></li>
          <?php else: ?>
            <li><a href="login.php" id="login-btn">Login</a></li>
          <?php endif; ?>
        </ul>
      </nav>
    </header>
<style>
  /* Header & Navbar */
header {
    background-color: #5a2d0c; /* chocolate brown */
    padding: 15px 0;
    position: sticky;
    top: 0;
    z-index: 100;
}

.navbar {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 20px;
}

.navbar ul {
    list-style: none;
    display: flex;
    gap: 20px;
}

.navbar ul li a {
    text-decoration: none;
    color: #fff;
    font-weight: 600;
    padding: 8px 12px;
    border-radius: 6px;
    transition: background 0.2s, color 0.2s;
}

.navbar ul li a:hover,
.navbar ul li a.active {
    background-color: #f9d6b3; /* light chocolate highlight */
    color: #5a2d0c;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .navbar {
        flex-direction: column;
        align-items: flex-start;
    }

    .navbar ul {
        flex-direction: column;
        gap: 10px;
        width: 100%;
    }

    .navbar ul li a {
        width: 100%;
        text-align: left;
        padding: 10px;
    }
}

</style>
