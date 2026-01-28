<template>
    <div>
        <h2 class="text-xl md:text-3xl font-bold text-gray-800 mb-6 text-center">Following</h2>

        <!-- Loading state -->
        <div v-if="followingLoading" class="space-y-4 sm:space-y-6 mt-2">
            <div v-for="i in 3" :key="i"
                class="group flex flex-col sm:flex-row bg-white rounded-xl border p-3 sm:p-4 gap-3 sm:gap-4 items-start">
                <div class="w-full sm:w-40 md:w-52 h-40 sm:h-40 md:h-52 rounded-lg border bg-gray-200 animate-pulse"></div>
                <div class="flex-1 flex flex-col min-h-32 sm:min-h-48 w-full">
                    <div class="h-5 sm:h-6 bg-gray-200 rounded animate-pulse mb-3 sm:mb-4"></div>
                    <div class="h-3 sm:h-4 bg-gray-200 rounded animate-pulse mb-1 sm:mb-2"></div>
                    <div class="h-3 sm:h-4 bg-gray-200 rounded animate-pulse mb-3 sm:mb-4"></div>
                    <div class="flex gap-1 sm:gap-2 mt-auto">
                        <div class="h-8 sm:h-10 bg-gray-200 rounded animate-pulse w-24 sm:w-32"></div>
                        <div class="h-8 sm:h-10 bg-gray-200 rounded animate-pulse w-20 sm:w-24"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty state -->
        <div v-else-if="followingBusinesses.length === 0" class="text-center py-12">
            <div class="text-gray-500 text-lg">No businesses followed yet</div>
            <p class="text-gray-400 mt-2">Start following businesses to see them here</p>
        </div>

        <!-- Following businesses list -->
        <div v-else class="space-y-6 mt-2">
            <div v-for="business in followingBusinesses" :key="business.id"
                class="bg-white rounded-xl border p-4 transition-all hover:shadow-lg">
                <router-link :to="`/${business.slug}`"
                    class="group flex gap-4 items-start cursor-pointer">
                    <!-- <div class="max-h-52 max-w-52 product-image rounded-lg border content-center bg-green-50 overflow-hidden">
                    <img :src="business.business_profile || 'https://placehold.co/260x200'" :alt="business.business_name" class="w-full h-full object-cover transition-transform duration-500 ease-in-out transform group-hover:scale-110" />
                </div> -->
                    <div class="max-h-40 max-w-40 product-image">
                        <div
                            class="rounded-lg border content-center bg-green-50 max-h-44 max-w-44 md:max-h-52 md:max-w-52 product-image overflow-hidden">
                            <img :src="business.business_profile || 'https://placehold.co/200x200'"
                                :alt="business.business_name"
                                class="w-full h-full object-cover transition-transform duration-500 ease-in-out transform group-hover:scale-110" />
                        </div>
                        <VerifiedBadge :accountVerified="business.account_verification" class="md:hidden mt-2" />
                    </div>
                    <div class="flex-1 flex flex-col min-h-48">
                        <!-- store name -->
                        <div class="flex justify-between">
                            <div class="flex items-center gap-2">
                                <BuildingOfficeIcon class="inline-block h-5" />
                                <span class="text-sm md:text-xl font-semibold line-clamp-2 capitalize">{{
                                    business.business_name
                                    }}</span>
                                    <VerifiedBadge :accountVerified="business.account_verification" class="hidden md:block"/>
                            </div>
                            <button @click.stop.prevent="unfollowBusiness(business.id)"
                                class="p-2 rounded-md duration-200 ease-in-out bg-primary/10 hover:bg-primary/20 relative">
                                <img src="@/assets/icons/svgs/FollowStoreIcon.svg" alt="Follow Store"
                                    class="h-6 transition" :class="{
                                        'filter-primary': true,
                                    }" />
                            </button>
                        </div>
                        <div class="md:flex items-center gap-2 my-1">
                            <div class="flex items-center gap-2">
                                <span v-if="business.rating?.rate"
                                    class="bg-green-700 text-sm md:text-base font-semibold text-white px-1 rounded-md">{{
                                        business.rating.rate }}</span>
                                <div v-if="business.rating?.rate">
                                    <StarRating :rating="parseFloat(business.rating.rate)" iconSize="h-5" />
                                </div>
                                <span v-if="business.rating?.count" class="text-xs text-gray-600">({{
                                    business.rating.count
                                    }})</span>
                            </div>
                            <span v-if="business.in_business" class="text-xs md:text-sm ml-2">{{ business.in_business }}
                                Years
                                in business</span>
                        </div>
                        <div class="md:flex items-center gap-2 mt-1 text-xs text-gray-800">
                            <div class="line-clamp-1 flex content-center font-medium mb-2 md:mb-0">
                                <MapPinIcon class="inline-block h-5" />
                                <span class="mt-[1px]">{{ business.location }}</span>
                            </div>
                            <span class="hidden md:block mx-1">•</span>
                            <div class="flex flex-wrap gap-1">
                                <span v-for="type in business.business_type" :key="type"
                                    class="bg-primary/10 font-semibold py-0.5 rounded px-2 capitalize">{{ type }}</span>
                            </div>
                        </div>
                        <!-- action buttons (desktop) -->
                        <div class="hidden md:flex flex-wrap gap-2 mt-auto">
                            <button v-if="business?.number"
                                class="bg-primary text-white px-4 py-2 rounded-md flex items-center gap-1 text-sm font-medium hover:bg-primary/85 transition-all"
                                @click.stop.prevent="callBusiness(business.number)">
                                <PhoneIcon class="inline-block h-4" />{{ business?.number }}
                            </button>
                            <button v-if="business?.alternate_number"
                                class="text-primary px-4 py-1 rounded-md flex items-center gap-1 text-sm font-medium border-primary border hover:bg-primary/15 transition-all"
                                @click.stop.prevent="whatsappBusiness(business.alternate_number)">
                                <img src="@/assets/icons/svgs/WhatsappIcon.svg" class="w-7" alt="WhatsApp" />
                                WhatsApp
                            </button>
                            <button
                                class="bg-blue-600 text-white px-4 py-2 rounded-md flex items-center gap-1 text-sm font-medium hover:bg-blue-500 transition-all"
                                @click.stop.prevent="startBestPriceChat(business)" :disabled="isOwnBusiness(business)"
                                :class="{ 'opacity-50 cursor-not-allowed': isOwnBusiness(business), 'hover:bg-blue-500': !isOwnBusiness(business) }">
                                <ReceiptPercentIcon class="inline-block h-4" />Get Best Price
                            </button>
                        </div>
                    </div>
                </router-link>
                <!-- action buttons (mobile) -->
                <div class="relative md:hidden mt-3">
                    <button v-if="showLeftActionArrow[business.id]" @click="scrollActions('left', business.id)"
                        class="absolute left-0 top-1/2 -translate-y-1/2 bg-white/80 backdrop-blur-sm rounded-full p-1 shadow-md z-10">
                        <ChevronLeftIcon class="h-5 w-5 text-gray-700" />
                    </button>
                    <div :ref="el => actionContainers[business.id] = el" @scroll="checkActionScroll($event, business.id)"
                        class="flex gap-2 overflow-x-auto scrollbar-hide">
                        <div class="flex gap-2 min-w-max">
                        <button v-if="business?.number"
                            class="bg-primary text-white px-2 py-1 rounded-md flex items-center gap-1 text-xs font-medium hover:bg-primary/85 transition-all whitespace-nowrap"
                            @click.stop.prevent="callBusiness(business.number)">
                            <PhoneIcon class="inline-block h-3" />{{ business.number }}
                        </button>
                        <button v-if="business?.alternate_number"
                            class="text-primary px-2 py-0.5 rounded-md flex items-center gap-1 text-xs font-medium border-primary border hover:bg-primary/15 transition-all whitespace-nowrap"
                            @click.stop.prevent="whatsappBusiness(business.alternate_number)">
                            <img src="@/assets/icons/svgs/WhatsappIcon.svg" class="w-5" alt="WhatsApp" />
                            WhatsApp
                        </button>
                        <button
                            class="bg-blue-600 text-white px-2 py-1 rounded-md flex items-center gap-1 text-xs font-medium hover:bg-blue-500 transition-all whitespace-nowrap"
                            @click.stop.prevent="startBestPriceChat(business)" :disabled="isOwnBusiness(business)"
                            :class="{ 'opacity-50 cursor-not-allowed': isOwnBusiness(business), 'hover:bg-blue-500': !isOwnBusiness(business) }">
                            <ReceiptPercentIcon class="inline-block h-3" />
                            Get Best Price
                        </button>
                    </div>
                    </div>
                    <button v-if="showRightActionArrow[business.id]" @click="scrollActions('right', business.id)"
                        class="absolute right-0 top-1/2 -translate-y-1/2 bg-white/80 backdrop-blur-sm rounded-full p-1 shadow-md z-10">
                        <ChevronRightIcon class="h-5 w-5 text-gray-700" />
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import StarRating from '@/components/StarRating.vue'
import VerifiedBadge from '@/components/common/VerifiedBadge.vue'
import {
    BuildingOfficeIcon,
    PhoneIcon,
    MapPinIcon,
    ReceiptPercentIcon,
    ChevronLeftIcon,
    ChevronRightIcon
} from "@heroicons/vue/24/outline";

export default {
    components: {
        BuildingOfficeIcon,
        PhoneIcon,
        ReceiptPercentIcon,
        MapPinIcon,
        StarRating,
        VerifiedBadge,
        ChevronLeftIcon,
        ChevronRightIcon
    },
    data() {
        return {
            showLeftActionArrow: {},
            showRightActionArrow: {},
            actionContainers: {},
        }
    },
    computed: {
        ...mapGetters(['followingBusinesses', 'followingLoading'])
    },
    watch: {
        followingBusinesses() {
            this.$nextTick(this.checkAllScrolls);
        }
    },
    methods: {
        ...mapActions(['removeFromFollowing']),
        callBusiness(number) {
            window.location.href = `tel:${number}`
        },
        async unfollowBusiness(businessId) {
            try {
                await this.removeFromFollowing(businessId);
                // The business will be automatically removed from the list due to the store action
            } catch (error) {
                console.error('Error unfollowing business:', error);
                // You might want to show a toast notification here
            }
        },
        whatsappBusiness(number) {
            const message = `Hi, I'm interested in your products. Can you provide more information?`
            const whatsappUrl = `https://wa.me/${number}?text=${encodeURIComponent(message)}`
            window.open(whatsappUrl, '_blank')
        },
        async startBestPriceChat(business) {
            try {
                // If not authenticated, navigating will trigger auth guard
                if (!this.$store.getters.isAuthenticated) {
                    this.$router.push({ name: 'conversations' });
                    return;
                }
                const businessId = business?.id;
                if (!businessId) return;
                const res = await this.$store.dispatch('startConversation', { business_id: businessId });
                const conversation = res?.data;
                if (conversation?.id) {
                    await this.$store.dispatch('fetchConversationMessages', conversation.id);

                    // Only send automatic message if there are no existing messages
                    const existingMessages = this.$store.getters.messages;
                    if (!existingMessages || existingMessages.length === 0) {
                        // Send a general message since this is from business profile, not a specific product
                        const message = `Hi! I'm interested in getting the best prices for your products. Can you help me?`;

                        await this.$store.dispatch('sendConversationMessage', {
                            conversationId: conversation.id,
                            message: message
                        });
                    }
                this.$router.push({ name: 'conversation-messages', params: { id: conversation.id } });
                }
            } catch (e) {
                console.error('Failed to start best-price chat:', e);
            }
        },
        isOwnBusiness(business) {
            return business?.id === this.$store.getters.user?.business?.id;
        },
        checkActionScroll(event, businessId) {
            const container = event.target;
            if (container) {
                const { scrollLeft, scrollWidth, clientWidth } = container;
                const buffer = 2;
                this.showLeftActionArrow[businessId] = scrollLeft > buffer;
                this.showRightActionArrow[businessId] = scrollLeft < scrollWidth - clientWidth - buffer;
            }
        },
        scrollActions(direction, businessId) {
            const container = this.actionContainers[businessId];
            if (container) {
                const scrollAmount = container.clientWidth * 0.7;
                container.scrollBy({
                    left: direction === 'left' ? -scrollAmount : scrollAmount,
                    behavior: 'smooth'
                });
            }
        },
        checkAllScrolls() {
            for (const businessId in this.actionContainers) {
                const container = this.actionContainers[businessId];
                if (container) {
                    const { scrollLeft, scrollWidth, clientWidth } = container;
                    const buffer = 2;
                    this.showLeftActionArrow[businessId] = scrollLeft > buffer;
                    this.showRightActionArrow[businessId] = scrollWidth > clientWidth;
                }
            }
        }
    },
    async mounted() {
        try {
            window.addEventListener('resize', this.checkAllScrolls);
        } catch (error) {
            console.error('Error fetching following businesses:', error);
        }
    },
    beforeUnmount() {
        window.removeEventListener('resize', this.checkAllScrolls);
    }
}
</script>

<style scoped>
/* Approximate filter for #0b845c (green) */
.filter-primary {
    filter: invert(34%) sepia(73%) saturate(507%) hue-rotate(110deg) brightness(93%) contrast(97%);
}
</style>

<style>
.product-image {
    aspect-ratio: 1 / 1;
    width: 100%;
    height: auto;
}

.scrollbar-hide {
    -ms-overflow-style: none;
    /* Internet Explorer 10+ */
    scrollbar-width: none;
    /* Firefox */
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
    /* Safari and Chrome */
}
</style>
