<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="px-4 py-6 sm:px-0">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">مدیریت محتوای دوره</h1>
            <p class="mt-1 text-sm text-gray-600">
              {{ course?.title || 'در حال بارگذاری...' }}
            </p>
          </div>
          <div class="flex space-x-3 rtl:space-x-reverse">
            <router-link
              :to="{ name: 'admin-courses' }"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              <svg class="w-4 h-4 ml-2 rtl:ml-0 rtl:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
              </svg>
              بازگشت
            </router-link>
            <button
              @click="showAddContentModal = true"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              <svg class="w-4 h-4 ml-2 rtl:ml-0 rtl:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
              </svg>
              افزودن محتوا
            </button>
          </div>
        </div>
      </div>

      <!-- Content List -->
      <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div v-if="loading" class="p-8 text-center">
          <div class="inline-flex items-center">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            در حال بارگذاری...
          </div>
        </div>

        <div v-else-if="contents.length === 0" class="p-8 text-center">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">هیچ محتوایی یافت نشد</h3>
          <p class="mt-1 text-sm text-gray-500">برای شروع، محتوای جدیدی اضافه کنید.</p>
        </div>

                 <div v-else class="divide-y divide-gray-200">
           <div v-if="reordering" class="px-4 py-2 bg-blue-50 border-l-4 border-blue-400">
             <div class="flex items-center text-blue-700">
               <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                 <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                 <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
               </svg>
               در حال تغییر ترتیب محتوا...
             </div>
           </div>
           <div
             v-for="content in contents"
             :key="content.id"
             :draggable="!reordering"
             @dragstart="dragStart($event, content)"
             @dragover="dragOver($event, content)"
             @drop="drop($event, content)"
             :class="[
               'px-4 py-4 sm:px-6 transition-colors duration-150',
               reordering ? 'cursor-not-allowed' : 'cursor-move',
               draggedContent?.id === content.id ? 'opacity-50 bg-blue-50' : 'hover:bg-gray-50',
               draggedContent && draggedContent.id !== content.id ? 'border-l-4 border-l-transparent hover:border-l-indigo-400' : ''
             ]"
           >
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-4 rtl:space-x-reverse">
                                 <!-- Drag Handle -->
                 <div 
                   :class="[
                     'text-gray-400',
                     reordering ? 'cursor-not-allowed opacity-50' : 'cursor-move hover:text-gray-600'
                   ]"
                   :title="reordering ? 'در حال تغییر ترتیب...' : 'برای تغییر ترتیب بکشید'"
                 >
                   <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                     <path d="M7 2a2 2 0 1 1 .001 4.001A2 2 0 0 1 7 2zm0 6a2 2 0 1 1 .001 4.001A2 2 0 0 1 7 8zm0 6a2 2 0 1 1 .001 4.001A2 2 0 0 1 7 14zm6-8a2 2 0 1 1-.001-4.001A2 2 0 0 1 13 6zm0 2a2 2 0 1 1 .001 4.001A2 2 0 0 1 13 8zm0 6a2 2 0 1 1 .001 4.001A2 2 0 0 1 13 14z"></path>
                   </svg>
                 </div>

                <!-- Content Icon -->
                <div class="flex-shrink-0">
                  <div v-if="content.type === 'video'" class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
                    </svg>
                  </div>
                  <div v-else class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                    </svg>
                  </div>
                </div>

                <!-- Content Info -->
                <div class="min-w-0 flex-1">
                  <div class="flex items-center space-x-2 rtl:space-x-reverse">
                    <h3 class="text-sm font-medium text-gray-900 truncate">
                      {{ content.title }}
                    </h3>
                    <span v-if="content.is_free" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                      رایگان
                    </span>
                  </div>
                  <div class="mt-1 flex items-center space-x-4 rtl:space-x-reverse text-sm text-gray-500">
                    <span>{{ content.type === 'video' ? 'ویدیو' : 'متن' }}</span>
                    <span>ترتیب: {{ content.order }}</span>
                    <span v-if="content.type === 'video' && content.video_duration">
                      مدت: {{ formatDuration(content.video_duration) }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Actions -->
              <div class="flex items-center space-x-2 rtl:space-x-reverse">
                <button
                  @click="editContent(content)"
                  class="text-indigo-600 hover:text-indigo-900 p-1 rounded"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                </button>
                <button
                  @click="deleteContent(content)"
                  class="text-red-600 hover:text-red-900 p-1 rounded"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add/Edit Content Modal -->
    <div v-if="showAddContentModal || showEditContentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">
              {{ showEditContentModal ? 'ویرایش محتوا' : 'افزودن محتوا جدید' }}
            </h3>
            <button
              @click="closeModal"
              class="text-gray-400 hover:text-gray-600"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>

          <form @submit.prevent="saveContent" class="space-y-4">
            <!-- Content Type -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">نوع محتوا</label>
              <div class="flex space-x-4 rtl:space-x-reverse">
                <label class="flex items-center">
                  <input
                    v-model="contentForm.type"
                    type="radio"
                    value="text"
                    class="ml-2 rtl:ml-0 rtl:mr-2"
                  >
                  متن
                </label>
                <label class="flex items-center">
                  <input
                    v-model="contentForm.type"
                    type="radio"
                    value="video"
                    class="ml-2 rtl:ml-0 rtl:mr-0 rtl:mr-2"
                  >
                  ویدیو
                </label>
              </div>
            </div>

            <!-- Title -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">عنوان</label>
              <input
                v-model="contentForm.title"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="عنوان محتوا را وارد کنید"
              >
            </div>

            <!-- Content -->
            <div v-if="contentForm.type === 'text'">
              <label class="block text-sm font-medium text-gray-700 mb-2">محتوا</label>
              <textarea
                v-model="contentForm.content"
                rows="6"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="محتوا را وارد کنید"
              ></textarea>
            </div>

            <!-- Video Upload -->
            <div v-if="contentForm.type === 'video'">
              <label class="block text-sm font-medium text-gray-700 mb-2">آپلود ویدیو</label>
              <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                <div v-if="!videoFile && !contentForm.video_path">
                  <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                  </svg>
                  <div class="mt-4">
                    <label class="cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                      <span>انتخاب فایل</span>
                      <input
                        ref="videoInput"
                        type="file"
                        accept="video/*"
                        class="sr-only"
                        @change="handleVideoSelect"
                      >
                    </label>
                  </div>
                  <p class="mt-1 text-xs text-gray-500">MP4, AVI, MOV تا 100MB</p>
                </div>
                <div v-else class="text-center">
                  <div class="flex items-center justify-center space-x-2 rtl:space-x-reverse">
                    <svg class="w-8 h-8 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-sm text-gray-900">{{ videoFile ? videoFile.name : 'ویدیو آپلود شده' }}</span>
                  </div>
                  <button
                    type="button"
                    @click="removeVideo"
                    class="mt-2 text-sm text-red-600 hover:text-red-500"
                  >
                    حذف
                  </button>
                </div>
              </div>
            </div>

            <!-- Order -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">ترتیب</label>
              <input
                v-model.number="contentForm.order"
                type="number"
                min="1"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="ترتیب نمایش"
              >
            </div>

            <!-- Is Free -->
            <div>
              <label class="flex items-center">
                <input
                  v-model="contentForm.is_free"
                  type="checkbox"
                  class="ml-2 rtl:ml-0 rtl:mr-2 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                >
                <span class="text-sm text-gray-700">این محتوا رایگان است</span>
              </label>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-3 rtl:space-x-reverse pt-4">
              <button
                type="button"
                @click="closeModal"
                class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                انصراف
              </button>
              <button
                type="submit"
                :disabled="saving"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
              >
                <svg v-if="saving" class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ saving ? 'در حال ذخیره...' : 'ذخیره' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900 mt-4">حذف محتوا</h3>
          <p class="mt-2 text-sm text-gray-500">
            آیا مطمئن هستید که می‌خواهید این محتوا را حذف کنید؟ این عمل قابل بازگشت نیست.
          </p>
          <div class="flex justify-center space-x-3 rtl:space-x-reverse mt-6">
            <button
              @click="showDeleteModal = false"
              class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              انصراف
            </button>
            <button
              @click="confirmDelete"
              :disabled="deleting"
              class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50"
            >
              {{ deleting ? 'در حال حذف...' : 'حذف' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { courseService } from '@/services/courseService'
import { toast } from 'vue3-toastify'

export default {
  name: 'CourseContents',
  setup() {
    const route = useRoute()
    const router = useRouter()
    
    const courseId = route.params.courseId
    const course = ref(null)
    const contents = ref([])
         const loading = ref(false)
     const saving = ref(false)
     const deleting = ref(false)
     const reordering = ref(false)
    
    // Modal states
    const showAddContentModal = ref(false)
    const showEditContentModal = ref(false)
    const showDeleteModal = ref(false)
    
         // Form data
     const contentForm = reactive({
       id: null,
       title: '',
       content: '',
       type: 'text',
       order: 1,
       is_free: false,
       video_path: null
     })
    
    // Video file
    const videoFile = ref(null)
    const videoInput = ref(null)
    
         // Content to delete
     const contentToDelete = ref(null)
     
     // Drag and drop
     const draggedContent = ref(null)
     
     // Load course and contents
    const loadData = async () => {
      loading.value = true
      try {
        const [courseData, contentsData] = await Promise.all([
          courseService.getCourse(courseId),
          courseService.getCourseContents(courseId)
        ])
        course.value = courseData
        contents.value = contentsData.sort((a, b) => a.order - b.order)
      } catch (error) {
        console.error('Error loading data:', error)
        toast.error('خطا در بارگذاری اطلاعات')
      } finally {
        loading.value = false
      }
    }
    
    // Format video duration
    const formatDuration = (seconds) => {
      const hours = Math.floor(seconds / 3600)
      const minutes = Math.floor((seconds % 3600) / 60)
      const secs = seconds % 60
      
      if (hours > 0) {
        return `${hours}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`
      }
      return `${minutes}:${secs.toString().padStart(2, '0')}`
    }
    
    // Handle video file selection
    const handleVideoSelect = (event) => {
      const file = event.target.files[0]
      if (file) {
        if (file.size > 100 * 1024 * 1024) { // 100MB limit
          toast.error('حجم فایل نباید بیشتر از 100 مگابایت باشد')
          return
        }
        videoFile.value = file
      }
    }
    
    // Remove video
    const removeVideo = () => {
      videoFile.value = null
      contentForm.video_path = null
      if (videoInput.value) {
        videoInput.value.value = ''
      }
    }
    
    // Show add content modal
    const showAddModal = () => {
      resetForm()
      showAddContentModal.value = true
    }
    
         // Edit content
     const editContent = (content) => {
       Object.assign(contentForm, {
         id: content.id,
         title: content.title,
         content: content.content || '',
         type: content.type,
         order: content.order,
         is_free: content.is_free,
         video_path: content.video_path
       })
       videoFile.value = null
       showEditContentModal.value = true
     }
    
    // Delete content
    const deleteContent = (content) => {
      contentToDelete.value = content
      showDeleteModal.value = true
    }
    
    // Confirm delete
    const confirmDelete = async () => {
      if (!contentToDelete.value) return
      
      deleting.value = true
      try {
        await courseService.deleteCourseContent(contentToDelete.value.id)
        contents.value = contents.value.filter(c => c.id !== contentToDelete.value.id)
        toast.success('محتوا با موفقیت حذف شد')
        showDeleteModal.value = false
        contentToDelete.value = null
      } catch (error) {
        console.error('Error deleting content:', error)
        toast.error('خطا در حذف محتوا')
      } finally {
        deleting.value = false
      }
    }
    
    // Save content
    const saveContent = async () => {
      saving.value = true
      try {
        let videoPath = contentForm.video_path
        
        // Upload video if new file selected
        if (videoFile.value) {
          const formData = new FormData()
          formData.append('video', videoFile.value)
          formData.append('course_id', courseId)
          
          const uploadResult = await courseService.uploadVideo(formData)
          videoPath = uploadResult.video_path
        }
        
        const contentData = {
          title: contentForm.title,
          content: contentForm.content,
          type: contentForm.type,
          order: contentForm.order,
          is_free: contentForm.is_free,
          video_path: videoPath
        }
        
                 if (showEditContentModal.value && contentForm.id) {
           // Update existing content
           await courseService.updateCourseContent(contentForm.id, contentData)
           toast.success('محتوا با موفقیت بروزرسانی شد')
         } else {
           // Create new content
           await courseService.createCourseContent(courseId, contentData)
           toast.success('محتوا با موفقیت اضافه شد')
         }
        
        closeModal()
        loadData()
      } catch (error) {
        console.error('Error saving content:', error)
        toast.error('خطا در ذخیره محتوا')
      } finally {
        saving.value = false
      }
    }
    
    // Close modal
    const closeModal = () => {
      showAddContentModal.value = false
      showEditContentModal.value = false
      resetForm()
    }
    
         // Drag and drop handlers
     const dragStart = (event, content) => {
       draggedContent.value = content
       event.dataTransfer.effectAllowed = 'move'
     }
     
     const dragOver = (event, content) => {
       event.preventDefault()
       if (draggedContent.value && draggedContent.value.id !== content.id) {
         event.dataTransfer.dropEffect = 'move'
       }
     }
     
     const drop = async (event, targetContent) => {
       event.preventDefault()
       if (!draggedContent.value || draggedContent.value.id === targetContent.id) return
       
       reordering.value = true
       try {
         const newOrder = targetContent.order
         const oldOrder = draggedContent.value.order
         
         // Update orders for all affected content
         const contentToUpdate = contents.value.find(c => c.id === draggedContent.value.id)
         if (contentToUpdate) {
           contentToUpdate.order = newOrder
         }
         
         // Reorder contents array
         contents.value.sort((a, b) => a.order - b.order)
         
         // Update order in backend
         await courseService.reorderContents(courseId, {
           content_id: draggedContent.value.id,
           new_order: newOrder
         })
         
         toast.success('ترتیب محتوا با موفقیت تغییر کرد')
       } catch (error) {
         console.error('Error reordering content:', error)
         toast.error('خطا در تغییر ترتیب محتوا')
         // Reload data to restore original order
         loadData()
       } finally {
         reordering.value = false
         draggedContent.value = null
       }
     }
     
     // Reset form
     const resetForm = () => {
       Object.assign(contentForm, {
         id: null,
         title: '',
         content: '',
         type: 'text',
         order: contents.value.length + 1,
         is_free: false,
         video_path: null
       })
       videoFile.value = null
       if (videoInput.value) {
         videoInput.value.value = ''
       }
     }
    
    onMounted(() => {
      loadData()
    })
    
         return {
       course,
       contents,
       loading,
       saving,
       deleting,
       reordering,
       showAddContentModal,
       showEditContentModal,
       showDeleteModal,
       contentForm,
       videoFile,
       videoInput,
       contentToDelete,
       formatDuration,
       handleVideoSelect,
       removeVideo,
       editContent,
       deleteContent,
       confirmDelete,
       saveContent,
       closeModal,
       dragStart,
       dragOver,
       drop
     }
  }
}
</script>
