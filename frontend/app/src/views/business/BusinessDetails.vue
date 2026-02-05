<template>
<div>
    <AdminDashboardHeader title="Business Details" />
    
    <!-- Loading State - Centered -->
    <div v-if="loading" class="flex items-center justify-center min-h-[60vh]">
        <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mx-auto"></div>
            <p class="mt-4 text-gray-600">Loading business details...</p>
        </div>
    </div>

    <!-- Main Content -->
    <div v-else class="max-w-2xl md:mx-auto m-2 sm:m-4 md:mt-8 bg-white rounded-xl shadow border p-4 md:p-8">
        <!-- Server Error Display -->
        <div v-if="serverErrors.length > 0" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-center">
                <ExclamationTriangleIcon class="w-5 h-5 text-red-400 mr-2" />
                <h3 class="text-red-800 font-medium">Please fix the following errors:</h3>
            </div>
            <ul class="mt-2 text-red-700 text-sm">
                <li v-for="(error, index) in serverErrors" :key="index" class="ml-6 list-disc">
                    {{ error }}
                </li>
            </ul>
        </div>

        <h2 class="text-2xl font-bold text-primary mb-2">Business Details</h2>
        <p class="text-gray-600 mb-6">Manage your business information and settings</p>
        
        <form @submit.prevent="onSubmit" class="space-y-6">
            <!-- Business Profile (Profile Picture Upload) -->
            <div class="flex justify-between">
                <div class="flex flex-col items-start gap-2 mb-6">
                <label class="font-medium text-gray-700 mb-1">Business Profile Picture</label>
                <div class="relative group">
                    <div class="w-32 h-32 bg-gray-100 flex items-center justify-center overflow-hidden border border-gray-300 rounded-lg relative">
                        <OptimizedImage v-if="profilePreview || currentProfile" :src="profilePreview || currentProfile" alt="Profile Preview" class="object-cover w-full h-full" />
                        <CameraIcon v-else class="w-12 h-12 text-gray-400" />
                        
                        <!-- Loading overlay when updating -->
                        <div v-if="profileUpdating" class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-white"></div>
                        </div>
                        
                        <!-- Hover overlay with camera icon -->
                        <div v-else class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                            <CameraIcon class="w-8 h-8 text-white" />
                        </div>
                        
                        <!-- Edit icon at top-right corner -->
                        <div class="absolute top-2 right-2 bg-primary rounded-full p-2 shadow-md">
                            <PencilIcon class="w-3 text-white" />
                        </div>
                    </div>
                    
                    <!-- Hidden file input -->
                    <input 
                        type="file" 
                        accept="image/*" 
                        @change="onProfileChange" 
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" 
                    />
                    
                </div>
            </div>
            <div>
                <router-link :to="`/${myBusiness?.slug}`" target="_blank" class="text-primary flex items-center gap-2 border border-primary rounded-md px-3 py-1.5 hover:bg-primary/10 transition-colors">
                    <EyeIcon class="w-5 h-5" />
                    Preview</router-link>
            </div>
            </div>

            <!-- Contact Name -->
            <div class="relative group mb-6">
                <span class="absolute left-4 top-6 -translate-y-1/2 text-gray-400">
                    <UserIcon class="w-5 h-5" />
                </span>
                <Field 
                    v-model="form.contactName" 
                    name="contactName"
                    type="text" 
                    id="contact-name" 
                    class="block w-full rounded-lg border pl-12 py-3 focus:border-primary focus:ring-blue-200 outline-none transition placeholder-transparent text-base" 
                    :class="{ 'border-red-300 focus:border-red-500': slotProps?.errors?.contactName, 'border-gray-300': !slotProps?.errors?.contactName }"
                    placeholder="Contact Name"
                    rules="required|min:2|max:50|alpha_spaces" />
                <label for="contact-name" 
                    class="absolute left-12 z-10 -translate-y-1/2 bg-white text-sm transition-all duration-200 pointer-events-none group-focus-within:top-0 group-focus-within:text-xs group-focus-within:left-8 group-focus-within:-translate-y-1.5" 
                    :class="{ 
                        'top-0 text-xs left-8 text-primary': form.contactName && !slotProps?.errors?.contactName, 
                        'top-0 text-xs left-8 text-red-500': slotProps?.errors?.contactName,
                        'top-6 text-gray-500': !form.contactName && !slotProps?.errors?.contactName 
                    }">
                    Contact Name
                </label>
                <!-- Error Message -->
                <div v-if="slotProps?.errors?.contactName" class="mt-1 text-red-500 text-sm flex items-center">
                    <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                    {{ slotProps?.errors?.contactName }}
                </div>
            </div>

            <!-- Business Name -->
            <div class="relative group mb-6">
                <span class="absolute left-4 top-6 -translate-y-1/2 text-gray-400">
                    <BuildingOfficeIcon class="w-5 h-5" />
                </span>
                <Field 
                    v-model="form.businessName" 
                    name="businessName"
                    type="text" 
                    id="business-name" 
                    class="block w-full rounded-lg border pl-12 py-3 focus:border-primary focus:ring-blue-200 outline-none transition placeholder-transparent text-base" 
                    :class="{ 'border-red-300 focus:border-red-500': slotProps?.errors?.businessName, 'border-gray-300': !slotProps?.errors?.businessName }"
                    placeholder="Business Name"
                    rules="required|min:2|max:100" />
                <label for="business-name" 
                    class="absolute left-12 z-10 -translate-y-1/2 bg-white text-sm transition-all duration-200 pointer-events-none group-focus-within:top-0 group-focus-within:text-xs group-focus-within:left-8 group-focus-within:-translate-y-1.5" 
                    :class="{ 
                        'top-0 text-xs left-8 text-primary': form.businessName && !slotProps?.errors?.businessName, 
                        'top-0 text-xs left-8 text-red-500': slotProps?.errors?.businessName,
                        'top-6 text-gray-500': !form.businessName && !slotProps?.errors?.contactName 
                    }">
                    Business Name
                </label>
                <!-- Error Message -->
                <div v-if="slotProps?.errors?.businessName" class="mt-1 text-red-500 text-sm flex items-center">
                    <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                    {{ slotProps.errors.businessName }}
                </div>
            </div>

            <!-- About Business -->
            <div class="relative group mb-6">
                <span class="absolute left-4 top-4 text-gray-400">
                    <DocumentTextIcon class="w-5 h-5" />
                </span>
                <Field 
                    v-model="form.aboutBusiness" 
                    name="aboutBusiness"
                    as="textarea"
                    id="about-business" 
                    rows="3" 
                    class="block w-full rounded-lg border pl-12 pt-3 pb-2 focus:border-primary focus:ring-blue-200 outline-none transition placeholder-transparent text-base resize-none" 
                    :class="{ 'border-red-300 focus:border-red-500': slotProps?.errors?.aboutBusiness, 'border-gray-300': !slotProps?.errors?.aboutBusiness }"
                    placeholder="About Business"
                    rules="required|min:10|max:500" />
                <label for="about-business" 
                    class="absolute left-12 z-10 bg-white text-sm transition-all duration-200 pointer-events-none group-focus-within:-top-1 group-focus-within:text-xs group-focus-within:left-8 group-focus-within:-translate-y-1.5" 
                    :class="{ 
                        '-top-1 text-xs left-8 text-primary': form.aboutBusiness && !slotProps?.errors?.aboutBusiness, 
                        '-top-1 text-xs left-8 text-red-500': slotProps?.errors?.aboutBusiness,
                        'top-3 text-gray-500': !form.aboutBusiness && !slotProps?.errors?.aboutBusiness 
                    }">
                    About Business
                </label>
                <!-- Error Message -->
                <div v-if="slotProps?.errors?.aboutBusiness" class="mt-1 text-red-500 text-sm flex items-center">
                    <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                    {{ slotProps.errors.aboutBusiness }}
                </div>
            </div>

            <!-- Established -->
            <div class="relative group mb-6">
                <span class="absolute left-4 top-6 -translate-y-1/2 text-gray-400">
                    <CalendarIcon class="w-5 h-5" />
                </span>
                <Field 
                    v-model="form.established" 
                    name="established"
                    type="number" 
                    id="established" 
                    min="1800" 
                    :max="currentYear" 
                    step="1" 
                    class="block w-full rounded-lg border pl-12 py-3 focus:border-primary focus:ring-blue-200 outline-none transition placeholder-transparent text-base" 
                    :class="{ 'border-red-300 focus:border-red-500': slotProps?.errors?.established, 'border-gray-300': !slotProps?.errors?.established }"
                    placeholder="e.g. 2005 (Optional)"
                    :rules="`numeric|min_value:1800|max_value:${currentYear}`" />
                <label for="established" 
                    class="absolute left-12 z-10 -translate-y-1/2 bg-white text-sm transition-all duration-200 pointer-events-none group-focus-within:top-0 group-focus-within:text-xs group-focus-within:left-8 group-focus-within:-translate-y-1.5" 
                    :class="{ 
                        'top-0 text-xs left-8 text-primary': form.established && !slotProps?.errors?.established, 
                        'top-0 text-xs left-8 text-red-500': slotProps?.errors?.established,
                        'top-6 text-gray-500': !form.established && !slotProps?.errors?.established 
                    }">
                    Established Year (Optional)
                </label>
                <!-- Error Message -->
                <div v-if="slotProps?.errors?.established" class="mt-1 text-red-500 text-sm flex items-center">
                    {{ slotProps.errors.established }}
                </div>
            </div>
            
            <!-- Number of Employee (Custom Dropdown, Flexible Direction) -->
            <div class="relative group mb-6" ref="dropdownWrapper">
                <span class="absolute left-4 top-6 -translate-y-1/2 text-gray-400 z-10">
                    <UsersIcon class="w-5 h-5" />
                </span>
                <div @click="toggleDropdown" tabindex="0"
                    class="block w-full rounded-lg border pl-12 pr-10 py-3 text-gray-700 font-medium bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition text-base hover:border-primary/70 shadow-sm cursor-pointer relative select-none"
                    :class="{ 'border-red-300 focus:border-red-500': slotProps?.errors?.numberOfEmployee, 'border-gray-300': !slotProps?.errors?.numberOfEmployee }">
                    <span v-if="form.numberOfEmployee">{{ form.numberOfEmployee }}</span>
                    <span v-else class="text-gray-400">Select number of employees (Optional)</span>
                    <!-- Heroicons chevron -->
                    <ChevronDownIcon v-if="!dropdownOpen || dropdownDirection === 'down'" class="pointer-events-none absolute right-4 top-6 -translate-y-1/2 w-5 h-5 text-gray-400 transition-transform duration-200" />
                    <ChevronUpIcon v-else class="pointer-events-none absolute right-4 top-6 -translate-y-1/2 w-5 h-5 text-gray-400 transition-transform duration-200" />
                    <!-- Dropdown -->
                    <transition name="fade">
                        <ul v-if="dropdownOpen" :class="[
                            'absolute left-0 w-full bg-gray-50 border border-gray-200 rounded-lg shadow-lg z-30 py-1',
                            dropdownDirection === 'up' ? 'bottom-full mb-2' : 'mt-2'
                        ]">
                            <li v-for="option in employeeOptions" :key="option"
                                @mousedown.prevent="selectEmployee(option)"
                                :class="[
                                    'px-4 py-2 cursor-pointer transition',
                                    form.numberOfEmployee === option ? 'bg-primary/10 text-primary font-semibold' : 'hover:bg-gray-200 text-gray-700'
                                ]">
                                {{ option }}
                            </li>
                        </ul>
                    </transition>
                </div>
                <!-- Error Message -->
                <div v-if="slotProps?.errors?.numberOfEmployee" class="mt-1 text-red-500 text-sm flex items-center">
                    <ExclamationCircleIcon class="w-4 h-4 mr-1" />
                    {{ slotProps.errors.numberOfEmployee }}
                </div>
            </div>

            <button type="submit" :disabled="slotProps?.isSubmitting || loading" class="w-full bg-primary hover:bg-primary/85 text-white font-semibold py-2 rounded-md transition disabled:opacity-60 disabled:cursor-not-allowed">
                {{ slotProps?.isSubmitting ? 'Saving...' : 'Save Details' }}
            </button>
        </form>
    </div>
</div>
</template>

<script>
import AdminDashboardHeader from '@/components/business/AdminDashboardHeader.vue';
import {
    UserIcon,
    BuildingOfficeIcon,
    CameraIcon,
    DocumentTextIcon,
    CalendarIcon,
    UsersIcon,
    ChevronDownIcon,
    ChevronUpIcon,
    ExclamationTriangleIcon,
    CheckCircleIcon,
    ExclamationCircleIcon,
    PencilIcon,
    EyeIcon
} from '@heroicons/vue/24/outline';
import { mapGetters, mapActions } from 'vuex';
import { push } from 'notivue';
import { ref, computed, watch, nextTick } from 'vue';
import { validateImageFile } from '@/utils/imageValidation.js';
import OptimizedImage from '@/components/common/OptimizedImage.vue';

export default {
    components: {
        AdminDashboardHeader,
        UserIcon,
        BuildingOfficeIcon,
        CameraIcon,
        DocumentTextIcon,
        CalendarIcon,
        UsersIcon,
        ChevronDownIcon,
        ChevronUpIcon,
        ExclamationTriangleIcon,
        CheckCircleIcon,
        ExclamationCircleIcon,
        PencilIcon,
        EyeIcon,
        OptimizedImage
    },
    data() {
        return {
            form: {
                contactName: '',
                businessName: '',
                aboutBusiness: '',
                established: '',
                numberOfEmployee: '',
                businessProfile: null
            },
            profilePreview: null,
            currentProfile: null,
            dropdownOpen: false,
            dropdownDirection: 'down',
            employeeOptions: [
                'Less than 10',
                '10 to 50',
                '51 to 100',
                '101 to 500',
                '500+'
            ],
            loading: false,
            profileUpdating: false,
            serverErrors: [],
            successMessage: ''
        }
    },
    computed: {
        ...mapGetters(['myBusiness', 'myBusinessDetails']),
        currentYear() {
            return new Date().getFullYear();
        }
    },
    async mounted() {
        await this.loadBusinessData();
        // Add click outside handler for dropdown
        document.addEventListener('click', this.handleClickOutside);
    },
    beforeUnmount() {
        // Remove click outside handler
        document.removeEventListener('click', this.handleClickOutside);
    },
    methods: {
        ...mapActions(['fetchMyBusiness', 'fetchMyBusinessDetails', 'updateBusinessInfo', 'updateBusinessDetails', 'updateBusinessProfile']),

        async loadBusinessData() {
            try {
                this.loading = true;
                this.serverErrors = [];
                
                // Fetch business data if not already loaded
                if (!this.myBusiness) {
                    await this.fetchMyBusiness();
                }
                if (!this.myBusinessDetails) {
                    await this.fetchMyBusinessDetails();
                }

                // Populate form with existing data
                this.populateForm();
            } catch (error) {
                console.error('Error loading business data:', error);
                this.serverErrors = ['Failed to load business data. Please try again.'];
            } finally {
                this.loading = false;
            }
        },

        populateForm() {
            // Populate from myBusiness (contact name, business name, profile)
            if (this.myBusiness) {
                this.form.contactName = this.myBusiness.name || '';
                this.form.businessName = this.myBusiness.business_name || '';
                this.currentProfile = this.myBusiness.business_profile || null;
            }

            // Populate from myBusinessDetails (about, established, number of employees)
            if (this.myBusinessDetails) {
                this.form.aboutBusiness = this.myBusinessDetails.about || '';
                this.form.established = this.myBusinessDetails.established || '';
                this.form.numberOfEmployee = this.myBusinessDetails.number_of_employee || '';
            }
        },

        onProfileChange(e) {
            const file = e.target.files[0];
            if (file) {
                this.form.businessProfile = file;
                this.profilePreview = URL.createObjectURL(file);
                
                // Directly update profile picture when image is selected
                this.updateProfilePicture();
            } else {
                this.form.businessProfile = null;
                this.profilePreview = null;
            }
        },

        async updateProfilePicture() {
            try {
                this.profileUpdating = true;
                this.serverErrors = [];

                // Validate file using utility function
                const validation = validateImageFile(this.form.businessProfile);
                if (!validation.isValid) {
                    return;
                }

                // Call store action to update profile
                const response = await this.updateBusinessProfile({
                    business_profile: this.form.businessProfile
                });

                if (response.status) {
                    push.success('Profile picture updated successfully!');
                    this.currentProfile = this.profilePreview;
                    this.profilePreview = null;
                    this.form.businessProfile = null;
                } else {
                    push.error(response.message || 'Failed to update profile picture');
                }
            } catch (error) {
                console.error('Error updating profile picture:', error);
                if (error.response?.data?.errors) {
                    const errorMessages = Object.values(error.response.data.errors).flat();
                    push.error(errorMessages[0] || 'Failed to update profile picture');
                } else {
                    push.error('Failed to update profile picture. Please try again.');
                }
            } finally {
                this.profileUpdating = false;
            }
        },



        toggleDropdown() {
            if (!this.dropdownOpen) {
                this.$nextTick(() => {
                    const wrapper = this.$refs.dropdownWrapper;
                    const rect = wrapper.getBoundingClientRect();
                    const spaceBelow = window.innerHeight - rect.bottom;
                    const spaceAbove = rect.top;
                    // 200px is an estimated dropdown height
                    this.dropdownDirection = spaceBelow < 200 && spaceAbove > spaceBelow ? 'up' : 'down';
                });
            }
            this.dropdownOpen = !this.dropdownOpen;
        },

        selectEmployee(value) {
            this.form.numberOfEmployee = value;
            this.dropdownOpen = false;
        },

        handleClickOutside(event) {
            if (this.$refs.dropdownWrapper && !this.$refs.dropdownWrapper.contains(event.target)) {
                this.dropdownOpen = false;
            }
        },

        async onSubmit(values) {
            try {
                this.loading = true;
                this.serverErrors = [];

                // Update business info (contact name, business name)
                await this.updateBusinessInfo({
                    name: this.form.contactName,
                    business_name: this.form.businessName
                });

                // Update business details (about, established, number of employees)
                await this.updateBusinessDetails({
                    about: this.form.aboutBusiness,
                    established: this.form.established || null,
                    number_of_employee: this.form.numberOfEmployee || null
                });

                push.success('Business details updated successfully!');
                
                // Refresh data
                await this.loadBusinessData();
            } catch (error) {
                console.error('Error updating business details:', error);
                
                if (error.response?.data?.errors) {
                    // Handle server-side validation errors
                    const errorMessages = Object.values(error.response.data.errors).flat();
                    push.error(errorMessages[0] || 'Failed to update business details');
                } else if (error.response?.data?.message) {
                    push.error(error.response.data.message);
                } else {
                    push.error('Failed to update business details. Please try again.');
                }
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
</style>