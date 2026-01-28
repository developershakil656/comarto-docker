<template>
<div class="flex">
    <div v-for="i in 5" :key="i">
        <div @click="handleStarClick(i)" @mouseenter="hoveredRating = i" @mouseleave="hoveredRating = 0">
            <StarIcon class="h-10 p-1 m-1 border rounded-md cursor-pointer transition-colors duration-200" :class="getStarClass(i)" />
        </div>
    </div>
</div>
</template>

<script>
import {
    StarIcon
} from "@heroicons/vue/24/outline";

export default {
    name: 'MakeReview',
    components: {
        StarIcon
    },
    props: {
        businessId: {
            type: String,
            required: true
        },
        businessSlug: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            selectedRating: 0,
            hoveredRating: 0
        }
    },
    methods: {
        getStarClass(starIndex) {
            const effectiveRating = this.hoveredRating || this.selectedRating;
            if (starIndex <= effectiveRating) {
                return 'text-yellow-400 fill-current';
            }
            return 'text-gray-400';
        },
        
        handleStarClick(rating) {
            this.selectedRating = rating;
            // Navigate to review page with the selected rating
            this.$router.push({
                name: 'make-review',
                params: {
                    // businessId: this.businessId,
                    businessSlug: this.businessSlug
                },
                query: {
                    rating: rating
                }
            });
        }
    }
}
</script>
