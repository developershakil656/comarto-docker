<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center admin-header">
      <h1 class="text-2xl font-bold text-gray-900">Leads</h1>
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
            placeholder="Search by description..."
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
          <select 
            v-model="filters.status"
            class="admin-select"
          >
            <option value="">All</option>
            <option value="open">Open</option>
            <option value="closed">Closed</option>
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
          <label class="block text-sm font-medium text-gray-700 mb-1">Location ID</label>
          <input
            v-model="filters.location_id"
            type="number"
            class="admin-input"
            placeholder="Enter location ID"
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Category ID</label>
          <input
            v-model="filters.category_id"
            type="number"
            class="admin-input"
            placeholder="Enter category ID"
          >
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

    <!-- Leads Table -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden admin-card">
      <div class="responsive-table-container">
        <table class="min-w-full divide-y divide-gray-200 responsive-table">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proposals</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="lead in leads" :key="lead.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" data-label="ID">{{ lead.id }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" data-label="Description">
                {{ lead.description || 'N/A' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" data-label="Category">
                <div v-if="lead.category">{{ lead.category.name }}</div>
                <div v-else>N/A</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" data-label="Location">
                <div v-if="lead.location">
                  {{ lead.location.upazila_name }}, {{ lead.location.district_name }}
                </div>
                <div v-else>N/A</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" data-label="User">
                <div class="font-medium">{{ lead.user.name || 'N/A' }}</div>
                <div class="text-gray-400">{{ lead.user.number || 'N/A' }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" data-label="Quantity">
                {{ lead.quantity || 'N/A' }} {{ lead.unit_name || '' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" data-label="Proposals">
                {{ lead.proposal || 'N/A' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" data-label="Created">
                {{ formatDate(lead.created_at) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap" data-label="Status">
                <span :class="[
                  'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                  lead.status === 'active' ? 'bg-green-100 text-green-800' : 
                  lead.status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                  'bg-gray-100 text-gray-800'
                ]">
                  {{ lead.status || 'N/A' }}
                </span>
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

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useLeadStore } from '../stores/lead'
import { useToast } from '../composables/useToast'

const leadStore = useLeadStore()
const { showToast } = useToast()

const leads = computed(() => leadStore.leads)
const loading = computed(() => leadStore.loading)
const pagination = computed(() => leadStore.pagination)
const error = computed(() => leadStore.error)

// Filters
const filters = ref({
  keyword: '',
  status: '',
  start_date: '',
  end_date: '',
  location_id: '',
  category_id: '',
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
const fetchLeads = async (params = {}) => {
  try {
    await leadStore.fetchAll(params)
  } catch (err) {
    showToast(error.value || 'Failed to fetch leads', 'error')
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
  if (filters.value.per_page) params.per_page = filters.value.per_page
  fetchLeads(params)
}

const resetFilters = () => {
  filters.value = {
    keyword: '',
    status: '',
    start_date: '',
    end_date: '',
    location_id: '',
    category_id: '',
    per_page: '10'
  }
  fetchLeads()
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
  if (filters.value.per_page) params.per_page = filters.value.per_page
  
  fetchLeads(params)
}

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'short', day: 'numeric' }
  return new Date(dateString).toLocaleDateString(undefined, options)
}

onMounted(() => {
  fetchLeads()
})
</script>