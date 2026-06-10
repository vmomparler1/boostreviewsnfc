<?php
require_once __DIR__ . '/functions.php';

header('Content-Type: application/xml; charset=UTF-8');

$posts = db()->query(
    "SELECT * FROM blog WHERE status = 'published' AND published_at <= NOW() ORDER BY published_at DESC"
)->fetchAll();

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml">
<?php foreach (LANGS as $lang): ?>
  <url>
    <loc><?= e(lang_url($lang)) ?></loc>
<?php foreach (LANGS as $alt): ?>
    <xhtml:link rel="alternate" hreflang="<?= e($alt) ?>" href="<?= e(lang_url($alt)) ?>"/>
<?php endforeach; ?>
    <xhtml:link rel="alternate" hreflang="x-default" href="<?= e(lang_url(DEFAULT_LANG)) ?>"/>
    <changefreq>daily</changefreq>
  </url>
<?php endforeach; ?>
<?php foreach ($posts as $post): ?>
<?php foreach (LANGS as $lang): ?>
  <url>
    <loc><?= e(post_url($post, $lang)) ?></loc>
    <lastmod><?= e(date('c', strtotime($post['updated_at']))) ?></lastmod>
<?php foreach (LANGS as $alt): ?>
    <xhtml:link rel="alternate" hreflang="<?= e($alt) ?>" href="<?= e(post_url($post, $alt)) ?>"/>
<?php endforeach; ?>
    <xhtml:link rel="alternate" hreflang="x-default" href="<?= e(post_url($post, DEFAULT_LANG)) ?>"/>
  </url>
<?php endforeach; ?>
<?php endforeach; ?>
</urlset>
