<?php
require_once __DIR__ . '/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/');
    exit;
}
verify_csrf();

$st = db()->prepare('DELETE FROM blog WHERE id = :id');
$st->execute([':id' => (int)($_POST['id'] ?? 0)]);

header('Location: /admin/?deleted=1');
exit;
