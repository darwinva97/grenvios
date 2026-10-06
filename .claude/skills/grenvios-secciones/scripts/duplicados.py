"""Formularios y FAQ duplicados por página (uso: python3 duplicados.py <carpeta_html> <salida.json>). «despues» simula los cambios de 2026-10-06 sobre HTML anterior a publicarlos; con el tema ya publicado, antes = después."""
import re, os, sys, json, html, collections
D = sys.argv[1]
COPIA = re.compile(r'-[234](?:-a-|-para-|__|$)|sample-page')

def ruta(n):
    m = re.match(r'^(ec|co|cl|bo|ar|us|es|ve|cu)(__|$)', n); return m.group(1) if m else 'pe'

def forms(s):
    out = []
    for m in re.finditer(r'<form\b([^>]*)>(.*?)</form>', s, re.S):
        at, body = m.group(1), m.group(2)
        cls = (re.search(r'class="([^"]*)"', at) or [None, ''])[1]
        act = (re.search(r'action="([^"]*)"', at) or [None, ''])[1]
        ident = (re.search(r'id="([^"]*)"', at) or [None, ''])[1]
        if re.search(r'search', cls + act + ident, re.I) or re.search(r'name="s"', body): continue
        if ident == 'commentform' or 'wp-comments-post' in act: continue
        if re.search(r'mailchimp|newsletter|subscribe', cls + act + ident, re.I): continue
        if re.search(r'rastreo|tracking|guia', cls + ident, re.I) and not re.search(r'cotiz|quote', cls + ident, re.I): continue
        out.append(cls.split()[0] if cls else (ident or act.rsplit('/', 1)[-1] or 'form'))
    return out

def faqs(s):
    b = []
    b += ['acordeon'] * len(re.findall(r'class="grenvios-faq-section', s))
    b += ['dest-seo-faq'] * len(re.findall(r'class="dest-seo-faq"', s))
    b += ['gr-post-faq'] * len(re.findall(r'class="(?:[^"]* )?gr-post-faq(?: [^"]*)?"', s))
    # bloque de país con preguntas (varios gr-faq-item dentro de una misma sección)
    for sec in re.findall(r'<section\b[^>]*>.*?</section>', s, re.S):
        if 'grenvios-faq-section' in sec[:200]: continue
        if len(re.findall(r'class="(?:[^"]* )?gr-faq-item(?: [^"]*)?"', sec)) >= 2: b.append('pais gr-faq-item')
    return b

def preguntas(s):
    q = []
    for x in re.findall(r'<button class="accordion-button[^"]*"[^>]*>(.*?)</button>', s, re.S): q.append(x)
    for x in re.findall(r'class="dest-seo-faq-item"><h3>(.*?)</h3>', s, re.S): q.append(x)
    for x in re.findall(r'class="[^"]*\bgr-faq-item\b[^"]*"[^>]*>\s*<(?:summary|h3|button|dt)[^>]*>(.*?)</', s, re.S): q.append(x)
    q = [html.unescape(re.sub(r'<[^>]+>', '', x)).strip().lower() for x in q]
    return [k for k, n in collections.Counter(q).items() if n > 1 and k]

def schema(s): return len(re.findall(r'"@type"\s*:\s*"FAQPage"', s))

def tras_cambios(s):
    """Lo que hace el código de la rama formularios-y-faq-duplicados."""
    s = re.sub(r'<section class="quick-quote-section[^"]*" id="cotiza-rapido">.*?</section>', '', s, flags=re.S)
    if 'dest-cotiza' in s:
        s = re.sub(r'<section class="gr-lf padding" id="solicitud".*?</section>', '', s, flags=re.S)
    s = re.sub(r'<section[^>]*>(?:(?!</section>).)*?class="dest-seo-faq".*?</section>', '', s, flags=re.S)
    return s

res = {}
for f in sorted(os.listdir(D)):
    n = f[:-5]
    if COPIA.search(n): continue
    s = open(os.path.join(D, f), encoding='utf-8', errors='ignore').read()
    if '<title>Página no encontrada' in s or '<title>Page not found' in s: continue
    m = re.search(r'<main\b.*</main>', s, re.S); cuerpo = m.group(0) if m else s
    a = tras_cambios(cuerpo)
    res[n] = {'ruta': ruta(n), 'antes': {'forms': forms(cuerpo), 'faqs': faqs(cuerpo)},
              'despues': {'forms': forms(a), 'faqs': faqs(a), 'repetidas': preguntas(a)}, 'schema': schema(s)}
json.dump(res, open(sys.argv[2], 'w'), ensure_ascii=False)

def resumen(cuando):
    f2 = [k for k, v in res.items() if len(v[cuando]['forms']) > 1]
    q2 = [k for k, v in res.items() if len(v[cuando]['faqs']) > 1]
    return f2, q2
print(f'Páginas analizadas: {len(res)} (sin copias -N ni 404)')
for cuando in ('antes', 'despues'):
    f2, q2 = resumen(cuando)
    print(f'\n== {cuando.upper()}: con 2+ formularios: {len(f2)} · con 2+ bloques FAQ: {len(q2)}')
    c = collections.Counter((tuple(res[k][cuando]['forms']), res[k]['ruta']) for k in f2)
    for (t, r), n in sorted(c.items(), key=lambda x: (x[0][0], x[0][1])): print(f'   formularios {list(t)} en {r}: {n}')
    c = collections.Counter((tuple(res[k][cuando]['faqs']), res[k]['ruta']) for k in q2)
    for (t, r), n in sorted(c.items()): print(f'   faq {list(t)} en {r}: {n}')
rep = {k: v['despues']['repetidas'] for k, v in res.items() if v['despues']['repetidas']}
print(f'\nPreguntas repetidas dentro de una página (después): {len(rep)} páginas')
for k, v in list(rep.items())[:8]: print('  ', k, v[:2])
sc = collections.Counter(v['schema'] for v in res.values())
print('\nFAQPage en el <head> por página (n.º de esquemas → páginas):', dict(sorted(sc.items())))
print('   con 2+:', [k for k, v in res.items() if v['schema'] > 1][:10])
sin = collections.Counter(res[k]['ruta'] for k, v in res.items() if not v['despues']['forms'])
con = collections.Counter(res[k]['ruta'] for k, v in res.items() if len(v['despues']['forms']) == 1)
print('\nPor ruta (después) — páginas con 1 formulario / sin formulario:', {r: (con[r], sin[r]) for r in sorted(set(con) | set(sin))})
