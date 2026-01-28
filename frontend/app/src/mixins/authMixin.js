export default {
  computed: {
    isAuthenticated() {
      return this.$store.getters.isAuthenticated;
    },
    user() {
      return this.$store.getters.user;
    }
  },
  methods: {
    checkAuth() {
      if (this.$route.meta.requiresAuth && !this.isAuthenticated) {
        this.$router.push({ path: '/', query: { login: 'required' } });
        return false;
      }
      return true;
    }
  },
  mounted() {
    // Check authentication on component mount if route requires auth
    if (this.$route.meta.requiresAuth) {
      this.checkAuth();
    }
  }
} 