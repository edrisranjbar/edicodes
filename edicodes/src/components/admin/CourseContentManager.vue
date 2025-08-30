<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-xl font-bold text-white font-vazir">مدیریت محتوای دوره</h2>
        <p class="text-gray-400 mt-1 font-vazir">{{ course?.title }}</p>
      </div>
      <button
        @click="showCreateModal = true"
        class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary/90 transition-colors duration-200 font-vazir"
      >
        <font-awesome-icon icon="plus" class="ml-2 h-4 w-4" />
        افزودن اپیزود جدید
      </button>
    </div>

    <!-- Content List -->
    <div class="bg-black/50 border border-white/10 rounded-lg p-6">
      <div v-if="loading" class="flex justify-center py-8">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
      </div>

      <div v-else-if="contents.length === 0" class="text-center py-8">
        <font-awesome-icon icon="folder-open" class="h-12 w-12 text-gray-500 mx-auto mb-4" />
        <p class="text-gray-400 font-vazir">هنوز محتوایی برای این دوره ایجاد نشده است</p>
        <button
          @click="showCreateModal = true"
          class="mt-4 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors duration-200 font-vazir"
        >
          ایجاد اولین اپیزود
        </button>
      </div>

      <div v-else class="space-y-4">
        <div
          v-for="content in sortedContents"
          :key="content.id"
          class="bg-black/30 border border-white/10 rounded-lg p-4 hover:bg-black/40 transition-colors duration-200"
        >
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4 space-x-reverse">
              <!-- Content Type Icon -->
              <div class="flex-shrink-0">
                <div class="w-10 h-10 rounded-lg bg-primary/20 flex items-center justify-center">
                  <font-awesome-icon
                    :icon="getContentTypeIcon(content.type)"
                    class="h-5 w-5 text-primary"
                  />
                </div>
              </div>

              <!-- Content Info -->
              <div class="flex-1">
                <div class="flex items-center space-x-2 space-x-reverse">
                  <h3 class="text-lg font-medium text-white font-vazir">{{ content.title }}</h3>
                  <span
                    v-if="content.is_free"
                    class="px-2 py-1 bg-green-500/20 text-green-400 text-xs rounded-full font-vazir"
                  >
                    رایگان
                  </span>
                </div>
                
                <div class="flex items-center space-x-4 space-x-reverse mt-2 text-sm text-gray-400">
                  <span class="font-vazir">{{ getContentTypeLabel(content.type) }}</span>
                  <span class="font-vazir">ترتیب: {{ content.order }}</span>
                  
                  <!-- File info for file type -->
                  <span v-if="content.type === 'file' && content.file_name" class="font-vazir">
                    {{ content.file_name }}
                  </span>
                  
                  <!-- Video duration for video type -->
                  <span v-if="content.type === 'video' && content.video_duration" class="font-vazir">
                    {{ formatDuration(content.video_duration) }}
                  </span>
                </div>

                <!-- Content preview -->
                <p v-if="content.content" class="text-gray-300 mt-2 text-sm font-vazir line-clamp-2">
                  {{ content.content }}
                </p>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center space-x-2 space-x-reverse">
              <button
                @click="editContent(content)"
                class="p-2 text-blue-400 hover:text-blue-300 transition-colors duration-200"
                title="ویرایش"
              >
                <font-awesome-icon icon="edit" class="h-4 w-4" />
              </button>
              <button
                @click="deleteContent(content)"
                class="p-2 text-red-400 hover:text-red-300 transition-colors duration-200"
                title="حذف"
              >
                <font-awesome-icon icon="trash" class="h-4 w-4" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div
      v-if="showCreateModal || showEditModal"
      class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
      @click.self="closeModal"
    >
      <div class="bg-gray-900 border border-white/10 rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-lg font-medium text-white font-vazir">
            {{ showEditModal ? 'ویرایش اپیزود' : 'ایجاد اپیزود جدید' }}
          </h3>
          <button
            @click="closeModal"
            class="text-gray-400 hover:text-white transition-colors duration-200"
          >
            <font-awesome-icon icon="times" class="h-5 w-5" />
          </button>
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-4">
          <!-- Content Type Selection -->
          <div>
            <label class="block text-sm font-medium text-gray-300 mb-2 font-vazir">
              نوع محتوا *
            </label>
            <select
              v-model="form.type"
              required
              class="w-full px-3 py-2 bg-black/50 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent font-vazir"
            >
              <option value="">انتخاب نوع محتوا</option>
              <option value="text">متن</option>
              <option value="video">ویدیو</option>
              <option value="file">فایل</option>
            </select>
          </div>

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

          <!-- File Upload Section -->
          <div v-if="form.type === 'video'">
            <label class="block text-sm font-medium text-gray-300 mb-2 font-vazir">
              فایل ویدیو
            </label>
            <div class="space-y-2">
              <input
                ref="videoInput"
                type="file"
                accept="video/*"
                @change="handleVideoUpload"
                class="hidden"
              />
              <button
                type="button"
                @click="$refs.videoInput.click()"
                class="w-full px-3 py-2 bg-primary/20 border border-primary/30 text-primary rounded-lg hover:bg-primary/30 transition-colors duration-200 font-vazir"
              >
                <font-awesome-icon icon="upload" class="ml-2 h-3 w-3" />
                انتخاب فایل ویدیو
              </button>
              <span v-if="selectedVideo" class="text-xs text-gray-400 font-vazir block text-center">
                {{ selectedVideo.name }}
              </span>
            </div>
          </div>

          <div v-if="form.type === 'file'">
            <label class="block text-sm font-medium text-gray-300 mb-2 font-vazir">
              فایل مستندات
            </label>
            <div class="space-y-2">
              <input
                ref="fileInput"
                type="file"
                accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.zip,.rar"
                @change="handleFileUpload"
                class="hidden"
              />
              <button
                type="button"
                @click="$refs.fileInput.click()"
                class="w-full px-3 py-2 bg-primary/20 border border-primary/30 text-primary rounded-lg hover:bg-primary/30 transition-colors duration-200 font-vazir"
              >
                <font-awesome-icon icon="upload" class="ml-2 h-3 w-3" />
                انتخاب فایل
              </button>
              <span v-if="selectedFile" class="text-xs text-gray-400 font-vazir block text-center">
                {{ selectedFile.name }}
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
              @click="closeModal"
              class="px-4 py-2 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors duration-200 font-vazir"
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
                {{ showEditModal ? 'در حال بروزرسانی...' : 'در حال ایجاد...' }}
              </span>
              <span v-else>{{ showEditModal ? 'بروزرسانی' : 'ایجاد' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import courseContentService from '@/services/courseContentService';

const route = useRoute();
const courseId = route.params.id;

// Reactive data
const loading = ref(false);
const submitting = ref(false);
const contents = ref([]);
const showCreateModal = ref(false);
const showEditModal = ref(false);
const selectedVideo = ref(null);
const selectedFile = ref(null);
const editingContent = ref(null);

// Form data
const form = ref({
  title: '',
  content: '',
  type: '',
  order: '',
  is_free: false
});

// Computed
const sortedContents = computed(() => {
  return [...contents.value].sort((a, b) => a.order - b.order);
});

// Methods
const fetchContents = async () => {
  try {
    loading.value = true;
    const response = await courseContentService.getContents(courseId);
    contents.value = response.data.data || [];
  } catch (error) {
    console.error('Error fetching contents:', error);
  } finally {
    loading.value = false;
  }
};

const getContentTypeIcon = (type) => {
  const typeMap = {
    text: 'file-lines',
    video: 'video',
    file: 'file'
  };
  return typeMap[type] || 'file';
};

const getContentTypeLabel = (type) => {
  const typeMap = {
    text: 'متن',
    video: 'ویدیو',
    file: 'فایل'
  };
  return typeMap[type] || 'نامشخص';
};

const formatDuration = (seconds) => {
  if (!seconds) return '0:00';
  const minutes = Math.floor(seconds / 60);
  const remainingSeconds = seconds % 60;
  return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
};

const handleVideoUpload = (event) => {
  selectedVideo.value = event.target.files[0];
};

const handleFileUpload = (event) => {
  selectedFile.value = event.target.files[0];
};

const editContent = (content) => {
  editingContent.value = content;
  form.value = {
    title: content.title,
    content: content.content,
    type: content.type,
    order: content.order,
    is_free: content.is_free
  };
  showEditModal.value = true;
};

const deleteContent = async (content) => {
  if (!confirm('آیا از حذف این اپیزود اطمینان دارید؟')) return;

  try {
    await courseContentService.deleteContent(courseId, content.id);
    await fetchContents();
  } catch (error) {
    console.error('Error deleting content:', error);
    alert('خطا در حذف اپیزود');
  }
};

const handleSubmit = async () => {
  try {
    submitting.value = true;

    if (showEditModal.value) {
      // Update existing content
      await courseContentService.updateContent(courseId, editingContent.value.id, form.value);
    } else {
      // Create new content
      if (form.value.type === 'video' && selectedVideo.value) {
        await courseContentService.createVideoEpisode(courseId, form.value, selectedVideo.value);
      } else if (form.value.type === 'file' && selectedFile.value) {
        await courseContentService.createFileEpisode(courseId, form.value, selectedFile.value);
      } else {
        await courseContentService.createTextEpisode(courseId, form.value);
      }
    }

    await fetchContents();
    closeModal();
  } catch (error) {
    console.error('Error saving content:', error);
    alert('خطا در ذخیره اپیزود');
  } finally {
    submitting.value = false;
  }
};

const closeModal = () => {
  showCreateModal.value = false;
  showEditModal.value = false;
  editingContent.value = null;
  selectedVideo.value = null;
  selectedFile.value = null;
  form.value = {
    title: '',
    content: '',
    type: '',
    order: '',
    is_free: false
  };
};

// Lifecycle
onMounted(() => {
  fetchContents();
});

// Watch for course changes
watch(() => route.params.id, () => {
  if (route.params.id) {
    fetchContents();
  }
});
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
