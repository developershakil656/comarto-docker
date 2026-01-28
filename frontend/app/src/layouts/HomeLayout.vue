<template>
  <div>
    <TopHeader v-if="showHeader" :keyword="$route.params.keyword || ''" />
    <router-view
      @show-header="(val) => (showHeader = val)"
      @show-footer="(val) => (showFooter = val)"
      @show-mobile-navigation="(val) => (showMobileNavigation = val)"
    />

    <div class="hidden md:block"><Footer v-if="showFooter" /></div>
    <MobileBottomNavigation v-if="showMobileNavigation" />
  </div>
</template>

<script>
import TopHeader from "@/components/header/TopHeader.vue";
import MobileBottomNavigation from "@/components/common/MobileBottomNavigation.vue";
import Footer from "@/components/common/Footer.vue";
export default {
  components: {
    TopHeader,
    MobileBottomNavigation,
    Footer,
  },
  data() {
    return {
      showHeader: true,
      showFooter: true,
      showMobileNavigation: true,
      pollingTimer: null
    };
  },
 computed: {
    isAuthenticated() {
      return this.$store.getters.isAuthenticated
    }
  },

  methods: {
    fetchConversations() {
      if (!this.isAuthenticated) return
      this.$store.dispatch('fetchConversations')
    },

    startPolling() {
      if (this.pollingTimer || !this.isAuthenticated) return

      this.pollingTimer = setInterval(() => {
        if (document.visibilityState === 'visible') {
          this.fetchConversations()
        }
      }, 30000)
    },

    stopPolling() {
      if (this.pollingTimer) {
        clearInterval(this.pollingTimer)
        this.pollingTimer = null
      }
    },

    handleVisibilityChange() {
      if (document.visibilityState === 'hidden') {
        this.stopPolling()
      } else {
        this.fetchConversations()
        this.startPolling()
      }
    }
  },

  watch: {
    isAuthenticated(isLoggedIn) {
      if (isLoggedIn) {
        this.fetchConversations()
        this.startPolling()
      } else {
        this.stopPolling()
      }
    }
  },

  mounted() {
    if (this.isAuthenticated) {
      this.fetchConversations()
      this.startPolling()
    }

    document.addEventListener(
      'visibilitychange',
      this.handleVisibilityChange
    )
  },

  beforeUnmount() {
    this.stopPolling()
    document.removeEventListener(
      'visibilitychange',
      this.handleVisibilityChange
    )
  }
}

</script>
