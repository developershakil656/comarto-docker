<template>
  <div
    class="fixed inset-0 z-[60] flex items-stretch md:items-center justify-center bg-white md:bg-gray-500 md:bg-opacity-75 p-0 md:p-4">
    <div
      class="bg-white w-full h-full md:h-auto md:rounded-lg md:shadow-xl md:max-w-4xl relative overflow-y-auto text-sm md:text-base">
      <MobileModalHeader title="Business Proposals" @back="$emit('close')" />
      <!-- Close button -->
      <button @click="$emit('close')"
        class="hidden md:block absolute top-4 right-4 z-10 p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-colors"
        title="Close">
        <XMarkIcon class="w-5 h-5" />
      </button>

      <!-- Header -->
      <div class="p-4 md:p-6">
        <div class="bg-white rounded-2xl shadow-lg md:rounded-none md:shadow-none p-4 md:p-0">
          <div class="md:flex md:items-start">
            <div
              class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 md:mx-0 md:h-10 md:w-10">
              <CheckBadgeIcon class="h-6 w-6 text-green-600" />
            </div>
            <div class="mt-3 text-center md:mt-0 md:ml-4 md:text-left w-full">
              <h3 class="text-base md:text-lg leading-6 font-medium text-gray-900 mb-2">
                Business Proposals
              </h3>
              <p class="text-sm md:text-base text-gray-500 mb-4">
                {{ proposals.length }} business{{ proposals.length !== 1 ? 'es' : '' }} have shown interest in your
                inquiry
              </p>

              <!-- Inquiry Details -->
              <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h4 class="text-sm md:text-base font-medium text-gray-900 mb-2">Your Inquiry Details</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs md:text-sm">
                  <div>
                    <span class="font-medium text-gray-600">Category:</span>
                    <span class="ml-2 text-gray-900">{{ inquiry.category?.name }}</span>
                  </div>
                  <div>
                    <span class="font-medium text-gray-600">Quantity:</span>
                    <span class="ml-2 text-gray-900">{{ inquiry.quantity }} {{ inquiry.unit_name }}</span>
                  </div>
                  <div v-if="inquiry?.email">
                    <span class="font-medium text-gray-600">Email:</span>
                    <span class="ml-2 text-gray-900">{{ inquiry.email }}</span>
                  </div>
                  <div>
                    <span class="font-medium text-gray-600">Location:</span>
                    <span class="ml-2 text-gray-900">{{ setLocationName(inquiry.location) }}</span>
                  </div>
                  <div v-if="inquiry.description">
                    <span class="font-medium text-gray-600">Description:</span>
                    <span class="ml-2 text-gray-900">{{ inquiry.description }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Proposals List -->
        <div class="my-2">
          <div class="space-y-4 max-h-96 overflow-y-auto">
            <div v-for="proposal in proposals" :key="proposal.id"
              class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
              <div class="flex flex-col md:flex-row md:items-start md:space-x-4 gap-4">
                <!-- Business Profile Image -->
                <div class="flex-shrink-0">
                  <img :src="proposal.business_profile || 'https://placehold.co/80x80/0b845c/white?text=Business'"
                    alt="Business Profile" class="w-16 h-16 rounded-lg object-cover border-2 border-gray-200" />
                </div>

                <!-- Business Information -->
                <div class="flex-1 min-w-0">
                  <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                      <h4 class="text-base md:text-lg font-semibold text-gray-900 truncate">
                        {{ proposal.business_name }}
                      </h4>
                      <VerifiedBadge :accountVerified="proposal.account_verification" />
                    </div>
                    <div v-if="proposal.rating" class="flex items-center space-x-1">
                      <StarIcon class="w-4 h-4 text-yellow-400 fill-current" />
                      <span class="text-xs md:text-sm font-medium text-gray-900">{{ proposal.rating.rate }}</span>
                      <span class="text-xs md:text-sm text-gray-500">({{ proposal.rating.count }})</span>
                    </div>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs md:text-sm mb-3">
                    <div>
                      <span class="font-medium text-gray-600">Location:</span>
                      <span class="ml-2 text-gray-900">{{ proposal.location?.upazila_name }}, {{
                        proposal.location?.district_name }}</span>
                    </div>
                    <div v-if="proposal.in_business">
                      <span class="font-medium text-gray-600">In Business:</span>
                      <span class="ml-2 text-gray-900">{{ proposal.in_business }} years</span>
                    </div>
                  </div>

                  <!-- Contact Information -->
                  <div class="flex flex-wrap md:items-center gap-2 md:space-x-6">
                    <div v-if="proposal.number" class="flex items-center gap-2">
                      <PhoneIcon class="w-4 h-4 text-gray-400" />
                      <span class="text-xs md:text-sm text-gray-600">{{ proposal.number }}</span>
                      <button @click="copyToClipboard(proposal.number)"
                        class="p-1 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded transition"
                        title="Copy number">
                        <ClipboardDocumentIcon class="w-4 h-4" />
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex-shrink-0 flex flex-col space-y-2 w-full md:w-auto mt-3 md:mt-0">
                  <button v-if="proposal.number" @click="makeCall(proposal.number)"
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm md:text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 w-full md:w-auto">
                    <PhoneIcon class="w-4 h-4 mr-1" />
                    Call
                  </button>

                  <button v-if="proposal.alternate_number" @click="openWhatsApp(proposal.alternate_number)"
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm md:text-base font-medium rounded-md text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400 w-full md:w-auto">
                    <img src="@/assets/icons/svgs/WhatsappIcon.svg" class="w-4 h-4 mr-1" alt="WhatsApp" />
                    WhatsApp
                  </button>

                  <button @click="sendMessage(proposal)"
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm md:text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 w-full md:w-auto">
                    <ChatBubbleLeftRightIcon class="w-4 h-4 mr-1" />
                    Message
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-4 py-3 md:px-6 md:flex md:flex-row-reverse">
          <button @click="$emit('close')"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-gray-600 text-sm md:text-base font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 md:ml-3 md:w-auto">
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {
  XMarkIcon,
  CheckBadgeIcon,
  StarIcon,
  PhoneIcon,
  ChatBubbleLeftRightIcon
} from '@heroicons/vue/24/outline';
import { push } from 'notivue';
import { ClipboardDocumentIcon } from '@heroicons/vue/24/outline';
import VerifiedBadge from '@/components/common/VerifiedBadge.vue';
import MobileModalHeader from '@/components/common/MobileModalHeader.vue';
import { useModalScroll } from '@/composables/useModalScroll';

export default {
  name: 'ProposalsModal',
  components: {
    XMarkIcon,
    CheckBadgeIcon,
    StarIcon,
    PhoneIcon,
    ChatBubbleLeftRightIcon,
    ClipboardDocumentIcon,
    VerifiedBadge,
    MobileModalHeader
  },
  setup() {
    const { openModal, closeModal } = useModalScroll()
    return { openModal, closeModal }
  },
  props: {
    inquiry: {
      type: Object,
      default: null
    },
    proposals: {
      type: Array,
      default: () => []
    }
  },
  methods: {
    setLocationName(location) {
      if (location.upazila_name) {
        return `${location.upazila_name}, ${location.district_name}`
      } else {
        return `${location.district_name}, District`
      }
    },

    makeCall(phoneNumber) {
      window.open(`tel:${phoneNumber}`, '_self');
    },

    async copyToClipboard(text) {
      try {
        await navigator.clipboard.writeText(text);
        push.success('Number copied to clipboard');
      } catch (e) {
        push.error('Failed to copy');
      }
    },

    openWhatsApp(phoneNumber) {
      const message = `Hi, I'm interested in your business proposal. Can you provide more information?`;
      const cleanedNumber = phoneNumber.replace(/\D/g, '');
      const whatsappUrl = `https://wa.me/${cleanedNumber}?text=${encodeURIComponent(message)}`;
      window.open(whatsappUrl, '_blank');
    },

    async sendMessage(proposal) {
      try {
        if (!this.inquiry || !proposal) {
          push.error('Inquiry or proposal data not available');
          return;
        }

        // Get the business ID from the proposal
        const businessId = proposal.id;
        if (!businessId) {
          push.error('Business ID not available');
          return;
        }

        // Start conversation with the business
        const response = await this.$store.dispatch('startConversation', {
          business_id: businessId
        });

        if (response && response.data) {
          const conversation = response.data;

          // Fetch conversation messages first
          await this.$store.dispatch('fetchConversationMessages', conversation.id);

          // Create a draft message with inquiry details
          const draftMessage = this.createInquiryMessage();

          // Store the draft message in localStorage for the user to review
          localStorage.setItem(`conversation_draft_${conversation.id}`, draftMessage);

          // Close the modal
          this.$emit('close');

          // Navigate directly to conversation messages page
          this.$router.push({
            name: 'conversation-messages',
            params: { id: conversation.id }
          });

          push.success('Conversation started! You can review and send your message.');
        }
      } catch (error) {
        push.error('Failed to start conversation. Please try again.');
      }
    },

    createInquiryMessage() {
      const inquiry = this.inquiry;

      let message = `Hi! I'm interested in your proposal for my inquiry. Here are the details:\n\n`;

      if (inquiry.category?.name) {
        message += `📋 Category: ${inquiry.category.name}\n`;
      }

      if (inquiry.quantity && inquiry.unit_name) {
        message += `📊 Quantity: ${inquiry.quantity} ${inquiry.unit_name}\n`;
      }

      if (inquiry.location) {
        message += `📍 Location: ${this.setLocationName(inquiry.location)}\n`;
      }

      if (inquiry.description) {
        message += `📝 Description: ${inquiry.description}\n`;
      }

      message += `\nI'd love to discuss the details and get your best offer. When would be a good time to talk?`;

      return message;
    }
  },
  mounted() {
    // Register modal for scroll management
    this.openModal('proposals-modal')
  },
  beforeUnmount() {
    // Unregister modal from scroll management
    this.closeModal('proposals-modal')
  }
};
</script>
