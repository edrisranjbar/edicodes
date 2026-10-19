const colors = {
  ink:'#152f42', muted:'#52677a', teal:'#065f63', white:'#ffffff',
  page:'#eff5f7', hero:'#123b48', heroAccent:'#92e8df', link:'#2058b0',
  math:'#edf3f8', hover:'#e6f4f4', table:'#e9f3f5', stripe:'#f8fbfc',
  result:'#edf8f6', worked:'#f1faf7', callout:'#fff7e7', option:'#f6f9fb',
  caption:'#f4f8fa', example:'#eff6ff', good:'#075d44', bad:'#9c3124',
  code:'#102c3b', codeText:'#d5f5f0', focus:'#123b48',
  selected:'#339780', unselected:'#e9f0f5', diagramInk:'#081e2a',
  track:'#e5eef1', progressTrack:'#dce8eb', blue:'#315fd1', purple:'#7958a6', gray:'#74858f',
};
const css=`
:root{--teal:${colors.teal}}.math{color:${colors.ink}}button .math,.hero .math{color:inherit;background:transparent}
button,select,textarea{border-color:${colors.muted}}textarea{background:${colors.white};color:${colors.ink}}textarea::placeholder{color:${colors.muted};opacity:1}
button:focus-visible,a:focus-visible,summary:focus-visible,textarea:focus-visible,select:focus-visible,input:focus-visible,.table-wrap:focus-visible{outline:3px solid ${colors.focus};outline-offset:5px;box-shadow:0 0 0 3px ${colors.white}}
.chart-track span{background:${colors.teal}}svg circle[stroke],svg rect[stroke],svg path{stroke:${colors.diagramInk}}svg circle[stroke="${colors.teal}"]{stroke-dasharray:8 4}
`.trimEnd();
function luminance(hex){const c=hex.slice(1).match(/../g).map(x=>parseInt(x,16)/255).map(x=>x<=.04045?x/12.92:((x+.055)/1.055)**2.4);return c[0]*.2126+c[1]*.7152+c[2]*.0722}
function ratio(a,b){const x=luminance(a),y=luminance(b);return (Math.max(x,y)+.05)/(Math.min(x,y)+.05)}
function audit(){
 const pairs=[];
 const add=(label,a,b,min=4.5)=>pairs.push({label,foreground:a,background:b,minimum:min,ratio:ratio(a,b)});
 for(const surface of ['white','page','math','hover','table','stripe','result','worked','callout','option','caption','example'])add('متن روی '+surface,colors.ink,colors[surface]);
 for(const surface of ['white','page','result','option','math'])add('متن فرعی روی '+surface,colors.muted,colors[surface]);
 for(const surface of ['white','page','hover'])add('پیوند روی '+surface,colors.link,colors[surface]);
 for(const surface of ['white','page'])add('متن فیروزه‌ای روی '+surface,colors.teal,colors[surface]);
 add('دکمهٔ انتخاب‌شده',colors.white,colors.teal);
 add('متن سربرگ',colors.white,colors.hero);add('عنوان فرعی سربرگ',colors.heroAccent,colors.hero);
 add('کد',colors.codeText,colors.code);
 add('بازخورد درست',colors.good,colors.white);add('بازخورد غلط',colors.bad,colors.white);add('سلول انتخاب‌شده',colors.good,colors.stripe);
 for(const surface of ['white','hover'])add('مرز کنترل روی '+surface,colors.muted,colors[surface],3);
 add('تمرکز روی سطح روشن',colors.focus,colors.white,3);add('حلقهٔ روشن تمرکز روی سربرگ',colors.white,colors.hero,3);add('حلقهٔ روشن تمرکز روی انتخاب',colors.white,colors.teal,3);
 add('ناحیهٔ انتخاب‌شدهٔ ون',colors.selected,colors.unselected,3);
 for(const surface of ['selected','unselected'])add('متن نمودار روی '+surface,colors.diagramInk,colors[surface]);
 for(const color of ['teal','blue','purple','gray'])add('نوار نمودار '+color,colors[color],colors.track,3);
 add('نوار پیشرفت',colors.teal,colors.progressTrack,3);
 return pairs;
}
module.exports={colors,css,audit};
if(require.main===module){
 const results=audit();for(const p of results)console.log(`${p.ratio>=p.minimum?'PASS':'FAIL'} ${p.ratio.toFixed(2)}:1 (>=${p.minimum}) ${p.label}`);
 const failed=results.filter(p=>p.ratio<p.minimum);console.log(`${results.length-failed.length}/${results.length} contrast checks passed`);if(failed.length)process.exitCode=1;
}
