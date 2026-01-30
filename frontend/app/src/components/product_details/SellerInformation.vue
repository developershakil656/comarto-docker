<template>
  <div>
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-300 mb-4">
      <h2 class="font-semibold text-gray-800 mb-4">Seller Information</h2>
      <div class="bg-white rounded-xl flex items-start gap-2 my-4">
            <router-link :to="`/${business.slug}`" class="w-20 h-20">
            <img :src="business.business_profile || 'https://placehold.co/64x64/FFD700/000000?text=store'" :alt="business.business_name || 'Seller'" class="mx-auto w-20 h-20 rounded-lg object-cover border" />
        </router-link>
        <div class="flex-1">
          <div class="flex items-center gap-2">
            <router-link :to="`/${business.slug}`" class="font-semibold text-gray-900 capitalize">{{ business.business_name || 'N/A' }}</router-link>
            
          </div>
          <VerifiedBadge :accountVerified="business.account_verification" />
          <div class="mt-2 flex flex-wrap gap-1">
            <span v-for="(type, idx) in business.business_type || []" :key="idx" class="bg-primary/10 font-semibold py-0.5 rounded px-1 mr-1 text-xs">{{ type }}</span>
          </div>
        </div>
      </div>
      <div class="flex items-center gap-1 flex-wrap border-b mb-3 pb-3">
        <div class="flex bg-primary font-semibold px-1 rounded-md items-center">
          <span class="text-white text-base">{{ business.rating?.rate || 'N/A' }}</span>
          <StarIcon class="text-yellow-400 h-4 pl-1" />
        </div>
        <span class="text-xs text-gray-600">{{ business.rating ? business.rating.count + ' Ratings' : '' }}</span>
        <span class="text-xs font-semibold ml-auto">{{ businessDetails?.in_business ? businessDetails.in_business + ' Years in business' : '' }}</span>
      </div>
      <div class="border-b mb-3 pb-3">
        <h2 class="font-semibold text-gray-800 mb-2">Address</h2>
        <p class="text-sm text-gray-900">{{ fullAddress }}</p>
        <div v-if="businessDetails?.direction">
          <button @click="openDirection" class="flex text-primary items-center text-sm mt-3 px-3 py-2 rounded-md font-semibold transition border border-primary/15 hover:bg-gray-200 duration-200 ease-in-out">
            <MapIcon class="h-4 mr-2"/>Get Direction
          </button>
        </div>
      </div>
      <div>
        <button 
          class="bg-primary text-white gap-1 w-full py-2 rounded-md flex mb-2 justify-center items-center text-sm font-medium hover:bg-primary/85 transition-all"
          @click.stop.prevent="callBusiness(business.number)"
        >
          <PhoneIcon class="inline-block h-4" />{{ business.number || 'N/A' }}
        </button>
        <button @click="openWhatsApp" v-if="business.alternate_number" class="text-primary gap-1 w-full py-1 rounded-md flex mb-2 justify-center items-center text-sm font-medium border-primary border hover:bg-primary/15 transition-all">
          <img src="@/assets/icons/svgs/WhatsappIcon.svg" class="w-6" alt="WhatsApp" />
          WhatsApp
        </button>
        <button 
          class="bg-blue-600 text-white gap-1 w-full py-2 rounded-md flex mb-2 justify-center items-center text-sm font-medium hover:bg-blue-500 transition-all"
          @click.stop.prevent="startBestPriceChat"
          :disabled="isOwnBusiness"
          :class="{ 'opacity-50 cursor-not-allowed': isOwnBusiness, 'hover:bg-blue-500': !isOwnBusiness }">
          <ReceiptPercentIcon class="inline-block h-4" />Get Best Price
        </button>
        <button v-if="businessDetails?.website" class="bg-red-500 text-white gap-1 w-full py-2 rounded-md flex mb-2 justify-center items-center text-sm font-medium hover:bg-red-400 transition-all">
          <GlobeAltIcon class="inline-block h-4" />
          <a :href="businessDetails.website" target="_blank" class="text-white underline">Visit Website</a>
        </button>
      </div>
    </div>
    <div class="w-full flex justify-center mb-2">
      <img src="https://placehold.co/325x325" alt="Register Business" class="rounded-md shadow">
    </div>
  </div>
</template>
<script>
import { StarIcon, PhoneIcon, ReceiptPercentIcon } from "@heroicons/vue/24/solid";
import { MapIcon, GlobeAltIcon } from "@heroicons/vue/24/outline";
import VerifiedBadge from '@/components/common/VerifiedBadge.vue';
export default {
  props: {
    business: { type: Object, required: true },
    businessDetails: { type: Object, required: false },
    product: { type: Object, required: false }
  },
  components: { StarIcon, PhoneIcon, ReceiptPercentIcon, MapIcon, GlobeAltIcon, VerifiedBadge },
  computed: {
    fullAddress() {
      const { address, location = {} } = this.business;
      return `${address ? address + ', ' : ''}${location.upazila_name || ''}, ${location.district_name || ''}`;
    },
    isOwnBusiness() {
      return this.business?.id === this.$store.getters.user?.business?.id;
    }
  },
  methods: {
    openDirection() {
      if (this.businessDetails?.direction) {
        window.open(this.businessDetails.direction, '_blank');
      }
    },
    callBusiness(number) {
      window.location.href = `tel:${number}`
    },
    openWhatsApp() {
      const number = this.business.alternate_number;
      if (!number) return;
      const url = window.location.href;
      const message = encodeURIComponent(`Hello, I am interested in your product! ${url}`);
      window.open(`https://wa.me/${number}?text=${message}`, '_blank');
    },
    async startBestPriceChat() {
      try {
        const userId = this.$store.getters.user?.id || null;
        // Start conversation with business and user_id
        const res = await this.$store.dispatch('startConversation', {
          business_id: this.business.id,
          user_id: userId
        });
        const conversation = res?.data;
        if (conversation?.id) {
          await this.$store.dispatch('fetchConversationMessages', conversation.id);
          
          // Create a draft message with product details
          const productSlug = this.$route?.params?.slug;
          const productId = this.$route?.params?.id;
          const baseUrl = window.location.origin;
          const productUrl = `${baseUrl}/product/${productSlug}`;
          const productName = this.product?.name || 'this product';
          
          const draftMessage = `Hi! I'm interested in getting the best price for this product: ${productUrl}\n\nProduct: ${productName}\n\nCould you please provide me with your best offer?`;
          localStorage.setItem(`conversation_draft_${conversation.id}`, draftMessage);
          
          this.$router.push({ 
            name: 'conversation-messages',
            params: { id: conversation?.id }
          });
        }
      } catch (e) {
        console.error('Failed to start best-price chat:', e);
      }
    }
  }
};
</script>