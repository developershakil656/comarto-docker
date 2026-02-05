<template>
  <div v-if="open" class="fixed inset-0 z-[60] flex items-stretch md:items-center justify-center bg-white md:bg-gray-500 md:bg-opacity-75 p-0 md:p-4">
    <div class="bg-white w-full h-full md:h-full md:rounded-lg md:shadow-xl md:max-w-4xl overflow-y-auto">
      <MobileModalHeader title="Business Gallery" @back="closeModal" />
        <!-- Header -->
      <div class="hidden md:block bg-white px-6 py-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Business Gallery</h3>
            <button
              @click="closeModal"
              class="text-gray-400 hover:text-gray-600 transition-colors"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Content -->
      <div class="p-4 md:px-6 md:py-6 h-full">
        <div class="bg-white rounded-2xl shadow-lg p-4 py-6 min-h-full">
          <!-- Loading State -->
          <div v-if="isLoading" class="flex items-center justify-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
            <span class="ml-3 text-gray-600">Loading gallery...</span>
          </div>
          
          <!-- Gallery Content -->
          <div v-else class="space-y-6">
            <!-- YouTube Video URL -->
            <div class="relative group">
              <span class="absolute left-4 top-6 -translate-y-1/2 text-gray-400 pointer-events-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </span>
              <input
                v-model="form.video_url"
                type="url"
                id="videoUrl"
                :class="[
                  'block w-full rounded-lg border pl-12 pr-4 py-3 focus:border-primary focus:ring-blue-200 outline-none transition placeholder-transparent text-base',
                  fieldErrors.video_url ? 'border-red-300 focus:border-red-500' : 'border-gray-300'
                ]"
                placeholder="YouTube Video URL"
              />
              <label for="videoUrl"
                class="absolute left-12 z-10 -translate-y-1/2 bg-white text-sm transition-all duration-200 pointer-events-none group-focus-within:top-0 group-focus-within:text-xs group-focus-within:left-8 group-focus-within:-translate-y-1.5"
                :class="{ 
                  'top-0 text-xs left-8 text-primary': form.video_url && !fieldErrors.video_url, 
                  'top-0 text-xs left-8 text-red-500': fieldErrors.video_url,
                  'top-1/2 text-gray-500': !form.video_url && !fieldErrors.video_url 
                }">
                YouTube Video URL
              </label>
              <!-- Error Message -->
              <div v-if="fieldErrors.video_url" class="mt-1 text-red-500 text-sm flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ fieldErrors.video_url }}
              </div>
            </div>

            <!-- Video Preview Section -->
            <div v-if="videoPreview && !fieldErrors.video_url" class="mb-4">
              <h3 class="text-sm font-medium text-gray-700 mb-3">Video Preview</h3>
              <div class="border border-gray-200 rounded-lg overflow-hidden bg-gray-50 max-w-xl mx-auto">
                <div class="aspect-video relative">
                  <OptimizedImage 
                    :src="videoPreview.thumbnail" 
                    :alt="videoPreview.title"
                    class="w-full h-full object-cover"
                  />
                  <div class="absolute inset-0 bg-black bg-opacity-20 flex items-center justify-center">
                    <div class="bg-red-600 text-white rounded-full p-2">
                      <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z"/>
                      </svg>
                    </div>
                  </div>
                </div>
                <div class="p-3">
                  <h4 class="font-medium text-gray-900 text-sm line-clamp-2">{{ videoPreview.title }}</h4>
                  <p class="text-gray-500 text-xs mt-1">{{ videoPreview.duration }}</p>
                </div>
              </div>
            </div>

            <!-- Help Text -->
            <div class="mb-6 p-3 bg-blue-50 rounded-lg">
              <div class="text-sm text-blue-700">
                <p class="font-medium mb-1">How to add a YouTube video:</p>
                <ul class="text-xs space-y-1">
                  <li>• Copy the URL from your YouTube video</li>
                  <li>• Supported formats: youtube.com/watch?v=..., youtu.be/...</li>
                  <li>• Make sure the video is public or unlisted</li>
                </ul>
              </div>
            </div>

            <!-- Upload Images -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Upload Images</label>
              <div class="border-2 border-dashed border-primary-200 rounded-xl p-6 text-center hover:border-primary-300 transition-colors duration-300">
                <input
                  @change="onImageChange"
                  type="file"
                  multiple
                  accept="image/*"
                  class="hidden"
                  id="business-image-upload"
                />
                <label for="business-image-upload" class="cursor-pointer">
                  <svg class="mx-auto h-12 w-12 text-primary-400" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                  </svg>
                  <p class="mt-2 text-sm text-primary-600">Click to upload images</p>
                  <p class="mt-1 text-xs text-gray-500">JPEG, JPG, PNG, and WEBP up to 5MB</p>
                </label>
              </div>
              <p class="text-sm text-gray-500 mt-2">Drag to reorder images.</p>
            </div>

            <!-- Business Images Grid -->
            <div v-if="images.length > 0">
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                Business Images
                <span v-if="imagePositionChanging" class="ml-2 text-sm text-blue-600">
                  <span class="animate-spin">⟳</span> Updating order...
                </span>
              </label>
              <draggable
                v-model="images"
                class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 cursor-move"
                :options="{ animation: 200, ghostClass: 'sortable-ghost' }"
                @start="onDragStart"
                @end="onImageReorder"
                item-key="index"
              >
                <template #item="{ element: image, index }">
                  <div class="relative group">
                    <OptimizedImage
                      :src="image.url"
                      :alt="`Business image ${index + 1}`"
                      class="w-full h-32 object-cover rounded-xl border-4 border-gray-300"
                    />
                    
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-300 rounded-xl flex items-center justify-center">
                      <button 
                        @click.stop="removeImage(index)"
                        :disabled="imageDeleting"
                        class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 disabled:opacity-50 disabled:cursor-not-allowed"
                      >
                        <span v-if="imageDeleting" class="animate-spin">⟳</span>
                        <span v-else>
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                          </svg>
                        </span>
                      </button>
                    </div>
                    <div v-if="image.isNew" class="absolute bottom-2 left-2 bg-yellow-500 text-white text-xs px-2 py-1 rounded-full">
                      New
                    </div>
                  </div>
                </template>
              </draggable>
            </div>

            <div v-if="images.length === 0" class="text-center py-8 text-gray-500">
              <p>No images uploaded yet. Upload images above to get started.</p>
            </div>
            
            <!-- Existing Images Info -->
            <div v-if="initialImages && initialImages.length > 0" class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
              <h4 class="text-sm font-medium text-blue-800 mb-2">Existing Images</h4>
              <p class="text-sm text-blue-700">
                You have {{ initialImages.length }} existing image(s) in your business gallery. 
                Upload new images above to add to your collection.
              </p>
            </div>
            
            <div v-if="fieldErrors.images" class="mt-2 text-sm text-red-600">
              {{ fieldErrors.images }}
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
          <button
            @click="closeModal"
            class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors"
          >
            Cancel
          </button>
          <button
            @click="saveGallery"
            :disabled="isSaving || imageUploading"
            class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary/85 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="isSaving || imageUploading">
              <span v-if="imageUploading">Uploading...</span>
              <span v-else>Saving...</span>
            </span>
            <span v-else>Save Gallery</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Success/Error Messages -->
    <div v-if="successMessage" class="fixed top-4 right-4 z-50 p-4 bg-green-100 border border-green-400 text-green-700 rounded-xl">
      {{ successMessage }}
    </div>
    <div v-if="errorMessage" class="fixed top-4 right-4 z-50 p-4 bg-red-100 border border-red-400 text-red-700 rounded-xl">
      {{ errorMessage }}
    </div>
  </div>
</template>

<script>
import draggable from 'vuedraggable';
import { mapActions } from 'vuex';
import { push } from 'notivue';
import { ref, computed, watch, nextTick } from 'vue';
import { validateImageFile } from '@/utils/imageValidation.js';
import MobileModalHeader from '@/components/common/MobileModalHeader.vue';
import axios from 'axios';
import { useModalScroll } from '@/composables/useModalScroll';
import OptimizedImage from '@/components/common/OptimizedImage.vue';

export default {
  name: 'BusinessGalleryModal',
  components: {
    draggable,
    MobileModalHeader,
    OptimizedImage
  },
  setup() {
    const { openModal, closeModal } = useModalScroll()
    return { openModal, closeModal }
  },
  props: {
    open: {
      type: Boolean,
      default: false
    },
    initialImages: {
      type: Array,
      default: () => []
    },
    initialVideoUrl: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      drag: false,
      isSaving: false,
      isLoading: false,
      imageUploading: false,
      imageDeleting: false,
      imagePositionChanging: false,
      successMessage: '',
      errorMessage: '',
      form: {
        video_url: ''
      },
      images: [],
      fieldErrors: {
        images: '',
        video_url: ''
      },
      videoPreview: null
    }
  },
  watch: {
    open(newVal) {
      if (newVal) {
        this.openModal('business-gallery-modal');
        this.initializeData();
      } else {
        this.closeModal('business-gallery-modal');
      }
    },
    'form.video_url': {
      handler(newVal) {
        if (this._videoPreviewTimeout) {
          clearTimeout(this._videoPreviewTimeout);
        }
        
        this._videoPreviewTimeout = setTimeout(() => {
          if (newVal && this.isValidYouTubeUrl(newVal)) {
            this.fetchVideoPreview(newVal);
          } else {
            this.videoPreview = null;
          }
        }, 500);
      },
      immediate: false
    }
  },
  beforeDestroy() {
    if (this._videoPreviewTimeout) {
      clearTimeout(this._videoPreviewTimeout);
    }
  },
  beforeUnmount() {
    if (this._videoPreviewTimeout) {
      clearTimeout(this._videoPreviewTimeout);
    }
  },
  methods: {
    ...mapActions([
      'uploadBusinessGalleryImages',
      'updateBusinessDetails',
      'fetchMyBusiness'
    ]),
    async initializeData() {
      this.isLoading = true;
      try {
        await this.fetchMyBusiness();
        
        this.form.video_url = this.initialVideoUrl || '';
        
        if (Array.isArray(this.initialImages)) {
          this.images = this.initialImages.map(img => ({
            id: img.id,
            url: img.url || img.image_url,
            isNew: false
          }));
        } else {
          this.images = [];
        }
        
        this.clearMessages();
        this.clearFieldErrors();
        
        if (this.form.video_url) {
          await this.fetchVideoPreview(this.form.video_url);
        }
      } finally {
        this.isLoading = false;
      }
    },
    
    closeModal() {
      this.$emit('close');
    },
    
    onImageChange(event) {
      const files = Array.from(event.target.files);
      files.forEach(file => {
        const validation = validateImageFile(file);
        if (!validation.isValid) {
          return;
        }
        const preview = URL.createObjectURL(file);
        this.images.push({
          url: preview,
          file,
          isNew: true
        });
      });
    },
    
    async removeImage(index) {
      const image = this.images[index];
      
      if (!image.id) {
        this.images.splice(index, 1);
        return;
      }
      
      if (image.id) {
        try {
          this.imageDeleting = true;
          this.errorMessage = '';
          
          await this.deleteImage(image.id);
          
          this.images.splice(index, 1);
          
          this.successMessage = 'Image deleted successfully!';
          setTimeout(() => {
            this.successMessage = '';
          }, 3000);
        } catch (error) {
          console.error('Error deleting image:', error);
          this.errorMessage = error.message || 'Failed to delete image. Please try again.';
        } finally {
          this.imageDeleting = false;
        }
      }
    },
    
    async deleteImage(imageId) {
      try {
        const response = await axios.delete(`/user/business/gallery/${imageId}`, {
          headers: {
            'Authorization': `Bearer ${this.$store.state.token}`,
            'Content-Type': 'application/json'
          }
        });
        
        return response.data;
      } catch (error) {
        console.error('Error deleting image:', error);
        throw new Error('Failed to delete image from server');
      }
    },
    
    clearMessages() {
      this.successMessage = '';
      this.errorMessage = '';
    },
    
    clearFieldErrors() {
      this.fieldErrors = {
        images: '',
        video_url: ''
      };
    },
    
    validateForm() {
      this.clearFieldErrors();
      let isValid = true;
      
      if (this.images.length === 0) {
        this.fieldErrors.images = 'Please upload at least one image';
        isValid = false;
      }
      
      if (this.form.video_url && !this.isValidYouTubeUrl(this.form.video_url)) {
        this.fieldErrors.video_url = 'Please enter a valid YouTube URL';
        isValid = false;
      }
      
      return isValid;
    },
    
    async saveGallery() {
      if (!this.validateForm()) {
        return;
      }
      
      this.isSaving = true;
      this.clearMessages();
      
      try {
        let hasChanges = false;
        
        const newImages = this.images.filter(img => img.isNew);
        if (newImages.length > 0) {
          await this.uploadBusinessImages();
          hasChanges = true;
        }
        
        if (this.form.video_url !== this.initialVideoUrl) {
          await this.updateBusinessVideo();
          hasChanges = true;
        }
        
        
        push.success('Business gallery updated successfully!');
        
        if (hasChanges) {
          
          this.$emit('gallery-updated', {
            images: this.images,
            video_url: this.form.video_url
          });
        }
        
        setTimeout(() => {
          this.closeModal();
        }, 1500);
        
        
      } catch (error) {
        console.error('Error saving business gallery:', error);
        push.error(error.message || 'Failed to save business gallery. Please try again.');
      } finally {
        this.isSaving = false;
      }
    },
    
    async uploadBusinessImages() {
      try {
        this.imageUploading = true;
        this.errorMessage = '';
        
        for (let i = 0; i < this.images.length; i++) {
          const image = this.images[i];
          
          if (image.id) continue;
          
          const response = await this.$store.dispatch('uploadBusinessGalleryImages', { 
            images: [image.file]
          });
          
          // if (response && response.length > 0) {
          //   const uploadedImage = response[0];
          //   this.images[i] = {
          //     id: uploadedImage.id,
          //     url: uploadedImage.url || uploadedImage.image_url,
          //     isNew: false
          //   };
          // }
        }
      } catch (error) {
        console.error('Error uploading business images:', error);
        throw error;
      } finally {
        this.imageUploading = false;
      }
    },
    
    async updateBusinessVideo() {
      try {
        await this.updateBusinessDetails({ 
          video_url: this.form.video_url 
        });
      } catch (error) {
        console.error('Error updating business video:', error);
        throw error;
      }
    },
    
    async onImageReorder(event) {
      this.drag = false;
      
      if (this.images.length === 0) return;
      
      try {
        this.imagePositionChanging = true;
        this.errorMessage = '';
        
        const imageIds = this.images.map((image, index) => ({
          id: image.id,
          position: index + 1
        })).filter(img => img.id);
        
        await this.updateImagePositions(imageIds);
        
        this.successMessage = 'Image order updated successfully!';
        setTimeout(() => {
          this.successMessage = '';
        }, 3000);
      } catch (error) {
        console.error('Error updating image positions:', error);
        this.errorMessage = error.message || 'Failed to update image positions. Please try again.';
      } finally {
        this.imagePositionChanging = false;
      }
    },
    
    onDragStart(event) {
      this.drag = true;
    },
    
    onDragCancel() {
      this.drag = false;
    },
    
    async updateImagePositions(imageIds) {
      try {
        const payload = {
          "image_ids": imageIds
        };
        
        const response = await axios.put('/user/business/gallery/positions', payload, {
          headers: {
            'Authorization': `Bearer ${this.$store.state.token}`,
            'Content-Type': 'application/json'
          }
        });
        
        return response.data;
      } catch (error) {
        console.error('Error updating image positions:', error);
        throw new Error('Failed to update image positions on server');
      }
    },
    
    isValidYouTubeUrl(url) {
      if (!url || typeof url !== 'string') return false;
      
      const youtubeRegex = /^(https?:\/\/)?(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)[a-zA-Z0-9_-]{11}([?&].*)?$/;
      return youtubeRegex.test(url);
    },
    
    async fetchVideoPreview(url) {
      try {
        const videoId = this.extractVideoId(url);
        
        if (!videoId) {
          this.videoPreview = null;
          return;
        }

        if (import.meta.env.VITE_YOUTUBE_API_KEY) {
          try {
            const response = await fetch(`https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails&id=${videoId}&key=${import.meta.env.VITE_YOUTUBE_API_KEY}`);
            if (!response.ok) {
              throw new Error(`YouTube API error: ${response.status}`);
            }
            const data = await response.json();
            if (data.items && data.items.length > 0) {
              const video = data.items[0];
              this.videoPreview = {
                thumbnail: video.snippet.thumbnails.high.url,
                title: video.snippet.title,
                duration: this.formatDuration(video.contentDetails.duration)
              };
              return;
            }
          } catch (apiError) {
            // YouTube API failed, will use fallback
          }
        }

        this.videoPreview = {
          thumbnail: `https://img.youtube.com/vi/${videoId}/maxresdefault.jpg`,
          title: 'Video Preview',
          duration: 'Loading...'
        };
      } catch (error) {
        this.videoPreview = null;
      }
    },
    
    extractVideoId(url) {
      if (!url || typeof url !== 'string') return null;
      
      const patterns = [
        /(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/,
        /youtube\.com\/embed\/([a-zA-Z0-9_-]{11})/,
        /youtube\.com\/v\/([a-zA-Z0-9_-]{11})/
      ];
      
      for (const pattern of patterns) {
        const match = url.match(pattern);
        if (match) {
          return match[1];
        }
      }
      
      return null;
    },
    
    formatDuration(duration) {
      const match = duration.match(/PT(\d+H)?(\d+M)?(\d+S)?/);
      let hours = 0;
      let minutes = 0;
      let seconds = 0;

      if (match[1]) hours = parseInt(match[1].replace('H', ''), 10);
      if (match[2]) minutes = parseInt(match[2].replace('M', ''), 10);
      if (match[3]) seconds = parseInt(match[3].replace('S', ''), 10);

      let formattedDuration = '';
      if (hours > 0) {
        formattedDuration += `${hours}h `;
      }
      if (minutes > 0 || hours > 0) {
        formattedDuration += `${minutes}m`;
      }
      if (seconds > 0 || minutes === 0) {
        formattedDuration += `${seconds}s`;
      }
      return formattedDuration.trim();
    }
  }
}
</script>

<style scoped>
/* Improve dragging experience */
.sortable-ghost {
  opacity: 0.5;
  transform: rotate(5deg);
}

/* Line clamp utility for video title */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
