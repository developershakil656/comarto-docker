<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between admin-header">
      <div>
        <h2 class="text-2xl font-bold text-gray-900">Business Types</h2>
        <p class="text-gray-600 mt-1">Manage all business type categories</p>
      </div>
      <button
        @click="openCreateModal"
        class="admin-button-primary flex items-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
        Add New Type
      </button>
    </div>

    <!-- Search Bar -->
    <div class="bg-white rounded-2xl shadow-sm p-4 admin-card">
      <input
        v-model="searchQuery"
        type="text"
        placeholder="Search business types..."
        class="admin-input"
      />
    </div>

    <!-- Table -->
    <DataTable
      :columns="columns"
      :data="filteredData"
      :loading="businessTypeStore.loading"
      empty-text="No business types found"
    >
      <template #cell-serial="{ index }">
        {{ index + 1 }}
      </template>

      <template #cell-status="{ row }">
        <span :class="[
          'px-3 py-1 rounded-full text-xs font-medium',
          row.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
        ]">
          {{ row.status }}
        </span>
      </template>

      <template #cell-actions="{ row }">
        <div class="flex space-x-2">
          <button
            @click="openEditModal(row)"
            class="text-indigo-600 hover:text-indigo-900"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
          </button>
          <button
            @click="deleteBusinessType(row.id)"
            class="text-red-600 hover:text-red-900"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
          </button>
        </div>
      </template>
    </DataTable>

    <!-- Create/Edit Modal -->
    <ModalComponent
      v-model="showModal"
      :title="editingType ? 'Edit Business Type' : 'Create Business Type'"
    >
      <form @submit.prevent="handleSubmit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
          <input
            v-model="typeValue"
            type="text"
            required
            class="admin-input"
            placeholder="Enter business type name"
            :class="typeError ? 'border-red-500' : ''"
          >
          <ErrorMessage :error="typeError" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
          <select
            v-model="statusValue"
            class="admin-select"
            :class="statusError ? 'border-red-500' : ''"
          >
            <option value="active">Active</option>
            <option value="blocked">Blocked</option>
          </select>
          <ErrorMessage :error="statusError" />
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
            :disabled="businessTypeStore.loading"
            class="admin-button-primary"
          >
            {{ businessTypeStore.loading ? 'Saving...' : 'Save Type' }}
          </button>
        </div>
      </form>
    </ModalComponent>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useForm, useField } from 'vee-validate'
import * as yup from 'yup'
import DataTable from '../components/DataTable.vue'
import ModalComponent from '../components/ModalComponent.vue'
import LoadingSpinner from '../components/LoadingSpinner.vue'
import ErrorMessage from '../components/ErrorMessage.vue'
import { useBusinessTypeStore } from '../stores/businessType'
import { useToast } from '../composables/useToast'
import Swal from 'sweetalert2'

const businessTypeStore = useBusinessTypeStore()
const { showToast } = useToast()

const searchQuery = ref('')
const showModal = ref(false)
const editingType = ref(null)

// Validation schema
const validationSchema = yup.object({
  type: yup.string().required('Business type name is required').min(2, 'Must be at least 2 characters'),
  status: yup.string().required('Status is required').oneOf(['active', 'blocked'], 'Invalid status')
})

const { errors, resetForm, setErrors, validate } = useForm({
  validationSchema,
  initialValues: {
    type: '',
    status: 'active'
  }
})

const { value: typeValue, errorMessage: typeError } = useField('type')
const { value: statusValue, errorMessage: statusError } = useField('status')

const formData = ref({
  type: '',
  status: 'active'
})

const columns = [
  { key: 'serial', label: '#', sortable: false },
  { key: 'id', label: 'ID' },
  { key: 'type', label: 'Type' },
  { key: 'status', label: 'Status' },
  { key: 'actions', label: 'Actions', sortable: false }
]

const filteredData = computed(() => {
  if (!searchQuery.value) return businessTypeStore.businessTypes
  
  return businessTypeStore.businessTypes.filter(item =>
    item.type?.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})

const openCreateModal = () => {
  editingType.value = null
  formData.value = { type: '', status: 'active' }
  resetForm({
    values: {
      type: '',
      status: 'active'
    }
  })
  showModal.value = true
}

const openEditModal = (item) => {
  editingType.value = item
  formData.value = { ...item }
  resetForm({
    values: {
      type: item.type,
      status: item.status
    }
  })
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const handleSubmit = async () => {
  // Validate form before submission
  const { valid } = await validate()
  if (!valid) return
  
  // Update formData from vee-validate values
  formData.value.type = typeValue.value
  formData.value.status = statusValue.value
  
  try {
    if (editingType.value) {
      await businessTypeStore.update(editingType.value.id, formData.value)
      showToast('Business type updated successfully', 'success')
    } else {
      await businessTypeStore.create(formData.value)
      showToast('Business type created successfully', 'success')
    }
    showModal.value = false
  } catch (error) {
    // Handle validation errors from API
    const errorMessage = error.response?.data?.data?.[0] || 
                        error.response?.data?.message || 
                        'Operation failed'
    
    // Try to set field-specific errors if available
    if (error.response?.data?.errors) {
      setErrors(error.response.data.errors)
    } else {
      showToast(errorMessage, 'error')
    }
  }
}

const deleteBusinessType = async (id) => {
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
      await businessTypeStore.remove(id)
      showToast('Business type deleted successfully', 'success')
      Swal.fire(
        'Deleted!',
        'The business type has been deleted.',
        'success'
      )
    } catch (error) {
      // Handle validation errors
      const errorMessage = error.response?.data?.data?.[0] || 
                          error.response?.data?.message || 
                          'Delete failed'
      showToast(errorMessage, 'error')
      Swal.fire(
        'Error!',
        errorMessage,
        'error'
      )
    }
  }
}

onMounted(() => {
  businessTypeStore.fetchAll()
})
</script>