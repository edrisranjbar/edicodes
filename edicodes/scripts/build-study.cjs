const fs = require('node:fs')
const path = require('node:path')
const crypto = require('node:crypto')
const { execFileSync } = require('node:child_process')
const postcss = require('postcss')
const frontend = path.resolve(__dirname, '..')
const source = path.resolve(frontend, '..', 'study')
const output = path.join(frontend, 'public', 'study-content')
const hash = data => crypto.createHash('sha256').update(data).digest('hex').slice(0, 16)
const write = (file, data) => { fs.mkdirSync(path.dirname(file), { recursive: true }); fs.writeFileSync(file, data) }
execFileSync(process.execPath, [path.join(source, 'scripts', 'build.cjs')], { stdio: 'inherit' })
const walk = folder => fs.readdirSync(folder, { withFileTypes: true }).flatMap(item => {
  const file = path.join(folder, item.name)
  return item.isDirectory() ? walk(file) : [file]
})
const inputs = [path.join(source, 'index.html')]
for (const folder of ['chapters', 'intensive', ...Array.from({ length: 84 }, (_, i) => `day-${String(i + 1).padStart(2, '0')}`)]) inputs.push(...walk(path.join(source, folder)))
let css = fs.readFileSync(path.join(source, 'assets', 'styles.css'), 'utf8')
const fontFiles = ['Vazir-Regular-FD.ttf', 'Vazir-Bold-FD.ttf', 'Morabba-Bold.ttf']
let fontIndex = 0
css = css.replace(/data:font\/ttf;base64,([A-Za-z0-9+/=]+)/g, (_, encoded) => {
  const bytes = Buffer.from(encoded, 'base64')
  const name = `${path.parse(fontFiles[fontIndex++]).name}.${hash(bytes)}.ttf`
  write(path.join(output, 'assets', 'fonts', name), bytes)
  return `fonts/${name}`
})
if (fontIndex !== 3) throw new Error('Expected all three bundled course fonts')
const parsed = postcss.parse(css)
parsed.walkRules(rule => {
  if (rule.parent.type === 'atrule' && /keyframes$/i.test(rule.parent.name)) return
  rule.selectors = rule.selectors.flatMap(selector => {
    if (selector === '*') return ['.study-root', '.study-root *']
    if (selector === ':root' || selector === 'body') return ['.study-root']
    if (selector.startsWith('body ')) return [selector.replace(/^body/, '.study-root')]
    return [`.study-root ${selector}`]
  })
})
css = parsed.toString() + '\n.study-root{min-height:100vh;direction:rtl} .study-root a{text-decoration:underline;text-underline-offset:4px} .study-root .chapter-sidebar a,.study-root .hero-action{text-decoration:none}\n'
const cssName = `study.${hash(css)}.css`
write(path.join(output, 'assets', cssName), css)
const manifest = { version: 1, stylesheet: `/study-content/assets/${cssName}`, pages: {} }
const routeFor = relative => '/study' + (relative === 'index.html' ? '' : '/' + relative.replace(/\/index\.html$/, ''))
for (const input of inputs) {
  const relative = path.relative(source, input).split(path.sep).join('/')
  if (!input.endsWith('.html')) {
    fs.mkdirSync(path.dirname(path.join(output, 'files', relative)), { recursive: true })
    fs.copyFileSync(input, path.join(output, 'files', relative))
    continue
  }
  const html = fs.readFileSync(input, 'utf8')
  const body = html.match(/<body>([\s\S]*?)<\/body>/)[1]
  const script = body.match(/<script>([\s\S]*?)<\/script>/)?.[1]
  let markup = body.replace(/<script>[\s\S]*?<\/script>/, '')
  markup = markup.replace(/href="([^\"]+)"/g, (match, href) => {
    if (/^(?:#|[a-z]+:|\/)/i.test(href)) return match
    const resolved = path.posix.normalize(path.posix.join(path.posix.dirname(relative), href))
    if (resolved.startsWith('../')) throw new Error(`Link escapes course: ${relative}: ${href}`)
    return `href="${resolved.endsWith('.html') ? routeFor(resolved) : '/study-content/files/' + resolved}"`
  })
  markup = markup.replace('<nav aria-label="مسیر صفحه">', '<nav aria-label="مسیر صفحه"><a href="/">EdiCodes</a><span aria-hidden="true">/</span>')
  const page = { title: html.match(/<title>(.*?)<\/title>/)[1], source: relative, html: markup, module: null }
  if (script) {
    const module = `export function mount(document,window,localStorage,confirm){\n${script}\n}\n`
    const filename = `runtime.${hash(module)}.js`
    write(path.join(output, 'assets', filename), module)
    page.module = `/study-content/assets/${filename}`
  }
  const filename = `page.${hash(JSON.stringify(page))}.json`
  write(path.join(output, 'pages', filename), JSON.stringify(page))
  manifest.pages[routeFor(relative)] = `/study-content/pages/${filename}`
}
manifest.revision = hash(JSON.stringify(manifest))
write(path.join(output, 'manifest.json'), JSON.stringify(manifest, null, 2) + '\n')
console.log(`Prepared ${Object.keys(manifest.pages).length} native Vue study pages (${manifest.revision})`)
