import axios from 'axios';

// Get the API base URL from config
const API_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api';

// Create axios instance with auth header
const createAuthInstance = () => {
  const token = localStorage.getItem('admin_token');
  const instance = axios.create({
    baseURL: API_URL,
    headers: {
      'Authorization': `Bearer ${token}`,
      'Accept': 'application/json'
    }
  });

  return instance;
};



/**
 * Service for handling course content-related API calls
 */
const courseContentService = {
  /**
   * Get all contents for a course
   * @param {number|string} courseId - Course ID
   * @returns {Promise} - API response
   */
  async getContents(courseId) {
    const instance = createAuthInstance();
    return instance.get(`/admin/courses/${courseId}/contents`);
  },

  /**
   * Get a single content by ID
   * @param {number|string} courseId - Course ID
   * @param {number|string} contentId - Content ID
   * @returns {Promise} - API response
   */
  async getContent(courseId, contentId) {
    const instance = createAuthInstance();
    return instance.get(`/admin/courses/${courseId}/contents/${contentId}`);
  },



  /**
   * Delete a content
   * @param {number|string} courseId - Course ID
   * @param {number|string} contentId - Content ID
   * @returns {Promise} - API response
   */
  async deleteContent(courseId, contentId) {
    const instance = createAuthInstance();
    return instance.delete(`/admin/courses/${courseId}/contents/${contentId}`);
  },

  /**
   * Reorder content items
   * @param {number|string} courseId - Course ID
   * @param {Array} contentOrders - Array of content orders
   * @returns {Promise} - API response
   */
  async reorderContents(courseId, contentOrders) {
    const instance = createAuthInstance();
    return instance.post(`/admin/courses/${courseId}/contents/reorder`, { content_orders: contentOrders });
  },







  /**
   * Create a text episode
   * @param {number|string} courseId - Course ID
   * @param {Object} episodeData - Episode data
   * @returns {Promise} - API response
   */






  /**
   * Get content types for dropdown
   * @returns {Array} - Array of content types
   */
  getContentTypes() {
    return [
      { value: 'video', label: 'ویدیو', icon: 'video' }
    ];
  },


};

export default courseContentService;
