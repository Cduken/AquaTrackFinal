<script setup>
import { ref, computed } from "vue";
import { Link, usePage, router } from "@inertiajs/vue3";
import AquatrackLogo from "../AquatrackLogo.vue";
import gsap from "gsap";
import { ScrollToPlugin } from "gsap/ScrollToPlugin";

gsap.registerPlugin(ScrollToPlugin);

const page = usePage();

defineProps({
    canLogin: {
        type: Boolean,
        default: true,
    },
    canRegister: {
        type: Boolean,
        default: true,
    },
});

const isSidebarOpen = ref(false);
const showReportModal = ref(false);
const isMobileMenuOpen = ref(false);

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};

const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

const handleAddReport = () => {
    showReportModal.value = true;
    isSidebarOpen.value = false;
};

// Detect if we're on specific pages
const isReportsPage = computed(() => page.url.startsWith("/reports"));
const isSelectRolesPage = computed(() => page.url.startsWith("/select-roles"));
const isHomePage = computed(() => page.url === "/");

// Single computed to decide if we should show "Back to Home"
const showBackToHome = computed(
    () => isReportsPage.value || isSelectRolesPage.value
);

// Check if we should show the back arrow instead of the burger menu
const showBackArrow = computed(
    () => isSelectRolesPage.value || isReportsPage.value
);

// Check if we should show Features and About buttons (only on home page)
const showFeaturesAboutButtons = computed(() => isHomePage.value);

// Method to handle smooth scrolling
const scrollToSection = (sectionId) => {
    // If we're not on home page, navigate to home first with hash
    if (!isHomePage.value) {
        router.visit(`/#${sectionId}`);
        return;
    }

    // If we're already on home page, scroll to section
    const element = document.getElementById(sectionId);
    if (element) {
        gsap.to(window, {
            duration: 1,
            scrollTo: {
                y: element,
                offsetY: 80, // Adjust for fixed header height
            },
            ease: "power2.inOut",
        });
    }
};
</script>

<template>
    <!-- Navbar -->
    <header
        class="sticky top-0 z-50 inset-0 lg:py-6 md:py-4 bg-white/80 backdrop-blur-md"
    >
        <div class="mx-auto px-4 py-3 sm:px-6 lg:px-[100px]">
            <div class="flex items-center justify-between">
                <div class="flex-shrink-0">
                    <Link href="/">
                        <AquatrackLogo class="h-8 w-auto" />
                    </Link>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center gap-3">
                    <!-- Features Button (Only on Home Page) -->
                    <button
                        v-if="showFeaturesAboutButtons"
                        @click="scrollToSection('features')"
                        class="inline-flex items-center px-4 py-[10px] text-sm bg-white/20 border border-gray-300/30 text-gray-900 rounded-[50px] hover:bg-white/40 hover:text-gray-700 hover:border-gray-300/90 transition duration-300"
                    >
                        <v-icon name="bi-stars" class="h-4 w-4 mr-2" />
                        <span>Features</span>
                    </button>

                    <!-- About Button (Only on Home Page) -->
                    <button
                        v-if="showFeaturesAboutButtons"
                        @click="scrollToSection('about')"
                        class="inline-flex items-center px-4 py-[10px] text-sm bg-white/20 border border-gray-300/30 text-gray-900 rounded-[50px] hover:bg-white/40 hover:text-gray-700 hover:border-gray-300/90 transition duration-300"
                    >
                        <v-icon name="bi-info-circle" class="h-4 w-4 mr-2" />
                        <span>About</span>
                    </button>

                    <!-- Back arrow for specific pages -->
                    <Link
                        v-if="showBackToHome"
                        href="/"
                        class="inline-flex items-center px-4 py-[10px] text-sm bg-white/20 border border-gray-300/30 text-black rounded-[50px] hover:bg-white/40 hover:text-gray-700 hover:border-gray-300/50 transition duration-300"
                    >
                        <v-icon name="bi-arrow-bar-left" class="h-6 w-6" />
                        <span>Back to Home</span>
                    </Link>

                    <!-- Get Started button for home page -->
                    <Link
                        v-else
                        :href="route('select-roles')"
                        class="inline-flex items-center px-4 py-[10px] text-sm bg-white/20 border border-gray-300/30 text-gray-900 rounded-[50px] hover:bg-white/40 hover:text-gray-700 hover:border-gray-300/90 transition duration-300"
                    >
                        <span>Get Started</span>
                        <v-icon
                            name="bi-arrow-bar-right"
                            class="h-6 w-6 ml-2"
                        />
                    </Link>
                </div>

                <!-- Mobile Navigation -->
                <div class="flex md:hidden items-center gap-2">
                    <!-- Mobile Features Button (Only on Home Page) -->
                    <button
                        v-if="showFeaturesAboutButtons"
                        @click="scrollToSection('features')"
                        class="inline-flex items-center p-2 text-sm bg-white/20 border border-gray-300/30 text-gray-900 rounded-full hover:bg-white/40 transition duration-300"
                        title="Features"
                    >
                        <v-icon name="bi-stars" class="h-4 w-4" />
                    </button>

                    <!-- Mobile About Button (Only on Home Page) -->
                    <button
                        v-if="showFeaturesAboutButtons"
                        @click="scrollToSection('about')"
                        class="inline-flex items-center p-2 text-sm bg-white/20 border border-gray-300/30 text-gray-900 rounded-full hover:bg-white/40 transition duration-300"
                        title="About"
                    >
                        <v-icon name="bi-info-circle" class="h-4 w-4" />
                    </button>

                    <!-- Back arrow for specific pages -->
                    <Link
                        v-if="showBackToHome"
                        href="/"
                        class="inline-flex items-center p-2 text-sm bg-white/20 border border-gray-300/30 text-black rounded-full hover:bg-white/40 transition duration-300"
                        title="Back to Home"
                    >
                        <v-icon name="bi-arrow-bar-left" class="h-4 w-4" />
                    </Link>

                    <!-- Get Started button for home page -->
                    <Link
                        v-else
                        :href="route('select-roles')"
                        class="inline-flex items-center p-2 text-sm bg-white/20 border border-gray-300/30 text-gray-900 rounded-full hover:bg-white/40 transition duration-300"
                        title="Get Started"
                    >
                        <v-icon name="bi-arrow-bar-right" class="h-4 w-4" />
                    </Link>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-1">
        <slot></slot>
    </main>
</template>

<style scoped>
/* Ensure main content isn't affected by the overlay */
main {
    position: relative;
    z-index: 10;
}

/* Smooth transition for the overlay background */
.fixed.bg-black {
    transition: opacity 0.3s ease-in-out;
    opacity: 1;
}

.fixed.bg-black[style="display: none"] {
    opacity: 0;
}
</style>
