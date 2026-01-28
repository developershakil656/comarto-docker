<template>
  <div class="p-8">
    <h2 class="text-2xl font-bold mb-4">Modal Scroll Test</h2>
    <p class="mb-4">This page has scrollable content to test the modal scroll management.</p>
    
    <!-- Test buttons -->
    <div class="space-y-4">
      <button 
        @click="showLoginModal = true"
        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
      >
        Open Login Modal
      </button>
      
      <button 
        @click="showInquiryModal = true"
        class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600"
      >
        Open Inquiry Modal
      </button>
      
      <button 
        @click="showErrorModal = true"
        class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600"
      >
        Open Error Modal
      </button>
    </div>
    
    <!-- Scrollable content -->
    <div class="mt-8 space-y-4">
      <div v-for="i in 20" :key="i" class="p-4 bg-gray-100 rounded">
        <h3 class="font-semibold">Content Block {{ i }}</h3>
        <p>This is some content to make the page scrollable. When a modal is open, you should not be able to scroll the background.</p>
      </div>
    </div>
    
    <!-- Modals -->
    <LoginModal :show="showLoginModal" @close="showLoginModal = false" />
    <InquiryModal v-if="showInquiryModal" @close="showInquiryModal = false" />
    <ErrorModal :open="showErrorModal" :message="'This is a test error message'" @close="showErrorModal = false" />
  </div>
</template>

<script>
import LoginModal from '@/components/modals/LoginModal.vue'
import InquiryModal from '@/components/modals/InquiryModal.vue'
import ErrorModal from '@/views/business/modals/ErrorModal.vue'

export default {
  name: 'ModalScrollTest',
  components: {
    LoginModal,
    InquiryModal,
    ErrorModal
  },
  data() {
    return {
      showLoginModal: false,
      showInquiryModal: false,
      showErrorModal: false
    }
  }
}
</script>
