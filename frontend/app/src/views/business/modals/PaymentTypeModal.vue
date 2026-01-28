<template>
  <Transition name="modal" appear>
    <div v-if="open" class="fixed inset-0 z-[60] flex items-stretch md:items-center justify-center bg-white md:bg-black md:bg-opacity-30 p-0 md:p-4" style="min-height: 100vh;" @click="$emit('close')">
      <Transition name="modal-content" appear>
        <div class="bg-white w-full h-full md:h-auto md:rounded-xl md:shadow-lg md:max-w-lg md:max-h-[90vh] overflow-y-auto" @click.stop>
          <MobileModalHeader title="Payment Type" @back="$emit('close')" />
          <div class="p-4 md:p-8 relative">
            <button @click="$emit('close')" class="hidden md:block absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors">
        <XMarkIcon class="w-6 h-6" />
      </button>
      <h2 class="hidden md:block text-xl font-semibold mb-6 text-center">
        <slot> Payment Type </slot>
      </h2>
      <form @submit.prevent="onSubmit" class="space-y-6 rounded-2xl shadow-lg px-4 py-6 min-h-full">
        <!-- Payment Type -->
        <div class="relative group">
          <span class="absolute left-4 top-4 text-gray-400 pointer-events-none">
            <BanknotesIcon class="w-5 h-5" />
          </span>
          <textarea v-model="form.paymentType" id="paymentType" required rows="2"
            class="block w-full rounded-lg border border-gray-300 pl-12 pt-3 pr-4 pb-2 focus:border-primary focus:ring-blue-200 outline-none transition placeholder-transparent text-base resize-none"
            placeholder="Payment Type"></textarea>
          <label for="paymentType"
            class="absolute left-12 z-10 bg-white text-gray-500 text-sm transition-all duration-200 pointer-events-none group-focus-within:-top-1 group-focus-within:text-xs group-focus-within:left-8 group-focus-within:text-primary"
            :class="{ '-top-1 text-xs left-8 text-primary': form.paymentType, 'top-3': !form.paymentType }">
            Payment Type
          </label>
        </div>
        <p class="text-xs md:text-sm text-gray-500 mt-1 ml-1">Cash on Delivery, Bkash, Bank Transfer etc.</p>

        <button type="submit" :disabled="loading" class="w-full bg-primary hover:bg-primary/85 text-white font-semibold py-2 rounded-md transition disabled:opacity-60 disabled:cursor-not-allowed">
          <span v-if="loading" class="flex items-center justify-center">
            <ArrowPathIcon class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" />
            Saving...
          </span>
          <span v-else>
            Save Payment Type
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
import { XMarkIcon, BanknotesIcon, ExclamationCircleIcon, ArrowPathIcon } from '@heroicons/vue/24/outline';
import MobileModalHeader from '@/components/common/MobileModalHeader.vue';
import { useModalScroll } from '@/composables/useModalScroll';

export default {
  name: 'PaymentTypeModal',
  components: {
    XMarkIcon,
    BanknotesIcon,
    ExclamationCircleIcon,
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
    initialPaymentType: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      form: {
        paymentType: ''
      },
      error: '',
      loading: false
    }
  },

  watch: {
    open(newVal) {
      if (newVal) {
        this.openModal('payment-type-modal');
        this.initializeForm();
      } else {
        this.closeModal('payment-type-modal');
      }
    },
    initialPaymentType(newVal) {
      if (this.open) {
        this.initializeForm();
      }
    }
  },
  methods: {
    initializeForm() {
      this.form.paymentType = this.initialPaymentType;
      this.error = '';
    },

    async onSubmit() {
      try {
        this.loading = true;
        
        // Update business details with payment type
        await this.$store.dispatch('updateBusinessDetails', {
          payment_type: this.form.paymentType
        });
        
        push.success('Payment type updated successfully!');
        
        // Close the modal
        this.$emit('close');
      } catch (error) {
        console.error('Error updating payment type:', error);
        push.error('Failed to update payment type. Please try again.');
      } finally {
        this.loading = false;
      }
    }
  }
}
</script> 