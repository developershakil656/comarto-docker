<template>
  <div class="bg-gray-50 min-h-screen font-poppins">
    <!-- <div class="flex">
      <div class="flex-1"> -->
        <AdminDashboardHeader title="Buy Lead Credits" backUrl="/my/business/dashboard" />

        <div class="container mx-auto mb-10">
          <!-- Current Balance -->
          <section class="md:mx-12 mt-6">
            <div class="bg-white rounded-xl shadow border p-6">
              <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900">Current Credits</h2>
                <button @click="refreshCredits"
                  class="px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg">Refresh</button>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                  <div class="text-xl md:text-2xl font-bold text-blue-600">{{ leadCredits?.free_remaining || 0 }}</div>
                  <div class="text-sm text-gray-600">Monthly Free Credits Remaining</div>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                  <div class="text-xl md:text-2xl font-bold text-green-600">{{ leadCredits?.paid_credits || 0 }}</div>
                  <div class="text-sm text-gray-600">Paid Credits Available</div>
                </div>
                <div v-if="leadCredits?.expire_date" class="text-center p-4 bg-gray-50 rounded-lg">
                  <div :class="[
                    'text-xl md:text-2xl font-bold',
                    isCreditsExpired ? 'text-red-600' : 'text-orange-600'
                  ]">
                    {{ leadCredits.expire_date }}
                  </div>
                  <div class="text-sm text-gray-600">
                    {{ isCreditsExpired ? 'Paid Credits Expired' : 'Paid Credits Expires On' }}
                  </div>
                </div>
              </div>
              <div v-if="isCreditsExpired" class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-sm text-red-600">
                  ⚠️ Your paid credits have expired. Purchase new credits to continue viewing lead contacts.
                </p>
              </div>
            </div>
          </section>

          <!-- Packages -->
          <section class="md:mx-12 mt-6">
            <div class="bg-white rounded-xl shadow border p-6">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Available Packages</h3>
                <button @click="fetchPackages" class="px-3 py-1.5 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg">
                  {{ loading ? 'Loading...' : 'Reload' }}
                </button>
              </div>
              <div v-if="loading" class="text-center py-8">
                <div class="text-gray-500">Loading packages...</div>
              </div>
              <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="pkg in regularPackages" :key="pkg.id"
                  class="border rounded-xl p-6 hover:shadow-md transition-shadow bg-gray-50">
                  <div class="text-center">
                    <div class="text-xl md:text-2xl font-bold text-gray-900">{{ pkg.name }}</div>
                    <div class="text-3xl font-bold text-blue-600 mt-2">{{ pkg.credits }}</div>
                    <div class="text-sm text-gray-600">Credits</div>
                    <div class="text-lg font-semibold text-gray-900 mt-2">{{ pkg.price }} BDT</div>
                    <div class="text-sm text-gray-600">{{ pkg.duration_months }} month(s)</div>
                  </div>
                  <div class="mt-4">
                    <button @click="openPurchase(pkg)"
                      class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                      Buy Now
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </section>

          <!-- Optional: Purchase History -->
          <section v-if="history.length" class="md:mx-12 mt-6">
            <div class="bg-white rounded-xl shadow border p-6">
              <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Purchase History</h3>
                <button @click="fetchHistory"
                  class="px-3 py-1.5 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg">Reload</button>
              </div>
              <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div v-for="item in history" :key="item.transaction_id" class="border rounded-lg p-4 bg-gray-50">
                  <div class="flex items-center justify-between">
                    <div class="text-gray-900 font-medium">{{ item.credits }} Credits</div>
                    <div class="text-gray-700">{{ item.amount }} BDT</div>
                  </div>
                  <div class="mt-2 text-sm capitalize">Status: <span
                      :class="item.status === 'completed' ? 'text-green-500' : (item.status === 'pending' ? 'text-yellow-500' : 'text-red-500')">{{
                      item.status }}</span></div>
                  <div class="mt-2 text-sm text-blue-600">Package: {{ item.package_name }}</div>
                  <div class="text-sm text-gray-600" v-if="item.duration_months">
                    Validity: {{ item.duration_months }} {{ item.duration_months === 1 ? 'month' : 'months' }}
                  </div>
                  <div class="mt-2 text-sm capitalize">Method: {{ item.payment_method }}</div>
                  <div class="text-sm text-primary">Txn ID: {{ item.transaction_id }}</div>
                  <div class="text-sm text-gray-500 mt-1">{{ formatDate(item.created_at) }}</div>
                </div>
              </div>
            </div>
          </section>
        </div>

        <!-- Purchase modal redesigned -->
        <div v-if="showPurchaseModal" class="fixed inset-0 z-[60]">
          <div class="absolute inset-0 bg-black/40" @click="closePurchase"></div>
          <div class="relative max-w-2xl md:mx-auto sm:m-4 m-2 my-4 bg-white rounded-xl shadow max-h-[95vh] flex flex-col">


            <!-- Scrollable content -->
            <div class="flex-1 overflow-y-auto">
              <div class="flex items-center px-3 md:px-6 py-2 border-b">
                <img src="@/assets/icons/svgs/bKash-Logo.svg" alt="BKash" class="h-16 transition m-auto" />
                <!-- <button class="text-gray-500 hover:text-gray-700" @click="closePurchase">✕</button> -->
              </div>
              <!-- Summary header like gateway card -->
              <div v-if="selectedPackage" class="px-3 md:px-6 pt-4">
                <div class="bg-gray-50 rounded-lg p-4 border">
                  <div class="flex items-center justify-between">
                    <div class="font-bold">
                      <p class="text-sm text-gray-700">{{ selectedPackage.credits }} Credits</p>
                      <p class="text-sm text-gray-700">{{ selectedPackage.duration_months }} month(s)</p>
                    </div>
                    <div class="text-right">
                      <p class="text-xl md:text-2xl font-bold text-teal-600">{{ selectedPackage.price }} BDT</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Steps for chosen method -->
              <div class="px-3 md:px-6 mt-5 pb-4">
                <div class="bg-white border rounded-lg p-4">
                  <div class="flex items-center gap-2 mb-3">
                    <p class="text-base font-semibold text-gray-900">bKash Personal</p>
                  </div>

                  <ol class="space-y-3">
                    <li class="flex items-start gap-3">
                      <span
                        class="h-6 w-6 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs font-semibold">1</span>
                      <span class="text-sm text-gray-700">Dial <span class="font-semibold text-primary">*247#</span> or
                        open the <span class="font-semibold text-primary">Bkash</span> app.</span>
                    </li>
                    <li class="flex items-start gap-3">
                      <span
                        class="h-6 w-6 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs font-semibold">2</span>
                      <span class="text-sm text-gray-700">Choose <span class="font-semibold text-primary">Send
                          Money</span></span>
                    </li>
                    <li class="flex items-start gap-3">
                      <span
                        class="h-6 w-6 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs font-semibold">3</span>
                      <div class="flex-1 flex items-center gap-2">
                        <span class="text-sm text-gray-700">Enter the Number</span>
                        <span class="px-2 py-1 bg-primary/10 text-primary rounded text-sm font-medium">{{
                          paymentAccountNumber }}</span>
                        <button class="text-xs px-2 py-1 rounded bg-primary text-white"
                          @click="copyToClipboard(paymentAccountNumber)">Copy</button>
                      </div>
                    </li>
                    <li class="flex items-start gap-3">
                      <span
                        class="h-6 w-6 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs font-semibold">4</span>
                      <div class="flex-1 flex items-center gap-2">
                        <span class="text-sm text-gray-700">Enter the Amount</span>
                        <span class="px-2 py-1 bg-primary/10 text-primary rounded text-sm font-semibold">{{
                          selectedPackage?.price }} BDT</span>
                        <button class="text-xs px-2 py-1 rounded bg-primary text-white"
                          @click="copyToClipboard(String(selectedPackage?.price))">Copy</button>
                      </div>
                    </li>
                    <li class="flex items-start gap-3">
                      <span
                        class="h-6 w-6 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs font-semibold">5</span>
                      <span class="text-sm text-gray-700">Now enter your <span class="font-semibold text-primary">BKash
                          PIN</span> to confirm.</span>
                    </li>
                    <li class="flex items-start gap-3">
                      <span
                        class="h-6 w-6 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs font-semibold">6</span>
                      <div class="flex-1">
                        <label class="text-sm text-gray-700">Put the <span
                            class="font-semibold text-primary">Transaction ID</span> in the box below and press
                          Verify</label>
                        <input v-model="transactionId" type="text" placeholder="Enter Transaction ID"
                          class="mt-2 w-full border rounded-lg px-3 py-2" />
                      </div>
                    </li>
                  </ol>
                </div>
              </div>

              <!-- Footer -->
              <div class="bg-gray-50 px-3 md:px-6 py-2 rounded-b-xl flex items-center justify-between">
                <button @click="closePurchase"
                  class="px-4 py-2 bg-white border rounded-lg text-gray-700 hover:bg-gray-100">Cancel</button>
                <button @click="confirmPurchase" :disabled="purchasing || !transactionId.trim()"
                  :class="['px-5 py-2 rounded-lg text-white font-medium', (purchasing || !transactionId.trim()) ? 'bg-green-300' : 'bg-green-600 hover:bg-green-700']">
                  {{ purchasing ? 'Processing...' : `Verify` }}
                </button>
              </div>
            </div>

          </div>
        </div>
      <!-- </div>
    </div> -->
  </div>

</template>

<script>
import AdminDashboardHeader from '@/components/business/AdminDashboardHeader.vue'
import { push } from 'notivue'
import axios from 'axios'
import Swal from 'sweetalert2'

export default {
  name: 'BuyLeadCredits',
  components: { AdminDashboardHeader },
  data() {
    return {
      packages: [],
      showPurchaseModal: false,
      selectedPackage: null,
      paymentMethod: 'Bkash',
      transactionId: '',
      purchasing: false,
      history: [],
      loading: false,
      paymentNumbers: {
        Bkash: '01919215182',
        Nagad: '',
        Rocket: ''
      }
    }
  },
  computed: {
    leadCredits() {
      return this.$store.getters.myBusiness?.lead_credits
    },
    isCreditsExpired() {
      if (!this.leadCredits?.expire_date) return false;
      const expireDate = new Date(this.leadCredits.expire_date);
      const now = new Date();
      return this.leadCredits.paid_credits > 0 && now > expireDate;
    },
    regularPackages() {
      return this.packages.filter(pkg => pkg.name !== 'Quick');
    },
    paymentAccountNumber() {
      const method = this.paymentMethod || 'Bkash'
      const numbers = this.paymentNumbers || {}
      return numbers[method] || ''
    }
  },
  methods: {
    // Single method flow (bKash Personal)
    copyToClipboard(value) {
      try {
        navigator.clipboard?.writeText(value)
        push.success('Copied to clipboard')
      } catch (_) {
        // ignore
      }
    },
    paymentIcon(method) { return '' },
    async refreshCredits() {
      try {
        await this.$store.dispatch('fetchMyBusiness')
      } catch (e) {
        // no-op
      }
    },
    openPurchase(pkg) {
      this.selectedPackage = pkg
      this.showPurchaseModal = true
    },
    closePurchase() {
      this.showPurchaseModal = false
      this.selectedPackage = null
      this.transactionId = ''
      this.paymentMethod = 'Bkash'
    },
    async confirmPurchase() {
      if (!this.selectedPackage) return
      if (!this.transactionId.trim()) {
        push.error('Please enter a transaction ID')
        return
      }

      // SweetAlert2 confirmation
      const result = await Swal.fire({
        title: 'Confirm Transaction',
        html: `<div class="text-left">
                 <p class="mb-2">Are you sure this transaction is correct?</p>
                 <p class="text-sm text-gray-600">Amount: <b>${this.selectedPackage.price} BDT</b></p>
                 <p class="text-sm text-gray-600">Txn ID: <b>${this.transactionId}</b></p>
               </div>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, verify',
        cancelButtonText: 'Cancel'
      })

      if (!result.isConfirmed) {
        return
      }
      this.purchasing = true
      try {
        const payload = {
          package_id: this.selectedPackage.id,
          payment_method: this.paymentMethod,
          transaction_id: this.transactionId
        }
        const response = await axios.post('/user/business/lead/credits/purchase', payload)
        if (response?.data?.status) {
          push.success('Credits purchased successfully')
          await this.$store.dispatch('fetchMyBusiness')
          this.closePurchase()
          this.fetchHistory()
        } else {
          push.error(response?.data?.message || 'Purchase failed')
        }
      } catch (error) {
        push.error(error?.response?.data?.message || 'Failed to purchase credits')
      } finally {
        this.purchasing = false
      }
    },
    async fetchHistory() {
      try {
        const response = await axios.get('/user/business/lead/credits/purchase')
        if (response?.data?.status) {
          const data = response.data.data
          // API doc shows a single object; normalize to array
          this.history = Array.isArray(data) ? data : (data ? [data] : [])
        }
      } catch (_) {
        // ignore
      }
    },
    formatDate(dateString) {
      if (!dateString) return 'N/A'
      const date = new Date(dateString)
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    },
    async fetchPackages() {
      try {
        this.loading = true;
        const response = await axios.get('/user/business/lead/credits/packages');

        if (response?.data?.status) {
          this.packages = response.data.data || [];
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
  async mounted() {
    if (!this.$store.getters.myBusiness) {
      await this.$store.dispatch('fetchMyBusiness')
    }
    this.fetchPackages()
    this.fetchHistory()
  }
}
</script>

<style scoped>
.font-poppins {
  font-family: 'Poppins', sans-serif;
}
</style>
