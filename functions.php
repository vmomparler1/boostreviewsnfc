<?php
require_once __DIR__ . '/db.php';

/** HTML-escape helper */
function e(?string $s): string
{
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}

/** Validate and return the current language, falling back to the default */
function current_lang(): string
{
    $lang = $_GET['lang'] ?? DEFAULT_LANG;
    return in_array($lang, LANGS, true) ? $lang : DEFAULT_LANG;
}

/** Pick the best language from the browser's Accept-Language header */
function detect_lang(): string
{
    $header = strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '');
    foreach (explode(',', $header) as $part) {
        $code = substr(trim($part), 0, 2);
        if (in_array($code, LANGS, true)) {
            return $code;
        }
    }
    return DEFAULT_LANG;
}

/** UI translations */
function t(string $key, string $lang): string
{
    static $strings = [
        'tagline' => [
            'en' => 'Compare prices of NFC review plaques, stands and cards to boost your Google Reviews.',
            'es' => 'Compara precios de placas, soportes y tarjetas NFC para conseguir más reseñas de Google.',
        ],
        'read_more'    => ['en' => 'Read more',          'es' => 'Leer más'],
        'back_to_blog' => ['en' => '← Back to the blog', 'es' => '← Volver al blog'],
        'no_posts'     => ['en' => 'No posts yet. Check back soon!', 'es' => 'Todavía no hay artículos. ¡Vuelve pronto!'],
        'published_on' => ['en' => 'Published on',       'es' => 'Publicado el'],
        'updated_on'   => ['en' => 'Updated on',         'es' => 'Actualizado el'],
        'newer'        => ['en' => '← Newer posts',      'es' => '← Artículos más recientes'],
        'older'        => ['en' => 'Older posts →',      'es' => 'Artículos anteriores →'],
        'not_found'    => ['en' => 'Post not found',     'es' => 'Artículo no encontrado'],
        'not_found_body' => [
            'en' => 'The article you are looking for does not exist or has been moved.',
            'es' => 'El artículo que buscas no existe o se ha movido.',
        ],
    ];
    return $strings[$key][$lang] ?? $strings[$key][DEFAULT_LANG] ?? $key;
}

/** Absolute URL of the blog index for a language */
function lang_url(string $lang): string
{
    return BASE_URL . '/' . $lang . '/';
}

/** Absolute URL of a post in a given language */
function post_url(array $post, string $lang): string
{
    return BASE_URL . '/' . $lang . '/' . $post['slug_' . $lang] . '/';
}

/** Format a date for display in the current language */
function format_date(?string $ts, string $lang): string
{
    if (!$ts) {
        return '';
    }
    $time = strtotime($ts);
    if ($lang === 'es') {
        $months = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
                   'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        return date('j', $time) . ' de ' . $months[(int)date('n', $time) - 1] . ' de ' . date('Y', $time);
    }
    return date('F j, Y', $time);
}

/** Published posts, newest first */
function get_published_posts(int $page = 1): array
{
    $offset = ($page - 1) * POSTS_PER_PAGE;
    $st = db()->prepare(
        "SELECT * FROM blog
         WHERE status = 'published' AND published_at <= NOW()
         ORDER BY published_at DESC
         LIMIT :limit OFFSET :offset"
    );
    $st->bindValue(':limit', POSTS_PER_PAGE, PDO::PARAM_INT);
    $st->bindValue(':offset', $offset, PDO::PARAM_INT);
    $st->execute();
    return $st->fetchAll();
}

function count_published_posts(): int
{
    return (int) db()->query(
        "SELECT COUNT(*) FROM blog WHERE status = 'published' AND published_at <= NOW()"
    )->fetchColumn();
}

/** Find a published post by its slug in a given language */
function get_post_by_slug(string $slug, string $lang): ?array
{
    $st = db()->prepare(
        "SELECT * FROM blog
         WHERE slug_" . ($lang === 'es' ? 'es' : 'en') . " = :slug
           AND status = 'published' AND published_at <= NOW()
         LIMIT 1"
    );
    $st->execute([':slug' => $slug]);
    $post = $st->fetch();
    return $post ?: null;
}
