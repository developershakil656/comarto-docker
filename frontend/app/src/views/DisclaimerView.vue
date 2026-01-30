<template>
  <div>
      <BottomHeader />
    <div class="container mx-auto px-4 py-8 max-w-4xl">
      <h1 class="text-3xl font-bold mb-6 text-gray-800">Disclaimer</h1>
      
      <div class="prose max-w-none">
        <p class="mb-4"><strong>Last updated:</strong> December 23, 2025</p>
        
        <h2 class="text-2xl font-semibold mt-8 mb-4">General Information</h2>
        <p class="mb-4">
          The information provided on this website is for general informational purposes only. 
          Comarto makes no representations or warranties of any kind, express or implied, 
          about the completeness, accuracy, reliability, or availability of the information, 
          products, services, or related graphics contained on this website.
        </p>
        
        <h2 class="text-2xl font-semibold mt-8 mb-4">Use of Services</h2>
        <p class="mb-4">
          Any reliance you place on the information provided on this website is strictly at your own risk. 
          Comarto disclaims all liability and responsibility arising from any reliance placed on such 
          materials by you or any other visitor to our website, or by anyone who may be informed of 
          any of its contents.
        </p>
        
        <h2 class="text-2xl font-semibold mt-8 mb-4">Third-Party Content</h2>
        <p class="mb-4">
          The content provided by businesses and users on our platform represents their own views and 
          opinions, and does not necessarily reflect the views and opinions of Comarto. We do not 
          guarantee the accuracy, completeness, or reliability of any third-party content.
        </p>
        
        <h2 class="text-2xl font-semibold mt-8 mb-4">Limitation of Liability</h2>
        <p class="mb-4">
          Comarto shall not be liable for any loss or damage, including but not limited to indirect 
          or consequential loss or damage, or any loss or damage whatsoever arising from loss of 
          data or profits arising out of, or in connection with, the use of this website.
        </p>
        
        <h2 class="text-2xl font-semibold mt-8 mb-4">External Links</h2>
        <p class="mb-4">
          Our website may contain links to external websites. We have no control over the nature, 
          content, and availability of those sites and are not responsible for any information, 
          products, or services provided by third-party websites.
        </p>
        
        <h2 class="text-2xl font-semibold mt-8 mb-4">Professional Advice</h2>
        <p class="mb-4">
          The information on this website is not intended to be a substitute for professional advice. 
          You should consult with appropriate professionals before making any business decisions 
          based on the information provided on this site.
        </p>
        
        <h2 class="text-2xl font-semibold mt-8 mb-4">Changes to Content</h2>
        <p class="mb-4">
          We reserve the right to modify, update, or remove any content on this website at any time 
          without prior notice. Continued use of the website after any changes constitutes acceptance 
          of those changes.
        </p>
        
        <h2 class="text-2xl font-semibold mt-8 mb-4">Accuracy of Information</h2>
        <p class="mb-4">
          While we strive to keep the information on our website up to date and accurate, we make no 
          representations or warranties of any kind about the completeness, accuracy, reliability, 
          or suitability of the information, products, services, or related graphics.
        </p>
        
        <h2 class="text-2xl font-semibold mt-8 mb-4">Contact Us</h2>
        <p class="mb-4">
          If you have any questions about this disclaimer, please contact us at:
          <a href="mailto:comarto.com@gmail.com" class="text-blue-600 hover:underline">comarto.com@gmail.com</a>
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import BottomHeader from '@/components/header/BottomHeader.vue';
import { useSEO } from '@/composables/useSEO';

export default {
  name: 'DisclaimerView',
  components: {
      BottomHeader
  },
  async mounted() {
    // Set SEO meta tags for the disclaimer page
    const { setMetaTags } = useSEO();
    setMetaTags(
      'Disclaimer - Comarto',
      'Read Comarto\'s disclaimer for terms of use, limitations of liability, and information about our B2B marketplace services in Bangladesh.',
      null,
      'Comarto, disclaimer, terms, liability, B2B, marketplace, Bangladesh'
    );
  }
}
</script>

<style scoped>
/* Additional styles if needed */
</style>