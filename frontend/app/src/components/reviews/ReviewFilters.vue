<template>
  <div class="flex space-x-2 flex-wrap border-b border-gray-300 pb-2">
    <p class="w-full pl-2 md:pl-3 mb-1 md:mb-2 font-medium">Filter Reviews</p>
    <button 
      v-for="filter in filters" 
      :key="filter.value"
      @click="$emit('filter-changed', filter.value)" 
      :class="[
        'px-3 py-1 mt-1 rounded text-xs font-medium transition-colors',
        currentFilter === filter.value 
          ? 'bg-primary/15 text-primary' 
          : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
      ]"
    >
      {{ filter.label }}
    </button>
  </div>
</template>

<script>
export default {
  name: 'ReviewFilters',
  props: {
    currentFilter: {
      type: String,
      default: 'latest'
    }
  },
  data() {
    return {
      filters: [
        { value: 'latest', label: 'Latest' },
        { value: 'low-to-high', label: 'Low to High' },
        { value: 'high-to-low', label: 'High to Low' },
        { value: 'with-image', label: 'Ratings with Images' }
      ]
    }
  }
}
</script> 