<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shop</title>
  <link rel="stylesheet" href="../../project/shopPage/shop.css">
</head>
<body>

  <h2>Shop</h2>

  <!-- Category Buttons -->
  <div class="menu">
    <button onclick="filterProducts('all')">Default</button>
    <button onclick="filterProducts('wedding')">Wedding</button>
    <button onclick="filterProducts('birthday')">Birthday</button>
    <button onclick="filterProducts('anniversary')">Anniversary</button>
    <button onclick="filterProducts('forher')">For Her</button>
    <button onclick="filterProducts('forhim')">For Him</button>
    <button onclick="filterProducts('forkids')">For Kids</button>
  </div>

  <!-- Products -->
  <div class="products" id="productList">

    <div class="product wedding">
      <img src="images/wedding.jpg" alt="Wedding Gift">
      <h3>Wedding Gift</h3>
      <p>Beautiful wedding decoration gift.</p>
    </div>

    <div class="product birthday">
      <img src="images/birthday.jpg" alt="Birthday Gift">
      <h3>Birthday Gift</h3>
      <p>Perfect gift for birthday celebrations.</p>
    </div>

    <div class="product anniversary">
      <img src="images/anniversary.jpg" alt="Anniversary Gift">
      <h3>Anniversary Gift</h3>
      <p>Celebrate love with this gift.</p>
    </div>

    <div class="product forher">
      <img src="images/forher.jpg" alt="Gift for Her">
      <h3>Gift for Her</h3>
      <p>Special collection for her.</p>
    </div>

    <div class="product forhim">
      <img src="images/forhim.jpg" alt="Gift for Him">
      <h3>Gift for Him</h3>
      <p>Exclusive collection for him.</p>
    </div>

    <div class="product forkids">
      <img src="images/forkids.jpg" alt="Gift for Kids">
      <h3>Gift for Kids</h3>
      <p>Fun and joyful gift for kids.</p>
    </div>

  </div>

  <script src="shop.js"></script>
</body>
</html>