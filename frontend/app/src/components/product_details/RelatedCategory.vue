<template>
    <div v-if="categories && categories.length > 0" class="container mx-auto my-10">
        <div class="md:mx-2">
            <div class="flex justify-between items-center mb-4">
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
                    0: { slidesPerView: 2, spaceBetween: 10 },     // Mobile
                    640: { slidesPerView: 3, spaceBetween: 15 },   // Tablet
                    1024: { slidesPerView: 5, spaceBetween: 25 }   // Desktop
                }" class="mySwiper">
                <swiper-slide v-for="(category, index) in categories" :key="category.id || index"
                    class="flex justify-center">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden mx-1 md:mx-2 cursor-pointer 
             w-[150px] md:w-[200px] h-56 md:h-72 flex flex-col mb-1"
                        @click="navigateToCategory(category.slug, category.id)">
                        <!-- Image -->
                        <div class="w-full h-36 md:h-48 bg-primary/10 flex-shrink-0">
                            <OptimizedImage :src="category.icon || 'https://placehold.co/150/F5F5DC/808080?text=No+Image'"
                                :alt="category.name" class="w-full h-full object-cover object-center" />
                        </div>

                        <!-- Content -->
                        <div class="p-3 flex items-center justify-center flex-1">
                            <h3 class="text-sm md:text-lg font-semibold text-center line-clamp-2 capitalize">
                                {{ category.name }}
                            </h3>
                        </div>
                    </div>
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
    },
    props: {
        title: {
            type: String,
            required: true,
        },
        categories: {
            type: Array,
            required: true,
            default: () => []
        },
        loading: {
            type: Boolean,
            default: false,
        }
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
    methods: {
        navigateToCategory(slug, id) {
            // Navigate to category page or search results filtered by category
            // Use selectedLocation from getters or 'all-bangladesh' as default
            const selectedLocation = this.$store.getters.selectedLocation;
            let locationSlug = 'all-bangladesh';
            
            // If we have a selected location, convert it to slug format
            if (selectedLocation) {
                // Convert "Nawabganj, Dhaka" to "Nawabganj-Dhaka"
                locationSlug = selectedLocation.replace(/,\s*/g, '-').replace(/\s+/g, '-').toLowerCase();
            }
            
            this.$router.push({ 
                name: 'search', 
                params: { 
                    location: locationSlug,
                    keyword: slug 
                } 
            });
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
