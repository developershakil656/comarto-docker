import { ref, computed } from 'vue'
import axios from 'axios'

export function useReviews(businessId, currentFilter = ref('latest')) {
  const reviews = ref([])
  const rating = ref({})
  const loading = ref(false)
  const loadingMore = ref(false)
  const meta = ref({ current_page: 1, last_page: 1, total: 0 })

  const fetchReviews = async () => {
    try {
      loading.value = true
      const response = await axios.get(`/business/reviews/${businessId}`, { 
        params: { 
          page: 1,
          sort: currentFilter.value || 'latest'
        } 
      })
      if (response.data.status) {
        // Support both old and new shapes
        const list = response.data.data?.data || response.data.data?.reviews?.data || response.data.data?.reviews || []
        const m = response.data.data?.meta || response.data.data?.reviews?.meta || { current_page: 1, last_page: 1, total: Array.isArray(list) ? list.length : 0 }
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

  const loadMoreReviews = async () => {
    try {
      if (!canLoadMore.value || loadingMore.value) return []
      loadingMore.value = true
      const nextPage = (meta.value.current_page || 1) + 1
      const response = await axios.get(`/business/reviews/${businessId}`, { 
        params: { 
          page: nextPage,
          sort: currentFilter.value || 'latest'
        } 
      })
      const list = response.data.data?.data || response.data.data?.reviews?.data || response.data.data?.reviews || []
      const m = response.data.data?.meta || response.data.data?.reviews?.meta
      if (Array.isArray(list) && list.length) {
        // For pagination, append to the end to maintain the order as returned by API
        // The API should return reviews in the correct order for the current filter
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

  const filteredReviews = computed(() => {
    if (!reviews.value || !reviews.value.length) return []
    
    let filtered = [...reviews.value]
    if (!Array.isArray(filtered)) return []
    
    return filtered
  })

  return {
    reviews,
    rating,
    loading,
    loadingMore,
    meta,
    fetchReviews,
    loadMoreReviews,
    canLoadMore,
    filteredReviews
  }
} 