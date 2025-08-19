<template>
  <div>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
      <h1 class="text-xl font-vazir text-white mb-4 sm:mb-0">
        {{ isEditing ? 'ویرایش مطلب' : 'ایجاد مطلب جدید' }}
      </h1>
      <div class="flex space-x-2 space-x-reverse">
        <button 
          type="button" 
          @click="router.push('/admin/posts')" 
          class="px-3 py-2 bg-white/10 text-white rounded-lg hover:bg-white/20 transition-colors duration-200 text-sm font-vazir"
        >
          انصراف
        </button>
        <button 
          type="button" 
          @click="savePost" 
          :disabled="isSaving"
          class="px-3 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors duration-200 flex items-center text-sm font-vazir"
        >
          <font-awesome-icon v-if="isSaving" icon="circle-notch" class="animate-spin ml-1" />
          <font-awesome-icon v-else icon="save" class="ml-1" />
          {{ isSaving ? 'در حال ذخیره...' : 'ذخیره مطلب' }}
        </button>
      </div>
    </div>
    
    <!-- Alert for errors -->
    <div v-if="error" class="bg-red-500/20 border border-red-500/30 text-red-400 px-4 py-3 rounded-lg mb-6 font-vazir">
      {{ error }}
    </div>
    
    <!-- Blog post form -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Main content column -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Title and slug -->
        <div class="bg-black/50 rounded-lg border border-white/10 p-6">
          <div class="mb-4">
            <label for="title" class="block text-white font-vazir mb-2">عنوان مطلب</label>
            <input 
              v-model="post.title" 
              type="text" 
              id="title" 
              class="w-full bg-black/20 border border-white/10 rounded-lg py-2 px-4 font-vazir text-white/90 focus:outline-none focus:border-primary/50"
              placeholder="عنوان مطلب را وارد کنید"
            />
          </div>
          
          <div>
            <label for="slug" class="block text-white font-vazir mb-2">نامک (slug)</label>
            <div class="flex">
              <input 
                v-model="post.slug" 
                type="text" 
                id="slug" 
                class="w-full bg-black/20 border border-white/10 rounded-lg py-2 px-4 font-vazir text-white/90 focus:outline-none focus:border-primary/50"
                placeholder="نامک-مطلب"
                dir="ltr"
              />
              <button 
                @click="generateSlug" 
                type="button"
                class="bg-white/10 hover:bg-white/20 text-white px-3 rounded-lg mr-2"
                title="ساخت خودکار از عنوان"
              >
                <font-awesome-icon icon="magic" />
              </button>
            </div>
            <p class="mt-1 text-white/50 text-xs font-vazir">
              نامک در URL مطلب استفاده می‌شود، مثال: example.com/blog/{{ post.slug || 'نامک-مطلب' }}
            </p>
          </div>
        </div>
        
        <!-- Content -->
        <div class="bg-black/50 rounded-lg border border-white/10 p-6">
          <div class="mb-4">
            <label for="summary" class="block text-white font-vazir mb-2">خلاصه مطلب</label>
            <textarea 
              v-model="post.summary" 
              id="summary" 
              rows="3"
              class="w-full bg-black/20 border border-white/10 rounded-lg py-2 px-4 font-vazir text-white/90 focus:outline-none focus:border-primary/50 resize-none"
              placeholder="خلاصه‌ای از مطلب را وارد کنید"
            ></textarea>
          </div>
          
          <div>
            <label for="content" class="block text-white font-vazir mb-2">متن مطلب</label>
            <p class="text-white/50 text-xs mb-3 font-vazir">
              ویرایشگر بلوکی مدرن با پشتیبانی از عناوین، فهرست، نقل‌قول، تصاویر، ویدیو، لینک و بلوک‌های کد. خروجی HTML برای سازگاری با مطالب قدیمی حفظ می‌شود.
            </p>
            <BlockEditor
              v-model="post.content"
              :upload-url="uploadEndpoint"
              :auth-token="adminToken"
            />
          </div>
        </div>
      </div>
      
      <!-- Sidebar column -->
      <div class="lg:col-span-1 space-y-6">
        <!-- Status and Publish -->
        <div class="bg-black/50 rounded-lg border border-white/10 p-6">
          <h3 class="text-white font-vazir mb-4">انتشار</h3>
          
          <div class="mb-4">
            <div class="flex items-center">
              <input 
                v-model="post.published" 
                type="checkbox" 
                id="published" 
                class="w-5 h-5 bg-black/20 border border-white/10 rounded text-primary focus:ring-primary focus:ring-opacity-25"
              />
              <label for="published" class="text-white font-vazir mr-2">منتشر شده</label>
            </div>
            <p class="mt-1 text-white/50 text-xs font-vazir">
              اگر این گزینه را انتخاب نکنید، مطلب به صورت پیش‌نویس ذخیره می‌شود.
            </p>
          </div>
          
          <div class="mb-4">
            <label for="published_at" class="block text-white font-vazir mb-2">تاریخ انتشار</label>
            <input 
              v-model="post.published_at" 
              type="datetime-local" 
              id="published_at" 
              class="w-full bg-black/20 border border-white/10 rounded-lg py-2 px-4 font-vazir text-white/90 focus:outline-none focus:border-primary/50"
              dir="ltr"
            />
          </div>
        </div>
        
        <!-- Category -->
        <div class="bg-black/50 rounded-lg border border-white/10 p-6">
          <h3 class="text-white font-vazir mb-4">دسته‌بندی</h3>
          
          <div class="relative">
            <select 
              v-model="post.category_id" 
              class="w-full bg-black/20 border border-white/10 rounded-lg py-2 px-4 font-vazir text-white/90 focus:outline-none focus:border-primary/50 appearance-none"
            >
              <option :value="null">بدون دسته‌بندی</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center px-4 text-white/50">
              <font-awesome-icon icon="chevron-down" class="h-4 w-4" />
            </div>
          </div>
          
          <div class="mt-4 flex justify-end">
            <router-link 
              to="/admin/categories/create" 
              class="text-primary text-sm hover:text-primary-light transition-colors duration-150 font-vazir flex items-center"
            >
              <font-awesome-icon icon="plus" class="ml-1" />
              افزودن دسته‌بندی جدید
            </router-link>
          </div>
        </div>
        
        <!-- Featured Image -->
        <div class="bg-black/50 rounded-lg border border-white/10 p-6">
          <h3 class="text-white font-vazir mb-4">تصویر شاخص</h3>
          
          <div v-if="post.image" class="mb-4">
            <p class="block text-white font-vazir mb-2">پیش‌نمایش:</p>
            <div class="relative rounded overflow-hidden h-48 bg-black/50">
              <img 
                :src="post.image" 
                alt="Preview" 
                class="w-full h-full object-cover"
                @error="imageError = true"
              />
              <div v-if="imageError" class="absolute inset-0 flex items-center justify-center">
                <span class="text-red-400 text-sm font-vazir">خطا در بارگذاری تصویر</span>
              </div>
              <button 
                @click="removeImage" 
                class="absolute top-2 right-2 bg-red-500/80 text-white rounded-full w-8 h-8 flex items-center justify-center hover:bg-red-600/80 transition-colors duration-200"
                title="حذف تصویر"
              >
                <font-awesome-icon icon="times" />
              </button>
            </div>
          </div>
          
          <div v-if="!post.image">
            <file-uploader 
              :upload-endpoint="uploadEndpoint"
              accept="image/jpeg,image/png,image/webp"
              :max-size="5 * 1024 * 1024"
              @upload-success="handleImageUploadSuccess"
              @upload-error="handleImageUploadError"
            />
          </div>
          
          <div v-if="post.image" class="mt-4">
            <label for="image" class="block text-white font-vazir mb-2">آدرس تصویر</label>
            <input 
              v-model="post.image" 
              type="url" 
              id="image" 
              class="w-full bg-black/20 border border-white/10 rounded-lg py-2 px-4 font-vazir text-white/90 focus:outline-none focus:border-primary/50"
              placeholder="https://example.com/image.jpg"
              dir="ltr"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import FileUploader from '@/components/admin/FileUploader.vue';
import BlockEditor from '@/components/admin/BlockEditor.vue';

const route = useRoute();
const router = useRouter();
const error = ref('');
const isSaving = ref(false);
const imageError = ref(false);
const categories = ref([]);

// API URL
const apiUrl = import.meta.env.VITE_API_BASE_URL;
const adminApiUrl = computed(() => `${apiUrl}/admin`);
const uploadEndpoint = computed(() => `${adminApiUrl.value}/upload/image`);
const adminToken = localStorage.getItem('admin_token') || '';

// Form state
const post = ref({
  title: '',
  slug: '',
  summary: '',
  content: '',
  image: '',
  published: false,
  published_at: formatDateTime(new Date()),
  category_id: null
});

// Check if we're editing
const isEditing = computed(() => !!route.params.id);

// Format date for datetime-local input
function formatDateTime(date) {
  const d = new Date(date);
  // Adjust for timezone offset
  const localDate = new Date(d.getTime() - d.getTimezoneOffset() * 60000);
  return localDate.toISOString().slice(0, 16);
}

// Generate slug from title
function generateSlug() {
  if (!post.value.title) {
    error.value = 'ابتدا عنوان مطلب را وارد کنید.';
    return;
  }
  
  error.value = '';
  
  // Convert to lowercase, replace spaces with hyphens, remove special chars
  const slug = post.value.title
    .toLowerCase()
    .replace(/[\s_]+/g, '-')
    .replace(/[^\u0600-\u06FF\uFB8A\u067E\u0686\u06AF\u200C\u200Fa-z0-9-]/g, '')
    .replace(/-+/g, '-')
    .replace(/^-+|-+$/g, '');
  
  post.value.slug = slug;
}

// Fetch categories
async function fetchCategories() {
  try {
    const response = await axios.get(`${apiUrl}/categories`);
    
    if (response.data && Array.isArray(response.data.data)) {
      categories.value = response.data.data;
    } else if (Array.isArray(response.data)) {
      categories.value = response.data;
    }
  } catch (err) {
    console.error('Error fetching categories:', err);
  }
}

// Fetch post data if editing
async function fetchPost(id) {
  try {
    console.log('Fetching post with ID:', id);
    
    // Use the admin API endpoint
    const response = await axios.get(`${adminApiUrl.value}/posts/${id}`, {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('admin_token')}`
      }
    });
    
    console.log('Response data:', response.data);
    
    // Extract the post data from the nested data structure that Laravel API resources return
    let postData;
    if (response.data && response.data.data) {
      // API returned a wrapped response (Laravel API Resource)
      postData = response.data.data;
    } else {
      // API returned a direct response
      postData = response.data;
    }
    
    // Format the date for the input
    if (postData.published_at) {
      postData.published_at = formatDateTime(postData.published_at);
    } else {
      postData.published_at = formatDateTime(new Date());
    }
    
    // Set category_id if it exists
    if (postData.category) {
      postData.category_id = postData.category.id;
    }
    
    console.log('Post data after processing:', postData);
    post.value = postData;
    console.log('Final post value:', post.value);
  } catch (err) {
    console.error('Error fetching post:', err);
    error.value = 'خطا در بارگذاری اطلاعات مطلب. لطفا دوباره تلاش کنید.';
  }
}

// Save the post
async function savePost() {
  try {
    if (!post.value.title) {
      error.value = 'عنوان مطلب الزامی است.';
      return;
    }
    
    if (!post.value.slug) {
      generateSlug();
    }
    
    isSaving.value = true;
    error.value = '';
    
    // Prepare data for API
    const postData = {
      title: post.value.title,
      slug: post.value.slug,
      summary: post.value.summary,
      content: post.value.content,
      image: post.value.image,
      published: post.value.published,
      published_at: post.value.published_at,
      category_id: post.value.category_id
    };
    
    // We need to use the admin routes for saving posts since the public routes are read-only
    let response;
    const token = localStorage.getItem('admin_token');
    const headers = {
      'Authorization': `Bearer ${token}`
    };
    
    console.log('Saving post with data:', postData);
    console.log('Using auth token:', token ? 'Token exists' : 'No token');
    
    if (isEditing.value) {
      // Update existing post
      response = await axios.put(
        `${adminApiUrl.value}/posts/${route.params.id}`, 
        postData,
        { headers }
      );
    } else {
      // Create new post
      response = await axios.post(
        `${adminApiUrl.value}/posts`, 
        postData,
        { headers }
      );
    }
    
    console.log('Save response:', response.data);
    
    // Redirect to posts list
    router.push('/admin/posts');
  } catch (err) {
    console.error('Error saving post:', err);
    if (!error.value) {
      error.value = err.response?.data?.message || 'خطا در ذخیره مطلب. لطفا دوباره تلاش کنید.';
    }
  } finally {
    isSaving.value = false;
  }
}

// Reset image error when image URL changes
watch(() => post.value.image, () => {
  imageError.value = false;
});

// Handle image upload success
function handleImageUploadSuccess(imageUrl) {
  post.value.image = imageUrl;
  imageError.value = false;
}

// Handle image upload error
function handleImageUploadError(errorMessage) {
  error.value = errorMessage;
}

// Remove uploaded image
function removeImage() {
  post.value.image = '';
}

onMounted(async () => {
  await fetchCategories();
  
  if (isEditing.value) {
    await fetchPost(route.params.id);
  }
});
</script> 