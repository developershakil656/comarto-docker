<template>
  <Transition name="modal" appear>
    <div v-if="open" class="fixed inset-0 z-[60] flex items-stretch md:items-center justify-center bg-white md:bg-black md:bg-opacity-30 p-0 md:p-4" style="min-height: 100vh;" @click="$emit('close')">
      <Transition name="modal-content" appear>
        <div class="bg-white w-full h-full md:h-auto md:rounded-xl md:shadow-lg md:max-w-lg md:max-h-[90vh] overflow-y-auto" @click.stop>
          <MobileModalHeader title="Business Type" @back="$emit('close')" />
          <div class="p-4 md:p-8 relative">
            <button @click="$emit('close')" class="hidden md:block absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors">
              <XMarkIcon class="w-6 h-6" />
            </button>
            <h2 class="hidden md:block text-xl font-semibold mb-6 text-center">
              <slot> Business Type </slot>
            </h2>
            <form @submit.prevent="onSubmit" class="space-y-6 rounded-2xl shadow-lg px-4 py-6 min-h-full">
              <!-- Business Type (Multi-select) -->
              <div>
                <div class="flex text-sm md:text-base font-medium justify-between mb-4 rounded-md px-2 py-2 bg-primary/20">
                  <label class="flex items-center gap-2">
                    <BriefcaseIcon class="w-5 h-5" />
                    Business Type
                  </label>
                  <button type="button" class="flex items-center gap-2 select-none" @click="toggleSelectAll">
                    <span class="">Select All</span>
                    <input type="checkbox" :checked="areAllSelected" class="accent-primary h-5 w-5 rounded border-gray-300 transition-all duration-200 focus:ring-2 focus:ring-primary/50 cursor-pointer" />
                  </button>
                </div>
                <div v-for="type in businessTypeOptions" :key="type">
                  <label class="flex items-center justify-between border hover:bg-primary/10 rounded-md px-2 py-2 mb-2 transition cursor-pointer">  
                    <span class="text-sm md:text-base font-medium text-gray-900 capitalize">{{ type }}</span>
                    <input type="checkbox" :value="type" v-model="form.businessTypes" class="accent-primary h-5 w-5 rounded border-gray-300 transition-all duration-200 focus:ring-2 focus:ring-primary/50" />
                  </label>
                </div>
              </div>
              
              <!-- Error Message -->
              <div v-if="error" class="bg-red-50 border border-red-200 rounded-md p-3">
                <p class="text-red-600 text-sm">{{ error }}</p>
              </div>
              
              <button type="submit" :disabled="loading" class="w-full bg-primary hover:bg-primary/85 text-white font-semibold py-2 rounded-md transition disabled:opacity-60 disabled:cursor-not-allowed">
                <span v-if="loading" class="flex items-center justify-center">
                  <ArrowPathIcon class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" />
                  Updating...
                </span>
                <span v-else>
                  {{ mode === 'dashboard' ? 'Update Business Type' : 'Save Business Type' }}
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
import { XMarkIcon, BriefcaseIcon, ArrowPathIcon } from '@heroicons/vue/24/outline';
import MobileModalHeader from '@/components/common/MobileModalHeader.vue';
import { useModalScroll } from '@/composables/useModalScroll';

export default {
    name: 'BusinessTypeModal',
    components: {
        XMarkIcon,
        BriefcaseIcon,
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
        selectedBusinessTypes: {
            type: Array,
            default: () => []
        },
        mode: {
            type: String,
            default: 'registration', // 'registration' or 'dashboard'
            validator: (value) => ['registration', 'dashboard'].includes(value)
        }
    },
    data() {
        return {
            businessTypeOptions: [
                'exporters',
                'manufacturers',
                'dealers',
                'importers',
                'distributors',
                'wholesalers'
            ],
            form: {
                businessTypes: []
            },
            loading: false,
            error: null
        }
    },
    computed: {
        areAllSelected() {
            return this.form.businessTypes.length === this.businessTypeOptions.length;
        }
    },
    watch: {
        open(newVal) {
            if (newVal) {
                this.openModal('business-type-modal');
                // Initialize form with selected business types when modal opens
                this.form.businessTypes = [...this.selectedBusinessTypes];
            } else {
                this.closeModal('business-type-modal');
            }
        }
    },
    methods: {
        async onSubmit() {
            this.error = null;
            
            if (this.mode === 'dashboard') {
                // Dashboard mode - make API call to update business types
                await this.updateBusinessTypes();
            } else {
                // Registration mode - just emit the selected types
                this.$emit('business-types-selected', this.form.businessTypes);
                this.$emit('close');
                this.resetForm();
            }
        },
        
        async updateBusinessTypes() {
            
            this.loading = true;
            try {
                // Make API call to update business using the PUT endpoint
                const response = await this.$store.dispatch('updateBusinessInfo', {
                    business_type: this.form.businessTypes
                });
                
                // Emit success event
                this.$emit('business-types-updated', this.form.businessTypes);
                this.$emit('close');
                this.resetForm();
                
                // Show success message
                push.success('Business types updated successfully');
                
            } catch (error) {
                console.error('Error updating business types:', error);
                push.error(error.response?.data?.message || 'Failed to update business types. Please try again.');
            } finally {
                this.loading = false;
            }
        },
        
        resetForm() {
            this.form = {
                businessTypes: []
            };
            this.error = null;
        },
        
        toggleSelectAll() {
            if (this.areAllSelected) {
                this.form.businessTypes = [];
            } else {
                this.form.businessTypes = [...this.businessTypeOptions];
            }
        }
    }
}
</script>
