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
      <div class="product wedding"  data-price="899" >
        <div class="product-img">
          <img src="../../project/photos/FORHIM1.png" alt="Joyful Munch">
          <div class="hover-icons">
            <span onclick="addToCart('The Joyful Munch')">🛒</span>
            <span>🔍</span>
          </div>
        </div>
        <h3>The Joyful Munch</h3>
        <p><span class="new-price">₹899</span></p>
        
      </div>

      <!-- Product 2 -->
      <div class="product birthday">
        <div class="product-img">
          <img src="../../project/photos/forher2.png" alt="Shubh Utsav Box">
          <div class="hover-icons">
            <span onclick="addToCart('Shubh Utsav Box')">🛒</span>
            <span>🔍</span>
          </div>
        </div>
        <h3>Shubh Utsav Box</h3>
        <p><span class="new-price">₹1399</span></p>
      </div>

      <!-- Product 3 -->
      <div class="product anniversary">
        <div class="product-img">
          <img src="../../project/photos/bday2.jpg" alt="Flavors of Diwali">
          <div class="hover-icons">
            <span onclick="addToCart('Flavors of Diwali')">🛒</span>
            <span>🔍</span>
          </div>
        </div>
        <h3>Flavors of Diwali</h3>
        <p><span class="new-price">₹1299</span></p>
      </div>

      <!-- Product 4 -->
      <div class="product forher">
        <div class="product-img">
          <img src="../../project/photos/men.png" alt="Happiness Basket">
          <div class="hover-icons">
            <span onclick="addToCart('Happiness Basket')">🛒</span>
            <span>🔍</span>
          </div>
        </div>
        <h3>Happiness Basket</h3>
        <p><span class="new-price">₹2399</span></p>
      </div>

  <!-- Product 5 -->
      <div class="product forhim" >
        <div class="product-img">
          <img src="../../project/photos/men.png" alt="Happiness Basket">
          <div class="hover-icons">
            <span onclick="addToCart('Happiness Basket')">🛒</span>
            <span>🔍</span>
          </div>
        </div>
        <h3>Happiness Basket</h3>
        <p><span class="new-price">₹2399</span></p>
      </div>

  <!-- Product 6 -->
      <div class="product forkids" >
        <div class="product-img">
          <img src="../../project/photos/men.png" alt="Happiness Basket">
          <div class="hover-icons">
            <span onclick="addToCart('Happiness Basket')">🛒</span>
            <span>🔍</span>
          </div>
        </div>
        <h3>Happiness Basket</h3>
        <p><span class="new-price">₹2399</span></p>
      </div>

    <!-- </div> -->
  </section>
  <script src="../../project/shopPage/shop2.js"></script>
</body>
</html>
<?php
require_once('../footerPage/footer.php');
?>