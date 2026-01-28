<template>
  <div class="min-h-screen bg-gray-50 pb-16 sm:pb-0">
    <!-- Main Header -->
    <BottomHeader />

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8 py-4 sm:py-6 lg:py-8 capitalize">
      <div class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
        <!-- Page Header -->
            <!-- <button @click="goBack"
              class="hidden md:flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 rounded-lg hover:bg-gray-100 transition-colors duration-200 mr-3 sm:mr-4 flex-shrink-0">
              <ArrowLeftIcon class="w-5 h-5 sm:w-6 sm:h-6 text-gray-600" />
            </button> -->
            <h1 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 capitalize flex-1 min-w-0 mb-4">
              Browse All Categories :
            </h1>

        <!-- <div class="text-base sm:text-lg font-bold text-gray-900 mb-4 sm:mb-6">Browse All Categories :</div> -->

        <!-- Loading State -->
        <div v-if="loading">
          <SkeletonLoader type="category-items" :count="16" />
        </div>

        <!-- Categories Grid -->
        <div v-else-if="categories.length > 0"
          class="grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-2 sm:gap-3">
          <router-link v-for="category in categories" :key="category.id"
            :to="`/category/${category.slug}`"
            class="flex items-center p-1 sm:p-2 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors duration-200 border border-gray-100 group">
            <div
              class="w-16 h-16 sm:w-16 sm:h-16 rounded-lg bg-gray-100 flex items-center justify-center mr-2 sm:mr-3 flex-shrink-0 group-hover:bg-gray-200 transition-colors duration-200">
              <img v-if="category.icon" :src="category.icon" :alt="category.name"
                class="w-12 h-12 sm:w-12 sm:h-12 object-contain" @error="handleImageError(category.name)" />
              <div v-else class="h-12 w-12 sm:h-16 sm:w-16 bg-gray-200 rounded"></div>
            </div>
            <div
              class="line-clamp-2 text-xs sm:text-sm font-medium text-gray-900 overflow-hidden group-hover:text-primary transition-colors duration-200">
              {{ category.name }}
            </div>
          </router-link>
        </div>

        <!-- No Categories State -->
        <div v-else class="text-center py-8 sm:py-12">
          <div class="text-gray-500 text-base sm:text-lg">No categories found.</div>
        </div>
      </div>
    </div>

    <!-- Mobile Bottom Navigation -->
    <MobileBottomNavigation />
  </div>
</template>

<script>
import axios from 'axios'
import { useSEO } from '@/composables/useSEO';
import { ArrowLeftIcon } from '@heroicons/vue/24/outline'
import BottomHeader from '@/components/header/BottomHeader.vue'
import SkeletonLoader from '@/components/SkeletonLoader.vue'
import MobileBottomNavigation from '@/components/common/MobileBottomNavigation.vue'

export default {
  name: 'AllCategoriesView',
  components: {
    ArrowLeftIcon,
    BottomHeader,
    SkeletonLoader,
    MobileBottomNavigation
  },
  data() {
    return {
      categories: [],
      loading: false
    }
  },
  async mounted() {
    // Set SEO meta tags for Bangladesh B2B directory categories page
    const { setMetaTags } = useSEO();
    setMetaTags(
      'Business Categories in Bangladesh - Comarto B2B Marketplace & Wholesale Directory',
      'Browse all business categories in Bangladesh. Find wholesale suppliers, manufacturers, and service providers by industry on Comarto.',
      null,
      'Comarto, business categories, wholesale, suppliers, manufacturers, Bangladesh, B2B, trade, commerce, industry, marketplace'
    );
    
    await this.fetchCategories()
  },
  methods: {
    async fetchCategories() {
      try {
        this.loading = true
        const response = await axios.get('/categories')
        
        if (response.data.status) {
          this.categories = response.data.data
        }
      } catch (error) {
        console.error('Error fetching categories:', error)
      } finally {
        this.loading = false
      }
    },
    
    goBack() {
      this.$router.go(-1)
    },

    handleImageError(name) {
      // Fallback to a default icon if the image fails to load
      event.target.src = `https://placehold.co/40x40/CCCCCC/FFFFFF?text=${name.toUpperCase().charAt(0)}`
    }
  }
}
</script>
