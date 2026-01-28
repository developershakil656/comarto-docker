<template>
  <div>
    <BottomHeader />
    <div class="container mx-auto px-4 py-8 max-w-4xl">
      <h1 class="text-3xl font-bold mb-6 text-gray-800">Privacy Policy</h1>
      
      <div class="prose max-w-none">
        <p class="mb-4"><strong>Last updated:</strong> December 23, 2025</p>
        
        <h2 class="text-2xl font-semibold mt-8 mb-4">Introduction</h2>
        <p class="mb-4">
          We respect your privacy and are committed to protecting your personal data. This privacy policy 
          explains how we collect, use, and protect your information when you use our services.
        </p>
        
        <h2 class="text-2xl font-semibold mt-8 mb-4">Information We Collect</h2>
        <p class="mb-4">
          We may collect, use, store and transfer different kinds of personal information about you when you 
          use our platform, including:
        </p>
        <ul class="list-disc pl-6 mb-4">
          <li>Identity Data: name, username, business name</li>
          <li>Contact Data: email address, phone number, address</li>
          <li>Technical Data: IP address, browser type, operating system</li>
          <li>Usage Data: information about how you use our services</li>
          <li>Marketing and Communications Data: your preferences for receiving marketing communications</li>
        </ul>
        
        <h2 class="text-2xl font-semibold mt-8 mb-4">How We Use Your Information</h2>
        <p class="mb-4">
          We use your personal data for the following purposes:
        </p>
        <ul class="list-disc pl-6 mb-4">
          <li>To provide and maintain our services</li>
          <li>To improve, personalize, and expand our services</li>
          <li>To communicate with you about our services</li>
          <li>To provide customer support</li>
          <li>To detect, prevent, and address technical issues</li>
          <li>To comply with legal obligations</li>
        </ul>
        
        <h2 class="text-2xl font-semibold mt-8 mb-4">Data Security</h2>
        <p class="mb-4">
          We have implemented appropriate security measures to prevent your personal data from being 
          accidentally lost, used or accessed in an unauthorized way, altered or disclosed.
        </p>
        
        <h2 class="text-2xl font-semibold mt-8 mb-4">Your Data Protection Rights</h2>
        <p class="mb-4">
          Depending on your location, you may have the following rights regarding your personal data:
        </p>
        <ul class="list-disc pl-6 mb-4">
          <li>The right to access your personal data</li>
          <li>The right to rectify your personal data</li>
          <li>The right to erasure of your personal data</li>
          <li>The right to restrict processing of your personal data</li>
          <li>The right to data portability</li>
          <li>The right to object to processing</li>
        </ul>
        
        <h2 class="text-2xl font-semibold mt-8 mb-4">Contact Us</h2>
        <p class="mb-4">
          If you have any questions about this privacy policy, please contact us at:
          <a href="mailto:privacy@comarto.com" class="text-blue-600 hover:underline">privacy@comarto.com</a>
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import BottomHeader from '@/components/header/BottomHeader.vue';
import { useSEO } from '@/composables/useSEO';

export default {
  name: 'PrivacyPolicyView',
  components: {
    BottomHeader
  },
  async mounted() {
    // Set SEO meta tags for the privacy policy page
    const { setMetaTags } = useSEO();
    setMetaTags(
      'Privacy Policy - Comarto',
      'Comarto\'s privacy policy explains how we collect, use, and protect your personal data when using our B2B marketplace services in Bangladesh.',
      null,
      'Comarto, privacy policy, data protection, personal information, B2B, marketplace, Bangladesh'
    );
  }
}
</script>

<style scoped>
.privacy-policy-container {
  min-height: calc(100vh - 120px);
}
</style>