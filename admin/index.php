<?php
require_once __DIR__ . '/auth.php';
require_login();

$posts = db()->query("SELECT * FROM blog ORDER BY COALESCE(published_at, created_at) DESC")->fetchAll();
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Posts — <?= e(SITE_NAME) ?> Admin</title>
<link rel="stylesheet" href="/assets/style.css">
</head>
<body class="admin">
<main class="container">
  <div class="admin-bar">
    <h1>Posts</h1>
    <div>
      <a class="btn" href="/admin/edit.php">+ New post</a>
      <a class="btn secondary" href="/en/" target="_blank">View site</a>
      <a class="btn secondary" href="/admin/logout.php">Log out</a>
    </div>
  </div>

  <?php if (isset($_GET['saved'])): ?><div class="alert success">Post saved.</div><?php endif; ?>
  <?php if (isset($_GET['deleted'])): ?><div class="alert success">Post deleted.</div><?php endif; ?>

  <?php if (!$posts): ?>
    <p>No posts yet. Create your first one!</p>
  <?php else: ?>
  <table class="admin-table">
    <thead>
      <tr><th>Title (EN)</th><th>Status</th><th>Published</th><th></th></tr>
    </thead>
    <tbody>
      <?php foreach ($posts as $post): ?>
      <tr>
        <td>
          <a href="/admin/edit.php?id=<?= (int)$post['id'] ?>"><?= e($post['title_en']) ?></a>
        </td>
        <td><span class="badge <?= e($post['status']) ?>"><?= e($post['status']) ?></span></td>
        <td><?= $post['published_at'] ? e(date('Y-m-d H:i', strtotime($post['published_at']))) : '—' ?></td>
        <td>
          <?php if ($post['status'] === 'published'): ?>
            <a href="/en/<?= e($post['slug_en']) ?>/" target="_blank">View</a> ·
          <?php endif; ?>
          <a href="/admin/edit.php?id=<?= (int)$post['id'] ?>">Edit</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</main>
</body>
</html>
