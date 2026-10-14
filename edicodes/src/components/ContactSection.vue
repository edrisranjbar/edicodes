<template>
  <section class="py-16 px-4 md:px-8 bg-black/40">
    <div class="container mx-auto max-w-6xl">
      <div class="grid md:grid-cols-2 gap-8">
        <!-- Contact Form -->
        <div class="bg-black/40 p-8 rounded-2xl border border-white/5">
          <h2 class="text-3xl font-bold mb-8 text-white">ارسال پیام</h2>
          
          <!-- Success Message -->
          <div v-if="showSuccess" class="mb-6 p-4 bg-primary/10 border border-primary/20 rounded-lg">
            <p class="text-primary flex items-center gap-2">
              <font-awesome-icon icon="check-circle" />
              پیام شما با موفقیت ارسال شد. در اسرع وقت پاسخ خواهم داد.
            </p>
          </div>

          <!-- Error Message -->
          <div v-if="error" class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-lg">
            <p class="text-red-500 flex items-center gap-2">
              <font-awesome-icon icon="exclamation-circle" />
              {{ error }}
            </p>
          </div>

          <form @submit.prevent="handleSubmit" class="space-y-6">
            <div class="grid md:grid-cols-2 gap-4">
              <div>
                <label for="name" class="block text-sm font-medium text-white/70 mb-2">نام شما <span class="text-primary">*</span></label>
                <input
                  type="text"
                  id="name"
                  v-model="form.name"
                  :class="{'border-red-500/50': errors.name}"
                  class="w-full px-4 py-3 bg-black/40 border border-white/10 rounded-lg text-white placeholder-white/30 focus:outline-none focus:border-primary transition-colors"
                  placeholder="نام و نام خانوادگی"
                  :disabled="isLoading"
                />
                <p v-if="errors.name" class="mt-1 text-sm text-red-500">{{ errors.name }}</p>
              </div>
              <div>
                <label for="email" class="block text-sm font-medium text-white/70 mb-2">ایمیل شما <span class="text-primary">*</span></label>
                <input
                  type="email"
                  id="email"
                  v-model="form.email"
                  :class="{'border-red-500/50': errors.email}"
                  class="w-full px-4 py-3 bg-black/40 border border-white/10 rounded-lg text-white placeholder-white/30 focus:outline-none focus:border-primary transition-colors"
                  placeholder="example@domain.com"
                  dir="ltr"
                  :disabled="isLoading"
                />
                <p v-if="errors.email" class="mt-1 text-sm text-red-500">{{ errors.email }}</p>
              </div>
            </div>
            <div>
              <label for="subject" class="block text-sm font-medium text-white/70 mb-2">موضوع <span class="text-primary">*</span></label>
              <input
                type="text"
                id="subject"
                v-model="form.subject"
                :class="{'border-red-500/50': errors.subject}"
                class="w-full px-4 py-3 bg-black/40 border border-white/10 rounded-lg text-white placeholder-white/30 focus:outline-none focus:border-primary transition-colors"
                placeholder="موضوع پیام"
                :disabled="isLoading"
              />
              <p v-if="errors.subject" class="mt-1 text-sm text-red-500">{{ errors.subject }}</p>
            </div>
            <div>
              <label for="message" class="block text-sm font-medium text-white/70 mb-2">پیام <span class="text-primary">*</span></label>
              <textarea
                id="message"
                v-model="form.message"
                :class="{'border-red-500/50': errors.message}"
                rows="4"
                class="w-full px-4 py-3 bg-black/40 border border-white/10 rounded-lg text-white placeholder-white/30 focus:outline-none focus:border-primary transition-colors"
                placeholder="پیام خود را بنویسید..."
                :disabled="isLoading"
              ></textarea>
              <p v-if="errors.message" class="mt-1 text-sm text-red-500">{{ errors.message }}</p>
            </div>
            <button
              type="submit"
              :disabled="isLoading"
              class="w-full bg-primary hover:bg-primary-dark text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <font-awesome-icon v-if="isLoading" icon="circle-notch" class="animate-spin" />
              <font-awesome-icon v-else icon="paper-plane" />
              {{ isLoading ? 'در حال ارسال...' : 'ارسال پیام' }}
            </button>
          </form>
        </div>

        <!-- Contact Information -->
        <div class="space-y-8">
          <div class="bg-black/40 p-8 rounded-2xl border border-white/5">
            <h2 class="text-3xl font-bold mb-8 text-white">اطلاعات تماس</h2>
            <div class="space-y-6">
              <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center text-primary">
                  <font-awesome-icon icon="envelope" size="lg" />
                </div>
                <div>
                  <h3 class="text-white/70 text-sm mb-1">ایمیل</h3>
                  <a href="mailto:edrisranjbar.dev@gmail.com" class="text-white hover:text-primary transition-colors" dir="ltr">
                    edrisranjbar.dev@gmail.com
                  </a>
                </div>
              </div>
              <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center text-primary">
                  <font-awesome-icon icon="location-dot" size="lg" />
                </div>
                <div>
                  <h3 class="text-white/70 text-sm mb-1">موقعیت</h3>
                  <p class="text-white">آماده همکاری به صورت دورکاری در سراسر دنیا</p>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-black/40 p-8 rounded-2xl border border-white/5">
            <h2 class="text-3xl font-bold mb-4 text-white">همکاری کنیم</h2>
            <p class="text-white/70 mb-8">
              همیشه آماده شنیدن ایده‌های جدید، پروژه‌های خلاقانه و فرصت‌های همکاری هستم.
            </p>
            <div class="bg-black/40 p-4 rounded-lg border border-primary/20">
              <h3 class="text-primary font-semibold mb-2">در حال حاضر در دسترس</h3>
              <p class="text-white/70">
                آماده همکاری به صورت فریلنسری، قراردادی یا تمام وقت هستم. بیایید با هم چیز فوق‌العاده‌ای بسازیم.
                <a href="/donation" class="text-primary hover:text-primary-dark transition-colors mr-1">
                  از طریق حمایت مالی
                </a>
                از من پشتیبانی کنید.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue'

const form = ref({
  name: '',
  email: '',
  subject: '',
  message: ''
})

const isLoading = ref(false)
const showSuccess = ref(false)
const error = ref('')
const errors = ref({})

const validateForm = () => {
  errors.value = {}
  
  if (!form.value.name.trim()) {
    errors.value.name = 'لطفاً نام خود را وارد کنید'
  }
  
  if (!form.value.email.trim()) {
    errors.value.email = 'لطفاً ایمیل خود را وارد کنید'
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.value.email)) {
    errors.value.email = 'لطفاً یک ایمیل معتبر وارد کنید'
  }
  
  if (!form.value.subject.trim()) {
    errors.value.subject = 'لطفاً موضوع پیام را وارد کنید'
  }
  
  if (!form.value.message.trim()) {
    errors.value.message = 'لطفاً پیام خود را وارد کنید'
  }
  
  return Object.keys(errors.value).length === 0
}

const sendContactForm = async (formData) => {
  try {
    const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || 'https://api.edrisranjbar.ir';
    const response = await fetch(`${apiBaseUrl}/send-message`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(formData)
    });
    
    const data = await response.json();
    
    if (!response.ok) {
      // Handle validation errors from the server
      if (response.status === 422 && data.errors) {
        // Map backend errors to our frontend errors object
        const serverErrors = {};
        if (data.errors.name) serverErrors.name = data.errors.name[0];
        if (data.errors.email) serverErrors.email = data.errors.email[0];
        if (data.errors.subject) serverErrors.subject = data.errors.subject[0];
        if (data.errors.message) serverErrors.message = data.errors.message[0];
        
        return { 
          success: false, 
          error: data.message || 'لطفاً تمام فیلدها را به درستی پر کنید',
          validationErrors: serverErrors
        };
      }
      
      throw new Error(data.message || 'خطا در ارسال فرم');
    }
    
    return { 
      success: true 
    };
  } catch (error) {
    return { 
      success: false, 
      error: error.message || 'متأسفانه در ارسال پیام خطایی رخ داد. لطفاً دوباره تلاش کنید.' 
    };
  }
}

const handleSubmit = async () => {
  if (isLoading.value) return
  
  error.value = ''
  showSuccess.value = false
  
  if (!validateForm()) return
  
  isLoading.value = true
  
  try {
    const response = await sendContactForm({
      name: form.value.name,
      email: form.value.email,
      subject: form.value.subject,
      message: form.value.message,
      timestamp: new Date().toISOString()
    });
    
    if (response.success) {
      showSuccess.value = true
      form.value = {
        name: '',
        email: '',
        subject: '',
        message: ''
      }
    } else {
      error.value = response.error
      
      // Update validation errors from server if they exist
      if (response.validationErrors) {
        errors.value = response.validationErrors
      }
    }
  } catch (err) {
    error.value = 'متأسفانه در ارسال پیام خطایی رخ داد. لطفاً دوباره تلاش کنید.'
  } finally {
    isLoading.value = false
  }
}
</script> 