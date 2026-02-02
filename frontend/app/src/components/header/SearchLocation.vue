<template>
  <div class="md:text-sm text-xs relative location-search">
    <div
      class="border border-gray-300 rounded-md bg-gray-100 p-1 md:p-2 shadow-md focus-within:ring-1 focus-within:ring-primary"
    >
      <label class="flex items-center">
        <MapPinIcon class="inline-block h-6 w-6 mr-1 text-gray-800" />
        <input
          type="text"
          name="searchLocation"
          v-model="searchQuery"
          :placeholder="selectedLocation || 'All Bangladesh'"
          class="bg-gray-100 outline-none w-full text-black placeholder-gray-800"
          @focus="showLocationDropdown = true"
          @input="debouncedSearch"
        />
        <ChevronDownIcon
          class="inline-block h-6 w-6 ml-1 text-gray-800 transition-transform duration-200 cursor-pointer"
          :class="{ 'rotate-180': showLocationDropdown }"
          @click.stop.prevent="toggleLocationDropdown"
        />
      </label>
    </div>
    <!-- Location Dropdown -->
    <div
      v-if="showLocationDropdown"
      class="absolute top-full left-0 w-full bg-white border border-primary rounded-md shadow-lg z-[60] max-h-72 overflow-y-auto dropdown-container"
    >
      <!-- Locations List -->
      <div v-if="locations.length > 0">
        <div
          class="px-2 md:px-4 py-2 hover:bg-gray-100 cursor-pointer text-black border-b border-gray-100 last:border-b-0"
          v-if="!searchQuery"
          @click="selectLocation('')"
        >
          All Bangladesh
        </div>
        <div
          v-for="location in locations"
          :key="location"
          class="px-2 md:px-4 py-2 hover:bg-gray-100 cursor-pointer text-black border-b border-gray-100 last:border-b-0"
          @click="selectLocation(setLocationName(location))"
        >
          {{ setLocationName(location) }}
        </div>
      </div>
      <!-- No Results -->
      <div v-else class="px-2 md:px-4 py-2 text-gray-500 text-center">
        No locations found
      </div>
    </div>
  </div>
</template>

<script>
import { ChevronDownIcon, MapPinIcon } from "@heroicons/vue/24/outline";
export default {
  name: "SearchLocation",
  components: {
    ChevronDownIcon,
    MapPinIcon,
  },
  data() {
    return {
      showLocationDropdown: false,
      searchQuery: this.$store.getters.selectedLocation,
      searchTimeout: null,
    };
  },
  computed: {
    locations() {
      return this.$store.getters.locations;
    },
    selectedLocation() {
      return this.$store.getters.selectedLocation;
    },
  },
  methods: {
    setLocationName(location) {
      if (location.upazila_name) {
        return `${location.upazila_name}, ${location.district_name}`;
      } else {
        return `${location.district_name}, District`;
      }
    },

    async toggleLocationDropdown() {
      this.showLocationDropdown = !this.showLocationDropdown;
      if (
        this.showLocationDropdown &&
        this.locations.length === 0 &&
        !this.selectedLocation
      ) {
        await this.$store.dispatch("fetchLocations");
      }
    },
    selectLocation(location) {
      this.$store.dispatch("setSelectedLocation", location);
      this.searchQuery = location;
      this.showLocationDropdown = false;
    },
    async searchLocations() {
      await this.$store.dispatch("fetchLocations", this.searchQuery);
    },
    debouncedSearch() {
      clearTimeout(this.searchTimeout);
      this.searchTimeout = setTimeout(() => {
        this.searchLocations();
      }, 500);
    },
    handleClickOutside(e) {
      if (!e.target.closest(".location-search")) {
        this.showLocationDropdown = false;
      }
    },
  },
  mounted() {
    // Only fetch locations if they haven't been loaded yet
    if (this.locations.length === 0) {
      this.$store.dispatch("fetchLocations");
    }
    // Close dropdown when clicking outside
    document.addEventListener("click", this.handleClickOutside);
  },
  beforeUnmount() {
    if (this.searchTimeout) {
      clearTimeout(this.searchTimeout);
    }
    document.removeEventListener("click", this.handleClickOutside);
  },
  watch: {
    showLocationDropdown(val) {
      if (!val) {
        this.searchQuery = this.selectedLocation;
      }
    },
    selectedLocation(val) {
      this.searchQuery = this.selectedLocation;
    },
  },
};
</script>
