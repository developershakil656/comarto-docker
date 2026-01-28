<template>
  <div class="h-screen flex flex-col bg-blue-50">
    <!-- <div class="hidden md:block">
			<TopHeader />
		</div> -->
    <div
      class="flex-1 grid grid-cols-1 lg:grid-cols-12 bg-white/80 backdrop-blur-sm"
      style="height: calc(100vh - 84px)"
    >
      <!-- Conversation list -->
      <aside
        class="lg:col-span-3 border-r border-gray-200/50 flex flex-col bg-white/90 backdrop-blur-sm"
      >
        <div class="p-4 flex-shrink-0">
          <div class="relative">
            <input
              v-model="threadSearch"
              type="text"
              placeholder="Search conversations..."
              class="w-full border-0 rounded-2xl px-4 py-3 pl-12 text-sm bg-gray-100/80 focus:bg-white focus:ring-2 focus:ring-primary/20 focus:shadow-lg outline-none transition-all duration-300"
            />
            <svg
              class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
              ></path>
            </svg>
          </div>
        </div>

        <!-- Filters -->
        <div class="px-4 pb-3 flex-shrink-0">
          <div class="flex bg-gray-100/60 rounded-2xl p-1.5 backdrop-blur-sm">
            <button @click="setFilter('all')" :class="buttonClass('all')">
              All
            </button>
            <button @click="setFilter('buyers')" :class="buttonClass('buyers')">
              Buyers
              <span
                v-if="buyerUnreadCount > 0"
                class="absolute -top-1 bg-red-500 text-white text-xs px-1.5 py-0.5 rounded-full shadow-lg"
                >{{ buyerUnreadCount }}</span
              >
            </button>
            <button
              @click="setFilter('sellers')"
              :class="buttonClass('sellers')"
            >
              Sellers
              <span
                v-if="sellerUnreadCount > 0"
                class="absolute -top-1 bg-red-500 text-white text-xs px-1.5 py-0.5 rounded-full shadow-lg"
                >{{ sellerUnreadCount }}</span
              >
            </button>
          </div>
        </div>

        <div
          class="px-4 pb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider flex-shrink-0"
        >
          Conversations
        </div>
        <div class="flex-1 overflow-y-auto px-2">
          <button
            v-for="c in filteredConversations"
            :key="c.id"
            class="w-full text-left px-3 py-3 hover:bg-gray-50/80 rounded-2xl flex gap-3 items-start transition-all duration-200 group"
            @click="openThread(c)"
          >
            <div class="relative">
              <img
                :src="getAvatar(c)"
                class="w-12 h-12 rounded-full border-2 border-white shadow-lg object-cover"
              />
              <div
                class="absolute -bottom-1 -right-1 w-4 h-4 border-2 border-white rounded-full"
                :class="c.is_online ? 'bg-green-500' : 'bg-gray-300'"
                :title="c.is_online ? 'Online' : 'Offline'"
              ></div>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-1">
                <div
                  class="font-bold text-gray-900 truncate group-hover:text-primary transition-colors duration-200"
                >
                  {{ partnerName(c) }}
                </div>
                <div class="flex items-center gap-2">
                  <span
                    v-if="c.unread_count > 0"
                    class="bg-red-500 text-white text-xs px-2 py-1 rounded-full shadow-lg font-bold"
                    >{{ c.unread_count }}</span
                  >
                  <div class="text-[11px] text-gray-500 font-medium">
                    {{ diffForHumans(c.last_message?.created_at) }}
                  </div>
                </div>
              </div>
              <div class="text-sm text-gray-600 truncate font-medium">
                {{ c.last_message?.message || "Start the conversation" }}
              </div>
            </div>
          </button>
          <div
            v-if="!conversationsLoading && filteredConversations.length === 0"
            class="px-4 py-12 text-center"
          >
            <div
              class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4"
            >
              <svg
                class="w-8 h-8 text-gray-400"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
                ></path>
              </svg>
            </div>
            <div class="text-gray-500 font-medium">
              No {{ activeFilter === "all" ? "" : activeFilter }} conversations
              found
            </div>
            <div class="text-gray-400 text-sm mt-1">
              Start a new conversation to begin messaging
            </div>
          </div>
          <div
            v-if="conversationsLoading && filteredConversations.length === 0"
            class="px-4 py-12 text-center"
          >
            <div
              class="w-8 h-8 border-2 border-primary border-t-transparent rounded-full animate-spin mx-auto mb-4"
            ></div>
            <div class="text-gray-500 font-medium">
              Loading conversations...
            </div>
          </div>
        </div>
      </aside>

      <!-- Right welcome panel (same as Conversations.vue when no thread is open) -->
      <section class="hidden lg:flex lg:col-span-9 items-center justify-center">
        <div class="text-center animate-scale-in px-6">
          <div
            class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg"
          >
            <svg
              class="w-12 h-12 text-primary"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
              ></path>
            </svg>
          </div>
          <div class="text-gray-800 font-bold text-2xl mb-2">
            Welcome to Messages
          </div>
          <div class="text-gray-500 font-medium text-lg mb-3">
            Start connecting with businesses and customers
          </div>
          <div class="text-gray-400 text-sm">
            Choose a conversation from the sidebar to begin messaging
          </div>
          <div class="mt-6 flex justify-center space-x-4">
            <div class="flex items-center text-xs text-gray-400">
              <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
              Online
            </div>
            <div class="flex items-center text-xs text-gray-400">
              <svg
                class="w-4 h-4 mr-2"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                ></path>
              </svg>
              Secure
            </div>
          </div>
        </div>
      </section>
    </div>
    <MobileBottomNavigation />
  </div>
</template>

<script>
import axios from "axios";
import MobileBottomNavigation from "@/components/common/MobileBottomNavigation.vue";

export default {
  name: "ConversationsListView",
  components: { MobileBottomNavigation },
  data() {
    return {
      activeFilter: "all",
      threadSearch: "",
    };
  },
  computed: {
    conversations() {
      return this.$store.getters.conversations;
    },
    conversationsLoading() {
      return this.$store.getters.conversationsLoading;
    },
    buyerUnreadCount() {
      return this.conversations
        .filter((c) => c.my_role === "business") // Role where you are selling to buyers
        .reduce((acc, c) => acc + (c.unread_count || 0), 0);
    },
    sellerUnreadCount() {
      return this.conversations
        .filter((c) => c.my_role === "user") // Role where you are buying from sellers
        .reduce((acc, c) => acc + (c.unread_count || 0), 0);
    },
    filteredConversations() {
      let list = [...(this.conversations || [])];
      if (this.activeFilter === "buyers")
        list = list.filter((c) => c.my_role === "business");
      else if (this.activeFilter === "sellers")
        list = list.filter((c) => c.my_role === "user");
      if (!this.threadSearch.trim()) return list;
      const q = this.threadSearch.trim().toLowerCase();
      return list.filter(
        (c) =>
          this.partnerName(c).toLowerCase().includes(q) ||
          (c.last_message?.message || "").toLowerCase().includes(q)
      );
    },
  },
  methods: {
    checkScreenSize() {
      // Tailwind's 'md' breakpoint is 768px
      this.isDesktop = window.innerWidth >= 768;
      this.$emit("show-header", this.isDesktop);
    },
    buttonClass(type) {
      const base =
        "relative flex-1 py-2.5 px-4 text-sm font-semibold rounded-xl transition-all duration-200";
      return [
        base,
        this.activeFilter === type
          ? "bg-white text-primary shadow-lg shadow-primary/20"
          : "text-gray-600 hover:text-gray-900 hover:bg-white/50",
      ];
    },
    setFilter(filter) {
      this.activeFilter = filter;
    },
    partnerName(c) {
      return c?.profile?.name || "Conversation";
    },
    getAvatar(c) {
      return c?.profile?.profile || "https://placehold.co/150x150/0b845c/white?text=Profile";
    },
    diffForHumans(input) {
      const parse = (val) => {
        if (!val) return null;
        if (val instanceof Date) return val;
        if (typeof val === "number") return new Date(val);
        const d = new Date(val);
        return isNaN(d) ? null : d;
      };
      const date = parse(input);
      if (!date) return "";
      const now = new Date();
      let diffMs = Math.abs(date.getTime() - now.getTime());
      const sec = Math.round(diffMs / 1000);
      const min = Math.round(sec / 60);
      const hr = Math.round(min / 60);
      const day = Math.round(hr / 24);
      if (sec < 45) return `${sec} seconds ago`;
      if (min < 45) return `${min} minutes ago`;
      if (hr < 36) return `${hr} hours ago`;
      if (day < 26) return `${day} days ago`;
      const month = Math.round(day / 30);
      if (month < 11) return `${month} months ago`;
      const year = Math.round(month / 12);
      return `${year} years ago`;
    },
    openThread(c) {
      this.$router.push({
        name: "conversation-messages",
        params: { id: c.id },
      });
    },
  },
  async mounted() {
    if (!this.$store.getters.isAuthenticated) {
      this.$router.push({ name: "home" });
      return;
    }
    const token = this.$store.getters.token;
    if (token)
      axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
    await this.$store.dispatch("fetchConversations");
    
    this.$emit("show-footer", false);
    this.checkScreenSize()
  },
  // Add this navigation guard
  beforeRouteLeave(to, from, next) {
    // When leaving the UserProfile section, tell the parent to show the header again
    this.checkScreenSize();
    this.$emit("show-footer", true);
    next();
  },
};
</script>

<style scoped>
/* Keep list scroll nice */
.overflow-y-auto::-webkit-scrollbar {
  width: 8px;
}
.overflow-y-auto::-webkit-scrollbar-thumb {
  background: rgba(193, 193, 193, 0.8);
  border-radius: 4px;
}
</style>
