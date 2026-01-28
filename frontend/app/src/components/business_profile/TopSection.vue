<template>
  <div>
    <div class="container mx-auto">
      <div class="bg-white rounded-xl shadow p-4 mt-4">
        <div
          class="flex flex-col md:flex-row md:items-start gap-2 md:gap-4 justify-between"
        >
          <!-- Left Side: Image and Info -->
          <div class="flex items-start gap-4">
            <!-- Business Image -->
            <!-- <img :src="businessData?.business_profile || 'https://placehold.co/150'"
                            :alt="businessData?.business_name || 'Business'"
                            class="w-20 h-20 rounded-lg object-cover border flex-shrink-0" /> -->
            <div class="flex flex-col max-w-40 justify-center gap-2">
              <div
                class="rounded-lg border content-center bg-green-50 max-h-44 max-w-44 md:max-h-52 md:max-w-52 product-image overflow-hidden"
              >
                <img
                  :src="
                    businessData.business_profile ||
                    'https://placehold.co/200x200'
                  "
                  :alt="businessData.business_name"
                  class="w-full h-full object-cover transition-transform duration-500 ease-in-out transform group-hover:scale-110"
                />
              </div>
              <VerifiedBadge
                :accountVerified="businessData.account_verification"
                class="md:hidden mt-2"
              />
              <div class="flex gap-2 justify-center md:hidden">
                <button
                  @click="toggleFollow"
                  class="p-1.5 border rounded-md transition duration-200 ease-in-out bg-primary/10 hover:bg-primary/20"
                  :disabled="followingLoading"
                >
                  <img
                    src="@/assets/icons/svgs/FollowStoreIcon.svg"
                    alt="Follow Store"
                    class="h-4 md:h-6 transition"
                    :class="{
                      'filter-primary': isFollowing,
                    }"
                  />
                </button>

                <button
                  @click="shareBusiness"
                  class="p-1.5 border rounded-md transition hover:bg-primary/20 duration-200 ease-in-out bg-gray-200"
                >
                  <ShareIcon class="h-4 md:h-6" />
                </button>
              </div>
            </div>
            <!-- Main Info -->
            <div class="md:flex-1 min-w-0">
              <div class="flex items-center gap-1 mb-2">
                <h2 class="text-xl md:text-2xl font-medium capitalize">
                  {{ businessData?.business_name || "Business Name" }}
                </h2>
                <!-- <VerifiedBadge :accountVerified="businessData?.account_verification" /> -->
                <VerifiedBadge
                  :accountVerified="businessData?.account_verification"
                  class="hidden md:block"
                />
              </div>
              <div class="flex items-center gap-1 flex-wrap mb-3">
                <div
                  class="flex bg-primary font-semibold px-1.5 py-0.5 rounded-md items-center"
                >
                  <span class="text-white text-lg">{{
                    businessData?.rating?.rate || "0.0"
                  }}</span>
                  <StarIcon class="text-yellow-400 h-5 pl-1" />
                </div>
                <span class="text-sm text-gray-600 ml-1"
                  >{{ businessData?.rating?.count || 0 }} Ratings</span
                >
                <div class="flex flex-wrap items-center gap-1">
                  <span
                    v-for="type in businessData?.business_type"
                    :key="type"
                    class="bg-primary/10 font-semibold py-0.5 rounded px-1 text-xs capitalize"
                    >{{ type }}</span
                  >
                </div>
              </div>
              <div class="flex flex-wrap items-center gap-2 text-sm mb-3">
                <span class="flex items-center">
                  <MapPinIcon class="inline-block h-5 text-gray-800 -mt-0.5" />
                  {{ businessData?.location?.upazila_name }},
                  {{ businessData?.location?.district_name }}
                </span>
                <span v-if="businessDetails?.timing"
                  ><span class="text-gray-300"> • </span
                  ><span
                    :class="
                      getCurrentDayTiming().toLowerCase() == 'closed'
                        ? 'text-red-600'
                        : 'text-green-600'
                    "
                  >
                    {{ getCurrentDayTiming() }}</span
                  ></span
                >
                <span v-if="businessDetails?.in_business">
                  <span class="text-gray-300"> • </span
                  >{{ businessDetails.in_business }} Years in Business</span
                >
              </div>

              <div class="hidden md:flex items-center gap-2 flex-wrap">
                <button
                  class="bg-primary text-white gap-1 py-2 rounded-md flex px-2 text-sm font-medium hover:bg-primary/85 transition-all"
                  @click.stop.prevent="callBusiness(businessData?.number)"
                >
                  <PhoneIcon class="inline-block h-4" />{{
                    businessData?.number || "N/A"
                  }}
                </button>
                <button
                  @click="openWhatsApp"
                  class="text-primary gap-1 py-[5px] rounded-md items-center flex px-2 text-sm font-medium border-primary border hover:bg-primary/15 transition-all"
                >
                  <img
                    src="@/assets/icons/svgs/WhatsappIcon.svg"
                    class="w-6"
                    alt="WhatsApp"
                  />
                  WhatsApp
                </button>
                <button
                  @click="startBestPriceChat"
                  class="text-primary gap-1 py-[7px] rounded-md items-center flex px-3 text-sm font-medium border-primary border hover:bg-primary/15 transition-all"
                >
                  <ChatBubbleLeftRightIcon class="w-5 h-5 mr-1" />
                  Message
                </button>
                <a
                  :href="businessDetails?.facebook"
                  target="_blank"
                  v-if="businessDetails?.facebook"
                  class="text-primary gap-1 py-[5px] rounded-md items-center flex px-2 text-sm font-medium border-primary border hover:bg-primary/15 transition-all"
                >
                  <img
                    src="@/assets/icons/svgs/FacebookIcon.svg"
                    class="w-6"
                    alt="facebook"
                  />
                  Facebook
                </a>
                <a
                  :href="businessDetails?.website"
                  target="_blank"
                  v-if="businessDetails?.website"
                  class="bg-red-500 text-white gap-1 py-2 rounded-md flex px-2 text-sm font-medium hover:bg-red-400 transition-all"
                >
                  <GlobeAltIcon class="inline-block h-5" />Visit Website
                </a>
                <!-- <button class="bg-blue-600 text-white gap-1 py-2 rounded-md flex px-2 text-sm font-medium hover:bg-blue-500 transition-all" 
                            @click="startBestPriceChat"
                            :disabled="isOwnBusiness"
                            :class="{ 'opacity-50 cursor-not-allowed': isOwnBusiness, 'hover:bg-blue-500': !isOwnBusiness }">
                            <ReceiptPercentIcon class="inline-block h-4" />Get Best Price
                        </button> -->
              </div>
            </div>
          </div>

          <!-- Right Side: Action Buttons -->
          <div class="relative md:hidden">
            <button
              v-if="showLeftActionArrow"
              @click="scrollActions('left')"
              class="absolute left-0 top-1/2 -translate-y-1/2 bg-white/80 backdrop-blur-sm rounded-full p-1 shadow-md z-10"
            >
              <ChevronLeftIcon class="h-5 w-5 text-gray-700" />
            </button>
            <div
              ref="actionContainer"
              @scroll="checkActionScroll"
              class="flex gap-2 overflow-x-auto scrollbar-hide"
            >
              <div class="flex gap-2 min-w-max">
                <button
                  class="bg-primary text-white gap-1 py-2 rounded-md flex px-2 text-sm font-medium hover:bg-primary/85 transition-all"
                  @click.stop.prevent="callBusiness(businessData?.number)"
                >
                  <PhoneIcon class="inline-block h-4" />{{
                    businessData?.number || "N/A"
                  }}
                </button>
                <button
                  @click="openWhatsApp"
                  class="text-primary gap-1 py-[5px] rounded-md items-center flex px-2 text-sm font-medium border-primary border hover:bg-primary/15 transition-all"
                >
                  <img
                    src="@/assets/icons/svgs/WhatsappIcon.svg"
                    class="w-6"
                    alt="WhatsApp"
                  />
                  WhatsApp
                </button>
                <button
                  @click="startBestPriceChat"
                  class="text-primary gap-1 py-[7px] rounded-md items-center flex px-3 text-sm font-medium border-primary border hover:bg-primary/15 transition-all"
                >
                  <ChatBubbleLeftRightIcon class="w-5 h-5 mr-1" />
                  Message
                </button>
                <a
                  :href="businessDetails?.facebook"
                  target="_blank"
                  v-if="businessDetails?.facebook"
                  class="text-primary gap-1 py-[5px] rounded-md items-center flex px-2 text-sm font-medium border-primary border hover:bg-primary/15 transition-all"
                >
                  <img
                    src="@/assets/icons/svgs/FacebookIcon.svg"
                    class="w-6"
                    alt="facebook"
                  />
                  Facebook
                </a>
                <a
                  :href="businessDetails?.website"
                  target="_blank"
                  v-if="businessDetails?.website"
                  class="bg-red-500 text-white gap-1 py-2 rounded-md flex px-2 text-sm font-medium hover:bg-red-400 transition-all"
                >
                  <GlobeAltIcon class="inline-block h-5" />Visit Website
                </a>
                <!-- <button class="bg-blue-600 text-white gap-1 py-2 rounded-md flex px-2 text-sm font-medium hover:bg-blue-500 transition-all" 
                            @click="startBestPriceChat"
                            :disabled="isOwnBusiness"
                            :class="{ 'opacity-50 cursor-not-allowed': isOwnBusiness, 'hover:bg-blue-500': !isOwnBusiness }">
                            <ReceiptPercentIcon class="inline-block h-4" />Get Best Price
                        </button> -->
              </div>
            </div>
            <button
              v-if="showRightActionArrow"
              @click="scrollActions('right')"
              class="absolute right-0 top-1/2 -translate-y-1/2 bg-white/80 backdrop-blur-sm rounded-full p-1 shadow-md z-10"
            >
              <ChevronRightIcon class="h-5 w-5 text-gray-700" />
            </button>
          </div>
          <!-- Right Side: Tags, Bookmark, Rating -->
          <div class="min-w-max flex-col h-36 hidden md:flex">
            <!-- Make it flex-col and give it full height -->
            <!-- Top Buttons -->
            <div class="flex gap-2 justify-end">
              <button
                @click="toggleFollow"
                class="p-1.5 border rounded-md transition duration-200 ease-in-out bg-primary/10 hover:bg-primary/20"
                :disabled="followingLoading"
              >
                <img
                  src="@/assets/icons/svgs/FollowStoreIcon.svg"
                  alt="Follow Store"
                  class="h-8 transition"
                  :class="{
                    'filter-primary': isFollowing,
                  }"
                />
              </button>

              <button
                @click="shareBusiness"
                class="p-1.5 border rounded-md transition hover:bg-primary/20 duration-200 ease-in-out bg-gray-200"
              >
                <ShareIcon class="h-8" />
              </button>
            </div>

            <!-- Rating section pushed to bottom -->
            <div class="mt-auto">
              <p class="text-base font-medium text-end mb-1">Click to Rate</p>
              <MakeReview
                :businessSlug="businessData.slug"
                :businessId="businessData.id"
              />
            </div>
          </div>
          <!-- </div> -->
        </div>
      </div>
    </div>
  </div>
</template>

<script>
// import MakeReview from "@/MakeReview.vue";
import MakeReview from "@/components/MakeReview.vue";
import VerifiedBadge from "@/components/common/VerifiedBadge.vue";
import { mapGetters, mapActions } from "vuex";
import {
  StarIcon,
  PhoneIcon,
  ReceiptPercentIcon,
  CheckCircleIcon,
} from "@heroicons/vue/24/solid";
import {
  MapPinIcon,
  GlobeAltIcon,
  BookmarkIcon,
  ShareIcon,
  ChevronLeftIcon,
  ChevronRightIcon,
  ChatBubbleLeftRightIcon,
} from "@heroicons/vue/24/outline";
export default {
  components: {
    MakeReview,
    VerifiedBadge,
    StarIcon,
    MapPinIcon,
    PhoneIcon,
    ReceiptPercentIcon,
    GlobeAltIcon,
    CheckCircleIcon,
    BookmarkIcon,
    ShareIcon,
    ChatBubbleLeftRightIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
  },
  props: {
    businessData: {
      type: Object,
      default: null,
    },
    businessDetails: {
      type: Object,
      default: null,
    },
  },
  data() {
    return {
      showLeftActionArrow: false,
      showRightActionArrow: false,
    };
  },
  computed: {
    ...mapGetters(["followingBusinesses", "followingLoading"]),
    isFollowing() {
      if (!this.businessData?.id || !this.followingBusinesses) return false;
      return this.followingBusinesses.some(
        (business) => business.id === this.businessData.id
      );
    },
    isOwnBusiness() {
      return this.businessData?.id === this.$store.getters.user?.business?.id;
    },
  },
  methods: {
    async startBestPriceChat() {
      try {
        const businessId = this.businessData?.id;
        if (!businessId) return;
        const res = await this.$store.dispatch("startConversation", {
          business_id: businessId,
        });
        const conversation = res?.data;
        if (conversation?.id) {
          console.log("Conversation started 454545:", conversation);
          await this.$store.dispatch(
            "fetchConversationMessages",
            conversation.id
          );

          // Create a draft message for best price inquiry
          const draftMessage = `Hi! I'm interested in getting the best prices for your products. Can you help me with competitive pricing?`;
          localStorage.setItem(
            `conversation_draft_${conversation.id}`,
            draftMessage
          );
          this.$router.push({
            name: "conversation-messages",
            params: { id: conversation?.id },
          });
        }
      } catch (e) {
        console.error("Failed to start best-price chat:", e);
      }
    },
    async toggleFollow() {
      if (!this.businessData?.id) return;

      try {
        if (this.isFollowing) {
          await this.$store.dispatch(
            "removeFromFollowing",
            this.businessData.id
          );
        } else {
          await this.$store.dispatch("addToFollowing", this.businessData.id);
        }
      } catch (error) {
        console.error("Error toggling follow:", error);
        // You might want to show a toast notification here
      }
    },
    getCurrentDayTiming() {
      const timing =
        this.businessDetails?.timing?.[
          ["sun", "mon", "tue", "wed", "thu", "fri", "sat"][new Date().getDay()]
        ];
      if (!timing?.end && (typeof timing === "string")){
        if (timing.toLowerCase() == "24 hrs"){
          return "Open 24 hours";
        }
        return timing;
      } 

      // Shortcut: Create date objects using a dummy date + the time string
      const start = new Date(`2000/01/01 ${timing.start}`);
      const end = new Date(`2000/01/01 ${timing.end}`);
      const now = new Date(
        `2000/01/01 ${new Date().toLocaleTimeString("en-US", {
          hour12: true,
          hour: "numeric",
          minute: "numeric",
        })}`
      );

      if (start <= now && now < end){
        return `Open until ${timing.end}`;
      } else {
        return "Closed";
      }
    },
    callBusiness(number) {
      window.location.href = `tel:${number}`;
    },
    openWhatsApp() {
      const whatsappNumber = this.businessData?.alternate_number;
      if (whatsappNumber) {
        const message = `Hi ${
          this.businessData?.name || "there"
        }, I'm interested in your business ${
          this.businessData?.business_name || ""
        }. Can you please provide more information about your products/services?`;
        const whatsappUrl = `https://wa.me/${whatsappNumber.replace(
          /\D/g,
          ""
        )}?text=${encodeURIComponent(message)}`;
        window.open(whatsappUrl, "_blank");
      } else {
        alert("WhatsApp number not available");
      }
    },

    shareBusiness() {
      if (navigator.share) {
        navigator.share({
          title: this.businessData?.business_name || "Business Profile",
          text: `Check out ${
            this.businessData?.business_name || "this business"
          }`,
          url: window.location.href,
        });
      } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.href);
        alert("Link copied to clipboard!");
      }
    },
    checkActionScroll() {
      const container = this.$refs.actionContainer;
      if (container) {
        const { scrollLeft, scrollWidth, clientWidth } = container;
        // A small buffer is added to account for sub-pixel rendering issues
        const buffer = 2;
        this.showLeftActionArrow = scrollLeft > buffer;
        this.showRightActionArrow =
          scrollLeft < scrollWidth - clientWidth - buffer;
      }
    },
    scrollActions(direction) {
      const container = this.$refs.actionContainer;
      if (container) {
        const scrollAmount = container.clientWidth * 0.7; // Scroll 70% of the visible width
        container.scrollBy({
          left: direction === "left" ? -scrollAmount : scrollAmount,
          behavior: "smooth",
        });
      }
    },
  },
  async mounted() {
    this.$nextTick(() => {
      this.checkActionScroll();
      window.addEventListener("resize", this.checkActionScroll);
    });
  },
  beforeUnmount() {
    window.removeEventListener("resize", this.checkActionScroll);
  },
};
</script>
<style>
/* Approximate filter for #0b845c (green) */
.filter-primary {
  filter: invert(34%) sepia(73%) saturate(507%) hue-rotate(110deg)
    brightness(93%) contrast(97%);
}
</style>
