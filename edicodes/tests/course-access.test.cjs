const { test } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');

const component = fs.readFileSync(path.join(__dirname, '../src/views/CourseDetail.vue'), 'utf8');
const handler = component.slice(component.indexOf('const viewContent ='), component.indexOf('// Lifecycle'))
  .replaceAll('import.meta.env.VITE_API_BASE_URL', "'https://example.test/api'");

for (const scenario of [
  { name: 'guests can open free lessons', free: true, token: null, enrolled: false, allowed: true },
  { name: 'guests are prompted to log in for paid lessons', free: false, token: null, enrolled: false, allowed: false },
  { name: 'unenrolled users cannot open paid lessons', free: false, token: 'user', enrolled: false, allowed: false },
  { name: 'enrolled users can open paid lessons', free: false, token: 'user', enrolled: true, allowed: true },
]) {
  test(scenario.name, async () => {
    const shown = [], alerts = [];
    const context = {
      course: { value: { id: 2, slug: 'python-programming' } },
      localStorage: { getItem: key => key === 'user_token' ? scenario.token : null },
      isEnrolled: { value: scenario.enrolled }, contentLoading: { value: false },
      router: { push: location => shown.push(location) }, alert: text => alerts.push(text), console,
    };
    vm.runInNewContext(handler + '\nglobalThis.openLesson = viewContent;', context);
    const lesson = { title: 'Python', order: 1, is_free: scenario.free, content: 'print("hello")' };
    await context.openLesson(lesson);
    assert.equal(shown.length, scenario.allowed ? 1 : 0);
    if (scenario.allowed) {
      assert.equal(shown[0].name, 'course-lesson');
      assert.equal(shown[0].params.order, 1);
    }
    assert.equal(alerts.length, scenario.allowed ? 0 : 1);
    assert.equal(context.contentLoading.value, false);
  });
}
