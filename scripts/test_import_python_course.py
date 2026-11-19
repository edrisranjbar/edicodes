import contextlib
import io
import unittest
import tempfile
from pathlib import Path

from import_python_course import LessonText, LessonHtml, import_course


class FakeApi:
    def __init__(self):
        self.course = None
        self.lessons = []
        self.published = False

    def call(self, method, path, data=None):
        if path == '/admin/profile':
            return {'admin': {'id': 1}}
        if path.startswith('/admin/courses?page='):
            return {'data': {'data': [self.course] if self.course else [], 'last_page': 1}}
        if path == '/admin/courses' and method == 'POST':
            self.course = {**data, 'id': 7}
            return {'data': self.course}
        if path.endswith('/contents'):
            if method == 'POST':
                self.lessons.append(dict(data))
            return {'data': self.lessons}
        if '/contents/' in path and method == 'PUT':
            lesson_id = int(path.rsplit('/', 1)[1])
            next(item for item in self.lessons if item['id'] == lesson_id).update(data)
            return {'data': data}
        if method == 'PUT':
            self.published = True
            self.course.update(data)
            return {'data': self.course}
        if path.startswith('/courses/slug/'):
            return {'data': {'course': {**self.course, 'contents': self.lessons}}}
        raise AssertionError((method, path))


class ImportTests(unittest.TestCase):
    def payload(self):
        return {'course': {'slug': 'python-programming', 'status': 'draft'},
                'lessons': [{'title': f'Lesson {n}', 'order': n, 'content': 'text', 'is_free': True}
                            for n in range(1, 38)]}

    def test_resume_is_idempotent_and_publication_follows_readback(self):
        api = FakeApi()
        with contextlib.redirect_stdout(io.StringIO()):
            import_course(api, self.payload(), False)
            self.assertFalse(api.published)
            api.lessons = api.lessons[:12]  # Simulate a partial prior import.
            import_course(api, self.payload(), True)
            import_course(api, self.payload(), True)
        self.assertTrue(api.published)
        self.assertEqual(len(api.lessons), 37)

    def test_conflicting_lesson_prevents_publication(self):
        api = FakeApi()
        api.course = {'id': 7, 'slug': 'python-programming'}
        api.lessons = [{'title': 'Another lesson', 'order': 1}]
        with self.assertRaises(ValueError):
            import_course(api, self.payload(), True)
        self.assertFalse(api.published)

    def test_html_replacement_backs_up_originals_and_keeps_lesson_ids(self):
        api = FakeApi()
        api.course = {'id': 7, 'slug': 'python-programming'}
        api.lessons = [dict(item, id=item['order'] + 100, content='old text') for item in self.payload()['lessons']]
        with tempfile.TemporaryDirectory() as directory, contextlib.redirect_stdout(io.StringIO()):
            backup = Path(directory) / 'before.json'
            import_course(api, self.payload(), True, True, backup)
            self.assertIn('old text', backup.read_text())
            self.assertTrue(api.published)
            self.assertEqual(api.lessons[0]['id'], 101)
            self.assertEqual(api.lessons[0]['content'], 'text')
            with self.assertRaises(ValueError):
                import_course(api, self.payload(), True, True, backup)

    def test_text_extraction_preserves_code_and_omits_placeholder_media(self):
        parser = LessonText()
        parser.feed('<nav>outside</nav><main><h1>Title</h1><pre>if x:\n    print(&quot;&lt;x&gt;&quot;)</pre>'
                    '<button>Copy</button><script>bad()</script>'
                    '<iframe src="https://youtube.com/embed/VIDEO_ID"></iframe>'
                    '<iframe src="https://youtube.com/embed/realvideo"></iframe></main>')
        text = parser.text()
        self.assertIn('if x:\n    print("<x>")', text)
        self.assertIn('https://youtube.com/embed/realvideo', text)
        for unwanted in ['outside', 'Copy', 'bad()', 'VIDEO_ID']:
            self.assertNotIn(unwanted, text)

    def test_html_extraction_keeps_structure_and_quiz_data_without_scripts(self):
        parser = LessonHtml()
        parser.feed('<nav>outside</nav><main><h1>Python</h1><p style="color: red"><strong>Learn</strong></p>'
                    '<pre><code>if x:\n    print(&quot;&lt;x&gt;&quot;)</code></pre>'
                    '<label data-correct="1"><input type="radio" name="q1">Yes</label>'
                    '<button onclick="copyCode(this)">Copy</button><script>bad()</script>'
                    '<iframe src="https://www.youtube.com/embed/VIDEO_ID"></iframe></main>')
        html = parser.html()
        self.assertIn('<strong>Learn</strong>', html)
        self.assertIn('data-lesson-style="color: red"', html)
        self.assertIn('    print(&quot;&lt;x&gt;&quot;)', html)
        self.assertIn('data-correct="1"', html)
        self.assertIn('data-action="copy"', html)
        for unwanted in ['onclick', 'bad()', '<script', 'VIDEO_ID', 'outside']:
            self.assertNotIn(unwanted, html)


if __name__ == '__main__':
    unittest.main()
