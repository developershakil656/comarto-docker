<template>
  <div class="fixed inset-0 flex items-stretch md:items-center justify-center z-[60] p-0 md:p-4 bg-white md:bg-black md:bg-opacity-50">
    <div class="bg-white w-full h-full md:h-auto md:rounded-2xl md:shadow-2xl md:max-w-5xl md:max-h-[90vh] overflow-y-auto relative">
      <MobileModalHeader title="Inquiry" @back="$emit('close')" />
      <h2 class="hidden md:block absolute top-4 right-12">{{currentStep + 1}}/2</h2>
      <button @click="$emit('close')" class="hidden md:block text-gray-400 hover:text-gray-600 transition-colors absolute top-4 right-4">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      <!-- Main Content - Two Column Layout -->
      <div class="p-4 md:p-0">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden md:rounded-none md:shadow-none">
      <div class="flex">
        <!-- Left Column - Eye-catching Quotes and Visuals -->
        <div class="hidden md:block w-1/2 bg-gradient-to-br from-teal-400 to-blue-500 p-8 justify-center relative overflow-hidden">
          <!-- Brand/Logo -->
          <div class="text-center mb-8">
            <img src="/logo.svg" alt="Logo" class="h-8 m-auto" />
            <p class="text-teal-100 text-lg">Connect • Trade • Grow</p>
          </div>

          <!-- Eye-catching Quotes -->
          <div class="space-y-6 text-center">
            <div class="bg-white/20 backdrop-blur-sm rounded-2xl p-6 border border-white/30">
              <div class="text-4xl mb-3">💼</div>
              <h4 class="text-xl font-bold text-white mb-2">Find Your Perfect Match</h4>
              <p class="text-teal-100">Connect with verified suppliers and get the best deals for your business needs.</p>
            </div>
            
            <div class="bg-white/20 backdrop-blur-sm rounded-2xl p-6 border border-white/30">
              <div class="text-4xl mb-3">🚀</div>
              <h4 class="text-xl font-bold text-white mb-2">Fast & Efficient</h4>
              <p class="text-teal-100">Submit your requirements once and receive multiple competitive quotes from top suppliers.</p>
            </div>
            
            <div class="bg-white/20 backdrop-blur-sm rounded-2xl p-6 border border-white/30">
              <div class="text-4xl mb-3">🎯</div>
              <h4 class="text-xl font-bold text-white mb-2">Quality Assured</h4>
              <p class="text-teal-100">All suppliers are verified and rated by our community of business professionals.</p>
            </div>
          </div>

          <!-- Decorative Elements -->
          <div class="absolute bottom-8 left-8 animate-pulse">
            <div class="w-16 h-16 bg-yellow-300 rounded-full opacity-80 shadow-lg"></div>
          </div>
          <div class="absolute top-8 right-8 animate-bounce">
            <div class="w-12 h-12 bg-pink-300 rounded-full opacity-80 shadow-lg"></div>
          </div>
          <div class="absolute top-1/2 left-4 animate-spin">
            <div class="w-8 h-8 bg-white/30 rounded-full"></div>
          </div>
          <div class="absolute bottom-1/3 right-4 animate-pulse">
            <div class="w-6 h-6 bg-blue-300 rounded-full opacity-60"></div>
          </div>
        </div>

        <!-- Right Column - Form Content -->
        <div class="w-full md:w-1/2 p-4 md:p-8">
          <!-- Step Content -->
           <div class="flex h-1 w-full my-4"  :class="{ 'bg-gradient-to-r from-teal-500 to-blue-600': currentStep }">
              <div class="w-full" :class="{ 'bg-gradient-to-r from-teal-500 to-blue-600': !currentStep }"></div>
              <div class="w-full" :class="{ 'bg-gray-300': !currentStep }"></div>
            </div>
          <div class="space-y-6">
            
            <!-- Step 1: Basic Information -->
            <div v-if="currentStep === 0" class="space-y-6">
              <div class="text-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800 mb-2">
                  Inquiry Details
                </h3>
                <p class="text-gray-600">
                  Tell us what you need and we'll connect you with the best suppliers
                </p>
              </div>
              
              <!-- Location Selection -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2 items-center">
                  <span class="mr-2">📍</span>
                  Preferred Location *
                </label>
                <div class="relative location-dropdown">
                  <input
                    v-model="locationSearch"
                    :placeholder="selectedLocation ? `${selectedLocation.upazila_name}, ${selectedLocation.district_name}` : 'Search Location'"
                    @focus="closeAllDropdowns(); showLocationDropdown = true"
                    @input="handleLocationInput"
                    type="text"
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300 shadow-sm"
                  />
                  <div v-if="showLocationDropdown && locations.length > 0" class="absolute z-10 w-full mt-1 bg-white border-2 border-gray-200 rounded-xl shadow-xl max-h-60 overflow-y-auto">
                    <div 
                      v-for="location in locations" 
                      :key="location.id"
                      @click="selectLocation(location)"
                      class="px-4 py-3 hover:bg-primary-50 cursor-pointer transition-colors duration-200 border-b border-gray-100 last:border-b-0"
                    >
                      {{ setLocationName(location) }}
                    </div>
                  </div>
                </div>
              </div>

              <!-- Category Selection -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2 items-center">
                  <span class="mr-2">🏷️</span>
                  Category *
                </label>
                <div class="relative category-dropdown">
                  <input
                    v-model="categorySearch"
                    :placeholder="selectedCategory ? selectedCategory.name : 'Search Category'"
                    @focus="closeAllDropdowns(); showCategoryDropdown = true"
                    @input="handleCategoryInput"
                    type="text"
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300 shadow-sm"
                  />
                  <div v-if="showCategoryDropdown && productCategories.length > 0" class="absolute z-10 w-full mt-1 bg-white border-2 border-gray-200 rounded-xl shadow-xl max-h-60 overflow-y-auto">
                    <div 
                      v-for="category in productCategories" 
                      :key="category.id"
                      @click="selectCategory(category)"
                      class="px-4 py-3 hover:bg-primary-50 cursor-pointer transition-colors duration-200 border-b border-gray-100 last:border-b-0"
                    >
                      {{ category.parent }} > {{ category.name }}
                    </div>
                  </div>
                </div>
              </div>

                             <!-- Quantity and Unit -->
               <div>
                 <label class=" text-sm font-medium text-gray-700 mb-2 flex items-center">
                   <span class="mr-2">📊</span>
                   Quantity (approx.)*
                 </label>
                <div class="md:flex md:space-x-3">
                                     <input
                     v-model="form.quantity"
                     type="number"
                     min="1"
                     required
                     class="w-full px-4 py-3 border-2 mb-2 md:mb-0 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300 shadow-sm"
                     placeholder="Enter quantity"
                   />
                  <div class="relative unit-dropdown">
                                         <input
                       v-model="unitSearch"
                       :placeholder="selectedUnit ? selectedUnit.full_form : 'Search Unit'"
                       @focus="closeAllDropdowns(); showUnitDropdown = true"
                       @input="handleUnitInput"
                       type="text"
                       required
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300 shadow-sm"
                     />
                    <div v-if="showUnitDropdown && productUnits.length > 0" class="absolute z-10 w-full mt-1 bg-white border-2 border-gray-200 rounded-xl shadow-xl max-h-60 overflow-y-auto">
                      <div 
                        v-for="unit in productUnits" 
                        :key="unit.id"
                        @click="selectUnit(unit)"
                        class="px-4 py-3 hover:bg-primary-50 cursor-pointer transition-colors duration-200 border-b border-gray-100 last:border-b-0"
                      >
                        {{ unit.full_form }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
               
               <button 
                 @click="nextStep" 
                 :disabled="!canProceedToStep2"
                 class="w-full py-4 bg-gradient-to-r from-teal-500 to-blue-600 text-white text-lg font-semibold rounded-xl shadow-lg hover:from-teal-600 hover:to-blue-700 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105"
               >
                 Next Step
               </button>
            </div>

            <!-- Step 2: Description and Submit -->
            <div v-if="currentStep === 1" class="space-y-6">
              <div class="text-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800 mb-2">
                  Tell Us More
                </h3>
                <p class="text-gray-600">
                  Help suppliers understand your exact requirements
                </p>
              </div>
              
              <!-- Description -->
              <div>
                <label class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                  <span class="mr-2">📝</span>
                  Description *
                </label>
                <textarea
                  v-model="form.description"
                  rows="4"
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300 resize-none shadow-sm"
                  placeholder="Describe your requirements in detail..."
                ></textarea>
              </div>

              <!-- Summary -->
              <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl p-6 border border-blue-100 shadow-sm">
                <h4 class="font-semibold text-gray-800 mb-4 flex items-center">
                  <span class="mr-2">📋</span>
                  Inquiry Summary
                </h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                  <div class="bg-white/60 rounded-lg p-3">
                    <span class="text-gray-600 block text-xs uppercase tracking-wide">Preferred Location:</span>
                    <p class="font-medium text-gray-800">{{ selectedLocation ? setLocationName(selectedLocation) : 'Not selected' }}</p>
                  </div>
                  <div class="bg-white/60 rounded-lg p-3">
                    <span class="text-gray-600 block text-xs uppercase tracking-wide">Category:</span>
                    <p class="font-medium text-gray-800">{{ selectedCategory ? selectedCategory.name : 'Not selected' }}</p>
                  </div>
                                     <div class="bg-white/60 rounded-lg p-3">
                     <span class="text-gray-600 block text-xs uppercase tracking-wide">Quantity (approx.):</span>
                     <p class="font-medium text-gray-800">
                       {{ form.quantity && selectedUnit ? `${form.quantity} ${getDisplayUnit()}` : 'Not specified' }}
                     </p>
                   </div>
                </div>
              </div>

              <div class="flex space-x-4">
                <button 
                  @click="previousStep" 
                  class="flex-1 py-4 bg-gray-200 text-gray-800 text-lg font-semibold rounded-xl hover:bg-gray-300 transition-all duration-300"
                >
                  Previous
                </button>
                <button 
                  @click="submitInquiry" 
                  :disabled="!canSubmit || isSubmitting"
                  class="flex-1 py-4 bg-gradient-to-r from-teal-500 to-blue-600 text-white text-lg font-semibold rounded-xl shadow-lg hover:from-teal-600 hover:to-blue-700 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105"
                >
                  <span v-if="isSubmitting">
                    Submitting Inquiry...
                  </span>
                  <span v-else>
                    Submit Inquiry
                  </span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import { push } from 'notivue';
import { useModalScroll } from '@/composables/useModalScroll';
import MobileModalHeader from '@/components/common/MobileModalHeader.vue';
import OptimizedImage from '@/components/common/OptimizedImage.vue';

export default {
  name: 'InquiryModal',
  components: { MobileModalHeader, OptimizedImage },
  setup() {
    const { openModal, closeModal } = useModalScroll()
    return { openModal, closeModal }
  },
  data() {
    return {
      currentStep: 0,
      locationSearch: '',
      categorySearch: '',
      unitSearch: '',
      showLocationDropdown: false,
      showCategoryDropdown: false,
      showUnitDropdown: false,
      selectedLocation: null,
      selectedCategory: null,
      selectedUnit: null,
      searchTimeout: null,
      isSubmitting: false,
      form: {
        location_id: '',
        category_id: '',
        quantity: '',
        unit_id: '',
        unit: '',
        description: ''
      }
    }
  },
  computed: {
    ...mapGetters(['locations', 'productCategories', 'productUnits', 'isAuthenticated']),
    

    
    canProceedToStep2() {
      return this.selectedLocation && this.selectedCategory && this.form.quantity && this.selectedUnit
    },
    
    canSubmit() {
      return this.form.description && this.form.description.trim().length > 0
    },
    
    validationErrors() {
      const errors = []
      if (!this.selectedLocation) errors.push('Location is required')
      if (!this.selectedCategory) errors.push('Category is required')
      if (!this.form.quantity) errors.push('Quantity is required')
      if (!this.selectedUnit) errors.push('Unit is required')
      if (!this.form.description || this.form.description.trim().length === 0) errors.push('Description is required')
      return errors
    }
  },
  methods: {
    ...mapActions(['fetchLocations', 'searchProductCategories', 'searchProductUnits']),
    
    setLocationName(location) {
      if (location.upazila_name) {
        return `${location.upazila_name}, ${location.district_name}`
      } else {
        return `${location.district_name}, District`
      }
    },
    getDisplayUnit() {
      if (this.selectedUnit) {
        return this.selectedUnit.full_form
      }
      return ''
    },
    
    async debouncedSearchLocations() {
      clearTimeout(this.searchTimeout)
      this.searchTimeout = setTimeout(async () => {
        if (this.locationSearch.trim()) {
          await this.fetchLocations(this.locationSearch)
        }
      }, 300)
    },
    
    async debouncedCategorySearch() {
      clearTimeout(this.searchTimeout)
      this.searchTimeout = setTimeout(async () => {
        if (this.categorySearch.trim()) {
          await this.searchProductCategories(this.categorySearch)
        }
      }, 300)
    },
    
    async debouncedUnitSearch() {
      clearTimeout(this.searchTimeout)
      this.searchTimeout = setTimeout(async () => {
        if (this.unitSearch.trim()) {
          await this.searchProductUnits(this.unitSearch)
        }
      }, 300)
    },
    
    selectLocation(location) {
      this.selectedLocation = location
      this.form.location_id = location.id
      this.showLocationDropdown = false
      this.locationSearch = this.setLocationName(location)
    },
    
    selectCategory(category) {
      this.selectedCategory = category
      this.form.category_id = category.id
      this.showCategoryDropdown = false
      this.categorySearch = category.name
    },
    
    selectUnit(unit) {
      this.selectedUnit = unit
      this.form.unit_id = unit.id
      this.form.unit = unit.full_form
      this.showUnitDropdown = false
      this.unitSearch = unit.full_form
    },
    
    clearLocation() {
      this.selectedLocation = null
      this.form.location_id = ''
    },
    
    clearCategory() {
      this.selectedCategory = null
      this.form.category_id = ''
    },
    
    clearUnit() {
      this.selectedUnit = null
      this.form.unit_id = ''
      this.form.unit = ''
    },
    
    nextStep() {
      if (this.canProceedToStep2) {
        this.currentStep = 1
      }
    },
    
    previousStep() {
      this.currentStep = 0
      this.showLocationDropdown = false
      this.showCategoryDropdown = false
      this.showUnitDropdown = false
    },
    
    async submitInquiry() {
      if (!this.isAuthenticated) {
        // this.$emit('close')
        // Redirect to login or show login modal
        push.error('Please login to submit inquiries')
        return
      }
      
      if (!this.canSubmit) return
      
      this.isSubmitting = true
      
      try {
        // Prepare the form data with unit_name
        const inquiryData = {
          ...this.form,
          unit_name: this.selectedUnit ? this.selectedUnit.full_form : ''
        }
        
        const response = await this.$store.dispatch('createInquiry', inquiryData)
        // Check if response exists and has success status
        if (response) {
          push.success('Inquiry submitted successfully!')
          this.$emit('close')
          this.resetForm()
        } else {
          // Handle case where response exists but indicates failure
          const errorMessage = response?.message || 'Failed to submit inquiry'
          push.error(errorMessage)
        }
      } catch (error) {
        // Handle actual errors (network issues, etc.)
        const errorMessage = error?.message || error?.error || 'Failed to submit inquiry'
        push.error(errorMessage)
      } finally {
        this.isSubmitting = false
      }
    },
    
    resetForm() {
      this.currentStep = 0
      this.locationSearch = ''
      this.categorySearch = ''
      this.unitSearch = ''
      this.selectedLocation = null
      this.selectedCategory = null
      this.selectedUnit = null
      this.form = {
        location_id: '',
        category_id: '',
        quantity: '',
        unit_id: '',
        unit: '',
        description: ''
      }
    },
    
    handleClickOutside(event) {
      // Close location dropdown if click is outside
      if (!event.target.closest('.location-dropdown')) {
        this.showLocationDropdown = false
      }
      // Close category dropdown if click is outside
      if (!event.target.closest('.category-dropdown')) {
        this.showCategoryDropdown = false
      }
      // Close unit dropdown if click is outside
      if (!event.target.closest('.unit-dropdown')) {
        this.showUnitDropdown = false
      }
    },
    
    closeAllDropdowns() {
      this.showLocationDropdown = false
      this.showCategoryDropdown = false
      this.showUnitDropdown = false
    },
    
    handleKeydown(event) {
      if (event.key === 'Escape') {
        this.closeAllDropdowns()
      }
    },
    
    handleLocationInput(event) {
      this.locationSearch = event.target.value
      this.debouncedSearchLocations()
    },
    
    handleCategoryInput(event) {
      this.categorySearch = event.target.value
      this.debouncedCategorySearch()
    },
    
    handleUnitInput(event) {
      this.unitSearch = event.target.value
      this.debouncedUnitSearch()
    }
  },
  
  watch: {
    showLocationDropdown(val) {
      if (!val && this.locationSearch) {
        this.locationSearch = this.setLocationName(this.selectedLocation)
        // Only clear if there's search text and no selected location
        // if (!this.selectedLocation) {
        //   this.locationSearch = ''
        // }
      }
    },
    showCategoryDropdown(val) {
      if (!val && this.categorySearch) {
        this.categorySearch = this.selectedCategory.name;
        // Only clear if there's search text and no selected category
        // if (!this.selectedCategory) {
        //   this.categorySearch = ''
        // }
      }
    },
    showUnitDropdown(val) {
      if (!val && this.unitSearch) {
        this.unitSearch = this.selectedUnit.full_form;
        // Only clear if there's search text and no selected unit
        // if (!this.selectedUnit) {
        //   this.unitSearch = ''
        // }
      }
    }
  },
  
  async mounted() {
    // Register modal for scroll management
    this.openModal('inquiry-modal')
    
    // Load initial data
    await this.fetchLocations('')
    await this.searchProductCategories('')
    await this.searchProductUnits('')
    
    // Set default location if available in store
    if (this.$store.state.selectedLocation) {
      // You might need to fetch the location details here
    }
    
    // Handle click outside dropdowns - use capture phase to ensure it runs early
    document.addEventListener('click', this.handleClickOutside, true)
    
    // Handle escape key to close dropdowns
    document.addEventListener('keydown', this.handleKeydown)
  },
  
  beforeDestroy() {
    // Unregister modal from scroll management
    this.closeModal('inquiry-modal')
    
    document.removeEventListener('click', this.handleClickOutside, true)
    document.removeEventListener('keydown', this.handleKeydown)
    if (this.searchTimeout) {
      clearTimeout(this.searchTimeout)
    }
  }
}
</script>

<style scoped>
.location-dropdown,
.category-dropdown,
.unit-dropdown {
  position: relative;
}
</style>
