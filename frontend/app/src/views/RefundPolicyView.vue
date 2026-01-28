<template>
  <div>
      <BottomHeader />
    <div class="container mx-auto px-4 py-8 max-w-4xl">
      <h1 class="text-3xl font-bold mb-6 text-gray-800">Refund Policy</h1>
      
      <div class="prose max-w-none">
        <p class="mb-4"><strong>Last updated:</strong> December 23, 2025</p>
        
        <h2 class="text-2xl font-semibold mt-8 mb-4">Refund Overview</h2>
        <p class="mb-4">
          Our refund policy is designed to ensure customer satisfaction while maintaining fair business practices. 
          This policy applies to all purchases and services provided through our platform.
        </p>
        
        <h2 class="text-2xl font-semibold mt-8 mb-4">Eligibility for Refunds</h2>
        <p class="mb-4">
          Refunds may be available under the following circumstances:
        </p>
        <ul class="list-disc pl-6 mb-4">
          <li>Service not delivered as described</li>
          <li>Technical issues that cannot be resolved</li>
          <li>Business listing not activated within specified timeframe</li>
          <li>Duplicate charges or billing errors</li>
          <li>Cancelled services before commencement</li>
        </ul>
        
        <h2 class="text-2xl font-semibold mt-8 mb-4">Refund Process</h2>
        <p class="mb-4">
          To request a refund, please contact our support team with the following information:
        </p>
        <ul class="list-disc pl-6 mb-4">
          <li>Order/transaction reference number</li>
          <li>Reason for refund request</li>
          <li>Supporting documentation (if applicable)</li>
        </ul>
        <p class="mb-4">
          Refund requests are typically processed within 5-7 business days. Once approved, refunds will be 
          credited to the original payment method within 3-5 business days.
        </p>
        
        <h2 class="text-2xl font-semibold mt-8 mb-4">Non-Refundable Items</h2>
        <p class="mb-4">
          The following items and services are generally non-refundable:
        </p>
        <ul class="list-disc pl-6 mb-4">
          <li>Completed business listing services</li>
          <li>Lead credits once purchased</li>
          <li>Customized services already delivered</li>
          <li>Free trial periods</li>
          <li>Services that have already been rendered</li>
        </ul>
        
        <h2 class="text-2xl font-semibold mt-8 mb-4">Payment Methods</h2>
        <p class="mb-4">
          Refunds will be processed using the same payment method used for the original transaction. 
          In cases where the original payment method is no longer available, alternative arrangements 
          will be made on a case-by-case basis.
        </p>
        
        <h2 class="text-2xl font-semibold mt-8 mb-4">Contact Us</h2>
        <p class="mb-4">
          For refund requests or questions about our refund policy, please contact us at:
          <a href="mailto:refunds@comarto.com" class="text-blue-600 hover:underline">refunds@comarto.com</a>
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import BottomHeader from '@/components/header/BottomHeader.vue';
import { useSEO } from '@/composables/useSEO';

export default {
  name: 'RefundPolicyView',
  components: {
      BottomHeader
  },
  async mounted() {
    // Set SEO meta tags for the refund policy page
    const { setMetaTags } = useSEO();
    setMetaTags(
      'Refund Policy - Comarto',
      'Comarto\'s refund policy outlines eligibility, process, and terms for refunds on business listings and services in Bangladesh.',
      null,
      'Comarto, refund policy, business services, listings, Bangladesh, B2B'
    );
  }
}
</script>

<style scoped>
/* Additional styles if needed */
</style>