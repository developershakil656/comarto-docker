<template>
  <div>
    <div v-if="pageLoading">
      <SkeletonLoader type="business-profile" />
    </div>
    <div v-else-if="businessData && businessDetails">
      <TopSection
        :businessData="businessData"
        :businessDetails="businessDetails"
      />
      <MainSection
        :businessData="businessData"
        :businessDetails="businessDetails"
      />
    </div>
    <div v-else class="flex justify-center items-center min-h-screen">
      <div class="text-center">
        <h2 class="text-2xl font-semibold text-gray-600 mb-2">
          Business Not Found
        </h2>
        <p class="text-gray-500">
          The business you're looking for doesn't exist or has been removed.
        </p>
      </div>
    </div>
    <MobileBottomNavigation />
  </div>
</template>

<script>
import TopSection from "@/components/business_profile/TopSection.vue";
import MainSection from "@/components/business_profile/MainSection.vue";
import SkeletonLoader from "@/components/SkeletonLoader.vue";
import MobileBottomNavigation from "@/components/common/MobileBottomNavigation.vue";
import { mapActions, mapGetters } from "vuex";
import { useSEO } from '@/composables/useSEO';

export default {
  components: {
    TopSection,
    MainSection,
    SkeletonLoader,
    MobileBottomNavigation,
  },
  props: {
    businessSlug: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      pageLoading: true, // Local flag to control the initial mount
    };
  },
  computed: {
    ...mapGetters(["businessData", "businessDetails"]),
  },
  async mounted() {
    this.pageLoading = true;
    try {
      // First fetch business data
      await this.fetchBusinessData(this.businessSlug);

      // Then fetch business details using the business id from the fetched data
      if (this.businessData?.id) {
        await this.fetchBusinessDetails(this.businessData.id);
      }
      
      // Set SEO meta tags for the business profile
      this.updateBusinessMetaTags();
    } finally {
      this.pageLoading = false; // Only show components once all data is fetched
    }
  },
  methods: {
    ...mapActions(["fetchBusinessData", "fetchBusinessDetails"]),
    
    updateBusinessMetaTags() {
      if (this.businessData) {
        const { setMetaTags } = useSEO();
        const businessName = this.businessData.business_name;
        const locationParts = [];
        if (this.businessData.location?.upazila_name) {
          locationParts.push(this.businessData.location.upazila_name);
        }
        if (this.businessData.location?.district_name) {
          locationParts.push(this.businessData.location.district_name);
        }
        const location = locationParts.join(', ') || 'Bangladesh';
        const description = this.businessData.details?.description || `Connect with ${businessName}, a verified business in ${location}. Find products, contact information, and business details.`;
        
        setMetaTags(
          `${businessName} - Business Profile in ${location} | Comarto Bangladesh B2B Marketplace`,
          description,
          this.businessData.business_profile,
          `${businessName}, suppliers, manufacturers, ${location}, Comarto, B2B, wholesale, trade, business profile`
        );
      }
    },
  },
};
</script>
