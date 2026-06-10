<?php
require_once __DIR__ . '/config.php';

/**
 * Connects using DATABASE_URL. Supports both engines:
 *   mysql://user:pass@host:3306/dbname
 *   postgresql://user:pass@host:5432/dbname?sslmode=require
 */
function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $p = parse_url(DATABASE_URL);
        parse_str($p['query'] ?? '', $q);
        $dbname = ltrim($p['path'], '/');

        if (strpos($p['scheme'], 'mysql') === 0) {
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                $p['host'],
                $p['port'] ?? 3306,
                $dbname
            );
        } else {
            $dsn = sprintf(
                'pgsql:host=%s;port=%d;dbname=%s;sslmode=%s',
                $p['host'],
                $p['port'] ?? 5432,
                $dbname,
                $q['sslmode'] ?? 'require'
            );
        }

        $pdo = new PDO($dsn, $p['user'] ?? null, rawurldecode($p['pass'] ?? ''), [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    return $pdo;
}
