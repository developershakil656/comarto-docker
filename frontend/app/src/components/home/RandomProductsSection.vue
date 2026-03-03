<template>
  <div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-xl font-bold">Random Products</h2>
      <router-link
        :to="{ name: 'random-products' }"
        class="text-primary font-medium text-sm hover:underline"
      >
        View All
      </router-link>
    </div>

    <!-- Loading Skeleton -->
    <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
      <div v-for="i in 10" :key="i" class="bg-white rounded-xl border p-4">
        <div class="animate-pulse">
          <div class="bg-gray-200 rounded-lg h-48 mb-4"></div>
          <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
          <div class="h-3 bg-gray-200 rounded w-1/2 mb-3"></div>
          <div class="h-3 bg-gray-200 rounded w-2/3"></div>
        </div>
      </div>
    </div>

    <!-- Products Grid -->
    <div v-else>
      <div
        v-if="limitedResults && limitedResults.length"
        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6"
      >
        <div
          v-for="(item, idx) in limitedResults"
          :key="item.id || idx"
          class="bg-white rounded-xl border p-4 transition-all hover:shadow-lg"
        >
          <router-link
            :to="`/product/${item.slug}`"
            class="group flex flex-col h-full cursor-pointer relative"
          >
            <button
              @click.stop.prevent="toggleFavourite(item)"
              class="absolute top-2 right-2 z-10 p-1 bg-white/80 rounded-full hover:bg-white transition-colors"
            >
              <HeartIcon v-if="!isFavourite(item.id)" class="h-5 w-5 text-gray-600 hover:text-red-500" />
              <HeartIconSolid v-else class="h-5 w-5 text-red-500" />
            </button>

            <div class="mb-3">
              <div class="rounded-lg border bg-green-50 overflow-hidden aspect-square">
                <OptimizedImage
                  :src="item.feature_image || 'https://placehold.co/260x260'"
                  :alt="item.name"
                  class="w-full h-full object-cover transition-transform duration-500 ease-in-out transform group-hover:scale-110"
                />
              </div>
            </div>

            <div class="flex-1 flex flex-col">
              <div class="flex justify-between">
                <h2 class="font-semibold line-clamp-2 mb-2 capitalize">
                  {{ item.name }}
                </h2>
              </div>

              <div class="flex items-baseline font-semibold mb-2">
                <span class="font-poppins sm">৳ {{ item.price }}</span>
                <span v-if="item.product_unit" class="text-xs ml-1 font-normal">
                  / {{ getUnitDisplay(item) }}
                </span>
              </div>

              <div class="flex items-center gap-2 mb-2">
                <BuildingOfficeIcon class="h-4 text-gray-500" />
                <span class="text-sm text-gray-700 truncate">
                  {{ item.business.business_name }}
                </span>
              </div>

              <div class="flex items-center gap-2 text-xs text-gray-600 mb-3">
                <MapPinIcon class="h-4" />
                <span class="truncate">{{ item.business.location }}</span>
              </div>

              <div class="mt-auto flex flex-col gap-2">
                <button
                  class="flex-1 bg-primary text-white px-3 py-2 rounded-md text-sm font-medium hover:bg-primary/85 transition-all"
                  @click.stop.prevent="callBusiness(item.business.number)"
                >
                  <PhoneIcon class="inline-block h-4 mr-1" />
                  Call
                </button>
                <a
                  v-if="item.business.alternate_number"
                  @click.stop
                  :href="`https://wa.me/88${
                    item.business.alternate_number
                  }?text=${encodeURIComponent(
                    'Hello, I am interested in your product! https://comarto.com/product/' + item.slug
                  )}`"
                  target="_blank"
                  class="text-primary px-3 py-2 rounded-md flex justify-center text-center gap-1 text-xs md:text-sm font-medium border-primary border hover:bg-primary/15 transition-all whitespace-nowrap"
                >
                  <img
                    src="@/assets/icons/svgs/WhatsappIcon.svg"
                    class="w-5"
                    alt="WhatsApp"
                  />
                  WhatsApp
                </a>
                <button
                  v-if="!item.business.alternate_number"
                  class="flex-1 bg-blue-600 text-white px-3 py-2 rounded-md text-sm font-medium hover:bg-blue-500 transition-all"
                  @click.stop.prevent="startBestPriceChat(item)"
                  :disabled="isOwnBusiness(item)"
                  :class="{
                    'opacity-50 cursor-not-allowed': isOwnBusiness(item),
                    'hover:bg-blue-500': !isOwnBusiness(item),
                  }"
                >
                  <ReceiptPercentIcon class="inline-block h-4 mr-1" />
                  Best Price
                </button>
              </div>
            </div>
          </router-link>
        </div>
      </div>

      <div v-else class="text-center py-12">
        <div class="text-gray-400 mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.47.881-6.08 2.32M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <h3 class="text-xl font-medium text-gray-900 mb-2">No products found</h3>
        <p class="text-gray-500">Try again later or check back soon</p>
      </div>
    </div>
  </div>
</template>

<script>
import {
  BuildingOfficeIcon,
  MapPinIcon,
  PhoneIcon,
  ReceiptPercentIcon,
  HeartIcon,
} from "@heroicons/vue/24/outline";
import { HeartIcon as HeartIconSolid } from "@heroicons/vue/24/solid";
import OptimizedImage from '@/components/common/OptimizedImage.vue';

export default {
  name: "RandomProductsSection",
  components: {
    BuildingOfficeIcon,
    MapPinIcon,
    PhoneIcon,
    ReceiptPercentIcon,
    HeartIcon,
    HeartIconSolid,
    OptimizedImage,
  },
  computed: {
    searchResults() {
      return this.$store.getters.searchResults;
    },
    loading() {
      return this.$store.getters.loading;
    },
    favouriteProducts() {
      return this.$store.getters.favouriteProducts;
    },
    limitedResults() {
      return this.searchResults ? this.searchResults.slice(0, 10) : [];
    },
    getUnitDisplay() {
      return (item) => {
        const unit = item.product_unit || {};
        if (item.unit_quantity && item.unit_quantity > 1) {
          return `${item.unit_quantity} ${unit.plural_form}`;
        }
        return unit.short_form || unit.full_form || "Unit";
      };
    },
  },
  methods: {
    callBusiness(number) {
      window.location.href = `tel:${number}`;
    },
    isFavourite(productId) {
      if (!this.favouriteProducts || this.favouriteProducts.length === 0) {
        return false;
      }
      return this.favouriteProducts.some(product => product.id === productId);
    },
    async toggleFavourite(item) {
      try {
        if (this.isFavourite(item.id)) {
          await this.$store.dispatch("removeFromFavourites", item.id);
        } else {
          await this.$store.dispatch("addToFavourites", item.id);
        }
      } catch (error) {
        console.error("Failed to toggle favourite:", error);
      }
    },
    async startBestPriceChat(item) {
      try {
        const productName = item?.name || "Product";
        const userId = this.$store.getters.user?.id || null;
        const businessId = item?.business?.id;
        if (!businessId) return;
        const res = await this.$store.dispatch("startConversation", {
          business_id: businessId,
          user_id: userId,
        });
        const conversation = res?.data;
        if (conversation?.id) {
          await this.$store.dispatch(
            "fetchConversationMessages",
            conversation.id
          );
          const productSlug = item?.slug;
          const baseUrl = window.location.origin;
          const productUrl = `${baseUrl}/product/${productSlug}`;
          const draftMessage = `Hi! I'm interested in getting the best price for this product: ${productUrl}\n\nProduct: ${productName}\n\nCould you please provide me with your best offer?`;
          localStorage.setItem(
            `conversation_draft_${conversation.id}`,
            draftMessage
          );
          this.$router.push({
            name: "conversation-messages",
            params: { id: conversation?.id },
          });
        }
      } catch (e) {
        console.error("Failed to start best-price chat:", e);
      }
    },
    isOwnBusiness(item) {
      return item?.business?.id === this.$store.getters.user?.business?.id;
    },
    async fetchRandomProducts() {
      try {
        const location = this.$store.getters.selectedLocation || "";
        await this.$store.dispatch("fetchRandomProducts", { location });
      } catch (error) {
        console.error("Error fetching random products:", error);
      }
    },
  },
  mounted() {
    this.fetchRandomProducts();
  },
};
</script>

<style scoped>
/* reuse existing styles if needed */
</style>
