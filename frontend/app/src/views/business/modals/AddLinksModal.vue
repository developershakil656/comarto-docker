<template>
  <Transition name="modal" appear>
    <div v-if="open" class="fixed inset-0 z-[60] flex items-stretch md:items-center justify-center bg-white md:bg-black md:bg-opacity-30 p-0 md:p-4" style="min-height: 100vh;" @click="$emit('close')">
      <Transition name="modal-content" appear>
        <div class="bg-white w-full h-full md:h-auto md:rounded-xl md:shadow-lg md:max-w-lg md:max-h-[90vh] overflow-y-auto" @click.stop>
          <MobileModalHeader title="Add Links" @back="$emit('close')" />
          <div class="p-4 md:p-8 relative">
            <button @click="$emit('close')" class="hidden md:block absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors">
              <XMarkIcon class="w-6 h-6" />
            </button>
            <h2 class="hidden md:block text-xl font-semibold mb-6 text-center">
              <slot> Add Links </slot>
            </h2>
            <Form @submit="onSubmit" v-slot="{ errors }" class="rounded-2xl shadow-lg px-4 py-6 min-h-full">
              <!-- Website -->
              <div class="relative group mb-6">
                <span class="absolute left-4 top-6 -translate-y-1/2 text-gray-400 pointer-events-none">
                  <GlobeAltIcon class="w-5 h-5" />
                </span>
                <Field 
                  v-model="form.website" 
                  name="website"
                  type="text" 
                  id="website"
                  :class="[
                    'block w-full rounded-lg border pl-12 pr-4 py-3 focus:border-primary focus:ring-blue-200 outline-none transition placeholder-transparent text-base',
                    { 'border-red-300 focus:border-red-500': errors.website, 'border-gray-300': !errors.website }
                  ]"
                  placeholder="Website"
                  rules="url" />
                <label for="website"
                  class="absolute left-12 z-10 -translate-y-1/2 bg-white text-sm transition-all duration-200 pointer-events-none group-focus-within:top-0 group-focus-within:text-xs group-focus-within:left-8 group-focus-within:-translate-y-1.5"
                  :class="{ 
                    'top-0 text-xs left-8 text-primary': form.website && !errors.website, 
                    'top-0 text-xs left-8 text-red-500': errors.website,
                    'top-1/2 text-gray-500': !form.website && !errors.website 
                  }">
                  Website
                </label>
                <!-- Error Message -->
                <div v-if="errors.website" class="mt-1 text-red-500 text-sm flex items-center">
                  <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                  {{ errors.website }}
                </div>
              </div>
              <!-- Facebook Page -->
              <div class="relative group mb-6">
                <span class="absolute left-4 top-6 -translate-y-1/2 text-gray-400 pointer-events-none">
                  <LinkIcon class="w-5 h-5" />
                </span>
                <Field 
                  v-model="form.facebook" 
                  name="facebook"
                  type="text" 
                  id="facebook"
                  :class="[
                    'block w-full rounded-lg border pl-12 pr-4 py-3 focus:border-primary focus:ring-blue-200 outline-none transition placeholder-transparent text-base',
                    { 'border-red-300 focus:border-red-500': errors.facebook, 'border-gray-300': !errors.facebook }
                  ]"
                  placeholder="Facebook Page"
                  rules="url" />
                <label for="facebook"
                  class="absolute left-12 z-10 -translate-y-1/2 bg-white text-sm transition-all duration-200 pointer-events-none group-focus-within:top-0 group-focus-within:text-xs group-focus-within:left-8 group-focus-within:-translate-y-1.5"
                  :class="{ 
                    'top-0 text-xs left-8 text-primary': form.facebook && !errors.facebook, 
                    'top-0 text-xs left-8 text-red-500': errors.facebook,
                    'top-1/2 text-gray-500': !form.facebook && !errors.facebook 
                  }">
                  Facebook Page
                </label>
                <!-- Error Message -->
                <div v-if="errors.facebook" class="mt-1 text-red-500 text-sm flex items-center">
                  <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                  {{ errors.facebook }}
                </div>
              </div>
              <!-- Map Direction -->
              <div class="relative group mb-6">
                <span class="absolute left-4 top-6 -translate-y-1/2 text-gray-400 pointer-events-none">
                  <MapPinIcon class="w-5 h-5" />
                </span>
                <Field 
                  v-model="form.mapDirection" 
                  name="mapDirection"
                  type="text" 
                  id="mapDirection"
                  :class="[
                    'block w-full rounded-lg border pl-12 pr-4 py-3 focus:border-primary focus:ring-blue-200 outline-none transition placeholder-transparent text-base',
                    { 'border-red-300 focus:border-red-500': errors.mapDirection, 'border-gray-300': !errors.mapDirection }
                  ]"
                  placeholder="Map Direction"
                  rules="url" />
                <label for="mapDirection"
                  class="absolute left-12 z-10 -translate-y-1/2 bg-white text-sm transition-all duration-200 pointer-events-none group-focus-within:top-0 group-focus-within:text-xs group-focus-within:left-8 group-focus-within:-translate-y-1.5"
                  :class="{ 
                    'top-0 text-xs left-8 text-primary': form.mapDirection && !errors.mapDirection, 
                    'top-0 text-xs left-8 text-red-500': errors.mapDirection,
                    'top-1/2 text-gray-500': !form.mapDirection && !errors.mapDirection 
                  }">
                  Map Direction
                </label>
                <!-- Error Message -->
                <div v-if="errors.mapDirection" class="mt-1 text-red-500 text-sm flex items-center">
                  <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                  {{ errors.mapDirection }}
                </div>
              </div>
              <!-- Error Message -->
              <div v-if="error" class="text-red-500 text-sm flex items-center">
                <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                {{ error }}
              </div>
              
              <button type="submit" :disabled="loading" class="w-full bg-primary hover:bg-primary/85 text-white font-semibold py-2 rounded-md transition disabled:opacity-60 disabled:cursor-not-allowed">
                <span v-if="loading" class="flex items-center justify-center">
                  <ArrowPathIcon class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" />
                  Saving...
                </span>
                <span v-else>
                  Save Links
                </span>
              </button>
            </Form>
          </div>
        </div>
      </Transition>
    </div>
  </Transition>
</template>

<script>
import { GlobeAltIcon, LinkIcon, XMarkIcon, MapPinIcon, ExclamationCircleIcon, ArrowPathIcon } from '@heroicons/vue/24/outline';
import { Form, Field } from 'vee-validate';
import { push } from 'notivue';
import { url } from '@vee-validate/rules';
import MobileModalHeader from '@/components/common/MobileModalHeader.vue';
import { useModalScroll } from '@/composables/useModalScroll';

export default {
  name: 'AddLinksModal',
  components: {
    GlobeAltIcon,
    LinkIcon,
    XMarkIcon,
    MapPinIcon,
    ExclamationCircleIcon,
    ArrowPathIcon,
    Form,
    Field,
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
    initialLinks: {
      type: Object,
      default: () => ({})
    }
  },
  data() {
    return {
      form: {
        website: '',
        facebook: '',
        mapDirection: ''
      },
      error: '',
      fieldErrors: {
        website: '',
        facebook: '',
        mapDirection: ''
      },
      loading: false
    }
  },

  watch: {
    open(newVal) {
      if (newVal) {
        this.openModal('add-links-modal');
        this.initializeForm();
      } else {
        this.closeModal('add-links-modal');
      }
    },
    initialLinks: {
      handler() {
        if (this.open) {
          this.initializeForm();
        }
      },
      deep: true
    }
  },

  methods: {

    initializeForm() {
      this.form.website = this.initialLinks.website || '';
      this.form.facebook = this.initialLinks.facebook || '';
      this.form.mapDirection = this.initialLinks.direction || '';
      this.error = '';
      this.fieldErrors = {
        website: '',
        facebook: '',
        mapDirection: ''
      };
    },

    async onSubmit(values) {
      try {
        this.error = '';
        this.fieldErrors = {
          website: '',
          facebook: '',
          mapDirection: ''
        };
        
        // Update business details with links data
        await this.$store.dispatch('updateBusinessDetails', {
          website: this.form.website,
          facebook: this.form.facebook,
          direction: this.form.mapDirection
        });
        
        push.success('Business links updated successfully!');
        
        // Close the modal
        this.$emit('close');
      } catch (error) {
        console.error('Error updating business links:', error);
        
        // Handle API validation errors
        if (error.response && error.response.data && error.response.data.data) {
          const validationErrors = error.response.data.data;
          
          // Extract field-specific errors
          if (validationErrors.website) {
            this.fieldErrors.website = validationErrors.website[0];
          }
          if (validationErrors.facebook) {
            this.fieldErrors.facebook = validationErrors.facebook[0];
          }
          if (validationErrors.direction) {
            this.fieldErrors.mapDirection = validationErrors.direction[0];
          }
          
          // If there are field errors, don't show the general error
          if (Object.values(this.fieldErrors).some(err => err)) {
            return;
          }
        }
        
        push.error('Failed to update business links. Please try again.');
      }
    }
  }
}
</script> 