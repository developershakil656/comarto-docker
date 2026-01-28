<template>
    <div>
        <svg class="w-20 h-20" viewBox="0 0 36 36">
                                <!-- Background circle -->
                                <circle
                                    class="text-gray-200"
                                    stroke-width="2.5"
                                    stroke="currentColor"
                                    fill="none"
                                    cx="18"
                                    cy="18"
                                    r="16"
                                />
                                <!-- Progress arc -->
                                <circle
                                    class="text-yellow-500"
                                    stroke-width="2.5"
                                    stroke-linecap="round"
                                    stroke="currentColor"
                                    fill="none"
                                    cx="18"
                                    cy="18"
                                    r="16"
                                    :stroke-dasharray="circumference"
                                    :stroke-dashoffset="progressOffset"
                                />
                            </svg>
                            <span class="absolute inset-0 flex items-center justify-center font-bold text-yellow-500 text-lg">{{ percent }}%</span>
    </div>
</template>
<script>
export default {
    data() {
        return {
            percent: 70,
            radius: 16
        }
    },
    computed: {
        circumference() {
            return 2 * Math.PI * this.radius;
        },
        progressOffset() {
            return this.circumference * (1 - this.percent / 100);
        }
    }
}
</script>