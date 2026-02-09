<template lang="">
<!-- main header -->
<!-- <TopHeader :keyword="$route.params.keyword || ''" /> -->
<BottomHeader />

<div class="container">
    <!-- Remove static h1, dynamic results are shown in SearchItems -->
</div>

<div class="min-h-screen container mx-auto">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row gap-6 my-4">
        <SearchSidebar
            :selectedBusinessTypes="selectedBusinessTypes"
            :isOpen="isSidebarOpen"
            :suppliers="suppliers"
            @filter-change="onFilterChange"
            @close="isSidebarOpen = false"
        />
        <SearchItems @toggle-sidebar="isSidebarOpen = !isSidebarOpen" :keyword="keyword"/>
    </div>
</div>

    <MobileBottomNavigation/>
</template>

<script>
import BottomHeader from '@/components/header/BottomHeader.vue'
import { useSEO } from '@/composables/useSEO';
import SearchItems from '@/components/search/SearchItems.vue';
import SearchSidebar from '@/components/search/SearchSidebar.vue'
import MobileBottomNavigation from '@/components/common/MobileBottomNavigation.vue';

export default {
    components: {
        BottomHeader,
        SearchItems,
        SearchSidebar,
        MobileBottomNavigation
    },
    data() {
        return {
            isSidebarOpen: false,
            searchTimeout: null,
            updatingRouteFromStore: false, // Flag to prevent infinite loop between route and store watchers
            resizeTimeout: null // Throttle resize events
        };
    },
    props: {
        location: {
            type: String,
            default: 'all-bangladesh'
        },
        keyword: {
            type: String,
            default: ''
        }
    },
    computed: {
        selectedBusinessTypes() {
            const q = this.$route.query.business_type;
            return q ? q.split(',').filter(Boolean) : [];
        },
        suppliers() {
            return this.$route.query.suppliers === 'true';
        }
    },
    watch: {
        $route: {
            handler(newRoute, oldRoute) {
                // Check if both routes exist before comparing parameters
                if (!newRoute || !oldRoute) return;
                
                // Only trigger search if route parameters actually changed
                if ((newRoute.params && oldRoute.params && 
                     newRoute.params.location !== oldRoute.params.location) || 
                    (newRoute.params && oldRoute.params && 
                     newRoute.params.keyword !== oldRoute.params.keyword) ||
                    (newRoute.query && oldRoute.query && 
                     newRoute.query.business_type !== oldRoute.query.business_type) ||
                    (newRoute.query && oldRoute.query && 
                     newRoute.query.suppliers !== oldRoute.query.suppliers)) {
                    
                    // Get route parameters for search
                    const keyword = (newRoute.params && newRoute.params.keyword) || '';
                    const category_slug = (newRoute.query && newRoute.query.category_slug) || '';
                    const location = (newRoute.params && newRoute.params.location) || 'all-bangladesh';
                    const businessTypes = (newRoute.query && newRoute.query.business_type)
                        ? newRoute.query.business_type.split(',').filter(Boolean)
                        : [];
                    const suppliers = (newRoute.query && newRoute.query.suppliers) === 'true';

                    
                    // Perform search with updated parameters
                    this.doSearch(keyword, businessTypes, suppliers, location, category_slug);
                }
            },
            immediate: true // Trigger on initial route as well
        },
        '$store.getters.selectedLocation': {
            handler(newLocation) {
                // Get the current route parameters
                const keyword = this.$route.params.keyword || '';
                const currentRouteLocation = this.$route.params.location || 'all-bangladesh';
                
                // Convert the new location to slug format
                let newLocationSlug = 'all-bangladesh';
                if (newLocation) {
                    // Convert "Nawabganj, Dhaka" to "Nawabganj-Dhaka"
                    newLocationSlug = newLocation.replace(/,\s*/g, '-').replace(/\s+/g, '-').toLowerCase();
                }
                
                // Only update the route if the location has actually changed
                if (currentRouteLocation !== newLocationSlug) {
                    // Prevent infinite loop by setting a flag
                    this.updatingRouteFromStore = true;
                    
                    // Update the route to reflect the new location
                    this.$router.replace({
                        name: 'search',
                        params: {
                            location: newLocationSlug,
                            keyword: keyword
                        },
                        query: this.$route.query
                    }).finally(() => {
                        // Reset the flag after route update
                        this.$nextTick(() => {
                            this.updatingRouteFromStore = false;
                        });
                    });
                }
            }
        }
    },
    methods: {
        updateSearchMetaTags(keyword, location) {
            const { setMetaTags } = useSEO();
            const locationName = location.replace(/-/g, ' ').replace('all bangladesh', 'Bangladesh');
            
            if (keyword) {
                setMetaTags(
                    `Search Results for '${keyword}' in ${locationName} - Comarto B2B Marketplace`,
                    `Find ${keyword} products or suppliers, manufacturers and businesses in ${locationName}. Connect with verified sellers in Bangladesh on Comarto.`,
                    null,
                    `${keyword}, suppliers, manufacturers, ${locationName}, Comarto, B2B, wholesale, trade`
                );
            } else {
                setMetaTags(
                    `Search for Suppliers in ${locationName} - Comarto B2B Marketplace`,
                    `Find products or suppliers, manufacturers and businesses in ${locationName}. Connect with verified sellers in Bangladesh on Comarto.`,
                    null,
                    `suppliers, manufacturers, ${locationName}, Comarto, B2B, wholesale, trade`
                );
            }
        },
        
        unslugify(slug) {
            return slug
                .split('-')
                .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                .join(' ');
        },
        checkScreenSize() {
            // md breakpoint is 768px in Tailwind CSS
            this.isLargeScreen = window.innerWidth >= 768;
            // Set sidebar open state based on screen size on initial load only
            if (this._isInitialLoad) {
                this.isSidebarOpen = this.isLargeScreen;
                this._isInitialLoad = false;
            }
        },
        onFilterChange(filter) {
            const businessTypes = filter.businessTypes || [];
            const query = {
                ...this.$route.query,
                business_type: businessTypes.join(','),
                suppliers: filter.suppliers ? 'true' : 'false'
            };
            // Remove category_slugs from query if present
            // delete query.category_slugs;
            this.$router.replace({
                name: 'search',
                params: {
                    location: this.$route.params.location || 'all-bangladesh',
                    keyword: this.$route.params.keyword || ''
                },
                query
            });
        },
        async doSearch(keyword, businessTypes, suppliers, location = 'all-bangladesh', category_slug = '') {
            // Update meta tags for the search
            this.updateSearchMetaTags(keyword, location);
            
            // Location verification is now handled in the mounted hook and through the store watcher
            // We no longer need to verify the location here to prevent duplicate calls
            // if (location === 'all-bangladesh') {
            //     this.$store.commit('SET_SELECTED_LOCATION', '');
            // }

            // Clear any existing timeout to prevent duplicate calls
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }
            
            // Debounce the search call to prevent rapid successive API calls
            this.searchTimeout = setTimeout(() => {
                const params = { keyword: keyword || '' };
                if (category_slug) {
                    params.category_slug = category_slug;
                }
                if (businessTypes && businessTypes.length) {
                    params.business_types = businessTypes.join(',');
                }
                if (suppliers) {
                    params.suppliers = 'true';
                }
        
                // Add location to params if it's not 'all-bangladesh'
                if (location !== 'all-bangladesh') {
                    params.location = location;
                }
        
                // Track the search keyword if it exists
                if (keyword && keyword.trim()) {
                    this.$store.dispatch('trackSearchKeyword', keyword.trim());
                }
                this.$store.dispatch('search', params);
            }, 300);
        },
        
        setSidebarState() {
            // Throttled version to prevent scroll jank
            if (this.resizeTimeout) {
                clearTimeout(this.resizeTimeout);
            }
            
            this.resizeTimeout = setTimeout(() => {
                // Get screen width
                const screenWidth = window.innerWidth;
                
                // Tailwind's md breakpoint is 768px
                // Set sidebar open for medium screens and above
                this.isSidebarOpen = screenWidth >= 768;
            }, 150); // 150ms throttle as per best practice
        }
    },
    async mounted() {
        // Get route parameters for initial search
        const keyword = this.$route.params.keyword || '';
        const category_slug = this.$route.query.category_slug || '';
        const location = this.$route.params.location || 'all-bangladesh';
        const businessTypes = this.$route.query.business_type
            ? this.$route.query.business_type.split(',').filter(Boolean)
            : [];
        const suppliers = this.$route.query.suppliers === 'true';
        
        // Verify and set location on component mount
        if (location !== 'all-bangladesh') {
            const isValidLocation = await this.$store.dispatch('verifyAndSetLocation', location);
            console.log('Location verification from mounted');
            
            // If location verification failed, redirect to all-bangladesh
            if (!isValidLocation) {
                this.$router.replace({
                    name: 'search',
                    params: {
                        location: 'all-bangladesh',
                        keyword: this.$route.params.keyword || ''
                    },
                    query: this.$route.query
                });
                return; // Exit early if location verification failed
            }
        }
        
        // Set sidebar open state based on screen size
        this.setSidebarState();
        
        // Add resize listener to handle screen size changes
        window.addEventListener('resize', this.setSidebarState);
        
        // Perform initial search with all parameters
        this.doSearch(keyword, businessTypes, suppliers, location, category_slug);
    },
    beforeUnmount() {
        // Clean up timeout
        if (this.searchTimeout) {
            clearTimeout(this.searchTimeout);
        }
        
        // Clean up resize timeout
        if (this.resizeTimeout) {
            clearTimeout(this.resizeTimeout);
        }
        
        // Remove resize listener
        window.removeEventListener('resize', this.setSidebarState);
    }
};
</script>