import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { adminAPI } from '../api/services'

export const useAuthStore = defineStore('auth', () => {
  const token = ref(localStorage.getItem('admin_token') || null)
  const user = ref(JSON.parse(localStorage.getItem('admin_user') || 'null'))
  const loading = ref(false)

  const isAuthenticated = computed(() => !!token.value)

  const setToken = (newToken) => {
    token.value = newToken
    if (newToken) {
      localStorage.setItem('admin_token', newToken)
    } else {
      localStorage.removeItem('admin_token')
    }
  }

  const setUser = (userData) => {
    user.value = userData
    if (userData) {
      localStorage.setItem('admin_user', JSON.stringify(userData))
    } else {
      localStorage.removeItem('admin_user')
    }
  }

  const login = async (credentials) => {
    loading.value = true
    try {
      const response = await adminAPI.login(credentials)
      const authToken = response.data.data.token
      setToken(authToken)
      
      // Set user data from admin object
      if (response.data.data.admin) {
        setUser(response.data.data.admin)
      }
      
      return response
    } finally {
      loading.value = false
    }
  }

  const logout = async () => {
    try {
      await adminAPI.logout()
    } catch (error) {
      console.error('Logout API error:', error)
    } finally {
      setToken(null)
      setUser(null)
    }
  }

  const updateProfile = async (data) => {
    loading.value = true
    try {
      const response = await adminAPI.update(data)
      
      // Update user from response if available
      if (response.data.data) {
        setUser(response.data.data)
      } else {
        // Fallback: manually update the user object with the new data
        const updatedUser = { ...user.value, ...data }
        delete updatedUser.old_password
        delete updatedUser.new_password
        setUser(updatedUser)
      }
      
      return response
    } finally {
      loading.value = false
    }
  }

  // New function to update profile picture
  const updateProfilePicture = async (formData) => {
    loading.value = true
    try {
      const response = await adminAPI.updateProfilePicture(formData)
      
      // Update user from response if available
      if (response.data.data && response.data.data) {
        setUser(response.data.data)
      }
      
      return response
    } finally {
      loading.value = false
    }
  }

  return {
    token,
    user,
    loading,
    isAuthenticated,
    login,
    logout,
    updateProfile,
    updateProfilePicture, // Export the new function
    setUser
  }
})