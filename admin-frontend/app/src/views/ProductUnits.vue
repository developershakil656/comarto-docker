<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between admin-header">
      <div>
        <h2 class="text-2xl font-bold text-gray-900">Product Units</h2>
        <p class="text-gray-600 mt-1">Manage measurement units for products</p>
      </div>
      <button @click="openCreateModal" class="admin-button-primary flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
        Add Unit
      </button>
    </div>

    <div class="bg-white rounded-2xl shadow-sm p-4 admin-card">
      <input v-model="searchQuery" type="text" placeholder="Search product units..." class="admin-input" />
    </div>

    <DataTable :columns="columns" :data="filteredData" :loading="productUnitStore.loading" empty-text="No product units found">
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

    <ModalComponent 
      v-model="showModal" 
      :title="editingItem ? 'Edit Product Unit' : 'Create Product Unit'"
    >
      <form @submit.prevent="handleSubmit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Short Form</label>
          <input 
            v-model="shortFormValue" 
            type="text" 
            class="admin-input"
            :class="shortFormError ? 'border-red-500' : ''"
            placeholder="e.g., kg, L, pcs" 
          />
          <ErrorMessage :error="shortFormError" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Plural Form</label>
          <input 
            v-model="pluralFormValue" 
            type="text" 
            class="admin-input"
            :class="pluralFormError ? 'border-red-500' : ''"
            placeholder="e.g., kilograms, liters, pieces" 
          />
          <ErrorMessage :error="pluralFormError" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Full Form</label>
          <input 
            v-model="fullFormValue" 
            type="text" 
            class="admin-input"
            :class="fullFormError ? 'border-red-500' : ''"
            placeholder="e.g., kilogram, liter, piece" 
          />
          <ErrorMessage :error="fullFormError" />
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
            :disabled="productUnitStore.loading" 
            class="admin-button-primary"
          >
            {{ productUnitStore.loading ? 'Saving...' : 'Save Unit' }}
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
import { useProductUnitStore } from '../stores/productUnit'
import { useToast } from '../composables/useToast'

const productUnitStore = useProductUnitStore()
const { showToast } = useToast()
const submitting = ref(false)
const searchQuery = ref('')
const showModal = ref(false)
const showDeleteModal = ref(false)
const editingItem = ref(null)
const deletingItem = ref(null)

// Validation schema
const validationSchema = yup.object({
  short_form: yup.string().required('Short form is required').min(1, 'Must be at least 1 character'),
  plural_form: yup.string().required('Plural form is required').min(1, 'Must be at least 1 character'),
  full_form: yup.string().required('Full form is required').min(2, 'Must be at least 2 characters'),
  status: yup.string().required('Status is required').oneOf(['active', 'blocked'], 'Invalid status')
})

const { errors, resetForm, setErrors, validate } = useForm({
  validationSchema,
  initialValues: {
    short_form: '',
    plural_form: '',
    full_form: '',
    status: 'active'
  }
})

const { value: shortFormValue, errorMessage: shortFormError } = useField('short_form')
const { value: pluralFormValue, errorMessage: pluralFormError } = useField('plural_form')
const { value: fullFormValue, errorMessage: fullFormError } = useField('full_form')
const { value: statusValue, errorMessage: statusError } = useField('status')

const formData = ref({ short_form: '', plural_form: '', full_form: '', status: 'active' })

const columns = [
  { key: 'serial', label: '#', sortable: false },
  { key: 'id', label: 'ID' },
  { key: 'short_form', label: 'Short Form' },
  { key: 'plural_form', label: 'Plural Form' },
  { key: 'full_form', label: 'Full Form' },
  { key: 'status', label: 'Status' },
  { key: 'actions', label: 'Actions', sortable: false }
]

const filteredData = computed(() => {
  if (!searchQuery.value) return productUnitStore.productUnits
  return productUnitStore.productUnits.filter(item => 
    item.short_form?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    item.full_form?.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})

const openCreateModal = () => {
  editingItem.value = null
  formData.value = { short_form: '', plural_form: '', full_form: '', status: 'active' }
  resetForm({
    values: {
      short_form: '',
      plural_form: '',
      full_form: '',
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
      short_form: item.short_form,
      plural_form: item.plural_form,
      full_form: item.full_form,
      status: item.status
    }
  })
  showModal.value = true
}

// closeModal function removed - using v-model binding instead

const handleSubmit = async () => {
  // Validate form before submission
  const { valid } = await validate()
  if (!valid) return
  
  submitting.value = true
  try {
    if (editingItem.value) {
      await productUnitStore.update(editingItem.value.id, {
        short_form: shortFormValue.value,
        plural_form: pluralFormValue.value,
        full_form: fullFormValue.value,
        status: statusValue.value
      })
      showToast('Product unit updated successfully', 'success')
    } else {
      await productUnitStore.create({
        short_form: shortFormValue.value,
        plural_form: pluralFormValue.value,
        full_form: fullFormValue.value,
        status: statusValue.value
      })
      showToast('Product unit created successfully', 'success')
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
    await productUnitStore.remove(deletingItem.value.id)
    showToast('Product unit deleted successfully', 'success')
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

onMounted(() => productUnitStore.fetchAll())
</script>