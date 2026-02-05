<template>
    <div class="w-full">
        <swiper
            :modules="modules"
            :thumbs="{ swiper: thumbsSwiper }"
            :space-between="10"
            class="mb-4 rounded-lg max-h-96 max-w-96 bg-primary/10 aspect-square"
            @swiper="setMainSwiper"
            @slideChange="onSlideChange"
            :loop="true"
            :autoplay="{ delay: 2500, disableOnInteraction: false }"
            :speed="1000"
            @mouseenter="pauseAutoplay"
            @mouseleave="resumeAutoplay"
        >
            <swiper-slide v-for="(img, idx) in images" :key="idx">
                <OptimizedImage 
                    :src="img" 
                    alt="Product Image" 
                    class="max-w-96 w-full rounded-lg object-contain max-h-96 cursor-pointer hover:opacity-90 transition-opacity"
                    @click="openLightbox(idx)"
                />
            </swiper-slide>
        </swiper>
        <swiper
            @swiper="setThumbsSwiper"
            :modules="modules"
            :slides-per-view="3"
            :space-between="10"
            watch-slides-progress
            class="thumbs-swiper mt-2"
            style="max-width: 260px;"
        >
            <swiper-slide v-for="(img, idx) in images" :key="'thumb-' + idx">
                <OptimizedImage :src="typeof img === 'string' ? img.replace('400x400', '80x80') : img" alt="Product Thumbnail" class="w-full h-20 border-2 rounded-lg cursor-pointer" :class="{ 'border-primary': activeIndex === idx, 'border-gray-300': activeIndex !== idx }" @click="slideTo(idx)" />
            </swiper-slide>
        </swiper>

        <!-- Lightbox Modal -->
        <div 
            v-if="lightboxOpen" 
            @click="closeLightbox"
            @touchstart="handleTouchStart"
            @touchend="handleTouchEnd"
            class="fixed inset-0 bg-black bg-opacity-95 z-[70] flex items-center justify-center p-4"
        >
            <div 
                @click.stop
                class="relative w-full max-w-6xl max-h-full"
            >
                <!-- Close Button -->
                <button 
                    @click="closeLightbox"
                    class="absolute top-4 right-4 z-10 text-white hover:text-gray-300 transition-colors"
                >
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>

                <!-- Navigation Arrows -->
                <button 
                    v-if="images.length > 1"
                    @click="previousImage"
                    class="absolute left-4 top-1/2 -translate-y-1/2 z-10 text-white hover:text-gray-300 transition-colors"
                >
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>

                <button 
                    v-if="images.length > 1"
                    @click="nextImage"
                    class="absolute right-4 top-1/2 -translate-y-1/2 z-10 text-white hover:text-gray-300 transition-colors"
                >
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>

                <!-- Content -->
                <div class="flex flex-col items-center">
                    <!-- Image Viewer -->
                    <div class="w-full">
                        <OptimizedImage 
                            :src="currentImageUrl" 
                            :alt="`Product Image ${lightboxIndex + 1}`"
                            class="max-w-full max-h-[80vh] object-contain mx-auto rounded-lg"
                        />
                        <div class="mt-4 text-center">
                            <p class="text-white text-lg">{{ lightboxIndex + 1 }} of {{ images.length }}</p>
                            <p class="text-white text-sm text-gray-300 mt-1">Product Gallery</p>
                        </div>
                    </div>

                    <!-- Thumbnail Navigation -->
                    <div v-if="images.length > 1" class="mt-6 flex gap-2 overflow-x-auto max-w-full pb-2">
                        <div 
                            v-for="(image, index) in images" 
                            :key="index"
                            @click.stop="goToImage(index)"
                            class="flex-shrink-0 cursor-pointer rounded-lg overflow-hidden border-2 transition-all duration-200"
                            :class="lightboxIndex === index ? 'border-white' : 'border-gray-600 hover:border-gray-400'"
                        >
                            <div class="w-16 h-16 bg-gray-800">
                                <OptimizedImage 
                                    :src="image" 
                                    :alt="`Thumbnail ${index + 1}`"
                                    class="w-full h-full object-cover"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
/* .thumbs-swiper .swiper-wrapper {
    justify-content: center;
} */

/* Custom scrollbar for lightbox thumbnails */
.overflow-x-auto::-webkit-scrollbar {
    height: 6px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
}
</style>
<script>
import { Swiper, SwiperSlide } from 'swiper/vue';
import 'swiper/css';
import 'swiper/css/thumbs';
import 'swiper/css/navigation';
import { Thumbs, Navigation, Autoplay } from 'swiper/modules';
import { ref, onMounted, onUnmounted, computed } from 'vue';
import OptimizedImage from '@/components/common/OptimizedImage.vue';

export default {
    name: 'GallerySlider',
    props: {
        images: {
            type: Array,
            required: true,
        },
    },
    components: {
        Swiper,
        SwiperSlide,
        OptimizedImage
    },
    setup(props) {
        const thumbsSwiper = ref(null);
        const mainSwiper = ref(null);
        const activeIndex = ref(0);
        const lightboxOpen = ref(false);
        const lightboxIndex = ref(0);
        const touchStartX = ref(0);
        const touchStartY = ref(0);
        const modules = [Thumbs, Navigation, Autoplay];

        const setThumbsSwiper = (swiper) => {
            thumbsSwiper.value = swiper;
        };
        const setMainSwiper = (swiper) => {
            mainSwiper.value = swiper;
        };
        const slideTo = (idx) => {
            if (mainSwiper.value) mainSwiper.value.slideTo(idx);
        };
        const onSlideChange = () => {
            if (mainSwiper.value) {
                activeIndex.value = mainSwiper.value.realIndex;
            }
        };
        const pauseAutoplay = () => {
            if (mainSwiper.value && mainSwiper.value.autoplay) {
                mainSwiper.value.autoplay.stop();
            }
        };
        const resumeAutoplay = () => {
            if (mainSwiper.value && mainSwiper.value.autoplay) {
                mainSwiper.value.autoplay.start();
            }
        };

        // Lightbox methods
        const openLightbox = (index) => {
            lightboxIndex.value = index;
            lightboxOpen.value = true;
            document.body.style.overflow = 'hidden';
        };

        const closeLightbox = () => {
            lightboxOpen.value = false;
            document.body.style.overflow = 'auto';
        };

        const nextImage = () => {
            if (lightboxIndex.value < props.images.length - 1) {
                lightboxIndex.value++;
            }
        };

        const previousImage = () => {
            if (lightboxIndex.value > 0) {
                lightboxIndex.value--;
            }
        };

        const goToImage = (index) => {
            lightboxIndex.value = index;
        };

        const handleTouchStart = (event) => {
            if (!lightboxOpen.value) return;
            touchStartX.value = event.touches[0].clientX;
            touchStartY.value = event.touches[0].clientY;
        };

        const handleTouchEnd = (event) => {
            if (!lightboxOpen.value) return;
            
            const touchEndX = event.changedTouches[0].clientX;
            const touchEndY = event.changedTouches[0].clientY;
            
            const deltaX = touchEndX - touchStartX.value;
            const deltaY = touchEndY - touchStartY.value;
            
            if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) > 50) {
                if (deltaX > 0) {
                    previousImage();
                } else {
                    nextImage();
                }
            }
        };

        const handleKeydown = (event) => {
            if (!lightboxOpen.value) return;
            
            switch (event.key) {
                case 'Escape':
                    closeLightbox();
                    break;
                case 'ArrowLeft':
                    previousImage();
                    break;
                case 'ArrowRight':
                    nextImage();
                    break;
            }
        };

        // Computed properties
        const currentImageUrl = computed(() => {
            return props.images[lightboxIndex.value] || null;
        });

        onMounted(() => {
            if (mainSwiper.value) {
                activeIndex.value = mainSwiper.value.realIndex;
            }
            document.addEventListener('keydown', handleKeydown);
        });

        onUnmounted(() => {
            document.body.style.overflow = 'auto';
            document.removeEventListener('keydown', handleKeydown);
        });

        return {
            thumbsSwiper,
            mainSwiper,
            setThumbsSwiper,
            setMainSwiper,
            slideTo,
            activeIndex,
            onSlideChange,
            modules,
            pauseAutoplay,
            resumeAutoplay,
            // Lightbox
            lightboxOpen,
            lightboxIndex,
            currentImageUrl,
            openLightbox,
            closeLightbox,
            nextImage,
            previousImage,
            goToImage,
            handleTouchStart,
            handleTouchEnd,
        };
    },
};
</script>

<style>
.swiper-wrapper {
  align-items: center !important;
}

.swiper-slide {
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  height: 100% !important;
}</style>