<template>
  <div>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
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
      <!-- Main: title + body only -->
      <div class="lg:col-span-2 space-y-6">
        <div class="bg-black/50 rounded-lg border border-white/10 p-6">
          <label for="title" class="block text-white font-vazir mb-2">عنوان مطلب</label>
          <input 
            v-model="post.title" 
            type="text" 
            id="title" 
            class="w-full bg-black/20 border border-white/10 rounded-lg py-2 px-4 font-vazir text-white/90 focus:outline-none focus:border-primary/50"
            placeholder="عنوان مطلب را وارد کنید"
          />
        </div>
        
        <div class="bg-black/50 rounded-lg border border-white/10 p-6">
          <label class="block text-white font-vazir mb-2">متن مطلب</label>
          <div
            v-if="isEditing && !blockEditorReady"
            class="min-h-[18rem] rounded-lg border border-white/10 bg-black/20 flex items-center justify-center text-white/50 font-vazir text-sm"
          >
            در حال بارگذاری ویرایشگر…
          </div>
          <BlockEditor
            v-else
            v-model="post.content"
            :upload-url="uploadEndpoint"
            :auth-token="adminToken"
          />
        </div>
      </div>
      
      <PostFormSidebar
        v-model:post="post"
        :categories="categories"
        :upload-endpoint="uploadEndpoint"
        @generate-slug="generateSlug"
        @upload-error="handleImageUploadError"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import BlockEditor from '@/components/admin/BlockEditor.vue';
import PostFormSidebar from '@/components/admin/posts/PostFormSidebar.vue';

const route = useRoute();
const router = useRouter();
const error = ref('');
const isSaving = ref(false);
const categories = ref([]);
/** Mount TipTap only after post HTML is loaded when editing — avoids empty→full setContent ProseMirror bugs */
const blockEditorReady = ref(!route.params.id);

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
  blockEditorReady.value = false;
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
  } finally {
    blockEditorReady.value = true;
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

function handleImageUploadError(errorMessage) {
  error.value = errorMessage;
}

onMounted(async () => {
  await fetchCategories();
  
  if (isEditing.value) {
    await fetchPost(route.params.id);
  }
});
</script> 