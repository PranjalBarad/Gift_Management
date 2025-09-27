<?php
require_once('../../project/headerPage/header.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shop</title>
  <link rel="stylesheet" href="../../project/shopPage/shop2.css">
</head>
<body>

  <!-- Shop Section -->
  <section class="shop-section">
    <div class="breadcrumb">
      <a href="../../project/homePage/home.php">Home</a> / <span>Shop</span>
    </div>

    <div class="shop-header">
      <p>Showing 6 results</p>
      <select id="sortSelect" onchange="sortProducts(this.value)">
        <option value="all">Default Sorting</option>
        <option value="wedding">Wedding</option>
        <option value="birthday">Birthday</option>
        <option value="anniversary">Anniversary</option>
        <option value="forher">For Her</option>
        <option value="forhim">For Him</option>
        <option value="forkids">For Kids</option>
      </select>
    </div>

    <div class="product-grid">
      <!-- Product 1 -->
      <div class="product forhim" data-price="1999">
        <div class="product-img">
          <img src="../../project/photos/FORHIM1.png"  alt="Classic Luxury Box">
          <div class="hover-icons">
            <span class="cart-btn">🛒</span>
            <span class="view-btn">🔍</span>
          </div>
        </div>
        <h3>Classic Luxury Box</h3>
        <p class="new-price">₹1999</p>
      </div>

      <!-- Product 2 -->
      <div class="product anniversary" data-price="1399">
        <div class="product-img">
          <img src="../../project/photos/forher2.png" alt="anniversary">
          <div class="hover-icons">
            <span class="cart-btn">🛒</span>
            <span class="view-btn">🔍</span>
          </div>
        </div>
        <h3>Pookie Basket</h3>
        <p class="new-price">₹1399</p>
      </div>

      <!-- Product 3 -->
      <div class="product birthday" data-price="1299">
        <div class="product-img">
          <img src="../../project/photos/bday2.jpg" alt="birthday">
          <div class="hover-icons">
            <span class="cart-btn">🛒</span>
            <span class="view-btn">🔍</span>
          </div>
        </div>
        <h3>Birthday Bliss Hamper</h3>
        <p class="new-price">₹1299</p>
      </div>

      <!-- Product 4 -->
      <div class="product forher" data-price="2399">
        <div class="product-img">
          <img src="../../project/photos/forher1.jpg" alt="Lavender Care Box">
          <div class="hover-icons">
            <span class="cart-btn">🛒</span>
            <span class="view-btn">🔍</span>
          </div>
        </div>
        <h3>Lavender Care Box</h3>
        <p class="new-price">₹2399</p>
      </div>

      <!-- Product 5 -->
      <div class="product forhim" data-price="2099">
        <div class="product-img">
          <img src="../../project/photos/men.png" alt="Happiness Basket">
          <div class="hover-icons">
            <span class="cart-btn">🛒</span>
            <span class="view-btn">🔍</span>
          </div>
        </div>
        <h3>Midnight Elegance Box</h3>
        <p class="new-price">₹2099</p>
      </div>

      <!-- Product 6 -->
      <div class="product forkids" data-price="1099">
        <div class="product-img">
          <img src="../../project/photos/happy.png" alt="Happiness Basket">
          <div class="hover-icons">
            <span class="cart-btn">🛒</span>
            <span class="view-btn">🔍</span>
          </div>
        </div>
        <h3>Happiness Basket</h3>
        <p class="new-price">₹1099</p>
      </div>
    </div>
  </section>


<!-- Single Modal (only one per page) -->
<div id="productModal" class="modal">
  <div class="modal-content">
    <span id="modalClose">&times;</span>
    <div class="modal-body">
      <div class="modal-left">
        <img id="modalImg" src="" class="cart-item-img" alt="Product Image" />
      </div>
      <div class="modal-right">
        <h3 id="modalTitle"></h3>
        <p>
          <span id="modalPrice" class="modal-price"></span>
          <span id="modalOriginalPrice" class="modal-original-price"></span>
        </p>
        <button id="modalAdd" class="add-cart-btn">ADD TO CART</button>
        <div id="modalDesc" class="modal-desc"></div>
        <div id="modalIncludes" class="modal-includes"></div>
      </div>
    </div>
  </div>
</div>

  <!-- Toast -->
  <div id="cartToast">✅ Box added to cart successfully.</div>

  <script src="../../project/shopPage/shop2.js"></script>
</body>
</html>
<?php
require_once('../footerPage/footer.php');
?>