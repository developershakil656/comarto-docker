<template>
  <div class="md:hidden h-10"></div>
  <!-- Mobile Bottom Navigation -->
  <div
    class="fixed bottom-0 left-0 right-0 bg-gradient-to-br from-white to-primary-50 backdrop-blur-xl border-t border-primary-100/80 flex justify-around items-center py-2 z-50 shadow-lg shadow-primary-500/10 md:hidden">
    <!-- Home -->
    <div
      class="flex flex-col items-center cursor-pointer px-3 py-2 rounded-2xl transition-all duration-300 ease-out min-w-12"
      :class="{
        'text-primary -translate-y-0.5': $route.name === 'home',
        'text-gray-600 hover:-translate-y-0.5': $route.name !== 'home'
      }" @click="navigateTo('home')">
      <div class="flex items-center justify-center mb-1.5">
        <HomeIcon v-if="$route.name === 'home'" class="w-6 h-6 transition-all duration-300"
          :class="{ 'scale-110': $route.name === 'home' }" />
        <HomeIconOutline v-else class="w-6 h-6 transition-all duration-300" />
      </div>
      <span class="text-xs font-medium text-center leading-tight transition-all duration-300"
        :class="{ 'font-semibold text-primary': $route.name === 'home', 'text-gray-600': $route.name !== 'home' }">Home</span>
    </div>

    <!-- Category -->
    <div
      class="flex flex-col items-center cursor-pointer px-3 py-2 rounded-2xl transition-all duration-300 ease-out min-w-12"
      :class="{
        'text-primary -translate-y-0.5': $route.name === 'category-detail' || $route.name === 'all-categories',
        'text-gray-600 hover:-translate-y-0.5': $route.name !== 'category-detail' && $route.name !== 'all-categories'
      }" @click="navigateTo('category-detail')">
      <div class="flex items-center justify-center mb-1.5">
        <Squares2X2Icon v-if="$route.name === 'category-detail' || $route.name === 'all-categories'"
          class="w-6 h-6 transition-all duration-300"
          :class="{ 'scale-110': $route.name === 'category-detail' || $route.name === 'all-categories' }" />
        <Squares2X2IconOutline v-else class="w-6 h-6 transition-all duration-300" />
      </div>
      <span class="text-xs font-medium text-center leading-tight transition-all duration-300"
        :class="{ 'font-semibold text-primary': $route.name === 'category-detail' || $route.name === 'all-categories', 'text-gray-600': $route.name !== 'category-detail' && $route.name !== 'all-categories' }">Category</span>
    </div>

    <!-- Message -->
    <div
      class="flex flex-col items-center cursor-pointer px-3 py-2 rounded-2xl transition-all duration-300 ease-out min-w-12"
      :class="{
        'text-primary -translate-y-0.5': $route.name === 'conversations',
        'text-gray-600 hover:-translate-y-0.5': $route.name !== 'conversations'
      }" @click="navigateTo('conversations')">
      <div class="flex items-center justify-center mb-1.5 relative">
        <ChatBubbleLeftRightIcon v-if="$route.name === 'conversations'" class="w-6 h-6 transition-all duration-300"
          :class="{ 'scale-110': $route.name === 'conversations' }" />
        <ChatBubbleLeftRightIconOutline v-else class="w-6 h-6 transition-all duration-300" />
        <div v-if="isAuthenticated && hasUnreadMessages"
          class="absolute -top-0.5 -right-0.5 w-2 h-2 bg-gradient-to-br from-red-500 to-red-600 rounded-full border-2 border-white animate-pulse">
        </div>
      </div>
      <span class="text-xs font-medium text-center leading-tight transition-all duration-300"
        :class="{ 'font-semibold text-primary': $route.name === 'conversations', 'text-gray-600': $route.name !== 'conversations' }">Message</span>
    </div>

    <!-- Enquiry -->
    <div
      class="flex flex-col items-center cursor-pointer px-3 py-2 rounded-2xl transition-all duration-300 ease-out min-w-12"
      :class="{
        'text-primary -translate-y-0.5': $route.name === 'inquiries',
        'text-gray-600 hover:-translate-y-0.5': $route.name !== 'inquiries'
      }" @click="navigateTo('inquiries')">
      <div class="flex items-center justify-center mb-1.5 relative">
        <ChatBubbleOvalLeftEllipsisIcon v-if="$route.name === 'inquiries'" class="w-6 h-6 transition-all duration-300"
          :class="{ 'scale-110': $route.name === 'inquiries' }" />
        <ChatBubbleOvalLeftEllipsisIconOutline v-else class="w-6 h-6 transition-all duration-300" />
        <div v-if="isAuthenticated && hasUnreadInquiries"
          class="absolute -top-0.5 -right-0.5 w-2 h-2 bg-gradient-to-br from-red-500 to-red-600 rounded-full border-2 border-white animate-pulse">
        </div>
      </div>
      <span class="text-xs font-medium text-center leading-tight transition-all duration-300"
        :class="{ 'font-semibold text-primary': $route.name === 'inquiries', 'text-gray-600': $route.name !== 'inquiries' }">Enquiry</span>
    </div>

    <!-- Profile -->
    <div
      class="flex flex-col items-center cursor-pointer px-3 py-2 rounded-2xl transition-all duration-300 ease-out min-w-12"
      :class="{
        'text-primary -translate-y-0.5': isUserRoute,
        'text-gray-600 hover:-translate-y-0.5': !isUserRoute
      }" @click="navigateTo('user')">
      <div class="flex items-center justify-center mb-1.5 relative">
        <UserIcon v-if="isUserRoute" class="w-6 h-6 transition-all duration-300"
          :class="{ 'scale-110': isUserRoute }" />
        <UserIconOutline v-else class="w-6 h-6 transition-all duration-300" />
      </div>
      <span class="text-xs font-medium text-center leading-tight transition-all duration-300"
        :class="{ 'font-semibold text-primary': isUserRoute, 'text-gray-600': !isUserRoute }">Profile</span>
    </div>
  </div>

  <!-- Inquiry Modal -->
  <InquiryModal v-if="showInquiryModal" @close="showInquiryModal = false" />

  <!-- Login Modal -->
  <LoginModal :show="showLoginModal" @close="showLoginModal = false" />

  <!-- User Slider -->
  <UserSlider :show="showUserSlider" @close="showUserSlider = false" />
</template>

<script>
import { mapGetters } from 'vuex'
import {
  HomeIcon,
  Squares2X2Icon,
  ChatBubbleLeftRightIcon,
  ChatBubbleOvalLeftEllipsisIcon,
  UserIcon
} from '@heroicons/vue/24/solid'

import {
  HomeIcon as HomeIconOutline,
  Squares2X2Icon as Squares2X2IconOutline,
  ChatBubbleLeftRightIcon as ChatBubbleLeftRightIconOutline,
  ChatBubbleOvalLeftEllipsisIcon as ChatBubbleOvalLeftEllipsisIconOutline,
  UserIcon as UserIconOutline
} from '@heroicons/vue/24/outline'

import InquiryModal from '@/components/modals/InquiryModal.vue'
import LoginModal from '@/components/modals/LoginModal.vue'
import UserSlider from '@/components/user/UserSlider.vue'

export default {
  name: 'MobileBottomNavigation',
  components: {
    HomeIcon,
    HomeIconOutline,
    Squares2X2Icon,
    Squares2X2IconOutline,
    ChatBubbleLeftRightIcon,
    ChatBubbleLeftRightIconOutline,
    ChatBubbleOvalLeftEllipsisIcon,
    ChatBubbleOvalLeftEllipsisIconOutline,
    UserIcon,
    UserIconOutline,
    InquiryModal,
    LoginModal,
    UserSlider
  },
  data() {
    return {
      showInquiryModal: false,
      showLoginModal: false,
      showUserSlider: false
    }
  },
  computed: {
    ...mapGetters(['isAuthenticated']),
    hasUnreadMessages() {
      // This would typically come from your store or API
      // For now, return false as a placeholder
      return false;
    },
    hasUnreadInquiries() {
      // This would typically come from your store or API
      // For now, return false as a placeholder
      return false;
    },
    isUserRoute() {
      // Check if current route is any user-related route (except 'inquiries')
      const userRoutes = [
        'user',
        'account-details',
        'following',
        'favourites',
        'reviews',
        'identity-verify'
      ];

      return (
        (userRoutes.includes(this.$route.name) || this.$route.path.startsWith('/user')) &&
        this.$route.name !== 'inquiries'
      );
    }
  },
  methods: {
    navigateTo(routeName) {
      if (routeName === 'category-detail') {
        this.$router.push({ name: 'all-categories' });
      } else if (routeName === 'user') {
        // Open UserSlider on mobile instead of navigating
        if (this.isAuthenticated) {
          this.showUserSlider = true;
        } else {
          // Show login modal
          this.showLoginModal = true;
        }
      } else if (routeName === 'conversations') {
        // Check if user is authenticated for protected routes
        if (this.isAuthenticated) {
          this.$router.push({ name: routeName });
        } else {
          // Show login modal
          this.showLoginModal = true;
        }
      } else if (routeName === 'inquiries') {
        // Check if user is authenticated for protected routes
        if (this.isAuthenticated) {
          this.$router.push({ name: routeName });
        } else {
          // Show login modal
          this.showLoginModal = true;
        }
      } else {
        this.$router.push({ name: routeName });
      }
    }
  }
}
</script>

<style scoped>
/* Custom animations that can't be easily replicated with Tailwind */
@keyframes pulse-notification {

  0%,
  100% {
    transform: scale(1);
    opacity: 1;
  }

  50% {
    transform: scale(1.2);
    opacity: 0.8;
  }
}

/* Ensure content doesn't get hidden behind the fixed nav */
@media (max-width: 767px) {
  body {
    padding-bottom: 88px;
  }

  .main-content {
    margin-bottom: 88px;
  }
}

/* Add safe area support for devices with home indicators */
@supports (padding-bottom: env(safe-area-inset-bottom)) {
  .fixed.bottom-0 {
    padding-bottom: calc(8px + env(safe-area-inset-bottom));
    bottom:-1px;
  
  }
}
</style>
