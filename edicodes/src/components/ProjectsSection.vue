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

      <!-- Featured project -->
      <article
        class="featured max-w-7xl mx-auto mb-14 grid grid-cols-1 lg:grid-cols-5 bg-black/40 rounded-2xl overflow-hidden border border-primary/20 hover:border-primary/40 transition-colors duration-300 group"
      >
        <a
          :href="featured.websiteUrl || featured.githubUrl"
          target="_blank"
          rel="noopener noreferrer"
          class="relative lg:col-span-3 overflow-hidden aspect-video lg:aspect-auto lg:min-h-[22rem] block"
          :aria-label="`مشاهده ${featured.title}`"
        >
          <img
            :src="featured.image"
            :alt="featured.imageAlt"
            loading="lazy"
            class="absolute inset-0 w-full h-full object-cover object-top transition-transform duration-700 group-hover:scale-105"
          />
          <div
            class="absolute inset-0 bg-gradient-to-t lg:bg-gradient-to-l from-black/70 via-black/10 to-transparent"
          ></div>
        </a>

        <div class="lg:col-span-2 p-6 md:p-8 flex flex-col">
          <div class="flex items-center gap-2 mb-4">
            <span
              class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1 rounded-full bg-secondary/15 text-secondary border border-secondary/30"
            >
              <font-awesome-icon icon="star" />
              پروژه ویژه
            </span>
            <span class="text-xs text-white/50">متن‌باز · MIT</span>
          </div>

          <h3 class="text-2xl md:text-3xl font-bold text-white mb-1" dir="ltr" style="text-align: right">
            {{ featured.title }}
          </h3>
          <p class="text-primary-light text-sm mb-4">{{ featured.subtitle }}</p>
          <p class="text-white/75 leading-relaxed mb-5">{{ featured.description }}</p>

          <ul class="grid grid-cols-2 gap-2 mb-6">
            <li
              v-for="feature in featured.features"
              :key="feature.label"
              class="flex items-center gap-2 text-sm text-white/80 bg-white/5 rounded-lg px-3 py-2"
            >
              <font-awesome-icon :icon="feature.icon" class="text-primary w-4" />
              <span>{{ feature.label }}</span>
            </li>
          </ul>

          <div class="flex flex-wrap gap-1.5 mb-6">
            <span
              v-for="tag in featured.tags"
              :key="tag"
              class="text-xs px-2 py-1 rounded bg-white/5 text-white/60 border border-white/10"
              dir="ltr"
            >
              {{ tag }}
            </span>
          </div>

          <div class="mt-auto flex flex-wrap items-center gap-3">
            <a
              :href="featured.githubUrl"
              target="_blank"
              rel="noopener noreferrer"
              class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary-dark text-white rounded-lg transition-colors duration-200 font-medium"
            >
              <font-awesome-icon :icon="['fab', 'github']" />
              <span>مشاهده در گیت‌هاب</span>
            </a>
          </div>
        </div>
      </article>

      <!-- Filters -->
      <div class="flex flex-wrap justify-center gap-2 mb-8" role="tablist" aria-label="دسته‌بندی پروژه‌ها">
        <button
          v-for="category in categories"
          :key="category.id"
          type="button"
          role="tab"
          :aria-selected="activeCategory === category.id"
          @click="activeCategory = category.id"
          :class="[
            'px-4 py-2 rounded-full text-sm border transition-colors duration-200',
            activeCategory === category.id
              ? 'bg-primary text-white border-primary'
              : 'bg-white/5 text-white/70 border-white/10 hover:text-white hover:border-white/30',
          ]"
        >
          {{ category.label }}
          <span class="mr-1 text-xs opacity-70">{{ countFor(category.id) }}</span>
        </button>
      </div>

      <TransitionGroup
        name="project"
        tag="div"
        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-7xl mx-auto"
      >
        <article
          v-for="project in filteredProjects"
          :key="project.title"
          class="bg-black/40 rounded-xl overflow-hidden border border-white/5 hover:border-primary/30 hover:-translate-y-1 transition-all duration-300 group flex flex-col"
        >
          <div class="relative overflow-hidden aspect-video">
            <img
              v-if="project.image"
              :src="project.image"
              :alt="project.imageAlt"
              loading="lazy"
              class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
            />
            <div
              v-else
              :class="[
                'placeholder w-full h-full flex flex-col items-center justify-center gap-3 transition-transform duration-500 group-hover:scale-110',
                `placeholder--${project.category}`,
              ]"
            >
              <font-awesome-icon :icon="categoryIcon(project.category)" class="text-3xl text-white/80" />
              <span class="text-white text-lg font-bold px-6 text-center">{{ project.title }}</span>
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent pointer-events-none"></div>
            <span
              v-if="project.comingSoon"
              class="absolute top-3 left-3 text-xs font-medium px-2.5 py-1 rounded-full bg-secondary text-dark"
            >
              به‌زودی
            </span>
          </div>

          <div class="p-5 flex flex-col flex-1">
            <h3 class="text-lg font-bold text-white mb-2">{{ project.title }}</h3>
            <p class="text-white/70 text-sm leading-relaxed mb-4 line-clamp-3">
              {{ project.description }}
            </p>
            <div class="flex flex-wrap gap-1.5 mb-5">
              <span
                v-for="tag in project.tags"
                :key="tag"
                class="text-xs px-2 py-0.5 rounded bg-white/5 text-white/60 border border-white/10"
              >
                {{ tag }}
              </span>
            </div>

            <div class="mt-auto flex justify-between items-center pt-4 border-t border-white/5">
              <a
                v-if="project.websiteUrl || project.githubUrl"
                :href="project.websiteUrl || project.githubUrl"
                target="_blank"
                rel="noopener noreferrer"
                class="text-primary hover:text-primary-light transition-colors flex items-center text-sm"
              >
                <span>مشاهده پروژه</span>
                <font-awesome-icon icon="arrow-left" class="mr-1 transition-transform group-hover:-translate-x-1" />
              </a>
              <span v-else class="text-white/40 text-sm">پروژه مشتری</span>
              <div class="flex items-center gap-4">
                <a
                  v-if="project.storeUrl"
                  :href="project.storeUrl"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="text-white/60 hover:text-white transition-colors"
                  :aria-label="`نصب ${project.title} از کافه‌بازار`"
                >
                  <font-awesome-icon icon="download" size="lg" />
                </a>
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
        </article>
      </TransitionGroup>

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
import { computed, ref } from 'vue'

const featured = {
  title: 'Edi Life OS',
  subtitle: 'سیستم‌عامل شخصی زندگی، خودمیزبان و آماده هوش مصنوعی',
  description:
    'داشبوردی یکپارچه و خصوصی برای تمرکز، عادت‌ها، اهداف، امور مالی و پروژه‌ها؛ به‌جای پراکندگی بین چند اپ مختلف. عادت‌ها پیشرفت اهداف را می‌سازند، کارت‌های کانبان در تقویم دیده می‌شوند و همه‌چیز در یک امتیاز روزانه خلاصه می‌شود. با سرور MCP داخلی، Claude و دیگر دستیارهای هوش مصنوعی می‌توانند در کنار شما برنامه‌ریزی کنند.',
  image: '/projects/lifeos.webp',
  imageAlt: 'داشبورد Edi Life OS با امتیاز بهره‌وری روزانه و شش بُعد زندگی',
  features: [
    { icon: 'seedling', label: 'اهداف SMART و رشد' },
    { icon: 'clock', label: 'تایمر تمرکز پومودورو' },
    { icon: 'check-circle', label: 'ردیابی عادت‌ها' },
    { icon: 'diagram-project', label: 'بوردهای کانبان' },
    { icon: 'money-bill-wave', label: 'مدیریت مالی و بودجه' },
    { icon: 'bolt', label: 'سرور MCP با ۲۶ ابزار' },
  ],
  tags: ['PHP', 'MySQL', 'JavaScript', 'MCP', 'Docker', 'Self-hosted'],
  githubUrl: 'https://github.com/edrisranjbar/lifeos',
}

const categories = [
  { id: 'all', label: 'همه' },
  { id: 'mobile', label: 'اپلیکیشن' },
  { id: 'app', label: 'وب‌اپلیکیشن' },
  { id: 'shop', label: 'وب‌سایت و فروشگاه' },
  { id: 'tool', label: 'ابزار و کتابخانه' },
]

const categoryIcons = {
  app: 'rocket',
  shop: 'globe',
  tool: 'code',
}

const projects = [
  {
    title: 'قرآن رادیو',
    category: 'app',
    description:
      'رادیوی آنلاین قرآن برای پخش تلاوت در دو سبک تحقیق و ترتیل، با رابط کاربری مدرن، طراحی موبایل‌محور و نمایش زنده تعداد شنوندگان.',
    image: '/projects/quran-radio.png',
    imageAlt: 'قرآن رادیو - پخش آنلاین تلاوت قرآن',
    tags: ['Nuxt', 'رادیو آنلاین', 'قرآن'],
    websiteUrl: 'https://quran-radio.ir',
    githubUrl: 'https://github.com/edrisranjbar/quran-radio',
  },
  {
    title: 'اپلیکیشن اذکار نور',
    category: 'mobile',
    description:
      'اپلیکیشن اندروید برای دسترسی آسان به اذکار صبح و شب، دعاها، تسبیح، آیه روز و مجموعه‌ای از یادآوری‌های اسلامی با رابط کاربری ساده و آرام.',
    image: '/projects/nour-adhkar.webp',
    imageAlt: 'اپلیکیشن اذکار نور',
    tags: ['اندروید', 'Kotlin', 'کافه‌بازار'],
    websiteUrl: 'https://adhkar.ir',
    storeUrl: 'https://cafebazaar.ir/app/ir.adhkar.app',
    githubUrl: 'https://github.com/edrisranjbar/Nour-Adhkar-App',
  },
  {
    title: 'اپلیکیشن بده بستون',
    category: 'mobile',
    description:
      'اپلیکیشن حسابداری شخصی برای ثبت درآمدها و هزینه‌ها، دسته‌بندی تراکنش‌ها و مشاهده گزارش‌ها و تحلیل‌های مالی.',
    image: '/projects/bede-bestoon.webp',
    imageAlt: 'اپلیکیشن حسابداری شخصی بده بستون',
    tags: ['اندروید', 'Laravel API', 'مدیریت مالی'],
    storeUrl: 'https://cafebazaar.ir/app/ir.bessoon.bedebestoon',
    githubUrl: 'https://github.com/edrisranjbar/bede-bessoon-api',
  },
  {
    title: 'لینک کیت',
    category: 'app',
    description:
      'سرویس فارسی کوتاه‌کردن لینک با پنل کاربری و مدیریت، ورود پیامکی، آمار و تحلیل کلیک‌ها و رابط کاربری واکنش‌گرا.',
    tags: ['Laravel', 'Tailwind CSS', 'کوتاه‌کننده لینک'],
    githubUrl: 'https://github.com/edrisranjbar/linkkit',
  },
  {
    title: 'coolping',
    category: 'tool',
    description:
      'جایگزینی مدرن و سرگرم‌کننده برای دستور ping با خروجی رنگی، بازخورد ایموجی، مانیتورینگ پیوسته و امکان ثبت گزارش.',
    image: '/projects/coolping.png',
    imageAlt: 'coolping - ابزار مدرن بررسی پینگ و تاخیر شبکه',
    tags: ['لینوکس', 'شبکه', 'ترمینال'],
    websiteUrl: 'https://edrisranjbar.github.io/coolping/',
    githubUrl: 'https://github.com/edrisranjbar/coolping',
  },
  {
    title: 'افزونه وردپرس Soonify',
    category: 'tool',
    description:
      'افزونه‌ای سبک و مدرن برای ساخت صفحه «به‌زودی» در وردپرس با پشتیبانی کامل از زبان فارسی، چیدمان راست‌چین، فونت محلی و امکان شخصی‌سازی عنوان، توضیح و رنگ‌ها.',
    image: '/projects/soonify.png',
    imageAlt: 'افزونه وردپرس Soonify',
    tags: ['WordPress', 'PHP', 'RTL'],
    githubUrl: 'https://github.com/edrisranjbar/soonify',
  },
  {
    title: 'mypassmaker',
    category: 'tool',
    description:
      'ابزار ساده و امن پایتون برای تولید رمزهای عبور تصادفی و عبارت‌های رمز قابل یادآوری، ارزیابی امنیت رمز و استفاده از طریق خط فرمان.',
    tags: ['Python', 'کتابخانه', 'امنیت'],
    githubUrl: 'https://github.com/edrisranjbar/mypassmaker',
  },
  {
    title: 'وب‌سایت سماک قرآن',
    category: 'shop',
    description: 'طراحی و توسعه وب‌سایت مؤسسه قرآنی سماک.',
    tags: ['وب‌سایت', 'قرآن'],
  },
  {
    title: 'فروشگاه اینترنتی عینک ایندکس',
    category: 'shop',
    description: 'طراحی و توسعه فروشگاه اینترنتی عینک ایندکس.',
    tags: ['فروشگاه اینترنتی', 'عینک'],
  },
  {
    title: 'فروشگاه کتونی پاویس',
    category: 'shop',
    description: 'طراحی و توسعه فروشگاه اینترنتی کتونی پاویس.',
    tags: ['فروشگاه اینترنتی', 'کتونی'],
  },
  {
    title: 'فروشگاه کیف و کفش آیهان',
    category: 'shop',
    description: 'طراحی و توسعه فروشگاه اینترنتی کیف و کفش آیهان.',
    tags: ['فروشگاه اینترنتی', 'کیف و کفش'],
  },
]

const activeCategory = ref('all')

const filteredProjects = computed(() =>
  activeCategory.value === 'all'
    ? projects
    : projects.filter((project) => project.category === activeCategory.value),
)

const countFor = (id) =>
  id === 'all' ? projects.length : projects.filter((project) => project.category === id).length

const categoryIcon = (category) => categoryIcons[category] || 'code'
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

.placeholder {
  background-color: #1a1a2e;
  background-image:
    radial-gradient(circle at 20% 20%, rgba(124, 150, 247, 0.35), transparent 55%),
    radial-gradient(circle at 80% 90%, rgba(108, 99, 255, 0.25), transparent 50%);
}

.placeholder--shop {
  background-image:
    radial-gradient(circle at 80% 20%, rgba(255, 186, 8, 0.25), transparent 55%),
    radial-gradient(circle at 20% 90%, rgba(124, 150, 247, 0.25), transparent 50%);
}

.placeholder--tool {
  background-image:
    radial-gradient(circle at 50% 10%, rgba(57, 230, 173, 0.22), transparent 55%),
    radial-gradient(circle at 10% 90%, rgba(124, 150, 247, 0.25), transparent 50%);
}

.project-enter-active,
.project-leave-active {
  transition:
    opacity 0.25s ease,
    transform 0.25s ease;
}

.project-enter-from,
.project-leave-to {
  opacity: 0;
  transform: scale(0.96);
}

@media (prefers-reduced-motion: reduce) {
  .project-enter-active,
  .project-leave-active {
    transition: none;
  }
}
</style>
