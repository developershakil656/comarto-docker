<template>
  <div class="md:sticky relative top-0 z-[60] bg-white md:border-b border-gray-100 md:shadow-sm">
    <header>
      <div class="w-full py-1">
        <div class="container mx-auto px-3 pt-3 md:p-3">
          <div class="md:flex items-center justify-between gap-4 font-semibold">
            <div class="flex gap-4 justify-between items-center">
              <!-- Logo -->
              <div class="text-2xl font-bold text-primary">
                <router-link to="/">
                  <OptimizedImage
                    src="/logo.svg"
                    alt="Logo"
                    class="h-7"
                    :lazy="false"
                  />
                </router-link>
              </div>

              <!-- Search and Location -->
              <div class="w-7/12 md:w-auto">
                <!-- Location Search -->
                <SearchLocation />
              </div>
            </div>

            <!-- Main Search Bar -->
            <div
              class="hidden md:flex items-center flex-1 w-full border border-gray-300 rounded-md shadow-md focus-within:ring-1 focus-within:ring-primary p-[1px]"
            >
              <input
                type="text"
                name="searchKeyword"
                v-model="searchKeyword"
                placeholder='export quality denim in savar'
                class="outline-none text-xs md:text-sm flex-1 p-2 pl-4 placeholder-gray-500"
                @keyup.enter="onSearch"
              />
              <button
                class="bg-primary text-white px-[6px] py-[4px] m-[3px] rounded-md border border-[#b3c6c5] hover:bg-primary/80 transition duration-200 ease-in-out"
                @click="onSearch"
              >
                <MagnifyingGlassIcon
                  class="inline-block h-5 w-5 mt-[-2px] font-semibold"
                />
              </button>
            </div>
            <!-- Right Section: Language, Seller, Login -->
            <div class="md:flex hidden items-center gap-6 text-xs md:text-sm">
              <!-- <div class="flex items-center cursor-pointer font-semibold text-black hover:text-primary transition duration-200 ease-in-out">
                                <LanguageIcon class="inline-block h-5 w-5" />
                                EN
                            </div> -->
              <!-- Become a Seller -->
              <div
                class="flex flex-col items-center cursor-pointer"
                v-if="!user?.business"
              >
                <router-link
                  to="/free/listing"
                  class="text-black rounded-md font-semibold transition hover:bg-gray-200 duration-200 ease-in-out p-2"
                >
                  <span
                    class="bg-red-600 text-white text-xs px-1 rounded absolute mt-[-15px] ml-[26px]"
                    >LIST FOR FREE</span
                  >
                  <BuildingOfficeIcon class="inline-block h-5 w-5" />
                  <span class="text-center ml-1">Become a Seller</span>
                </router-link>
              </div>

              <div class="flex flex-col cursor-pointer" v-if="user?.business">
                <router-link
                  to="/my/business/dashboard"
                  class="text-black items-center rounded-md font-semibold transition hover:bg-gray-200 duration-200 ease-in-out p-2 max-w-xs"
                >
                  <span
                    class="bg-primary text-white text-xs px-1 rounded absolute mt-[-16px] ml-[22px]"
                    >Dashboard</span
                  >
                  <BuildingOfficeIcon class="inline-block h-5 w-5" />
                  <span class="text-center ml-1 capitalize">{{
                    user.business.business_name
                  }}</span>
                </router-link>
              </div>
              <button
                v-if="!isAuthenticated"
                class="bg-primary px-4 py-2 text-white rounded-md font-semibold transition duration-200 hover:bg-primary/80"
                @click="showLogin = true"
              >
                <UserCircleIcon class="inline-block h-5 w-5" />
                Login / Sign Up
              </button>
              <div v-else class="flex items-center gap-2 flex-shrink-0">
                <router-link
                  to="/messages"
                  class="relative mr-2 hover:text-primary"
                  title="Messages"
                >
                  <EnvelopeIcon class="w-6 h-6" />
                  <span
                    v-if="totalUnreadConversations > 0"
                    class="absolute -top-2 -right-2 bg-red-500 text-white text-xs px-1.5 py-0.5 rounded-full"
                    >{{ totalUnreadConversations }}</span
                  >
                </router-link>
                <button>
                  <BellAlertIcon class="w-6 mr-4" />
                </button>
                <!-- <div class=""> -->
                <OptimizedImage
                  :src="
                    user?.profile ||
                    'https://placehold.co/150x150/0b845c/white?text=Profile'
                  "
                  alt="Profile"
                  class="w-9 h-9 rounded-full object-cover border border-gray-300"
                  @click="onProfileClick"
                />
                <!-- </div> -->
              </div>
              <!-- <div class="flex items-center">
                                <BellAlertIcon class="w-6 mr-4 " />
                                <div class="">
                                    <OptimizedImage src="https://placehold.co/150x150/0b845c/white?text=Profile" alt="Profile" class="w-9 h-9 rounded-full object-cover border border-gray-300" @click="onProfileClick" />
                                </div>
                            </div> -->
            </div>
          </div>
        </div>
      </div>

      <!-- Spacer to prevent content from being hidden under the fixed header -->
      <!-- <div class="h-[77px]"></div> -->
      <!-- Modals moved from MainHeader.vue -->
      <LoginModal
        ref="loginModalRef"
        :show="showLogin"
        @close="showLogin = false"
        @submit="onLoginSubmit"
      />
      <EnterNameModal
        :show="showName"
        @submit="onNameSubmit"
        @close="showName = false"
      />
    </header>

    <!-- Main Search Bar in mobile device -->
    <div
      class="md:hidden font-semibold sticky p-3.5 -top-0.5 border-b bg-white border-gray-100 z-[59]"
    >
      <div
        class="flex items-center flex-1 w-full border border-gray-300 rounded-md shadow-md focus-within:ring-1 focus-within:ring-primary p-[1px]"
      >
        <input
          type="text"
          name="searchKeyword"
          v-model="searchKeyword"
          placeholder='export quality denim in savar'
          class="outline-none text-xs md:text-sm flex-1 p-2 pl-4 placeholder-gray-500"
          @keyup.enter="onSearch"
        />
        <button
          class="bg-primary text-white px-[6px] py-[4px] m-[3px] rounded-md border border-[#b3c6c5] hover:bg-primary/80 transition duration-200 ease-in-out"
          @click="onSearch"
        >
          <MagnifyingGlassIcon
            class="inline-block h-5 w-5 mt-[-2px] font-semibold"
          />
        </button>
      </div>
    </div>
    <UserSlider :show="showProfile" :user="user" @close="onCloseProfile" />
  </div>
</template>

<script>
import {
  MagnifyingGlassIcon,
  LanguageIcon,
  BuildingOfficeIcon,
  UserCircleIcon,
  BellAlertIcon,
  EnvelopeIcon,
} from "@heroicons/vue/24/outline";
import UserSlider from "@/components/user/UserSlider.vue";
import LoginModal from "@/components/modals/LoginModal.vue";
import EnterNameModal from "@/components/modals/EnterNameModal.vue";
import SearchLocation from "@/components/header/SearchLocation.vue";
import OptimizedImage from "@/components/common/OptimizedImage.vue";
export default {
  props: {
    keyword: {
      type: String,
      default: "",
    },
  },
  components: {
    MagnifyingGlassIcon,
    LanguageIcon,
    BuildingOfficeIcon,
    UserCircleIcon,
    BellAlertIcon,
    UserSlider,
    LoginModal,
    EnterNameModal,
    SearchLocation,
    OptimizedImage,
    EnvelopeIcon,
  },
  data() {
    return {
      showProfile: false,
      showLogin: false,
      showName: false,
      searchKeyword: this.keyword,
    };
  },
  computed: {
    locations() {
      return this.$store.getters.location;
    },
    selectedLocation() {
      return this.$store.getters.selectedLocation;
    },
    isAuthenticated() {
      return this.$store.getters.isAuthenticated;
    },
    user() {
      return this.$store.getters.user;
    },
    totalUnreadConversations() {
      return this.$store.getters.totalUnreadCount;
    },
  },
  methods: {
    onProfileClick() {
      this.showProfile = true;
    },
    onCloseProfile() {
      this.showProfile = false;
    },
    onLoginSubmit(result) {
      // Login successful - result contains { user, token, message }
      this.showLogin = false;
    },
    onNameSubmit(name) {
      this.showName = false;
      // Name has been saved and user data updated in store
    },
    logout() {
      this.$store.dispatch("logout");
      this.showProfile = false;
    },
    async onSearch() {
      if (!this.searchKeyword.trim()) return;
      // Track the search keyword
      this.$store.dispatch("trackSearchKeyword", this.searchKeyword);
      // Remove the search API call here since SearchView will handle it
      // Just navigate to the search page
      // Get the selected location from store
      const selectedLocation = this.$store.getters.selectedLocation;
      let locationSlug = "all-bangladesh";

      // If we have a selected location, convert it to slug format
      if (selectedLocation) {
        // Convert "Nawabganj, Dhaka" to "Nawabganj-Dhaka"
        locationSlug = selectedLocation
          .replace(/,\s*/g, "-")
          .replace(/\s+/g, "-")
          .toLowerCase();
      }

      this.$router.push({
        name: "search",
        params: {
          location: locationSlug,
          keyword: this.searchKeyword,
        },
      });
    },
  },
  mounted() {
    // Dispatching action to fetch favourite products globally
    this.$store.dispatch("fetchFavouriteProducts").catch((error) => {
      console.error("Failed to load favourites:", error);
    });
    // Dispatching action to fetch following businesses globally
    this.$store.dispatch("fetchFollowingBusinesses").catch((error) => {
      console.error("Failed to load following businesses:", error);
    });
    // Locations API is now handled by SearchLocation.vue to avoid duplicate calls
    // Close dropdown when clicking outside
    document.addEventListener("click", (e) => {
      if (!e.target.closest(".relative")) {
        // This logic is now handled by SearchLocation.vue
      }
    });
    // Check if user name is null and show name modal
    if (this.user && !this.user.name) {
      this.showName = true;
    }
  },
  beforeUnmount() {
    // Clean up timeout
    // This logic is now handled by SearchLocation.vue
  },
  watch: {
    keyword(newVal) {
      this.searchKeyword = newVal;
    },
    // Refresh user data when navigating to conversations page
    $route(to) {
      if (to.path === "/messages" && this.isAuthenticated) {
        this.$store.dispatch("fetchUser");
      }
    },
  },
};
</script>

<style>
/* category css start */
.paper-content {
  transform-origin: top;
  --tw-scale-y: 0;
  transform: translate(var(--tw-translate-x), var(--tw-translate-y))
    rotate(var(--tw-rotate)) skew(var(--tw-skew-x)) skewY(var(--tw-skew-y))
    scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 0.3s;
}

.category-active {
  --tw-scale-y: 1;
  transform: translate(var(--tw-translate-x), var(--tw-translate-y))
    rotate(var(--tw-rotate)) skew(var(--tw-skew-x)) skewY(var(--tw-skew-y))
    scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
}

.category-list {
  position: absolute;
  /* top: 2.75rem; */
  width: 13rem;
  border-radius: 0.5rem;
  --tw-bg-opacity: 1;
  background-color: rgb(255 255 255 / var(--tw-bg-opacity, 1));
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
  --tw-shadow: 0px 15px 40px rgba(73, 72, 72, 0.1);
  --tw-shadow-colored: 0px 15px 40px var(--tw-shadow-color);
  box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000),
    var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
}

.category-item {
  position: relative;
  padding-left: 1rem;
  padding-right: 1rem;
}

.category-menu {
  display: flex;
  height: 2.5rem;
  width: 100%;
  align-items: center;
  justify-content: space-between;
  gap: 0.5rem;
  border-bottom-width: 1px;
  --tw-border-opacity: 1;
  border-color: rgb(243 244 246 / var(--tw-border-opacity, 1));
  --tw-text-opacity: 1;
  color: rgb(31 31 57 / var(--tw-text-opacity, 1));
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 0.3s;
}

.category-nest {
  visibility: hidden;
  position: absolute;
  top: 0;
  left: 12rem;
  width: 13rem;
  border-radius: 0.5rem;
  --tw-bg-opacity: 1;
  background-color: rgb(255 255 255 / var(--tw-bg-opacity, 1));
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
  opacity: 0;
  --tw-shadow: 0px 15px 40px rgba(73, 72, 72, 0.1);
  --tw-shadow-colored: 0px 15px 40px var(--tw-shadow-color);
  box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000),
    var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 0.3s;
}

.category-item:hover > ul {
  visibility: visible;
  left: 13rem;
  opacity: 1;
}

.category-item:hover > .category-menu {
  --tw-text-opacity: 1;
  color: rgb(var(--primary) / var(--tw-text-opacity, 1));
}

.category-item:last-child > .category-menu {
  border-style: none;
}

.down-arrow {
  display: flex;
  align-items: center;
  gap: 0.375rem;
}

.down-arrow:after {
  font-family: var(--icon-font);
  font-size: 0.875rem;
  line-height: 1.25rem;
  font-weight: 600;
}

/* category css end */

/* Dropdown scrollbar fix */
.dropdown-container {
  box-sizing: border-box;
}

.dropdown-container::-webkit-scrollbar {
  width: 8px;
}

.dropdown-container::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 4px;
}

.dropdown-container::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 4px;
}

.dropdown-container::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}
</style>
