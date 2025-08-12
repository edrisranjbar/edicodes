<template>
  <nav class="fixed top-0 left-0 right-0 z-50 bg-black/30 backdrop-blur-2xl border-b border-white/10 shadow-lg shadow-black/5" dir="rtl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center h-20">
        <!-- Logo -->
        <div class="flex-shrink-0">
          <router-link to="/" class="flex items-center">
            <img src="@/assets/images/edicodes.png" alt="ادی کدز" class="h-8 w-auto" />
          </router-link>
        </div>

        <!-- Desktop Menu -->
        <div class="hidden md:flex items-center gap-2 mr-8">
          <router-link 
            to="/" 
            class="nav-link"
          >
            <font-awesome-icon icon="home" class="ml-1.5" />
            خانه
          </router-link>
          <router-link 
            to="/blog" 
            class="nav-link" 
            :class="{ 'active': $route.path.includes('/blog') }"
          >
            <font-awesome-icon icon="book" class="ml-1.5" />
            وبلاگ
          </router-link>
          <router-link 
            to="/#projects" 
            class="nav-link" 
            :class="{ 'active': $route.hash === '#projects' }"
          >
            <font-awesome-icon icon="code" class="ml-1.5" />
            پروژه‌های آزاد
          </router-link>
          <router-link 
            to="/#youtube" 
            class="nav-link" 
            :class="{ 'active': $route.hash === '#youtube' }"
            @click="openYouTube"
          >
            <font-awesome-icon :icon="['fab', 'youtube']" class="ml-1.5" />
            یوتیوب
          </router-link>
          <router-link 
            to="/donation" 
            class="nav-link" 
            :class="{ 'active': $route.path === '/donation' }"
          >
            <font-awesome-icon icon="heart" class="ml-1.5" />
            حمایت
          </router-link>
        </div>

        <!-- Mobile menu button -->
        <div class="md:hidden mr-auto">
          <button 
            @click="isOpen = !isOpen" 
            class="w-10 h-10 flex items-center justify-center rounded-lg bg-white/5 hover:bg-white/10 transition-colors backdrop-blur-xl border border-white/10"
          >
            <span class="sr-only">باز کردن منو</span>
            <font-awesome-icon 
              :icon="isOpen ? 'xmark' : 'bars'" 
              class="text-white/70 text-xl"
            />
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile menu -->
    <transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="transform -translate-y-full opacity-0"
      enter-to-class="transform translate-y-0 opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="transform translate-y-0 opacity-100"
      leave-to-class="transform -translate-y-full opacity-0"
    >
      <div v-if="isOpen" class="md:hidden bg-black/30 backdrop-blur-2xl border-b border-white/10">
        <div class="px-4 py-3 space-y-1">
          <router-link to="/" class="mobile-nav-link" @click="isOpen = false">
            <font-awesome-icon icon="home" class="ml-2" />
            خانه
          </router-link>
          <router-link to="/blog" class="mobile-nav-link" @click="isOpen = false">
            <font-awesome-icon icon="book" class="ml-2" />
            وبلاگ
          </router-link>
          <router-link to="/#projects" class="mobile-nav-link" @click="isOpen = false">
            <font-awesome-icon icon="code" class="ml-2" />
            پروژه‌های آزاد
          </router-link>
          <router-link 
            to="/#youtube" 
            class="mobile-nav-link" 
            @click="openYouTubeAndCloseMenu"
          >
            <font-awesome-icon :icon="['fab', 'youtube']" class="ml-2" />
            یوتیوب
          </router-link>
          <router-link to="/donation" class="mobile-nav-link" @click="isOpen = false">
            <font-awesome-icon icon="heart" class="ml-2" />
            حمایت
          </router-link>
        </div>
      </div>
    </transition>
  </nav>
</template>

<script setup>
import { ref } from 'vue'

const isOpen = ref(false)

const openYouTube = () => {
  window.open('https://www.youtube.com/@edrisranjbar', '_blank')
}

const openYouTubeAndCloseMenu = () => {
  openYouTube()
  isOpen.value = false
}
</script>

<style scoped>
.nav-link {
  @apply px-4 py-2 text-white/80 hover:text-white rounded-lg transition-all duration-200 text-sm font-medium flex items-center;
  @apply hover:bg-white/5 hover:backdrop-blur-xl hover:border hover:border-white/10 hover:shadow-lg hover:shadow-black/5;
}

.nav-link.active {
  @apply text-primary bg-white/5 backdrop-blur-xl border border-white/10 shadow-lg shadow-black/5;
}

.nav-link-special {
  @apply px-4 py-2 text-primary bg-primary/10 backdrop-blur-xl border border-primary/20 rounded-lg transition-all duration-200 text-sm font-medium flex items-center;
  @apply hover:bg-primary/20 hover:border-primary/30 hover:shadow-lg hover:shadow-primary/10;
}

.mobile-nav-link {
  @apply flex items-center px-4 py-3 text-base font-medium text-white/80 hover:text-white rounded-lg transition-all duration-200;
  @apply hover:bg-white/5 hover:backdrop-blur-xl hover:border hover:border-white/10;
}
</style> 