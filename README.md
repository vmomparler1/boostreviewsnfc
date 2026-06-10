# BoostReviewsNFC Blog

Simple bilingual (EN/ES) SEO-friendly blog with a minimal CMS. Plain PHP, vanilla JS, PostgreSQL.

## Setup

1. **Create the table** — run `schema.sql` against the `just5stars_prod` database.
2. **Configure** — in `config.php`:
   - Replace `XXXX` in `DATABASE_URL` with the real DB password.
   - Set `BASE_URL` to the real domain (used for canonicals, hreflang, sitemap).
3. **Deploy** — upload everything to an Apache host with PHP ≥ 8.0 and the
   `pdo_pgsql` extension. `.htaccess` handles the pretty URLs (needs
   `mod_rewrite` and `AllowOverride All`).

## URLs

| URL | What |
|---|---|
| `/` | Redirects to `/en/` or `/es/` based on browser language |
| `/en/`, `/es/` | Blog index per language |
| `/en/my-post/`, `/es/mi-articulo/` | Single post |
| `/sitemap.xml` | Dynamic sitemap with hreflang alternates |
| `/admin/` | CMS (password protected) |

## CMS

Log in at `/admin/login.php`. Each post holds both languages in one row
(title, slug, SEO title/description, excerpt and HTML content per language),
plus an optional cover image, draft/published status and a publish date.
Slugs auto-generate from titles; SEO fields show live character counters.

### Image uploads

Images (JPG/PNG/GIF/WebP, max 5 MB) can be uploaded from the post editor:

- **Cover image** — "Upload image…" below the cover field uploads the file
  and fills in its URL (a plain external URL still works too).
- **Inline images** — "Insert image…" below each content textarea uploads
  the file and inserts an `<img>` tag at the cursor, leaving the caret
  inside `alt=""` so you can type the description right away.

Files land in `/uploads/YYYY/MM/` with a sanitized, unique filename. Make
sure the `uploads/` directory is writable by the web server. Uploads are
validated server-side (real image check, type whitelist, size limit) and the
directory's `.htaccess` blocks script execution.

## SEO features

- Per-language `<title>`, meta description, canonical URL
- `hreflang` alternates (en / es / x-default) on every page and in the sitemap
- Open Graph + Twitter Card tags
- JSON-LD structured data (`Blog` on the index, `BlogPosting` on posts)
- Semantic HTML, lazy-loaded images, `robots.txt` blocking `/admin/`
- Drafts and future-dated posts are never exposed publicly

## Security notes

- Admin password stored as a bcrypt hash (`config.php`)
- CSRF tokens on all admin forms, prepared statements everywhere
- `.htaccess` blocks direct access to `config.php`, `db.php`, `functions.php`, `schema.sql`
