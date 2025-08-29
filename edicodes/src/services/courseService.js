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
 * Service for handling course-related API calls
 */
const courseService = {
  /**
   * Get all courses (admin)
   * @param {number} page - Page number for pagination
   * @returns {Promise} - API response
   */
  async getCourses(page = 1) {
    const instance = createAuthInstance();
    const params = page > 1 ? `?page=${page}` : '';
    return instance.get(`/admin/courses${params}`);
  },

  /**
   * Get a single course by ID
   * @param {number|string} id - Course ID
   * @returns {Promise} - API response
   */
  async getCourse(id) {
    const instance = createAuthInstance();
    return instance.get(`/admin/courses/${id}`);
  },

  /**
   * Create a new course
   * @param {Object} courseData - Course data
   * @returns {Promise} - API response
   */
  async createCourse(courseData) {
    const instance = createAuthInstance();
    
    // Check if we have a file to upload
    if (courseData.thumbnail instanceof File) {
      // Create FormData for file upload
      const formData = new FormData();
      
      // Add all course data to FormData
      Object.keys(courseData).forEach(key => {
        if (key === 'thumbnail' && courseData[key] instanceof File) {
          formData.append('thumbnail', courseData[key]);
        } else if (key !== 'thumbnail') {
          // Handle boolean values properly
          if (typeof courseData[key] === 'boolean') {
            formData.append(key, courseData[key] ? '1' : '0');
          } else {
            formData.append(key, courseData[key]);
          }
        }
      });
      
      // Update headers for multipart/form-data
      instance.defaults.headers['Content-Type'] = 'multipart/form-data';
      return instance.post('/admin/courses', formData);
    } else {
      // Regular JSON request
      return instance.post('/admin/courses', courseData);
    }
  },

  /**
   * Update an existing course
   * @param {number|string} id - Course ID
   * @param {Object} courseData - Course data
   * @returns {Promise} - API response
   */
  async updateCourse(id, courseData) {
    const instance = createAuthInstance();
    
    // Check if we have a file to upload
    if (courseData.thumbnail instanceof File) {
      // Create FormData for file upload
      const formData = new FormData();
      
      // Add all course data to FormData
      Object.keys(courseData).forEach(key => {
        if (key === 'thumbnail' && courseData[key] instanceof File) {
          formData.append('thumbnail', courseData[key]);
        } else if (key !== 'thumbnail') {
          // Handle boolean values properly
          if (typeof courseData[key] === 'boolean') {
            formData.append(key, courseData[key] ? '1' : '0');
          } else {
            formData.append(key, courseData[key]);
          }
        }
      });
      
      // Update headers for multipart/form-data
      instance.defaults.headers['Content-Type'] = 'multipart/form-data';
      return instance.put(`/admin/courses/${id}`, formData);
    } else {
      // Regular JSON request
      return instance.put(`/admin/courses/${id}`, courseData);
    }
  },

  /**
   * Delete a course
   * @param {number|string} id - Course ID
   * @returns {Promise} - API response
   */
  async deleteCourse(id) {
    const instance = createAuthInstance();
    return instance.delete(`/admin/courses/${id}`);
  },

  /**
   * Get course contents
   * @param {number|string} courseId - Course ID
   * @returns {Promise} - API response
   */
  async getCourseContents(courseId) {
    const instance = createAuthInstance();
    return instance.get(`/admin/courses/${courseId}/contents`);
  },

  /**
   * Create course content
   * @param {number|string} courseId - Course ID
   * @param {Object} contentData - Content data
   * @returns {Promise} - API response
   */
  async createCourseContent(courseId, contentData) {
    const instance = createAuthInstance();
    return instance.post(`/admin/courses/${courseId}/contents`, contentData);
  },

  /**
   * Update course content
   * @param {number|string} courseId - Course ID
   * @param {number|string} contentId - Content ID
   * @param {Object} contentData - Content data
   * @returns {Promise} - API response
   */
  async updateCourseContent(courseId, contentId, contentData) {
    const instance = createAuthInstance();
    return instance.put(`/admin/courses/${courseId}/contents/${contentId}`, contentData);
  },

  /**
   * Delete course content
   * @param {number|string} courseId - Course ID
   * @param {number|string} contentId - Content ID
   * @returns {Promise} - API response
   */
  async deleteCourseContent(courseId, contentId) {
    const instance = createAuthInstance();
    return instance.delete(`/admin/courses/${courseId}/contents/${contentId}`);
  },

  /**
   * Reorder course contents
   * @param {number|string} courseId - Course ID
   * @param {Array} contentOrders - Array of content orders
   * @returns {Promise} - API response
   */
  async reorderContents(courseId, contentOrders) {
    const instance = createAuthInstance();
    return instance.post(`/admin/courses/${courseId}/contents/reorder`, { content_orders: contentOrders });
  },

  /**
   * Upload video for course content
   * @param {number|string} courseId - Course ID
   * @param {FormData} formData - Form data with video and content_id
   * @returns {Promise} - API response
   */
  async uploadVideo(courseId, formData) {
    const token = localStorage.getItem('admin_token');
    const instance = axios.create({
      baseURL: API_URL,
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'multipart/form-data'
      }
    });
    return instance.post(`/admin/courses/${courseId}/contents/upload-video`, formData);
  }
};

export default courseService;

