<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between admin-header">
      <div>
        <h2 class="text-2xl font-bold text-gray-900">Categories</h2>
        <p class="text-gray-600 mt-1">Manage product categories</p>
      </div>
      <button @click="openCreateModal" class="admin-button-primary flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
        Add Category
      </button>
    </div>

    <div class="bg-white rounded-2xl shadow-sm p-4 admin-card">
      <input v-model="searchQuery" type="text" placeholder="Search categories..." class="admin-input" />
    </div>

    <DataTable :columns="columns" :data="filteredData" :loading="categoryStore.loading" empty-text="No categories found">
      <template #cell-serial="{ index }">
        {{ index + 1 }}
      </template>
      <template #cell-status="{ row }">
        <span :class="['px-3 py-1 rounded-full text-xs font-medium', row.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
          {{ row.status }}
        </span>
      </template>
      <template #cell-icon="{ row }">
        <div v-if="row.icon" class="w-12 h-12 rounded overflow-hidden">
          <img :src="row.icon" :alt="row.name" class="w-full h-full object-cover">
        </div>
        <div v-else class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center">
          <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
        </div>
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

    <ModalComponent v-model="showModal" :title="editingItem ? 'Edit Category' : 'Create Category'">
      <form @submit.prevent="handleSubmit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Category Name</label>
          <input 
            v-model="nameValue" 
            type="text" 
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" 
            :class="nameError ? 'border-red-500' : 'border-gray-300'"
            placeholder="e.g., drinks, food" 
          />
          <ErrorMessage :error="nameError" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Category Title</label>
          <input 
            v-model="titleValue" 
            type="text" 
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" 
            :class="titleError ? 'border-red-500' : 'border-gray-300'"
            placeholder="e.g., Drinks & Beverages" 
          />
          <ErrorMessage :error="titleError" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Category Description</label>
          <textarea 
            v-model="descriptionValue" 
            rows="3"
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" 
            :class="descriptionError ? 'border-red-500' : 'border-gray-300'"
            placeholder="Describe the category..." 
          ></textarea>
          <ErrorMessage :error="descriptionError" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Category Icon/Image</label>
          <div class="space-y-2">
            <input 
              type="file" 
              accept="image/*" 
              @change="handleImageChange"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
            />
            <div v-if="imagePreview" class="mt-2">
              <img :src="imagePreview" alt="Preview" class="w-32 h-32 object-cover rounded-lg border border-gray-300" />
            </div>
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Parent Category ID (optional)</label>
          <input 
            v-model="parentIdValue" 
            type="number" 
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" 
            :class="parentIdError ? 'border-red-500' : 'border-gray-300'"
            placeholder="Leave empty for root category" 
          />
          <ErrorMessage :error="parentIdError" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
          <select 
            v-model="statusValue" 
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
            :class="statusError ? 'border-red-500' : 'border-gray-300'"
          >
            <option value="active">Active</option>
            <option value="blocked">Blocked</option>
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
      <p class="text-gray-600">Are you sure you want to delete this category?</p>
      <template #footer>
        <button @click="showDeleteModal = false" class="admin-button-secondary">Cancel</button>
        <button @click="handleDelete" class="admin-button-danger">Delete</button>
      </template>
    </ModalComponent>

    <LoadingSpinner :visible="submitting" text="Processing..." />
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
import { useCategoryStore } from '../stores/category'
import { useToast } from '../composables/useToast'

const categoryStore = useCategoryStore()
const { showToast } = useToast()
const searchQuery = ref('')
const showModal = ref(false)
const showDeleteModal = ref(false)
const editingItem = ref(null)
const deletingItem = ref(null)
const submitting = ref(false)
const imageFile = ref(null)
const imagePreview = ref('')

// Validation schema
const validationSchema = yup.object({
  name: yup.string().required('Category name is required').min(2, 'Must be at least 2 characters'),
  title: yup.string(),
  description: yup.string(),
  parent_id: yup.number().nullable().transform((value, originalValue) => originalValue === '' ? null : value),
  status: yup.string().required('Status is required').oneOf(['active', 'blocked'], 'Invalid status')
})

const { errors, resetForm, setErrors, validate } = useForm({
  validationSchema,
  initialValues: {
    name: '',
    title: '',
    description: '',
    parent_id: null,
    status: 'active'
  }
})

const { value: nameValue, errorMessage: nameError } = useField('name')
const { value: titleValue, errorMessage: titleError } = useField('title')
const { value: descriptionValue, errorMessage: descriptionError } = useField('description')
const { value: parentIdValue, errorMessage: parentIdError } = useField('parent_id')
const { value: statusValue, errorMessage: statusError } = useField('status')

const columns = [
  { key: 'serial', label: '#', sortable: false },
  { key: 'id', label: 'ID' },
  { key: 'name', label: 'Name' },
  { key: 'title', label: 'Title' },
  { key: 'description', label: 'Description' },
  { key: 'icon', label: 'Icon' },
  { key: 'parent_id', label: 'Parent ID' },
  { key: 'status', label: 'Status' },
  { key: 'actions', label: 'Actions', sortable: false }
]

const filteredData = computed(() => {
  if (!searchQuery.value) return categoryStore.categories
  return categoryStore.categories.filter(item => item.name?.toLowerCase().includes(searchQuery.value.toLowerCase()))
})

const openCreateModal = () => {
  editingItem.value = null
  imageFile.value = null
  imagePreview.value = ''
  resetForm({
    values: {
      name: '',
      title: '',
      description: '',
      parent_id: null,
      status: 'active'
    }
  })
  showModal.value = true
}

const openEditModal = (item) => {
  editingItem.value = item
  imageFile.value = null
  imagePreview.value = item.icon || '' // Set preview to existing icon if editing
  resetForm({
    values: {
      name: item.name,
      title: item.title || '',
      description: item.description || '',
      parent_id: item.parent_id || null,
      status: item.status
    }
  })
  showModal.value = true
}

const handleImageChange = (event) => {
  const file = event.target.files[0]
  if (!file) return

  // Validate file type
  if (!file.type.startsWith('image/')) {
    showToast('Please select an image file', 'error')
    return
  }

  // Validate file size (max 2MB)
  if (file.size > 2 * 1024 * 1024) {
    showToast('Image size must be less than 2MB', 'error')
    return
  }

  imageFile.value = file
  
  // Create preview
  const reader = new FileReader()
  reader.onload = (e) => {
    imagePreview.value = e.target.result
  }
  reader.readAsDataURL(file)
}

const handleSubmit = async () => {
  // Validate form before submission
  const { valid } = await validate()
  if (!valid) return
  
  submitting.value = true
  try {
    // Create FormData for file upload
    const submitData = new FormData()
    submitData.append('name', nameValue.value)
    if (titleValue.value) {
      submitData.append('title', titleValue.value)
    }
    if (descriptionValue.value) {
      submitData.append('description', descriptionValue.value)
    }
    submitData.append('status', statusValue.value)
    
    if (parentIdValue.value) {
      submitData.append('parent_id', parentIdValue.value)
    }
    
    // Append image file if selected (using 'icon' field as per backend)
    if (imageFile.value) {
      submitData.append('icon', imageFile.value)
    }

    if (editingItem.value) {
      await categoryStore.update(editingItem.value.id, submitData)
      showToast('Category updated successfully', 'success')
    } else {
      await categoryStore.create(submitData)
      showToast('Category created successfully', 'success')
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
    await categoryStore.remove(deletingItem.value.id)
    showToast('Category deleted successfully', 'success')
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

onMounted(() => categoryStore.fetchAll())
</script>