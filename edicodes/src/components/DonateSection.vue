<template>
  <section id="donate" class="py-16 px-4 md:px-8 bg-black/40">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
      <div class="max-w-3xl mx-auto">
        <div class="text-center mb-8">
          <h2 class="text-4xl font-bold mb-4 font-vazir">
            <span class="text-primary">حمایت</span> مالی (دونیت)
          </h2>
          <p class="text-white/70 mb-8">
            با حمایت مالی از من، به توسعه پروژه‌های متن‌باز و تولید محتوای آموزشی کمک کنید.
          </p>
        </div>

        <!-- Success message -->
        <div v-if="showSuccess" class="bg-primary/10 border border-primary/30 text-primary p-4 rounded-lg mb-6">
          <p class="flex items-center justify-center gap-2">
            <font-awesome-icon icon="check-circle" />
            {{ successMessage }}
          </p>
        </div>

        <!-- Error message -->
        <div v-if="error" class="bg-red-500/10 border border-red-500/30 text-red-500 p-4 rounded-lg mb-6">
          <p class="flex items-center justify-center gap-2">
            <font-awesome-icon icon="exclamation-circle" />
            {{ error }}
          </p>
        </div>

        <div class="bg-black/40 rounded-2xl p-8 border border-white/5">
          <!-- Form inputs -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
              <input
                type="text"
                v-model="name"
                placeholder="نام (اختیاری)"
                class="w-full bg-black/40 border border-white/10 rounded-lg px-4 py-3 text-white placeholder-white/30 focus:outline-none focus:border-primary transition-colors"
              />
            </div>
            <div>
              <input
                type="email"
                v-model="email"
                placeholder="ایمیل (اختیاری)"
                class="w-full bg-black/40 border border-white/10 rounded-lg px-4 py-3 text-white placeholder-white/30 focus:outline-none focus:border-primary transition-colors"
                dir="ltr"
              />
            </div>
          </div>
          
          <div class="mb-6">
            <textarea
              v-model="message"
              placeholder="پیام شما (اختیاری)"
              rows="2"
              class="w-full bg-black/40 border border-white/10 rounded-lg px-4 py-3 text-white placeholder-white/30 focus:outline-none focus:border-primary transition-colors"
            ></textarea>
          </div>

          <!-- Amount Selection -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <button
              @click="selectAmount(50000)"
              :class="[
                'p-4 rounded-lg border transition-all duration-200 text-lg font-medium',
                amount === 50000
                  ? 'bg-primary/10 border-primary text-primary'
                  : 'border-white/5 text-white/70 hover:border-primary/50'
              ]"
            >
              ۵۰,۰۰۰ تومان
            </button>
            <button
              @click="selectAmount(250000)"
              :class="[
                'p-4 rounded-lg border transition-all duration-200 text-lg font-medium relative',
                amount === 250000
                  ? 'bg-primary/10 border-primary text-primary'
                  : 'border-white/5 text-white/70 hover:border-primary/50'
              ]"
            >
              ۲۵۰,۰۰۰ تومان
              <span class="absolute -top-2 right-2 text-xs bg-primary text-white px-2 py-0.5 rounded-full">پیشنهادی</span>
            </button>
            <button
              @click="selectAmount(1000000)"
              :class="[
                'p-4 rounded-lg border transition-all duration-200 text-lg font-medium',
                amount === 1000000
                  ? 'bg-primary/10 border-primary text-primary'
                  : 'border-white/5 text-white/70 hover:border-primary/50'
              ]"
            >
              ۱,۰۰۰,۰۰۰ تومان
            </button>
          </div>

          <!-- Amount Input -->
          <div class="mb-8">
            <div class="relative">
              <input
                type="number"
                v-model="amount"
                placeholder="مبلغ دلخواه (تومان)"
                class="w-full bg-black/40 border border-white/10 rounded-lg px-4 py-3 text-white placeholder-white/30 focus:outline-none focus:border-primary transition-colors text-center"
                min="10000"
                step="10000"
                @input="validateAmount"
              />
            </div>
            <p v-if="amountError" class="text-red-500 text-sm mt-1">{{ amountError }}</p>
          </div>

          <!-- Payment Button -->
          <button
            @click="handleDonate"
            class="w-full bg-primary hover:bg-primary-dark text-white font-medium py-4 px-8 rounded-lg transition-colors duration-200 flex items-center justify-center gap-3 text-lg disabled:opacity-50 disabled:cursor-not-allowed"
            :disabled="!isValidAmount || isLoading"
          >
            <font-awesome-icon v-if="isLoading" icon="circle-notch" class="animate-spin" />
            <font-awesome-icon v-else icon="credit-card" />
            {{ isLoading ? 'در حال اتصال به درگاه...' : 'حمایت کنید' }}
          </button>
        </div>

        <!-- Donors List -->
        <div class="mt-20">
          <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center gap-3 mb-3">
              <div class="w-1 h-8 bg-gradient-to-b from-transparent via-primary to-transparent rounded-full"></div>
              <h3 class="text-3xl font-bold text-white font-vazir flex items-center gap-3">
                <font-awesome-icon icon="heart" class="text-primary animate-pulse" />
                حامیان ما
              </h3>
              <div class="w-1 h-8 bg-gradient-to-b from-transparent via-primary to-transparent rounded-full"></div>
            </div>
            <p class="text-white/60 text-base max-w-2xl mx-auto leading-relaxed">
              از تمامی حامیان عزیز که در توسعه پروژه‌های متن‌باز و تولید محتوای آموزشی همراه ما هستند، صمیمانه سپاسگزاریم
            </p>
          </div>

          <!-- Loading State -->
          <div v-if="donorsLoading" class="flex justify-center py-12">
            <div class="w-10 h-10 border-4 border-primary/30 border-t-primary rounded-full animate-spin"></div>
          </div>

          <!-- Error State -->
          <div v-else-if="donorsError" class="text-center py-12">
            <font-awesome-icon icon="exclamation-triangle" class="text-red-500/50 text-3xl mb-3" />
            <p class="text-red-500/70 text-sm">خطا در بارگذاری لیست حامیان</p>
          </div>

          <!-- Empty State -->
          <div v-else-if="donors.length === 0" class="text-center py-12">
            <font-awesome-icon icon="heart" class="text-white/20 text-4xl mb-3" />
            <p class="text-white/50 text-sm">هنوز حامیانی ثبت نشده است</p>
          </div>

          <!-- Donors Grid -->
          <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div
              v-for="donor in donors"
              :key="donor.id"
              class="relative bg-gradient-to-br from-black/40 via-black/30 to-black/40 border border-white/10 rounded-2xl p-6 hover:border-primary/40 hover:shadow-lg hover:shadow-primary/5 transition-all duration-300 group overflow-hidden"
            >
              <!-- Decorative gradient overlay -->
              <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-primary/5 via-transparent to-purple-600/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
              
              <!-- Content -->
              <div class="relative z-10">
                <!-- Donor Header -->
                <div class="flex items-start justify-between mb-4">
                  <div class="flex items-center gap-3 flex-1">
                    <div class="relative">
                      <div class="w-12 h-12 rounded-full bg-gradient-to-br from-primary/30 to-purple-600/30 flex items-center justify-center border border-primary/30 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                        <font-awesome-icon icon="user" class="text-primary text-base" />
                      </div>
                      <!-- Decorative ring -->
                      <div class="absolute inset-0 rounded-full border-2 border-primary/20 scale-125 opacity-0 group-hover:opacity-100 group-hover:scale-150 transition-all duration-500"></div>
                    </div>
                    <div class="flex-1 min-w-0">
                      <h4 class="text-white font-vazir font-semibold text-base truncate">{{ donor.name }}</h4>
                      <div class="flex items-center gap-2 mt-1">
                        <font-awesome-icon icon="clock" class="text-white/30 text-xs" />
                        <p class="text-white/50 text-xs">{{ donor.created_at_human }}</p>
                      </div>
                    </div>
                  </div>
                  <div class="text-left mr-2">
                    <span class="text-primary text-base font-vazir">{{ donor.formatted_amount }}</span>
                  </div>
                </div>

                <!-- Donor Message -->
                <div v-if="donor.message" class="mt-4 pt-4 border-t border-white/10">
                  <div class="flex items-start gap-2">
                    <font-awesome-icon icon="quote-right" class="text-primary/40 text-xs mt-1 flex-shrink-0" />
                    <p class="text-white/70 text-sm leading-relaxed font-vazir" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                      {{ donor.message }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Load More Button -->
          <div v-if="hasMoreDonors" class="text-center mt-10">
            <button
              @click="loadMoreDonors"
              :disabled="loadingMore"
              class="group relative bg-gradient-to-r from-black/50 to-black/40 hover:from-primary/20 hover:to-purple-600/20 border border-white/10 hover:border-primary/40 text-white px-8 py-3.5 rounded-xl transition-all duration-300 font-vazir disabled:opacity-50 disabled:cursor-not-allowed overflow-hidden"
            >
              <!-- Shine effect on hover -->
              <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000"></div>
              
              <span class="relative flex items-center justify-center gap-2">
                <font-awesome-icon v-if="loadingMore" icon="circle-notch" class="animate-spin" />
                <font-awesome-icon v-else icon="chevron-down" class="text-primary group-hover:translate-y-1 transition-transform duration-300" />
                <span>{{ loadingMore ? 'در حال بارگذاری...' : 'نمایش حامیان بیشتر' }}</span>
              </span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const amount = ref(250000)
const name = ref('')
const email = ref('')
const message = ref('')
const isLoading = ref(false)
const error = ref('')
const amountError = ref('')
const showSuccess = ref(false)
const successMessage = ref('')

// Donors state
const donors = ref([])
const donorsLoading = ref(false)
const donorsError = ref(false)
const currentPage = ref(1)
const hasMoreDonors = ref(false)
const loadingMore = ref(false)

const isValidAmount = computed(() => {
  return Number(amount.value) >= 1000 && !amountError.value
})

const selectAmount = (value) => {
  amount.value = value
  amountError.value = ''
}

const validateAmount = () => {
  amountError.value = ''
  
  if (!amount.value) {
    amountError.value = 'لطفاً مبلغ را وارد کنید'
    return
  }
  
  if (Number(amount.value) < 1000) {
    amountError.value = 'حداقل مبلغ ۱۰۰۰ تومان است'
    amount.value = 1000
    return
  }
}

const handleDonate = async () => {
  if (!isValidAmount.value || isLoading.value) return
  
  error.value = ''
  showSuccess.value = false
  isLoading.value = true
  
  try {
    const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || 'https://api.edrisranjbar.ir';
    const response = await fetch(`${apiBaseUrl}/donations/pay`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        amount: amount.value,
        name: name.value,
        email: email.value, 
        message: message.value
      })
    });
    
    const data = await response.json();
    
    if (response.ok && data.success) {
      // Store donation ID in localStorage for future reference if needed
      if (data.donation_id) {
        localStorage.setItem('current_donation_id', data.donation_id);
      }
      
      // Redirect to payment gateway
      window.location.href = data.payment_url;
    } else {
      error.value = data.message || 'خطا در اتصال به درگاه پرداخت. لطفاً مجدد تلاش کنید.';
      
      if (data.errors && data.errors.amount) {
        amountError.value = data.errors.amount[0];
      }
    }
  } catch (err) {
    error.value = 'خطا در اتصال به سرور. لطفاً مجدد تلاش کنید.';
    console.error('Donation error:', err);
  } finally {
    isLoading.value = false;
  }
}

// Fetch donors from API
const fetchDonors = async (page = 1) => {
  try {
    if (page === 1) {
      donorsLoading.value = true
    } else {
      loadingMore.value = true
    }
    donorsError.value = false
    
    const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || 'https://api.edrisranjbar.ir'
    const response = await fetch(`${apiBaseUrl}/donations?per_page=8&page=${page}`)
    
    if (!response.ok) {
      throw new Error('Failed to fetch donors')
    }
    
    const result = await response.json()
    
    if (result.success && result.data) {
      if (page === 1) {
        donors.value = result.data.data
      } else {
        donors.value = [...donors.value, ...result.data.data]
      }
      
      currentPage.value = result.data.current_page
      hasMoreDonors.value = result.data.current_page < result.data.last_page
    }
  } catch (err) {
    console.error('Error fetching donors:', err)
    donorsError.value = true
  } finally {
    donorsLoading.value = false
    loadingMore.value = false
  }
}

// Load more donors
const loadMoreDonors = () => {
  fetchDonors(currentPage.value + 1)
}

// Fetch donors on mount
onMounted(() => {
  fetchDonors()
})
</script>

<style scoped>
.donate-card {
  @apply bg-white/5 rounded-lg border border-white/10 hover:border-primary/30 transition-all duration-200;
}

.btn-primary {
  @apply inline-block px-6 py-3 bg-primary hover:bg-primary-dark text-white rounded-lg transition-colors duration-200 font-medium;
}
</style> 