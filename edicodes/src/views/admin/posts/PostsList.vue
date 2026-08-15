<template>
  <div>
    <!-- Header with actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
      <h1 class="text-xl font-vazir text-white mb-4 sm:mb-0">مدیریت مطالب</h1>
      <div class="flex space-x-3 space-x-reverse">
        <router-link
          to="/admin/posts/create"
          class="px-3 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors duration-200 flex items-center text-sm font-vazir"
        >
          <font-awesome-icon icon="plus" class="ml-1" />
          ایجاد مطلب جدید
        </router-link>
      </div>
    </div>

    <!-- Toast Notifications -->
    <div class="fixed top-5 left-5 z-50 flex flex-col space-y-4">
      <div
        v-for="(toast, index) in toasts"
        :key="index"
        :class="[
          toast.type === 'success' ? 'bg-green-500/90 text-white border-green-700' :
          toast.type === 'error' ? 'bg-red-500/90 text-white border-red-700' :
          'bg-blue-500/90 text-white border-blue-700'
        ]"
        class="px-4 py-3 rounded-lg shadow-lg border flex items-center max-w-md transform transition-all duration-300"
        :style="{
          opacity: toast.visible ? '1' : '0',
          transform: toast.visible ? 'translateX(0)' : 'translateX(-100%)'
        }"
      >
        <div class="flex-shrink-0 mr-2">
          <font-awesome-icon
            :icon="toast.type === 'success' ? 'check-circle' : toast.type === 'error' ? 'exclamation-circle' : 'info-circle'"
            class="h-5 w-5"
          />
        </div>
        <div class="mr-2 font-vazir">{{ toast.message }}</div>
        <button @click="removeToast(index)" class="mr-auto text-white hover:text-white/80">
          <font-awesome-icon icon="times" class="h-4 w-4" />
        </button>
      </div>
    </div>

    <!-- Search and Filter -->
    <div class="bg-black/50 rounded-lg border border-white/10 p-4 mb-6 flex flex-col md:flex-row gap-4">
      <div class="flex-1">
        <div class="relative">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="جستجو در مطالب..."
            class="w-full bg-black/20 border border-white/10 rounded-lg py-2 px-4 pr-10 font-vazir text-white/90 focus:outline-none focus:border-primary/50"
            @keyup.enter="fetchPosts(1)"
          />
          <button
            class="absolute left-3 top-1/2 transform -translate-y-1/2 text-white/50 hover:text-primary"
            @click="fetchPosts(1)"
          >
            <font-awesome-icon icon="search" />
          </button>
        </div>
      </div>
      <div>
        <div class="relative">
          <select
            v-model="selectedCategory"
            class="w-full bg-black/20 border border-white/10 rounded-lg py-2 px-4 font-vazir text-white/90 focus:outline-none focus:border-primary/50 appearance-none cursor-pointer"
            @change="fetchPosts(1)"
          >
            <option value="">همه دسته‌بندی‌ها</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
              {{ category.name }}
            </option>
          </select>
          <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center px-4 text-white/50">
            <font-awesome-icon icon="chevron-down" class="h-4 w-4" />
          </div>
        </div>
      </div>
    </div>

    <!-- Loading state -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary"></div>
    </div>

    <!-- Posts Table -->
    <div v-else-if="posts.length" class="bg-black/50 border border-white/10 rounded-lg overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="text-right bg-black/60 border-b border-white/10">
              <th class="py-3 pr-6 text-sm font-vazir text-white/60 font-normal">عنوان</th>
              <th class="py-3 px-3 text-sm font-vazir text-white/60 font-normal">دسته‌بندی</th>
              <th class="py-3 px-3 text-sm font-vazir text-white/60 font-normal">تاریخ</th>
              <th class="py-3 px-3 text-sm font-vazir text-white/60 font-normal">وضعیت</th>
              <th class="py-3 pl-6 text-sm font-vazir text-white/60 font-normal">عملیات</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="post in posts" :key="post.id" class="text-right border-b border-white/5 hover:bg-white/5">
              <td class="py-4 pr-6">
                <div class="font-vazir text-white text-sm truncate max-w-xs">
                  {{ post.title }}
                </div>
                <div class="text-white/50 text-xs mt-1 truncate max-w-xs">
                  {{ post.slug }}
                </div>
              </td>
              <td class="py-4 px-3">
                <span class="inline-flex px-2 py-1 bg-primary/10 text-primary text-xs rounded-full font-vazir">
                  {{ post.category?.name || 'بدون دسته‌بندی' }}
                </span>
              </td>
              <td class="py-4 px-3">
                <div class="font-vazir text-white/70 text-sm">{{ formatDate(post.published_at) }}</div>
              </td>
              <td class="py-4 px-3">
                <span
                  :class="statusClass(post)"
                  class="inline-flex px-2 py-1 text-xs rounded-full font-vazir"
                >
                  {{ statusLabel(post) }}
                </span>
              </td>
              <td class="py-4 pl-6">
                <div class="flex items-center space-x-2 space-x-reverse">
                  <router-link :to="`/admin/posts/edit/${post.id}`" class="text-blue-400 hover:text-blue-300">
                    <font-awesome-icon icon="edit" />
                  </router-link>
                  <a
                    :href="`/blog/${post.slug}`"
                    target="_blank"
                    class="text-green-400 hover:text-green-300 mx-2"
                  >
                    <font-awesome-icon icon="eye" />
                  </a>
                  <button
                    @click="confirmDelete(post)"
                    class="text-red-400 hover:text-red-300"
                  >
                    <font-awesome-icon icon="trash-alt" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Empty state -->
    <div v-else class="bg-black/50 border border-white/10 rounded-lg p-12 text-center">
      <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary/10 mb-4">
        <font-awesome-icon icon="newspaper" class="text-primary h-8 w-8" />
      </div>
      <h3 class="text-lg font-vazir text-white mb-2">مطلبی یافت نشد</h3>
      <p class="text-white/60 font-vazir mb-6">
        <span v-if="searchQuery || selectedCategory">جستجو یا فیلتر خود را تغییر دهید یا </span>
        <span v-else>برای شروع </span>
        مطلب جدیدی ایجاد کنید.
      </p>
      <router-link
        to="/admin/posts/create"
        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors duration-200 inline-flex items-center text-sm font-vazir"
      >
        <font-awesome-icon icon="plus" class="ml-1" />
        ایجاد مطلب جدید
      </router-link>
    </div>

    <!-- Pagination -->
    <div v-if="posts.length && totalPages > 1" class="flex justify-center mt-6">
      <div class="flex space-x-1 space-x-reverse">
        <button
          @click="changePage(currentPage - 1)"
          :disabled="currentPage === 1"
          class="px-3 py-1 rounded-lg text-sm font-vazir bg-black/50 border border-white/10 text-white hover:bg-white/10 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-150"
        >
          قبلی
        </button>

        <template v-for="page in displayedPages" :key="page">
          <button
            v-if="page !== '...'"
            @click="changePage(page)"
            :class="currentPage === page ? 'bg-primary text-white border-primary' : 'bg-black/50 text-white border-white/10 hover:bg-white/10'"
            class="px-3 py-1 rounded-lg text-sm font-vazir border transition-colors duration-150"
          >
            {{ page }}
          </button>
          <span
            v-else
            class="px-3 py-1 text-sm font-vazir text-white/50"
          >
            ...
          </span>
        </template>

        <button
          @click="changePage(currentPage + 1)"
          :disabled="currentPage === totalPages"
          class="px-3 py-1 rounded-lg text-sm font-vazir bg-black/50 border border-white/10 text-white hover:bg-white/10 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-150"
        >
          بعدی
        </button>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center">
      <div class="absolute inset-0 bg-black/70" @click="showDeleteModal = false"></div>
      <div class="relative bg-black/90 rounded-lg border border-white/10 p-6 w-full max-w-md">
        <h3 class="text-xl font-vazir text-white mb-4">تایید حذف</h3>
        <p class="font-vazir text-white/70 mb-6">
          آیا از حذف مطلب <span class="text-white font-bold">"{{ postToDelete?.title }}"</span> اطمینان دارید؟
          این عمل قابل بازگشت نیست.
        </p>
        <div class="flex justify-end space-x-3 space-x-reverse">
          <button
            @click="showDeleteModal = false"
            class="px-4 py-2 bg-white/10 text-white rounded-lg hover:bg-white/20 transition-colors duration-200 text-sm font-vazir"
          >
            انصراف
          </button>
          <button
            @click="deletePost"
            :disabled="isDeleting"
            class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors duration-200 text-sm font-vazir flex items-center"
          >
            <font-awesome-icon v-if="isDeleting" icon="circle-notch" class="animate-spin h-4 w-4 ml-2" />
            <font-awesome-icon v-else icon="trash-alt" class="ml-2" />
            {{ isDeleting ? 'در حال حذف...' : 'حذف' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const loading = ref(true);
const posts = ref([]);
const categories = ref([]);
const searchQuery = ref('');
const selectedCategory = ref('');
const currentPage = ref(1);
const totalPages = ref(1);
const showDeleteModal = ref(false);
const postToDelete = ref(null);
const isDeleting = ref(false);
const toasts = ref([]);

const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  const options = { year: 'numeric', month: 'long', day: 'numeric' };

  // Get the formatted date parts from Intl.DateTimeFormat
  const formatter = new Intl.DateTimeFormat('fa-IR', options);
  const parts = formatter.formatToParts(date);

  // Extract the individual parts
  let day = '';
  let month = '';
  let year = '';

  parts.forEach(part => {
    if (part.type === 'day') day = part.value;
    if (part.type === 'month') month = part.value;
    if (part.type === 'year') year = part.value;
  });

  // Format according to the required pattern: ۲۳ اردیبهشت ۱۴۰۴
  return `${day} ${month} ${year}`;
};

// Return a Persian label for the post status, supporting scheduled posts
const statusLabel = (post) => {
  if (post.status === 'published' || post.published) return 'منتشر شده';
  if (post.status === 'scheduled') return 'زمان‌بندی شده';
  return 'پیش‌نویس';
};

// Return Tailwind classes for the status badge
const statusClass = (post) => {
  if (post.status === 'published' || post.published) return 'bg-green-500/10 text-green-500';
  if (post.status === 'scheduled') return 'bg-blue-500/10 text-blue-500';
  return 'bg-yellow-500/10 text-yellow-500';
};

const fetchPosts = async (page = 1) => {
  try {
    loading.value = true;
    currentPage.value = page;

    // Build the API request parameters
    const params = { page };

    if (searchQuery.value) {
      params.search = searchQuery.value;
    }

    if (selectedCategory.value) {
      params.category_id = selectedCategory.value;
    }

    const response = await axios.get(`${import.meta.env.VITE_API_BASE_URL}/admin/posts`, {
      params,
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('admin_token')}`
      }
    });

    if (response.data && response.data.data) {
      posts.value = response.data.data;

      // Set pagination data
      if (response.data.meta) {
        totalPages.value = response.data.meta.last_page || 1;
      }
    }
  } catch (error) {
    console.error('Error fetching posts:', error);
    showToast('خطا در بارگذاری مطالب. لطفاً دوباره تلاش کنید.', 'error');
  } finally {
    loading.value = false;
  }
};

const fetchCategories = async () => {
  try {
    const response = await axios.get(`${import.meta.env.VITE_API_BASE_URL}/admin/categories`, {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('admin_token')}`
      }
    });

    if (response.data && Array.isArray(response.data.data)) {
      categories.value = response.data.data;
    } else if (Array.isArray(response.data)) {
      categories.value = response.data;
    }
  } catch (error) {
    console.error('Error fetching categories:', error);
    showToast('خطا در بارگذاری دسته‌بندی‌ها', 'error');
  }
};

const confirmDelete = (post) => {
  postToDelete.value = post;
  showDeleteModal.value = true;
};

// Toast notification system
const showToast = (message, type = 'info', duration = 5000) => {
  const id = Date.now();
  const toast = {
    id,
    message,
    type,
    visible: false,
  };

  toasts.value.push(toast);

  // Trigger animation in next tick
  setTimeout(() => {
    const index = toasts.value.findIndex(t => t.id === id);
    if (index !== -1) {
      toasts.value[index].visible = true;
    }
  }, 10);

  // Auto remove after duration
  setTimeout(() => {
    removeToast(toasts.value.findIndex(t => t.id === id));
  }, duration);
};

const removeToast = (index) => {
  if (index === -1 || !toasts.value[index]) return;

  // Trigger exit animation
  toasts.value[index].visible = false;

  // Remove from array after animation completes
  setTimeout(() => {
    toasts.value.splice(index, 1);
  }, 300);
};

const deletePost = async () => {
  if (!postToDelete.value) return;

  try {
    isDeleting.value = true;

    // Use the admin API endpoint for deletion
    await axios.delete(`${import.meta.env.VITE_API_BASE_URL}/admin/posts/${postToDelete.value.id}`, {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('admin_token')}`
      }
    });

    // Remove post from the list
    posts.value = posts.value.filter(post => post.id !== postToDelete.value.id);

    // Close modal
    showDeleteModal.value = false;
    postToDelete.value = null;

    // Show success toast
    showToast('مطلب با موفقیت حذف شد', 'success');

    // If page is now empty but there are other pages, go to previous page
    if (posts.value.length === 0 && currentPage.value > 1) {
      changePage(currentPage.value - 1);
    }
  } catch (error) {
    console.error('Error deleting post:', error);

    // Show error toast
    showToast(`خطا در حذف مطلب: ${error.response?.data?.message || 'خطای ناشناخته'}`, 'error');

    // Close modal in case of error
    showDeleteModal.value = false;
  } finally {
    isDeleting.value = false;
  }
};

const changePage = (page) => {
  if (page < 1 || page > totalPages.value) return;
  fetchPosts(page);
};

// Calculate displayed pages for pagination
const displayedPages = computed(() => {
  const total = totalPages.value;
  const current = currentPage.value;

  if (total <= 7) {
    return Array.from({ length: total }, (_, i) => i + 1);
  }

  if (current <= 3) {
    return [1, 2, 3, 4, '...', total];
  }

  if (current >= total - 2) {
    return [1, '...', total - 3, total - 2, total - 1, total];
  }

  return [1, '...', current - 1, current, current + 1, '...', total];
});

onMounted(() => {
  fetchCategories();
  fetchPosts();
});
</script>
