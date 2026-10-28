<?php
session_start();
require_once '../../project/db.php'; // <-- Correct DB connection

// Check for remember me cookie
if (!isset($_SESSION['user']) && isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE remember_token=?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    if ($user) {
        $_SESSION['user'] = $user;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $pass = $_POST['password'] ?? '';

    // Admin login shortcut
    if ($email === 'yesha@gmail.com' && $pass === '123') {
        header('Location: ../admin/index.php');
        exit;
    }

    // Check if user exists in DB
    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($pass, $user['password'])) {
        $_SESSION['user'] = $user;

        // Always set a persistent login cookie
        $token = bin2hex(random_bytes(16));
        setcookie('remember_token', $token, time() + (86400 * 30), "/"); // 30 days

        // Save token in DB
        $stmt = $conn->prepare("UPDATE users SET remember_token=? WHERE id=?");
        $stmt->bind_param("si", $token, $user['id']);
        $stmt->execute();

        // Redirect as needed
        header('Location: ../homePage/home.php');
        exit;
    } else {
        $error = "Invalid email or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login page</title>
  <link rel="stylesheet" href="../../project/logIn/logc.css" />
</head>
<body>
  <div class="container" >
    <form class="login-form" action="" method="post">
      <h2>Login  🎁</h2>
      <?php if (!empty($error)): ?>
        <div style="color:red;"><?php echo $error; ?></div><br>
      <?php endif; ?>
      <input class="input2" type="email" name="email" placeholder="Email" id="em" required >
      <input class="input2" type="password" name="password" id="pass" placeholder="Password" required />
      <button class="btn" type="submit">Login</button>
      <p class="register-link">Don't have an account? <a href="register.php"> Register now</a></p>
    </form>
  </div>
</body>
</html>