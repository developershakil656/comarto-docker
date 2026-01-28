<template>
    <!-- bottom header -->
    <div class="hidden md:block">
        <div class="container mx-auto p-3 border">
            <!-- <div class="flex items-center justify-between gap-4 font-semibold"> -->
            <!-- Navigation Row -->
            <div class="flex items-center justify-between">
                <!-- Left: Browse Category -->
                <div class="relative z-50">
                    <button @click="toggleDropdown" ref="categoryBtn" class="flex items-center bg-primary text-white px-4 py-2 rounded-md font-semibold text-base shadow-none transition hover:bg-primary/85 relative">
                        <Squares2X2Icon class="inline-block h-5 w-5 mr-2" />
                        Browse Category
                        <ChevronDownIcon class="inline-block h-4 w-4 ml-2" />
                    </button>

                    <div class="paper-content" :class="{ 'category-active': showDropdown }">
                        <!-- Dropdown Menu -->
                        <div v-if="showDropdown" @click.stop>
                            <!-- Level 1 -->
                            <ul class="category-list">
                                <CategoryLoop v-for="(category, index) in categories" :key="index" :category="category" />
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Center: Navigation Links -->
                <nav class="flex space-x-8 font-semibold text-base">
                    <a href="#" class="text-primary">Home</a>
                    <a href="#" class="hover:text-primary/85">Trending</a>
                    <a href="#" class="hover:text-primary/85">Daily Deals</a>
                    <a href="#" class="hover:text-primary/85">Flash Sale</a>
                </nav>

                <!-- Right: Contact and Cart -->
                <div class="flex items-center space-x-4">
                    <button @click="openInquiryModal" class="text-black text-sm px-4 py-2 rounded-md font-semibold shadow-none transition hover:bg-gray-200 duration-200 ease-in-out">
                        <ChatBubbleOvalLeftEllipsisIcon class="h-5 w-5 inline-block" />
                        Team Up with Suppliers
                    </button>
                    <!-- <button href="#" class="text-black px-4 py-2 rounded-md font-semibold shadow-none transition hover:bg-gray-200 duration-200 ease-in-out">
                        <MegaphoneIcon class="h-5 w-5 inline-block" />
                        Advertise
                    </button> -->
                    <button href="#" class="text-black px-4 py-2 rounded-md font-semibold shadow-none transition hover:bg-gray-200 duration-200 ease-in-out">
                        <HeartIcon class="h-5 w-5 inline-block" />
                        Favourite
                    </button>
                </div>
            </div>
            <!-- </div> -->
        </div>
    </div>
    
    <!-- Inquiry Modal -->
    <InquiryModal v-if="showInquiryModal" @close="closeInquiryModal" />
</template>
<script>
import CategoryLoop from '@/components/CategoryLoop.vue'
import {
    ChevronDownIcon,
    Squares2X2Icon,
    ChevronRightIcon,
    MegaphoneIcon,
    HeartIcon,
    ChatBubbleOvalLeftEllipsisIcon
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
        ChatBubbleOvalLeftEllipsisIcon
    },
    data() {
        return {
            showDropdown: false,
            showInquiryModal: false,
            categories: [{
                    name: "Food",
                    children: [{
                            name: "Fruits & Vegetables",
                            children: [{
                                name: "Citrus",
                                children: [{
                                        name: "Orange",
                                    },
                                    {
                                        name: "Lemon",
                                    },
                                ],
                            }, ],
                        },
                        {
                            name: "Meat & Fish",
                            children: [{
                                    name: "Chicken",
                                    children: [{
                                            name: "Broiler",
                                        },
                                        {
                                            name: "Country Chicken",
                                        },
                                    ],
                                },
                                {
                                    name: "Beef",
                                },
                                {
                                    name: "Frozen Fish",
                                },
                            ],
                        },
                    ],
                },
                {
                    name: "Drinks",
                    children: [{
                            name: "Soft Drinks",
                            children: [{
                                name: "Fresh",
                                children: [{
                                        name: "Cola",
                                    },
                                    {
                                        name: "Googly",
                                    },
                                ],
                            }, {
                                name: "Akij",
                                children: [{
                                        name: "Mojo",
                                    },
                                    {
                                        name: "Cleamon",
                                    },
                                ],
                            }, ],
                        },
                        {
                            name: "Water",
                            children: [{
                                    name: "Mum",
                                },
                                {
                                    name: "Mukta",
                                },
                            ],
                        },
                    ],
                },
            ],
        };
    },
    methods: {
        toggleDropdown() {
            this.showDropdown = !this.showDropdown;
        },
        closeDropdown() {
            this.showDropdown = false;
        },
        handleClickOutside(event) {
            if (
                this.$refs.categoryBtn &&
                !this.$refs.categoryBtn.contains(event.target)
            ) {
                this.closeDropdown();
            }
        },
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
    },
    mounted() {
        document.addEventListener("click", this.handleClickOutside);
    },
    beforeDestroy() {
        document.removeEventListener("click", this.handleClickOutside);
    },
}
</script>