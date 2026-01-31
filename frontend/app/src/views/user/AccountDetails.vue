<template>
<div>
    <!-- Profile Info -->
<h2 class="text-xl md:text-3xl font-bold text-gray-800 mb-6 text-center">Account Information</h2>
<div class="bg-white rounded-lg p-4 sm:p-6 shadow border mt-2">
    <div v-if="profileUploading" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-30">
        <div class="bg-white rounded-md p-4 shadow flex items-center gap-3">
            <svg class="animate-spin h-5 w-5 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
            <span class="text-sm text-gray-700">Updating profile...</span>
        </div>
    </div>
    <h3 class="md:text-xl font-semibold mb-6">Personal Info</h3>
    <div class="md:grid md:grid-cols-2 gap-6 space-y-4 md:space-y-0 text-xs md:text-base">

        <div class="flex flex-col col-span-2 mb-2">
            <!-- <label class="mb-1 font-medium text-gray-600">Profile Picture</label> -->
            <div class="flex items-center space-x-4">
                <div class="relative group">
                    <OptimizedImage :src="userData.profile || 'https://placehold.co/150x150/0b845c/white?text=Profile'" alt="Profile" class="w-28 h-28 rounded-full object-cover border-2 border-gray-300" />

                    <!-- Hover overlay with camera icon -->
                    <div class="absolute inset-0 bg-black bg-opacity-50 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center">
                        <CameraIcon class="w-6 h-6 text-white" />
                    </div>

                    <!-- Pencil icon on border -->
                    <div class="absolute -bottom-1 -right-1 bg-primary text-white p-1.5 rounded-full border-2 border-white shadow-sm">
                        <PencilIcon class="h-3" />
                    </div>

                    <label for="profile-upload" class="absolute inset-0 cursor-pointer">
                        <input id="profile-upload" type="file" accept="image/*" class="hidden" @change="handleProfileUpload" />
                    </label>
                </div>
                <div class="text-sm hidden sm:block text-gray-500">
                    Click to upload new image
                </div>
            </div>
        </div>
        <div class="flex flex-col">
            <label class="mb-1 font-medium text-gray-600">Full Name</label>
            <div class="flex items-center rounded-md border p-0.5 bg-gray-50">
                <div class="flex-1 px-4 py-2 text-gray-900 truncate">
                    {{ userData.name || 'Guest' }}
                </div>
                <button @click="openNameModal()" class="px-3 py-1 text-sm text-primary hover:text-primary-dark transition-colors">
                    Edit
                </button>
            </div>
        </div>
        <div class="flex flex-col">
            <label class="mb-1 font-medium text-gray-600">Email</label>
            <div v-if="userData.email" class="flex items-center rounded-md border p-0.5 bg-gray-50">
                <span class="flex items-center px-2 border-r border-gray-300 text-gray-600">
                    <!-- <img :src="GoogleIcon" alt="Google" class="w-6 mr-1" /> -->
                    <img src="@/assets/icons/svgs/GoogleIcon.svg" class="w-4 md:w-5 mr-1" alt="Google" />
                    <span class="hidden md:block">Email</span>
                </span>
                <div class="flex-1 px-4 py-2 text-gray-900 truncate">
                    {{ userData.email }}
                </div>
                <button @click="openEmailModal()" class="px-3 py-1 text-sm text-primary hover:text-primary-dark transition-colors">
                    Edit
                </button>
            </div>
            <div v-else class="flex flex-col space-y-3">
                <button @click="openEmailModal()" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark transition-colors w-fit">
                    <PlusIcon class="w-4 h-4 mr-2" />
                    Add Email
                </button>
                <div class="text-sm text-gray-500 italic">Please enter your email</div>
            </div>
            
            <!-- Toasts now used instead of inline messages -->
        </div>
        <div class="flex flex-col">
            <label class="mb-1 font-medium text-gray-600">Phone Number</label>
            <div v-if="userData.number" class="flex items-center rounded-md border p-0.5 bg-gray-50">
                <span class="flex items-center px-2 border-r border-gray-300 text-gray-600">
                    <img src="https://flagcdn.com/bd.svg" alt="BD" class="w-4 md:w-6 mr-1" />
                    <span class="hidden md:block">+88</span>
                </span>
                <div class="flex-1 px-4 py-2 text-gray-900">
                    {{ userData.number }}
                </div>
                <button @click="openPhoneModal()" class="px-3 py-1 text-sm text-primary hover:text-primary-dark transition-colors">
                    Edit
                </button>
            </div>
            <div v-else class="flex flex-col space-y-3">
                <div class="text-sm text-gray-500 italic">Please enter your number</div>
                <button @click="openPhoneModal()" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark transition-colors w-fit">
                    <PlusIcon class="w-4 h-4 mr-2" />
                    Add Number
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Location Info -->
<div class="bg-white rounded-lg p-6 shadow border mt-6">
    <div class="flex items-center justify-between mb-6">
        <h3 class="md:text-xl font-semibold">Location Information</h3>
        <button v-if="!isEditingLocation" @click="startEditingLocation" class="px-3 py-1 text-sm text-primary hover:text-primary-dark transition-colors border border-primary rounded-md hover:bg-primary/10">
            {{ hasLocationData ? 'Edit' : 'Add' }}
        </button>
    </div>
    
    
    <!--View and Edit Mode -->
    <div class="space-y-4 md:space-y-6 text-xs md:text-base">
        <!-- Search Location -->
        <div class="relative group location-input-container">
            <span class="absolute left-3 top-6 -translate-y-1/2 text-gray-400 pointer-events-none">
                <MapPinIcon class="w-5 h-5" />
            </span>
            <input
                v-model="locationSearch"
                @focus="showLocationDropdown = true"
                @blur="handleInputBlur"
                @input="debouncedSearchLocations"
                type="text"
                :disabled="!isEditingLocation"
                class="block w-full rounded-lg border pl-10 pr-4 py-3 focus:border-primary focus:ring-blue-200 outline-none transition text-base"
                :class="fieldErrors.location_id ? 'border-red-300' : 'border-gray-300'"
                autocomplete="off"
            />
            <label class="absolute left-10 z-10 -translate-y-1/2 bg-white text-gray-500 text-sm transition-all duration-200 pointer-events-none group-focus-within:top-0 group-focus-within:text-xs group-focus-within:left-6 group-focus-within:text-primary group-focus-within:-translate-y-1.5"
                :class="{ 'top-0 text-xs left-6 text-primary': locationSearch, 'top-6': !locationSearch }">
                Search Location
            </label>
            <!-- Individual field error -->
            <div v-if="fieldErrors.location_id" class="mt-1 text-red-500 text-sm">
                {{ fieldErrors.location_id }}
            </div>
            <div v-if="showLocationDropdown && businessLocations.length > 0" class="absolute left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-30 max-h-60 overflow-auto">
                <div
                    v-for="location in businessLocations"
                    :key="location.id"
                    @mousedown.prevent="selectLocation(location)"
                    class="px-4 py-2 cursor-pointer hover:bg-primary/10"
                    :class="{ 'font-semibold text-primary': location.id === locationForm.location_id }"
                >
                    {{ location.upazila_name }}, {{ location.district_name }}
                </div>
            </div>
        </div>
        
        <!-- Post Code -->
        <div class="relative group">
            <span class="absolute left-3 top-6 -translate-y-1/2 text-gray-400 pointer-events-none">
                <MagnifyingGlassIcon class="w-5 h-5" />
            </span>
            <input v-model="locationForm.post_code" type="number" id="postCode" required pattern="\\d{4}" maxlength="4"
                class="block w-full rounded-lg border pl-10 pr-4 py-3 focus:border-primary focus:ring-blue-200 outline-none transition text-base"
                :class="fieldErrors.post_code ? 'border-red-300' : 'border-gray-300'"
                @input="validatePostCode"
                @focus="clearFieldError('post_code')"
                :disabled="!isEditingLocation"
            />
            <label for="postCode"
                class="absolute left-10 z-10 -translate-y-1/2 bg-white text-gray-500 text-sm transition-all duration-200 pointer-events-none group-focus-within:top-0 group-focus-within:text-xs group-focus-within:left-6 group-focus-within:text-primary group-focus-within:-translate-y-1.5"
                :class="{ 'top-0 text-xs left-6 text-primary': locationForm.post_code, 'top-6': !locationForm.post_code }">
                Post Code
            </label>
            <!-- Individual field error -->
            <div v-if="fieldErrors.post_code" class="mt-1 text-red-500 text-sm">
                {{ fieldErrors.post_code }}
            </div>
        </div>
        
        <!-- Full Address -->
        <div class="relative group">
            <span class="absolute left-3 top-4 text-gray-400 pointer-events-none">
                <HomeIcon class="w-5 h-5" />
            </span>
            <textarea v-model="locationForm.address" id="fullAddress" required rows="3"
                class="block w-full rounded-lg border pl-10 pt-3 pr-4 pb-2 focus:border-primary focus:ring-blue-200 outline-none transition text-base resize-none"
                :class="fieldErrors.address ? 'border-red-300' : 'border-gray-300'"
                @input="validateAddress"
                maxlength="200"
                @focus="clearFieldError('address')"
                :disabled="!isEditingLocation"
            ></textarea>
            <label for="fullAddress"
                class="absolute left-10 z-10 bg-white text-gray-500 text-sm transition-all duration-200 pointer-events-none group-focus-within:-top-1 group-focus-within:text-xs group-focus-within:left-6 group-focus-within:text-primary"
                :class="{ '-top-1 text-xs left-6 text-primary': locationForm.address, 'top-4': !locationForm.address }">
                Full Address
            </label>
            <!-- Character count -->
            <div class="absolute right-3 bottom-2 text-xs text-gray-400">
                {{ locationForm.address.length }}/200
            </div>
            <!-- Individual field error -->
            <div v-if="fieldErrors.address" class="mt-1 text-red-500 text-sm">
                {{ fieldErrors.address }}
            </div>
        </div>
        
        <!-- Error Message -->
        <div v-if="locationError" class="bg-red-50 border border-red-200 rounded-md p-3">
            <p class="text-red-600 text-sm">{{ locationError }}</p>
        </div>

        <!-- Success Message -->
        <div v-if="locationSuccess" class="bg-green-50 border border-green-200 rounded-md p-3">
            <p class="text-green-600 text-sm">{{ locationSuccess }}</p>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex gap-3" v-if="isEditingLocation">
            <button @click="updateLocation" :disabled="locationLoading" class="flex-1 bg-primary hover:bg-primary/85 text-xs md:text-sm text-white font-semibold py-3 rounded-lg transition disabled:opacity-60 disabled:cursor-not-allowed">
                <span v-if="locationLoading" class="flex items-center justify-center">
                    <ArrowPathIcon class="animate-spin mr-1 h-5 text-white" />
                    Updating...
                </span>
                <span v-else>
                    {{ hasLocationData ? 'Update' : 'Add' }}
                </span>
            </button>
            <button @click="cancelEditingLocation" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                Cancel
            </button>
        </div>
    </div>
</div>

<!-- Phone Number Input Modal -->
<div v-if="showPhoneModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[55]">
    <div class="bg-white rounded-lg p-2 w-full max-w-md mx-4 md:p-4">
        <div class="items-center justify-between mb-2 hidden md:flex">
            <h3 class="text-lg font-semibold text-gray-900">
                {{ userData.number ? 'Update Phone Number' : 'Add Phone Number' }}
            </h3>
            <button @click="closePhoneModal" class="text-gray-400 hover:text-gray-600">
                <XMarkIcon class="w-6 h-6" />
            </button>
        </div>

        <!-- Mobile Header -->
        <div class="md:hidden mb-2">
            <MobileModalHeader 
                title="Update Phone Number" 
                @back="closePhoneModal" 
            />
        </div>
        <!-- Error Message -->
        <div v-if="errorMessage" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-md">
            <p class="text-sm text-red-600">{{ errorMessage }}</p>
        </div>

        <div class="space-y-4 p-2 md:p-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                <div class="flex text-sm md:text-base">
                    <span class="inline-flex items-center px-3 py-2 border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm rounded-l-md">
                        <img src="https://flagcdn.com/bd.svg" alt="BD" class="w-4 mr-1" />
                        +88
                    </span>
                    <input v-model="phoneNumber" type="tel" placeholder="Enter phone number (11 digits)" :class="[
                                'flex-1 w-full px-2 md:px-3 py-2 border rounded-r-md focus:ring-1 focus:ring-primary focus:border-primary outline-none transition',
                                errorMessage ? 'border-red-300' : 'border-primary'
                            ]" maxlength="11" @input="validatePhoneNumber" @focus="clearMessages" />
                </div>
                <p class="text-xs text-gray-500 mt-1">Enter your 11-digit Bangladesh mobile number (e.g., 013XXXXXXXXX, 017XXXXXXXXX)</p>
            </div>
            <button @click="sendOTP" :disabled="!isValidPhoneNumber || loading || userData?.number == phoneNumber" class="w-full px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                <span v-if="loading">Sending...</span>
                <span v-else>Send OTP</span>
            </button>
        </div>
    </div>
</div>

<!-- OTP Verification Modal -->
<OtpVerificationModal :show="showOtpModal" :mobile="phoneNumber" :loading="otpLoading" :error-message="otpErrorMessage" @submit="verifyOTP" @resend="resendOTP" @close="closeOtpModal" />

<!-- Email Input Modal -->
<div v-if="showEmailModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[55]">
    <div class="bg-white rounded-lg p-0.5 w-full max-w-md mx-4 md:p-4">
        <div class=" items-center justify-between mb-4 hidden md:flex">
            <h3 class="text-lg font-semibold text-gray-900">
                {{ userData.email ? 'Update Google Email' : 'Add Google Email' }}
            </h3>
            <button @click="closeEmailModal" class="text-gray-400 hover:text-gray-600">
                <XMarkIcon class="w-6 h-6" />
            </button>
        </div>
        <!-- Mobile Header -->
        <div class="md:hidden mb-2">
            <MobileModalHeader 
                title="Update Gmail" 
                @back="closeEmailModal" 
            />
        </div>
        <!-- Error Message -->
        <div v-if="emailErrorMessage" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-md">
            <div class="flex items-start">
                <XCircleIcon class="w-5 h-5 text-red-500 mr-2 mt-0.5 flex-shrink-0" />
                <div>
                    <p class="text-sm text-red-600 font-medium">Authentication Failed</p>
                    <p class="text-sm text-red-600 mt-1">{{ emailErrorMessage }}</p>
                    <p class="text-xs text-red-500 mt-2">Please try again or contact support if the issue persists.</p>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        <div v-if="emailSuccessMessage" class="mb-4 p-3 bg-green-50 border border-green-200 rounded-md">
            <p class="text-sm text-green-600">{{ emailSuccessMessage }}</p>
        </div>

        <div class="space-y-4 p-4 md:p-6">
            
            <div class="text-center">
                <!-- Mobile Header -->

                <p class="text-gray-600 mb-4 text-sm md:text-base">
                    {{ userData.email ? 'Update your Google account email address' : 'Connect your Google account to add an email address' }}
                </p>
                
                <button @click="authenticateWithGoogle" :disabled="emailLoading" class="w-full text-sm md:text-base flex items-center justify-center gap-3 border border-gray-300 bg-white hover:bg-gray-100 text-gray-700 font-semibold py-3 px-4 rounded-md transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <img src="@/assets/icons/svgs/GoogleIcon.svg" class="w-5 h-5" alt="Google" />
                    <span v-if="emailLoading">Authenticating...</span>
                    <span v-else>{{ userData.email ? 'Update with Google' : 'Continue with Google' }}</span>
                </button>
                
                <!-- Retry button when there's an error -->
                <button v-if="emailErrorMessage" @click="retryGoogleAuthentication" class="w-full mt-3 flex items-center justify-center gap-3 border border-red-300 bg-red-50 hover:bg-red-100 text-red-700 font-semibold py-2 px-4 rounded-md transition">
                    <ArrowPathIcon class="w-4 h-4" />
                    Retry Google Authentication
                </button>
                
                <p class="text-xs text-gray-500 mt-3">
                    This will open a Google authentication window to securely update your email address.
                </p>
            </div>
        </div>
    </div>
</div>

    <!-- Enter Name Modal -->
    <EnterNameModal :show="showNameModal" message="Update your name" :old-name="userData.name" @close="closeNameModal" />
</div>
</template>

<script>
import authMixin from '@/mixins/authMixin.js'
import { validateImageFile } from '@/utils/imageValidation.js'
import OptimizedImage from '@/components/common/OptimizedImage.vue'
import {
    push
} from 'notivue'
import {
    CameraIcon,
    PencilIcon,
    PlusIcon,
    XMarkIcon,
    CheckCircleIcon,
    XCircleIcon,
    ArrowPathIcon,
    MapPinIcon,
    MagnifyingGlassIcon,
    HomeIcon
} from '@heroicons/vue/24/outline'
import OtpVerificationModal from '@/components/modals/OtpVerificationModal.vue'
import EnterNameModal from '@/components/modals/EnterNameModal.vue'
import GoogleIcon from '@/assets/icons/svgs/GoogleIcon.svg'
import MobileModalHeader from '@/components/common/MobileModalHeader.vue'
import { useModalScroll } from '@/composables/useModalScroll'

export default {
    components: {
        CameraIcon,
        PencilIcon,
        PlusIcon,
        XMarkIcon,
        OtpVerificationModal,
        OptimizedImage,
        EnterNameModal,
        GoogleIcon,
        CheckCircleIcon,
        XCircleIcon,
        ArrowPathIcon,
        MapPinIcon,
        MagnifyingGlassIcon,
        HomeIcon,
        MobileModalHeader
    },
    mixins: [authMixin],
    data() {
        return {
            showNameModal: false,
            showPhoneModal: false,
            showOtpModal: false,
            showEmailModal: false,
            phoneNumber: '',
            loading: false,
            profileUploading: false,
            errorMessage: '',
            otpLoading: false,
            otpErrorMessage: '',
            emailLoading: false,
            emailErrorMessage: '',
            emailSuccessMessage: '',
            // Location editing state
            isEditingLocation: false,
            locationForm: {
                address: '',
                post_code: '',
                location_id: null
            },
            locationSearch: '',
            showLocationDropdown: false,
            locationLoading: false,
            locationError: null,
            locationSuccess: null,
            searchTimeout: null,
            // Individual field errors
            fieldErrors: {
                address: null,
                post_code: null,
                location_id: null
            }
        }
    },
    computed: {
        userData() {
            // Use authenticated user data from store, with fallback to empty object
            const user = this.$store.getters.user;
            return user || {
                name: '',
                email: '',
                number: null,
                profile: ''
            }
        },

        isValidPhoneNumber() {
            return this.isValidBangladeshNumber(this.phoneNumber);
        },
        
        hasLocationData() {
            return !!(this.userData.location || this.userData.post_code || this.userData.address);
        },
        
        businessLocations() {
            return this.$store.getters.businessLocations;
        }
    },
    mounted() {
        // Check for any Google email update errors or success messages
        this.checkForGoogleEmailUpdateErrors();
        
        // Check for messages when window regains focus (user returns from OAuth)
        window.addEventListener('focus', () => {
            this.checkForGoogleEmailUpdateErrors();
        });
        
        // Fetch business locations for location editing
        this.$store.dispatch('fetchBusinessLocations', '');
        
        this.locationForm = {
                address: this.userData.address || '',
                post_code: this.userData.post_code || '',
                location_id: this.userData.location?.id || null
            };
        this.locationSearch = this.userData.location ? 
            `${this.userData.location.upazila_name}, ${this.userData.location.district_name}` : '';
    },
    
    beforeUnmount() {
        if (this.searchTimeout) {
            clearTimeout(this.searchTimeout);
        }
    },
    methods: {

        async handleProfileUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            // Validate file using utility function
            const validation = validateImageFile(file);
            if (!validation.isValid) {
                push.error(validation.error);
                return;
            }

            try {
                // Call API to update profile with loader
                this.profileUploading = true;
                // await this.updateProfile(file);
                
                const response = await this.$store.dispatch('updateUserProfile', {
                    profile: file
                });
                
                if (response.status) {
                    push.success('Profile updated');
                } else {
                    push.error(response.data.profile[0]);
                }
                // Clear the input
                event.target.value = '';

            } catch (error) {
                // Silent error handling
                push.error('Failed to update profile');
            } finally {
                this.profileUploading = false;
            }
        },

        openNameModal() {
            this.showNameModal = true;
        },

        closeNameModal() {
            this.showNameModal = false;
        },

        openPhoneModal() {
            this.showPhoneModal = true;
            this.phoneNumber = '';
            this.loading = false;
            this.errorMessage = '';
            this.openModal('phone-modal');
        },

        closePhoneModal() {
            this.showPhoneModal = false;
            this.errorMessage = '';
            this.closeModal('phone-modal');
        },

        clearMessages() {
            this.errorMessage = '';
        },

        async sendOTP() {
            if (!this.phoneNumber || this.phoneNumber.length < 11) {
                this.errorMessage = 'Please enter a valid 11-digit mobile number';
                return;
            }

            // Validate Bangladesh mobile number format
            if (!this.isValidBangladeshNumber(this.phoneNumber)) {
                this.errorMessage = 'Mobile number must start with 013, 014, 015, 016, 017, 018, or 019';
                return;
            }

            this.loading = true;
            this.errorMessage = '';
            try {
                    await this.$store.dispatch('sendOTP', this.phoneNumber);
                this.showOtpModal = true; // Open OTP modal
                this.closePhoneModal(); // Close phone input modal
            } catch (error) {
                this.errorMessage = 'Failed to send OTP. Please try again.';
                console.error(error);
            } finally {
                this.loading = false;
            }
        },

        async verifyOTP(otp) {
            this.otpLoading = true;
            this.otpErrorMessage = '';
            try {
                await this.$store.dispatch('verifyOTP', {
                    number: this.phoneNumber,
                    otp: otp,
                    profile_update: true
                });
                // Close OTP modal and show success
                this.closeOtpModal();
                // Refresh user data
                await this.$store.dispatch('fetchUser');
                // Toast success
                push.success('Phone number updated successfully!');
            } catch (error) {
                this.otpErrorMessage = error.response.data.message;
                if(error.response.data.message == "This number is unavailable!"){
                    push.error(error.response.data.message);
                    this.closeOtpModal();
                }
            } finally {
                this.otpLoading = false;
            }
        },

        async resendOTP() {
            this.otpLoading = true;
            this.otpErrorMessage = '';
            try {
                await this.$store.dispatch('sendOTP', this.phoneNumber);
                // Clear any previous error messages
                this.otpErrorMessage = '';
            } catch (error) {
                this.otpErrorMessage = 'Failed to resend OTP. Please try again.';
                console.error(error);
            } finally {
                this.otpLoading = false;
            }
        },

        validatePhoneNumber() {
            // Remove any non-digit characters
            let value = this.phoneNumber.replace(/\D/g, '');

            // Limit to 11 digits
            if (value.length > 11) {
                value = value.slice(0, 11);
            }

            this.phoneNumber = value;
            this.errorMessage = '';

            // Validate number using the same pattern as FreeListingView
            if (value.length > 0 && value.length < 11) {
                this.errorMessage = 'Please enter a valid 11-digit mobile number';
            } else if (value.length === 11 && !/^01[3-9][0-9]{8}$/.test(value)) {
                this.errorMessage = 'Mobile number must start with 013, 014, 015, 016, 017, 018, or 019';
            } else if (value.length === 11) {
                this.errorMessage = '';
            }
        },

        isValidBangladeshNumber(number) {
            // Use the same pattern as FreeListingView: 01[3-9][0-9]{8}
            return /^01[3-9][0-9]{8}$/.test(number);
        },
        closeOtpModal() {
            this.showOtpModal = false;
            this.otpErrorMessage = '';
        },
        openEmailModal() {
            this.emailErrorMessage = '';
            this.emailSuccessMessage = '';
            this.showEmailModal = true;
            this.openModal('email-modal');
            
            // Check for any Google email update errors or success messages
            this.checkForGoogleEmailUpdateErrors();
        },
        closeEmailModal() {
            this.showEmailModal = false;
            this.emailErrorMessage = '';
            this.emailSuccessMessage = '';
            this.closeModal('email-modal');
        },
        async authenticateWithGoogle() {
            this.emailLoading = true;
            this.emailErrorMessage = '';
            this.emailSuccessMessage = '';

            try {
                await this.$store.dispatch('authenticateWithGoogle');
                // The user will be redirected to Google OAuth, so this code won't execute immediately
                // The result will be handled in the Google callback
            } catch (error) {
                // This will only execute if there's an immediate error (not OAuth related)
                const msg = error.response?.data?.message || 'Failed to initiate Google authentication.';
                this.emailErrorMessage = msg;
                push.error(msg);
                console.error('Google authentication error:', error);
            } finally {
                this.emailLoading = false;
            }
        },
        retryGoogleAuthentication() {
            this.emailLoading = true;
            this.emailErrorMessage = '';
            this.emailSuccessMessage = '';
            
            // Call the authenticate method
            this.authenticateWithGoogle();
        },
        checkForGoogleEmailUpdateErrors() {
            // Check for Google email update errors
            const errorMessage = localStorage.getItem('googleEmailUpdateError');
            if (errorMessage) {
                // Show toast and clear local
                this.emailErrorMessage = errorMessage;
                push.error(errorMessage);
                
                // Clear localStorage after processing
                localStorage.removeItem('googleEmailUpdateError');
            }
            
            // Check for Google email update success
            const successMessage = localStorage.getItem('googleEmailUpdateSuccess');
            if (successMessage) {
                // Show toast and clear local
                this.emailSuccessMessage = successMessage;
                push.success(successMessage);
                
                // Clear localStorage after processing
                localStorage.removeItem('googleEmailUpdateSuccess');
            }
        },
        clearEmailMessages() {
            this.emailErrorMessage = '';
            this.emailSuccessMessage = '';
        },
        
        // Location editing methods
        startEditingLocation() {
            this.isEditingLocation = true;
            this.locationError = null;
            this.locationSuccess = null;
            this.clearAllFieldErrors();
            
            // Fetch business locations if not already loaded
            if (!this.businessLocations.length) {
                this.$store.dispatch('fetchBusinessLocations', '');
            }
        },
        
        cancelEditingLocation() {
            this.isEditingLocation = false;
            this.locationError = null;
            this.locationSuccess = null;
            this.showLocationDropdown = false;
            this.clearAllFieldErrors();
        },
        
        async updateLocation() {
            this.locationError = null;
            this.locationSuccess = null;
            this.clearAllFieldErrors();
            
            // Validate required fields
            if (!this.locationForm.address || !this.locationForm.post_code || !this.locationForm.location_id) {
                if (!this.locationForm.address) this.fieldErrors.address = 'Address is required';
                if (!this.locationForm.post_code) this.fieldErrors.post_code = 'Post code is required';
                if (!this.locationForm.location_id) this.fieldErrors.location_id = 'Location is required';
                return;
            }
            
            // Validate post code format (4 digits)
            if (!/^\d{4}$/.test(this.locationForm.post_code)) {
                this.fieldErrors.post_code = 'Post code must be exactly 4 digits';
                return;
            }
            
            // Trim address only when submitting and validate length
            const trimmedAddress = this.locationForm.address.trim();
            if (trimmedAddress.length < 10) {
                this.fieldErrors.address = 'Address must be at least 10 characters long';
                return;
            }
            
            this.locationLoading = true;
            try {
                const response = await this.$store.dispatch('updateUserLocation', {
                    address: trimmedAddress,
                    post_code: this.locationForm.post_code,
                    location_id: this.locationForm.location_id
                });
                
                if (response.status) {
                    this.locationSuccess = this.hasLocationData ? 'Location updated successfully!' : 'Location added successfully!';
                    this.isEditingLocation = false;
                    push.success('Location updated successfully!');
                    
                    // Clear success message after 3 seconds
                    setTimeout(() => {
                        this.locationSuccess = null;
                    }, 3000);
                } else {
                    this.locationError = response.message || 'Failed to update location';
                    push.error(this.locationError);
                }
            } catch (error) {
                console.error('Error updating user location:', error);
                this.locationError = error.response?.data?.message || 'Failed to update location. Please try again.';
            } finally {
                this.locationLoading = false;
            }
        },
        
        // Location search methods
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
            this.locationForm.location_id = location.id;
            this.locationSearch = `${location.upazila_name}, ${location.district_name}`;
            this.showLocationDropdown = false;
            this.clearFieldError('location_id');
        },
        
        handleInputBlur() {
            setTimeout(() => {
                this.showLocationDropdown = false;
            }, 150);
        },
        
        // Location validation methods
        validatePostCode() {
            let value = this.locationForm.post_code.toString().replace(/\D/g, '');
            if (value.length > 4) {
                value = value.slice(0, 4);
            }
            this.locationForm.post_code = value;
            // Clear field error when user starts typing
            this.clearFieldError('post_code');
        },
        
        validateAddress() {
            // Don't trim on input - only trim when submitting
            // This allows users to type spaces normally
            // Clear field error when user starts typing
            this.clearFieldError('address');
        },
        
        clearLocationError() {
            this.locationError = null;
        },
        
        clearFieldError(fieldName) {
            if (this.fieldErrors[fieldName]) {
                this.fieldErrors[fieldName] = null;
            }
        },
        
        clearAllFieldErrors() {
            this.fieldErrors = {
                address: null,
                post_code: null,
                location_id: null
            };
        }
    },
    setup() {
        const { openModal, closeModal } = useModalScroll();
        return { openModal, closeModal };
    }
}
</script>

<style scoped>
</style>
