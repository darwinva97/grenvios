/*
 * Cotizador del hero: opción preseleccionada y desplegables propios.
 *
 * El <select> nativo sigue siendo el que manda —es el que se envía y el que
 * valida el navegador—; encima se dibuja una lista con el estilo de la tarjeta.
 * En pantallas táctiles no se sustituye: el selector del sistema operativo es
 * más cómodo que cualquier lista hecha a mano.
 */
(function () {
	'use strict';

	var form = document.getElementById('gr-hero-quote');
	if (!form) return;

	/* ── 1) Opción preseleccionada (ruta de país, sede) ──────────────────── */
	Array.prototype.forEach.call(form.querySelectorAll('select[data-preset]'), function (sel) {
		var valor = (sel.getAttribute('data-preset') || '').trim();
		if (!valor) return;

		var existe = Array.prototype.some.call(sel.options, function (o) {
			return o.value.toLowerCase() === valor.toLowerCase();
		});
		// Si el país de la ruta no está en la lista editable, se añade: la
		// portada de esa ruta no puede ofrecer un destino que no sea el suyo.
		if (!existe) {
			var op = document.createElement('option');
			op.value = valor;
			op.textContent = valor;
			sel.appendChild(op);
		}
		sel.value = valor;
	});

	/* ── 2) Desplegable propio ───────────────────────────────────────────── */
	var tactil = window.matchMedia && window.matchMedia('(hover: none)').matches;
	if (tactil) return;

	var abierto = null;

	var cerrar = function () {
		if (!abierto) return;
		abierto.classList.remove('is-open');
		abierto.querySelector('.gr-dd-list').hidden = true;
		abierto.querySelector('.gr-dd-toggle').setAttribute('aria-expanded', 'false');
		abierto = null;
	};

	Array.prototype.forEach.call(form.querySelectorAll('.gr-hq-input select'), function (sel) {
		var caja = document.createElement('div');
		caja.className = 'gr-dd';

		var boton = document.createElement('button');
		boton.type = 'button';
		boton.className = 'gr-dd-toggle';
		boton.setAttribute('aria-haspopup', 'listbox');
		boton.setAttribute('aria-expanded', 'false');
		boton.setAttribute('aria-label', sel.getAttribute('aria-label') || '');

		var lista = document.createElement('ul');
		lista.className = 'gr-dd-list';
		lista.setAttribute('role', 'listbox');
		lista.hidden = true;

		var pintar = function () {
			var op = sel.options[sel.selectedIndex];
			boton.textContent = op ? op.textContent : '';
			// El texto inicial («Hasta», «Tipo de envío») se ve como placeholder.
			boton.classList.toggle('is-placeholder', !sel.value);
			Array.prototype.forEach.call(lista.children, function (li) {
				var activo = li.getAttribute('data-value') === sel.value;
				li.classList.toggle('is-selected', activo);
				li.setAttribute('aria-selected', activo ? 'true' : 'false');
			});
		};

		Array.prototype.forEach.call(sel.options, function (op) {
			if (op.value === '') return;   // el texto inicial no es una opción elegible
			var li = document.createElement('li');
			li.setAttribute('role', 'option');
			li.setAttribute('data-value', op.value);
			li.textContent = op.textContent;
			li.addEventListener('click', function () {
				sel.value = op.value;
				// 'change' para que lo vean la validación del navegador y cualquier script.
				sel.dispatchEvent(new Event('change', { bubbles: true }));
				pintar();
				cerrar();
				boton.focus();
			});
			lista.appendChild(li);
		});

		boton.addEventListener('click', function (e) {
			e.stopPropagation();
			var estaba = abierto === caja;
			cerrar();
			if (estaba) return;
			caja.classList.add('is-open');
			lista.hidden = false;
			boton.setAttribute('aria-expanded', 'true');
			abierto = caja;
		});

		boton.addEventListener('keydown', function (e) {
			if (e.key === 'Escape') { cerrar(); return; }
			if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
				e.preventDefault();
				var ops = Array.prototype.filter.call(sel.options, function (o) { return o.value !== ''; });
				var i = ops.indexOf(sel.options[sel.selectedIndex]);
				i = e.key === 'ArrowDown' ? Math.min(i + 1, ops.length - 1) : Math.max(i - 1, 0);
				if (ops[i]) {
					sel.value = ops[i].value;
					sel.dispatchEvent(new Event('change', { bubbles: true }));
					pintar();
				}
			}
		});

		sel.parentNode.insertBefore(caja, sel);
		caja.appendChild(boton);
		caja.appendChild(lista);
		caja.appendChild(sel);      // el <select> real queda dentro, oculto visualmente
		sel.classList.add('gr-dd-native');
		sel.addEventListener('change', pintar);
		pintar();
	});

	document.addEventListener('click', cerrar);
	// Tras enviar, el formulario se resetea: hay que repintar los botones.
	form.addEventListener('reset', function () {
		setTimeout(function () {
			Array.prototype.forEach.call(form.querySelectorAll('.gr-hq-input select'), function (sel) {
				sel.dispatchEvent(new Event('change', { bubbles: true }));
			});
		}, 0);
	});
})();
