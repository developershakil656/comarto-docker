<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center admin-header">
      <h1 class="text-2xl font-bold text-gray-900">Products</h1>
      <router-link 
        to="/products/create"
        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
      >
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Add Product
      </router-link>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-sm p-6 admin-card">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 admin-filters">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Keyword</label>
          <input
            v-model="filters.keyword"
            type="text"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
            placeholder="Search by name, slug..."
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
          <select 
            v-model="filters.status"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 appearance-none"
          >
            <option value="">All</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="blocked">Blocked</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
          <input
            v-model="filters.start_date"
            type="date"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
          <input
            v-model="filters.end_date"
            type="date"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Location ID</label>
          <input
            v-model="filters.location_id"
            type="number"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
            placeholder="Enter location ID"
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Category ID</label>
          <input
            v-model="filters.category_id"
            type="number"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
            placeholder="Enter category ID"
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Business ID</label>
          <input
            v-model="filters.business_id"
            type="number"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
            placeholder="Enter business ID"
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Per Page</label>
          <select 
            v-model="filters.per_page"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 appearance-none"
          >
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
        </div>
        <div class="flex items-end space-x-2">
          <button 
            @click="applyFilters"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors"
          >
            Apply Filters
          </button>
          <button 
            @click="resetFilters"
            class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors"
          >
            Reset
          </button>
        </div>
      </div>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden admin-card">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 table-auto responsive-table">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Product</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Business</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">MOQ</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="product in products" :key="product.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" data-label="ID">{{ product.id }}</td>
              <td class="px-6 py-4 whitespace-nowrap" data-label="Product">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-10 w-10">
                    <img v-if="product.feature_image" :src="product.feature_image" :alt="product.name || 'Product'" class="h-10 w-10 rounded-md">
                    <div v-else class="h-10 w-10 rounded-md bg-indigo-100 flex items-center justify-center">
                      <span class="text-indigo-800 font-medium">{{ (product.name || '?').charAt(0) }}</span>
                    </div>
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">{{ product.name || 'N/A' }}</div>
                    <div class="text-sm text-gray-500">{{ product.slug || 'N/A' }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" data-label="Business">
                <div v-if="product.business">{{ product.business.business_name }}</div>
                <div v-else>N/A</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" data-label="Price">
                {{ product.currency }} {{ product.price || 'N/A' }} / {{ product.product_unit?.short_form }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" data-label="Stock">
                {{ product.stock || 'N/A' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" data-label="MOQ">
                {{ product.moq || 'N/A' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap" data-label="Status">
                <select
                  v-model="product.status"
                  @change="updateProductStatus(product.id, product.status)"
                  class="admin-select text-sm min-w-[120px]"
                  value="product.status"
                  :class="{
                    'text-green-500': product.status === 'active',
                    'text-yellow-500': product.status === 'inactive',
                    'text-red-500': product.status === 'blocked'
                  }"
                >
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                  <option value="blocked">Blocked</option>
                </select>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" data-label="Actions">
                <div class="flex items-center space-x-3">
                  <router-link
                    :to="`/products/${product.id}/edit`"
                    class="text-blue-600 hover:text-blue-900 transition-colors"
                    title="Edit Product"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                  </router-link>
                  <button
                    @click="deleteProduct(product.id)"
                    class="text-red-600 hover:text-red-900 transition-colors"
                    title="Delete Product"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
        <div class="flex-1 flex justify-between sm:hidden">
          <button 
            @click="changePage(pagination.current_page - 1)"
            :disabled="pagination.current_page === 1"
            class="admin-button-secondary"
          >
            Previous
          </button>
          <button 
            @click="changePage(pagination.current_page + 1)"
            :disabled="pagination.current_page === pagination.last_page"
            class="admin-button-secondary ml-3"
          >
            Next
          </button>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
          <div>
            <p class="text-sm text-gray-700">
              Showing
              <span class="font-medium">{{ (pagination.current_page - 1) * pagination.per_page + 1 }}</span>
              to
              <span class="font-medium">{{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }}</span>
              of
              <span class="font-medium">{{ pagination.total }}</span>
              results
            </p>
          </div>
          <div>
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
              <button 
                @click="changePage(pagination.current_page - 1)"
                :disabled="pagination.current_page === 1"
                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <span class="sr-only">Previous</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
              </button>
              <button 
                v-for="page in pageNumbers"
                :key="page"
                @click="changePage(page)"
                :class="[
                  page === pagination.current_page ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                  'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
                ]"
              >
                {{ page }}
              </button>
              <button 
                @click="changePage(pagination.current_page + 1)"
                :disabled="pagination.current_page === pagination.last_page"
                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <span class="sr-only">Next</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
              </button>
            </nav>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading Spinner -->
    <div v-if="loading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-white"></div>
    </div>
  </div>
</template>

<style scoped>
/* responsive table helper, turns rows into pseudo-cards on small devices */
@media (max-width: 640px) {
  .responsive-table thead {
    display: none;
  }
  .responsive-table tr {
    display: block;
    margin-bottom: 0.75rem;
  }
  .responsive-table td {
    display: flex;
    justify-content: space-between;
    padding-left: 0.75rem;
    padding-right: 0.75rem;
  }
  .responsive-table td::before {
    content: attr(data-label);
    font-weight: 600;
  }
}
</style>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useProductStore } from '../stores/product'
import { useToast } from '../composables/useToast'

const productStore = useProductStore()
const { showToast } = useToast()
const route = useRoute()
const router = useRouter()

const products = computed(() => productStore.products)
const loading = computed(() => productStore.loading)
const pagination = computed(() => productStore.pagination)
const error = computed(() => productStore.error)

// Filters
const filters = ref({
  keyword: '',
  status: 'active',
  start_date: '',
  end_date: '',
  location_id: '',
  category_id: '',
  business_id: '',
  per_page: '10'
})

// Computed properties
const pageNumbers = computed(() => {
  const pages = []
  const totalPages = pagination.value.last_page
  const currentPage = pagination.value.current_page
  
  if (totalPages <= 5) {
    for (let i = 1; i <= totalPages; i++) {
      pages.push(i)
    }
  } else {
    if (currentPage <= 3) {
      pages.push(1, 2, 3, 4, '...', totalPages)
    } else if (currentPage >= totalPages - 2) {
      pages.push(1, '...', totalPages - 3, totalPages - 2, totalPages - 1, totalPages)
    } else {
      pages.push(1, '...', currentPage - 1, currentPage, currentPage + 1, '...', totalPages)
    }
  }
  
  return pages
})

// Methods
const fetchProducts = async (params = {}) => {
  try {
    await productStore.fetchAll(params)
  } catch (err) {
    showToast(error.value || 'Failed to fetch products', 'error')
  }
}

const updateProductStatus = async (id, status) => {
  try {
    await productStore.updateStatus(id, status)
    showToast('Product status updated successfully', 'success')
  } catch (err) {
    showToast(error.value || 'Failed to update product status', 'error')
  }
}

const deleteProduct = async (id) => {
  if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
    try {
      await productStore.delete(id)
      showToast('Product deleted successfully', 'success')
      fetchProducts()
    } catch (err) {
      showToast(error.value || 'Failed to delete product', 'error')
    }
  }
}

const applyFilters = () => {
  const params = {}
  if (filters.value.keyword) params.keyword = filters.value.keyword
  if (filters.value.status) params.status = filters.value.status
  if (filters.value.start_date) params.start_date = filters.value.start_date
  if (filters.value.end_date) params.end_date = filters.value.end_date
  if (filters.value.location_id) params.location_id = filters.value.location_id
  if (filters.value.category_id) params.category_id = filters.value.category_id
  if (filters.value.business_id) params.business_id = filters.value.business_id
  if (filters.value.per_page) params.per_page = filters.value.per_page
  fetchProducts(params)
}

const resetFilters = () => {
  filters.value = {
    keyword: '',
    status: '',
    start_date: '',
    end_date: '',
    location_id: '',
    category_id: '',
    business_id: '',
    per_page: '10'
  }
  fetchProducts()
}

const changePage = (page) => {
  if (page < 1 || page > pagination.value.last_page) return
  
  const params = {
    page: page
  }
  
  if (filters.value.keyword) params.keyword = filters.value.keyword
  if (filters.value.status) params.status = filters.value.status
  if (filters.value.start_date) params.start_date = filters.value.start_date
  if (filters.value.end_date) params.end_date = filters.value.end_date
  if (filters.value.location_id) params.location_id = filters.value.location_id
  if (filters.value.category_id) params.category_id = filters.value.category_id
  if (filters.value.business_id) params.business_id = filters.value.business_id
  if (filters.value.per_page) params.per_page = filters.value.per_page
  
  fetchProducts(params)
}

onMounted(() => {
  // Check if business_id is provided in the route query
  if (route.query.business_id) {
    filters.value.business_id = route.query.business_id
  }
  applyFilters()
})
</script>