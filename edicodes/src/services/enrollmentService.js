import axios from 'axios';

// Get the API base URL from config
const API_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api';

// Create axios instance with auth header
const createAuthInstance = () => {
  const token = localStorage.getItem('admin_token');
  return axios.create({
    baseURL: API_URL,
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    }
  });
};

/**
 * Service for handling enrollment-related API calls
 */
const enrollmentService = {
  /**
   * Get enrollment statistics (admin)
   * @returns {Promise} - API response
   */
  async getStatistics() {
    const instance = createAuthInstance();
    return instance.get('/admin/enrollments/statistics');
  },

  /**
   * Get all enrollments (admin)
   * @returns {Promise} - API response
   */
  async getEnrollments() {
    const instance = createAuthInstance();
    return instance.get('/admin/enrollments');
  },

  /**
   * Get enrollments for a specific course
   * @param {number|string} courseId - Course ID
   * @returns {Promise} - API response
   */
  async getCourseEnrollments(courseId) {
    const instance = createAuthInstance();
    return instance.get(`/admin/courses/${courseId}/enrollments`);
  },

  /**
   * Get enrollments for a specific user
   * @param {number|string} userId - User ID
   * @returns {Promise} - API response
   */
  async getUserEnrollments(userId) {
    const instance = createAuthInstance();
    return instance.get(`/admin/users/${userId}/enrollments`);
  },

  /**
   * Update enrollment status
   * @param {number|string} enrollmentId - Enrollment ID
   * @param {Object} statusData - Status data
   * @returns {Promise} - API response
   */
  async updateEnrollmentStatus(enrollmentId, statusData) {
    const instance = createAuthInstance();
    return instance.patch(`/admin/enrollments/${enrollmentId}/status`, statusData);
  },

  /**
   * Cancel enrollment (admin)
   * @param {number|string} enrollmentId - Enrollment ID
   * @param {Object} cancelData - Cancel reason data
   * @returns {Promise} - API response
   */
  async cancelEnrollment(enrollmentId, cancelData) {
    const instance = createAuthInstance();
    return instance.delete(`/admin/enrollments/${enrollmentId}`, { data: cancelData });
  },

  /**
   * Get enrollment details
   * @param {number|string} enrollmentId - Enrollment ID
   * @returns {Promise} - API response
   */
  async getEnrollment(enrollmentId) {
    const instance = createAuthInstance();
    return instance.get(`/admin/enrollments/${enrollmentId}`);
  },

  /**
   * Process refund for enrollment
   * @param {number|string} enrollmentId - Enrollment ID
   * @param {Object} refundData - Refund data
   * @returns {Promise} - API response
   */
  async processRefund(enrollmentId, refundData) {
    const instance = createAuthInstance();
    return instance.post(`/admin/enrollments/${enrollmentId}/refund`, refundData);
  }
};

export default enrollmentService;



