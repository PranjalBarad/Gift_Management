<?php
session_start();

// Include the DB connection. db.php is at the project root relative to this file.
require_once dirname(__DIR__, 1) . '/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Basic trimming
  $email = trim($_POST['email'] ?? '');
  $pass = $_POST['password'] ?? '';
  $cpass = $_POST['cpassword'] ?? '';
  $name = trim($_POST['name'] ?? '');
  $phone = trim($_POST['phone'] ?? '');

  // Validate required fields
  if ($email === '' || $pass === '' || $cpass === '' || $name === '' || $phone === '') {
    $error = 'All fields are required.';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Please enter a valid email address.';
  } elseif ($pass !== $cpass) {
    $error = 'Passwords do not match.';
  } elseif (strlen($pass) < 6) {
    $error = 'Password must be at least 6 characters.';
  } elseif (!preg_match('/^[0-9+\-\s]{7,20}$/', $phone)) {
    // allow digits, spaces, + and - between 7 and 20 chars
    $error = 'Please enter a valid phone number.';
  } else {
    // Check if email already exists
    $stmt = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    if ($stmt === false) {
      $error = 'Database error (prepare).';
    } else {
      $stmt->bind_param('s', $email);
      $stmt->execute();
      $stmt->store_result();

      if ($stmt->num_rows > 0) {
        $error = 'Email already registered.';
      } else {
        // Register user
        $hashed = password_hash($pass, PASSWORD_DEFAULT);
        $stmt->close();

        $ins = $conn->prepare('INSERT INTO users (email, password, name, phone) VALUES (?, ?, ?, ?)');
        if ($ins === false) {
          $error = 'Database error (prepare insert).';
        } else {
          $ins->bind_param('ssss', $email, $hashed, $name, $phone);
          if ($ins->execute()) {
            // Fetch inserted id
            $userId = $ins->insert_id;
            // Log in user
            $_SESSION['user'] = [
              'id' => $userId,
              'email' => $email,
              'name' => $name,
              'phone' => $phone
            ];
            // Redirect to login or profile page — using relative path to log.php
            header('Location: log.php');
            exit;
          } else {
            $error = 'Registration failed. Please try again later.';
          }
          $ins->close();
        }
      }
      $stmt->close();
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register page</title>
  <link rel="stylesheet" href="../../project/logIn/register2.css" />
</head>

<body>
  <section class="container">
    <form class="register-form" action="#" method="post">
      <h2>Register now </h2>
      <?php if (!empty($error)): ?>
        <div style="color:red;"><?php echo $error; ?></div>
      <?php endif; ?>
      <input type="text" placeholder="Name" name="name" required>
      <input type="email" placeholder="Email" name="email" required>
      <input type="text" placeholder="Phone" name="phone" required>
      <input type="password" placeholder="Password" name="password" required />
      <input type="password" placeholder="Confirm password" name="cpassword" required />
      <button type="submit" class="btn" value="Register now" name="submit-btn">Register</button>
      <p class="login-link">Already have an account? <a href="log.php"> LogIn now</a></p>
    </form>
  </section>
</body>

</html>