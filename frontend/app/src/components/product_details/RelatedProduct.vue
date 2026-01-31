<template>
    <div v-if="products && products.length > 0" class="container mx-auto my-10">
        <div class="md:mx-2">
            <div class="flex justify-between items-center md:mb-4">
                <h2 class="text-lg md:text-2xl font-semibold">{{ title }}</h2>
                <div class="flex space-x-2">
                    <button
                        :class="`${prevButton} swiper-button-prev-custom text-black rounded-md font-semibold shadow-none transition hover:bg-gray-200 duration-200 ease-in-out p-1.5`">
                        <ChevronLeftIcon class="w-6" />
                    </button>
                    <button
                        :class="`${nextButton} swiper-button-next-custom text-black rounded-md font-semibold shadow-none transition hover:bg-gray-200 duration-200 ease-in-out p-1.5`">
                        <ChevronRightIcon class="w-6" />
                    </button>
                </div>
            </div>
            <swiper :slides-per-view="5" :space-between="25" :navigation="navigationOptions" @swiper="onSwiper"
                :modules="modules" :breakpoints="{
                    0: { slidesPerView: 2, spaceBetween: 15 },
                    640: { slidesPerView: 3, spaceBetween: 20 },
                    1024: { slidesPerView: 5, spaceBetween: 25 }
                }" class="mySwiper">
                <swiper-slide v-for="(product, index) in products" :key="product.id || index">
                    <router-link :to="`/product/${product.slug}`" class="block h-full">
                        <div
                            class="mb-1 relative bg-white rounded-lg shadow-md overflow-hidden mx-1 md:mx-2 cursor-pointer w-[150px] md:w-[200px] h-72 md:h-96 flex flex-col">
                            <!-- Heart Icon -->
                            <button @click.stop="toggleFavourite(product)"
                                class="absolute top-2 right-2 z-10 p-1 bg-white/80 rounded-full hover:bg-white transition-colors">
                                <HeartIcon v-if="!isFavourite(product.id)"
                                    class="h-5 w-5 text-gray-600 hover:text-red-500" />
                                <HeartIconSolid v-else class="h-5 w-5 text-red-500" />
                            </button>

                            <!-- Image -->
                            <div class="w-full h-36 md:h-48 bg-primary/10 flex-shrink-0">
                                <OptimizedImage :src="product.feature_image || 'https://placehold.co/180/F5F5DC/808080?text=No+Image'"
                                    :alt="product.name" class="w-full h-full object-cover" />
                            </div>

                            <!-- Content -->
                            <div class="p-4 flex flex-col justify-between flex-1">
                                <div>
                                    <h3 class="text-sm md:text-lg font-semibold mb-1 md:mb-3 line-clamp-2 capitalize">
                                        {{ product.name }}
                                    </h3>
                                    <div class="flex flex-wrap items-baseline mb-3">
                                        <span class="text-sm md:text-xl font-poppins font-medium">৳ {{ product.price ||
                                            'N/A' }}</span>/ <span class="text-sm ml-1 font-normal">{{
                                            getUnitDisplay(product)
                                            }}</span>
                                    </div>
                                </div>
                                <button
                                    class="w-full bg-primary text-white py-2 text-sm md:text-base font-semibold px-2 md:px-4 rounded-md hover:bg-primary/85 focus:outline-none"
                                    @click.stop.prevent="startBestPriceChat(product)" :disabled="isOwnBusiness(product.business_id)"
                                    :class="{
                                        'opacity-50 cursor-not-allowed': isOwnBusiness(product.business_id),
                                        'hover:bg-primary/85': !isOwnBusiness(product.business_id)
                                    }">
                                    Get Best Price
                                </button>
                            </div>
                        </div>
                    </router-link>
                </swiper-slide>
            </swiper>
        </div>
    </div>
</template>

<script>
import {
    ref
} from 'vue';
import {
    ChevronLeftIcon,
    ChevronRightIcon,
} from "@heroicons/vue/24/solid";
import OptimizedImage from '@/components/common/OptimizedImage.vue';
import {
    HeartIcon
} from "@heroicons/vue/24/outline";
import {
    HeartIcon as HeartIconSolid
} from "@heroicons/vue/24/solid";
import {
    Swiper,
    SwiperSlide
} from 'swiper/vue';
import 'swiper/css';
import 'swiper/css/navigation';
import {
    Navigation
} from 'swiper/modules';

export default {
    components: {
        Swiper,
        SwiperSlide,
        ChevronLeftIcon,
        ChevronRightIcon,
        HeartIcon,
        HeartIconSolid,
    },
    props: {
        title: {
            type: String,
            required: true,
        },
        products: {
            type: Array,
            required: true,
            default: () => []
        },
        loading: {
            type: Boolean,
            default: false,
        },
        favouriteProducts: {
            type: Array,
            default: () => []
        },
    },
    setup(props) {
        const swiperInstance = ref(null);
        const nextButton = props.title.replace(/\s+/g, '-') + '-next';
        const prevButton = props.title.replace(/\s+/g, '-') + '-prev';
        const navigationOptions = {
            nextEl: `.${nextButton}`,
            prevEl: `.${prevButton}`,
        };

        const onSwiper = (swiper) => {
            swiperInstance.value = swiper;
            if (swiper.params.navigation) {
                swiper.navigation.destroy();
                swiper.navigation.init();
                swiper.navigation.update();
            }
        };


        return {
            modules: [Navigation],
            onSwiper,
            navigationOptions,
            nextButton,
            prevButton
        };
    },
    computed: {
        // Standardized unit display format
        getUnitDisplay() {
            return (item) => {
                const unit = item.product_unit || {};
                if (item.unit_quantity && item.unit_quantity > 1) {
                    return `Per ${item.unit_quantity} ${unit.plural_form || unit.full_form || 'Units'}`;
                }
                return unit.short_form || unit.full_form || 'Unit';
            };
        }
    },
    methods: {
        // Check if a product is in favourites
        isFavourite(productId) {
            if (!this.favouriteProducts || this.favouriteProducts.length === 0) {
                return false;
            }
            return this.favouriteProducts.some(product => product.id === productId);
        },
        // Toggle favourite status
        async toggleFavourite(product) {
            try {
                if (this.isFavourite(product.id)) {
                    // Remove from favourites
                    await this.$store.dispatch('removeFromFavourites', product.id);
                } else {
                    // Add to favourites
                    await this.$store.dispatch('addToFavourites', product.id);
                }
            } catch (error) {
                console.error('Failed to toggle favourite:', error);
            }
        },
        async startBestPriceChat(product) {
            try {
                const businessId = product?.business_id;
                if (!businessId) return;
                const res = await this.$store.dispatch('startConversation', {
                    business_id: businessId
                });
                const conversation = res?.data;
                if (conversation?.id) {
                    await this.$store.dispatch('fetchConversationMessages', conversation.id);

                    // Create a draft message with product details
                    const productSlug = product?.slug;
                    const productId = product?.id;
                    const baseUrl = window.location.origin;
                    const productUrl = `${baseUrl}/product/${productSlug}`;
                    const productName = product?.name || 'this product';

                    const draftMessage = `Hi! I'm interested in getting the best price for this product: ${productUrl}\n\nProduct: ${productName}\n\nCould you please provide me with your best offer?`;
                    localStorage.setItem(`conversation_draft_${conversation.id}`, draftMessage);
                    
                    this.$router.push({
                        name: 'conversation-messages',
                        params: { id: conversation?.id }
                    });
                }
            } catch (e) {
                console.error('Failed to start best-price chat:', e);
            }
        },
        isOwnBusiness(business_id) {
            return business_id === this.$store.getters?.user?.business?.id || false;
        }
    }
};
</script>

<style>
/* Custom styles for navigation buttons if needed */
.swiper-button-prev-custom,
.swiper-button-next-custom {
    position: static !important;
    width: auto !important;
    height: auto !important;
    margin-top: 0 !important;
    transform: none !important;
}

.swiper-button-prev-custom:after,
.swiper-button-next-custom:after {
    content: none !important;
}
</style>
