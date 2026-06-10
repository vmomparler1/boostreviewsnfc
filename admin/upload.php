<?php
require_once __DIR__ . '/auth.php';

header('Content-Type: application/json');

function fail(string $msg, int $code = 400): void
{
    http_response_code($code);
    echo json_encode(['error' => $msg]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    fail('Method not allowed', 405);
}
if (!is_logged_in()) {
    fail('Not authenticated', 401);
}
if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
    fail('Invalid CSRF token', 403);
}

if (empty($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    $err = $_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE;
    fail($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE
        ? 'File exceeds the server upload size limit.'
        : 'No file uploaded or upload failed.');
}

$file = $_FILES['image'];

if ($file['size'] > 5 * 1024 * 1024) {
    fail('Image is too large (max 5 MB).');
}

// Validate that it really is an image, not just by extension
$info = @getimagesize($file['tmp_name']);
$allowed = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/gif'  => 'gif',
    'image/webp' => 'webp',
];
$mime = $info['mime'] ?? '';
if (!$info || !isset($allowed[$mime])) {
    fail('Unsupported file type. Use JPG, PNG, GIF or WebP.');
}
$ext = $allowed[$mime];

// Safe, unique filename based on the original name
$base = pathinfo($file['name'], PATHINFO_FILENAME);
$base = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $base));
$base = trim(substr($base, 0, 60), '-') ?: 'image';
$name = $base . '-' . bin2hex(random_bytes(4)) . '.' . $ext;

$subdir = date('Y/m');
$dir = dirname(__DIR__) . '/uploads/' . $subdir;
if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
    fail('Could not create the uploads directory.', 500);
}

if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $name)) {
    fail('Could not save the uploaded file.', 500);
}

echo json_encode(['url' => '/uploads/' . $subdir . '/' . $name]);
