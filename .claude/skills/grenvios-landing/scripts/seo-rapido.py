"""Comprobación SEO rápida de las páginas clave de Grenvíos.
Uso: python seo-rapido.py [ruta ...]   (sin argumentos: las páginas clave)
"""
import re,sys,urllib.request,html
sys.stdout.reconfigure(encoding='utf-8')
urls=sys.argv[1:] or ['','servicios/','servicios/envio-internacional-de-paquetes/','servicios/envio-internacional-de-documentos/','servicios/carga-internacional/','servicios/apostilla-y-traduccion/','destinos/','destinos/ecuador/','destinos/estados-unidos/','cotizar/','contacto/','nosotros/','blog/','servicios/peso-volumetrico/','ec/','us/envios-a-estados-unidos/']
print(f"{'url':52} {'tit':>3} {'desc':>4} H1 H2 {'pal':>5} cot  wa img sinalt canon schema")
for u in urls:
    try:
        r=urllib.request.urlopen('http://greenvios.localhost/'+u,timeout=60); h=r.read().decode('utf-8','ignore')
    except Exception as e: print(u,'ERR',e); continue
    t=re.search(r'<title>(.*?)</title>',h,re.S); t=html.unescape(t.group(1).strip()) if t else ''
    d=re.search(r'<meta name="description" content="([^"]*)"',h); d=html.unescape(d.group(1)) if d else ''
    body=re.sub(r'<(script|style|header|footer|nav)\b.*?</\1>',' ',h,flags=re.S)
    txt=re.sub(r'<[^>]+>',' ',body); pal=len(re.findall(r'\w+',txt))
    imgs=re.findall(r'<img\b[^>]*>',h); sinalt=[i for i in imgs if not re.search(r'\salt=',i)]
    print(f"{'/'+u:52} {len(t):>3} {len(d):>4} {len(re.findall(r'<h1\b',h)):>2} {len(re.findall(r'<h2\b',h)):>2} {pal:>5} {len(re.findall(r'href=\"[^\"]*/cotizar',h)):>3} {len(re.findall(r'wa\.me',h)):>3} {len(imgs):>3} {len(sinalt):>5} {len(re.findall(r'rel=\"canonical\"',h)):>5} {len(re.findall(r'application/ld\+json',h)):>5}")
    if len(t)>62 or len(d)>170 or len(d)<70: print('   ! title/desc:',t,'|',d)
