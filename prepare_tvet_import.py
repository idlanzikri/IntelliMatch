import pathlib, json, re, html
def clean(s): return ' '.join(html.unescape(re.sub('<[^>]+>', ' ', s)).split())
data=json.loads(pathlib.Path('tvet-directory/directory.json').read_text(encoding='utf8'))
metadata={}
for path in pathlib.Path('tvet-directory').glob('*.html'):
    s=path.read_text(encoding='utf8')
    if 'Carian Institusi' not in s: continue
    for name,body in re.findall(r'<h5[^>]*>(.*?)</h5>(.*?)(?=<h5|$)',s,re.S):
        state=re.search(r'bi-geo-alt[^<]*</i>(.*?)</p>',body,re.S)
        address=re.search(r'<p class="mb-3[^>]*>(.*?)</p>',body,re.S)
        metadata[clean(name)]={'state': clean(state.group(1)) if state else None,'address':clean(address.group(1)) if address else None}
for a in data: a['locations']={n:metadata[n] for n in a['institutions'] if n in metadata}
pathlib.Path('database/data').mkdir(exist_ok=True)
pathlib.Path('database/data/tvet-directory.json').write_text(json.dumps(data,ensure_ascii=False,indent=2),encoding='utf8')
