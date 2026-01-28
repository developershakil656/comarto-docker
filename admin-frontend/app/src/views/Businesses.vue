<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center admin-header">
      <h1 class="text-2xl font-bold text-gray-900">Businesses</h1>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-sm p-6 admin-card">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 admin-filters">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Keyword</label>
          <input
            v-model="filters.keyword"
            type="text"
            class="admin-input"
            placeholder="Search by name, phone, email..."
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
          <select 
            v-model="filters.status"
            class="admin-select"
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
            class="admin-input"
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
          <input
            v-model="filters.end_date"
            type="date"
            class="admin-input"
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
          <input
            v-model="filters.location_id"
            type="number"
            class="admin-input"
            placeholder="Enter location ID"
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Business Type</label>
          <select 
            v-model="filters.business_type"
            class="admin-select"
            multiple
          >
            <option 
              v-for="type in businessTypes" 
              :key="type.id" 
              :value="type.type"
            >
              {{ type.type }}
            </option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Per Page</label>
          <select 
            v-model="filters.per_page"
            class="admin-select"
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
            class="admin-button-primary"
          >
            Apply Filters
          </button>
          <button 
            @click="resetFilters"
            class="admin-button-secondary"
          >
            Reset
          </button>
        </div>
      </div>
    </div>

    <!-- Businesses Table -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden admin-card">
      <div class="responsive-table-container">
        <table class="min-w-full divide-y divide-gray-200 responsive-table">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Business</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="business in businesses" :key="business.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" data-label="ID">{{ business.id }}</td>
              <td class="px-6 py-4 whitespace-nowrap" data-label="Business">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-10 w-10">
                    <img v-if="business.business_profile" :src="business.business_profile" :alt="business.business_name || 'Business'" class="h-10 w-10 rounded-full">
                    <div v-else class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                      <span class="text-indigo-800 font-medium">{{ (business.business_name || '?').charAt(0) }}</span>
                    </div>
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">{{ business.business_name || 'N/A' }}</div>
                    <div class="text-sm text-gray-500">{{ business.slug || 'N/A' }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" data-label="Type">
                <div v-for="type in business.business_type" :key="type" class="inline-block bg-gray-100 rounded-full px-2 py-1 text-xs font-medium text-gray-800 mr-1">
                  {{ type }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" data-label="Contact">
                <div>{{ business.number || 'N/A' }}</div>
                <div class="text-gray-400">{{ business.alternate_number || 'N/A' }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" data-label="Location">
                {{ business.location || 'N/A' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap" data-label="Status">
                <select
                  v-model="business.status"
                  @change="updateBusinessStatus(business.id, business.status)"
                  class="admin-select text-sm min-w-[120px]"
                  :value="business.status"
                  :class="{
                    'text-green-500': business.status === 'active',
                    'text-yellow-500': business.status === 'inactive',
                    'text-red-500': business.status === 'blocked'
                  }"
                >
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                  <option value="blocked">Blocked</option>
                </select>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" data-label="Created">
                {{ formatDate(business.created_at) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" data-label="Actions">
                <router-link 
                  :to="{ name: 'Products', query: { business_id: business.id } }"
                  class="text-indigo-600 hover:text-indigo-900 mr-3"
                >
                  View Products
                </router-link>
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

    <!-- Business Products Modal (removed - using router-link instead) -->

    <!-- Loading Spinner -->
    <div v-if="loading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-white"></div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useBusinessStore } from '../stores/business'
import { useBusinessTypeStore } from '../stores/businessType'
import { useToast } from '../composables/useToast'

const businessStore = useBusinessStore()
const businessTypeStore = useBusinessTypeStore()
const { showToast } = useToast()
const router = useRouter()

const businesses = computed(() => businessStore.businesses)
const loading = computed(() => businessStore.loading)
const pagination = computed(() => businessStore.pagination)
const error = computed(() => businessStore.error)
const businessTypes = computed(() => businessTypeStore.businessTypes)

// Modals and data (removed - using router-link instead)
// const showProductsModal = ref(false)
// const businessProducts = ref([])
// const currentBusinessId = ref(null)

// Filters
const filters = ref({
  keyword: '',
  status: 'active',
  start_date: '',
  end_date: '',
  location_id: '',
  business_type: [],
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
const fetchBusinesses = async (params = {}) => {
  try {
    await businessStore.fetchAll(params)
  } catch (err) {
    showToast(error.value || 'Failed to fetch businesses', 'error')
  }
}

const updateBusinessStatus = async (id, status) => {
  try {
    await businessStore.updateStatus(id, status)
    showToast('Business status updated successfully', 'success')
  } catch (err) {
    showToast(error.value || 'Failed to update business status', 'error')
  }
}

// Removed viewBusinessProducts and closeProductsModal functions - using router-link instead

const applyFilters = () => {
  const params = {}
  if (filters.value.keyword) params.keyword = filters.value.keyword
  if (filters.value.status) params.status = filters.value.status
  if (filters.value.start_date) params.start_date = filters.value.start_date
  if (filters.value.end_date) params.end_date = filters.value.end_date
  if (filters.value.location_id) params.location_id = filters.value.location_id
  if (filters.value.business_type && filters.value.business_type.length > 0) params.business_type = filters.value.business_type.join(',')
  if (filters.value.per_page) params.per_page = filters.value.per_page
  fetchBusinesses(params)
}

const resetFilters = () => {
  filters.value = {
    keyword: '',
    status: 'active',
    start_date: '',
    end_date: '',
    location_id: '',
    business_type: [],
    per_page: '10'
  }
  fetchBusinesses()
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
  if (filters.value.business_type && filters.value.business_type.length > 0) params.business_type = filters.value.business_type.join(',')
  if (filters.value.per_page) params.per_page = filters.value.per_page
  
  fetchBusinesses(params)
}

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'short', day: 'numeric' }
  return new Date(dateString).toLocaleDateString(undefined, options)
}

onMounted(async () => {
  // Fetch businesses and business types
  await Promise.all([
    fetchBusinesses(),
    businessTypeStore.fetchAll()
  ])
})
</script>