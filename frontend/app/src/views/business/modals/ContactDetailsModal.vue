<template>
  <div
    v-if="open"
    class="fixed inset-0 z-[60] flex items-stretch md:items-center justify-center bg-white md:bg-black md:bg-opacity-30 p-0 md:p-4"
    style="min-height: 100vh"
  >
    <div
      class="bg-white w-full h-full md:h-auto md:rounded-xl md:shadow-lg md:max-w-2xl md:max-h-[90vh] overflow-y-auto"
      @click.stop
    >
      <MobileModalHeader title="Contact Details" @back="$emit('close')" />
      <!-- Close button -->

      <div class="p-4 md:p-6 md:pb-4">
        <div class="bg-white rounded-2xl shadow-lg p-4 py-6 min-h-full relative">
          <button
            @click="$emit('close')"
            class="hidden md:block absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors"
          >
            <XMarkIcon class="w-6 h-6" />
          </button>
          <div class="md:flex md:items-start">
            <div
              class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 md:mx-0 md:h-10 md:w-10"
            >
              <UserIcon class="h-6 w-6 text-green-600" />
            </div>
            <div class="mt-3 text-center md:mt-0 md:ml-4 md:text-left w-full">
              <h3
                class="text-lg md:text-xl leading-6 font-medium text-gray-900 mb-4"
              >
                Contact Details
              </h3>

              <div
                v-if="
                  contactData &&
                  (contactData.name || contactData.number || contactData.email)
                "
                class="space-y-4"
              >
                <!-- Profile Image and Name -->
                <div class="flex items-center gap-4">
                  <img
                    :src="
                      contactData.profile ||
                      'https://placehold.co/100x100/0b845c/white?text=Profile'
                    "
                    alt="Profile"
                    class="w-16 h-16 rounded-full object-cover border-2 border-gray-200"
                  />
                  <div>
                    <h4 class="text-xl font-semibold text-gray-900">
                      {{ contactData.name }}
                    </h4>
                    <VerifiedBadge
                      :accountVerified="contactData.account_verification"
                    />
                  </div>
                </div>

                <!-- Contact Information -->
                <div class="space-y-3 text-left">
                  <div
                    v-if="contactData.number"
                    class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg"
                  >
                    <PhoneIcon class="h-5 text-blue-600" />
                    <div class="w-4/5 md:w-auto">
                      <p class="text-sm md:text-base text-gray-600">Number</p>
                      <p class="font-medium text-gray-900 overflow-x-auto">
                        {{ contactData.number }}
                      </p>
                    </div>
                    <button
                      @click="copyToClipboard(contactData.number)"
                      class="ml-auto p-1 text-gray-400 hover:text-gray-600"
                      title="Copy number"
                    >
                      <DocumentDuplicateIcon class="w-4 h-4" />
                    </button>
                  </div>

                  <div
                    v-if="contactData.email"
                    class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg"
                  >
                    <EnvelopeIcon class="h-5 text-blue-600" />
                    <div class="w-4/5 md:w-auto">
                      <p class="text-sm md:text-base text-gray-600">Email</p>
                      <p class="font-medium text-gray-900 overflow-x-auto">
                        {{ contactData.email }}
                      </p>
                    </div>
                    <button
                      @click="copyToClipboard(contactData.email)"
                      class="ml-auto p-1 text-gray-400 hover:text-gray-600"
                      title="Copy email"
                    >
                      <DocumentDuplicateIcon class="w-4 h-4" />
                    </button>
                  </div>

                  <div
                    v-if="contactData.address"
                    class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg"
                  >
                    <MapPinIcon class="w-5 h-5 text-blue-600" />
                    <div class="w-4/5 md:w-auto">
                      <p class="text-sm text-gray-600">Address</p>
                      <p class="font-medium text-gray-900 overflow-x-auto">
                        {{ contactData.address }}
                      </p>
                    </div>
                    <button
                      @click="copyToClipboard(contactData.address)"
                      class="ml-auto p-1 text-gray-400 hover:text-gray-600"
                      title="Copy address"
                    >
                      <DocumentDuplicateIcon class="w-4 h-4" />
                    </button>
                  </div>

                  <div
                    v-if="contactData.location"
                    class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg"
                  >
                    <MapPinIcon class="w-5 h-5 text-blue-600" />
                    <div class="w-4/5 md:w-auto">
                      <p class="text-sm text-gray-600">Location</p>
                      <p class="font-medium text-gray-900 overflow-x-auto">
                        {{ setLocationName(contactData.location) }}
                      </p>
                    </div>
                  </div>

                  <div
                    v-if="contactData.post_code"
                    class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg"
                  >
                    <TagIcon class="w-5 h-5 text-blue-600" />
                    <div class="w-4/5 md:w-auto">
                      <p class="text-sm text-gray-600">Post Code</p>
                      <p class="font-medium text-gray-900 overflow-x-auto">
                        {{ contactData.post_code }}
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3 mt-6 text-sm md:text-base">
                  <button
                    v-if="contactData.number"
                    @click="makeCall(contactData.number)"
                    class="flex-1 flex items-center justify-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                  >
                    <PhoneIcon class="w-4 h-4" />
                    Call Now
                  </button>
                  <button
                    v-if="contactData.email"
                    @click="sendEmail(contactData.email)"
                    class="flex-1 flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                  >
                    <EnvelopeIcon class="w-4 h-4" />
                    Send Email
                  </button>
                </div>

                <!-- Send Message Button -->
                <div class="mt-4 text-sm md:text-base">
                  <button
                    @click="sendMessage"
                    class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors font-medium"
                  >
                    <ChatBubbleLeftRightIcon class="w-5 h-5" />
                    Send Message
                  </button>
                </div>
              </div>

              <div v-else class="text-center py-8">
                <ExclamationTriangleIcon
                  class="w-12 h-12 text-yellow-500 mx-auto mb-4"
                />
                <p class="text-gray-600">
                  No contact details available for this lead
                </p>
                <p class="text-sm text-gray-500 mt-2">
                  The contact information may not be provided by the user
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-gray-50 px-4 py-3 md:px-6 md:flex md:flex-row-reverse">
        <button
          @click="$emit('close')"
          class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 md:ml-3 md:w-auto md:text-sm"
        >
          Close
        </button>
      </div>
      <!-- </div> -->
    </div>
  </div>
</template>

<script>
import {
  UserIcon,
  PhoneIcon,
  EnvelopeIcon,
  MapPinIcon,
  TagIcon,
  DocumentDuplicateIcon,
  ExclamationTriangleIcon,
  XMarkIcon,
  ChatBubbleLeftRightIcon,
} from "@heroicons/vue/24/outline";
import { push } from "notivue";
import VerifiedBadge from "@/components/common/VerifiedBadge.vue";
import MobileModalHeader from "@/components/common/MobileModalHeader.vue";
import { useModalScroll } from "@/composables/useModalScroll";

export default {
  name: "ContactDetailsModal",
  components: {
    UserIcon,
    PhoneIcon,
    EnvelopeIcon,
    MapPinIcon,
    TagIcon,
    DocumentDuplicateIcon,
    ExclamationTriangleIcon,
    XMarkIcon,
    ChatBubbleLeftRightIcon,
    VerifiedBadge,
    MobileModalHeader,
  },
  setup() {
    const { openModal, closeModal } = useModalScroll();
    return { openModal, closeModal };
  },
  props: {
    open: {
      type: Boolean,
      default: false,
    },
    contactData: {
      type: Object,
      default: null,
    },
    leadData: {
      type: Object,
      default: null,
    },
  },
  watch: {
    open(newVal) {
      if (newVal) {
        this.openModal("contact-details-modal");
      } else {
        this.closeModal("contact-details-modal");
      }
    },
  },
  methods: {
    setLocationName(location) {
      if (location.upazila_name) {
        return `${location.upazila_name}, ${location.district_name}`;
      } else {
        return `${location.district_name}, District`;
      }
    },
    copyToClipboard(text) {
      navigator.clipboard
        .writeText(text)
        .then(() => {
          push.success("Copied to clipboard!");
        })
        .catch(() => {
          push.error("Failed to copy to clipboard");
        });
    },
    makeCall(phoneNumber) {
      window.open(`tel:${phoneNumber}`, "_self");
    },
    sendEmail(email) {
      window.open(`mailto:${email}`, "_self");
    },
    async sendMessage() {
      try {
        if (!this.leadData || !this.contactData) {
          push.error("Lead data not available");
          return;
        }

        // Get the user ID from the contact data
        const userId = this.contactData.id;
        if (!userId) {
          push.error("User ID not available");
          return;
        }

        // Get the business ID from the current user's business
        const businessId = this.$store.getters.myBusiness?.id;
        if (!businessId) {
          push.error("Business data not available");
          return;
        }

        // Start conversation with the user
        const response = await this.$store.dispatch("startConversation", {
          business_id: businessId,
          user_id: userId,
          role: "business"
        });

        if (response && response.data) {
          const conversation = response.data;

          // Fetch conversation messages first
          await this.$store.dispatch(
            "fetchConversationMessages",
            conversation.id
          );

          // Create a draft message with lead details
          const leadDetails = this.createLeadMessage();

          // Store the draft message in localStorage for the user to review
          localStorage.setItem(
            `conversation_draft_${conversation.id}`,
            leadDetails
          );

          // Close the modal
          this.$emit("close");

          // Navigate directly to conversation messages page
          this.$router.push({
            name: "conversation-messages",
            params: { id: conversation.id },
          });

          push.success(
            "Conversation started! You can review and send your message."
          );
        }
      } catch (error) {
        console.error("Error starting conversation:", error);
        push.error("Failed to start conversation. Please try again.");
      }
    },
    setLocationName(location) {
      if (location.upazila_name) {
        return `${location.upazila_name}, ${location.district_name}`;
      } else {
        return `${location.district_name}, District`;
      }
    },
    createLeadMessage() {
      const lead = this.leadData;
      const contact = this.contactData;

      let message = `Hi ${contact.name || "there"}! 👋\n\n`;
      message += `I saw your inquiry request and I'm interested in helping you. Here are the details:\n\n`;

      if (lead.category?.name) {
        message += `📋 Category: ${lead.category.name}\n`;
      }

      if (lead.description) {
        message += `📝 Description: ${lead.description}\n`;
      }

      if (lead.quantity && lead.unit_name) {
        message += `📊 Quantity: ${lead.quantity} ${lead.unit_name}\n`;
      }

      if (lead.location) {
        message += `📍 Location: ${this.setLocationName(lead.location)}\n`;
      }

      message += `\nI'd love to discuss this further and provide you with the best solution. When would be a good time to talk?`;

      return message;
    },
  },
};
</script>
