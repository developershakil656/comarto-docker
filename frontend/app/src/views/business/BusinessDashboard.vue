<template>
    <div class="bg-gray-50 min-h-screen font-poppins">
        <div class="flex">
            <!-- Removed static sidebar -->
            <div class="flex-1">
                <div class="hidden md:block">
                    <AdminDashboardHeader title="Dashboard" />
                </div>

                <div class="container mx-auto mb-6">
                    <!-- Banner -->
                    <section class="md:mx-12 mt-6">
                        <div class="rounded-xl shadow flex flex-col md:flex-row items-center justify-between p-10 bg-primary/10 relative overflow-hidden bg-no-repeat bg-center"
                            style="background-image: url('https://placehold.co/720x220?text=Next-Level-Business');">
                            <div class="flex-1">
                                <h2 class="text-2xl font-bold mb-2">
                                    <span class="text-primary">Share all your business details</span><br />
                                    <span class="text-gray-800">Attract More Customers</span>
                                </h2>
                                <button @click="handleAdvertiseClick" v-if="false"
                                    class="mt-4 px-6 py-2 bg-primary text-white font-semibold rounded-lg shadow hover:bg-primary/85 transition">Advertise
                                    Now</button>
                            </div>
                        </div>
                    </section>

                    <!-- Profile Score -->
                    <section class="md:mx-12 mt-6" v-if="false">
                        <div
                            class="bg-white rounded-xl shadow border flex flex-col md:flex-row items-center justify-between p-4">
                            <div class="flex items-center gap-4">
                                <!-- Circular Progress -->
                                <div class="flex items-center p-4 w-fit">
                                    <div class="relative w-20 h-20 mr-4">
                                        <ProfileCircleProgress />
                                    </div>
                                    <div>
                                        <div class="font-semibold text-primary text-lg leading-tight mb-1">Increase
                                            Business Profile Score</div>
                                        <div class="text-gray-500 text-sm">Reach out to more customers</div>
                                    </div>
                                </div>
                            </div>
                            <button @click="handleIncreaseScoreClick"
                                class="mt-4 md:mt-0 px-6 py-2 bg-primary text-white font-semibold rounded-lg shadow hover:bg-primary/85 transition">INCREASE
                                SCORE</button>
                        </div>
                    </section>

                    <!-- Quick Links -->
                    <section class="md:mx-12 mt-8">
                        <div class="bg-white rounded-xl shadow border p-6">
                            <h3 class="font-semibold text-lg mb-4">Quick Links</h3>
                            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-6">
                                <component v-for="link in quickLinks" :key="link.label"
                                    :is="link.type === 'route' ? 'router-link' : 'button'"
                                    v-bind="link.type === 'route' ? { to: link.url } : {}"
                                    @click="link.type === 'modal' ? openModal(link.modal) : null"
                                    class="flex flex-col items-center group cursor-pointer" type="button">
                                    <div
                                        :class="['w-14 h-14 flex items-center justify-center rounded-full mb-2 border border-gray-200 transition hover:opacity-70', link.bg]">
                                        <component :is="link.icon" :class="['w-7 h-7', link.iconColor]" />
                                    </div>
                                    <span class="text-xs text-center font-medium text-gray-700">{{ link.label }}</span>
                                </component>
                            </div>
                        </div>
                    </section>

                    <!-- Suggestions / Cards -->
                    <section class="md:mx-12 mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center h-full">
                            <DevicePhoneMobileIcon class="text-3xl text-blue-500 mb-2 w-10 h-10" />
                            <div class="font-semibold mb-1">Download App</div>
                            <div class="text-gray-500 text-sm mb-2 text-center">Download App for Quick Access</div>
                            <button @click="handleDownloadApp"
                                class="px-4 py-2 mt-auto bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition">Download
                                QR</button>
                        </div>

                        <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center h-full">
                            <IdentificationIcon class="text-3xl text-green-500 mb-2 w-10 h-10" />
                            <div class="font-semibold mb-1">Verify Your Identity</div>
                            <div class="text-gray-500 text-sm mb-2 text-center">Verify your identity to make your
                                business more trustworthy</div>
                            <router-link to="/user/identity/verify"
                                class="px-4 mt-auto py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition">Verify
                                Identity</router-link>
                        </div>

                        <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center h-full">
                            <MapPinIcon class="text-3xl text-purple-500 mb-2 w-10 h-10" />
                            <div class="font-semibold mb-1">Add Complete Address</div>
                            <div class="text-gray-500 text-sm mb-2 text-center">Add store location for your customer to
                                locate you easily</div>
                            <button @click="handleAddAddress"
                                class="px-4 py-2 mt-auto bg-purple-500 text-white rounded-lg font-medium hover:bg-purple-600 transition">Add
                                Address</button>
                        </div>
                        <!-- <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center">
                <ArrowUpTrayIcon class="text-3xl text-yellow-500 mb-2 w-10 h-10" />
                <div class="font-semibold mb-1">Upload Rate Card / Catalogue</div>
                <div class="text-gray-500 text-sm mb-2 text-center">Upload a Rate Card / Catalogue to help your customers</div>
                <button @click="handleUploadCatalogue" class="px-4 py-2 bg-yellow-500 text-white rounded-lg font-medium hover:bg-yellow-600 transition">Upload</button>
            </div> -->
                    </section>
                </div>
            </div>
        </div>
        <!-- Example modal -->
        <AddContactsModal :open="showModal === 'addContact'" @close="closeModal" />
        <AddLinksModal :open="showModal === 'addLinks'" :initial-links="{
            website: myBusinessDetails?.website,
            facebook: myBusinessDetails?.facebook,
            direction: myBusinessDetails?.direction
        }" @close="closeModal" />
        <PaymentTypeModal :open="showModal === 'paymentType'" :initial-payment-type="myBusinessDetails?.payment_type"
            @close="closeModal" />
        <BusinessTypeModal :open="showModal === 'businessType'"
            :selected-business-types="this.myBusiness?.business_type" mode="dashboard" :business-id="myBusiness?.id"
            @close="closeModal" />
        <BusinessTimingsModal :open="showModal === 'businessTimings'" :initial-timings="myBusinessDetails?.timing || {}"
            @close="closeModal" />
        <BusinessAddressModal :open="showModal === 'businessAddress'" :initial-data="{
            address: myBusiness?.address || '',
            post_code: myBusiness?.post_code || '',
            location: myBusiness?.location || null
        }" @close="closeModal" @address-updated="handleAddressUpdated" />
        <BusinessGalleryModal :open="showModal === 'businessGallery'" :initial-images="myBusiness?.gallery || []"
            :initial-video-url="myBusinessDetails?.video_url || ''" @close="closeModal"
            @gallery-updated="handleGalleryUpdated" />
    </div>

    <!-- Mobile Bottom Navigation for Business -->
    <BusinessMobileBottomNavigation />
</template>

<script>
import {
    PencilSquareIcon,
    MegaphoneIcon,
    PhotoIcon,
    EnvelopeIcon,
    ClockIcon,
    DocumentDuplicateIcon,
    TagIcon,
    StarIcon,
    GlobeAltIcon,
    VideoCameraIcon,
    LinkIcon,
    IdentificationIcon,
    MapPinIcon,
    ArrowUpTrayIcon,
    BanknotesIcon,
    BriefcaseIcon,
    PlusIcon,
    InboxIcon,
    CreditCardIcon,
    DevicePhoneMobileIcon
} from '@heroicons/vue/24/outline';
import { push } from 'notivue';
import { useModalScroll } from '@/composables/useModalScroll';
import AdminDashboardHeader from '@/components/business/AdminDashboardHeader.vue';
import ProfileCircleProgress from '@/components/business/ProfileCircleProgress.vue';
import AddContactsModal from './modals/AddContactsModal.vue';
import AddLinksModal from './modals/AddLinksModal.vue';
import PaymentTypeModal from './modals/PaymentTypeModal.vue';
import BusinessTypeModal from './modals/BusinessTypeModal.vue';
import BusinessTimingsModal from './modals/BusinessTimingsModal.vue';
import BusinessAddressModal from './modals/BusinessAddressModal.vue';
import BusinessGalleryModal from './modals/BusinessGalleryModal.vue';
import BusinessMobileBottomNavigation from '@/components/business/BusinessMobileBottomNavigation.vue';
export default {
    name: 'BusinessDashboard',
    setup() {
        const { openModal: openModalScroll, closeModal: closeModalScroll } = useModalScroll();
        return { openModalScroll, closeModalScroll };
    },
    components: {
        AdminDashboardHeader,
        ProfileCircleProgress,
        PencilSquareIcon,
        MegaphoneIcon,
        PhotoIcon,
        EnvelopeIcon,
        ClockIcon,
        DocumentDuplicateIcon,
        TagIcon,
        StarIcon,
        GlobeAltIcon,
        VideoCameraIcon,
        LinkIcon,
        IdentificationIcon,
        MapPinIcon,
        ArrowUpTrayIcon,
        BanknotesIcon,
        BriefcaseIcon,
        PlusIcon,
        InboxIcon,
        CreditCardIcon,
        AddContactsModal,
        AddLinksModal,
        PaymentTypeModal,
        BusinessTypeModal,
        BusinessTimingsModal,
        BusinessAddressModal,
        BusinessGalleryModal,
        DevicePhoneMobileIcon,
        BusinessMobileBottomNavigation
    },
    data() {
        return {
            sidebarOpen: false,
            currentBusinessTypes: [], // Current business types
            quickLinks: [
                { label: 'Add Product', icon: 'PlusIcon', bg: 'bg-green-100', iconColor: 'text-green-600', type: 'route', url: '/my/business/add/product' },
                { label: 'My Products', icon: 'TagIcon', bg: 'bg-indigo-100', iconColor: 'text-indigo-600', type: 'route', url: '/my/business/products' },
                { label: 'Business Details', icon: 'PencilSquareIcon', bg: 'bg-blue-100', iconColor: 'text-blue-600', type: 'route', url: '/my/business/details' },
                { label: 'Reviews', icon: 'StarIcon', bg: 'bg-yellow-100', iconColor: 'text-yellow-500', type: 'route', url: '/my/business/reviews' },
                { label: 'Leads', icon: 'InboxIcon', bg: 'bg-purple-100', iconColor: 'text-purple-600', type: 'route', url: '/my/business/leads' },
                { label: 'Buy Lead Credits', icon: 'BanknotesIcon', bg: 'bg-emerald-50', iconColor: 'text-emerald-600', type: 'route', url: '/my/business/lead-credits/buy' },
                { label: 'Gallery & Video', icon: 'PhotoIcon', bg: 'bg-pink-100', iconColor: 'text-pink-500', type: 'modal', modal: 'businessGallery' },
                { label: 'Add Contact', icon: 'EnvelopeIcon', bg: 'bg-yellow-100', iconColor: 'text-yellow-500', type: 'modal', modal: 'addContact' },
                { label: 'Business Address', icon: 'MapPinIcon', bg: 'bg-gray-100', iconColor: 'text-gray-700', type: 'modal', modal: 'businessAddress' },
                { label: 'Business Timings', icon: 'ClockIcon', bg: 'bg-green-100', iconColor: 'text-green-600', type: 'modal', modal: 'businessTimings' },
                { label: 'Add Links', icon: 'LinkIcon', bg: 'bg-teal-100', iconColor: 'text-teal-600', type: 'modal', modal: 'addLinks' },
                { label: 'Payment Type', icon: 'CreditCardIcon', bg: 'bg-green-50', iconColor: 'text-green-700', type: 'modal', modal: 'paymentType' },
                { label: 'Business Type', icon: 'BriefcaseIcon', bg: 'bg-gray-100', iconColor: 'text-gray-700', type: 'modal', modal: 'businessType' },
            ],
            showModal: null
        }
    },
    methods: {
        openModal(modalName) {
            this.showModal = modalName;
            // Use the modal scroll composable to prevent body scroll
            this.openModalScroll(modalName);
        },

        closeModal() {
            const currentModal = this.showModal;
            // Use the modal scroll composable to restore body scroll first
            this.closeModalScroll(currentModal);
            // Then close the modal
            this.showModal = null;
        },

        async handleAddressUpdated(updatedAddress) {
            // The store is already updated by the modal, so we just need to refresh the data
            await this.$store.dispatch('fetchMyBusiness');
            push.success('Business address updated successfully!');
        },

        async handleGalleryUpdated(galleryData) {
            // Refresh the business data to get updated gallery
            await this.$store.dispatch('fetchMyBusiness');
            push.success('Business gallery updated successfully!');
        },
        handleAdvertiseClick() {
            push.info('Opening advertising options');
        },
        handleIncreaseScoreClick() {
            push.info('Opening business profile score management');
        },
        handleDownloadApp() {
            push.info('Comming soon!');
            // Implement actual QR code download logic here
        },
        handleAddAddress() {
            // push.info('Opening address management');
            this.showModal = 'businessAddress';
        },
        handleUploadCatalogue() {
            push.info('Opening rate card/catalogue management');
        }
    },
    computed: {
        myBusiness() {
            return this.$store.getters.myBusiness;
        },
        myBusinessDetails() {
            return this.$store.getters.myBusinessDetails;
        }
    }

}
</script>

<style scoped>
.font-poppins {
    font-family: 'Poppins', sans-serif;
}
</style>
