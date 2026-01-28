<template>
  <div v-if="open" class="fixed inset-0 z-50 flex items-stretch md:items-center justify-center bg-white md:bg-gray-500 md:bg-opacity-75 p-0 md:p-4">
    <div class="bg-white w-full h-full md:h-auto md:rounded-lg md:shadow-xl md:max-w-2xl overflow-y-auto">
      <MobileModalHeader title="Buy Credits" @back="$emit('close')" />
      <div class="p-4 md:px-6 md:pb-4">
        <div class="bg-white rounded-2xl shadow-lg p-4 py-6 min-h-full">
          <div class="md:flex md:items-start">
          <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 md:mx-0 md:h-10 md:w-10">
            <ExclamationTriangleIcon class="h-6 w-6 text-yellow-600" />
          </div>
          <div class="mt-3 text-center md:mt-0 md:ml-4 md:text-left w-full">
            <h3 class="text-lg md:text-xl leading-6 font-medium text-gray-900 mb-4">
              Insufficient Credits
            </h3>
              
              <div class="space-y-4">
                <p class="text-gray-600 text-sm md:text-base">
                  You don't have enough credits to view this lead's contact details. You can either purchase credits or pay 29 BDT to view this contact directly.
                </p>

                <!-- Current Credits Display -->
                <div class="bg-gray-50 rounded-lg p-4">
                  <h4 class="text-sm md:text-base font-medium text-gray-900 mb-2">Current Credits</h4>
                  <div class="space-y-2">
                    <div class="flex justify-between">
                      <span class="text-sm text-gray-600">Free Credits:</span>
                      <span class="text-sm md:text-base font-medium">{{ leadCredits?.free_remaining || 0 }} remaining</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-sm text-gray-600">Paid Credits:</span>
                      <span class="text-sm font-medium">{{ leadCredits?.paid_credits || 0 }} available</span>
                    </div>
                    <div v-if="leadCredits?.expire_date" class="flex justify-between">
                      <span class="text-sm text-gray-600">Expires:</span>
                      <span :class="[
                        'text-sm font-medium',
                        isCreditsExpired ? 'text-red-600' : 'text-green-600'
                      ]">
                        {{ leadCredits.expire_date }}
                        <span v-if="isCreditsExpired" class="text-xs text-red-500 ml-1">(Expired)</span>
                      </span>
                    </div>
                  </div>
                  <div v-if="isCreditsExpired" class="mt-3 p-2 bg-red-50 border border-red-200 rounded">
                    <p class="text-xs text-red-600">
                      Your paid credits have expired. Purchase new credits to continue viewing lead contacts.
                    </p>
                  </div>
                </div>

                <!-- Credit Packages -->
                <div class="space-y-3">
                  <div class="flex items-center justify-between">
                    <h4 class="font-medium text-gray-900">Available Packages</h4>
                    <router-link 
                      to="/my/business/lead-credits/buy"
                      class="text-sm text-blue-600 hover:text-blue-800 underline"
                      @click="$emit('close')"
                    >
                      View All Packages
                    </router-link>
                  </div>
                  <div class="grid gap-3">
                    <!-- Quick package option -->
                    <div 
                      v-if="quickPackage"
                      :class="[
                        'border rounded-lg p-4 cursor-pointer transition-colors',
                        selectedPackage === quickPackage.id
                          ? 'border-green-500 bg-green-50'
                          : 'border-gray-200 hover:border-gray-300'
                      ]"
                      @click="selectedPackage = quickPackage.id"
                    >
                      <div class="flex items-center justify-between">
                        <div>
                          <h5 class="font-medium text-gray-900">{{ quickPackage.name }}</h5>
                          <p class="text-sm text-gray-600">{{ quickPackage.credits }} Credits • One-time payment</p>
                        </div>
                        <div class="text-right">
                          <p class="text-lg font-bold text-green-600">{{ quickPackage.price }} BDT</p>
                        </div>
                      </div>
                    </div>
                    
                    <!-- Credit packages -->
                    <div 
                      v-for="creditPackage in regularPackages" 
                      :key="creditPackage.id"
                      :class="[
                        'border rounded-lg p-4 cursor-pointer transition-colors',
                        selectedPackage === creditPackage.id
                          ? 'border-blue-500 bg-blue-50'
                          : 'border-gray-200 hover:border-gray-300'
                      ]"
                      @click="selectedPackage = creditPackage.id"
                    >
                      <div class="flex items-center justify-between">
                        <div>
                          <h5 class="font-medium text-gray-900">{{ creditPackage.name }}</h5>
                          <p class="text-sm text-gray-600">{{ creditPackage.credits }} Credits • {{ creditPackage.duration_months }} month(s)</p>
                        </div>
                        <div class="text-right">
                          <p class="text-lg font-bold text-blue-600">{{ creditPackage.price }} BDT</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="bg-gray-50 px-4 py-3 md:px-6 md:flex md:flex-row-reverse">
          <button
            @click="purchaseCredits"
            :disabled="!selectedPackage"
            :class="[
              'w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 md:ml-3 md:w-auto md:text-sm',
              selectedPackage
                ? 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500'
                : 'bg-gray-300 text-gray-500 cursor-not-allowed'
            ]"
          >
            Purchase Credits
          </button>
          <button
            @click="$emit('close')"
            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 md:mt-0 md:ml-3 md:w-auto md:text-sm"
          >
            Cancel
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ExclamationTriangleIcon } from '@heroicons/vue/24/outline';
import { push } from 'notivue';
import axios from 'axios';
import MobileModalHeader from '@/components/common/MobileModalHeader.vue';
import { useModalScroll } from '@/composables/useModalScroll';

export default {
  name: 'BuyCreditsModal',
  setup() {
    const { openModal, closeModal } = useModalScroll()
    return { openModal, closeModal }
  },
  components: {
    ExclamationTriangleIcon,
    MobileModalHeader
  },
  props: {
    open: {
      type: Boolean,
      default: false
    },
    leadData: {
      type: Object,
      default: null
    }
  },
  data() {
    return {
      selectedPackage: null,
      creditPackages: [],
      loading: false
    };
  },
  computed: {
    leadCredits() {
      return this.$store.getters.myBusiness?.lead_credits;
    },
    isCreditsExpired() {
      if (!this.leadCredits?.expire_date) return false;
      const expireDate = new Date(this.leadCredits.expire_date);
      const now = new Date();
      return now > expireDate;
    },
    quickPackage() {
      return this.creditPackages.find(pkg => pkg.name === 'Quick');
    },
    regularPackages() {
      return this.creditPackages.filter(pkg => pkg.name !== 'Quick');
    }
  },
  methods: {
    async purchaseCredits() {
      if (!this.selectedPackage) return;

      // Check if it's the Quick package
      if (this.selectedPackage === this.quickPackage?.id) {
        // Handle quick payment using the Quick package from API
        try {
          const payload = {
            package_id: this.quickPackage.id,
            payment_method: "Bkash",
            transaction_id: `quick_${Date.now()}`
          };

          const response = await axios.post('/user/business/lead/credits/purchase', payload);
          
          if (response?.data?.status) {
            push.success('Payment processed successfully!');
            
            // Refresh business data to get updated credits
            await this.$store.dispatch('fetchMyBusiness');
            
            // Now call the View Contact API
            if (this.leadData) {
              await this.viewContactAfterPayment();
            }
            
            this.$emit('close');
            this.$emit('credits-purchased');
          } else {
            push.error(response?.data?.message || 'Payment failed');
          }
        } catch (error) {
          console.error('Error processing quick payment:', error);
          push.error(error?.response?.data?.message || 'Failed to process payment');
        }
        return;
      }

      const selectedPkg = this.regularPackages.find(pkg => pkg.id === this.selectedPackage);
      if (!selectedPkg) return;

      // For credit packages, redirect to the full BuyLeadCredits page for actual purchase
      // This ensures consistent purchase flow and payment processing
      this.$emit('close');
      this.$router.push('/my/business/lead-credits/buy');
    },
    
    async viewContactAfterPayment() {
      try {
        // Call the unlock lead contact API
        const response = await this.$store.dispatch('unlockLeadContact', this.leadData.id);
        
        if (response.status) {
          // Emit event to parent to show contact modal
          this.$emit('view-contact', {
            leadData: this.leadData,
            contactData: response.data
          });
          
        } else {
          push.error(response.message || 'Failed to unlock contact details');
        }
      } catch (error) {
        console.error('Error unlocking contact after payment:', error);
        push.error('Failed to unlock contact details');
      }
    },
    
    async fetchCreditPackages() {
      try {
        this.loading = true;
        const response = await axios.get('/user/business/lead/credits/packages');
        
        if (response?.data?.status) {
          this.creditPackages = response.data.data || [];
        } else {
          push.error(response?.data?.message || 'Failed to fetch credit packages');
        }
      } catch (error) {
        console.error('Error fetching credit packages:', error);
        push.error('Failed to fetch credit packages');
      } finally {
        this.loading = false;
      }
    }
  },
  watch: {
    open(newVal) {
      if (newVal) {
        this.openModal('buy-credits-modal');
        this.selectedPackage = null;
        this.fetchCreditPackages();
      } else {
        this.closeModal('buy-credits-modal');
      }
    }
  }
};
</script>
