<template>
  <div v-if="images && images.length > 0" class="image-gallery">
    <!-- Image Grid -->
    <div class="flex flex-wrap gap-2">
      <div 
        v-for="(image, index) in processedImages" 
        :key="index"
        class="relative group cursor-pointer"
        @click="openLightbox(index)"
      >
        <OptimizedImage 
          :src="image.url" 
          :alt="`Image ${index + 1}`"
          class="w-24 h-24 object-cover rounded-lg border border-gray-200 hover:border-primary transition-all duration-300 hover:scale-105"
        />
        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300 rounded-lg flex items-center justify-center">
          <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-100 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
          </svg>
        </div>
      </div>
    </div>

    <!-- Enhanced Lightbox Modal -->
    <div 
      v-if="lightboxOpen" 
      @click="closeLightbox"
      @touchstart="handleTouchStart"
      @touchend="handleTouchEnd"
      class="fixed inset-0 bg-black bg-opacity-90 z-[60] flex items-center justify-center p-4"
    >
      <div 
        @click.stop
        class="relative w-full max-w-4xl max-h-full"
      >
        <!-- Close Button -->
        <button 
          @click="closeLightbox"
          class="absolute top-4 right-4 z-10 text-white hover:text-gray-300 transition-colors"
        >
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>

        <!-- Navigation Arrows -->
        <button 
          v-if="processedImages.length > 1"
          @click="previousItem"
          class="absolute left-4 top-1/2 -translate-y-1/2 z-10 text-white hover:text-gray-300 transition-colors"
        >
          <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
          </svg>
        </button>

        <button 
          v-if="processedImages.length > 1"
          @click="nextItem"
          class="absolute right-4 top-1/2 -translate-y-1/2 z-10 text-white hover:text-gray-300 transition-colors"
        >
          <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
        </button>

        <!-- Content -->
        <div class="flex flex-col items-center">
          <!-- Image Viewer -->
          <div class="w-full">
            <OptimizedImage 
              :src="currentImageUrl" 
              :alt="`Image ${currentIndex + 1}`"
              class="max-w-full max-h-[70vh] object-contain mx-auto rounded-lg"
            />
            <div class="mt-4 text-center">
              <p class="text-white text-lg">{{ currentIndex + 1 }} of {{ processedImages.length }}</p>
              <p class="text-white text-sm text-gray-300 mt-1">{{ galleryTitle || 'Image Gallery' }}</p>
            </div>
          </div>

          <!-- Thumbnail Navigation -->
          <div v-if="processedImages.length > 1" class="mt-6 flex gap-2 overflow-x-auto max-w-full pb-2">
            <div 
              v-for="(image, index) in processedImages" 
              :key="index"
              @click.stop="goToItem(index)"
              class="flex-shrink-0 cursor-pointer rounded-lg overflow-hidden border-2 transition-all duration-200"
              :class="currentIndex === index ? 'border-white' : 'border-gray-600 hover:border-gray-400'"
            >
              <div class="w-16 h-16 bg-gray-800">
                <OptimizedImage 
                  :src="image.url" 
                  :alt="`Thumbnail ${index + 1}`"
                  class="w-full h-full object-cover"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import OptimizedImage from '@/components/common/OptimizedImage.vue';

export default {
  name: 'ImageGallery',
  components: {
    OptimizedImage
  },
  props: {
    images: {
      type: Array,
      required: true
    },
    galleryTitle: {
      type: String,
      default: 'Image Gallery'
    }
  },
  data() {
    return {
      lightboxOpen: false,
      currentIndex: 0,
      touchStartX: 0,
      touchStartY: 0
    }
  },
  computed: {
    processedImages() {
      return this.images.map(img => {
        if (typeof img === 'string') {
          return { url: img };
        }
        return img;
      });
    },
    currentImageUrl() {
      const image = this.processedImages[this.currentIndex]
      return image ? image.url : null
    }
  },
  methods: {
    openLightbox(index) {
      this.currentIndex = index
      this.lightboxOpen = true
      document.body.style.overflow = 'hidden'
    },
    closeLightbox() {
      this.lightboxOpen = false
      document.body.style.overflow = 'auto'
    },
    nextItem() {
      if (this.currentIndex < this.processedImages.length - 1) {
        this.currentIndex++
      }
    },
    previousItem() {
      if (this.currentIndex > 0) {
        this.currentIndex--
      }
    },
    goToItem(index) {
      this.currentIndex = index
    },
    handleTouchStart(event) {
      if (!this.lightboxOpen) return
      this.touchStartX = event.touches[0].clientX
      this.touchStartY = event.touches[0].clientY
    },
    handleTouchEnd(event) {
      if (!this.lightboxOpen) return
      
      const touchEndX = event.changedTouches[0].clientX
      const touchEndY = event.changedTouches[0].clientY
      
      const deltaX = touchEndX - this.touchStartX
      const deltaY = touchEndY - this.touchStartY
      
      // Only handle horizontal swipes with minimal vertical movement
      if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) > 50) {
        if (deltaX > 0) {
          this.previousItem()
        } else {
          this.nextItem()
        }
      }
    },
    handleKeydown(event) {
      if (!this.lightboxOpen) return
      
      switch (event.key) {
        case 'Escape':
          this.closeLightbox()
          break
        case 'ArrowLeft':
          this.previousItem()
          break
        case 'ArrowRight':
          this.nextItem()
          break
      }
    }
  },
  mounted() {
    // Add keyboard event listeners
    document.addEventListener('keydown', this.handleKeydown)
  },
  beforeUnmount() {
    document.body.style.overflow = 'auto'
    document.removeEventListener('keydown', this.handleKeydown)
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
