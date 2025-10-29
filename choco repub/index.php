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

include __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <div class="hero-text">
        <h1>Welcome to Chocolate Republic 🍫</h1>
        <p>Your sweet journey starts here – discover our best chocolates!</p>
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
</section>

<section class="products">
    <h2>Latest Chocolates</h2>
    <div class="product-grid">
        <?php foreach ($products as $p) : 
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

<style>
/* General Reset */
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Poppins', sans-serif; background: #f9f1f0; color: #333; line-height: 1.6; }

/* Hero Section */
.hero { text-align: center; margin-bottom: 50px; }
.hero-text {
    padding: 60px 20px;
    background: rgba(90,45,12,0.85);
    color: #fff;
    border-radius: 12px;
    max-width: 700px;
    margin: -80px auto 40px auto;
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
}
.hero h1 { font-size: 2.5rem; margin-bottom: 20px; }
.hero p { font-size: 1.1rem; }

/* Slideshow */
.slideshow-container { position: relative; max-width: 1000px; margin: 0 auto 60px auto; border-radius: 12px; overflow: hidden; }
.slide { display: none; width: 100%; }
.slide img { width: 100%; display: block; border-radius: 12px; }

/* Products Section */
.products { max-width: 1200px; margin: 0 auto 60px auto; padding: 0 20px; }
.products h2 { text-align: center; color: #5a2d0c; margin-bottom: 30px; font-size: 2rem; }
.product-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; }

.product-card {
    background: #fff; padding: 15px; border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.1); text-align: center;
    transition: transform 0.2s, box-shadow 0.2s;
}
.product-card:hover { transform: translateY(-5px); box-shadow: 0 12px 25px rgba(0,0,0,0.2); }
.product-card img { max-width: 100%; height: 180px; object-fit: cover; border-radius: 8px; margin-bottom: 10px; }
.product-card p { margin: 5px 0; }
.product-card .price { color: #5a2d0c; font-weight: 700; margin: 10px 0; }
.product-card input[type="number"] { width: 60px; padding: 6px; border-radius: 6px; border: 1px solid #ccc; margin-right: 5px; }
.product-card button { background: #5a2d0c; color: #fff; border: none; padding: 8px 12px; border-radius: 6px; cursor: pointer; font-weight: 600; }
.product-card button:hover { background: #43210a; }

/* Footer */
footer { background: #5a2d0c; color: #fff; text-align: center; padding: 20px 0; margin-top: 60px; border-radius: 12px 12px 0 0; }
</style>
