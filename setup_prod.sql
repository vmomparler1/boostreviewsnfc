CREATE TABLE IF NOT EXISTS blog (
id SERIAL PRIMARY KEY,
slug_en VARCHAR(220) NOT NULL UNIQUE,
slug_es VARCHAR(220) NOT NULL UNIQUE,
title_en VARCHAR(220) NOT NULL,
title_es VARCHAR(220) NOT NULL,
meta_title_en VARCHAR(70),
meta_title_es VARCHAR(70),
meta_desc_en VARCHAR(170),
meta_desc_es VARCHAR(170),
excerpt_en TEXT,
excerpt_es TEXT,
content_en TEXT NOT NULL DEFAULT '',
content_es TEXT NOT NULL DEFAULT '',
cover_image VARCHAR(500),
status VARCHAR(10) NOT NULL DEFAULT 'draft' CHECK (status IN ('draft','published')),
published_at TIMESTAMPTZ,
created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_blog_status_published ON blog (status, published_at DESC);

INSERT INTO blog (slug_en, slug_es, title_en, title_es, meta_title_en, meta_title_es, meta_desc_en, meta_desc_es, excerpt_en, excerpt_es, content_en, content_es, status, published_at)
VALUES (
'best-nfc-google-review-plaques-2026',
'mejores-placas-nfc-resenas-google-2026',
'The Best NFC Google Review Plaques in 2026: Price Comparison',
'Las mejores placas NFC para reseñas de Google en 2026: comparativa de precios',
'Best NFC Google Review Plaques 2026 — Compared',
'Mejores placas NFC para reseñas de Google 2026',
'We compare the top NFC review plaques and stands of 2026 by price, build quality and tap reliability so you can boost your Google Reviews.',
'Comparamos las mejores placas y soportes NFC de 2026 por precio, calidad y fiabilidad para conseguir más reseñas de Google.',
'We compared the most popular NFC review plaques and stands on the market. Here is what we found, from budget cards to premium metal plaques.',
'Hemos comparado las placas y soportes NFC más populares del mercado. Esto es lo que encontramos, desde tarjetas económicas hasta placas metálicas premium.',
'<p>NFC review plaques make it effortless for customers to leave a Google Review: one tap with their phone and the review form opens. But prices range from 10€ to over 60€. Which one is worth it?</p><h2>Quick comparison</h2><table><thead><tr><th>Product</th><th>Type</th><th>Price</th></tr></thead><tbody><tr><td>Just5Stars Plaque</td><td>Wall plaque</td><td>29€</td></tr><tr><td>Generic NFC Card</td><td>Card</td><td>12€</td></tr><tr><td>Premium Metal Stand</td><td>Counter stand</td><td>59€</td></tr></tbody></table><h2>What to look for</h2><p>Check the NFC chip type (NTAG213 is enough), whether a QR code fallback is printed, and if the link is reprogrammable.</p>',
'<p>Las placas NFC hacen que dejar una reseña de Google sea cuestión de un toque con el móvil. Pero los precios van desde 10€ hasta más de 60€. ¿Cuál merece la pena?</p><h2>Comparativa rápida</h2><table><thead><tr><th>Producto</th><th>Tipo</th><th>Precio</th></tr></thead><tbody><tr><td>Placa Just5Stars</td><td>Placa de pared</td><td>29€</td></tr><tr><td>Tarjeta NFC genérica</td><td>Tarjeta</td><td>12€</td></tr><tr><td>Soporte metálico premium</td><td>Soporte de mostrador</td><td>59€</td></tr></tbody></table><h2>Qué tener en cuenta</h2><p>Revisa el tipo de chip NFC (NTAG213 es suficiente), si incluye código QR de respaldo y si el enlace es reprogramable.</p>',
'published',
NOW()
);
