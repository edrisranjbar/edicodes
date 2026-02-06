<template>
  <section class="relative min-h-[90vh] flex items-center bg-dark">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        <!-- Text Content -->
        <div class="text-center lg:text-right space-y-8">
          <div class="space-y-4">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold font-vazir">
              <span class="text-primary">ادریس رنجبر</span>
            </h1>
            <h2 class="text-xl sm:text-2xl lg:text-3xl font-medium text-white/85 font-vazir">
              توسعه‌دهنده بک‌اند و هم‌بنیانگذار آی‌تدبیر 
              <span class="inline-block animate-bounce">👨‍💻</span>
            </h2>
          </div>
          
          <p class="text-base sm:text-lg text-white/60 leading-relaxed font-vazir max-w-2xl">
            سلام! <span class="inline-block animate-wave">👋</span> من عاشق برنامه‌نویسیم و 9 ساله که با PHP و Python سروکار دارم.
             اینجا تجربیاتم رو به اشتراک می‌ذارم و از آشنایی با برنامه‌نویس‌های جدید خوشحال میشم! 
            <span class="inline-block">✨</span>
          </p>
          
          <div class="flex flex-wrap gap-4 justify-center lg:justify-start pt-4">
            <a
              href="https://linkedin.com/in/edrisranjbar"
              target="_blank"
              class="btn-primary font-vazir flex items-center gap-2"
            >
              <font-awesome-icon :icon="['fab', 'linkedin']" />
              لینکدین من
            </a>
            <router-link
              to="/#blog"
              class="btn-secondary font-vazir flex items-center gap-2"
            >
              <font-awesome-icon icon="book" />
              مطالب وبلاگ
            </router-link>
          </div>
        </div>

        <!-- Profile Image -->
        <div class="relative order-first lg:order-last">
          <img
            src="@/assets/images/profile.png"
            alt="ادریس رنجبر"
            class="w-64 sm:w-80 md:w-96 lg:w-full max-w-lg mx-auto rounded-2xl shadow-xl"
            loading="eager"
          />
          <!-- Decorative Elements -->
          <div class="absolute -top-4 -right-4 w-20 sm:w-24 lg:w-24 h-20 sm:h-24 lg:h-24 bg-primary/20 rounded-full blur-xl"></div>
          <div class="absolute -bottom-4 -left-4 w-24 sm:w-28 lg:w-32 h-24 sm:h-28 lg:h-32 bg-secondary/20 rounded-full blur-xl"></div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.btn-primary {
  @apply px-6 py-3 bg-primary hover:bg-primary-dark text-white rounded-lg transition-colors duration-200 font-medium;
}

.btn-secondary {
  @apply px-6 py-3 bg-transparent hover:bg-white/10 text-white border border-white/20 hover:border-white/40 rounded-lg transition-all duration-200 font-medium;
}

@keyframes wave {
  0%, 100% { transform: rotate(0deg); }
  25% { transform: rotate(-10deg); }
  75% { transform: rotate(10deg); }
}

.animate-wave {
  animation: wave 1.5s infinite;
  transform-origin: bottom center;
  display: inline-block;
}
</style> 