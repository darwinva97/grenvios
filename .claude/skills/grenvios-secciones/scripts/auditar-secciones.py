#!/usr/bin/env python3
"""Audita las secciones de un sitio Grenvíos: cuáles son «solo texto» (sin foto,
icono grande, número, bandera, tabla ni tarjetas), H2 repetidos en una página y
páginas basura (copias -2/-3/-4, «Sample Page»).

Uso:
  python3 auditar-secciones.py https://grenvios.com /nosotros/ /ar/aduana-de-argentina/
  python3 auditar-secciones.py https://grenvios.com --sitemap [--ruta ar] [--max 80]
  python3 auditar-secciones.py --dir carpeta_con_html          # HTML ya descargado
Solo biblioteca estándar. Pide 4 páginas a la vez: no lo lances contra producción
en bucle.
"""
import re, sys, os, json, argparse, collections, urllib.request
from concurrent.futures import ThreadPoolExecutor
from html.parser import HTMLParser

VOID = {'area','base','br','col','embed','hr','img','input','link','meta','source','track','wbr'}
# Secciones que no son de contenido o que tienen su propio diseño.
FUERA = re.compile(r'\b(header|footer|page-header|gr-lf|grenvios-related|gr-bep|gr-faq|faq|breadcrumb|gr-toc|cta-section|grenvios-cta|gr-ent|running-text|grenvios-promo-section|blog-section|gr-cobertura--nota)\b')
# Mismo criterio que grenvios_sv_tiene_visual() en inc/secciones-visuales.php.
TARJETAS = re.compile(r'\b(rast-state-card|srv-panel|srv-two-grid|srv-trio-grid|gr-bq-card|dest-card|gr-dsol-card|gr-he-card|gr-n2-card|gr-n3-card)\b')
COPIA = re.compile(r'-[234](?:-a-|-para-|/|$)|sample-page')

class Arbol(HTMLParser):
    def __init__(s):
        super().__init__(convert_charrefs=True); s.root = {'t': 'root', 'a': {}, 'c': [], 'x': []}; s.pila = [s.root]
    def handle_starttag(s, t, a):
        n = {'t': t, 'a': dict(a), 'c': [], 'x': []}; s.pila[-1]['c'].append(n)
        if t not in VOID: s.pila.append(n)
    def handle_endtag(s, t):
        for i in range(len(s.pila) - 1, 0, -1):
            if s.pila[i]['t'] == t: del s.pila[i:]; return
    def handle_data(s, d):
        if d.strip(): s.pila[-1]['x'].append(d.strip())

def nodos(n):
    yield n
    for c in n['c']: yield from nodos(c)
def cls(n): return n['a'].get('class') or ''
def texto(n): return ' '.join(x for m in nodos(n) for x in m['x'])

def visuales(sec):
    v = set()
    for m in nodos(sec):
        t, c = m['t'], cls(m)
        if t in ('img', 'svg', 'table', 'figure', 'ol', 'picture', 'iframe', 'video'): v.add(t)
        elif t == 'i' and re.search(r'\bfa-|logis-', c) and not re.search(r'fa-(check|xmark|arrow|angle|chevron)', c): v.add('icono')
        elif 'background-image' in (m['a'].get('style') or ''): v.add('fondo')
        elif TARJETAS.search(c): v.add('tarjetas')
    return sorted(v)

def analizar(html):
    m = re.search(r'<main\b.*?</main>', html, re.S)
    p = Arbol()
    try: p.feed(m.group(0) if m else html)
    except Exception: pass
    out = []
    def rec(n):
        for c in n['c']:
            if c['t'] == 'section':
                if not FUERA.search(cls(c)):
                    h2 = next((texto(x) for x in nodos(c) if x['t'] in ('h1', 'h2')), '')
                    out.append({'clase': cls(c).strip(), 'h2': h2[:80], 'visual': visuales(c), 'palabras': len(texto(c).split())})
            else: rec(c)
    rec(p.root)
    t = re.search(r'<title>([^<]*)', html)
    return {'title': t.group(1) if t else '', 'secciones': out}

def bajar(url):
    try:
        with urllib.request.urlopen(urllib.request.Request(url, headers={'User-Agent': 'grenvios-auditoria'}), timeout=40) as r:
            return url, r.status, r.read().decode('utf-8', 'ignore')
    except Exception as e:
        return url, getattr(e, 'code', 0), ''

def urls_sitemap(base, ruta):
    _, _, idx = bajar(base.rstrip('/') + '/sitemap.xml')
    hojas = [u for u in re.findall(r'<loc>([^<]+)</loc>', idx) if 'paginas' in u and (not ruta or f'-{ruta}-' in u)]
    res = []
    for h in hojas: res += re.findall(r'<loc>([^<]+)</loc>', bajar(h)[2])
    return res

def main():
    ap = argparse.ArgumentParser()
    ap.add_argument('base', nargs='?'); ap.add_argument('rutas', nargs='*')
    ap.add_argument('--sitemap', action='store_true'); ap.add_argument('--ruta', default='')
    ap.add_argument('--max', type=int, default=0); ap.add_argument('--dir'); ap.add_argument('--json')
    a = ap.parse_args()
    paginas = {}
    if a.dir:
        for f in sorted(os.listdir(a.dir)):
            if f.endswith('.html'): paginas[f[:-5]] = (200, open(os.path.join(a.dir, f), encoding='utf-8', errors='ignore').read())
    else:
        urls = urls_sitemap(a.base, a.ruta) if a.sitemap else [a.base.rstrip('/') + r for r in a.rutas]
        if a.max: urls = urls[:a.max]
        with ThreadPoolExecutor(4) as ex:
            for u, st, h in ex.map(bajar, urls): paginas[u.replace(a.base.rstrip('/'), '') or '/'] = (st, h)

    informe, tipos, basura, errores = {}, collections.Counter(), [], []
    for k, (st, h) in paginas.items():
        if st != 200: errores.append((k, st)); continue
        if COPIA.search(k): basura.append(k); continue
        r = analizar(h)
        # Las preguntas frecuentes son acordeón por norma: no cuentan.
        txt = [s for s in r['secciones'] if not s['visual'] and s['palabras'] > 25 and not s['h2'].lower().startswith('preguntas frecuentes')]
        cnt = collections.Counter(s['h2'] for s in r['secciones'] if s['h2'])
        r['solo_texto'] = txt; r['h2_repetidos'] = [x for x, n in cnt.items() if n > 1]
        informe[k] = r
        for s in txt:
            tipos[' '.join(x for x in s['clase'].split() if not re.match(r'padding|bg-grey|gr-pseo--', x))] += 1

    malas = {k: v for k, v in informe.items() if v['solo_texto'] or v['h2_repetidos']}
    print(f"Páginas analizadas: {len(informe)} · con secciones de solo texto o H2 repetidos: {len(malas)}")
    print(f"Secciones de solo texto: {sum(len(v['solo_texto']) for v in informe.values())}")
    if tipos:
        print('\nPor tipo de sección:')
        for t, n in tipos.most_common(): print(f'  {n:4d}  {t}')
    if malas:
        print('\nPáginas:')
        for k, v in sorted(malas.items(), key=lambda x: -len(x[1]['solo_texto'])):
            print(f"  {k}  ({len(v['solo_texto'])}/{len(v['secciones'])} solo texto)")
            for s in v['solo_texto']: print(f"      · {s['h2'][:70]}  [{s['clase'][:50]}]")
            for h in v['h2_repetidos']: print(f"      ⚠ H2 repetido: {h[:70]}")
    if basura: print(f"\nCopias -2/-3/-4 o «Sample Page» (borrar o redirigir): {len(basura)}\n  " + '\n  '.join(sorted(basura)[:30]) + ('\n  …' if len(basura) > 30 else ''))
    if errores: print('\nSin HTTP 200: ' + ', '.join(f'{k} ({s})' for k, s in errores[:30]))
    if a.json: json.dump(informe, open(a.json, 'w'), ensure_ascii=False, indent=1)

if __name__ == '__main__':
    main()
