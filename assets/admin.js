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

// ---------- Image uploads ----------

// Pick a file, POST it to /admin/upload.php, and resolve with its URL
function uploadImage(statusEl) {
  return new Promise(function (resolve) {
    var picker = document.createElement('input');
    picker.type = 'file';
    picker.accept = 'image/jpeg,image/png,image/gif,image/webp';
    // Safari only opens the dialog (and reliably fires "change") for inputs
    // that are attached to the document
    picker.style.display = 'none';
    document.body.appendChild(picker);
    picker.addEventListener('change', function () {
      var file = picker.files[0];
      var data = new FormData();
      if (file) {
        data.append('image', file);
        data.append('csrf', document.querySelector('input[name="csrf"]').value);
      }
      picker.remove();
      if (!file) return;
      statusEl.textContent = 'Uploading…';
      statusEl.classList.remove('error');
      fetch('/admin/upload.php', { method: 'POST', body: data })
        .then(function (res) { return res.json(); })
        .then(function (json) {
          if (json.url) {
            statusEl.textContent = '';
            resolve(json.url);
          } else {
            statusEl.textContent = json.error || 'Upload failed.';
            statusEl.classList.add('error');
          }
        })
        .catch(function () {
          statusEl.textContent = 'Upload failed.';
          statusEl.classList.add('error');
        });
    });
    picker.click();
  });
}

// "Upload image…" buttons that fill a text input (cover image) and show a preview
document.querySelectorAll('[data-upload-to]').forEach(function (btn) {
  var input = document.getElementById(btn.dataset.uploadTo);
  var status = document.querySelector('[data-status-for="' + btn.dataset.uploadTo + '"]');
  var preview = document.getElementById(btn.dataset.uploadTo + '_preview');
  btn.addEventListener('click', function () {
    uploadImage(status).then(function (url) {
      input.value = url;
      if (preview) {
        preview.src = url;
        preview.hidden = false;
      }
    });
  });
  if (preview && input) {
    input.addEventListener('input', function () {
      preview.src = input.value;
      preview.hidden = input.value.trim() === '';
    });
  }
});

// "Insert image…" buttons that upload and insert an <img> tag at the cursor
document.querySelectorAll('[data-insert-image]').forEach(function (btn) {
  var textarea = document.getElementById(btn.dataset.insertImage);
  var status = document.querySelector('[data-status-for="' + btn.dataset.insertImage + '"]');
  var everFocused = false;
  textarea.addEventListener('focus', function () { everFocused = true; });
  btn.addEventListener('click', function () {
    // Capture the cursor now, before the file dialog steals focus.
    // If the textarea was never focused, append at the end instead of position 0.
    var start = everFocused ? textarea.selectionStart : textarea.value.length;
    var end = everFocused ? textarea.selectionEnd : textarea.value.length;
    uploadImage(status).then(function (url) {
      var tag = '<img src="' + url + '" alt="" loading="lazy">';
      var before = textarea.value.slice(0, start);
      var after = textarea.value.slice(end);
      // Keep the tag on its own line so it is easy to spot in the HTML
      if (before !== '' && !/\n$/.test(before)) tag = '\n' + tag;
      if (after === '' || !/^\n/.test(after)) tag = tag + '\n';
      textarea.value = before + tag + after;
      textarea.focus();
      // Place the cursor inside alt="" so a description can be typed right away
      var altPos = start + tag.indexOf('alt="') + 5;
      textarea.setSelectionRange(altPos, altPos);
      // Scroll the insertion point into view
      textarea.blur();
      textarea.focus();
      status.textContent = 'Inserted ' + url + ' — add the alt text.';
    });
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
