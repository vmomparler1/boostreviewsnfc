<?php
require_once __DIR__ . '/auth.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Small delay to slow down brute-force attempts
    usleep(300000);
    if (password_verify($_POST['password'] ?? '', ADMIN_PASSWORD_HASH)) {
        session_regenerate_id(true);
        $_SESSION['admin'] = true;
        header('Location: /admin/');
        exit;
    }
    $error = 'Wrong password.';
}

if (is_logged_in()) {
    header('Location: /admin/');
    exit;
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Login — <?= e(SITE_NAME) ?> Admin</title>
<link rel="stylesheet" href="/assets/style.css">
</head>
<body class="admin">
<main class="container" style="max-width:420px; padding-top:4rem;">
  <h1><?= e(SITE_NAME) ?> Admin</h1>
  <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <div class="field">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" required autofocus>
    </div>
    <button class="btn" type="submit">Log in</button>
  </form>
</main>
</body>
</html>
