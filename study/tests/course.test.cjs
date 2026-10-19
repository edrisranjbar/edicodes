const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');
const root = path.resolve(__dirname, '..');
const fa = n => String(n).replace(/\d/g, x => '۰۱۲۳۴۵۶۷۸۹'[x]);
const answers = [[0,1],[1,2],[1,2],[1,0],[0,1],[1,1,1,2],[1,2]];
test('authored text and essential graphical color pairs meet numerical contrast thresholds',()=>{
 const {audit,css}=require('../scripts/contrast.cjs');
 const results=audit();
 for(const p of results)assert.ok(p.ratio>=p.minimum,`${p.label}: ${p.ratio.toFixed(2)} < ${p.minimum}`);
 const shared=fs.readFileSync(path.join(root,'assets/styles.css'),'utf8');
 assert.ok(shared.endsWith(css+'\n'),'Contrast overrides must be present in the generated stylesheet');
 for(let day=1;day<=7;day++)assert.ok(html(day).includes(css),'Contrast overrides must be embedded in every lesson');
});
const html = day => fs.readFileSync(path.join(root, `day-${String(day).padStart(2,'0')}/index.html`),'utf8');
// Lightweight DOM adapter executes the actual shipped script without dependencies.
function mount(day, storage = new Map(), blocked = false) {
  const source = html(day), nodes = [];
  for (const match of source.matchAll(/<([a-z][\w-]*)\b([^>]*)>/gi)) {
    const attrs = {};
    for (const a of match[2].matchAll(/([\w-]+)="([^"]*)"/g)) attrs[a[1]]=a[2];
    const node={tag:match[1],attrs,dataset:{},style:{},value:attrs.value||'',textContent:'',checked:false,events:{},setAttribute(k,v){this.attrs[k]=v},addEventListener(k,f){this.events[k]=f}};
    Object.defineProperty(node,'innerHTML',{get(){return this.markup||''},set(v){this.markup=v;this.textContent=v.replace(/<[^>]+>/g,'').replaceAll('&amp;','&').replaceAll('&lt;','<').replaceAll('&gt;','>')}});
    for (const [k,v] of Object.entries(attrs)) if(k.startsWith('data-'))node.dataset[k.slice(5)]=v;
    nodes.push(node);
  }
  function select(s) {
    if(s.startsWith('#'))return nodes.filter(n=>n.attrs.id===s.slice(1));
    if(s.startsWith('.'))return nodes.filter(n=>(n.attrs.class||'').split(' ').includes(s.slice(1)));
    const tag=s.match(/^[a-z]+/)?.[0];
    const filters=[...s.matchAll(/\[([\w-]+)(?:=["']?([^\]"']+)["']?)?\]/g)];
    return nodes.filter(n=>(!tag||n.tag===tag)&&filters.every(([,k,v])=>v===undefined?k in n.attrs:n.attrs[k]===v));
  }
  const context={document:{querySelector:s=>select(s)[0]||null,querySelectorAll:select},localStorage:{getItem(k){if(blocked)throw Error('blocked');return storage.get(k)||null},setItem(k,v){if(blocked)throw Error('blocked');storage.set(k,v)}},confirm:()=>true};
  vm.createContext(context);
  const script=source.match(/<script>([\s\S]*?)<\/script>/)[1];
  vm.runInContext(script,context);
  return {$:s=>select(s)[0],all:select,ctx:context,storage,run:s=>vm.runInContext(s,context)};
}
test('84 preserved lesson directories and original topic sections',()=>{
  const plan=JSON.parse(fs.readFileSync(path.join(root,'plan.json'),'utf8'));
  assert.equal(plan.length,84);
  assert.deepEqual(plan.map(x=>x.day),Array.from({length:84},(_,i)=>i+1));
  for(const d of plan){assert.equal(d.week,Math.ceil(d.day/7));for(const sub of ['notes','images','videos','resources'])assert.ok(fs.existsSync(path.join(root,`day-${String(d.day).padStart(2,'0')}`,sub,'README.md')))}
});
test('all published pages are Persian RTL, standalone, with valid local links and scripts',()=>{
  for(const file of ['index.html',...Array.from({length:7},(_,i)=>`day-0${i+1}/index.html`),...Array.from({length:9},(_,i)=>`chapters/chapter-${String(i+2).padStart(2,'0')}/index.html`),...Array.from({length:28},(_,i)=>`intensive/day-${String(i+1).padStart(2,'0')}/index.html`)]){
    const full=path.join(root,file),s=fs.readFileSync(full,'utf8');
    assert.match(s,/<html lang="fa" dir="rtl">/);assert.match(s,/<meta name="viewport"/);
    assert.ok(!/<(?:script|img)[^>]+src=|<link[^>]+href=/.test(s));
    assert.ok(!/https?:\/\//.test(s));
    const script=s.match(/<script>([\s\S]*?)<\/script>/);if(script)new vm.Script(script[1]);
    for(const [,href] of s.matchAll(/href="([^"]+)"/g)){if(href.startsWith('#')){assert.ok(s.includes(`id="${href.slice(1)}"`));continue}assert.ok(fs.existsSync(path.resolve(path.dirname(full),href)),`${file}: ${href}`)}
    const ids=[...s.matchAll(/\bid="([^"]+)"/g)].map(m=>m[1]);assert.equal(ids.length,new Set(ids).size);
  }
});
test('ten-chapter syllabus links every chapter and marks the active chapter and lesson',()=>{
 const chapters=JSON.parse(fs.readFileSync(path.join(root,'syllabus.json'),'utf8'));
 assert.equal(chapters.length,10);assert.equal(chapters[0].title,'ریاضی مقدماتی برای ML');
 for(const c of chapters){assert.ok(c.topics.length>=6);assert.ok(c.prereq&&c.project&&c.exit)}
 const {sidebar}=require('../scripts/syllabus.cjs');
 const lesson=sidebar('../',1,3);
 assert.match(lesson,/href="\.\.\/day-03\/index.html" aria-current="page"/);
 assert.match(lesson,/href="\.\.\/index.html" aria-current="true"/);
 for(let c=2;c<=10;c++){
   const p=fs.readFileSync(path.join(root,`chapters/chapter-${String(c).padStart(2,'0')}/index.html`),'utf8');
   assert.match(p,new RegExp(`href="../../chapters/chapter-${String(c).padStart(2,'0')}/index.html" aria-current="page"`));
   assert.match(p,/درس‌های تعاملی این فصل هنوز ساخته نشده‌اند/);
 }
});
test('quizzes: wrong/right feedback, 100%, persistence, notes, and day-only reset',()=>{
 for(let day=1;day<=7;day++){
   const storage=new Map([['unrelated','keep']]);let ui=mount(day,storage);
   assert.equal(ui.$('#progress').attrs['aria-valuenow'],0);
   answers[day-1].forEach((a,i)=>{
     const wrong=ui.$(`input[name=q${i}][value="${(a+1)%3}"]`);wrong.events.change();assert.match(ui.$(`#feedback-${i}`).textContent,/هنوز درست نیست/);
     const right=ui.$(`input[name=q${i}][value="${a}"]`);right.events.change();assert.match(ui.$(`#feedback-${i}`).textContent,/درست است/);
   });
   ui.$('#note').events.input({target:{value:'یادداشت آزمایشی'}});ui.$('#complete').onclick();
   assert.equal(ui.$('#progress').attrs['aria-valuenow'],100);
   ui=mount(day,storage);assert.equal(ui.$('#progress').attrs['aria-valuenow'],100);assert.equal(ui.$('#note').value,'یادداشت آزمایشی');
   ui.$('#reset').onclick();assert.equal(ui.$('#progress').attrs['aria-valuenow'],0);assert.equal(ui.$('#note').value,'');assert.equal(storage.get('unrelated'),'keep');
 }
});
test('every lesson remains interactive when localStorage is unavailable',()=>{
 for(let day=1;day<=7;day++){const ui=mount(day,new Map(),true);ui.$('#complete').onclick();assert.match(ui.$('#storage').textContent,/در دسترس نیست/);assert.ok(Number(ui.$('#progress').attrs['aria-valuenow'])>0)}
});
test('all six operations across all 4096 pairs of subsets of the six-user universe',()=>{
 const ui=mount(5);
 ui.ctx.expected = null;
 ui.run(`for(let am=0;am<64;am++){for(let bm=0;bm<64;bm++){A=new Set(U.filter((x,i)=>am&(1<<i)));B=new Set(U.filter((x,i)=>bm&(1<<i)));for(const name of Object.keys(ops)){op=name;draw();const expected=U.filter(x=>{const a=A.has(x),b=B.has(x);switch(name){case 'union':return a||b;case 'intersection':return a&&b;case 'ab':return a&&!b;case 'ba':return b&&!a;case 'complement':return !a;case 'xor':return a!==b}});if(!$('#output').textContent.endsWith(fmt(expected)+' · تعداد: '+fa(expected.length)))throw Error('operation '+name);const inactive=U.filter(x=>!A.has(x)&&!B.has(x));if(inactive.length+U.filter(x=>A.has(x)||B.has(x)).length!==6)throw Error('partition')}}}`);
});
test('user buttons update membership, operation selection, and project report',()=>{
 const ui=mount(5);ui.$('[data-group=A][data-member="5"]').onclick();ui.$('[data-group=B][data-member="5"]').onclick();
 assert.match(ui.$('#report').textContent,/فعال: ۵ \| مشترک: ۲/);assert.match(ui.$('#report').textContent,/غیرفعال: ۱/);
 ui.$('[data-op=xor]').onclick();assert.equal(ui.$('[data-op=xor]').attrs['aria-pressed'],true);assert.match(ui.$('#output').textContent,/تعداد: ۳/);
});
test('power sets and Cartesian products for zero through four elements',()=>{
 const ui=mount(4);
 for(let n=0;n<=4;n++){ui.$('#size').value=String(n);ui.$('#size').onchange();assert.match(ui.$('#power-count').textContent,new RegExp('تعداد زیرمجموعه‌ها: '+fa(2**n)));assert.equal(ui.$('#power-result').textContent.split('   •   ').length,2**n);if(n===0)assert.equal(ui.$('#cart-result').textContent,'∅');else assert.equal(ui.$('#cart-result').textContent.split('   •   ').length,n*2)}
});
test('dashboard preserves lesson progress and resumes the correct intensive day',()=>{
 const nodes=Object.fromEntries(Array.from({length:7},(_,i)=>[i+1,{textContent:''}]));const overall={textContent:''};const storage=new Map();
 storage.set('discrete-fa-v1-day-1',JSON.stringify({answers:{0:0,1:1},done:true}));
 storage.set('discrete-fa-v1-day-2',JSON.stringify({answers:{0:1,1:2},done:false}));
 let refresh;
 const extra={'#path-progress':{textContent:''},'#path-fill':{style:{}},'#resume-link':{textContent:'',href:''}};
 const context={document:{querySelector:s=>s==='#overall'?overall:extra[s]||nodes[Number(s.match(/data-day="(\d+)"/)[1])]},localStorage:{getItem:k=>storage.get(k)||null},window:{addEventListener:(name,fn)=>{assert.equal(name,'pageshow');refresh=fn}}};
 vm.runInNewContext(fs.readFileSync(path.join(root,'index.html'),'utf8').match(/<script>([\s\S]*?)<\/script>/)[1],context);
 assert.match(overall.textContent,/۱ از ۷/);assert.match(nodes[1].textContent,/کامل شده/);assert.equal(nodes[2].textContent,'درس آماده است');
 storage.set('discrete-fa-v1-day-2',JSON.stringify({answers:{0:1,1:2},done:true}));refresh();assert.match(overall.textContent,/۲ از ۷/);
 assert.equal(extra['#resume-link'].href,'intensive/day-01/index.html');
 storage.set('discrete-fa-v1-day-3',JSON.stringify({answers:{0:1,1:2},done:true}));refresh();
 assert.equal(extra['#resume-link'].href,'intensive/day-02/index.html');
 assert.match(extra['#path-progress'].textContent,/۳ از ۸۴/);
 assert.equal(extra['#path-fill'].style.width,3/84*100+'%');
});
test('28 intensive days contain every one of the 84 lessons exactly once in the original order',()=>{
 const days=JSON.parse(fs.readFileSync(path.join(root,'intensive-plan.json'),'utf8'));
 const plan=JSON.parse(fs.readFileSync(path.join(root,'plan.json'),'utf8'));
 assert.equal(days.length,28);
 assert.deepEqual(days.flatMap(d=>d.lessons.map(p=>p.lesson)),Array.from({length:84},(_,i)=>i+1));
 for(const d of days){
  assert.equal(d.lessons.length,3);assert.equal(d.week,Math.ceil(d.day/7));
  for(const sub of ['notes','images','videos','resources'])assert.ok(fs.existsSync(path.join(root,`intensive/day-${String(d.day).padStart(2,'0')}/${sub}/README.md`)));
  for(const [i,p] of d.lessons.entries()){
   assert.equal(p.ready,p.lesson<=7);assert.equal(plan[p.lesson-1].intensiveDay,d.day);assert.equal(p.slot,i+1);
  }
 }
 assert.equal(days[0].lessons.filter(p=>p.ready).length,3);
 assert.equal(days[1].lessons.filter(p=>p.ready).length,3);
 assert.equal(days[2].lessons.filter(p=>p.ready).length,1);
 assert.equal(days[3].lessons.filter(p=>p.ready).length,0);
});
test('mixed RTL prose keeps complete formulas isolated, including parentheses and operators',()=>{
 const {rich,formatHtml}=require('../scripts/learning-design.cjs');
 for(const formula of ['A - B','A + B','۲ * ۳ = ۶','۶ / ۲ = ۳','2 ** 3 = 8','U − (A ∪ B)','A ∩ (B ∪ C) = (A ∩ B) ∪ (A ∩ C)','A = {۱، ۲}','|A ∪ B| = |A| + |B| − |A ∩ B|']){
   assert.equal(rich(formula),`<bdi class="math" dir="ltr">${formula}</bdi>`);
 }
 assert.equal(formatHtml('<p>A - B</p><pre><code>A &amp; B</code></pre>'),'<p><bdi class="math" dir="ltr">A - B</bdi></p><pre><code>A &amp; B</code></pre>');
 assert.ok(!formatHtml('<p>علامت &amp; یعنی اشتراک</p>').includes('&amp;amp;'));
 assert.equal(rich('<img src=x onerror=alert(1)>').includes('<img'),false);
});
test('live membership tables and disjoint-group charts follow empty and full groups',()=>{
 const ui=mount(5);
 assert.equal((ui.$('#members-table').innerHTML.match(/<tr>/g)||[]).length,6);
 ui.run('A=new Set();B=new Set();draw()');
 assert.match(ui.$('#group-chart').innerHTML,/هیچ‌کدام <strong>۶ از ۶/);
 assert.equal((ui.$('#members-table').innerHTML.match(/selected-cell/g)||[]).length,0);
 ui.run('A=new Set(U);B=new Set(U);draw()');
 assert.match(ui.$('#group-chart').innerHTML,/هر دو <strong>۶ از ۶/);
 assert.equal((ui.$('#members-table').innerHTML.match(/selected-cell/g)||[]).length,6);
 const power=mount(4);power.$('#size').value='0';power.$('#size').onchange();assert.match(power.$('#cart-table').innerHTML,/هیچ زوج مرتبی/);
 power.$('#size').value='4';power.$('#size').onchange();assert.equal((power.$('#cart-table').innerHTML.match(/<bdi/g)||[]).length,8);
});
