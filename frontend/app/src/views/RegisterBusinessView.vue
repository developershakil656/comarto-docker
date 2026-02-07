<template>
  <div>
    <!-- <div class="hidden md:block">
      <TopHeader />
    </div> -->
    

    <div class="min-h-screen bg-gray-50 transition-colors duration-300 py-6">
    <div class="register-business-container max-w-6xl mx-auto flex flex-col md:flex-row bg-white rounded-xl shadow-md">
      <!-- Left: Mobile Preview Placeholder -->
      <div class="hidden md:flex w-1/2 flex-col items-center justify-center p-8 border-r border-gray-100">
        <!-- Replace with actual image if available -->
        <div class="w-80 h-[520px] bg-gray-100 rounded-2xl flex items-center justify-center relative overflow-hidden">
          <span class="text-gray-400 italic">Business Preview</span>
        </div>
      </div>

      <!-- Right: Stepper and Form (Full width on mobile) -->
      <div class="w-full md:w-1/2 p-6 md:p-10">
        <!-- Stepper/Progress Bar -->
        <div class="flex justify-between items-center mb-8">
          <div v-for="(step, idx) in steps" :key="idx" class="flex-1 flex flex-col items-center">
            <div 
              :class="[
                'w-8 h-8 rounded-full flex items-center justify-center transition-all duration-200',
                currentStep >= idx ? 'bg-primary text-white cursor-pointer hover:bg-primary/80' : 'bg-gray-200 text-gray-600',
                idx <= currentStep ? 'cursor-pointer' : 'cursor-not-allowed opacity-50'
              ]"
              @click="goToStep(idx)"
            >
              {{ idx + 1 }}
            </div>
            <span 
              class="mt-2 text-xs text-center transition-all duration-200 leading-tight" 
              :class="[
                currentStep >= idx ? 'font-bold text-primary' : '',
                idx <= currentStep ? 'cursor-pointer hover:text-primary/70' : 'cursor-not-allowed opacity-50'
              ]" 
              @click="goToStep(idx)"
            >
              {{ step }}
            </span>
          </div>
        </div>

        <!-- Step Content -->
        <div>
          <div v-if="currentStep === 0">
            <!-- Step 1: Business Details -->
            <h2 class="text-lg md:text-2xl font-bold mb-6">Enter Your Business Details</h2>
            <Form :validation-schema="validationSchema" @submit="onBusinessDetailsSubmit" class="space-y-4">
              <!-- Business Name -->
              <div>
                <label class="block text-sm font-medium">Business Name *</label>
                <Field name="business_name" as="input" class="input" placeholder="Enter your business name" v-model="businessDetails.business_name"/>
                <ErrorMessage name="business_name" class="text-red-500 text-xs" />
              </div>

              <!-- Business Type -->
              <div>
                <label class="block text-sm font-medium">Business Type *</label>
                <div class="relative">
                  <button 
                    type="button"
                    @click="showBusinessTypeModal = true"
                    class="input text-left cursor-pointer flex items-center justify-between"
                    :class="businessDetails.business_types && businessDetails.business_types.length > 0 ? 'bg-white' : 'bg-gray-50'"
                  >
                    <span v-if="businessDetails.business_types && businessDetails.business_types.length > 0" class="text-gray-900">
                      {{ businessDetails.business_types.join(', ') }}
                    </span>
                    <span v-else class="text-gray-500">Select business types...</span>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                  </button>
                  <!-- Selected business types display -->
                  <div v-if="businessDetails.business_types && businessDetails.business_types.length > 0" class="mt-2 flex flex-wrap gap-2">
                    <span 
                      v-for="type in businessDetails.business_types" 
                      :key="type"
                      class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-primary/10 text-primary"
                    >
                      {{ type }}
                      <button 
                        type="button"
                        @click="removeBusinessType(type)"
                        class="ml-1 text-primary hover:text-primary/70"
                      >
                        ×
                      </button>
                    </span>
                  </div>
                </div>
                <Field name="business_types" type="hidden" v-model="businessDetails.business_types" />
                <ErrorMessage name="business_types" class="text-red-500 text-xs" />
              </div>

              <!-- Location -->
              <div>
                <label for="business-location-search" class="block text-sm font-medium">Upazila/District *</label>
                <div class="relative location-dropdown">
                  <input
                    class="input"
                    id="business-location-search"
                    name="business-location-search"
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
              </div>
              <Field name="location_id" type="hidden" v-model="businessDetails.location_id" />
              <ErrorMessage name="location_id" class="text-red-500 text-xs" />

              <!-- Full Address -->
              <div>
                <label class="block text-sm font-medium">Full Address *</label>
                <Field name="address" as="textarea" class="input min-h-[80px] resize-none" placeholder="Enter complete address" v-model="businessDetails.address"/>
                <ErrorMessage name="address" class="text-red-500 text-xs" />
              </div>

              <!-- Post Code -->
              <div>
                <label class="block text-sm font-medium">Post Code *</label>
                <Field 
                  name="post_code" 
                  as="input" 
                  type="text"
                  maxlength="4"
                  pattern="[0-9]{4}"
                  class="input" 
                  placeholder="Enter 4-digit post code" 
                  v-model="businessDetails.post_code"
                />
                <ErrorMessage name="post_code" class="text-red-500 text-xs" />
              </div>

              <button type="submit" class="w-full mt-6 py-3 bg-primary text-white text-lg font-semibold rounded-lg shadow hover:bg-primary/85 transition" :disabled="isCreatingBusiness">
                {{ isCreatingBusiness ? 'Creating Business...' : 'Save and Continue' }}
              </button>
            </Form>
          </div>
          <div v-else-if="currentStep === 1">
            <!-- Step 2: Contact Details -->
            <h2 class="text-lg md:text-2xl font-bold mb-6">Add Contact Details</h2>
            <Form :validation-schema="contactValidationSchema" @submit="onContactDetailsSubmit" class="space-y-4">
              <!-- Contact Person -->
              <div>
                <label class="block text-sm font-medium">Contact Person *</label>
                <Field name="contact_person" as="input" class="input" placeholder="Enter contact person name" v-model="contactDetails.contact_person"/>
                <ErrorMessage name="contact_person" class="text-red-500 text-xs" />
              </div>

              <!-- Mobile Number -->
              <div>
                <label class="block text-sm font-medium">Mobile Number *</label>
                <div class="flex gap-2">
                  <span class="inline-flex items-center px-3 bg-gray-100 border border-gray-200 rounded-l-md text-gray-700">+88</span>
                  <Field 
                    name="mobile_number" 
                    as="input" 
                    type="text"
                    class="input rounded-l-none" 
                    placeholder="Mobile Number" 
                    :disabled="true"
                    v-model="contactDetails.mobile_number"
                  />
                </div>
                <ErrorMessage name="mobile_number" class="text-red-500 text-xs" />
              </div>

              

              <!-- Email -->
              <div>
                <label class="block text-sm font-medium">Email</label>
                <Field name="email" as="input" type="email" class="input" placeholder="Enter email address" v-model="contactDetails.email" />
                <ErrorMessage name="email" class="text-red-500 text-xs" />
              </div>

              <button type="submit" class="w-full mt-6 py-3 bg-primary text-white text-lg font-semibold rounded-lg shadow hover:bg-primary/85 transition" :disabled="isCreatingBusiness">Save and Continue</button>
            </Form>
          </div>
          <div v-else-if="currentStep === 2">
            <!-- Step 3: Business Timings -->
            <h2 class="text-lg md:text-2xl font-bold mb-2">Add Business Timings</h2>
            <p class="text-gray-500 mb-6">Let your customers know when you are open for business</p>
            <form @submit.prevent="handleTimingNextStep" class="space-y-4">
              <div class="space-y-4">
                <div v-for="(day, idx) in daysOfWeek" :key="day" class="border rounded-lg p-4">
                  <div class="flex items-center justify-between mb-3">
                    <label class="text-sm font-bold">{{ day }}</label>
                    <div class="flex items-center gap-2">
                      <label class="flex items-center gap-2 text-sm">
                        <input 
                          type="checkbox" 
                          id="24hours-{{ day }}"
                          name="24hours-{{ day }}"
                          v-model="timeSlot.days[idx].is24Hours" 
                          @change="handle24HoursChange(idx)"
                        /> 24 Hours
                      </label>
                      <label class="flex items-center gap-2 text-sm">
                        <input 
                          type="checkbox" 
                          id="closed-{{ day }}"
                          name="closed-{{ day }}"
                          v-model="timeSlot.days[idx].isClosed" 
                          @change="handleClosedChange(idx)"
                        /> Closed
                      </label>
                    </div>
                  </div>
                  
                  <div v-if="!timeSlot.days[idx].is24Hours && !timeSlot.days[idx].isClosed" class="flex gap-4 items-end">
                    <div class="flex-1">
                      <label class="block text-sm font-medium">Open at</label>
                      <select 
                        v-model="timeSlot.days[idx].open" 
                        id="open-{{ day }}"
                        name="open-{{ day }}"
                        class="input" 
                        @change="updateTimingData"
                      >
                        <option value="">Select time</option>
                        <option v-for="time in timeOptions" :key="time" :value="time">{{ time }}</option>
                      </select>
                    </div>
                    <div class="flex-1">
                      <label class="block text-sm font-medium">Close at</label>
                      <select 
                        v-model="timeSlot.days[idx].close" 
                        id="close-{{ day }}"
                        name="close-{{ day }}"
                        class="input" 
                        @change="updateTimingData"
                      >
                        <option value="">Select time</option>
                        <option v-for="time in timeOptions" :key="time" :value="time">{{ time }}</option>
                      </select>
                    </div>
                  </div>
                  
                  <div v-else-if="timeSlot.days[idx].is24Hours" class="text-green-600 font-medium">
                    Open 24 Hours
                  </div>
                  
                  <div v-else-if="timeSlot.days[idx].isClosed" class="text-red-600 font-medium">
                    Closed
                  </div>
                </div>
              </div>
              
              <p v-if="timingWarning" class="text-red-500 text-sm ml-2">{{ timingWarning }}</p>
              <button type="submit" class="w-full mt-6 py-3 bg-primary text-white text-lg font-semibold rounded-lg shadow hover:bg-primary/85 transition" :disabled="!canContinueTimings() || isCreatingBusiness">Save and Continue</button>
            </form>
          </div>
          <div v-else-if="currentStep === 3">
            <!-- Step 4: Add Photos -->
            <div class="flex flex-col items-center justify-center h-full">
              <h2 class="text-lg md:text-2xl font-bold mb-2">Add Photos</h2>
              <p class="text-gray-500 mb-6">Add your business profile photo <span class="text-red-500">*</span></p>
              
              <!-- Error message for photo requirement -->
              <div v-if="photoError" class="text-red-500 text-sm mb-4 text-center">
                {{ photoError }}
              </div>
              <!-- <p class="text-gray-500 mb-6">Showcase photos of your business to look authentic</p> -->
              <div class="w-full flex justify-center mb-8">
                <label class="flex flex-col items-center justify-center w-60 h-60 border-2 border-dashed border-blue-400 rounded-xl cursor-pointer hover:bg-blue-50 transition">
                  <div v-if="photoPreview" class="w-full h-full relative">
                    <OptimizedImage :src="photoPreview" alt="Business Profile Preview" class="w-full h-full object-cover rounded-xl" />
                    <button 
                      @click.stop="removePhoto" 
                      class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600"
                    >
                      ×
                    </button>
                  </div>
                  <div v-else>
                  <CameraIcon class="h-10 w-10 mx-auto text-blue-400 mb-2" />
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
              
              <!-- Business Slug Field -->
              <div class="mb-6 w-full max-w-md">
                <label class="block text-sm font-medium mb-2">Business Slug * (This cannot be changed later)</label>
                <input 
                  v-model="businessDetails.slug" 
                  type="text" 
                  class="input w-full" 
                  placeholder="Enter business slug" 
                  @input="onSlugChange"
                />
                <div v-if="slugError" class="text-red-500 text-xs mt-1">
                  {{ slugError }}
                </div>
                <div v-if="slugAvailable" class="text-green-500 text-xs mt-1">
                  ✓ This slug is available
                </div>
              </div>
              
              
              <button 
                @click="handlePhotoNextStep" 
                class="w-full sm:w-1/2 py-3 bg-primary text-white text-lg font-semibold rounded-lg shadow hover:bg-primary/85 transition" 
                :disabled="isCreatingBusiness || !slugValid"
              >
                {{ isCreatingBusiness ? 'Creating Business...' : 'Create Business' }}
              </button>
            </div>
          </div>
        </div>

        <!-- Navigation Buttons for other steps -->
        <!-- <div v-if="currentStep > 0" class="flex justify-between mt-8">
          <button @click="prevStep" class="px-4 py-2 bg-gray-300 rounded">Back</button>
          <div class="flex-1"></div>
          <button v-if="currentStep < steps.length - 1" @click="nextStep" class="px-4 py-2 bg-primary text-white rounded">Save and Continue</button>
          <button v-else class="px-4 py-2 bg-green-600 text-white rounded">Finish</button>
        </div> -->
      </div>
    </div>
  </div>

  <MobileBottomNavigationVue/>


    <!-- Business Type Modal -->
    <BusinessTypeModal 
      :open="showBusinessTypeModal" 
      :selected-business-types="businessDetails.business_types"
      mode="registration"
      @close="showBusinessTypeModal = false"
      @business-types-selected="handleBusinessTypesSelected"
    />
  </div>
</template>

<script setup>
import { push } from 'notivue';
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import { useStore } from 'vuex';
import BusinessTypeModal from '@/views/business/modals/BusinessTypeModal.vue';
import { Form, Field, ErrorMessage, defineRule, configure, useForm } from 'vee-validate';
import { getCurrentInstance } from 'vue';
import { required, min } from '@vee-validate/rules';
import axios from 'axios';
import { CameraIcon } from '@heroicons/vue/24/outline';
import MobileBottomNavigationVue from '@/components/common/MobileBottomNavigation.vue';
import OptimizedImage from '@/components/common/OptimizedImage.vue';
import { validateImageFile } from '@/utils/imageValidation.js';

const router = useRouter();
const store = useStore();

const { appContext } = getCurrentInstance();
const yup = appContext.config.globalProperties.$yup;


const steps = [
  'Business Details',
  'Contact Details',
  'Business Timings',
  'Add Photos',
];
const currentStep = ref(0);
const isCreatingBusiness = ref(false);

const businessDetails = ref({
  business_name: '',
  business_types: [], // Changed from business_type_id to business_types array
  location_id: null,
  address: '',
  post_code: '',
  slug: '',
});

// Business Type Modal
const showBusinessTypeModal = ref(false);

// Location Search
const locationSearch = ref('');
const showLocationDropdown = ref(false);
const selectedLocation = ref(null);
const searchTimeout = ref(null);

const contactDetails = ref({
  contact_person: '',
  mobile_number: '',
  email: '',
});

// Photo upload state
const businessProfilePhoto = ref(null);
const photoPreview = ref(null);
const photoError = ref('');

// Slug validation state
const slugError = ref('');
const slugAvailable = ref(false);
const slugValid = ref(false);

// Debounce utility and timeout reference
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
let timingWarning = ref('');

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

// Computed properties
const businessLocations = computed(() => store.getters.businessLocations);

// Methods
const searchLocations = async () => {
  showLocationDropdown.value = true;
  await store.dispatch('fetchBusinessLocations', locationSearch.value);
};

const debouncedSearchLocations = () => {
  clearTimeout(searchTimeout.value);
  searchTimeout.value = setTimeout(() => {
    searchLocations();
  }, 300);
};

const handleBusinessTypesSelected = (selectedTypes) => {
  businessDetails.value.business_types = selectedTypes;
  showBusinessTypeModal.value = false;
};

const removeBusinessType = (typeToRemove) => {
  businessDetails.value.business_types = businessDetails.value.business_types.filter(
    type => type !== typeToRemove
  );
};

const selectLocation = (location) => {
  selectedLocation.value = location;
  locationSearch.value = `${location.upazila_name}, ${location.district_name}`;
  businessDetails.value.location_id = location.id;
  showLocationDropdown.value = false;
};

const handleClickOutside = (event) => {
  // Close location dropdown
  if (!event.target.closest('.location-dropdown')) {
    showLocationDropdown.value = false;
  }
};

// Initialize mobile number securely from store
onMounted(async () => {
  const authedUser = store.getters.user;
  if (!authedUser?.number) {
    // If somehow here without a verified number, redirect back to Free Listing
    router.push({ name: 'free-listing' });
    return;
  }
  contactDetails.value.mobile_number = authedUser.number;

  // Fetch initial business locations
  await store.dispatch('fetchBusinessLocations', '');

  // Add click outside listener
  document.addEventListener('click', handleClickOutside);

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

// Watch for timing changes
watch(() => timeSlot.value.days, () => {
  updateTimingData();
}, { deep: true, immediate: true });

const nextStep = () => {
  if (validateCurrentStep()) {
    if (currentStep.value < 3) {
      currentStep.value++;
      push.info(`Moving to step ${currentStep.value + 1}`);
    }
  }
};

const prevStep = () => {
  if (currentStep.value > 0) {
    currentStep.value--;
    push.info(`Moving to step ${currentStep.value + 1}`);
  }
};

function goToStep(stepIndex) {
  // Only allow navigation to completed steps or current step
  if (stepIndex <= currentStep.value) {
    currentStep.value = stepIndex;
  }
}

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

function handleTimingNextStep() {
  if (!canContinueTimings()) {
    timingWarning.value = 'Please select at least one day to be open.';
    return;
  }
  timingWarning.value = '';
  
  // Timing data is already collected by updateTimingData() function
  nextStep();
}

function validateCurrentStep() {
  // Validate based on the current step
  if (currentStep.value === 0) {
    const nameOk = typeof businessDetails.value.business_name === 'string' && businessDetails.value.business_name.trim().length >= 3;
    const typesOk = Array.isArray(businessDetails.value.business_types) && businessDetails.value.business_types.length > 0;
    const locationOk = !!businessDetails.value.location_id;
    const addressOk = typeof businessDetails.value.address === 'string' && businessDetails.value.address.trim().length > 0;
    const postOk = /^\d{4}$/.test(String(businessDetails.value.post_code || ''));
    const ok = nameOk && typesOk && locationOk && addressOk && postOk;
    if (!ok) {
      push.warning('Please complete all required business details correctly.');
    }
    return ok;
  }
  
  if (currentStep.value === 1) {
    const contactOk = typeof contactDetails.value.contact_person === 'string' && contactDetails.value.contact_person.trim().length >= 2;
    const mobileOk = !!contactDetails.value.mobile_number;
    const emailVal = String(contactDetails.value.email || '').trim();
    const emailOk = emailVal === '' ? true : /.+@.+\..+/.test(emailVal);
    const ok = contactOk && mobileOk && emailOk;
    if (!ok) {
      push.warning('Please complete contact details correctly.');
    }
    return ok;
  }
  
  if (currentStep.value === 2) {
    const ok = canContinueTimings();
    if (!ok) {
      push.warning('Please select at least one open day or set 24 hours.');
    }
    // Validate slug format
  if (!businessDetails.value.slug) {
    // If empty, set to slugified business name
    if (businessDetails.value.business_name) {
      businessDetails.value.slug = slugify(businessDetails.value.business_name);
      onSlugChange(); // Use debounced validation
    }
  }
    return ok;
  }
  
  if (currentStep.value === 3) {
    const ok = !!businessProfilePhoto.value;
    if (!ok) {
      push.warning('Business profile photo is required.');
    }
    return ok;
  }
  
  return true;
}

defineRule('required', required);
defineRule('min', min);

const validationSchema = yup.object({
  business_name: yup.string().required('Business name is required').min(3, 'Business name must be at least 3 characters'),
  business_types: yup.array().of(yup.string()).min(1, 'Please select at least one business type').required().label('Business Types'),
  location_id: yup.string().required('Please select a location').label('Location'),
  address: yup.string().required('Address is required'),
  post_code: yup.string()
    .required('Post code is required')
    .matches(/^\d{4}$/, 'Post code must be exactly 4 digits')
    .label('Post Code'),
});

const contactValidationSchema = yup.object({
  contact_person: yup.string().required('Contact person name is required').min(2, 'Contact person name must be at least 2 characters'),
  mobile_number: yup.string().required('Mobile number is required'),
  email: yup.string().email('Please enter a valid email address').optional(),
});

function onBusinessDetailsSubmit(values) {
  // Update businessDetails with validated values
  businessDetails.value = { ...businessDetails.value, ...values };
  nextStep();
}

function onContactDetailsSubmit(values) {
  // Update contactDetails with validated values
  contactDetails.value = { ...contactDetails.value, ...values };
  
  // Continue to next step without creating business yet
  nextStep();
}

const createBusiness = async () => {
  if (!validateCurrentStep()) {
    return;
  }
  
  isCreatingBusiness.value = true;
  push.info('Creating your business...');
  
  try {
    // Merge businessDetails and contactDetails
    const businessData = {
      name: contactDetails.value.contact_person,
      business_name: businessDetails.value.business_name,
      slug: businessDetails.value.slug, // Add the business slug
      number: contactDetails.value.mobile_number,
      email: contactDetails.value.email,
      address: businessDetails.value.address,
      location_id: businessDetails.value.location_id,
      post_code: businessDetails.value.post_code,
      business_type: businessDetails.value.business_types,
      timings: timingData.value, // Add the business_details structure
    };


    const response = await axios.post('/user/my/business', businessData);
    
    if (response.data.status) {
      
      // Upload profile photo if provided
      if (businessProfilePhoto.value) {
        // Validate the image before uploading
        const validation = validateImageFile(businessProfilePhoto.value);
        
        if (validation.isValid) {
          try {
            const formData = new FormData();
            formData.append('business_profile', businessProfilePhoto.value);
            formData.append('_method', 'PUT');
            await axios.post('/user/business/profile', formData);
            push.success('Business profile photo uploaded successfully!');
          } catch (photoError) {
            console.error('Error uploading profile photo:', photoError);
            push.warning('Business created but profile photo upload failed');
          }
        } else {
          console.error('Profile photo validation failed:', validation.error);
          push.warning('Profile photo validation failed: ' + validation.error);
        }
      }
      
      push.success('Business created successfully!');
      
      // Ensure store reflects new business before navigating (route guard checks this)
      try {
        await store.dispatch('fetchUser');
      } catch (e) {
        console.warn('Failed to refresh user after business creation:', e?.message || e);
      }
      try {
        const myBiz = await store.dispatch('fetchMyBusiness');
        if (myBiz) {
          const updatedUser = { ...(store.getters.user || {}), business: myBiz };
          store.commit('SET_USER', updatedUser);
        }
      } catch (e) {
        console.warn('Failed to fetch my business after creation:', e?.message || e);
      }
      
      // Redirect to business dashboard
      router.push({ name: 'business-dashboard' });
    } else {
      console.error('Failed to create business:', response.data.message);
      push.error('Failed to create business: ' + response.data.message);
    }
  } catch (error) {
    console.error('Error creating business:', error);
    push.error(error.response.data.message || 'Error creating business. Please try again.');
  } finally {
    isCreatingBusiness.value = false;
  }
}

// Removed base64 helper; using FormData

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
      photoError.value = ''; // Clear previous error
    } else {
      // Display validation error
      photoError.value = validation.error;
      
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
  photoError.value = 'Business profile photo is required.';
}

function onSlugChange() {
  // Reset validation state
  slugValid.value = false;
  slugAvailable.value = false;
  
  const slug = businessDetails.value.slug;
  
  // Validate slug format (alpha_dash, min 3, max 50)
  const slugRegex = /^[a-zA-Z0-9_-]{3,50}$/;
  if (!slugRegex.test(slug)) {
    slugError.value = 'Slug must be 3-50 characters and can only contain letters, numbers, underscores, and dashes.';
    slugAvailable.value = false;
    slugValid.value = false;
    return;
  }
  
  // Clear any existing timeout
  if (slugCheckTimeout.value) {
    clearTimeout(slugCheckTimeout.value);
  }
  
  // Set a new timeout to check slug availability after 500ms
  slugCheckTimeout.value = setTimeout(() => {
    checkSlugAvailability();
  }, 500);
}

function slugify(text) {
  return text
    .toString()
    .toLowerCase()
    .trim()
    .replace(/[\s\W-]+/g, '-')
    .replace(/^-+|-+$/g, '');
}

async function checkSlugAvailability() {
  const slug = businessDetails.value.slug;
  
  if (!slug) return;
  
  try {
    // Call the API to check slug availability via Vuex
    const response = await store.dispatch('checkSlugAvailability', { slug });
    
    if (response.status) {
      // Slug is available
      slugError.value = '';
      slugAvailable.value = true;
      slugValid.value = true;
    } else {
      // Handle validation errors
      if (response.data && response.data.slug) {
        slugError.value = response.data.slug[0];
      } else {
        slugError.value = response.message || 'Slug is not available';
      }
      slugAvailable.value = false;
      slugValid.value = false;
    }
  } catch (error) {
    console.error('Error checking slug availability:', error);
    slugError.value = 'Error checking slug availability. Please try again.';
    slugAvailable.value = false;
    slugValid.value = false;
  }
}

function handlePhotoNextStep() {
  if (!businessProfilePhoto.value) {
    photoError.value = 'Business profile photo is required.';
    return;
  }
  
  // Validate the image before proceeding
  const validation = validateImageFile(businessProfilePhoto.value);
  if (!validation.isValid) {
    photoError.value = validation.error;
    return;
  }
  
  if (!slugValid.value) {
    slugError.value = 'Please enter a valid business slug.';
    return;
  }

  // Create business with photo
  createBusiness();
}
</script>

<style scoped>
.input {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  background: #f9fafb;
  font-size: 1rem;
  outline: none;
  transition: border 0.2s, background 0.3s, color 0.3s;
}
.input:focus {
  border-color: #2563eb;
  background: #fff;
}
textarea.input {
  min-height: 80px;
  resize: vertical;
  font-family: inherit;
}
.register-business-container {
  min-height: 600px;
  transition: box-shadow 0.3s, background 0.3s, color 0.3s;
}
</style>