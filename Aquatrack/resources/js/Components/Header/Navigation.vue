//Navigation.vue
<script setup>
import { ref, computed } from "vue";
import { Link, usePage, router } from "@inertiajs/vue3";
import AquatrackLogo from "../AquatrackLogo.vue";
import {
    Home,
    Megaphone,
    ClipboardList,
    Menu,
    X,
    ArrowLeft,
    ArrowRight,
    Flag,
} from "lucide-vue-next";

const page = usePage();

const isMobileMenuOpen = ref(false);

const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

// Close mobile menu when route changes
router.on("navigate", () => {
    isMobileMenuOpen.value = false;
});

// Page detection
const isHomePage = computed(() => page.url === "/");
const isReportsPage = computed(() => page.url.startsWith("/reports"));
const isAnnouncementsPage = computed(() =>
    page.url.startsWith("/announcements")
);
const isTrackReportPage = computed(() => page.url.startsWith("/track-report"));
const isReportIssuePage = computed(() => page.url.startsWith("/report-issue"));
const isSelectRolesPage = computed(() => page.url.startsWith("/select-roles"));

// Active link classes - More subtle design
const getLinkClasses = (path, isMobile = false) => {
    const baseClasses = isMobile
        ? "flex items-center px-4 py-3 transition duration-200 text-sm"
        : "flex items-center px-3 py-2 transition duration-300 rounded-lg text-sm";

    // More subtle active state - just border and text color change
    const activeClasses = isMobile
        ? "text-white border-l-2 border-blue-400 bg-slate-800/50"
        : "text-white border-b-2 border-blue-400";

    const inactiveClasses = isMobile
        ? "text-slate-300 hover:text-white hover:bg-slate-800"
        : "text-slate-300 hover:text-white hover:bg-slate-800/50";

    // Check if current page matches the path
    const isActive =
        page.url === path ||
        (path === "/" && isHomePage.value) ||
        (path === "/announcements" && isAnnouncementsPage.value) ||
        (path === "/track-report" && isTrackReportPage.value) ||
        (path === "/report-issue" && isReportIssuePage.value);

    return `${baseClasses} ${isActive ? activeClasses : inactiveClasses}`;
};
</script>

<template>
    <!-- Navbar -->
    <nav class="bg-transparent relative p-4">
        <div class="mx-auto px-4 sm:px-6 lg:px-[100px]">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <Link href="/" class="flex items-center">
                        <AquatrackLogo class="h-8 w-auto" />
                    </Link>
                </div>

                <!-- Desktop Navigation Links with Get Started/Go Back Button -->
                <div class="hidden md:flex items-center">
                    <!-- Only show navigation links if NOT on select-roles page -->
                    <div
                        v-if="!isSelectRolesPage"
                        class="flex items-center gap-4"
                    >
                        <Link href="/" :class="getLinkClasses('/')">
                            <Home class="h-4 w-4 mr-2" />
                            Home
                        </Link>

                        <Link
                            :href="route('announcements.guest')"
                            :class="getLinkClasses('/announcements')"
                        >
                            <Megaphone class="h-4 w-4 mr-2" />
                            Announcements
                        </Link>

                        <Link
                            :href="route('track-report')"
                            :class="getLinkClasses('/track-report')"
                        >
                            <ClipboardList class="h-4 w-4 mr-2" />
                            Track Report
                        </Link>

                        <Link
                            :href="route('report-issue.create')"
                            :class="getLinkClasses('/report-issue')"
                        >
                            <Flag class="h-4 w-4 mr-2" />
                            Report Issue
                        </Link>
                    </div>

                    <!-- Get Started / Go Back Button -->
                    <Link
                        v-if="!isSelectRolesPage"
                        :href="route('select-roles')"
                        class="flex items-center px-6 py-2.5 text-sm ml-6 text-gray-200 bg-transparent border border-gray-200/20 rounded-lg hover:text-white hover:border-white/40 transition duration-300"
                    >
                        Get Started
                        <ArrowRight class="h-4 w-4 ml-2" />
                    </Link>

                    <!-- Go Back Button (only on select-roles page) -->
                    <Link
                        v-else
                        href="/"
                        class="flex items-center px-6 py-2.5 text-sm text-gray-200 bg-transparent border border-gray-200/20 rounded-lg hover:text-white hover:border-white/40 transition duration-300"
                    >
                        <ArrowLeft class="h-4 w-4 mr-2" />
                        Go Back
                    </Link>
                </div>

                <!-- Mobile Navigation - Only Menu Toggle -->
                <div class="flex md:hidden items-center">
                    <!-- Mobile Menu Toggle -->
                    <button
                        @click="toggleMobileMenu"
                        class="flex items-center justify-center p-2 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition duration-300"
                    >
                        <Menu class="h-5 w-5" />
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Dropdown Menu -->
        <div
            v-if="isMobileMenuOpen"
            class="fixed inset-0 z-50 md:hidden"
            @click="isMobileMenuOpen = false"
        >
            <!-- Dropdown Content -->
            <div
                class="absolute top-16 right-4 w-[343px] xs:w-[500px] sm:w-[620px] bg-slate-900 border border-slate-700 rounded-lg shadow-2xl transform transition-all duration-300 ease-out"
                :class="{
                    'opacity-0 scale-95 translate-y-2': !isMobileMenuOpen,
                    'opacity-100 scale-100 translate-y-0': isMobileMenuOpen,
                }"
                @click.stop
            >
                <!-- Dropdown Links - Only show if NOT on select-roles page -->
                <div v-if="!isSelectRolesPage" class="py-2">
                    <Link
                        href="/"
                        :class="getLinkClasses('/', true)"
                        @click="isMobileMenuOpen = false"
                    >
                        <Home class="h-4 w-4 mr-3" />
                        <span class="font-medium">Home</span>
                    </Link>

                    <Link
                        :href="route('announcements.guest')"
                        :class="getLinkClasses('/announcements', true)"
                        @click="isMobileMenuOpen = false"
                    >
                        <Megaphone class="h-4 w-4 mr-3" />
                        <span class="font-medium">Announcements</span>
                    </Link>

                    <Link
                        :href="route('track-report')"
                        :class="getLinkClasses('/track-report', true)"
                        @click="isMobileMenuOpen = false"
                    >
                        <ClipboardList class="h-4 w-4 mr-3" />
                        <span class="font-medium">Track Report</span>
                    </Link>

                    <Link
                        :href="route('report-issue.create')"
                        :class="getLinkClasses('/report-issue', true)"
                        @click="isMobileMenuOpen = false"
                    >
                        <Flag class="h-4 w-4 mr-3" />
                        <span class="font-medium">Report Issue</span>
                    </Link>
                </div>

                <!-- Mobile Action Button -->
                <div class="border-t border-slate-700 p-2">
                    <Link
                        v-if="!isSelectRolesPage"
                        :href="route('select-roles')"
                        class="flex items-center justify-center w-full px-4 py-3 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition duration-200"
                        @click="isMobileMenuOpen = false"
                    >
                        Get Started
                        <ArrowRight class="h-4 w-4 ml-2" />
                    </Link>

                    <Link
                        v-else
                        href="/"
                        class="flex items-center justify-center w-full px-4 py-3 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition duration-200"
                        @click="isMobileMenuOpen = false"
                    >
                        <ArrowLeft class="h-4 w-4 mr-2" />
                        Go Back
                    </Link>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-1">
        <slot></slot>
    </main>
</template>
