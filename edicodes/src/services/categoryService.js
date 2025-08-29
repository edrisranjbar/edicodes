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

const categoryService = {
  async getCategories() {
    const instance = createAuthInstance();
    return instance.get('/admin/categories');
  },

  async getCategory(id) {
    const instance = createAuthInstance();
    return instance.get(`/admin/categories/${id}`);
  },

  async createCategory(categoryData) {
    const instance = createAuthInstance();
    return instance.post('/admin/categories', categoryData);
  },

  async updateCategory(id, categoryData) {
    const instance = createAuthInstance();
    return instance.put(`/admin/categories/${id}`, categoryData);
  },

  async deleteCategory(id) {
    const instance = createAuthInstance();
    return instance.delete(`/admin/categories/${id}`);
  }
};

export default categoryService;
