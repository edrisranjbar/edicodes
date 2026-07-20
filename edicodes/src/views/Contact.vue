<template>
  <div class="min-h-screen bg-dark text-white font-vazir pt-24">
    <div v-if="copiedText" class="fixed bottom-6 left-1/2 z-[60] -translate-x-1/2 rounded-full border border-white/10 bg-black/80 px-4 py-2 text-sm text-white shadow-lg shadow-black/30 backdrop-blur-xl">
      {{ copiedText }}
    </div>

    <section class="px-4 md:px-8 py-16">
      <div class="container mx-auto max-w-7xl">
        <div class="text-center mb-12">
          <h1 class="text-3xl md:text-4xl lg:text-5xl font-black text-white mb-4">
            تماس با من
          </h1>
          <p class="text-white/70 text-lg max-w-2xl mx-auto leading-8">
            برای همکاری، مشاوره یا ساخت پروژه، مستقیم با من در تماس باشید.
          </p>
        </div>

        <div class="grid lg:grid-cols-[1.1fr_0.9fr] gap-8 items-stretch">
          <div class="relative overflow-hidden rounded-[32px] border border-white/10 bg-[#0f172a]/90 p-8 md:p-10 shadow-[0_30px_90px_rgba(15,23,42,0.5)] backdrop-blur-2xl">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(59,130,246,0.25),_transparent_50%)]" />
            <div class="relative">
              <p class="text-white/70 text-lg leading-8 max-w-2xl">
                از این مسیرها برای رسیدن سریع به من استفاده کنید؛ همیشه برای گفت‌وگوی حرفه‌ای، بررسی ایده‌های خلاقانه و آغاز پروژه‌های ارزشمند آماده‌ام. اگر به دنبال همکاری، مشاوره یا ساخت چیزی خاص هستید، خوشحال می‌شوم در اولین فرصت با شما صحبت کنم.
              </p>

              <div class="flex flex-col gap-4 mt-8">
                <div class="contact-card group relative overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] p-4 transition-all duration-300 hover:-translate-y-1 hover:border-primary/40 hover:bg-primary/10">
                  <a href="mailto:edrisranjbar.dev@gmail.com" class="flex items-center gap-3 text-white">
                    <div class="flex h-11 w-11 min-w-11 items-center justify-center rounded-xl bg-primary/15 text-primary">
                      <svg viewBox="0 0 24 24" class="h-5 w-5 fill-current" aria-hidden="true">
                        <path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Zm0 4.2-8 5.2-8-5.2V6l8 5.2L20 6v2.2Z"/>
                      </svg>
                    </div>
                    <div>
                      <p class="text-sm text-white/55">ایمیل</p>
                      <p class="font-semibold text-white">edrisranjbar.dev@gmail.com</p>
                    </div>
                  </a>
                  <button @click.prevent="copyToClipboard('edrisranjbar.dev@gmail.com', 'آدرس ایمیل کپی شد')" class="absolute left-4 top-4 inline-flex items-center rounded-full border border-white/10 bg-black/20 px-3 py-1 text-xs text-white/70 opacity-0 transition-all duration-300 group-hover:opacity-100 hover:text-white">
                    کپی
                  </button>
                </div>

                <div class="contact-card group relative overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] p-4 transition-all duration-300 hover:-translate-y-1 hover:border-emerald-400/40 hover:bg-emerald-500/10">
                  <a href="https://wa.me/989962933405" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 text-white">
                    <div class="flex h-11 w-11 min-w-11 items-center justify-center rounded-xl bg-emerald-500/15 text-emerald-400">
                      <svg viewBox="0 0 24 24" class="h-5 w-5 fill-current" aria-hidden="true">
                        <path d="M12.04 2.5A9.49 9.49 0 0 0 3.03 11.74c0 1.67.44 3.3 1.28 4.74L2.5 21.5l5.16-1.35a9.46 9.46 0 0 0 4.38 1.03h.01c5.24 0 9.49-4.25 9.49-9.49S17.28 2.5 12.04 2.5Zm0 17.3h-.01a7.82 7.82 0 0 1-3.97-1.1l-.28-.17-3.06.8.82-2.98-.18-.29A7.81 7.81 0 1 1 12.04 19.8Zm4.34-5.83c-.24-.12-1.4-.69-1.62-.77-.22-.08-.38-.12-.54.12-.16.24-.62.77-.76.92-.14.16-.28.18-.52.06-.24-.12-1.02-.38-1.95-1.2-.72-.64-1.2-1.43-1.34-1.67-.14-.24-.01-.37.11-.49.11-.11.24-.28.36-.42.12-.14.16-.24.24-.4.08-.16.04-.3-.02-.42-.06-.12-.54-1.3-.74-1.78-.19-.47-.39-.41-.54-.42-.14-.01-.3-.01-.46-.01-.16 0-.42.06-.64.3-.22.24-.86.84-.86 2.05 0 1.21.88 2.38.99 2.54.12.16 1.72 2.64 4.18 3.69.59.25 1.05.4 1.41.51.6.19 1.14.16 1.57.1.48-.07 1.4-.57 1.6-1.12.2-.55.2-1.02.14-1.12-.06-.1-.22-.16-.46-.28Z"/>
                      </svg>
                    </div>
                    <div>
                      <p class="text-sm text-white/55">واتساپ</p>
                      <p class="font-semibold text-white">+98 996 293 3405</p>
                    </div>
                  </a>
                  <button @click.prevent="copyToClipboard('+989962933405', 'شماره واتساپ کپی شد')" class="absolute left-4 top-4 inline-flex items-center rounded-full border border-white/10 bg-black/20 px-3 py-1 text-xs text-white/70 opacity-0 transition-all duration-300 group-hover:opacity-100 hover:text-white">
                    کپی
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div class="space-y-4">
            <div class="rounded-[28px] border border-white/10 bg-[#111827]/90 p-6 shadow-[0_20px_60px_rgba(0,0,0,0.35)] backdrop-blur-xl">
              <div class="flex items-center justify-between mb-5">
                <div>
                  <p class="text-sm text-white/55">شبکه‌های اجتماعی</p>
                  <h2 class="text-xl font-bold text-white">در جاهای مختلف</h2>
                </div>
              </div>

              <div class="space-y-3">
                <a href="https://www.linkedin.com/in/edrisranjbar" target="_blank" rel="noopener noreferrer" class="social-link group flex items-center justify-between rounded-2xl border border-white/10 bg-white/[0.03] px-4 py-4 transition-all duration-300 hover:-translate-y-1 hover:border-primary/30 hover:bg-primary/10">
                  <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary/15 text-primary">
                      <font-awesome-icon :icon="['fab', 'linkedin']" />
                    </div>
                    <span class="font-medium text-white">لینکدین</span>
                  </div>
                  <font-awesome-icon icon="arrow-left" class="text-white/45 transition-transform duration-300 group-hover:translate-x-1" />
                </a>

                <a href="https://t.me/edicodes_pv" target="_blank" rel="noopener noreferrer" class="social-link group flex items-center justify-between rounded-2xl border border-white/10 bg-white/[0.03] px-4 py-4 transition-all duration-300 hover:-translate-y-1 hover:border-sky-400/30 hover:bg-sky-500/10">
                  <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-500/15 text-sky-400">
                      <font-awesome-icon :icon="['fab', 'telegram']" />
                    </div>
                    <span class="font-medium text-white">تلگرام</span>
                  </div>
                  <font-awesome-icon icon="arrow-left" class="text-white/45 transition-transform duration-300 group-hover:translate-x-1" />
                </a>

                <a href="https://x.com/edris__ranjbar" target="_blank" rel="noopener noreferrer" class="social-link group flex items-center justify-between rounded-2xl border border-white/10 bg-white/[0.03] px-4 py-4 transition-all duration-300 hover:-translate-y-1 hover:border-white/20 hover:bg-white/10">
                  <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/10 text-white">
                      <span class="text-sm font-black">X</span>
                    </div>
                    <span class="font-medium text-white">ایکس</span>
                  </div>
                  <font-awesome-icon icon="arrow-left" class="text-white/45 transition-transform duration-300 group-hover:translate-x-1" />
                </a>
              </div>
            </div>

            <div class="rounded-[28px] border border-primary/20 bg-primary/10 p-6 shadow-lg shadow-primary/10">
              <p class="text-sm text-primary/80 mb-2">در دسترس برای</p>
              <p class="text-white font-semibold text-lg">
                همکاری فریلنسری، پروژه‌های شخصی و ساخت محصولات دیجیتال
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { onBeforeUnmount, ref } from 'vue'

const copiedText = ref('')
let toastTimer = null

const copyToClipboard = async (value, message) => {
  try {
    await navigator.clipboard.writeText(value)
    copiedText.value = message

    if (toastTimer) {
      clearTimeout(toastTimer)
    }

    toastTimer = setTimeout(() => {
      copiedText.value = ''
    }, 1800)
  } catch (error) {
    copiedText.value = 'کپی با خطا مواجه شد'

    if (toastTimer) {
      clearTimeout(toastTimer)
    }

    toastTimer = setTimeout(() => {
      copiedText.value = ''
    }, 1800)
  }
}

onBeforeUnmount(() => {
  if (toastTimer) {
    clearTimeout(toastTimer)
  }
})
</script>

<style scoped>
.contact-card {
  animation: contactFade 0.7s ease-out both;
}

.social-link {
  animation: contactFade 0.7s ease-out both;
}

.social-link:nth-child(2) {
  animation-delay: 0.08s;
}

.social-link:nth-child(3) {
  animation-delay: 0.16s;
}

@keyframes contactFade {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
