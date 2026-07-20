<template>
  <div>
    <div v-if="isLoading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary"></div>
    </div>

    <div v-else>
      <!-- Tabs -->
      <div class="bg-black/50 rounded-lg p-2 mb-6 flex space-x-2 space-x-reverse border border-white/10 w-full">
        <button
          @click="activeTab = 'profile'"
          :class="[activeTab === 'profile' ? 'bg-primary/20 text-primary' : 'text-white/70 hover:bg-white/5']"
          class="flex-1 py-2 px-4 text-sm rounded-lg transition-colors duration-200 font-vazir"
        >
          <font-awesome-icon icon="user" class="ml-2" />
          اطلاعات پروفایل
        </button>
        <button
          @click="activeTab = 'password'"
          :class="[activeTab === 'password' ? 'bg-primary/20 text-primary' : 'text-white/70 hover:bg-white/5']"
          class="flex-1 py-2 px-4 text-sm rounded-lg transition-colors duration-200 font-vazir"
        >
          <font-awesome-icon icon="lock" class="ml-2" />
          تغییر رمز عبور
        </button>
        <button
          @click="activeTab = 'youtube'"
          :class="[activeTab === 'youtube' ? 'bg-primary/20 text-primary' : 'text-white/70 hover:bg-white/5']"
          class="flex-1 py-2 px-4 text-sm rounded-lg transition-colors duration-200 font-vazir"
        >
          <font-awesome-icon icon="play" class="ml-2" />
          ویدیوهای یوتیوب
        </button>
      </div>

      <!-- Profile Settings -->
      <div v-if="activeTab === 'profile'" class="bg-black/50 rounded-lg border border-white/10 p-6 mb-6 max-w-xl">
        <h2 class="text-lg font-vazir text-white mb-4">اطلاعات پروفایل</h2>

        <div v-if="notifications.profile.show"
          :class="[notifications.profile.type === 'success' ? 'bg-green-500/10 text-green-500' : 'bg-red-500/10 text-red-500']"
          class="p-3 rounded-lg mb-4 text-sm font-vazir">
          {{ notifications.profile.message }}
        </div>

        <form @submit.prevent="updateProfile">
          <div class="mb-4">
            <label for="name" class="block text-white/70 mb-2 text-sm font-vazir">نام</label>
            <input
              id="name"
              v-model="profile.name"
              type="text"
              class="w-full bg-gray-800 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary transition"
              required
              :disabled="profileLoading"
            />
            <span v-if="validationErrors.name" class="text-red-500 text-xs mt-1 block">{{ validationErrors.name[0] }}</span>
          </div>

          <div class="mb-6">
            <label for="email" class="block text-white/70 mb-2 text-sm font-vazir">ایمیل</label>
            <input
              id="email"
              v-model="profile.email"
              type="email"
              class="w-full bg-gray-800 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary transition"
              required
              :disabled="profileLoading"
            />
            <span v-if="validationErrors.email" class="text-red-500 text-xs mt-1 block">{{ validationErrors.email[0] }}</span>
          </div>

          <button
            type="submit"
            class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors duration-200 flex items-center text-sm font-vazir"
            :disabled="profileLoading"
          >
            <span v-if="profileLoading" class="inline-block h-4 w-4 border-2 border-t-white/5 border-r-white/5 border-white rounded-full animate-spin ml-2"></span>
            ذخیره تغییرات
          </button>
        </form>
      </div>

      <!-- Password Change -->
      <div v-if="activeTab === 'password'" class="bg-black/50 rounded-lg border border-white/10 p-6 mb-6 max-w-xl">
        <h2 class="text-lg font-vazir text-white mb-4">تغییر رمز عبور</h2>

        <div v-if="notifications.password.show"
          :class="[notifications.password.type === 'success' ? 'bg-green-500/10 text-green-500' : 'bg-red-500/10 text-red-500']"
          class="p-3 rounded-lg mb-4 text-sm font-vazir">
          {{ notifications.password.message }}
        </div>

        <form @submit.prevent="changePassword">
          <div class="mb-4">
            <label for="current_password" class="block text-white/70 mb-2 text-sm font-vazir">رمز عبور فعلی</label>
            <input
              id="current_password"
              v-model="passwordData.current_password"
              type="password"
              class="w-full bg-gray-800 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary transition"
              required
              :disabled="passwordLoading"
            />
            <span v-if="validationErrors.current_password" class="text-red-500 text-xs mt-1 block">{{ validationErrors.current_password[0] }}</span>
          </div>

          <div class="mb-4">
            <label for="new_password" class="block text-white/70 mb-2 text-sm font-vazir">رمز عبور جدید</label>
            <input
              id="new_password"
              v-model="passwordData.new_password"
              type="password"
              class="w-full bg-gray-800 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary transition"
              required
              :disabled="passwordLoading"
            />
            <span v-if="validationErrors.new_password" class="text-red-500 text-xs mt-1 block">{{ validationErrors.new_password[0] }}</span>
          </div>

          <div class="mb-6">
            <label for="new_password_confirmation" class="block text-white/70 mb-2 text-sm font-vazir">تکرار رمز عبور جدید</label>
            <input
              id="new_password_confirmation"
              v-model="passwordData.new_password_confirmation"
              type="password"
              class="w-full bg-gray-800 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary transition"
              required
              :disabled="passwordLoading"
            />
          </div>

          <button
            type="submit"
            class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors duration-200 flex items-center text-sm font-vazir"
            :disabled="passwordLoading"
          >
            <span v-if="passwordLoading" class="inline-block h-4 w-4 border-2 border-t-white/5 border-r-white/5 border-white rounded-full animate-spin ml-2"></span>
            تغییر رمز عبور
          </button>
        </form>
      </div>

      <!-- YouTube Video Management -->
      <div v-if="activeTab === 'youtube'" class="bg-black/50 rounded-lg border border-white/10 p-6 max-w-4xl">
        <h2 class="text-lg font-vazir text-white mb-4">ویدیوهای یوتیوب</h2>

        <div v-if="notifications.youtube.show"
          :class="[notifications.youtube.type === 'success' ? 'bg-green-500/10 text-green-500' : 'bg-red-500/10 text-red-500']"
          class="p-3 rounded-lg mb-4 text-sm font-vazir">
          {{ notifications.youtube.message }}
        </div>

        <form @submit.prevent="saveYouTubeItem" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="youtube_title" class="block text-white/70 mb-2 text-sm font-vazir">عنوان ویدیو</label>
              <input
                id="youtube_title"
                v-model="youtubeForm.title"
                type="text"
                class="w-full bg-gray-800 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary transition"
                required
                :disabled="youtubeSaving"
              />
              <span v-if="youtubeValidationErrors.title" class="text-red-500 text-xs mt-1 block">{{ youtubeValidationErrors.title[0] }}</span>
            </div>

            <div>
              <label for="youtube_url" class="block text-white/70 mb-2 text-sm font-vazir">لینک ویدیو</label>
              <input
                id="youtube_url"
                v-model="youtubeForm.url"
                type="url"
                class="w-full bg-gray-800 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary transition"
                required
                :disabled="youtubeSaving"
              />
              <span v-if="youtubeValidationErrors.url" class="text-red-500 text-xs mt-1 block">{{ youtubeValidationErrors.url[0] }}</span>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-white/70 mb-2 text-sm font-vazir">تصویر بندانگشتی</label>
              <FileUploader
                :upload-endpoint="uploadEndpoint"
                accept="image/jpeg,image/png,image/webp"
                :max-size="5 * 1024 * 1024"
                @upload-success="onThumbnailUploadSuccess"
                @upload-error="onThumbnailUploadError"
              />
              <span v-if="youtubeValidationErrors.thumbnail" class="text-red-500 text-xs mt-1 block">{{ youtubeValidationErrors.thumbnail[0] }}</span>

              <div v-if="youtubeForm.thumbnail" class="mt-3">
                <p class="text-white/60 text-xs font-vazir mb-2">آدرس تصویر فعلی</p>
                <input
                  v-model="youtubeForm.thumbnail"
                  type="url"
                  class="w-full bg-gray-800 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary transition"
                  :disabled="youtubeSaving"
                  dir="ltr"
                  placeholder="https://example.com/thumbnail.jpg"
                />
              </div>
            </div>

            <div>
              <label for="youtube_position" class="block text-white/70 mb-2 text-sm font-vazir">ترتیب نمایش</label>
              <input
                id="youtube_position"
                v-model.number="youtubeForm.position"
                type="number"
                min="0"
                class="w-full bg-gray-800 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary transition"
                :disabled="youtubeSaving"
              />
            </div>
          </div>

          <div class="flex items-center justify-between gap-4 flex-col md:flex-row">
            <label class="flex items-center space-x-3 space-x-reverse text-white/80 text-sm font-vazir">
              <input type="checkbox" v-model="youtubeForm.is_active" class="w-4 h-4 text-primary bg-gray-800 border border-white/10 rounded" />
              <span>فعال باشد</span>
            </label>

            <div class="flex items-center gap-2">
              <button
                type="submit"
                class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors duration-200 text-sm font-vazir"
                :disabled="youtubeSaving"
              >
                <span v-if="youtubeSaving" class="inline-block h-4 w-4 border-2 border-t-white/5 border-r-white/5 border-white rounded-full animate-spin ml-2"></span>
                {{ youtubeForm.id ? 'بروزرسانی ویدیو' : 'افزودن ویدیو جدید' }}
              </button>

              <button
                type="button"
                class="px-4 py-2 bg-white/10 text-white rounded-lg hover:bg-white/20 transition-colors duration-200 text-sm font-vazir"
                @click="resetYouTubeForm"
                :disabled="youtubeSaving"
              >
                بازنشانی فرم
              </button>
            </div>
          </div>
        </form>

        <div class="mt-8">
          <h3 class="text-base font-vazir text-white mb-4">لیست کلیپ‌ها</h3>
          <div v-if="youtubeLoading" class="flex justify-center py-8">
            <div class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-primary"></div>
          </div>

          <div v-else-if="youtubeItems.length === 0" class="text-white/70 text-sm font-vazir">
            هیچ کلیپی ثبت نشده است.
          </div>

          <div v-else class="space-y-4">
            <div v-for="item in youtubeItems" :key="item.id" class="border border-white/10 rounded-xl bg-black/40 p-4">
              <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                  <div class="text-white font-vazir text-sm">{{ item.title }}</div>
                  <div class="text-white/60 text-xs mt-1">{{ item.url }}</div>
                </div>
                <div class="text-white/70 text-xs space-x-2 space-x-reverse flex items-center">
                  <span>مشاهده: {{ item.is_active ? 'فعال' : 'غیرفعال' }}</span>
                  <span>ترتیب: {{ item.position }}</span>
                </div>
              </div>

              <div class="mt-4 flex flex-wrap gap-2">
                <button
                  type="button"
                  class="px-3 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition text-xs font-vazir"
                  @click="editYouTubeItem(item)"
                >
                  ویرایش
                </button>
                <button
                  type="button"
                  class="px-3 py-2 bg-red-500/90 text-white rounded-lg hover:bg-red-500 transition text-xs font-vazir"
                  @click="deleteYouTubeItem(item)"
                >
                  حذف
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import axios from 'axios';
import FileUploader from '@/components/admin/FileUploader.vue';

const apiUrl = import.meta.env.VITE_API_BASE_URL;
const uploadEndpoint = computed(() => `${apiUrl}/admin/upload/image`);

// State management
const isLoading = ref(true);
const activeTab = ref('profile');
const profile = reactive({
  name: '',
  email: ''
});

const passwordData = reactive({
  current_password: '',
  new_password: '',
  new_password_confirmation: ''
});

const youtubeItems = ref([]);
const youtubeLoading = ref(false);
const youtubeSaving = ref(false);

const youtubeForm = reactive({
  id: null,
  title: '',
  url: '',
  thumbnail: '',
  position: 0,
  is_active: true
});

const profileLoading = ref(false);
const passwordLoading = ref(false);
const validationErrors = reactive({});
const youtubeValidationErrors = reactive({});

// Notification system
const notifications = reactive({
  profile: {
    show: false,
    message: '',
    type: 'success',
    timeout: null
  },
  password: {
    show: false,
    message: '',
    type: 'success',
    timeout: null
  },
  youtube: {
    show: false,
    message: '',
    type: 'success',
    timeout: null
  }
});

const showNotification = (section, message, type = 'success', duration = 5000) => {
  if (notifications[section].timeout) {
    clearTimeout(notifications[section].timeout);
  }

  notifications[section].show = true;
  notifications[section].message = message;
  notifications[section].type = type;

  notifications[section].timeout = setTimeout(() => {
    notifications[section].show = false;
  }, duration);
};

const resetYouTubeForm = () => {
  youtubeForm.id = null;
  youtubeForm.title = '';
  youtubeForm.url = '';
  youtubeForm.thumbnail = '';
  youtubeForm.position = 0;
  youtubeForm.is_active = true;
  Object.keys(youtubeValidationErrors).forEach(key => delete youtubeValidationErrors[key]);
};

const fetchProfile = async () => {
  try {
    const token = localStorage.getItem('admin_token');
    if (!token) return;

    const response = await axios.get(`${import.meta.env.VITE_API_BASE_URL}/admin/profile`, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });

    if (response.data.success) {
      profile.name = response.data.admin.name;
      profile.email = response.data.admin.email;
    }
  } catch (error) {
    console.error('Error fetching profile:', error);
    showNotification('profile', 'خطا در دریافت اطلاعات پروفایل', 'error');
  }
};

const fetchYouTubeItems = async () => {
  try {
    youtubeLoading.value = true;
    const token = localStorage.getItem('admin_token');
    if (!token) return;

    const response = await axios.get(`${import.meta.env.VITE_API_BASE_URL}/admin/youtube-videos`, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });

    youtubeItems.value = response.data.videos || [];
  } catch (error) {
    console.error('Error fetching YouTube videos:', error);
    showNotification('youtube', 'خطا در دریافت لیست ویدیوها', 'error');
  } finally {
    youtubeLoading.value = false;
  }
};

const updateProfile = async () => {
  try {
    profileLoading.value = true;
    validationErrors.name = null;
    validationErrors.email = null;

    const token = localStorage.getItem('admin_token');
    if (!token) {
      showNotification('profile', 'جلسه کاری شما منقضی شده است. لطفاً مجدداً وارد شوید.', 'error');
      return;
    }

    const response = await axios.post(`${import.meta.env.VITE_API_BASE_URL}/admin/update-profile`, {
      name: profile.name,
      email: profile.email
    }, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });

    if (response.data.success) {
      showNotification('profile', response.data.message);
    }
  } catch (error) {
    const errorMessage = error.response?.data?.message || 'خطایی رخ داد. لطفاً مجدداً تلاش کنید.';
    showNotification('profile', errorMessage, 'error');
    if (error.response?.data?.errors) {
      Object.assign(validationErrors, error.response.data.errors);
    }
  } finally {
    profileLoading.value = false;
  }
};

const changePassword = async () => {
  try {
    passwordLoading.value = true;
    validationErrors.current_password = null;
    validationErrors.new_password = null;

    const token = localStorage.getItem('admin_token');
    if (!token) {
      showNotification('password', 'جلسه کاری شما منقضی شده است. لطفاً مجدداً وارد شوید.', 'error');
      return;
    }

    const response = await axios.post(`${import.meta.env.VITE_API_BASE_URL}/admin/change-password`, {
      current_password: passwordData.current_password,
      new_password: passwordData.new_password,
      new_password_confirmation: passwordData.new_password_confirmation
    }, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });

    if (response.data.success) {
      showNotification('password', response.data.message);
      passwordData.current_password = '';
      passwordData.new_password = '';
      passwordData.new_password_confirmation = '';
    }
  } catch (error) {
    const errorMessage = error.response?.data?.message || 'خطایی رخ داد. لطفاً مجدداً تلاش کنید.';
    showNotification('password', errorMessage, 'error');
    if (error.response?.data?.errors) {
      Object.assign(validationErrors, error.response.data.errors);
    }
  } finally {
    passwordLoading.value = false;
  }
};

const onThumbnailUploadSuccess = (url) => {
  youtubeForm.thumbnail = url;
};

const onThumbnailUploadError = (message) => {
  youtubeValidationErrors.thumbnail = [message];
};

const saveYouTubeItem = async () => {
  try {
    youtubeSaving.value = true;
    Object.keys(youtubeValidationErrors).forEach((key) => delete youtubeValidationErrors[key]);

    const token = localStorage.getItem('admin_token');
    if (!token) {
      showNotification('youtube', 'جلسه کاری شما منقضی شده است. لطفاً مجدداً وارد شوید.', 'error');
      return;
    }

    const payload = {
      title: youtubeForm.title,
      url: youtubeForm.url,
      thumbnail: youtubeForm.thumbnail,
      position: youtubeForm.position,
      is_active: youtubeForm.is_active,
    };

    const request = youtubeForm.id
      ? axios.put(`${import.meta.env.VITE_API_BASE_URL}/admin/youtube-videos/${youtubeForm.id}`, payload, {
          headers: { Authorization: `Bearer ${token}` }
        })
      : axios.post(`${import.meta.env.VITE_API_BASE_URL}/admin/youtube-videos`, payload, {
          headers: { Authorization: `Bearer ${token}` }
        });

    const response = await request;

    if (response.data.success) {
      showNotification('youtube', response.data.message);
      resetYouTubeForm();
      await fetchYouTubeItems();
    }
  } catch (error) {
    const errorMessage = error.response?.data?.message || 'خطایی رخ داد. لطفاً مجدداً تلاش کنید.';
    showNotification('youtube', errorMessage, 'error');
    if (error.response?.data?.errors) {
      Object.assign(youtubeValidationErrors, error.response.data.errors);
    }
  } finally {
    youtubeSaving.value = false;
  }
};

const editYouTubeItem = (item) => {
  youtubeForm.id = item.id;
  youtubeForm.title = item.title;
  youtubeForm.url = item.url;
  youtubeForm.thumbnail = item.thumbnail;
  youtubeForm.position = item.position ?? 0;
  youtubeForm.is_active = item.is_active ?? true;
};

const deleteYouTubeItem = async (item) => {
  if (!confirm('آیا مطمئن هستید که می‌خواهید این کلیپ را حذف کنید؟')) {
    return;
  }

  try {
    const token = localStorage.getItem('admin_token');
    if (!token) {
      showNotification('youtube', 'جلسه کاری شما منقضی شده است. لطفاً مجدداً وارد شوید.', 'error');
      return;
    }

    const response = await axios.delete(`${import.meta.env.VITE_API_BASE_URL}/admin/youtube-videos/${item.id}`, {
      headers: { Authorization: `Bearer ${token}` }
    });

    if (response.data.success) {
      showNotification('youtube', response.data.message);
      await fetchYouTubeItems();
    }
  } catch (error) {
    const errorMessage = error.response?.data?.message || 'خطا در حذف ویدیو رخ داد.';
    showNotification('youtube', errorMessage, 'error');
  }
};

onMounted(async () => {
  try {
    await Promise.all([fetchProfile(), fetchYouTubeItems()]);
  } finally {
    isLoading.value = false;
  }
});
</script>
