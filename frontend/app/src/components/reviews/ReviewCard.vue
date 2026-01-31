<template>
  <div class="border border-gray-300 p-4 rounded-md" :data-review-id="review.id">
    <!-- Review Header -->
    <div class="flex items-center justify-between mb-2">
      <div class="flex items-center">
        <OptimizedImage :src="review.user.profile" alt="avatar" class="w-10 h-10 rounded-full mr-3" />
        <div>
          <div class="font-semibold text-sm capitalize">{{ review.user.name }} <span v-if="currentUser && currentUser.id === review.user.id" class="ml-1 bg-red-100 text-red-700 px-1.5 py-0.5 rounded text-[10px]">You</span></div>
          <div class="text-xs text-gray-700">{{ review.date }}</div>
        </div>
      </div>

      <!-- 3-dot menu - only show if current user is the author -->
      <div v-if="currentUser && currentUser.id === review.user.id" class="relative">
        <button @click="toggleReviewMenu(review.id)" class="p-1 hover:bg-gray-100 rounded-full">
          <EllipsisVerticalIcon class="h-5 w-5 text-gray-500" />
        </button>

        <!-- Dropdown Menu -->
        <div v-if="activeMenu === review.id" class="absolute right-0 top-8 bg-white border border-gray-200 rounded-lg shadow-lg z-10 min-w-[150px]">
          <button @click="openEditModal(review)" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 text-blue-600 flex items-center">
            <PencilIcon class="h-4 w-4 mr-2" />
            Edit
          </button>
          <button @click="openDeleteModal(review)" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 text-red-600 flex items-center">
            <TrashIcon class="h-4 w-4 mr-2" />
            Delete
          </button>
        </div>
      </div>
    </div>

    <!-- Rating -->
    <div class="flex items-center mb-1">
      <StarRating :rating="review.rate" iconSize="h-7" />
    </div>

    <!-- Review Message -->
    <div v-if="review.message" class="mb-2 text-sm">"{{ review.message }}"</div>

    <!-- Review Images -->
    <ReviewImageGallery v-if="review.images && review.images.length > 0" :images="review.images" />

    <!-- First Reply Only -->
    <div v-if="review.replies && review.replies.length" class="space-y-2 mt-3">
      <div class="flex items-start bg-primary/10 rounded p-2">
        <OptimizedImage :src="review.replies[0].user.profile" alt="owner" class="w-10 h-10 rounded-full mr-2" />
        <div>
          <div @click="openRepliesModal(review)" class="cursor-pointer">
            <div class="text-sm font-semibold capitalize">
              {{ review.replies[0].user.name }} 
              <span v-if="currentUser && currentUser.id === review.replies[0].user.id" class="ml-1 bg-red-100 text-red-700 px-1.5 py-0.5 rounded text-[10px]">You</span>
              <span v-if="review.replies[0].owner" class="ml-1 bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded text-[10px]">Owner</span>
            </div>
            <div class="text-xs text-gray-500 mb-1">{{ review.replies[0].date }}</div>
            <div class="text-sm mt-1">{{ review.replies[0].message }}</div>
          </div>

          <!-- Reply Images -->
          <ReviewImageGallery v-if="review.replies[0].images && review.replies[0].images.length > 0" :images="review.replies[0].images" />
        </div>
      </div>
    </div>

    <!-- Actions -->
    <div class="flex items-center space-x-6 text-xs text-gray-500 mt-3">
      <button @click="openRepliesModal(review)" class="flex items-center hover:text-primary cursor-pointer">
        <ChatBubbleBottomCenterTextIcon class="h-4 mr-1" />
        {{ (review.replies && review.replies.length) || 0 }} Comments
      </button>
      <!-- Reply Button - only show for authenticated users -->
      <button v-if="currentUser" @click="openRepliesModal(review)" class="flex items-center hover:text-primary cursor-pointer">
        <PencilSquareIcon class="h-4 mr-1" />
        Write a comment
      </button>
    </div>
  </div>
</template>

<script>
import {
  ChatBubbleBottomCenterTextIcon,
  PencilSquareIcon,
  EllipsisVerticalIcon,
  PencilIcon,
  TrashIcon
} from '@heroicons/vue/24/outline'
import StarRating from '@/components/StarRating.vue'
import ReviewImageGallery from '@/components/ReviewImageGallery.vue'
import OptimizedImage from '@/components/common/OptimizedImage.vue'

export default {
  name: 'ReviewCard',
  components: {
    ChatBubbleBottomCenterTextIcon,
    PencilSquareIcon,
    EllipsisVerticalIcon,
    PencilIcon,
    TrashIcon,
    StarRating,
    ReviewImageGallery,
    OptimizedImage
  },
  props: {
    review: {
      type: Object,
      required: true
    },
    currentUser: {
      type: Object,
      default: null
    }
  },
  data() {
    return {
      activeMenu: null
    }
  },
  methods: {
    toggleReviewMenu(reviewId) {
      this.activeMenu = this.activeMenu === reviewId ? null : reviewId
    },
    openEditModal(review) {
      this.$emit('edit-review', review)
      this.activeMenu = null
    },
    openDeleteModal(review) {
      this.$emit('delete-review', review)
      this.activeMenu = null
    },
    openRepliesModal(review) {
      this.$emit('open-replies', review)
    }
  }
}
</script> 