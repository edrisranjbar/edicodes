import axios from 'axios'

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://127.0.0.1:8000/api'

class PaymentService {
  constructor() {
    this.api = axios.create({
      baseURL: API_BASE_URL,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    })
    
    // Add auth token to requests if available
    this.api.interceptors.request.use((config) => {
      const token = localStorage.getItem('auth_token')
      if (token) {
        config.headers.Authorization = `Bearer ${token}`
      }
      return config
    })
  }

  /**
   * Get available payment methods
   */
  async getPaymentMethods() {
    try {
      const response = await this.api.get('/payment/methods')
      return response.data
    } catch (error) {
      console.error('Error fetching payment methods:', error)
      throw error
    }
  }

  /**
   * Create payment request for enrollment
   */
  async createPayment(enrollmentId) {
    try {
      const response = await this.api.post(`/enrollments/${enrollmentId}/create-payment`)
      return response.data
    } catch (error) {
      console.error('Error creating payment:', error)
      throw error
    }
  }

  /**
   * Check payment status
   */
  async checkPaymentStatus(enrollmentId) {
    try {
      const response = await this.api.get(`/enrollments/${enrollmentId}/payment-status`)
      return response.data
    } catch (error) {
      console.error('Error checking payment status:', error)
      throw error
    }
  }

  /**
   * Process refund
   */
  async refundPayment(enrollmentId, reason = null) {
    try {
      const data = reason ? { reason } : {}
      const response = await this.api.post(`/enrollments/${enrollmentId}/refund`, data)
      return response.data
    } catch (error) {
      console.error('Error processing refund:', error)
      throw error
    }
  }

  /**
   * Redirect to payment gateway
   */
  redirectToPayment(paymentUrl) {
    if (paymentUrl) {
      window.location.href = paymentUrl
    }
  }

  /**
   * Handle payment callback
   */
  async handlePaymentCallback(callbackData) {
    try {
      const response = await this.api.post('/payment/callback', callbackData)
      return response.data
    } catch (error) {
      console.error('Error handling payment callback:', error)
      throw error
    }
  }

  /**
   * Format currency (Toman)
   */
  formatCurrency(amount) {
    return new Intl.NumberFormat('fa-IR', {
      style: 'currency',
      currency: 'IRR',
      minimumFractionDigits: 0,
      maximumFractionDigits: 0
    }).format(amount * 10) // Convert Toman to Rials for display
  }

  /**
   * Format currency in Toman
   */
  formatToman(amount) {
    return new Intl.NumberFormat('fa-IR').format(amount) + ' تومان'
  }

  /**
   * Get payment method display name
   */
  getPaymentMethodName(methodId) {
    const methods = {
      'zarinpal': 'زرین‌پال',
      'nextpay': 'نکست‌پی',
      'parsijoo': 'پارس‌ایجو',
      'free': 'رایگان'
    }
    return methods[methodId] || methodId
  }

  /**
   * Get payment status display name
   */
  getPaymentStatusName(status) {
    const statuses = {
      'pending': 'در انتظار پرداخت',
      'completed': 'پرداخت شده',
      'failed': 'ناموفق',
      'cancelled': 'لغو شده',
      'refunded': 'بازپرداخت شده',
      'expired': 'منقضی شده'
    }
    return statuses[status] || status
  }

  /**
   * Get payment status color
   */
  getPaymentStatusColor(status) {
    const colors = {
      'pending': 'text-yellow-600 bg-yellow-100',
      'completed': 'text-green-600 bg-green-100',
      'failed': 'text-red-600 bg-red-100',
      'cancelled': 'text-gray-600 bg-gray-100',
      'refunded': 'text-blue-600 bg-blue-100',
      'expired': 'text-red-600 bg-red-100'
    }
    return colors[status] || 'text-gray-600 bg-gray-100'
  }

  /**
   * Check if payment is pending
   */
  isPaymentPending(status) {
    return status === 'pending'
  }

  /**
   * Check if payment is completed
   */
  isPaymentCompleted(status) {
    return status === 'completed'
  }

  /**
   * Check if payment can be refunded
   */
  canRefundPayment(status, paidAt) {
    if (status !== 'completed' || !paidAt) {
      return false
    }
    
    // Check if within 24 hours
    const hoursSincePayment = (Date.now() - new Date(paidAt).getTime()) / (1000 * 60 * 60)
    return hoursSincePayment <= 24
  }

  /**
   * Get refund deadline
   */
  getRefundDeadline(paidAt) {
    if (!paidAt) return null
    
    const deadline = new Date(paidAt)
    deadline.setHours(deadline.getHours() + 24)
    return deadline
  }

  /**
   * Format refund deadline
   */
  formatRefundDeadline(paidAt) {
    const deadline = this.getRefundDeadline(paidAt)
    if (!deadline) return null
    
    return new Intl.DateTimeFormat('fa-IR', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    }).format(deadline)
  }
}

export const paymentService = new PaymentService()














