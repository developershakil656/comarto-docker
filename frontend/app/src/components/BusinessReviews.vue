<template>
  <div>
  <!-- <div class="bg-white rounded-lg shadow-md p-4 md:p-6 mb-6 border border-gray-300"> -->
    <h2 class="text-lg md:text-2xl font-semibold mb-4 capitalize">Reviews & Ratings - {{ businessName }}</h2>

    <!-- Content (hidden when loading) -->
    <div v-if="!loading">
      <div class="mb-5 p-4 rounded-md shadow border" v-if="currentUser?.business?.id !== businessId">
        <div class="text-sm md:text-lg font-medium mb-1">Start your Review</div>
        <MakeReview :businessSlug="businessSlug" :businessId="businessId" />
      </div>

      <ReviewRating :rating="rating" />
    </div>

    <!-- All Review Images Slider -->
    <div v-if="allReviewImages.length > 0" class="mb-6">
      <h3 class="text-sm md:text-lg font-semibold mb-3">All Review Images</h3>
      <ImageSlider :images="allReviewImages" sliderTitle="Business Review Images" :visibleCount="5" />
    </div>

    <!-- Loading State -->
    <div v-if="loading">
      <ReviewSkeleton />
    </div>

    <!-- User Reviews -->
    <div v-else-if="filteredReviews.length" class="space-y-4">
      <div class="text-sm text-gray-600">{{ totalReviews }} reviews found</div>
      <ReviewFilters :currentFilter="currentFilter" @filter-changed="setFilter" />

      <ReviewCard v-for="(review, index) in filteredReviews"
        :key="`${review.id || review.date}-${index}-${currentFilter}`" :review="review" :currentUser="currentUser"
        @edit-review="openEditModal" @delete-review="openDeleteModal" @open-replies="openRepliesModal" />
      <div ref="loadMoreRef" class="h-6"></div>
      <div v-if="reviewsLoadingMore" class="text-center text-gray-500 py-2">Loading more...</div>
      <div v-else-if="!reviewsCanLoadMore && filteredReviews.length" class="text-center text-gray-400 py-2 text-sm">No
        more results</div>
    </div>

    <!-- No Reviews -->
    <div v-else-if="!loading" class="text-center py-8">
      <div class="text-gray-500">
        {{ currentFilter === 'with-image' ? 'No reviews with images found.' : 'No reviews found.' }}
      </div>
    </div>

    <!-- Modals -->
    <EditReviewModal :show="showEditModal" :review="selectedReviewForAction" :businessId="businessId"
      @close="closeEditModal" @review-updated="handleReviewUpdated" @error="handleError" />

    <DeleteReviewModal :show="showDeleteModal" :item="selectedReviewForAction" @close="closeDeleteModal"
      @item-deleted="handleItemDeleted" @error="handleError" />

    <ReviewRepliesModal :show="showRepliesModal" :review="selectedReview" :currentUser="currentUser"
      :businessId="businessId" @close="closeRepliesModal" @reply-submitted="handleReplySubmitted"
      @reply-updated="handleReplyUpdated" @reply-deleted="handleReplyDeleted" @error="handleError"
      @success="handleSuccess" />
  <!-- </div> -->
   </div>
</template>

<script setup>
import { ref, onMounted, watch, computed, nextTick } from 'vue'
import { useStore } from 'vuex'
import { useRoute, useRouter } from 'vue-router'
import MakeReview from "@/components/MakeReview.vue"
import ReviewFilters from './reviews/ReviewFilters.vue'
import ReviewRating from './reviews/ReviewRating.vue'
import ReviewCard from './reviews/ReviewCard.vue'
import ReviewSkeleton from './reviews/ReviewSkeleton.vue'
import EditReviewModal from './reviews/EditReviewModal.vue'
import DeleteReviewModal from './reviews/DeleteReviewModal.vue'
import ReviewRepliesModal from './reviews/ReviewRepliesModal.vue'
import { useReviews } from '@/composables/useReviews'
import { useReviewFilters } from '@/composables/useReviewFilters'
import ImageSlider from '@/components/common/ImageSlider.vue'

const props = defineProps({
  businessSlug: {
    type: String,
    required: true
  },
  businessId: {
    type: String,
    required: true
  },
  businessName: {
    type: String,
    default: ''
  }
})
const store = useStore()
const route = useRoute()
const router = useRouter()
const currentFilter = ref('latest')
const currentUser = computed(() => store.getters.user)
const targetReviewId = ref(null)
const loadMoreRef = ref(null)
let observer = null

// Modal states
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const showRepliesModal = ref(false)
const selectedReviewForAction = ref(null)
const selectedReview = ref(null)

const { reviews, rating, loading, fetchReviews, loadMoreReviews, canLoadMore, loadingMore, meta } = useReviews(props.businessId, currentFilter)
const { filteredReviews } = useReviewFilters(reviews, currentFilter)

const totalReviews = computed(() => meta.value?.total || filteredReviews.value.length)
const reviewsCanLoadMore = computed(() => canLoadMore.value)
const reviewsLoadingMore = computed(() => loadingMore.value)

const setupInfiniteScroll = () => {
  if (observer) observer.disconnect()
  const options = { root: null, rootMargin: '200px', threshold: 0 }
  observer = new IntersectionObserver(async (entries) => {
    const entry = entries[0]
    if (entry && entry.isIntersecting && canLoadMore.value && !loadingMore.value && !loading.value) {
      await loadMoreReviews()
    }
  }, options)
  if (loadMoreRef.value) observer.observe(loadMoreRef.value)
}

const allReviewImages = computed(() => {
  return reviews.value.flatMap(review => review.images || []);
});

const setFilter = (filter) => {
  currentFilter.value = filter
}

// Modal handlers
const openEditModal = (review) => {
  selectedReviewForAction.value = review
  showEditModal.value = true
}

const closeEditModal = () => {
  showEditModal.value = false
  selectedReviewForAction.value = null
}

const openDeleteModal = (item) => {
  selectedReviewForAction.value = item
  showDeleteModal.value = true
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  selectedReviewForAction.value = null
}

const openRepliesModal = (review) => {
  // Find the latest version of the review from the main array
  const latestReview = reviews.value.find(r => r.id === review.id)
  selectedReview.value = latestReview || review
  showRepliesModal.value = true
}

const closeRepliesModal = () => {
  showRepliesModal.value = false
  selectedReview.value = null
}

// Event handlers - Simple approach: always refresh from server
const handleReviewUpdated = (reviewId, updatedData) => {
  fetchReviews()
}

const handleItemDeleted = (item, message) => {
  fetchReviews()
}

const handleReplySubmitted = (replyData) => {
  fetchReviews()
}

const handleReplyUpdated = (replyId, updatedData) => {
  fetchReviews()
}

const handleReplyDeleted = (replyId) => {
  fetchReviews()
}

const handleError = (message) => {
  console.error(message)
}

const handleSuccess = (message) => {
}

onMounted(() => {
  fetchReviews().then(() => nextTick(() => setupInfiniteScroll()))
  // Check for reviewId in query parameters
  if (route.query.reviewId) {
    targetReviewId.value = route.query.reviewId
  }
})

watch(() => props.businessId, (newVal, oldVal) => {
  if (newVal !== oldVal) {
    fetchReviews()
  }
})

    // Re-attach observer when list updates
    watch([loading, filteredReviews], () => nextTick(() => setupInfiniteScroll()))

    // Refetch when filter changes
    watch(currentFilter, (newVal, oldVal) => {
      if (newVal !== oldVal) {
        fetchReviews()
      }
    })

// Watch for filtered reviews to scroll to target review
watch(filteredReviews, async (newReviews) => {
  if (targetReviewId.value && newReviews.length > 0) {
    await nextTick()
    const targetReview = newReviews.find(review => review.id === targetReviewId.value)
    if (targetReview) {
      // Find the review element and scroll to it
      const reviewElement = document.querySelector(`[data-review-id="${targetReviewId.value}"]`)
      if (reviewElement) {
        reviewElement.scrollIntoView({
          behavior: 'smooth',
          block: 'center'
        })
        // Add highlight effect
        reviewElement.classList.add('ring-2', 'ring-primary', 'ring-opacity-100')
        setTimeout(() => {
          reviewElement.classList.remove('ring-2', 'ring-primary', 'ring-opacity-100')
        }, 10000)
        // Remove reviewId and tab from route after 2 seconds
        setTimeout(() => {
          const query = { ...route.query }
          delete query.reviewId
          delete query.tab
          router.replace({
            path: route.path,
            query: Object.keys(query).length > 0 ? query : undefined
          })
        }, 10000)
      }
    }
  }
})

// Cleanup
watch(() => route.fullPath, () => {
  if (observer) observer.disconnect()
})

</script>