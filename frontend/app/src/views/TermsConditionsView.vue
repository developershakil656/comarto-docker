<template>
  <div>
    <BottomHeader />
    <div class="container mx-auto px-4 py-8 max-w-4xl">
      <h1 class="text-3xl font-bold mb-6 text-gray-800">Terms & Conditions</h1>
      
      <div class="prose max-w-none">
        <p class="mb-4"><strong>Last updated:</strong> December 23, 2025</p>
        
        <h2 class="text-2xl font-semibold mt-8 mb-4">Introduction</h2>
        <p class="mb-4">
          Welcome to Comarto! These terms and conditions outline the rules and regulations for the use of 
          our platform and services.
        </p>
        
        <h2 class="text-2xl font-semibold mt-8 mb-4">Acceptance of Terms</h2>
        <p class="mb-4">
          By accessing or using our platform, you agree to be bound by these Terms and Conditions. If you 
          disagree with any part of these terms, you may not access our services.
        </p>
        
        <h2 class="text-2xl font-semibold mt-8 mb-4">User Accounts</h2>
        <p class="mb-4">
          When you create an account with us, you must provide accurate and complete information. You are 
          responsible for maintaining the security of your account and for all activities that occur under 
          your account.
        </p>
        
        <h2 class="text-2xl font-semibold mt-8 mb-4">User Content</h2>
        <p class="mb-4">
          Users may post, upload, or otherwise make available content on our platform. By doing so, you 
          grant us a non-exclusive, worldwide, royalty-free license to use, display, and distribute your 
          content in connection with our services.
        </p>
        <p class="mb-4">
          You are solely responsible for any content you post, and you must ensure that such content 
          complies with applicable laws and regulations.
        </p>
        
        <h2 class="text-2xl font-semibold mt-8 mb-4">Prohibited Activities</h2>
        <p class="mb-4">
          You agree not to engage in any of the following prohibited activities:
        </p>
        <ul class="list-disc pl-6 mb-4">
          <li>Copying, distributing, or publishing any part of our platform</li>
          <li>Using our platform for any commercial purpose without our prior written consent</li>
          <li>Using our platform in any way that violates applicable laws or regulations</li>
          <li>Attempting to gain unauthorized access to our platform or related systems</li>
          <li>Interfering with or disrupting the security of our platform</li>
          <li>Using our platform to transmit any unlawful, harassing, or harmful content</li>
        </ul>
        
        <h2 class="text-2xl font-semibold mt-8 mb-4">Intellectual Property</h2>
        <p class="mb-4">
          Our platform and its original content, features, and functionality are owned by Comarto and are 
          protected by international copyright, trademark, patent, trade secret, and other intellectual 
          property laws.
        </p>
        
        <h2 class="text-2xl font-semibold mt-8 mb-4">Limitation of Liability</h2>
        <p class="mb-4">
          In no event shall Comarto, nor its directors, employees, partners, agents, suppliers, or 
          affiliates, be liable for any indirect, incidental, special, consequential, or punitive damages, 
          including without limitation, loss of profits, data, use, goodwill, or other intangible losses, 
          resulting from your access to or use of our platform.
        </p>
        
        <h2 class="text-2xl font-semibold mt-8 mb-4">Governing Law</h2>
        <p class="mb-4">
          These Terms shall be governed and construed in accordance with the laws of Bangladesh, without 
          regard to its conflict of law provisions.
        </p>
        
        <h2 class="text-2xl font-semibold mt-8 mb-4">Changes to Terms</h2>
        <p class="mb-4">
          We reserve the right, at our sole discretion, to modify or replace these Terms at any time. 
          If a revision is material, we will provide at least 30 days' notice prior to any new terms 
          taking effect.
        </p>
        
        <h2 class="text-2xl font-semibold mt-8 mb-4">Contact Us</h2>
        <p class="mb-4">
          If you have any questions about these Terms and Conditions, please contact us at:
          <a href="mailto:legal@comarto.com" class="text-blue-600 hover:underline">legal@comarto.com</a>
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import BottomHeader from '@/components/header/BottomHeader.vue';
import { useSEO } from '@/composables/useSEO';

export default {
  name: 'TermsConditionsView',
  components: {
    BottomHeader
  },
  async mounted() {
    // Set SEO meta tags for the terms and conditions page
    const { setMetaTags } = useSEO();
    setMetaTags(
      'Terms & Conditions - Comarto',
      'Read Comarto\'s terms and conditions governing the use of our B2B marketplace platform and services in Bangladesh.',
      null,
      'Comarto, terms and conditions, user agreement, B2B, marketplace, Bangladesh'
    );
  }
}
</script>

<style scoped>
.terms-conditions-container {
  min-height: calc(100vh - 120px);
}
</style>