<template>
  <section v-if="videos.length" id="youtube" class="py-16">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
      <div class="mb-12 text-center">
        <div class="inline-flex items-center justify-center mb-4">
          <span class="flex items-center justify-center w-14 h-14 rounded-full bg-red-600 shadow-lg shadow-red-500/30">
            <svg viewBox="0 0 24 24" class="w-7 h-7 text-white" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
              <path d="M10 15.5L15.5 12L10 8.5V15.5Z" />
              <path fill-rule="evenodd" clip-rule="evenodd" d="M4 6.75C4 5.23122 5.23122 4 6.75 4H17.25C18.7688 4 20 5.23122 20 6.75V17.25C20 18.7688 18.7688 20 17.25 20H6.75C5.23122 20 4 18.7688 4 17.25V6.75ZM6.75 5.5C6.05964 5.5 5.5 6.05964 5.5 6.75V17.25C5.5 17.9404 6.05964 18.5 6.75 18.5H17.25C17.9404 18.5 18.5 17.9404 18.5 17.25V6.75C18.5 6.05964 17.9404 5.5 17.25 5.5H6.75Z" />
            </svg>
          </span>
        </div>
        <h2 class="text-4xl font-bold mb-4 text-white font-vazir">
          <span class="text-primary">ویدیوهای</span> یوتیوب
        </h2>
        <p class="max-w-3xl mx-auto text-white/70 font-vazir text-base mb-6">
          تازه‌ترین کلیپ‌های یوتیوب را در قالب یک کاروسل زیبا ببینید و مستقیما به صفحه ویدیو بروید.
        </p>
        <a
          :href="channelSubscribeUrl"
          target="_blank"
          rel="noopener noreferrer"
          class="inline-flex items-center justify-center gap-2 rounded-full border border-white/15 bg-red-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-red-500/20 transition hover:bg-red-700"
        >
          <svg viewBox="0 0 24 24" class="w-4 h-4" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 15.5L15.5 12L10 8.5V15.5Z" />
            <path fill-rule="evenodd" clip-rule="evenodd" d="M4 6.75C4 5.23122 5.23122 4 6.75 4H17.25C18.7688 4 20 5.23122 20 6.75V17.25C20 18.7688 18.7688 20 17.25 20H6.75C5.23122 20 4 18.7688 4 17.25V6.75ZM6.75 5.5C6.05964 5.5 5.5 6.05964 5.5 6.75V17.25C5.5 17.9404 6.05964 18.5 6.75 18.5H17.25C17.9404 18.5 18.5 17.9404 18.5 17.25V6.75C18.5 6.05964 17.9404 5.5 17.25 5.5H6.75Z" />
          </svg>
          اشتراک در یوتیوب
        </a>
      </div>

      <div class="rounded-3xl bg-black/40 border border-white/10 p-6">
        <div class="relative">
          <div ref="slider" class="flex gap-4 overflow-x-auto snap-x snap-mandatory scroll-smooth pb-2">
            <a
              v-for="video in videos"
              :key="video.id"
              :href="video.url"
              target="_blank"
              class="min-w-[280px] snap-center flex-shrink-0 rounded-3xl overflow-hidden bg-black/30 hover:shadow-2xl transition-shadow duration-300"
            >
              <div class="aspect-[16/9] overflow-hidden bg-black/20">
                <img :src="video.thumbnail" :alt="video.title" class="w-full h-full object-cover" loading="lazy" />
              </div>
              <div class="p-4">
                <div class="text-white text-base md:text-lg font-semibold font-vazir leading-6 max-h-12 overflow-hidden">
                  {{ video.title }}
                </div>
              </div>
            </a>
          </div>

          <button
            v-if="videos.length > 1"
            type="button"
            @click="scroll(-1)"
            class="absolute left-0 top-1/2 -translate-y-1/2 p-3 bg-black/70 text-white rounded-full shadow-xl hover:bg-black transition"
            aria-label="قبلی"
          >
            ‹
          </button>
          <button
            v-if="videos.length > 1"
            type="button"
            @click="scroll(1)"
            class="absolute right-0 top-1/2 -translate-y-1/2 p-3 bg-black/70 text-white rounded-full shadow-xl hover:bg-black transition"
            aria-label="بعدی"
          >
            ›
          </button>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'
import axios from 'axios'

const videos = ref([])
const slider = ref(null)
const channelSubscribeUrl = 'https://www.youtube.com/@edrisranjbar?sub_confirmation=1'

const loadVideos = async () => {
  try {
    const res = await axios.get(`${import.meta.env.VITE_API_BASE_URL}/youtube/carousel`)
    if (res.data && Array.isArray(res.data.videos)) {
      videos.value = res.data.videos
    }
  } catch (err) {
    videos.value = []
    console.debug('Failed to load YouTube carousel items', err)
  }
}

const scroll = (direction) => {
  if (!slider.value) return
  const width = slider.value.clientWidth
  slider.value.scrollBy({ left: direction * width * 0.95, behavior: 'smooth' })
}

onMounted(loadVideos)
</script>

<style scoped>
/* Tailwind utility classes handle the layout */
</style>
