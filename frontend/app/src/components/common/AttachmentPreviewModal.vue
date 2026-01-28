<template>
  <div v-if="isVisible" class="fixed inset-0 bg-black bg-opacity-90 z-[60] flex justify-center p-4" @click="closeModal">
    <div class="relative w-full max-w-4xl max-h-full" @click.stop>
      <!-- Close Button -->
      <button 
        @click="closeModal"
        class="absolute top-4 right-4 z-10 text-white hover:text-gray-300 transition-colors"
      >
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>

      <!-- Download Button -->
      <button 
        v-if="currentAttachment && isImage"
        @click="downloadFile"
        class="absolute top-4 right-16 z-10 text-white hover:text-gray-300 transition-colors"
        title="Download image"
      >
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
      </button>

      <!-- Navigation Arrows -->
      <button 
        v-if="allImages && allImages.length > 1 && currentIndex > 0"
        @click="previousImage"
        class="absolute left-4 top-1/2 -translate-y-1/2 z-10 text-white hover:text-gray-300 transition-colors"
      >
        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
      </button>

      <button 
        v-if="allImages && allImages.length > 1 && currentIndex < allImages.length - 1"
        @click="nextImage"
        class="absolute right-4 top-1/2 -translate-y-1/2 z-10 text-white hover:text-gray-300 transition-colors"
      >
        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
      </button>

      <!-- Content -->
      <div class="flex flex-col items-center h-full">
        <!-- Image Viewer -->
        <div v-if="currentAttachment && isImage" class="w-full items-start m-auto">
          <div v-if="imageLoading" class="flex items-center justify-center w-full h-96 bg-gray-800 rounded">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-white"></div>
          </div>
          <img 
            v-show="!imageLoading"
            :src="currentAttachmentUrl" 
            :alt="`Image ${currentIndex + 1}`"
            class="max-w-full max-h-[70vh] object-contain mx-auto rounded-lg"
            @error="handleImageError"
            @load="handleImageLoad"
          />
          <div class="mt-4 text-center">
            <p v-if="allImages && allImages.length > 1" class="text-white text-lg">{{ currentIndex + 1 }} of {{ allImages.length }}</p>
            <p class="text-white text-sm text-gray-300 mt-1">{{ getFileName(currentAttachment) }}</p>
          </div>
        </div>

        <!-- Video Viewer -->
        <div v-else-if="currentAttachment && isVideo" class="w-full m-auto">
          <video 
            :src="currentAttachmentUrl" 
            class="max-w-full max-h-[70vh] object-contain mx-auto rounded-lg"
            controls
            @contextmenu.prevent
            @dragstart.prevent
            @selectstart.prevent
          />
          <div class="mt-4 text-center">
            <p class="text-sm text-gray-300">{{ getFileName(currentAttachment) }}</p>
          </div>
        </div>

        <!-- Thumbnail Navigation -->
        <div v-if="allImages && allImages.length > 1" class="flex gap-2 overflow-x-auto max-w-full pb-2 mt-auto">
          <div 
            v-for="(image, index) in allImages" 
            :key="index"
            @click.stop="goToImage(index)"
            class="flex-shrink-0 cursor-pointer rounded-lg overflow-hidden border-2 transition-all duration-200"
            :class="currentIndex === index ? 'border-white' : 'border-gray-600 hover:border-gray-400'"
          >
            <div class="w-16 h-16 bg-gray-800">
              <img 
                :src="getImageUrl(image)" 
                :alt="`Thumbnail ${index + 1}`"
                class="w-full h-full object-cover"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { XMarkIcon } from '@heroicons/vue/24/outline'
import { useModalScroll } from '@/composables/useModalScroll'

export default {
  name: 'AttachmentPreviewModal',
  components: {
    XMarkIcon
  },
  setup() {
    const { openModal, closeModal } = useModalScroll()
    return { openModal, closeModal }
  },
  data() {
    return {
      imageLoading: true
    }
  },
  props: {
    attachment: {
      type: Object,
      default: null
    },
    isVisible: {
      type: Boolean,
      default: false
    },
    allImages: {
      type: Array,
      default: () => []
    },
    currentIndex: {
      type: Number,
      default: 0
    }
  },
  computed: {
    currentAttachment() {
      // Use allImages array if available, otherwise fall back to single attachment
      if (this.allImages && this.allImages.length > 0) {
        return this.allImages[this.currentIndex] || this.allImages[0];
      }
      return this.attachment;
    },
    isImage() {
      if (!this.currentAttachment) return false;
      
      // Check multiple possible properties for mime type
      const mimeType = this.currentAttachment.mime_type || this.currentAttachment.mimeType || this.currentAttachment.type;
      const isImage = mimeType && mimeType.startsWith('image/');
      
      // Also check file extension as fallback
      const fileName = this.currentAttachment.original_name || this.currentAttachment.name || this.currentAttachment.filename || '';
      const hasImageExtension = /\.(jpg|jpeg|png|gif|webp|bmp|svg)$/i.test(fileName);
      
      return isImage || hasImageExtension;
    },
    isVideo() {
      if (!this.currentAttachment) return false;
      
      const mimeType = this.currentAttachment.mime_type || this.currentAttachment.mimeType || this.currentAttachment.type;
      const isVideo = mimeType && mimeType.startsWith('video/');
      
      const fileName = this.currentAttachment.original_name || this.currentAttachment.name || this.currentAttachment.filename || '';
      const hasVideoExtension = /\.(mp4|avi|mov|wmv|flv|webm|mkv)$/i.test(fileName);
      
      return isVideo || hasVideoExtension;
    },
    currentAttachmentUrl() {
      return this.currentAttachment ? (this.currentAttachment.url || this.currentAttachment.file_url || this.currentAttachment.download_url || this.currentAttachment.path || '') : '';
    }
  },
  methods: {
    closeModal() {
      this.$emit('close');
    },
    nextImage() {
      if (this.allImages && this.allImages.length > 1 && this.currentIndex < this.allImages.length - 1) {
        this.$emit('next-image');
      }
    },
    previousImage() {
      if (this.allImages && this.allImages.length > 1 && this.currentIndex > 0) {
        this.$emit('previous-image');
      }
    },
    goToImage(index) {
      if (this.allImages && this.allImages.length > 0 && index >= 0 && index < this.allImages.length) {
        this.$emit('go-to-image', index);
      }
    },
    getImageUrl(image) {
      return image ? (image.url || image.file_url || image.download_url || image.path || '') : '';
    },
    getFileName(attachment) {
      if (!attachment) return '';
      return attachment.original_name || attachment.name || attachment.filename || 'File';
    },
    downloadFile() {
      if (!this.currentAttachment) return;
      
      const link = document.createElement('a');
      link.href = this.currentAttachmentUrl;
      link.download = this.getFileName(this.currentAttachment);
      link.target = '_blank';
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    },
    handleImageError(event) {
      this.imageLoading = false;
    },
    handleImageLoad() {
      this.imageLoading = false;
    },
    handleKeydown(event) {
      if (!this.isVisible) return;
      
      switch (event.key) {
        case 'Escape':
          this.closeModal();
          break;
        case 'ArrowLeft':
          this.previousImage();
          break;
        case 'ArrowRight':
          this.nextImage();
          break;
      }
    }
  },
  mounted() {
    // Add keyboard event listeners
    document.addEventListener('keydown', this.handleKeydown);
  },
  beforeUnmount() {
    // Remove keyboard event listeners
    document.removeEventListener('keydown', this.handleKeydown);
  },
  watch: {
    isVisible(newVal) {
      if (newVal) {
        this.openModal('attachment-preview-modal');
        this.imageLoading = true;
      } else {
        this.closeModal('attachment-preview-modal');
        this.imageLoading = false;
      }
    },
    currentIndex() {
      this.imageLoading = true;
    }
  }
}
</script>

<style scoped>
/* Custom scrollbar for thumbnails */
.overflow-x-auto::-webkit-scrollbar {
  height: 6px;
}

.overflow-x-auto::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.3);
  border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.5);
}
</style>
