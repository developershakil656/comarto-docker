<template lang="">
  <div>
    <!-- main header -->
    <BottomHeader />

    <div class="bg-gray-50">
      <div class="lg:flex container py-4 md:py-8 mx-auto">
        <!-- Slider: takes remaining space -->
        <div class="lg:w-[70%]">
          <MainSlider />
        </div>

        <!-- Sidebar: takes only its own width -->
        <div class="lg:w-[30%]">
          <BuyPostListBusiness />
        </div>
      </div>
    </div>

    <!-- Category Grid -->
    <CategoryGrid />

    <!-- Suggested Categories based on user's search history -->
    <CategoryWiseSubcategories
      v-for="(group, idx) in suggestedCategories"
      :key="`suggested-${idx}`"
      :title="group.category.name"
      :categorySlug="group.category.slug"
      :items="group.categories"
      :viewAllLink="{
        name: 'category-detail',
        params: { categorySlug: group.category.slug },
      }"
      :odd="(categoryGroups.length + idx) % 2 === 0"
    />

    <!-- Category-wise Subcategories -->
    <CategoryWiseSubcategories
      v-for="(group, idx) in categoryGroups"
      :key="idx"
      :title="`${group.category.name}`"
      :categorySlug="group.category.slug"
      :items="group.categories"
      :viewAllLink="{
        name: 'category-detail',
        params: { categorySlug: group.category.slug },
      }"
      :odd="idx % 2 === 0"
    />

    <!-- Business Registration Encouragement Section -->
    <BusinessRegistrationCTA
      v-if="shouldShowBusinessRegistration"
      @register-click="navigateToBusinessRegistration"
    />

    <div class="block md:hidden"><Footer /></div>
    <!-- Mobile Bottom Navigation -->
    <MobileBottomNavigation />
  </div>
</template>

<script>
import axios from "axios";
import { useSEO } from '@/composables/useSEO';
import BottomHeader from "@/components/header/BottomHeader.vue";
import MainSlider from "@/components/home/MainSlider.vue";
import CategoryGrid from "@/components/home/CategoryGrid.vue";
import CategoryWiseSubcategories from "@/components/home/CategoryWiseSubcategories.vue";
import BusinessRegistrationCTA from "@/components/home/BusinessRegistrationCTA.vue";
import BuyPostListBusiness from "@/components/home/BuyPostListBusiness.vue";
import authMixin from "@/mixins/authMixin.js";
import MobileBottomNavigation from "@/components/common/MobileBottomNavigation.vue";
import Footer from "@/components/common/Footer.vue";

export default {
  mixins: [authMixin],
  components: {
    BottomHeader,
    MainSlider,
    CategoryGrid,
    CategoryWiseSubcategories,
    BusinessRegistrationCTA,
    BuyPostListBusiness,
    MobileBottomNavigation,
    Footer,
  },

  data() {
    return {
      categoryGroups: [],
      suggestedCategories: [],
    };
  },
  computed: {
    shouldShowBusinessRegistration() {
      // Show the section if user is not logged in OR if user is logged in but doesn't have a business
      return (
        !this.isAuthenticated || (this.isAuthenticated && !this.user?.business)
      );
    },
  },
  async mounted() {
    // Set SEO meta tags for Bangladesh B2B directory
    const { setMetaTags } = useSEO();
    setMetaTags(
      'Comarto - Bangladesh B2B Marketplace for Wholesale Suppliers & Manufacturers',
      'Connect with verified wholesale suppliers, manufacturers, and businesses in Bangladesh. Find reliable suppliers for all your business needs on Comarto.',
      null,
      'Comarto, B2B, wholesale, suppliers, manufacturers, Bangladesh, marketplace, trade, commerce, import, export'
    );

    // Handle authentication status messages
    const urlParams = new URLSearchParams(window.location.search);
    const success = urlParams.get("success");
    const error = urlParams.get("error");

    if (success === "login") {
      // Show success message
      this.showNotification("Login successful!", "success");
      // Clean up URL
      this.$router.replace("/");
    } else if (error) {
      // Show error message
      let errorMessage = "Authentication failed";
      switch (error) {
        case "auth_failed":
          errorMessage = "Google authentication failed";
          break;
        case "no_code":
          errorMessage = "No authorization code received";
          break;
        case "callback_failed":
          errorMessage = "Failed to process authentication";
          break;
      }
      this.showNotification(errorMessage, "error");
      // Clean up URL
      this.$router.replace("/");
    }

    // Load top categories for the home sections
    await this.fetchTopCategories();

    // Load suggested categories based on user's search history
    await this.fetchSuggestedCategories();
  },
  methods: {
    async fetchTopCategories() {
      try {
        const { data } = await axios.get("/top/categories");
        // Expecting data to be an array of groups: { category: {name,slug}, categories: [...] }
        if (Array.isArray(data)) {
          this.categoryGroups = data;
        } else if (Array.isArray(data?.data)) {
          // fallback if API wraps in { data: [...] }
          this.categoryGroups = data.data;
        }
      } catch (e) {
        console.error(e);
      }
    },
    showNotification(message, type = "info") {
      // You can implement a toast notification system here
      if (type === "success") {
        alert("✅ " + message);
      } else if (type === "error") {
        alert("❌ " + message);
      } else {
        alert(message);
      }
    },
    async fetchSuggestedCategories() {
      try {
        const response = await this.$store.dispatch("fetchSuggestedCategories");
        // Expecting data to be an array of groups: { category: {name,slug}, categories: [...] }
        if (Array.isArray(response)) {
          this.suggestedCategories = response;
        } else if (Array.isArray(response?.data)) {
          // fallback if API wraps in { data: [...] }
          this.suggestedCategories = response.data;
        }
      } catch (e) {
        console.error("Error fetching suggested categories:", e);
        this.suggestedCategories = [];
      }
    },
    navigateToBusinessRegistration() {
      if (
        !this.isAuthenticated ||
        (this.isAuthenticated && !this.user?.business)
      ) {
        this.$router.push({ name: "free-listing" });
      }
    },
  },
};
</script>

<style lang=""></style>
