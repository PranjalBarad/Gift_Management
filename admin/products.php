<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Products - Wow Box Admin</title>
  <link rel="stylesheet" href="assets/style.css">
  <script src="assets/script.js" defer></script>
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <h2>🎁 Wow Box</h2>
    <a href="index.php">📊 Dashboard</a>
    <a href="products.php" class="active">📦 Products</a>
    <a href="orders.php">🛒 Orders</a>
    <a href="customers.php">👥 Customers</a>
    <a href="notifications.php">🔔 Notifications</a>
    <a href="#">🚪 Logout</a>
  </div>

  <!-- Main -->
  <div class="main">
    <div class="topbar">
      <h1>Products</h1>
      <div class="profile">
        <img src="https://i.pravatar.cc/36" alt="Admin">
        <span>Admin</span>
      </div>
    </div>

    <button class="btn" onclick="openModal()">➕ Add Product</button>

    <table class="table">
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Price</th>
        <th>Stock</th>
      </tr>
      <tr>
        <td>1</td>
        <td>Festive Hamper</td>
        <td>₹1,299</td>
        <td>10</td>
      </tr>
      <tr>
        <td>2</td>
        <td>Chocolate Box</td>
        <td>₹799</td>
        <td>25</td>
      </tr>
    </table>
  </div>

  <!-- Modal -->
  <div class="modal" id="productModal">
    <div class="modal-content">
      <h2>Add Product</h2>
      <input type="text" placeholder="Product Name">
      <input type="number" placeholder="Price">
      <input type="number" placeholder="Stock">
      <button class="btn">Save</button>
      <button class="btn" style="background:#888" onclick="closeModal()">Cancel</button>
    </div>
  </div>
</body>
</html>
