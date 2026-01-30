import { createStore } from "vuex";
import axios from "axios";
import { push } from "notivue";

// Configure axios base URL
axios.defaults.baseURL = import.meta.env.VITE_API_BASE_URL || "https://api.comarto.com/api/v1";

export default createStore({
  state: {
    locations: [],
    businessLocations: [], // Separate state for business registration locations
    selectedLocation: localStorage.getItem("selectedLocation") || "",
    searchResults: [],
    searchMeta: { current_page: 1, last_page: 1, total: 0 },
    searchParams: null,
    loadingMore: false,
    businessTypes: [],
    productDetails: null, // Added for product details
    businessData: null, // Added for business data
    businessDetails: null, // Added for business details
    myBusiness: null, // Added for user's business data
    myBusinessDetails: null, // Added for user's business details
    loading: true, // Added loading state
    // Authentication state
    user: JSON.parse(localStorage.getItem("user")) || null,
    isAuthenticated: !!localStorage.getItem("user"),
    authLoading: false,
    token: localStorage.getItem("token") || null,
    // Favourites state
    favouriteProducts: [],
    favouritesLoading: false,
    // Following state
    followingBusinesses: [],
    followingLoading: false,
    // Reviews state
    userReviews: [],
    reviewsLoading: false,
    // Product creation state
    productCreation: {
      loading: false,
      success: false,
      error: null,
      productId: null,
    },
    // Product units and categories for search
    productUnits: [],
    productCategories: [],
    unitsLoading: false,
    categoriesLoading: false,
    // User products state
    myProducts: [],
    myProductsLoading: false,
    // Messaging state
    conversations: [],
    conversationsLoading: false,
    totalUnreadCount: 0, // Global variable for header
    messages: [],
    // Add message fetching debounce tracking
    lastMessageFetchTime: 0,
    // Inquiry management state
    userInquiries: [],
    inquiriesLoading: false,
    inquiriesMeta: { current_page: 1, last_page: 1, total: 0 },
    inquiriesLoadingMore: false,
    // Leads management state
    leads: [],
    leadsLoading: false,
    leadsMeta: { current_page: 1, last_page: 1, total: 0 },
    leadsParams: null,
    leadsLoadingMore: false,
    unlockedLeads: [],
    unlockedLeadsLoading: false,
    unlockedLeadsMeta: { current_page: 1, last_page: 1, total: 0 },
    unlockedLeadsLoadingMore: false,
    favoriteLeads: [],
    favoriteLeadsLoading: false,
    // Keyword tracking state
    searchKeywords: JSON.parse(localStorage.getItem("searchKeywords")) || [],
  },
  getters: {
    locations: (state) => state.locations,
    selectedLocation: (state) => state.selectedLocation,
    searchResults: (state) => state.searchResults,
    searchMeta: (state) => state.searchMeta,
    canLoadMore: (state) =>
      (state.searchMeta?.current_page || 1) <
      (state.searchMeta?.last_page || 1),
    loadingMore: (state) => state.loadingMore,
    businessTypes: (state) => state.businessTypes,
    productDetails: (state) => state.productDetails, // Added getter
    businessData: (state) => state.businessData, // Added getter
    businessDetails: (state) => state.businessDetails, // Added getter
    myBusiness: (state) => state.myBusiness, // Added getter
    myBusinessDetails: (state) => state.myBusinessDetails, // Added getter
    loading: (state) => state.loading, // Added loading getter
    businessLocations: (state) => state.businessLocations,
    // Authentication getters
    user: (state) => state.user,
    isAuthenticated: (state) => state.isAuthenticated,
    authLoading: (state) => state.authLoading,
    token: (state) => state.token,
    // Favourites getters
    favouriteProducts: (state) => state.favouriteProducts,
    favouritesLoading: (state) => state.favouritesLoading,
    // Following getters
    followingBusinesses: (state) => state.followingBusinesses,
    followingLoading: (state) => state.followingLoading,
    // Reviews getters
    userReviews: (state) => state.userReviews,
    reviewsLoading: (state) => state.reviewsLoading,
    // Product creation getters
    productCreation: (state) => state.productCreation,
    // Product units and categories getters
    productUnits: (state) => state.productUnits,
    productCategories: (state) => state.productCategories,
    unitsLoading: (state) => state.unitsLoading,
    categoriesLoading: (state) => state.categoriesLoading,
    // User products getters
    myProducts: (state) => state.myProducts,
    myProductsLoading: (state) => state.myProductsLoading,
    // Messaging getters
    conversations: (state) => state.conversations,
    conversationsLoading: (state) => state.conversationsLoading,
    totalUnreadCount: (state) => state.totalUnreadCount, // For header
    messages: (state) => state.messages,
    // Inquiry management getters
    userInquiries: (state) => state.userInquiries,
    inquiriesLoading: (state) => state.inquiriesLoading,
    inquiriesMeta: (state) => state.inquiriesMeta,
    inquiriesCanLoadMore: (state) =>
      (state.inquiriesMeta?.current_page || 1) <
      (state.inquiriesMeta?.last_page || 1),
    inquiriesLoadingMore: (state) => state.inquiriesLoadingMore,
    // Leads management getters
    leads: (state) => state.leads,
    leadsLoading: (state) => state.leadsLoading,
    leadsMeta: (state) => state.leadsMeta,
    leadsCanLoadMore: (state) =>
      (state.leadsMeta?.current_page || 1) < (state.leadsMeta?.last_page || 1),
    leadsLoadingMore: (state) => state.leadsLoadingMore,
    unlockedLeads: (state) => state.unlockedLeads,
    unlockedLeadsLoading: (state) => state.unlockedLeadsLoading,
    unlockedLeadsMeta: (state) => state.unlockedLeadsMeta,
    unlockedLeadsCanLoadMore: (state) =>
      (state.unlockedLeadsMeta?.current_page || 1) <
      (state.unlockedLeadsMeta?.last_page || 1),
    unlockedLeadsLoadingMore: (state) => state.unlockedLeadsLoadingMore,
    favoriteLeads: (state) => state.favoriteLeads,
    favoriteLeadsLoading: (state) => state.favoriteLeadsLoading,
    searchKeywords: (state) => state.searchKeywords,
    lastFiveKeywords: (state) => state.searchKeywords.slice(-5),
  },
  mutations: {
    SET_LOCATIONS(state, locations) {
      state.locations = locations;
    },
    SET_BUSINESS_LOCATIONS(state, locations) {
      state.businessLocations = locations;
    },
    SET_SELECTED_LOCATION(state, location) {
      state.selectedLocation = location;
      localStorage.setItem("selectedLocation", location);
    },
    SET_SEARCH_RESULTS(state, results) {
      state.searchResults = results;
    },
    APPEND_SEARCH_RESULTS(state, results) {
      state.searchResults = [...state.searchResults, ...results];
    },
    SET_SEARCH_META(state, meta) {
      state.searchMeta = {
        current_page: meta?.current_page || 1,
        last_page: meta?.last_page || 1,
        total: meta?.total || 0,
      };
    },
    SET_SEARCH_PARAMS(state, params) {
      state.searchParams = params || null;
    },
    SET_BUSINESS_TYPES(state, types) {
      state.businessTypes = types;
    },
    SET_PRODUCT_DETAILS(state, details) {
      // Added mutation
      state.productDetails = details;
    },
    SET_BUSINESS_DATA(state, data) {
      // Added mutation
      state.businessData = data;
    },
    SET_BUSINESS_DETAILS(state, details) {
      // Added mutation
      state.businessDetails = details;
    },
    SET_MY_BUSINESS(state, business) {
      // Added mutation
      state.myBusiness = business;
    },
    SET_MY_BUSINESS_DETAILS(state, details) {
      // Added mutation
      state.myBusinessDetails = details;
    },
    SET_LOADING(state, loading) {
      // Added loading mutation
      state.loading = loading;
    },
    SET_LOADING_MORE(state, loading) {
      state.loadingMore = !!loading;
    },
    // Authentication mutations
    SET_USER(state, user) {
      state.user = user;
      state.isAuthenticated = !!user;
      if (user) {
        localStorage.setItem("user", JSON.stringify(user));
      } else {
        localStorage.removeItem("user");
      }
      state.totalUnreadCount = user?.total_unread_conversations || 0;
    },
    SET_AUTH_LOADING(state, loading) {
      state.authLoading = loading;
    },
    SET_TOKEN(state, token) {
      state.token = token;
      if (token) {
        localStorage.setItem("token", token);
        axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
      } else {
        localStorage.removeItem("token");
        delete axios.defaults.headers.common["Authorization"];
      }
    },
    // Favourites mutations
    SET_FAVOURITE_PRODUCTS(state, products) {
      state.favouriteProducts = products;
    },
    SET_FAVOURITES_LOADING(state, loading) {
      state.favouritesLoading = loading;
    },
    // Following mutations
    SET_FOLLOWING_BUSINESSES(state, businesses) {
      state.followingBusinesses = businesses;
    },
    SET_FOLLOWING_LOADING(state, loading) {
      state.followingLoading = loading;
    },
    // Reviews mutations
    // SET_USER_REVIEWS(state, reviews) {
    //   state.userReviews = reviews;
    // },
    // SET_REVIEWS_LOADING(state, loading) {
    //   state.reviewsLoading = loading;
    // },
    // Product creation mutations
    SET_PRODUCT_CREATION_LOADING(state, loading) {
      state.productCreation.loading = loading;
    },
    SET_PRODUCT_CREATION_SUCCESS(state, success) {
      state.productCreation.success = success;
    },
    SET_PRODUCT_CREATION_ERROR(state, error) {
      state.productCreation.error = error;
    },
    SET_PRODUCT_CREATION_PRODUCT_ID(state, productId) {
      state.productCreation.productId = productId;
    },
    // Product units and categories mutations
    SET_PRODUCT_UNITS(state, units) {
      state.productUnits = units;
    },
    SET_UNITS_LOADING(state, loading) {
      state.unitsLoading = loading;
    },
    SET_PRODUCT_CATEGORIES(state, categories) {
      state.productCategories = categories;
    },
    SET_CATEGORIES_LOADING(state, loading) {
      state.categoriesLoading = loading;
    },
    // User products mutations
    SET_MY_PRODUCTS(state, products) {
      state.myProducts = Array.isArray(products) ? products : [];
    },
    SET_MY_PRODUCTS_LOADING(state, loading) {
      state.myProductsLoading = !!loading;
    },
    REMOVE_MY_PRODUCT(state, productId) {
      state.myProducts = state.myProducts.filter((p) => p.id !== productId);
    },
    // Messaging mutations
    SET_CONVERSATIONS_LOADING(state, loading) {
      state.conversationsLoading = !!loading;
    },
    SET_CONVERSATIONS(state, data) {
      state.conversations = Array.isArray(data) ? data : [];
      // Auto-calculate global unread count
      state.totalUnreadCount = state.conversations.reduce((acc, conv) => {
        return acc + (conv.unread_count || 0);
      }, 0);
    },
    SET_MESSAGES(state, messages) {
      state.messages = Array.isArray(messages) ? messages : [];
    },
    ADD_MESSAGE(state, message) {
      state.messages = [...state.messages, message];
    },
    UPDATE_CONVERSATION_LAST_MESSAGE(state, { conversationId, message }) {
      // Update last message in conversations
      const conversation = state.conversations.find(
        (c) => c.id === conversationId
      );
      if (conversation) {
        conversation.last_message = message;
      }
    },
    // Optimistic message mutations for instant messaging
    ADD_OPTIMISTIC_MESSAGE(state, message) {
      state.messages = [...state.messages, message];
    },
    REMOVE_OPTIMISTIC_MESSAGE(state, messageId) {
      state.messages = state.messages.filter((m) => m.id !== messageId);
    },
    SET_LAST_MESSAGE_FETCH_TIME(state, timestamp) {
      state.lastMessageFetchTime = timestamp;
    },
    // UPDATE_CONVERSATION_UNREAD_COUNT(state, { conversationId, unreadCount }) {
    //   const conversation = state.conversations.find(
    //     (c) => c.id === conversationId
    //   );
    //   if (conversation) {
    //     conversation.unread_count = unreadCount;
    //   }
    // },
    // index.js mutations
    UPDATE_CONVERSATION_UNREAD(state, { conversationId, count }) {
      const conv = state.conversations.find((c) => c.id === conversationId);
      if (conv) {
        conv.unread_count = count;
        
        // Recalculate global unread count for the Header
        state.totalUnreadCount = state.conversations.reduce((acc, c) => {
          return acc + (c.unread_count || 0);
        }, 0);
      }
    },
    // Inquiry management mutations
    SET_USER_INQUIRIES(state, inquiries) {
      state.userInquiries = inquiries;
    },
    APPEND_USER_INQUIRIES(state, inquiries) {
      state.userInquiries = [...state.userInquiries, ...inquiries];
    },
    SET_INQUIRIES_LOADING(state, loading) {
      state.inquiriesLoading = loading;
    },
    SET_INQUIRIES_LOADING_MORE(state, loading) {
      state.inquiriesLoadingMore = loading;
    },
    SET_INQUIRIES_META(state, meta) {
      state.inquiriesMeta = {
        current_page: meta?.current_page || 1,
        last_page: meta?.last_page || 1,
        total: meta?.total || 0,
      };
    },
    ADD_USER_INQUIRY(state, inquiry) {
      state.userInquiries.unshift(inquiry);
    },
    // Leads management mutations
    SET_LEADS(state, leads) {
      state.leads = Array.isArray(leads) ? leads : [];
    },
    SET_LEADS_LOADING(state, loading) {
      state.leadsLoading = !!loading;
    },
    APPEND_LEADS(state, leads) {
      state.leads = [...state.leads, ...(Array.isArray(leads) ? leads : [])];
    },
    SET_LEADS_META(state, meta) {
      state.leadsMeta = {
        current_page: meta?.current_page || 1,
        last_page: meta?.last_page || 1,
        total: meta?.total || 0,
      };
    },
    SET_LEADS_PARAMS(state, params) {
      state.leadsParams = params || null;
    },
    SET_LEADS_LOADING_MORE(state, loading) {
      state.leadsLoadingMore = !!loading;
    },
    SET_UNLOCKED_LEADS(state, leads) {
      state.unlockedLeads = Array.isArray(leads) ? leads : [];
    },
    APPEND_UNLOCKED_LEADS(state, leads) {
      state.unlockedLeads = [
        ...state.unlockedLeads,
        ...(Array.isArray(leads) ? leads : []),
      ];
    },
    SET_UNLOCKED_LEADS_LOADING(state, loading) {
      state.unlockedLeadsLoading = !!loading;
    },
    SET_UNLOCKED_LEADS_META(state, meta) {
      state.unlockedLeadsMeta = meta || {
        current_page: 1,
        last_page: 1,
        total: 0,
      };
    },
    SET_UNLOCKED_LEADS_LOADING_MORE(state, loading) {
      state.unlockedLeadsLoadingMore = !!loading;
    },
    SET_FAVORITE_LEADS(state, leads) {
      state.favoriteLeads = Array.isArray(leads) ? leads : [];
    },
    SET_FAVORITE_LEADS_LOADING(state, loading) {
      state.favoriteLeadsLoading = !!loading;
    },
    // TOGGLE_LEAD_FAVORITE mutation removed - using isLeadFavorite method instead
    UPDATE_USER_INQUIRY(state, { inquiryId, updates }) {
      const index = state.userInquiries.findIndex(
        (inquiry) => inquiry.id === inquiryId
      );
      if (index !== -1) {
        state.userInquiries[index] = {
          ...state.userInquiries[index],
          ...updates,
        };
      }
    },
    // Keyword tracking mutations
    ADD_SEARCH_KEYWORD(state, keyword) {
      // Remove if already exists to avoid duplicates
      const filteredKeywords = state.searchKeywords.filter(
        (k) => k !== keyword
      );
      // Add to end and keep only last 10 keywords
      state.searchKeywords = [...filteredKeywords, keyword].slice(-10);
      localStorage.setItem(
        "searchKeywords",
        JSON.stringify(state.searchKeywords)
      );
    },
    SET_SEARCH_KEYWORDS(state, keywords) {
      state.searchKeywords = keywords;
      localStorage.setItem("searchKeywords", JSON.stringify(keywords));
    },
  },
  actions: {
    async fetchLocations({ commit }, keyword = "") {
      try {
        const response = await axios.post("/locations", {
          keyword: keyword,
        });
        commit("SET_LOCATIONS", response.data.data);
        return response.data.data;
      } catch (error) {
        console.error("Error fetching locations:", error);
        commit("SET_LOCATIONS", []);
        throw error;
      }
    },
    setSelectedLocation({ commit }, location) {
      commit("SET_SELECTED_LOCATION", location);
    },
    async verifyAndSetLocation({ commit, dispatch, state }, locationName) {
      const selectedLocation = state.selectedLocation;

      // If location is empty or not a string, just clear the selected location
      if (
        !locationName ||
        typeof locationName !== "string" ||
        locationName === "all-bangladesh"
      ) {
        // Only commit if the current location is not already empty
        if (selectedLocation !== "") {
          commit("SET_SELECTED_LOCATION", "");
        }
        return true;
      }

      // If selectedLocation and locationName are the same, no need to do anything
      // Normalize both values for comparison (convert "savar, dhaka" and "savar-dhaka" to "savar dhaka")
      const normalizedSelectedLocation = selectedLocation
        .replace(/[,\-]+/g, "")
        .trim()
        .toLowerCase();
      const normalizedLocationName = locationName
        .replace(/[,\-]+/g, " ")
        .trim()
        .toLowerCase();

      if (normalizedSelectedLocation === normalizedLocationName) {
        return true;
      }

      try {
        // Convert URL-friendly format back to searchable format
        // "Nawabganj-Dhaka" -> "Nawabganj Dhaka"
        const searchKeyword = locationName.replace(/-/g, " ");

        // Search for the location to verify it exists
        const locations = await dispatch("fetchLocations", searchKeyword);

        // Check if any of the returned locations match the provided name
        const matchedLocation = locations.find((loc) => {
          const fullName = loc.upazila_name
            ? `${loc.upazila_name}-${loc.district_name}`
            : `${loc.district_name}-district`;

          // Normalize both fullName and locationName to handle format differences
          const normalizedFullName = fullName
            .replace(/[\s,\-]+/g, " ")
            .trim()
            .toLowerCase();
          const normalizedLocationName = locationName
            .replace(/[\s,\-]+/g, " ")
            .trim()
            .toLowerCase();

          // Exact or fuzzy match comparison
          return normalizedFullName === normalizedLocationName;
        });

        if (matchedLocation) {
          // Location is valid, set the original format in the store
          const originalFormat = matchedLocation.upazila_name
            ? `${matchedLocation.upazila_name}, ${matchedLocation.district_name}`
            : `${matchedLocation.district_name}, District`;

          // Only commit if the location is actually different
          if (selectedLocation !== originalFormat) {
            commit("SET_SELECTED_LOCATION", originalFormat);
          }
          return true;
        } else {
          // Location not found, clear the selected location only if it's not already empty
          if (selectedLocation !== "") {
            commit("SET_SELECTED_LOCATION", "");
          }
          return false;
        }
      } catch (error) {
        console.error("Error verifying location:", error);
        // On error, clear the selected location only if it's not already empty
        if (selectedLocation !== "") {
          commit("SET_SELECTED_LOCATION", "");
        }
        return false;
      }
    },
    async search({ commit, state }, payload) {
      try {
        commit("SET_LOADING", true); // Set loading to true
        const location = state.selectedLocation;
        let params = [];
        if (payload && typeof payload === "object") {
          if (payload.keyword !== undefined)
            params.push(`keyword=${encodeURIComponent(payload.keyword)}`);
          if (payload.business_types && payload.business_types.length)
            params.push(
              `business_types=${encodeURIComponent(payload.business_types)}`
            );
          if (payload.suppliers === true || payload.suppliers === "true")
            params.push("suppliers=true");
          if (payload.category_slugs && payload.category_slugs.length)
            params.push(
              `category_slugs=${encodeURIComponent(payload.category_slugs)}`
            );
        } else {
          params.push(`keyword=${encodeURIComponent(payload)}`);
        }
        // Filter out ", District" suffix if it exists
        const filteredLocation = location
          ? location.replace(/, District$/, "")
          : "";
        params.push(`location=${encodeURIComponent(filteredLocation)}`);
        // Always start from page 1 for a new search
        params.push("page=1");
        const queryString = params.join("&");
        const response = await axios.get(`/search?${queryString}`);
        const list = response?.data?.data?.data || [];
        const meta = response?.data?.data?.meta || {
          current_page: 1,
          last_page: 1,
          total: list.length,
        };
        commit("SET_SEARCH_RESULTS", Array.isArray(list) ? list : []);
        commit("SET_SEARCH_META", meta);
        // Save params (excluding page) for subsequent loads
        const baseParams = {
          ...(typeof payload === "object" ? payload : { keyword: payload }),
          location: filteredLocation,
        };
        commit("SET_SEARCH_PARAMS", baseParams);
        return list;
      } catch (error) {
        console.error("Error fetching search results:", error);
        commit("SET_SEARCH_RESULTS", []);
        commit("SET_SEARCH_META", { current_page: 1, last_page: 1, total: 0 });
        throw error;
      } finally {
        commit("SET_LOADING", false); // Set loading to false
      }
    },
    async loadMoreSearch({ commit, state, getters }) {
      try {
        if (!getters.canLoadMore || state.loadingMore) return [];
        commit("SET_LOADING_MORE", true);
        const nextPage = (state.searchMeta?.current_page || 1) + 1;
        const params = [];
        const p = state.searchParams || {};
        if (p.keyword !== undefined)
          params.push(`keyword=${encodeURIComponent(p.keyword)}`);
        if (p.business_types)
          params.push(`business_types=${encodeURIComponent(p.business_types)}`);
        if (p.suppliers === true || p.suppliers === "true")
          params.push("suppliers=true");
        if (p.category_slugs)
          params.push(`category_slugs=${encodeURIComponent(p.category_slugs)}`);
        const filteredLocation = (
          p.location ||
          state.selectedLocation ||
          ""
        ).replace(/, District$/, "");
        params.push(`location=${encodeURIComponent(filteredLocation)}`);
        params.push(`page=${nextPage}`);
        const queryString = params.join("&");
        const response = await axios.get(`/search?${queryString}`);
        const list = response?.data?.data?.data || [];
        const meta = response?.data?.data?.meta || null;
        commit("APPEND_SEARCH_RESULTS", Array.isArray(list) ? list : []);
        if (meta) commit("SET_SEARCH_META", meta);
        return list;
      } catch (error) {
        console.error("Error loading more search results:", error);
        return [];
      } finally {
        commit("SET_LOADING_MORE", false);
      }
    },
    async fetchBusinessData({ commit }, businessSlug) {
      try {
        commit("SET_LOADING", true);
        const response = await axios.get(`/business/${businessSlug}`);
        commit("SET_BUSINESS_DATA", response.data.data);
        return response.data.data;
      } catch (error) {
        console.error("Error fetching business data:", error);
        commit("SET_BUSINESS_DATA", null);
        throw error;
      } finally {
        commit("SET_LOADING", false);
      }
    },
    async fetchBusinessDetails({ commit }, businessId) {
      try {
        commit("SET_LOADING", true);
        const response = await axios.get(`/business/details/${businessId}`);
        commit("SET_BUSINESS_DETAILS", response.data.data);
        return response.data.data;
      } catch (error) {
        console.error("Error fetching business details:", error);
        commit("SET_BUSINESS_DETAILS", null);
        throw error;
      } finally {
        commit("SET_LOADING", false);
      }
    },
    async fetchBusinessTypes({ commit }) {
      try {
        const response = await axios.get("/all/business/types");
        commit("SET_BUSINESS_TYPES", response.data.data || []);
        return response.data.data || [];
      } catch (error) {
        commit("SET_BUSINESS_TYPES", []);
        throw error;
      }
    },
    async updateBusinessTypes({ commit }, { businessId, businessTypes }) {
      try {
        const response = await axios.put(`/business/${businessId}/types`, {
          business_types: businessTypes,
        });
        return response.data;
      } catch (error) {
        console.error("Error updating business types:", error);
        throw error;
      }
    },
    async fetchProductDetails({ commit }, slug) {
      try {
        commit("SET_LOADING", true); // Set loading to true
        const response = await axios.get(`/product/${slug}`);
        commit("SET_PRODUCT_DETAILS", response.data.data);
        return response.data.data;
      } catch (error) {
        console.error("Error fetching product details:", error);
        commit("SET_PRODUCT_DETAILS", null);
        throw error;
      } finally {
        commit("SET_LOADING", false); // Set loading to false
      }
    },
    // Authentication actions
    async googleLogin({ commit, rootState }, fromRoute = null) {
      try {
        commit("SET_AUTH_LOADING", true);

        // Store the route user came from for redirect after login
        if (fromRoute) {
          localStorage.setItem("googleLoginRedirect", fromRoute);
        }

        // Direct Google OAuth redirect using v2 endpoint
        const clientId =
          "1055841862498-4cfk8ek9i0h2j57p5e582ugftvhiug4f.apps.googleusercontent.com";
        const redirectUri = `${window.location.origin}/user/auth/google/callback`;
        const scope = "email profile";

        const authUrl =
          `https://accounts.google.com/o/oauth2/v2/auth?` +
          `client_id=${clientId}&` +
          `redirect_uri=${encodeURIComponent(redirectUri)}&` +
          `response_type=code&` +
          `scope=${encodeURIComponent(scope)}&` +
          `access_type=offline&` +
          `prompt=consent`;

        window.location.href = authUrl;
      } catch (error) {
        console.error("Error initiating Google login:", error);
        commit("SET_AUTH_LOADING", false);
        throw error;
      }
    },
    async handleGoogleCallback({ commit, dispatch }, { code, state }) {
      try {
        commit("SET_AUTH_LOADING", true);

        // Check if this is for email update
        if (state === "email_update") {
          try {
            // This is for updating email, call the update API
            const response = await axios.put("/user/google/update", { code });

            // Check if the update was successful
            if (response.data.status) {
              // Check if user data is included in the response
              if (response.data.data && response.data.data.user) {
                const { user } = response.data.data;

                // Update user data in store
                commit("SET_USER", user);
              } else if (response.data.user) {
                // Alternative response structure where user is directly in response.data
                commit("SET_USER", response.data.user);
              } else if (
                response.data.data &&
                typeof response.data.data === "object"
              ) {
                // If data exists but no user, it might be a different structure

                // Try to fetch updated user data as fallback
                try {
                  await dispatch("fetchUser");
                } catch (fetchError) {
                  console.warn(
                    "Could not fetch updated user data:",
                    fetchError
                  );
                }
              } else if (!response.data.data) {
                // API returned success but no data - this is valid for some endpoints
                try {
                  await dispatch("fetchUser");
                } catch (fetchError) {
                  console.warn(
                    "Could not fetch updated user data:",
                    fetchError
                  );
                }
              } else {
                // If no user data in response, fetch updated user data
                try {
                  await dispatch("fetchUser");
                } catch (fetchError) {
                  console.warn(
                    "Could not fetch updated user data:",
                    fetchError
                  );
                }
              }

              // Store success message for display on the account details page
              localStorage.setItem(
                "googleEmailUpdateSuccess",
                "Google email address updated successfully!"
              );

              // Redirect back to the original route
              const redirectRoute = localStorage.getItem(
                "googleEmailUpdateRedirect"
              );
              if (redirectRoute) {
                localStorage.removeItem("googleEmailUpdateRedirect");
                window.location.href = redirectRoute;
              }

              return { isEmailUpdate: true, success: true };
            } else {
              // Handle API error response
              const errorMessage =
                response.data.message || "Failed to update email address";
              console.error("API returned error status:", errorMessage);
              throw new Error(errorMessage);
            }
          } catch (updateError) {
            console.error("Error during Google email update:", updateError);

            // Provide more specific error message
            let errorMessage = "Failed to update email address.";
            if (updateError.response?.data?.message) {
              errorMessage = updateError.response.data.message;
            } else if (updateError.message) {
              errorMessage = updateError.message;
            }

            throw new Error(errorMessage);
          }
        } else {
          // This is for regular login
          const response = await axios.post("/user/google/login", { code });

          const { user, token } = response.data.data;

          // Set user and token
          commit("SET_USER", user);
          commit("SET_TOKEN", token);

          return { user, token, isEmailUpdate: false };
        }
      } catch (error) {
        console.error("Error handling Google callback:", error);

        // For email update errors, don't clear user data, just throw the error
        if (state === "email_update") {
          // Store error message for display on the account details page
          localStorage.setItem(
            "googleEmailUpdateError",
            error.response?.data?.message || error.message
          );

          // Redirect back to the original route with error
          const redirectRoute = localStorage.getItem(
            "googleEmailUpdateRedirect"
          );
          if (redirectRoute) {
            localStorage.removeItem("googleEmailUpdateRedirect");
            window.location.href = redirectRoute;
          }
        } else {
          // For regular login errors, clear user data
          commit("SET_USER", null);
          commit("SET_TOKEN", null);
        }

        throw error;
      } finally {
        commit("SET_AUTH_LOADING", false);
      }
    },
    // async sendOTP({ commit }, number) {
    //   try {
    //     commit('SET_AUTH_LOADING', true);
    //     const response = await axios.post('/user/mobile/send/otp', { number });

    //     if (response.data.status) {
    //       return { success: true, message: response.data.message };
    //     } else {
    //       throw new Error(response.data.message || 'Failed to send OTP');
    //     }
    //   } catch (error) {
    //     console.error('Error sending OTP:', error);
    //     throw error;
    //   } finally {
    //     commit('SET_AUTH_LOADING', false);
    //   }
    // },
    async otpLogin({ commit }, { number, otp }) {
      try {
        commit("SET_AUTH_LOADING", true);
        const response = await axios.post("/user/mobile/login", {
          number,
          otp,
        });

        if (response.data.status) {
          const { user, token } = response.data.data;

          // Set user and token
          commit("SET_USER", user);
          commit("SET_TOKEN", token);

          return { user, token, message: response.data.message };
        } else {
          throw new Error(response.data.message || "Login failed");
        }
      } catch (error) {
        console.error("Error during OTP login:", error);
        commit("SET_USER", null);
        commit("SET_TOKEN", null);
        throw error;
      } finally {
        commit("SET_AUTH_LOADING", false);
      }
    },
    async sendOTP({ commit }, phone) {
      try {
        commit("SET_AUTH_LOADING", true);
        const response = await axios.post("/user/mobile/send/otp", {
          number: phone,
        });

        return response.data;
      } catch (error) {
        console.error("Error sending OTP:", error);
        throw error;
      } finally {
        commit("SET_AUTH_LOADING", false);
      }
    },
    async verifyOTP({ commit }, { number, otp, profile_update = false }) {
      try {
        commit("SET_AUTH_LOADING", true);
        const response = await axios.post("/user/mobile/verify/otp", {
          number,
          otp,
          profile_update,
        });

        // If verification is successful, update the user data
        if (response.data.status) {
          // Update user data in store if the response includes updated user info
          if (response.data.data && response.data.data.user) {
            commit("SET_USER", response.data.data.user);
          }
        }

        return response.data;
      } catch (error) {
        console.error("Error during OTP verification:", error);
        throw error;
      } finally {
        commit("SET_AUTH_LOADING", false);
      }
    },
    async logout({ commit }) {
      try {
        // Call the backend logout API to invalidate the session
        await axios.post("/user/logout");
      } catch (error) {
        console.error("Error during logout API call:", error);
        // Continue with local logout even if API call fails
      } finally {
        // Clear local state regardless of API call outcome
        commit("SET_USER", null);
        commit("SET_TOKEN", null);
      }
    },

    // Check if user is authenticated by verifying token
    async checkAuth({ commit, state }) {
      try {
        if (!state.token) {
          return false;
        }

        // Set the token in axios headers
        axios.defaults.headers.common[
          "Authorization"
        ] = `Bearer ${state.token}`;

        // Verify token with backend
        const response = await axios.get("/user/details");
        const user = response.data.data;

        // Update user data if needed
        commit("SET_USER", user);
        return true;
      } catch (error) {
        console.error("Token verification failed:", error);
        // Clear invalid token and user data
        commit("SET_USER", null);
        commit("SET_TOKEN", null);
        return false;
      }
    },

    // Initialize authentication state on app startup
    async initAuth({ dispatch, state }) {
      if (state.token) {
        // Verify token with backend
        const isValid = await dispatch("checkAuth");
        if (!isValid) {
        }
      }
    },

    // Update user profile
    async updateUserProfile({ commit, state }, profileData) {
      try {
        let requestData = profileData;
        if (profileData && profileData.profile instanceof File) {
          const formData = new FormData();
          formData.append("profile", profileData.profile);
          requestData = formData;
          // If you want to keep PUT semantics, add _method
          requestData.append("_method", "PUT");
        }
        const response = await axios.post("/user/profile", requestData);

        // Update user data in store if the response includes updated user info
        if (response.data.data) {
          commit("SET_USER", response.data.data);
        } else {
          // If backend doesn't return updated user data, update locally
          const updatedUser = { ...state.user };
          if (profileData && profileData.profile) {
            updatedUser.profile = state.user?.profile; // keep previous if backend doesn't return new URL
          }
          commit("SET_USER", updatedUser);
        }

        return response.data;
      } catch (error) {
        return error.response.data;
      }
    },

    // Authenticate with Google for email update
    async authenticateWithGoogle({ commit, state }) {
      try {
        commit("SET_AUTH_LOADING", true);

        // Store the current route for redirect after Google auth
        const currentRoute = window.location.pathname;
        localStorage.setItem("googleEmailUpdateRedirect", currentRoute);

        // Direct Google OAuth redirect using v2 endpoint
        const clientId =
          "1055841862498-4cfk8ek9i0h2j57p5e582ugftvhiug4f.apps.googleusercontent.com";
        const redirectUri = `${window.location.origin}/user/auth/google/callback`;
        const scope = "email profile";

        const authUrl =
          `https://accounts.google.com/o/oauth2/v2/auth?` +
          `client_id=${clientId}&` +
          `redirect_uri=${encodeURIComponent(redirectUri)}&` +
          `response_type=code&` +
          `scope=${encodeURIComponent(scope)}&` +
          `access_type=offline&` +
          `prompt=consent&` +
          `state=email_update`;

        window.location.href = authUrl;
      } catch (error) {
        console.error(
          "Error initiating Google authentication for email update:",
          error
        );
        commit("SET_AUTH_LOADING", false);
        throw error;
      }
    },

    // Fetch user details
    async fetchUser({ commit }) {
      try {
        const response = await axios.get("/user/details");
        if (response.data.data) {
          commit("SET_USER", response.data.data);
        }
        return response.data.data;
      } catch (error) {
        console.error("Error fetching user details:", error);
        throw error;
      }
    },
    // Favourites actions
    async fetchFavouriteProducts({ commit, state }) {
      try {
        // Only fetch if authenticated
        if (!state.isAuthenticated || !state.token) {
          commit("SET_FAVOURITE_PRODUCTS", []);
          return [];
        }
        commit("SET_FAVOURITES_LOADING", true);
        const response = await axios.get("/user/favourites");
        commit("SET_FAVOURITE_PRODUCTS", response.data.data);
        return response.data.data;
      } catch (error) {
        console.error("Error fetching favourite products:", error);
        commit("SET_FAVOURITE_PRODUCTS", []);
        throw error;
      } finally {
        commit("SET_FAVOURITES_LOADING", false);
      }
    },
    async addToFavourites({ commit, state }, productId) {
      try {
        // Only call if authenticated
        if (!state.isAuthenticated || !state.token) {
          push.error("Not authenticated")
          return state.favouriteProducts;
        }
        const response = await axios.post("/user/favourites", {
          product_id: productId,
        });
        // Update favourites with the response data
        if (response.data.data) {
          push.success("Added to favourites");
          commit("SET_FAVOURITE_PRODUCTS", response.data.data);
        }
        return response.data.data;
      } catch (error) {
        console.error("Error adding to favourites:", error);
        throw error;
      }
    },
    async removeFromFavourites({ commit, state }, productId) {
      try {
        // Only call if authenticated
        if (!state.isAuthenticated || !state.token) {
          push.error("Not authenticated")
          return { status: false, message: "Not authenticated" };
        }
        const response = await axios.delete(`/user/favourites/${productId}`);
        // Remove the product from favourites if delete was successful
        if (response.data.status) {
          const updatedFavourites = state.favouriteProducts.filter(
            (product) => product.id !== productId
          );
          push.success("Removed from favourites");
          commit("SET_FAVOURITE_PRODUCTS", updatedFavourites);
        }
        return response.data;
      } catch (error) {
        console.error("Error removing from favourites:", error);
        throw error;
      }
    },
    // Following actions
    async fetchFollowingBusinesses({ commit, state }) {
      try {
        // Only fetch if authenticated
        if (!state.isAuthenticated || !state.token) {
          commit("SET_FOLLOWING_BUSINESSES", []);
          return [];
        }
        commit("SET_FOLLOWING_LOADING", true);
        const response = await axios.get("/user/followings");
        commit("SET_FOLLOWING_BUSINESSES", response.data.data || []);
        return response.data.data;
      } catch (error) {
        console.error("Error fetching following businesses:", error);
        commit("SET_FOLLOWING_BUSINESSES", []);
        throw error;
      } finally {
        commit("SET_FOLLOWING_LOADING", false);
      }
    },
    async addToFollowing({ commit, state }, businessId) {
      try {
        // Only call if authenticated
        if (!state.isAuthenticated || !state.token) {
          push.error("Not authenticated")
          return;
        }
        
        if(businessId == state.user?.business?.id){
          push.warning("Cannot follow your own business")
          return;
        }
        
        const response = await axios.post("/user/followings", {
          business_id: businessId,
        });
        // Update following with the response data
        if (response.data.data) {
          push.success("Added to following");
          commit("SET_FOLLOWING_BUSINESSES", response.data.data);
        }
        return response.data.data;
      } catch (error) {
        console.error("Error adding to following:", error);
        throw error;
      }
    },
    async removeFromFollowing({ commit, state }, businessId) {
      try {
        // Only call if authenticated
        if (!state.isAuthenticated || !state.token) {
          push.error("Not authenticated")
          return;
        }
        const response = await axios.delete(`/user/followings/${businessId}`);
        // Remove the business from following if delete was successful
        if (response.data.status) {
          const updatedFollowing = state.followingBusinesses.filter(
            (business) => business.id !== businessId
          );
          push.success("Removed from following");
          commit("SET_FOLLOWING_BUSINESSES", updatedFollowing);
        }
        return response.data;
      } catch (error) {
        console.error("Error removing from following:", error);
        throw error;
      }
    },
    // Reviews actions
    // Removed: fetchUserReviews action - now handled by useUserReviews composable
    async fetchBusinessLocations({ commit }, keyword = "") {
      try {
        const response = await axios.post("/user/business/locations", {
          keyword,
        });
        commit("SET_BUSINESS_LOCATIONS", response.data.data || []);
        return response.data.data || [];
      } catch (error) {
        commit("SET_BUSINESS_LOCATIONS", []);
        throw error;
      }
    },

    // Fetch user's business data
    async fetchMyBusiness({ commit }) {
      try {
        const response = await axios.get("/user/my/business");
        commit("SET_MY_BUSINESS", response.data.data);
        return response.data.data;
      } catch (error) {
        console.error("Error fetching my business:", error);
        commit("SET_MY_BUSINESS", null);
        throw error;
      }
    },

    // Fetch user's business details
    async fetchMyBusinessDetails({ commit }) {
      try {
        const response = await axios.get("/user/business/details");
        commit("SET_MY_BUSINESS_DETAILS", response.data.data);
        return response.data.data;
      } catch (error) {
        console.error("Error fetching my business details:", error);
        commit("SET_MY_BUSINESS_DETAILS", null);
        throw error;
      }
    },

    // Update user's business details
    async updateBusinessDetails({ commit }, details) {
      try {
        const response = await axios.put("/user/business/details", details);
        if (response.data.status) {
          // Update the store with new details
          commit("SET_MY_BUSINESS_DETAILS", response.data.data);
        }
        return response.data;
      } catch (error) {
        console.error("Error updating business details:", error);
        throw error;
      }
    },

    // Update business information using the main business update endpoint
    async updateBusinessInfo({ commit, state }, updateData) {
      try {
        // Get current business data to merge with updates
        const currentBusiness = state.myBusiness;
        if (!currentBusiness) {
          throw new Error("No business data available");
        }

        // Prepare the update payload with all required fields
        const updatePayload = {
          name: currentBusiness.name || "",
          business_name: currentBusiness.business_name || "",
          number: currentBusiness.number || "",
          alternate_number: currentBusiness.alternate_number || "",
          email: state.myBusinessDetails?.email || currentBusiness.email || "",
          address: currentBusiness.address || "",
          post_code: currentBusiness.post_code || "",
          location_id: currentBusiness.location?.id || null,
          business_type: currentBusiness.business_type || [],
        };

        // Merge with the provided update data
        Object.assign(updatePayload, updateData);

        const response = await axios.put(
          "/user/my/business/update",
          updatePayload
        );

        if (response.data.status) {
          // Update the store with new business data
          commit("SET_MY_BUSINESS", response.data.data);

          // Also update business details if available
          if (state.myBusinessDetails) {
            commit("SET_MY_BUSINESS_DETAILS", response.data.data);
          }
        }

        return response.data;
      } catch (error) {
        console.error("Error updating business information:", error);
        throw error;
      }
    },

    // Update business profile picture
    async updateBusinessProfile({ commit, state }, profileData) {
      try {
        // If profileData contains a File object, use FormData
        let requestData = profileData;
        let config = {};

        if (profileData.business_profile instanceof File) {
          const formData = new FormData();
          formData.append("business_profile", profileData.business_profile);
          formData.append("_method", "PUT");
          requestData = formData;
          // Don't set Content-Type header for FormData, let browser set it with boundary
        }

        const response = await axios.post(
          "/user/business/profile",
          requestData,
          config
        );
        commit("SET_MY_BUSINESS", response.data.data);

        return response.data.data;
      } catch (error) {
        console.error("Error updating business profile:", error);
        throw error;
      }
    },
    // Search product units with keyword
    async searchProductUnits({ commit }, keyword = "") {
      try {
        commit("SET_UNITS_LOADING", true);
        const response = await axios.post("/user/business/product/units", {
          keyword,
        });
        commit("SET_PRODUCT_UNITS", response.data.data || []);
        return response.data.data || [];
      } catch (error) {
        console.error("Error searching product units:", error);
        commit("SET_PRODUCT_UNITS", []);
        throw error;
      } finally {
        commit("SET_UNITS_LOADING", false);
      }
    },
    // Search product categories with keyword
    async searchProductCategories({ commit }, keyword = "") {
      try {
        commit("SET_CATEGORIES_LOADING", true);
        const response = await axios.post("/user/business/product/categories", {
          keyword,
        });
        commit("SET_PRODUCT_CATEGORIES", response.data.data || []);
        return response.data.data || [];
      } catch (error) {
        console.error("Error searching product categories:", error);
        commit("SET_PRODUCT_CATEGORIES", []);
        throw error;
      } finally {
        commit("SET_CATEGORIES_LOADING", false);
      }
    },

    // Search general product categories (for leads)
    async searchGeneralCategories({ commit }, keyword = "") {
      try {
        commit("SET_CATEGORIES_LOADING", true);
        // Try general categories endpoint first
        let response;
        try {
          response = await axios.get("/categories", { params: { keyword } });
        } catch (error) {
          // Fallback to business categories if general endpoint doesn't exist
          response = await axios.post("/user/business/product/categories", {
            keyword,
          });
        }
        commit("SET_PRODUCT_CATEGORIES", response.data.data || []);
        return response.data.data || [];
      } catch (error) {
        console.error("Error searching general categories:", error);
        commit("SET_PRODUCT_CATEGORIES", []);
        throw error;
      } finally {
        commit("SET_CATEGORIES_LOADING", false);
      }
    },

    // Fetch business categories
    async fetchBusinessCategories({ commit }) {
      try {
        commit("SET_CATEGORIES_LOADING", true);
        const response = await axios.get("/user/business/categories");
        return response.data;
      } catch (error) {
        console.error("Error fetching business categories:", error);
        throw error;
      } finally {
        commit("SET_CATEGORIES_LOADING", false);
      }
    },
    // Upload product images
    async uploadProductImages({ commit, state }, { productId, images }) {
      try {
        commit("SET_PRODUCT_CREATION_LOADING", true);

        const uploadPromises = images.map(async (image, index) => {
          try {
            let requestData;

            // If image is a File object, use FormData
            if (image instanceof File) {
              const formData = new FormData();
              formData.append("product_id", productId);
              formData.append("image", image);
              requestData = formData;
            } else {
              // If image is a base64 string (for backward compatibility), convert to FormData
              // This handles the case where existing code might still pass base64
              const formData = new FormData();
              formData.append("product_id", productId);
              formData.append("image", image);
              requestData = formData;
            }

            const response = await axios.post(
              "/user/product/gallery",
              requestData
            );

            if (!response.data.status) {
              throw new Error(`Failed to upload image ${index + 1}`);
            }

            return response.data.data;
          } catch (error) {
            console.error(`Store: Error uploading image ${index + 1}:`, error);
            throw error;
          }
        });

        const results = await Promise.all(uploadPromises);
        return results;
      } catch (error) {
        console.error("Store: Error uploading product images:", error);
        commit(
          "SET_PRODUCT_CREATION_ERROR",
          error.message || "Failed to upload images"
        );
        throw error;
      } finally {
        commit("SET_PRODUCT_CREATION_LOADING", false);
      }
    },

    // Update product image positions
    async updateProductImagePositions({ commit }, { productId, imageIds }) {
      try {
        const response = await axios.put("/user/product/gallery/positions", {
          product_id: productId,
          image_ids: imageIds,
        });

        if (!response.data.status) {
          throw new Error(
            response.data.message || "Failed to update image positions"
          );
        }

        return response.data.data;
      } catch (error) {
        console.error("Store: Error updating product image positions:", error);
        throw error;
      }
    },

    // Upload business gallery images
    async uploadBusinessGalleryImages({ commit }, { images }) {
      try {
        const uploadPromises = images.map(async (image, index) => {
          try {
            let requestData;

            // If image is a File object, use FormData
            if (image instanceof File) {
              const formData = new FormData();
              formData.append("image", image);
              requestData = formData;
            } else {
              // If image is a base64 string (for backward compatibility), convert to FormData
              // This handles the case where existing code might still pass base64
              const formData = new FormData();
              formData.append("image", image);
              requestData = formData;
            }

            const response = await axios.post(
              "/user/business/gallery",
              requestData
            );

            if (!response.data.status) {
              throw new Error(`Failed to upload image ${index + 1}`);
            }

            return response.data.data;
          } catch (error) {
            console.error(
              `Error uploading business image ${index + 1}:`,
              error
            );
            throw error;
          }
        });

        const results = await Promise.all(uploadPromises);
        return results;
      } catch (error) {
        console.error("Error uploading business gallery images:", error);
        throw error;
      }
    },

    // Update business video URL
    async updateBusinessVideo({ commit }, { video_url }) {
      try {
        const response = await axios.post("/user/business/video", {
          video_url: video_url,
        });

        if (!response.data.status) {
          throw new Error(
            response.data.message || "Failed to update video URL"
          );
        }

        return response.data.data;
      } catch (error) {
        console.error("Error updating business video:", error);
        throw error;
      }
    },
    // Store product with all data
    async storeProduct({ commit, dispatch }, productData) {
      try {
        commit("SET_PRODUCT_CREATION_LOADING", true);
        commit("SET_PRODUCT_CREATION_ERROR", null);

        const response = await axios.post("/user/products", productData);

        if (response.data.status) {
          commit("SET_PRODUCT_CREATION_SUCCESS", true);
          commit("SET_PRODUCT_CREATION_PRODUCT_ID", response.data.data.id);
          return response.data.data;
        } else {
          commit(
            "SET_PRODUCT_CREATION_ERROR",
            response.data.message || "Failed to create product"
          );
          throw new Error(response.data.message || "Failed to create product");
        }
      } catch (error) {
        console.error("Error storing product:", error);
        commit(
          "SET_PRODUCT_CREATION_ERROR",
          error.message || "Failed to create product"
        );
        throw error;
      } finally {
        commit("SET_PRODUCT_CREATION_LOADING", false);
      }
    },
    // Fetch user's products
    async fetchMyProducts({ commit }) {
      try {
        commit("SET_MY_PRODUCTS_LOADING", true);
        const response = await axios.get("/user/products");
        const products = response?.data?.data || [];
        commit("SET_MY_PRODUCTS", products);
        return products;
      } catch (error) {
        console.error("Error fetching my products:", error);
        commit("SET_MY_PRODUCTS", []);
        throw error;
      } finally {
        commit("SET_MY_PRODUCTS_LOADING", false);
      }
    },
    // Delete a user's product
    async deleteUserProduct({ commit }, productId) {
      try {
        const response = await axios.delete(`/user/products/${productId}`);
        if (response?.data?.status) {
          commit("REMOVE_MY_PRODUCT", productId);
        }
        return response?.data;
      } catch (error) {
        console.error("Error deleting product:", error);
        throw error;
      }
    },
    // Fetch a single product by ID
    async fetchProduct({ commit }, productId) {
      try {
        const response = await axios.get(`/user/products/${productId}`);
        return response?.data?.data;
      } catch (error) {
        console.error("Error fetching product:", error);
        throw error;
      }
    },
    // Update a product
    async updateProduct({ commit }, { productId, productData }) {
      try {
        const response = await axios.put(
          `/user/products/${productId}`,
          productData
        );
        return response?.data?.data;
      } catch (error) {
        console.error("Error updating product:", error);
        throw error;
      }
    },

    // Update user location
    async updateUserLocation({ commit }, locationData) {
      try {
        const response = await axios.put("/user/location", locationData);

        if (response.data.status && response.data.data) {
          // Update user data in store with the returned user data
          commit("SET_USER", response.data.data);
        }

        return response.data;
      } catch (error) {
        console.error("Error updating user location:", error);
        throw error;
      }
    },

    // Messaging actions
    async startConversation(
      { commit, state },
      { business_id, user_id = state?.user?.id, role = "user" }
    ) {
      try {
        // Only call if authenticated
        if (!state.isAuthenticated || !state.token) {
          push.error("Not authenticated")
          return;
        }
        if(role != "business" && business_id == state.user?.business?.id){
          push.error("Cannot start conversation with your own business")
          return;
        }
        const payload = { business_id };
        // Pass optional fields for backend to log context; backend may ignore unknown fields safely
        // user_id is redundant with auth but passed per requirement/spec
        if (user_id) payload.user_id = user_id;
        const response = await axios.post("/user/conversations/start", payload);
        const conversation = response?.data?.data;
        
        return response?.data;
      } catch (error) {
        console.error("Error starting conversation:", error);
        throw error;
      }
    },
    async fetchConversations({ commit }) {
      commit("SET_CONVERSATIONS_LOADING", true);
      try {
        const response = await axios.get("/user/conversations");
        commit("SET_CONVERSATIONS", response.data.data);
      } finally {
        commit("SET_CONVERSATIONS_LOADING", false);
      }
    },

    async fetchConversationMessages({ commit }, payload) {
  // Support both old style (id only) and new style ({conversationId, signal})
  const conversationId = typeof payload === 'object' ? payload.conversationId : payload;
  const signal = payload?.signal || null;

  try {
    // Note: We removed the 'lastMessageFetchTime' check so it always attempts 
    // to fetch when the UI requests a specific conversation switch.

    const response = await axios.get(
      `/user/conversations/${conversationId}/messages`,
      { signal } // Pass the abort signal to axios
    );
    
    const newMessages = response?.data?.data || [];
    commit("SET_MESSAGES", newMessages);
    return newMessages;
  } catch (error) {
    // If the error is an intentional abort, don't clear the messages or log error
    if (axios.isCancel(error) || error.name === 'AbortError') {
      return; 
    }
    
    console.error("Error fetching messages:", error);
    commit("SET_MESSAGES", []);
    throw error;
  }
},
    async sendConversationMessage(
      { commit },
      { conversationId, message, attachments = [] }
    ) {
      try {
        const formData = new FormData();
        formData.append("message", message);

        // Add attachments to form data
        if (attachments && attachments.length > 0) {
          attachments.forEach((file, index) => {
            formData.append(`attachments[${index}]`, file);
          });
        }

        const response = await axios.post(
          `/user/conversations/${conversationId}/messages`,
          formData,
          {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          }
        );
        const data = response?.data?.data;
        if (data) {
          commit("ADD_MESSAGE", data);
          // Update the conversation's last message
          commit("UPDATE_CONVERSATION_LAST_MESSAGE", {
            conversationId,
            message: data,
          });
        }
        return data;
      } catch (error) {
        console.error("Error sending message:", error);
        throw error;
      }
    },

    // Inquiry management actions
    async createInquiry({ commit }, inquiryData) {
      try {
        const response = await axios.post("/user/inquiries", inquiryData);
        const newInquiry = response.data.data;
        commit("ADD_USER_INQUIRY", newInquiry);
        return newInquiry;
      } catch (error) {
        console.error("Error creating inquiry:", error);
        throw error;
      }
    },

    async fetchUserInquiries({ commit }) {
      try {
        commit("SET_INQUIRIES_LOADING", true);
        const response = await axios.get("/user/inquiries", {
          params: { page: 1 },
        });
        const list = response.data.data?.data || response.data.data || [];
        const meta = response.data.data?.meta || {
          current_page: 1,
          last_page: 1,
          total: Array.isArray(list) ? list.length : 0,
        };
        commit("SET_USER_INQUIRIES", Array.isArray(list) ? list : []);
        commit("SET_INQUIRIES_META", meta);
        return list;
      } catch (error) {
        console.error("Error fetching user inquiries:", error);
        commit("SET_USER_INQUIRIES", []);
        commit("SET_INQUIRIES_META", {
          current_page: 1,
          last_page: 1,
          total: 0,
        });
        throw error;
      } finally {
        commit("SET_INQUIRIES_LOADING", false);
      }
    },
    async loadMoreUserInquiries({ commit, state, getters }) {
      try {
        if (!getters.inquiriesCanLoadMore || state.inquiriesLoadingMore)
          return [];
        commit("SET_INQUIRIES_LOADING_MORE", true);
        const nextPage = (state.inquiriesMeta?.current_page || 1) + 1;
        const response = await axios.get("/user/inquiries", {
          params: { page: nextPage },
        });
        const list = response.data.data?.data || response.data.data || [];
        const meta = response.data.data?.meta;
        if (Array.isArray(list) && list.length) {
          commit("APPEND_USER_INQUIRIES", list);
        }
        if (meta) commit("SET_INQUIRIES_META", meta);
        return list;
      } catch (error) {
        console.error("Error loading more user inquiries:", error);
        return [];
      } finally {
        commit("SET_INQUIRIES_LOADING_MORE", false);
      }
    },

    async closeInquiry({ commit }, inquiryId) {
      try {
        const response = await axios.post(`/user/inquiries/${inquiryId}/close`);
        commit("UPDATE_USER_INQUIRY", {
          inquiryId,
          updates: { status: "closed" },
        });
        return response.data.data;
      } catch (error) {
        console.error("Error closing inquiry:", error);
        throw error;
      }
    },

    // Leads management actions
    async fetchLeads({ commit }, filters = {}) {
      try {
        commit("SET_LEADS_LOADING", true);
        const params = new URLSearchParams();
        if (filters.category_ids)
          params.append("category_ids", filters.category_ids);
        if (filters.location) params.append("location", filters.location);
        if (filters.keyword) params.append("keyword", filters.keyword);
        if (filters.sort_by) params.append("sort_by", filters.sort_by);
        params.append("page", "1");
        const response = await axios.get(
          `/user/business/leads?${params.toString()}`
        );
        const list = response?.data?.data?.data || response?.data?.data || [];
        const meta = response?.data?.data?.meta || {
          current_page: 1,
          last_page: 1,
          total: Array.isArray(list) ? list.length : 0,
        };
        commit("SET_LEADS", Array.isArray(list) ? list : []);
        commit("SET_LEADS_META", meta);
        // Save base params excluding page
        commit("SET_LEADS_PARAMS", {
          category_ids: filters.category_ids || "",
          location: filters.location || "",
          keyword: filters.keyword || "",
          sort_by: filters.sort_by || "",
        });
        return { status: true, data: list, meta };
      } catch (error) {
        console.error("Error fetching leads:", error);
        commit("SET_LEADS", []);
        commit("SET_LEADS_META", { current_page: 1, last_page: 1, total: 0 });
        throw error;
      } finally {
        commit("SET_LEADS_LOADING", false);
      }
    },
    async loadMoreLeads({ commit, state, getters }) {
      try {
        if (!getters.leadsCanLoadMore || state.leadsLoadingMore) return [];
        commit("SET_LEADS_LOADING_MORE", true);
        const nextPage = (state.leadsMeta?.current_page || 1) + 1;
        const params = new URLSearchParams();
        const p = state.leadsParams || {};
        if (p.category_ids) params.append("category_ids", p.category_ids);
        if (p.location) params.append("location", p.location);
        if (p.keyword) params.append("keyword", p.keyword);
        if (p.sort_by) params.append("sort_by", p.sort_by);
        params.append("page", String(nextPage));
        const response = await axios.get(
          `/user/business/leads?${params.toString()}`
        );
        const list = response?.data?.data?.data || response?.data?.data || [];
        const meta = response?.data?.data?.meta || null;
        commit("APPEND_LEADS", Array.isArray(list) ? list : []);
        if (meta) commit("SET_LEADS_META", meta);
        return list;
      } catch (error) {
        console.error("Error loading more leads:", error);
        return [];
      } finally {
        commit("SET_LEADS_LOADING_MORE", false);
      }
    },

    async fetchUnlockedLeads({ commit }) {
      try {
        commit("SET_UNLOCKED_LEADS_LOADING", true);
        const response = await axios.get("/user/business/my/leads", {
          params: { page: 1 },
        });

        // Handle paginated response structure
        const leads = response.data.data?.data || response.data.data || [];
        const meta = response.data.data?.meta ||
          response.data.meta || {
            current_page: 1,
            last_page: 1,
            total: leads.length,
          };

        commit("SET_UNLOCKED_LEADS", leads);
        commit("SET_UNLOCKED_LEADS_META", meta);
        return response.data;
      } catch (error) {
        console.error("Error fetching unlocked leads:", error);
        commit("SET_UNLOCKED_LEADS", []);
        commit("SET_UNLOCKED_LEADS_META", {
          current_page: 1,
          last_page: 1,
          total: 0,
        });
        throw error;
      } finally {
        commit("SET_UNLOCKED_LEADS_LOADING", false);
      }
    },

    async loadMoreUnlockedLeads({ commit, state }) {
      if (
        state.unlockedLeadsLoadingMore ||
        !state.unlockedLeadsMeta ||
        state.unlockedLeadsMeta.current_page >=
          state.unlockedLeadsMeta.last_page
      ) {
        return;
      }

      try {
        commit("SET_UNLOCKED_LEADS_LOADING_MORE", true);
        const nextPage = (state.unlockedLeadsMeta.current_page || 1) + 1;

        const response = await axios.get("/user/business/my/leads", {
          params: { page: nextPage },
        });

        // Handle paginated response structure
        const newLeads = response.data.data?.data || response.data.data || [];
        const meta =
          response.data.data?.meta ||
          response.data.meta ||
          state.unlockedLeadsMeta;

        commit("APPEND_UNLOCKED_LEADS", newLeads);
        commit("SET_UNLOCKED_LEADS_META", meta);
        return response.data;
      } catch (error) {
        console.error("Error loading more unlocked leads:", error);
        throw error;
      } finally {
        commit("SET_UNLOCKED_LEADS_LOADING_MORE", false);
      }
    },

    async fetchFavoriteLeads({ commit }) {
      try {
        commit("SET_FAVORITE_LEADS_LOADING", true);
        const response = await axios.get("/user/business/leads/favourites");
        const leads = response.data.data || [];
        commit("SET_FAVORITE_LEADS", leads);
        return response.data;
      } catch (error) {
        console.error("Error fetching favorite leads:", error);
        commit("SET_FAVORITE_LEADS", []);
        throw error;
      } finally {
        commit("SET_FAVORITE_LEADS_LOADING", false);
      }
    },

    async unlockLeadContact({ commit }, leadId) {
      try {
        const response = await axios.post(
          `/user/business/leads/${leadId}/unlock`
        );
        return response.data;
      } catch (error) {
        console.error("Error unlocking lead contact:", error);
        throw error;
      }
    },

    async toggleLeadFavorite({ commit }, leadId) {
      try {
        const response = await axios.post(
          `/user/business/leads/${leadId}/favourite`
        );
        return response.data;
      } catch (error) {
        console.error("Error toggling lead favorite status:", error);
        throw error;
      }
    },

    // Keyword tracking actions
    trackSearchKeyword({ commit }, keyword) {
      if (keyword && keyword.trim()) {
        commit("ADD_SEARCH_KEYWORD", keyword.trim());
      }
    },

    trackProductCategory({ commit }, categoryName) {
      if (categoryName && categoryName.trim()) {
        commit("ADD_SEARCH_KEYWORD", categoryName.trim());
      }
    },

    // Fetch suggested categories based on user's search history
    async fetchSuggestedCategories({ commit, state }) {
      try {
        const keywords = state.searchKeywords.slice(-5); // Get last 5 keywords
        const response = await axios.get("/suggested/categories", {
          params: { keywords: keywords },
        });
        return response.data;
      } catch (error) {
        console.error("Error fetching suggested categories:", error);
        throw error;
      }
    },
    
    // Check slug availability
    async checkSlugAvailability({ commit }, { slug }) {
      try {
        const response = await axios.post('/user/business/slug/check', { slug });
        return response.data;
      } catch (error) {
        console.error('Error checking slug availability:', error);
        throw error;
      }
    },
  },

  modules: {},
});
