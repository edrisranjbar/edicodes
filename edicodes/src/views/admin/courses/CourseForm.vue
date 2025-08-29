<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-white font-vazir">
          {{ isEditing ? 'ویرایش دوره' : 'ایجاد دوره جدید' }}
        </h1>
        <p class="text-gray-400 mt-1 font-vazir">
          {{ isEditing ? 'ویرایش اطلاعات دوره' : 'ایجاد دوره جدید برای دانشجویان' }}
        </p>
      </div>
      <router-link
        to="/admin/courses"
        class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors duration-200 font-vazir"
      >
        <font-awesome-icon icon="arrow-right" class="ml-2 h-4 w-4" />
        بازگشت به لیست
      </router-link>
    </div>

    <!-- Form -->
    <form @submit.prevent="handleSubmit" class="space-y-6">
      <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Right Sidebar -->
        <div class="lg:col-span-1 space-y-6 order-2">
          <!-- Pricing -->
          <div class="bg-black/50 border border-white/10 rounded-lg p-4">
            <h3 class="text-lg font-medium text-white mb-4 font-vazir">قیمت‌گذاری</h3>
            
            <div class="space-y-4">
              <div>
                <label for="price" class="block text-sm font-medium text-gray-300 mb-2 font-vazir">
                  قیمت (تومان) *
                </label>
                <input
                  id="price"
                  v-model="priceDisplayValue"
                  type="text"
                  min="0"
                  required
                  @input="handlePriceInput"
                  class="w-full px-3 py-2 bg-black/50 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent font-vazir text-sm text-center"
                  placeholder="0"
                  dir="ltr"
                />
                <p class="text-sm text-gray-400 mt-1 font-vazir">
                  برای دوره رایگان، مقدار 0 را وارد کنید
                </p>
              </div>

              <div class="flex items-center">
                <input
                  id="featured"
                  v-model="form.featured"
                  type="checkbox"
                  class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded"
                />
                <label for="featured" class="mr-2 block text-sm text-gray-300 font-vazir">
                  دوره ویژه (نمایش در صفحه اصلی)
                </label>
              </div>
            </div>
          </div>

          <!-- Slug -->
          <div class="bg-black/50 border border-white/10 rounded-lg p-4">
            <h3 class="text-lg font-medium text-white mb-4 font-vazir">نامک (URL)</h3>
            
            <div class="space-y-4">
              <div>
                <label for="slug" class="block text-sm font-medium text-gray-300 mb-2 font-vazir">
                  نامک دوره *
                </label>
                <div class="flex items-center space-x-2 space-x-reverse">
                  <input
                    id="slug"
                    v-model="form.slug"
                    type="text"
                    required
                    class="w-full px-3 py-2 bg-black/50 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent font-vazir text-sm"
                    placeholder="نامک-دوره"
                  />
                  <button
                    type="button"
                    @click="regenerateSlug"
                    class="px-2 py-2 bg-primary/20 border border-primary/30 text-primary rounded-lg hover:bg-primary/30 transition-colors duration-200 font-vazir text-xs"
                    title="تولید مجدد نامک از عنوان"
                  >
                    <font-awesome-icon icon="magic" class="h-3 w-3" />
                  </button>
                </div>
                <p class="text-xs text-gray-400 mt-1 font-vazir text-center">
                  نامک برای URL استفاده می‌شود
                </p>
              </div>
            </div>
          </div>



          <!-- Course Settings -->
          <div class="bg-black/50 border border-white/10 rounded-lg p-4">
            <h3 class="text-lg font-medium text-white mb-4 font-vazir">تنظیمات دوره</h3>
            
            <div class="space-y-4">
              <div>
                <label for="category_id" class="block text-sm font-medium text-gray-300 mb-2 font-vazir">
                  دسته‌بندی
                </label>
                <select
                  id="category_id"
                  v-model="form.category_id"
                  class="w-full px-3 py-2 bg-black/50 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent font-vazir text-sm"
                >
                  <option value="">انتخاب دسته‌بندی</option>
                  <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.name }}
                  </option>
                </select>
              </div>

              <div>
                <label for="level" class="block text-sm font-medium text-gray-300 mb-2 font-vazir">
                  سطح دوره *
                </label>
                <select
                  id="level"
                  v-model="form.level"
                  required
                  class="w-full px-3 py-2 bg-black/50 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent font-vazir text-sm"
                >
                  <option value="">انتخاب سطح</option>
                  <option value="beginner">مبتدی</option>
                  <option value="intermediate">متوسط</option>
                  <option value="advanced">پیشرفته</option>
                </select>
              </div>

              <div>
                <label for="duration" class="block text-sm font-medium text-gray-300 mb-2 font-vazir">
                  مدت زمان
                </label>
                <input
                  id="duration"
                  v-model="form.duration"
                  type="text"
                  class="w-full px-3 py-2 bg-black/50 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent font-vazir text-sm"
                  placeholder="مثال: 20 ساعت"
                />
              </div>

              <div>
                <label for="status" class="block text-sm font-medium text-gray-300 mb-2 font-vazir">
                  وضعیت *
                </label>
                <select
                  id="status"
                  v-model="form.status"
                  required
                  class="w-full px-3 py-2 bg-black/50 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent font-vazir text-sm"
                >
                  <option value="draft">پیش‌نویس</option>
                  <option value="published">منتشر شده</option>
                  <option value="archived">آرشیو شده</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Thumbnail -->
          <div class="bg-black/50 border border-white/10 rounded-lg p-4">
            <h3 class="text-lg font-medium text-white mb-4 font-vazir">تصویر دوره</h3>
            
            <div class="space-y-4">
              <!-- Current Thumbnail Preview -->
              <div v-if="thumbnailPreview || form.thumbnail" class="flex items-center space-x-4 space-x-reverse">
                <img
                  :src="thumbnailPreview || form.thumbnail"
                  :alt="form.title"
                  class="h-16 w-16 rounded-lg object-cover"
                />
                <button
                  type="button"
                  @click="removeThumbnail"
                  class="text-red-400 hover:text-red-300 text-xs font-vazir"
                >
                  حذف
                </button>
              </div>

              <!-- File Upload -->
              <div>
                <label for="thumbnail_file" class="block text-sm font-medium text-gray-300 mb-2 font-vazir">
                  انتخاب تصویر
                </label>
                <div class="space-y-2">
                  <input
                    id="thumbnail_file"
                    ref="fileInput"
                    type="file"
                    accept="image/jpg,image/jpeg,image/png,image/webp"
                    @change="handleThumbnailUpload"
                    class="hidden"
                  />
                  <button
                    type="button"
                    @click="$refs.fileInput.click()"
                    class="w-full px-3 py-2 bg-primary/20 border border-primary/30 text-primary rounded-lg hover:bg-primary/30 transition-colors duration-200 font-vazir text-sm"
                  >
                    <font-awesome-icon icon="upload" class="ml-2 h-3 w-3" />
                    انتخاب فایل
                  </button>
                  <span v-if="selectedFile" class="text-xs text-gray-400 font-vazir block text-center">
                    {{ selectedFile.name }}
                  </span>
                </div>
                <p class="text-xs text-gray-400 mt-2 font-vazir text-center">
                  JPG, PNG, WebP (10MB)
                </p>
              </div>
            </div>
          </div>



        </div>

        <!-- Main Content Area -->
        <div class="lg:col-span-3 space-y-6 order-1">
          <!-- Basic Information -->
          <div class="bg-black/50 border border-white/10 rounded-lg p-6">
            <h3 class="text-lg font-medium text-white mb-4 font-vazir">اطلاعات پایه</h3>
            
            <div class="space-y-4">
              <div>
                <label for="title" class="block text-sm font-medium text-gray-300 mb-2 font-vazir">
                  عنوان دوره *
                </label>
                <input
                  id="title"
                  v-model="form.title"
                  type="text"
                  required
                  class="w-full px-4 py-2 bg-black/50 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent font-vazir"
                  placeholder="عنوان دوره را وارد کنید"
                />
              </div>



              <div>
                <label for="short_description" class="block text-sm font-medium text-gray-300 mb-2 font-vazir">
                  توضیح کوتاه
                </label>
                <textarea
                  id="short_description"
                  v-model="form.short_description"
                  rows="3"
                  class="w-full px-4 py-2 bg-black/50 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent font-vazir"
                  placeholder="توضیح کوتاهی از دوره ارائه دهید"
                ></textarea>
              </div>

              <div>
                <label for="description" class="block text-sm font-medium text-gray-300 mb-2 font-vazir">
                  توضیحات کامل *
                </label>
                <textarea
                  id="description"
                  v-model="form.description"
                  rows="6"
                  required
                  class="w-full px-4 py-2 bg-black/50 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent font-vazir"
                  placeholder="توضیحات کامل دوره را وارد کنید"
                ></textarea>
              </div>
            </div>
          </div>



        </div>
      </div>

      <!-- Submit Buttons -->
      <div class="flex items-center justify-end space-x-4 space-x-reverse pt-6 border-t border-white/10">
        <router-link
          to="/admin/courses"
          class="px-6 py-2 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors duration-200 font-vazir"
        >
          انصراف
        </router-link>
        <button
          type="submit"
          :disabled="loading"
          class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200 font-vazir"
        >
          <span v-if="loading" class="inline-flex items-center">
            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white ml-2"></div>
            {{ isEditing ? 'در حال بروزرسانی...' : 'در حال ایجاد...' }}
          </span>
          <span v-else>{{ isEditing ? 'بروزرسانی دوره' : 'ایجاد دوره' }}</span>
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import courseService from '@/services/courseService';
import categoryService from '@/services/categoryService';
import { formatMoneyInput, parseMoneyInput, formatPriceLabel } from '@/utils/moneyFormatter';

const router = useRouter();
const route = useRoute();
const loading = ref(false);
const categories = ref([]);
const thumbnailPreview = ref('');
const selectedFile = ref(null);
const fileInput = ref(null);

const isEditing = computed(() => route.name === 'admin-courses-edit');

// Separate display value for price input
const priceDisplayValue = ref('0');

const form = ref({
  title: '',
  slug: '',
  description: '',
  short_description: '',
  price: 0,
  thumbnail: '',
  status: 'draft',
  featured: false,
  duration: '',
  level: '',
  category_id: '',
  admin_id: 1 // This should come from auth context
});



// Watch for form.price changes to update display
watch(() => form.value.price, (newPrice) => {
  priceDisplayValue.value = formatMoneyInput(newPrice);
});

// Initialize price display value
priceDisplayValue.value = formatMoneyInput(form.value.price);

// Auto-generate slug from title
const generateSlug = (title) => {
  return title
    .toLowerCase()
    .replace(/[^\u0600-\u06FF\u0750-\u077F\u08A0-\u08FF\uFB50-\uFDFF\uFE70-\uFEFFa-zA-Z0-9\s-]/g, '') // Keep Persian, English, numbers, spaces, and hyphens
    .replace(/\s+/g, '-') // Replace spaces with hyphens
    .replace(/-+/g, '-') // Replace multiple hyphens with single hyphen
    .replace(/^-|-$/g, ''); // Remove leading/trailing hyphens
};

// Watch for title changes to auto-generate slug
watch(() => form.value.title, (newTitle) => {
  if (newTitle && !form.value.slug) {
    form.value.slug = generateSlug(newTitle);
  }
});

// Manual slug regeneration
const regenerateSlug = () => {
  if (form.value.title) {
    form.value.slug = generateSlug(form.value.title);
  }
};

onMounted(async () => {
  await fetchCategories();
  
  if (isEditing.value) {
    await fetchCourse();
  }
});

const fetchCategories = async () => {
  try {
    const response = await categoryService.getCategories();
    categories.value = response.data.data || response.data;
  } catch (error) {
    console.error('Error fetching categories:', error);
    categories.value = [];
  }
};

const fetchCourse = async () => {
  try {
    const courseId = route.params.id;
    const response = await courseService.getCourse(courseId);
    
    // Check if course data exists
    if (!response.data || !response.data.data) {
      throw new Error('Course not found');
    }
    
    // Admin endpoint returns course data directly in response.data.data
    const course = response.data.data;
    
    // Validate course object
    if (!course || typeof course !== 'object') {
      throw new Error('Invalid course data');
    }
    
    form.value = {
      title: course.title || '',
      slug: course.slug || '',
      description: course.description || '',
      short_description: course.short_description || '',
      price: course.price || 0,
      thumbnail: course.thumbnail_url || course.thumbnail || '',
      status: course.status || 'draft',
      featured: course.featured || false,
      duration: course.duration || '',
      level: course.level || '',
      category_id: course.category_id || '',
      admin_id: course.admin_id || 1
    };
    
    // Update price display value
    priceDisplayValue.value = formatMoneyInput(form.value.price);
  } catch (error) {
    console.error('Error fetching course:', error);
    
    // Handle specific error cases
    if (error.response?.status === 404) {
      // Course not found - redirect back to courses list
      router.push('/admin/courses');
      return;
    }
    
    // Set default form values on error
    form.value = {
      title: '',
      slug: '',
      description: '',
      short_description: '',
      price: 0,
      thumbnail: '',
      status: 'draft',
      featured: false,
      duration: '',
      level: '',
      category_id: '',
      admin_id: 1
    };
    
    priceDisplayValue.value = '';
  }
};

const handleSubmit = async () => {
  try {
    loading.value = true;
    
    // Prepare form data
    const submitData = { ...form.value };
    
    // If we have a selected file, use it directly (the service will handle the upload)
    if (selectedFile.value) {
      submitData.thumbnail = selectedFile.value;
    }
    
    if (isEditing.value) {
      const courseId = route.params.id;
      await courseService.updateCourse(courseId, submitData);
      // Show success message
    } else {
      await courseService.createCourse(submitData);
      // Show success message
    }
    
    router.push('/admin/courses');
  } catch (error) {
    console.error('Error saving course:', error);
    // Show error message
  } finally {
    loading.value = false;
  }
};



// Handle price input changes
const handlePriceInput = (event) => {
  const inputValue = event.target.value;
  const parsedPrice = parseMoneyInput(inputValue);
  form.value.price = parsedPrice;
};

const getStatusText = (status) => {
  const statusMap = {
    draft: 'پیش‌نویس',
    published: 'منتشر شده',
    archived: 'آرشیو شده'
  };
  return statusMap[status] || status;
};

const getLevelText = (level) => {
  const levelMap = {
    beginner: 'مبتدی',
    intermediate: 'متوسط',
    advanced: 'پیشرفته'
  };
  return levelMap[level] || level;
};

const handleThumbnailUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    // Validate file size (10MB = 10 * 1024 * 1024 bytes)
    if (file.size > 10 * 1024 * 1024) {
      alert('حجم فایل نباید بیشتر از 10 مگابایت باشد');
      return;
    }

    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
      alert('فقط فایل‌های تصویری مجاز هستند');
      return;
    }

    selectedFile.value = file;
    
    // Create preview URL
    const reader = new FileReader();
    reader.onload = (e) => {
      thumbnailPreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const removeThumbnail = () => {
  form.value.thumbnail = '';
  thumbnailPreview.value = '';
  selectedFile.value = null;
  if (fileInput.value) {
    fileInput.value.value = '';
  }
};

const uploadThumbnail = async () => {
  if (!selectedFile.value) {
    return null;
  }

  try {
    const formData = new FormData();
    formData.append('thumbnail', selectedFile.value);

    const response = await fetch('/api/admin/upload/course-thumbnail', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('admin_token')}`,
      },
      body: formData
    });

    if (!response.ok) {
      throw new Error('Upload failed');
    }

    const result = await response.json();
    if (result.success) {
      return result.data.path; // Return the stored path
    } else {
      throw new Error(result.message || 'Upload failed');
    }
  } catch (error) {
    console.error('Error uploading thumbnail:', error);
    alert('خطا در آپلود تصویر: ' + error.message);
    return null;
  }
};
</script>

