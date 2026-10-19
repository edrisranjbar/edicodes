export function normalizeStudyPath(path) {
  return path.replace(/\/index\.html$/, '').replace(/\/+$/, '') || '/study'
}

export function resolveStudyLink(href, source, origin) {
  if (!href || /^(?:mailto:|tel:|javascript:|data:)/i.test(href)) return null
  const url = new URL(href, `${origin}/study/${source}`)
  if (url.origin !== origin) return null
  if (url.pathname === '/') return url.pathname + url.search + url.hash
  if (url.pathname === '/study' || url.pathname.startsWith('/study/')) return normalizeStudyPath(url.pathname) + url.search + url.hash
  return null
}
