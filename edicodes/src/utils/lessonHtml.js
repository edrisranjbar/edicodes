import DOMPurify from 'dompurify';

export function sanitizeLessonHtml(html) {
  const clean = DOMPurify.sanitize(html || '', {
    ADD_TAGS: ['iframe'],
    ADD_ATTR: ['allowfullscreen', 'frameborder'],
    FORBID_TAGS: ['style', 'script', 'form'],
    FORBID_ATTR: ['style', 'srcdoc'],
  });
  const doc = new DOMParser().parseFromString(clean, 'text/html');
  for (const frame of doc.querySelectorAll('iframe')) {
    let url;
    try { url = new URL(frame.getAttribute('src')); } catch { frame.remove(); continue; }
    const valid = url.protocol === 'https:' && (
      (['www.youtube.com', 'www.youtube-nocookie.com'].includes(url.hostname) && /^\/embed\/[\w-]+$/.test(url.pathname)) ||
      (url.hostname === 'www.aparat.com' && url.pathname.startsWith('/video/video/embed/videohash/'))
    );
    if (!valid || /VIDEO_ID|APARAT_HASH|PLACEHOLDER/.test(url.href)) { frame.remove(); continue; }
    frame.setAttribute('loading', 'lazy');
    frame.setAttribute('sandbox', 'allow-scripts allow-same-origin allow-presentation');
    frame.setAttribute('referrerpolicy', 'strict-origin-when-cross-origin');
    frame.setAttribute('title', frame.getAttribute('title') || 'ویدیوی آموزشی');
  }
  for (const link of doc.querySelectorAll('a[target="_blank"]')) {
    link.setAttribute('rel', 'noopener noreferrer');
  }
  return doc.body.innerHTML;
}

export async function handleLessonAction(event, root) {
  const target = event.target.closest('button, a');
  if (!target || !root.contains(target)) return;
  if (target.matches('.video-tab')) {
    for (const tab of root.querySelectorAll('.video-tab')) tab.classList.toggle('active', tab === target);
    for (const panel of root.querySelectorAll('.video-player-wrap')) panel.classList.toggle('active', panel.id === target.dataset.target);
  }
  if (target.matches('.code-copy-btn, [data-action="copy"]')) {
    const code = target.closest('.code-block')?.querySelector('pre');
    if (!code) return;
    try {
      await navigator.clipboard.writeText(code.textContent);
      target.textContent = 'کپی شد';
    } catch { target.textContent = 'کپی انجام نشد'; }
  }
  if (target.matches('.quiz-check-btn')) {
    const section = target.closest('.quiz-section');
    const cards = [...section.querySelectorAll('.quiz-card')];
    const complete = cards.every(card => card.querySelector('input:checked'));
    const result = section.querySelector('.quiz-result');
    if (!result) return;
    result.classList.add('visible');
    result.setAttribute('role', 'status');
    if (!complete) {
      result.querySelector('.quiz-result-msg').textContent = 'لطفاً ابتدا به همه سؤال‌ها پاسخ بده.';
      return;
    }
    let score = 0;
    for (const card of cards) {
      for (const option of card.querySelectorAll('.quiz-option')) {
        const checked = option.querySelector('input')?.checked;
        const correct = option.dataset.correct === '1';
        option.classList.toggle('correct', correct);
        option.classList.toggle('incorrect', checked && !correct);
        if (correct && checked) score++;
      }
    }
    result.querySelector('.quiz-result-score').textContent = `${score.toLocaleString('fa-IR')} از ${cards.length.toLocaleString('fa-IR')}`;
    result.querySelector('.quiz-result-fill').style.width = `${100 * score / cards.length}%`;
    result.querySelector('.quiz-result-msg').textContent = score === cards.length ? 'عالی بود! حالا تمرین‌ها را انجام بده.' : 'پاسخ‌های سبز را مرور کن و دوباره تلاش کن.';
  }
}
