// Pages/Announcements/Announcements.vue
<script setup>
import { Head, Link } from "@inertiajs/vue3";
import Navigation from "@/Components/Header/Navigation.vue";
import {
    Calendar,
    Clock,
    User,
    Home,
    X,
    Search,
    Bell,
    Megaphone,
    TrendingUp,
    ArrowRight,
    Sparkles,
    CheckCircle2,
} from "lucide-vue-next";
import { ref, computed } from "vue";

const props = defineProps({
    title: String,
    description: String,
    announcements: {
        type: Array,
        default: () => [],
    },
});

// Reactive data
const searchQuery = ref("");
const selectedFilter = ref("all");

// Filter only active announcements
const activeAnnouncements = computed(() => {
    return props.announcements.filter((announcement) => {
        const now = new Date();
        const startDate = announcement.start_date
            ? new Date(announcement.start_date)
            : null;
        const endDate = announcement.end_date
            ? new Date(announcement.end_date)
            : null;

        if (announcement.status !== "Active") return false;

        if (startDate && endDate) {
            return now >= startDate && now <= endDate;
        } else if (startDate && !endDate) {
            return now >= startDate;
        } else if (!startDate && endDate) {
            return now <= endDate;
        }

        return true;
    });
});

const filteredAnnouncements = computed(() => {
    let filtered = activeAnnouncements.value;

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(
            (ann) =>
                ann.title.toLowerCase().includes(query) ||
                ann.content.toLowerCase().includes(query)
        );
    }

    // Sort by date (newest first)
    return filtered.sort((a, b) => {
        const dateA = new Date(a.start_date || a.created_at);
        const dateB = new Date(b.start_date || b.created_at);
        return dateB - dateA;
    });
});

// Methods
const clearFilters = () => {
    searchQuery.value = "";
    selectedFilter.value = "all";
};

const getDaysRemaining = (endDate) => {
    if (!endDate) return null;
    const end = new Date(endDate);
    const now = new Date();
    const diffTime = end - now;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays > 0 ? diffDays : 0;
};

const getTimeAgo = (date) => {
    const now = new Date();
    const then = new Date(date);
    const diffTime = Math.abs(now - then);
    const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
    const diffHours = Math.floor(diffTime / (1000 * 60 * 60));

    if (diffHours < 1) return "Just now";
    if (diffHours < 24) return `${diffHours}h ago`;
    if (diffDays === 1) return "Yesterday";
    if (diffDays < 7) return `${diffDays}d ago`;
    if (diffDays < 30) return `${Math.floor(diffDays / 7)}w ago`;
    return then.toLocaleDateString("en-US", { month: "short", day: "numeric" });
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString("en-US", {
        month: "short",
        day: "numeric",
        year: "numeric",
    });
};
</script>

<template>
    <Head>
        <title>{{ title || "Announcements - Clarin Water District" }}</title>
        <meta name="description" :content="description" />
    </Head>

    <div
        class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900"
    >
        <Navigation />

        <!-- Compact Hero Section -->
        <section class="pt-8 lg:pt-20  pb-6 px-4">
            <div class="max-w-6xl mx-auto">
                <!-- Header -->
                <div class="text-center mb-6">


                    <h1
                        class="text-3xl sm:text-4xl md:text-6xl font-bold text-white mb-2"
                    >
                        Announce<span class="bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-transparent">ments</span>
                    </h1>
                    <p
                        class="text-sm sm:text-base text-slate-400 max-w-2xl mx-auto"
                    >
                        Stay updated with the latest news from Clarin Water
                        District
                    </p>
                </div>

                <!-- Quick Stats -->
                <div class="grid grid-cols-3 gap-3 max-w-2xl mx-auto mb-6">
                    <div
                        class="bg-slate-800/50 backdrop-blur-sm rounded-xl border border-slate-700/50 p-3 text-center"
                    >
                        <div class="text-2xl font-bold text-white mb-0.5">
                            {{ activeAnnouncements.length }}
                        </div>
                        <div class="text-xs text-slate-400">Active</div>
                    </div>
                    <div
                        class="bg-slate-800/50 backdrop-blur-sm rounded-xl border border-slate-700/50 p-3 text-center"
                    >
                        <div class="text-2xl font-bold text-white mb-0.5">
                            {{ new Date().getDate() }}
                        </div>
                        <div class="text-xs text-slate-400">
                            {{
                                new Date().toLocaleDateString("en-US", {
                                    month: "short",
                                })
                            }}
                        </div>
                    </div>
                    <div
                        class="bg-slate-800/50 backdrop-blur-sm rounded-xl border border-slate-700/50 p-3 text-center"
                    >
                        <div class="text-2xl font-bold text-white mb-0.5">
                            {{ new Date().getFullYear() }}
                        </div>
                        <div class="text-xs text-slate-400">Year</div>
                    </div>
                </div>

                <!-- Search Bar -->
                <div class="max-w-2xl mx-auto">
                    <div class="relative">
                        <Search
                            class="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400 w-4 h-4"
                        />
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search announcements..."
                            class="w-full pl-10 pr-10 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-blue-500/50 focus:ring-2 focus:ring-blue-500/20 transition"
                        />
                        <button
                            v-if="searchQuery"
                            @click="searchQuery = ''"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-slate-400 hover:text-white transition"
                        >
                            <X class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <section class="pb-12 px-4">
            <div class="max-w-6xl mx-auto">
                <!-- Results Info -->
                <div v-if="searchQuery" class="mb-4 text-center">
                    <p class="text-xs text-slate-400">
                        Found
                        <span class="text-white font-semibold">{{
                            filteredAnnouncements.length
                        }}</span>
                        {{
                            filteredAnnouncements.length === 1
                                ? "announcement"
                                : "announcements"
                        }}
                        <button
                            @click="clearFilters"
                            class="text-blue-400 hover:text-blue-300 ml-1 underline"
                        >
                            Clear search
                        </button>
                    </p>
                </div>

                <!-- Announcements List -->
                <div v-if="filteredAnnouncements.length > 0" class="space-y-3">
                    <div
                        v-for="(announcement, index) in filteredAnnouncements"
                        :key="announcement.id"
                        class="group bg-slate-800/40 backdrop-blur-lg rounded-xl border border-slate-700/50 hover:border-slate-600/50 hover:bg-slate-800/60 transition-all duration-300 overflow-hidden"
                    >
                        <div class="p-4 sm:p-5">
                            <!-- Header -->
                            <div
                                class="flex items-start justify-between gap-3 mb-3"
                            >
                                <div class="flex-1 min-w-0">
                                    <div
                                        class="flex items-center gap-2 mb-1.5 flex-wrap"
                                    >
                                        <!-- Status Badge -->
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-500/20 text-green-400 text-xs font-medium rounded-md border border-green-500/30"
                                        >
                                            <span
                                                class="w-1 h-1 bg-green-400 rounded-full animate-pulse"
                                            ></span>
                                            Active
                                        </span>

                                        <!-- Time Badge -->
                                        <span class="text-xs text-slate-500">
                                            {{
                                                getTimeAgo(
                                                    announcement.start_date ||
                                                        announcement.created_at
                                                )
                                            }}
                                        </span>

                                        <!-- Days Remaining Badge -->
                                        <span
                                            v-if="
                                                announcement.end_date &&
                                                getDaysRemaining(
                                                    announcement.end_date
                                                ) > 0
                                            "
                                            class="inline-flex items-center gap-1 px-2 py-0.5 bg-orange-500/20 text-orange-400 text-xs font-medium rounded-md border border-orange-500/30"
                                        >
                                            <Clock class="w-3 h-3" />
                                            {{
                                                getDaysRemaining(
                                                    announcement.end_date
                                                )
                                            }}d left
                                        </span>
                                    </div>

                                    <!-- Title -->
                                    <h3
                                        class="text-base sm:text-lg font-semibold text-white mb-2 group-hover:text-blue-400 transition-colors leading-snug"
                                    >
                                        {{ announcement.title }}
                                    </h3>

                                    <!-- Content -->
                                    <p
                                        class="text-xs sm:text-sm text-slate-300 leading-relaxed mb-3 line-clamp-3"
                                    >
                                        {{ announcement.content }}
                                    </p>

                                    <!-- Meta Info -->
                                    <div
                                        class="flex flex-wrap items-center gap-3 text-xs"
                                    >
                                        <div
                                            class="flex items-center gap-1.5 text-slate-400"
                                        >
                                            <User class="w-3.5 h-3.5" />
                                            <span>{{
                                                announcement.author
                                            }}</span>
                                        </div>
                                        <div
                                            class="flex items-center gap-1.5 text-slate-400"
                                        >
                                            <Calendar class="w-3.5 h-3.5" />
                                            <span>{{
                                                formatDate(
                                                    announcement.start_date ||
                                                        announcement.created_at
                                                )
                                            }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Icon -->
                                <div class="flex-shrink-0">
                                    <div
                                        class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-blue-500/20 to-cyan-500/20 rounded-lg flex items-center justify-center border border-blue-500/20 group-hover:scale-110 transition-transform"
                                    >
                                        <Megaphone
                                            class="w-5 h-5 sm:w-6 sm:h-6 text-blue-400"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Accent Bar -->
                        <div
                            class="h-1 bg-gradient-to-r from-blue-500/50 via-cyan-500/50 to-blue-500/50"
                        ></div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="flex items-center justify-center py-16">
                    <div class="text-center max-w-md">
                        <div
                            class="w-16 h-16 bg-slate-800/50 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-slate-700/50"
                        >
                            <Bell class="w-8 h-8 text-slate-500" />
                        </div>
                        <h2 class="text-xl font-bold text-white mb-2">
                            {{
                                activeAnnouncements.length === 0
                                    ? "No Active Announcements"
                                    : "No Results Found"
                            }}
                        </h2>
                        <p class="text-sm text-slate-400 mb-6">
                            {{
                                activeAnnouncements.length === 0
                                    ? "There are currently no active announcements. Check back later for updates."
                                    : "We couldn't find any announcements matching your search."
                            }}
                        </p>
                        <div
                            class="flex flex-col sm:flex-row gap-2 justify-center"
                        >
                            <button
                                v-if="activeAnnouncements.length > 0"
                                @click="clearFilters"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition"
                            >
                                <X class="w-4 h-4" />
                                Clear Search
                            </button>
                            <Link
                                href="/"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2 border border-slate-600 hover:border-slate-500 text-slate-300 hover:text-white text-sm font-medium rounded-lg transition"
                            >
                                <Home class="w-4 h-4" />
                                Back to Home
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Footer Info -->
                <div
                    v-if="filteredAnnouncements.length > 0"
                    class="mt-8 text-center"
                >
                    <div
                        class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800/50 rounded-lg border border-slate-700/50"
                    >
                        <CheckCircle2 class="w-4 h-4 text-green-400" />
                        <span class="text-xs text-slate-400">
                            All announcements are up to date as of
                            {{
                                new Date().toLocaleDateString("en-US", {
                                    month: "long",
                                    day: "numeric",
                                    year: "numeric",
                                })
                            }}
                        </span>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<style scoped>
/* Line clamp utility */
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

@keyframes pulse {
    0%,
    100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Smooth scrolling */
html {
    scroll-behavior: smooth;
}

/* Better mobile touch targets */
@media (max-width: 640px) {
    button,
    a[role="button"] {
        min-height: 44px;
    }
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: rgba(30, 41, 59, 0.3);
}

::-webkit-scrollbar-thumb {
    background: rgba(71, 85, 105, 0.5);
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: rgba(71, 85, 105, 0.7);
}
</style>
