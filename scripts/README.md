# Python course API import

Lessons have dedicated pages at `/courses/:slug/lessons/:order`. HTML bodies retain
headings, paragraphs, highlighted code, tables, approved video embeds, quizzes,
exercise hints, and references. HTML is sanitized in the reader; imported scripts
and inline event handlers are never executed. Plain-text lessons remain supported.

## Deploy

Pull `master`, then run in the backend directory:

```bash
php artisan migrate --force
```

The migration expands lesson storage and adds `content_format` (`text` by default,
`html` for rich lessons). Install frontend dependencies and rebuild/deploy its
`dist` directory as usual:

```bash
cd edicodes
npm ci
npm run build
```

## Prepare and import

Python 3.10+ and PHP must be on PATH. Prepare a reviewable HTML payload locally:

```powershell
python scripts/import_python_course.py --source C:/Users/edi/Desktop/python-course --output C:/Users/edi/Desktop/python-course-html-import.json
```

Replace the previously imported plain-text bodies through the deployed API while
keeping lesson IDs, order, course identity and enrollment. The backup path must
not exist; it preserves all existing lesson records before any update:

```powershell
python scripts/import_python_course.py --source C:/Users/edi/Desktop/python-course --output C:/Users/edi/Desktop/python-course-html-import.json --token-file C:/Users/edi/Desktop/edrisranjbar_apikey.txt --apply --publish --update-existing --backup C:/Users/edi/Desktop/python-course-before-html.json
```

For a new course, omit `--update-existing` and `--backup`. Authenticated imports
require HTTPS. Tokens are read from the local file and never saved to payloads or
backups. Exact matches are skipped; duplicate titles/orders or conflicting lessons
are rejected unless replacement is explicitly requested. The final readback must
match all 37 lesson records before the importer reports success.

The reader preserves video tabs, code-copy buttons, quiz grading and native
expandable exercise hints. Placeholder video URLs are excluded. Styling is adapted
from the original course and scoped to the lesson article so it cannot affect
other site pages. To regenerate it after source CSS changes:

```powershell
node scripts/build-python-lesson-css.cjs C:/Users/edi/Desktop/python-course
```

## Verify

```powershell
python -m unittest discover -s scripts -p "test_*.py"
node --test edicodes/tests/course-access.test.cjs edicodes/tests/course-service.test.cjs
cd backend
php artisan test --filter=CourseImportApiTest
```
