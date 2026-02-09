<template>
  <nav class="bg-gray-50 px-2 py-3 sm:px-6 lg:px-8">
    <ol class="flex flex-wrap items-center md:space-x-2 text-xs md:text-sm">
      <li>
        <router-link to="/" class="text-gray-500 hover:text-primary transition-colors duration-200">
          Home
        </router-link>
      </li>
      <li class="flex items-center">
        <ChevronRightIcon class="h-4 w-4 text-gray-400 mx-1" />
      </li>
      <li>
        <router-link to="/categories" class="text-gray-500 hover:text-primary transition-colors duration-200">
          Categories
        </router-link>
      </li>
      <!-- Extra chevron after Categories if there are no specific breadcrumbs, OR chevron separator when there are breadcrumbs -->
      
      <li v-for="(crumb, index) in breadcrumbs" :key="index" class="flex items-center">
        
        <ChevronRightIcon class="h-4 w-4 text-gray-400 mx-1" />
        <router-link 
          :to="crumb.route" 
          class="text-gray-900 hover:text-primary transition-colors duration-200 capitalize"
          :class="{ 'font-medium text-primary': index === breadcrumbs.length - 1 }"
        >
          {{ crumb.label }}
        </router-link>
      </li>
    </ol>
  </nav>
</template>

<script>
import { ChevronRightIcon } from '@heroicons/vue/24/outline'

export default {
  name: 'Breadcrumb',
  components: {
    ChevronRightIcon
  },
  props: {
    categoryPath: {
      type: [String, Array],
      default: () => []
    }
  },
  computed: {
    breadcrumbs() {
      if (!this.categoryPath) return [];
      
      let pathParts = [];
      if (Array.isArray(this.categoryPath)) {
        pathParts = [...this.categoryPath];
      } else {
        pathParts = this.categoryPath.split('/');
      }
      
      const crumbs = [];
      
      for (let i = 0; i < pathParts.length; i++) {
        const currentPath = pathParts.slice(0, i + 1).join('/');
        const label = pathParts[i];
        
        crumbs.push({
          label: label,
          route: `/category/${currentPath}`
        });
      }
      
      return crumbs;
    }
  }
}
</script>