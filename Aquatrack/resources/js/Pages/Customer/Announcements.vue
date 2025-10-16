<template>
    <CustomerLayout>
        <div class="w-full bg-white rounded-2xl shadow-sm overflow-hidden">
            <!-- Header Section -->
            <div
                class="border-b border-gray-100 p-8 bg-gradient-to-r from-blue-50 via-indigo-50 to-purple-50"
            >
                <div class="flex items-center gap-4">
                    <div
                        class="p-3 bg-white rounded-xl shadow-sm border border-blue-100"
                    >
                        <Megaphone class="text-blue-600 w-7 h-7" />
                    </div>
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">
                            Announcements
                        </h1>
                        <p class="text-gray-600 text-base">
                            Stay informed with the latest updates and important
                            notices
                        </p>
                    </div>
                    <div
                        class="hidden lg:flex items-center gap-3 bg-white px-4 py-3 rounded-xl shadow-sm border border-gray-200"
                    >
                        <div
                            class="w-3 h-3 bg-green-500 rounded-full animate-pulse"
                        ></div>
                        <span class="text-sm font-medium text-gray-700">
                            {{ announcements.length }} Total Announcements
                        </span>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="flex flex-col xl:flex-row p-6 gap-8">
                <!-- Announcements Section -->
                <div class="xl:w-2/3">
                    <!-- Filter & Stats Bar -->
                    <div
                        class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 p-5 bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl border border-gray-200"
                    >
                        <div class="flex-1">
                            <h2 class="text-xl font-bold text-gray-900 mb-1">
                                Latest Announcements
                            </h2>
                            <div class="flex items-center gap-3 flex-wrap">
                                <p class="text-sm text-gray-600">
                                    Showing
                                    <span class="font-semibold text-blue-600">{{
                                        filteredAnnouncements.length
                                    }}</span>
                                    of
                                    <span class="font-semibold">{{
                                        announcements.length
                                    }}</span>
                                    items
                                </p>
                                <span
                                    v-if="selectedDate"
                                    class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium flex items-center gap-1"
                                >
                                    <Filter class="w-3 h-3" />
                                    Filtered by
                                    {{ formatDisplayDate(selectedDate) }}
                                </span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button
                                @click="clearDateFilter"
                                class="px-4 py-2.5 text-sm bg-white hover:bg-gray-50 text-gray-700 rounded-xl transition-all duration-200 flex items-center gap-2 border border-gray-300 hover:border-gray-400 shadow-sm hover:shadow-md"
                                :class="{
                                    'opacity-50 cursor-not-allowed pointer-events-none':
                                        !selectedDate,
                                }"
                                :disabled="!selectedDate"
                            >
                                <RefreshCw class="w-4 h-4" />
                                Clear Filter
                            </button>
                        </div>
                    </div>

                    <!-- Announcements Grid -->
                    <div class="space-y-5">
                        <!-- Announcement Cards -->
                        <div
                            v-for="announcement in filteredAnnouncements"
                            :key="announcement.id"
                            @click="openAnnouncementModal(announcement)"
                            class="group cursor-pointer transition-all duration-300 hover:scale-[1.02]"
                        >
                            <div
                                class="bg-gradient-to-br from-white to-gray-50 border-2 border-gray-200 rounded-2xl p-6 hover:shadow-xl hover:border-blue-300 transition-all duration-300 relative overflow-hidden"
                            >
                                <!-- Background Decoration -->
                                <div
                                    class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-full -translate-y-16 translate-x-16 opacity-0 group-hover:opacity-100 transition-opacity duration-500"
                                ></div>

                                <div class="relative z-10">
                                    <div
                                        class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4 mb-4"
                                    >
                                        <div class="flex-1">
                                            <h3
                                                class="text-xl font-bold text-gray-900 group-hover:text-blue-700 transition-colors duration-200 line-clamp-2 mb-2"
                                            >
                                                {{ announcement.title }}
                                            </h3>
                                            <p
                                                class="text-gray-600 text-sm leading-relaxed line-clamp-2"
                                            >
                                                {{ announcement.content }}
                                            </p>
                                        </div>
                                        <div
                                            class="flex sm:flex-col items-center sm:items-end gap-3"
                                        >
                                            <span
                                                :class="
                                                    getStatusClasses(
                                                        announcement.status
                                                    )
                                                "
                                                class="px-4 py-2 rounded-full text-sm font-semibold border shadow-sm whitespace-nowrap"
                                            >
                                                {{ announcement.status }}
                                            </span>
                                            <div
                                                class="hidden sm:flex items-center gap-1 text-xs text-gray-500"
                                            >
                                                <Eye class="w-3 h-3" />
                                                <span
                                                    >Click to view details</span
                                                >
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Date & Time Info -->
                                    <div
                                        class="flex flex-wrap items-center gap-4 pt-4 border-t border-gray-100"
                                    >
                                        <div
                                            class="flex items-center gap-2 text-sm text-gray-600"
                                        >
                                            <div
                                                class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center"
                                            >
                                                <Calendar
                                                    class="text-blue-600 w-4 h-4"
                                                />
                                            </div>
                                            <div>
                                                <div
                                                    class="text-xs text-gray-500"
                                                >
                                                    Starts
                                                </div>
                                                <div class="font-medium">
                                                    {{
                                                        formatDisplayDate(
                                                            announcement.start_date
                                                        )
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-gray-300">→</div>
                                        <div
                                            class="flex items-center gap-2 text-sm text-gray-600"
                                        >
                                            <div
                                                class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center"
                                            >
                                                <CalendarCheck
                                                    class="text-green-600 w-4 h-4"
                                                />
                                            </div>
                                            <div>
                                                <div
                                                    class="text-xs text-gray-500"
                                                >
                                                    Ends
                                                </div>
                                                <div class="font-medium">
                                                    {{
                                                        formatDisplayDate(
                                                            announcement.end_date
                                                        )
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-1"></div>
                                        <div
                                            class="flex items-center gap-2 text-xs text-gray-500 bg-gray-100 px-3 py-1.5 rounded-full"
                                        >
                                            <Clock class="w-3 h-3" />
                                            <span
                                                >Updated
                                                {{
                                                    formatRelativeTime(
                                                        announcement.updated_at
                                                    )
                                                }}</span
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div
                            v-if="filteredAnnouncements.length === 0"
                            class="text-center py-16 bg-gradient-to-br from-gray-50 to-blue-50 rounded-2xl border-2 border-dashed border-gray-300"
                        >
                            <div class="max-w-md mx-auto">
                                <div
                                    class="w-20 h-20 bg-white rounded-2xl shadow-sm border border-gray-200 flex items-center justify-center mx-auto mb-6"
                                >
                                    <Megaphone class="text-gray-400 w-8 h-8" />
                                </div>
                                <h3
                                    class="text-2xl font-bold text-gray-700 mb-3"
                                >
                                    No announcements found
                                </h3>
                                <p
                                    class="text-gray-500 text-lg mb-6 leading-relaxed"
                                >
                                    <span v-if="selectedDate">
                                        There are no announcements scheduled
                                        for<br />
                                        <span
                                            class="font-semibold text-blue-600"
                                            >{{
                                                formatDisplayDate(selectedDate)
                                            }}</span
                                        >
                                    </span>
                                    <span v-else>
                                        No announcements available at the
                                        moment.<br />
                                        Check back later for updates.
                                    </span>
                                </p>
                                <button
                                    v-if="selectedDate"
                                    @click="clearDateFilter"
                                    class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl transition-all duration-200 inline-flex items-center gap-2 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                                >
                                    <RefreshCw class="w-4 h-4" />
                                    Show All Announcements
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Calendar & Sidebar Section -->
                <div class="xl:w-1/3">
                    <div class="space-y-6 sticky top-6">
                        <!-- Calendar Card -->
                        <div
                            class="bg-white border-2 border-gray-200 rounded-2xl shadow-sm overflow-hidden"
                        >
                            <div class="p-6 border-b border-gray-100">
                                <h3
                                    class="text-lg font-bold text-gray-900 mb-1 flex items-center gap-2"
                                >
                                    <Calendar class="text-blue-600 w-5 h-5" />
                                    Announcement Calendar
                                </h3>
                                <p class="text-sm text-gray-500">
                                    Click on dates to filter announcements
                                </p>
                            </div>

                            <!-- Mini Calendar -->
                            <div class="p-6">
                                <!-- Calendar Header -->
                                <div
                                    class="flex justify-between items-center mb-6"
                                >
                                    <button
                                        @click="prevMonth"
                                        class="w-10 h-10 rounded-xl hover:bg-gray-100 transition-all duration-200 flex items-center justify-center group"
                                    >
                                        <ChevronLeft
                                            class="text-gray-600 group-hover:text-blue-600 w-5 h-5"
                                        />
                                    </button>
                                    <h4
                                        class="text-lg font-bold text-gray-900 px-4 py-2 bg-gray-100 rounded-xl"
                                    >
                                        {{ currentMonthYear }}
                                    </h4>
                                    <button
                                        @click="nextMonth"
                                        class="w-10 h-10 rounded-xl hover:bg-gray-100 transition-all duration-200 flex items-center justify-center group"
                                    >
                                        <ChevronRight
                                            class="text-gray-600 group-hover:text-blue-600 w-5 h-5"
                                        />
                                    </button>
                                </div>

                                <!-- Calendar Grid -->
                                <div class="space-y-3">
                                    <!-- Week Days Header -->
                                    <div class="grid grid-cols-7 gap-1">
                                        <div
                                            v-for="day in daysOfWeek"
                                            :key="day"
                                            class="text-center text-sm font-semibold text-gray-500 py-2"
                                        >
                                            {{ day }}
                                        </div>
                                    </div>

                                    <!-- Calendar Days -->
                                    <div class="grid grid-cols-7 gap-1">
                                        <div
                                            v-for="day in calendarDays"
                                            :key="day.date"
                                            @click="selectDate(day.date)"
                                            :class="getDayClasses(day)"
                                            class="h-12 flex flex-col items-center justify-center rounded-xl text-sm transition-all duration-200 relative group cursor-pointer"
                                        >
                                            <span class="font-medium z-10">{{
                                                day.day
                                            }}</span>
                                            <span
                                                v-if="
                                                    hasAnnouncementOnDate(
                                                        day.date
                                                    )
                                                "
                                                class="absolute top-1 right-1 w-2 h-2 bg-blue-500 rounded-full z-10"
                                            ></span>
                                            <!-- Hover effect -->
                                            <div
                                                class="absolute inset-1 bg-blue-600 rounded-lg opacity-0 group-hover:opacity-10 transition-opacity duration-200"
                                            ></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Today Button -->
                                <button
                                    @click="goToToday"
                                    class="w-full mt-4 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl transition-all duration-200 flex items-center justify-center gap-2 font-medium"
                                >
                                    <CalendarCheck
                                        class="text-gray-600 w-4 h-4"
                                    />
                                    Go to Today
                                </button>
                            </div>

                            <!-- Selected Date Info -->
                            <div
                                v-if="selectedDate"
                                class="border-t border-gray-100 p-4 bg-blue-50"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center"
                                        >
                                            <Filter
                                                class="text-blue-600 w-4 h-4"
                                            />
                                        </div>
                                        <div>
                                            <div
                                                class="text-sm font-medium text-blue-900"
                                            >
                                                Filtering by:
                                            </div>
                                            <div
                                                class="text-lg font-bold text-blue-700"
                                            >
                                                {{
                                                    formatDisplayDate(
                                                        selectedDate
                                                    )
                                                }}
                                            </div>
                                        </div>
                                    </div>
                                    <button
                                        @click="clearDateFilter"
                                        class="w-8 h-8 rounded-lg hover:bg-blue-200 transition-colors flex items-center justify-center"
                                    >
                                        <X class="text-blue-600 w-5 h-5" />
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Upcoming Announcements Card -->
                        <div
                            class="bg-white border-2 border-gray-200 rounded-2xl shadow-sm overflow-hidden"
                        >
                            <div class="p-6 border-b border-gray-100">
                                <h4
                                    class="text-lg font-bold text-gray-900 mb-1 flex items-center gap-2"
                                >
                                    <Clock class="text-green-600 w-5 h-5" />
                                    Upcoming Events
                                </h4>
                                <p class="text-sm text-gray-500">
                                    Next
                                    {{ upcomingAnnouncements.length }} scheduled
                                    announcements
                                </p>
                            </div>
                            <div class="p-1">
                                <div class="space-y-2 max-h-80 overflow-y-auto">
                                    <div
                                        v-for="announcement in upcomingAnnouncements"
                                        :key="announcement.id"
                                        @click="
                                            openAnnouncementModal(announcement)
                                        "
                                        class="p-4 rounded-xl border-2 border-transparent hover:border-blue-300 hover:bg-blue-50 cursor-pointer transition-all duration-200 group"
                                    >
                                        <div class="flex items-start gap-3">
                                            <div
                                                class="w-12 h-12 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0"
                                            >
                                                <Calendar
                                                    class="text-blue-600 w-5 h-5"
                                                />
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div
                                                    class="text-xs font-semibold text-blue-600 mb-1"
                                                >
                                                    {{
                                                        formatDisplayDate(
                                                            announcement.start_date
                                                        )
                                                    }}
                                                </div>
                                                <div
                                                    class="text-sm font-bold text-gray-900 truncate group-hover:text-blue-700 transition-colors"
                                                >
                                                    {{ announcement.title }}
                                                </div>
                                                <div
                                                    class="text-xs text-gray-500 mt-1 flex items-center gap-1"
                                                >
                                                    <User class="w-3 h-3" />
                                                    {{
                                                        announcement.author ||
                                                        "Administrator"
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        v-if="
                                            upcomingAnnouncements.length === 0
                                        "
                                        class="text-center py-8 text-gray-500"
                                    >
                                        <CalendarX
                                            class="text-gray-300 w-8 h-8 mb-2 mx-auto"
                                        />
                                        <div class="text-sm">
                                            No upcoming announcements
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div
                            class="bg-gradient-to-br from-blue-50 to-indigo-100 border-2 border-blue-200 rounded-2xl p-6"
                        >
                            <h4
                                class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2"
                            >
                                <BarChart3 class="text-blue-600 w-5 h-5" />
                                Quick Stats
                            </h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div
                                    class="text-center p-4 bg-white rounded-xl shadow-sm border border-blue-100"
                                >
                                    <div
                                        class="text-2xl font-bold text-blue-600 mb-1"
                                    >
                                        {{ activeAnnouncements.length }}
                                    </div>
                                    <div
                                        class="text-xs text-gray-600 font-medium"
                                    >
                                        Active
                                    </div>
                                </div>
                                <div
                                    class="text-center p-4 bg-white rounded-xl shadow-sm border border-blue-100"
                                >
                                    <div
                                        class="text-2xl font-bold text-purple-600 mb-1"
                                    >
                                        {{ upcomingAnnouncements.length }}
                                    </div>
                                    <div
                                        class="text-xs text-gray-600 font-medium"
                                    >
                                        Upcoming
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Announcement Modal -->
        <div
            v-if="selectedAnnouncement"
            class="fixed inset-0 z-50 overflow-y-auto"
        >
            <!-- Backdrop -->
            <div
                class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
                @click="closeAnnouncementModal"
            ></div>

            <!-- Modal Container -->
            <div class="flex min-h-full items-center justify-center p-4">
                <!-- Modal Content -->
                <div
                    class="relative bg-white rounded-2xl shadow-xl w-full max-w-2xl transform transition-all"
                >
                    <!-- Header -->
                    <div class="border-b border-gray-100 p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center"
                                >
                                    <Megaphone class="text-blue-600 w-5 h-5" />
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900">
                                        Announcement Details
                                    </h2>
                                    <p class="text-gray-500 text-sm">
                                        Complete information
                                    </p>
                                </div>
                            </div>
                            <button
                                @click="closeAnnouncementModal"
                                class="w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center transition-colors"
                            >
                                <X class="text-gray-500 w-5 h-5" />
                            </button>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-6 max-h-[60vh] overflow-y-auto">
                        <!-- Status and Dates -->
                        <div
                            class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100"
                        >
                            <span
                                :class="{
                                    'bg-green-100 text-green-800 border border-green-200':
                                        selectedAnnouncement.status ===
                                        'Active',
                                    'bg-red-100 text-red-800 border border-red-200':
                                        selectedAnnouncement.status ===
                                        'Inactive',
                                    'bg-blue-100 text-blue-800 border border-blue-200':
                                        selectedAnnouncement.status ===
                                        'Scheduled',
                                }"
                                class="px-4 py-2 rounded-full text-sm font-semibold"
                            >
                                {{ selectedAnnouncement.status }}
                            </span>
                            <div class="text-right text-sm text-gray-600">
                                <div class="flex items-center gap-4">
                                    <div class="flex items-center gap-2">
                                        <Calendar
                                            class="text-blue-500 w-4 h-4"
                                        />
                                        <span>{{
                                            formatDisplayDate(
                                                selectedAnnouncement.start_date
                                            )
                                        }}</span>
                                    </div>
                                    <div class="text-gray-300">•</div>
                                    <div class="flex items-center gap-2">
                                        <CalendarCheck
                                            class="text-green-500 w-4 h-4"
                                        />
                                        <span>{{
                                            formatDisplayDate(
                                                selectedAnnouncement.end_date
                                            )
                                        }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Title -->
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">
                            {{ selectedAnnouncement.title }}
                        </h3>

                        <!-- Content -->
                        <div class="prose prose-lg max-w-none">
                            <p
                                class="text-gray-700 leading-relaxed whitespace-pre-line"
                            >
                                {{ selectedAnnouncement.content }}
                            </p>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div
                        class="border-t border-gray-100 px-6 py-4 bg-gray-50 rounded-b-2xl"
                    >
                        <div
                            class="flex items-center justify-between text-sm text-gray-500"
                        >
                            <div class="flex items-center gap-2">
                                <Clock class="w-4 h-4" />
                                <span
                                    >Created:
                                    {{
                                        formatDateTime(
                                            selectedAnnouncement.created_at
                                        )
                                    }}</span
                                >
                            </div>
                            <div class="flex items-center gap-2">
                                <User class="w-4 h-4" />
                                <span
                                    >By:
                                    {{
                                        selectedAnnouncement.author ||
                                        "Administrator"
                                    }}</span
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>

<script setup>
import CustomerLayout from "@/Layouts/CustomerLayout.vue";
import { ref, computed, onMounted, onUnmounted } from "vue";
import {
    Megaphone,
    Filter,
    RefreshCw,
    Eye,
    Calendar,
    CalendarCheck,
    Clock,
    ChevronLeft,
    ChevronRight,
    X,
    User,
    CalendarX,
    BarChart3,
} from "lucide-vue-next";

const props = defineProps({
    announcements: {
        type: Array,
        required: true,
        default: () => [],
    },
});

// Reactive state
const selectedAnnouncement = ref(null);
const selectedDate = ref(null);
const currentMonth = ref(new Date().getMonth());
const currentYear = ref(new Date().getFullYear());

// Constants
const daysOfWeek = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

// Date utilities
const dateUtils = {
    // Get today's date in YYYY-MM-DD format (timezone safe)
    getToday() {
        const now = new Date();
        return new Date(now.getTime() - now.getTimezoneOffset() * 60000)
            .toISOString()
            .split("T")[0];
    },

    // Create date string without timezone issues
    createDateString(year, month, day) {
        const date = new Date(year, month, day);
        return new Date(date.getTime() - date.getTimezoneOffset() * 60000)
            .toISOString()
            .split("T")[0];
    },

    // Check if two dates are the same (ignoring time)
    isSameDate(date1, date2) {
        if (!date1 || !date2) return false;
        return (
            new Date(date1).toDateString() === new Date(date2).toDateString()
        );
    },

    // Format date for display
    formatDisplay(dateString) {
        if (!dateString) return "Not specified";
        try {
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return "Invalid date";
            return date.toLocaleDateString(undefined, {
                year: "numeric",
                month: "short",
                day: "numeric",
            });
        } catch (error) {
            console.error("Error formatting date:", error);
            return "Date error";
        }
    },

    // Format date and time
    formatDateTime(dateString) {
        if (!dateString) return "Not available";
        try {
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return "Invalid date";
            const options = {
                year: "numeric",
                month: "short",
                day: "numeric",
                hour: "2-digit",
                minute: "2-digit",
            };
            return date.toLocaleDateString(undefined, options);
        } catch (error) {
            console.error("Error formatting date time:", error);
            return "Date error";
        }
    },

    // Format relative time
    formatRelative(dateString) {
        if (!dateString) return "Recently";
        try {
            const date = new Date(dateString);
            const now = new Date();
            const diffTime = Math.abs(now - date);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if (diffDays === 1) return "Yesterday";
            if (diffDays < 7) return `${diffDays} days ago`;
            if (diffDays < 30) return `${Math.ceil(diffDays / 7)} weeks ago`;
            return `${Math.ceil(diffDays / 30)} months ago`;
        } catch (error) {
            return "Recently";
        }
    },
};

// Computed properties
const currentMonthYear = computed(() => {
    const date = new Date(currentYear.value, currentMonth.value);
    return date.toLocaleDateString(undefined, {
        month: "long",
        year: "numeric",
    });
});

const calendarDays = computed(() => {
    const days = [];
    const firstDayOfMonth = new Date(currentYear.value, currentMonth.value, 1);
    const lastDayOfMonth = new Date(
        currentYear.value,
        currentMonth.value + 1,
        0
    );

    // Get the starting day of the week (0 = Sunday, 6 = Saturday)
    const startDay = firstDayOfMonth.getDay();

    // Days from previous month
    const prevMonthLastDay = new Date(
        currentYear.value,
        currentMonth.value,
        0
    ).getDate();
    for (let i = startDay - 1; i >= 0; i--) {
        const dayNumber = prevMonthLastDay - i;
        days.push({
            day: dayNumber,
            date: dateUtils.createDateString(
                currentYear.value,
                currentMonth.value - 1,
                dayNumber
            ),
            isCurrentMonth: false,
            isToday: dateUtils.isSameDate(
                dateUtils.createDateString(
                    currentYear.value,
                    currentMonth.value - 1,
                    dayNumber
                ),
                dateUtils.getToday()
            ),
        });
    }

    // Current month days
    for (let i = 1; i <= lastDayOfMonth.getDate(); i++) {
        days.push({
            day: i,
            date: dateUtils.createDateString(
                currentYear.value,
                currentMonth.value,
                i
            ),
            isCurrentMonth: true,
            isToday: dateUtils.isSameDate(
                dateUtils.createDateString(
                    currentYear.value,
                    currentMonth.value,
                    i
                ),
                dateUtils.getToday()
            ),
        });
    }

    // Next month days to complete the grid (6 rows × 7 columns = 42 cells)
    const remainingDays = 42 - days.length;
    for (let i = 1; i <= remainingDays; i++) {
        days.push({
            day: i,
            date: dateUtils.createDateString(
                currentYear.value,
                currentMonth.value + 1,
                i
            ),
            isCurrentMonth: false,
            isToday: dateUtils.isSameDate(
                dateUtils.createDateString(
                    currentYear.value,
                    currentMonth.value + 1,
                    i
                ),
                dateUtils.getToday()
            ),
        });
    }

    return days;
});

const filteredAnnouncements = computed(() => {
    if (!selectedDate.value) {
        return props.announcements;
    }

    return props.announcements.filter((announcement) => {
        const startDate = new Date(announcement.start_date);
        const endDate = new Date(announcement.end_date);
        const selected = new Date(selectedDate.value);

        // Reset times to compare dates only
        startDate.setHours(0, 0, 0, 0);
        endDate.setHours(0, 0, 0, 0);
        selected.setHours(0, 0, 0, 0);

        return selected >= startDate && selected <= endDate;
    });
});

const upcomingAnnouncements = computed(() => {
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    return props.announcements
        .filter((announcement) => {
            const startDate = new Date(announcement.start_date);
            startDate.setHours(0, 0, 0, 0);
            return startDate > today;
        })
        .sort((a, b) => new Date(a.start_date) - new Date(b.start_date))
        .slice(0, 5);
});

const activeAnnouncements = computed(() => {
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    return props.announcements.filter((announcement) => {
        const startDate = new Date(announcement.start_date);
        const endDate = new Date(announcement.end_date);
        startDate.setHours(0, 0, 0, 0);
        endDate.setHours(0, 0, 0, 0);

        return (
            today >= startDate &&
            today <= endDate &&
            announcement.status === "Active"
        );
    });
});

// Methods
const getStatusClasses = (status) => {
    const baseClasses =
        "px-4 py-2 rounded-full text-sm font-semibold border shadow-sm whitespace-nowrap";
    const statusClasses = {
        Active: "bg-green-100 text-green-800 border-green-200",
        Inactive: "bg-red-100 text-red-800 border-red-200",
        Scheduled: "bg-blue-100 text-blue-800 border-blue-200",
    };
    return `${baseClasses} ${
        statusClasses[status] || "bg-gray-100 text-gray-800 border-gray-200"
    }`;
};

const getDayClasses = (day) => {
    const classes = [];

    if (!day.isCurrentMonth) {
        classes.push("text-gray-400");
    } else {
        classes.push("text-gray-700 hover:bg-blue-50");
    }

    if (day.isToday) {
        classes.push("bg-blue-100 text-blue-800 font-bold");
    }

    if (dateUtils.isSameDate(day.date, selectedDate.value)) {
        classes.push("bg-blue-600 text-white font-bold shadow-md");
    }

    if (hasAnnouncementOnDate(day.date) && day.isCurrentMonth) {
        classes.push("relative");
    }

    return classes.join(" ");
};

const formatDisplayDate = (dateString) => {
    return dateUtils.formatDisplay(dateString);
};

const formatDateTime = (dateString) => {
    return dateUtils.formatDateTime(dateString);
};

const formatRelativeTime = (dateString) => {
    return dateUtils.formatRelative(dateString);
};

const isSelectedDate = (date) => {
    return dateUtils.isSameDate(date, selectedDate.value);
};

const isToday = (date) => {
    return dateUtils.isSameDate(date, dateUtils.getToday());
};

const hasAnnouncementOnDate = (date) => {
    return props.announcements.some((announcement) => {
        const startDate = new Date(announcement.start_date);
        const endDate = new Date(announcement.end_date);
        const checkDate = new Date(date);

        // Reset times to compare dates only
        startDate.setHours(0, 0, 0, 0);
        endDate.setHours(0, 0, 0, 0);
        checkDate.setHours(0, 0, 0, 0);

        return checkDate >= startDate && checkDate <= endDate;
    });
};

const selectDate = (date) => {
    selectedDate.value = date;
};

const clearDateFilter = () => {
    selectedDate.value = null;
};

const prevMonth = () => {
    if (currentMonth.value === 0) {
        currentMonth.value = 11;
        currentYear.value--;
    } else {
        currentMonth.value--;
    }
};

const nextMonth = () => {
    if (currentMonth.value === 11) {
        currentMonth.value = 0;
        currentYear.value++;
    } else {
        currentMonth.value++;
    }
};

const goToToday = () => {
    const today = new Date();
    currentMonth.value = today.getMonth();
    currentYear.value = today.getFullYear();
    selectedDate.value = dateUtils.getToday();
};

// Modal functions
const openAnnouncementModal = (announcement) => {
    selectedAnnouncement.value = announcement;
    document.body.style.overflow = "hidden";
};

const closeAnnouncementModal = () => {
    selectedAnnouncement.value = null;
    document.body.style.overflow = "auto";
};

// Keyboard event handler
const handleEscapeKey = (event) => {
    if (event.key === "Escape" && selectedAnnouncement.value) {
        closeAnnouncementModal();
    }
};

// Lifecycle
onMounted(() => {
    window.addEventListener("keydown", handleEscapeKey);
});

onUnmounted(() => {
    window.removeEventListener("keydown", handleEscapeKey);
});
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.transition-all {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.hover-lift:hover {
    transform: translateY(-4px);
}

/* Custom scrollbar for upcoming events */
.scrollbar-thin::-webkit-scrollbar {
    width: 6px;
}

.scrollbar-thin::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

.scrollbar-thin::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.scrollbar-thin::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
