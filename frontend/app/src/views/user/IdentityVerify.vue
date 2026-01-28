<template>
  <div class="min-h-screen rounded-lg">
    <div class="max-w-4xl mx-auto text-sm md:text-base">
      <!-- Header Section -->
      <div class="text-center mb-8">
        <h1 class="text-xl md:text-3xl font-bold text-gray-800 mb-2">Identity Verification</h1>
        <p class="text-gray-600 md:text-base">Complete your identity verification using your National ID Card</p>
      </div>

      <!-- Main Card -->
      <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <!-- Card Header -->
        <div class="bg-gradient-to-r from-primary to-primary/90 px-8 py-6">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
        </div>
        <div>
              <h2 class="text-lg md:text-2xl font-bold text-white">NID Verification</h2>
              <p class="text-white md:text-base">Secure and confidential identity verification process</p>
            </div>
            </div>
        </div>

        <!-- Card Body -->
        <div class="p-8 text-sm md:text-base">
          <!-- Phone Verification Warning -->
          <div v-if="!isNumberVerified" class="mb-6">
            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                  <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                  </svg>
                </div>
        <div>
                  <h3 class="font-semibold text-red-800">Phone Verification Required</h3>
                  <p class="text-red-700 text-sm">Please verify your phone number before proceeding with NID verification.</p>
                </div>
              </div>
            </div>
        </div>

          <!-- Verification Form -->
          <div v-if="currentStatus === null || showForm" class="space-y-6">
            <div v-if="apiError" class="p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
              {{ apiError }}
            </div>
            <!-- NID Number Input -->
            <div class="space-y-2">
              <label class="block text-sm font-semibold text-gray-700" for="nidNumber">
                NID Number <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <input 
                  id="nidNumber" 
                  v-model="nidNumber"
                  type="text" 
                  placeholder="Enter your NID number (13 or 17 digits)"
                  :class="[
                    'w-full px-4 py-3 border rounded-xl text-gray-700 placeholder-gray-400 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent',
                    nidNumberError ? 'border-red-300 bg-red-50' : 'border-gray-300 hover:border-gray-400'
                  ]"
                  :disabled="!isNumberVerified"
                  @input="validateNidNumber"
                  @blur="validateNidNumber"
                />
                <div v-if="nidNumber" class="absolute right-3 top-1/2 transform -translate-y-1/2">
                  <div v-if="nidNumberError" class="w-5 h-5 text-red-500">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                  </div>
                  <div v-else class="w-5 h-5 text-green-500">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                  </div>
                </div>
              </div>
              <div v-if="nidNumberError" class="text-red-600 text-sm flex items-center gap-1">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                {{ nidNumberError }}
              </div>
              <p class="text-gray-500 text-xs">NID number must be exactly 13 or 17 digits</p>
    </div>

            <!-- NID Front Upload -->
            <div class="space-y-3">
              <label class="block text-sm font-semibold text-gray-700" for="nidFront">
                NID Front Image <span class="text-red-500">*</span>
              </label>
              <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-primary transition-colors duration-200">
                <input 
                  id="nidFront" 
                  type="file" 
                  accept="image/*" 
                  @change="onFileChange($event, 'front')" 
                  :disabled="!isNumberVerified || !isNidNumberValid" 
                  class="hidden"
                />
                <label 
                  for="nidFront" 
                  :class="[
                    'cursor-pointer block',
                    (!isNumberVerified || !isNidNumberValid) ? 'opacity-50 cursor-not-allowed' : ''
                  ]"
                >
                  <div class="space-y-3">
                    <div class="w-16 h-16 mx-auto bg-primary/10 rounded-full flex items-center justify-center">
                      <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                      </svg>
                    </div>
                    <div>
                      <p class="text-sm font-medium text-gray-700">Click to upload front image</p>
                      <p class="text-xs text-gray-500">PNG, JPG, JPEG up to 5MB</p>
                    </div>
                  </div>
                </label>
              </div>
              <div v-if="nidFrontPreview" class="mt-3 flex justify-center">
                <div class="relative inline-block">
                  <img :src="nidFrontPreview" alt="NID Front Preview" class="w-64 rounded-lg border shadow-sm" />
                  <button 
                    @click="removeImage('front')" 
                    class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition-colors"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                  </button>
                </div>
              </div>
    </div>

            <!-- NID Back Upload -->
            <div class="space-y-3">
              <label class="block text-sm font-semibold text-gray-700" for="nidBack">
                NID Back Image <span class="text-red-500">*</span>
              </label>
              <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-primary transition-colors duration-200">
                <input 
                  id="nidBack" 
                  type="file" 
                  accept="image/*" 
                  @change="onFileChange($event, 'back')" 
                  :disabled="!isNumberVerified || !isNidNumberValid" 
                  class="hidden"
                />
                <label 
                  for="nidBack" 
                  :class="[
                    'cursor-pointer block',
                    (!isNumberVerified || !isNidNumberValid) ? 'opacity-50 cursor-not-allowed' : ''
                  ]"
                >
                  <div class="space-y-3">
                    <div class="w-16 h-16 mx-auto bg-primary/10 rounded-full flex items-center justify-center">
                      <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                      </svg>
        </div>
        <div>
                        <p class="text-sm font-medium text-gray-700">Click to upload back image</p>
                        <p class="text-xs text-gray-500">PNG, JPG, JPEG up to 5MB</p>
                      </div>
                  </div>
                </label>
              </div>
              <div v-if="nidBackPreview" class="mt-3 flex justify-center">
                <div class="relative inline-block">
                  <img :src="nidBackPreview" alt="NID Back Preview" class="w-64 rounded-lg border shadow-sm" />
                  <button 
                    @click="removeImage('back')" 
                    class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition-colors"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                  </button>
                </div>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4">
              <button 
                @click="submitVerification"
                :disabled="!canSubmit"
                :class="[
                  'w-full py-3 px-6 rounded-xl font-semibold text-white transition-all duration-200 transform text-sm md:text-base',
                  canSubmit 
                    ? 'bg-gradient-to-r from-primary to-primary/90 hover:from-primary/90 hover:to-primary hover:scale-[1.02] shadow-lg' 
                    : 'bg-gray-400 cursor-not-allowed'
                ]"
              >
                <span v-if="!isSubmitting">Submit for Verification</span>
                <span v-else class="flex items-center justify-center gap-2">
                  <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Submitting...
                </span>
              </button>
            </div>
          </div>
          <!-- Verification Status: In Review -->
          <div v-if="currentStatus === 'in review' && !showForm" class="text-center py-6 md:py-12 text-sm md:text-base">
            <div class="w-20 h-20 mx-auto mb-6 bg-primary/10 rounded-full flex items-center justify-center">
              <svg class="w-10 h-10 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"></path>
              </svg>
            </div>
            <h3 class="text-base md:text-xl font-semibold text-gray-800 mb-2">Your verification is under review</h3>
            <p class="text-gray-600 md:text-base">Thanks for submitting your details. We'll notify you once it's completed.</p>
          </div>

          <!-- Verification Status: Success -->
          <div v-if="currentStatus === 'confirmed' && !showForm" class="text-center py-6 md:py-12 text-sm md:text-base">
            <div class="w-20 h-20 mx-auto mb-6 bg-green-100 rounded-full flex items-center justify-center">
              <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <h3 class="text-base md:text-xl font-semibold text-gray-800 mb-2">Identity Verified Successfully!</h3>
            <p class="text-gray-600 md:text-base mb-6">Your identity has been verified. You can now access all features.</p>
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 max-w-md mx-auto">
              <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-green-800 font-medium">Verification Complete</span>
              </div>
            </div>
          </div>
          <!-- Verification Status: Rejected -->
          <div v-if="currentStatus === 'rejected' && !showForm" class="text-center py-6 md:py-12 text-sm md:text-base">
            <div class="w-20 h-20 mx-auto mb-6 bg-red-100 rounded-full flex items-center justify-center">
              <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <h3 class="text-base md:text-xl font-semibold text-gray-800 mb-2">Verification Rejected</h3>
            <p class="text-gray-600 md:text-base mb-6">We couldn't verify your identity this time. You can review your details and apply again.</p>
            <button 
              @click="startReapply" 
              class="bg-gradient-to-r from-primary to-primary/90 hover:from-primary/90 hover:to-primary text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 hover:scale-105 shadow-lg text-sm md:text-base"
            >
              Re-apply for Verification
            </button>
          </div>
        </div>
    </div>
    </div>
</div>
</template>

<script>
import axios from 'axios'
import { validateImageFile } from '@/utils/imageValidation.js'
import {push} from 'notivue'
export default {
    data() {
        return {
            isNumberVerified: true, // Set to true if the user's number is verified
      nidNumber: '',
      nidNumberError: '',
            nidFrontPreview: null,
            nidBackPreview: null,
      nidFrontFile: null,
      nidBackFile: null,
      isSubmitting: false,
      showForm: false,
      apiError: ''
    };
  },
  computed: {
    user() {
      return this.$store.getters.user
    },
    currentStatus() {
      const raw = this.user?.account_verification
      if (raw === null || raw === undefined) return null
      if (typeof raw === 'string') {
        const normalized = raw.trim().toLowerCase()
        if (normalized === 'null') return null
        if (normalized === 'in review') return 'in review'
        if (normalized === 'confirmed') return 'confirmed'
        if (normalized === 'rejected') return 'rejected'
      }
      return raw
    },
    isNidNumberValid() {
      return this.nidNumber && !this.nidNumberError;
    },
    canSubmit() {
      return this.isNumberVerified && 
             this.isNidNumberValid && 
             this.nidFrontPreview && 
             this.nidBackPreview && 
             !this.isSubmitting;
    }
  },
    methods: {
    validateNidNumber() {
      if (!this.nidNumber) {
        this.nidNumberError = 'NID number is required';
        return false;
      }
      
      const cleanNid = this.nidNumber.replace(/\D/g, '');
      if (cleanNid.length !== 13 && cleanNid.length !== 17) {
        this.nidNumberError = 'NID number must be exactly 13 or 17 digits';
        return false;
      }
      
      if (!/^\d+$/.test(cleanNid)) {
        this.nidNumberError = 'NID number can only contain digits';
        return false;
      }
      
      this.nidNumberError = '';
      return true;
    },
        onFileChange(event, side) {
            const file = event.target.files[0];
            if (file) {
                const validation = validateImageFile(file);
                if (!validation.isValid) {
                    push.error(validation.error);
                    return;
                }
                const preview = URL.createObjectURL(file);
                if (side === 'front') {
                    this.nidFrontPreview = preview;
                    this.nidFrontFile = file;
                } else {
                    this.nidBackPreview = preview;
                    this.nidBackFile = file;
                }
            }
        },
    removeImage(side) {
      if (side === 'front') {
        this.nidFrontPreview = null;
        document.getElementById('nidFront').value = '';
      } else {
        this.nidBackPreview = null;
        document.getElementById('nidBack').value = '';
      }
    },
    async submitVerification() {
      if (!this.canSubmit) return;
      this.apiError = ''
      this.isSubmitting = true;
      try {
        const cleanNid = this.nidNumber.replace(/\D/g, '')
        const formData = new FormData()
        formData.append('nid_number', cleanNid)
        if (this.nidFrontFile) formData.append('nid_front', this.nidFrontFile)
        if (this.nidBackFile) formData.append('nid_back', this.nidBackFile)
        await axios.post('/user/account/verification', formData)
        // Refresh user to get updated verification status
        await this.$store.dispatch('fetchUser')
        this.showForm = false
        // Clear local inputs
        this.nidNumber = ''
        this.nidNumberError = ''
        this.removeImage('front')
        this.removeImage('back')
      } catch (error) {
        this.apiError = error?.response?.data?.message || error.message || 'Submission failed'
      } finally {
        this.isSubmitting = false;
      }
    },
    startReapply() {
      this.showForm = true
      this.nidNumber = ''
      this.nidNumberError = ''
      this.nidFrontPreview = null
      this.nidBackPreview = null
      const front = document.getElementById('nidFront')
      const back = document.getElementById('nidBack')
      if (front) front.value = ''
      if (back) back.value = ''
    },
  },
  async mounted() {
    try {
      if (!this.user) {
        await this.$store.dispatch('fetchUser')
      }
      if (this.currentStatus === 'rejected') {
        this.showForm = false
      }
    } catch (e) {}
  }
};
</script>

<style scoped>
@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style>
