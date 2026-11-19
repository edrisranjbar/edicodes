export const courseProgressKey = courseId => `edicodes:course:${courseId}:completed:v1`;

export function readCourseProgress(storage, courseId) {
  try {
    const value = JSON.parse(storage.getItem(courseProgressKey(courseId)) || '[]');
    return Array.isArray(value) ? [...new Set(value.filter(id => typeof id === 'string' || Number.isSafeInteger(id)).map(String))] : [];
  } catch { return []; }
}

export function writeCourseProgress(storage, courseId, completed) {
  try {
    storage.setItem(courseProgressKey(courseId), JSON.stringify([...new Set(completed.map(String))]));
    return true;
  } catch { return false; }
}

export function summarizeCourseProgress(lessons, completed) {
  const ids = new Set(completed.map(String));
  const count = lessons.filter(lesson => ids.has(String(lesson.id))).length;
  return { count, remaining: lessons.length - count, percent: lessons.length ? Math.round(count / lessons.length * 100) : 0 };
}
