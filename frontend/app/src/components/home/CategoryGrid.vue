<template>
  <div class="py-8 px-4 bg-white">
    <div class="max-w-7xl mx-auto">
      <!-- Header with View All Button -->
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-sm md:text-lg font-semibold text-gray-900">Categories</h2>
        <router-link 
          to="/categories" 
          class="text-blue-600 text-xs md:text-sm font-medium hover:text-blue-700 transition-colors duration-200"
        >
          View All Categories
        </router-link>
      </div>

      <!-- Skeleton loader when loading -->
      <SkeletonLoader v-if="loading" type="category-grid" :count="16" />
      
      <!-- Actual content when loaded -->
      <div v-else class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-6">
        <router-link 
          v-for="category in categories" 
          :key="category.id"
          :to="`/category/${category.slug}`"
          class="flex flex-col items-center group cursor-pointer transition-transform duration-200 hover:-translate-y-1"
        >
          <div class="w-20 h-20 bg-white rounded-xl shadow-md flex items-center justify-center mb-3 group-hover:shadow-lg transition-shadow duration-200">
            <img 
              :src="category.icon" 
              :alt="category.name"
              class="w-16 h-16 object-contain"
              @error="handleImageError(category.name)"
            />
          </div>
          <div class="text-xs md:text-sm font-medium text-gray-900 text-center leading-tight max-w-24">
            {{ category.name }}
          </div>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import SkeletonLoader from '../SkeletonLoader.vue'

export default {
  name: 'CategoryGrid',
  components: {
    SkeletonLoader
  },
  data() {
    return {
      categories: [],
      loading: false
    }
  },
  async mounted() {
    await this.fetchCategories()
  },
  methods: {
    async fetchCategories() {
      try {
        this.loading = true
        const response = await axios.get('/categories?limit=16')
        
        if (response.data.status) {
          this.categories = response.data.data
        }
      } catch (error) {
        console.error('Error fetching categories:', error)
        
      } finally {
        this.loading = false
      }
    },
    handleImageError(name) {
      // Fallback to a default icon if the image fails to load
      event.target.src = `https://placehold.co/40x40/CCCCCC/FFFFFF?text=${name.toUpperCase().charAt(0)}`
    },
  }
}
</script>

