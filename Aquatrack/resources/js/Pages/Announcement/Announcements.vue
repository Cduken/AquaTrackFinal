// Pages/Announcements/Announcements.vue
<script setup>
import { Head, Link } from "@inertiajs/vue3";
import Navigation from "@/Components/Header/Navigation.vue";
import {
    Calendar,
    Clock,
    User,
    Info,
    Home,
    ChevronLeft,
    ChevronRight,
    Filter,
    X,
    CalendarDays,
    Search,
    AlertCircle,
    Bell,
    Megaphone,
    Shield,
} from "lucide-vue-next";
import { ref, computed, onMounted } from "vue";

const props = defineProps({
    title: String,
    description: String,
    announcements: {
        type: Array,
        default: () => [],
    },
});

// Reactive data
const currentDate = ref(new Date());
const selectedDate = ref(null);
const searchQuery = ref("");
const showMobileFilters = ref(false);

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

// Computed properties
const currentMonth = computed(() => {
    return currentDate.value.toLocaleDateString("en-US", {
        month: "long",
        year: "numeric",
    });
});

const calendarDays = computed(() => {
    const year = currentDate.value.getFullYear();
    const month = currentDate.value.getMonth();

    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const startDay = new Date(firstDay);
    startDay.setDate(startDay.getDate() - firstDay.getDay());

    const days = [];
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    for (let i = 0; i < 42; i++) {
        const date = new Date(startDay);
        date.setDate(startDay.getDate() + i);

        const dateString = date.toISOString().split("T")[0];

        // Check if any announcement is active on this date
        const hasActiveAnnouncements = activeAnnouncements.value.some((ann) => {
            const annStart = ann.start_date
                ? new Date(ann.start_date)
                : new Date(ann.created_at);
            const annEnd = ann.end_date ? new Date(ann.end_date) : null;

            const annStartDate = annStart.toISOString().split("T")[0];
            const annEndDate = annEnd
                ? annEnd.toISOString().split("T")[0]
                : null;

            if (annEndDate) {
                return dateString >= annStartDate && dateString <= annEndDate;
            }
            return dateString >= annStartDate;
        });

        days.push({
            date: new Date(date),
            isCurrentMonth: date.getMonth() === month,
            isToday: date.toDateString() === today.toDateString(),
            isSelected:
                selectedDate.value &&
                date.toDateString() === selectedDate.value.toDateString(),
            hasActiveAnnouncements,
        });
    }

    return days;
});

const filteredAnnouncements = computed(() => {
    let filtered = activeAnnouncements.value;

    // Filter by search query
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(
            (ann) =>
                ann.title.toLowerCase().includes(query) ||
                ann.content.toLowerCase().includes(query)
        );
    }

    // Filter by selected date
    if (selectedDate.value) {
        const selectedDateString = selectedDate.value
            .toISOString()
            .split("T")[0];
        filtered = filtered.filter((ann) => {
            const annStart = ann.start_date
                ? new Date(ann.start_date)
                : new Date(ann.created_at);
            const annEnd = ann.end_date ? new Date(ann.end_date) : null;

            const annStartDate = annStart.toISOString().split("T")[0];
            const annEndDate = annEnd
                ? annEnd.toISOString().split("T")[0]
                : null;

            if (annEndDate) {
                return (
                    selectedDateString >= annStartDate &&
                    selectedDateString <= annEndDate
                );
            }
            return selectedDateString >= annStartDate;
        });
    }

    return filtered;
});

// Methods
const navigateMonth = (direction) => {
    const newDate = new Date(currentDate.value);
    newDate.setMonth(newDate.getMonth() + direction);
    currentDate.value = newDate;
};

const selectDate = (day) => {
    if (
        selectedDate.value &&
        day.date.toDateString() === selectedDate.value.toDateString()
    ) {
        selectedDate.value = null;
    } else {
        selectedDate.value = day.date;
    }
    showMobileFilters.value = false;
};

const clearFilters = () => {
    selectedDate.value = null;
    searchQuery.value = "";
    showMobileFilters.value = false;
};

const getDaysRemaining = (endDate) => {
    if (!endDate) return null;
    const end = new Date(endDate);
    const now = new Date();
    const diffTime = end - now;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays > 0 ? diffDays : 0;
};

const toggleMobileFilters = () => {
    showMobileFilters.value = !showMobileFilters.value;
};

// Set today as default selected date
onMounted(() => {
    selectedDate.value = new Date();
});
</script>

<template>
    <Head>
        <title>{{ title || "Announcements - Clarin Water District" }}</title>
        <meta name="description" :content="description" />
    </Head>

    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
        <Navigation />

        <!-- Hero Section -->
        <section class="pt-[80px] pb-6 px-4 sm:px-6">
            <div class="max-w-7xl mx-auto">
                <div class="text-center">

                <div
                        class="inline-flex items-center px-3 py-1.5 rounded-full bg-blue-500/10 border border-blue-500/20 mb-4 text-xs"
                    >
                        <Megaphone class="w-3 h-3 text-blue-400 mr-1.5" />
                        <span class="font-medium text-blue-300"
                            >Announcements</span
                        >
                    </div>


                    <h1 class="text-2xl sm:text-3xl lg:text-6xl font-bold text-white mb-3">
                        Active <span class="bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-transparent">Announcements</span>
                    </h1>
                    <p class="text-lg text-slate-300 max-w-2xl mx-auto mb-4 px-4">
                        Stay informed with current updates from Clarin Water District
                    </p>

                    <!-- Quick Stats - Mobile optimized -->
                    <div class="grid grid-cols-3 gap-2 max-w-lg mx-auto px-4">
                        <div
                            class="bg-slate-800/50 backdrop-blur-sm rounded-lg border border-slate-700/50 p-2"
                        >
                            <div class="flex items-center justify-center space-x-1">
                                <CalendarDays class="text-blue-400 flex-shrink-0" :size="14" />
                                <div class="text-left min-w-0">
                                    <p class="text-base font-bold text-white truncate">
                                        {{ activeAnnouncements.length }}
                                    </p>
                                    <p class="text-slate-400 text-xs truncate">
                                        Active
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div
                            class="bg-slate-800/50 backdrop-blur-sm rounded-lg border border-slate-700/50 p-2"
                        >
                            <div class="flex items-center justify-center space-x-1">
                                <AlertCircle class="text-green-400 flex-shrink-0" :size="14" />
                                <div class="text-left min-w-0">
                                    <p class="text-base font-bold text-white truncate">
                                        {{ new Date().getDate() }}
                                    </p>
                                    <p class="text-slate-400 text-xs truncate">
                                        Today
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div
                            class="bg-slate-800/50 backdrop-blur-sm rounded-lg border border-slate-700/50 p-2"
                        >
                            <div class="flex items-center justify-center space-x-1">
                                <Clock class="text-purple-400 flex-shrink-0" :size="14" />
                                <div class="text-left min-w-0">
                                    <p class="text-base font-bold text-white truncate">
                                        {{ new Date().getFullYear() }}
                                    </p>
                                    <p class="text-slate-400 text-xs truncate">
                                        Year
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Mobile Filter Toggle -->
        <div class="lg:hidden px-4 mb-3">
            <button
                @click="toggleMobileFilters"
                class="w-full flex items-center justify-center space-x-2 bg-slate-800/50 backdrop-blur-sm border border-slate-700/50 rounded-lg py-2 text-white hover:bg-slate-700/50 transition duration-200 text-sm"
            >
                <Filter :size="14" />
                <span>{{
                    showMobileFilters
                        ? "Hide Filters"
                        : "Show Filters & Calendar"
                }}</span>
            </button>
        </div>

        <!-- Main Content -->
        <section class="pb-6 px-4 sm:px-6">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-col lg:flex-row lg:space-x-4">
                    <!-- Calendar & Filters Sidebar -->
                    <div
                        class="lg:w-80 mb-4 lg:mb-0"
                        :class="[
                            showMobileFilters ? 'block' : 'hidden lg:block',
                        ]"
                    >
                        <div
                            class="bg-slate-800/50 backdrop-blur-sm rounded-xl border border-slate-700/50 p-4 lg:sticky lg:top-4"
                        >
                            <!-- Welcome Message -->
                            <div class="mb-4">
                                <h2 class="text-sm font-semibold text-white mb-1 flex items-center">
                                    <Megaphone class="mr-2 flex-shrink-0" :size="14" />
                                    Active Notices
                                </h2>
                                <p class="text-slate-400 text-xs">
                                    View current announcements
                                </p>
                            </div>

                            <!-- Quick Actions -->
                            <div class="space-y-2 mb-4">
                                <button
                                    @click="clearFilters"
                                    :class="[
                                        'w-full flex items-center justify-between px-3 py-2 rounded-lg border transition duration-200 text-xs',
                                        !selectedDate && !searchQuery
                                            ? 'bg-blue-500/20 border-blue-500/50 text-blue-300'
                                            : 'bg-slate-700/30 border-slate-600/30 text-slate-300 hover:bg-slate-700/50 hover:border-slate-500',
                                    ]"
                                >
                                    <span class="flex items-center truncate">
                                        <Bell class="mr-2 flex-shrink-0" :size="12" />
                                        <span class="truncate">Show All Active</span>
                                    </span>
                                    <span
                                        class="text-xs bg-slate-600/50 px-1.5 py-0.5 rounded flex-shrink-0 ml-1"
                                    >
                                        {{ activeAnnouncements.length }}
                                    </span>
                                </button>
                            </div>

                            <!-- Search -->
                            <div class="mb-4">
                                <label class="block text-xs font-medium text-slate-400 mb-1 flex items-center">
                                    <Search :size="12" class="mr-1 flex-shrink-0" />
                                    Search Announcements
                                </label>
                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Search announcements..."
                                    class="w-full px-3 py-2 bg-slate-700/50 border border-slate-600/50 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:border-blue-500 transition duration-200 text-xs"
                                />
                            </div>

                            <!-- Calendar Section -->
                            <div class="mb-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-sm font-semibold text-white flex items-center">
                                        <Calendar class="mr-1 flex-shrink-0" :size="12" />
                                        Calendar
                                    </h3>
                                    <button
                                        @click="clearFilters"
                                        class="flex items-center text-xs text-slate-400 hover:text-white transition duration-200"
                                    >
                                        <X :size="10" class="mr-1 flex-shrink-0" />
                                        Clear
                                    </button>
                                </div>

                                <!-- Calendar Navigation -->
                                <div class="flex items-center justify-between mb-2 px-1">
                                    <button
                                        @click="navigateMonth(-1)"
                                        class="p-1 hover:bg-slate-700/50 rounded transition duration-200 flex-shrink-0"
                                    >
                                        <ChevronLeft :size="14" class="text-slate-400" />
                                    </button>
                                    <h4 class="text-xs font-semibold text-white text-center px-1 truncate">
                                        {{ currentMonth }}
                                    </h4>
                                    <button
                                        @click="navigateMonth(1)"
                                        class="p-1 hover:bg-slate-700/50 rounded transition duration-200 flex-shrink-0"
                                    >
                                        <ChevronRight :size="14" class="text-slate-400" />
                                    </button>
                                </div>

                                <!-- Calendar Grid -->
                                <div class="grid grid-cols-7 gap-0.5 mb-2 px-1">
                                    <div
                                        v-for="day in ['S', 'M', 'T', 'W', 'T', 'F', 'S']"
                                        :key="day"
                                        class="text-center text-xs font-medium text-slate-400 py-1"
                                    >
                                        {{ day }}
                                    </div>
                                    <div
                                        v-for="day in calendarDays"
                                        :key="day.date.getTime()"
                                        @click="selectDate(day)"
                                        :class="[
                                            'h-7 rounded border transition-all duration-200 cursor-pointer flex items-center justify-center text-xs font-medium relative',
                                            day.isCurrentMonth
                                                ? day.isToday
                                                    ? 'bg-blue-500/30 border-blue-500 text-white'
                                                    : day.isSelected
                                                    ? 'bg-slate-600 border-slate-500 text-white'
                                                    : 'bg-slate-700/30 border-slate-600/30 text-slate-300 hover:bg-slate-700/50 hover:border-slate-500'
                                                : 'bg-slate-800/20 border-slate-700/20 text-slate-500',
                                        ]"
                                    >
                                        {{ day.date.getDate() }}
                                        <!-- Blue dot for dates with active announcements -->
                                        <div
                                            v-if="day.hasActiveAnnouncements"
                                            class="absolute -top-0.5 -right-0.5 w-1.5 h-1.5 bg-blue-500 rounded-full border border-slate-800"
                                        ></div>
                                    </div>
                                </div>

                                <!-- Calendar Legend -->
                                <div class="space-y-1 text-xs">
                                    <div class="flex items-center space-x-1 text-slate-400">
                                        <div class="w-1.5 h-1.5 bg-blue-500 rounded-full flex-shrink-0"></div>
                                        <span class="text-xs">Active announcements</span>
                                    </div>
                                    <div class="flex items-center space-x-1 text-slate-400">
                                        <div class="w-1.5 h-1.5 bg-blue-500/30 rounded-full border border-blue-500 flex-shrink-0"></div>
                                        <span class="text-xs">Today's date</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Help Text -->
                            <div class="text-center p-2 bg-blue-500/10 rounded-lg border border-blue-500/20">
                                <p class="text-xs text-blue-300">
                                    💡 Click dates with blue dots to see active announcements
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Announcements Content -->
                    <div class="flex-1 min-w-0">
                        <!-- Content Header -->
                        <div
                            class="bg-slate-800/50 backdrop-blur-sm rounded-xl border border-slate-700/50 p-3 mb-3"
                        >
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex-1 min-w-0">
                                    <h2 class="text-lg font-bold text-white mb-1 truncate">
                                        {{
                                            selectedDate
                                                ? `Announcements for ${selectedDate.toLocaleDateString(
                                                      "en-US",
                                                      {
                                                          month: "short",
                                                          day: "numeric",
                                                          year: "numeric",
                                                      }
                                                  )}`
                                                : "All Active Announcements"
                                        }}
                                    </h2>
                                    <p class="text-slate-400 text-xs">
                                        {{
                                            filteredAnnouncements.length
                                        }}
                                        announcement{{
                                            filteredAnnouncements.length !== 1
                                                ? "s"
                                                : ""
                                        }}
                                        <span v-if="searchQuery" class="text-blue-300">
                                            matching "{{ searchQuery }}"</span>
                                        <span v-if="selectedDate" class="text-blue-300">
                                            on this date</span>
                                    </p>
                                </div>
                                <div
                                    class="flex items-center space-x-1 mt-2 sm:mt-0 sm:ml-3 flex-shrink-0"
                                    v-if="selectedDate || searchQuery"
                                >
                                    <Filter :size="12" class="text-blue-400" />
                                    <span class="text-blue-400 text-xs">Filters Active</span>
                                </div>
                            </div>
                        </div>

                        <!-- Announcements List -->
                        <div
                            v-if="filteredAnnouncements.length > 0"
                            class="space-y-3"
                        >
                            <div
                                v-for="announcement in filteredAnnouncements"
                                :key="announcement.id"
                                class="bg-slate-800/30 backdrop-blur-sm rounded-lg border border-slate-700/30 p-3 hover:border-slate-600/50 transition-all duration-200"
                            >
                                <div class="flex flex-col space-y-2">
                                    <div class="flex items-start justify-between">
                                        <h3 class="text-base font-semibold text-white flex-1 pr-2">
                                            {{ announcement.title }}
                                        </h3>
                                        <div class="flex-shrink-0">
                                            <span
                                                class="inline-flex items-center px-1.5 py-0.5 bg-green-500/20 text-green-400 text-xs rounded border border-green-500/30"
                                            >
                                                Active
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Announcement Meta Info -->
                                    <div class="flex flex-wrap gap-1">
                                        <span
                                            class="flex items-center bg-slate-700/50 px-2 py-0.5 rounded text-xs text-slate-300"
                                        >
                                            <Calendar :size="10" class="mr-1 flex-shrink-0" />
                                            {{ new Date(announcement.start_date || announcement.created_at).toLocaleDateString() }}
                                        </span>
                                        <span
                                            v-if="announcement.end_date"
                                            class="flex items-center bg-slate-700/50 px-2 py-0.5 rounded text-xs text-slate-300"
                                        >
                                            <Calendar :size="10" class="mr-1 flex-shrink-0" />
                                            {{ new Date(announcement.end_date).toLocaleDateString() }}
                                        </span>
                                        <span
                                            class="flex items-center bg-slate-700/50 px-2 py-0.5 rounded text-xs text-slate-300"
                                        >
                                            <User :size="10" class="mr-1 flex-shrink-0" />
                                            {{ announcement.author }}
                                        </span>
                                    </div>

                                    <!-- Days Remaining -->
                                    <div
                                        v-if="
                                            announcement.end_date &&
                                            getDaysRemaining(announcement.end_date) > 0
                                        "
                                        class="flex items-center gap-1"
                                    >
                                        <span class="text-xs text-slate-500">Valid for:</span>
                                        <span
                                            class="px-1.5 py-0.5 bg-green-500/20 text-green-400 text-xs rounded border border-green-500/30"
                                        >
                                            {{
                                                getDaysRemaining(announcement.end_date)
                                            }}
                                            days remaining
                                        </span>
                                    </div>

                                    <!-- Announcement Content -->
                                    <div class="prose prose-invert max-w-none">
                                        <p class="text-slate-300 leading-relaxed whitespace-pre-line text-sm">
                                            {{ announcement.content }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div v-else class="text-center">
                            <div
                                class="bg-slate-800/50 backdrop-blur-sm rounded-xl border border-slate-700/50 p-6"
                            >
                                <div
                                    class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center mx-auto mb-3"
                                >
                                    <Info :size="20" class="text-blue-400" />
                                </div>
                                <h2 class="text-lg font-bold text-white mb-2">
                                    {{
                                        activeAnnouncements.length === 0
                                            ? "No Active Announcements"
                                            : "No Matching Announcements"
                                    }}
                                </h2>
                                <p class="text-slate-300 mb-4 max-w-md mx-auto text-sm px-4">
                                    {{
                                        activeAnnouncements.length === 0
                                            ? "There are no active announcements at the moment. Please check back later for updates."
                                            : "No announcements match your current filters. Try adjusting your search or date selection."
                                    }}
                                </p>
                                <div class="flex flex-col sm:flex-row gap-2 justify-center">
                                    <button
                                        v-if="activeAnnouncements.length > 0"
                                        @click="clearFilters"
                                        class="inline-flex items-center justify-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded transition duration-200 text-sm"
                                    >
                                        <X :size="14" class="mr-1 flex-shrink-0" />
                                        Clear Filters
                                    </button>
                                    <Link
                                        href="/"
                                        class="inline-flex items-center justify-center px-3 py-2 border border-slate-600 hover:border-slate-500 text-slate-300 hover:text-white font-medium rounded transition duration-200 text-sm"
                                    >
                                        <Home :size="14" class="mr-1 flex-shrink-0" />
                                        Back to Home
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<style scoped>
/* Custom scrollbar for better mobile experience */
.prose {
    max-height: none;
    overflow: visible;
}

/* Ensure proper text wrapping on mobile */
.text-wrap {
    word-wrap: break-word;
    overflow-wrap: break-word;
}

/* Improve touch targets for mobile */
@media (max-width: 640px) {
    button, [role="button"] {
        min-height: 44px;
    }

    .calendar-day {
        min-height: 32px;
        min-width: 32px;
    }
}
</style>
