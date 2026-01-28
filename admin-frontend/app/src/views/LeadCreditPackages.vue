<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center admin-header">
      <h1 class="text-2xl font-bold text-gray-900">Lead Credit Packages</h1>
      <button 
        @click="openCreateModal"
        class="admin-button-primary flex items-center"
      >
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
        Add Package
      </button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-sm p-6 admin-card">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 admin-filters">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
          <select 
            v-model="filters.is_active"
            class="admin-select"
          >
            <option value="">All</option>
            <option value="1">Active</option>
            <option value="0">Inactive</option>
          </select>
        </div>
        <div class="flex items-end">
          <button 
            @click="applyFilters"
            class="admin-button-primary"
          >
            Apply Filters
          </button>
        </div>
      </div>
    </div>

    <!-- Packages Table -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden admin-card">
      <div class="responsive-table-container">
        <table class="min-w-full divide-y divide-gray-200 responsive-table">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Credits</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="pkg in packages" :key="pkg.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" data-label="ID">{{ pkg.id }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" data-label="Name">{{ pkg.name || 'N/A' }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" data-label="Credits">{{ pkg.credits || 'N/A' }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" data-label="Price">{{ pkg.currency || 'BDT' }} {{ pkg.price || 'N/A' }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" data-label="Duration">{{ pkg.duration_months || 'N/A' }} months</td>
              <td class="px-6 py-4 whitespace-nowrap" data-label="Status">
                <span :class="[
                  'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                  pkg.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                ]">
                  {{ pkg.is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" data-label="Actions">
                <button 
                  @click="openEditModal(pkg)"
                  class="text-indigo-600 hover:text-indigo-900 mr-3"
                >
                  Edit
                </button>
                <button 
                  @click="deletePackage(pkg.id)"
                  class="text-red-600 hover:text-red-900"
                >
                  Delete
                </button>
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

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-2xl shadow-xl max-w-md w-full max-h-screen overflow-y-auto">
        <div class="p-6">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">
              {{ editingPackage ? 'Edit Package' : 'Create Package' }}
            </h3>
            <button @click="closeModal" class="text-gray-400 hover:text-gray-500">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <form @submit.prevent="savePackage" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Package Name *</label>
              <input
                v-model="form.name"
                type="text"
                required
                class="admin-input"
                placeholder="Enter package name"
              >
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Credits *</label>
              <input
                v-model.number="form.credits"
                type="number"
                min="1"
                required
                class="admin-input"
                placeholder="Enter number of credits"
              >
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Price *</label>
              <input
                v-model.number="form.price"
                type="number"
                step="0.01"
                min="0"
                required
                class="admin-input"
                placeholder="Enter price"
              >
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
              <input
                v-model="form.currency"
                type="text"
                class="admin-input"
                placeholder="Enter currency (default: BDT)"
              >
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Duration (months)</label>
              <input
                v-model.number="form.duration_months"
                type="number"
                min="1"
                class="admin-input"
                placeholder="Enter duration in months (default: 3)"
              >
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
              <select
                v-model="form.is_active"
                class="admin-select"
              >
                <option :value="true">Active</option>
                <option :value="false">Inactive</option>
              </select>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
              <button
                type="button"
                @click="closeModal"
                class="admin-button-secondary"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="loading"
                class="admin-button-primary"
              >
                {{ loading ? 'Saving...' : 'Save Package' }}
              </button>
            </div>
          </form>
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
import { useLeadCreditPackageStore } from '../stores/leadCreditPackage'
import { useToast } from '../composables/useToast'
import Swal from 'sweetalert2'

const leadCreditPackageStore = useLeadCreditPackageStore()
const { showToast } = useToast()

const packages = computed(() => leadCreditPackageStore.packages)
const loading = computed(() => leadCreditPackageStore.loading)
const pagination = computed(() => leadCreditPackageStore.pagination)
const error = computed(() => leadCreditPackageStore.error)

// Form data
const showModal = ref(false)
const editingPackage = ref(null)
const form = ref({
  name: '',
  credits: 0,
  price: 0,
  currency: 'BDT',
  duration_months: 3,
  is_active: true
})

// Filters
const filters = ref({
  is_active: ''
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
const fetchPackages = async (params = {}) => {
  try {
    await leadCreditPackageStore.fetchAll(params)
  } catch (err) {
    showToast(error.value || 'Failed to fetch packages', 'error')
  }
}

const openCreateModal = () => {
  editingPackage.value = null
  form.value = {
    name: '',
    credits: 0,
    price: 0,
    currency: 'BDT',
    duration_months: 3,
    is_active: true
  }
  showModal.value = true
}

const openEditModal = (pkg) => {
  editingPackage.value = pkg
  form.value = { ...pkg }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editingPackage.value = null
}

const savePackage = async () => {
  try {
    if (editingPackage.value) {
      await leadCreditPackageStore.update(editingPackage.value.id, form.value)
      showToast('Package updated successfully', 'success')
    } else {
      await leadCreditPackageStore.create(form.value)
      showToast('Package created successfully', 'success')
    }
    closeModal()
  } catch (err) {
    showToast(error.value || 'Failed to save package', 'error')
  }
}

const deletePackage = async (id) => {
  // Show SweetAlert confirmation
  const result = await Swal.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, delete it!'
  })

  if (result.isConfirmed) {
    try {
      await leadCreditPackageStore.remove(id)
      showToast('Package deleted successfully', 'success')
      Swal.fire(
        'Deleted!',
        'The package has been deleted.',
        'success'
      )
    } catch (err) {
      showToast(error.value || 'Failed to delete package', 'error')
      Swal.fire(
        'Error!',
        error.value || 'Failed to delete package',
        'error'
      )
    }
  }
}

const applyFilters = () => {
  const params = {}
  if (filters.value.is_active !== '') {
    params.is_active = filters.value.is_active
  }
  fetchPackages(params)
}

const changePage = (page) => {
  if (page < 1 || page > pagination.value.last_page) return
  
  const params = {
    page: page
  }
  
  if (filters.value.is_active !== '') {
    params.is_active = filters.value.is_active
  }
  
  fetchPackages(params)
}

onMounted(() => {
  fetchPackages()
})
</script>