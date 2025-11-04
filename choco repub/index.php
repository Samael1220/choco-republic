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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Chocolate Republic - Premium Artisan Chocolates</title>
    <link rel="stylesheet" href="index.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1>Indulge in <span class="accent">Premium Chocolates</span></h1>
                    <p>Discover artisan bars, truffles, and classics crafted with passion to delight every bite.</p>
                    <div class="hero-actions">
                        <a href="products.php" class="btn btn-primary">
                            <span>Shop Now</span>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                        <a href="#latest" class="btn btn-secondary">Browse Latest</a>
                    </div>
                </div>
                <div class="hero-image">
                    <div class="slideshow">
                        <?php
                        $slides = ['banner.jpg', '1.jpg', '2.jpg'];
                        foreach ($slides as $index => $slide) :
                            $slidePath = 'images/' . $slide;
                        ?>
                            <div class="slide <?= $index === 0 ? 'active' : '' ?>">
                                <img src="<?= htmlspecialchars($slidePath) ?>" alt="Chocolate <?= $index + 1 ?>">
                                <div class="slide-overlay"></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <div class="features-grid">
                <div class="feature">
                    <div class="feature-icon">
                        <div class="icon-bg">🚚</div>
                    </div>
                    <h3>Fast Delivery</h3>
                    <p>Swift shipping so your cravings don't wait</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">
                        <div class="icon-bg">🍫</div>
                    </div>
                    <h3>Premium Quality</h3>
                    <p>Curated selection from artisan makers</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">
                        <div class="icon-bg">💝</div>
                    </div>
                    <h3>Perfect Gifts</h3>
                    <p>Beautifully packaged treats for every occasion</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Latest Products Section -->
    <section id="latest" class="products">
        <div class="container">
            <div class="section-header">
                <h2>Latest Arrivals</h2>
                <p>Fresh from our chocolate kitchen</p>
            </div>
            <div class="products-grid">
                <?php foreach ($products as $p) : 
                    $imgPath = !empty($p['image']) ? 'images/' . basename($p['image']) : 'images/0.png';
                ?>
                    <div class="product-card">
                        <div class="product-img">
                            <a href="product.php?id=<?= (int)$p['id'] ?>">
                                <img src="<?= htmlspecialchars($imgPath) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                                <div class="product-overlay">
                                    <span>Quick View</span>
                                </div>
                            </a>
                        </div>
                        <div class="product-info">
                            <h3><?= htmlspecialchars($p['name']) ?></h3>
                            <p class="price">₱<?= number_format((float)$p['price'], 2) ?></p>
                            <form method="post" action="cart_add.php" class="add-form">
                                <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrfToken()) ?>">
                                <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                                <div class="form-row">
                                    <input type="number" name="quantity" min="1" value="1" class="qty-input">
                                    <button type="submit" class="add-btn">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                            <path d="M3 3H5L5.4 5M7 13H17L21 5H5.4M7 13L5.4 5M7 13L4.7 15.3C4.3 15.7 4.6 16.4 5.2 16.4H17M17 13V16.4M9 19C9 19.6 8.6 20 8 20C7.4 20 7 19.6 7 19C7 18.4 7.4 18 8 18C8.6 18 9 18.4 9 19ZM17 19C17 19.6 16.6 20 16 20C15.4 20 15 19.6 15 19C15 18.4 15.4 18 16 18C16.6 18 17 18.4 17 19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        Add to Cart
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="section-footer">
                <a href="products.php" class="btn btn-outline">View All Products</a>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <div class="cta-content">
                <h3>Join the Chocolate Republic</h3>
                <p>Get exclusive offers and early access to new creations</p>
                <div class="cta-actions">
                    <a href="register.php" class="btn btn-light">Sign Up Free</a>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Simple slideshow
        let currentSlide = 0;
        const slides = document.querySelectorAll('.slide');
        
        function nextSlide() {
            slides[currentSlide].classList.remove('active');
            currentSlide = (currentSlide + 1) % slides.length;
            slides[currentSlide].classList.add('active');
        }
        
        setInterval(nextSlide, 4000);

        // Add hover effects
        document.querySelectorAll('.product-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });

        // Feature hover effects
        document.querySelectorAll('.feature').forEach(feature => {
            feature.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-3px)';
            });
            
            feature.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    </script>

    
</body>
</html>