import test from 'node:test'
import assert from 'node:assert/strict'
import fs from 'node:fs'
import path from 'node:path'
import { fileURLToPath, pathToFileURL } from 'node:url'
import { normalizeStudyPath, resolveStudyLink } from '../src/utils/studyNavigation.js'
const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..')
const output = path.join(root, 'public/study-content')
const manifest = JSON.parse(fs.readFileSync(path.join(output, 'manifest.json'), 'utf8'))
test('native Vue study bundle has every lesson, chapter and intensive-day page', () => {
  assert.equal(Object.keys(manifest.pages).length, 45)
  for (let d = 1; d <= 28; d++) assert.ok(manifest.pages[`/study/intensive/day-${String(d).padStart(2, '0')}`])
  assert.ok(manifest.pages['/study/day-01'])
  assert.ok(manifest.pages['/study/chapters/chapter-10'])
})
test('website navigation preserves local lesson links, hashes, downloads and external boundaries', () => {
  assert.equal(normalizeStudyPath('/study/index.html'), '/study')
  assert.equal(normalizeStudyPath('/study/day-01/'), '/study/day-01')
  assert.equal(resolveStudyLink('intensive/day-02/index.html', 'index.html', 'https://edrisranjbar.ir'), '/study/intensive/day-02')
  assert.equal(resolveStudyLink('#quiz-section', 'day-01/index.html', 'https://edrisranjbar.ir'), '/study/day-01#quiz-section')
  assert.equal(resolveStudyLink('../day-02/index.html', 'day-01/index.html', 'https://edrisranjbar.ir'), '/study/day-02')
  assert.equal(resolveStudyLink('/study-content/files/example.py', 'index.html', 'https://edrisranjbar.ir'), null)
  assert.equal(resolveStudyLink('https://example.com/', 'index.html', 'https://edrisranjbar.ir'), null)
  assert.equal(resolveStudyLink('/', 'index.html', 'https://edrisranjbar.ir'), '/')
})
test('every exported page link and runtime resolves to the same-origin payload', async () => {
  for (const [route, url] of Object.entries(manifest.pages)) {
    const page = JSON.parse(fs.readFileSync(path.join(root, 'public', url), 'utf8'))
    assert.ok(!/<script|<style/.test(page.html), route)
    for (const [, href] of page.html.matchAll(/href="([^\"]+)"/g)) {
      if (href === '/' || href.startsWith('#')) continue
      if (href.startsWith('/study-content/')) assert.ok(fs.existsSync(path.join(root, 'public', href)), href)
      else assert.ok(manifest.pages[normalizeStudyPath(href)], `${route}: ${href}`)
    }
    if (page.module) {
      const imported = await import(pathToFileURL(path.join(root, 'public', page.module)))
      assert.equal(typeof imported.mount, 'function')
    }
  }
})
test('shared fonts are local and stylesheet cannot leak into other EdiCodes routes', () => {
  const css = fs.readFileSync(path.join(root, 'public', manifest.stylesheet), 'utf8')
  assert.ok(!css.includes('data:font/ttf;base64'))
  assert.ok(!/https?:/.test(css))
  assert.ok(css.includes('.study-root .chapter-sidebar'))
  assert.ok(css.includes('.study-root{'))
  for (const [, font] of css.matchAll(/url\("(fonts\/[^\"]+)"\)/g)) assert.ok(fs.existsSync(path.join(root, 'public', path.posix.dirname(manifest.stylesheet), font)))
  const navbar = fs.readFileSync(path.join(root, 'src/components/Navbar.vue'), 'utf8')
  assert.equal((navbar.match(/to="\/study"/g) || []).length, 2)
})
