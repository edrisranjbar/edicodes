const { test } = require('node:test');
const assert = require('node:assert/strict');
const progress = import('../src/utils/courseProgress.js');

const memory = () => {
  const values = new Map();
  return { getItem: key => values.get(key) ?? null, setItem: (key, value) => values.set(key, value) };
};

test('completion persists across reloads and is isolated per course', async () => {
  const { readCourseProgress, writeCourseProgress } = await progress;
  const storage = memory();
  assert.equal(writeCourseProgress(storage, 2, [11, 12, 12]), true);
  assert.deepEqual(readCourseProgress(storage, 2), ['11', '12']);
  assert.deepEqual(readCourseProgress(storage, 3), []);
  writeCourseProgress(storage, 2, [12]);
  assert.deepEqual(readCourseProgress(storage, 2), ['12']);
});

test('percentage counts only current lesson IDs and reports remaining lessons', async () => {
  const { summarizeCourseProgress } = await progress;
  const lessons = Array.from({ length: 37 }, (_, i) => ({ id: i + 1 }));
  assert.deepEqual(summarizeCourseProgress(lessons, ['1', '1', '99']), { count: 1, remaining: 36, percent: 3 });
  assert.deepEqual(summarizeCourseProgress(lessons, lessons.map(x => x.id)), { count: 37, remaining: 0, percent: 100 });
  assert.deepEqual(summarizeCourseProgress([], []), { count: 0, remaining: 0, percent: 0 });
});

test('corrupt or unavailable browser storage does not break the reader', async () => {
  const { readCourseProgress, writeCourseProgress } = await progress;
  for (const value of ['broken', '{}', 'null', '[null,{},"12",12]']) {
    const ids = readCourseProgress({ getItem: () => value }, 2);
    assert.deepEqual(ids, value.startsWith('[') ? ['12'] : []);
  }
  const blocked = { getItem() { throw new Error('blocked'); }, setItem() { throw new Error('blocked'); } };
  assert.deepEqual(readCourseProgress(blocked, 2), []);
  assert.equal(writeCourseProgress(blocked, 2, [1]), false);
});
