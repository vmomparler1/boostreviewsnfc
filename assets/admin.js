// Admin helpers: slug auto-generation + SEO character counters

function slugify(text) {
  return text
    .toLowerCase()
    .trim()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '') // strip accents
    .replace(/ñ/g, 'n')
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '');
}

// Auto-fill the slug from the title until the slug is edited manually
document.querySelectorAll('[data-slug-target]').forEach(function (titleInput) {
  var slugInput = document.getElementById(titleInput.dataset.slugTarget);
  if (!slugInput) return;
  var touched = slugInput.value !== '';
  slugInput.addEventListener('input', function () { touched = true; });
  titleInput.addEventListener('input', function () {
    if (!touched) slugInput.value = slugify(titleInput.value);
  });
});

// Live character counters for SEO title/description fields
document.querySelectorAll('[data-count-for]').forEach(function (counter) {
  var input = document.getElementById(counter.dataset.countFor);
  var max = parseInt(counter.dataset.max, 10);
  if (!input) return;
  function update() {
    counter.textContent = input.value.length + '/' + max;
    counter.classList.toggle('over', input.value.length > max);
  }
  input.addEventListener('input', update);
  update();
});
