/* Grenvíos — Seguimiento de envíos (consulta pública por número de guía) */
(function () {
  'use strict';
  var cfg = window.grenviosTrack;
  if (!cfg) return;
  var states = cfg.states || {};
  var labels = Object.keys(states);

  function esc(s) {
    return String(s == null ? '' : s).replace(/[&<>"]/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c];
    });
  }

  function render(container, d) {
    if (!d || !d.found) {
      container.innerHTML =
        '<div class="gv-track-card gv-notfound">' +
          '<div class="gv-hero-ico"><i class="fa-solid fa-magnifying-glass"></i></div>' +
          '<div class="gv-meta">' +
            '<div class="gv-cur">No encontramos esa guía</div>' +
            '<div class="gv-sub">Revisa el número o ' +
              '<a href="' + cfg.wa + '" target="_blank" rel="noopener">escríbenos por WhatsApp</a>.</div>' +
          '</div>' +
        '</div>';
      return;
    }
    var total = labels.length || d.total || 7;
    var idx   = d.indice;
    var pct   = total > 1 ? Math.round(idx / (total - 1) * 100) : 0;

    // Fase legible + variante de color
    var phase, variant;
    var lname = (labels[idx] || '').toLowerCase();
    if (idx >= total - 1)              { phase = '¡Entregado!';        variant = 'done'; }
    else if (idx === 0)               { phase = 'Pedido registrado';   variant = 'start'; }
    else if (lname.indexOf('reten') > -1) { phase = 'Requiere atención'; variant = 'warn'; }
    else                              { phase = 'En camino';           variant = 'progress'; }

    // Bloque de datos del envío (solo campos con valor)
    function svcIcon(v) {
      var s = (v || '').toLowerCase();
      if (s.indexOf('a') === 0 || s.indexOf('aér') > -1 || s.indexOf('aer') > -1) return 'fa-solid fa-plane-up';
      if (s.indexOf('terr') > -1) return 'fa-solid fa-truck';
      return 'fa-solid fa-truck-fast';
    }
    function tipoIcon(v) {
      return (v || '').toLowerCase().indexOf('doc') > -1 ? 'fa-solid fa-file-lines' : 'fa-solid fa-box';
    }
    function item(ico, label, value) {
      if (!value) return '';
      return '<div class="gv-di">' +
               '<span class="gv-di-ico"><i class="' + ico + '"></i></span>' +
               '<span class="gv-di-txt"><small>' + esc(label) + '</small><b>' + esc(value) + '</b></span>' +
             '</div>';
    }
    var chips = item('fa-solid fa-user', 'Cliente', d.cliente) +
                item(svcIcon(d.servicio), 'Servicio', d.servicio) +
                item(tipoIcon(d.tipo), 'Tipo', d.tipo);
    var details = '';
    if (chips) details += '<div class="gv-details">' + chips + '</div>';
    if (d.detalle) {
      details += '<div class="gv-note">' +
                   '<span class="gv-note-ico"><i class="fa-solid fa-circle-info"></i></span>' +
                   '<span class="gv-note-txt"><small>Descripción del envío</small>' + esc(d.detalle) + '</span>' +
                 '</div>';
    }

    var curIcon = states[labels[idx]] || 'fa-solid fa-box';
    var steps = labels.map(function (lbl, i) {
      var st  = i < idx ? 'done' : (i === idx ? 'current' : 'pending');
      var ico = st === 'done' ? 'fa-solid fa-check' : (states[lbl] || 'fa-solid fa-circle');
      return '<li class="gv-step ' + st + '">' +
               '<span class="gv-node"><i class="' + ico + '"></i></span>' +
               '<span class="gv-name">' + esc(lbl) + '</span>' +
             '</li>';
    }).join('');

    container.innerHTML =
      '<div class="gv-track-card gv-' + variant + '">' +
        '<div class="gv-top">' +
          '<div class="gv-hero-ico"><i class="' + curIcon + '"></i></div>' +
          '<div class="gv-meta">' +
            '<span class="gv-badge">' + esc(phase) + '</span>' +
            '<div class="gv-cur">' + esc(d.estado) + '</div>' +
            '<div class="gv-sub">Guía <strong>' + esc(d.guia) + '</strong>' +
              (d.fecha ? ' &middot; <i class="fa-regular fa-clock"></i> ' + esc(d.fecha) : '') + '</div>' +
          '</div>' +
          '<div class="gv-pct"><span>' + pct + '%</span><small>completado</small></div>' +
        '</div>' +
        details +
        '<ol class="gv-stepper">' + steps + '</ol>' +
      '</div>';

    // Animación: llena el conector y aparecen los pasos en cascada.
    setTimeout(function () {
      var sp = container.querySelector('.gv-stepper');
      if (sp) sp.style.setProperty('--gv-fill', pct + '%');
      var els = container.querySelectorAll('.gv-step');
      els.forEach(function (el, i) { setTimeout(function () { el.classList.add('in'); }, 70 * i); });
    }, 60);
  }

  function ensureResult(form) {
    var c = form.parentNode.querySelector('.grenvios-track-result');
    if (!c) {
      c = document.createElement('div');
      c.className = 'grenvios-track-result';
      form.parentNode.insertBefore(c, form.nextSibling);
    }
    return c;
  }

  function query(guia, container) {
    container.innerHTML = '<div class="gv-track-card gv-track-loading"><i class="fa-solid fa-spinner fa-spin"></i> Consultando tu envío…</div>';
    fetch(cfg.rest + '?guia=' + encodeURIComponent(guia))
      .then(function (r) { return r.json(); })
      .then(function (d) { render(container, d); })
      .catch(function () { render(container, { found: false }); });
  }

  // Cualquier .track-form: en la página de rastreo muestra el resultado inline;
  // en otros lugares (widget del Inicio) redirige a la página de rastreo con la guía.
  document.addEventListener('submit', function (e) {
    var f = e.target;
    if (!f || !f.classList || !f.classList.contains('track-form')) return;
    e.preventDefault();
    var input = f.querySelector('[name="guia"]');
    var guia = input ? input.value.trim() : '';
    if (!guia) return;
    if (document.querySelector('.grenvios-tracking')) {
      query(guia, ensureResult(f));
    } else {
      window.location.href = cfg.rastreo + '?guia=' + encodeURIComponent(guia);
    }
  });

  // Si llega ?guia= a la página de rastreo, consulta automáticamente.
  function autorun() {
    var form = document.querySelector('.grenvios-tracking .track-form');
    if (!form) return;
    var m = location.search.match(/[?&]guia=([^&]+)/);
    if (!m) return;
    var g = decodeURIComponent(m[1].replace(/\+/g, ' '));
    var inp = form.querySelector('[name="guia"]');
    if (inp) inp.value = g;
    query(g, ensureResult(form));
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', autorun);
  else autorun();
})();
