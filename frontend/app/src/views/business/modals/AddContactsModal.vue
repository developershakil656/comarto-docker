<template>
  <Transition name="modal" appear>
    <div v-if="open" class="fixed inset-0 z-[60] flex items-stretch md:items-center justify-center bg-white md:bg-black md:bg-opacity-30 p-0 md:p-4" style="min-height: 100vh;" @click="$emit('close')">
      <Transition name="modal-content" appear>
        <div class="bg-white w-full h-full md:h-auto md:rounded-xl md:shadow-lg md:max-w-lg md:max-h-[90vh] overflow-y-auto" @click.stop>
          <MobileModalHeader title="Add Contact" @back="$emit('close')" />
          <div class="p-4 md:p-8 relative">
            <button @click="$emit('close')" class="hidden md:block absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors">
              <XMarkIcon class="w-6 h-6" />
            </button>
            <h2 class="hidden md:block text-xl font-semibold mb-6 text-center">
              <slot> Add Contact </slot>
            </h2>
            
            <!-- Loading State -->
            <div v-if="loading" class="flex items-center justify-center py-8">
              <ArrowPathIcon class="animate-spin h-8 w-8 text-primary" />
              <span class="ml-2 text-gray-600">Loading...</span>
            </div>

            <Form @submit="onSubmit" v-slot="{ errors, isSubmitting }" class="rounded-2xl shadow-lg p-4 py-6 min-h-full">
              <!-- Number -->
              <div class="relative group mb-6">
                <span class="absolute left-4 top-6 -translate-y-1/2 text-gray-400 pointer-events-none">
                  <PhoneIcon class="w-5 h-5" />
                </span>
                <div class="flex gap-2">
                  <Field 
                    v-model="form.number" 
                    name="number"
                    type="text" 
                    id="number"
                    class="block flex-1 w-full rounded-lg border pl-12 pr-4 py-3 focus:border-primary focus:ring-blue-200 outline-none transition placeholder-transparent text-base"
                    :class="{ 'border-red-300 focus:border-red-500': errors.number, 'border-gray-300': !errors.number }"
                    placeholder="Number"
                    rules="bd_mobile" />
                  <button 
                    @click="sendNumberOTP" 
                    type="button"
                    :disabled="!canSendNumberOTP"
                    class="px-2 py-3 text-xs md:text-base md:px-4 bg-primary text-white rounded-lg hover:bg-primary/85 transition disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap"
                  >
                    <ArrowPathIcon v-if="sendingNumberOTP" class="animate-spin h-4 w-4" />
                    <span v-else>Send OTP</span>
                  </button>
                </div>
                <label for="number"
                  class="absolute left-12 z-10 -translate-y-1/2 bg-white text-sm transition-all duration-200 pointer-events-none group-focus-within:top-0 group-focus-within:text-xs group-focus-within:left-8 group-focus-within:-translate-y-1.5"
                  :class="{ 
                    'top-0 text-xs left-8 text-primary': form.number && !errors.number, 
                    'top-0 text-xs left-8 text-red-500': errors.number,
                    'top-6 text-gray-500': !form.number && !errors.number 
                  }">
                  Number
                </label>
                <!-- Error Message -->
                <div v-if="errors.number" class="mt-1 text-red-500 text-sm flex items-center">
                  <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                  {{ errors.number }}
                </div>
                <!-- OTP Verification Status for Number -->
                <div v-if="numberVerificationStatus" class="mt-2">
                  <div v-if="numberVerificationStatus === 'pending'" class="flex items-center text-yellow-600">
                    <ClockIcon class="w-4 h-4 mr-1" />
                    <span class="text-sm">Pending verification</span>
                  </div>
                  <div v-if="numberVerificationStatus === 'verified'" class="flex items-center text-green-600">
                    <CheckCircleIcon class="w-4 h-4 mr-1" />
                    <span class="text-sm">Verified</span>
                  </div>
                  <div v-if="numberVerificationStatus === 'failed'" class="flex items-center text-red-600">
                    <XCircleIcon class="w-4 h-4 mr-1" />
                    <span class="text-sm">Verification failed</span>
                  </div>
                </div>
                <!-- OTP Input for Number -->
                <div v-if="numberVerificationStatus === 'pending'" class="mt-3">
                  <div class="flex gap-2 border-b border-gray-300 pb-2">
                    <input 
                      v-model="numberOTP" 
                      type="text" 
                      placeholder="Enter OTP" 
                      maxlength="6"
                      class="flex-1 px-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                    />
                    <button 
                      @click="verifyNumberOTP" 
                      type="button"
                      :disabled="!numberOTP || numberOTP.length !== 6 || verifyingNumberOTP"
                      class="px-2 py-2 text-xs md:text-base md:px-4 bg-primary text-white rounded-lg hover:bg-primary/85 transition disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                      <ArrowPathIcon v-if="verifyingNumberOTP" class="animate-spin h-4 w-4" />
                      <span v-else>Verify</span>
                    </button>
                  </div>
                </div>
              </div>

              <!-- WhatsApp Number -->
              <div class="relative group mb-6">
                <span class="absolute left-4 top-6 -translate-y-1/2 text-gray-400 pointer-events-none">
                  <ChatBubbleLeftRightIcon class="w-5 h-5" />
                </span>
                <div class="flex gap-2">
                  <Field 
                    v-model="form.whatsapp" 
                    name="whatsapp"
                    type="text" 
                    id="whatsapp"
                    class="block flex-1 w-full rounded-lg border pl-12 pr-4 py-3 focus:border-primary focus:ring-blue-200 outline-none transition placeholder-transparent text-base"
                    :class="{ 'border-red-300 focus:border-red-500': errors.whatsapp, 'border-gray-300': !errors.whatsapp }"
                    placeholder="WhatsApp Number"
                    rules="bd_mobile" />
                  <button 
                    @click="sendWhatsappOTP" 
                    type="button"
                    :disabled="!canSendWhatsappOTP"
                    class="px-2 py-2 text-xs md:text-base md:px-4 bg-primary text-white rounded-lg hover:bg-primary/85 transition disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap"
                  >
                    <ArrowPathIcon v-if="sendingWhatsappOTP" class="animate-spin h-4 w-4" />
                    <span v-else>Send OTP</span>
                  </button>
                </div>
                <label for="whatsapp"
                  class="absolute left-12 z-10 -translate-y-1/2 bg-white text-sm transition-all duration-200 pointer-events-none group-focus-within:top-0 group-focus-within:text-xs group-focus-within:left-8 group-focus-within:-translate-y-1.5"
                  :class="{ 
                    'top-0 text-xs left-8 text-primary': form.whatsapp && !errors.whatsapp, 
                    'top-0 text-xs left-8 text-red-500': errors.whatsapp,
                    'top-6 text-gray-500': !form.whatsapp && !errors.whatsapp 
                  }">
                  WhatsApp Number
                </label>
                <!-- Error Message -->
                <div v-if="errors.whatsapp" class="mt-1 text-red-500 text-sm flex items-center">
                  <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                  {{ errors.whatsapp }}
                </div>
                <!-- OTP Verification Status for WhatsApp -->
                <div v-if="whatsappVerificationStatus" class="mt-2">
                  <div v-if="whatsappVerificationStatus === 'pending'" class="flex items-center text-yellow-600">
                    <ClockIcon class="w-4 h-4 mr-1" />
                    <span class="text-sm">Pending verification</span>
                  </div>
                  <div v-if="whatsappVerificationStatus === 'verified'" class="flex items-center text-green-600">
                    <CheckCircleIcon class="w-4 h-4 mr-1" />
                    <span class="text-sm">Verified</span>
                  </div>
                  <div v-if="whatsappVerificationStatus === 'failed'" class="flex items-center text-red-600">
                    <XCircleIcon class="w-4 h-4 mr-1" />
                    <span class="text-sm">Verification failed</span>
                  </div>
                </div>
                <!-- OTP Input for WhatsApp -->
                <div v-if="whatsappVerificationStatus === 'pending'" class="mt-3">
                  <div class="flex gap-2 border-b border-gray-300 pb-2">
                    <input 
                      v-model="whatsappOTP" 
                      type="text" 
                      placeholder="Enter OTP" 
                      maxlength="6"
                      class="flex-1 px-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                    />
                    <button 
                      @click="verifyWhatsappOTP" 
                      type="button"
                      :disabled="!whatsappOTP || whatsappOTP.length !== 6 || verifyingWhatsappOTP"
                      class="px-2 py-2 text-xs md:text-base md:px-4 bg-primary text-white rounded-lg hover:bg-primary/85 transition disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                      <ArrowPathIcon v-if="verifyingWhatsappOTP" class="animate-spin h-4 w-4" />
                      <span v-else>Verify</span>
                    </button>
                  </div>
                </div>
              </div>

              <!-- Email -->
              <div class="relative group mb-6">
                <span class="absolute left-4 top-6 -translate-y-1/2 text-gray-400 pointer-events-none">
                  <EnvelopeIcon class="w-5 h-5" />
                </span>
                <Field 
                  v-model="form.email" 
                  name="email"
                  type="email" 
                  id="email"
                  class="block w-full rounded-lg border pl-12 pr-4 py-3 focus:border-primary focus:ring-blue-200 outline-none transition placeholder-transparent text-base"
                  :class="{ 'border-red-300 focus:border-red-500': errors.email, 'border-gray-300': !errors.email }"
                  placeholder="Email"
                  rules="required|email" />
                <label for="email"
                  class="absolute left-12 z-10 -translate-y-1/2 bg-white text-sm transition-all duration-200 pointer-events-none group-focus-within:top-0 group-focus-within:text-xs group-focus-within:left-8 group-focus-within:-translate-y-1.5"
                  :class="{ 
                    'top-0 text-xs left-8 text-primary': form.email && !errors.email, 
                    'top-0 text-xs left-8 text-red-500': errors.email,
                    'top-6 text-gray-500': !form.email && !errors.email 
                  }">
                  Email
                </label>
                <!-- Error Message -->
                <div v-if="errors.email" class="mt-1 text-red-500 text-sm flex items-center">
                  <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                  {{ errors.email }}
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex gap-3">
                <button 
                  type="submit" 
                  :disabled="isSubmitting || !canSubmit"
                  class="flex-1 bg-primary hover:bg-primary/85 text-white font-semibold py-2 rounded-lg transition disabled:opacity-60 disabled:cursor-not-allowed"
                >
                  <span v-if="isSubmitting" class="flex items-center justify-center">
                    <ArrowPathIcon class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" />
                    Saving...
                  </span>
                  <span v-else>
                    Save Contact
                  </span>
                </button>
              </div>
            </Form>
          </div>
        </div>
      </Transition>
    </div>
  </Transition>
</template>

<script>
import { Form, Field } from 'vee-validate';
import { push } from 'notivue';
import {
  PhoneIcon,
  EnvelopeIcon,
  XMarkIcon,
  CheckCircleIcon,
  ExclamationCircleIcon,
  ArrowPathIcon,
  ClockIcon,
  XCircleIcon,
  ChatBubbleLeftRightIcon
} from '@heroicons/vue/24/outline';
import MobileModalHeader from '@/components/common/MobileModalHeader.vue';
import { useModalScroll } from '@/composables/useModalScroll';

export default {
  name: 'AddContactsModal',
  components: {
    MobileModalHeader,
    XMarkIcon,
    PhoneIcon,
    ChatBubbleLeftRightIcon,
    EnvelopeIcon,
    ExclamationCircleIcon,
    ArrowPathIcon,
    CheckCircleIcon,
    ClockIcon,
    XCircleIcon,
    Form,
    Field
  },
  setup() {
    const { openModal, closeModal } = useModalScroll()
    return { openModal, closeModal }
  },
  props: {
    open: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      loading: false,
      form: {
        number: '',
        whatsapp: '',
        email: ''
      },
      originalData: {
        number: '',
        whatsapp: '',
        email: ''
      },
      verifiedNumbers: {
        number: '',
        whatsapp: ''
      },
      numberOTP: '',
      whatsappOTP: '',
      numberVerificationStatus: null, // null, 'pending', 'verified', 'failed'
      whatsappVerificationStatus: null, // null, 'pending', 'verified', 'failed'
      sendingNumberOTP: false,
      sendingWhatsappOTP: false,
      verifyingNumberOTP: false,
      verifyingWhatsappOTP: false,
      successMessage: '',
      errorMessage: ''
    }
  },
  computed: {
    myBusiness() {
      return this.$store.getters.myBusiness;
    },
    canSubmit() {
      // Check if email is provided
      const emailValid = this.form.email && this.form.email.trim() !== '';
      
      // Check if numbers are verified (if they were changed)
      const numberVerified = !this.form.number || // No number provided
        this.form.number === this.originalData.number || // Same as original (no change)
        this.numberVerificationStatus === 'verified'; // Explicitly verified in current session
      
      const whatsappVerified = !this.form.whatsapp || // No whatsapp number provided
        this.form.whatsapp === this.originalData.whatsapp || // Same as original (no change)
        this.whatsappVerificationStatus === 'verified'; // Explicitly verified in current session
      
      return emailValid && numberVerified && whatsappVerified;
    },
    canSendNumberOTP() {
      // Can send OTP if:
      // 1. Number is provided and not empty
      // 2. Number is different from original number
      // 3. Number is different from verified number
      // 4. Number is not already verified in current session
      // 5. Not currently sending OTP
      // 6. Number passes validation (no validation errors)
      return this.form.number && 
        this.form.number.trim() !== '' &&
        this.form.number !== this.originalData.number && 
        this.form.number !== this.verifiedNumbers.number &&
        this.numberVerificationStatus !== 'verified' &&
        !this.sendingNumberOTP &&
        this.isNumberValid();
    },
    canSendWhatsappOTP() {
      // Can send OTP if:
      // 1. WhatsApp number is provided and not empty
      // 2. Number is different from original number
      // 3. Number is different from verified number
      // 4. Number is not already verified in current session
      // 5. Not currently sending OTP
      // 6. Number passes validation (no validation errors)
      return this.form.whatsapp && 
        this.form.whatsapp.trim() !== '' &&
        this.form.whatsapp !== this.originalData.whatsapp && 
        this.form.whatsapp !== this.verifiedNumbers.whatsapp &&
        this.whatsappVerificationStatus !== 'verified' &&
        !this.sendingWhatsappOTP &&
        this.isWhatsappValid();
    }
  },
  watch: {
    open(newVal) {
      if (newVal) {
        this.openModal('add-contacts-modal');
        this.loadExistingData();
        // Reset only form-specific data, not verification status
        this.numberOTP = '';
        this.whatsappOTP = '';
        this.successMessage = '';
        this.errorMessage = '';
        this.sendingNumberOTP = false;
        this.sendingWhatsappOTP = false;
        this.verifyingNumberOTP = false;
        this.verifyingWhatsappOTP = false;
      } else {
        this.closeModal('add-contacts-modal');
      }
    },
    'form.number'(newVal, oldVal) {
      // Reset verification status when number changes
      if (newVal !== this.originalData.number && newVal !== this.verifiedNumbers.number) {
        this.numberVerificationStatus = null;
        this.numberOTP = '';
      }else{
        this.numberVerificationStatus = 'verified';
      }
    },
    'form.whatsapp'(newVal, oldVal) {
      // Reset verification status when whatsapp number changes
      if (newVal !== this.originalData.whatsapp && newVal !== this.verifiedNumbers.whatsapp) {
        this.whatsappVerificationStatus = null;
        this.whatsappOTP = '';
      }else{
        this.whatsappVerificationStatus = 'verified';
      }
    }
  },
  methods: {
    // Validation methods to check if numbers are valid
    isNumberValid() {
      // Check if number passes bd_mobile validation
      if (!this.form.number || this.form.number.trim() === '') {
        return false;
      }
      
      // Basic validation for Bangladeshi mobile number
      // Should start with +880 or 01 and be 11 digits total
      const number = this.form.number.trim();
      const bdMobileRegex = /^(\+880|880)?01[3-9]\d{8}$/;
      return bdMobileRegex.test(number);
    },
    
    isWhatsappValid() {
      // Check if whatsapp number passes bd_mobile validation
      if (!this.form.whatsapp || this.form.whatsapp.trim() === '') {
        return false;
      }
      
      // Basic validation for Bangladeshi mobile number
      // Should start with +880 or 01 and be 11 digits total
      const number = this.form.whatsapp.trim();
      const bdMobileRegex = /^(\+880|880)?01[3-9]\d{8}$/;
      return bdMobileRegex.test(number);
    },
    
    loadExistingData() {
      this.loading = true;
      try {
        // Load existing data from myBusiness (from BusinessDashboard.vue)
        if (this.myBusiness) {
          this.form.number = this.myBusiness.number || '';
          this.form.whatsapp = this.myBusiness.alternate_number || '';
          this.form.email = this.myBusiness.email || '';
          
          // Store original data for comparison
          this.originalData.number = this.myBusiness.number || '';
          this.originalData.whatsapp = this.myBusiness.alternate_number || '';
          
          // Initialize verified numbers (initially same as original since they're already verified)
          this.verifiedNumbers.number = this.myBusiness.number || '';
          this.verifiedNumbers.whatsapp = this.myBusiness.alternate_number || '';
          
          // Set verification status for existing numbers (only if not already verified in current session)
          if (this.myBusiness.number && !this.numberVerificationStatus) {
            this.numberVerificationStatus = 'verified';
          } else if (!this.myBusiness.number) {
            this.numberVerificationStatus = null;
          }
          if (this.myBusiness.alternate_number && !this.whatsappVerificationStatus) {
            this.whatsappVerificationStatus = 'verified';
          } else if (!this.myBusiness.alternate_number) {
            this.whatsappVerificationStatus = null;
          }
        }
        
      } catch (error) {
        console.error('Error loading existing data:', error);
        this.errorMessage = 'Failed to load existing data';
      } finally {
        this.loading = false;
      }
    },
    
    resetForm() {
      this.numberOTP = '';
      this.whatsappOTP = '';
      this.successMessage = '';
      this.errorMessage = '';
      this.sendingNumberOTP = false;
      this.sendingWhatsappOTP = false;
      this.verifyingNumberOTP = false;
      this.verifyingWhatsappOTP = false;
      // Don't reset verification status or verified numbers here
      // Only reset them when modal is opened
    },
    
    async sendNumberOTP() {
      if (!this.canSendNumberOTP) {
        this.errorMessage = 'Cannot send OTP for this number';
        return;
      }
      
      this.sendingNumberOTP = true;
      this.errorMessage = '';
      
      try {
        await this.$store.dispatch('sendOTP', this.form.number);
        this.numberVerificationStatus = 'pending';
        this.successMessage = 'OTP sent successfully to your number';
      } catch (error) {
        console.error('Error sending number OTP:', error);
        this.errorMessage = error.response?.data?.message || 'Failed to send OTP';
      } finally {
        this.sendingNumberOTP = false;
      }
    },
    
    async sendWhatsappOTP() {
      if (!this.canSendWhatsappOTP) {
        this.errorMessage = 'Cannot send OTP for this number';
        return;
      }
      
      this.sendingWhatsappOTP = true;
      this.errorMessage = '';
      
      try {
        await this.$store.dispatch('sendOTP', this.form.whatsapp);
        this.whatsappVerificationStatus = 'pending';
        this.successMessage = 'OTP sent successfully to your WhatsApp number';
      } catch (error) {
        console.error('Error sending WhatsApp OTP:', error);
        this.errorMessage = error.response?.data?.message || 'Failed to send OTP';
      } finally {
        this.sendingWhatsappOTP = false;
      }
    },
    
    async verifyNumberOTP() {
      if (!this.numberOTP || this.numberOTP.length !== 6) {
        this.errorMessage = 'Please enter a valid 6-digit OTP';
        return;
      }
      
      this.verifyingNumberOTP = true;
      this.errorMessage = '';
      try {
        await this.$store.dispatch('verifyOTP', {
          number: this.form.number,
          otp: this.numberOTP
        });
        
        this.numberVerificationStatus = 'verified';
        this.verifiedNumbers.number = this.form.number; // Remember the verified number
        push.success('Number verified successfully');
        this.numberOTP = '';
      } catch (error) {
        console.error('Error verifying number OTP:', error);
        this.numberVerificationStatus = 'failed';
        push.error(error.response?.data?.message || 'Failed to verify OTP');
      } finally {
        this.verifyingNumberOTP = false;
      }
    },
    
    async verifyWhatsappOTP() {
      if (!this.whatsappOTP || this.whatsappOTP.length !== 6) {
        this.errorMessage = 'Please enter a valid 6-digit OTP';
        return;
      }
      
      this.verifyingWhatsappOTP = true;
      this.errorMessage = '';
      
      try {
        await this.$store.dispatch('verifyOTP', {
          number: this.form.whatsapp,
          otp: this.whatsappOTP
        });
        
        this.whatsappVerificationStatus = 'verified';
        this.verifiedNumbers.whatsapp = this.form.whatsapp; // Remember the verified number
        push.success('WhatsApp number verified successfully');
        this.whatsappOTP = '';
      } catch (error) {
        console.error('Error verifying whatsapp OTP:', error);
        this.whatsappVerificationStatus = 'failed';
        push.error(error.response?.data?.message || 'Failed to verify OTP');
      } finally {
        this.verifyingWhatsappOTP = false;
      }
    },
    
    async onSubmit(values) {
      if (!this.canSubmit) {
        push.error('Please verify all required fields before submitting');
        return;
      }
      
      try {
        // Prepare update data for all fields (number, alternate_number, and email)
        const updateData = {};
        
        if (this.form.number !== this.originalData.number) {
          updateData.number = this.form.number;
        }
        if (this.form.whatsapp !== this.originalData.whatsapp) {
          updateData.alternate_number = this.form.whatsapp;
        }
        if (this.form.email !== this.originalData.email) {
          updateData.email = this.form.email;
        }
        
        // Use updateBusinessInfo for all fields
        if (Object.keys(updateData).length > 0) {
          await this.$store.dispatch('updateBusinessInfo', updateData);
        }
        
        push.success('Contact information updated successfully!');
        
        // Close modal after a short delay
        setTimeout(() => {
          this.$emit('close');
          this.resetForm();
        }, 1500);
        
      } catch (error) {
        console.error('Error updating contact information:', error);
        push.error(error.response?.data?.message || 'Failed to update contact information');
      }
    }
  }
}
</script> 