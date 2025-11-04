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

<section class="product-form-section">
  <div class="form-container">
    <div class="form-header">
      <h2><?= $id ? 'Edit Product' : 'Add New Product' ?></h2>
      <p class="form-subtitle"><?= $id ? 'Update product information' : 'Fill in the product details below' ?></p>
    </div>
    
    <form action="product_save.php" method="post" enctype="multipart/form-data" id="productForm">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrfToken()) ?>">
      <input type="hidden" name="id" value="<?= (int)$id ?>">
      
      <div class="form-grid">
        <div class="form-group">
          <label for="name" class="form-label">Product Name</label>
          <input type="text" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required 
                 placeholder="Enter product name" class="form-input">
        </div>
        
        <div class="form-group">
          <label for="price" class="form-label">Price ($)</label>
          <input type="number" id="price" name="price" step="0.01" value="<?= htmlspecialchars($product['price']) ?>" required 
                 placeholder="0.00" class="form-input" min="0">
        </div>
        
        <div class="form-group">
          <label for="stock" class="form-label">Stock Quantity</label>
          <input type="number" id="stock" name="stock" min="0" value="<?= (int)$product['stock'] ?>" required 
                 placeholder="0" class="form-input">
        </div>
        
        <div class="form-group full-width">
          <label for="description" class="form-label">Description</label>
          <textarea id="description" name="description" rows="4" 
                    placeholder="Enter product description" class="form-textarea"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
        </div>
        
        <div class="form-group full-width">
          <label class="form-label">Product Image</label>
          
          <!-- Current Image (for edit mode) -->
          <?php if (!empty($product['image'])): ?>
            <?php
            $imgPath = ltrim($product['image'], '/');
            if (!file_exists(__DIR__ . '../images/' . basename($imgPath))) {
                $imgPath = 'images/placeholder.png';
            } else {
                $imgPath = 'images/' . basename($imgPath);
            }
            ?>
            <div class="image-preview-container">
              <div class="current-image">
                <span class="image-label">Current Image:</span>
                <img src="<?= htmlspecialchars($imgPath) ?>" alt="<?= htmlspecialchars($product['name'] ?? '') ?>" class="image-preview" id="currentImage">
              </div>
            </div>
          <?php endif; ?>
          
          <!-- New Image Preview -->
          <div class="new-image-preview" id="newImagePreview" style="display: none;">
            <div class="current-image">
              <span class="image-label">New Image Preview:</span>
              <img src="" alt="New image preview" class="image-preview" id="imagePreview">
              <button type="button" class="remove-image-btn" id="removeImageBtn">×</button>
            </div>
          </div>
          
          <!-- File Input -->
          <div class="file-input-container">
            <input type="file" name="image" id="image" class="file-input" accept="image/*">
            <label for="image" class="file-input-label">
              <span class="file-input-text" id="fileInputText">Choose new image</span>
              <span class="file-input-button">Browse</span>
            </label>
          </div>
          <p class="file-help-text">Supported formats: JPG, PNG, GIF. Max size: 2MB</p>
        </div>
      </div>
      
      <div class="form-actions">
        <button type="submit" class="btn-primary">
          <span class="btn-text">Save Product</span>
        </button>
        <a href="products.php" class="btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    const newImagePreview = document.getElementById('newImagePreview');
    const currentImage = document.getElementById('currentImage');
    const removeImageBtn = document.getElementById('removeImageBtn');
    const fileInputText = document.getElementById('fileInputText');

    // Image preview functionality
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        
        if (file) {
            // Check file size (2MB limit)
            if (file.size > 2 * 1024 * 1024) {
                alert('File size must be less than 2MB');
                this.value = '';
                return;
            }
            
            // Check file type
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!validTypes.includes(file.type)) {
                alert('Please select a valid image file (JPG, PNG, GIF)');
                this.value = '';
                return;
            }
            
            const reader = new FileReader();
            
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                newImagePreview.style.display = 'block';
                fileInputText.textContent = 'Change image';
                
                // Hide current image if exists
                if (currentImage) {
                    currentImage.parentElement.style.display = 'none';
                }
            }
            
            reader.readAsDataURL(file);
        }
    });

    // Remove image preview
    removeImageBtn.addEventListener('click', function() {
        imageInput.value = '';
        newImagePreview.style.display = 'none';
        fileInputText.textContent = 'Choose new image';
        
        // Show current image again if exists
        if (currentImage) {
            currentImage.parentElement.style.display = 'flex';
        }
    });

    // Form validation
    const form = document.getElementById('productForm');
    form.addEventListener('submit', function(e) {
        const name = document.getElementById('name').value.trim();
        const price = document.getElementById('price').value;
        const stock = document.getElementById('stock').value;
        
        if (!name) {
            e.preventDefault();
            alert('Please enter a product name');
            document.getElementById('name').focus();
            return;
        }
        
        if (!price || parseFloat(price) < 0) {
            e.preventDefault();
            alert('Please enter a valid price');
            document.getElementById('price').focus();
            return;
        }
        
        if (!stock || parseInt(stock) < 0) {
            e.preventDefault();
            alert('Please enter a valid stock quantity');
            document.getElementById('stock').focus();
            return;
        }
    });

    const priceInput = document.getElementById('price');
    priceInput.addEventListener('blur', function() {
        if (this.value) {
            this.value = parseFloat(this.value).toFixed(2);
        }
    });

    // Stock input validation
    const stockInput = document.getElementById('stock');
    stockInput.addEventListener('input', function() {
        if (this.value < 0) {
            this.value = 0;
        }
    });
});
</script>