<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/db.php';

// Fetch latest 6 products
$products = [];
$res = $conn->query("SELECT id, name, price, image FROM products ORDER BY created_at DESC LIMIT 6");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $products[] = $row;
    }
}


?>
<?php

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Chocolate Republic</title>
    <link rel="stylesheet" href="index.css" />
  </head>
  <body>
    <?php include __DIR__ . '/includes/header.php'; ?>

    <section class="hero">
      <div class="hero-inner">
        <div class="hero-copy">
          <h1>Indulge in Premium Chocolates</h1>
          <p>Discover artisan bars, truffles, and classics crafted to delight every bite.</p>
          <div class="hero-cta">
            <a class="btn primary" href="products.php">Shop Chocolates</a>
            <a class="btn ghost" href="#latest">Browse Latest</a>
          </div>
        </div>
        <div class="slideshow-container">
          <?php
          $slides = ['banner.jpg', '1.jpg', '2.jpg'];
          foreach ($slides as $slide) :
              $slidePath = 'images/' . $slide;
          ?>
            <div class="slide fade">
              <img src="<?= htmlspecialchars($slidePath) ?>" alt="<?= htmlspecialchars($slide) ?>">
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <section class="brands" aria-label="Popular brands">
      <div class="brands-track">
        <?php 
          $brands = ['cadbury.png','ferrero.png','hershey.png','kitkat.png','mars.png','milka.png','milkyway.png','reeses.png','snickers.png','toblerone.png','twix.png'];
          foreach ($brands as $b):
        ?>
          <img src="images/<?= htmlspecialchars($b) ?>" alt="<?= htmlspecialchars(pathinfo($b, PATHINFO_FILENAME)) ?>" />
        <?php endforeach; ?>
      </div>
    </section>

    <section class="features" aria-label="Why shop with us">
      <div class="feature">
        <div class="icon">🚚</div>
        <h3>Fast Delivery</h3>
        <p>Swift shipping so your cravings don’t have to wait.</p>
      </div>
      <div class="feature">
        <div class="icon">🍫</div>
        <h3>Premium Quality</h3>
        <p>Curated selection from beloved brands and artisan makers.</p>
      </div>
      <div class="feature">
        <div class="icon">💝</div>
        <h3>Perfect Gifts</h3>
        <p>Make every occasion sweeter with beautifully packed treats.</p>
      </div>
    </section>

    <section id="latest" class="products">
      <h2>Latest Chocolates</h2>
      <div class="product-grid">
        <?php foreach ($products as $p) : 
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
    </section>

    <section class="cta-banner">
      <div class="cta-inner">
        <h3>Join the Republic and get sweet deals</h3>
        <p>Sign up to receive exclusive promos and early access to limited drops.</p>
        <a class="btn primary" href="register.php">Create Account</a>
      </div>
    </section>

<script>
let slideIndex = 0;
function showSlides() {
    const slides = document.getElementsByClassName("slide");
    for (let slide of slides) slide.style.display = "none";
    slideIndex++;
    if (slideIndex > slides.length) slideIndex = 1;
    slides[slideIndex - 1].style.display = "block";
    setTimeout(showSlides, 4000);
}
showSlides();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>


