<template>
  <section id="projects" class="py-16 px-4 md:px-8 bg-gradient-to-b from-black/50 to-black/30">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-12">
        <h2 class="text-4xl font-bold mb-4 font-vazir">
          <span class="text-primary">پروژه</span>های آزاد
        </h2>
        <p class="text-white/70 max-w-3xl mx-auto">
            این‌ها پروژه‌هایی هستند که به صورت متن‌باز توسعه می‌دهم و از طراحی تا پیاده‌سازی، توسعه، و نگهداری آن‌ها را به عنوان یک توسعه‌دهنده فول‌استک شخصاً انجام می‌دهم.
            از مشارکت علاقه‌مندان در توسعه این پروژه‌ها استقبال می‌کنم و در گیت‌هاب منتظر پول‌ریکوئست‌های شما هستم!
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
        <!-- Project 1 -->
        <div class="bg-black/40 rounded-xl overflow-hidden border border-white/5 hover:border-primary/30 transition-all duration-200 group">
          <div class="relative overflow-hidden aspect-video">
            <img 
              src="/projects/nour.webp" 
              alt="اذکار نور - اپلیکیشن اذکار اسلامی" 
              class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
            <div class="absolute bottom-0 right-0 p-3">
              <span class="bg-primary text-white text-xs px-2 py-1 rounded">وب اپلیکیشن </span>
              <span class="bg-gray-800 text-white text-xs px-2 py-1 rounded mr-1">ویو جی اس</span>
            </div>
          </div>
          <div class="p-6">
            <h3 class="text-xl font-bold text-white mb-2">اذکار نور - اپلیکیشن اذکار اسلامی</h3>
            <p class="text-white/70 mb-4 line-clamp-3">
              پروژه‌ای برای دسترسی آسان و سریع به ذکرهای مخصوص روزهای هفته، ادعیه، صلوات، تسبیحات و اذکار اسلامی. این پروژه با ویو‌جی‌اس توسعه یافته و هدف آن حفظ و گسترش فرهنگ ذکر و دعا در جامعه اسلامی است.
            </p>
            <div class="flex justify-between items-center">
              <a 
                href="https://adhkar.ir/" 
                target="_blank" 
                class="text-primary hover:text-primary-dark transition-colors flex items-center"
              >
                <span>مشاهده پروژه</span>
                <font-awesome-icon icon="arrow-left" class="mr-1" />
              </a>
              <div class="flex items-center gap-4">
                <a 
                  href="https://github.com/edrisranjbar/nour-adhkar" 
                  target="_blank" 
                  class="text-white/60 hover:text-white transition-colors"
                >
                  <font-awesome-icon :icon="['fab', 'github']" size="lg" />
                </a>
                <a 
                  href="https://adhkar.ir/"
                  target="_blank" 
                  class="text-white/60 hover:text-white transition-colors"
                >
                  <font-awesome-icon icon="globe" size="lg" />
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Project 2 -->
        <div class="bg-black/40 rounded-xl overflow-hidden border border-white/5 hover:border-primary/30 transition-all duration-200 group">
          <div class="relative overflow-hidden aspect-video">
            <svg 
              class="w-full h-full bg-black/40" 
              xmlns="http://www.w3.org/2000/svg" 
              viewBox="0 0 800 450" 
              preserveAspectRatio="xMidYMid slice"
            >
              <rect width="100%" height="100%" fill="#1a1a2e" />
              <g transform="translate(400, 225)">
                <text 
                  text-anchor="middle" 
                  dy=".3em" 
                  fill="#ffffff" 
                  font-family="Arial, sans-serif" 
                  font-size="32" 
                  font-weight="bold"
                >
                  مای‌پسمیکر
                </text>
                <path 
                  d="M-40 40 L40 40" 
                  stroke="#10b981" 
                  stroke-width="4" 
                  stroke-linecap="round"
                />
              </g>
            </svg>
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
            <div class="absolute bottom-0 right-0 p-3">
              <span class="bg-primary text-white text-xs px-2 py-1 rounded">پایتون </span>
              <span class="bg-gray-800 text-white text-xs px-2 py-1 rounded mr-1">کتابخانه </span>
              <span class="bg-gray-800 text-white text-xs px-2 py-1 rounded mr-1">امنیت </span>
            </div>
          </div>
          <div class="p-6">
            <h3 class="text-xl font-bold text-white mb-2">کتابخانه پایتون مای‌پسمیکر</h3>
            <p class="text-white/70 mb-4 line-clamp-3">
              ابزار ساده و امن برای تولید رمزهای عبور تصادفی و عبارت‌های رمز قابل یادآوری. قابلیت‌هایی چون تنظیم طول رمز، ارزیابی امنیت رمز، رابط خط فرمان و ذخیره‌سازی رمزها در قالب‌های مختلف را دارد.
            </p>
            <div class="flex justify-between items-center">
              <a 
                href="https://github.com/edrisranjbar/mypassmaker" 
                target="_blank" 
                class="text-primary hover:text-primary-dark transition-colors flex items-center"
              >
                <span>مشاهده پروژه</span>
                <font-awesome-icon icon="arrow-left" class="mr-1" />
              </a>
              <div class="flex space-x-2">
                <a 
                  href="https://github.com/edrisranjbar/mypassmaker" 
                  target="_blank" 
                  class="text-white/60 hover:text-white transition-colors"
                >
                  <font-awesome-icon :icon="['fab', 'github']" size="lg" />
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Project 3 - Morse Magic -->
        <div class="bg-black/40 rounded-xl overflow-hidden border border-white/5 hover:border-primary/30 transition-all duration-200 group">
          <div class="relative overflow-hidden aspect-video">
            <img 
              src="/projects/morse-magic.webp" 
              alt="مورس مجیک - ابزار یادگیری و تبدیل متن به کد مورس و بالعکس" 
              class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
            <div class="absolute bottom-0 right-0 p-3">
              <span class="bg-primary text-white text-xs px-2 py-1 rounded">وب سایت</span>
            </div>
          </div>
          <div class="p-6">
            <h3 class="text-xl font-bold text-white mb-2">مورس مجیک</h3>
            <p class="text-white/70 mb-4 line-clamp-3">
              ابزار قدرتمند برای تبدیل متن به کد مورس و بالعکس. قابلیت‌های پیشرفته شامل پخش صوتی، تنظیم سرعت، پشتیبانی از کاراکترهای خاص و رابط گرافیکی کاربرپسند.
            </p>
            <div class="flex justify-between items-center">
              <a 
                href="https://edrisranjbar.github.io/morse-magic/" 
                target="_blank" 
                class="text-primary hover:text-primary-dark transition-colors flex items-center"
              >
                <span>مشاهده پروژه</span>
                <font-awesome-icon icon="arrow-left" class="mr-1" />
              </a>
              <div class="flex space-x-2">
                <a 
                  href="https://github.com/edrisranjbar/morse-magic" 
                  target="_blank" 
                  class="text-white/60 hover:text-white transition-colors"
                >
                  <font-awesome-icon :icon="['fab', 'github']" size="lg" />
                </a>
              </div>
            </div>
          </div>
        </div>

      <!-- Project 4 - Coolping -->
      <div class="bg-black/40 rounded-xl overflow-hidden border border-white/5 hover:border-primary/30 transition-all duration-200 group">
        <div class="relative overflow-hidden aspect-video">
          <img 
            src="/projects/coolping.png" 
            alt="Coolping - ابزار تست پینگ و بررسی تاخیر شبکه" 
            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
          />
          <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
          <div class="absolute bottom-0 right-0 p-3">
            <span class="bg-primary text-white text-xs px-2 py-1 rounded">لینوکسی </span>
            <span class="bg-gray-800 text-white text-xs px-2 py-1 rounded mr-1">کتابخانه </span>
          </div>
        </div>
        <div class="p-6">
          <h3 class="text-xl font-bold text-white mb-2">ابزار ترمینال coolping</h3>
          <p class="text-white/70 mb-4 line-clamp-3">
            ابزار ساده و باحال برای تست پینگ و بررسی تاخیر شبکه. مناسب برای برنامه نویس ها، سیستم ادمین ها و کاربران حرفه‌ای جهت مانیتورینگ وضعیت اتصال اینترنت.
          </p>
          <div class="flex justify-between items-center">
            <a 
              href="https://edrisranjbar.github.io/coolping/" 
              target="_blank" 
              class="text-primary hover:text-primary-dark transition-colors flex items-center"
            >
              <span>مشاهده پروژه</span>
              <font-awesome-icon icon="arrow-left" class="mr-1" />
            </a>
            <div class="flex space-x-2">
              <a 
                href="https://github.com/edrisranjbar/coolping" 
                target="_blank" 
                class="text-white/60 hover:text-white transition-colors"
              >
                <font-awesome-icon :icon="['fab', 'github']" size="lg" />
              </a>
            </div>
          </div>
        </div>
      </div>
      <!-- Project 5 - Quran Radio -->
      <div class="bg-black/40 rounded-xl overflow-hidden border border-white/5 hover:border-primary/30 transition-all duration-200 group">
        <div class="relative overflow-hidden aspect-video">
          <img 
            src="/projects/quran-radio.png" 
            alt="رادیو قرآن - پخش زنده قرآن کریم" 
            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
          />
          <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
          <div class="absolute bottom-0 right-0 p-3">
            <span class="bg-primary text-white text-xs px-2 py-1 rounded">وب اپلیکیشن</span>
            <span class="bg-gray-800 text-white text-xs px-2 py-1 rounded mr-1">استریم صوتی</span>
          </div>
        </div>
        <div class="p-6">
          <h3 class="text-xl font-bold text-white mb-2">رادیو قرآن</h3>
          <p class="text-white/70 mb-4 line-clamp-3">
            پخش زنده تلاوت قرآن کریم و دسترسی به ایستگاه‌های قرآنی با رابط کاربری ساده و سبک. مناسب برای شنیدن تلاوت‌ها و برنامه‌های قرآنی به صورت آنلاین.
          </p>
          <div class="flex justify-between items-center">
            <a 
              href="https://online-nour-cast.ir/" 
              target="_blank" 
              class="text-primary hover:text-primary-dark transition-colors flex items-center"
            >
              <span>مشاهده پروژه</span>
              <font-awesome-icon icon="arrow-left" class="mr-1" />
            </a>
            <div class="flex items-center gap-4">
              <a 
                href="https://github.com/edrisranjbar/quran-radio" 
                target="_blank" 
                class="text-white/60 hover:text-white transition-colors"
              >
                <font-awesome-icon :icon="['fab', 'github']" size="lg" />
              </a>
              <a 
                href="https://online-nour-cast.ir/"
                target="_blank" 
                class="text-white/60 hover:text-white transition-colors"
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
// Project section component
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