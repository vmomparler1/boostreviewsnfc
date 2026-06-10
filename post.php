<?php
require_once __DIR__ . '/functions.php';

$lang = current_lang();
$slug = $_GET['slug'] ?? '';
$post = $slug !== '' ? get_post_by_slug($slug, $lang) : null;

if (!$post) {
    http_response_code(404);
    $page_title = t('not_found', $lang) . ' — ' . SITE_NAME;
    $meta_desc  = t('not_found_body', $lang);
    $canonical  = lang_url($lang);
    $alternates = [];
    foreach (LANGS as $l) {
        $alternates[$l] = lang_url($l);
    }
    require __DIR__ . '/partials/head.php';
    echo '<h1>' . e(t('not_found', $lang)) . '</h1>';
    echo '<p>' . e(t('not_found_body', $lang)) . '</p>';
    echo '<p><a href="' . e(lang_url($lang)) . '">' . e(t('back_to_blog', $lang)) . '</a></p>';
    require __DIR__ . '/partials/foot.php';
    exit;
}

$title      = $post['title_' . $lang];
$page_title = ($post['meta_title_' . $lang] ?: $title) . ' — ' . SITE_NAME;
$meta_desc  = $post['meta_desc_' . $lang] ?: (string)$post['excerpt_' . $lang];
$canonical  = post_url($post, $lang);
$og_type    = 'article';
$og_image   = $post['cover_image'] ?: null;
$alternates = [];
foreach (LANGS as $l) {
    $alternates[$l] = post_url($post, $l);
}
$jsonld = [
    '@context' => 'https://schema.org',
    '@type'    => 'BlogPosting',
    'headline' => $title,
    'description'   => $meta_desc,
    'datePublished' => date('c', strtotime($post['published_at'])),
    'dateModified'  => date('c', strtotime($post['updated_at'])),
    'inLanguage'    => $lang,
    'mainEntityOfPage' => $canonical,
    'image'     => $og_image ?: null,
    'author'    => ['@type' => 'Organization', 'name' => SITE_NAME, 'url' => BASE_URL],
    'publisher' => ['@type' => 'Organization', 'name' => SITE_NAME, 'url' => BASE_URL],
];
$jsonld = array_filter($jsonld, function ($v) { return $v !== null; });

require __DIR__ . '/partials/head.php';
?>

<article class="post">
  <header>
    <h1><?= e($title) ?></h1>
    <p class="post-meta">
      <time datetime="<?= e(date('Y-m-d', strtotime($post['published_at']))) ?>">
        <?= e(t('published_on', $lang)) ?> <?= e(format_date($post['published_at'], $lang)) ?>
      </time>
    </p>
  </header>

  <?php if (!empty($post['cover_image'])): ?>
    <img class="cover" src="<?= e($post['cover_image']) ?>" alt="<?= e($title) ?>">
  <?php endif; ?>

  <div class="post-content">
    <?= $post['content_' . $lang] /* trusted HTML authored in the admin panel */ ?>
  </div>

  <p><a href="<?= e(lang_url($lang)) ?>"><?= e(t('back_to_blog', $lang)) ?></a></p>
</article>

<?php require __DIR__ . '/partials/foot.php'; ?>
