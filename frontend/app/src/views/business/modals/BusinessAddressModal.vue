<template>
  <Transition name="modal" appear>
    <div v-if="open" class="fixed inset-0 z-[60] flex items-stretch md:items-center justify-center bg-white md:bg-black md:bg-opacity-30 p-0 md:p-4" style="min-height: 100vh;" @click="$emit('close')">
      <Transition name="modal-content" appear>
        <div class="bg-white w-full h-full md:h-auto md:rounded-xl md:shadow-lg md:max-w-lg md:max-h-[90vh] overflow-y-auto" @click.stop>
          <MobileModalHeader title="Business Address" @back="$emit('close')" />
          <div class="p-4 md:p-8 relative">
            <button @click="$emit('close')" class="hidden md:block absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors">
              <XMarkIcon class="w-6 h-6" />
            </button>
            <h2 class="hidden md:block text-xl font-semibold mb-6 text-center">
              <slot> Business Address </slot>
            </h2>
            <form @submit.prevent="onSubmit" class="space-y-6 rounded-2xl shadow-lg px-4 py-6 min-h-full">
              <!-- Search Location -->
              <div class="relative group location-input-container">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                  <MapPinIcon class="w-5 h-5" />
                </span>
                <input
                  v-model="locationSearch"
                  @focus="showLocationDropdown = true"
                  @blur="handleInputBlur"
                  @input="debouncedSearchLocations"
                  type="text"
                  class="block w-full rounded-lg border border-gray-300 pl-12 pr-4 py-3 focus:border-primary focus:ring-blue-200 outline-none transition text-base"
                  autocomplete="off"
                  placeholder="Search location..."
                />
                <label
                  class="absolute left-12 z-10 -translate-y-1/2 bg-white text-gray-500 text-sm transition-all duration-200 pointer-events-none group-focus-within:top-0 group-focus-within:text-xs group-focus-within:left-8 group-focus-within:text-primary group-focus-within:-translate-y-1.5"
                  :class="{ 'top-0 text-xs left-8 text-primary': locationSearch, 'top-1/2': !locationSearch }"
                >
                  Search Location
                </label>
                <div v-if="showLocationDropdown && businessLocations.length > 0" class="absolute left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-30 max-h-60 overflow-auto">
                  <div
                    v-for="location in businessLocations"
                    :key="location.id"
                    @mousedown.prevent="selectLocation(location)"
                    class="px-4 py-2 cursor-pointer hover:bg-primary/10"
                    :class="{ 'font-semibold text-primary': location.id === form.location_id }"
                  >
                    {{ location.upazila_name }}, {{ location.district_name }}
                  </div>
                </div>
              </div>
              
              <!-- Post Code -->
              <div class="relative group">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                  <MagnifyingGlassIcon class="w-5 h-5" />
                </span>
                <input v-model="form.post_code" type="number" id="postCode" required pattern="\\d{4}" maxlength="4"
                  class="block w-full rounded-lg border border-gray-300 pl-12 pr-4 py-3 focus:border-primary focus:ring-blue-200 outline-none transition text-base"
                />
                <label for="postCode"
                  class="absolute left-12 z-10 -translate-y-1/2 bg-white text-gray-500 text-sm transition-all duration-200 pointer-events-none group-focus-within:top-0 group-focus-within:text-xs group-focus-within:left-8 group-focus-within:text-primary group-focus-within:-translate-y-1.5"
                  :class="{ 'top-0 text-xs left-8 text-primary': form.post_code, 'top-1/2': !form.post_code }">
                  Post Code
                </label>
              </div>
              
              <!-- Full Address -->
              <div class="relative group">
                <span class="absolute left-4 top-4 text-gray-400 pointer-events-none">
                  <HomeIcon class="w-5 h-5" />
                </span>
                <textarea v-model="form.address" id="fullAddress" required rows="3"
                  class="block w-full rounded-lg border border-gray-300 pl-12 pt-3 pr-4 pb-2 focus:border-primary focus:ring-blue-200 outline-none transition text-base resize-none"
                ></textarea>
                <label for="fullAddress"
                  class="absolute left-12 z-10 bg-white text-gray-500 text-sm transition-all duration-200 pointer-events-none group-focus-within:-top-1 group-focus-within:text-xs group-focus-within:left-8 group-focus-within:text-primary"
                  :class="{ '-top-1 text-xs left-8 text-primary': form.address, 'top-3': !form.address }">
                  Full Address
                </label>
              </div>
              
              <!-- Error Message -->
              <div v-if="error" class="bg-red-50 border border-red-200 rounded-md p-3">
                <p class="text-red-600 text-sm">{{ error }}</p>
              </div>
              
              <button type="submit" class="w-full bg-primary hover:bg-primary/85 text-white font-semibold py-2 rounded-md transition disabled:opacity-60 disabled:cursor-not-allowed" :disabled="loading">
                <span v-if="loading" class="flex items-center justify-center">
                  <ArrowPathIcon class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" />
                  Updating...
                </span>
                <span v-else>
                  Update Address
                </span>
              </button>
            </form>
          </div>
        </div>
      </Transition>
    </div>
  </Transition>
</template>

<script>
import { push } from 'notivue';
import { XMarkIcon, MapPinIcon, MagnifyingGlassIcon, HomeIcon, ArrowPathIcon } from '@heroicons/vue/24/outline';
import MobileModalHeader from '@/components/common/MobileModalHeader.vue';
import { useModalScroll } from '@/composables/useModalScroll';

export default {
  name: 'BusinessAddressModal',
  components: {
    XMarkIcon,
    MapPinIcon,
    MagnifyingGlassIcon,
    HomeIcon,
    ArrowPathIcon,
    MobileModalHeader
  },
  setup() {
    const { openModal, closeModal } = useModalScroll()
    return { openModal, closeModal }
  },
  props: {
    open: {
      type: Boolean,
      default: false
    },
    initialData: {
      type: Object,
      default: () => ({
        address: '',
        post_code: '',
        location: null
      })
    }
  },
  data() {
    return {
      form: {
        address: '',
        post_code: '',
        location_id: null
      },
      locationSearch: '',
      showLocationDropdown: false,
      selectedLocation: null,
      searchTimeout: null,
      loading: false,
      error: null
    };
  },
  computed: {
    businessLocations() {
      return this.$store.getters.businessLocations;
    }
  },
  watch: {
    open(newVal) {
      if (newVal) {
        this.openModal('business-address-modal');
        // Initialize form with current business data when modal opens
        this.form = {
          address: this.initialData.address || '',
          post_code: this.initialData.post_code || '',
          location_id: this.initialData.location.id || null
        };
        
        // Set location search text if location_id exists
        if (this.initialData.location) {
          this.locationSearch = `${this.initialData.location.upazila_name}, ${this.initialData.location.district_name}`;
          this.setLocationSearchText();
        }
        
        // Debug: Log the data received
        console.log('BusinessAddressModal opened with:', {
          initialData: this.initialData,
          form: this.form,
          locationSearch: this.locationSearch
        });
      } else {
        this.closeModal('business-address-modal');
      }
    }
  },
  methods: {
    async searchLocations() {
      this.showLocationDropdown = true;
      await this.$store.dispatch('fetchBusinessLocations', this.locationSearch);
    },
    
    debouncedSearchLocations() {
      clearTimeout(this.searchTimeout);
      this.searchTimeout = setTimeout(() => {
        this.searchLocations();
      }, 300);
    },
    
    selectLocation(location) {
      this.selectedLocation = location;
      this.locationSearch = `${location.upazila_name}, ${location.district_name}`;
      this.form.location_id = location.id;
      this.showLocationDropdown = false;
    },
    
    setLocationSearchText() {
      // Find the location object by ID and set the search text
      const location = this.businessLocations.find(loc => loc.id === this.initialData.location.id);
      if (location) {
        this.locationSearch = `${location.upazila_name}, ${location.district_name}`;
      }
    },
    
    async onSubmit() {
      this.error = null;
      
      // Validate required fields
      if (!this.form.address || !this.form.post_code || !this.form.location_id) {
        this.error = 'Please fill in all required fields';
        return;
      }
      
      this.loading = true;
      try {
        // Make API call to update business address using the PUT endpoint
        const response = await this.$store.dispatch('updateBusinessInfo', {
          address: this.form.address,
          post_code: this.form.post_code,
          location_id: this.form.location_id
        });
        
        // Emit success event
        this.$emit('address-updated', this.form);
        this.$emit('close');
        this.resetForm();
        
        // Show success message
        push.success('Business address updated successfully');
        
      } catch (error) {
        console.error('Error updating business address:', error);
        this.error = error.response?.data?.message || 'Failed to update business address. Please try again.';
      } finally {
        this.loading = false;
      }
    },
    
    resetForm() {
      this.form = {
        address: '',
        post_code: '',
        location_id: null
      };
      this.locationSearch = '';
      this.selectedLocation = null;
      this.error = null;
    },
    
    handleClickOutside(event) {
      // Close location dropdown when clicking outside the location input area
      const locationInput = event.target.closest('.location-input-container');
      if (!locationInput) {
        this.showLocationDropdown = false;
      }
    },
    
    handleInputBlur() {
      // Close dropdown when input loses focus, but with a small delay to allow for item selection
      setTimeout(() => {
        this.showLocationDropdown = false;
      }, 150);
    }
  },
  
  mounted() {
    // Fetch initial business locations
    this.$store.dispatch('fetchBusinessLocations', '');
    
    // Add click outside listener with a slight delay to avoid conflicts with modal click handling
    this.$nextTick(() => {
      document.addEventListener('click', this.handleClickOutside, true);
    });
  },
  
  beforeUnmount() {
    if (this.searchTimeout) {
      clearTimeout(this.searchTimeout);
    }
    document.removeEventListener('click', this.handleClickOutside, true);
  }
}
</script> 