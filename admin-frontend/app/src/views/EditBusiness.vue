<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center admin-header">
      <h1 class="text-2xl font-bold text-gray-900">Edit Business</h1>
      <router-link 
        :to="{ name: 'Businesses' }" 
        class="admin-button-secondary"
      >
        Back to Businesses
      </router-link>
    </div>

    <div class="bg-white rounded-2xl shadow-sm p-6 admin-card">
      <Form v-if="businessData" :validation-schema="validationSchema" @submit="onSubmit" class="space-y-6">
        <div class="section">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Business Details</h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Business Name *</label>
              <Field name="business_name" as="input" class="admin-input" placeholder="Enter business name" v-model="formData.business_name"/>
              <ErrorMessage name="business_name" class="text-red-500 text-xs mt-1" />
            </div>

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

        <div class="section">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Contact Details</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Contact Person *</label>
              <Field name="name" as="input" class="admin-input" v-model="formData.name"/>
              <ErrorMessage name="name" class="text-red-500 text-xs mt-1" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Mobile Number *</label>
              <div class="flex gap-2">
                <span class="inline-flex items-center px-3 bg-gray-100 border border-gray-200 rounded-l-md">+88</span>
                <Field name="number" as="input" class="admin-input rounded-l-none" v-model="formData.number" />
              </div>
              <ErrorMessage name="number" class="text-red-500 text-xs mt-1" />
            </div>
          </div>
        </div>

        <div class="section">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Location & Address</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">Full Address *</label>
              <Field name="address" as="textarea" class="admin-input min-h-[80px]" v-model="formData.address"/>
              <ErrorMessage name="address" class="text-red-500 text-xs mt-1" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Post Code *</label>
              <Field name="post_code" as="input" class="admin-input" v-model="formData.post_code" />
              <ErrorMessage name="post_code" class="text-red-500 text-xs mt-1" />
            </div>
            <div class="md:col-span-3">
              <label class="block text-sm font-medium text-gray-700 mb-1">Upazila/District *</label>
              <div class="relative location-dropdown">
                <input
                  class="admin-input"
                  placeholder="Search location..."
                  @focus="showLocationDropdown = true"
                  :value="locationSearch"
                  @input="locationSearch = $event.target.value; debouncedSearchLocations()"
                />
                <div v-if="showLocationDropdown && businessLocations.length > 0" class="absolute z-10 w-full mt-1 bg-white border rounded-md shadow-lg max-h-60 overflow-y-auto">
                  <div 
                    v-for="location in businessLocations" 
                    :key="location.id"
                    @click="selectLocation(location)"
                    class="px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm"
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

        <div class="section">
  <h2 class="text-lg font-semibold text-gray-900 mb-4">Business Profile Photo</h2>
  <div class="flex justify-center mb-4">
    <div class="relative w-60 h-60">
      
      <div 
        @click="triggerFileInput"
        class="w-full h-full border-2 border-dashed border-blue-400 rounded-xl cursor-pointer hover:bg-blue-50 transition overflow-hidden flex flex-col items-center justify-center"
      >
        <div v-if="photoPreview || (businessData && businessData.business_profile)" class="w-full h-full">
          <img :src="photoPreview || businessData.business_profile" alt="Preview" class="w-full h-full object-cover" />
        </div>

        <div v-else class="flex flex-col items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
          </svg>
          <span class="text-blue-500 font-semibold">Add Photo</span>
        </div>
      </div>

      <button 
        v-if="photoPreview || (businessData && businessData.business_profile)"
        type="button" 
        @click.stop="removePhoto" 
        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center hover:bg-red-600 shadow-lg z-20"
      >
        ×
      </button>

      <input 
        ref="fileInput"
        type="file" 
        class="hidden" 
        accept="image/*"
        @change="handlePhotoUpload"
      />
    </div>
  </div>
</div>

        <div class="flex justify-end pt-4">
          <button type="submit" class="admin-button-primary" :disabled="isSubmitting">
            {{ isSubmitting ? 'Updating Business...' : 'Update Business' }}
          </button>
        </div>
      </Form>
      
      <div v-else class="text-center py-8">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
        <p class="mt-2 text-gray-600">Loading business data...</p>
      </div>
    </div>

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
import { useRouter, useRoute } from 'vue-router';
import { Form, Field, ErrorMessage } from 'vee-validate';
import * as yup from 'yup';
import { useToast } from '../composables/useToast';
import { useLocationStore } from '../stores/location';
import { useBusinessStore } from '../stores/business';
import { useBusinessTypeStore } from '../stores/businessType';
import { businessUtilityAPI } from '../api/services';
import BusinessTypeModal from './modals/BusinessTypeModal.vue';

// --- Composables & Routing ---
const router = useRouter();
const route = useRoute();
const locationStore = useLocationStore();
const businessStore = useBusinessStore();
const businessTypeStore = useBusinessTypeStore();
const { showToast } = useToast();

// --- State Management ---
const businessId = parseInt(route.params.id);
const isSubmitting = ref(false);
const businessData = ref(null);
const showBusinessTypeModal = ref(false);

// Form Fields
const formData = ref({
  business_name: '',
  slug: '',
  business_type: [],
  name: '',
  number: '',
  alternate_number: '',
  email: '',
  address: '',
  post_code: '',
  location_id: null,
  status: 'active',
});

// Location & Slug State
const locationSearch = ref('');
const showLocationDropdown = ref(false);
const searchTimeout = ref(null);
const slugError = ref('');
const slugAvailable = ref(false);
const slugValid = ref(false);
const slugCheckTimeout = ref(null);

// Photo Upload State
const fileInput = ref(null); // Ref for the hidden input
const businessProfilePhoto = ref(null);
const photoPreview = ref(null);

// Timing State
const timingData = ref({ 
  sat: null, sun: null, mon: null, tue: null, wed: null, thu: null, fri: null 
});

// --- Computed ---
const businessLocations = computed(() => locationStore.businessLocations);

// --- Methods: Photo Handling ---
const triggerFileInput = () => {
  if (fileInput.value) {
    fileInput.value.click();
  }
};

const handlePhotoUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    const maxSize = 5 * 1024 * 1024; // 5MB
    if (file.size > maxSize) {
      showToast('File size exceeds 5MB limit', 'error');
      return;
    }

    if (photoPreview.value) {
      URL.revokeObjectURL(photoPreview.value);
    }
    
    photoPreview.value = URL.createObjectURL(file);
    businessProfilePhoto.value = file;
  }
};

const removePhoto = () => {
  if (photoPreview.value) {
    URL.revokeObjectURL(photoPreview.value);
  }
  photoPreview.value = null;
  businessProfilePhoto.value = null;
  if (fileInput.value) {
    fileInput.value.value = ''; // Reset input so same file can be picked again
  }
};

// --- Methods: Business Logic ---
const loadBusinessData = async () => {
  try {
    const response = await businessStore.getById(businessId);
    const b = response.data.data;
    businessData.value = b;
    
    // Fill Form
    formData.value = {
      business_name: b.business_name || '',
      slug: b.slug || '',
      business_type: Array.isArray(b.business_type) ? b.business_type : [],
      name: b.name || '',
      number: b.number || '',
      alternate_number: b.alternate_number || '',
      email: b.email || '',
      address: b.address || '',
      post_code: b.post_code || '',
      location_id: b.location_id || null,
      status: b.status || 'active',
    };
    
    locationSearch.value = b.location || '';
    if (b.business_profile) {
      photoPreview.value = b.business_profile;
    }
  } catch (error) {
    showToast('Failed to load business data', 'error');
    router.push({ name: 'Businesses' });
  }
};

const checkSlugAvailability = async (slugValue) => {
  if (!slugValue) return;
  if (businessData.value && businessData.value.slug === slugValue) {
    slugValid.value = true;
    slugAvailable.value = false;
    return;
  }

  clearTimeout(slugCheckTimeout.value);
  slugCheckTimeout.value = setTimeout(async () => {
    try {
      const res = await businessUtilityAPI.checkSlugAvailability(slugValue);
      if (res.data.status) {
        slugError.value = '';
        slugAvailable.value = true;
        slugValid.value = true;
      }
    } catch (error) {
      slugError.value = error.response?.data?.errors?.slug[0] || 'Slug unavailable';
      slugValid.value = false;
      slugAvailable.value = false;
    }
  }, 500);
};

// --- Methods: Location ---
const searchLocations = async () => {
  showLocationDropdown.value = true;
  await locationStore.searchLocations(locationSearch.value);
};

const debouncedSearchLocations = () => {
  clearTimeout(searchTimeout.value);
  searchTimeout.value = setTimeout(() => searchLocations(), 300);
};

const selectLocation = (location) => {
  locationSearch.value = `${location.upazila_name}, ${location.district_name}`;
  formData.value.location_id = Number(location.id);
  showLocationDropdown.value = false;
};

// --- Methods: Business Type ---
const handleBusinessTypesSelected = (selectedTypes) => {
  formData.value.business_type = selectedTypes;
  showBusinessTypeModal.value = false;
};

const removeBusinessType = (typeToRemove) => {
  formData.value.business_type = formData.value.business_type.filter(t => t !== typeToRemove);
};

// --- Lifecycle & Watchers ---
onMounted(async () => {
  document.addEventListener('click', (e) => {
    if (!e.target.closest('.location-dropdown')) showLocationDropdown.value = false;
  });
  await Promise.all([
    locationStore.searchLocations(),
    businessTypeStore.fetchAll()
  ]);
  await loadBusinessData();
});

onUnmounted(() => {
  if (photoPreview.value) URL.revokeObjectURL(photoPreview.value);
});

watch(() => formData.value.slug, (newSlug) => checkSlugAvailability(newSlug));

// --- Validation ---
const validationSchema = yup.object({
  business_name: yup.string().required('Required').min(3),
  slug: yup.string().required().test('is-slug-valid', 'Slug is taken', () => slugValid.value),
  business_type: yup.array().min(1, 'Select at least one type'),
  name: yup.string().required(),
  number: yup.string().required().matches(/^\d{11}$/, '11 digits required'),
  address: yup.string().required(),
  post_code: yup.string().required().matches(/^\d{4}$/, '4 digits required'),
  location_id: yup.number().required('Location required'),
});

// --- Submit ---
const onSubmit = async (values) => {
  isSubmitting.value = true;
  try {
    const data = new FormData();

    // 2. Append standard fields
    Object.keys(values).forEach(key => {
      if (key === 'business_type') {
        values[key].forEach((val, index) => {
          data.append(`business_type[${index}]`, val);
        });
      } else {
        data.append(key, values[key]);
      }
    });

    // 3. Append JSON and Files
    data.append('timings', JSON.stringify(timingData.value));
    
    if (businessProfilePhoto.value) {
      data.append('business_profile', businessProfilePhoto.value);
    }

    // 4. Send request (Note: Ensure the store uses POST method internally 
    // because we are using _method=PUT in FormData)
    const response = await businessStore.update(businessId, data);
    
    if (response) {
      showToast('Business updated successfully!', 'success');
      router.push({ name: 'Businesses' });
    }
  } catch (error) {
    console.error(error);
    const apiErrors = error.response?.data?.errors;
    if (apiErrors) {
      showToast(Object.values(apiErrors)[0][0], 'error');
    } else {
      showToast('Update failed', 'error');
    }
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<style scoped>
.section { padding: 1.5rem 0; border-bottom: 1px solid #e5e7eb; }
.section:last-child { border-bottom: none; }
.admin-input { width: 100%; padding: 0.6rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; }
.admin-button-primary { background: #2563eb; color: #fff; padding: 0.6rem 1.5rem; border-radius: 0.5rem; font-weight: 600; }
.admin-button-primary:disabled { opacity: 0.5; }
.admin-button-secondary { background: #f3f4f6; padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; }
</style>