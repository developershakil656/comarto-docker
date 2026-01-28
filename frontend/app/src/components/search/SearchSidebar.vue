<template>
<div>
  <!-- Overlay for mobile -->
  <div v-if="isVisible" @click="closeSidebar" class="fixed inset-0 bg-black bg-opacity-50 z-[60] md:hidden"></div>

  <!-- Sidebar -->
  <aside 
    v-if="isVisible"
    :class="[
      'transition-transform duration-300 ease-in-out',
      'fixed top-0 left-0 h-full bg-white z-[60] overflow-y-auto p-4 w-80 flex flex-col gap-4',
      'md:relative md:flex md:w-80 md:ml-0 md:mr-0 md:top-0 md:h-auto md:z-auto md:bg-transparent md:p-0 md:overflow-y-visible',
      'md:transform-none',
      { 'animate-slide-in-left': isOpen && !isClosing, 'animate-slide-out-left': isClosing }
    ]"
    @click.stop
  >
    <div v-if="isOpen" class="flex justify-between font-semibold text-gray-500 md:hidden items-center">
      <h2>Filter Search</h2>
      <button @click="closeSidebar" class="top-1 right-2 p-2">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
    </button>
    </div>

    <!-- products or suppliers filter -->
    <div class="border p-1 rounded-md">
      <div class="flex flex-wrap gap-2 justify-center font-semibold text-sm md:text-base">
        <button class="px-2 md:px-5 py-1 rounded-md transition duration-200 ease-in-out"
          :class="suppliersLocal ? 'hover:bg-gray-200 text-black' : 'bg-black text-white'" @click="setSuppliers(false)">
          <CubeIcon class="h-5 w-5 inline-block" />
          Products
        </button>
        <button class="px-2 md:5 py-1 rounded-md transition duration-200 ease-in-out"
          :class="suppliersLocal ? 'bg-black text-white' : 'hover:bg-gray-200 text-black'" @click="setSuppliers(true)">
          <TruckIcon class="h-5 w-5 inline-block" />
          Suppliers
        </button>
      </div>
    </div>

    <!-- Business Type Filter -->
    <div class="border p-4 rounded-md">
      <div class="flex items-center justify-between cursor-pointer select-none"
        @click="businessTypeOpen = !businessTypeOpen">
        <span class="font-semibold md:text-lg">Business Type</span>
        <span class="transition-transform duration-300" :class="{ 'rotate-180': businessTypeOpen }">
          <ChevronUpIcon class="h-5" />
        </span>
      </div>

      <div v-if="businessTypeOpen" class="mt-2 flex flex-col gap-2 overflow-hidden transition-all">
        <div v-if="loadingBusinessTypes" class="text-gray-500 text-sm">Loading business types...</div>
        <div v-else-if="businessTypesError" class="text-red-500 text-sm">{{ businessTypesError }}</div>
        <template v-else>
          <label v-for="businessType in businessTypes" :key="businessType"
            class="flex items-center gap-2 cursor-pointer text-base font-normal">
            <input type="checkbox" :value="businessType" v-model="selectedBusinessTypesLocal"
              class="accent-primary h-4 w-4 rounded border-gray-300" />
            <span>{{ businessType }}</span>
          </label>
        </template>
      </div>

    </div>

    <!-- Categories Filter -->
    <div class="border p-4 rounded-md">
      <div class="flex items-center justify-between cursor-pointer select-none"
        @click="categoriesOpen = !categoriesOpen">
        <span class="font-semibold md:text-lg">Categories</span>
        <span class="transition-transform duration-300" :class="{ 'rotate-180': categoriesOpen }">
          <ChevronUpIcon class="h-5" />
        </span>
      </div>

      <div v-if="categoriesOpen" class="mt-2 space-y-4 transition-all">
        <!-- Category Search Input -->
        <div>
          <!-- <label class="block text-sm font-semibold text-gray-700 mb-2">Search Categories</label> -->
          <div class="relative">
            <input v-model="categoryDropdown.search" @focus="handleCategoryFocus" @input="debouncedCategorySearch"
              type="text"
              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300 hover:border-primary-200"
              placeholder="Type to search categories..." />
            <div v-if="categoryDropdown.isOpen"
              class="absolute z-10 w-full mt-1 bg-white border-2 border-primary-200 rounded-xl shadow-xl ">
              <div class="max-h-60 overflow-y-auto">
                <div v-if="categoriesLoading" class="px-4 py-3 text-gray-500 text-center">
                  Loading categories...
                </div>
                <div v-if="availableCategories.length === 0 && tempSelectedCategories.length === 0"
                  class="px-4 py-3 text-gray-500 text-center">
                  <div v-if="selectedCategoriesLocal.length > 0">All available categories are already selected</div>
                  <div v-else>No categories found</div>
                </div>
                <!-- Temporary Selected Categories -->
                <div v-if="tempSelectedCategories.length > 0" class="border-b border-gray-200 p-3">
                  <div class="text-xs text-gray-600 mb-2">Selected for application:</div>
                  <div class="flex flex-wrap gap-1">
                     <div v-for="category in tempSelectedCategories" :key="category.slug"
                       class="flex items-center space-x-1 px-2 py-1 bg-primary-100 text-primary-800 rounded text-xs">
                       <span>{{ category.name }}</span>
                       <button @click.stop="removeTempCategory(category.slug)"
                         class="text-primary-600 hover:text-primary-800 ml-1">
                         ✕
                       </button>
                     </div>
                  </div>
                </div>

                 <div v-for="category in availableCategories" :key="category.slug"
                   @click.stop="isCategorySelected(category.slug) ? null : selectCategory(category)"
                   class="px-4 py-3 transition-colors duration-200 flex items-center justify-between" :class="{
                     'bg-gray-100 cursor-not-allowed opacity-60': isCategorySelected(category.slug),
                     'hover:bg-primary-50 cursor-pointer': !isCategorySelected(category.slug)
                   }">
                   <span>{{ category.name }}</span>
                   <span v-if="isCategorySelected(category.slug)"
                     class="text-xs text-gray-500 bg-gray-200 px-2 py-1 rounded">
                     Selected
                   </span>
                 </div>
              </div>
              <!-- Apply Button - Always Visible -->
              <div v-if="tempSelectedCategories.length > 0" class="m-2">
                <button @click="applyCategoryFilter"
                  class="w-full bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/85 transition-colors duration-200 text-sm font-medium">
                  Apply {{ tempSelectedCategories.length }} Categor{{ tempSelectedCategories.length === 1 ? 'y' : 'ies'
                  }}
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Selected Categories Display -->
        <div v-if="selectedCategoriesLocal.length > 0">
          <div class="flex items-center justify-between mb-2">
            <label class="text-sm font-semibold text-gray-700">Selected Categories</label>
            <button @click="clearAllCategories"
              class="text-xs text-red-600 hover:text-red-800 hover:bg-red-50 px-2 py-1 rounded transition-colors duration-200">
              Clear All
            </button>
          </div>
          <div class="flex flex-wrap gap-2">
             <div v-for="category in selectedCategoriesLocal" :key="category.slug"
               class="flex items-center space-x-2 px-3 py-2 bg-primary-100 text-primary-800 rounded-lg text-sm md:text-base line-clamp-1">
               <span>{{ category.name }}</span>
               <button @click="removeCategory(category.slug)" class="text-primary-600 hover:text-primary-800">
                 ✕
               </button>
             </div>
          </div>
        </div>

        <div v-if="selectedCategoriesLocal.length === 0" class="text-center py-4 text-gray-500 text-sm">
          Showing all categories. Search and select categories for more specific results.
        </div>

      </div>
    </div>

    <!-- Register Business Promo Box -->
    <div class="w-full flex justify-center">
      <img src="https://placehold.co/325x325" alt="Register Business" class="rounded-md shadow" />
    </div>

    <!-- Quote Box -->
    <div class="bg-primary/10 rounded-xl border border-gray-200 shadow p-4 sticky top-28">
      <div class="text-center mb-4">
        <div class="text-3xl mb-2">💼</div>
        <div class="text-sm">Looking for <span class="text-primary font-semibold text-base">Rice ?</span></div>
        <div class="text-xs mt-2 text-gray-600">Get competitive quotes from verified suppliers</div>
      </div>
      
      <div class="space-y-3 mb-4">
        <div class="flex items-center gap-2 text-xs text-gray-700">
          <span class="text-green-500">✓</span>
          <span>Multiple verified suppliers</span>
        </div>
        <div class="flex items-center gap-2 text-xs text-gray-700">
          <span class="text-green-500">✓</span>
          <span>Best price comparison</span>
        </div>
        <div class="flex items-center gap-2 text-xs text-gray-700">
          <span class="text-green-500">✓</span>
          <span>Quick response guaranteed</span>
        </div>
      </div>
      
      <button @click="showInquiryModal = true"
        class="w-full bg-primary hover:bg-primary/85 text-white font-semibold rounded py-3 flex items-center justify-center gap-2 text-sm transition transform hover:scale-105">
        Get Quotes Now <span class="ml-1">&#x27A1;</span>
      </button>
    </div>

  </aside>
  
  <!-- Inquiry Modal -->
  <InquiryModal v-if="showInquiryModal" @close="showInquiryModal = false" />
</div>
</template>

<script>
import {
  UserIcon,
  PhoneArrowUpRightIcon,
} from "@heroicons/vue/24/solid";

import {
  CubeIcon,
  TruckIcon,
  ChevronUpIcon
} from "@heroicons/vue/24/outline";
import axios from 'axios';
import InquiryModal from '../modals/InquiryModal.vue';
import { useModalScroll } from '@/composables/useModalScroll';
export default {
  components: {
    UserIcon,
    PhoneArrowUpRightIcon,
    CubeIcon,
    TruckIcon,
    ChevronUpIcon,
    InquiryModal
  },
  emits: ['filter-change', 'close'],
  props: {
    isOpen: {
      type: Boolean,
      default: true
    },
    selectedBusinessTypes: {
      type: Array,
      default: () => []
    },
    suppliers: {
      type: Boolean,
      default: false
    },
    selectedCategories: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      selectedBusinessTypesLocal: [],
      businessTypeOpen: true,
      loadingBusinessTypes: false,
      businessTypesError: null,
      suppliersLocal: this.suppliers,
      selectedCategoriesLocal: [],
      tempSelectedCategories: [], // Temporary selection before applying
      categoriesOpen: true,
             categoryDropdown: {
         isOpen: false,
         search: ''
       },
       categorySearchTimeout: null,
       isSearching: false, // Flag to prevent unnecessary emissions during search
       showInquiryModal: false,
       isVisible: this.isOpen,
       isClosing: false
    };
  },
  computed: {
    businessTypes() {
      return this.$store.getters.businessTypes;
    },
    filteredCategories() {
      return this.safeProductCategories;
    },
     availableCategories() {
       // Filter out already selected categories and temporarily selected ones
       const selectedSlugs = this.selectedCategoriesLocal.map(cat => cat.slug);
       const tempSelectedSlugs = this.tempSelectedCategories.map(cat => cat.slug);
       return this.safeProductCategories.filter(category =>
         !selectedSlugs.includes(category.slug) && !tempSelectedSlugs.includes(category.slug)
       );
     },
    categoriesLoading() {
      return this.$store.getters.categoriesLoading;
    },
    // Safe data getters with fallbacks
    safeProductCategories() {
      return Array.isArray(this.$store.getters.productCategories) ? this.$store.getters.productCategories : [];
    }
  },
  watch: {
    selectedBusinessTypes: {
      immediate: true,
      handler(newVal) {
        this.selectedBusinessTypesLocal = [...newVal];
      }
    },
    suppliers: {
      immediate: true,
      handler(newVal) {
        this.suppliersLocal = newVal;
      }
    },
    selectedCategories: {
      immediate: true,
      handler(newVal) {
        this.selectedCategoriesLocal = [...newVal];
      }
    },
     selectedBusinessTypesLocal: {
       handler(newVal) {
         this.$emit('filter-change', {
           businessTypes: newVal,
           suppliers: this.suppliersLocal,
           categorySlugs: this.selectedCategoriesLocal.map(cat => cat.slug)
         });
       },
       deep: true
     },
     suppliersLocal(newVal) {
       this.$emit('filter-change', {
         businessTypes: this.selectedBusinessTypesLocal,
         suppliers: newVal,
         categorySlugs: this.selectedCategoriesLocal.map(cat => cat.slug)
       });
     },
         selectedCategoriesLocal: {
       handler(newVal, oldVal) {
         // Don't emit during search operations
         if (this.isSearching) return;
         
         // Only emit if the actual category slugs have changed, not just the array reference
         const newSlugs = newVal.map(cat => cat.slug).sort();
         const oldSlugs = (oldVal || []).map(cat => cat.slug).sort();
         if (JSON.stringify(newSlugs) !== JSON.stringify(oldSlugs)) {
           this.$emit('filter-change', {
             businessTypes: this.selectedBusinessTypesLocal,
             suppliers: this.suppliersLocal,
             categorySlugs: newVal.map(cat => cat.slug)
           });
         }
       },
       deep: true
     },
    isOpen(newVal) {
      this.isVisible = newVal; // This line is correct
      if (newVal) {
        this.isClosing = false;
        this.openModal('search-sidebar');
      } else {
        this.closeModal('search-sidebar');
      }
    }
  },
  methods: {
    setSuppliers(val) {
      this.suppliersLocal = val;
    },
     selectCategory(category) {
       // Add to temporary selection instead of immediately applying
       if (!this.tempSelectedCategories.find(c => c.slug === category.slug)) {
         this.tempSelectedCategories.push(category);
       }
       this.categoryDropdown.search = '';
       // Keep dropdown open to show Apply button and allow more selections
     },
     removeCategory(categorySlug) {
       this.selectedCategoriesLocal = this.selectedCategoriesLocal.filter(c => c.slug !== categorySlug);
     },
    applyCategoryFilter() {
      // Apply temporary selections to actual selection
      this.selectedCategoriesLocal = [...this.selectedCategoriesLocal, ...this.tempSelectedCategories];
      this.tempSelectedCategories = [];
      this.categoryDropdown.isOpen = false;
    },
     removeTempCategory(categorySlug) {
       this.tempSelectedCategories = this.tempSelectedCategories.filter(c => c.slug !== categorySlug);
     },
     isCategorySelected(categorySlug) {
       return this.selectedCategoriesLocal.some(cat => cat.slug === categorySlug);
     },
    clearAllCategories() {
      this.selectedCategoriesLocal = [];
    },
         // Debounced search for categories
     debouncedCategorySearch() {
       // Keep dropdown open while typing
       this.categoryDropdown.isOpen = true;
       this.isSearching = true;
       clearTimeout(this.categorySearchTimeout);
       this.categorySearchTimeout = setTimeout(() => {
         this.searchCategories();
       }, 300);
     },
     async searchCategories() {
       try {
         this.isSearching = true;
         await this.$store.dispatch('searchProductCategories', this.categoryDropdown.search || '');
       } catch (error) {
         console.error('Error searching categories:', error);
       } finally {
         this.isSearching = false;
       }
     },
         handleCategoryFocus() {
       // Always open dropdown on focus to allow searching
       this.categoryDropdown.isOpen = true;
       // If no categories loaded yet, trigger initial search
       if (this.safeProductCategories.length === 0) {
         this.isSearching = true;
         this.searchCategories();
       }
     },
    addClickOutsideListeners() {
      document.addEventListener('click', this.handleClickOutside);
    },
    removeClickOutsideListeners() {
      document.removeEventListener('click', this.handleClickOutside);
    },
    handleClickOutside(event) {
      // Check if click is outside the category dropdown
      const categoryDropdown = event.target.closest('.relative');
      if (!categoryDropdown) {
        this.categoryDropdown.isOpen = false;
        // Clear temporary selections when clicking outside
        this.tempSelectedCategories = [];
      }
    },
    closeSidebar() {
      this.isClosing = true;
      setTimeout(() => {
        // isVisible is set to false to remove the element from the DOM after animation
        this.isVisible = false;
        this.$emit('close');
      }, 400); // Match animation duration
    },
  },
  setup() {
    const { openModal, closeModal } = useModalScroll();
    return { openModal, closeModal };
  },
  async mounted() {
    this.loadingBusinessTypes = true;
    this.businessTypesError = null;
    try {
      await this.$store.dispatch('fetchBusinessTypes');
    } catch (error) {
      this.businessTypesError = 'Failed to load business types.';
      console.error(error);
    } finally {
      this.loadingBusinessTypes = false;
    }

    // Initialize categories search
    this.searchCategories();
    this.addClickOutsideListeners();
  },
  beforeUnmount() {
    // Clear timeout
    if (this.categorySearchTimeout) {
      clearTimeout(this.categorySearchTimeout);
    }
    this.removeClickOutsideListeners();
    if (this.isOpen) {
      this.closeModal('search-sidebar');
    }
  }
}
</script>

<style scoped>
@keyframes slide-in-left {
  0% { transform: translateX(-100%); }
  100% { transform: translateX(0); }
}

@keyframes slide-out-left {
  0% { transform: translateX(0); }
  100% { transform: translateX(-100%); }
}

.animate-slide-in-left {
  animation: slide-in-left 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.animate-slide-out-left {
  animation: slide-out-left 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}
/* Primary color utilities for categories filter */
.bg-primary-100 {
  background-color: rgba(11, 132, 92, 0.1);
}

.text-primary-800 {
  color: rgba(11, 132, 92, 0.8);
}

.text-primary-600 {
  color: rgba(11, 132, 92, 0.6);
}

.hover\:text-primary-800:hover {
  color: rgba(11, 132, 92, 0.8);
}

.border-primary-200 {
  border-color: rgba(11, 132, 92, 0.2);
}

.hover\:border-primary-200:hover {
  border-color: rgba(11, 132, 92, 0.2);
}

.focus\:ring-primary-500:focus {
  --tw-ring-color: rgba(11, 132, 92, 0.5);
}

.hover\:bg-primary-50:hover {
  background-color: rgba(11, 132, 92, 0.05);
}
</style>
