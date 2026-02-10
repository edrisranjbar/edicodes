<template>
  <footer class="bg-dark/50 py-12 mt-16" dir="rtl">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <!-- About -->
        <div>
          <h3 class="text-lg font-semibold mb-4 font-vazir">درباره من</h3>
          <p class="text-white/70 text-sm mb-4 font-vazir">
            توسعه‌دهنده بک‌اند، مدیر سیستم و متخصص معماری سیستم. متخصص در توسعه API و زیرساخت‌های نرم‌افزاری.
          </p>
        </div>

        <!-- Quick Links -->
        <div>
          <h3 class="text-lg font-semibold mb-4 font-vazir">دسترسی سریع</h3>
          <ul class="space-y-2">
            <li>
              <router-link to="/" class="text-white/70 hover:text-white transition-colors duration-200 text-sm font-vazir flex items-center gap-2">
                <font-awesome-icon icon="home" class="w-4 h-4" />
                صفحه اصلی
              </router-link>
            </li>
            <li>
              <router-link to="/#blog" class="text-white/70 hover:text-white transition-colors duration-200 text-sm font-vazir flex items-center gap-2">
                <font-awesome-icon icon="book" class="w-4 h-4" />
                وبلاگ
              </router-link>
            </li>
            <li>
              <router-link to="/donation" class="text-white/70 hover:text-white transition-colors duration-200 text-sm font-vazir flex items-center gap-2">
                <font-awesome-icon icon="heart" class="w-4 h-4" />
                حمایت
              </router-link>
            </li>
          </ul>
        </div>

        <!-- Social Links -->
        <div>
          <h3 class="text-lg font-semibold mb-4 font-vazir">شبکه‌های اجتماعی</h3>
          <ul class="space-y-2">
            <li>
              <a href="https://github.com/edrisranjbar" target="_blank" class="text-white/70 hover:text-white transition-colors duration-200 text-sm font-vazir flex items-center gap-2">
                <font-awesome-icon :icon="['fab', 'github']" class="w-4 h-4" />
                گیت‌هاب
              </a>
            </li>
            <li>
              <a href="https://www.youtube.com/@edrisranjbar" target="_blank" class="text-white/70 hover:text-white transition-colors duration-200 text-sm font-vazir flex items-center gap-2">
                <font-awesome-icon :icon="['fab', 'youtube']" class="w-4 h-4" />
                یوتیوب
              </a>
            </li>
            <li>
              <a href="https://linkedin.com/in/edrisranjbar" target="_blank" class="text-white/70 hover:text-white transition-colors duration-200 text-sm font-vazir flex items-center gap-2">
                <font-awesome-icon :icon="['fab', 'linkedin']" class="w-4 h-4" />
                لینکدین
              </a>
            </li>
            <li>
              <a href="https://t.me/edrisranjbar" target="_blank" class="text-white/70 hover:text-white transition-colors duration-200 text-sm font-vazir flex items-center gap-2">
                <font-awesome-icon :icon="['fab', 'telegram']" class="w-4 h-4" />
                تلگرام
              </a>
            </li>
          </ul>
        </div>

        <!-- Contact -->
        <div>
          <h3 class="text-lg font-semibold mb-4 font-vazir">تماس با من</h3>
          <ul class="space-y-2">
            <li class="flex items-center gap-2 text-white/70 text-sm font-vazir">
              <font-awesome-icon icon="envelope" class="w-4 h-4" />
              edrisranjbar.dev@gmail.com
            </li>
            <li class="flex items-center gap-2 text-white/70 text-sm font-vazir">
              <font-awesome-icon icon="home" class="w-4 h-4" />
              قشم، هرمزگان
            </li>
          </ul>
        </div>
      </div>

      <div class="border-t border-white/10 mt-12 pt-8 text-center">
        <p class="text-white/50 text-sm font-vazir">
          تمامی حقوق محفوظ است © {{ persianYear }}
        </p>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import moment from 'moment-jalaali'

const persianYear = ref('')

const toPersianNumber = (number) => {
  const persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹']
  return number.toString().split('').map(digit => persianDigits[digit] || digit).join('')
}

onMounted(() => {
  persianYear.value = toPersianNumber(moment().jYear())
})
</script>

<style scoped>
.footer-link {
  @apply text-white/85 hover:text-white transition-colors duration-200 text-sm inline-block;
}

.social-link {
  @apply px-6 py-3 bg-white/5 hover:bg-white/10 text-white/85 hover:text-white rounded-lg text-sm transition-all duration-200 border border-white/10 hover:border-primary/30;
}
</style> 