# Study section — Vue frontend, Laravel backend

The native Vue route `/study` hosts the Persian ML study curriculum. Lesson and schedule URLs, such as `/study/day-01`, `/study/intensive/day-01`, and `/study/chapters/chapter-02`, use the existing Vue Router history fallback. Navbar links are available on desktop and mobile. The study layout supplies its own navigation and a link back to EdiCodes; the normal site navbar, footer and Telegram banner are hidden only on study routes.

The Laravel backend, production database and admin authentication are unchanged. Course contents are static, repository-owned educational material. No extra API, migrations, account or database table is required. Study progress remains browser-local, using the existing `discrete-fa-v1-day-*` keys. Progress in a local `file:` page cannot automatically move to the website's different origin.

## Source and build

`study/scripts/` contains the editable course generator copied from the original Desktop course. Update the repository copy for subsequent website changes. `study/assets/fonts/` contains the local fonts. The original Desktop course remains independent.

From `edicodes/`:

```text
node scripts/build-study.cjs
npm run test:study
npm run build
```

The study build first regenerates the standalone source course, then exports a 45-page manifest and payloads into `edicodes/public/study-content/`. Vite copies that folder into the production build. The build script shares the stylesheet and three fonts across all pages, rather than repeating embedded fonts in every downloaded page. Generated payload names contain content hashes. `manifest.json` is fetched with revalidation to pick up a new release.

The Vue component scopes course CSS to `.study-root`, mounts build-generated HTML, and imports same-origin ES modules for the existing interactions. It does not use an iframe, `eval`, or execute scripts from user-supplied HTML. Navigation uses Vue Router; downloads stay normal file links. Route changes abort pending requests, reject stale results and remove registered window listeners.

`npm run build` builds study data before the existing sitemap/Vite steps. A deployment command that invokes Vite directly must first run `node scripts/build-study.cjs`. Deploy the entire generated `edicodes/dist/`, including `study-content/` and the new JS/CSS assets. Publish the new root index only after its referenced assets are available. Preserve the Laravel application, environment files, uploads, storage and existing server configuration.

## Validation and release

The source course has 13 focused tests. Website-specific tests cover the 45-page manifest, internal routes, hash links, files, imported lesson runtimes, local fonts and menu links. A successful build or push does not by itself confirm production deployment. Check `/study`, a lesson, the manifest, stylesheet and fonts on `https://edrisranjbar.ir` after release.

Local production-preview validation on 2026-10-19: all 17 automated tests passed; the Vue production build and focused lint checks passed. In the actual browser, `/study` and a lesson rendered correctly, group membership updated the intersection, incorrect/correct quiz feedback worked, progress reached 100%, and the note and progress survived reload. Section navigation preserved the current lab state, and the dashboard reflected the completed lesson. Runtime-generated continuation links are normalized for normal clicks and opening in another tab.
