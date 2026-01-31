<template>
    <div>
        <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">Your Reviews and Ratings</h2>

        <!-- Loading State -->
        <div v-if="loading" class="space-y-4 sm:space-y-6 mt-2">
            <div v-for="i in 3" :key="i"
                class="group flex flex-col sm:flex-row bg-white rounded-xl border p-3 sm:p-4 gap-3 sm:gap-4 items-start transition-all hover:shadow-lg">
                <div class="w-full sm:w-40 md:w-52 h-40 sm:h-40 md:h-52 rounded-lg border bg-gray-200 animate-pulse"></div>
                <div class="flex-1 flex flex-col min-h-32 sm:min-h-48 space-y-2 sm:space-y-4 w-full">
                    <div class="h-5 sm:h-6 bg-gray-200 rounded animate-pulse"></div>
                    <div class="h-3 sm:h-4 bg-gray-200 rounded animate-pulse w-1/3"></div>
                    <div class="h-3 sm:h-4 bg-gray-200 rounded animate-pulse"></div>
                    <div class="flex gap-1 sm:gap-2">
                        <div v-for="j in 3" :key="j" class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 rounded-lg border bg-gray-200 animate-pulse">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reviews List -->
        <div v-else-if="reviews.length > 0" class="space-y-6 mt-2">
                         <router-link 
                 v-for="review in reviews" 
                 :key="review.id"
                 :to="`/${review.business.slug}?tab=reviews&reviewId=${review.id}`"
                 class="group flex flex-col md:flex-row bg-white rounded-xl border p-4 gap-4 items-start transition-all hover:shadow-lg cursor-pointer"
             >
                 <!-- Review Images -->
                 <div class="w-52 h-52 rounded-lg border content-center bg-green-50 overflow-hidden flex-shrink-0">
                     <OptimizedImage 
                         :src="review.images && review.images.length > 0 ? review.images[0].url : (review.business.business_profile || 'https://placehold.co/208x208')" 
                         :alt="`Review image for ${review.business.business_name}`"
                         class="w-full h-full object-cover" 
                     />
                 </div>

                <div class="flex-1 flex flex-col min-h-48">
                    <!-- User Info and Rating -->
                    <div class="items-start mb-2">
                        <div class="flex justify-between">
                            <h2 class="text-xl font-semibold line-clamp-2 capitalize">{{ review.business.business_name }}</h2>
                                <span style="line-height: normal" :class="[
                                'px-2 py-1 rounded-full text-xs font-medium',
                                review.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'
                            ]">
                                {{ review.status }}
                            </span>
                        </div>
                        <div class="text-sm text-gray-700 mb-3">{{ review.date }}</div>
                        <div class="my-2">
                            <StarRating :rating="review.rate" iconSize="h-7" />
                        </div>

                        <div>
                            <p class="ml-1 line-clamp-4">"{{ review.message }}"</p>
                        </div>
                        <!-- Review Message -->


                        <!-- Additional Images -->
                        <div v-if="review.images && review.images.length > 1" class="flex gap-2 mt-3">
                            <div 
                                v-for="(image, index) in review.images.slice(1, 4)" 
                                :key="image.id"
                                class="w-24 h-24 rounded-lg border content-center bg-green-50 overflow-hidden flex-shrink-0"
                            >
                                <OptimizedImage 
                                    :src="image.url" 
                                    :alt="`Review image ${index + 2}`"
                                    class="w-full h-full object-cover" 
                                />
                            </div>
                        </div>

                                                 <!-- Review Status -->
                         <div class="mt-auto pt-2">
                             
                         </div>
                     </div>
                 </div>
             </router-link>

            <!-- Load More Sentinel -->
            <div ref="loadMoreRef" class="h-6"></div>
            
            <!-- Loading more indicator -->
            <div v-if="loadingMore" class="text-center text-gray-500 py-4">
                Loading more...
            </div>
            
            <div
                v-else-if="!canLoadMore && reviews.length > 0"
                class="text-center text-gray-400 py-4 text-sm"
            >
                No more reviews
            </div>
        </div>
        <!-- Empty State -->
        <div v-else class="space-y-6 mt-2">
            <div class="text-center py-12">
                <div class="text-gray-400 mb-4">
                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No reviews yet</h3>
                <p class="text-gray-500">You haven't written any reviews yet. Start reviewing businesses you've visited!
                </p>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue'
import StarRating from '@/components/StarRating.vue'
import { useUserReviews } from '@/composables/useUserReviews'
import OptimizedImage from '@/components/common/OptimizedImage.vue'

export default {
    name: 'UserReviews',
    components: {
        StarRating,
        OptimizedImage
    },
    setup() {
        const { reviews, loading, loadingMore, meta, fetchUserReviews, loadMoreReviews, canLoadMore } = useUserReviews()
        const observer = ref(null)
        const loadMoreRef = ref(null)

        const setupInfiniteScroll = () => {
            if (observer.value) {
                observer.value.disconnect()
            }
            
            const options = { root: null, rootMargin: '200px', threshold: 0 }
            observer.value = new IntersectionObserver(async (entries) => {
                const entry = entries[0]
                if (
                    entry &&
                    entry.isIntersecting &&
                    canLoadMore.value &&
                    !loadingMore.value &&
                    !loading.value
                ) {
                    await loadMoreReviews()
                }
            }, options)
            
            if (loadMoreRef.value) {
                observer.value.observe(loadMoreRef.value)
            }
        }

        onMounted(async () => {
            try {
                await fetchUserReviews()
                setupInfiniteScroll()
            } catch (error) {
                console.error('Error fetching reviews:', error)
            }
        })

        onUnmounted(() => {
            if (observer.value) {
                observer.value.disconnect()
                observer.value = null
            }
        })

        return {
            reviews,
            loading,
            loadingMore,
            meta,
            canLoadMore,
            loadMoreRef
        }
    }
}
</script>
