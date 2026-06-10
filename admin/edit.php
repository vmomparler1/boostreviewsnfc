<?php
require_once __DIR__ . '/auth.php';
require_login();

function slugify(string $s): string
{
    $s = mb_strtolower(trim($s), 'UTF-8');
    $s = strtr($s, ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ü'=>'u','ñ'=>'n']);
    $s = preg_replace('/[^a-z0-9]+/', '-', $s);
    return trim($s, '-');
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $id = (int)($_POST['id'] ?? 0);

    $data = [
        'title_en'      => trim($_POST['title_en'] ?? ''),
        'title_es'      => trim($_POST['title_es'] ?? ''),
        'slug_en'       => slugify(($_POST['slug_en'] ?? '') ?: ($_POST['title_en'] ?? '')),
        'slug_es'       => slugify(($_POST['slug_es'] ?? '') ?: ($_POST['title_es'] ?? '')),
        'meta_title_en' => trim($_POST['meta_title_en'] ?? ''),
        'meta_title_es' => trim($_POST['meta_title_es'] ?? ''),
        'meta_desc_en'  => trim($_POST['meta_desc_en'] ?? ''),
        'meta_desc_es'  => trim($_POST['meta_desc_es'] ?? ''),
        'excerpt_en'    => trim($_POST['excerpt_en'] ?? ''),
        'excerpt_es'    => trim($_POST['excerpt_es'] ?? ''),
        'content_en'    => $_POST['content_en'] ?? '',
        'content_es'    => $_POST['content_es'] ?? '',
        'cover_image'   => trim($_POST['cover_image'] ?? ''),
        'status'        => ($_POST['status'] ?? 'draft') === 'published' ? 'published' : 'draft',
        'published_at'  => ($_POST['published_at'] ?? '') !== '' ? str_replace('T', ' ', $_POST['published_at']) : null,
    ];

    if ($data['title_en'] === '' || $data['title_es'] === '' || $data['slug_en'] === '' || $data['slug_es'] === '') {
        $error = 'Titles and slugs are required in both languages.';
    } else {
        if ($data['status'] === 'published' && !$data['published_at']) {
            $data['published_at'] = date('Y-m-d H:i:s');
        }
        try {
            if ($id > 0) {
                $sets = [];
                foreach (array_keys($data) as $col) {
                    $sets[] = "$col = :$col";
                }
                $sql = 'UPDATE blog SET ' . implode(', ', $sets) . ', updated_at = NOW() WHERE id = :id';
                $data['id'] = $id;
                db()->prepare($sql)->execute($data);
            } else {
                $cols = array_keys($data);
                $sql = 'INSERT INTO blog (' . implode(', ', $cols) . ') VALUES (:' . implode(', :', $cols) . ')';
                db()->prepare($sql)->execute($data);
            }
            header('Location: /admin/?saved=1');
            exit;
        } catch (PDOException $ex) {
            $error = in_array($ex->getCode(), ['23000', '23505'], true)
                ? 'A post with that slug already exists. Choose a different slug.'
                : 'Database error: ' . $ex->getMessage();
        }
    }
    $post = $data + ['id' => $id];
} elseif ($id > 0) {
    $st = db()->prepare('SELECT * FROM blog WHERE id = :id');
    $st->execute([':id' => $id]);
    $post = $st->fetch();
    if (!$post) {
        header('Location: /admin/');
        exit;
    }
} else {
    $post = [
        'id' => 0, 'title_en' => '', 'title_es' => '', 'slug_en' => '', 'slug_es' => '',
        'meta_title_en' => '', 'meta_title_es' => '', 'meta_desc_en' => '', 'meta_desc_es' => '',
        'excerpt_en' => '', 'excerpt_es' => '', 'content_en' => '', 'content_es' => '',
        'cover_image' => '', 'status' => 'draft', 'published_at' => null,
    ];
}

$publishedValue = $post['published_at'] ? date('Y-m-d\TH:i', strtotime($post['published_at'])) : '';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= $post['id'] ? 'Edit post' : 'New post' ?> — <?= e(SITE_NAME) ?> Admin</title>
<link rel="stylesheet" href="/assets/style.css?v=<?= filemtime(dirname(__DIR__) . '/assets/style.css') ?>">
</head>
<body class="admin">
<main class="container">
  <div class="admin-bar">
    <h1><?= $post['id'] ? 'Edit post' : 'New post' ?></h1>
    <a class="btn secondary" href="/admin/">← All posts</a>
  </div>

  <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>

  <form method="post" action="/admin/edit.php">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="id" value="<?= (int)$post['id'] ?>">

    <div class="lang-cols">
      <?php foreach (['en' => 'English 🇬🇧', 'es' => 'Español 🇪🇸'] as $l => $label): ?>
      <section class="lang-col">
        <h3><?= $label ?></h3>
        <div class="field">
          <label for="title_<?= $l ?>">Title</label>
          <input type="text" id="title_<?= $l ?>" name="title_<?= $l ?>" value="<?= e($post['title_' . $l]) ?>" data-slug-target="slug_<?= $l ?>" required>
        </div>
        <div class="field">
          <label for="slug_<?= $l ?>">Slug (URL)</label>
          <input type="text" id="slug_<?= $l ?>" name="slug_<?= $l ?>" value="<?= e($post['slug_' . $l]) ?>" pattern="[a-z0-9-]+">
        </div>
        <div class="field">
          <label for="meta_title_<?= $l ?>">SEO title <span class="char-count" data-count-for="meta_title_<?= $l ?>" data-max="60"></span></label>
          <input type="text" id="meta_title_<?= $l ?>" name="meta_title_<?= $l ?>" maxlength="70" value="<?= e($post['meta_title_' . $l]) ?>">
        </div>
        <div class="field">
          <label for="meta_desc_<?= $l ?>">SEO description <span class="char-count" data-count-for="meta_desc_<?= $l ?>" data-max="160"></span></label>
          <textarea id="meta_desc_<?= $l ?>" name="meta_desc_<?= $l ?>" maxlength="170" rows="2"><?= e($post['meta_desc_' . $l]) ?></textarea>
        </div>
        <div class="field">
          <label for="excerpt_<?= $l ?>">Excerpt (shown in the post list)</label>
          <textarea id="excerpt_<?= $l ?>" name="excerpt_<?= $l ?>" rows="3"><?= e($post['excerpt_' . $l]) ?></textarea>
        </div>
        <div class="field">
          <label for="content_<?= $l ?>">Content (HTML allowed)</label>
          <textarea class="content" id="content_<?= $l ?>" name="content_<?= $l ?>"><?= e($post['content_' . $l]) ?></textarea>
          <div class="upload-row">
            <button type="button" class="btn secondary btn-small" data-insert-image="content_<?= $l ?>">Insert image…</button>
            <span class="upload-status" data-status-for="content_<?= $l ?>"></span>
          </div>
        </div>
      </section>
      <?php endforeach; ?>
    </div>

    <div class="field" style="margin-top:1.5rem;">
      <label for="cover_image">Cover image (optional)</label>
      <input type="text" id="cover_image" name="cover_image" value="<?= e($post['cover_image']) ?>" placeholder="https://... or upload below">
      <div class="upload-row">
        <button type="button" class="btn secondary btn-small" data-upload-to="cover_image">Upload image…</button>
        <span class="upload-status" data-status-for="cover_image"></span>
      </div>
      <img id="cover_image_preview" class="cover-preview" src="<?= e($post['cover_image']) ?>" alt="" <?= $post['cover_image'] ? '' : 'hidden' ?>>
    </div>

    <div class="field">
      <label for="status">Status</label>
      <select id="status" name="status">
        <option value="draft" <?= $post['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
        <option value="published" <?= $post['status'] === 'published' ? 'selected' : '' ?>>Published</option>
      </select>
    </div>

    <div class="field">
      <label for="published_at">Publish date (leave empty to use "now" when publishing)</label>
      <input type="datetime-local" id="published_at" name="published_at" value="<?= e($publishedValue) ?>">
    </div>

    <p>
      <button class="btn" type="submit">Save</button>
      <?php if ($post['id']): ?>
        <a class="btn secondary" href="/en/<?= e($post['slug_en']) ?>/" target="_blank">Preview EN</a>
        <a class="btn secondary" href="/es/<?= e($post['slug_es']) ?>/" target="_blank">Preview ES</a>
      <?php endif; ?>
    </p>
  </form>

  <?php if ($post['id']): ?>
  <form method="post" action="/admin/delete.php" onsubmit="return confirm('Delete this post permanently?');">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="id" value="<?= (int)$post['id'] ?>">
    <button class="btn danger" type="submit">Delete post</button>
  </form>
  <?php endif; ?>
</main>
<script src="/assets/admin.js?v=<?= filemtime(dirname(__DIR__) . '/assets/admin.js') ?>"></script>
</body>
</html>
