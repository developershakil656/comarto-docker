<template>
  <div class="min-h-screen container mx-auto">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row gap-6 my-4">
      <SearchSidebar
        :selectedBusinessTypes="selectedBusinessTypes"
        :isOpen="isSidebarOpen"
        :suppliers="suppliers"
        @filter-change="onFilterChange"
        @close="isSidebarOpen = false"
      />
      <CategoryProductItems 
        @toggle-sidebar="isSidebarOpen = !isSidebarOpen" 
        :categorySlug="categorySlug"
        :categoryName="categoryName"
      />
    </div>
  </div>
</template>

<script>
import SearchSidebar from '@/components/search/SearchSidebar.vue'
import CategoryProductItems from '@/components/category/CategoryProductItems.vue'

export default {
  name: 'CategoryProduct',
  components: {
    SearchSidebar,
    CategoryProductItems
  },
  props: {
    categorySlug: {
      type: String,
      required: true
    },
    categoryName: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      isSidebarOpen: false,
      resizeTimeout: null
    };
  },
  computed: {
    selectedBusinessTypes() {
      const q = this.$route.query.business_type;
      return q ? q.split(',').filter(Boolean) : [];
    },
    suppliers() {
      return this.$route.query.suppliers === 'true';
    }
  },
  watch: {
    $route: {
      handler(newRoute, oldRoute) {
        // Check if both routes exist before comparing parameters
        if (!newRoute || !oldRoute) return;
        
        // Only trigger if route parameters actually changed
        if ((newRoute.query && oldRoute.query && 
             newRoute.query.business_type !== oldRoute.query.business_type) ||
            (newRoute.query && oldRoute.query && 
             newRoute.query.suppliers !== oldRoute.query.suppliers)) {
          
          const businessTypes = (newRoute.query && newRoute.query.business_type)
              ? newRoute.query.business_type.split(',').filter(Boolean)
              : [];
          const suppliers = (newRoute.query && newRoute.query.suppliers) === 'true';
          
          // Perform search with updated parameters
          this.doSearch(businessTypes, suppliers);
        }
      },
      immediate: true
    },
    // Watch for location changes
    '$store.getters.selectedLocation': {
      handler(newLocation, oldLocation) {
        // Only trigger if location actually changed
        if (newLocation !== oldLocation) {
          const businessTypes = this.$route.query.business_type
              ? this.$route.query.business_type.split(',').filter(Boolean)
              : [];
          const suppliers = this.$route.query.suppliers === 'true';
          
          // Perform search with updated location
          this.doSearch(businessTypes, suppliers);
        }
      }
    }
  },
  methods: {
    checkScreenSize() {
      // md breakpoint is 768px in Tailwind CSS
      this.isLargeScreen = window.innerWidth >= 768;
      // Set sidebar open state based on screen size on initial load only
      if (this._isInitialLoad) {
        this.isSidebarOpen = this.isLargeScreen;
        this._isInitialLoad = false;
      }
    },
    onFilterChange(filter) {
      const businessTypes = filter.businessTypes || [];
      const query = {
        ...this.$route.query,
        business_type: businessTypes.join(','),
        suppliers: filter.suppliers ? 'true' : 'false'
      };
      this.$router.replace({
        name: this.$route.name,
        params: this.$route.params,
        query
      });
    },
    async doSearch(businessTypes, suppliers) {
      // Clear any existing timeout to prevent duplicate calls
      if (this.searchTimeout) {
        clearTimeout(this.searchTimeout);
      }
      
      // Debounce the search call
      this.searchTimeout = setTimeout(() => {
        const params = { 
          category_slug: this.categorySlug
        };
        
        if (businessTypes && businessTypes.length) {
          params.business_types = businessTypes.join(',');
        }
        if (suppliers) {
          params.suppliers = 'true';
        }
        
        this.$store.dispatch('search', params);
      }, 300);
    },
    
    setSidebarState() {
      // Throttled version to prevent scroll jank
      if (this.resizeTimeout) {
        clearTimeout(this.resizeTimeout);
      }
      
      this.resizeTimeout = setTimeout(() => {
        // Get screen width
        const screenWidth = window.innerWidth;
        
        // Tailwind's md breakpoint is 768px
        // Set sidebar open for medium screens and above
        this.isSidebarOpen = screenWidth >= 768;
      }, 150);
    }
  },
  async mounted() {
    this._isInitialLoad = true;
    
    const businessTypes = this.$route.query.business_type
        ? this.$route.query.business_type.split(',').filter(Boolean)
        : [];
    const suppliers = this.$route.query.suppliers === 'true';

    // Set sidebar open state based on screen size
    this.setSidebarState();
    
    // Add resize listener to handle screen size changes
    window.addEventListener('resize', this.setSidebarState);
    
    // Perform initial search
    this.doSearch(businessTypes, suppliers);
  },
  beforeUnmount() {
    // Clean up timeout
    if (this.searchTimeout) {
      clearTimeout(this.searchTimeout);
    }
    
    // Clean up resize timeout
    if (this.resizeTimeout) {
      clearTimeout(this.resizeTimeout);
    }
    
    // Remove resize listener
    window.removeEventListener('resize', this.setSidebarState);
  }
};
</script>