<template lang="">
  <h2 class="text-xl md:text-3xl font-bold text-gray-800 mb-6 text-center">
    Favourites
  </h2>

  <!-- Loading State - Only show on initial load -->
  <div
    v-if="favouritesLoading && favouriteProducts.length === 0"
    class="space-y-4 sm:space-y-6 mt-2"
  >
    <div
      v-for="n in 3"
      :key="n"
      class="group flex flex-col sm:flex-row bg-white rounded-xl border p-3 sm:p-4 gap-3 sm:gap-4 items-start"
    >
      <div class="w-full sm:w-40 md:w-52 h-40 sm:h-40 md:h-52 rounded-lg border bg-gray-200 animate-pulse"></div>
      <div class="flex-1 space-y-2 sm:space-y-4 w-full">
        <div class="h-5 sm:h-6 bg-gray-200 rounded animate-pulse"></div>
        <div class="h-3 sm:h-4 bg-gray-200 rounded w-1/3 animate-pulse"></div>
        <div class="h-3 sm:h-4 bg-gray-200 rounded w-1/2 animate-pulse"></div>
        <div class="h-3 sm:h-4 bg-gray-200 rounded w-2/3 animate-pulse"></div>
      </div>
    </div>
  </div>

  <!-- Empty State -->
  <div v-else-if="favouriteProducts.length === 0" class="text-center py-12">
    <div class="text-gray-500 text-lg mb-4">No favourite products yet</div>
    <p class="text-gray-400">
      Start adding products to your favourites to see them here.
    </p>
  </div>

  <!-- Favourite Products List -->
  <div v-else class="space-y-6 mt-2">
    <div
      v-for="product in favouriteProducts"
      :key="product.id"
      class="bg-white rounded-xl border p-4 transition-all hover:shadow-lg"
    >
      <router-link
        :to="`/product/${product.slug}`"
        class="group flex gap-4 items-start cursor-pointer"
      >
    <div class="max-h-52 max-w-52 product-image">
      <div
          class="rounded-lg border content-center bg-green-50 max-h-44 max-w-44 md:max-h-52 md:max-w-52 product-image overflow-hidden"
        >
          <OptimizedImage
            :src="product.feature_image || 'https://placehold.co/200x200'"
            :alt="product.name"
            class="w-full h-full object-cover transition-transform duration-500 ease-in-out transform group-hover:scale-110"
          />
        </div>
        <VerifiedBadge :accountVerified="product.business.account_verification" class="md:hidden mt-2"/>
      </div>
        <div class="flex-1 flex flex-col">
          <div class="flex justify-between">
            <h2
              class="text-sm md:text-lg font-semibold line-clamp-2 capitalize"
            >
              {{ product.name }}
            </h2>
            <button
              class="px-1 text-red-500 hover:text-red-700 transition-colors"
              @click.stop.prevent="handleRemoveFromFavourites(product.id)"
            >
              <HeartIcon v-if="removingProductId === product.id" class="h-7" />
              <HeartIconSolid v-else class="h-7" />
            </button>
          </div>

          <!-- pricing -->
          <div class="flex items-baseline font-semibold">
            <span class="text-xs md:text-base font-poppins"
              >৳ {{ product.price }}</span
            >
            <span
              v-if="product.product_unit"
              class="text-xs md:text-sm ml-1 font-normal"
              >/ {{ getUnitDisplay(product) }}</span
            >
          </div>

          <!-- store name -->
          <div class="flex items-center gap-2 my-1">
            <BuildingOfficeIcon class="inline-block h-5" />
            <router-link
              :to="`/${product.business.slug}`"
              class="font-medium text-sm md:text-base text-gray-900 hover:text-primary cursor-pointer transition-colors capitalize"
              @click.stop
            >
              {{ product.business.business_name }}
            </router-link>
            <VerifiedBadge :accountVerified="product.business.account_verification" class="hidden md:block"/>
          </div>

          <!-- rating and business info -->
          <div class="md:flex items-center gap-2 my-1">
            <div class="flex items-center gap-2">
              <span
                v-if="product.business.rating"
                class="bg-green-700 text-sm md:text-base font-semibold text-white px-1 rounded-md"
              >
                {{ product.business.rating.rate }}
              </span>
              <div v-if="product.business.rating">
                <StarRating
                  :rating="parseFloat(product.business.rating.rate)"
                  iconSize="h-5"
                />
              </div>
              <span
                v-if="product.business.rating"
                class="text-xs text-gray-600"
              >
                ({{ product.business.rating.count }})
              </span>
            </div>
            <span
              v-if="product.business.in_business"
              class="text-xs md:text-sm ml-2"
            >
              {{ product.business.in_business }} Years in business
            </span>
          </div>

          <!-- location and business types -->
          <div class="md:flex items-center gap-2 mt-1 text-xs text-gray-800">
            <div
              class="line-clamp-1 flex content-center font-medium mb-2 md:mb-0"
            >
              <MapPinIcon class="inline-block h-5" />
              <span class="mt-[1px]">{{ product.business.location }}</span>
            </div>

            <span class="hidden md:block mx-1">•</span>
            <div class="flex flex-wrap gap-1 capitalize">
              <span
                v-for="(type, index) in product.business.business_type"
                :key="index"
                class="bg-primary/10 font-semibold py-0.5 rounded px-2"
              >
                {{ type }}
              </span>
            </div>
          </div>

          <!-- action buttons -->
          <div class="hidden md:flex flex-wrap gap-2 mt-3">
            <button
              class="bg-primary text-white px-4 py-2 rounded-md flex items-center gap-1 text-xs md:text-sm font-medium hover:bg-primary/85 transition-all"
              @click.stop.prevent="callBusiness(product.business.number)"
            >
              <PhoneIcon class="inline-block h-4" />
              {{ product.business.number }}
            </button>
            <button
              class="text-primary px-4 py-1 rounded-md flex items-center gap-1 text-xs md:text-sm font-medium border-primary border hover:bg-primary/15 transition-all"
              @click.stop.prevent="whatsappBusiness(product.business.alternate_number)"
              v-if="product.business.alternate_number"
            >
              <img
                src="@/assets/icons/svgs/WhatsappIcon.svg"
                class="w-7"
                alt="WhatsApp"
              />
              WhatsApp
            </button>
            <button
              class="bg-blue-600 text-white px-4 py-2 rounded-md flex items-center gap-1 text-xs md:text-sm font-medium hover:bg-blue-500 transition-all"
              @click.stop.prevent="getBestPrice(product)"
              :disabled="isOwnBusiness(product)"
              :class="{ 'opacity-50 cursor-not-allowed': isOwnBusiness(product), 'hover:bg-blue-500': !isOwnBusiness(product) }"
            >
              <ReceiptPercentIcon class="inline-block h-4" />
              Get Best Price
            </button>
          </div>
        </div>
      </router-link>
      <!-- action buttons -->
      <div class="relative md:hidden mt-3">
          <button v-if="showLeftActionArrow[product.id]" @click="scrollActions('left', product.id)"
              class="absolute left-0 top-1/2 -translate-y-1/2 bg-white/80 backdrop-blur-sm rounded-full p-1 shadow-md z-10">
              <ChevronLeftIcon class="h-5 w-5 text-gray-700" />
          </button>
          <div :ref="el => actionContainers[product.id] = el" @scroll="checkActionScroll($event, product.id)"
              class="flex gap-2 overflow-x-auto scrollbar-hide">
              <div class="flex gap-2 min-w-max">
          <button
            class="bg-primary text-white px-2 py-1 rounded-md flex items-center gap-1 text-xs md:text-sm font-medium hover:bg-primary/85 transition-all whitespace-nowrap"
            @click.stop.prevent="callBusiness(product.business.number)"
          >
            <PhoneIcon class="inline-block h-3" />
            {{ product.business.number }}
          </button>
          <button
            class="text-primary px-2 py-0.5 rounded-md flex items-center gap-1 text-xs md:text-sm font-medium border-primary border hover:bg-primary/15 transition-all whitespace-nowrap"
            @click.stop.prevent="whatsappBusiness(product.business.number)"
          >
            <img
              src="@/assets/icons/svgs/WhatsappIcon.svg"
              class="w-5"
              alt="WhatsApp"
            />
            WhatsApp
          </button>
          <button
            class="bg-blue-600 text-white px-2 py-1 rounded-md flex items-center gap-1 text-xs md:text-sm font-medium hover:bg-blue-500 transition-all whitespace-nowrap"
            @click.stop.prevent="getBestPrice(product)"
            :disabled="isOwnBusiness(product)"
            :class="{ 'opacity-50 cursor-not-allowed': isOwnBusiness(product), 'hover:bg-blue-500': !isOwnBusiness(product) }"
          >
            <ReceiptPercentIcon class="inline-block h-3" />
            Get Best Price
          </button>
        </div>
          </div>
          <button v-if="showRightActionArrow[product.id]" @click="scrollActions('right', product.id)"
              class="absolute right-0 top-1/2 -translate-y-1/2 bg-white/80 backdrop-blur-sm rounded-full p-1 shadow-md z-10">
              <ChevronRightIcon class="h-5 w-5 text-gray-700" />
          </button>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import StarRating from "@/components/StarRating.vue";
import VerifiedBadge from "@/components/common/VerifiedBadge.vue";
import OptimizedImage from '@/components/common/OptimizedImage.vue';
import {
  HeartIcon,
  BuildingOfficeIcon,
  PhoneIcon,
  MapPinIcon,
  ReceiptPercentIcon,
  ChevronLeftIcon,
  ChevronRightIcon,
} from "@heroicons/vue/24/outline";
import { HeartIcon as HeartIconSolid } from "@heroicons/vue/24/solid";

// NOTE: For shallowRef optimization, uncomment below:
// import { useVuexFavourites } from '@/composables/useVuexShallowRef'

export default {
  components: {
    HeartIcon,
    HeartIconSolid,
    BuildingOfficeIcon,
    PhoneIcon,
    ReceiptPercentIcon,
    MapPinIcon,
    StarRating,
    OptimizedImage,
    VerifiedBadge,
    ChevronLeftIcon,
    ChevronRightIcon,
  },
  data() {
    return {
      removingProductId: null,
      showLeftActionArrow: {},
      showRightActionArrow: {},
      actionContainers: {},
    };
  },
  computed: {
    ...mapGetters(["favouriteProducts", "favouritesLoading"]),
  },
  watch: {
    favouriteProducts() {
      this.$nextTick(this.checkAllScrolls);
    },
  },
  methods: {
    ...mapActions(["removeFromFavourites"]),

    async handleRemoveFromFavourites(productId) {
      try {
        this.removingProductId = productId;
        await this.removeFromFavourites(productId);
        // You could add a success toast notification here
      } catch (error) {
        console.error("Failed to remove from favourites:", error);
        // You could add an error toast notification here
      } finally {
        this.removingProductId = null;
      }
    },

    callBusiness(number) {
      window.location.href = `tel:${number}`;
    },

    whatsappBusiness(number) {
      const message = `Hi, I'm interested in your products. Can you provide more information?`;
      const whatsappUrl = `https://wa.me/${number}?text=${encodeURIComponent(
        message
      )}`;
      window.open(whatsappUrl, "_blank");
    },

    async getBestPrice(product) {
      try {
        const productName = product?.name || "Product";
        const userId = this.$store.getters.user?.id || null;
        const businessId = product?.business?.id;
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

          // Automatically send a message with the product URL
          const productSlug = product?.slug;
          const productId = product?.id;
          const baseUrl = window.location.origin;
          const productUrl = `${baseUrl}/product/${productSlug}`;

          const message = `Hi! I'm interested in getting the best price for this product: ${productUrl}`;

          await this.$store.dispatch("sendConversationMessage", {
            conversationId: conversation.id,
            message: message,
          });
        }
        this.$router.push({ name: "conversations" });
      } catch (e) {
        console.error("Failed to start best-price chat:", e);
      }
    },

    getUnitDisplay(product) {
      const unit = product.product_unit || {};
      if (product.unit_quantity && product.unit_quantity > 1) {
        return `${product.unit_quantity} ${unit.plural_form}`;
      }
      return unit.short_form || unit.full_form || "Unit";
    },

    isOwnBusiness(product) {
      return product?.business?.id === this.$store.getters.user?.business?.id;
    },

    checkActionScroll(event, productId) {
      const container = event.target;
      if (container) {
        const { scrollLeft, scrollWidth, clientWidth } = container;
        const buffer = 2;
        this.showLeftActionArrow[productId] = scrollLeft > buffer;
        this.showRightActionArrow[productId] =
          scrollLeft < scrollWidth - clientWidth - buffer;
      }
    },

    scrollActions(direction, productId) {
      const container = this.actionContainers[productId];
      if (container) {
        const scrollAmount = container.clientWidth * 0.7;
        container.scrollBy({
          left: direction === "left" ? -scrollAmount : scrollAmount,
          behavior: "smooth",
        });
      }
    },

    checkAllScrolls() {
      for (const productId in this.actionContainers) {
        const container = this.actionContainers[productId];
        if (container) {
          const { scrollLeft, scrollWidth, clientWidth } = container;
          const buffer = 2;
          this.showLeftActionArrow[productId] = scrollLeft > buffer;
          this.showRightActionArrow[productId] = scrollWidth > clientWidth;
        }
      }
    },
  },

  mounted() {
    window.addEventListener("resize", this.checkAllScrolls);
  },
  beforeUnmount() {
    window.removeEventListener("resize", this.checkAllScrolls);
  },
};
</script>

<style>
.product-image {
  aspect-ratio: 1 / 1;
  width: 100%;
  height: auto;
}

.scrollbar-hide {
  -ms-overflow-style: none;
  /* Internet Explorer 10+ */
  scrollbar-width: none;
  /* Firefox */
}

.scrollbar-hide::-webkit-scrollbar {
  display: none;
  /* Safari and Chrome */
}
</style>
