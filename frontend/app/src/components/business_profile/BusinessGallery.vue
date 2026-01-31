<template>
  <div class="business-gallery">
    <!-- Loading State -->
    <div v-if="galleryLoading" class="text-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mx-auto"></div>
      <p class="mt-4 text-gray-500">Loading gallery...</p>
    </div>

    <!-- Gallery Grid -->
    <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-6">
      <!-- YouTube Video Thumbnail -->
      <div 
        v-if="businessDetails?.video_url" 
        @click="openLightbox('video', 0)"
        class="relative group cursor-pointer rounded-lg overflow-hidden border border-gray-200 hover:border-primary transition-all duration-300 hover:shadow-lg"
      >
        <div class="aspect-video bg-gray-100 relative">
          <div v-if="videoLoading" class="w-full h-full flex items-center justify-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
          </div>
          <OptimizedImage 
            v-else-if="videoThumbnail"
            :src="videoThumbnail" 
            :alt="videoTitle || 'YouTube Video'"
            class="w-full h-full object-cover"
          />
          <div v-else class="w-full h-full bg-gradient-to-br from-red-400 to-red-600 flex items-center justify-center">
            <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
              <path d="M8 5v14l11-7z"/>
            </svg>
          </div>
          <div class="absolute inset-0 bg-black bg-opacity-20 group-hover:bg-opacity-30 transition-all duration-300 flex items-center justify-center">
            <div class="bg-red-600 text-white rounded-full p-3 group-hover:scale-110 transition-transform duration-300">
              <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M8 5v14l11-7z"/>
              </svg>
            </div>
          </div>
        </div>
        <div class="p-3 bg-white">
          <p class="text-sm font-medium text-gray-900 line-clamp-2">{{ videoTitle || 'YouTube Video' }}</p>
          <p class="text-xs text-gray-500 mt-1">Click to play</p>
        </div>
      </div>

      <!-- Business Images -->
      <div 
        v-for="(image, index) in businessImages" 
        :key="image.id || index"
        @click="openLightbox('image', getVideoOffset() + index)"
        class="relative group cursor-pointer rounded-lg overflow-hidden border border-gray-200 hover:border-primary transition-all duration-300 hover:shadow-lg"
      >
        <div class="aspect-square bg-gray-100">
          <OptimizedImage 
            :src="image.url || image.image_url" 
            :alt="`Business Image ${index + 1}`"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
          />
          <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300 flex items-center justify-center">
            <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="!businessDetails?.video_url && (!businessImages || businessImages.length === 0)" class="text-center py-12">
      <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
      </svg>
      <h3 class="mt-4 text-lg font-medium text-gray-900">No Gallery Items</h3>
      <p class="mt-2 text-sm text-gray-500">This business hasn't added any images or videos yet.</p>
    </div>

    <!-- Lightbox Modal -->
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
          v-if="totalItems > 1"
          @click="previousItem"
          class="absolute left-4 top-1/2 -translate-y-1/2 z-10 text-white hover:text-gray-300 transition-colors"
        >
          <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
          </svg>
        </button>

        <button 
          v-if="totalItems > 1"
          @click="nextItem"
          class="absolute right-4 top-1/2 -translate-y-1/2 z-10 text-white hover:text-gray-300 transition-colors"
        >
          <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
        </button>

        <!-- Content -->
        <div class="flex flex-col items-center">
          <!-- Video Player -->
          <div v-if="currentItemType === 'video'" class="w-full">
            <div class="aspect-video bg-black rounded-lg overflow-hidden">
              <iframe
                v-if="videoEmbedUrl"
                :src="videoEmbedUrl"
                class="w-full h-full"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
              ></iframe>
            </div>
            <div class="mt-4 text-center">
              <h3 class="text-xl font-semibold text-white">{{ videoTitle || 'YouTube Video' }}</h3>
            </div>
          </div>

          <!-- Image Viewer -->
          <div v-else class="w-full">
            <OptimizedImage 
              :src="currentImageUrl" 
              :alt="`Business Image ${currentIndex + 1}`"
              class="max-w-full max-h-[70vh] object-contain mx-auto rounded-lg"
            />
            <div class="mt-4 text-center">
              <p class="text-white text-lg">{{ currentIndex + 1 }} of {{ totalItems }}</p>
              <p class="text-white text-sm mt-1">
                {{ currentItemType === 'video' ? 'YouTube Video' : `Business Image ${currentIndex - getVideoOffset() + 1}` }}
              </p>
            </div>
          </div>

          <!-- Thumbnail Navigation -->
          <div v-if="totalItems > 1" class="mt-6 flex gap-2 overflow-x-auto max-w-full pb-2">
            <div 
              v-for="(item, index) in allItems" 
              :key="index"
              @click.stop="goToItem(index)"
              class="flex-shrink-0 cursor-pointer rounded-lg overflow-hidden border-2 transition-all duration-200"
              :class="currentIndex === index ? 'border-white' : 'border-gray-600 hover:border-gray-400'"
            >
              <div class="w-16 h-16 bg-gray-800">
                <OptimizedImage 
                  v-if="item.type === 'image'"
                  :src="item.url || item.image_url" 
                  :alt="`Thumbnail ${index + 1}`"
                  class="w-full h-full object-cover"
                />
                <div v-else class="w-full h-full bg-red-600 flex items-center justify-center">
                  <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z"/>
                  </svg>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import OptimizedImage from '@/components/common/OptimizedImage.vue'

export default {
  name: 'BusinessGallery',
  components: {
    OptimizedImage
  },
  props: {
    businessData: {
      type: Object,
      default: null
    },
    businessDetails: {
      type: Object,
      default: null
    }
  },
  data() {
    return {
      lightboxOpen: false,
      currentIndex: 0,
      currentItemType: 'image', // 'image' or 'video'
      videoThumbnail: null,
      videoTitle: null,
      videoLoading: false,
      touchStartX: 0,
      touchStartY: 0,
      galleryLoading: true
    }
  },
  computed: {
    businessImages() {
      return this.businessData?.gallery || []
    },
    totalItems() {
      let count = this.businessImages.length
      if (this.businessDetails?.video_url) count++
      return count
    },
    allItems() {
      const items = []
      
      // Add video if exists
      if (this.businessDetails?.video_url) {
        items.push({
          type: 'video',
          url: this.businessDetails.video_url
        })
      }
      
      // Add images
      this.businessImages.forEach(image => {
        items.push({
          type: 'image',
          url: image.url || image.image_url,
          id: image.id
        })
      })
      
      return items
    },
    currentImageUrl() {
      if (this.currentItemType === 'video') return null
      
      // Calculate the correct image index by subtracting video offset
      const imageIndex = this.currentIndex - this.getVideoOffset()
      const image = this.businessImages[imageIndex]
      return image ? (image.url || image.image_url) : null
    },
    videoEmbedUrl() {
      if (!this.businessDetails?.video_url) return null
      const videoId = this.extractVideoId(this.businessDetails.video_url)
      return videoId ? `https://www.youtube.com/embed/${videoId}` : null
    }
  },
  async mounted() {
    if (this.businessDetails?.video_url) {
      await this.fetchVideoPreview()
    }
    
    // Add keyboard event listeners
    document.addEventListener('keydown', this.handleKeydown)
    
    // Gallery is ready
    this.galleryLoading = false
  },
  methods: {
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
    getVideoOffset() {
      return this.businessDetails?.video_url ? 1 : 0
    },
    openLightbox(type, index) {
      this.currentItemType = type
      this.currentIndex = index
      this.lightboxOpen = true
      document.body.style.overflow = 'hidden'
    },
    closeLightbox() {
      this.lightboxOpen = false
      document.body.style.overflow = 'auto'
    },
    nextItem() {
      if (this.currentIndex < this.totalItems - 1) {
        this.currentIndex++
        this.updateCurrentItemType()
      }
    },
    previousItem() {
      if (this.currentIndex > 0) {
        this.currentIndex--
        this.updateCurrentItemType()
      }
    },
    goToItem(index) {
      this.currentIndex = index
      this.updateCurrentItemType()
    },
    updateCurrentItemType() {
      if (this.businessDetails?.video_url && this.currentIndex === 0) {
        this.currentItemType = 'video'
      } else {
        this.currentItemType = 'image'
      }
    },
    async fetchVideoPreview() {
      try {
        this.videoLoading = true
        const videoId = this.extractVideoId(this.businessDetails.video_url)
        if (!videoId) {
          this.videoThumbnail = null
          return
        }

        // Set fallback thumbnail immediately
        this.videoThumbnail = `https://img.youtube.com/vi/${videoId}/hqdefault.jpg`
        
        // Try to get video info from YouTube API (optional enhancement)
        try {
          const response = await fetch(`https://www.googleapis.com/youtube/v3/videos?id=${videoId}&key=YOUR_API_KEY&part=snippet,contentDetails`)
          
          if (response.ok) {
            const data = await response.json()
            if (data.items && data.items.length > 0) {
              const video = data.items[0]
              this.videoTitle = video.snippet.title
              // Use higher quality thumbnail if available
              if (video.snippet.thumbnails.maxres) {
                this.videoThumbnail = video.snippet.thumbnails.maxres.url
              } else if (video.snippet.thumbnails.high) {
                this.videoThumbnail = video.snippet.thumbnails.high.url
              }
            }
          }
        } catch (apiError) {
          // API call failed, but we already have fallback thumbnail
          console.log('YouTube API not available, using fallback thumbnail')
        }
      } catch (error) {
        console.error('Error fetching video preview:', error)
        // Ensure we have at least a fallback thumbnail
        const videoId = this.extractVideoId(this.businessDetails.video_url)
        if (videoId) {
          this.videoThumbnail = `https://img.youtube.com/vi/${videoId}/hqdefault.jpg`
        }
      } finally {
        this.videoLoading = false
      }
    },
    extractVideoId(url) {
      if (!url) return null
      
      const patterns = [
        /(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&\n?#]+)/,
        /youtube\.com\/v\/([^&\n?#]+)/,
        /youtube\.com\/watch\?.*v=([^&\n?#]+)/
      ]
      
      for (const pattern of patterns) {
        const match = url.match(pattern)
        if (match) return match[1]
      }
      
      return null
    }
  },
  beforeUnmount() {
    document.body.style.overflow = 'auto'
    document.removeEventListener('keydown', this.handleKeydown)
  }
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

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

/* Gallery grid hover effects */
.business-gallery .grid > div {
  transition: all 0.3s ease;
}

.business-gallery .grid > div:hover {
  transform: translateY(-2px);
}

/* Lightbox animations */
.lightbox-enter-active,
.lightbox-leave-active {
  transition: opacity 0.3s ease;
}

.lightbox-enter-from,
.lightbox-leave-to {
  opacity: 0;
}

/* Image zoom effect on hover */
.business-gallery img {
  transition: transform 0.3s ease;
}

.business-gallery .group:hover img {
  transform: scale(1.05);
}
</style>
