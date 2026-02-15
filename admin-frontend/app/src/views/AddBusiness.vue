<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center admin-header">
      <h1 class="text-2xl font-bold text-gray-900">Add Business</h1>
      <router-link 
        :to="{ name: 'Businesses' }" 
        class="admin-button-secondary"
      >
        Back to Businesses
      </router-link>
    </div>

    <div class="bg-white rounded-2xl shadow-sm p-6 admin-card">
      <Form :validation-schema="validationSchema" @submit="onSubmit" class="space-y-6">
        <!-- Business Details Section -->
        <div class="section">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Business Details</h2>
          
          <!-- Business Name -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Business Name *</label>
              <Field name="business_name" as="input" class="admin-input" placeholder="Enter business name" v-model="formData.business_name"/>
              <ErrorMessage name="business_name" class="text-red-500 text-xs mt-1" />
            </div>

            <!-- Business Slug -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Business Slug *</label>
              <Field name="slug" as="input" class="admin-input" placeholder="Enter business slug" v-model="formData.slug"/>
              <div v-if="slugAvailable" class="text-green-500 text-xs mt-1">
                ✓ This slug is available
              </div>

              <div v-else class="text-red-500 text-xs mt-1">
                {{ slugError }}
              </div>
            </div>
          </div>

          <!-- Business Type -->
          <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Business Type *</label>
            <div class="relative">
              <button 
                type="button"
                @click="showBusinessTypeModal = true"
                class="admin-input text-left cursor-pointer flex items-center justify-between"
                :class="formData.business_type && formData.business_type.length > 0 ? 'bg-white' : 'bg-gray-50'"
              >
                <span v-if="formData.business_type && formData.business_type.length > 0" class="text-gray-900">
                  {{ formData.business_type.join(', ') }}
                </span>
                <span v-else class="text-gray-500">Select business types...</span>
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
              </button>
              <!-- Selected business types display -->
              <div v-if="formData.business_type && formData.business_type.length > 0" class="mt-2 flex flex-wrap gap-2">
                <span 
                  v-for="type in formData.business_type" 
                  :key="type"
                  class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                >
                  {{ type }}
                  <button 
                    type="button"
                    @click="removeBusinessType(type)"
                    class="ml-1 text-blue-800 hover:text-blue-600"
                  >
                    ×
                  </button>
                </span>
              </div>
            </div>
            <Field name="business_type" type="hidden" v-model="formData.business_type" />
            <ErrorMessage name="business_type" class="text-red-500 text-xs mt-1" />
          </div>
        </div>

        <!-- Contact Details Section -->
        <div class="section">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Contact Details</h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Contact Person *</label>
              <Field name="name" as="input" class="admin-input" placeholder="Enter contact person name" v-model="formData.name"/>
              <ErrorMessage name="name" class="text-red-500 text-xs mt-1" />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Mobile Number *</label>
              <div class="flex gap-2">
                <span class="inline-flex items-center px-3 bg-gray-100 border border-gray-200 rounded-l-md text-gray-700">+88</span>
                <Field 
                  name="number" 
                  as="input" 
                  type="text"
                  class="admin-input rounded-l-none" 
                  placeholder="Mobile Number" 
                  v-model="formData.number"
                />
              </div>
              <ErrorMessage name="number" class="text-red-500 text-xs mt-1" />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Alternate Number</label>
              <div class="flex gap-2">
                <span class="inline-flex items-center px-3 bg-gray-100 border border-gray-200 rounded-l-md text-gray-700">+88</span>
                <Field 
                  name="alternate_number" 
                  as="input" 
                  type="text"
                  class="admin-input rounded-l-none" 
                  placeholder="Alternate Number" 
                  v-model="formData.alternate_number"
                />
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
              <Field name="email" as="input" type="email" class="admin-input" placeholder="Enter email address" v-model="formData.email" />
              <ErrorMessage name="email" class="text-red-500 text-xs mt-1" />
            </div>
          </div>
        </div>

        <!-- Location & Address Section -->
        <div class="section">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Location & Address</h2>
          
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">Full Address *</label>
              <Field name="address" as="textarea" class="admin-input min-h-[80px] resize-none" placeholder="Enter complete address" v-model="formData.address"/>
              <ErrorMessage name="address" class="text-red-500 text-xs mt-1" />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Post Code *</label>
              <Field 
                name="post_code" 
                as="input" 
                type="text"
                maxlength="4"
                pattern="[0-9]{4}"
                class="admin-input" 
                placeholder="Enter 4-digit post code" 
                v-model="formData.post_code"
              />
              <ErrorMessage name="post_code" class="text-red-500 text-xs mt-1" />
            </div>

            <div class="md:col-span-3">
              <label for="location-search" class="block text-sm font-medium text-gray-700 mb-1">Upazila/District *</label>
              <div class="relative location-dropdown">
                <input
                  class="admin-input"
                  id="location-search"
                  name="location-search"
                  placeholder="Search location..."
                  @focus="showLocationDropdown = true"
                  :value="locationSearch"
                  @input="locationSearch = $event.target.value; debouncedSearchLocations()"
                />
                <div v-if="showLocationDropdown && businessLocations.length > 0" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto">
                  <div 
                    v-for="location in businessLocations" 
                    :key="location.id"
                    @click="selectLocation(location)"
                    class="px-4 py-2 hover:bg-gray-100 cursor-pointer"
                  >
                    {{ location.upazila_name }}, {{ location.district_name }}
                  </div>
                </div>
              </div>
              <Field type="hidden" name="location_id" v-model="formData.location_id" />
              <ErrorMessage name="location_id" class="text-red-500 text-xs mt-1" />
            </div>
          </div>
        </div>

        <!-- Business Timings Section -->
        <div class="section hidden">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Business Timings</h2>
          <p class="text-gray-500 mb-4">Let your customers know when you are open for business</p>
          
          <div class="space-y-4">
            <div v-for="(day, idx) in daysOfWeek" :key="day" class="border rounded-lg p-4">
              <div class="flex items-center justify-between mb-3">
                <label class="text-sm font-bold">{{ day }}</label>
                <div class="flex items-center gap-2">
                  <label class="flex items-center gap-2 text-sm">
                    <input 
                      type="checkbox" 
                      :id="`24hours-${day}`"
                      :name="`24hours-${day}`"
                      v-model="timeSlot.days[idx].is24Hours" 
                      @change="handle24HoursChange(idx)"
                    /> 24 Hours
                  </label>
                  <label class="flex items-center gap-2 text-sm">
                    <input 
                      type="checkbox" 
                      :id="`closed-${day}`"
                      :name="`closed-${day}`"
                      v-model="timeSlot.days[idx].isClosed" 
                      @change="handleClosedChange(idx)"
                    /> Closed
                  </label>
                </div>
              </div>
              
              <div v-if="!timeSlot.days[idx].is24Hours && !timeSlot.days[idx].isClosed" class="flex gap-4 items-end">
                <div class="flex-1">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Open at</label>
                  <select 
                    v-model="timeSlot.days[idx].open" 
                    :id="`open-${day}`"
                    :name="`open-${day}`"
                    class="admin-select" 
                    @change="updateTimingData"
                  >
                    <option value="">Select time</option>
                    <option v-for="time in timeOptions" :key="time" :value="time">{{ time }}</option>
                  </select>
                </div>
                <div class="flex-1">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Close at</label>
                  <select 
                    v-model="timeSlot.days[idx].close" 
                    :id="`close-${day}`"
                    :name="`close-${day}`"
                    class="admin-select" 
                    @change="updateTimingData"
                  >
                    <option value="">Select time</option>
                    <option v-for="time in timeOptions" :key="time" :value="time">{{ time }}</option>
                  </select>
                </div>
              </div>
              
              <div v-else-if="timeSlot.days[idx].is24Hours" class="text-green-600 font-medium mt-2">
                Open 24 Hours
              </div>
              
              <div v-else-if="timeSlot.days[idx].isClosed" class="text-red-600 font-medium mt-2">
                Closed
              </div>
            </div>
          </div>
          
          <p v-if="timingWarning" class="text-red-500 text-sm ml-2 mt-2">{{ timingWarning }}</p>
        </div>

        <!-- Business Profile Photo Section -->
        <div class="section">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Business Profile Photo</h2>
          <p class="text-gray-500 mb-4">Add your business profile photo</p>
          
          <div class="flex justify-center mb-4">
            <label class="flex flex-col items-center justify-center w-60 h-60 border-2 border-dashed border-blue-400 rounded-xl cursor-pointer hover:bg-blue-50 transition">
              <div v-if="photoPreview" class="w-full h-full relative">
                <img :src="photoPreview" alt="Business Profile Preview" class="w-full h-full object-cover rounded-xl" />
                <button 
                  @click.stop="removePhoto" 
                  class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600"
                >
                  ×
                </button>
              </div>
              <div v-else>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-blue-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="text-blue-500 font-semibold">Add Photo</span>
              </div>
              <input 
                type="file" 
                class="hidden" 
                accept="image/*"
                @change="handlePhotoUpload"
              />
            </label>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end pt-4">
          <button type="submit" class="admin-button-primary" :disabled="isSubmitting">
            {{ isSubmitting ? 'Creating Business...' : 'Create Business' }}
          </button>
        </div>
      </Form>
    </div>

    <!-- Business Type Modal -->
    <BusinessTypeModal 
      :open="showBusinessTypeModal" 
      :selected-business-types="formData.business_type"
      mode="registration"
      @close="showBusinessTypeModal = false"
      @business-types-selected="handleBusinessTypesSelected"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { useRouter } from 'vue-router';

import { Form, Field, ErrorMessage } from 'vee-validate';
import { getCurrentInstance } from 'vue';
import * as yup from 'yup';
import { useToast } from '../composables/useToast';
import { useLocationStore } from '../stores/location';
import { useBusinessStore } from '../stores/business';
import { useBusinessTypeStore } from '../stores/businessType';
import { businessUtilityAPI } from '../api/services';
import BusinessTypeModal from './modals/BusinessTypeModal.vue';

const router = useRouter();
const locationStore = useLocationStore();
const businessStore = useBusinessStore();
const businessTypeStore = useBusinessTypeStore();
const { showToast } = useToast();

// Form data
const formData = ref({
  business_name: '',
  slug: '',
  business_type: ['wholesalers'],
  name: '',
  number: '',
  alternate_number: '',
  email: '',
  address: '',
  post_code: '',
  location_id: null,
  status: 'active',
});

// Business Type Modal
const showBusinessTypeModal = ref(false);



// Location Search
const locationSearch = ref('');
const showLocationDropdown = ref(false);
const searchTimeout = ref(null);

// Photo upload state
const businessProfilePhoto = ref(null);
const photoPreview = ref(null);

// Slug validation state
const slugError = ref('');
const slugAvailable = ref(false);
const slugValid = ref(false);
const slugCheckTimeout = ref(null);

// Week starts with Saturday
const daysOfWeek = ['Sat', 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri'];

// Generate time options in AM/PM format
const generateTimeOptions = () => {
  const times = [];
  const ampm = ['am', 'pm'];
  for (let h = 0; h < 24; h++) {
    for (let m = 0; m < 60; m += 30) {
      let hour = h % 12 === 0 ? 12 : h % 12;
      let minute = m === 0 ? '00' : '30';
      let suffix = ampm[Math.floor(h / 12)];
      times.push(`${hour}:${minute} ${suffix}`);
    }
  }
  return times;
};

const timeOptions = generateTimeOptions();

const timeSlot = ref({
  days: daysOfWeek.map(day => ({
    day: day,
    is24Hours: false,
    isClosed: false,
    open: '9:00 am',
    close: '10:00 pm',
  })),
});

const timingWarning = ref('');

// Timing data structure to match Timing.vue component
const timingData = ref({
  sat: null,
  sun: null,
  mon: null,
  tue: null,
  wed: null,
  thu: null,
  fri: null
});

// Loading state
const isSubmitting = ref(false);

// Computed properties
const businessLocations = computed(() => locationStore.businessLocations);
const businessTypes = computed(() => businessTypeStore.businessTypes);

// Methods
const searchLocations = async () => {
  showLocationDropdown.value = true;
  await locationStore.searchLocations(locationSearch.value);
};

const debouncedSearchLocations = () => {
  clearTimeout(searchTimeout.value);
  searchTimeout.value = setTimeout(() => {
    searchLocations();
  }, 300);
};

const handleBusinessTypesSelected = (selectedTypes) => {
  formData.value.business_type = selectedTypes;
  showBusinessTypeModal.value = false;
};

const removeBusinessType = (typeToRemove) => {
  formData.value.business_type = formData.value.business_type.filter(
    type => type !== typeToRemove
  );
};



const selectLocation = (location) => {
  locationSearch.value = `${location.upazila_name}, ${location.district_name}`;
  formData.value.location_id = Number(location.id); // Ensure it's a number
  showLocationDropdown.value = false;
};

const handleClickOutside = (event) => {
  // Close location dropdown
  if (!event.target.closest('.location-dropdown')) {
    showLocationDropdown.value = false;
  }
};

onMounted(async () => {
  // Add click outside listener
  document.addEventListener('click', handleClickOutside);
  // Load locations and business types
  await Promise.all([
    locationStore.searchLocations(),
    businessTypeStore.fetchAll()
  ]);
});

// Clean up event listener
onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
  clearTimeout(searchTimeout.value);
  
  // Clear slug check timeout if exists
  if (slugCheckTimeout.value) {
    clearTimeout(slugCheckTimeout.value);
  }
  
  // Clean up photo preview URL
  if (photoPreview.value) {
    URL.revokeObjectURL(photoPreview.value);
  }
});

// Watch for business name changes to auto-generate slug
watch(() => formData.value.business_name, (newName) => {
  if (newName) {
    formData.value.slug = slugify(newName);
  }
});

// Watch for slug changes to check availability
watch(() => formData.value.slug, (newSlug) => {
  if (newSlug) {
    checkSlugAvailability(newSlug);
  } else {
    slugError.value = '';
    slugAvailable.value = false;
    slugValid.value = false;
  }
});

function slugify(text) {
  return text
    .toString()
    .toLowerCase()
    .trim()
    .replace(/[\s\W-]+/g, '-')
    .replace(/^-+|-+$/g, '');
}

// Function to check slug availability
const checkSlugAvailability = async (slugValue) => {
  if (!slugValue) {
    slugError.value = '';
    slugAvailable.value = false;
    slugValid.value = false;
    return;
  }

  try {
    // Clear previous timeout if exists
    if (slugCheckTimeout.value) {
      clearTimeout(slugCheckTimeout.value);
    }

    // Set a timeout to debounce the API call
    slugCheckTimeout.value = setTimeout(async () => {
      try {
        const response = await businessUtilityAPI.checkSlugAvailability(slugValue);
        if (response.data.status) {
          slugError.value = '';
          slugAvailable.value = true;
          slugValid.value = true;
        }
      } catch (error) {
        const errors = error.response?.data?.data;
        if (errors && errors.slug) {
          slugError.value = errors.slug[0];
        } else {
          slugError.value = error.response?.data?.message || 'Error checking slug availability';
        }
        slugAvailable.value = false;
        slugValid.value = false;
      }
    }, 500); // 500ms debounce
  } catch (error) {
    console.error('Error setting up slug availability check:', error);
    slugError.value = 'Error checking slug availability';
    slugAvailable.value = false;
    slugValid.value = false;
  }
};

// Watch for timing changes
watch(() => timeSlot.value.days, () => {
  updateTimingData();
}, { deep: true, immediate: true });

function updateTimingData() {
  // Map days correctly: Sat=0, Sun=1, Mon=2, Tue=3, Wed=4, Thu=5, Fri=6
  const daysMapping = ['sat', 'sun', 'mon', 'tue', 'wed', 'thu', 'fri'];
  
  daysMapping.forEach((day, index) => {
    const daySettings = timeSlot.value.days[index];
    
    if (daySettings.is24Hours) {
      timingData.value[day] = '24 Hrs';
    } else if (daySettings.isClosed) {
      timingData.value[day] = 'Closed';
    } else if (daySettings.open && daySettings.close) {
      timingData.value[day] = {
        start: daySettings.open,
        end: daySettings.close
      };
    } else {
      timingData.value[day] = null;
    }
  });
}

function handle24HoursChange(dayIndex) {
  const day = timeSlot.value.days[dayIndex];
  if (day.is24Hours) {
    day.isClosed = false;
    day.open = '';
    day.close = '';
  }
  updateTimingData();
}

function handleClosedChange(dayIndex) {
  const day = timeSlot.value.days[dayIndex];
  if (day.isClosed) {
    day.is24Hours = false;
    day.open = '';
    day.close = '';
  }
  updateTimingData();
}

function canContinueTimings() {
  return timeSlot.value.days.some(day => !day.isClosed);
}

// Image validation helper
const validateImageFile = (file) => {
  const maxSize = 5 * 1024 * 1024; // 5MB
  const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
  
  if (file.size > maxSize) {
    return { isValid: false, error: 'File size exceeds 5MB limit' };
  }
  
  if (!allowedTypes.includes(file.type)) {
    return { isValid: false, error: 'Invalid file type. Only JPEG, PNG, JPG, and WebP are allowed' };
  }
  
  return { isValid: true };
};

function handlePhotoUpload(event) {
  const file = event.target.files[0];
  if (file) {
    // Validate the image file using the utility function
    const validation = validateImageFile(file);
    
    if (validation.isValid) {
      // Revoke previous preview URL if exists
      if (photoPreview.value) {
        URL.revokeObjectURL(photoPreview.value);
      }
      
      photoPreview.value = URL.createObjectURL(file);
      businessProfilePhoto.value = file;
    } else {
      // Display validation error
      showToast(validation.error, 'error');
      
      // Reset the file input
      event.target.value = '';
    }
  }
}

function removePhoto() {
  if (photoPreview.value) {
    URL.revokeObjectURL(photoPreview.value);
  }
  photoPreview.value = null;
  businessProfilePhoto.value = null;
}

// Validation Schema
const validationSchema = yup.object({
  business_name: yup.string().required('Business name is required').min(3, 'Business name must be at least 3 characters'),
  slug: yup.string()
    .required('Business slug is required')
    .matches(/^[a-zA-Z0-9_-]{3,50}$/, 'Slug must be 3-50 characters and can only contain letters, numbers, underscores, and dashes')
    .test('is-slug-available', 'This business slug is already taken', () => slugValid.value),
  business_type: yup.array().of(yup.string()).min(1, 'Please select at least one business type').required().label('Business Types'),
  name: yup.string().required('Contact person name is required').min(2, 'Contact person name must be at least 2 characters'),
  number: yup.string().required('Mobile number is required').matches(/^\d{11}$/, 'Mobile number must be 11 digits'),
  email: yup.string().email('Please enter a valid email address').optional(),
  address: yup.string().required('Address is required'),
  post_code: yup.string()
    .required('Post code is required')
    .matches(/^\d{4}$/, 'Post code must be exactly 4 digits')
    .label('Post Code'),
  location_id: yup.number().required('Please select a location').label('Location'),
});

// Submit handler
const onSubmit = async (values) => {
  isSubmitting.value = true;
  
  try {
    // if (businessProfilePhoto.value) {
    //     const validation = validateImageFile(businessProfilePhoto.value);
        
    //     if (validation.isValid) {
    //       try {
    //         // Upload profile photo separately after creating the business
    //         const formData = new FormData();
    //         formData.append('business_profile', businessProfilePhoto.value);
    //         // Use the admin backend API for profile update
    //         const photoResponse = await businessStore.update(response.data.data.id, formData);
    //         showToast('Business and profile photo created successfully!', 'success');
    //       } catch (photoError) {
    //         console.error('Error uploading profile photo:', photoError);
    //         showToast('Business created but profile photo upload failed', 'warning');
    //       }
    //     } else {
    //       console.error('Profile photo validation failed:', validation.error);
    //       showToast('Profile photo validation failed: ' + validation.error, 'warning');
    //     }
    //   }
    // Prepare FormData to handle file upload
    const formData = new FormData();
    
    // Add all form values
    Object.keys(values).forEach(key => {
      if (key === 'business_type') {
        // Handle array values
        values[key].forEach((value, index) => {
          formData.append(`${key}[${index}]`, value);
        });
      } else {
        formData.append(key, values[key]);
      }
    });
    
    // Add timing data
    formData.append('timings', JSON.stringify(timingData.value));
    
    // Add business profile photo if exists
    if (businessProfilePhoto.value) {
      const validation = validateImageFile(businessProfilePhoto.value);
      if (!validation.isValid) {
        showToast('Profile photo validation failed: ' + validation.error, 'error');
        isSubmitting.value = false;
        return;
      }
      formData.append('business_profile', businessProfilePhoto.value);
    }

    // Create the business first
    const response = await businessStore.create(formData);
    
    if (response) {
      
        showToast('Business created successfully!', 'success');
     
        
      
      router.push({ name: 'Businesses' });
    } else {
      showToast('Failed to create business', 'error');
    }
  } catch (error) {
    console.error('Error creating business:', error);
    // Handle validation errors from API
    if (error.response?.data?.errors) {
      Object.values(error.response.data.errors).forEach(errorList => {
        showToast(errorList[0], 'error');
      });
    } else {
      showToast(error.response?.data?.message || 'Error creating business. Please try again.', 'error');
    }
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<style scoped>
.section {
  padding: 1rem 0;
  border-bottom: 1px solid #e5e7eb;
}

.section:last-child {
  border-bottom: none;
}

.admin-input {
  width: 100%;
  padding: 0.5rem 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  background-color: #fff;
  font-size: 0.875rem;
  outline: none;
  transition: border-color 0.2s;
}

.admin-input:focus {
  border-color: #2563eb;
  background-color: #fff;
}

.admin-select {
  width: 100%;
  padding: 0.5rem 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  background-color: #fff;
  font-size: 0.875rem;
  outline: none;
  transition: border-color 0.2s;
}

.admin-button-primary {
  background-color: #2563eb;
  color: white;
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 0.375rem;
  cursor: pointer;
  font-weight: 500;
  transition: background-color 0.2s;
}

.admin-button-primary:hover {
  background-color: #1d4ed8;
}

.admin-button-primary:disabled {
  background-color: #9ca3af;
  cursor: not-allowed;
}

.admin-button-secondary {
  background-color: #f3f4f6;
  color: #374151;
  padding: 0.5rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  cursor: pointer;
  font-weight: 500;
  transition: background-color 0.2s;
}

.admin-button-secondary:hover {
  background-color: #e5e7eb;
}
</style>