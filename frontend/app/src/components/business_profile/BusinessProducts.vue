<template>
    <div>
        <div class="md:flex justify-between items-center mb-4">
            <div class="flex items-center gap-1 justify-between mb-2 md:mb-0">
                <h2 class="text-lg md:text-2xl font-semibold">All Products</h2>
                <!-- Clear All Filters Button -->
                <button 
                    v-if="selectedCategories.length > 0 || searchQuery.trim()"
                    @click="clearAllFilters"
                    class="text-sm text-gray-600 hover:text-primary flex items-center gap-1 md:hidden"
                >
                    <span>Clear All</span>
                </button>
            </div>
            <div class="flex items-center gap-4">
                <!-- Clear All Filters Button -->
                <button 
                    v-if="selectedCategories.length > 0 || searchQuery.trim()"
                    @click="clearAllFilters"
                    class="text-sm text-gray-600 hover:text-primary items-center gap-1 w-full md:w-auto hidden md:flex"
                >
                    <span>Clear All</span>
                </button>
                <!-- Search Bar -->
                <div class="relative w-full md:w-auto">
                    <input 
                        v-model="searchQuery"
                        @input="debouncedSearch"
                        type="text" 
                        placeholder="Search products..."
                        class="pl-10 w-full pr-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none md:w-64"
                    >
                    <MagnifyingGlassIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" />
                </div>
                
                <!-- Category Filter -->
                <div class="flex items-center gap-2 min-h-10 z-50">
                    <span class="text-sm text-gray-600 hidden md:block">Filter by:</span>
                    <div class="relative ">
                        <button @click="showCategoryFilter = !showCategoryFilter" class="min-h-10 flex items-center gap-2 px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50">
                            <FunnelIcon class="h-4 w-4" />
                            <span class="hidden md:block">{{ selectedCategories.length > 0 ? `${selectedCategories.length} Selected` : 'All Categories' }}</span>
                            <ChevronDownIcon class="h-4 w-4" />
                        </button>
                        
                        <!-- Category Filter Dropdown -->
                        <div v-if="showCategoryFilter" class="absolute right-0 mt-2 w-64 bg-white border border-gray-200 rounded-md shadow-lg z-10 max-h-60 overflow-y-auto">
                            <div class="p-3">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium">Categories</span>
                                    <button @click="clearCategoryFilter" class="text-xs text-primary hover:underline">Clear All</button>
                                </div>
                                <div class="space-y-2">
                                    <label v-for="category in availableCategories" :key="category.id" class="flex items-center">
                                        <input 
                                            type="checkbox" 
                                            :value="category.id" 
                                            v-model="selectedCategories"
                                            class="rounded border-gray-300 text-primary focus:ring-primary accent-primary"
                                        >
                                        <span class="ml-2 text-sm" :class="{ 'font-medium text-primary': selectedCategories.includes(category.id) }">
                                            {{ category.name }}
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="loadingProducts">
            <SkeletonLoader type="business-products" />
        </div>

        <!-- Products Display -->
        <div v-else-if="categoryWiseProducts.length > 0"  @click="showCategoryFilter = false">
            <div v-for="(category, index) in categoryWiseProducts" :key="index" class="mb-6">
                <h2 class="font-semibold text-lg mb-2">{{ category.name.toUpperCase() }} ({{ category.products.length }} PRODUCTS)</h2>
                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    <div class="relative bg-white rounded-lg shadow-md overflow-hidden w-full max-w-52 group" v-for="(product, productIndex) in category.products" :key="productIndex">
                        <!-- Heart Icon for Favourite -->
                        <button 
                            @click.stop="toggleFavourite(product)"
                            class="absolute top-2 right-2 z-10 p-1 bg-white/80 rounded-full hover:bg-white transition-colors"
                        >
                            <HeartIcon v-if="!isFavourite(product.id)" class="h-4 md:h-5 w-4 md:w-5 text-gray-600 hover:text-red-500" />
                            <HeartIconSolid v-else class="h-4 md:h-5 w-4 md:w-5 text-red-500" />
                        </button>
                        
                        <router-link :to="`/product/${product.slug}`" class="flex flex-col h-full">
                            <div class="w-full h-36 md:h-48 bg-primary/10 flex-shrink-0">
                                <img :src="product.feature_image || 'https://placehold.co/180/F5F5DC/808080?text=No+Image'" :alt="product.name" class="w-full h-full object-cover" />
                            </div>
                            <div class="p-4 flex flex-col justify-between flex-1">
                                <div>
                                    <h3 class="text-sm md:text-lg font-semibold mb-1 md:mb-3 line-clamp-2 capitalize">{{ product.name }}</h3>
                                    <div class="flex flex-wrap items-baseline mb-3">
                                        <span class="text-sm md:text-xl font-poppins font-medium">৳ {{ product.price || 'N/A' }}</span>/ <span class="text-sm ml-1 font-normal">{{ getUnitDisplay(product) }}</span>
                                    </div>
                                </div>
                                <button class="w-full bg-primary text-white py-2 text-xs md:text-base font-semibold px-2 md:px-4 rounded-md hover:bg-primary/85 focus:outline-none"
                                    @click.stop.prevent="startBestPriceChat(product)" :disabled="isOwnBusiness"
                                    :class="{ 'opacity-50 cursor-not-allowed': isOwnBusiness, 'hover:bg-primary/85': !isOwnBusiness }">
                                    Get Best Price
                                </button>
                            </div>
                        </router-link>
                    </div>
                </div>
            </div>
        </div>

        <!-- No Products State -->
        <div v-else class="text-center py-12">
            <CubeIcon class="h-12 w-12 text-gray-400 mx-auto mb-4" />
            <h3 class="text-lg font-medium text-gray-900 mb-2">NO PRODUCTS FOUND</h3>
            <p class="text-gray-600">This business hasn't added any products yet.</p>
        </div>
    </div>
</template>

<script>
import SkeletonLoader from '@/components/SkeletonLoader.vue';
import {
    CubeIcon,
    FunnelIcon,
    ChevronDownIcon,
    MagnifyingGlassIcon,
    HeartIcon
} from '@heroicons/vue/24/outline';
import { HeartIcon as HeartIconSolid } from '@heroicons/vue/24/solid';

export default {
    name: 'BusinessProducts',
    components: {
        CubeIcon,
        FunnelIcon,
        ChevronDownIcon,
        MagnifyingGlassIcon,
        HeartIcon,
        HeartIconSolid,
        SkeletonLoader
    },
    props: {
        businessId: {
            type: String,
            required: true
        },
        products: {
            type: Array,
            default: () => []
        },
        loadingProducts: {
            type: Boolean,
            default: () => false
        },
    },
    data() {
        return {
            selectedCategories: [],
            showCategoryFilter: false,
            searchQuery: '',
            searchTimeout: null
        }
    },
    computed: {
        // Get unique categories from all products
        availableCategories() {
            const categories = new Map();
            this.products.forEach(product => {
                product.categories.forEach(category => {
                    if (!categories.has(category.id)) {
                        categories.set(category.id, category);
                    }
                });
            });
            return Array.from(categories.values());
        },
        
        // Filter products based on selected categories and search query
        filteredProducts() {
            let filtered = this.products;
            
            // Filter by search query
            if (this.searchQuery.trim()) {
                const query = this.searchQuery.toLowerCase().trim();
                filtered = filtered.filter(product => 
                    product.name.toLowerCase().includes(query) ||
                    product.categories.some(category => 
                        category.name.toLowerCase().includes(query)
                    )
                );
            }
            
            // Filter by selected categories
            if (this.selectedCategories.length > 0) {
                filtered = filtered.filter(product => 
                    product.categories.some(category => 
                        this.selectedCategories.includes(category.id)
                    )
                );
            }
            
            return filtered;
        },
        
        // Group products by category
        categoryWiseProducts() {
            const categoryMap = new Map();
            
            this.filteredProducts.forEach(product => {
                // If categories are selected, only show products in those categories
                if (this.selectedCategories.length > 0) {
                    // Only add product to categories that are selected
                    product.categories.forEach(category => {
                        if (this.selectedCategories.includes(category.id)) {
                            if (!categoryMap.has(category.id)) {
                                categoryMap.set(category.id, {
                                    id: category.id,
                                    name: category.name,
                                    products: []
                                });
                            }
                            categoryMap.get(category.id).products.push(product);
                        }
                    });
                } else {
                    // If no categories selected, show all products grouped by their categories
                    product.categories.forEach(category => {
                        if (!categoryMap.has(category.id)) {
                            categoryMap.set(category.id, {
                                id: category.id,
                                name: category.name,
                                products: []
                            });
                        }
                        categoryMap.get(category.id).products.push(product);
                    });
                }
            });
            
            return Array.from(categoryMap.values());
        },
        
        // Get favourite products from store
        favouriteProducts() {
            return this.$store.getters.favouriteProducts;
        },
        isOwnBusiness() {
            return this.businessId === this.$store.getters.user?.business?.id;
        }
    },
    methods: {
        // async fetchBusinessProducts() {
        //     if (!this.businessId) return;
            
        //     this.loadingProducts = true;
        //     try {
        //         const response = await axios.get(`/business/products/${this.businessId}`);
        //         if (response.data.status) {
        //             this.products = response.data.data;
        //         }
        //     } catch (error) {
        //         console.error('Error fetching business products:', error);
        //         this.products = [];
        //     } finally {
        //         this.loadingProducts = false;
        //     }
        // },
        
        clearCategoryFilter() {
            this.selectedCategories = [];
        },
        
        clearSearch() {
            this.searchQuery = '';
        },
        
        clearAllFilters() {
            this.selectedCategories = [];
            this.searchQuery = '';
        },
        
        // Debounced search for products
        debouncedSearch() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                // The search is handled by the computed property, so no additional action needed
                // This just prevents excessive re-renders while typing
            }, 300);
        },
        
        // Check if a product is in favourites
        isFavourite(productId) {
            if (!this.favouriteProducts || this.favouriteProducts.length === 0) {
                return false;
            }
            return this.favouriteProducts.some(product => product.id === productId);
        },
        
        // Toggle favourite status
        async toggleFavourite(product) {
            try {
                if (this.isFavourite(product.id)) {
                    // Remove from favourites
                    await this.$store.dispatch('removeFromFavourites', product.id);
                } else {
                    // Add to favourites
                    await this.$store.dispatch('addToFavourites', product.id);
                }
            } catch (error) {
                console.error('Failed to toggle favourite:', error);
            }
        },

        // Helper to get unit display for a product
        getUnitDisplay(product) {
            const unit = product.product_unit || {};
            if (product.unit_quantity && product.unit_quantity > 1) {
                return `Per ${product.unit_quantity} ${unit.plural_form || unit.full_form || 'Units'}`;
            }
            return unit.short_form || unit.full_form || 'Unit';
        },
        async startBestPriceChat(product) {
            try {
                const productName = product?.name || 'Product';
                const userId = this.$store.getters.user?.id || null;
                const businessId = this.businessId;
                if (!businessId) return;
                const res = await this.$store.dispatch('startConversation', {
                    business_id: businessId,
                    product_name: productName,
                    user_id: userId
                });
                const conversation = res?.data;
                if (conversation?.id) {
                    await this.$store.dispatch('fetchConversationMessages', conversation.id);
                    
                    // Create a draft message with product details
                    const productSlug = product?.slug;
                    const productId = product?.id;
                    const baseUrl = window.location.origin;
                    const productUrl = `${baseUrl}/product/${productSlug}`;
                    
                    const draftMessage = `Hi! I'm interested in getting the best price for this product: ${productUrl}\n\nProduct: ${productName}\n\nCould you please provide me with your best offer?`;
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
    },
    
    // watch: {
    //     businessId: {
    //         immediate: true,
    //         handler(newId) {
    //             if (newId) {
    //                 this.fetchBusinessProducts();
    //             }
    //         }
    //     }
    // },
    
    mounted() {
        // Close category filter when clicking outside
        document.addEventListener('click', (e) => {
            if (!this.$el.contains(e.target)) {
                this.showCategoryFilter = false;
            }
        });
    },
    
    beforeUnmount() {
        // Clean up event listener
        document.removeEventListener('click', this.handleClickOutside);
        
        // Clean up search timeout
        if (this.searchTimeout) {
            clearTimeout(this.searchTimeout);
        }
    }
}
</script> 