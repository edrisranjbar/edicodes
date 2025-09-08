import axios from 'axios';

// Get the API base URL from config
const API_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api';

/**
 * Global upload service for handling video uploads
 */
const uploadService = {
  /**
   * Upload video file to course content
   * @param {number|string} courseId - Course ID
   * @param {File} videoFile - Video file to upload
   * @param {Object} contentData - Content data (title, content, order, is_free)
   * @returns {Promise} - API response
   */
  async uploadVideo(courseId, videoFile, contentData) {
    const formData = new FormData();

    // Add video file
    formData.append('video', videoFile);

    // Add content data
    Object.keys(contentData).forEach(key => {
      if (contentData[key] !== null && contentData[key] !== undefined) {
        // Convert boolean values to strings for Laravel validation
        if (typeof contentData[key] === 'boolean') {
          formData.append(key, contentData[key] ? '1' : '0');
        } else {
          formData.append(key, contentData[key]);
        }
      }
    });

    const token = localStorage.getItem('admin_token');
    const instance = axios.create({
      baseURL: API_URL,
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'multipart/form-data'
      }
    });

    try {
      const response = await instance.post(`/admin/courses/${courseId}/contents`, formData);
      console.log('✅ Video upload successful:', response.data);
      return response;
    } catch (error) {
      console.error('❌ Video upload failed:', error.response?.data || error.message);
      throw error;
    }
  },

  /**
   * Update course content with new video
   * @param {number|string} courseId - Course ID
   * @param {number|string} contentId - Content ID
   * @param {File} videoFile - Video file to upload (optional)
   * @param {Object} contentData - Content data to update
   * @returns {Promise} - API response
   */
  async updateVideo(courseId, contentId, videoFile, contentData) {
    const formData = new FormData();

    // Add method override for Laravel (PUT via POST)
    formData.append('_method', 'PUT');

    // Add video file if provided
    if (videoFile) {
      formData.append('video', videoFile);
    }

    // Add content data
    Object.keys(contentData).forEach(key => {
      if (contentData[key] !== null && contentData[key] !== undefined) {
        // Convert boolean values to strings for Laravel validation
        if (typeof contentData[key] === 'boolean') {
          formData.append(key, contentData[key] ? '1' : '0');
        } else {
          formData.append(key, contentData[key]);
        }
      }
    });

    const token = localStorage.getItem('admin_token');
    const instance = axios.create({
      baseURL: API_URL,
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'multipart/form-data'
      }
    });

    try {
      // Use POST with _method=PUT to work around Laravel's multipart PUT issues
      const response = await instance.post(`/admin/courses/${courseId}/contents/${contentId}`, formData);
      console.log('✅ Video update successful:', response.data);
      return response;
    } catch (error) {
      console.error('❌ Video update failed:', error.response?.data || error.message);
      throw error;
    }
  },

  /**
   * Validate video file
   * @param {File} file - File to validate
   * @returns {Object} - Validation result
   */
  validateVideo(file) {
    const errors = [];

    if (!file) {
      errors.push('فایلی انتخاب نشده است');
      return { valid: false, errors };
    }

    // Check file type
    const allowedTypes = ['video/mp4', 'video/avi', 'video/mov', 'video/wmv', 'video/flv', 'video/webm'];
    if (!allowedTypes.includes(file.type)) {
      errors.push('فرمت فایل ویدیو نامعتبر است. فرمت‌های مجاز: MP4, AVI, MOV, WMV, FLV, WebM');
    }

    // Check file size (max 200MB)
    const maxSize = 200 * 1024 * 1024; // 200MB
    if (file.size > maxSize) {
      errors.push('حجم فایل بیش از حد مجاز است. حداکثر حجم: 200MB');
    }

    return {
      valid: errors.length === 0,
      errors
    };
  }
};

export default uploadService;
