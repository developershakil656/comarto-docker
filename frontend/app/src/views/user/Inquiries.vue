<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8 text-sm md:text-base">
      <!-- Header -->
      <div class="mb-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-3">
        <div class="">
          <h1 class="text-xl md:text-3xl font-bold text-gray-900">My Inquiries</h1>
          <p class="mt-2 text-gray-600 md:text-base">Manage your product inquiries and best deal requests</p>
        </div>
        <button 
            @click="createNewInquiry"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm md:text-base font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary/85 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary w-full md:w-auto"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Create Inquiry
          </button>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
          <div class="flex items-center">
            <div class="flex-shrink-0">
                              <div class="w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
            </div>
            <div class="ml-4">
                          <p class="text-sm md:text-base font-medium text-gray-600">Active Inquiries</p>
            <p class="text-lg md:text-2xl font-bold text-gray-900">{{ activeInquiriesCount }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
          <div class="flex items-center">
            <div class="flex-shrink-0">
                              <div class="w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                </div>
            </div>
            <div class="ml-4">
                          <p class="text-sm md:text-base font-medium text-gray-600">Closed Inquiries</p>
            <p class="text-lg md:text-2xl font-bold text-gray-900">{{ closedInquiriesCount }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
          <div class="flex items-center">
            <div class="flex-shrink-0">
                              <div class="w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                  </svg>
                </div>
            </div>
            <div class="ml-4">
                          <p class="text-sm md:text-base font-medium text-gray-600">Total Inquiries</p>
            <p class="text-lg md:text-2xl font-bold text-gray-900">{{ totalInquiriesCount }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Inquiries List -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-base md:text-lg font-semibold text-gray-900">All Inquiries</h2>
        </div>

        <!-- Loading State -->
        <div v-if="inquiriesLoading" class="p-8 text-center text-sm md:text-base">
          <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-gray-600">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Loading inquiries...
          </div>
        </div>

        <!-- Empty State -->
        <div v-else-if="userInquiries.length === 0" class="p-8 text-center text-sm md:text-base">
          <div class="text-gray-400 mb-4">
            <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
          </div>
          <h3 class="text-base md:text-lg font-medium text-gray-900 mb-2">No inquiries yet</h3>
          <p class="text-gray-500 mb-4">You haven't created any inquiries yet. Start by creating your first inquiry.</p>
          <button 
            @click="createNewInquiry"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm md:text-base font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary/85 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Create Inquiry
          </button>
        </div>

        <!-- Inquiries List -->
        <div v-else class="divide-y divide-gray-200">
          <!-- Total inquiries count -->
          <div class="px-6 py-3 bg-gray-50 border-b border-gray-200">
            <div class="text-sm md:text-base text-gray-600">{{ totalInquiriesCount }} inquiries found</div>
          </div>
          <div 
            v-for="inquiry in userInquiries" 
            :key="inquiry.id"
            :class="[
              'p-6 transition-all duration-200',
              inquiry.status === 'closed' ? 'bg-gray-50 opacity-75' : 'hover:bg-gray-50'
            ]"
          >
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
              <div class="flex-1">
                <div class="flex items-center space-x-3 mb-3">
                                      <span 
                      :class="[
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                        inquiry.status === 'closed' 
                          ? 'bg-gray-100 text-gray-800' 
                          : 'bg-primary-100 text-primary-800'
                      ]"
                    >
                      {{ inquiry.status === 'closed' ? 'Closed' : 'Active' }}
                    </span>
                  <span class="text-xs md:text-sm text-gray-500">
                    Created {{ formatDate(inquiry.created_at) }}
                  </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                  <div>
                    <p class="text-xs md:text-sm font-medium text-gray-600">Location</p>
                    <p class="text-xs md:text-sm text-gray-900">{{ setLocationName(inquiry.location) }}</p>
                  </div>
                  <div>
                    <p class="text-xs md:text-sm font-medium text-gray-600">Category</p>
                    <p class="text-xs md:text-sm text-gray-900">{{ inquiry.category?.name }}</p>
                  </div>
                  <div>
                    <p class="text-xs md:text-sm font-medium text-gray-600">Quantity</p>
                    <p class="text-xs md:text-sm text-gray-900">{{ inquiry.quantity }}</p>
                  </div>
                </div>

                <div v-if="inquiry.description">
                  <p class="text-xs md:text-sm font-medium text-gray-600 mb-1">Description</p>
                  <p class="text-xs md:text-sm text-gray-900">{{ inquiry.description }}</p>
                </div>
              </div>

              <div class="mt-4 md:mt-0 md:ml-6 w-full md:w-auto flex-shrink-0 flex flex-col gap-2">
                <!-- View Proposals Button -->
                <button
                  v-if="inquiry.proposal && inquiry.proposal.length > 0"
                  @click="openProposalsModal(inquiry)"
                  class="inline-flex items-center px-4 py-2 border border-transparent text-sm md:text-base font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 w-full md:w-auto"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  View Proposals ({{ inquiry.proposal.length }})
                </button>
                
                <!-- Close Inquiry Button -->
                <button
                  v-if="inquiry.status !== 'closed'"
                  @click="closeInquiryHandler(inquiry.id)"
                  :disabled="closingInquiryId === inquiry.id"
                  class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm md:text-base leading-4 md:leading-5 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary disabled:opacity-50 disabled:cursor-not-allowed w-full md:w-auto"
                >
                  <span v-if="closingInquiryId === inquiry.id">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-600" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Closing...
                  </span>
                  <span v-else>Close Inquiry</span>
                </button>
                <span v-else class="text-sm text-gray-500">Closed</span>
              </div>
            </div>
          </div>
          <!-- Load More Sentinel -->
          <div ref="loadMoreRef" class="h-6"></div>
          <!-- Loading more indicator -->
          <div v-if="inquiriesLoadingMore" class="text-center text-gray-500 py-4">Loading more...</div>
          <div v-else-if="!inquiriesCanLoadMore && userInquiries.length" class="text-center text-gray-400 py-4 text-sm">No more results</div>
        </div>
      </div>
      <InquiryModal v-if="showInquiryModal" @close="showInquiryModal = false" />
      
      <!-- Proposals Modal -->
      <ProposalsModal 
        v-if="showProposalsModal" 
        :inquiry="selectedInquiry"
        :proposals="selectedInquiry?.proposal || []"
        @close="showProposalsModal = false" 
      />
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import {
    push
} from 'notivue'
import InquiryModal from '@/components/modals/InquiryModal.vue'
import ProposalsModal from '@/components/modals/ProposalsModal.vue'

export default {
  name: 'Inquiries',
  components: {
    InquiryModal,
    ProposalsModal
  },
  data() {
    return {
      closingInquiryId: null,
      showInquiryModal: false,
      showProposalsModal: false,
      selectedInquiry: null,
      observer: null
    }
  },
  computed: {
    ...mapGetters(['userInquiries', 'inquiriesLoading', 'inquiriesMeta', 'inquiriesCanLoadMore', 'inquiriesLoadingMore']),
    
    activeInquiriesCount() {
      return this.userInquiries.filter(inquiry => inquiry.status !== 'closed').length
    },
    
    closedInquiriesCount() {
      return this.userInquiries.filter(inquiry => inquiry.status === 'closed').length
    },
    
    totalInquiriesCount() {
      return this.inquiriesMeta?.total || this.userInquiries.length
    }
  },
  methods: {
    ...mapActions(['fetchUserInquiries', 'closeInquiry', 'loadMoreUserInquiries']),
    
    setLocationName(location) {
      if (location.upazila_name) {
        return `${location.upazila_name}, ${location.district_name}`
      } else {
        return `${location.district_name}, District`
      }
    },

    async closeInquiryHandler(inquiryId) {
      this.closingInquiryId = inquiryId
      try {
        await this.closeInquiry(inquiryId)
        push.success('Inquiry closed successfully!')
      } catch (error) {
        push.error(error.message || 'Failed to close inquiry')
      } finally {
        this.closingInquiryId = null
      }
    },
    
    formatDate(dateString) {
      if (!dateString) return 'Unknown date'
      const date = new Date(dateString)
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      })
    },
    
    createNewInquiry() {
      // Navigate to home page to open the inquiry modal
      this.showInquiryModal = true
      // You might want to emit an event or use a global state to trigger the modal
    },
    
    openProposalsModal(inquiry) {
      this.selectedInquiry = inquiry
      this.showProposalsModal = true
    },

    setupInfiniteScroll() {
      if (this.observer) {
        this.observer.disconnect()
      }
      const options = { root: null, rootMargin: '200px', threshold: 0 }
      this.observer = new IntersectionObserver(async (entries) => {
        const entry = entries[0]
        if (entry && entry.isIntersecting && this.inquiriesCanLoadMore && !this.inquiriesLoadingMore && !this.inquiriesLoading) {
          await this.loadMoreUserInquiries()
        }
      }, options)
      if (this.$refs.loadMoreRef) {
        this.observer.observe(this.$refs.loadMoreRef)
      }
    }
  },
  
  async mounted() {
    try {
      await this.fetchUserInquiries()
      this.$nextTick(() => {
        this.setupInfiniteScroll()
      })
    } catch (error) {
      console.error('Error fetching inquiries:', error)
      push.error('Failed to load inquiries')
    }
  },

  beforeUnmount() {
    if (this.observer) {
      this.observer.disconnect()
      this.observer = null
    }
  },

  watch: {
    inquiriesLoading(newVal) {
      if (!newVal) {
        this.$nextTick(() => this.setupInfiniteScroll())
      }
    },
    userInquiries() {
      this.$nextTick(() => this.setupInfiniteScroll())
    }
  }
}
</script>
