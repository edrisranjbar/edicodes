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

  // Add response interceptor for debugging
  instance.interceptors.response.use(
    (response) => response,
    (error) => {
      console.error('Axios error:', error);
      if (error.response) {
        console.error('Response status:', error.response.status);
        console.error('Response headers:', error.response.headers);
        console.error('Response data:', error.response.data);
      }
      return Promise.reject(error);
    }
  );

  return instance;
};

/**
 * Service for handling course-related API calls
 */
const courseService = {
  /**
   * Get all published courses (public)
   * @returns {Promise} - API response
   */
  async getCourses() {
    const instance = axios.create({
      baseURL: API_URL,
      headers: {
        'Accept': 'application/json'
      }
    });
    return instance.get('/courses');
  },

  /**
   * Get course contents (public)
   * @param {number|string} courseId - Course ID
   * @returns {Promise} - API response
   */
  async getCourseContents(courseId) {
    const instance = axios.create({
      baseURL: API_URL,
      headers: {
        'Accept': 'application/json'
      }
    });
    return instance.get(`/courses/${courseId}/contents`);
  },

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


          // Try appending with explicit filename and type
          formData.append('file', courseData[key], courseData[key].name);
        } else if (key !== 'thumbnail') {
          const value = courseData[key];

          // Handle different data types properly
          if (typeof value === 'boolean') {
            formData.append(key, value ? '1' : '0');
          } else if (typeof value === 'number') {
            formData.append(key, value.toString());
          } else if (value === null || value === undefined) {
            // For create operations, ensure required fields are not null
            if (['title', 'description', 'price', 'status', 'level', 'admin_id'].includes(key)) {
              formData.append(key, value || '');
            }
          } else {
            formData.append(key, value);
          }
        }
      });



      // Don't set Content-Type header - let axios set it automatically for FormData
      console.log('Sending FormData request to:', '/admin/courses');

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
          // Try appending with explicit filename and type
          formData.append('thumbnail', courseData[key], courseData[key].name);
        } else if (key !== 'thumbnail') {
          const value = courseData[key];

          // Handle different data types properly
          if (typeof value === 'boolean') {
            formData.append(key, value ? '1' : '0');
          } else if (typeof value === 'number') {
            formData.append(key, value.toString());
          } else if (value === null || value === undefined) {
            // For update operations, only include defined values
            // Skip null/undefined to avoid overriding existing values unintentionally
          } else {
            formData.append(key, value);
          }
        }
      });



      // Don't set Content-Type header - let axios set it automatically for FormData

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

// Test function for debugging file uploads
export const testFileUpload = async (file) => {
  console.log('Testing file upload with file:', file);

  const instance = createAuthInstance();

  // Create FormData
  const formData = new FormData();
  formData.append('file', file, file.name);

  // Add some test data
  formData.append('title', 'Test Course');
  formData.append('description', 'Test description');
  formData.append('price', '1000');
  formData.append('status', 'draft');
  formData.append('level', 'beginner');
  formData.append('admin_id', '1');

  console.log('FormData created:');
  for (let [key, value] of formData.entries()) {
    if (value instanceof File) {
      console.log(`- ${key}: File (${value.name}, ${value.type}, ${value.size} bytes)`);
    } else {
      console.log(`- ${key}: ${value}`);
    }
  }

  try {
    const response = await instance.post('/admin/courses', formData);
    console.log('Upload successful:', response);
    return response;
  } catch (error) {
    console.error('Upload failed:', error);
    throw error;
  }
};

export default courseService;

