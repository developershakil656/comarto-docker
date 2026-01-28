<template>
  <section class="my-6" :class="odd ? 'bg-gray-50' : 'bg-white'">
    <div class="container mx-auto px-4 py-4">
      <!-- Skeleton loader when loading -->
      <SkeletonLoader
        v-if="loading"
        type="category-wise-subcategories"
        :count="6"
      />

      <!-- Actual content when loaded -->
      <div v-else>
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-sm md:text-lg font-semibold capitalize">
            {{ title }}
          </h2>
          <router-link
            v-if="viewAllLink"
            :to="viewAllLink"
            class="text-blue-600 text-xs md:text-sm ml-2"
            >View all</router-link
          >
        </div>

        <div
          class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4"
        >
          <div
            v-for="(item, idx) in items"
            :key="idx"
            class="rounded-xl border border-gray-200 overflow-hidden bg-white hover:shadow-sm transition"
          >
            <router-link
              :to="
                item.children
                  ? `/category/${item.slug}`
                  : `/search/${selectedLocationSlug}?category_slugs=${item.slug}`
              "
              class="block"
            >
              <div
                class="aspect-[4/3] bg-gray-50 flex items-center justify-center p-3"
              >
                <img
                  v-if="item.icon"
                  :src="item.icon"
                  :alt="item.name"
                  class="max-h-full max-w-full object-contain"
                />
                <div v-else class="h-16 w-16 bg-gray-200 rounded"></div>
              </div>
              <div class="p-3 text-center">
                <p class="text-xs md:text-sm font-medium capitalize">
                  {{ item.name }}
                </p>
              </div>
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import SkeletonLoader from "../SkeletonLoader.vue";

export default {
  name: "CategoryWiseSubcategories",
  components: {
    SkeletonLoader,
  },
  props: {
    title: { type: String, required: true },
    categorySlug: { type: String, required: true },
    items: { type: Array, required: true },
    viewAllLink: { type: [String, Object], default: "" },
    odd: { type: Boolean, default: false },
    loading: { type: Boolean, default: false },
  },
  computed: {
    selectedLocationSlug() {
      const selectedLocation = this.$store.getters.selectedLocation;
      if (selectedLocation) {
        // Convert "Nawabganj, Dhaka" to "Nawabganj-Dhaka"
        return selectedLocation
          .replace(/,\s*/g, "-")
          .replace(/\s+/g, "-")
          .toLowerCase();
      }
      return "all-bangladesh";
    },
  },
};
</script>

<style scoped></style>
