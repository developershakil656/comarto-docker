<template>
<div class="container mx-auto my-5">
    <div class="flex flex-col md:flex-row gap-4">
        <!-- Left Column: Product Details -->
        <div class="md:w-9/12 flex-col border border-gray-300 rounded-lg shadow-md p-1">
            <div class="flex gap-2 flex-col lg:flex-row">
                <!-- Product Image and Thumbnails Slider -->
                <div class="bg-white p-3 flex items-center md:w-5/12">
                    <GallerySlider :images="galleryImages" />
                </div>

                <!-- Product Name, Price, Quantity -->
                <div class="bg-white p-3 md:w-7/12">
                    <div class="flex justify-between items-start">
                        <h1 class="text-sm md:text-xl font-semibold capitalize">{{ product.name }}</h1>
                        <button
                            @click="toggleFavourite"
                            class="transition-colors p-1"
                            :aria-label="isFavourite ? 'Remove from favourites' : 'Add to favourites'"
                        >
                            <HeartIcon v-if="!isFavourite" class="h-7 w-7 text-gray-600 hover:text-red-500" />
                            <HeartIconSolid v-else class="h-7 w-7 text-red-500" />
                        </button>
                    </div>
                    
                    <!-- Pricing -->
                    <div class="flex items-baseline mb-2 md:my-4">
                        <span class="text-sm md:text-lg font-poppins font-semibold">৳ {{ product.price }}</span> /
                        <span class="ml-1 text-sm md:text-base">{{ unitDisplay }}</span>
                    </div>
                    
                    <!-- Quantity Input and Unit Display -->
                    <Form @submit="startBestPriceChat" class="flex flex-wrap items-start  gap-2 md:gap-3 mb-6">
                        <div>
                            <Field 
                            v-slot="{ field, errorMessage }"
                            name="quantity"
                            :rules="{ required: true, positive_integer: true }"
                            v-model="quantity"
                        >
                            <input 
                                v-bind="field"
                                type="number" 
                                placeholder="Quantity" 
                                class="py-1 px-3 border border-gray-300 rounded-md w-9/12 md:w-32 focus:outline-none focus:ring-1 focus:ring-primary" 
                                :class="{ 'border-red-500': errorMessage }"
                                min="1"
                            />
                            <div v-if="errorMessage" class="text-red-500 text-sm mt-1">{{ errorMessage }}</div>
                        </Field>
                        </div>
                        <!-- <span class="py-1 px-3 border border-gray-300 rounded-md">
                            {{ unitDisplay }}
                        </span> -->
                        <button type="submit" class="bg-primary text-white px-6 py-1.5 rounded-md hover:bg-primary/85 transition w-full md:w-auto font-semibold">
                            Get Quote
                        </button>
                    </Form>

                    <!-- Specifications -->
                    <div>
                        <h2 class="text-sm md:text-base font-semibold my-2">Specification</h2>
                        <table class="table-auto border-collapse border w-full rounded-md p-2">
                            <tbody>
                                <tr v-for="[key, value] in specificationEntries" :key="key">
                                    <td class="p-2 border border-gray-200">{{ key }}</td>
                                    <td class="p-2 border border-gray-200 font-medium">{{ value }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="bg-white p-3 mt-3">
                <div class="flex justify-between items-center mb-2">
                    <h2 class="text-sm md:text-base font-semibold text-gray-800">Description</h2>
                    <button @click="scrollToDetails" class="text-primary hover:underline hover:text-primary/85 text-sm">View Details</button>
                </div>
                <p class="text-gray-700 leading-relaxed text-[15px] whitespace-pre-line">
                    {{ truncatedDescription }}
                </p>
            </div>
        </div>

        <!-- Right Column: Seller Information -->
        <div class="md:w-3/12 flex flex-col gap-6">
            <SellerInformation :business="business" :businessDetails="businessDetails" :product="product" />
        </div>
    </div>
</div>
</template>

<script>
import GallerySlider from './GallerySlider.vue';
import SellerInformation from './SellerInformation.vue';
import { mapGetters, mapActions } from 'vuex';
import { HeartIcon } from '@heroicons/vue/24/outline';
import { HeartIcon as HeartIconSolid } from '@heroicons/vue/24/solid';
import { Field, Form } from 'vee-validate';
import { required, min_value } from '@vee-validate/rules';
import { defineRule } from 'vee-validate';

// Define a custom rule for positive integers
defineRule('positive_integer', (value) => {
  if (!value) return true; // Allow empty values to be handled by required rule
  const num = parseInt(value, 10);
  return !isNaN(num) && num > 0 && Number.isInteger(parseFloat(value));
});

export default {
    components: { GallerySlider, SellerInformation, HeartIcon, HeartIconSolid, Field, Form },
    data() {
        return {
            quantity: ''
        }
    },
    props: {
        product: { type: Object, required: true },
        business: { type: Object, required: true },
        businessDetails: { type: Object, required: false },
        favouriteProducts: { type: Array, required: false, default: () => [] }
    },
    computed: {
        galleryImages() {
            const gallery = this.product.gallery;
            if (gallery && gallery.length) {
                return gallery.map(img => typeof img === 'string' ? img : img.url);
            }
            return ['https://placehold.co/400x400'];
        },
        showPerUnit() {
            return this.product.unit_quantity && this.product.unit_quantity > 1;
        },
        unitDisplay() {
            const unit = this.product.product_unit;
            if (this.showPerUnit) {
                // return `Per ${this.product.unit_quantity} ${unit?.plural_form || unit?.full_form || 'Units'}`;
                return `${this?.product?.unit_quantity} ${unit?.full_form}s`;
            }
            return unit?.full_form || 'Unit';
        },
        specificationEntries() {
            return Object.entries(this.product.specification || {}).slice(0, 7);
        },
        truncatedDescription() {
            const details = this.product.details || '';
            return details.length > 300 ? details.slice(0, 300) + '...' : details;
        },
        isFavourite() {
            return this.favouriteProducts.some(p => p.id === this.product.id);
        }
    },
    methods: {
        ...mapActions(['addToFavourites', 'removeFromFavourites']),
        

        scrollToDetails() {
            const detailsElement = document.getElementById('product-details');
            if (detailsElement) {
                // Get the header height to offset the scroll
                const header = document.querySelector('header');
                const headerHeight = header ? header.offsetHeight : 80; // fallback to 80px
                
                // Calculate the target position with offset
                const targetPosition = detailsElement.offsetTop - headerHeight - 20; // 20px extra padding
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        },
        async toggleFavourite() {
            if (this.isFavourite) {
                await this.removeFromFavourites(this.product.id);
            } else {
                await this.addToFavourites(this.product.id);
            }
        },
        
        async startBestPriceChat(values) {
            try {
                const businessId = this.business?.id;
                if (!businessId) return;
                
                const res = await this.$store.dispatch('startConversation', {
                    business_id: businessId
                });
                
                const conversation = res?.data;
                if (conversation?.id) {
                    await this.$store.dispatch('fetchConversationMessages', conversation.id);

                    // Create a draft message with product details and quantity
                    const productSlug = this.product?.slug;
                    const productId = this.product?.id;
                    const baseUrl = window.location.origin;
                    const productUrl = `${baseUrl}/product/${productSlug}`;
                    const productName = this.product?.name || 'this product';
                    const quantity = values.quantity; // Get quantity from validated form values

                    const draftMessage = `Hi! I'm interested in getting the best price for this product: ${productUrl}

                    Product: ${productName}
                    Quantity: ${quantity} (${this.unitDisplay})

                    Could you please provide me with your best offer?`;
                    localStorage.setItem(`conversation_draft_${conversation.id}`, draftMessage);
                    
                    this.$router.push({
                        name: 'conversation-messages',
                        params: { id: conversation?.id }
                    });
                }
            } catch (e) {
                console.error('Failed to start best-price chat:', e);
            }
        }
    }
}
</script>

