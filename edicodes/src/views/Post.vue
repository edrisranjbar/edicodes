<template>
  <div class="pt-16" dir="rtl">
    <!-- Loading state -->
    <div v-if="loading" class="container mx-auto px-4 sm:px-6 lg:px-8 py-32 flex justify-center">
      <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary"></div>
    </div>
    
    <!-- Error state -->
    <div v-else-if="error" class="container mx-auto px-4 sm:px-6 lg:px-8 py-32">
      <div class="max-w-xl mx-auto text-center">
        <div class="w-16 h-16 bg-red-500/10 rounded-lg flex items-center justify-center mb-4 mx-auto">
          <font-awesome-icon icon="exclamation-circle" class="text-2xl text-red-500" />
        </div>
        <h2 class="text-2xl font-bold mb-4 font-vazir">خطایی رخ داد</h2>
        <p class="text-white/70 mb-6 font-vazir">
          متاسفانه در بارگذاری مطلب مشکلی پیش آمد. لطفا دوباره تلاش کنید.
        </p>
        <router-link to="/blog" class="inline-block px-6 py-3 bg-primary/10 text-primary hover:bg-primary/20 rounded-lg transition-colors duration-300 font-vazir">
          بازگشت به وبلاگ
        </router-link>
      </div>
    </div>
    
    <!-- Post Content -->
    <div v-else class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <article class="max-w-4xl mx-auto">
        <!-- Post Header -->
        <header class="mb-12">
          <h1 class="text-4xl font-bold mb-6 font-vazir">{{ post.title }}</h1>
          <div class="flex items-center flex-wrap gap-4 text-white/50 font-vazir">
            <router-link 
              :to="`/blog?category=${post.category?.slug}`" 
              class="rounded-full bg-primary/20 px-3 py-1 text-xs text-primary hover:bg-primary/30 transition-colors"
            >
              {{ post.category?.name || 'عمومی' }}
            </router-link>
            <time class="flex items-center gap-1">
              <font-awesome-icon icon="calendar" class="text-xs" />
              {{ formatDate(post.published_at) }}
            </time>
          </div>
        </header>

        <!-- Post Image -->
        <div class="relative rounded-lg overflow-hidden mb-12 bg-black/50">
          <img 
            :src="post.image || 'https://edrisranjbar.ir/images/blog/default.jpg'" 
            :alt="post.title" 
            class="w-full h-80 md:h-96 object-cover rounded-lg" 
            @error="handleImageError"
          />
          <div v-if="imageError" class="absolute inset-0 flex items-center justify-center">
            <font-awesome-icon icon="book" class="text-6xl text-primary/30" />
          </div>
        </div>

        <!-- Post Content -->
        <div class="prose prose-invert prose-lg max-w-none font-vazir blog-content" v-html="post.content"></div>

        <!-- Donation Box -->
        <div class="mt-10 p-6 bg-black/40 rounded-2xl border border-white/10 text-center">
          <p class="mb-4 text-white/80 font-vazir">
            اگر این محتوا برای شما مفید بود، با حمایت مالی به تولید محتوای رایگان کمک کنید.
          </p>
          <router-link
            to="/donation"
            class="inline-flex items-center gap-2 px-5 py-2 bg-primary/20 text-primary hover:bg-primary/30 rounded-lg transition-colors duration-200 font-vazir"
          >
            <font-awesome-icon icon="heart" />
            <span>حمایت از محتوای رایگان</span>
          </router-link>
        </div>

        <!-- Post Footer -->
        <footer class="mt-12 pt-8 border-t border-white/10">
          <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <router-link to="/blog" class="flex items-center text-primary hover:text-primary-dark transition-colors duration-200 font-vazir">
              <font-awesome-icon icon="arrow-left" class="ml-1" />
              <span>بازگشت به وبلاگ</span>
            </router-link>
            <div class="flex gap-4 items-center">
              <span class="text-sm text-white/50">اشتراک گذاری:</span>
              <a 
                :href="`https://twitter.com/intent/tweet?text=${encodeURIComponent(post.title)}&url=${encodeURIComponent(currentUrl)}`"
                target="_blank"
                rel="noopener noreferrer"
                class="text-white/50 hover:text-white transition-colors duration-200"
                title="اشتراک گذاری در توییتر"
              >
                <font-awesome-icon :icon="['fab', 'twitter']" />
              </a>
              <a 
                :href="`https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(currentUrl)}`"
                target="_blank"
                rel="noopener noreferrer"
                class="text-white/50 hover:text-white transition-colors duration-200"
                title="اشتراک گذاری در لینکدین"
              >
                <font-awesome-icon :icon="['fab', 'linkedin']" />
              </a>
              <a 
                :href="`https://t.me/share/url?url=${encodeURIComponent(currentUrl)}&text=${encodeURIComponent(post.title)}`"
                target="_blank"
                rel="noopener noreferrer"
                class="text-white/50 hover:text-white transition-colors duration-200"
                title="اشتراک گذاری در تلگرام"
              >
                <font-awesome-icon :icon="['fab', 'telegram']" />
              </a>
            </div>
          </div>
        </footer>
        
        <!-- Comments Section -->
        <CommentSection v-if="post.id" :post-id="post.id" />
      </article>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import CommentSection from '../components/CommentSection.vue';

const route = useRoute();
const router = useRouter();
const post = ref({
  title: '',
  summary: '',
  content: '',
  published_at: null,
  category: null,
  image: ''
});
const loading = ref(true);
const error = ref(null);
const imageError = ref(false);

// Create a computed property for the current URL that initializes only after component is mounted
const currentUrl = ref('');

const handleImageError = () => {
  imageError.value = true;
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  // Convert to Persian date format
  return new Intl.DateTimeFormat('fa-IR', options).format(date);
};

const fetchPost = async (slug) => {
  try {
    loading.value = true;
    error.value = null;
    
    console.log('Fetching post with slug:', slug);
    const response = await axios.get(`${import.meta.env.VITE_API_BASE_URL}/posts/slug/${slug}`);
    
    console.log('API Response:', response.data);
    
    // Check if response has data property (Laravel API Resource format)
    if (response.data && response.data.data) {
      // Extract the post data from the nested data property
      post.value = response.data.data;
      console.log('Post data:', post.value);
      
      // Record page view
      await recordPageView(post.value.id);
      
      // Update document title
      if (post.value.title) {
        document.title = `${post.value.title} | ادیکدز`;
      }
    } else {
      // If the response doesn't have data, redirect to NotFound
      router.replace({ name: 'not-found' });
      return;
    }
    
    loading.value = false;
  } catch (err) {
    console.error('Error fetching post:', err);
    
    // Check if it's a 404 error
    if (err.response && err.response.status === 404) {
      // Redirect to the 404 page
      router.replace({ name: 'not-found' });
      return;
    }
    
    error.value = err.message || 'خطا در دریافت اطلاعات';
    loading.value = false;
  }
};

// Record page view
const recordPageView = async (postId) => {
  try {
    await axios.post(`${import.meta.env.VITE_API_BASE_URL}/posts/${postId}/view`);
  } catch (err) {
    console.error('Error recording page view:', err);
  }
};

// Re-fetch when the slug changes
watch(() => route.params.slug, (newSlug) => {
  if (newSlug) {
    fetchPost(newSlug);
  }
});

onMounted(async () => {
  // Set the current URL after component is mounted
  if (typeof window !== 'undefined') {
    currentUrl.value = window.location.href;
  }
  
  if (route.params.slug) {
    await fetchPost(route.params.slug);
  } else {
    router.push('/blog');
  }
});
</script>

<style>
.prose {
  @apply text-white/90;
}

.prose h2 {
  @apply text-2xl font-bold mb-6 mt-10 font-vazir text-primary/90;
}

.prose p {
  @apply mb-6 leading-relaxed font-vazir text-lg;
}

.prose ul {
  @apply list-disc mb-6 pr-6 font-vazir;
}

.prose li {
  @apply mb-2 font-vazir;
}

.blog-content pre {
  @apply my-6 p-4 bg-black/50 rounded-lg border border-white/10 overflow-x-auto;
}

.blog-content code {
  @apply font-mono text-primary-light text-sm;
}

.blog-content pre code {
  @apply text-white/80;
}

.blog-content a {
  @apply text-primary hover:text-primary-light underline;
}

.blog-content blockquote {
  @apply border-r-4 border-primary/50 pr-4 italic my-6 text-white/70;
}
</style> 