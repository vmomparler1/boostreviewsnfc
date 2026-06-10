-- BoostReviewsNFC blog — run this once against just5stars_prod
CREATE TABLE IF NOT EXISTS blog (
    id             SERIAL PRIMARY KEY,
    slug_en        VARCHAR(220) NOT NULL UNIQUE,
    slug_es        VARCHAR(220) NOT NULL UNIQUE,
    title_en       VARCHAR(220) NOT NULL,
    title_es       VARCHAR(220) NOT NULL,
    meta_title_en  VARCHAR(70),
    meta_title_es  VARCHAR(70),
    meta_desc_en   VARCHAR(170),
    meta_desc_es   VARCHAR(170),
    excerpt_en     TEXT,
    excerpt_es     TEXT,
    content_en     TEXT NOT NULL DEFAULT '',
    content_es     TEXT NOT NULL DEFAULT '',
    cover_image    VARCHAR(500),
    status         VARCHAR(10) NOT NULL DEFAULT 'draft' CHECK (status IN ('draft', 'published')),
    published_at   TIMESTAMPTZ,
    created_at     TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at     TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_blog_status_published ON blog (status, published_at DESC);
