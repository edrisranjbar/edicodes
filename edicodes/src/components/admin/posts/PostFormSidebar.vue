<template>
  <div class="lg:col-span-1">
    <div class="bg-black/50 rounded-lg border border-white/10 p-6 space-y-8">
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
            type="button"
            class="bg-white/10 hover:bg-white/20 text-white px-3 rounded-lg mr-2 shrink-0"
            title="ساخت خودکار از عنوان"
            @click="emit('generate-slug')"
          >
            <font-awesome-icon icon="magic" />
          </button>
        </div>
      </div>

      <div>
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
        <span class="block text-white font-vazir mb-2">انتشار</span>
        <div class="flex items-center">
          <input
            v-model="post.published"
            type="checkbox"
            id="published"
            class="w-5 h-5 bg-black/20 border border-white/10 rounded text-primary focus:ring-primary focus:ring-opacity-25"
          />
          <label for="published" class="text-white font-vazir mr-2 cursor-pointer">منتشر شده</label>
        </div>
      </div>

      <div>
        <label for="published_at" class="block text-white font-vazir mb-2">تاریخ انتشار</label>
        <input
          v-model="post.published_at"
          type="datetime-local"
          id="published_at"
          class="w-full bg-black/20 border border-white/10 rounded-lg py-2 px-4 font-vazir text-white/90 focus:outline-none focus:border-primary/50"
          dir="ltr"
        />
      </div>

      <div>
        <label for="category" class="block text-white font-vazir mb-2">دسته‌بندی</label>
        <div class="relative">
          <select
            id="category"
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
        <div class="mt-3 flex justify-end">
          <router-link
            to="/admin/categories/create"
            class="text-primary text-sm hover:text-primary-light transition-colors duration-150 font-vazir flex items-center"
          >
            <font-awesome-icon icon="plus" class="ml-1" />
            افزودن دسته‌بندی جدید
          </router-link>
        </div>
      </div>

      <div>
        <span class="block text-white font-vazir mb-2">تصویر شاخص</span>
        <div v-if="post.image">
          <p class="text-white/60 text-xs font-vazir mb-2">پیش‌نمایش</p>
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
              type="button"
              class="absolute top-2 right-2 bg-red-500/80 text-white rounded-full w-8 h-8 flex items-center justify-center hover:bg-red-600/80 transition-colors duration-200"
              title="حذف تصویر"
              @click="removeImage"
            >
              <font-awesome-icon icon="times" />
            </button>
          </div>
        </div>
        <div v-if="!post.image">
          <FileUploader
            :upload-endpoint="uploadEndpoint"
            accept="image/jpeg,image/png,image/webp"
            :max-size="5 * 1024 * 1024"
            @upload-success="onUploadSuccess"
            @upload-error="onUploadError"
          />
        </div>
        <div v-if="post.image" class="mt-4">
          <label for="image" class="block text-white/80 font-vazir text-sm mb-2">آدرس تصویر</label>
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
</template>

<script setup>
import { ref, watch } from 'vue'
import FileUploader from '@/components/admin/FileUploader.vue'

defineProps({
  categories: {
    type: Array,
    default: () => []
  },
  uploadEndpoint: {
    type: String,
    required: true
  }
})

const emit = defineEmits(['generate-slug', 'upload-error'])

const post = defineModel('post', { type: Object, required: true })

const imageError = ref(false)

watch(
  () => post.value.image,
  () => {
    imageError.value = false
  }
)

function removeImage() {
  post.value.image = ''
}

function onUploadSuccess(imageUrl) {
  post.value.image = imageUrl
  imageError.value = false
}

function onUploadError(message) {
  emit('upload-error', message)
}
</script>
