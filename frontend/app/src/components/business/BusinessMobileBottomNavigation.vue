<template>
  <div class="md:hidden h-16"></div>
  <!-- Mobile Bottom Navigation for Business -->
  <div
    class="fixed bottom-0 left-0 right-0 bg-gradient-to-br from-white to-primary-50 backdrop-blur-xl border-t border-primary-100/80 flex justify-around items-center py-2 z-50 shadow-lg shadow-primary-500/10 md:hidden">

    <!-- Dashboard -->
    <router-link :to="{ name: 'business-dashboard' }"
      class="flex flex-col items-center cursor-pointer px-3 py-2 rounded-2xl transition-all duration-300 ease-out min-w-12"
      :class="isActive(['business-dashboard']) ? 'text-primary -translate-y-0.5' : 'text-gray-600 hover:-translate-y-0.5'">
      <div class="flex items-center justify-center mb-1.5">
        <HomeIcon v-if="isActive(['business-dashboard'])" class="w-6 h-6 transition-all duration-300"
          :class="{ 'scale-110': isActive(['business-dashboard']) }" />
        <HomeIconOutline v-else class="w-6 h-6 transition-all duration-300" />
      </div>
      <span class="text-xs font-medium text-center leading-tight transition-all duration-300"
        :class="{ 'font-semibold text-primary': isActive(['business-dashboard']), 'text-gray-600': !isActive(['business-dashboard']) }">Dashboard</span>
    </router-link>

    <!-- Leads -->
    <router-link :to="{ name: 'business-leads' }"
      class="flex flex-col items-center cursor-pointer px-3 py-2 rounded-2xl transition-all duration-300 ease-out min-w-12"
      :class="isActive(['business-leads']) ? 'text-primary -translate-y-0.5' : 'text-gray-600 hover:-translate-y-0.5'">
      <div class="flex items-center justify-center mb-1.5">
        <InboxIcon v-if="isActive(['business-leads'])" class="w-6 h-6 transition-all duration-300"
          :class="{ 'scale-110': isActive(['business-leads']) }" />
        <InboxIconOutline v-else class="w-6 h-6 transition-all duration-300" />
      </div>
      <span class="text-xs font-medium text-center leading-tight transition-all duration-300"
        :class="{ 'font-semibold text-primary': isActive(['business-leads']), 'text-gray-600': !isActive(['business-leads']) }">Leads</span>
    </router-link>

    <!-- Message -->
    <router-link :to="{ name: 'conversations' }"
      class="flex flex-col items-center cursor-pointer px-3 py-2 rounded-2xl transition-all duration-300 ease-out min-w-12"
      :class="isActive(['conversations', 'conversation-messages']) ? 'text-primary -translate-y-0.5' : 'text-gray-600 hover:-translate-y-0.5'">
      <div class="flex items-center justify-center mb-1.5 relative">
        <ChatBubbleLeftRightIcon v-if="isActive(['conversations', 'conversation-messages'])"
          class="w-6 h-6 transition-all duration-300"
          :class="{ 'scale-110': isActive(['conversations', 'conversation-messages']) }" />
        <ChatBubbleLeftRightIconOutline v-else class="w-6 h-6 transition-all duration-300" />
        <div v-if="totalUnreadCount > 0"
          class="absolute -top-1 -right-1.5 w-4 h-4 bg-red-500 text-white text-[10px] flex items-center justify-center rounded-full border-2 border-white">
          {{ totalUnreadCount > 9 ? '9+' : totalUnreadCount }}
        </div>
      </div>
      <span class="text-xs font-medium text-center leading-tight transition-all duration-300"
        :class="{ 'font-semibold text-primary': isActive(['conversations', 'conversation-messages']), 'text-gray-600': !isActive(['conversations', 'conversation-messages']) }">Message</span>
    </router-link>

    <!-- Product -->
    <router-link :to="{ name: 'business-products' }"
      class="flex flex-col items-center cursor-pointer px-3 py-2 rounded-2xl transition-all duration-300 ease-out min-w-12"
      :class="isActive(['business-products', 'business-add-product', 'EditProduct']) ? 'text-primary -translate-y-0.5' : 'text-gray-600 hover:-translate-y-0.5'">
      <div class="flex items-center justify-center mb-1.5">
        <TagIcon v-if="isActive(['business-products', 'business-add-product', 'EditProduct'])"
          class="w-6 h-6 transition-all duration-300"
          :class="{ 'scale-110': isActive(['business-products', 'business-add-product', 'EditProduct']) }" />
        <TagIconOutline v-else class="w-6 h-6 transition-all duration-300" />
      </div>
      <span class="text-xs font-medium text-center leading-tight transition-all duration-300"
        :class="{ 'font-semibold text-primary': isActive(['business-products', 'business-add-product', 'EditProduct']), 'text-gray-600': !isActive(['business-products', 'business-add-product', 'EditProduct']) }">Product</span>
    </router-link>

    <!-- Profile -->
    <button @click="isSliderOpen = true"
      class="flex flex-col items-center cursor-pointer px-3 py-2 rounded-2xl transition-all duration-300 ease-out min-w-12"
      :class="isSliderOpen ? 'text-primary -translate-y-0.5' : 'text-gray-600 hover:-translate-y-0.5'">
      <div class="flex items-center justify-center mb-1.5">
        <UserCircleIcon class="w-6 h-6 transition-all duration-300"
          :class="{ 'scale-110': isSliderOpen }" />
      </div>
      <span class="text-xs font-medium text-center leading-tight transition-all duration-300"
        :class="{ 'font-semibold text-primary': isSliderOpen, 'text-gray-600': !isSliderOpen }">Profile</span>
    </button>

  </div>
  <BusinessSlider :show="isSliderOpen" @close="isSliderOpen = false" />
</template>

<script>
import { mapGetters } from 'vuex';
import { HomeIcon, InboxIcon, ChatBubbleLeftRightIcon, TagIcon } from '@heroicons/vue/24/solid';
import { HomeIcon as HomeIconOutline, InboxIcon as InboxIconOutline, ChatBubbleLeftRightIcon as ChatBubbleLeftRightIconOutline, TagIcon as TagIconOutline, UserCircleIcon } from '@heroicons/vue/24/outline';
import BusinessSlider from '@/components/business/BusinessSlider.vue';

export default {
  name: 'BusinessMobileBottomNavigation',
  components: {
    BusinessSlider,
    HomeIcon, HomeIconOutline,
    InboxIcon, InboxIconOutline,
    ChatBubbleLeftRightIcon, ChatBubbleLeftRightIconOutline,
    TagIcon, TagIconOutline,
    UserCircleIcon
  },
  computed: {
    ...mapGetters(['totalUnreadCount']),
  },
  data() {
    return {
      isSliderOpen: false,
    };
  },
  computed: {
    ...mapGetters(['totalUnreadCount']),
  },
  methods: {
    isActive(routeNames) {
      return routeNames.includes(this.$route.name);
    }
  }
}
</script>

<style scoped>
/* Add safe area support for devices with home indicators */
@supports (padding-bottom: env(safe-area-inset-bottom)) {
  .fixed.bottom-0 {
    padding-bottom: calc(8px + env(safe-area-inset-bottom));
  }
}
</style>