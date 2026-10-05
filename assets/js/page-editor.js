/* Grenvíos — Editor de Página en línea (Sidebar + Acordeones) */
(function () {
  'use strict';

  /* ── Modal de confirmación con la paleta Grenvíos (reemplaza a window.confirm) ── */
  function nepConfirm(message, opts) {
    opts = opts || {};
    return new Promise(function (resolve) {
      var old = document.querySelector('.nep-confirm-overlay');
      if (old) old.remove();
      var ov = document.createElement('div');
      ov.className = 'nep-confirm-overlay';
      ov.innerHTML =
        '<div class="nep-confirm" role="dialog" aria-modal="true" aria-label="Confirmar">' +
          '<div class="nep-confirm-icon"><i class="fa-solid fa-trash-can"></i></div>' +
          '<h3 class="nep-confirm-title"></h3>' +
          '<p class="nep-confirm-msg"></p>' +
          '<div class="nep-confirm-actions">' +
            '<button type="button" class="nep-confirm-cancel"></button>' +
            '<button type="button" class="nep-confirm-ok"><i class="fa-solid fa-trash"></i> <span></span></button>' +
          '</div>' +
        '</div>';
      ov.querySelector('.nep-confirm-title').textContent = opts.title || '¿Eliminar elemento?';
      ov.querySelector('.nep-confirm-msg').textContent   = message || '';
      ov.querySelector('.nep-confirm-cancel').textContent = opts.cancel || 'Cancelar';
      ov.querySelector('.nep-confirm-ok span').textContent = opts.ok || 'Eliminar';
      document.body.appendChild(ov);
      requestAnimationFrame(function () { ov.classList.add('show'); });

      function done(val) {
        ov.classList.remove('show');
        document.removeEventListener('keydown', onKey);
        setTimeout(function () { if (ov.parentNode) ov.remove(); }, 220);
        resolve(val);
      }
      function onKey(e) {
        if (e.key === 'Escape') done(false);
        else if (e.key === 'Enter') done(true);
      }
      ov.querySelector('.nep-confirm-cancel').addEventListener('click', function () { done(false); });
      ov.querySelector('.nep-confirm-ok').addEventListener('click', function () { done(true); });
      ov.addEventListener('click', function (e) { if (e.target === ov) done(false); });
      document.addEventListener('keydown', onKey);
      var okb = ov.querySelector('.nep-confirm-ok');
      if (okb) okb.focus();
    });
  }

  function init() {
  if (!window.grenviosEditorCfg) return;

  const cfg    = window.grenviosEditorCfg;
  const fab    = document.getElementById('nep-fab');
  const panel  = document.getElementById('nep-panel');
  const closeB = document.getElementById('nep-close');
  const saveB  = document.getElementById('nep-save');
  const status = document.getElementById('nep-status');
  if (!fab || !panel) return;

  /* ── Abrir / cerrar ── */
  var hasLenis = function () { return window.lenis && typeof window.lenis.stop === 'function'; };
  // Con Lenis: se detiene el scroll suave (bloquea trackpad/rueda) SIN overflow:hidden,
  // para que lenis.scrollTo(force) pueda llevar a la sección. Sin Lenis: overflow:hidden.
  function lockPage()   { if ( hasLenis() ) { window.lenis.stop(); } else { document.documentElement.classList.add('nep-lock'); } }
  function unlockPage() { document.documentElement.classList.remove('nep-lock'); if ( hasLenis() && typeof window.lenis.start === 'function' ) window.lenis.start(); }
  // Lleva (scroll) la PÁGINA a una sección aunque el scroll esté bloqueado.
  /* Secciones pintadas por PHP no tienen ancla: se localizan por el texto de
     su primer campo (lo que hay antes de cualquier {{token}}). */
  function findByText(acc) {
    if (!acc) return null;
    var inputs = acc.querySelectorAll('input[type="text"][data-field-key], textarea[data-field-key]');
    for (var i = 0; i < inputs.length; i++) {
      var doc = new DOMParser().parseFromString(inputs[i].value || '', 'text/html');
      var txt = (doc.body.textContent || '').split('{{')[0].replace(/\s+/g, ' ').trim().slice(0, 40);
      if (txt.length < 6) continue;
      var nodes = document.querySelectorAll('h1,h2,h3,h4,p,li,span,a');
      for (var j = 0; j < nodes.length; j++) {
        if (panel.contains(nodes[j]) || nodes[j].closest('header, footer, nav')) continue;
        if ((nodes[j].textContent || '').replace(/\s+/g, ' ').indexOf(txt) !== -1) {
          return nodes[j].closest('section') || nodes[j];
        }
      }
    }
    return null;
  }
  function scrollPageTo(sel, acc) {
    var el = null;
    try { el = sel ? document.querySelector(sel) : null; } catch (e) { el = null; }
    if (!el) el = findByText(acc);
    if (!el) return;
    if ( hasLenis() && typeof window.lenis.scrollTo === 'function' ) {
      window.lenis.scrollTo(el, { offset: -110, duration: 0.6, force: true });
    } else {
      document.documentElement.classList.remove('nep-lock');
      el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  }
  function openPanel()  { panel.classList.add('open');  fab.classList.add('active'); lockPage(); }
  function closePanel() { panel.classList.remove('open'); fab.classList.remove('active'); unlockPage(); clearHighlight(); }
  fab.addEventListener('click', () => panel.classList.contains('open') ? closePanel() : openPanel());
  closeB.addEventListener('click', closePanel);

  /* ── La rueda del ratón sobre el panel SIEMPRE desplaza el sidebar, nunca la página ── */
  panel.addEventListener('wheel', function (e) {
    const b = document.getElementById('nep-body');
    if (!b) return;
    const step = e.deltaY * (e.deltaMode === 1 ? 16 : (e.deltaMode === 2 ? b.clientHeight : 1));
    b.scrollTop += step;
    e.preventDefault();   // evita que el desplazamiento pase a la página
  }, { passive: false });
  /* Táctil: contener el scroll dentro del panel */
  panel.addEventListener('touchmove', function (e) { e.stopPropagation(); }, { passive: true });

  /* ── Acordeones + resaltado de la sección en la página ── */
  function clearHighlight() {
    document.querySelectorAll('.nep-section-hl').forEach(el => {
      el.classList.remove('nep-section-hl');
      el.removeAttribute('data-nep-label');
    });
  }
  panel.querySelectorAll('.nep-acc-header').forEach(header => {
    header.addEventListener('click', function () {
      const acc    = this.closest('.nep-accordion');
      const body   = acc.querySelector('.nep-acc-body');
      const isOpen = this.classList.contains('open');
      panel.querySelectorAll('.nep-acc-header').forEach(h => {
        h.classList.remove('open');
        h.closest('.nep-accordion').querySelector('.nep-acc-body').classList.remove('open');
      });
      clearHighlight();
      if (!isOpen) {
        this.classList.add('open');
        body.classList.add('open');
        // Sidebar: sube la sección abierta al tope del panel para ver sus campos.
        const bodyEl = document.getElementById('nep-body');
        if (bodyEl) {
          const delta = acc.getBoundingClientRect().top - bodyEl.getBoundingClientRect().top;
          bodyEl.scrollBy({ top: delta, behavior: 'smooth' });
        }
        // Página: lleva (scroll) a la sección que se está editando (aunque esté bloqueada).
        scrollPageTo(acc.dataset.sel, acc);
      }
    });
  });

  /* ── Selector de imágenes (wp.media) ── */
  function wrapInput(wrap) { return wrap ? wrap.querySelector('input[type="hidden"]') : null; }
  panel.addEventListener('click', e => {
    const btn = e.target.closest('.nep-img-pick');
    if (!btn) return;
    if (typeof wp === 'undefined' || !wp.media) { alert('El selector de medios de WordPress no está disponible.'); return; }
    const wrap = btn.closest('.nep-img-wrap');
    const inp  = wrapInput(wrap);
    if (!inp) return;
    const frame = wp.media({ title: 'Seleccionar imagen', button: { text: 'Usar esta imagen' }, multiple: false });
    frame.on('select', () => {
      const att = frame.state().get('selection').first().toJSON();
      inp.value = att.url;
      const prev = wrap.querySelector('.nep-img-preview');
      if (prev) { prev.style.backgroundImage = 'url(' + att.url + ')'; prev.innerHTML = ''; }
      btn.innerHTML = '<i class="fa-solid fa-upload"></i> Cambiar';
      const rem = wrap.querySelector('.nep-img-remove');
      if (rem) rem.style.display = '';
    });
    frame.open();
  });
  panel.addEventListener('click', e => {
    const btn = e.target.closest('.nep-img-remove');
    if (!btn) return;
    const wrap = btn.closest('.nep-img-wrap');
    const inp  = wrapInput(wrap);
    if (!inp) return;
    inp.value = '';
    const prev = wrap.querySelector('.nep-img-preview');
    if (prev) { prev.style.backgroundImage = ''; prev.innerHTML = '<i class="fa-solid fa-image"></i>'; }
    const pick = wrap.querySelector('.nep-img-pick');
    if (pick) pick.innerHTML = '<i class="fa-solid fa-upload"></i> Seleccionar';
    btn.style.display = 'none';
  });

  /* ── Repeaters: agregar / quitar / reordenar ── */
  function renumber(acc) {
    if (!acc) return;
    const label = acc.dataset.repItemlabel || 'Item';
    acc.querySelectorAll('.nep-rep-list > .nep-rep-item').forEach((item, i) => {
      const t = item.querySelector('.nep-rep-item-title');
      if (t) t.textContent = label + ' ' + (i + 1);
    });
  }
  panel.addEventListener('click', e => {
    const addBtn = e.target.closest('.nep-rep-add');
    if (addBtn) {
      const acc  = addBtn.closest('[data-rep-key]');
      const tpl  = acc.querySelector('.nep-rep-tpl');
      const list = acc.querySelector('.nep-rep-list');
      if (!tpl || !list) return;
      const node = tpl.content.firstElementChild.cloneNode(true);
      list.appendChild(node);
      renumber(acc);
      node.scrollIntoView({ behavior: 'smooth', block: 'center' });
      const firstInput = node.querySelector('input[type="text"], textarea');
      if (firstInput) firstInput.focus();
      return;
    }
    const rmBtn = e.target.closest('.nep-rep-remove');
    if (rmBtn) {
      const item = rmBtn.closest('.nep-rep-item');
      const acc  = rmBtn.closest('[data-rep-key]');
      const name = (acc && acc.dataset.repItemlabel) ? acc.dataset.repItemlabel.toLowerCase() : 'elemento';
      if (item) {
        nepConfirm('Se eliminará este ' + name + ' cuando guardes los cambios. Esta acción no se puede deshacer.', {
          title: '¿Eliminar ' + name + '?'
        }).then(function (ok) {
          if (!ok) return;
          item.remove();
          renumber(acc);
        });
      }
      return;
    }
    const upBtn = e.target.closest('.nep-rep-up');
    if (upBtn) {
      const item = upBtn.closest('.nep-rep-item');
      const prev = item.previousElementSibling;
      if (prev) item.parentNode.insertBefore(item, prev);
      renumber(upBtn.closest('[data-rep-key]'));
      return;
    }
    const downBtn = e.target.closest('.nep-rep-down');
    if (downBtn) {
      const item = downBtn.closest('.nep-rep-item');
      const next = item.nextElementSibling;
      if (next) item.parentNode.insertBefore(next, item);
      renumber(downBtn.closest('[data-rep-key]'));
      return;
    }
  });

  /* ── Guardar ── */
  /* Solo se envían los campos que se tocaron. Enviarlos todos escribía en la
   * página el valor por defecto de cada campo, y a partir de ahí dejaba de
   * seguir al tema: en las copias de cada ruta congelaba la cabecera del país
   * y en cualquier página impedía que un texto por defecto mejorado llegara. */
  panel.querySelectorAll('[data-field-key]').forEach(el => { el.dataset.orig = el.value; });
  function setStatus(msg, cls) { status.textContent = msg; status.className = cls; }
  saveB.addEventListener('click', async () => {
    // Campos fijos (fuera de repeaters)
    const fields = {};
    panel.querySelectorAll('[data-field-key]').forEach(el => {
      if ((el.tagName === 'INPUT' || el.tagName === 'TEXTAREA' || el.tagName === 'SELECT') && el.value !== el.dataset.orig) fields[el.dataset.fieldKey] = el.value;
    });
    // Repeaters
    const repeaters = {};
    panel.querySelectorAll('[data-rep-key]').forEach(acc => {
      const items = [];
      acc.querySelectorAll('.nep-rep-list > .nep-rep-item').forEach(item => {
        const obj = {};
        item.querySelectorAll('[data-rep-sub]').forEach(el => { obj[el.dataset.repSub] = el.value; });
        items.push(obj);
      });
      repeaters[acc.dataset.repKey] = items;
    });
    // Ajustes globales del sitio (colores, redes, encabezado, pie)
    const globals = {};
    panel.querySelectorAll('[data-global-key]').forEach(el => { globals[el.dataset.globalKey] = el.value; });
    saveB.disabled = true;
    saveB.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Guardando...';
    setStatus('Guardando...', 'saving');
    try {
      // Datos del país de la ruta (se guardan por país, no en la página)
      const pais = {};
      panel.querySelectorAll('[data-pais-key]').forEach(el => { pais[el.dataset.paisKey] = el.value; });
      const payload = JSON.stringify({ post_id: cfg.postId, fields, repeaters, globals, pais });
      const doPost = (url) => fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': cfg.nonce },
        body: payload
      });
      let res = await doPost(cfg.restUrl);
      let ct = (res.headers.get('content-type') || '');
      // Si la REST "bonita" (/wp-json/) falla o devuelve HTML, reintenta con ?rest_route=
      if ((!res.ok || ct.indexOf('json') === -1) && cfg.restUrlAlt) {
        res = await doPost(cfg.restUrlAlt);
        ct = (res.headers.get('content-type') || '');
      }
      if (ct.indexOf('json') === -1) {
        throw new Error('El servidor no devolvió JSON (código ' + res.status + '). Revisa: Ajustes → Enlaces permanentes → Guardar cambios, y que la API REST no esté bloqueada.');
      }
      const data = await res.json();
      if (data.success) {
        setStatus('✓ ¡Guardado! Recargando...', 'success');
        setTimeout(() => window.location.reload(), 600);
        return;
      }
      throw new Error(data.message || 'Error desconocido');
    } catch (err) {
      setStatus('✗ ' + err.message, 'error');
      saveB.disabled = false;
      saveB.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Guardar';
    }
  });
  }
  if ( document.readyState === 'loading' ) document.addEventListener( 'DOMContentLoaded', init );
  else init();
})();
