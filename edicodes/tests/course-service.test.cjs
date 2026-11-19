const { test } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');

test('public course requests work without an admin token; admin requests remain authenticated', async () => {
  const calls = [];
  const axios = {
    create(options) {
      return {
        interceptors: { response: { use() {} } },
        get(path) { calls.push({ path, options }); },
      };
    },
    get(path, options) { calls.push({ path, options }); },
  };
  let source = fs.readFileSync(new URL('../src/services/courseService.js', `file://${__filename.replaceAll('\\', '/')}`), 'utf8');
  source = source.replace("import axios from 'axios';", '')
    .replaceAll('import.meta.env.VITE_API_BASE_URL', "'https://example.test/api'")
    .replace('export const testFileUpload', 'const testFileUpload')
    .replace('export default courseService;', 'globalThis.service = courseService;');
  const context = { axios, localStorage: { getItem: () => 'admin-secret' }, console };
  vm.runInNewContext(source, context);
  await context.service.getPublicCourses();
  await context.service.getPublicCourseContents(12);
  await context.service.getPublicCourse('python-programming');
  await context.service.getCourses(2);
  await context.service.getCourseContents(12);
  assert.deepEqual(calls.map(c => c.path), [
    '/courses', '/courses/12/contents', 'https://example.test/api/courses/slug/python-programming',
    '/admin/courses?page=2', '/admin/courses/12/contents',
  ]);
  for (const call of calls.slice(0, 3)) assert.equal(call.options.headers.Authorization, undefined);
  for (const call of calls.slice(3)) assert.equal(call.options.headers.Authorization, 'Bearer admin-secret');
});
