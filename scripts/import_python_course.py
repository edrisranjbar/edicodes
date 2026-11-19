"""Import the local PHP course through the authenticated EdiCodes API."""
import argparse
import json
import re
import subprocess
import urllib.error
import urllib.request
from html.parser import HTMLParser
from html import escape
from pathlib import Path


class LessonText(HTMLParser):
    """Extract readable main content, retaining code whitespace and real media links."""
    blocks = {'p', 'div', 'section', 'h1', 'h2', 'h3', 'h4', 'li', 'tr', 'pre', 'br'}

    def __init__(self):
        super().__init__(convert_charrefs=True)
        self.stack = []
        self.parts = []
        self.pre = 0

    def handle_starttag(self, tag, attrs):
        attrs = dict(attrs)
        parent = self.stack[-1][1] if self.stack else False
        skip = parent or tag in {'script', 'style', 'button', 'svg', 'nav'} or any(
            c in attrs.get('class', '').split()
            for c in ['lesson-nav-btns', 'video-tabs', 'breadcrumb']
        )
        if tag not in {'br', 'hr', 'img', 'input', 'meta', 'link', 'source', 'wbr'}:
            self.stack.append((tag, skip))
        if skip or not any(t == 'main' for t, _ in self.stack):
            return
        if tag in self.blocks:
            self.parts.append('\n')
        if tag == 'pre':
            self.pre += 1
        if tag in {'a', 'iframe'}:
            url = attrs.get('href' if tag == 'a' else 'src', '')
            if url.startswith('https://') and not re.search(r'VIDEO_ID|APARAT_HASH|YOUR_|PLACEHOLDER', url, re.I):
                self.parts.append('\n' + url + '\n')

    def handle_endtag(self, tag):
        if self.stack and not self.stack[-1][1] and any(t == 'main' for t, _ in self.stack):
            if tag in self.blocks:
                self.parts.append('\n')
            if tag == 'pre':
                self.pre = max(0, self.pre - 1)
        for i in range(len(self.stack) - 1, -1, -1):
            if self.stack[i][0] == tag:
                del self.stack[i:]
                break

    def handle_data(self, data):
        if self.stack and not self.stack[-1][1] and any(t == 'main' for t, _ in self.stack):
            self.parts.append(data if self.pre else re.sub(r'\s+', ' ', data))

    def text(self):
        return re.sub(r'\n[ \t]*\n(?:[ \t]*\n)+', '\n\n', ''.join(self.parts)).strip()


class LessonHtml(HTMLParser):
    """Keep lesson markup and interaction data, without executing imported scripts."""
    void = {'br', 'hr', 'img', 'input', 'meta', 'link', 'source', 'wbr', 'area', 'embed'}

    def __init__(self):
        super().__init__(convert_charrefs=True)
        self.stack = []
        self.parts = []
        self.pre = 0

    def handle_starttag(self, tag, attrs):
        attrs = dict(attrs)
        active = tag == 'main' or any(t == 'main' for t, _, _ in self.stack)
        skip = (self.stack[-1][1] if self.stack else False) or tag in {'script', 'style', 'svg', 'nav'} or any(
            c in attrs.get('class', '').split() for c in ['lesson-nav-btns', 'breadcrumb']
        )
        url = attrs.get('href', attrs.get('src', ''))
        if re.search(r'VIDEO_ID|APARAT_HASH|YOUR_|PLACEHOLDER', url, re.I):
            skip = True
        emit = active and not skip and tag != 'main'
        if tag not in self.void:
            self.stack.append((tag, skip, emit))
        if not emit:
            return
        kept = {}
        for key, value in attrs.items():
            if key in {'class', 'id', 'dir', 'title', 'href', 'src', 'target', 'rel', 'type', 'name', 'value', 'allowfullscreen', 'frameborder', 'checked', 'open'} or key.startswith('data-'):
                if key in {'href', 'src'}:
                    if value.startswith(('javascript:', 'data:', 'http:')):
                        continue
                    if re.fullmatch(r'\d+\.php', value):
                        value = '/courses/python-programming/lessons/' + value[:-4]
                kept[key] = value
        if 'copyCode' in attrs.get('onclick', ''):
            kept['data-action'] = 'copy'
        self.parts.append('<' + tag + ''.join(' ' + k + ('' if v is None else '="' + escape(v, quote=True) + '"') for k, v in kept.items()) + '>')
        if tag == 'pre':
            self.pre += 1

    def handle_endtag(self, tag):
        for i in range(len(self.stack) - 1, -1, -1):
            if self.stack[i][0] == tag:
                _, _, emit = self.stack[i]
                if emit:
                    self.parts.append('</' + tag + '>')
                    if tag == 'pre':
                        self.pre = max(0, self.pre - 1)
                del self.stack[i:]
                break

    def handle_data(self, data):
        if self.stack and not self.stack[-1][1] and any(t == 'main' for t, _, _ in self.stack):
            self.parts.append(escape(data if self.pre else re.sub(r'\s+', ' ', data)))

    def html(self):
        html = ''.join(self.parts).strip()
        html = re.sub(r'<section\b[^>]*\bid="videos"[^>]*>.*?</section>',
                      lambda match: match[0] if '<iframe' in match[0] else '', html, flags=re.S)
        return '<div class="lesson-main">' + html + '</div>'


def build_payload(source):
    lessons = []
    for number in range(1, 38):
        path = source / f'{number}.php'
        raw = path.read_text(encoding='utf-8-sig')
        title = re.search(r'\$description\s*=\s*([\'"])(.*?)\1\s*;', raw)
        if not title:
            raise ValueError(f'Missing lesson title: {path}')
        result = subprocess.run(['php', '-d', 'display_errors=stderr', str(path)],
                                cwd=source, capture_output=True, check=True)
        if result.stderr.strip():
            raise ValueError(f'PHP warnings in lesson {number}: {result.stderr.decode("utf-8")}')
        parser = LessonHtml()
        parser.feed(result.stdout.decode('utf-8'))
        text = parser.html()
        if len(text) < 200 or len(text) > 1000000:
            raise ValueError(f'Unexpected lesson size: {number} ({len(text.encode("utf-8"))} bytes)')
        lessons.append({'title': f'{number}. {title[2]}', 'content': text,
                        'content_format': 'html', 'order': number, 'is_free': True})
    return {'course': {
        'title': 'آموزش رایگان پایتون از صفر تا پروژه', 'slug': 'python-programming',
        'description': 'دوره رایگان و فارسی پایتون با ادریس رنجبر در ۳۷ درس، از نصب و مبانی برنامه‌نویسی تا کار با داده، ساخت وب‌اپلیکیشن و ربات تلگرام. شامل متن آموزشی، مثال، تمرین و لینک ویدیوهای موجود.',
        'short_description': '۳۷ درس رایگان فارسی پایتون با مثال، تمرین و پروژه؛ مدرس: ادریس رنجبر.',
        'price': 0, 'status': 'draft', 'level': 'beginner', 'featured': False,
    }, 'lessons': lessons}


class Api:
    def __init__(self, base, token):
        self.base = base.rstrip('/')
        self.token = token

    def call(self, method, path, data=None):
        request = urllib.request.Request(self.base + path, method=method,
            headers={'Authorization': 'Bearer ' + self.token, 'Accept': 'application/json',
                     'Content-Type': 'application/json'},
            data=None if data is None else json.dumps(data, ensure_ascii=False).encode())
        try:
            with urllib.request.urlopen(request, timeout=60) as response:
                return json.load(response)
        except urllib.error.HTTPError as error:
            raise RuntimeError(f'{method} {path}: HTTP {error.code}: {error.read().decode()[:1000]}') from None


def import_course(api, payload, publish, update_existing=False, backup_path=None):
    profile = api.call('GET', '/admin/profile')['admin']
    courses, page = [], 1
    while True:
        batch = api.call('GET', f'/admin/courses?page={page}')['data']
        courses.extend(batch['data'])
        if page >= batch['last_page']:
            break
        page += 1
    matches = [c for c in courses if c['slug'] == payload['course']['slug']]
    if len(matches) > 1:
        raise ValueError('Multiple matching courses; refusing an ambiguous import')
    if matches:
        course = matches[0]
    else:
        course = api.call('POST', '/admin/courses', {**payload['course'], 'admin_id': profile['id']})['data']
        if course['slug'] != payload['course']['slug']:
            raise RuntimeError('Server did not accept the requested slug. Deploy the API change first.')
    course_id = course['id']
    path = f'/admin/courses/{course_id}/contents'
    existing = api.call('GET', path)['data']
    if update_existing:
        if not backup_path:
            raise ValueError('Updating existing lessons requires a backup path')
        backup_path = Path(backup_path)
        if backup_path.exists():
            raise ValueError('Backup file already exists; choose a new path to preserve the original')
        backup_path.write_text(json.dumps({'course': course, 'lessons': existing}, ensure_ascii=False, indent=2), encoding='utf-8')
    for lesson in payload['lessons']:
        matches = [c for c in existing if c['order'] == lesson['order']]
        if len(matches) > 1 or (matches and matches[0]['title'] != lesson['title']):
            raise ValueError(f'Conflicting lesson at order {lesson["order"]}; refusing to overwrite')
        if matches:
            if any(matches[0].get(k) != v for k, v in lesson.items()):
                if not update_existing:
                    raise ValueError(f'Existing lesson {lesson["order"]} differs; review before updating')
                api.call('PUT', path + '/' + str(matches[0]['id']), lesson)
        else:
            api.call('POST', path, lesson)
        print(f'Verified lesson {lesson["order"]}/37')
    actual = api.call('GET', path)['data']
    expected = sorted(payload['lessons'], key=lambda x: x['order'])
    actual = sorted([{k: c.get(k) for k in expected[0]} for c in actual], key=lambda x: x['order'])
    if actual != expected:
        raise RuntimeError('Course readback differs from source; course was not published')
    if publish:
        api.call('PUT', f'/admin/courses/{course_id}', {'status': 'published'})
        public = api.call('GET', f'/courses/slug/{payload["course"]["slug"]}')['data']['course']
        if public['status'] != 'published' or len(public['contents']) != 37:
            raise RuntimeError('Public course verification failed')
    print(f'Course {course_id}: 37 lessons verified; ' + ('published' if publish else 'publication not requested'))


def main():
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument('--source', type=Path, required=True)
    parser.add_argument('--output', type=Path, required=True, help='Reviewable JSON payload (no credentials)')
    parser.add_argument('--api-base', default='https://api.edrisranjbar.ir/api')
    parser.add_argument('--token-file', type=Path)
    parser.add_argument('--apply', action='store_true')
    parser.add_argument('--publish', action='store_true')
    parser.add_argument('--update-existing', action='store_true', help='Replace matching lesson bodies, keeping IDs and enrollment')
    parser.add_argument('--backup', type=Path, help='New JSON backup file required when replacing existing lessons')
    args = parser.parse_args()
    payload = build_payload(args.source.resolve())
    args.output.parent.mkdir(parents=True, exist_ok=True)
    args.output.write_text(json.dumps(payload, ensure_ascii=False, indent=2), encoding='utf-8')
    print(f'Prepared {len(payload["lessons"])} lessons: {args.output}')
    if args.apply:
        if not args.token_file:
            parser.error('--apply requires --token-file')
        if not args.api_base.startswith('https://'):
            parser.error('Authenticated imports require HTTPS')
        token = args.token_file.read_text(encoding='utf-8-sig').strip().removeprefix('Bearer ')
        import_course(Api(args.api_base, token), payload, args.publish, args.update_existing, args.backup)


if __name__ == '__main__':
    main()
