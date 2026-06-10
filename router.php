<?php
/**
 * Router for PHP's built-in dev server (mirrors the .htaccess rewrites).
 * Usage: php -S 127.0.0.1:8000 router.php
 * Production (Apache) uses .htaccess instead — this file is ignored there.
 */
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($uri === '/sitemap.xml') {
    require __DIR__ . '/sitemap.php';
    return true;
}
if (preg_match('#^/(en|es)/?$#', $uri, $m)) {
    $_GET['lang'] = $m[1];
    require __DIR__ . '/index.php';
    return true;
}
if (preg_match('#^/(en|es)/([a-z0-9-]+)/?$#', $uri, $m)) {
    $_GET['lang'] = $m[1];
    $_GET['slug'] = $m[2];
    require __DIR__ . '/post.php';
    return true;
}
if ($uri === '/admin' || $uri === '/admin/') {
    require __DIR__ . '/admin/index.php';
    return true;
}

// Everything else: serve files / scripts as-is
return false;
