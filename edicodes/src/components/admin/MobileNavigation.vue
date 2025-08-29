<template>
  <nav class="flex-1 space-y-2">
    <router-link 
      to="/admin" 
      exact-active-class="bg-primary/20 text-primary"
      class="flex items-center py-2 px-3 text-sm text-white/90 rounded-lg hover:bg-white/5 transition-colors duration-150 font-vazir"
    >
      <font-awesome-icon icon="tachometer-alt" class="ml-2 h-5 w-5" />
      داشبورد
    </router-link>
    
    <router-link 
      to="/admin/analytics"
      :class="[$route.path.startsWith('/admin/analytics') ? 'bg-primary/20 text-primary' : '']"
      class="flex items-center py-2 px-3 text-sm text-white/90 rounded-lg hover:bg-white/5 transition-colors duration-150 font-vazir"
    >
      <font-awesome-icon icon="chart-line" class="ml-2 h-5 w-5" />
      آمار بازدید
    </router-link>
    
    <router-link 
      to="/admin/posts" 
      :class="[$route.path.startsWith('/admin/posts') ? 'bg-primary/20 text-primary' : '']"
      class="flex items-center py-2 px-3 text-sm text-white/90 rounded-lg hover:bg-white/5 transition-colors duration-150 font-vazir"
    >
      <font-awesome-icon icon="newspaper" class="ml-2 h-5 w-5" />
      مدیریت مطالب
    </router-link>
    
    <router-link 
      to="/admin/comments" 
      :class="[$route.path.startsWith('/admin/comments') ? 'bg-primary/20 text-primary' : '']"
      class="flex items-center py-2 px-3 text-sm text-white/90 rounded-lg hover:bg-white/5 transition-colors duration-150 font-vazir"
    >
      <font-awesome-icon icon="comments" class="ml-2 h-5 w-5" />
      مدیریت دیدگاه‌ها
    </router-link>
    
    <router-link 
      to="/admin/categories" 
      :class="[$route.path.startsWith('/admin/categories') ? 'bg-primary/20 text-primary' : '']"
      class="flex items-center py-2 px-3 text-sm text-white/90 rounded-lg hover:bg-white/5 transition-colors duration-150 font-vazir"
    >
      <font-awesome-icon icon="folder" class="ml-2 h-5 w-5" />
      دسته‌بندی‌ها
    </router-link>
    
    <router-link 
      to="/admin/courses"
      :class="[$route.path.startsWith('/admin/courses') ? 'bg-primary/20 text-primary' : '']" 
      class="flex items-center py-2 px-3 text-sm text-white/90 rounded-lg hover:bg-white/5 transition-colors duration-150 font-vazir"
    >
      <font-awesome-icon icon="graduation-cap" class="ml-2 h-5 w-5" />
      مدیریت دوره‌ها
    </router-link>
    
    <router-link 
      to="/admin/students"
      :class="[$route.path.startsWith('/admin/students') ? 'bg-primary/20 text-primary' : '']" 
      class="flex items-center py-2 px-3 text-sm text-white/90 rounded-lg hover:bg-white/5 transition-colors duration-150 font-vazir"
    >
      <font-awesome-icon icon="users" class="ml-2 h-5 w-5" />
      مدیریت دانشجویان
    </router-link>
    
    <router-link 
      to="/admin/enrollments"
      :class="[$route.path.startsWith('/admin/enrollments') ? 'bg-primary/20 text-primary' : '']" 
      class="flex items-center py-2 px-3 text-sm text-white/90 rounded-lg hover:bg-white/5 transition-colors duration-150 font-vazir"
    >
      <font-awesome-icon icon="user-graduate" class="ml-2 h-5 w-5" />
      ثبت‌نام‌ها
    </router-link>
    
    <router-link 
      to="/admin/donations" 
      :class="[$route.path.startsWith('/admin/donations') ? 'bg-primary/20 text-primary' : '']"
      class="flex items-center py-2 px-3 text-sm text-white/90 rounded-lg hover:bg-white/5 transition-colors duration-150 font-vazir"
    >
      <font-awesome-icon icon="donate" class="ml-2 h-5 w-5" />
      حمایت‌های مالی
    </router-link>
    
    <router-link 
      to="/admin/settings" 
      :class="[$route.path.startsWith('/admin/settings') ? 'bg-primary/20 text-primary' : '']"
      class="flex items-center py-2 px-3 text-sm text-white/90 rounded-lg hover:bg-white/5 transition-colors duration-150 font-vazir"
    >
      <font-awesome-icon icon="cog" class="ml-2 h-5 w-5" />
      تنظیمات
    </router-link>
    
    <a 
      href="/" 
      target="_blank" 
      class="flex items-center py-2 px-3 text-sm text-white/90 rounded-lg hover:bg-white/5 transition-colors duration-150 font-vazir"
    >
      <font-awesome-icon icon="globe" class="ml-2 h-5 w-5" />
      مشاهده سایت
    </a>
  </nav>
</template>

<script setup>
import { useRoute } from 'vue-router';

const route = useRoute();
</script> 