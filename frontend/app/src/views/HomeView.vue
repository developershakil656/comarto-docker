<template>
  <div>
    <BottomHeader />

    <div class="bg-gray-50">
      <div class="lg:flex container py-4 md:py-8 mx-auto">
        <div class="lg:w-[70%]">
          <MainSlider />
        </div>

        <div class="lg:w-[30%]">
          <BuyPostListBusiness />
        </div>
      </div>
    </div>

    <div
      v-if="showInstallButton"
      class="block md:hidden bg-primary text-white text-center py-2.5 my-3 mx-4 rounded"
    >
      <a
        href="https://comarto.com/app/download"
        class="font-medium flex items-center justify-center w-full"
      >
        <ArrowDownTrayIcon class="h-5 w-5 mr-2" />
        Download Comarto App
      </a>
    </div>

    <CategoryGrid />

    <CategoryWiseSubcategories
      v-for="(group, idx) in suggestedCategories"
      :key="`suggested-${idx}`"
      :title="group.category.name"
      :categorySlug="group.category.slug"
      :items="group.categories"
      :viewAllLink="{
        name: 'category-detail',
        params: { categoryPath: group.category.slug },
      }"
      :odd="(categoryGroups.length + idx) % 2 === 0"
    />

    <CategoryWiseSubcategories
      v-for="(group, idx) in categoryGroups"
      :key="idx"
      :title="`${group.category.name}`"
      :categorySlug="group.category.slug"
      :items="group.categories"
      :viewAllLink="{
        name: 'category-detail',
        params: { categoryPath: group.category.slug },
      }"
      :odd="idx % 2 === 0"
    />

    <BusinessRegistrationCTA
      v-if="shouldShowBusinessRegistration"
      @register-click="navigateToBusinessRegistration"
    />

    <div class="block md:hidden"><Footer /></div>
    <MobileBottomNavigation />
  </div>
</template>

<script>
import axios from "axios";
import { useSEO } from "@/composables/useSEO";
import BottomHeader from "@/components/header/BottomHeader.vue";
import MainSlider from "@/components/home/MainSlider.vue";
import CategoryGrid from "@/components/home/CategoryGrid.vue";
import CategoryWiseSubcategories from "@/components/home/CategoryWiseSubcategories.vue";
import BusinessRegistrationCTA from "@/components/home/BusinessRegistrationCTA.vue";
import BuyPostListBusiness from "@/components/home/BuyPostListBusiness.vue";
import authMixin from "@/mixins/authMixin.js";
import MobileBottomNavigation from "@/components/common/MobileBottomNavigation.vue";
import Footer from "@/components/common/Footer.vue";
import { ArrowDownTrayIcon } from "@heroicons/vue/24/solid";

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
    ArrowDownTrayIcon,
  },

  data() {
    return {
      categoryGroups: [],
      suggestedCategories: [],
      showInstallButton: false
    };
  },

  computed: {
    shouldShowBusinessRegistration() {
      return (
        !this.isAuthenticated || (this.isAuthenticated && !this.user?.business)
      );
    },
  },

  mounted() {
    this.checkInstallState();

    // Android/Desktop install event
    window.addEventListener("appinstalled", () => {
      localStorage.setItem("pwa_installed", "1");
      this.showInstallButton = false;
    });

    // Detect display-mode change (Chrome desktop/mobile)
    window.matchMedia("(display-mode: standalone)").addEventListener("change", (e) => {
      if (e.matches) {
        localStorage.setItem("pwa_installed", "1");
        this.showInstallButton = false;
      }
    });

    // Also hide after successful install
    window.addEventListener('appinstalled', () => {
      this.showInstallButton = false;
    });
    // SEO and Auth logic
    const { setMetaTags } = useSEO();
    setMetaTags(
      "Comarto - Bangladesh B2B Marketplace for Wholesale Suppliers & Manufacturers",
      "Connect with verified wholesale suppliers, manufacturers, and businesses in Bangladesh.",
      null,
      "Comarto, B2B, wholesale, suppliers, manufacturers, Bangladesh",
    );

    this.handleAuthParams();
    this.fetchTopCategories();
    this.fetchSuggestedCategories();
  },

  methods: {
    checkInstallState() {
      const isStandalone =
        window.matchMedia("(display-mode: standalone)").matches ||
        window.navigator.standalone === true; // iOS

      const savedState = localStorage.getItem("pwa_installed") === "1";

      // Final decision
      if (isStandalone || savedState) {
        this.showInstallButton = false;
      } else {
        this.showInstallButton = true;
      }
    },

    async fetchTopCategories() {
      try {
        const { data } = await axios.get("/top/categories");
        this.categoryGroups = Array.isArray(data) ? data : data?.data || [];
      } catch (e) {
        console.error(e);
      }
    },

    async fetchSuggestedCategories() {
      try {
        const response = await this.$store.dispatch("fetchSuggestedCategories");
        this.suggestedCategories = Array.isArray(response)
          ? response
          : response?.data || [];
      } catch (e) {
        console.error("Error fetching suggested categories:", e);
      }
    },

    handleAuthParams() {
      const urlParams = new URLSearchParams(window.location.search);
      const success = urlParams.get("success");
      if (success === "login") {
        this.showNotification("Login successful!", "success");
        this.$router.replace("/");
      }
    },

    showNotification(message, type = "info") {
      alert((type === "success" ? "✅ " : "❌ ") + message);
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
