import { ref, computed } from 'vue'
import axios from 'axios'

export function useUserReviews() {
  const reviews = ref([])
  const loading = ref(false)
  const loadingMore = ref(false)
  const meta = ref({ current_page: 1, last_page: 1, per_page: 10, total: 0 })

  const fetchUserReviews = async () => {
    try {
      loading.value = true
      const response = await axios.get('/user/business/review', { 
        params: { 
          page: 1
        } 
      })
      if (response.data.status) {
        const list = response.data.data?.data || []
        const m = response.data.data?.meta || { current_page: 1, last_page: 1, per_page: 10, total: Array.isArray(list) ? list.length : 0 }
        reviews.value = Array.isArray(list) ? list : []
        meta.value = m
      }
    } catch (error) {
      console.error('Error fetching user reviews:', error)
      reviews.value = []
      meta.value = { current_page: 1, last_page: 1, per_page: 10, total: 0 }
    } finally {
      loading.value = false
    }
  }

  const loadMoreReviews = async () => {
    try {
      if (!canLoadMore.value || loadingMore.value) return []
      loadingMore.value = true
      const nextPage = (meta.value.current_page || 1) + 1
      const response = await axios.get('/user/reviews', { 
        params: { 
          page: nextPage
        } 
      })
      const list = response.data.data?.data || []
      const m = response.data.data?.meta
      
      if (Array.isArray(list) && list.length) {
        reviews.value = [...reviews.value, ...list]
      }
      if (m) meta.value = m
      return list
    } catch (error) {
      console.error('Error loading more user reviews:', error)
      return []
    } finally {
      loadingMore.value = false
    }
  }

  const canLoadMore = computed(() => (meta.value.current_page || 1) < (meta.value.last_page || 1))

  return {
    reviews,
    loading,
    loadingMore,
    meta,
    fetchUserReviews,
    loadMoreReviews,
    canLoadMore
  }
}