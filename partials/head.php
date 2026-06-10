<?php
/**
 * Expects: $lang, $page_title, $meta_desc, $canonical, $alternates (lang => url),
 * optional $og_type, $og_image, $jsonld (array)
 */
?><!DOCTYPE html>
<html lang="<?= e($lang) ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($page_title) ?></title>
<meta name="description" content="<?= e($meta_desc) ?>">
<link rel="canonical" href="<?= e($canonical) ?>">
<?php foreach ($alternates as $altLang => $altUrl): ?>
<link rel="alternate" hreflang="<?= e($altLang) ?>" href="<?= e($altUrl) ?>">
<?php endforeach; ?>
<link rel="alternate" hreflang="x-default" href="<?= e($alternates[DEFAULT_LANG]) ?>">
<meta property="og:site_name" content="<?= e(SITE_NAME) ?>">
<meta property="og:type" content="<?= e($og_type ?? 'website') ?>">
<meta property="og:title" content="<?= e($page_title) ?>">
<meta property="og:description" content="<?= e($meta_desc) ?>">
<meta property="og:url" content="<?= e($canonical) ?>">
<meta property="og:locale" content="<?= $lang === 'es' ? 'es_ES' : 'en_US' ?>">
<?php if (!empty($og_image)): ?>
<meta property="og:image" content="<?= e($og_image) ?>">
<?php endif; ?>
<meta name="twitter:card" content="summary_large_image">
<link rel="stylesheet" href="/assets/style.css">
<?php if (!empty($jsonld)): ?>
<script type="application/ld+json"><?= json_encode($jsonld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
<?php endif; ?>
</head>
<body>
<header class="site-header">
  <div class="container header-inner">
    <a class="logo" href="<?= e(lang_url($lang)) ?>"><?= e(SITE_NAME) ?></a>
    <nav class="lang-switch" aria-label="Language">
      <?php foreach ($alternates as $altLang => $altUrl): ?>
        <a href="<?= e($altUrl) ?>" <?= $altLang === $lang ? 'class="active" aria-current="page"' : '' ?> lang="<?= e($altLang) ?>"><?= strtoupper(e($altLang)) ?></a>
      <?php endforeach; ?>
    </nav>
  </div>
</header>
<main class="container">
