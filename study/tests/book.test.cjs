const test=require('node:test');
const assert=require('node:assert/strict');
const fs=require('node:fs');
const path=require('node:path');
const vm=require('node:vm');
const chapters=require('../book/chapters.cjs');
const labs=require('../book/labs.cjs');
const root=path.resolve(__dirname,'..');
const html=id=>fs.readFileSync(path.join(root,'book',`chapter-${String(id).padStart(2,'0')}`,'index.html'),'utf8');
function mount(id,storage=new Map(),blocked=false){
 const source=html(id),nodes=[],stack=[];
 for(const m of source.matchAll(/<(\/?)([a-z][\w-]*)\b([^>]*)>/gi)){
  if(m[1]){const position=stack.map(n=>n.tag).lastIndexOf(m[2]);if(position>=0)stack.splice(position);continue}
  const attrs={};for(const a of m[3].matchAll(/([\w-]+)="([^"]*)"/g))attrs[a[1]]=a[2];
  const node={tag:m[2],id:attrs.id,attrs,parent:stack.at(-1),events:{},style:{},value:attrs.value||'',checked:false,textContent:'',setAttribute(k,v){this.attrs[k]=v},addEventListener(event,fn){this.events[event]=fn}};
  Object.defineProperty(node,'className',{get(){return this.attrs.class||''},set(v){this.attrs.class=v}});
  Object.defineProperty(node,'innerHTML',{get(){return this.markup||''},set(value){this.markup=value;this.textContent=value.replace(/<[^>]+>/g,'').replaceAll('&amp;','&').replaceAll('&lt;','<').replaceAll('&gt;','>')}});
  if(node.tag==='select'){const tail=source.slice(m.index).split('</select>')[0];const options=[...tail.matchAll(/<option value="([^"]*)"([^>]*)>/g)];node.value=(options.find(v=>v[2].includes('selected'))||options[0])?.[1]||''}
  nodes.push(node);if(!/^(input|meta|link|img|br|hr)$/.test(node.tag)&&!m[3].endsWith('/'))stack.push(node);
 }
 function match(node,s){if(s[0]==='#')return node.attrs.id===s.slice(1);const tag=s.match(/^[a-z][\w-]*/)?.[0];if(tag&&node.tag!==tag)return false;for(const c of s.matchAll(/\.([\w-]+)/g))if(!(node.attrs.class||'').split(' ').includes(c[1]))return false;for(const a of s.matchAll(/\[([\w-]+)(?:(\^=|=)"?([^\]"']*)"?)?\]/g)){const v=node.attrs[a[1]];if(a[2]==='='?v!==a[3]:a[2]==='^='?!v?.startsWith(a[3]):v===undefined)return false}return true}
 function select(selector){return [...new Set(selector.split(',').flatMap(s=>{const parts=s.trim().split(/\s+/);return nodes.filter(node=>{if(!match(node,parts.at(-1)))return false;let ancestor=node.parent;for(let i=parts.length-2;i>=0;i--){while(ancestor&&!match(ancestor,parts[i]))ancestor=ancestor.parent;if(!ancestor)return false;ancestor=ancestor.parent}return true})}))]}
 const context={document:{querySelector:s=>select(s)[0]||null,querySelectorAll:select},localStorage:{getItem(k){if(blocked)throw Error('blocked');return storage.get(k)||null},setItem(k,v){if(blocked)throw Error('blocked');storage.set(k,v)}},window:{addEventListener(){}},confirm:()=>true};
 vm.createContext(context);vm.runInContext(source.match(/<script>([\s\S]*?)<\/script>/)[1],context);
 return {$:s=>select(s)[0],storage,context};
}
test('seven complete source-aligned chapters, original explanations and valid chapter links',()=>{
 assert.deepEqual(chapters.map(c=>c.id),[1,2,3,4,5,6,7]);
 assert.deepEqual(chapters.map(c=>c.pages[0]),[3,37,87,113,155,177,191]);
 assert.deepEqual(chapters.map(c=>c.pages[1]),[34,85,110,153,175,189,213]);
 for(const c of chapters){
  assert.ok(c.sections.length>=7);assert.equal(c.quiz.length,6);assert.ok(c.objectives.length>=3);
  for(const section of c.sections){assert.ok(section.paragraphs.length>=2);assert.ok(section.ref)}
  const file=path.join(root,'book',`chapter-${String(c.id).padStart(2,'0')}`,'index.html'),s=html(c.id);
  assert.match(s,/<html lang="fa" dir="rtl">/);assert.match(s,/صفحه‌های چاپی/);new vm.Script(s.match(/<script>([\s\S]*?)<\/script>/)[1]);
  for(const [,href]of s.matchAll(/href="([^\"]+)"/g)){if(href.startsWith('data:'))continue;const bare=href.split(/[?#]/)[0];if(!bare){assert.ok(s.includes('id="'+href.slice(1)+'"'));continue}const target=path.resolve(path.dirname(file),bare);assert.ok(fs.existsSync(target),href);const hash=href.split('#')[1];if(hash)assert.ok(fs.readFileSync(target,'utf8').includes('id="'+hash+'"'))}
  const notebook=JSON.parse(fs.readFileSync(path.join(path.dirname(file),'resources','exercises.ipynb'),'utf8'));assert.equal(notebook.nbformat,4);assert.equal(notebook.cells.filter(c=>c.cell_type==='code').length,2);
 }
});
test('mathematics of all seven live labs, including zero denominators and unstable learning rates',()=>{
 for(let n=40;n<=80;n++){const s=labs.splitCounts(n);assert.equal(s.train+s.validation+s.test,100)}
 let previousRecall=1;
 for(let i=0;i<=100;i++){const r=labs.classification(i/100);assert.equal(r.tp+r.fp+r.fn+r.tn,6);assert.ok(r.recall<=previousRecall);previousRecall=r.recall;if(r.precision!==null)assert.ok(r.precision>=0&&r.precision<=1)}
 assert.equal(labs.classification(1).precision,null);assert.equal(labs.classification(0).recall,1);
 assert.deepEqual([labs.classification(.5).tp,labs.classification(.5).fp,labs.classification(.5).fn,labs.classification(.5).tn],[2,1,1,2]);
 const stable=labs.descent(.1,8);assert.ok(stable.at(-1).loss<4);assert.ok(labs.descent(1.2,8).at(-1).loss>4);assert.ok(Math.abs(labs.descent(.1,2)[2].loss-1.6384)<1e-12);
 assert.equal(labs.rbf(1,0),1);assert.ok(labs.rbf(2,1)<labs.rbf(.5,1));
 assert.equal(labs.gini([0,0,0]),0);assert.equal(labs.gini([0,1]),.5);assert.equal(labs.treeSplit(3.5).cost,0);assert.ok(labs.treeSplit(2.5).cost>0);
 const v=labs.voting([.49,.49,.99]);assert.equal(v.hard,0);assert.equal(v.soft,1);assert.ok(Math.abs(v.mean-1.97/3)<1e-12);
});
test('all book quizzes, notes, completion and reset run in the actual shipped script',()=>{
 for(const chapter of chapters){
  const store=new Map([['discrete-fa-v1-day-1','preserve'],['unrelated','keep']]);let ui=mount(chapter.id,store);
  assert.equal(ui.$('#book-progress').attrs['aria-valuenow'],0);
  chapter.quiz.forEach((q,i)=>{const wrong=ui.$('input[name="book-q'+i+'"][value="'+(q[2]+1)%3+'"]');wrong.events.change();assert.match(ui.$('#book-feedback-'+i).textContent,/هنوز درست نیست/);const right=ui.$('input[name="book-q'+i+'"][value="'+q[2]+'"]');right.events.change();assert.match(ui.$('#book-feedback-'+i).textContent,/درست است/)});
  ui.$('#book-note').events.input({target:{value:'یادداشت فصل '+chapter.id}});ui.$('#book-done').events.change({target:{checked:true}});
  assert.equal(ui.$('#book-progress').attrs['aria-valuenow'],100);assert.ok(ui.$('#book-note-download').href.startsWith('data:text/plain;'));
  ui=mount(chapter.id,store);assert.equal(ui.$('#book-note').value,'یادداشت فصل '+chapter.id);assert.equal(ui.$('#book-progress').attrs['aria-valuenow'],100);
  ui.$('#book-reset').events.click();assert.equal(ui.$('#book-progress').attrs['aria-valuenow'],0);assert.equal(ui.$('#book-note').value,'');assert.equal(store.get('unrelated'),'keep');assert.equal(store.get('discrete-fa-v1-day-1'),'preserve');
 }
});
test('all chapters tolerate disabled storage and interactive controls update real outputs',()=>{
 for(const c of chapters){const ui=mount(c.id,new Map(),true);assert.match(ui.$('#book-storage').textContent,/در دسترس نیست/);assert.ok(ui.$('#lab-result').textContent.length>0)}
 const threshold=mount(3);threshold.$('#threshold').value='100';threshold.$('#threshold').events.input();assert.match(threshold.$('#lab-result').textContent,/تعریف‌نشده/);
 const leakage=mount(2);leakage.$('#imputer').value='all';leakage.$('#imputer').events.input();assert.match(leakage.$('#lab-result').textContent,/نشت/);
 const gradient=mount(4);gradient.$('#rate').value='120';gradient.$('#rate').events.input();assert.match(gradient.$('#lab-result').textContent,/بیشتر/);
 const tree=mount(6);tree.$('#tree').value='3.5';tree.$('#tree').events.input();assert.match(tree.$('#lab-result').textContent,/هزینهٔ وزن‌دار ۰/);
 const voting=mount(7);voting.$('#oob-row').value='0';voting.$('#oob-row').events.input();assert.match(voting.$('#oob-result').textContent,/دیده شده/);voting.$('#oob-row').value='2';voting.$('#oob-row').events.input();assert.match(voting.$('#oob-result').textContent,/دیده نشده/);
});
