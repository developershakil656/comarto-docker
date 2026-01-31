<template>
<div>
    <div v-if="show" class="fixed inset-0 z-[60] flex items-stretch md:items-center justify-center bg-white md:bg-black md:bg-opacity-40 md:backdrop-blur-sm p-0 md:p-4">
        <div class="bg-white w-full h-full md:h-auto md:rounded-2xl md:shadow-2xl relative overflow-y-auto md:max-h-[90vh] md:max-w-md">
            <MobileModalHeader title="Login" @back="$emit('close')" />
            <div class="md:p-8 p-4">
                <div class="bg-white rounded-2xl shadow-lg md:rounded-none md:shadow-none p-5 md:p-0">
            <!-- Logo and Welcome -->
            <div class="flex flex-wrap items-center mb-6">
                <div class="flex items-center pr-1.5">
                    <img src="/logo.svg" alt="Logo" class="h-6" />
                </div>
                <div class="pl-2 border-l-2">
                    <div class="text-base font-bold">Welcome</div>
                    <div class="text-sm">Login for a seamless experience</div>
                </div>
            </div>

            <!-- Step 1: Mobile Number Input -->
            <div v-if="!otpSent">
                <Form @submit="sendOTP" v-slot="{ errors, isSubmitting }">
                    <!-- Mobile Number Floating Label Input -->
                    <div class="mb-4 relative group">
                        <Field v-model="mobile" name="mobile" type="tel" maxlength="11" pattern="01[3-9][0-9]{8}" id="mobile-input" class="block w-full rounded-lg border pl-14 sm:pl-16 py-3 sm:py-4 focus:border-primary focus:ring-blue-200 outline-none transition placeholder-transparent text-base sm:text-lg" :class="{ 'border-red-300 focus:border-red-500': errors.mobile, 'border-gray-300': !errors.mobile }" placeholder="Enter Mobile Number" @input="onNumberInput" rules="bd_mobile" />
                        <span class="absolute left-3 sm:left-4 top-1/2 -translate-y-1/2 text-gray-500 text-sm sm:text-base pointer-events-none select-none pr-2 border-r-2">+88</span>
                        <label for="mobile-input" class="absolute left-12 sm:left-16 z-10 -translate-y-1/2 bg-white text-xs sm:text-sm transition-all duration-200 pointer-events-none group-focus-within:top-0 group-focus-within:text-xs group-focus-within:left-4 sm:group-focus-within:left-6 group-focus-within:-translate-y-1.5" :class="{ 
                  'top-0 text-xs left-4 sm:left-6 text-primary': mobile && !errors.mobile, 
                  'top-0 text-xs left-4 sm:left-6 text-red-500': errors.mobile,
                  'top-1/2 text-gray-500': !mobile && !errors.mobile 
                }">
                            Enter Mobile Number
                        </label>
                        <div class="absolute inset-0 rounded-lg pointer-events-none border-2 border-transparent transition" :class="{ 'border-red-500 shadow-md': errors.mobile, 'border-primary shadow-md': !errors.mobile && mobile }"></div>

                    </div>
                    <!-- Error Message -->
                    <div v-if="errors.mobile" class="-mt-3 text-red-500 text-sm flex items-center">
                        {{ errors.mobile }}
                    </div>

                    <div class="text-center mb-3">
                        <span class="text-gray-700 text-sm">By Continuing, You are Agree to <router-link to="/terms-conditions" class="text-primary underline">Terms and Conditions</router-link></span>
                    </div>
                    <!-- <div class="text-center">
                        <a href="#" class="text-gray-700 underline text-sm">T&amp;C's Privacy Policy</a>
                    </div> -->

                    <button type="submit" :disabled="isSubmitting || loading" class="w-full mt-4 bg-primary hover:bg-primary/85 text-white font-semibold py-3 sm:py-2 rounded-md transition disabled:opacity-60 disabled:cursor-not-allowed text-base sm:text-sm">
                        {{ loading ? 'Sending OTP...' : 'Send OTP' }}
                    </button>
                </Form>
            </div>

            <!-- Step 2: OTP Input -->
            <div v-else>
                <!-- Error Message -->
                <div v-if="errorMessage" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-red-700 text-sm">{{ errorMessage }}</span>
                    </div>
                </div>

                <!-- OTP Form -->
                <Form @submit="verifyOTP" v-slot="{ errors, isSubmitting }">
                    <div class="flex flex-wrap justify-center mb-4 text-center text-sm sm:text-base">
                        <span>Enter the code sent to</span>
                        <p class="ml-2"> <b>+ 88 {{ mobile }}</b>
                            <button type="button" @click="backToMobile" class="ml-1 text-primary hover:underline align-middle">
                                <PencilIcon class="inline h-4 w-4 ml-1" />
                            </button></p>
                    </div>

                    <div class="flex justify-center gap-1 sm:gap-2 mb-2 px-2">
                        <input v-for="(digit, idx) in otp" :key="idx" :ref="el => otpRefs[idx] = el" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="w-9 h-12 sm:w-12 sm:h-14 md:w-14 md:h-16 text-lg sm:text-xl md:text-2xl text-center border rounded focus:border-primary focus:ring-2 focus:ring-blue-200 outline-none transition" :class="{ 'border-red-300 focus:border-red-500': errors[`otp.${idx}`], 'border-gray-300': !errors[`otp.${idx}`] }" v-model="otp[idx]" @input="onOtpInput(idx, $event)" @keydown.backspace="onOtpBackspace(idx, $event)" autocomplete="one-time-code" placeholder="-" />
                    </div>

                    <!-- OTP Error Message -->
                    <div v-if="errors.otp" class="mb-4 text-red-500 text-sm text-center">
                        {{ errors.otp }}
                    </div>

                    <div class="flex justify-between items-center my-6 text-sm">
                        <span class="text-gray-500">Didn't Receive the OTP?</span>
                        <button type="button" @click="resendOTP" :disabled="resendTimer > 0" class="text-primary hover:underline disabled:opacity-50 disabled:cursor-not-allowed">
                            {{ resendTimer > 0 ? `Resend in ${Math.floor(resendTimer / 60)}:${(resendTimer % 60).toString().padStart(2, '0')}` : 'Resend OTP' }}
                        </button>
                    </div>

                    <button type="submit" :disabled="isSubmitting || loading" class="w-full hover:bg-primary/85 bg-primary text-white font-semibold py-3 sm:py-2 rounded-md transition disabled:opacity-60 disabled:cursor-not-allowed text-base sm:text-sm">
                        {{ loading ? 'Verifying...' : 'Continue' }}
                    </button>
                </Form>
            </div>

            <div class="flex items-center">
                <div class="border-b w-full"></div>
                <p class="px-2 my-2">Or,</p>
                <div class="border-b w-full"></div>
            </div>

            <button type="button" @click="onGoogleLogin" class="w-full flex items-center justify-center gap-2 border border-gray-300 bg-white hover:bg-gray-100 text-gray-700 font-semibold py-3 sm:py-2 rounded-md transition text-base sm:text-sm">
                <img src="@/assets/icons/svgs/GoogleIcon.svg" class="w-5" alt="Google" />
                Login with Google
            </button>

            <button @click="$emit('close')" class="hidden md:block absolute top-3 right-3 text-gray-600 hover:text-gray-800 bg-gray-200 p-2 rounded">
                <XMarkIcon class="h-4" />
            </button>
                </div>
            </div>
        </div>
    </div>
    <EnterNameModal :show="showName" @submit="onNameSubmit" @close="showName = false"/>
</div>
</template>

<script>
import {
    nextTick
} from 'vue';
import {
    XMarkIcon,
    PencilIcon
} from "@heroicons/vue/24/outline";
import {
    Form,
    Field
} from 'vee-validate';
import EnterNameModal from '@/components/modals/EnterNameModal.vue';
import { useModalScroll } from '@/composables/useModalScroll';
import MobileModalHeader from '@/components/common/MobileModalHeader.vue';
import { push } from 'notivue';
import OptimizedImage from '@/components/common/OptimizedImage.vue';

export default {
    components: {
        XMarkIcon,
        PencilIcon,
        Form,
        Field,
        EnterNameModal,
        MobileModalHeader,
        OptimizedImage
    },
    props: {
        show: {
            type: Boolean,
            default: false
        },
        number: {
            type: String,
            required: false
        }
    },
    emits: ['close', 'submit'],

    setup() {
        const { openModal, closeModal } = useModalScroll()
        return { openModal, closeModal }
    },

    data() {
        return {
            mobile: '',
            otp: ['', '', '', '', '', ''],
            otpRefs: [],
            otpSent: false,
            loading: false,
            resendTimer: 0,
            resendInterval: null,
            errorMessage: '',
            showName: false
        }
    },

    watch: {
        show(newVal) {
            if (newVal) {
                this.openModal('login-modal');
                this.otp = ['', '', '', '', '', ''];
                this.startResendTimer();
            } else {
                this.closeModal('login-modal');
                this.clearResendTimer();
            }
        },
        number(newVal) {
            this.mobile = newVal || '';
        },
        errorMessage(newVal) {
            // Clear OTP when there's an error
            if (newVal) {
                this.otp = ['', '', '', '', '', ''];
                // Focus on first input after error
                nextTick(() => {
                    if (this.otpRefs[0]) {
                        this.otpRefs[0].focus();
                    }
                });
            }
        }
    },
    methods: {
        onNumberInput(e) {
            this.mobile = this.mobile.replace(/[^0-9]/g, '');
        },
        onOtpInput(idx, e) {
            const val = e.target.value.replace(/\D/g, '');
            this.otp[idx] = val;
            if (val && idx < 5) {
                nextTick(() => {
                    if (this.otpRefs[idx + 1] && this.otpRefs[idx + 1].focus) {
                        this.otpRefs[idx + 1].focus();
                    }
                });
            }
        },
        onOtpBackspace(idx, e) {
            if (!this.otp[idx] && idx > 0) {
                nextTick(() => {
                    if (this.otpRefs[idx - 1] && this.otpRefs[idx - 1].focus) {
                        this.otpRefs[idx - 1].focus();
                    }
                });
            }
        },
        async sendOTP() {
            try {
                this.loading = true;
                // Use store action for sending OTP
                await this.$store.dispatch('sendOTP', this.mobile);

                this.otpSent = true;
                this.startResendTimer();

            } catch (error) {
                // Error handled by store action
            } finally {
                this.loading = false;
            }
        },
        async verifyOTP(values) {
            // Validate OTP manually
            const otpString = this.otp.join('');
            if (otpString.length !== 6) {
                this.errorMessage = 'Please enter all 6 digits';
                return;
            }

            if (!/^\d{6}$/.test(otpString)) {
                this.errorMessage = 'OTP must contain only numbers';
                return;
            }

            // Clear any previous error message
            this.errorMessage = '';

            try {
                this.loading = true;
                // Use store action for OTP login
                const {user, result, message} = await this.$store.dispatch('otpLogin', {
                    number: this.mobile,
                    otp: otpString
                });

                push.success(message);
                // Login successful
                this.$emit('submit', result);
                

                // Close modal first
                this.$emit('close');
                // Check if user name is null and show name modal

                if (user) {
                    if(!user.name){
                        this.showName = true;
                    }
                }

                // Reset form data after closing
                this.resetData();


            } catch (error) {
                // Handle different types of errors
                this.errorMessage = error?.response?.data?.message || 'An error occurred during verification.';
            } finally {
                this.loading = false;
            }
        },
        async resendOTP() {
            if (this.resendTimer > 0) return;

            try {
                this.loading = true;

                // Use store action for resending OTP
                await this.$store.dispatch('sendOTP', this.mobile);

                this.startResendTimer();

            } catch (error) {
                // Error handled by store action
            } finally {
                this.loading = false;
            }
        },
        startResendTimer() {
            this.resendTimer = 120; // 2 minutes (120 seconds)
            this.resendInterval = setInterval(() => {
                this.resendTimer--;
                if (this.resendTimer <= 0) {
                    this.clearResendTimer();
                }
            }, 1000);
        },
        clearResendTimer() {
            if (this.resendInterval) {
                clearInterval(this.resendInterval);
                this.resendInterval = null;
            }
            this.resendTimer = 0;
        },
        backToMobile() {
            this.otpSent = false;
            this.otp = ['', '', '', '', '', ''];
            if (this.resendInterval) {
                clearInterval(this.resendInterval);
                this.resendTimer = 0;
            }
        },
        resetData() {
            this.mobile = '';
            this.otp = ['', '', '', '', '', ''];
            this.otpSent = false;
            this.loading = false;
            this.resendTimer = 0;
            this.errorMessage = '';
            if (this.resendInterval) {
                clearInterval(this.resendInterval);
            }
        },
        async onGoogleLogin() {
            try {
                // Check for intended route first (from router guard), then fall back to current route
                const intendedRoute = localStorage.getItem('intendedRoute');
                const redirectRoute = intendedRoute || this.$route.fullPath;
                
                // Clear the intended route since we're using it
                if (intendedRoute) {
                    localStorage.removeItem('intendedRoute');
                }
                
                await this.$store.dispatch('googleLogin', redirectRoute);
            } catch (error) {
                // Error handled by store action
            }
        },
        onNameSubmit(name) {
            this.showName = false;
            this.$emit('submit', name);
        }
    },
    beforeUnmount() {
        if (this.resendInterval) {
            clearInterval(this.resendInterval);
        }
    }
}
</script>
