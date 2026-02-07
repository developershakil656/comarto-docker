<template>
  <div>
    <div class="lg:pl-4 mt-4 flex justify-center gap-4 lg:block lg:mt-0 overflow-hidden">
    <!-- Buy Post Requirements Section -->
    <div class="bg-white rounded-lg shadow-md mb-4 relative w-full aspect-[5/2.8] cursor-pointer" @click="showInquiryModal = true">
      <OptimizedImage
        :src="buyRequirementImage"
        alt="Buy Requirement"
        class="w-full h-full object-cover rounded-lg"
      />
      <div class="absolute top-0 p-2 sm:p-6">
          <h2 class="text-xs sm:text-sm md:text-xl text-white mb-2 sm:mb-6">
            Looking for a product?
          </h2>
        <!-- <p class="text-gray-600 mb-4">Find and connect with businesses that have specific requirements.</p> -->
        <button
          class="w-full text-xs sm:text-sm hover:bg-primary/70 text-white font-medium p-1 sm:p-2 rounded-lg transition duration-300 border border-white"
        >
          <span class="hidden sm:inline">Post </span>Buy Requirement
        </button>
      </div>
    </div>

    <!-- List Business Section -->
    <div class="bg-white rounded-lg shadow-md mb-4 w-full aspect-[5/2.8]">
      <router-link to="/free/listing" class="relative">
      <OptimizedImage
        :src="listBusinessImage"
        alt="List Business"
        class="w-full h-full object-cover rounded-lg"
      />
      <div class="absolute top-0 p-2 sm:p-6">
          <h2 class="text-xs sm:text-sm md:text-xl text-white mb-2 sm:mb-6">
            List Your Business
          </h2>
        <!-- <p class="text-gray-600 mb-4">Find and connect with businesses that have specific requirements.</p> -->
        <button
          class="w-full text-xs sm:text-sm hover:bg-sky-500/70 text-white font-medium p-1 sm:p-2 rounded-lg transition duration-300 border border-white"
        >
          List Business
        </button>
      </div>
      </router-link>
    </div>
  </div>
  <InquiryModal v-if="showInquiryModal" @close="showInquiryModal = false" />
  </div>
</template>

<script>
import InquiryModal from '@/components/modals/InquiryModal.vue'
import OptimizedImage from '@/components/common/OptimizedImage.vue'

// Import images
import buyRequirementImage from '@/assets/images/buy-requirement.png'
import listBusinessImage from '@/assets/images/list-business.png'

export default {
  name: "BuyPostListBusiness",
  components: {
    InquiryModal,
    OptimizedImage
  },
  data() {
    return {
      showInquiryModal: false,
      buyRequirementImage,
      listBusinessImage
    }
  }
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
