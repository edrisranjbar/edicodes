<template>
  <div class="pt-16" dir="rtl">
    <HeroSection />
    <JourneySection />
    <AboutMeSection />
    <ProjectsSection />
    <PhilosophySection />
    <BlogSection />
    <ContactSection />
    <DonateSection />
  </div>
</template>

<script setup>
import BlogSection from '@/components/BlogSection.vue'
import DonateSection from '@/components/DonateSection.vue'
import HeroSection from '@/components/HeroSection.vue'
import JourneySection from '@/components/JourneySection.vue'
import PhilosophySection from '@/components/PhilosophySection.vue'
import ExperienceSection from '@/components/ExperienceSection.vue'
import ContactSection from '@/components/ContactSection.vue'
import AboutMeSection from '@/components/AboutMeSection.vue'
import ProjectsSection from '@/components/ProjectsSection.vue'
import analyticsService from '@/services/analyticsService'
import { onMounted } from 'vue'

onMounted(() => {
  // Record a page view for the homepage
  analyticsService.recordGenericPageView('/')
    .catch(err => console.error('Error recording page view:', err))
})
</script>

<style scoped>
.btn-primary {
  @apply px-6 py-3 bg-primary hover:bg-primary-dark text-white rounded-lg transition-colors duration-200 font-medium;
}

.btn-secondary {
  @apply px-6 py-3 bg-transparent hover:bg-white/10 text-white border border-white/20 hover:border-white/40 rounded-lg transition-all duration-200 font-medium;
}
</style> 