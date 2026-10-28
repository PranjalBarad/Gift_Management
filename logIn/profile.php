<?php
session_start();
require_once '../../project/db.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user'])) {
  header('Location: log.php');
  exit;
}

// Get user info from session or database
$user = $_SESSION['user'];

// If you want to fetch fresh data from DB, uncomment below:
// $stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
// $stmt->bind_param("i", $user['id']);
// $stmt->execute();
// $user = $stmt->get_result()->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Profile</title>
  <link rel="stylesheet" href="../../project/common.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../../project/logIn/profile.css">
</head>

<body>
  <div class="profile-wrapper">
    <div class="profile-container">
      <!-- Sidebar -->
      <div class="profile-sidebar">
        <div class="sidebar-header">
          <i class="fas fa-user-circle"></i>
          <h3><?php echo htmlspecialchars($user['name'] ?? 'User'); ?></h3>
          <p class="user-email"><?php echo htmlspecialchars($user['email'] ?? ''); ?></p>
        </div>
        <nav class="sidebar-menu">
          <a href="#profile" class="menu-item active" data-section="profile">
            <i class="fas fa-user"></i> Profile
          </a>
          <a href="../cartPage/cart.php" class="menu-item">
            <i class="fas fa-shopping-cart"></i> My Cart
          </a>
          <a href="orders.php" class="menu-item">
            <i class="fas fa-box"></i> My Orders
          </a>
          <a href="logout.php" class="menu-item logout">
            <i class="fas fa-sign-out-alt"></i> Logout
          </a>
        </nav>
      </div>

      <!-- Main Content -->
      <div class="profile-main">
        <div class="profile-header">
          <h2>Your Profile</h2>
          <p>Manage your account information</p>
        </div>

        <form class="profile-form">
          <div class="form-section">
            <h3 class="section-title">Personal Information</h3>
            
            <div class="form-grid">
              <div class="form-group">
                <label for="name">Full Name</label>
                <div class="input-wrapper">
                  <i class="fas fa-user"></i>
                  <input type="text" id="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" readonly>
                </div>
              </div>

              <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-wrapper">
                  <i class="fas fa-envelope"></i>
                  <input type="email" id="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" readonly>
                </div>
              </div>

              <div class="form-group">
                <label for="username">Username</label>
                <div class="input-wrapper">
                  <i class="fas fa-id-card"></i>
                  <input type="text" id="username" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" readonly>
                </div>
              </div>

              <div class="form-group">
                <label for="phone">Phone Number</label>
                <div class="input-wrapper">
                  <i class="fas fa-phone"></i>
                  <input type="text" id="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" readonly>
                </div>
              </div>
            </div>
          </div>

          <div class="form-actions">
            <button type="button" class="btn-save" disabled>
              <i class="fas fa-save"></i> SAVE PROFILE
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>

</html>