const fs = require('node:fs');
const path = require('node:path');

// Embed bundled fonts so copied, standalone HTML files also work offline.
const faces = [
  ['Course Vazir', 400, 'Vazir-Regular-FD.ttf'],
  ['Course Vazir', 700, 'Vazir-Bold-FD.ttf'],
  ['Course Morabba', 700, 'Morabba-Bold.ttf'],
];
module.exports = faces.map(([family, weight, file]) => {
  const bytes = fs.readFileSync(path.join(__dirname, '..', 'assets', 'fonts', file));
  return `@font-face{font-family:"${family}";font-style:normal;font-weight:${weight};font-display:swap;src:url("data:font/ttf;base64,${bytes.toString('base64')}") format("truetype")}`;
}).join('\n');
