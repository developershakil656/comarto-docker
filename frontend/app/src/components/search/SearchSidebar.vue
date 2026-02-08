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
      <h2>Filters</h2>
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



    <!-- Register Business Promo Box -->
    <div class="w-full flex justify-center">
      <OptimizedImage src="https://placehold.co/325x325" alt="Register Business" class="rounded-md shadow" />
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
import OptimizedImage from '@/components/common/OptimizedImage.vue';
export default {
  components: {
    UserIcon,
    PhoneArrowUpRightIcon,
    CubeIcon,
    TruckIcon,
    ChevronUpIcon,
    OptimizedImage,
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
    }
  },
  data() {
    return {
      selectedBusinessTypesLocal: [],
      businessTypeOpen: true,
      loadingBusinessTypes: false,
      businessTypesError: null,
      suppliersLocal: this.suppliers,

       showInquiryModal: false,
       isVisible: this.isOpen,
       isClosing: false
    };
  },
  computed: {
    businessTypes() {
      return this.$store.getters.businessTypes;
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
    selectedBusinessTypesLocal: {
       handler(newVal) {
         this.$emit('filter-change', {
           businessTypes: newVal,
           suppliers: this.suppliersLocal
         });
       },
       deep: true
     },
     suppliersLocal(newVal) {
       this.$emit('filter-change', {
         businessTypes: this.selectedBusinessTypesLocal,
         suppliers: newVal
       });
     },
    isOpen(newVal) {
      this.isVisible = newVal; // This line is correct
      if (newVal) {
        this.isClosing = false;
        // Only lock scroll for mobile (when overlay is visible)
        // Desktop sidebar should not lock scroll
        if (window.innerWidth < 768) {
          this.openModal('search-sidebar');
        }
      } else {
        this.closeModal('search-sidebar');
      }
    }
  },
  methods: {
    setSuppliers(val) {
      this.suppliersLocal = val;
    },
    addClickOutsideListeners() {
      document.addEventListener('click', this.handleClickOutside);
    },
    removeClickOutsideListeners() {
      document.removeEventListener('click', this.handleClickOutside);
    },
    closeSidebar() {
      this.isClosing = true;
      setTimeout(() => {
        // isVisible is set to false to remove the element from the DOM after animation
        this.isVisible = false;
        this.$emit('close');
      }, 400); // Match animation duration
    },
    
    // Handle window resize to update scroll locking behavior
    handleResize() {
      if (this.resizeTimeout) {
        clearTimeout(this.resizeTimeout);
      }
      
      this.resizeTimeout = setTimeout(() => {
        // Update scroll locking based on current screen size
        if (this.isOpen) {
          if (window.innerWidth < 768) {
            // Mobile - enable scroll lock
            this.openModal('search-sidebar');
          } else {
            // Desktop - disable scroll lock
            this.closeModal('search-sidebar');
          }
        }
      }, 150); // Throttle resize events
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

    this.addClickOutsideListeners();
    
    // Add resize listener
    window.addEventListener('resize', this.handleResize);
  },
  beforeUnmount() {
    this.removeClickOutsideListeners();
    if (this.isOpen) {
      this.closeModal('search-sidebar');
    }
    
    // Remove resize listener
    window.removeEventListener('resize', this.handleResize);
    
    // Clear resize timeout
    if (this.resizeTimeout) {
      clearTimeout(this.resizeTimeout);
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
