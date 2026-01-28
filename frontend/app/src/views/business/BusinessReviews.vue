<template>
	<div>
		<AdminDashboardHeader title="Business Reviews" />
		<div class="max-w-4xl md:mx-auto sm:m-4 m-2 md:mt-8">
			<div class="bg-white rounded-lg shadow-md p-6 mb-6 border border-gray-300">
				<h2 class="text-xl md:text-2xl font-semibold mb-4 capitalize">Reviews & Ratings - {{ businessName || '' }}</h2>

			<!-- Content (hidden when loading) -->
			<div v-if="!loading">
				<ReviewRating :rating="rating" />
			</div>

			<!-- Loading State -->
			<div v-if="loading">
				<ReviewSkeleton />
			</div>

			<!-- User Reviews -->
			<div v-else-if="filteredReviews.length" class="space-y-4">
				<div class="text-sm text-gray-600">{{ totalReviews }} reviews found</div>
				<ReviewFilters 
					:currentFilter="currentFilter" 
					@filter-changed="setFilter" 
				/>

				<ReviewCard 
					v-for="(review, index) in filteredReviews" 
					:key="`${review.id || review.date}-${index}-${currentFilter}`"
					:review="review"
					:currentUser="currentUser"
					@edit-review="openEditModal"
					@delete-review="openDeleteModal"
					@open-replies="openRepliesModal"
				/>
				<div ref="loadMoreRef" class="h-6"></div>
				<div v-if="loadingMore" class="text-center text-gray-500 py-2">Loading more...</div>
				<div v-else-if="!canLoadMore && filteredReviews.length" class="text-center text-gray-400 py-2 text-sm">No more results</div>
			</div>

			<!-- No Reviews -->
			<div v-else-if="!loading" class="text-center py-8">
				<div class="text-gray-500">
				{{ currentFilter === 'with-image' ? 'No reviews with images found.' : 'No reviews found.' }}
			</div>
		</div>

			<!-- Modals -->
			<EditReviewModal 
				:show="showEditModal"
				:review="selectedReviewForAction"
				:businessId="businessId"
				@close="closeEditModal"
				@review-updated="handleReviewUpdated"
				@error="handleError"
			/>

			<DeleteReviewModal 
				:show="showDeleteModal"
				:item="selectedReviewForAction"
				@close="closeDeleteModal"
				@item-deleted="handleItemDeleted"
				@error="handleError"
			/>

			<ReviewRepliesModal 
				:show="showRepliesModal"
				:review="selectedReview"
				:currentUser="currentUser"
				:businessId="businessId"
				@close="closeRepliesModal"
				@reply-submitted="handleReplySubmitted"
				@reply-updated="handleReplyUpdated"
				@reply-deleted="handleReplyDeleted"
				@error="handleError"
				@success="handleSuccess"
			/>
		</div>
		</div>
	</div>
</template>

<script>
import { push } from 'notivue';
import { ref, computed, watch, nextTick, onMounted } from 'vue';
import { useStore } from 'vuex'
import axios from 'axios'
import AdminDashboardHeader from '@/components/business/AdminDashboardHeader.vue'
import ReviewFilters from '@/components/reviews/ReviewFilters.vue'
import ReviewRating from '@/components/reviews/ReviewRating.vue'
import ReviewCard from '@/components/reviews/ReviewCard.vue'
import ReviewSkeleton from '@/components/reviews/ReviewSkeleton.vue'
import EditReviewModal from '@/components/reviews/EditReviewModal.vue'
import DeleteReviewModal from '@/components/reviews/DeleteReviewModal.vue'
import ReviewRepliesModal from '@/components/reviews/ReviewRepliesModal.vue'
import { useReviewFilters } from '@/composables/useReviewFilters'

export default {
	name: 'BusinessReviews',
	components: {
		AdminDashboardHeader,
		ReviewFilters,
		ReviewRating,
		ReviewCard,
		ReviewSkeleton,
		EditReviewModal,
		DeleteReviewModal,
		ReviewRepliesModal
	},
			setup() {
			const store = useStore()
			const currentFilter = ref('latest')
			const currentUser = computed(() => store.getters.user)
			const myBusiness = computed(() => store.getters.myBusiness)
			
			// Modal states
			const showEditModal = ref(false)
			const showDeleteModal = ref(false)
			const showRepliesModal = ref(false)
			const selectedReviewForAction = ref(null)
			const selectedReview = ref(null)
			
			// Get business data from Vuex
			const businessId = computed(() => myBusiness.value?.id || '')
			const businessName = computed(() => myBusiness.value?.business_name || '')
			
			// Create a custom fetchReviews function that uses the current businessId
			const reviews = ref([])
			const rating = ref({})
			const loading = ref(false)
			const loadingMore = ref(false)
			const meta = ref({ current_page: 1, last_page: 1, total: 0 })
			const loadMoreRef = ref(null)
			let observer = null
			
			const fetchReviews = async () => {
				if (!businessId.value) return
				
				try {
					loading.value = true
					const response = await axios.get(`/business/reviews/${businessId.value}`, { 
						params: { 
							page: 1,
							sort: currentFilter.value || 'latest'
						} 
					})
					if (response.data.status) {
						// Handle both old and new API response structures
						const list = response.data.data.data || response.data.data.reviews || []
						const m = response.data.data.meta || { current_page: 1, last_page: 1, total: Array.isArray(list) ? list.length : 0 }
						reviews.value = Array.isArray(list) ? list : []
						rating.value = response.data.data.rating || {}
						meta.value = m
					}
				} catch (error) {
					console.error('Error fetching reviews:', error)
					reviews.value = []
					rating.value = {}
					meta.value = { current_page: 1, last_page: 1, total: 0 }
				} finally {
					loading.value = false
				}
			}

			const canLoadMore = computed(() => (meta.value.current_page || 1) < (meta.value.last_page || 1))
			const totalReviews = computed(() => meta.value?.total || filteredReviews.value.length)

			const loadMoreReviews = async () => {
				try {
					if (!canLoadMore.value || loadingMore.value) return []
					loadingMore.value = true
					const nextPage = (meta.value.current_page || 1) + 1
					const response = await axios.get(`/business/reviews/${businessId.value}`, { 
						params: { 
							page: nextPage,
							sort: currentFilter.value || 'latest'
						} 
					})
					const list = response.data.data.data || response.data.data.reviews || []
					const m = response.data.data.meta
					if (Array.isArray(list) && list.length) {
						// Append new reviews to the end to maintain pagination order
						reviews.value = [...reviews.value, ...list]
					}
					if (m) meta.value = m
					return list
				} catch (error) {
					console.error('Error loading more reviews:', error)
					return []
				} finally {
					loadingMore.value = false
				}
			}

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

			const { filteredReviews } = useReviewFilters(reviews, currentFilter)

		const setFilter = (filter) => {
			currentFilter.value = filter
			push.info(`Filtering reviews by: ${filter}`)
		}

		// Modal handlers
		const openEditModal = (item) => {
			selectedReviewForAction.value = item
			showEditModal.value = true
			push.info('Opening review editor')
		}

		const closeEditModal = () => {
			showEditModal.value = false
			selectedReviewForAction.value = null
		}

		const openDeleteModal = (item) => {
			selectedReviewForAction.value = item
			showDeleteModal.value = true
			push.info('Opening delete confirmation')
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
			push.info('Opening review replies')
		}

		const closeRepliesModal = () => {
			showRepliesModal.value = false
			selectedReview.value = null
		}

		// Event handlers - Simple approach: always refresh from server
		const handleReviewUpdated = (reviewId, updatedData) => {
			fetchReviews()
			push.success('Review updated successfully')
		}

		const handleItemDeleted = (item, message) => {
			fetchReviews()
			push.success(message || 'Review deleted successfully')
		}

		const handleReplySubmitted = (replyData) => {
			fetchReviews()
			push.success('Reply submitted successfully')
		}

		const handleReplyUpdated = (replyId, updatedData) => {
			fetchReviews()
			push.success('Reply updated successfully')
		}

		const handleReplyDeleted = (replyId) => {
			fetchReviews()
			push.success('Reply deleted successfully')
		}

		const handleError = (message) => {
			console.error(message)
			push.error(message || 'An error occurred')
		}

		const handleSuccess = (message) => {
			console.log(message)
			push.success(message || 'Action completed successfully')
		}

		onMounted(() => {
			fetchReviews().then(() => nextTick(() => setupInfiniteScroll()))
		})

		// Re-attach observer when list updates
		watch([loading, filteredReviews], () => nextTick(() => setupInfiniteScroll()))

		watch(() => businessId.value, (newVal, oldVal) => {
			if (newVal !== oldVal && newVal) {
				fetchReviews()
			}
		})

		watch(() => currentFilter.value, (newVal, oldVal) => {
			if (newVal !== oldVal) {
				fetchReviews()
			}
		})

		return {
			currentFilter,
			reviews,
			rating,
			loading,
			loadingMore,
			totalReviews,
			canLoadMore,
			loadMoreRef,
			filteredReviews,
			currentUser,
			businessId,
			businessName,
			showEditModal,
			showDeleteModal,
			showRepliesModal,
			selectedReviewForAction,
			selectedReview,
			setFilter,
			openEditModal,
			closeEditModal,
			openDeleteModal,
			closeDeleteModal,
			openRepliesModal,
			closeRepliesModal,
			handleReviewUpdated,
			handleItemDeleted,
			handleReplySubmitted,
			handleReplyUpdated,
			handleReplyDeleted,
			handleError,
			handleSuccess
		}
	}
}
</script>