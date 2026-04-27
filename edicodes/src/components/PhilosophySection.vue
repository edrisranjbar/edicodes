<template>
  <section class="py-16 px-4 md:px-8 bg-white/5">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
      <div class="max-w-4xl mx-auto">
        <div class="text-center mb-12">
          <h2 class="text-4xl font-bold mb-4 font-vazir">
            <span class="text-primary">فلسفه</span> کاری من
          </h2>
          <p class="text-white/70 max-w-3xl mx-auto">
            اصول و ارزش‌هایی که در توسعه و طراحی نرم‌افزار به آن‌ها پایبند هستم.
          </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Clean Architecture -->
          <div class="bg-white/5 rounded-lg p-6 border border-white/10 hover:border-primary/50 transition-colors group">
            <div class="w-12 h-12 bg-primary/20 rounded-lg flex items-center justify-center mb-4 group-hover:bg-primary/30 transition-colors">
              <font-awesome-icon icon="code" class="text-2xl text-primary" />
            </div>
            <h3 class="text-xl font-bold text-white mb-3">توسعه چابک</h3>
            <p class="text-white/70">
از مهم‌ترین اصول کاری من پایبندی به توسعه چابک نرم افزار و فاز بندی پروژه ها در قالب قدم های قابل اجرا برای کاهش هزینه و افزایش  بهره‌وری و رسیدن سریع‌تر به خروجی قابل استفاده در پروژه هاست.
            </p>
          </div>

          <!-- Performance First -->
          <div class="bg-white/5 rounded-lg p-6 border border-white/10 hover:border-orange-400/50 transition-colors group">
            <div class="w-12 h-12 bg-orange-400/20 rounded-lg flex items-center justify-center mb-4 group-hover:bg-orange-400/30 transition-colors">
              <font-awesome-icon icon="bolt" class="text-2xl text-orange-400" />
            </div>
            <h3 class="text-xl font-bold text-orange-400 mb-3">عملکرد محور</h3>
            <p class="text-white/70">
              بهینه‌سازی عملکرد و سرعت با استفاده از بهترین شیوه‌ها و تکنیک‌های برنامه‌نویسی، در عین حال پیشگیری از پیچیدگی بی‌مورد در ساختار پروژه. تلاش خودم رو میکنم که پروژه قابل نگهداری و توسعه باشد و تا حد ممکن اصول DRY و معماری تمیز رو رعایت کنم.
            </p>
          </div>

          <!-- Security Focused -->
          <div class="bg-white/5 rounded-lg p-6 border border-white/10 hover:border-primary/50 transition-colors group">
            <div class="w-12 h-12 bg-primary/20 rounded-lg flex items-center justify-center mb-4 group-hover:bg-primary/30 transition-colors">
              <font-awesome-icon icon="shield" class="text-2xl text-primary" />
            </div>
            <h3 class="text-xl font-bold text-white mb-3">امنیت محور</h3>
            <p class="text-white/70">
              امنیت نسبی است، اما من همیشه سعی می‌کنم تا بالاترین استانداردهای امنیتی را در پروژه‌هایم رعایت کنم.
              امنیت برای من یکی از ستون‌های اصلی هر پروژه‌ای هست. از رمزنگاری داده‌ها و احراز هویت قوی گرفته تا جلوگیری از تزریق SQL و حملات XSS، همیشه سعی می‌کنم تا جزئیات را در نظر بگیرم. محافظت از اطلاعات حساس کاربران و رعایت استانداردهای امنیتی برای من ضروری است. کدهام رو قبل از تکمیل پروژه بازبینی می‌کنم تا مطمئن شوم هیچ نقطه ضعفی وجود ندارد.
            </p>
          </div>
          
          <!-- Fun and Challenging -->
          <div class="bg-white/5 rounded-lg p-6 border border-white/10 hover:border-orange-400/50 transition-colors group">
            <div class="w-12 h-12 bg-orange-400/20 rounded-lg flex items-center justify-center mb-4 group-hover:bg-orange-400/30 transition-colors">
              <font-awesome-icon icon="rocket" class="text-2xl text-orange-400" />
            </div>
            <h3 class="text-xl font-bold text-orange-400 mb-3">لذت و چالش</h3>
            <p class="text-white/70">
              اگر کار جذاب و چالش‌برانگیز نباشه، انجامش نمی‌دم! کدنویسی برای من فقط یک شغل نیست، بلکه عشق و هنره
              من به دنبال پروژه‌هایی هستم که نه تنها مهارت‌هایم را به چالش بکشند، بلکه به من اجازه دهند خلاقیت خود را نیز ابراز کنم. برای من، لذت بردن از فرآیند توسعه و دیدن تأثیر مثبت کارم بر کاربران نهایی بسیار مهم است. هر پروژه‌ای که انتخاب می‌کنم باید فرصتی برای یادگیری و رشد فراهم کند.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.philosophy-card {
  @apply bg-white/5 p-8 rounded-lg border border-white/10 hover:border-primary/30 transition-all duration-200;
}
</style> 