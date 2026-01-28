<template>
  <div class="space-y-6">
    <div class="bg-white rounded-2xl shadow-sm p-6 admin-card">
      <h2 class="text-2xl font-bold text-gray-900 mb-6">Admin Profile</h2>
      
      <!-- Profile Picture Section -->
      <div class="flex items-center gap-6 mb-8 pb-8 border-b">
        <div class="relative">
          <div v-if="profileImageUrl" class="w-24 h-24 rounded-full overflow-hidden">
            <img :src="profileImageUrl" :alt="adminName" class="w-full h-full object-cover">
          </div>
          <div v-else class="w-24 h-24 bg-indigo-600 rounded-full flex items-center justify-center text-white text-3xl font-bold">
            {{ (adminName || '?').charAt(0).toUpperCase() }}
          </div>
          <button 
            @click="triggerFileInput"
            class="absolute bottom-0 right-0 w-8 h-8 bg-white border-2 border-gray-300 rounded-full flex items-center justify-center hover:bg-gray-50 transition-colors"
          >
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
          </button>
          <input 
            ref="fileInput"
            type="file"
            accept="image/*"
            @change="handleImageUpload"
            class="hidden"
          />
        </div>
        <div class="flex-1">
          <h3 class="text-xl font-semibold text-gray-900">{{ adminName || 'Admin User' }}</h3>
          <p class="text-gray-600">Administrator</p>
        </div>
        <div>
          <button
            @click="handleLogout"
            class="flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200 font-medium"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            Logout
          </button>
        </div>
      </div>

      <!-- Update Profile Form -->
      <form @submit.prevent="handleUpdateProfile" class="space-y-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
          <input
            v-model="profileForm.name"
            type="text"
            required
            class="admin-input"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
          <input
            v-model="profileForm.email"
            type="email"
            required
            class="admin-input"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
          <input
            v-model="profileForm.number"
            type="tel"
            class="admin-input"
          />
        </div>

        <div class="flex justify-end">
          <button
            type="submit"
            :disabled="authStore.loading"
            class="admin-button-primary"
          >
            {{ authStore.loading ? 'Updating...' : 'Update Profile' }}
          </button>
        </div>
      </form>
    </div>

    <!-- Activity Chart Section -->
    <div class="bg-white rounded-2xl shadow-sm p-6 admin-card">
      <h2 class="text-2xl font-bold text-gray-900 mb-6">Activity Overview</h2>
      <ActivityChart :data="chartData" type="line" />
    </div>

    <!-- Change Password Section -->
    <div class="bg-white rounded-2xl shadow-sm p-6 admin-card">
      <h2 class="text-2xl font-bold text-gray-900 mb-6">Change Password</h2>
      
      <form @submit.prevent="handleChangePassword" class="space-y-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
          <input
            v-model="passwordForm.old_password"
            type="password"
            required
            class="admin-input"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
          <input
            v-model="passwordForm.new_password"
            type="password"
            required
            class="admin-input"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
          <input
            v-model="passwordForm.new_password_confirmation"
            type="password"
            required
            class="admin-input"
          />
        </div>

        <div class="flex justify-end">
          <button
            type="submit"
            :disabled="authStore.loading"
            class="admin-button-primary"
          >
            {{ authStore.loading ? 'Changing...' : 'Change Password' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import LoadingSpinner from '../components/LoadingSpinner.vue'
import ActivityChart from '../components/ActivityChart.vue'
import { useAuthStore } from '../stores/auth'
import { adminAPI } from '../api/services'
import { useToast } from '../composables/useToast'

const router = useRouter()
const authStore = useAuthStore()
const { showToast } = useToast()

const adminName = ref('Admin User')
const uploading = ref(false)
const fileInput = ref(null)
const imagePreview = ref('')

const profileImageUrl = computed(() => {
  return authStore.user?.profile || imagePreview.value
})

const profileForm = ref({
  name: '',
  email: '',
  number: ''
})

const passwordForm = ref({
  old_password: '',
  new_password: '',
  new_password_confirmation: ''
})

// Sample chart data - in a real app, this would come from an API
const chartData = ref({
  labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
  datasets: [
    {
      label: 'Logins',
      backgroundColor: 'rgba(99, 102, 241, 0.2)',
      borderColor: 'rgba(99, 102, 241, 1)',
      borderWidth: 2,
      data: [12, 19, 3, 5, 2, 3, 9],
      tension: 0.4,
      fill: true
    },
    {
      label: 'Actions',
      backgroundColor: 'rgba(16, 185, 129, 0.2)',
      borderColor: 'rgba(16, 185, 129, 1)',
      borderWidth: 2,
      data: [5, 10, 8, 15, 7, 7, 12],
      tension: 0.4,
      fill: true
    }
  ]
})

onMounted(() => {
  if (authStore.user) {
    adminName.value = authStore.user.name || 'Admin User'
    profileForm.value.name = authStore.user.name || ''
    profileForm.value.email = authStore.user.email || ''
    profileForm.value.number = authStore.user.number || ''
  }
})

const handleUpdateProfile = async () => {
  try {
    const updateData = {
      name: profileForm.value.name,
      email: profileForm.value.email,
      number: profileForm.value.number
    }
    
    await authStore.updateProfile(updateData)
    
    // Update local admin name display
    adminName.value = profileForm.value.name
    
    showToast('Profile updated successfully', 'success')
  } catch (error) {
    // Handle validation errors
    const errorMessage = error.response?.data?.data?.[0] || 
                        error.response?.data?.message || 
                        'Update failed'
    showToast(errorMessage, 'error')
  }
}

const handleChangePassword = async () => {
  try {
    // Validate password confirmation
    if (passwordForm.value.new_password !== passwordForm.value.new_password_confirmation) {
      showToast('New passwords do not match', 'error')
      return
    }
    
    // Call API to change password
    await adminAPI.changePassword(passwordForm.value)
    
    showToast('Password changed successfully', 'success')
    
    // Clear password fields
    passwordForm.value.old_password = ''
    passwordForm.value.new_password = ''
    passwordForm.value.new_password_confirmation = ''
  } catch (error) {
    // Handle validation errors
    const errorMessage = error.response?.data?.data?.[0] || 
                        error.response?.data?.message || 
                        'Password change failed'
    showToast(errorMessage, 'error')
  }
}

const triggerFileInput = () => {
  fileInput.value?.click()
}

const handleImageUpload = async (event) => {
  const file = event.target.files[0]
  if (!file) return

  // Validate file type
  if (!file.type.startsWith('image/')) {
    showToast('Please select an image file', 'error')
    return
  }

  // Validate file size (max 5MB)
  if (file.size > 5 * 1024 * 1024) {
    showToast('Image size must be less than 5MB', 'error')
    return
  }

  uploading.value = true
  try {
    // Create preview
    const reader = new FileReader()
    reader.onload = (e) => {
      imagePreview.value = e.target.result
    }
    reader.readAsDataURL(file)

    // Create FormData to send actual file
    const formData = new FormData()
    formData.append('profile', file)

    await authStore.updateProfilePicture(formData)
    showToast('Profile picture updated successfully', 'success')
  } catch (error) {
    // Handle validation errors
    const errorMessage = error.response?.data?.data?.[0] || 
                        error.response?.data?.message || 
                        'Upload failed'
    showToast(errorMessage, 'error')
  } finally {
    uploading.value = false
    // Clear file input
    if (fileInput.value) fileInput.value.value = ''
  }
}

const handleLogout = async () => {
  await authStore.logout()
  showToast('Logged out successfully', 'success')
  router.push('/login')
}
</script>