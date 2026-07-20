<template>
  <component
    :is="to ? RouterLink : 'a'"
    v-bind="linkProps"
    :class="classes"
  >
    <slot />
  </component>
</template>

<script setup>
import { computed } from 'vue'
import { RouterLink } from 'vue-router'

const props = defineProps({
  to: { type: [String, Object], default: null },
  href: { type: String, default: null },
  variant: { type: String, default: 'link' }, // 'link' | 'badge'
  target: { type: String, default: null }
})

const linkProps = computed(() => {
  if (props.to) return { to: props.to }
  return { href: props.href || '#', target: props.target }
})

const classes = computed(() => {
  if (props.variant === 'badge') {
    return 'bg-primary/10 hover:bg-primary/20 text-primary text-xs px-3 py-1 rounded-md font-vazir transition-colors'
  }
  // default link
  return 'text-primary hover:text-primary-light transition-colors duration-150 font-vazir'
})
</script>

<style scoped>
/* nothing here - styling via Tailwind classes */
</style>
