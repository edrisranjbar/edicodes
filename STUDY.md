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

The study build first regenerates the standalone source course, then exports a 53-page manifest and payloads into `edicodes/public/study-content/`. Vite copies that folder into the production build. The build script shares the stylesheet and three fonts across all pages, rather than repeating embedded fonts in every downloaded page. Generated payload names contain content hashes. `manifest.json` is fetched with revalidation to pick up a new release.

The Vue component scopes course CSS to `.study-root`, mounts build-generated HTML, and imports same-origin ES modules for the existing interactions. It does not use an iframe, `eval`, or execute scripts from user-supplied HTML. Navigation uses Vue Router; downloads stay normal file links. Route changes abort pending requests, reject stale results and remove registered window listeners.

`npm run build` builds study data before the existing sitemap/Vite steps. A deployment command that invokes Vite directly must first run `node scripts/build-study.cjs`. Deploy the entire generated `edicodes/dist/`, including `study-content/` and the new JS/CSS assets. Publish the new root index only after its referenced assets are available. Preserve the Laravel application, environment files, uploads, storage and existing server configuration.

## Validation and release

The source course has 13 focused tests. Website-specific tests cover the 53-page manifest, internal routes, hash links, files, imported lesson runtimes, local fonts and menu links. A successful build or push does not by itself confirm production deployment. Check `/study`, a lesson, the manifest, stylesheet and fonts on `https://edrisranjbar.ir` after release.

Local production-preview validation on 2026-10-19: all 17 automated tests passed; the Vue production build and focused lint checks passed. In the actual browser, `/study` and a lesson rendered correctly, group membership updated the intersection, incorrect/correct quiz feedback worked, progress reached 100%, and the note and progress survived reload. Section navigation preserved the current lab state, and the dashboard reflected the completed lesson. Runtime-generated continuation links are normalized for normal clicks and opening in another tab.


## Book chapters 1–7 (2026-11-06)

`/study/book` starts the next phase after prerequisites. Seven original Persian lessons cover chapters 1–7 of the supplied 2019 second-edition PDF (14 chapters in this local release). Coverage is 50% by chapter count; personal completion is displayed separately. Editable sources live in `study/book/`; generated chapter HTML/resources are ignored and rebuilt during the study export. The PDF and extracted book text are not published.

The lessons contain 58 teaching sections, 42 feedback questions, seven live numerical labs, seven independent Python examples/notebooks, optional Scikit-Learn scripts, exercises with solutions, local notes/downloads and chapter navigation. New progress keys are `edicodes-homl2-v1-chapter-01` through `07`; prerequisite keys are preserved. Scientific/source review and its limits are documented in `study/book/REVIEW.md`.

Validation: all 21 Node tests passed, all seven independent examples and seven notebooks executed, all seven optional library scripts parsed, all 45 contrast pairs passed, focused lint and the Vite production build passed. Scikit-Learn installation failed due to TLS/network errors; optional library scripts were not executed. Browser checks confirmed threshold zero-denominator handling, incorrect/correct feedback, 100% completion and note persistence after reload, hard/soft voting disagreement, local font styles and working native chapter links. Narrow viewport DOM checks found no document-width overflow; the browser viewport screenshot tool was inconsistent during mobile inspection, so a complete mobile visual sign-off is not claimed.

Release uses the user-authorized push to `origin/master`. This repository has no configured deployment workflow; production availability must be verified separately from Git transport.
