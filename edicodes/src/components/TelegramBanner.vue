<template>
  <a
    href="https://t.me/edrisranjbar"
    target="_blank"
    rel="noopener noreferrer"
    class="telegram-banner fixed bottom-4 left-4 z-[100] flex items-center gap-2 bg-[#229ED9] text-white px-4 py-3 rounded-full shadow-lg hover:bg-[#1b8bc2] transition-all duration-300 hover:scale-105 group"
  >
    <font-awesome-icon :icon="['fab', 'telegram']" class="text-2xl shrink-0" />
    <span class="font-vazir text-sm font-medium pr-1 hidden sm:inline-block max-w-0 overflow-hidden group-hover:max-w-[10rem] transition-all duration-500 ease-in-out whitespace-nowrap">
      عضویت در کانال تلگرام
    </span>
  </a>
</template>

<script setup>
// Sticky Telegram banner – shown on all non-admin pages
</script>

<style scoped>
.telegram-banner {
  /* Ensure it stays above modals and nav */
}
</style>
