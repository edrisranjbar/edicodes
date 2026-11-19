# Python course API import

The Laravel API accepts text-only lessons at `POST /api/admin/courses/{id}/contents`.
Send `title`, `content`, `order`, and `is_free` as JSON with an active admin's
Bearer token. Existing multipart video uploads still work. A lesson needs either
nonempty text or a video. Course creation also accepts a unique ASCII `slug`.

After pulling the backend update on the server, no migrations are needed.
Deploy/rebuild the Vue frontend as usual to get safe plain-text lesson display
and clickable HTTPS video/reference links. Lesson text is intentionally plain
text, preserving code indentation; interactive quizzes remain on the original
course site.

Prepare a reviewable payload locally (requires Python 3.10+ and PHP on PATH):

```powershell
python scripts/import_python_course.py --source C:/Users/edi/Desktop/python-course --output C:/Users/edi/Desktop/python-course-import.json
```

Import through the deployed API and publish after all 37 lessons are verified:

```powershell
python scripts/import_python_course.py --source C:/Users/edi/Desktop/python-course --output C:/Users/edi/Desktop/python-course-import.json --token-file C:/Users/edi/Desktop/edrisranjbar_apikey.txt --apply --publish
```

The token stays in its local file and is never written to the payload. An import
resumes by course slug and lesson order, skips exact matches, and refuses
conflicting existing lessons. On interruption, rerun the same command. New courses
stay in draft until every lesson matches the API readback. Placeholder video URLs
are excluded; existing videos are linked rather than downloaded or reuploaded.

Verification:

```powershell
python -m unittest discover -s scripts -p "test_*.py"
node --test edicodes/tests/course-service.test.cjs
cd backend
php artisan test --filter=CourseImportApiTest
```
