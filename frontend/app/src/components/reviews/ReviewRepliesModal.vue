<template>
<div v-if="show" class="fixed inset-0 flex items-stretch md:items-center justify-center z-[9999] p-0 md:p-4 bg-white md:bg-black md:bg-opacity-50">
    <div class="bg-white w-full h-full md:h-auto md:rounded-lg md:max-w-4xl md:max-h-[90vh] flex flex-col">
      <MobileModalHeader title="All Replies" @back="$emit('close')" />
        <!-- Modal Header -->
        <div class="hidden md:flex items-center justify-between p-4 border-b flex-shrink-0">
            <h3 class="text-lg font-semibold">All Replies</h3>
            <button @click="$emit('close')" class="text-gray-500 hover:text-gray-700">
                <XMarkIcon class="w-6 h-6" />
            </button>
        </div>

        <!-- Modal Content -->
        <div class="flex flex-col flex-1 overflow-hidden">
            <!-- Scrollable Content -->
            <div class="p-4 overflow-y-auto flex-1">
                <div v-if="review" class="space-y-4">
                    <!-- Original Review -->
                    <div class="border-b pb-4">
                        <div class="flex items-center mb-2">
                            <OptimizedImage :src="review.user.profile" alt="avatar" class="w-10 h-10 rounded-full mr-3" />
                            <div>
                                <div class="font-semibold text-sm capitalize">
                                    {{ review.user.name }}
                                    <span v-if="currentUser && currentUser.id === review.user.id" class="ml-1 bg-red-100 text-red-700 px-1.5 py-0.5 rounded text-[10px]">You</span>
                                </div>
                                <div class="text-xs text-gray-700">{{ review.date }}</div>
                            </div>
                        </div>
                        <div class="flex items-center mb-1">
                            <StarRating :rating="review.rate" iconSize="h-5" />
                        </div>
                        <div v-if="review.message" class="text-sm">"{{ review.message }}"</div>

                        <!-- Original Review Images in Modal -->
                        <ReviewImageGallery v-if="review.images && review.images.length > 0" :images="review.images" />
                    </div>

                    <!-- All Replies -->
                    <div class="space-y-3">
                        <div v-for="(reply, index) in review.replies" :key="`${reply.id || reply._id || index}-${reply.images ? reply.images.length : 0}`" class="flex items-start space-x-3">
                            <OptimizedImage :src="reply.user.profile" alt="owner" class="w-8 h-8 rounded-full flex-shrink-0" />
                            <div class="flex-1">
                                <!-- Reply Content -->
                                <div class="bg-gray-100 rounded-lg p-3">
                                    <div class="flex items-center justify-between">
                                        <div class="text-sm font-semibold text-gray-900 capitalize">
                                            {{ reply.user.name }}
                                            <span v-if="currentUser && currentUser.id === reply.user.id" class="ml-1 bg-red-100 text-red-700 px-1.5 py-0.5 rounded text-[10px]">You</span>
                                            <span v-if="reply.owner" class="ml-1 bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded text-[10px]">Owner</span>
                                        </div>

                                        <!-- Reply Actions - only show if current user is the author -->
                                        <div v-if="currentUser && currentUser.id == reply.user.id" class="relative" style="z-index: 10;">
                                            <button @click="toggleReplyMenu(index)" class="p-1 hover:bg-gray-200 rounded-full">
                                                <EllipsisVerticalIcon class="h-4 w-4 text-gray-500" />
                                            </button>

                                            <!-- Reply Dropdown Menu -->
                                            <div v-if="activeReplyMenu === index" class="absolute right-0 top-6 bg-white border border-gray-200 rounded-lg shadow-lg z-50 min-w-[120px]">
                                                <button @click="openEditReplyInline(reply, index)" class="w-full text-left px-3 py-2 text-xs hover:bg-gray-50 flex items-center">
                                                    <PencilIcon class="h-3 w-3 mr-2" />
                                                    Edit reply
                                                </button>
                                                <button @click="openDeleteModal(reply)" class="w-full text-left px-3 py-2 text-xs hover:bg-gray-50 text-red-600 flex items-center">
                                                    <TrashIcon class="h-3 w-3 mr-2" />
                                                    Delete reply
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Display Mode -->
                                    <div v-if="editingReplyIndex !== index">
                                        <div class="text-xs text-gray-500 -mt-1 mb-1">{{ reply.date }}</div>
                                        <div class="text-sm text-gray-900">{{ reply.message }}</div>

                                        <!-- Reply Images -->
                                        <ReviewImageGallery v-if="reply.images && reply.images.length > 0" :images="reply.images" :key="`reply-images-${reply.id}-${reply.images.length}`" />
                                    </div>

                                    <!-- Edit Mode -->
                                    <div v-else>
                                        <div class="relative">
                                            <textarea v-model="replyForm.message" rows="2" ref="editReplyTextarea" @input="autoResizeTextarea($event.target)" class="w-full px-3 pt-2 pb-6 border border-gray-300 bg-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent resize-none overflow-hidden" placeholder="Edit your reply...">
                        </textarea>

                                            <!-- Controls inside textarea -->
                                            <div class="absolute bottom-2 right-4 flex items-center space-x-2">
                                                <!-- Image Icon -->
                                                <button @click="triggerReplyFileUpload" class="text-indigo-500 hover:text-indigo-600 transition-colors duration-200 p-1 rounded hover:bg-indigo-100">
                                                    <PhotoIcon class="w-4 h-4" />
                                                </button>

                                                <!-- Send Button -->
                                                <button @click="updateReply" :disabled="!replyForm.message.trim() || submittingReply" class="text-gradient-to-r from-purple-500 to-pink-500 disabled:opacity-50 disabled:cursor-not-allowed p-1 rounded hover:bg-gradient-to-r hover:from-purple-100 hover:to-pink-50 transition-all duration-200">
                                                    <PaperAirplaneIcon class="w-4 h-4" />
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Selected Images below textarea -->
                                        <div v-if="replyForm.images.length > 0" class="mt-3 flex space-x-2">
                                            <div v-for="(image, imgIndex) in replyForm.images" :key="imgIndex" class="relative">
                                                <OptimizedImage :src="image.url" alt="Reply image" class="w-24 h-24 object-cover rounded border border-gray-200 shadow-sm" />
                                                <button @click="removeReplyImage(imgIndex)" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center text-xs">
                                                    ×
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Cancel instruction -->
                                        <div class="text-xs text-gray-500 mt-2">
                                            Press Esc to <button @click="cancelReplyEdit" class="text-blue-500 hover:underline">cancel</button>.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fixed Reply Form at Bottom - Only show when not editing -->
            <div v-if="currentUser && review && editingReplyIndex === null" class="border-t bg-gray-50 p-4 flex-shrink-0">
                <!-- Loading indicator -->
                <div v-if="submittingReply" class="mb-3 text-center">
                    <div class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-700 rounded-lg">
                        <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-blue-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Processing...
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <OptimizedImage :src="currentUser?.business?.id === businessId ? currentUser?.business?.business_profile : currentUser?.profile" alt="avatar" class="w-8 h-8 rounded-full flex-shrink-0" />
                    <div class="flex-1">
                        <div class="relative">
                            <textarea v-model="replyForm.message" rows="2" ref="replyTextarea" @input="autoResizeTextarea($event.target)" class="w-full px-3 py-2 border border-gray-300 bg-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent resize-none overflow-hidden" placeholder="Write your reply...">
                </textarea>

                            <!-- Controls inside textarea -->
                            <div class="absolute bottom-2 right-2 flex items-center space-x-2">
                                <!-- Image Icon -->
                                <button @click="triggerReplyFileUpload" class="text-indigo-500 hover:text-indigo-600 transition-colors duration-200 p-1 rounded hover:bg-indigo-50">
                                    <PhotoIcon class="w-4 h-4" />
                                </button>

                                <!-- Send Button -->
                                <button @click="submitReply" :disabled="!replyForm.message.trim() || submittingReply" class="text-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 disabled:opacity-50 disabled:cursor-not-allowed p-1 rounded hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 transition-all duration-200">
                                    <PaperAirplaneIcon class="w-4 h-4" />
                                </button>
                            </div>
                        </div>

                        <!-- Selected Images below textarea -->
                        <div v-if="replyForm.images.length > 0" class="mt-3 flex space-x-2">
                            <div v-for="(image, index) in replyForm.images" :key="index" class="relative">
                                <OptimizedImage :src="image.url" alt="Reply image" class="w-24 h-24 object-cover rounded border border-gray-200 shadow-sm" />
                                <button @click="removeReplyImage(index)" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center text-xs">
                                    ×
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden file input for reply images -->
    <input ref="replyFileInput" type="file" multiple accept="image/*" @change="handleReplyFileUpload" class="hidden" style="display: none;" />

    <!-- Delete Modal for replies -->
    <DeleteReviewModal :show="showDeleteModal" :item="selectedReplyForAction" @close="closeDeleteModal" @item-deleted="handleReplyDeleted" @error="handleError" />
</div>
</template>

<script>
import {
    XMarkIcon,
    EllipsisVerticalIcon,
    PencilIcon,
    TrashIcon,
    PhotoIcon,
    PaperAirplaneIcon
} from '@heroicons/vue/24/outline'
import StarRating from '@/components/StarRating.vue'
import ReviewImageGallery from '@/components/ReviewImageGallery.vue'
import DeleteReviewModal from './DeleteReviewModal.vue'
import axios from 'axios'
import { validateImageFile } from '@/utils/imageValidation.js'
import MobileModalHeader from '@/components/common/MobileModalHeader.vue'
import { useModalScroll } from '@/composables/useModalScroll'
import OptimizedImage from '@/components/common/OptimizedImage.vue'

export default {
    name: 'ReviewRepliesModal',
    components: {
        XMarkIcon,
        EllipsisVerticalIcon,
        PencilIcon,
        TrashIcon,
        PhotoIcon,
        PaperAirplaneIcon,
        StarRating,
        ReviewImageGallery,
        DeleteReviewModal,
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
        currentUser: {
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
            activeReplyMenu: null,
            submittingReply: false,
            editingReplyIndex: null,
            selectedReplyForAction: null,
            showDeleteModal: false,
            replyForm: {
                message: '',
                images: []
            },
        }
    },
    watch: {
        show(newVal) {
            if (newVal) {
                this.openModal('review-replies-modal');
                this.resetForm()
            } else {
                this.closeModal('review-replies-modal');
            }
        }
    },
    mounted() {
        document.addEventListener('keydown', this.handleKeydown)
    },
    beforeUnmount() {
        document.removeEventListener('keydown', this.handleKeydown)
    },
    methods: {
        // Form Management
        resetForm() {
            this.replyForm = {
                message: '',
                images: []
            }
            this.editingReplyIndex = null
            this.selectedReplyForAction = null
            this.activeReplyMenu = null
        },

        // Keyboard Event Handling
        handleKeydown(event) {
            if (event.key === 'Escape' && this.editingReplyIndex !== null) {
                this.cancelReplyEdit()
            }
        },

        // UI State Management
        toggleReplyMenu(replyId) {
            this.activeReplyMenu = this.activeReplyMenu === replyId ? null : replyId
        },

        // Edit Mode Management
        openEditReplyInline(reply, index) {
            this.selectedReplyForAction = reply
            this.replyForm = {
                message: reply.message || '',
                images: reply.images ? reply.images.map(img => ({
                    url: typeof img === 'string' ? img : img.url,
                    id: img.id || null,
                    isExisting: true
                })) : []
            }
            this.editingReplyIndex = index
            this.activeReplyMenu = null
        },

        cancelReplyEdit() {
            this.editingReplyIndex = null
            this.selectedReplyForAction = null
            this.replyForm = {
                message: '',
                images: []
            }
        },

        // API Operations
        async updateReply() {
            if (!this.selectedReplyForAction) return

            try {
                this.submittingReply = true

                // Prepare two arrays for images
                const originalImages = this.selectedReplyForAction.images || []
                const currentImages = this.replyForm.images || []

                // Array 1: Deleted image IDs (images that were in original but not in current)
                const deletedImageIds = originalImages
                    .filter(originalImg => {
                        const originalUrl = typeof originalImg === 'string' ? originalImg : originalImg.url
                        const originalId = originalImg.id
                        
                        const isStillPresent = currentImages.some(currentImg => {
                            const currentUrl = currentImg.url || (typeof currentImg === 'string' ? currentImg : null)
                            const currentId = currentImg.id
                            return currentUrl === originalUrl && currentId === originalId
                        })
                        return !isStillPresent
                    })
                    .map(img => {
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
                    })
                    .filter(id => id !== null)

                const formData = new FormData()
                formData.append('business_id', this.businessId)
                formData.append('rating', 5)
                formData.append('message', this.replyForm.message)
                formData.append('parent_id', this.review.id)
                formData.append('_method', 'PUT')
                deletedImageIds.forEach((id, index) => formData.append(`images[delete][${index}]`, id))
                currentImages.filter(img => img.file).forEach((img, index) => {
                    formData.append(`images[add][${index}]`, img.file)
                })

                const response = await axios.post(`/user/business/review/${this.selectedReplyForAction.id}`, formData)

                if (response.data.status) {
                    // Update the reply in review.replies using response data
                    const updatedReply = response.data.data
                    const replyIndex = this.review.replies.findIndex(r => r.id === this.selectedReplyForAction.id)
                    
                    if (replyIndex !== -1) {
                        // Update existing reply - use direct assignment for Vue 3
                        this.review.replies[replyIndex] = updatedReply
                    }

                    this.$emit('reply-updated', this.selectedReplyForAction.id, {
                        message: this.replyForm.message,
                        images: updatedReply.images || []
                    })
                    this.cancelReplyEdit()

                    // Show success message
                    this.$emit('success', 'Reply updated successfully!')
                }
            } catch (error) {
                this.$emit('error', 'Failed to update reply')
            } finally {
                this.submittingReply = false
            }
        },

        // File Upload Management
        triggerReplyFileUpload() {
            if (this.$refs.replyFileInput) {
                this.$refs.replyFileInput.click()
            }
        },

        handleReplyFileUpload(event) {
            const files = Array.from(event.target.files)
            files.forEach(file => {
                if (file.type.startsWith('image/')) {
                    const validation = validateImageFile(file)
                    if (!validation.isValid) {
                        this.$emit('error', validation.error)
                        return
                    }
                    const preview = URL.createObjectURL(file)
                    this.replyForm.images.push({
                        url: preview,
                        file: file,
                        isExisting: false
                    })
                }
            })
            event.target.value = ''
        },

        removeReplyImage(index) {
            this.replyForm.images.splice(index, 1)
        },

        // Delete Modal Management

        async submitReply() {
            if (!this.replyForm.message.trim() || !this.review) return

            try {
                this.submittingReply = true

                const formData = new FormData()
                formData.append('business_id', this.businessId)
                formData.append('rating', 5)
                formData.append('message', this.replyForm.message)
                formData.append('parent_id', this.review.id)
                this.replyForm.images.filter(img => img.file).forEach((img, index) => {
                    formData.append(`images[${index}]`, img.file)
                })

                const response = await axios.post('/user/business/review', formData)

                if (response.data.status) {
                    // Use the actual API response data for instant update
                    const serverReply = response.data.data

                    // Add to local review immediately for instant display
                    if (!this.review.replies) {
                        this.review.replies = []
                    }
                    this.review.replies.push(serverReply)

                    this.$emit('reply-submitted', {
                        message: this.replyForm.message,
                        images: serverReply.images || []
                    })
                    this.resetForm()

                    // Show success message
                    this.$emit('success', 'Reply submitted successfully!')
                }
            } catch (error) {
                this.$emit('error', 'Failed to submit reply')
            } finally {
                this.submittingReply = false
            }
        },

        openDeleteModal(reply) {
            this.selectedReplyForAction = reply
            this.showDeleteModal = true
            this.activeReplyMenu = null
        },

        closeDeleteModal() {
            this.showDeleteModal = false
            this.selectedReplyForAction = null
        },

        handleReplyDeleted(reply, message) {
            // Remove reply locally immediately for instant display
            if (this.review.replies) {
                this.review.replies = this.review.replies.filter(r => r.id !== reply.id)
            }
            this.$emit('reply-deleted', reply.id)
            this.closeDeleteModal()
        },

        // Utility Methods
        handleError(message) {
            this.$emit('error', message)
        },

        autoResizeTextarea(textarea) {
            textarea.style.height = 'auto'
            textarea.style.height = textarea.scrollHeight + 'px'
        }
    }
}
</script>
