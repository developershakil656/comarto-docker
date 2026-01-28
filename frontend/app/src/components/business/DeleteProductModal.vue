<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
    <!-- Mobile: Full screen with card design -->
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center md:block md:p-0">
      <!-- Background overlay -->
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="$emit('close')"></div>

      <!-- Modal panel -->
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all md:my-8 md:align-middle md:max-w-lg md:w-full">
        <!-- Mobile Header -->
        <div class="md:hidden">
          <MobileModalHeader 
            title="Delete Product" 
            @back="$emit('close')" 
          />
        </div>

        <!-- Content -->
        <div class="bg-white px-4 pt-5 pb-4md:p-6 md:pb-4">
          <div class="md:flex md:items-start">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 md:mx-0 md:h-10 md:w-10">
              <ExclamationTriangleIcon class="h-6 w-6 text-red-600" />
            </div>
            <div class="mt-3 text-center md:mt-0 md:ml-4 md:text-left w-full">
              <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4 md:hidden">
                Delete Product
              </h3>
              
              <div class="space-y-4">
                <p class="text-gray-600">
                  Are you sure you want to delete "<strong>{{ productName }}</strong>"? This action cannot be undone.
                </p>
              </div>
            </div>
          </div>
        </div>
        
        <div class="bg-gray-50 px-4 py-3 md:px-6 md:flex md:flex-row-reverse">
          <button
            @click="deleteProduct"
            :disabled="deleting"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 md:ml-3 md:w-auto md:text-sm disabled:opacity-50"
          >
            {{ deleting ? 'Deleting...' : 'Delete Product' }}
          </button>
          <button
            @click="$emit('close')"
            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 md:mt-0 md:ml-3 md:w-auto md:text-sm"
          >
            Cancel
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ExclamationTriangleIcon } from '@heroicons/vue/24/outline'
import { useModalScroll } from '@/composables/useModalScroll'
import MobileModalHeader from '@/components/common/MobileModalHeader.vue'

export default {
  name: 'DeleteProductModal',
  components: {
    ExclamationTriangleIcon,
    MobileModalHeader
  },
  setup() {
    const { openModal, closeModal } = useModalScroll()
    return { openModal, closeModal }
  },
  props: {
    show: {
      type: Boolean,
      default: false
    },
    productId: {
      type: String,
      default: null
    },
    productName: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      deleting: false
    }
  },
  watch: {
    show(newVal) {
      if (newVal) {
        this.openModal('delete-product-modal')
      } else {
        this.closeModal('delete-product-modal')
      }
    }
  },
  methods: {
    async deleteProduct() {
      if (!this.productId) return

      try {
        this.deleting = true
        await this.$store.dispatch('deleteUserProduct', this.productId)
        this.$emit('product-deleted', this.productId, 'Product deleted successfully')
        this.$emit('close')
      } catch (error) {
        this.$emit('error', 'Failed to delete product. Please try again.')
      } finally {
        this.deleting = false
      }
    }
  }
}
</script>
