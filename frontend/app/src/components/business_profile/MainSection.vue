<template>
<div class="mx-auto mt-4 h-fit">
    <!-- navigation tabs -->
    <div class="bg-white sticky top-16 z-[51] pt-2 md:px-6">
        <button v-if="showLeftArrow" @click="scrollTabs('left')" class="absolute left-0 top-1/2 -translate-y-1/2 bg-white/80 backdrop-blur-sm rounded-full p-1 shadow-md z-10 md:hidden">
            <ChevronLeftIcon class="h-6 w-6 text-gray-700" />
        </button>
        <div ref="tabContainer" @scroll="checkScroll" class="flex gap-6 mx-2 overflow-x-auto text-sm md:text-base scrollbar-hide">
            <div class="flex gap-2 min-w-max items-center">
                <button class="p-3 border-b-4 font-semibold flex lg:hidden"
                    :class="activeTab === 'contact' ? 'border-primary text-primary' : 'border-transparent text-gray-600 hover:text-primary'"
                    @click="setActiveTab('contact')">
                    <IdentificationIcon class="h-4 md:h-5 mr-1" />Contact
                </button>
                <button class="p-3 border-b-4 font-semibold flex"
                    :class="activeTab === 'business' ? 'border-primary text-primary' : 'border-transparent text-gray-600 hover:text-primary'"
                    @click="setActiveTab('business')">
                    <BuildingOfficeIcon class="h-4 md:h-5 mr-1" />Business Details
                </button>
                <button class="p-3 border-b-4 font-semibold flex"
                    :class="activeTab === 'products' ? 'border-primary text-primary' : 'border-transparent text-gray-600 hover:text-primary'"
                    @click="setActiveTab('products')">
                    <CubeIcon class="h-4 md:h-5 mr-1" />Products
                </button>
                <button class="p-3 border-b-4 font-semibold flex"
                    :class="activeTab === 'reviews' ? 'border-primary text-primary' : 'border-transparent text-gray-600 hover:text-primary'"
                    @click="setActiveTab('reviews')">
                    <StarIcon class="h-4 md:h-5 mr-1" />Reviews & Ratings
                </button>
            </div>
        </div>
        <button v-if="showRightArrow" @click="scrollTabs('right')" class="absolute right-0 top-1/2 -translate-y-1/2 bg-white/80 backdrop-blur-sm rounded-full p-1 shadow-md z-10 md:hidden">
            <ChevronRightIcon class="h-6 w-6 text-gray-700" />
        </button>
    </div>
    <!-- Main Content Section -->
    <div class="container flex flex-col lg:flex-row gap-6 mb-4">
        <!-- Main Content -->
        <div class="flex-1 rounded-md shadow border p-4">

            <!-- Business Details -->
            <div v-if="activeTab === 'business'">
                <div class="break-all">
                    <h2 class="text-lg md:text-2xl font-semibold mb-4">Business Details</h2>
                    <div class="mb-5" v-if="businessDetails?.about">
                        <div class="text-gray-500 text-sm font-semibold mb-1">Business summary</div>
                        <div class="font-semibold">{{ businessDetails.about }}</div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mb-5">
                        <div>
                            <div class="text-gray-500 text-sm font-semibold">Contact Name</div>
                            <div class=" md:text-lg font-semibold">{{ businessData?.name || 'N/A' }}</div>
                        </div>
                        <div>
                            <div class="text-gray-500 text-sm font-semibold">Year of Establishment</div>
                            <div class=" md:text-lg font-semibold">{{ businessDetails?.established || 'N/A' }}</div>
                        </div>
                        <div>
                            <div class="text-gray-500 text-sm font-semibold">No of Employee</div>
                            <div class=" md:text-lg font-semibold">{{ businessDetails?.number_of_employee || 'N/A' }}</div>
                        </div>
                        <div>
                            <div class="text-gray-500 text-sm font-semibold">Years in Business</div>
                            <div class="font-semibold text-black">{{ businessDetails?.in_business || 'N/A' }} Years</div>
                        </div>
                        <div>
                            <div class="text-gray-500 text-sm font-semibold">Mode of Payment</div>
                            <div class="font-semibold text-black">{{ businessDetails?.payment_type || 'N/A' }}</div>
                        </div>
                        <div v-if="businessDetails?.tin">
                            <div class="text-gray-500 text-sm font-semibold">TIN</div>
                            <div class="font-semibold text-black">{{ businessDetails.tin }}</div>
                        </div>
                    </div>
                    <div class="mb-5">
                        <div class="text-gray-500 text-sm font-semibold mb-1">Full Address</div>
                        <div class="font-semibold">{{ businessData?.address || 'N/A' }}</div>
                    </div>
                    <div class="mb-5" v-if="businessDetails?.direction">
                        <div class="text-gray-500 text-sm font-semibold mb-1">Direction</div>
                        <div class="font-semibold">
                            <a :href="businessDetails.direction" target="_blank" class="text-primary hover:underline">View on Google Maps</a>
                        </div>
                    </div>
                </div>

                <!-- Business Gallery -->
                <div class="mt-8">
                    <h3 class="text-xl font-semibold mb-4">Gallery</h3>
                    <BusinessGallery :businessData="businessData" :businessDetails="businessDetails" />
                </div>
            </div>

            <!-- Product Grid -->
            <div v-else-if="activeTab === 'products'">
                <BusinessProducts :businessId="businessData?.id" :products="products" :loadingProducts="loadingProducts"/>
            </div>

            <div v-else-if="activeTab === 'reviews'">
                <BusinessReviews :businessName="businessData.business_name" :businessId="businessData.id" :businessSlug="businessData.slug"/>
            </div>

            <!-- Contact Info (Sidebar content on mobile) -->
            <div v-else-if="activeTab === 'contact'" class="md:hidden">
                <SideBar :businessData="businessData" :businessDetails="businessDetails" />
            </div>
        </div>
        <!-- Sidebar -->
        <!-- Hidden on mobile, shown on desktop -->
        <aside class="w-full md:w-80 flex-shrink-0 hidden lg:block">
            <!-- Sidebar content remains unchanged -->
            <SideBar :businessData="businessData" :businessDetails="businessDetails" />
        </aside>
    </div>
</div>
</template>

<script>
import axios from 'axios';
import BusinessReviews from '@/components/BusinessReviews.vue'
import BusinessProducts from './BusinessProducts.vue'
import BusinessGallery from './BusinessGallery.vue'
import SideBar from './SideBar.vue';
import {
    BuildingOfficeIcon,
    StarIcon,
    CubeIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
    IdentificationIcon
} from '@heroicons/vue/24/outline';

export default {
    components: {
        SideBar,
        BusinessProducts,
        BusinessGallery,
        BuildingOfficeIcon,
        BusinessReviews,
        StarIcon,
        CubeIcon,
        ChevronLeftIcon,
        ChevronRightIcon,
        IdentificationIcon
    },
    props: {
        businessData: {
            type: Object,
            default: null
        },
        businessDetails: {
            type: Object,
            default: null
        }
    },
    data() {
        return {
            activeTab: 'business',
            showLeftArrow: false,
            showRightArrow: false,
            loadingProducts: false,
            products: [],
        }
    },
    watch: {
        activeTab(newTab) {
            // Update the URL without triggering a page scroll or router navigation.
            // This preserves the user's scroll position when switching tabs.
            const url = new URL(window.location);
            url.searchParams.set('tab', newTab);
            const relativeUrl = url.pathname + url.search;
            // Use replaceState to avoid adding to the browser's history stack.
            window.history.replaceState({ ...window.history.state, current: relativeUrl }, '', relativeUrl);
        }
    },
    mounted() {
        // Check if there's a tab query parameter and set the active tab
        const tab = this.$route.query.tab
        if (tab && ['business', 'products', 'reviews', 'contact'].includes(tab)) {
            this.activeTab = tab
        }

        this.$nextTick(() => {
            this.checkScroll();
            window.addEventListener('resize', this.checkScroll);
        });

        this.fetchBusinessProducts();
    },
    beforeUnmount() {
        window.removeEventListener('resize', this.checkScroll);
    },
    methods: {
        setActiveTab(tab) {
            this.activeTab = tab;
        },
        checkScroll() {
            const container = this.$refs.tabContainer;
            if (container) {
                const { scrollLeft, scrollWidth, clientWidth } = container;
                this.showLeftArrow = scrollLeft > 0;
                this.showRightArrow = scrollLeft < scrollWidth - clientWidth - 1; // -1 for precision
            }
        },
        scrollTabs(direction) {
            const container = this.$refs.tabContainer;
            if (container) {
                const scrollAmount = container.clientWidth * 0.8; // Scroll 80% of the visible width
                container.scrollBy({
                    left: direction === 'left' ? -scrollAmount : scrollAmount,
                    behavior: 'smooth'
                });
            }
        },
        async fetchBusinessProducts() {
            
            this.loadingProducts = true;
            try {
                const response = await axios.get(`/business/products/${this.businessData?.id}`);
                if (response.data.status) {
                    this.products = response.data.data;
                }
            } catch (error) {
                console.error('Error fetching business products:', error);
                this.products = [];
            } finally {
                this.loadingProducts = false;
            }
        },
    }
}
</script>
<style scoped>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none; /* IE and Edge */
    scrollbar-width: none; /* Firefox */
}
</style>
