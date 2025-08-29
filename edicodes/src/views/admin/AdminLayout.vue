<template>
  <div class="min-h-screen bg-gray-900 flex flex-col lg:flex-row" dir="rtl">
    <!-- Sidebar (mobile menu) -->
    <div v-if="isMenuOpen" class="fixed inset-0 z-40 lg:hidden" aria-modal="true">
      <div class="fixed inset-0 bg-black/50" @click="toggleMenu"></div>
      <nav class="relative flex flex-col max-w-xs w-full h-full bg-black/95 border-l border-white/10 py-4 pb-12 overflow-y-auto">
        <div class="flex items-center justify-between px-4">
          <div class="flex items-center">
            <span class="text-xl font-bold text-primary">ادیکدز</span>
            <span class="text-white/70 text-xs mr-2">پنل مدیریت</span>
          </div>
          <button 
            @click="toggleMenu" 
            class="text-white rounded-md p-2 inline-flex items-center justify-center hover:bg-white/10 focus:outline-none"
          >
            <font-awesome-icon icon="xmark" class="h-6 w-6" />
          </button>
        </div>
        <div class="mt-8 px-3">
          <!-- Sidebar Navigation -->
          <MobileNavigation />
        </div>
      </nav>
    </div>

    <!-- Static sidebar for desktop -->
    <div class="hidden lg:flex lg:flex-shrink-0">
      <div class="flex flex-col w-64">
        <div class="flex flex-col flex-grow bg-black/90 border-l border-white/10 h-screen sticky top-0 overflow-y-auto">
          <div class="flex items-center h-16 flex-shrink-0 px-4 border-b border-white/10">
            <span class="text-xl font-bold text-primary">ادیکدز</span>
            <span class="text-white/70 text-xs mr-2">پنل مدیریت</span>
          </div>
          <div class="mt-5 flex-1 flex flex-col px-3">
            <!-- Desktop navigation -->
            <DesktopNavigation />
          </div>
          <div class="p-4 border-t border-white/10">
            <button 
              @click="logout" 
              class="w-full px-3 py-2 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500/20 transition-colors duration-200 flex items-center font-vazir text-sm"
            >
              <font-awesome-icon icon="sign-out-alt" class="ml-2" />
              خروج از حساب کاربری
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <div class="flex-1 flex flex-col">
      <!-- Top navbar -->
      <header class="bg-black/90 border-b border-white/10 sticky top-0 z-10">
        <div class="flex justify-between h-16 px-4 sm:px-6 lg:px-8">
          <div class="flex items-center">
            <button 
              @click="toggleMenu" 
              class="lg:hidden text-white p-2 rounded-md hover:bg-white/10 focus:outline-none"
            >
              <font-awesome-icon icon="bars" class="h-6 w-6" />
            </button>
            <h1 class="text-lg font-vazir text-white mr-2">{{ pageTitle }}</h1>
          </div>
          <div class="flex items-center">
            <span class="text-white/70 text-sm mr-2 font-vazir hidden sm:block">{{ formattedDate }}</span>
            <button 
              @click="logout" 
              class="lg:hidden text-red-400 p-2 rounded-md hover:bg-white/10 focus:outline-none"
            >
              <font-awesome-icon icon="sign-out-alt" class="h-5 w-5" />
            </button>
          </div>
        </div>
      </header>

      <!-- Main content area -->
      <main class="flex-1 p-4 sm:p-6 lg:p-8">
        <router-view />
      </main>

      <!-- Admin footer -->
      <footer class="bg-black/30 border-t border-white/10 py-4 text-center text-white/50 text-sm font-vazir">
        <div class="container mx-auto">
          &copy; {{ new Date().getFullYear() }} ادیکدز - تمامی حقوق محفوظ است
        </div>
      </footer>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import MobileNavigation from '@/components/admin/MobileNavigation.vue';
import DesktopNavigation from '@/components/admin/DesktopNavigation.vue';

const isMenuOpen = ref(false);
const router = useRouter();
const route = useRoute();

const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value;
};

const logout = () => {
  localStorage.removeItem('admin_token');
  router.push('/admin/login');
};

const pageTitle = computed(() => {
  switch (route.name) {
    case 'admin-dashboard': return 'داشبورد';
    case 'admin-analytics': return 'آمار بازدید';
    case 'admin-posts': return 'مدیریت مطالب';
    case 'admin-posts-create': return 'ایجاد مطلب جدید';
    case 'admin-posts-edit': return 'ویرایش مطلب';
    case 'admin-comments': return 'مدیریت دیدگاه‌ها';
    case 'admin-comments-detail': return 'جزئیات دیدگاه';
    case 'admin-categories': return 'مدیریت دسته‌بندی‌ها';
    case 'admin-categories-create': return 'ایجاد دسته‌بندی جدید';
    case 'admin-categories-edit': return 'ویرایش دسته‌بندی';
    case 'admin-donations': return 'مدیریت حمایت‌های مالی';
    case 'admin-courses': return 'مدیریت دوره‌ها';
    case 'admin-courses-create': return 'ایجاد دوره جدید';
    case 'admin-courses-edit': return 'ویرایش دوره';
    case 'admin-courses-contents': return 'مدیریت محتوای دوره';
    case 'admin-students': return 'مدیریت دانشجویان';
    case 'admin-enrollments': return 'مدیریت ثبت‌نام‌ها';
    case 'admin-settings': return 'تنظیمات حساب کاربری';
    default: return 'پنل مدیریت';
  }
});

const formattedDate = computed(() => {
  const date = new Date();
  const options = { 
    weekday: 'long', 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
  };
  
  // Get the formatted date parts from Intl.DateTimeFormat
  const formatter = new Intl.DateTimeFormat('fa-IR', options);
  const parts = formatter.formatToParts(date);
  
  // Extract the individual parts
  let weekday = '';
  let day = '';
  let month = '';
  let year = '';
  
  parts.forEach(part => {
    if (part.type === 'weekday') weekday = part.value;
    if (part.type === 'day') day = part.value;
    if (part.type === 'month') month = part.value;
    if (part.type === 'year') year = part.value;
  });
  
  // Format according to the required pattern: شنبه، ۲۳ اردیبهشت ۱۴۰۴
  return `${weekday}، ${day} ${month} ${year}`;
});

onMounted(() => {
  // Check authentication when component mounts
  const token = localStorage.getItem('admin_token');
  if (!token) {
    router.push('/admin/login');
  }
});
</script> 