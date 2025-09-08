<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-xl font-bold text-white font-vazir">مدیریت محتوای دوره</h2>
        <p class="text-gray-400 mt-1 font-vazir">{{ course?.title }}</p>
      </div>
      <button
        @click="openCreateModal"
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
          @click="openCreateModal"
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

    <!-- Create/Edit Modal Component -->
    <CourseContentModal
      :show-modal="showModal"
      :is-editing="isEditing"
      :editing-content="editingContent"
      :course-id="courseId"
      @close="closeModal"
      @success="handleModalSuccess"
      @error="handleModalError"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import courseContentService from '@/services/courseContentService';
import CourseContentModal from './CourseContentModal.vue';



const props = defineProps({
  course: {
    type: Object,
    required: true
  }
});

const route = useRoute();
const courseId = route.params.id;

// Reactive data
const loading = ref(false);
const contents = ref([]);
const showModal = ref(false);
const isEditing = ref(false);
const editingContent = ref(null);

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
  // All tutorial lessons have video, so all show video icon
  return 'video';
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

const openCreateModal = () => {
  isEditing.value = false;
  editingContent.value = null;
  showModal.value = true;
};

const editContent = (content) => {
  isEditing.value = true;
  editingContent.value = content;
  showModal.value = true;
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

const closeModal = () => {
  showModal.value = false;
  isEditing.value = false;
  editingContent.value = null;
};

const handleModalSuccess = async (result) => {
  console.log('Modal success:', result);
  await fetchContents();
};

const handleModalError = (error) => {
  console.error('Modal error:', error);
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
  line-clamp: 2;
}
</style>
