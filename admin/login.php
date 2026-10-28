<?php
session_start();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $user = $_POST['username'] ?? '';
  $pass = $_POST['password'] ?? '';

  // Fixed admin credentials (insecure for production)
  if ($user === 'admin' && $pass === 'admin123') {
    $_SESSION['is_admin'] = true;
    // Redirect to admin dashboard
    header('Location: index.php');
    exit;
  } else {
    $error = 'Invalid username or password';
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin Login - Buze Co</title>
  <link rel="stylesheet" href="assets/style.css">
  <style>
    :root {
      --bg: #f6f7fb;
      --card: #ffffff;
      --muted: #6b7280;
      --accent: #2563eb;
      --glass: rgba(255, 255, 255, 0.6)
    }

    html,
    body {
      height: 100%;
      margin: 0;
      font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Arial;
      background: linear-gradient(180deg, #f3f6ff 0%, #f6f7fb 100%)
    }

    body {
      display: flex;
      align-items: center;
      justify-content: center
    }

    .container {
      width: 100%;
      max-width: 440px;
      padding: 28px;
      animation: fadeIn .6s ease both
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(6px)
      }

      to {
        opacity: 1;
        transform: none
      }
    }

    .card {
      background: var(--card);
      border-radius: 14px;
      padding: 28px 22px;
      box-shadow: 0 10px 30px rgba(16, 24, 40, 0.08);
      border: 1px solid rgba(16, 24, 40, 0.04);
      backdrop-filter: blur(4px)
    }

    .brand {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 14px
    }

    .brand .logo {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      background: linear-gradient(135deg, #4f46e5, #06b6d4);
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-weight: 700;
      box-shadow: 0 6px 18px rgba(79, 70, 229, 0.18);
      font-size: 16px
    }

    h2 {
      margin: 0;
      font-size: 20px
    }

    p.sub {
      margin: 6px 0 18px;
      color: var(--muted);
      font-size: 14px
    }

    .error {
      background: #fff1f2;
      color: #7f1d1d;
      padding: 10px;
      border-radius: 8px;
      margin-bottom: 12px;
      border: 1px solid #fecaca
    }

    .field {
      position: relative;
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 12px
    }

    .field svg {
      position: absolute;
      left: 12px;
      width: 18px;
      height: 18px;
      fill: #9aa4b2
    }

    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 12px 14px 12px 40px;
      border-radius: 10px;
      border: 1px solid #e6e9ef;
      background: #fbfcfe;
      font-size: 15px
    }

    input::placeholder {
      color: #c7d0dc
    }

    input:focus {
      outline: none;
      box-shadow: 0 6px 18px rgba(37, 99, 235, 0.08);
      border-color: var(--accent)
    }

    /* removed show/hide toggle per request */
    .actions {
      display: flex;
      flex-direction: column;
      gap: 10px;
      margin-top: 6px
    }

    button.btn {
      background: linear-gradient(90deg, #3b82f6, #06b6d4);
      color: #fff;
      border: 0;
      padding: 12px 14px;
      border-radius: 10px;
      font-weight: 600;
      cursor: pointer;
      box-shadow: 0 8px 20px rgba(6, 182, 212, 0.12);
      transition: transform .12s ease, box-shadow .12s
    }

    button.btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 14px 30px rgba(6, 182, 212, 0.18)
    }

    .hint {
      margin-top: 12px;
      font-size: 13px;
      color: var(--muted);
      text-align: center
    }

    .small {
      font-size: 13px;
      color: #9aa4b2
    }

    @media (max-width:420px) {
      .container {
        padding: 14px
      }

      .card {
        padding: 18px
      }
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="card">
      <div class="brand">
        <div class="logo" aria-hidden="true">
          <!-- inline svg mark -->
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="3" y="3" width="18" height="18" rx="4" fill="white" opacity="0.06" />
            <path d="M7 12h10M7 8h10M7 16h6" stroke="white" stroke-width="1.5" stroke-linecap="round"
              stroke-linejoin="round" opacity="0.98" />
          </svg>
        </div>
        <div>
          <h2>Admin Sign in</h2>
          <p class="sub">Access the Buze Co admin dashboard</p>
        </div>
      </div>
      <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
      <form method="post" action="" onsubmit="btnState(this)">
        <div class="field">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path
              d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zM4 20v-1c0-2.21 3.58-4 8-4s8 1.79 8 4v1H4z" />
          </svg>
          <input type="text" name="username" placeholder="Username" required autofocus>
        </div>
        <div class="field" style="margin-bottom:6px;">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M12 17a2 2 0 100-4 2 2 0 000 4zM6 8V7a6 6 0 1112 0v1" />
          </svg>
          <input id="pwd" type="password" name="password" placeholder="Password" required>
        </div>
        <div class="actions">
          <button id="submitBtn" class="btn" type="submit">Sign in</button>
        </div>
      </form>
      <div style="display:flex;justify-content:center;align-items:center;margin-top:12px">
        <div class="small" style="color:var(--muted)">Secure access</div>
      </div>
    </div>
  </div>
  <script>
    // show/hide removed
    function btnState(form) {
      const b = document.getElementById('submitBtn'); b.disabled = true; b.textContent = 'Signing in...';
    }
  </script>
</body>

</html>