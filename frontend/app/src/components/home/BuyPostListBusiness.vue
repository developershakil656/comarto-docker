<template>
  <div class="lg:pl-4 mt-4 flex justify-center gap-4 lg:block lg:mt-0 overflow-hidden">
    <!-- Buy Post Requirements Section -->
    <div class="bg-white rounded-lg shadow-md mb-4 relative w-full aspect-[5/2.8] cursor-pointer" @click="showInquiryModal = true">
      <img
        src="./../../assets/images/buy-requirement.png"
        alt="Buy Requirement"
        class="w-full h-full object-cover rounded-lg"
      />
      <div class="absolute top-0 p-6">
          <h2 class="text-sm md:text-xl text-white mb-6">
            Looking for a product?
          </h2>
        <!-- <p class="text-gray-600 mb-4">Find and connect with businesses that have specific requirements.</p> -->
        <button
          class="w-full text-xs md:text-sm hover:bg-primary/70 text-white font-medium p-2 rounded-lg transition duration-300 border border-white"
          @click="navigateToBuyPostRequirements"
        >
          Post Buy Requirement
        </button>
      </div>
    </div>

    <!-- List Business Section -->
    <div class="bg-white rounded-lg shadow-md mb-4 w-full aspect-[5/2.8]">
      <router-link to="/free/listing" class="relative">
      <img
        src="./../../assets/images/list-business.png"
        alt="Buy Requirement"
        class="w-full h-full object-cover rounded-lg"
      />
      <div class="absolute top-0 p-6">
          <h2 class="text-sm md:text-white mb-6">
            List Your Business
          </h2>
        <!-- <p class="text-gray-600 mb-4">Find and connect with businesses that have specific requirements.</p> -->
        <button
          class="w-full text-xs md:text-sm hover:bg-sky-500/70 text-white font-medium p-2 rounded-lg transition duration-300 border border-white"
          @click="navigateToBuyPostRequirements"
        >
          List Business
        </button>
      </div>
      </router-link>
    </div>
  </div>
  <InquiryModal v-if="showInquiryModal" @close="showInquiryModal = false" />
</template>

<script>
import InquiryModal from '@/components/modals/InquiryModal.vue'
export default {
  name: "BuyPostListBusiness",
  components: {
    InquiryModal
  },data() {
    return {
      showInquiryModal: false,
    }
  },
  methods: {
  },
};
</script>

<style scoped>
.section {
  margin-bottom: 1.5rem;
}

.section:last-child {
  margin-bottom: 0;
}
</style>
