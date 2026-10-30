/* Admin behaviours: animations, live search, rich editor, previews */
(function () {
  const $ = (s, r = document) => r.querySelector(s), $$ = (s, r = document) => [...r.querySelectorAll(s)];
  const G = window.gsap;

  /* ---------- GSAP animations ---------- */
  if (G) {
    G.from('.anim-card', { y: 30, opacity: 0, duration: .7, stagger: .08, ease: 'power3.out', clearProps: 'transform,opacity' });
    G.from('.row-anim', { x: -18, opacity: 0, duration: .5, stagger: .04, delay: .2, ease: 'power2.out', clearProps: 'transform,opacity' });
    G.from('.menu-inner > li', { x: -22, opacity: 0, duration: .5, stagger: .035, ease: 'power2.out', clearProps: 'transform,opacity' });
    $$('.count').forEach(el => { const n = +el.dataset.n || 0, o = { v: 0 }; G.to(o, { v: n, duration: 1.4, ease: 'power3.out', onUpdate: () => el.textContent = Math.round(o.v).toLocaleString() }); });
    $$('#toasts .toast').forEach((t, i) => G.to(t, { opacity: 0, x: 60, delay: 4.2 + i * .4, duration: .5, onComplete: () => t.remove() }));
  }

  /* ---------- confirm on destructive forms ---------- */
  document.addEventListener('submit', e => { const f = e.target.closest('form[data-confirm]'); if (f && !confirm(f.dataset.confirm)) e.preventDefault(); });

  /* ---------- image / file previews ---------- */
  document.addEventListener('change', e => {
    const i = e.target.closest('input.img-input'); if (!i || !i.files[0]) return;
    const wrap = i.closest('.upload-box').querySelector('.preview-wrap');
    wrap.innerHTML = '<img class="preview" src="' + URL.createObjectURL(i.files[0]) + '">';
  });
  document.addEventListener('click', e => {
    const b = e.target.closest('.copy-url'); if (!b) return;
    navigator.clipboard && navigator.clipboard.writeText(b.dataset.url); const t = b.innerHTML; b.innerHTML = '<i class="bx bx-check"></i> Copied'; setTimeout(() => b.innerHTML = t, 1200);
  });

  /* ---------- live search (navbar) ---------- */
  const inp = $('#adminSearch'), box = $('#adminSearchBox');
  if (inp) {
    let timer, items = [], cur = -1, ctl;
    const close = () => { box.classList.remove('open'); cur = -1; };
    const render = list => {
      items = list; cur = -1;
      box.innerHTML = list.length ? list.map(r => '<a class="live-item" href="' + r.url + '"><span class="ico"><i class="bx ' + r.icon + '"></i></span><span class="txt"><b>' + esc(r.title) + '</b><small>' + esc(r.sub || '') + '</small></span><span class="tp">' + esc(r.type) + '</span>' + (r.image ? '<img src="' + r.image + '" alt="">' : '') + '</a>').join('') : '<div class="live-empty"><i class="bx bx-search-alt fs-3 d-block"></i>No matches</div>';
      box.classList.add('open');
      if (G) G.from(box.children, { y: 10, opacity: 0, stagger: .03, duration: .3 });
    };
    const esc = s => String(s).replace(/[&<>"]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));
    inp.addEventListener('input', () => {
      clearTimeout(timer); const q = inp.value.trim(); if (!q) return close();
      timer = setTimeout(() => { ctl && ctl.abort(); ctl = new AbortController();
        fetch(ADMIN.search + '?q=' + encodeURIComponent(q), { headers: { Accept: 'application/json' }, signal: ctl.signal, credentials: 'same-origin' }).then(r => r.json()).then(j => render(j.results)).catch(() => {}); }, 160);
    });
    inp.addEventListener('keydown', e => {
      const els = $$('.live-item', box);
      if (e.key === 'ArrowDown' || e.key === 'ArrowUp') { e.preventDefault(); if (!els.length) return; cur = (cur + (e.key === 'ArrowDown' ? 1 : -1) + els.length) % els.length; els.forEach((x, i) => x.classList.toggle('on', i === cur)); els[cur].scrollIntoView({ block: 'nearest' }); }
      else if (e.key === 'Enter' && els[cur]) { e.preventDefault(); location.href = els[cur].href; }
      else if (e.key === 'Escape') { close(); inp.blur(); }
    });
    document.addEventListener('click', e => { if (!e.target.closest('.live-search')) close(); });
    document.addEventListener('keydown', e => { if (e.key === '/' && !/INPUT|TEXTAREA|SELECT/.test(document.activeElement.tagName) && !document.activeElement.isContentEditable) { e.preventDefault(); inp.focus(); } });
  }

  /* ---------- rich text editor (TinyMCE, self-hosted) ---------- */
  function pickFromLibrary(cb, type) {
    let m = $('#mediaPicker');
    if (!m) { document.body.insertAdjacentHTML('beforeend', '<div class="modal fade" id="mediaPicker" tabindex="-1"><div class="modal-dialog modal-xl modal-dialog-scrollable"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Choose from media library</h5><button class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><div class="picker-grid" id="pickerGrid"></div></div></div></div></div>'); m = $('#mediaPicker'); }
    const grid = $('#pickerGrid'); grid.innerHTML = '<div class="text-muted p-3">Loading…</div>';
    const modal = bootstrap.Modal.getOrCreateInstance(m); modal.show();
    fetch(ADMIN.mediaList + (type ? '?type=' + type : ''), { headers: { Accept: 'application/json' }, credentials: 'same-origin' }).then(r => r.json()).then(list => {
      grid.innerHTML = list.length ? '' : '<div class="text-muted p-3">Library is empty — upload files first.</div>';
      list.forEach(it => { const b = document.createElement('button'); b.type = 'button'; b.innerHTML = it.type === 'video' ? '<video src="' + it.url + '" muted preload="metadata"></video>' : '<img src="' + it.url + '" alt="">'; b.onclick = () => { cb(it.url, { alt: it.title || '', title: it.title || '' }); modal.hide(); }; grid.appendChild(b); });
    });
  }
  const css = 'body{font-family:Public Sans,system-ui,sans-serif;font-size:16px;line-height:1.8;color:#364152;padding:14px 18px}img{max-width:100%;height:auto;border-radius:10px}' +
    'figure.image{margin:1em 0;display:table}figure.image figcaption{font-size:13px;color:#8592a3;text-align:center;padding-top:6px}' +
    '.align-left{float:left;margin:6px 26px 16px 0;max-width:50%}.align-right{float:right;margin:6px 0 16px 26px;max-width:50%}' +
    'img.align-center,figure.image.align-center{display:block;margin-left:auto;margin-right:auto;float:none;clear:both}' +
    'p.align-left,h1.align-left,h2.align-left,h3.align-left{float:none;max-width:none;margin:0 0 1em;text-align:left}p.align-center,h2.align-center,h3.align-center{text-align:center}p.align-right,h2.align-right,h3.align-right{float:none;max-width:none;margin:0 0 1em;text-align:right}p.align-justify{text-align:justify}' +
    '.img-full{width:100%}.img-md{width:60%}.img-sm{width:35%}blockquote{border-left:4px solid #696cff;margin-left:0;padding-left:16px;color:#566a7f}pre{background:#f5f5f9;padding:14px;border-radius:8px}table{border-collapse:collapse}td,th{border:1px solid #d9dee3;padding:8px}';
  const T = 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img,figure.image,video';
  if (window.tinymce === undefined) { const s = document.createElement('script'); s.src = ADMIN.tinymce + '/tinymce.min.js'; s.onload = initEditors; if ($('.rte')) document.head.appendChild(s); } else initEditors();
  function initEditors() {
    if (!$('.rte') || !window.tinymce) return;
    $$('textarea.rte').forEach(el => tinymce.init({
      target: el, height: +el.dataset.height || 560, license_key: 'gpl', base_url: ADMIN.tinymce, suffix: '.min', promotion: false, branding: false,
      min_height: 300, resize: true, menubar: 'file edit view insert format table tools',
      plugins: 'advlist autolink lists link image media table code codesample charmap searchreplace visualblocks wordcount fullscreen preview anchor insertdatetime quickbars',
      toolbar: 'undo redo | blocks fontsize | bold italic underline strikethrough forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media table codesample blockquote | removeformat | code visualblocks fullscreen preview',
      quickbars_insert_toolbar: 'image media table', quickbars_selection_toolbar: 'bold italic | blocks | link blockquote',
      image_caption: true, image_advtab: true, image_title: true, automatic_uploads: true, paste_data_images: true,
      image_class_list: [{ title: 'Original size', value: '' }, { title: 'Full width', value: 'img-full' }, { title: 'Medium (60%)', value: 'img-md' }, { title: 'Small (35%)', value: 'img-sm' }],
      formats: {
        alignleft: { selector: T, classes: 'align-left' }, aligncenter: { selector: T, classes: 'align-center' },
        alignright: { selector: T, classes: 'align-right' }, alignjustify: { selector: 'p,h1,h2,h3,h4,h5,h6', classes: 'align-justify' },
      },
      relative_urls: false, remove_script_host: true, convert_urls: true, content_style: css,
      codesample_languages: [{ text: 'PHP', value: 'php' }, { text: 'JavaScript', value: 'javascript' }, { text: 'HTML/XML', value: 'markup' }, { text: 'CSS', value: 'css' }, { text: 'Bash', value: 'bash' }, { text: 'SQL', value: 'sql' }, { text: 'JSON', value: 'json' }],
      images_upload_handler: blob => new Promise((res, rej) => {
        const fd = new FormData(); fd.append('file', blob.blob(), blob.filename());
        fetch(ADMIN.editorUpload, { method: 'POST', headers: { 'X-CSRF-TOKEN': ADMIN.csrf, Accept: 'application/json' }, body: fd, credentials: 'same-origin' })
          .then(r => r.ok ? r.json() : r.json().then(e => { throw new Error(e.message || 'Upload failed'); })).then(j => res(j.location)).catch(e => rej({ message: e.message, remove: true }));
      }),
      file_picker_types: 'image media', file_picker_callback: (cb, v, meta) => pickFromLibrary(cb, meta.filetype === 'media' ? 'video' : 'image'),
    }));
  }
})();
