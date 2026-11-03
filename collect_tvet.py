import re, html, urllib.request, urllib.parse, json, concurrent.futures, pathlib, time
OUT=pathlib.Path('tvet-directory'); OUT.mkdir(exist_ok=True)
def clean(s): return ' '.join(html.unescape(re.sub('<[^>]+>', ' ', s)).split())
def get(url):
    p=OUT/(str(__import__('hashlib').sha256(url.encode()).hexdigest())+'.html')
    if p.exists(): return p.read_text(encoding='utf8')
    for n in range(4):
        try:
            s=urllib.request.urlopen(url,timeout=60).read().decode(); p.write_text(s,encoding='utf8'); return s
        except Exception:
            if n==3: raise
            time.sleep(2)
def pages(url):
    s=get(url)
    total=max([1]+[int(x) for x in re.findall(r'[?&](?:amp;)?page=(\d+)',s)])
    yield s
    for i in range(2,total+1): yield get(url+('&' if '?' in url else '?')+'page='+str(i))
root=pathlib.Path('tvet-directory.html').read_text(encoding='utf8')
agencies=[]
for title,aid in re.findall(r'<h5[^>]*>(.*?)</h5>.*?/awam-agensi/(\d+)/senarai-institusi',root,re.S):
    agencies.append(dict(id=aid,name=clean(title),institutions={}))
def collect(a):
    base='https://www.tvet.gov.my/awam-agensi/'
    for s in pages(base+a['id']+'/senarai-institusi'):
        for name in re.findall(r'<h5[^>]*>(.*?)</h5>',s,re.S):
            name=clean(name)
            if name: a['institutions'].setdefault(name,[])
    for s in pages(base+'index-kursus?id_agensi='+a['id']):
        for title,body in re.findall(r'<h5[^>]*awam-course-title[^>]*>(.*?)</h5>(.*?)(?=<h5[^>]*awam-course-title|$)',s,re.S):
            inst=re.search(r'awam-course-institution[^>]*>(.*?)<br',body,re.S)
            link=re.search(r'href="([^"]*id_kursus=[^"]+)"',body)
            if not inst or not link: continue
            name=clean(inst.group(1)); courses=a['institutions'].setdefault(name,[])
            url=html.unescape(link.group(1))
            if any(c['url']==url for c in courses): continue
            courses.append(dict(name=re.sub(r'^\d+\.\s*','',clean(title)),url=url))
    print(a['name'],len(a['institutions']),sum(map(len,a['institutions'].values())),flush=True)
    return a
with concurrent.futures.ThreadPoolExecutor(max_workers=4) as pool: data=list(pool.map(collect,agencies))
(OUT/'directory.json').write_text(json.dumps(data,ensure_ascii=False,indent=2),encoding='utf8')
lines=['# TVET institutions and courses by agency','Retrieved 3 November 2026 from https://www.tvet.gov.my/awam-agensi/index','Duplicate names with different source IDs are retained. Institutions with no listed courses are included.','']
for a in data:
    lines+=['## '+a['name'],'']
    for name,courses in sorted(a['institutions'].items()):
        lines+=['### '+name,'']
        lines += ['- ['+c['name']+']('+c['url']+')' for c in courses] or ['No courses listed in the agency course directory.']
        lines+=['']
(OUT/'directory.md').write_text('\n'.join(lines),encoding='utf8')
body='<input id="q" placeholder="Search agencies, institutions or courses" style="width:95%;padding:12px" oninput="document.querySelectorAll(\'article\').forEach(e=>e.hidden=!e.textContent.toLowerCase().includes(this.value.toLowerCase()))">'
for a in data:
    for name,cs in sorted(a['institutions'].items()):
        body+='<article><h2>'+html.escape(a['name'])+'</h2><h3>'+html.escape(name)+'</h3><ul>'+(''.join('<li><a href="'+html.escape(c['url'])+'">'+html.escape(c['name'])+'</a></li>' for c in cs) or '<li>No courses listed</li>')+'</ul></article>'
(OUT/'directory.html').write_text('<!doctype html><meta charset="utf-8"><title>TVET Directory</title><style>body{font:16px system-ui;max-width:1100px;margin:40px auto;padding:20px}article{border-bottom:1px solid #ddd;padding:15px}h2{font-size:15px;color:#567}li{margin:8px}</style><h1>TVET institutions and courses by agency</h1><p>Retrieved 3 November 2026. Source: TVET Madani public directory.</p>'+body,encoding='utf8')
print('TOTAL',len(data),sum(len(a['institutions']) for a in data),sum(len(cs) for a in data for cs in a['institutions'].values()))
