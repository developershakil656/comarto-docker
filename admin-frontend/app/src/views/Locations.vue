<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between admin-header">
      <div>
        <h2 class="text-2xl font-bold text-gray-900">Locations</h2>
        <p class="text-gray-600 mt-1">Manage district and upazila locations</p>
      </div>
      <button @click="openCreateModal" class="admin-button-primary flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
        Add Location
      </button>
    </div>

    <div class="bg-white rounded-2xl shadow-sm p-4 admin-card">
      <input v-model="searchQuery" type="text" placeholder="Search locations..." class="admin-input" />
    </div>

    <DataTable :columns="columns" :data="filteredData" :loading="locationStore.loading" empty-text="No locations found">
      <template #cell-serial="{ index }">
        {{ index + 1 }}
      </template>
      <template #cell-status="{ row }">
        <span :class="['px-3 py-1 rounded-full text-xs font-medium', row.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">{{ row.status }}</span>
      </template>
      <template #cell-actions="{ row }">
        <div class="flex items-center gap-2">
          <button @click="openEditModal(row)" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
          <button @click="confirmDelete(row)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
        </div>
      </template>
    </DataTable>

    <!-- Create/Edit Modal -->
    <ModalComponent v-model="showModal" :title="editingItem ? 'Edit Location' : 'Create Location'">
      <form @submit.prevent="handleSubmit" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Upazila Name (English)</label>
            <input 
              v-model="upazilaNameValue" 
              type="text" 
              class="admin-input"
              :class="upazilaNameError ? 'border-red-500' : ''"
            />
            <ErrorMessage :error="upazilaNameError" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Upazila Name (বাংলা)</label>
            <input 
              v-model="upazilaNameBnValue" 
              type="text" 
              class="admin-input"
              :class="upazilaNameBnError ? 'border-red-500' : ''"
            />
            <ErrorMessage :error="upazilaNameBnError" />
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">District Name (English)</label>
            <input 
              v-model="districtNameValue" 
              type="text" 
              class="admin-input"
              :class="districtNameError ? 'border-red-500' : ''"
            />
            <ErrorMessage :error="districtNameError" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">District Name (বাংলা)</label>
            <input 
              v-model="districtNameBnValue" 
              type="text" 
              class="admin-input"
              :class="districtNameBnError ? 'border-red-500' : ''"
            />
            <ErrorMessage :error="districtNameBnError" />
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
          <select 
            v-model="statusValue" 
            class="admin-select"
          >
            <option value="active">Active</option>
            <option value="blocked">Blocked</option>
          </select>
        </div>

        <div class="flex justify-end gap-3 pt-4">
          <button 
            type="button" 
            @click="closeModal" 
            class="admin-button-secondary"
          >
            Cancel
          </button>
          <button 
            type="submit" 
            :disabled="locationStore.loading" 
            class="admin-button-primary"
          >
            {{ locationStore.loading ? 'Saving...' : 'Save Location' }}
          </button>
        </div>
      </form>
    </ModalComponent>

    <!-- Delete Confirmation Modal -->
    <ModalComponent v-model="showDeleteModal" title="Confirm Delete">
      <div class="space-y-4">
        <p>Are you sure you want to delete this location?</p>
        <div class="flex justify-end gap-3 pt-4">
          <button 
            type="button" 
            @click="showDeleteModal = false" 
            class="admin-button-secondary"
          >
            Cancel
          </button>
          <button 
            type="button" 
            @click="handleDelete" 
            :disabled="submitting" 
            class="admin-button-danger"
          >
            {{ submitting ? 'Deleting...' : 'Delete' }}
          </button>
        </div>
      </div>
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
import { useLocationStore } from '../stores/location'
import { useToast } from '../composables/useToast'

const locationStore = useLocationStore()
const { showToast } = useToast()
const submitting = ref(false)
const searchQuery = ref('')
const showModal = ref(false)
const showDeleteModal = ref(false)
const editingItem = ref(null)
const deletingItem = ref(null)

// Validation schema
const validationSchema = yup.object({
  upazila_name: yup.string().required('Upazila name (English) is required').min(2, 'Must be at least 2 characters'),
  upazila_name_bn: yup.string().required('Upazila name (বাংলা) is required').min(2, 'Must be at least 2 characters'),
  district_name: yup.string().required('District name (English) is required').min(2, 'Must be at least 2 characters'),
  district_name_bn: yup.string().required('District name (বাংলা) is required').min(2, 'Must be at least 2 characters'),
  status: yup.string().required('Status is required').oneOf(['active', 'blocked'], 'Invalid status')
})

const { errors, resetForm, setErrors, validate } = useForm({
  validationSchema,
  initialValues: {
    upazila_name: '',
    upazila_name_bn: '',
    district_name: '',
    district_name_bn: '',
    status: 'active'
  }
})

const { value: upazilaNameValue, errorMessage: upazilaNameError } = useField('upazila_name')
const { value: upazilaNameBnValue, errorMessage: upazilaNameBnError } = useField('upazila_name_bn')
const { value: districtNameValue, errorMessage: districtNameError } = useField('district_name')
const { value: districtNameBnValue, errorMessage: districtNameBnError } = useField('district_name_bn')
const { value: statusValue, errorMessage: statusError } = useField('status')

const formData = ref({ upazila_name: '', upazila_name_bn: '', district_name: '', district_name_bn: '', status: 'active' })

const columns = [
  { key: 'serial', label: '#', sortable: false },
  { key: 'id', label: 'ID' },
  { key: 'upazila_name', label: 'Upazila' },
  { key: 'upazila_name_bn', label: 'উপজেলা' },
  { key: 'district_name', label: 'District' },
  { key: 'district_name_bn', label: 'জেলা' },
  { key: 'status', label: 'Status' },
  { key: 'actions', label: 'Actions', sortable: false }
]

const filteredData = computed(() => {
  if (!searchQuery.value) return locationStore.locations
  return locationStore.locations.filter(item => 
    item.upazila_name?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    item.district_name?.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})

const openCreateModal = () => {
  editingItem.value = null
  formData.value = { upazila_name: '', upazila_name_bn: '', district_name: '', district_name_bn: '', status: 'active' }
  resetForm({
    values: {
      upazila_name: '',
      upazila_name_bn: '',
      district_name: '',
      district_name_bn: '',
      status: 'active'
    }
  })
  showModal.value = true
}

const openEditModal = (item) => {
  editingItem.value = item
  formData.value = { ...item }
  resetForm({
    values: {
      upazila_name: item.upazila_name,
      upazila_name_bn: item.upazila_name_bn,
      district_name: item.district_name,
      district_name_bn: item.district_name_bn,
      status: item.status
    }
  })
  showModal.value = true
}

const handleSubmit = async () => {
  // Validate form before submission
  const { valid } = await validate()
  if (!valid) return
  
  // Update formData from vee-validate values
  formData.value = {
    upazila_name: upazilaNameValue.value,
    upazila_name_bn: upazilaNameBnValue.value,
    district_name: districtNameValue.value,
    district_name_bn: districtNameBnValue.value,
    status: statusValue.value
  }
  
  submitting.value = true
  try {
    if (editingItem.value) {
      await locationStore.update(editingItem.value.id, formData.value)
      showToast('Location updated successfully', 'success')
    } else {
      await locationStore.create(formData.value)
      showToast('Location created successfully', 'success')
    }
    showModal.value = false
  } catch (error) {
    // Handle validation errors
    const errorMessage = error.response?.data?.data?.[0] || 
                        error.response?.data?.message || 
                        'Operation failed'
    
    // Try to set field-specific errors if available
    if (error.response?.data?.errors) {
      setErrors(error.response.data.errors)
    } else {
      showToast(errorMessage, 'error')
    }
  } finally {
    submitting.value = false
  }
}

const confirmDelete = (item) => {
  deletingItem.value = item
  showDeleteModal.value = true
}

const handleDelete = async () => {
  submitting.value = true
  try {
    await locationStore.remove(deletingItem.value.id)
    showToast('Location deleted successfully', 'success')
    showDeleteModal.value = false
  } catch (error) {
    // Handle validation errors
    const errorMessage = error.response?.data?.data?.[0] || 
                        error.response?.data?.message || 
                        'Delete failed'
    showToast(errorMessage, 'error')
  } finally {
    submitting.value = false
  }
}

onMounted(() => locationStore.fetchAll())
</script>
