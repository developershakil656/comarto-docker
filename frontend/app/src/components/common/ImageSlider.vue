<template>
  <div v-if="processedImages && processedImages.length > 0" class="image-slider">
    <!-- Slider Container -->
    <div class="relative group">
      <!-- Navigation Arrows -->
      <button 
        v-if="images.length > visibleCount"
        v-show="canScrollLeft"
        @click="previousSlide"
        class="absolute left-2 top-1/2 -translate-y-1/2 z-10 bg-white/80 backdrop-blur-sm rounded-full p-1 shadow-md"
      >
        <ChevronLeftIcon class="h-6 w-6 text-gray-700" />
      </button>

      <button 
        v-if="images.length > visibleCount"
        @click="nextSlide"
        v-show="canScrollRight"
        class="absolute right-2 top-1/2 -translate-y-1/2 z-10 bg-white/80 backdrop-blur-sm rounded-full p-1 shadow-md"
      >
        <ChevronRightIcon class="h-6 w-6 text-gray-700" />
      </button>

      <!-- Images Container -->
      <div ref="sliderContainer" @scroll="checkScroll" class="overflow-x-auto max-w-full scrollbar-hide">
        <div class="flex gap-3">
          <div 
            v-for="(image, index) in processedImages" 
            :key="index"
            class="flex-shrink-0 cursor-pointer group/item"
            @click="openLightbox(index)"
          >
            <div class="relative">
              <img 
                :src="image.url" 
                :alt="`Image ${index + 1}`"
                class="w-24 h-24 object-cover rounded-lg border border-gray-200 group-hover/item:border-primary transition-all duration-300 group-hover/item:scale-105"
              /> 
              <div class="absolute inset-0 bg-black bg-opacity-0 group-hover/item:bg-opacity-40 transition-all duration-300 rounded-lg flex items-center justify-center">
                <MagnifyingGlassPlusIcon class="w-6 h-6 text-white opacity-0 group-hover/item:opacity-100 transition-all duration-300" />
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Dots Indicator -->
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
          <XMarkIcon class="w-8 h-8" />
        </button>

        <!-- Navigation Arrows -->
        <button 
          v-if="processedImages.length > 1"
          @click="previousItem"
          class="absolute left-0 md:left-4 top-1/2 -translate-y-1/2 z-10 text-white hover:text-gray-300 transition-colors p-2 bg-black/20 rounded-full"
        >
          <ChevronLeftIcon class="w-8 h-8" />
        </button>

        <button 
          v-if="processedImages.length > 1"
          @click="nextItem"
          class="absolute right-0 md:right-4 top-1/2 -translate-y-1/2 z-10 text-white hover:text-gray-300 transition-colors p-2 bg-black/20 rounded-full"
        >
          <ChevronRightIcon class="w-8 h-8" />
        </button>

        <!-- Content -->
        <div class="flex flex-col items-center">
          <!-- Image Viewer -->
          <div class="w-full">
            <img 
              :src="currentImageUrl" 
              :alt="`Image ${lightboxIndex + 1}`"
              class="max-w-full max-h-[70vh] object-contain mx-auto rounded-lg"
            />
            <div class="mt-4 text-center">
              <p class="text-white text-lg">{{ lightboxIndex + 1 }} of {{ processedImages.length }}</p>
              <p class="text-white text-sm mt-1">{{ sliderTitle || 'Image Gallery' }}</p>
            </div>
          </div>

          <!-- Thumbnail Navigation -->
          <div v-if="processedImages.length > 1" class="mt-6 flex gap-2 overflow-x-auto max-w-full pb-2">
            <div 
              v-for="(image, index) in processedImages" 
              :key="index"
              @click.stop="goToItem(index)"
              class="flex-shrink-0 cursor-pointer rounded-lg overflow-hidden border-4 transition-all duration-200"
              :class="lightboxIndex === index ? 'border-primary' : 'border-gray-600 hover:border-primary'"
            >
              <div class="w-16 h-16 bg-gray-800">
                <img 
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
import {
  ChevronLeftIcon,
  ChevronRightIcon,
  MagnifyingGlassPlusIcon,
  XMarkIcon
} from '@heroicons/vue/24/outline';
export default {
  name: 'ImageSlider',
  props: {
    images: {
      type: Array,
      required: true
    },
    sliderTitle: {
      type: String,
      default: 'Image Gallery'
    },
    visibleCount: {
      type: Number,
      default: 4
    }
  },
  components: {
    ChevronLeftIcon,
    ChevronRightIcon,
    MagnifyingGlassPlusIcon,
    XMarkIcon
  },
  data() {
    return {
      lightboxOpen: false,
      lightboxIndex: 0,
      touchStartX: 0,
      touchStartY: 0,
      canScrollLeft: false,
      canScrollRight: false,
      resizeObserver: null,
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
      const image = this.processedImages[this.lightboxIndex]
      return image ? image.url : null
    }
  },
  methods: {
    initializeScrollState() {
      const container = this.$refs.sliderContainer;
      if (container) {
        // This method is now primarily for setting the initial state on scroll.
        const buffer = 1; // Use a small buffer to account for sub-pixel rendering
        const hasOverflow = container.scrollWidth > container.clientWidth + buffer;
        this.canScrollLeft = container.scrollLeft > buffer;
        this.canScrollRight = hasOverflow && (container.scrollLeft < container.scrollWidth - container.clientWidth - buffer);
      }
    },
    checkScroll() {
      const container = this.$refs.sliderContainer;
      if (container) {
        const { scrollLeft, scrollWidth, clientWidth } = container;
        const buffer = 2;
        this.canScrollLeft = scrollLeft > buffer;
        this.canScrollRight = scrollLeft < scrollWidth - clientWidth - buffer;
      }
    },
    nextSlide() {
      const container = this.$refs.sliderContainer;
      if (container) {
        container.scrollBy({
          left: container.clientWidth * 0.8,
          behavior: 'smooth'
        });
      }
    },
    previousSlide() {
      const container = this.$refs.sliderContainer;
      if (container) {
        container.scrollBy({
          left: -container.clientWidth * 0.8,
          behavior: 'smooth'
        });
      }
    },
    openLightbox(index) {
      this.lightboxIndex = index;
      this.lightboxOpen = true;
      document.body.style.overflow = 'hidden';
    },
    closeLightbox() {
      this.lightboxOpen = false;
      document.body.style.overflow = 'auto';
    },
    nextItem() {
      if (this.lightboxIndex < this.processedImages.length - 1) {
        this.lightboxIndex++;
      }
    },
    previousItem() {
      if (this.lightboxIndex > 0) {
        this.lightboxIndex--;
      }
    },
    goToItem(index) {
      this.lightboxIndex = index;
    },
    handleTouchStart(event) {
      if (!this.lightboxOpen) return;
      this.touchStartX = event.touches[0].clientX;
      this.touchStartY = event.touches[0].clientY;
    },
    handleTouchEnd(event) {
      if (!this.lightboxOpen) return;
      
      const touchEndX = event.changedTouches[0].clientX;
      const touchEndY = event.changedTouches[0].clientY;
      
      const deltaX = touchEndX - this.touchStartX;
      const deltaY = touchEndY - this.touchStartY;
      
      if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) > 50) {
        if (deltaX > 0) {
          this.previousItem();
        } else {
          this.nextItem();
        }
      }
    },
    handleKeydown(event) {
      if (!this.lightboxOpen) return;
      
      switch (event.key) {
        case 'Escape':
          this.closeLightbox();
          break;
        case 'ArrowLeft':
          this.previousItem();
          break;
        case 'ArrowRight':
          this.nextItem();
          break;
      }
    }
  },
  mounted() {
    const container = this.$refs.sliderContainer;
    if (container) {
      // Use ResizeObserver to reliably detect when the container size changes
      this.resizeObserver = new ResizeObserver(() => {
        this.checkScroll();
      });
      this.resizeObserver.observe(container);
      setTimeout(() => this.checkScroll(), 100);
    }
    document.addEventListener('keydown', this.handleKeydown);
  },
  beforeUnmount() {
    document.body.style.overflow = 'auto';
    document.removeEventListener('keydown', this.handleKeydown);
    if (this.resizeObserver) {
      this.resizeObserver.disconnect();
    }
  }
}
</script>

<style scoped>
/* Hides scrollbar for a cleaner look */
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

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
