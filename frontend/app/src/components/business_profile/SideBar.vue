<template>
  <div class="w-full font-medium">
    <div
      class="md:rounded-xl md:shadow md:px-4 md:pt-4 md:pb-2 md:mb-4 text-gray-800 md:border border-gray-100"
    >
      <!-- Contact Section -->
      <div>
        <h2 class="font-semibold mb-2 text-lg md:text-xl">Contact</h2>
        <div class="border-b">
          <button
            @click="callPhone(businessData?.number)"
            class="flex items-center gap-2 py-2.5 px-2 my-1 cursor-pointer hover:bg-primary/15 duration-200 ease-in-out rounded-md w-full text-left"
          >
            <PhoneIcon class="inline-block h-5 text-primary" />{{
              businessData?.number || "N/A"
            }}
          </button>
        </div>
        <div class="border-b" v-if="businessData?.alternate_number">
          <button
            @click="callPhone(businessData.alternate_number)"
            class="flex items-center gap-2 py-2.5 px-2 my-1 cursor-pointer hover:bg-primary/15 duration-200 ease-in-out rounded-md w-full text-left"
          >
            <PhoneIcon class="inline-block h-5 text-primary" />{{
              businessData.alternate_number
            }}
          </button>
        </div>
        <div class="border-b">
          <button
            @click="openWhatsApp"
            class="flex items-center gap-2 py-2.5 px-2 my-1 cursor-pointer hover:bg-primary/15 duration-200 ease-in-out rounded-md w-full text-left"
          >
            <img
              src="@/assets/icons/svgs/WhatsappIcon.svg"
              class="w-6"
              alt="WhatsApp"
            />
            WhatsApp
          </button>
        </div>
        <div class="border-b" v-if="businessData?.email">
          <a
            :href="`mailto:${businessData.email}`"
            class="flex items-center gap-2 py-2.5 px-2 my-1 cursor-pointer hover:bg-primary/15 duration-200 ease-in-out rounded-md"
          >
            <EnvelopeIcon class="inline-block h-5 text-primary" />
            {{ businessData.email }}
          </a>
        </div>
        <div class="border-b" v-if="businessDetails?.facebook">
          <a
            :href="businessDetails.facebook"
            target="_blank"
            class="flex items-center gap-2 py-2.5 px-2 my-1 cursor-pointer hover:bg-primary/15 duration-200 ease-in-out rounded-md"
          >
            <img
              src="@/assets/icons/svgs/FacebookIcon.svg"
              class="w-6"
              alt="Facebook"
            />
            Facebook
          </a>
        </div>

        <div class="border-b" v-if="businessDetails?.website">
          <a
            :href="businessDetails.website"
            target="_blank"
            class="flex items-center gap-2 py-2.5 px-2 my-1 cursor-pointer hover:bg-primary/15 duration-200 ease-in-out rounded-md"
          >
            <GlobeAltIcon class="inline-block h-5 text-primary" />Visit Website
          </a>
        </div>
      </div>

      <!-- Address Section -->
      <div class="mt-4">
        <div class="border-b pb-3">
          <h2 class="font-semibold text-lg md:text-xl mb-2">Address</h2>
          <div class="py-3 px-2">
            <p class="">{{ businessData?.address || "N/A" }}</p>
            <div v-if="businessDetails?.direction">
              <a
                :href="businessDetails.direction"
                target="_blank"
                class="flex text-primary items-center text-sm mt-3 px-3 py-2 rounded-md font-semibold transition border border-primary/15 hover:bg-gray-200 duration-200 ease-in-out"
              >
                <MapIcon class="h-4 mr-2" />Get Direction</a
              >
            </div>
          </div>
        </div>
      </div>

      <!-- Opening Hours Section -->
      <div class="border-b">
        <Timing :timing="businessDetails?.timing" />
      </div>

      <!-- Action Links -->

      <div class="border-b">
        <button
          @click="shareBusiness"
          class="flex items-center gap-2 py-2.5 px-2 rounded-md cursor-pointer hover:bg-primary/15 duration-200 ease-in-out my-1 w-full text-left"
        >
          <ShareIcon class="h-5 text-primary" />Share
        </button>
      </div>
      <div>
        <button
          @click="rateBusiness"
          class="flex items-center gap-2 py-2.5 px-2 rounded-md cursor-pointer hover:bg-primary/15 duration-200 ease-in-out my-1 w-full text-left"
        >
          <StarIcon class="h-5 text-primary" />Tap to Rate
        </button>
      </div>
    </div>

    <div
      class="rounded-xl shadow p-6 text-gray-800 border border-gray-100 mb-6"
    >
      <div>
        <h2 class="font-semibold text-lg md:text-xl mb-3">Report an error</h2>
        <p>
          💡 Have feedback or spotted an issue? <br />
          Help us keep this information accurate and relevant for you!
        </p>
        <p class="text-primary mt-2">report@comarto.com</p>
        <button
          @click="reportError"
          class="py-2 px-4 text-primary font-semibold border border-primary rounded-md mt-3 hover:bg-primary/15 duration-200 ease-in-out"
        >
          Report Now
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import Timing from "./Timing.vue";
import {
    StarIcon,
    PhoneIcon,
    GlobeAltIcon,
    MapIcon,
    ShareIcon,
    EnvelopeIcon,
} from "@heroicons/vue/24/outline";
export default {
    components: {
        Timing,
        StarIcon,
        PhoneIcon,
        MapIcon,
        GlobeAltIcon,
        ShareIcon,
        EnvelopeIcon
    },
    props: {
        businessData: {
            type: Object,
            default: null
        },
        businessDetails: {
            type: Object,
            default: null
        }
    },
    data() {
        return {}
    },
    methods: {
        callPhone(phoneNumber) {
            if (phoneNumber) {
                window.open(`tel:${phoneNumber}`, '_self');
            }
        },
        openWhatsApp() {
            const whatsappNumber = this.businessData?.alternate_number;
            if (whatsappNumber) {
                const message = `Hi ${this.businessData?.name || 'there'}, I'm interested in your business ${this.businessData?.business_name || ''}. Can you please provide more information about your products/services?`;
                const whatsappUrl = `https://wa.me/${whatsappNumber.replace(/\D/g, '')}?text=${encodeURIComponent(message)}`;
                window.open(whatsappUrl, '_blank');
            } else {
                alert('WhatsApp number not available');
            }
        },
        shareBusiness() {
            if (navigator.share) {
                navigator.share({
                    title: this.businessData?.business_name || 'Business Profile',
                    text: `Check out ${this.businessData?.business_name || 'this business'}`,
                    url: window.location.href
                });
            } else if (navigator.clipboard) {
                // Fallback: copy to clipboard if available
                navigator.clipboard.writeText(window.location.href)
                    .then(() => {
                        alert('Link copied to clipboard!');
                    })
                    .catch(err => {
                        // Fallback to execCommand if clipboard API fails
                        this.copyToClipboardFallback(window.location.href);
                    });
            } else {
                // Ultimate fallback
                this.copyToClipboardFallback(window.location.href);
            }
        },
        
        copyToClipboardFallback(text) {
            // Create a temporary textarea element
            const textArea = document.createElement('textarea');
            textArea.value = text;
            
            // Avoid scrolling to bottom
            textArea.style.top = '0';
            textArea.style.left = '0';
            textArea.style.position = 'fixed';
            textArea.style.opacity = '0';
            
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
                const successful = document.execCommand('copy');
                if (successful) {
                    alert('Link copied to clipboard!');
                } else {
                    alert('Failed to copy link');
                }
            } catch (err) {
                // Fallback to prompt in worst case
                prompt('Copy this link:', text);
            }
            
            document.body.removeChild(textArea);
        },
        rateBusiness() {
            // Navigate to review page with the selected rating
            this.$router.push({
                name: 'make-review',
                params: {
                    businessId: this.businessData.id,
                    businessSlug: this.$route.params.slug
                },
                query: {
                    rating: 5
                }
            });
        },
        reportError() {
        // 1. Format the name/slug for the subject line
        const businessName = this.businessData.name || this.businessData.slug;
        const capitalizedName = businessName.charAt(0).toUpperCase() + businessName.slice(1);

        // 2. Build a structured body
        const subject = `Report Issue: ${capitalizedName}`;
        const body = `Hello Comarto Support Team,

            I would like to report an issue regarding the following business:
            - Business Name: ${capitalizedName}
            - Business Link: ${window.location.href}

            Please let us know the nature of the issue (check all that apply):
            [ ] Incorrect contact information
            [ ] Business has closed down
            [ ] Fraudulent or misleading content
            [ ] Intellectual property violation
            [ ] Other: ______________________

            Additional Details:
            (Please describe the error or concern here...)

            ---
            Reported by: ${this.user?.name || 'Anonymous User'}
            User Email: ${this.user?.email || 'Not provided'}`;

        // 3. Construct the mailto link (using report@comarto.com as recommended)
        const mailtoLink = `mailto:report@comarto.com?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;

        // 4. Trigger the mail client
        window.location.href = mailtoLink;

        }
}

}
</script>
