import { ref, onMounted, onUnmounted } from "vue";

// Global state to track open modals
const openModals = ref(new Set());
let originalBodyStyle = "";
let scrollPosition = 0;

/**
 * Composable for managing modal scroll behavior
 * Automatically disables body scroll when any modal is open
 * and re-enables it when all modals are closed
 */
export function useModalScroll() {
  const modalId = ref(null);

  /**
   * Register a modal as open
   * @param {string} id - Unique identifier for the modal
   */
  const openModal = (id) => {
    if (!id) {
      return;
    }

    modalId.value = id;
    openModals.value.add(id);
    updateBodyScroll();
  };

  /**
   * Unregister a modal as closed
   * @param {string} id - Unique identifier for the modal
   */
  const closeModal = (id) => {
    if (!id) {
      return;
    }

    openModals.value.delete(id);
    modalId.value = null;
    updateBodyScroll();
  };

  /**
   * Update body scroll based on open modals
   */
  let scrollPosition = 0;

  const updateBodyScroll = () => {
    if (openModals.value.size > 0) {
      scrollPosition = window.scrollY;
      const scrollbarWidth =
        window.innerWidth - document.documentElement.clientWidth;

      // Disable background scroll
      document.documentElement.style.overflow = "hidden";
      document.body.style.overflow = "hidden";

      // Prevent layout shift
      document.documentElement.style.paddingRight = `${scrollbarWidth}px`;
    } else {
      // Restore scroll
      document.documentElement.style.overflow = "";
      document.body.style.overflow = "";
      document.documentElement.style.paddingRight = "";

      window.scrollTo(0, scrollPosition);
    }
  };

  /**
   * Get the current modal ID
   */
  const getModalId = () => modalId.value;

  /**
   * Check if any modals are currently open
   */
  const hasOpenModals = () => openModals.value.size > 0;

  /**
   * Force close all modals (emergency cleanup)
   */
  const closeAllModals = () => {
    openModals.value.clear();
    modalId.value = null;
    updateBodyScroll();
  };

  // Cleanup on component unmount
  onUnmounted(() => {
    if (modalId.value) {
      closeModal(modalId.value);
    }
  });

  return {
    openModal,
    closeModal,
    getModalId,
    hasOpenModals,
    closeAllModals,
  };
}

/**
 * Global modal manager for advanced use cases
 */
export const modalManager = {
  openModals: openModals,

  /**
   * Get count of open modals
   */
  getOpenModalCount: () => openModals.value.size,

  /**
   * Check if a specific modal is open
   */
  isModalOpen: (id) => openModals.value.has(id),

  /**
   * Get all open modal IDs
   */
  getOpenModalIds: () => Array.from(openModals.value),

  /**
   * Force close all modals
   */
  closeAll: () => {
    openModals.value.clear();
    updateBodyScroll();
  },
};

// Global function to update body scroll (used by modalManager)
const updateBodyScroll = () => {
  if (openModals.value.size > 0) {
    // Store original body style if not already stored
    if (!originalBodyStyle) {
      originalBodyStyle = document.body.style.overflow || "";
    }
    // Store current scroll position
    scrollPosition = window.scrollY;
    // Disable body scroll
    document.body.style.overflow = "hidden";
    // Prevent scroll on touch devices
    document.body.style.position = "fixed";
    document.body.style.width = "100%";
    document.body.style.top = `-${scrollPosition}px`;
  } else {
    // Restore original body style
    if (originalBodyStyle) {
      document.body.style.overflow = originalBodyStyle;
    } else {
      document.body.style.overflow = "";
    }
    // Restore scroll position and remove fixed positioning
    document.body.style.position = "";
    document.body.style.width = "";
    document.body.style.top = "";
    // Restore scroll position
    window.scrollTo(0, scrollPosition);
  }
};
