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
   * Create a new content (text only)
   * @param {number|string} courseId - Course ID
   * @param {Object} contentData - Content data
   * @returns {Promise} - API response
   */
  async createContent(courseId, contentData) {
    const instance = createAuthInstance();
    return instance.post(`/admin/courses/${courseId}/contents`, contentData);
  },

  /**
   * Create content with file upload in one request
   * @param {number|string} courseId - Course ID
   * @param {Object} contentData - Content data
   * @param {File} file - Optional file to upload
   * @returns {Promise} - API response
   */
  async createContentWithFile(courseId, contentData, file = null) {
    const instance = createAuthInstance();
    
    if (file) {
      // Create FormData for file upload
      const formData = new FormData();
      
      // Add content data
      Object.keys(contentData).forEach(key => {
        if (key !== 'file' && key !== 'video') {
          formData.append(key, contentData[key]);
        }
      });

      // Add file based on content type
      if (contentData.type === 'video' && file) {
        formData.append('video', file);
      } else if (contentData.type === 'file' && file) {
        formData.append('file', file);
      }

      return instance.post(`/admin/courses/${courseId}/contents/with-file`, formData);
    } else {
      // Regular content creation without file
      return instance.post(`/admin/courses/${courseId}/contents`, contentData);
    }
  },

  /**
   * Update an existing content
   * @param {number|string} courseId - Course ID
   * @param {number|string} contentId - Content ID
   * @param {Object} contentData - Content data
   * @returns {Promise} - API response
   */
  async updateContent(courseId, contentId, contentData) {
    const instance = createAuthInstance();
    return instance.put(`/admin/courses/${courseId}/contents/${contentId}`, contentData);
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
   * Upload video for course content
   * @param {number|string} courseId - Course ID
   * @param {number|string} contentId - Content ID
   * @param {File} videoFile - Video file to upload
   * @returns {Promise} - API response
   */
  async uploadVideo(courseId, contentId, videoFile) {
    const formData = new FormData();
    formData.append('video', videoFile);
    formData.append('content_id', contentId);

    const instance = createAuthInstance();
    return instance.post(`/admin/courses/${courseId}/contents/upload-video`, formData);
  },

  /**
   * Upload file for course content
   * @param {number|string} courseId - Course ID
   * @param {number|string} contentId - Content ID
   * @param {File} file - File to upload
   * @returns {Promise} - API response
   */
  async uploadFile(courseId, contentId, file) {
    const formData = new FormData();
    formData.append('file', file);
    formData.append('content_id', contentId);

    const instance = createAuthInstance();
    return instance.post(`/admin/courses/${courseId}/contents/upload-file`, formData);
  },

  /**
   * Get content with file URLs for streaming/download
   * @param {number|string} courseId - Course ID
   * @param {number|string} contentId - Content ID
   * @returns {Promise} - API response
   */
  async getContentWithFiles(courseId, contentId) {
    const instance = createAuthInstance();
    return instance.get(`/admin/courses/${courseId}/contents/${contentId}/files`);
  },

  /**
   * Create a text episode
   * @param {number|string} courseId - Course ID
   * @param {Object} episodeData - Episode data
   * @returns {Promise} - API response
   */
  async createTextEpisode(courseId, episodeData) {
    const contentData = {
      ...episodeData,
      type: 'text'
    };
    return this.createContent(courseId, contentData);
  },

  /**
   * Create a video episode
   * @param {number|string} courseId - Course ID
   * @param {Object} episodeData - Episode data
   * @param {File} videoFile - Video file
   * @returns {Promise} - API response
   */
  async createVideoEpisode(courseId, episodeData, videoFile) {
    const contentData = {
      ...episodeData,
      type: 'video'
    };
    return this.createContentWithFile(courseId, contentData, videoFile);
  },

  /**
   * Create a file episode
   * @param {number|string} courseId - Course ID
   * @param {Object} episodeData - Episode data
   * @param {File} file - Document file
   * @returns {Promise} - API response
   */
  async createFileEpisode(courseId, episodeData, file) {
    const contentData = {
      ...episodeData,
      type: 'file'
    };
    return this.createContentWithFile(courseId, contentData, file);
  },

  /**
   * Get content types for dropdown
   * @returns {Array} - Array of content types
   */
  getContentTypes() {
    return [
      { value: 'text', label: 'متن', icon: 'file-text' },
      { value: 'video', label: 'ویدیو', icon: 'video' },
      { value: 'file', label: 'فایل', icon: 'file' }
    ];
  },

  /**
   * Get file type icon based on file extension
   * @param {string} filename - Filename with extension
   * @returns {string} - FontAwesome icon name
   */
  getFileTypeIcon(filename) {
    if (!filename) return 'file';
    
    const extension = filename.split('.').pop()?.toLowerCase();
    
    switch (extension) {
      case 'pdf':
        return 'file-pdf';
      case 'doc':
      case 'docx':
        return 'file-word';
      case 'xls':
      case 'xlsx':
        return 'file-excel';
      case 'ppt':
      case 'pptx':
        return 'file-powerpoint';
      case 'txt':
        return 'file-text';
      case 'zip':
      case 'rar':
        return 'file-archive';
      default:
        return 'file';
    }
  },

  /**
   * Format file size for display
   * @param {number} bytes - File size in bytes
   * @returns {string} - Formatted file size
   */
  formatFileSize(bytes) {
    if (!bytes) return '0 B';
    
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(1024));
    
    return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i];
  }
};

export default courseContentService;
