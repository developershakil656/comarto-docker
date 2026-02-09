<template>
  <div v-if="show" class="fixed inset-0 z-[55] overflow-y-auto">
    <!-- Mobile: Full screen with card design -->
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center ">
      <!-- Background overlay -->
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="$emit('close')"></div>

      <!-- Modal panel -->
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all md:align-middle max-w-md w-full">
        <!-- Mobile Header -->
        <div class="md:hidden">
          <MobileModalHeader 
            title="Enter Name" 
            @back="$emit('close')" 
          />
        </div>

        <!-- Content -->
        <div class="bg-white px-4 pt-5 pb-4 md:p-6 md:pb-4">
          <!-- Desktop Header -->
          <div class="hidden md:flex items-center justify-between mb-6">
            <div>
              <OptimizedImage src="/logo.svg" class="h-6" alt="Logo" />
            </div>
            <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
              <XMarkIcon class="w-6 h-6" />
            </button>
          </div>

          <div v-if="!oldName" class="flex flex-wrap items-center mb-6">
            <div class="pl-3 border-l-2">
              <div class="md:text-lg font-bold">Welcome</div>
              <div class="text-sm md:text-base">Enter your name for a seamless experience</div>
            </div>
          </div>

          <!-- Error Message -->
          <div v-if="errorMessage" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-center">
              <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
              </svg>
              <span class="text-red-700 text-sm">{{ errorMessage }}</span>
            </div>
          </div>

          <!-- Form -->
          <Form @submit="onSubmit" v-slot="{ errors, isSubmitting }">
            <div class="text-base md:text-lg font-semibold mb-4">{{ message }}</div>
            <!-- Name Input -->
            <div class="mb-6 relative group">
              <Field v-model="name" name="name" type="text" id="name-input" class="block w-full rounded-lg border pl-4 py-3 focus:border-primary focus:ring-blue-200 outline-none transition placeholder-transparent text-base" :class="{ 'border-red-300 focus:border-red-500': errors.name, 'border-gray-300': !errors.name }" placeholder="Enter Name" :disabled="loading" rules="required|min:2|max:50|alpha_spaces" />

              <label for="name-input" class="absolute left-6 z-10 -translate-y-1/2 bg-white text-sm transition-all duration-200 pointer-events-none group-focus-within:top-0 group-focus-within:text-xs group-focus-within:left-6 group-focus-within:-translate-y-1.5" :class="{ 
                         'top-0 text-xs left-6 text-primary': name && !errors.name, 
                         'top-0 text-xs left-6 text-red-500': errors.name,
                         'top-1/2 text-gray-500': !name && !errors.name 
                     }">
                Enter Your Name
              </label>

              <!-- Error Message -->
              <div v-if="errors.name" class="mt-1 text-red-500 text-sm flex items-center">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                {{ errors.name }}
              </div>
            </div>

            <button type="submit" :disabled="isSubmitting || loading" class="w-full hover:bg-primary/85 bg-primary text-white font-semibold py-2 rounded-md transition disabled:opacity-60 disabled:cursor-not-allowed">
              {{ loading ? 'Saving...' : 'Submit' }}
            </button>
          </Form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import {
    push
} from 'notivue'
import {
    Form,
    Field
} from 'vee-validate';
import {
    XMarkIcon
} from '@heroicons/vue/24/outline'
import { useModalScroll } from '@/composables/useModalScroll'
import MobileModalHeader from '@/components/common/MobileModalHeader.vue'
import OptimizedImage from '@/components/common/OptimizedImage.vue'

export default {
    components: {
        Form,
        Field,
        XMarkIcon,
        MobileModalHeader,
        OptimizedImage
    },
    setup() {
        const { openModal, closeModal } = useModalScroll()
        return { openModal, closeModal }
    },
    props: {
        show: {
            type: Boolean,
            default: false
        },
        message: {
            type: String,
            default: 'You are Comarto First time user!'
        },
        oldName: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            name: '',
            loading: false,
            errorMessage: ''
        }
    },
    emits: ['submit', 'close'],
    mounted() {
        this.name = this.oldName
    },
    watch: {
        show(newVal) {
            if (newVal) {
                this.openModal('enter-name-modal')
                this.name = this.oldName
                this.errorMessage = ''
            } else {
                this.closeModal('enter-name-modal')
            }
        }
    },
    methods: {
        async onSubmit(values) {
            try {
                this.loading = true;
                this.errorMessage = '';

                const response = await axios.put('/user/name', {
                    name: values.name.trim()
                });

                if (response.data.status) {
                    // Update user data in store
                    const updatedUser = {
                        ...this.$store.getters.user,
                        name: values.name.trim()
                    };
                    this.$store.commit('SET_USER', updatedUser);

                    // Emit success
                    // this.$emit('submit', values.name.trim());
                    push.success('Name updated successfully!');

                    // Close modal
                    this.$emit('close');
                    setTimeout(() => {
                        this.$emit('close');
                    }, 1500);

                } else {
                    push.error(response.data.message || 'Failed to save name');
                }
            } catch (error) {
                let errorMessage = 'An error occurred while saving your name.';

                if (error.response) {
                    if (error.response.data && error.response.data.message) {
                        errorMessage = error.response.data.message;
                    } else if (error.response.status === 422) {
                        errorMessage = 'Please enter a valid name.';
                    } else if (error.response.status >= 500) {
                        errorMessage = 'Server error. Please try again later.';
                    }
                } else if (error.request) {
                    errorMessage = 'Network error. Please check your connection.';
                }

                this.errorMessage = errorMessage;
                push.error(errorMessage);
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
