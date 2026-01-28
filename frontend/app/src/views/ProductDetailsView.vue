<template>
  <div>
    <BottomHeader />

    <!-- Loading State -->
    <div v-if="isLoading">
      <SkeletonLoader type="product-details" />
      <SkeletonLoader type="related-products" :count="5" />
      <SkeletonLoader type="related-categories" :count="5" />
      <SkeletonLoader type="reviews" :count="3" />
    </div>

    <!-- Loaded Content -->
    <div v-else>
      <ProductInformation
        :product="productDetails.product"
        :business="productDetails.business"
        :businessDetails="productDetails.business_details"
        v-if="productDetails"
        :favouriteProducts="favouriteProducts"
      />
      <RelatedProduct
        :products="relatedSellerProducts"
        title="More from this seller"
        :loading="isLoading"
        :favouriteProducts="favouriteProducts"
      />
      <RelatedProduct
        :products="relatedProducts"
        title="Related Products"
        :loading="isLoading"
        :favouriteProducts="favouriteProducts"
      />
      <RelatedCategory
        :categories="relatedCategories"
        title="Related Categories"
        :loading="isLoading"
      />
      <ProductFullDetails
        :product="productDetails.product"
        v-if="productDetails"
      />
      <div class="container mx-auto my-3">
        <div
          class="bg-white rounded-lg shadow-md p-4 md:p-6 mb-6 border border-gray-300"
        >
          <BusinessReviews
            :businessName="productDetails.business.business_name"
            :businessSlug="productDetails.business.slug"
            :businessId="productDetails.business.id"
            v-if="productDetails && productDetails.business"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import axios from "axios";
import { useSEO } from '@/composables/useSEO';
import BottomHeader from "@/components/header/BottomHeader.vue";
import ProductInformation from "@/components/product_details/ProductInformation.vue";
import RelatedProduct from "@/components/product_details/RelatedProduct.vue";
import RelatedCategory from "@/components/product_details/RelatedCategory.vue";
import ProductFullDetails from "@/components/product_details/ProductFullDetails.vue";
import BusinessReviews from "@/components/BusinessReviews.vue";
import SkeletonLoader from "@/components/SkeletonLoader.vue";

export default {
  components: {
    BottomHeader,
    ProductInformation,
    RelatedProduct,
    RelatedCategory,
    ProductFullDetails,
    BusinessReviews,
    SkeletonLoader,
  },
  computed: {
    ...mapGetters(["productDetails"]),
    // Use both local loading and store loading
    isLoading() {
      return this.loading || this.$store.getters.loading;
    },
    
    // Get favourite products from store
    favouriteProducts() {
        return this.$store.getters.favouriteProducts;
    },
  },
  data() {
    return {
      relatedSellerProducts: [],
      relatedCategories: [],
      relatedProducts: [],
      loading: false, // Local loading state
    };
  },
  watch: {
    "$route.params.slug": {
      handler(newId, oldId) {
        if (newId && newId !== oldId) {
          this.loadProductData();
        }
      },
      immediate: true,
    },
  },
  methods: {
    updateProductMetaTags() {
      if (this.productDetails?.product && this.productDetails?.business) {
        const { setMetaTags } = useSEO();
        const productName = this.productDetails.product.name;
        const businessName = this.productDetails.business.business_name;
        const location = this.$store.getters.selectedLocation?.replace(/, /g, '-') || 'Bangladesh';
        
        setMetaTags(
          `${productName} - ${businessName} | ${location} Comarto B2B Marketplace`,
          `Buy ${productName} from ${businessName} in ${location}. Get wholesale prices and connect with verified suppliers in Bangladesh on Comarto.`,
          this.productDetails.product.feature_image_url,
          `${productName}, ${businessName}, wholesale, suppliers, ${location}, Comarto, B2B, manufacturing, trade`
        );
      }
    },
    
    async loadProductData() {
      const slug = this.$route.params.slug;
      if (slug) {
        try {
          // Set local loading to true immediately
          this.loading = true;

          // Reset related products while loading
          this.relatedSellerProducts = [];
          this.relatedCategories = [];

          // Fetch product details
          await this.$store.dispatch("fetchProductDetails", slug);
          
          // Ensure product data is available before proceeding
          await this.$nextTick(); // Wait for Vue to update with new data

          // Set SEO meta tags for the product
          this.updateProductMetaTags();

          // Track the product category for suggested categories
          if (this.productDetails?.product?.categories?.[0]?.name) {
            this.$store.dispatch(
              "trackProductCategory",
              this.productDetails.product.categories[0].name
            );
          }

          // Fetch related seller products
          await this.fetchRelatedSellerProducts();

          // Fetch related categories
          await this.fetchRelatedCategories();

          // Fetch related products
          await this.fetchRelatedProducts();
        } catch (e) {
          console.error("Error loading product data:", e);
          // handle error (show notification, etc.)
        } finally {
          this.loading = false; // Set loading to false after data is fetched
        }
      }
    },
    async fetchRelatedSellerProducts() {
      try {
        const productId = this.productDetails?.product.id;
        const businessId = this.productDetails?.business?.id;

        if (productId && businessId) {
          const response = await axios.get(
            `/more/products/from/${businessId}/${productId}`
          );
          if (response.data.status) {
            this.relatedSellerProducts = response.data.data || [];
          }
        }
      } catch (error) {
        console.error("Error fetching related seller products:", error);
        this.relatedSellerProducts = [];
      }
    },
    async fetchRelatedProducts() {
      try {
        const productId = this.productDetails?.product.id;
        if (productId) {
          const response = await axios.get(`/related/products/${productId}`);
          if (response.data.status) {
            this.relatedProducts = response.data.data || [];
          }
        }
      } catch (error) {
        console.error("Error fetching related products:", error);
        this.relatedProducts = [];
      }
    },
    async fetchRelatedCategories() {
      try {
        const productId = this.productDetails?.product.id;
        if (productId) {
          const response = await axios.get(`/related/categories/${productId}`);
          if (response.data.status) {
            this.relatedCategories = response.data.data || [];
          }
        }
      } catch (error) {
        console.error("Error fetching related categories:", error);
        this.relatedCategories = [];
      }
    },
  },
};
</script>

<style scoped>
/* Add any specific styles here if Tailwind isn't enough, but it should be for most cases */
</style>
