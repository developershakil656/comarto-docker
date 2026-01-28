<template>
<div class="hidden lg:flex bg-white shadow-lg rounded-lg p-4 sm:p-6 lg:p-8 flex-col items-center h-fit self-start w-full">
    <img :src="user?.profile || 'https://placehold.co/150x150/0b845c/white?text=Profile'" alt="Avatar" class="w-20 h-20 sm:w-24 sm:h-24 lg:w-28 lg:h-28 rounded-full border-2 sm:border-4 border-primary object-cover mb-3 sm:mb-4" />
    <div class="text-lg sm:text-xl font-semibold mb-1 text-center">{{ user?.name || 'User' }}</div>
    <div class="text-gray-500 text-xs sm:text-sm mb-4 sm:mb-6 text-center">{{ user?.email || user?.number || 'No number' }}</div>
    <nav class="w-full">
        <ul class="space-y-2 w-full">
            <li>
                <router-link
                    to="/user/account/details"
                    class="flex items-center w-full px-3 sm:px-4 py-2 sm:py-3 rounded-md hover:bg-primary/10 transition font-medium text-sm sm:text-base"
                    :class="$route.path === '/user/account/details' ? 'text-primary bg-primary/10' : ''"
                >
                    <UserIcon class="w-4 h-4 sm:w-5 sm:h-5 mr-2 sm:mr-3" />Account Details
                </router-link>
            </li>
            <li>
                <router-link
                    to="/user/inquiries"
                    class="flex items-center w-full px-3 sm:px-4 py-2 sm:py-3 rounded-md hover:bg-primary/10 transition font-medium text-sm sm:text-base"
                    :class="$route.path === '/user/inquiries' ? 'text-primary bg-primary/10' : ''"
                >
                <ChatBubbleOvalLeftEllipsisIcon class="w-4 h-4 sm:w-5 sm:h-5 mr-2 sm:mr-3" />
                    Inquiries
                </router-link>
            </li>
            <li>
                <router-link
                    to="/user/following"
                    class="flex items-center w-full px-3 sm:px-4 py-2 sm:py-3 rounded-md hover:bg-primary/10 transition font-medium text-sm sm:text-base"
                    :class="$route.path === '/user/following' ? 'text-primary bg-primary/10' : ''"
                >
                    <img src="@/assets/icons/svgs/FollowStoreIcon.svg" alt="Follow Store" class="w-4 h-4 sm:w-5 sm:h-5 mr-2 sm:mr-3 inline" :class="{
                            'filter-primary': $route.path === '/user/following',
                        }"/>Following
                </router-link>
            </li>
            <li>
                <router-link
                    to="/user/favourites"
                    class="flex items-center w-full px-3 sm:px-4 py-2 sm:py-3 rounded-md hover:bg-primary/10 transition font-medium text-sm sm:text-base"
                    :class="$route.path === '/user/favourites' ? 'text-primary bg-primary/10' : ''"
                >
                    <HeartIcon class="mr-2 sm:mr-3 w-4 h-4 sm:w-5 sm:h-5" />Favourites
                </router-link>
            </li>
            <li>
                <router-link
                    to="/user/reviews"
                    class="flex items-center w-full px-3 sm:px-4 py-2 sm:py-3 rounded-md hover:bg-primary/10 transition font-medium text-sm sm:text-base"
                    :class="$route.path === '/user/reviews' ? 'text-primary bg-primary/10' : ''"
                >
                    <StarIcon class="w-4 h-4 sm:w-5 sm:h-5 mr-2 sm:mr-3" />Reviews
                </router-link>
            </li>
            <li>
                <router-link
                    to="/user/identity/verify"
                    class="flex items-center w-full px-3 sm:px-4 py-2 sm:py-3 rounded-md hover:bg-primary/10 transition font-medium text-sm sm:text-base"
                    :class="$route.path === '/user/identity/verify' ? 'text-primary bg-primary/10' : ''"
                >
                    <IdentificationIcon class="mr-2 sm:mr-3 w-4 h-4 sm:w-5 sm:h-5" />Verify Identity
                </router-link>
            </li>
        </ul>
    </nav>
</div>
</template>

<script>
import {
    UserIcon,
    StarIcon,
    HeartIcon,
    IdentificationIcon,
    ChatBubbleOvalLeftEllipsisIcon
} from '@heroicons/vue/24/outline';

export default {
    components: {
        UserIcon,
        StarIcon,
        HeartIcon,
        IdentificationIcon,
        ChatBubbleOvalLeftEllipsisIcon
    },
    computed: {
        user() {
            return this.$store.getters.user;
        }
    }
}
</script>
