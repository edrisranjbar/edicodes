const fs = require('node:fs');
const path = require('node:path');
const postcss = require('../edicodes/node_modules/postcss');

const source = process.argv[2];
if (!source) throw new Error('Pass the python-course source directory');
const base = fs.readFileSync(path.join(source, 'assets/css/style.css'), 'utf8');
const variables = base.match(/:root\s*\{([^}]+)\}/)[1];
const css = postcss.parse(fs.readFileSync(path.join(source, 'assets/css/lesson.css'), 'utf8'));
const baseStyles = postcss.parse(base);
baseStyles.walkRules(rule => {
  if (rule.selectors.every(selector => /^\.token-[\w-]+$/.test(selector) || selector === '.no-border')) css.append(rule.clone());
});
css.walkRules(rule => {
  if (rule.parent.type === 'atrule' && /keyframes$/.test(rule.parent.name)) return;
  rule.selectors = rule.selectors.map(selector => `.lesson-html ${selector}`);
});
const overrides = `
.lesson-html {${variables} min-width:0; color:var(--text-primary); line-height:1.9; overflow-wrap:anywhere; }
.lesson-html *, .lesson-html *::before, .lesson-html *::after { box-sizing:border-box; }
.lesson-html .lesson-main { margin:0; padding:0; width:100%; max-width:none; min-width:0; }
.lesson-html .lesson-section { min-width:0; }
.lesson-html p { margin-block:1rem; }
.lesson-html ul, .lesson-html ol { padding-inline-start:1.5rem; margin-block:1rem; }
.lesson-html h2, .lesson-html h3 { margin-block:1.5rem .75rem; }
.lesson-html a { color:var(--accent-light); overflow-wrap:anywhere; }
.lesson-html pre { direction:ltr; text-align:left; white-space:pre; overflow-x:auto; max-width:100%; tab-size:4; }
.lesson-html pre code { white-space:inherit; font-size:.875rem; }
.lesson-html :not(pre)>code { direction:ltr; unicode-bidi:embed; overflow-wrap:anywhere; color:#fbbf24; }
.lesson-html table { width:100%; border-collapse:collapse; table-layout:fixed; margin-block:1.5rem; }
.lesson-html td, .lesson-html th { padding:.75rem; border:1px solid var(--border-light); overflow-wrap:anywhere; }
.lesson-html iframe { width:100%; max-width:100%; border:0; }
.lesson-html .code-block, .lesson-html .hint-body, .lesson-html .quiz-card, .lesson-html .quiz-option { min-width:0; max-width:100%; }
.lesson-html .code-block pre { margin:0; }
.lesson-html button { cursor:pointer; }
@media (max-width:640px) {
  .lesson-html .lesson-section { padding:1rem; }
  .lesson-html .lesson-title { font-size:1.6rem; }
  .lesson-html .lesson-lead { font-size:1rem; }
  .lesson-html .quiz-question-row, .lesson-html .reference-card { gap:.65rem; }
  .lesson-html td, .lesson-html th { padding:.5rem; font-size:.8rem; }
}
`;
fs.writeFileSync(path.join(__dirname, '../edicodes/src/assets/css/pythonLesson.css'),
  '/* Adapted from python-course/assets/css/lesson.css. Regenerate with scripts/build-python-lesson-css.cjs. */\n' + css.toString() + overrides);
