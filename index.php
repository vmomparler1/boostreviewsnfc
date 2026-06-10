<?php
require_once __DIR__ . '/functions.php';

// Root URL without a language → redirect to the visitor's language
if (!isset($_GET['lang'])) {
    header('Location: /' . detect_lang() . '/', true, 302);
    exit;
}

$lang = current_lang();
$page = max(1, (int)($_GET['page'] ?? 1));

$posts = get_published_posts($page);
$total = count_published_posts();
$totalPages = max(1, (int)ceil($total / POSTS_PER_PAGE));

$page_title = SITE_NAME . ' — ' . ($lang === 'es'
    ? 'Blog y comparador de precios de dispositivos NFC para reseñas de Google'
    : 'Blog & price comparison of NFC devices for Google Reviews');
$meta_desc = t('tagline', $lang);
$canonical = lang_url($lang) . ($page > 1 ? '?page=' . $page : '');
$alternates = [];
foreach (LANGS as $l) {
    $alternates[$l] = lang_url($l) . ($page > 1 ? '?page=' . $page : '');
}
$jsonld = [
    '@context' => 'https://schema.org',
    '@type'    => 'Blog',
    'name'     => SITE_NAME,
    'url'      => lang_url($lang),
    'description' => $meta_desc,
    'inLanguage'  => $lang,
];

require __DIR__ . '/partials/head.php';
?>

<h1><?= e(SITE_NAME) ?></h1>
<p class="tagline"><?= e(t('tagline', $lang)) ?></p>

<?php if (!$posts): ?>
  <p class="empty"><?= e(t('no_posts', $lang)) ?></p>
<?php endif; ?>

<section class="post-list">
<?php foreach ($posts as $post): ?>
  <article class="post-card">
    <?php if (!empty($post['cover_image'])): ?>
      <a href="<?= e(post_url($post, $lang)) ?>">
        <img src="<?= e($post['cover_image']) ?>" alt="<?= e($post['title_' . $lang]) ?>" loading="lazy">
      </a>
    <?php endif; ?>
    <div class="post-card-body">
      <h2><a href="<?= e(post_url($post, $lang)) ?>"><?= e($post['title_' . $lang]) ?></a></h2>
      <time datetime="<?= e(date('Y-m-d', strtotime($post['published_at']))) ?>">
        <?= e(format_date($post['published_at'], $lang)) ?>
      </time>
      <p><?= e($post['excerpt_' . $lang]) ?></p>
      <a class="read-more" href="<?= e(post_url($post, $lang)) ?>"><?= e(t('read_more', $lang)) ?> →</a>
    </div>
  </article>
<?php endforeach; ?>
</section>

<?php if ($totalPages > 1): ?>
<nav class="pagination" aria-label="Pagination">
  <?php if ($page > 1): ?>
    <a href="<?= e(lang_url($lang) . ($page > 2 ? '?page=' . ($page - 1) : '')) ?>"><?= e(t('newer', $lang)) ?></a>
  <?php endif; ?>
  <?php if ($page < $totalPages): ?>
    <a href="<?= e(lang_url($lang) . '?page=' . ($page + 1)) ?>"><?= e(t('older', $lang)) ?></a>
  <?php endif; ?>
</nav>
<?php endif; ?>

<?php require __DIR__ . '/partials/foot.php'; ?>
