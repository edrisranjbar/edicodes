<template>
  <div v-if="show" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
      <div class="mt-3">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-lg font-medium text-gray-900">پرداخت دوره</h3>
          <button
            @click="closeModal"
            class="text-gray-400 hover:text-gray-600"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- Course Info -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
          <div class="flex items-center space-x-4 rtl:space-x-reverse">
            <div class="flex-shrink-0">
              <img
                v-if="course?.thumbnail"
                :src="course.thumbnail"
                :alt="course?.title"
                class="w-16 h-16 rounded-lg object-cover"
              >
              <div v-else class="w-16 h-16 bg-indigo-100 rounded-lg flex items-center justify-center">
                <svg class="w-8 h-8 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                </svg>
              </div>
            </div>
            <div class="flex-1">
              <h4 class="text-lg font-medium text-gray-900">{{ course?.title }}</h4>
              <p class="text-sm text-gray-600">{{ course?.description }}</p>
              <div class="mt-2 flex items-center space-x-4 rtl:space-x-reverse text-sm text-gray-500">
                <span>{{ course?.duration }}</span>
                <span>{{ course?.level }}</span>
                <span>{{ course?.category?.name }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Payment Amount -->
        <div class="text-center mb-6">
          <div class="text-2xl font-bold text-gray-900">
            {{ paymentService.formatToman(course?.price || 0) }}
          </div>
          <p class="text-sm text-gray-600 mt-1">مبلغ قابل پرداخت</p>
        </div>

        <!-- Payment Methods -->
        <div v-if="!loading && !paymentCreated" class="mb-6">
          <h4 class="text-sm font-medium text-gray-700 mb-3">انتخاب روش پرداخت</h4>
          <div class="space-y-3">
            <div
              v-for="method in paymentMethods"
              :key="method.id"
              :class="[
                'border rounded-lg p-4 cursor-pointer transition-colors duration-150',
                selectedMethod?.id === method.id
                  ? 'border-indigo-500 bg-indigo-50'
                  : 'border-gray-300 hover:border-gray-400'
              ]"
              @click="selectPaymentMethod(method)"
            >
              <div class="flex items-center space-x-3 rtl:space-x-reverse">
                <input
                  type="radio"
                  :name="'payment_method'"
                  :value="method.id"
                  :checked="selectedMethod?.id === method.id"
                  class="text-indigo-600 focus:ring-indigo-500"
                >
                <div class="flex-1">
                  <div class="flex items-center space-x-3 rtl:space-x-reverse">
                    <img
                      v-if="method.logo"
                      :src="method.logo"
                      :alt="method.name"
                      class="w-8 h-8 object-contain"
                    >
                    <div>
                      <h5 class="text-sm font-medium text-gray-900">{{ method.name }}</h5>
                      <p class="text-xs text-gray-500">{{ method.description }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Payment Status -->
        <div v-if="paymentCreated" class="mb-6">
          <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center space-x-3 rtl:space-x-reverse">
              <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
              </svg>
              <div>
                <h5 class="text-sm font-medium text-green-800">درخواست پرداخت ایجاد شد</h5>
                <p class="text-sm text-green-700 mt-1">
                  شناسه تراکنش: {{ paymentData?.transaction_id }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="mb-6">
          <div class="flex items-center justify-center space-x-3 rtl:space-x-reverse">
            <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-sm text-gray-600">در حال پردازش...</span>
          </div>
        </div>

        <!-- Error Message -->
        <div v-if="error" class="mb-6">
          <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center space-x-3 rtl:space-x-reverse">
              <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
              </svg>
              <div>
                <h5 class="text-sm font-medium text-red-800">خطا در پردازش</h5>
                <p class="text-sm text-red-700 mt-1">{{ error }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-3 rtl:space-x-reverse">
          <button
            v-if="!paymentCreated"
            @click="closeModal"
            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            انصراف
          </button>
          
          <button
            v-if="!paymentCreated && selectedMethod && !loading"
            @click="createPayment"
            :disabled="!selectedMethod || loading"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
          >
            <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ loading ? 'در حال پردازش...' : 'پرداخت' }}
          </button>
          
          <button
            v-if="paymentCreated && paymentData?.payment_url"
            @click="redirectToPayment"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
          >
            <svg class="w-4 h-4 ml-2 rtl:ml-0 rtl:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
            </svg>
            ادامه پرداخت
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, onMounted, watch } from 'vue'
import { paymentService } from '@/services/paymentService'
import { toast } from 'vue3-toastify'

export default {
  name: 'PaymentModal',
  props: {
    show: {
      type: Boolean,
      default: false
    },
    course: {
      type: Object,
      required: true
    },
    enrollment: {
      type: Object,
      default: null
    }
  },
  emits: ['close', 'payment-success'],
  setup(props, { emit }) {
    const loading = ref(false)
    const error = ref('')
    const paymentMethods = ref([])
    const selectedMethod = ref(null)
    const paymentCreated = ref(false)
    const paymentData = ref(null)

    // Load payment methods on mount
    onMounted(async () => {
      try {
        const response = await paymentService.getPaymentMethods()
        paymentMethods.value = response.data.filter(method => method.enabled)
        if (paymentMethods.value.length > 0) {
          selectedMethod.value = paymentMethods.value[0]
        }
      } catch (err) {
        console.error('Error loading payment methods:', err)
        error.value = 'خطا در بارگذاری روش‌های پرداخت'
      }
    })

    // Reset state when modal opens
    watch(() => props.show, (newVal) => {
      if (newVal) {
        resetState()
      }
    })

    const resetState = () => {
      loading.value = false
      error.value = ''
      paymentCreated.value = false
      paymentData.value = null
      if (paymentMethods.value.length > 0) {
        selectedMethod.value = paymentMethods.value[0]
      }
    }

    const selectPaymentMethod = (method) => {
      selectedMethod.value = method
    }

    const createPayment = async () => {
      if (!selectedMethod.value) {
        error.value = 'لطفا روش پرداخت را انتخاب کنید'
        return
      }

      loading.value = true
      error.value = ''

      try {
        // If course is free, create enrollment directly
        if (props.course.price === 0) {
          // Handle free enrollment
          paymentCreated.value = true
          paymentData.value = {
            payment_required: false,
            message: 'ثبت نام در دوره رایگان با موفقیت انجام شد'
          }
          emit('payment-success', { enrollment: props.enrollment, free: true })
        } else {
          // Create payment request
          const response = await paymentService.createPayment(props.enrollment.id)
          
          if (response.success) {
            paymentCreated.value = true
            paymentData.value = response.data
            toast.success('درخواست پرداخت با موفقیت ایجاد شد')
          } else {
            error.value = response.message || 'خطا در ایجاد درخواست پرداخت'
          }
        }
      } catch (err) {
        console.error('Error creating payment:', err)
        error.value = 'خطا در ایجاد درخواست پرداخت'
      } finally {
        loading.value = false
      }
    }

    const redirectToPayment = () => {
      if (paymentData.value?.payment_url) {
        paymentService.redirectToPayment(paymentData.value.payment_url)
      }
    }

    const closeModal = () => {
      emit('close')
    }

    return {
      loading,
      error,
      paymentMethods,
      selectedMethod,
      paymentCreated,
      paymentData,
      paymentService,
      selectPaymentMethod,
      createPayment,
      redirectToPayment,
      closeModal
    }
  }
}
</script>



