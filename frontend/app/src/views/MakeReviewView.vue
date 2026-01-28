<template>
    <div>
    <div class="min-h-screen py-8">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Write Review</h1>
                </div>

                <!-- Loading State -->
                <div v-if="loading" class="border rounded-lg shadow-md p-4 sm:p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
                        <!-- Left Column Skeleton -->
                        <div class="space-y-4 sm:space-y-6">
                            <!-- Business Information Skeleton -->
                            <div class="flex items-start space-x-3 sm:space-x-4">
                                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gray-200 rounded-lg animate-pulse flex-shrink-0"></div>
                                <div class="flex-1 min-w-0">
                                    <div class="h-5 sm:h-6 bg-gray-200 rounded w-32 sm:w-48 mb-1 sm:mb-2 animate-pulse"></div>
                                    <div class="h-3 sm:h-4 bg-gray-200 rounded w-24 sm:w-32 animate-pulse"></div>
                                </div>
                            </div>

                            <!-- Rating Section Skeleton -->
                            <div>
                                <div class="h-5 sm:h-6 bg-gray-200 rounded w-48 sm:w-64 mb-3 sm:mb-4 animate-pulse"></div>
                                <div class="flex mb-3 sm:mb-4 gap-1">
                                    <div v-for="i in 5" :key="i" class="w-8 h-8 sm:w-10 sm:h-10 bg-gray-200 rounded-md animate-pulse"></div>
                                </div>
                                <div class="h-6 sm:h-8 bg-gray-200 rounded-full w-20 sm:w-24 animate-pulse"></div>
                            </div>
                        </div>

                        <!-- Right Column Skeleton -->
                        <div class="space-y-4 sm:space-y-6">
                            <!-- Review Text Area Skeleton -->
                            <div>
                                <div class="flex items-center justify-between mb-2 sm:mb-3">
                                    <div class="h-5 sm:h-6 bg-gray-200 rounded w-32 sm:w-48 animate-pulse"></div>
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 sm:w-4 sm:h-4 bg-gray-200 rounded mr-1 animate-pulse"></div>
                                        <div class="h-3 sm:h-4 bg-gray-200 rounded w-6 sm:w-8 animate-pulse"></div>
                                    </div>
                                </div>
                                <div class="w-full h-24 sm:h-32 bg-gray-200 rounded-lg animate-pulse"></div>
                            </div>

                            <!-- Photo Upload Section Skeleton -->
                            <div>
                                <div class="h-5 sm:h-6 bg-gray-200 rounded w-24 sm:w-32 mb-2 sm:mb-3 animate-pulse"></div>
                                <div class="grid grid-cols-3 gap-2 sm:gap-3">
                                    <div class="w-16 h-16 sm:w-20 sm:h-20 lg:w-24 lg:h-24 bg-gray-200 rounded-lg animate-pulse"></div>
                                    <div class="w-16 h-16 sm:w-20 sm:h-20 lg:w-24 lg:h-24 bg-gray-200 rounded-lg animate-pulse"></div>
                                    <div class="w-16 h-16 sm:w-20 sm:h-20 lg:w-24 lg:h-24 bg-gray-200 rounded-lg animate-pulse"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button Skeleton -->
                    <div class="mt-6 sm:mt-8 pt-4 sm:pt-6 border-t border-gray-200">
                        <div class="w-full h-10 sm:h-12 bg-gray-200 rounded-lg animate-pulse"></div>
                    </div>
                </div>

                <!-- Main Review Form -->
                <div v-else class="border rounded-lg shadow-md p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Left Column - Business Info & Rating -->
                        <div class="space-y-6">
                            <!-- Business Information -->
                            <router-link :to="`/${businessData?.slug}`" class="flex items-start space-x-4">
                                <div class="w-20 h-20 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <img 
                                        :src="businessData?.business_profile || 'https://placehold.co/80x80/FFD700/000000?text=GHEE'" 
                                        :alt="businessData?.business_name"
                                        class="w-full h-full object-cover rounded-lg"
                                    />
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900 capitalize">{{ businessData?.business_name || 'Business Name' }}</h2>
                                    <p class="text-gray-600">{{ businessData?.address || 'Business Location' }}</p>
                                </div>
                            </router-link>

                            <!-- Rating Section -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">How would you rate your experience?</h3>
                                <div class="flex mb-4">
                                    <div v-for="i in 5" :key="i">
                                        <div @click="rating = i" @mouseenter="hoveredRating = i" @mouseleave="hoveredRating = 0">
                                            <StarIcon class="h-10 p-1 m-1 border rounded-md cursor-pointer transition-colors duration-200" :class="getStarClass(i)" />
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Rating Label -->
                                <div class="inline-flex items-center px-3 py-1 bg-gray-100 rounded-full">
                                    <span class="text-sm font-medium text-gray-700">{{ getRatingLabel(rating) }}</span>
                                    <span class="ml-1">{{ getRatingEmoji(rating) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column - Review Text & Photos -->
                        <div class="space-y-6">
                            <!-- Review Text Area -->
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="text-lg font-semibold text-gray-900">Tell us about your experience</h3>
                                    <div class="flex items-center text-sm text-gray-500">
                                        <LightBulbIcon class="w-4 h-4 mr-1" />
                                        <span>Tips</span>
                                    </div>
                                </div>
                                <textarea 
                                    v-model="reviewText"
                                    placeholder="Please share your experience with the business. Things you can talk about: service, product and price"
                                    class="w-full h-32 p-3 border border-gray-300 rounded-lg resize-none focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"
                                ></textarea>
                            </div>

                            <!-- Photo Upload Section -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">Upload Photos</h3>
                                <div class="grid grid-cols-3 gap-3">
                                    <!-- Upload Button -->
                                    <div 
                                        @click="triggerFileUpload"
                                        class="w-24 h-24 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center cursor-pointer hover:border-primary transition-colors"
                                    >
                                        <CameraIcon class="w-6 h-6 text-gray-400" />
                                    </div>
                                    
                                    <!-- Uploaded Images -->
                                    <div 
                                        v-for="(image, index) in uploadedImages" 
                                        :key="index"
                                        class="relative w-24 h-24"
                                    >
                                        <img 
                                            :src="image.preview || image" 
                                            :alt="`Uploaded image ${index + 1}`"
                                            class="w-full h-full object-cover rounded-lg"
                                        />
                                        <button 
                                            @click="removeImage(index)"
                                            class="absolute -top-2 -right-2 w-6 h-6 bg-gray-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-gray-600"
                                        >
                                            ×
                                        </button>
                                    </div>
                                </div>
                                <input 
                                    ref="fileInput"
                                    type="file" 
                                    multiple 
                                    accept="image/*"
                                    @change="handleFileUpload"
                                    class="hidden"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <button 
                            @click="submitReview"
                            :disabled="!isFormValid || submitting"
                            class="w-full bg-primary text-white font-bold py-3 px-6 rounded-lg hover:bg-primary/90 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                        >
                            <span v-if="submitting">Submitting...</span>
                            <span v-else>Submit Review</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Login Modal -->
    <LoginModal 
        :show="showLoginModal" 
        @close="showLoginModal = false"
        @submit="onLoginSuccess"
    />
    </div>
</template>

<script>
import { CameraIcon, LightBulbIcon, StarIcon } from '@heroicons/vue/24/outline';
import axios from 'axios';
import { push } from 'notivue';
import { validateImageFile } from '@/utils/imageValidation.js';
import LoginModal from '@/components/modals/LoginModal.vue';

export default {
    name: 'MakeReviewView',
    components: {
        CameraIcon,
        LightBulbIcon,
        StarIcon,
        LoginModal
    },
    data() {
        return {
            rating: 0,
            hoveredRating: 0,
            reviewText: '',
            uploadedImages: [],
            submitting: false,
            businessData: null,
            loading: true,
            showLoginModal: false
        }
    },
    computed: {
        isFormValid() {
            return this.rating > 0 && this.reviewText.trim().length > 0;
        }
    },
    methods: {
        getRatingLabel(rating) {
            const labels = {
                1: 'Poor',
                2: 'Fair',
                3: 'Good',
                4: 'Very Good',
                5: 'Excellent'
            };
            return labels[rating] || 'Select Rating';
        },
        
        getRatingEmoji(rating) {
            const emojis = {
                1: '😞',
                2: '😐',
                3: '🙂',
                4: '😊',
                5: '😄'
            };
            return emojis[rating] || '';
        },
        
        getStarClass(starIndex) {
            const effectiveRating = this.hoveredRating || this.rating;
            if (starIndex <= effectiveRating) {
                return 'text-yellow-400 fill-current';
            }
            return 'text-gray-400';
        },
        
        triggerFileUpload() {
            this.$refs.fileInput.click();
        },
        
        handleFileUpload(event) {
            const files = Array.from(event.target.files);
            files.forEach(file => {
                if (file.type.startsWith('image/')) {
                    const validation = validateImageFile(file);
                    if (!validation.isValid) {
                        push.error(validation.error);
                        return;
                    }
                    const preview = URL.createObjectURL(file);
                    this.uploadedImages.push({ file, preview });
                }
            });
            event.target.value = '';
        },
        
        removeImage(index) {
            this.uploadedImages.splice(index, 1);
        },
        
        async submitReview() {
            if (!this.isFormValid) return;
            
            // Check if user is authenticated
            if (!this.$store.getters.isAuthenticated) {
                this.showLoginModal = true;
                return;
            }
            
            this.submitting = true;
            try {
                // Create the review with images included
                const files = this.uploadedImages.map(img => (img && img.file) ? img.file : img);
                const formData = new FormData();
                formData.append('business_id', this.businessData.id);
                formData.append('rating', this.rating);
                formData.append('message', this.reviewText);
                formData.append('parent_id', 'null');
                files.forEach((file, index) => formData.append(`images[${index}]`, file));
                
                const response = await axios.post('/user/business/review', formData);
                
                if (response.data.status) {
                    // Show success message
                    push.success('Review submitted successfully!');
                    // Go back to previous page
                    this.$router.go(-1);
                } else {
                    push.success(response.data.message);
                }
            } catch (error) {
                // alert('You have already reviewed this business');
                push.error(error.response.data.data?.business_id[0] || 'An error occurred while submitting your review.');
            } finally {
                this.submitting = false;
            }
        },
        
        onLoginSuccess() {
            this.showLoginModal = false;
            // After successful login, submit the review
            this.submitReview();
        },
        
        async fetchBusinessData() {
            this.loading = true;
            try {
                const response = await axios.get(`/business/${this.$route.params.businessSlug}`);
                if (response.data.status) {
                    this.businessData = response.data.data;
                }
            } catch (error) {
                console.error('Error fetching business data:', error);
            } finally {
                this.loading = false;
            }
        }
    },
    
    mounted() {
        this.fetchBusinessData();
        // Set rating from query parameter if available
        if (this.$route.query.rating) {
            this.rating = parseInt(this.$route.query.rating);
        }
    }
}
</script> 