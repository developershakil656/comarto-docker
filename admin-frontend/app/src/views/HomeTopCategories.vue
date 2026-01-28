<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between admin-header">
      <div>
        <h2 class="text-2xl font-bold text-gray-900">Home Top Categories</h2>
        <p class="text-gray-600 mt-1">Manage home top categories</p>
      </div>
      <button @click="openCreateModal" class="admin-button-primary flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
        Add Home Top Category
      </button>
    </div>

    <div class="bg-white rounded-2xl shadow-sm p-4 admin-card">
      <input v-model="searchQuery" type="text" placeholder="Search home top categories..." class="admin-input" />
    </div>

    <DataTable :columns="columns" :data="filteredData" :loading="homeTopCategoryStore.loading" empty-text="No home top categories found">
      <template #cell-serial="{ index }">
        {{ index + 1 }}
      </template>
      <template #cell-category_id="{ row }">
        {{ row.category?.id }}
      </template>
      <template #cell-category_name="{ row }">
        {{ row.category?.name }}
      </template>
      <template #cell-category="{ row }">
        <div class="flex items-center gap-3">
          <img 
            v-if="row.category?.icon" 
            :src="row.category.icon" 
            :alt="row.category.title" 
            class="w-16 h-16 object-cover rounded-md"
            @error="($event) => $event.target.classList.add('hidden')"
          />
          <div v-else class="bg-gray-200 border-2 border-dashed rounded-xl w-16 h-16" />
        </div>
      </template>
      <template #cell-recommended="{ row }">
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
          {{ row.recommended?.length || 0 }} items
        </span>
      </template>
      <template #cell-status="{ row }">
        <span :class="['px-3 py-1 rounded-full text-xs font-medium', row.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
          {{ row.status }}
        </span>
      </template>
      <template #cell-actions="{ row }">
        <div class="flex items-center gap-2">
          <button @click="openEditModal(row)" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
          </button>
          <button @click="confirmDelete(row)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
          </button>
        </div>
      </template>
    </DataTable>

    <ModalComponent v-model="showModal" :title="editingItem ? 'Edit Home Top Category' : 'Create Home Top Category'">
      <form @submit.prevent="handleSubmit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Category ID</label>
          <input 
            v-model="categoryIdValue" 
            type="number" 
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" 
            :class="categoryIdError ? 'border-red-500' : 'border-gray-300'"
            placeholder="Enter category ID"
          />
          <ErrorMessage :error="categoryIdError" />
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Recommended Items</label>
          <div class="space-y-2">
            <div v-for="(item, index) in recommendedItems" :key="index" class="flex items-center gap-2">
              <input 
                v-model="item.value" 
                type="number" 
                class="flex-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" 
                :class="item.error ? 'border-red-500' : 'border-gray-300'"
                placeholder="Item ID"
              />
              <button 
                @click="removeRecommendedItem(index)" 
                type="button"
                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
              </button>
            </div>
            <button 
              @click="addRecommendedItem" 
              type="button"
              class="flex items-center gap-2 text-indigo-600 hover:text-indigo-800"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
              </svg>
              Add Item
            </button>
          </div>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Serial</label>
          <input 
            v-model="serialValue" 
            type="number" 
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" 
            :class="serialError ? 'border-red-500' : 'border-gray-300'"
            placeholder="Order position"
          />
          <ErrorMessage :error="serialError" />
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
          <select 
            v-model="statusValue" 
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
            :class="statusError ? 'border-red-500' : 'border-gray-300'"
          >
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
          <ErrorMessage :error="statusError" />
        </div>
      </form>
      <template #footer>
        <button @click="showModal = false" type="button" class="admin-button-secondary">Cancel</button>
        <button @click="handleSubmit" type="submit" class="admin-button-primary">{{ editingItem ? 'Update' : 'Create' }}</button>
      </template>
    </ModalComponent>

    <ModalComponent v-model="showDeleteModal" title="Confirm Delete">
      <p class="text-gray-600">Are you sure you want to delete this home top category?</p>
      <template #footer>
        <button @click="showDeleteModal = false" class="admin-button-secondary">Cancel</button>
        <button @click="handleDelete" class="admin-button-danger">Delete</button>
      </template>
    </ModalComponent>

    <LoadingSpinner :visible="submitting" text="Processing..." />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, reactive } from 'vue'
import { useForm, useField } from 'vee-validate'
import * as yup from 'yup'
import DataTable from '../components/DataTable.vue'
import ModalComponent from '../components/ModalComponent.vue'
import LoadingSpinner from '../components/LoadingSpinner.vue'
import ErrorMessage from '../components/ErrorMessage.vue'
import { useHomeTopCategoryStore } from '../stores/homeTopCategory'
import { useCategoryStore } from '../stores/category'
import { useToast } from '../composables/useToast'

const homeTopCategoryStore = useHomeTopCategoryStore()
const categoryStore = useCategoryStore()
const { showToast } = useToast()
const searchQuery = ref('')
const showModal = ref(false)
const showDeleteModal = ref(false)
const editingItem = ref(null)
const deletingItem = ref(null)
const submitting = ref(false)

// Recommended items array
const recommendedItems = ref([{ value: '', error: '' }])

// Validation schema
const validationSchema = yup.object({
  category_id: yup.number().required('Category ID is required').positive('Category ID must be a positive number'),
  serial: yup.number().required('Serial is required').positive('Serial must be a positive number'),
  status: yup.string().required('Status is required').oneOf(['active', 'inactive'], 'Invalid status')
})

const { errors, resetForm, setErrors, validate } = useForm({
  validationSchema,
  initialValues: {
    category_id: '',
    serial: '',
    status: 'active'
  }
})

const { value: categoryIdValue, errorMessage: categoryIdError } = useField('category_id')
const { value: serialValue, errorMessage: serialError } = useField('serial')
const { value: statusValue, errorMessage: statusError } = useField('status')

const columns = [
  { key: 'serial', label: '#', sortable: false },
  { key: 'category_id', label: 'Category ID' },
  { key: 'category_name', label: 'Category Name' },
  { key: 'category', label: 'Category' },
  { key: 'recommended', label: 'Recommended Items' },
  { key: 'serial', label: 'Serial' },
  { key: 'status', label: 'Status' },
  { key: 'actions', label: 'Actions', sortable: false }
]

// Removed the categories computed property since we're not using the dropdown anymore
const filteredData = computed(() => {
  if (!searchQuery.value) return homeTopCategoryStore.homeTopCategories
  return homeTopCategoryStore.homeTopCategories.filter(item => 
    item.category?.title?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    item.category?.id?.toString().includes(searchQuery.value)
  )
})

const addRecommendedItem = () => {
  recommendedItems.value.push({ value: '', error: '' })
}

const removeRecommendedItem = (index) => {
  if (recommendedItems.value.length > 1) {
    recommendedItems.value.splice(index, 1)
  }
}

const openCreateModal = () => {
  editingItem.value = null
  recommendedItems.value = [{ value: '', error: '' }]
  resetForm({
    values: {
      category_id: '',
      serial: '',
      status: 'active'
    }
  })
  showModal.value = true
}

const openEditModal = (item) => {
  editingItem.value = item
  
  // Convert recommended array to recommendedItems format
  recommendedItems.value = item.recommended?.map(id => ({ value: id, error: '' })) || [{ value: '', error: '' }]
  
  if (recommendedItems.value.length === 0) {
    recommendedItems.value = [{ value: '', error: '' }]
  }
  
  resetForm({
    values: {
      category_id: item.category?.id || '',
      serial: item.serial || '',
      status: item.status || 'active'
    }
  })
  showModal.value = true
}

const handleSubmit = async () => {
  // Validate form before submission
  const { valid } = await validate()
  if (!valid) return
  
  // Validate recommended items
  let hasRecommendedErrors = false
  recommendedItems.value.forEach(item => {
    if (item.value && (isNaN(item.value) || parseInt(item.value) <= 0)) {
      item.error = 'Item ID must be a positive number'
      hasRecommendedErrors = true
    } else {
      item.error = ''
    }
  })
  
  if (hasRecommendedErrors) return
  
  submitting.value = true
  try {
    // Prepare recommended array
    const recommendedArray = recommendedItems.value
      .map(item => parseInt(item.value))
      .filter(value => !isNaN(value) && value > 0)
    
    const submitData = {
      category_id: parseInt(categoryIdValue.value),
      recommended: recommendedArray,
      serial: parseInt(serialValue.value),
      status: statusValue.value
    }

    if (editingItem.value) {
      await homeTopCategoryStore.update(editingItem.value.id, submitData)
      showToast('Home top category updated successfully', 'success')
    } else {
      await homeTopCategoryStore.create(submitData)
      showToast('Home top category created successfully', 'success')
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
    await homeTopCategoryStore.remove(deletingItem.value.id)
    showToast('Home top category deleted successfully', 'success')
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

onMounted(async () => {
  await homeTopCategoryStore.fetchAll()
  // We don't need to fetch categories anymore since we're using an input field
})
</script>