<template>
  <div
    v-if="showModal"
    class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
    @click.self="$emit('close')"
  >
    <div class="bg-gray-900 border border-white/10 rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
      <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-medium text-white font-vazir">
          {{ isEditing ? 'ویرایش اپیزود' : 'ایجاد اپیزود جدید' }}
        </h3>
        <button
          @click="$emit('close')"
          class="text-gray-400 hover:text-white transition-colors duration-200"
        >
          <font-awesome-icon icon="times" class="h-5 w-5" />
        </button>
      </div>

      <form @submit.prevent="handleSubmit" class="space-y-4">
        <!-- Title -->
        <div>
          <label class="block text-sm font-medium text-gray-300 mb-2 font-vazir">
            عنوان اپیزود *
          </label>
          <input
            v-model="form.title"
            type="text"
            required
            class="w-full px-3 py-2 bg-black/50 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent font-vazir"
            placeholder="عنوان اپیزود را وارد کنید"
          />
        </div>

        <!-- Content/Description -->
        <div>
          <label class="block text-sm font-medium text-gray-300 mb-2 font-vazir">
            توضیحات
          </label>
          <textarea
            v-model="form.content"
            rows="4"
            class="w-full px-3 py-2 bg-black/50 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent font-vazir"
            placeholder="توضیحات اپیزود را وارد کنید"
          ></textarea>
        </div>


        <!-- Video Upload -->
        <div>
          <label class="block text-sm font-medium text-gray-300 mb-2 font-vazir">
            فایل ویدیو *
          </label>

          <!-- Show existing video preview when editing -->
          <div v-if="isEditing && editingContent && editingContent.video_path" class="mb-4">
            <label class="block text-sm font-medium text-gray-300 mb-2 font-vazir">
              ویدیوی فعلی:
            </label>
            <div class="bg-black/30 border border-white/10 rounded-lg p-3">
              <video
                :src="videoUrl"
                controls
                class="w-full max-h-48 rounded-lg"
                preload="metadata"
              >
                مرورگر شما از پخش ویدیو پشتیبانی نمی‌کند.
              </video>
            </div>
          </div>

          <div class="space-y-2">
            <input
              ref="videoInput"
              type="file"
              accept="video/*"
              @change="handleVideoUpload"
              required
              class="hidden"
            />
            <button
              type="button"
              @click="$refs.videoInput.click()"
              class="w-full px-3 py-2 bg-primary/20 border border-primary/30 text-primary rounded-lg hover:bg-primary/30 transition-colors duration-200 font-vazir"
            >
              <font-awesome-icon icon="upload" class="ml-2 h-3 w-3" />
              {{ isEditing && editingContent && editingContent.video_path ? 'تغییر فایل ویدیو' : 'انتخاب فایل ویدیو' }}
            </button>
            <span v-if="selectedVideo" class="text-xs text-gray-400 font-vazir block text-center">
              {{ selectedVideo.name }}
            </span>
            <span v-else-if="isEditing && editingContent && editingContent.video_path" class="text-xs text-gray-400 font-vazir block text-center">
              فایل فعلی: {{ editingContent.video_path.split('/').pop() }}
            </span>
          </div>
        </div>

        <!-- Order -->
        <div>
          <label class="block text-sm font-medium text-gray-300 mb-2 font-vazir">
            ترتیب نمایش
          </label>
          <input
            v-model.number="form.order"
            type="number"
            min="1"
            class="w-full px-3 py-2 bg-black/50 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent font-vazir"
            placeholder="ترتیب نمایش اپیزود"
          />
        </div>

        <!-- Free Content Toggle -->
        <div class="flex items-center">
          <input
            id="is_free"
            v-model="form.is_free"
            type="checkbox"
            class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded"
          />
          <label for="is_free" class="mr-2 block text-sm text-gray-300 font-vazir">
            محتوای رایگان (برای پیش‌نمایش)
          </label>
        </div>



        <!-- Submit Buttons -->
        <div class="flex items-center justify-end space-x-4 space-x-reverse pt-4">
          <button
            type="button"
            @click="$emit('close')"
            :disabled="submitting"
            class="px-4 py-2 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200 font-vazir"
          >
            انصراف
          </button>
          <button
            type="submit"
            :disabled="submitting"
            class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200 font-vazir"
          >
            <span v-if="submitting" class="inline-flex items-center">
              <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white ml-2"></div>
              {{ isEditing ? 'در حال بروزرسانی...' : 'در حال ایجاد...' }}
            </span>
            <span v-else>{{ isEditing ? 'بروزرسانی' : 'ایجاد' }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import courseContentService from '@/services/courseContentService';
import uploadService from '@/services/uploadService';
import axios from 'axios';

// Create axios instance with auth header (same as service)
const createAuthInstance = () => {
  const token = localStorage.getItem('admin_token');
  const instance = axios.create({
    baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Accept': 'application/json'
    }
  });
  return instance;
};



const props = defineProps({
  showModal: {
    type: Boolean,
    default: false
  },
  isEditing: {
    type: Boolean,
    default: false
  },
  editingContent: {
    type: Object,
    default: null
  },
  courseId: {
    type: String,
    required: true
  }
});

const emit = defineEmits(['close', 'submit', 'success', 'error']);

// Reactive data
const submitting = ref(false);
const selectedVideo = ref(null);

// Form data
const form = ref({
  title: '',
  content: '',
  order: '',
  is_free: false
});

// Computed properties
const videoUrl = computed(() => {
  if (!props.editingContent?.video_path) return '';
  const baseUrl = (import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000').replace('/api', '');
  return baseUrl + '/storage/' + props.editingContent.video_path;
});

// Watch for modal opening to reset form
watch(() => props.showModal, (newVal) => {
  if (newVal) {
    if (props.isEditing && props.editingContent) {
      // Populate form with existing content data
      form.value = {
        title: props.editingContent.title,
        content: props.editingContent.content,
        order: props.editingContent.order,
        is_free: props.editingContent.is_free
      };
    } else {
      // Reset form for new content
      form.value = {
        title: '',
        content: '',
        order: '',
        is_free: false
      };
    }
    // Clear selected video
    selectedVideo.value = null;
  }
});

// Utility methods
const formatDuration = (seconds) => {
  if (!seconds) return '0:00';
  const minutes = Math.floor(seconds / 60);
  const remainingSeconds = seconds % 60;
  return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
};

// File handling methods
const handleVideoUpload = (event) => {
  if (!event.target.files.length) return;

  const file = event.target.files[0];
  console.log('📹 Selected video file:', {
    name: file.name,
    size: file.size,
    type: file.type
  });

  // Validate video file using uploadService
  const validation = uploadService.validateVideo(file);

  if (!validation.valid) {
    alert(validation.errors.join('\n'));
    event.target.value = '';
    return;
  }

  selectedVideo.value = file;
};



const handleSubmit = async () => {
  try {
    submitting.value = true;

    if (props.isEditing) {
      // Update existing content with video
      console.log('📝 Updating content with video');
      await uploadService.updateVideo(props.courseId, props.editingContent.id, selectedVideo.value, form.value);
      emit('success', { action: 'update', type: 'video', data: form.value });
    } else {
      // Create new content with video
      console.log('📝 Creating content with video');
      if (!selectedVideo.value) {
        alert('لطفاً فایل ویدیو را انتخاب کنید.');
        return;
      }
      await uploadService.uploadVideo(props.courseId, selectedVideo.value, form.value);
      emit('success', { action: 'create', type: 'video', data: form.value });
    }

    emit('close');
  } catch (error) {
    console.error('❌ Content operation failed:', error);

    // User-friendly error message
    let errorMessage = 'خطا در آپلود ویدیو';

    // Show detailed validation errors if available
    if (error.response?.data?.errors) {
      console.error('Validation Errors:', error.response.data.errors);
      const errorDetails = Object.values(error.response.data.errors).flat().join(', ');
      errorMessage += `: ${errorDetails}`;
    } else if (error.response?.data?.message) {
      errorMessage += `: ${error.response.data.message}`;
    } else if (error.message) {
      errorMessage += `: ${error.message}`;
    }

    alert(`${errorMessage}\n\nلطفاً دوباره امتحان کنید.`);
    emit('error', error);
  } finally {
    submitting.value = false;
  }
};
</script>

<style scoped>
</style>
