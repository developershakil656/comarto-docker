<template>
  <div
    v-if="show"
    class="fixed inset-0 z-[70] flex items-stretch md:items-center justify-center bg-white md:bg-black md:bg-opacity-40 md:backdrop-blur-sm p-0 md:p-4"
  >
    <div
      class="bg-white w-full h-full md:h-auto md:rounded-xl md:shadow-lg md:max-w-md relative overflow-y-auto"
    >
      <MobileModalHeader title="Verify OTP" @back="$emit('close')" />
      <div class="p-4 md:p-8">
        <!-- Logo and Welcome -->
        <div class="flex flex-wrap items-center mb-6">
          <div class="flex items-center pr-1.5">
            <img
              src="/logo.svg"
              alt="Logo"
              class="h-6"
            />
          </div>
          <div class="pl-2 border-l-2">
            <div class="text-base font-bold">Welcome</div>
            <div>List your business for free</div>
          </div>
        </div>

        <!-- Error Message -->
        <div
          v-if="errorMessage"
          class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg"
        >
          <div class="flex items-center">
            <svg
              class="w-5 h-5 text-red-500 mr-2"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path
                fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                clip-rule="evenodd"
              ></path>
            </svg>
            <span class="text-red-700 text-sm">{{ errorMessage }}</span>
          </div>
        </div>

        <!-- OTP Form -->
        <form @submit.prevent="onSubmit">
          <div
            class="flex flex-wrap justify-center gap-1 sm:gap-2 mb-4 text-center"
          >
            <span>Enter the code sent to</span>
            <span class="ml-1 font-bold">+ 88 {{ mobile }}</span>
          </div>
          <div class="flex justify-center gap-1 sm:gap-2 mb-2 px-2">
            <input
              v-for="(digit, idx) in otp"
              :key="idx"
              :ref="(el) => (otpRefs[idx] = el)"
              type="text"
              maxlength="1"
              inputmode="numeric"
              pattern="[0-9]*"
              class="w-9 h-12 sm:w-12 sm:h-14 md:w-14 md:h-16 text-lg sm:text-xl md:text-2xl text-center border border-gray-300 rounded focus:border-primary focus:ring-2 focus:ring-blue-200 outline-none transition"
              v-model="otp[idx]"
              @input="onOtpInput(idx, $event)"
              @keydown.backspace="onOtpBackspace(idx, $event)"
              autocomplete="one-time-code"
              placeholder="-"
            />
          </div>
          <div class="flex justify-between items-center my-6 text-sm">
            <span class="text-gray-500">Didn't Receive the OTP?</span>
            <button
              type="button"
              @click="onResend"
              :disabled="resendTimer > 0"
              class="text-primary hover:underline disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{
                resendTimer > 0
                  ? `Resend in ${Math.floor(resendTimer / 60)}:${(
                      resendTimer % 60
                    )
                      .toString()
                      .padStart(2, "0")}`
                  : "Resend OTP"
              }}
            </button>
          </div>
          <button
            type="submit"
            :disabled="!otpValid || loading"
            class="w-full hover:bg-primary/85 bg-primary text-white font-semibold py-3 sm:py-2 rounded-md transition disabled:opacity-60 disabled:cursor-not-allowed text-base sm:text-sm"
          >
            {{ loading ? "Verifying..." : "Continue" }}
          </button>
        </form>

        <button
          @click="$emit('close')"
          class="hidden md:block absolute top-3 right-3 text-gray-600 hover:text-gray-800 bg-gray-200 p-2 rounded"
        >
          <XMarkIcon class="h-4" />
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, nextTick } from "vue";
import { XMarkIcon, PencilIcon } from "@heroicons/vue/24/outline";
import MobileModalHeader from "@/components/common/MobileModalHeader.vue";
import { useModalScroll } from '@/composables/useModalScroll';
export default {
  components: { XMarkIcon, PencilIcon, MobileModalHeader },
  props: {
    show: { type: Boolean, default: false },
    mobile: { type: String, required: true },
    loading: { type: Boolean, default: false },
    errorMessage: { type: String, default: "" },
  },
  emits: ["submit", "edit-mobile", "resend", "close"],
  setup() {
    const { openModal, closeModal } = useModalScroll()
    const otp = ref(["", "", "", "", "", ""]);
    const otpRefs = Array(6).fill(null);
    return { otp, otpRefs,openModal, closeModal };
  },
  data() {
    return {
      resendTimer: 0,
      resendInterval: null,
    };
  },
  watch: {
    show(newVal) {
      if (newVal) {
        this.otp = ["", "", "", "", "", ""];
        this.startResendTimer();
        this.openModal('otp-verification-modal');
      } else {
        this.clearResendTimer();
        this.closeModal('otp-verification-modal');
      }
    },
    errorMessage(newVal) {
      // Clear OTP when there's an error
      if (newVal) {
        this.otp = ["", "", "", "", "", ""];
        // Focus on first input after error
        nextTick(() => {
          if (this.otpRefs[0]) {
            this.otpRefs[0].focus();
          }
        });
      }
    },
  },
  computed: {
    otpValid() {
      return this.otp.every((d) => d.match(/^\d$/));
    },
  },
  methods: {
    onOtpInput(idx, e) {
      const val = e.target.value.replace(/\D/g, "");
      this.otp[idx] = val;
      if (val && idx < 5) {
        nextTick(() => this.otpRefs[idx + 1]?.focus());
      }
    },
    onOtpBackspace(idx, e) {
      if (!this.otp[idx] && idx > 0) {
        nextTick(() => this.otpRefs[idx - 1]?.focus());
      }
    },
    onSubmit() {
      if (this.otpValid) {
        this.$emit("submit", this.otp.join(""));
      }
    },
    onResend() {
      if (this.resendTimer > 0) return;

      this.otp = ["", "", "", "", "", ""];
      this.$emit("resend");
      this.startResendTimer();
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
    }
  },
  beforeUnmount() {
    this.clearResendTimer();
    // Ensure body scroll is unlocked when component is destroyed
    if (this.show) {
      this.closeModal('otp-verification-modal');
    }
  },
};
</script>
