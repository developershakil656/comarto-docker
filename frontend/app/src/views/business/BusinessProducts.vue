<template>
  <div>
  <div>
    <AdminDashboardHeader title="Business Products" class="hidden md:block"/>
    <div class="max-w-6xl md:mx-auto sm:m-4 m-2 p-4 md:p-6 shadow rounded-md border md:my-4">
      <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4 mb-6">
        <!-- Filters -->
        <div class="flex flex-col sm:flex-row gap-4">
          <!-- Status Filter -->
          <div class="relative">
            <select
              v-model="statusFilter"
              @change="page = 1"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-primary focus:ring-primary/20 outline-none transition text-base bg-white"
            >
              <option value="">All Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
          
          <!-- Stock Filter -->
          <div class="relative">
            <select
              v-model="stockFilter"
              @change="page = 1"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-primary focus:ring-primary/20 outline-none transition text-base bg-white"
            >
              <option value="">All Stock</option>
              <option value="in stock">In Stock</option>
              <option value="out of stock">Out of Stock</option>
            </select>
          </div>
        </div>
        
        <!-- Actions -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
          <!-- Add Product Button -->
          <router-link 
            to="/my/business/add/product" 
            class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors font-medium flex items-center gap-2"
          >
            <PlusIcon class="w-5 h-5" />
            Add Product
          </router-link>
          
          <!-- Search Input -->
          <div class="relative">
            <input
              v-model="search"
              type="text"
              class="w-full rounded-lg border border-gray-300 pl-10 pr-4 py-2 focus:border-primary focus:ring-primary/20 outline-none transition text-base"
              placeholder="Search products..."
            />
            <MagnifyingGlassIcon class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" />
          </div>
        </div>
      </div>
      
      <!-- Filter Summary -->
      <div v-if="hasActiveFilters" class="mb-4 flex flex-col md:flex-row items-start md:items-center justify-between gap-3 bg-gray-50 px-4 py-3 rounded-lg">
        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-gray-600">
          <span v-if="statusFilter" class="flex items-center gap-2">
            <span class="font-medium">Status:</span>
            <span class="px-2 py-1 bg-primary-100 text-primary-800 rounded-full text-xs">{{ statusFilter }}</span>
          </span>
          <span v-if="stockFilter" class="flex items-center gap-2">
            <span class="font-medium">Stock:</span>
            <span class="px-2 py-1 bg-primary-100 text-primary-800 rounded-full text-xs">{{ stockFilter }}</span>
          </span>
          <span v-if="search" class="flex items-center gap-2">
            <span class="font-medium">Search:</span>
            <span class="px-2 py-1 bg-primary-100 text-primary-800 rounded-full text-xs">"{{ search }}"</span>
          </span>
        </div>
        <button
          @click="clearFilters"
          class="text-sm text-primary-600 hover:text-primary-800 font-medium"
        >
          Clear All Filters
        </button>
      </div>
      
      <div class="overflow-x-auto bg-white rounded-xl shadow min-h-screen">
        <div v-if="myProductsLoading" class="p-6 text-center text-gray-500">Loading products...</div>
        <table class="min-w-full divide-y divide-gray-200 text-sm md:text-base">
          <thead class="bg-primary/20">
            <tr>
              <th class="px-4 py-4 text-left text-xs font-medium uppercase tracking-wider">Image</th>
              <th class="px-4 py-4 text-left text-xs font-medium uppercase tracking-wider cursor-pointer" @click="sortBy('name')">
                Product Name
                <ChevronUpDownIcon class="inline w-4 h-4 ml-1 text-gray-400" />
              </th>
              <th class="px-4 py-4 text-left text-xs font-medium uppercase tracking-wider cursor-pointer" @click="sortBy('details')">
                Details
                <ChevronUpDownIcon class="inline w-4 h-4 ml-1 text-gray-400" />
              </th>
              <th class="px-4 py-4 text-left text-xs font-medium uppercase tracking-wider cursor-pointer" @click="sortBy('price')">
                Price
                <ChevronUpDownIcon class="inline w-4 h-4 ml-1 text-gray-400" />
              </th>
              <th class="px-4 py-4 text-left text-xs font-medium uppercase tracking-wider">Status</th>
              <th class="px-4 py-4 text-left text-xs font-medium uppercase tracking-wider">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="product in paginatedProducts" :key="product.id" class="hover:bg-gray-50">
              <td class="px-4 py-3">
                <OptimizedImage :src="featureImage(product)" alt="Product" class="max-w-36 w-28 h-24 md:w-36 md:h-24 object-cover rounded-md bg-gray-100" />
              </td>
              <td class="px-4 py-3 font-medium text-gray-900">{{ product.name }}</td>
              <td class="px-4 py-3 text-gray-700">{{ truncate(product.details, 150) }}</td>
              <td class="px-4 py-3 text-gray-700">{{ product.price }}<span v-if="product.product_unit"> / {{ getUnitDisplay(product) }}</span></td>
              <td class="px-4 py-3">
                <div class="flex flex-col gap-1">
                  <span 
                    :class="[
                      'px-2 py-1 text-xs font-medium rounded-full',
                      product.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                    ]"
                  >
                    {{ product.status }}
                  </span>
                  <span 
                    :class="[
                      'px-2 py-1 text-xs font-medium rounded-full min-w-max',
                      product.stock === 'in stock' ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800'
                    ]"
                  >
                    {{ product.stock }}
                  </span>
                </div>
              </td>
              <td class="px-4 py-3">
                <router-link :to="`/product/${product.slug}`" class="w-20 px-3 py-2 mb-1 flex items-center justify-center gap-1 rounded bg-green-500 text-white hover:bg-green-600 text-xs font-semibold mr-2">
                  <EyeIcon class="w-4 h-4" /> Show
                </router-link>
                <router-link :to="`/my/business/product/edit/${product.id}`" class="w-20 px-3 py-2 mb-1 flex items-center justify-center gap-1 rounded bg-blue-500 text-white hover:bg-blue-600 text-xs font-semibold mr-2">
                  <PencilSquareIcon class="w-4 h-4" /> Edit
                </router-link>
                <button class="w-20 px-3 py-2 flex items-center justify-center gap-1 rounded bg-red-500 text-white hover:bg-red-600 text-xs font-semibold" @click="openDeleteModal(product)">
                  <TrashIcon class="w-4 h-4" /> Delete
                </button>
              </td>
            </tr>
            <tr v-if="!myProductsLoading && paginatedProducts.length === 0">
              <td colspan="6" class="text-center py-6 text-gray-400">No products found.</td>
            </tr>
          </tbody>
        </table>
        <!-- Pagination -->
        <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-t border-gray-100">
          <div class="text-sm text-gray-600">Page {{ page }} of {{ totalPages }}</div>
          <div class="flex gap-2">
            <button @click="prevPage" :disabled="page === 1" class="px-3 py-1 rounded border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 disabled:opacity-50">Prev</button>
            <button @click="nextPage" :disabled="page === totalPages" class="px-3 py-1 rounded border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 disabled:opacity-50">Next</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Product Modal -->
    <DeleteProductModal
      :show="showDeleteModal"
      :product-id="productToDelete?.id"
      :product-name="productToDelete?.name"
      @close="closeDeleteModal"
      @product-deleted="onProductDeleted"
      @error="onDeleteError"
    />
  </div>
  <!-- Mobile Navigation -->
  <BusinessMobileBottomNavigation />
  </div>
</template>

<script>
import { MagnifyingGlassIcon, ChevronUpDownIcon, PencilSquareIcon, TrashIcon, EyeIcon, PlusIcon } from '@heroicons/vue/24/outline';
import AdminDashboardHeader from '@/components/business/AdminDashboardHeader.vue';
import DeleteProductModal from '@/components/business/DeleteProductModal.vue';
import { mapActions, mapGetters } from 'vuex';
import BusinessMobileBottomNavigation from '@/components/business/BusinessMobileBottomNavigation.vue';
import { push } from 'notivue';
import { ref, computed, watch, nextTick } from 'vue';
import OptimizedImage from '@/components/common/OptimizedImage.vue';

export default {
  name: 'BusinessProducts',
  components: {
    AdminDashboardHeader,
    MagnifyingGlassIcon,
    ChevronUpDownIcon,
    PencilSquareIcon,
    TrashIcon,
    EyeIcon,
    PlusIcon,
    DeleteProductModal,
    BusinessMobileBottomNavigation,
    OptimizedImage
  },
  data() {
    return {
      statusFilter: '',
      stockFilter: '',
      search: '',
      sortKey: '',
      sortAsc: true,
      page: 1,
      pageSize: 5,
      showDeleteModal: false,
      productToDelete: null
    };
  },
  computed: {
    ...mapGetters(['myProducts', 'myProductsLoading']),
    hasActiveFilters() {
      return this.statusFilter || this.stockFilter || this.search;
    },
    filteredProducts() {
      const source = Array.isArray(this.myProducts) ? this.myProducts : [];
      let filtered = source.filter(p => {
        // Status filter
        if (this.statusFilter && p.status !== this.statusFilter) {
          return false;
        }
        
        // Stock filter
        if (this.stockFilter && p.stock !== this.stockFilter) {
          return false;
        }
        
        // Search filter
        if (this.search) {
          const searchLower = this.search.toLowerCase();
          const nameMatch = (p.name || '').toLowerCase().includes(searchLower);
          const detailsMatch = (p.details || '').toLowerCase().includes(searchLower);
          if (!nameMatch && !detailsMatch) {
            return false;
          }
        }
        
        return true;
      });
      
      if (this.sortKey) {
        filtered = [...filtered].sort((a, b) => {
          let valA = a[this.sortKey];
          let valB = b[this.sortKey];
          if (typeof valA === 'string') valA = valA.toLowerCase();
          if (typeof valB === 'string') valB = valB.toLowerCase();
          if (valA < valB) return this.sortAsc ? -1 : 1;
          if (valA > valB) return this.sortAsc ? 1 : -1;
          return 0;
        });
      }
      return filtered;
    },
    paginatedProducts() {
      const start = (this.page - 1) * this.pageSize;
      return this.filteredProducts.slice(start, start + this.pageSize);
    },
    totalPages() {
      return Math.ceil(this.filteredProducts.length / this.pageSize) || 1;
    }
  },
  methods: {
    ...mapActions(['fetchMyProducts', 'deleteUserProduct']),
    sortBy(key) {
      if (this.sortKey === key) {
        this.sortAsc = !this.sortAsc;
      } else {
        this.sortKey = key;
        this.sortAsc = true;
      }
      push.info(`Sorted by ${key} (${this.sortAsc ? 'ascending' : 'descending'})`);
    },
    
    prevPage() {
      if (this.page > 1) {
        this.page--;
      }
    },
    
    nextPage() {
      if (this.page < this.totalPages) {
        this.page++;
      }
    },
    
    clearFilters() {
      this.statusFilter = '';
      this.stockFilter = '';
      this.search = '';
      this.page = 1;
      push.info('Filters cleared');
    },
    truncate(text, length) {
      const t = text || '';
      return t.length > length ? `${t.slice(0, length)}...` : t;
    },
    featureImage(product) {
      const gallery = Array.isArray(product.gallery) ? product.gallery : [];
      return gallery[0]?.url || 'https://placehold.co/144x96?text=No+Image';
    },
    openDeleteModal(product) {
      this.productToDelete = product;
      this.showDeleteModal = true;
    },
    closeDeleteModal() {
      this.showDeleteModal = false;
      this.productToDelete = null;
    },
    onProductDeleted(productId, message) {
      // If current page becomes empty after deletion, move back a page if possible
      if (this.paginatedProducts.length === 0 && this.page > 1) {
        this.page -= 1;
      }
      // Show success message
      push.success(message || 'Product deleted successfully');
    },
    
    onDeleteError(errorMessage) {
      // Show error message
      push.error(errorMessage || 'Failed to delete product');
    },
    getUnitDisplay(product) {
      const unit = product.product_unit || {};
      if (product.unit_quantity && product.unit_quantity > 1) {
        return `${product.unit_quantity} ${unit.plural_form }`;
      }
      return unit.short_form || unit.full_form || 'Unit';
    }
  },
  watch: {
    statusFilter() {
      this.page = 1;
    },
    stockFilter() {
      this.page = 1;
    },
    search() {
      this.page = 1;
    }
  },
  mounted() {
    this.fetchMyProducts();
  }
};
</script>

<!-- <style scoped>
.bg-primary {
  background-color: #2563eb;
}
.text-primary {
  color: #2563eb;
}
</style>  -->