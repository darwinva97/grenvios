// Capturas y comprobaciones de diseño con un navegador real (Edge/Chrome headless por CDP).
//
//   node capturas.mjs <carpeta-salida> <url> [url…]
//
// Por cada URL, en escritorio (1366) y móvil (390): captura de página completa y un
// informe de lo que suele romperse — scroll horizontal, elementos que se salen del
// ancho, tablas sin .gr-table-wrap, imágenes rotas y titulares desbordados.
// Requiere Node 22 (WebSocket nativo).
import { spawn } from 'node:child_process';
import { mkdirSync, writeFileSync, existsSync, readFileSync, rmSync } from 'node:fs';
import { join } from 'node:path';

const [, , out = 'capturas', ...urls] = process.argv;
if (!urls.length) { console.error('Uso: node capturas.mjs <salida> <url>…'); process.exit(1); }
mkdirSync(out, { recursive: true });

const candidatos = [
  'C:/Program Files/Google/Chrome/Application/chrome.exe',
  'C:/Program Files (x86)/Microsoft/Edge/Application/msedge.exe',
  'C:/Program Files/Microsoft/Edge/Application/msedge.exe',
  '/usr/bin/chromium', '/usr/bin/google-chrome', '/usr/bin/microsoft-edge',
];
const exe = candidatos.find(existsSync);
if (!exe) { console.error('No encuentro Chrome ni Edge'); process.exit(1); }

/* Puerto 0: el navegador elige uno libre y lo escribe en DevToolsActivePort.
 * Edge 154 dejó de abrir un puerto fijo (9333) y el script se quedaba sin conexión. */
const perfil = join(out, '.perfil');
try { rmSync(join(perfil, 'DevToolsActivePort'), { force: true }); } catch {}
const nav = spawn(exe, ['--headless=new', '--remote-debugging-port=0', '--remote-allow-origins=*', '--no-first-run',
  '--hide-scrollbars', `--user-data-dir=${perfil}`, ...(process.platform === 'linux' ? ['--no-sandbox'] : []), 'about:blank'], { stdio: 'ignore' });
let port = 0;
const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

let ws;
for (let i = 0; i < 160 && !ws; i++) {   // hasta 40 s: con poca RAM el navegador tarda en arrancar
  try {
    if (!port) { port = parseInt(readFileSync(join(perfil, 'DevToolsActivePort'), 'utf8'), 10) || 0; if (!port) throw 0; }
    const t = await (await fetch(`http://127.0.0.1:${port}/json`)).json();
    const p = t.find((x) => x.type === 'page');
    if (p) ws = new WebSocket(p.webSocketDebuggerUrl);
  } catch { await sleep(250); }
}
await new Promise((r) => ws.addEventListener('open', r));
let id = 0; const pend = new Map();
ws.addEventListener('message', (e) => { const m = JSON.parse(e.data); if (m.id && pend.has(m.id)) { pend.get(m.id)(m); pend.delete(m.id); } });
// Cada llamada con límite de 60 s: una página que cuelga el render no bloquea las demás.
const cdp = (method, params = {}) => new Promise((r) => {
  const i = ++id; pend.set(i, r); ws.send(JSON.stringify({ id: i, method, params }));
  setTimeout(() => { if (pend.has(i)) { pend.delete(i); r({ error: { message: 'timeout ' + method } }); } }, 60000);
});
await cdp('Page.enable'); await cdp('Runtime.enable');

const revisar = `(() => {
  const W = document.documentElement.clientWidth, r = [];
  if (document.documentElement.scrollWidth > W + 1) r.push('scroll horizontal: ' + document.documentElement.scrollWidth + 'px > ' + W + 'px');
  const main = document.querySelectorAll('main *, section *');
  let n = 0;
  for (const el of main) {
    if (el.closest('#nep-panel,#gr-fe-panel,.swiper,.owl-carousel,.marquee,[class*="slider"],[class*="marquee"]')) continue;
    const b = el.getBoundingClientRect();
    if (!b.width || (b.right <= W + 2 && b.left >= -2) || getComputedStyle(el).position === 'fixed') continue;
    // Dentro de un contenedor que se desplaza en horizontal es correcto (tablas);
    // dentro de uno con overflow:hidden queda RECORTADO, que es peor que desbordar.
    let p = el.parentElement, modo = '';
    for (; p && p !== document.body; p = p.parentElement) {
      const ox = getComputedStyle(p).overflowX;
      if (ox === 'auto' || ox === 'scroll') { modo = 'scroll'; break; }
      if (ox === 'hidden' || ox === 'clip') { if (p.getBoundingClientRect().right <= W + 2) { modo = 'recortado'; break; } }
    }
    if (modo === 'scroll') continue;
    if (n++ < 5) r.push((modo === 'recortado' ? 'RECORTADO (overflow:hidden): <' : 'se sale del ancho: <') + el.tagName.toLowerCase() + ' class="' + el.className + '"> ' + Math.round(b.left) + '→' + Math.round(b.right));
  }
  document.querySelectorAll('table').forEach(t => {
    if (t.getBoundingClientRect().width <= W) return;
    let p = t.parentElement, ok = false;
    for (; p && p !== document.body; p = p.parentElement) { const ox = getComputedStyle(p).overflowX; if (ox === 'auto' || ox === 'scroll') { ok = true; break; } }
    if (!ok) r.push('tabla sin contenedor con scroll que desborda');
  });
  document.querySelectorAll('img').forEach(i => { if (i.complete && i.naturalWidth === 0 && i.src) r.push('imagen rota: ' + i.src.split('/').pop()); });
  document.querySelectorAll('h1,h2,h3').forEach(h => { if (h.scrollWidth > h.clientWidth + 2) r.push('titular desbordado: ' + h.textContent.trim().slice(0, 50)); });
  return r;
})()`;

const informe = {};
for (const url of urls) {
  for (const [nombre, w, h, mobile] of [['escritorio', 1366, 900, false], ['movil', 390, 844, true]]) {
    await cdp('Emulation.setDeviceMetricsOverride', { width: w, height: h, deviceScaleFactor: 1, mobile });
    await cdp('Page.navigate', { url });
    await sleep(3500);
    // Bajar poco a poco dispara las animaciones de entrada (wow / lazy load).
    // Scroll por pasos lentos con la API de Lenis (el scroll suave del sitio): con
    // scrollTo() rápido Lenis devolvía la página arriba y ni ScrollTrigger (títulos
    // `text-anim`) ni WOW ni las imágenes lazy llegaban a activarse. Sin eventos de
    // entrada simulados, que en móvil se quedaban colgados.
    await cdp('Runtime.evaluate', { awaitPromise: true, expression: `(async()=>{
      const H=document.documentElement.scrollHeight, ir=y=>{ if(window.lenis&&window.lenis.scrollTo) window.lenis.scrollTo(y,{immediate:true}); else window.scrollTo(0,y); };
      for(let y=0;y<H;y+=300){ ir(y); await new Promise(r=>setTimeout(r,160)); }
      await new Promise(r=>setTimeout(r,1200)); ir(0);
    })()` });
    await sleep(800);
    // Opcional: CSS extra para aislar un problema de render (--css="…").
    const extra = process.env.CAPTURAS_CSS;
    if (extra) await cdp('Runtime.evaluate', { expression: `document.head.insertAdjacentHTML('beforeend', ${JSON.stringify('<style>' + extra + '</style>')})` });
    const res = await cdp('Runtime.evaluate', { expression: revisar, returnByValue: true });
    const slug = url.replace(/^https?:\/\/[^/]+/, '').replace(/\/+/g, '_').replace(/^_|_$/g, '') || 'home';
    // Por tramos de 8000 px: el navegador no genera imágenes de más de ~16 000 px
    // y las fichas de destino en móvil pasan de 20 000.
    const alto = (await cdp('Runtime.evaluate', { expression: 'document.documentElement.scrollHeight', returnByValue: true })).result.result.value;
    const capturas = [];
    for (let y = 0, i = 1; y < alto; y += 8000, i++) {
      const shot = await cdp('Page.captureScreenshot', { format: 'jpeg', quality: 70, captureBeyondViewport: true,
        clip: { x: 0, y, width: w, height: Math.min(8000, alto - y), scale: 1 } });
      if (!shot.result) { capturas.push('ERROR: ' + JSON.stringify(shot.error)); continue; }
      const f = join(out, `${slug}__${nombre}${alto > 8000 ? '_' + i : ''}.jpg`);
      writeFileSync(f, Buffer.from(shot.result.data, 'base64'));
      capturas.push(f);
    }
    informe[`${slug} (${nombre})`] = { capturas, problemas: (res.result && res.result.result ? res.result.result.value : ['ERROR: ' + JSON.stringify(res.error)]).concat(capturas.filter((c) => String(c).startsWith('ERROR'))) };
  }
}
writeFileSync(join(out, 'informe.json'), JSON.stringify(informe, null, 2));
for (const [k, v] of Object.entries(informe)) console.log(k, v.problemas.length ? '\n   - ' + v.problemas.join('\n   - ') : '✓');
ws.close(); nav.kill();
