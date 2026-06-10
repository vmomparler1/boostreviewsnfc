<?php
/**
 * Server diagnostic for BoostReviewsNFC.
 * Upload next to index.php, open /diagnose.php?key=brnfc2026 in the browser,
 * send me the output, then DELETE THIS FILE from the server.
 */
if (($_GET['key'] ?? '') !== 'brnfc2026') {
    http_response_code(403);
    exit('Forbidden');
}
header('Content-Type: text/plain; charset=UTF-8');
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "PHP version: " . PHP_VERSION . "\n";
echo "Web server:  " . ($_SERVER['SERVER_SOFTWARE'] ?? 'unknown') . "\n\n";

echo "--- Extensions ---\n";
foreach (['pdo_pgsql', 'pgsql', 'pdo_mysql', 'mysqli'] as $ext) {
    echo str_pad($ext, 12) . ': ' . (extension_loaded($ext) ? 'YES' : 'NO') . "\n";
}

echo "\n--- mod_rewrite ---\n";
if (function_exists('apache_get_modules')) {
    echo in_array('mod_rewrite', apache_get_modules()) ? "mod_rewrite: loaded\n" : "mod_rewrite: NOT loaded\n";
} else {
    echo "apache_get_modules() unavailable (normal under PHP-FPM/nginx)\n";
}

echo "\n--- config.php ---\n";
if (!file_exists(__DIR__ . '/config.php')) {
    echo "config.php NOT FOUND next to this file\n";
} else {
    require_once __DIR__ . '/config.php';
    echo "DATABASE_URL still has XXXX placeholder: " . (strpos(DATABASE_URL, ':XXXX@') !== false ? 'YES (fix this!)' : 'no, password is set') . "\n";
    echo "BASE_URL: " . BASE_URL . "\n";
}

echo "\n--- Database connection ---\n";
if (!extension_loaded('pdo_pgsql')) {
    echo "SKIPPED: pdo_pgsql extension is missing — this is the cause of the 500 error.\n";
} elseif (defined('DATABASE_URL')) {
    try {
        require_once __DIR__ . '/db.php';
        $count = db()->query('SELECT COUNT(*) FROM blog')->fetchColumn();
        echo "Connection OK — blog table has $count post(s).\n";
    } catch (Throwable $e) {
        echo "Connection FAILED: " . $e->getMessage() . "\n";
    }
}

echo "\n--- Rewrite test ---\n";
echo "Open /es/ — if it 404s while this page loads fine, .htaccess rewrites are not active.\n";
echo "\nDone. Delete this file from the server now.\n";
