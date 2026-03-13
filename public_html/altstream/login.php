<?php
require_once __DIR__ . '/auth_check.php';

$error = '';

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Already logged in — go to dashboard
if (is_authenticated()) {
    header('Location: dashboard.php');
    exit;
}

// Handle login attempt
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === ADMIN_USERNAME && password_verify($password, ADMIN_PASSWORD_HASH)) {
        session_regenerate_id(true);
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user'] = $username;
        $_SESSION['login_time'] = time();
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Altstream Admin Login</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Inter', -apple-system, sans-serif;
      background: #0a0e27;
      color: #fff;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
    }
    .login-card {
      background: #161b42;
      border: 1px solid rgba(255,255,255,0.08);
      border-radius: 20px;
      padding: 40px 32px;
      width: 100%;
      max-width: 400px;
    }
    .login-card h1 {
      font-size: 24px;
      font-weight: 700;
      text-align: center;
      margin-bottom: 6px;
      background: linear-gradient(135deg, #00d4ff, #7c3aed);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    .login-card .sub {
      text-align: center;
      color: #a0a3bd;
      font-size: 14px;
      margin-bottom: 28px;
    }
    .form-group { margin-bottom: 16px; }
    .form-group label {
      display: block;
      font-size: 13px;
      font-weight: 500;
      color: #a0a3bd;
      margin-bottom: 6px;
    }
    .form-group input {
      width: 100%;
      padding: 13px 16px;
      background: rgba(255,255,255,0.04);
      border: 1px solid rgba(255,255,255,0.1);
      border-radius: 10px;
      color: #fff;
      font-size: 15px;
      font-family: inherit;
      outline: none;
      transition: border-color 0.2s;
    }
    .form-group input:focus {
      border-color: #00d4ff;
      box-shadow: 0 0 0 3px rgba(0,212,255,0.1);
    }
    .form-group input::placeholder { color: #555e80; }
    .btn-login {
      width: 100%;
      padding: 15px;
      background: linear-gradient(135deg, #00d4ff, #7c3aed);
      border: none;
      border-radius: 10px;
      color: #fff;
      font-size: 15px;
      font-weight: 700;
      font-family: inherit;
      cursor: pointer;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      transition: transform 0.2s, box-shadow 0.2s;
      margin-top: 4px;
    }
    .btn-login:hover {
      transform: translateY(-1px);
      box-shadow: 0 8px 25px rgba(0,212,255,0.3);
    }
    .error-msg {
      background: rgba(239,68,68,0.1);
      border: 1px solid rgba(239,68,68,0.3);
      color: #ef4444;
      padding: 10px 14px;
      border-radius: 8px;
      font-size: 13px;
      margin-bottom: 16px;
      text-align: center;
    }
    .back-link {
      display: block;
      text-align: center;
      margin-top: 20px;
      color: #555e80;
      font-size: 13px;
      text-decoration: none;
    }
    .back-link:hover { color: #a0a3bd; }
  </style>
  <script src="/navbar.js" defer></script>
</head>
<body>
  <div class="login-card">
    <h1>Altstream Admin</h1>
    <p class="sub">Sign in to access the dashboard</p>

    <?php if ($error): ?>
      <div class="error-msg"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="Enter username" required autofocus>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Enter password" required>
      </div>
      <button type="submit" class="btn-login">Sign In</button>
    </form>
    <a href="/" class="back-link">&larr; Back to Hub</a>
  </div>
</body>
</html>
