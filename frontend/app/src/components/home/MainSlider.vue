<template>
<div>
    <div class="">
        <swiper :navigation="true" :pagination="{ clickable: true }" :modules="modules" :loop="true" :speed="1000" :autoplay="{
            delay: 5000,
            disableOnInteraction: false
        }" class="bg-gray-300 rounded-lg text-center main-swiper container">
            <swiper-slide><img class="w-full h-full object-cover" src="https://admin-api.comarto.com/storage/sliders/wholesale-business-from-teknaf-to-tetuliya.png" alt=""></swiper-slide>
            <swiper-slide><img class="w-full h-full object-cover" src="https://admin-api.comarto.com/storage/sliders/suppliers-in-bangladesh.png" alt=""></swiper-slide>
            <swiper-slide><img class="w-full h-full object-cover" src="https://admin-api.comarto.com/storage/sliders/local-wholesale-businesses-in-bangladesh.png" alt=""></swiper-slide>
        </swiper>
    </div>
</div>
</template>

<script>
import {
    Swiper,
    SwiperSlide
} from 'swiper/vue';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import {
    Navigation,
    Autoplay,
    Pagination
} from 'swiper/modules';
export default {
    components: {
        Swiper,
        SwiperSlide,
        Pagination
    },
    setup() {
        return {
            modules: [Navigation, Autoplay, Pagination],
        };
    },
}
</script>

<style>
/* responsive aspect ratio for main slider */
.main-swiper {
    aspect-ratio: 4 / 1.9;
    /* width: 100%;
    height: auto; */
}

/* slider custom css */
.main-swiper .swiper-button-prev {
    padding: 23px;
    visibility: hidden;
    left: 40px;
    right: inherit !important;
    height: 2rem;
    width: 2rem;
    border-radius: 9999px;
    --tw-bg-opacity: 1;
    background-color: rgb(255 255 255 / var(--tw-bg-opacity, 1));
    text-align: center;
    line-height: 2rem;
    --tw-text-opacity: 1;
    color: rgb(31 31 57 / var(--tw-text-opacity, 1));
    opacity: 0;
    --tw-shadow: 0 10px 15px -3px rgb(0 0 0 / .1), 0 4px 6px -4px rgb(0 0 0 / .1);
    --tw-shadow-colored: 0 10px 15px -3px var(--tw-shadow-color), 0 4px 6px -4px var(--tw-shadow-color);
    box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
    transition-property: all;
    transition-timing-function: cubic-bezier(.4, 0, .2, 1);
    transition-duration: .3s
}

.main-swiper .swiper-button-prev:after {
    /* font-family: var(--icon-font); */
    font-size: 1.125rem;
    line-height: 1.75rem;
    /* --tw-content: "î‰" !important; */
    /* content: var(--tw-content)!important */
}

.main-swiper .swiper-button-prev:after {
    font-size: 1.1rem;
}

.main-swiper .swiper-button-prev:hover {
    background-color: #0b845ccc;
    color: #fff;
}

@media (max-width: 640px) {
    .main-swiper .swiper-button-prev {
        padding: 10px;
        /* left:1.75rem!important;
        height: 2.5rem;
        width: 2.5rem;
        line-height: 2.5rem */
    }

    .main-swiper .swiper-button-prev:after {
        /* content: var(--tw-content); */
        font-size: 0.8rem;
        /* line-height: 1.75rem */
    }
}


.main-swiper .swiper-button-next {
    padding: 23px;
    visibility: hidden;
    right: 40px;
    left: inherit !important;
    height: 2rem;
    width: 2rem;
    border-radius: 9999px;
    --tw-bg-opacity: 1;
    background-color: rgb(255 255 255 / var(--tw-bg-opacity, 1));
    text-align: center;
    line-height: 2rem;
    --tw-text-opacity: 1;
    color: rgb(31 31 57 / var(--tw-text-opacity, 1));
    opacity: 0;
    --tw-shadow: 0 10px 15px -3px rgb(0 0 0 / .1), 0 4px 6px -4px rgb(0 0 0 / .1);
    --tw-shadow-colored: 0 10px 15px -3px var(--tw-shadow-color), 0 4px 6px -4px var(--tw-shadow-color);
    box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
    transition-property: all;
    transition-timing-function: cubic-bezier(.4, 0, .2, 1);
    transition-duration: .3s
}

.main-swiper .swiper-button-next:after {
    font-size: 1.1rem;
}

.main-swiper .swiper-button-next:hover {
    background-color: #0b845ccc;
    color: #fff;
}

/* @media (min-width: 640px) {
    .main-swiper .swiper-button-next {
        right:1.75rem!important;
        height: 2.5rem;
        width: 2.5rem;
        line-height: 2.5rem
    }

    .main-swiper .swiper-button-next:after {
        content: var(--tw-content);
        font-size: 1.25rem;
        line-height: 1.75rem
    }
} */
@media (max-width: 640px) {
    .main-swiper .swiper-button-next {
        padding: 10px;
    }

    .main-swiper .swiper-button-next:after {
        font-size: 0.8rem;
    }
}

.main-swiper:hover .swiper-button-prev,
.main-swiper:hover .swiper-button-next {
    visibility: visible;
    opacity: 1
}

/* slider custom css end */

/* pagination button  */
.swiper-pagination-bullet {
    height: .375rem;
    width: .75rem;
    flex-shrink: 0;
    border-radius: .75rem;
    --tw-bg-opacity: 1;
    background-color: #0b845ccc;
    opacity: .3;
    --tw-shadow: 0 1px 3px 0 rgb(0 0 0 / .1), 0 1px 2px -1px rgb(0 0 0 / .1);
    --tw-shadow-colored: 0 1px 3px 0 var(--tw-shadow-color), 0 1px 2px -1px var(--tw-shadow-color);
    box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
    transition-property: all;
    transition-timing-function: cubic-bezier(.4, 0, .2, 1);
    transition-duration: .4s
}

.swiper-pagination-bullet-active {
    width: 1rem;
    flex-shrink: 0;
    opacity: 1
}
</style>
