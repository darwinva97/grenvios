"""Auditoría país por país: cada página de una ruta habla de SU país."""
import re, os, json, html, collections, sys
H = sys.argv[1]
R = {'ec':'Ecuador','co':'Colombia','cl':'Chile','bo':'Bolivia','ar':'Argentina','us':'Estados Unidos','ve':'Venezuela','cu':'Cuba'}
TODOS = list(R.values()) + ['España', 'Perú']
COPIA = re.compile(r'-[234](?:-a-|-para-|__|$)|sample-page')
# Secciones/elementos que listan todos los destinos a propósito.
LISTAS = re.compile(r'\b(gr-home-rutas|gr-home-dest|gr-home-elegir|gr-mas|dest-seo-comp|gr-dcomp|gr-og|gr-otros|grenvios-related|gr-rel|gr-bep|grenvios-guides|footer|gr-lang|grenvios-mega|project-section|gr-hd-|nav|breadcrumb|gr-bh|blog-card|gr-pais-otros|gr-cob-otros|grenvios-faq-section)\b')

def limpiar(c):
    # quita scripts/estilos y bloques que listan destinos
    c = re.sub(r'<(script|style|nav|footer|header)\b.*?</\1>', ' ', c, flags=re.S)
    out = []
    for m in re.finditer(r'<section\b[^>]*class="([^"]*)"[^>]*>.*?</section>', c, re.S):
        if LISTAS.search(m.group(1)): continue
        sec = m.group(0)
        sec = re.sub(r'<table\b.*?</table>', ' ', sec, flags=re.S)        # tablas comparativas
        sec = re.sub(r'<(ul|ol)\b[^>]*class="[^"]*(?:gr-otros|dest-ciudades|gr-rt|gr-hd)[^"]*".*?</\1>', ' ', sec, flags=re.S)
        out.append(sec)
    t = html.unescape(re.sub(r'<[^>]+>', ' ', ' '.join(out)))
    return re.sub(r'\s+', ' ', t)

res = {}; malos = collections.defaultdict(list)
for f in sorted(os.listdir(H)):
    n = f[:-5]; m = re.match(r'^(ec|co|cl|bo|ar|us|ve|cu)(?:__|$)', n)
    if not m or COPIA.search(n): continue
    r = m.group(1); mio = R[r]
    s = open(os.path.join(H, f), encoding='utf-8', errors='ignore').read()
    if 'Página no encontrada' in s[:3000] or 'Page not found' in s[:3000]: continue
    title = html.unescape((re.search(r'<title>([^<]*)', s) or [None, ''])[1])
    h1 = html.unescape(re.sub('<[^>]+>', '', (re.search(r'<h1[^>]*>(.*?)</h1>', s, re.S) or [None, ''])[1])).strip()
    mm = re.search(r'<main\b.*</main>', s, re.S); c = mm.group(0) if mm else s
    t = limpiar(c)
    otros = {}
    for o in TODOS:
        if o == mio or (o == 'Perú'): continue
        k = len(re.findall(r'\b(?:a|en|de|hacia|para)\s+' + re.escape(o) + r'\b', t))
        if k: otros[o] = k
    ejemplos = []
    for o in otros:
        for mt in re.finditer(r'.{0,70}\b(?:a|en|de|hacia|para)\s+' + re.escape(o) + r'\b.{0,50}', t):
            ejemplos.append(mt.group(0).strip()); break
    res[n] = {'ruta': r, 'title': title, 'h1': h1, 'mio_en_title': mio.split()[0] in title or ('EE' in title and r == 'us'),
              'mio_en_h1': mio.split()[0] in h1, 'mio_en_texto': t.count(mio), 'otros': otros, 'ej': ejemplos[:3]}
    v = res[n]
    if not v['mio_en_title']: malos['title sin su país'].append(n)
    if not v['mio_en_h1']: malos['H1 sin su país'].append(n)
    if v['mio_en_texto'] == 0: malos['texto sin su país'].append(n)
    if sum(otros.values()) >= 1: malos['menciona otro país'].append(n)
json.dump(res, open(sys.argv[2], 'w'), ensure_ascii=False, indent=1)
print('páginas y entradas de rutas analizadas:', len(res), dict(collections.Counter(v['ruta'] for v in res.values())))
for k, v in malos.items():
    print(f'\n== {k}: {len(v)}')
    for n in v[:12]:
        x = res[n]; print(f"   {n}  | h1: {x['h1'][:60]} | otros: {x['otros']}")
        for e in x['ej'][:2]: print('        «' + e[:150] + '»')
