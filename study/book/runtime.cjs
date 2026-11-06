function initializeBook(data){
 const $=s=>document.querySelector(s),fa=n=>String(n).replace(/\d/g,d=>'۰۱۲۳۴۵۶۷۸۹'[d]);
 const key='edicodes-homl2-v1-chapter-'+String(data.id).padStart(2,'0');
 let state={answers:{},note:'',done:false},persistent=true;
 try{const stored=JSON.parse(localStorage.getItem(key)||'null');if(stored&&typeof stored==='object'){state.answers=stored.answers&&typeof stored.answers==='object'?stored.answers:{};state.note=typeof stored.note==='string'?stored.note:'';state.done=stored.done===true}}catch{persistent=false}
 function save(){try{localStorage.setItem(key,JSON.stringify(state));persistent=true}catch{persistent=false}$('#book-storage').textContent=persistent?'یادداشت و پاسخ‌ها در همین مرورگر ذخیره می‌شوند؛ برای نگهداری مستقل، یادداشت را دریافت کن.':'ذخیرهٔ مرورگر در دسترس نیست؛ پیش از بستن صفحه یادداشت را دریافت کن.';$('#book-note-download').href='data:text/plain;charset=utf-8,'+encodeURIComponent('یادداشت فصل '+fa(data.id)+' — '+data.title+'\n\n'+state.note)}
 function feedback(i){const v=state.answers[i],q=data.quiz[i];if(!Number.isInteger(v)||v<0||v>=q[1].length)return;const out=$('#book-feedback-'+i);out.innerHTML=rich((v===q[2]?'درست است. ':'هنوز درست نیست؛ دوباره بررسی کن. ')+q[3]);out.className='feedback '+(v===q[2]?'good':'bad');const input=$('input[name="book-q'+i+'"][value="'+v+'"]');if(input)input.checked=true}
 function progress(){const count=data.quiz.filter((q,i)=>state.answers[i]===q[2]).length,percent=Math.round((count+Number(state.done))/(data.quiz.length+1)*100);$('#book-progress-text').textContent='پیشرفت مطالعهٔ این فصل: '+fa(percent)+'٪؛ پاسخ درست '+fa(count)+' از '+fa(data.quiz.length);$('#book-progress').setAttribute('aria-valuenow',percent);$('#book-progress-fill').style.width=percent+'%';$('#book-done').checked=state.done;save()}
 data.quiz.forEach((q,i)=>{document.querySelectorAll('input[name="book-q'+i+'"]').forEach(input=>input.addEventListener('change',()=>{state.answers[i]=Number(input.value);feedback(i);progress()}));feedback(i)});
 $('#book-note').value=state.note;$('#book-note').addEventListener('input',event=>{state.note=event.target.value;save()});
 $('#book-done').addEventListener('change',event=>{state.done=event.target.checked;progress()});
 $('#book-reset').addEventListener('click',()=>{if(!confirm('فقط یادداشت و پیشرفت همین فصل کتاب پاک شود؟'))return;state={answers:{},note:'',done:false};document.querySelectorAll('input[name^="book-q"]').forEach(input=>input.checked=false);document.querySelectorAll('.book-feedback').forEach(el=>{el.textContent=''});data.quiz.forEach((_,i)=>$('#book-feedback-'+i).textContent='');$('#book-note').value='';progress()});
 progress();initializeLab(data.lab);
}
function initializeBookIndex(chapters){
 const fa=n=>String(n).replace(/\d/g,d=>'۰۱۲۳۴۵۶۷۸۹'[d]);
 function update(){let complete=0,available=true,next=null;
  for(const chapter of chapters){let finished=false;try{const s=JSON.parse(localStorage.getItem('edicodes-homl2-v1-chapter-'+String(chapter.id).padStart(2,'0'))||'null');finished=!!s&&s.done===true&&chapter.answers.every((answer,i)=>s.answers&&s.answers[i]===answer)}catch{available=false}
   if(finished)complete++;else if(next===null)next=chapter.id;
   const item=document.querySelector('[data-book-chapter="'+chapter.id+'"]');if(item)item.textContent=finished?'مطالعه کامل شده ✓':'درس آمادهٔ مطالعه است';
  }
  document.querySelector('#book-overall').textContent=available?'فصل خوانده‌شده: '+fa(complete)+' از ۷ فصل آماده':'ذخیرهٔ مرورگر در دسترس نیست؛ درس‌ها قابل استفاده‌اند.';
  document.querySelector('#book-overall-fill').style.width=complete/7*100+'%';
  const link=document.querySelector('#book-continue');link.href='chapter-'+String(next||7).padStart(2,'0')+'/index.html';link.textContent=next?'ادامهٔ فصل '+fa(next)+' ←':'مرور فصل هفتم ←';
 }update();window.addEventListener('pageshow',update);
}
module.exports={initializeBook,initializeBookIndex};
