<template>
  <div class="min-h-screen bg-gray-50 pb-16 sm:pb-0">
    <!-- Main Header -->
    <BottomHeader />

    <Breadcrumb :category-path="categoryPath" />

    <!-- Main Content -->
    <div
      class="max-w-8xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8 py-4 capitalize"
    >
      <div
        class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6"
      >
        <div v-if="subcategories.length > 0">
          <!-- Category Header -->
          <div
            class="flex flex-wrap mb-4 sm:mb-6 justify-between items-start sm:items-center"
          >
            <div class="flex items-center">
              <button
                @click="goBack"
                class="flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 rounded-lg hover:bg-gray-100 transition-colors duration-200 mr-3 sm:mr-4 flex-shrink-0"
              >
                <ArrowLeftIcon class="w-5 h-5 sm:w-6 sm:h-6 text-gray-600" />
              </button>
              <h1
                class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 capitalize flex-1 min-w-0"
              >
                {{ categoryData.name || categorySlug }}
              </h1>
            </div>
            <div class="sm:text-right pl-4">
              <router-link
                :to="{
                  name: 'search',
                  params: {
                    location: selectedLocationSlug,
                    keyword: '',
                  },
                  query: { category_slugs: categoryData.slug },
                }"
                class="text-xs sm:text-sm font-medium text-gray-900 hover:text-primary transition-colors duration-200"
                >See All
                <span class="text-primary">{{ categoryData.name }}</span>
                Products</router-link
              >
            </div>
          </div>

          <div
            class="text-base sm:text-lg font-bold text-gray-900 mb-4 sm:mb-6"
          >
            A TO Z :
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading">
          <SkeletonLoader type="category-items" :count="12" />
        </div>

        <!-- Subcategories Grid -->
        <div
          v-if="subcategories.length > 0"
          class="grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-3 sm:gap-4"
        >
          <router-link
            v-for="subcategory in subcategories"
            :key="subcategory.id"
            :to="`/category/${this.pathString}/${subcategory.slug}`"
            class="flex items-center p-3 sm:p-4 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors duration-200 border border-gray-100 group"
          >
            <div
              class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg bg-gray-100 flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0 group-hover:bg-gray-200 transition-colors duration-200"
            >
              <OptimizedImage
                v-if="subcategory.icon"
                :src="subcategory.icon"
                :alt="subcategory.name"
                class="w-6 h-6 sm:w-8 sm:h-8 object-contain"
              />
              <div
                v-else
                class="h-6 w-6 sm:h-8 sm:w-8 bg-gray-200 rounded"
              ></div>
            </div>
            <div
              class="text-xs sm:text-sm font-medium text-gray-900 group-hover:text-primary transition-colors duration-200 line-clamp-2"
            >
              {{ subcategory.name }}
            </div>
          </router-link>
        </div>

        <div v-else>
          <CategoryProduct
            :category-slug="categoryData.slug"
            :category-name="categoryData.name"
          />
        </div>
      </div>
      <!-- No Categories State - Show Products -->
    </div>

    <!-- category description -->
    <div
      v-if="categoryData.description"
      class="max-w-7xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8 py-4 sm:py-6 lg:py-8"
    >
      <div
        class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6"
      >
        {{ categoryData.description }}
      </div>
    </div>
    <!-- Mobile Bottom Navigation -->
    <MobileBottomNavigation />
  </div>
</template>

<script>
import axios from "axios";
import { useSEO } from "@/composables/useSEO";
import { ArrowLeftIcon } from "@heroicons/vue/24/outline";
import BottomHeader from "@/components/header/BottomHeader.vue";
import SkeletonLoader from "@/components/SkeletonLoader.vue";
import MobileBottomNavigation from "@/components/common/MobileBottomNavigation.vue";
import OptimizedImage from "@/components/common/OptimizedImage.vue";
import CategoryProduct from "@/components/CategoryProduct.vue";
import Breadcrumb from "@/components/common/Breadcrumb.vue";

export default {
  name: "CategoryDetailView",
  components: {
    ArrowLeftIcon,
    BottomHeader,
    SkeletonLoader,
    MobileBottomNavigation,
    OptimizedImage,
    CategoryProduct,
    Breadcrumb,
  },
  data() {
    return {
      categoryData: {},
      subcategories: [],
      loading: false,
    };
  },
  computed: {
    //   categoryPath() {
    //   const path = this.$route.params.categoryPath;
    //   if (Array.isArray(path)) {
    //     return path[path.length - 1]; // If router returns an array
    //   }
    //   return path.split('/').pop(); // Get 't-shirts' from 'fashion/t-shirts'
    // },
    categoryPath() {
      return this.$route.params.categoryPath;
    },

    pathString() {
      return Array.isArray(this.categoryPath)
        ? this.categoryPath.join("/")
        : this.categoryPath;
    },
    selectedLocationSlug() {
      const selectedLocation = this.$store.getters.selectedLocation;
      if (selectedLocation) {
        // Convert "Nawabganj, Dhaka" to "Nawabganj-Dhaka"
        return selectedLocation
          .replace(/,\s*/g, "-")
          .replace(/\s+/g, "-")
          .toLowerCase();
      }
      return "all-bangladesh";
    },
  },
  async mounted() {
    await this.fetchCategoryData();
  },
  methods: {
    async fetchCategoryData() {
      try {
        this.loading = true;
        const response = await axios.get(
          `/category/children/${this.pathString}`
        );

        if (response.data.status) {
          this.categoryData = response.data.data;
          this.subcategories = response.data.data.children || [];

          // Track the category name for suggested categories
          if (this.categoryData.name) {
            this.$store.dispatch(
              "trackProductCategory",
              this.categoryData.name
            );
          }

          // Set SEO meta tags after category data is loaded
          this.updateMetaTags();
        }
      } catch (error) {
        // Get the last part of categoryPath for search redirection
        let lastCategoryPart = '';
        if (Array.isArray(this.categoryPath)) {
          lastCategoryPart = this.categoryPath[this.categoryPath.length - 1];
        } else if (typeof this.categoryPath === 'string') {
          // Handle both comma-separated and slash-separated paths
          const separators = /[,\/]/;
          const pathParts = this.categoryPath.split(separators).filter(part => part.trim() !== '');
          lastCategoryPart = pathParts[pathParts.length - 1];
        }
        
        // Redirect to search page with the category slug
        if (lastCategoryPart) {
          this.$router.push({
            name: 'search',
            params: {
              location: this.selectedLocationSlug,
              keyword: lastCategoryPart
            },
            query: {
              category_slugs: this.pathString
            }
          });
        }
      } finally {
        this.loading = false;
      }
    },

    updateMetaTags() {
      if (this.categoryData.name) {
        const { setMetaTags } = useSEO();
        setMetaTags(
          `Find ${
            this.categoryData.name
          } products and suppliers in ${this.selectedLocationSlug.replace(
            /-/g,
            " "
          )} - Comarto B2B Marketplace`,
          `Browse ${
            this.categoryData.name
          } products and suppliers, manufacturers, and businesses in ${this.selectedLocationSlug.replace(
            /-/g,
            " "
          )}. Connect with verified sellers for your business needs on Comarto.`,
          this.categoryData?.icon || null,
          `${
            this.categoryData.name
          }, suppliers, manufacturers, ${this.selectedLocationSlug.replace(
            /-/g,
            " "
          )}, Comarto, B2B, wholesale, trade`
        );
      }
    },

    goBack() {
      this.$router.go(-1);
    },
  },
  watch: {
    categoryPath: {
      handler(newVal, oldVal) {
        // Only fetch data if the actual category path has changed
        const newPathString = Array.isArray(newVal) ? newVal.join('/') : newVal;
        const oldPathString = Array.isArray(oldVal) ? oldVal.join('/') : oldVal;
        
        if (newPathString !== oldPathString) {
          this.fetchCategoryData();
        }
      },
      immediate: false
    },
    // Watch for route changes that might affect the category path
    // $route(to, from) {
    //   // Only trigger if the category path portion of the route changed
    //   const toPath = to.params.categoryPath;
    //   const fromPath = from.params.categoryPath;
      
    //   const toPathString = Array.isArray(toPath) ? toPath.join('/') : toPath;
    //   const fromPathString = Array.isArray(fromPath) ? fromPath.join('/') : fromPath;
      
    //   if (toPathString !== fromPathString) {
    //     this.fetchCategoryData();
    //   }
    // }
  },
};
</script>
