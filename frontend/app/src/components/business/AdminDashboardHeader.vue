<template>
  <div class="sticky top-0 z-10">
    <BusinessSlider :show="sidebarOpen" @close="sidebarOpen = false" />

    <header class="bg-white shadow-sm px-4 md:px-12">
      <div class="flex items-center justify-between py-4 md:container mx-auto">
        <button
          class="flex items-center gap-2 px-2 md:px-4 py-2 rounded-md hover:bg-gray-200 transition text-base md:text-lg font-semibold"
          @click="$router.push(backUrl)"
        >
          <ChevronLeftIcon
            class="w-5 h-5"
            v-if="$route.name !== 'business-dashboard'"
          />
          <Squares2X2Icon
            class="w-5 h-5"
            v-if="$route.name === 'business-dashboard'"
          />
          <span>{{ title }}</span>
        </button>

        <div class="items-center gap-2 md:gap-4 hidden md:flex">
          <router-link
            to="/messages"
            class="relative mr-2 hover:text-primary"
            title="Messages"
          >
            <EnvelopeIcon class="w-7" />
            <span
              v-if="totalUnreadConversations > 0"
              class="absolute -top-2 -right-2 bg-red-500 text-white text-xs px-1.5 py-0.5 rounded-full"
            >
              {{ totalUnreadConversations }}
            </span>
          </router-link>

          <div
            class="items-center cursor-pointer hidden md:flex"
            @click="sidebarOpen = true"
          >
            <OptimizedImage
              :src="myBusiness?.business_profile || 'https://placehold.co/150x150/0b845c/white?text=Profile'"
              alt="Profile"
              class="w-9 h-9 rounded-full object-cover border border-gray-300"
            />
            <div class="hidden md:block ml-2 capitalize">
              <p class="text-lg font-semibold line-clamp-1 max-w-[200px]">
                {{ myBusiness?.business_name }}
              </p>
              <p class="text-gray-500 text-sm">
                {{ myBusiness?.name }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </header>
  </div>
</template>

<script>
import {
  ChevronLeftIcon,
  Squares2X2Icon,
  EnvelopeIcon
} from '@heroicons/vue/24/outline'
import BusinessSlider from '@/components/business/BusinessSlider.vue'
import OptimizedImage from '@/components/common/OptimizedImage.vue'

export default {
  name: 'BusinessHeader',

  props: {
    title: {
      type: String,
      default: 'Dashboard'
    },
    backUrl: {
      type: String,
      default: '/my/business/dashboard'
    }
  },

  components: {
    ChevronLeftIcon,
    Squares2X2Icon,
    EnvelopeIcon,
    BusinessSlider,
    OptimizedImage
  },

  data() {
    return {
      sidebarOpen: false,
      conversationTimer: null
    }
  },

  computed: {
    myBusiness() {
      return this.$store.getters.myBusiness
    },

    totalUnreadConversations() {
      return this.$store.getters.totalUnreadCount
    },

    isAuthenticated() {
      return this.$store.getters.isAuthenticated
    }
  },

  methods: {
    async fetchUserBusinessData() {
      if (!this.myBusiness || !this.myBusiness.business_name) {
        await this.$store.dispatch('fetchMyBusiness')
        await this.$store.dispatch('fetchMyBusinessDetails')
      }
    },

    fetchConversations() {
      if (!this.myBusiness) return
      this.$store.dispatch('fetchConversations')
    },

    startConversationPolling() {
      if (this.conversationTimer || !this.myBusiness) return

      this.conversationTimer = setInterval(() => {
        if (document.visibilityState === 'visible') {
          this.fetchConversations()
        }
      }, 30000)
    },

    stopConversationPolling() {
      if (this.conversationTimer) {
        clearInterval(this.conversationTimer)
        this.conversationTimer = null
      }
    },

    handleVisibilityChange() {
      if (document.visibilityState === 'hidden') {
        this.stopConversationPolling()
      } else {
        // this.fetchConversations()
        this.startConversationPolling()
      }
    }
  },

  watch: {
    isAuthenticated(isLoggedIn) {
      if (isLoggedIn) {
        // this.fetchConversations()
        this.startConversationPolling()
      } else {
        this.stopConversationPolling()
      }
    }
  },

  mounted() {
    this.fetchUserBusinessData()

    if (this.isAuthenticated) {
    //   this.fetchConversations()
      this.startConversationPolling()
    }

    document.addEventListener(
      'visibilitychange',
      this.handleVisibilityChange
    )
  },

  beforeUnmount() {
    this.stopConversationPolling()
    document.removeEventListener(
      'visibilitychange',
      this.handleVisibilityChange
    )
  }
}
</script>

<style>
/* No changes needed */
</style>
