<template>
  <!-- Main Content -->
  <main class="flex-1">
    <!-- Loading State -->
    <div v-if="loading" class="space-y-6">
      <SkeletonLoader type="product-card" :count="5" />
      <SkeletonLoader type="product-card" />
      <SkeletonLoader type="product-card" />
    </div>

    <!-- Product Card List -->
    <div v-else class="space-y-6">
      <!-- Total products found -->
      <div class="flex justify-between items-center">
        <div>
          <h1 v-if="keyword">
            Results found for "{{ keyword }} in
            {{ this.$store.getters.selectedLocation || "All Bangladesh" }}"
          </h1>
          <h1 v-else-if="formattedCategorySlugs">
            Results found for "{{ formattedCategorySlugs }} in
            {{ this.$store.getters.selectedLocation || "All Bangladesh" }}"
          </h1>
          <div class="text-sm font-semibold text-gray-600">
            {{ totalProducts }}
            {{ isSuppliersView ? "suppliers" : "products" }} found
          </div>
        </div>
        <button
          @click="$emit('toggle-sidebar')"
          class="md:hidden flex items-center gap-1 text-sm font-semibold text-gray-700 bg-gray-100 px-3 py-1.5 rounded-lg"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 12.414V17a1 1 0 01-1.447.894l-2-1A1 1 0 018 16.172V12.414L3.293 6.707A1 1 0 013 6V3z"
              clip-rule="evenodd"
            />
          </svg>
          Filter
        </button>
      </div>
      <template v-if="searchResults && searchResults.length">
        <div
          v-for="(item, idx) in searchResults"
          :key="item.id || idx"
          class="bg-white rounded-xl border p-4 transition-all hover:shadow-lg"
        >
          <router-link
            :to="`/product/${item.slug}`"
            class="group flex gap-4 items-start cursor-pointer"
          >
            <div class="max-h-52 max-w-52 product-image">
              <div
                class="rounded-lg border content-center bg-green-50 max-h-44 max-w-44 md:max-h-52 md:max-w-52 product-image overflow-hidden aspect-square"
              >
                <OptimizedImage
                  :src="
                    (isSuppliersView
                      ? item.business.business_profile
                      : item.feature_image) || 'https://placehold.co/260x200'
                  "
                  :alt="
                    isSuppliersView ? item.business.business_name : item.name
                  "
                  class="w-full h-full object-cover transition-transform duration-500 ease-in-out transform group-hover:scale-110"
                />
              </div>
              <VerifiedBadge
                :accountVerified="item.business.account_verification"
                class="md:hidden mt-2"
              />
            </div>
            <div class="flex-1 flex flex-col">
              <div class="flex-1 flex flex-col">
                <template v-if="!isSuppliersView">
                  <div class="flex justify-between">
                    <h2
                      class="text-sm md:text-base font-semibold line-clamp-2 capitalize"
                    >
                      {{ item.name }}
                    </h2>
                    <button
                      class="px-1 text-red-500 hover:text-red-700 transition-colors"
                      @click.stop.prevent="toggleFavourite(item)"
                    >
                      <HeartIcon v-if="!isFavourite(item.id)" class="h-7" />
                      <HeartIconSolid v-else class="h-7" />
                    </button>
                  </div>
                  <!-- pricing -->
                  <div class="flex items-baseline font-semibold">
                    <span class="text-xs md:text-base font-poppins"
                      >৳ {{ item.price }}</span
                    >
                    <span
                      v-if="item.product_unit"
                      class="text-xs md:text-sm ml-1 font-normal"
                      >/ {{ getUnitDisplay(item) }}</span
                    >
                  </div>
                </template>
                <!-- store name -->
                <div class="flex items-center gap-2 my-1">
                  <BuildingOfficeIcon class="inline-block h-5" />
                  <router-link
                    :to="`/${item.business.slug}`"
                    @click.stop
                    class="font-medium text-sm md:text-base text-gray-900 hover:text-primary cursor-pointer transition-colors capitalize"
                    :class="{ 'text-xl': isSuppliersView }"
                  >
                    {{ item.business.business_name }}
                  </router-link>
                  <VerifiedBadge
                    :accountVerified="item.business.account_verification"
                    class="hidden md:block"
                  />
                </div>
                <!-- rating and business info -->
                <div class="md:flex items-center gap-2 my-1">
                  <div class="flex items-center gap-2">
                    <span
                      v-if="item.business.rating"
                      class="bg-green-700 text-sm md:text-base font-semibold text-white px-1 rounded-md"
                      >{{ item.business.rating.rate }}</span
                    >
                    <div v-if="item.business.rating">
                      <StarRating
                        :rating="parseFloat(item.business.rating.rate)"
                        iconSize="h-5"
                      />
                    </div>
                    <span
                      v-if="item.business.rating"
                      class="text-xs text-gray-600"
                      >({{ item.business.rating.count }})</span
                    >
                  </div>
                  <span
                    v-if="item.business.in_business"
                    class="text-xs md:text-sm ml-2"
                    >{{ item.business.in_business }} Years in business</span
                  >
                </div>
                <!-- location and business types -->
                <div
                  class="md:flex items-center gap-2 mt-1 text-xs text-gray-800"
                >
                  <div
                    class="line-clamp-1 flex content-center font-medium mb-2 md:mb-0"
                  >
                    <MapPinIcon class="inline-block h-5" />
                    <span class="mt-[1px]">{{ item.business.location }}</span>
                  </div>
                  <span class="hidden md:block mx-1">•</span>
                  <div class="flex flex-wrap gap-1 capitalize">
                    <span
                      v-for="(tag, tIdx) in item.business.business_type || []"
                      :key="tIdx"
                      class="bg-primary/10 font-semibold py-0.5 rounded px-2"
                      >{{ tag }}</span
                    >
                  </div>
                </div>
              </div>
              <!-- action buttons (desktop) -->
              <div class="hidden md:flex flex-wrap gap-2 mt-3">
                <button
                  class="bg-primary text-white px-4 py-2 rounded-md flex items-center gap-1 text-xs md:text-sm font-medium hover:bg-primary/85 transition-all"
                  @click.stop.prevent="callBusiness(item.business.number)"
                >
                  <PhoneIcon class="inline-block h-4" />{{
                    item.business.number || "N/A"
                  }}
                </button>
                <a
                  v-if="item.business.alternate_number"
                  @click.stop
                  :href="`https://wa.me/${
                    item.business.alternate_number
                  }?text=${encodeURIComponent(
                    'Hello, I am interested in your product!'
                  )}`"
                  target="_blank"
                  class="text-primary px-4 py-1 rounded-md flex items-center gap-1 text-xs md:text-sm font-medium border-primary border hover:bg-primary/15 transition-all"
                >
                  <img
                    src="@/assets/icons/svgs/WhatsappIcon.svg"
                    class="w-7"
                    alt="WhatsApp"
                  />
                  WhatsApp
                </a>
                <button
                  class="bg-blue-600 text-white px-4 py-2 rounded-md flex items-center gap-1 text-xs md:text-sm font-medium hover:bg-blue-500 transition-all focus:outline-none"
                  @click.stop.prevent="startBestPriceChat(item)"
                  :disabled="isOwnBusiness(item)"
                  :class="{
                    'opacity-50 cursor-not-allowed': isOwnBusiness(item),
                    'hover:bg-blue-500': !isOwnBusiness(item),
                  }"
                >
                  <ReceiptPercentIcon class="inline-block h-4" />Get Best Price
                </button>
              </div>
            </div>
          </router-link>
          <!-- action buttons (mobile) -->
          <div class="flex md:hidden gap-2 mt-3 overflow-x-auto scrollbar-hide">
            <div class="flex gap-2 min-w-max">
              <button
                class="bg-primary text-white px-2 py-1 rounded-md flex items-center gap-1 text-xs md:text-sm font-medium hover:bg-primary/85 transition-all whitespace-nowrap"
                @click.stop.prevent="callBusiness(item.business.number)"
              >
                <PhoneIcon class="inline-block h-3" />{{
                  item.business.number || "N/A"
                }}
              </button>
              <a
                v-if="item.business.alternate_number"
                @click.stop
                :href="`https://wa.me/${
                  item.business.alternate_number
                }?text=${encodeURIComponent(
                  'Hello, I am interested in your product!'
                )}`"
                target="_blank"
                class="text-primary px-2 py-0.5 rounded-md flex items-center gap-1 text-xs md:text-sm font-medium border-primary border hover:bg-primary/15 transition-all whitespace-nowrap"
              >
                <img
                  src="@/assets/icons/svgs/WhatsappIcon.svg"
                  class="w-5"
                  alt="WhatsApp"
                />
                WhatsApp
              </a>
              <button
                class="bg-blue-600 text-white px-2 py-1 rounded-md flex items-center gap-1 text-xs md:text-sm font-medium hover:bg-blue-500 transition-all whitespace-nowrap"
                @click.stop.prevent="startBestPriceChat(item)"
                :disabled="isOwnBusiness(item)"
                :class="{
                  'opacity-50 cursor-not-allowed': isOwnBusiness(item),
                  'hover:bg-blue-500': !isOwnBusiness(item),
                }"
              >
                <ReceiptPercentIcon class="inline-block h-3" />
                Get Best Price
              </button>
            </div>
          </div>
        </div>
      </template>
      <div v-else class="text-center text-gray-500 py-10 text-lg">
        No results found.
      </div>
      <!-- Load More Sentinel -->
      <div ref="loadMoreRef" class="h-6"></div>
      <!-- Loading more indicator -->
      <div v-if="loadingMore" class="text-center text-gray-500 py-4">
        Loading more...
      </div>
      <div
        v-else-if="!canLoadMore && searchResults && searchResults.length"
        class="text-center text-gray-400 py-4 text-sm"
      >
        No more results
      </div>
    </div>
  </main>
</template>

<script>
import { push } from "notivue";
import {
  BuildingOfficeIcon,
  MapPinIcon,
  HeartIcon,
} from "@heroicons/vue/24/outline";
import { PhoneIcon, ReceiptPercentIcon } from "@heroicons/vue/24/solid";
import { HeartIcon as HeartIconSolid } from "@heroicons/vue/24/solid";
import StarRating from "@/components/StarRating.vue";
import SkeletonLoader from "@/components/SkeletonLoader.vue";
import VerifiedBadge from "@/components/common/VerifiedBadge.vue";
import OptimizedImage from '@/components/common/OptimizedImage.vue';
export default {
  components: {
    BuildingOfficeIcon,
    MapPinIcon,
    StarRating,
    PhoneIcon,
    ReceiptPercentIcon,
    HeartIcon,
    HeartIconSolid,
    SkeletonLoader,
    VerifiedBadge,
    OptimizedImage
  },
  emits: ["toggle-sidebar"],
  styles: {
    ".scrollbar-hide": {
      "-ms-overflow-style": "none",
      "scrollbar-width": "none",
    },
    ".scrollbar-hide::-webkit-scrollbar": {
      display: "none",
    },
  },
  data() {
    return {
      observer: null,
      categorySlugs: "",
    };
  },
  props: {
    keyword: {
      type: String,
      default: "",
    },
  },
  computed: {
    searchResults() {
      return this.$store.getters.searchResults;
    },
    loading() {
      return this.$store.getters.loading;
    },
    totalProducts() {
      return this.$store.getters.searchMeta?.total || 0;
    },
    loadingMore() {
      return this.$store.getters.loadingMore;
    },
    canLoadMore() {
      return this.$store.getters.canLoadMore;
    },
    isSuppliersView() {
      return this.$route?.query?.suppliers === "true";
    },
    favouriteProducts() {
      return this.$store.getters.favouriteProducts;
    },
    // Standardized unit display format
    getUnitDisplay() {
      return (item) => {
        const unit = item.product_unit || {};
        if (item.unit_quantity && item.unit_quantity > 1) {
          return `${item.unit_quantity} ${unit.plural_form}`;
        }
        return unit.short_form || unit.full_form || "Unit";
      };
    },
    //format slug to show in search result
    formattedCategorySlugs() {
      if (this.categorySlugs && this.categorySlugs.length > 0) {
        // Split the category slugs by commas, trim spaces, and handle the "and" before the last element
        const categories = this.categorySlugs
          .split(",")
          .map((item) => item.trim());

        if (categories.length === 1) {
          return categories[0]; // No need for "and" if there's only one item
        }

        // Add "and" before the last item and join with commas
        const lastCategory = categories.pop(); // Remove last item
        return categories.join(", ") + " and " + lastCategory; // Add "and" before last category
      }
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
      const isFav = this.favouriteProducts.some(
        (product) => product.id === productId
      );
      return isFav;
    },

    async toggleFavourite(item) {
      try {
        if (this.isFavourite(item.id)) {
          // Remove from favourites
          await this.$store.dispatch("removeFromFavourites", item.id);
        } else {
          // Add to favourites
          await this.$store.dispatch("addToFavourites", item.id);
        }
      } catch (error) {
        console.error("Failed to toggle favourite:", error);
        push.error("Failed to update favourites");
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
          product_name: productName,
          user_id: userId,
        });
        const conversation = res?.data;
        if (conversation?.id) {
          await this.$store.dispatch(
            "fetchConversationMessages",
            conversation.id
          );

          // Create a draft message with product details
          const productSlug = item?.slug;
          const productId = item?.id;
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
    setupInfiniteScroll() {
      if (this.observer) {
        this.observer.disconnect();
      }
      const options = { root: null, rootMargin: "200px", threshold: 0 };
      this.observer = new IntersectionObserver(async (entries) => {
        const entry = entries[0];
        if (
          entry &&
          entry.isIntersecting &&
          this.canLoadMore &&
          !this.loadingMore &&
          !this.loading
        ) {
          await this.$store.dispatch("loadMoreSearch");
        }
      }, options);
      if (this.$refs.loadMoreRef) {
        this.observer.observe(this.$refs.loadMoreRef);
      }
    },
  },

  mounted() {
    this.$nextTick(() => {
      this.setupInfiniteScroll();
    });
    this.categorySlugs = this.$route.query.category_slugs;
  },
  watch: {
    loading(newVal) {
      if (!newVal) {
        this.$nextTick(() => this.setupInfiniteScroll());
      }
    },
    searchResults() {
      this.$nextTick(() => this.setupInfiniteScroll());
    },
  },
  beforeUnmount() {
    if (this.observer) {
      this.observer.disconnect();
      this.observer = null;
    }
  },
};
</script>
