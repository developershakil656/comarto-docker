<template>
    <div>
         <!-- bottom header -->
    <div class="hidden md:block border">
        <div class="container mx-auto p-3">
            <!-- <div class="flex items-center justify-between gap-4 font-semibold"> -->
            <!-- Navigation Row -->
            <div class="flex items-center justify-between text-xs lg:text-sm font-semibold">
                <!-- Left: Browse Category -->
                <div class="relative z-50">

                    <button @click="openInquiryModal" class="flex gap-1 items-center bg-primary text-white px-4 py-2 rounded-md shadow-none transition hover:bg-primary/85">
                        <ChatBubbleOvalLeftEllipsisIcon class="h-5 w-5 inline-block" />
                        Post Buy Requirement
                    </button>
                </div>

                <!-- Center: Navigation Links -->
                <nav class="flex space-x-8">
                    <router-link to="/" class="hover:text-primary/85" active-class="text-primary">Home</router-link>
                    <router-link to="/categories" class="hover:text-primary/85" active-class="text-primary">All Categories</router-link>
                    <!-- <router-link to="/category/home-supplies" class="hover:text-primary/85" active-class="text-primary">Home Supplies</router-link> -->
                    <router-link to="/category/apparel-fashion" class="hover:text-primary/85" active-class="text-primary">Apparel & Fashion</router-link>
                    <!-- <router-link to="/category/food-beverage" class="hover:text-primary/85" active-class="text-primary">Food & Beverage</router-link> -->
                    <router-link to="/category/industrial-supplies" class="hover:text-primary/85" active-class="text-primary">Industrial Supplies</router-link>
                </nav>

                <!-- Right: Contact and Cart -->
                <div class="flex items-center space-x-4">
                    <!-- <router-link to="/categories" class="hover:text-primary/85">Contact with us</router-link> -->
                    <button @click="openContactEmail" class="flex gap-1 items-center text-black px-4 py-2 rounded-md font-semibold shadow-none transition hover:bg-gray-200 duration-200 ease-in-out">
                        <EnvelopeIcon class="w-5 h-5 inline-block" />
                        Contact with us
                    </button>
                </div>
            </div>
            <!-- </div> -->
        </div>
    </div>
    
    <!-- Inquiry Modal -->
    <InquiryModal v-if="showInquiryModal" @close="closeInquiryModal" />
    </div>
</template>
<script>
import CategoryLoop from '@/components/CategoryLoop.vue'
import {
    ChevronDownIcon,
    Squares2X2Icon,
    ChevronRightIcon,
    MegaphoneIcon,
    HeartIcon,
    ChatBubbleOvalLeftEllipsisIcon,
    EnvelopeIcon
} from "@heroicons/vue/24/outline";
import InquiryModal from '@/components/modals/InquiryModal.vue'

export default {
    components: {
        CategoryLoop,
        ChevronDownIcon,
        Squares2X2Icon,
        ChevronRightIcon,
        MegaphoneIcon,
        HeartIcon,
        InquiryModal,
        ChatBubbleOvalLeftEllipsisIcon,
        EnvelopeIcon
    },
    data() {
        return {
            showDropdown: false,
            showInquiryModal: false,
        };
    },
    methods: {
        openInquiryModal() {
            // if (!this.$store.getters.isAuthenticated) {
            //     // Show login modal or redirect to login
            //     this.$notify.error('Please login to submit inquiries');
            //     return;
            // }
            this.showInquiryModal = true;
        },
        closeInquiryModal() {
            this.showInquiryModal = false;
        },
        openContactEmail() {
            window.location.href = 'mailto:comarto.com@gmail.com';
        },
    },
}
</script>