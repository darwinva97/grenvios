"""Revisión de SEO en <head>, accesibilidad y rendimiento sobre el HTML servido.

    python a11y-perf.py http://greenvios.localhost/ /ec/ /blog/ …

Para cada URL (como visitante) informa:
  SEO   title (10–60), description (70–170), canonical, og:title/description/image
        (y que la imagen responda 200), twitter:card, un solo H1, lang y viewport.
  A11Y  imágenes sin alt, enlaces y botones sin nombre accesible, campos sin
        etiqueta, iframes sin title, ids duplicados y saltos de nivel en títulos.
  PERF  peso del HTML, tiempo de respuesta, hojas de estilo y scripts que
        bloquean el render, imágenes sin width/height (CLS) y la primera imagen
        de contenido con loading=lazy (retrasa el LCP).
Solo usa la librería estándar. No sustituye a Lighthouse: es lo que se puede
comprobar sin navegador y en segundos.
"""
import sys, re, time, html, urllib.request
from html.parser import HTMLParser

BASE = 'http://greenvios.localhost'


class P(HTMLParser):
    def __init__(self):
        super().__init__(convert_charrefs=True)
        self.stack = []; self.in_head = False; self.in_body = False
        self.title = ''; self._t = False
        self.meta = {}; self.links = []; self.h = []; self.ids = {}
        self.imgs = []; self.a = []; self.btn = []; self.inputs = []; self.labels = set(); self.iframes = []
        self.css_block = 0; self.js_block = 0; self._cur = None; self.html_lang = ''; self._h = None
        self.aria_hidden_depth = 0

    def handle_starttag(self, tag, attrs):
        a = dict(attrs)
        if tag == 'noscript': self.in_noscript = True
        if getattr(self, 'in_noscript', False): return   # copias de respaldo sin JS
        if tag == 'html': self.html_lang = a.get('lang', '')
        if tag == 'head': self.in_head = True
        if tag == 'body': self.in_head = False; self.in_body = True
        if 'id' in a and a['id']: self.ids[a['id']] = self.ids.get(a['id'], 0) + 1
        if tag == 'title' and self.in_head: self._t = True
        if tag == 'meta':
            k = a.get('name') or a.get('property')
            if k: self.meta[k.lower()] = a.get('content', '')
        if tag == 'link':
            rel = (a.get('rel') or '').lower()
            self.links.append((rel, a.get('href', ''), a.get('media', '')))
            if self.in_head and 'stylesheet' in rel and a.get('media', 'all') in ('all', '', 'screen'): self.css_block += 1
        if tag == 'script' and self.in_head and a.get('src') and 'defer' not in a and 'async' not in a and a.get('type', '') != 'module':
            self.js_block += 1
        if tag in ('h1', 'h2', 'h3', 'h4', 'h5', 'h6'):
            self._h = [int(tag[1]), '']
        if tag == 'img' and self.in_body:
            self.imgs.append(a)
        if tag == 'a' and self.in_body and a.get('aria-hidden') != 'true':
            self._cur = ['a', a, '', False]
        if tag == 'button':
            self._cur = ['button', a, '', False]
        if tag == 'label':
            self.in_label = True
            if a.get('for'): self.labels.add(a['for'])
        if tag in ('input', 'select', 'textarea') and a.get('type', '') not in ('hidden', 'submit', 'button') and not getattr(self, 'in_label', False):
            self.inputs.append(a)
        if tag == 'iframe': self.iframes.append(a)
        if self._cur and tag in ('img', 'svg', 'i') and self._cur[0] in ('a', 'button'):
            if tag == 'img' and a.get('alt'): self._cur[3] = True

    def handle_endtag(self, tag):
        if tag == 'noscript': self.in_noscript = False
        if tag == 'label': self.in_label = False
        if tag == 'title': self._t = False
        if tag in ('h1', 'h2', 'h3', 'h4', 'h5', 'h6') and self._h:
            self.h.append((self._h[0], self._h[1].strip())); self._h = None
        if self._cur and tag == self._cur[0]:
            kind, a, txt, img_alt = self._cur
            name = txt.strip() or a.get('aria-label', '').strip() or a.get('title', '').strip() or img_alt
            (self.a if kind == 'a' else self.btn).append((a, bool(name)))
            self._cur = None

    def handle_data(self, d):
        if self._t: self.title += d
        if self._h is not None: self._h[1] += d
        if self._cur: self._cur[2] += d


def fetch(u, head_only=False):
    req = urllib.request.Request(u, method='HEAD' if head_only else 'GET', headers={'User-Agent': 'Mozilla/5.0 auditoria-grenvios'})
    t = time.time()
    with urllib.request.urlopen(req, timeout=90) as r:
        body = b'' if head_only else r.read()
        return r.status, body, time.time() - t


def revisar(u):
    url = u if u.startswith('http') else BASE + '/' + u.lstrip('/')
    st, body, dt = fetch(url)
    h = body.decode('utf-8', 'ignore')
    p = P(); p.feed(h)
    out = []
    t = html.unescape(p.title.strip()); d = p.meta.get('description', '')
    if not (10 <= len(t) <= 60): out.append(f'SEO title {len(t)} car.: {t[:70]}')
    if not (70 <= len(d) <= 170): out.append(f'SEO description {len(d)} car.')
    if not any('canonical' in r for r, _, _ in p.links): out.append('SEO sin canonical')
    for k in ('og:title', 'og:description', 'og:image', 'og:url', 'twitter:card'):
        if not p.meta.get(k): out.append('SEO sin ' + k)
    og = p.meta.get('og:image', '')
    if og:
        try:
            s, _, _ = fetch(og, head_only=True)
            if s != 200: out.append(f'SEO og:image responde {s}')
        except Exception as e:
            out.append(f'SEO og:image no responde ({e})')
        if re.search(r'(slider-bg|page-banner|content-bg|post-\d|hero-background)', og): out.append('SEO og:image es un relleno de la plantilla: ' + og.split('/')[-1])
    h1 = [x for x in p.h if x[0] == 1]
    if len(h1) != 1: out.append(f'SEO {len(h1)} H1')
    if not p.html_lang: out.append('A11Y <html> sin lang')
    if 'viewport' not in p.meta: out.append('A11Y sin viewport')
    # A11Y
    sin_alt = [i for i in p.imgs if 'alt' not in i]
    if sin_alt: out.append(f'A11Y {len(sin_alt)} img sin alt: ' + ', '.join(i.get('src', '?').split('/')[-1][:30] for i in sin_alt[:3]))
    sin_nombre = [a for a, ok in p.a if not ok and a.get('href')]
    if sin_nombre: out.append(f'A11Y {len(sin_nombre)} enlaces sin nombre: ' + ', '.join((a.get('class') or a.get('href', ''))[:40] for a in sin_nombre[:3]))
    bsin = [a for a, ok in p.btn if not ok]
    if bsin: out.append(f'A11Y {len(bsin)} botones sin nombre: ' + ', '.join((a.get('class') or '?')[:40] for a in bsin[:3]))
    campos = [i for i in p.inputs if not (i.get('id') in p.labels or i.get('aria-label') or i.get('aria-labelledby') or i.get('title'))]
    if campos: out.append(f'A11Y {len(campos)} campos sin etiqueta: ' + ', '.join((i.get('name') or i.get('id') or '?') for i in campos[:4]))
    ifr = [i for i in p.iframes if not i.get('title')]
    if ifr: out.append(f'A11Y {len(ifr)} iframes sin title')
    dup = [k for k, v in p.ids.items() if v > 1]
    if dup: out.append(f'A11Y ids duplicados: ' + ', '.join(dup[:5]))
    prev = 1; saltos = []
    for lv, txt in p.h:
        if lv > prev + 1: saltos.append(f'h{prev}→h{lv} «{txt[:30]}»')
        prev = lv
    if saltos: out.append(f'A11Y {len(saltos)} saltos de título: ' + '; '.join(saltos[:3]))
    # PERF
    kb = len(body) // 1024
    if kb > 400: out.append(f'PERF HTML {kb} KB')
    if dt > 1.5: out.append(f'PERF respuesta {dt:.2f} s')
    if p.css_block > 6: out.append(f'PERF {p.css_block} hojas de estilo bloqueantes')
    if p.js_block: out.append(f'PERF {p.js_block} scripts en <head> sin defer/async')
    sin_dim = [i for i in p.imgs if not (i.get('width') and i.get('height')) and not i.get('src', '').startswith('data:')]
    if sin_dim: out.append(f'PERF {len(sin_dim)} img sin width/height')
    cont = [i for i in p.imgs if 'logo' not in (i.get('src', '') + i.get('class', '')).lower()]
    if cont and cont[0].get('loading') == 'lazy': out.append('PERF la primera imagen de contenido es lazy (LCP): ' + cont[0].get('src', '').split('/')[-1])
    return st, kb, dt, out


if __name__ == '__main__':
    urls = sys.argv[1:]
    tot = {}
    for u in urls:
        try:
            st, kb, dt, out = revisar(u)
        except Exception as e:
            print(f'ERR {u}: {e}'); continue
        print(f'\n{u}  [{st}] {kb} KB {dt:.2f}s')
        for o in out:
            print('   - ' + o); k = o.split(' ')[0] + ' ' + re.sub(r'[\d:].*', '', ' '.join(o.split(' ')[1:3])); tot[k] = tot.get(k, 0) + 1
        if not out: print('   ✓')
    print('\nResumen:', tot)
