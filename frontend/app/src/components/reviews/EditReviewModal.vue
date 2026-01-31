<template>
  <div v-if="show" class="fixed inset-0 flex items-stretch md:items-center justify-center z-[9999] p-0 md:p-4 bg-white md:bg-black md:bg-opacity-50">
    <div class="bg-white w-full h-full md:h-auto md:rounded-lg md:max-w-md md:max-h-[80vh] overflow-y-auto">
      <MobileModalHeader title="Edit Review" @back="$emit('close')" />
      <div class="p-4">
      <div class="hidden md:flex items-center justify-between p-4 border-b">
        <h3 class="text-lg font-semibold">Edit Review</h3>
        <button @click="$emit('close')" class="text-gray-500 hover:text-gray-700">
          <XMarkIcon class="w-6 h-6" />
        </button>
      </div>

      <div class="p-4">
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
          <StarRating :rating="editForm.rating" iconSize="h-6" @rating-changed="editForm.rating = $event" :editable="true" class="w-full" />
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Review</label>
          <div class="relative">
            <textarea 
              v-model="editForm.message" 
              rows="2" 
              ref="editReviewTextarea"
              @input="autoResizeTextarea($event.target)"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent resize-none overflow-hidden" 
              placeholder="Write your review...">
            </textarea>
            
            <!-- Image upload button inside textarea -->
            <div class="absolute bottom-2 right-2">
              <button @click="triggerImageUpload" class="text-indigo-500 hover:text-indigo-600 transition-colors duration-200 p-1 rounded hover:bg-indigo-100">
                <PhotoIcon class="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>

        <!-- Images Section -->
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Images</label>

          <!-- Selected Images -->
          <div v-if="editForm.images && editForm.images.length > 0" class="flex flex-wrap gap-2 mb-3">
            <div v-for="(image, index) in editForm.images" :key="index" class="relative">
              <OptimizedImage :src="image.url || image" alt="Review image" class="w-20 h-20 object-cover rounded-md border" />
              <button @click="removeImage(index)" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-600">
                ×
              </button>
            </div>
          </div>

                     <!-- Add New Images Button -->
           <div class="flex flex-wrap gap-2">
             <label class="flex flex-col items-center justify-center w-20 h-20 border-2 border-dashed border-gray-300 rounded cursor-pointer hover:border-primary">
               <input 
                 ref="fileInput"
                 type="file" 
                 class="hidden" 
                 @change="handleImageUpload" 
                 multiple 
                 accept="image/*" 
                 :key="fileInputKey" />
               <PlusIcon class="w-6 h-6 text-gray-400" />
               <span class="text-xs text-gray-500 mt-1">Add</span>
             </label>
           </div>
        </div>

        <div class="flex justify-end space-x-3">
          <button @click="$emit('close')" class="px-4 py-2 text-gray-600 hover:text-gray-800">
            Cancel
          </button>
          <button @click="updateReview" :disabled="updating" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark disabled:opacity-50">
            {{ updating ? 'Updating...' : 'Update' }}
          </button>
        </div>
      </div>
      </div>
    </div>
  </div>
</template>

<script>
import { XMarkIcon, PlusIcon, PhotoIcon } from '@heroicons/vue/24/outline'
import StarRating from '@/components/StarRating.vue'
import axios from 'axios'
import { validateImageFile } from '@/utils/imageValidation.js'
import MobileModalHeader from '@/components/common/MobileModalHeader.vue'
import { useModalScroll } from '@/composables/useModalScroll'
import OptimizedImage from '@/components/common/OptimizedImage.vue'

export default {
  name: 'EditReviewModal',
  components: {
    XMarkIcon,
    PlusIcon,
    PhotoIcon,
    StarRating,
    MobileModalHeader,
    OptimizedImage
  },
  setup() {
    const { openModal, closeModal } = useModalScroll()
    return { openModal, closeModal }
  },
  props: {
    show: {
      type: Boolean,
      default: false
    },
    review: {
      type: Object,
      default: null
    },
    businessId: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      updating: false,
      fileInputKey: 0,
      editForm: {
        rating: 0,
        message: '',
        images: []
      }
    }
  },
  watch: {
    review: {
      handler(newReview) {
        if (newReview) {
          this.editForm = {
            rating: newReview.rate || 0,
            message: newReview.message || '',
            images: newReview.images ? newReview.images.map(img => ({
              url: typeof img === 'string' ? img : img.url,
              id: img.id || null,
              isExisting: true
            })) : []
          }
        }
      },
      immediate: true
    },
    show(newVal) {
      if (newVal) {
        this.openModal('edit-review-modal');
        // Regenerate file input key to ensure fresh file input
        this.fileInputKey++
        // Reset file input when modal opens
        this.$nextTick(() => {
          if (this.$refs.fileInput) {
            this.$refs.fileInput.value = ''
          }
        })
      } else {
        this.closeModal('edit-review-modal');
      }
    }
  },
  methods: {
    async updateReview() {
      try {
        this.updating = true
        
        // Prepare images data for update
        const originalImages = this.review.images || []
        const currentImages = this.editForm.images || []
        
        // Find images to delete (existing images that are no longer in currentImages)
        const imagesToDelete = originalImages.filter(originalImg => {
          const originalUrl = typeof originalImg === 'string' ? originalImg : originalImg.url
          const originalId = originalImg.id
          
          return !currentImages.some(currentImg => {
            const currentUrl = currentImg.url || (typeof currentImg === 'string' ? currentImg : null)
            const currentId = currentImg.id
            return currentUrl === originalUrl && currentId === originalId
          })
        }).map(img => {
          // Extract gallery ID from image
          if (img.id) return img.id
          if (typeof img === 'string') {
            const urlParts = img.split('/')
            return urlParts[urlParts.length - 1]
          }
          if (img.url) {
            const urlParts = img.url.split('/')
            return urlParts[urlParts.length - 1]
          }
          return null
        }).filter(id => id !== null)

        // Build multipart form data for update
        const formData = new FormData()
        formData.append('business_id', this.businessId)
        formData.append('rating', this.editForm.rating)
        formData.append('message', this.editForm.message)
        formData.append('parent_id', '')
        formData.append('_method', 'PUT')
        imagesToDelete.forEach((id, index) => formData.append(`images[delete][${index}]`, id))
        currentImages.filter(img => img.file).forEach((img, index) => {
          formData.append(`images[add][${index}]`, img.file)
        })

        const response = await axios.post(`/user/business/review/${this.review.id}`, formData)

        if (response.data.status) {
          // Use the actual API response data for instant update
          const serverReview = response.data.data
          
          // Ensure images are properly formatted for display
          if (serverReview.images && Array.isArray(serverReview.images)) {
            serverReview.images = serverReview.images.map(img => {
              if (typeof img === 'string') {
                return img
              } else if (img.url) {
                return img.url
              } else if (img.id) {
                // If it's a gallery object, extract the URL
                return img.url || img.path || img
              }
              return img
            })
          }
          
          this.$emit('review-updated', this.review.id, {
            rating: this.editForm.rating,
            message: this.editForm.message,
            images: serverReview.images || []
          })
          this.$emit('close')
        }
      } catch (error) {
        this.$emit('error', 'Failed to update review')
      } finally {
        this.updating = false
      }
    },

    triggerImageUpload() {
      // Use the ref to access the file input
      if (this.$refs.fileInput) {
        // Clear any existing value to ensure fresh selection
        this.$refs.fileInput.value = ''
        this.$refs.fileInput.click()
      }
    },

    handleImageUpload(event) {
      const files = Array.from(event.target.files)
      
      // Clear the input value immediately to prevent reuse
      event.target.value = ''
      
      // Create a Set to track processed files and prevent duplicates
      const processedFiles = new Set()
      
      // Process each file
      files.forEach((file, index) => {
        if (file.type.startsWith('image/')) {
          // Create a unique key for this file
          const fileKey = `${file.name}_${file.size}_${file.lastModified}`
          
          // Skip if this file has already been processed
          if (processedFiles.has(fileKey)) {
            return
          }
          
          // Mark this file as processed
          processedFiles.add(fileKey)
          
          // Check if this file is already in the images array
          const isDuplicate = this.editForm.images.some(img => {
            if (img.file) {
              const imgKey = `${img.file.name}_${img.file.size}_${img.file.lastModified}`
              return imgKey === fileKey
            }
            return false
          })
          
          if (!isDuplicate) {
            const validation = validateImageFile(file)
            if (!validation.isValid) {
              return
            }
            const preview = URL.createObjectURL(file)
            this.editForm.images.push({
              url: preview,
              file: file,
              isExisting: false,
              fileId: `file_${Date.now()}_${index}`
            })
          }
        }
      })
    },

    removeImage(index) {
      this.editForm.images.splice(index, 1)
    },

    autoResizeTextarea(textarea) {
      textarea.style.height = 'auto'
      textarea.style.height = textarea.scrollHeight + 'px'
    }
  }
}
</script> 