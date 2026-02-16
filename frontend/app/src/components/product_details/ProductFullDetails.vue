<template>
<div id="product-details" class="container mx-auto my-3">
    <!-- Left Column: Product Details -->
    <div class="border border-gray-300 rounded-lg shadow-md p-4">
        
        <div class="mb-6">
            <h2 class="text-lg md:text-2xl font-semibold mb-4 capitalize">Product details of '{{ product.name }}'</h2>
            <p class="text-gray-700 leading-relaxed text-[15px] mb-4 whitespace-pre-line">
                {{ product.details }}
            </p>
        </div>

        <!-- Specifications -->
        <div class="">
            <h2 class="text-sm md:text-xl font-semibold mb-4">Specification</h2>

            <table class="table-auto border-collapse border w-full rounded-md p-2 text-gray-900">
                <tbody>
                    <tr v-for="entry in Object.entries(product.specification)" :key="entry[0]">
                        <td class="p-3 border border-gray-200 font-medium">{{ entry[0] }}</td>
                        <td class="p-3 border border-gray-200 font-semibold">{{ entry[1] }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div v-if="product.video_url" class="mt-8">
            <div class="w-5/6 aspect-video rounded overflow-hidden mx-auto -mb-4">
                <iframe
                    :src="`https://www.youtube.com/embed/${extractYouTubeId(product.video_url)}`"
                    frameborder="0"
                    allowfullscreen
                    class="w-full h-5/6 rounded-md"
                ></iframe>
            </div>
        </div>
    </div>
</div>
</template>

<script>
export default {
    props: {
        product: {
            type: Object,
            required: true
        }
    },
    methods: {
        extractYouTubeId(url) {
            // Handles various YouTube URL formats
            const regExp = /(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?|shorts)\/|.*[?&]v=)|youtu\.be\/)([\w-]{11})/;
            const match = url.match(regExp);
            return match ? match[1] : '';
        }
    }
}
</script>
