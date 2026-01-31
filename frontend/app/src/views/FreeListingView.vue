<template>
  <div>
    <div class="bg-white min-h-screen flex flex-col mb-10 md:mb-0">
      <!-- Header Section -->
      <div class="flex flex-col md:flex-row items-center justify-between px-6 py-10 max-w-7xl mx-auto w-full text-center md:text-left">
        <!-- Left Content -->
        <div class="flex-1 max-w-xl">
          <h1 class="text-2xl md:text-4xl font-bold mb-2 md:text-left">
            List Your Business <span class="text-primary">for FREE</span>
          </h1>
          <p class="text-base md:text-lg md:text-left">Popular B2B platform in Bangladesh</p>
          <!-- Mobile Input -->
          <div class="flex flex-col sm:flex-row items-center bg-white rounded-lg shadow-md border p-1.5 my-8 w-full max-w-[466px]">
            <div class="flex">
            <span class="flex flex-shrink-0 items-center px-2 border-r border-gray-300">
              <img src="https://flagcdn.com/bd.svg" alt="BD Flag" class="w-6 mr-1" />
              +88
            </span>
              <input 
              v-model="mobileNumber" 
              type="text" 
              placeholder="Enter WhatsApp No." 
              class="flex-1 px-3 py-2 outline-none" 
              maxlength="11"
              @input="onNumberInput"
              :class="{ 'border-red-300': numberError }"
            />
            </div>
            
            <button 
              @click="handleStartNow"
              class="bg-primary text-white px-5 py-2.5 rounded-lg sm:ml-2 mt-4 md:mt-0 sm:mt-0 w-full sm:w-auto font-semibold hover:bg-primary/85 transition disabled:opacity-50 disabled:cursor-not-allowed"
              :disabled="!isValidNumber || loading || !termsAgreed"
            >
              {{ loading ? 'Processing...' : 'Start Now →' }}
            </button>
          </div>
          <!-- Error Message -->
          <div v-if="numberError" class="text-red-500 text-sm -mt-7 mb-4">
            {{ numberError }}
          </div>
          
          <!-- Terms and Conditions Checkbox -->
          <div class="flex items-start mb-6 ml-1 text-left">
            <input 
              id="terms-agreement" 
              v-model="termsAgreed" 
              type="checkbox" 
              class="mt-1 h-4 w-4 text-primary rounded border-gray-300 focus:ring-primary"
              checked
            >
            <label for="terms-agreement" class="ml-2 text-sm text-gray-700">
              By continuing, you agree to our 
              <a href="/terms-conditions" class="text-primary hover:underline">Terms and Conditions</a>, 
              <a href="/privacy-policy" class="text-primary hover:underline">Privacy Policy</a>
            </label>
          </div>
          
          <!-- Features List -->
          <ul class="space-y-3 mb-8 text-sm text-left">
            <li class="flex items-start"><CheckCircleIcon class="text-green-600 w-4 md:w-6 mr-1 md:mr-2" />Get Discovered & Create Your Online Business</li>
            <li class="flex items-start"><CheckCircleIcon class="text-green-600 w-4 md:w-6 mr-1 md:mr-2" />Respond to Customer Reviews & Questions</li>
            <li class="flex items-start"><CheckCircleIcon class="text-green-600 w-4 md:w-6 mr-1 md:mr-2" />Showcase Your Product & Service Offerings</li>
          </ul>
          
        </div>
        <!-- Right Image (hidden on small screens, visible on md and up) -->
        <div class="hidden md:flex justify-center mt-4 md:mt-0">
          <!-- Stats -->
          <div class="mt-auto space-y-3 -mr-36 mb-2 z-10 hidden">
            <div class="flex items-center bg-green-100 rounded-xl px-12 py-2 w-full">
              <UserIcon class="mr-2 w-7 h-7 text-green-700" />
              <div>
                <p class="font-bold text-lg">19.1 Crore+</p>
                <p class="text-gray-600">Buyers*</p>
              </div>
            </div>
            <div class="flex items-center bg-blue-100 rounded-xl px-12 py-2 w-full">
              <BuildingOffice2Icon class="mr-2 w-7 h-7 text-blue-700" />
              <div>
                <p class="font-bold text-lg">19.1 Crore+</p>
                <p class="text-gray-600">Businesses Listed</p>
              </div>
            </div>
            <div class="flex items-center bg-pink-100 rounded-xl px-12 py-2 w-full">
              <CubeIcon class="mr-2 w-7 h-7 text-pink-700" />
              <div>
                <p class="font-bold text-lg">39 Crore+</p>
                <p class="text-gray-600">Products</p>
              </div>
            </div>
          </div>
          <div class="w-[550px] -ml-36">
            <OptimizedImage :src="businessImage" alt="Man Thumbs Up"/>
          </div>
          <!-- Replace the src with your own image if available -->
        </div>
      </div>

      <!-- Steps Section -->
      <div class="bg-gray-50 md:py-12 md:mt-8">
        <div class="max-w-5xl mx-auto px-4">
          <h2 class="text-xl md:text-3xl font-bold text-center mb-10">Get a FREE Business Listing in 3 Simple Steps</h2>
          <div class="flex flex-col md:flex-row justify-between items-center gap-8 md:gap-4">
            <!-- Step 1 -->
            <div class="flex flex-col items-center text-center flex-1">
              <div class="bg-white shadow-lg rounded-full w-20 md:w-24 h-20 md:h-24 flex items-center justify-center mb-4">
                <DevicePhoneMobileIcon class="w-10 md:w-12 text-primary" />
              </div>
              <h3 class="font-bold md:text-lg mb-1">Create Account</h3>
              <p class="text-gray-600">Enter your mobile number<br>to get started</p>
            </div>
            <!-- Arrow (for medium screens and up) -->
            <span class="hidden md:block text-3xl mx-4">→</span>
            <!-- Step 2 -->
            <div class="flex flex-col items-center text-center flex-1">
              <div class="bg-white shadow-lg rounded-full w-20 md:w-24 h-20 md:h-24 flex items-center justify-center mb-4">
                <BuildingOfficeIcon class="w-10 md:w-12 text-primary" />
              </div>
              <h3 class="font-bold md:text-lg mb-1">Enter Business Details</h3>
              <p class="text-gray-600">Add name, address,<br>business hours and photos</p>
            </div>
            <!-- Arrow (for medium screens and up) -->
            <span class="hidden md:block text-3xl mx-4">→</span>
            <!-- Step 3 -->
            <div class="flex flex-col items-center text-center flex-1">
              <div class="bg-white shadow-lg rounded-full w-20 md:w-24 h-20 md:h-24 flex items-center justify-center mb-4">
                <MagnifyingGlassIcon class="w-10 md:w-12 text-primary" />
              </div>
              <h3 class="font-bold md:text-lg mb-1">Select Categories</h3>
              <p class="text-gray-600">List your products and boom!</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <MobileBottomNavigation />

    <!-- Login Modal -->
    <LoginModal 
      :show="showLoginModal" 
      :number="mobileNumber"
      @close="showLoginModal = false"
      @submit="handleLoginSuccess"
    />

    <!-- OTP Verification Modal -->
    <OtpVerificationModal
      :show="showOtpModal"
      :mobile="mobileNumber"
      :loading="otpLoading"
      :error-message="otpError"
      @close="showOtpModal = false"
      @submit="handleOtpVerification"
      @resend="handleResendOtp"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useStore } from 'vuex';
import { useSEO } from '@/composables/useSEO';
import LoginModal from '@/components/modals/LoginModal.vue';
import OtpVerificationModal from '@/components/modals/OtpVerificationModal.vue';
import { CheckCircleIcon, DevicePhoneMobileIcon, UserIcon, BuildingOffice2Icon, CubeIcon } from '@heroicons/vue/24/solid';
import { BuildingOfficeIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline';
import MobileBottomNavigation from '@/components/common/MobileBottomNavigation.vue'
import Swal from 'sweetalert2';
import OptimizedImage from '@/components/common/OptimizedImage.vue';
import businessImage from '@/assets/images/comarto.com-create-business.png';

const router = useRouter();
const store = useStore();

// Reactive data
const mobileNumber = ref('');
const numberError = ref('');
const loading = ref(false);
const showLoginModal = ref(false);
const showOtpModal = ref(false);
const otpLoading = ref(false);
const otpError = ref('');
const termsAgreed = ref(true);

// Computed properties
const isAuthenticated = computed(() => store.getters.isAuthenticated);
const user = computed(() => store.getters.user);
const isValidNumber = computed(() => {
  const number = mobileNumber.value.replace(/\D/g, '');
  // Use the specific Bangladesh mobile number pattern: 01[3-9][0-9]{8}
  return /^01[3-9][0-9]{8}$/.test(number);
});

// Methods
const onNumberInput = (event) => {
  // Remove non-numeric characters
  let value = event.target.value.replace(/\D/g, '');
  
  // Limit to 11 digits
  if (value.length > 11) {
    value = value.slice(0, 11);
  }
  
  mobileNumber.value = value;
  numberError.value = '';
  
  // Validate number using the specific pattern
  if (value.length > 0 && value.length < 11) {
    numberError.value = 'Please enter a valid 11-digit mobile number';
  } else if (value.length === 11 && !/^01[3-9][0-9]{8}$/.test(value)) {
    numberError.value = 'Mobile number must start with 013, 014, 015, 016, 017, 018, or 019';
  } else if (value.length === 11) {
    numberError.value = '';
  }
};

const handleStartNow = async () => {
  // Validate number first
  if (!isValidNumber.value) {
    numberError.value = 'Please enter a valid 11-digit mobile number starting with 013, 014, 015, 016, 017, 018, or 019';
    return;
  }

  // Check if user is authenticated
  if (!isAuthenticated.value) {
    // Show login modal
    showLoginModal.value = true;
    return;
  }

  // If authenticated but no number in profile, prompt to add number first
  if (!user.value?.number) {
    const result = await Swal.fire({
      icon: 'warning',
      title: 'Add your mobile number',
      text: 'Please add your mobile number in Account Details before creating a business.',
      confirmButtonText: 'Go to Account Details',
      showCancelButton: true,
      cancelButtonText: 'Cancel'
    });
    if (result.isConfirmed) {
      router.push({ name: 'account-details' });
    }
    return;
  }

  // User is authenticated and has a number, check if number matches
  if (user.value?.number === mobileNumber.value) {
    // Number matches, redirect to business register
    router.push({ name: 'business-register' });
  } else {
    // Number doesn't match, send OTP for verification
    await handleSendOtp();
  }
};

const handleLoginSuccess = async (loginResult) => {
  showLoginModal.value = false;
  
  // Check if the logged-in user's number matches the input
  if (user.value?.number === mobileNumber.value) {
    // Number matches, redirect to business register
    router.push({ name: 'business-register' });
  } else {
    // Number doesn't match, send OTP for verification
    // await handleSendOtp();
  }
};

const handleSendOtp = async () => {
  try {
    loading.value = true;
    otpError.value = '';
    
    // Send OTP for business login
    await store.dispatch('sendOTP', mobileNumber.value);
    
    // Show OTP modal
    showOtpModal.value = true;
  } catch (error) {
    console.error('Error sending OTP:', error);
    otpError.value = error.message || 'Failed to send OTP. Please try again.';
  } finally {
    loading.value = false;
  }
};

const handleOtpVerification = async (otp) => {
  try {
    otpLoading.value = true;
    otpError.value = '';
    
    // Verify OTP for business login
    const response = await store.dispatch('verifyOTP', {
      number: mobileNumber.value,
      otp: otp
    });
    
    // Close OTP modal
    showOtpModal.value = false;
    
    // Redirect to business register
    router.push({ name: 'business-register' });
  } catch (error) {
    console.error('Error verifying OTP:', error);
    otpError.value = error.message || 'Invalid OTP. Please try again.';
  } finally {
    otpLoading.value = false;
  }
};

const handleResendOtp = async () => {
  await handleSendOtp();
};

// Set SEO meta tags for the free listing page
const { setMetaTags } = useSEO();
setMetaTags(
  'List Your Business for FREE - Comarto',
  'Get a free business listing on Comarto, Bangladesh\'s popular B2B marketplace. Connect with buyers, showcase products, and grow your business today.',
  null,
  'Comarto, free business listing, B2B, marketplace, Bangladesh, suppliers, manufacturers, wholesale'
);

// Initialize mobile number if user is authenticated
onMounted(() => {
  if (isAuthenticated.value && user.value?.number) {
    mobileNumber.value = user.value.number;
  }
});
</script>

<style scoped>
/* Add any custom styles if needed */
</style> 