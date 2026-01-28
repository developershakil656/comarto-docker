import { computed } from 'vue'

export function useReviewFilters(reviews, currentFilter) {
  const filteredReviews = computed(() => {
    if (!reviews.value || !reviews.value.length) return []
    
    let filtered = [...reviews.value]
    if (!Array.isArray(filtered)) return []
    
    switch (currentFilter.value) {
      case 'latest':
        // For pagination, trust API to return reviews in correct order
        // Don't re-sort to maintain pagination order
        return [...filtered]
        
      case 'low-to-high':
        return [...filtered].sort((a, b) => {
          if (!a.rate || !b.rate) return 0
          return a.rate - b.rate
        })
        
      case 'high-to-low':
        return [...filtered].sort((a, b) => {
          if (!a.rate || !b.rate) return 0
          return b.rate - a.rate
        })
        
      case 'with-image':
        return filtered.filter(review =>
          review && review.images && review.images.length > 0
        )
        
      default:
        return filtered
    }
  })

  return {
    filteredReviews
  }
} 