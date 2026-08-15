<template>
  <section id="projects" class="py-16 px-4 md:px-8 bg-gradient-to-b from-black/50 to-black/30">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-12">
        <h2 class="text-4xl font-bold mb-4 font-vazir">
          <span class="text-primary">پروژه&nbsp;</span>ها
        </h2>
        <p class="text-white/70 max-w-3xl mx-auto">
          این‌ها پروژه‌هایی هستند که از طراحی تا پیاده‌سازی، توسعه و نگهداری آن‌ها را به
          عنوان یک توسعه‌دهنده فول‌استک شخصاً انجام می‌دهم.
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 max-w-7xl mx-auto">
        <div
          v-for="project in projects"
          :key="project.title"
          class="bg-black/40 rounded-xl overflow-hidden border border-white/5 hover:border-primary/30 transition-all duration-200 group"
        >
          <div class="relative overflow-hidden aspect-video">
            <img
              v-if="project.image"
              :src="project.image"
              :alt="project.imageAlt"
              class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
            />
            <div
              v-else
              class="w-full h-full bg-[#1a1a2e] flex items-center justify-center transition-transform duration-500 group-hover:scale-110"
            >
              <span class="text-white text-xl font-bold px-6 text-center">{{ project.title }}</span>
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
            <div class="absolute bottom-0 right-0 p-3">
              <span
                v-for="(tag, index) in project.tags"
                :key="tag"
                :class="[
                  'text-white text-xs px-2 py-1 rounded',
                  index === 0 ? 'bg-primary' : 'bg-gray-800 mr-1',
                ]"
              >
                {{ tag }}
              </span>
            </div>
          </div>
          <div class="p-6">
            <h3 class="text-xl font-bold text-white mb-2">{{ project.title }}</h3>
            <p class="text-white/70 mb-4 line-clamp-3">
              {{ project.description }}
            </p>
            <div class="flex justify-between items-center">
              <a
                v-if="project.projectUrl"
                :href="project.projectUrl"
                target="_blank"
                rel="noopener noreferrer"
                class="text-primary hover:text-primary-dark transition-colors flex items-center"
              >
                <span>مشاهده پروژه</span>
                <font-awesome-icon icon="arrow-left" class="mr-1" />
              </a>
              <span v-else class="text-white/50">به‌زودی</span>
              <div class="flex items-center gap-4">
                <a
                  v-if="project.githubUrl"
                  :href="project.githubUrl"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="text-white/60 hover:text-white transition-colors"
                  :aria-label="`مخزن گیت‌هاب ${project.title}`"
                >
                  <font-awesome-icon :icon="['fab', 'github']" size="lg" />
                </a>
                <a
                  v-if="project.websiteUrl"
                  :href="project.websiteUrl"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="text-white/60 hover:text-white transition-colors"
                  :aria-label="`وب‌سایت ${project.title}`"
                >
                  <font-awesome-icon icon="globe" size="lg" />
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="text-center mt-12">
        <a
          href="https://github.com/edrisranjbar"
          target="_blank"
          rel="noopener noreferrer"
          class="inline-flex items-center px-6 py-3 bg-primary hover:bg-primary-dark text-white rounded-lg transition-colors duration-200 font-medium"
        >
          <span>مشاهده همه پروژه‌ها در گیت‌هاب</span>
          <font-awesome-icon :icon="['fab', 'github']" class="mr-2" />
        </a>
      </div>
    </div>
  </section>
</template>

<script setup>
const projects = [
  {
    title: 'وب‌سایت سماک قرآن',
    description: 'وب‌سایت سماک قرآن.',
    tags: ['وب‌سایت', 'قرآن'],
  },
  {
    title: 'فروشگاه اینترنتی عینک ایندکس',
    description: 'فروشگاه اینترنتی عینک ایندکس.',
    tags: ['فروشگاه اینترنتی', 'عینک'],
  },
  {
    title: 'فروشگاه کتونی پاویس',
    description: 'فروشگاه اینترنتی کتونی پاویس.',
    tags: ['فروشگاه اینترنتی', 'کتونی'],
  },
  {
    title: 'فروشگاه کیف و کفش آیهان',
    description: 'فروشگاه اینترنتی کیف و کفش آیهان.',
    tags: ['فروشگاه اینترنتی', 'کیف و کفش'],
  },
  {
    title: 'افزونه وردپرس Soonify',
    description:
      'افزونه‌ای سبک و مدرن برای ساخت صفحه «به‌زودی» در وردپرس با پشتیبانی کامل از زبان فارسی، چیدمان راست‌چین، فونت محلی و امکان شخصی‌سازی عنوان، توضیح و رنگ‌ها.',
    image: '/projects/soonify.png',
    imageAlt: 'افزونه وردپرس Soonify',
    tags: ['افزونه وردپرس', 'PHP', 'RTL'],
    projectUrl: 'https://github.com/edrisranjbar/soonify',
    githubUrl: 'https://github.com/edrisranjbar/soonify',
  },
  {
    title: 'اپلیکیشن بده بسون',
    description:
      'اپلیکیشن مدیریت مالی برای ثبت درآمدها و هزینه‌ها، دسته‌بندی تراکنش‌ها و مشاهده گزارش‌ها و تحلیل‌های مالی.',
    tags: ['اپلیکیشن', 'مدیریت مالی', 'Laravel'],
    projectUrl: 'https://github.com/edrisranjbar/bede-bessoon-api',
    githubUrl: 'https://github.com/edrisranjbar/bede-bessoon-api',
  },
  {
    title: 'اپلیکیشن اذکار نور (به‌زودی)',
    description:
      'پلتفرمی برای دسترسی آسان به اذکار صبح و شب، دعاها، تسبیح، آیه روز و مجموعه‌ای از یادآوری‌های اسلامی با رابط کاربری ساده و زیبا.',
    image: '/projects/nour.webp',
    imageAlt: 'اپلیکیشن اذکار نور',
    tags: ['به‌زودی', 'وب اپلیکیشن', 'Vue.js'],
    projectUrl: 'https://github.com/edrisranjbar/nour-adhkar',
    githubUrl: 'https://github.com/edrisranjbar/nour-adhkar',
  },
  {
    title: 'قرآن رادیو',
    description:
      'رادیوی آنلاین قرآن برای پخش تلاوت در دو سبک تحقیق و ترتیل، با رابط کاربری مدرن، طراحی موبایل‌محور و نمایش زنده تعداد شنوندگان.',
    image: '/projects/quran-radio.png',
    imageAlt: 'قرآن رادیو - پخش آنلاین تلاوت قرآن',
    tags: ['رادیو آنلاین', 'Nuxt', 'قرآن'],
    projectUrl: 'https://quran-radio.ir',
    websiteUrl: 'https://quran-radio.ir',
    githubUrl: 'https://github.com/edrisranjbar/quran-radio',
  },
  {
    title: 'لینک کیت',
    description:
      'سرویس فارسی کوتاه‌کردن لینک با پنل کاربری و مدیریت، ورود پیامکی، آمار و تحلیل کلیک‌ها و رابط کاربری واکنش‌گرا.',
    tags: ['کوتاه‌کننده لینک', 'Laravel', 'Tailwind CSS'],
    projectUrl: 'https://github.com/edrisranjbar/linkkit',
    githubUrl: 'https://github.com/edrisranjbar/linkkit',
  },
  {
    title: 'mypassmaker',
    description:
      'ابزار ساده و امن پایتون برای تولید رمزهای عبور تصادفی و عبارت‌های رمز قابل یادآوری، ارزیابی امنیت رمز و استفاده از طریق خط فرمان.',
    tags: ['پایتون', 'کتابخانه', 'امنیت'],
    projectUrl: 'https://github.com/edrisranjbar/mypassmaker',
    githubUrl: 'https://github.com/edrisranjbar/mypassmaker',
  },
  {
    title: 'coolping',
    description:
      'جایگزینی مدرن و سرگرم‌کننده برای دستور ping با خروجی رنگی، بازخورد ایموجی، مانیتورینگ پیوسته و امکان ثبت گزارش.',
    image: '/projects/coolping.png',
    imageAlt: 'coolping - ابزار مدرن بررسی پینگ و تاخیر شبکه',
    tags: ['لینوکس', 'شبکه', 'ترمینال'],
    projectUrl: 'https://edrisranjbar.github.io/coolping/',
    websiteUrl: 'https://edrisranjbar.github.io/coolping/',
    githubUrl: 'https://github.com/edrisranjbar/coolping',
  },
]
</script>

<style scoped>
.aspect-video {
  aspect-ratio: 16 / 9;
}

.line-clamp-3 {
  display: -webkit-box;
  line-clamp: 3;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
