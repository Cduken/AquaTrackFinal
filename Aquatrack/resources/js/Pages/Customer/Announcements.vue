<template>
    <CustomerLayout>
        <!-- Main Content -->
        <div class="flex flex-col">
            <!-- Header with Filters -->
            <div
                class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-0 p-2"
            >
                <!-- Left side - Title and description -->
                <div class="flex-1">
                    <h1 class="text-xl font-semibold text-gray-900">
                        Announcements
                    </h1>
                    <p class="text-gray-600 text-sm">
                        Stay updated with the latest news and updates
                    </p>
                </div>

                <!-- Right side - Filters and Reset -->
                <div
                    class="flex flex-col sm:flex-row items-start sm:items-center gap-2"
                >
                    <!-- Status Filter -->
                    <select
                        v-model="filters.status"
                        class="px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-[110px]"
                    >
                        <option value="">All Status</option>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>

                    <!-- Date Filter -->
                    <select
                        v-model="filters.dateRange"
                        class="px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-[110px]"
                    >
                        <option value="">All Dates</option>
                        <option value="today">Today</option>
                        <option value="upcoming">Upcoming</option>
                        <option value="past">Past</option>
                    </select>

                    <!-- Reset Filters Button -->
                    <button
                        @click="resetAllFilters"
                        class="flex items-center px-3 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                        :disabled="!hasActiveFilters"
                    >
                        <RefreshCw class="w-4 h-4 mr-1" />
                    </button>
                </div>
            </div>

            <!-- Content Sections -->
            <div class="flex flex-col xl:flex-row p-2 gap-4">
                <!-- Announcements Table Section -->
                <div class="xl:w-2/3">
                    <!-- Announcements Table with Fixed Height -->
                    <div
                        class="bg-white border border-gray-200 rounded-lg overflow-hidden flex flex-col"
                        style="height: 705px"
                    >
                        <!-- Single Table Container for perfect alignment -->
                        <div class="flex flex-col h-full">
                            <!-- Fixed Header -->
                            <div
                                class="flex-shrink-0 bg-gray-50 border-b border-gray-200"
                            >
                                <table class="w-full">
                                    <thead>
                                        <tr>
                                            <th
                                                class="w-24 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200"
                                            >
                                                Status
                                            </th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200"
                                            >
                                                Title
                                            </th>
                                            <th
                                                class="w-36 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200"
                                            >
                                                Start Date
                                            </th>
                                            <th
                                                class="w-36 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200"
                                            >
                                                End Date
                                            </th>
                                            <th
                                                class="w-32 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                            >
                                                Updated
                                            </th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>

                            <!-- Scrollable Body -->
                            <div class="flex-1 overflow-auto">
                                <table class="w-full">
                                    <colgroup>
                                        <col class="w-24" />
                                        <!-- Status -->
                                        <col />
                                        <!-- Title (flexible) -->
                                        <col class="w-36" />
                                        <!-- Start Date -->
                                        <col class="w-36" />
                                        <!-- End Date -->
                                        <col class="w-32" />
                                        <!-- Updated -->
                                    </colgroup>
                                    <tbody
                                        class="bg-white divide-y divide-gray-200"
                                    >
                                        <tr
                                            v-for="announcement in paginatedAnnouncements"
                                            :key="announcement.id"
                                            @click="
                                                openAnnouncementModal(
                                                    announcement
                                                )
                                            "
                                            class="hover:bg-blue-50 cursor-pointer transition-colors group"
                                        >
                                            <!-- Status -->
                                            <td
                                                class="w-24 px-4 py-3 whitespace-nowrap border-r border-gray-200 align-top"
                                            >
                                                <span
                                                    :class="
                                                        getStatusClasses(
                                                            announcement.status
                                                        )
                                                    "
                                                    class="inline-block px-2 py-1 rounded text-xs font-medium"
                                                >
                                                    {{ announcement.status }}
                                                </span>
                                            </td>

                                            <!-- Title with two lines -->
                                            <td
                                                class="px-4 py-3 border-r border-gray-200 align-top"
                                            >
                                                <div
                                                    class="flex flex-col min-w-0"
                                                >
                                                    <div
                                                        class="text-sm font-medium text-gray-900 group-hover:text-blue-600 line-clamp-1 break-words"
                                                    >
                                                        {{ announcement.title }}
                                                    </div>
                                                    <div
                                                        class="text-xs text-gray-500 line-clamp-1 mt-1 break-words"
                                                    >
                                                        {{
                                                            announcement.content
                                                        }}
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Start Date -->
                                            <td
                                                class="w-36 px-4 py-3 whitespace-nowrap text-sm text-gray-600 border-r border-gray-200 align-top"
                                            >
                                                {{
                                                    formatDisplayDate(
                                                        announcement.start_date
                                                    )
                                                }}
                                            </td>

                                            <!-- End Date -->
                                            <td
                                                class="w-36 px-4 py-3 whitespace-nowrap text-sm text-gray-600 border-r border-gray-200 align-top"
                                            >
                                                {{
                                                    formatDisplayDate(
                                                        announcement.end_date
                                                    )
                                                }}
                                            </td>

                                            <!-- Updated -->
                                            <td
                                                class="w-32 px-4 py-3 whitespace-nowrap text-xs text-gray-500 align-top"
                                            >
                                                {{
                                                    formatRelativeTime(
                                                        announcement.updated_at
                                                    )
                                                }}
                                            </td>
                                        </tr>

                                        <!-- Empty State -->
                                        <tr
                                            v-if="
                                                filteredAnnouncements.length ===
                                                0
                                            "
                                        >
                                            <td
                                                colspan="5"
                                                class="px-6 py-8 text-center"
                                            >
                                                <div class="max-w-md mx-auto">
                                                    <div
                                                        class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center mx-auto mb-3"
                                                    >
                                                        <Megaphone
                                                            class="text-gray-500 w-6 h-6"
                                                        />
                                                    </div>
                                                    <h3
                                                        class="text-lg font-semibold text-gray-700 mb-2"
                                                    >
                                                        No announcements found
                                                    </h3>
                                                    <p
                                                        class="text-gray-500 text-sm"
                                                    >
                                                        <span
                                                            v-if="
                                                                hasActiveFilters
                                                            "
                                                        >
                                                            No announcements
                                                            match your current
                                                            filters.
                                                        </span>
                                                        <span v-else>
                                                            No announcements
                                                            available at the
                                                            moment.
                                                        </span>
                                                    </p>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div
                                v-if="filteredAnnouncements.length > 0"
                                class="flex-shrink-0 border-t border-gray-200 bg-white"
                            >
                                <Pagination :data="paginationData" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Calendar & Sidebar Section -->
                <div class="xl:w-1/3">
                    <div class="space-y-4">
                        <!-- Calendar Card -->
                        <div
                            class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden"
                        >
                            <div class="p-4 border-b border-gray-100">
                                <h3
                                    class="text-base font-semibold text-gray-900 mb-1 flex items-center gap-2"
                                >
                                    <Calendar class="text-blue-600 w-4 h-4" />
                                    Calendar
                                </h3>
                                <p class="text-xs text-gray-500">
                                    Click dates to filter announcements
                                </p>
                            </div>

                            <!-- Mini Calendar -->
                            <div class="p-4">
                                <!-- Calendar Header -->
                                <div
                                    class="flex justify-between items-center mb-4"
                                >
                                    <button
                                        @click="prevMonth"
                                        class="w-8 h-8 rounded-lg hover:bg-gray-100 transition-colors flex items-center justify-center"
                                    >
                                        <ChevronLeft
                                            class="text-gray-600 w-4 h-4"
                                        />
                                    </button>
                                    <h4
                                        class="text-sm font-semibold text-gray-900 px-3 py-1 bg-gray-100 rounded"
                                    >
                                        {{ currentMonthYear }}
                                    </h4>
                                    <button
                                        @click="nextMonth"
                                        class="w-8 h-8 rounded-lg hover:bg-gray-100 transition-colors flex items-center justify-center"
                                    >
                                        <ChevronRight
                                            class="text-gray-600 w-4 h-4"
                                        />
                                    </button>
                                </div>

                                <!-- Calendar Grid -->
                                <div class="space-y-2">
                                    <!-- Week Days Header -->
                                    <div class="grid grid-cols-7 gap-1">
                                        <div
                                            v-for="day in daysOfWeek"
                                            :key="day"
                                            class="text-center text-xs font-medium text-gray-500 py-1"
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
                                            class="h-8 flex items-center justify-center rounded text-xs transition-colors relative group cursor-pointer"
                                        >
                                            <span class="relative z-10">{{
                                                day.day
                                            }}</span>
                                            <span
                                                v-if="
                                                    hasAnnouncementOnDate(
                                                        day.date
                                                    ) && day.isCurrentMonth
                                                "
                                                class="absolute bottom-1 w-1 h-1 bg-blue-500 rounded-full"
                                            ></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Today and Reset Buttons -->
                                <div class="flex gap-2 mt-3">
                                    <button
                                        @click="goToToday"
                                        class="flex-1 px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-sm transition-colors flex items-center justify-center gap-2"
                                    >
                                        <CalendarCheck
                                            class="text-gray-600 w-4 h-4"
                                        />
                                        Today
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Upcoming Announcements -->
                        <div
                            class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden"
                        >
                            <div class="p-4 border-b border-gray-100">
                                <h4
                                    class="text-base font-semibold text-gray-900 mb-1 flex items-center gap-2"
                                >
                                    <Clock class="text-green-600 w-4 h-4" />
                                    Upcoming
                                </h4>
                                <p class="text-xs text-gray-500">
                                    Next
                                    {{ upcomingAnnouncements.length }} events
                                </p>
                            </div>

                            <!-- Fixed height container -->
                            <div class="h-40 overflow-y-auto">
                                <!-- h-52 = 13rem (~208px) -->
                                <div class="space-y-2 p-2">
                                    <div
                                        v-for="announcement in upcomingAnnouncements"
                                        :key="announcement.id"
                                        @click="
                                            openAnnouncementModal(announcement)
                                        "
                                        class="p-3 rounded border border-transparent hover:border-blue-300 hover:bg-blue-50 cursor-pointer transition-colors"
                                    >
                                        <div class="flex items-start gap-2">
                                            <div
                                                class="w-8 h-8 bg-blue-100 rounded flex items-center justify-center flex-shrink-0"
                                            >
                                                <Calendar
                                                    class="text-blue-600 w-3 h-3"
                                                />
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div
                                                    class="text-xs font-medium text-blue-600 mb-1"
                                                >
                                                    {{
                                                        formatDisplayDate(
                                                            announcement.start_date
                                                        )
                                                    }}
                                                </div>
                                                <div
                                                    class="text-sm font-medium text-gray-900 line-clamp-1"
                                                >
                                                    {{ announcement.title }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        v-if="
                                            upcomingAnnouncements.length === 0
                                        "
                                        class="text-center py-4 text-gray-500 text-sm"
                                    >
                                        <CalendarX
                                            class="text-gray-300 w-6 h-6 mb-1 mx-auto"
                                        />
                                        No upcoming announcements
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
            <div
                class="fixed inset-0 bg-black bg-opacity-50"
                @click="closeAnnouncementModal"
            ></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div
                    class="relative bg-white rounded-lg shadow-lg w-full max-w-md"
                >
                    <!-- Header -->
                    <div class="border-b border-gray-200 p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div
                                    class="w-8 h-8 bg-blue-100 rounded flex items-center justify-center"
                                >
                                    <Megaphone class="text-blue-600 w-4 h-4" />
                                </div>
                                <div>
                                    <h2
                                        class="text-lg font-semibold text-gray-900"
                                    >
                                        Announcement Details
                                    </h2>
                                </div>
                            </div>
                            <button
                                @click="closeAnnouncementModal"
                                class="w-6 h-6 rounded hover:bg-gray-100 flex items-center justify-center"
                            >
                                <X class="text-gray-500 w-4 h-4" />
                            </button>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-4 max-h-96 overflow-y-auto">
                        <!-- Status and Dates -->
                        <div
                            class="flex items-center justify-between mb-4 pb-3 border-b border-gray-200"
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
                                class="px-3 py-1 rounded text-xs font-medium"
                            >
                                {{ selectedAnnouncement.status }}
                            </span>
                            <div class="text-right text-xs text-gray-600">
                                <div class="flex items-center gap-2">
                                    <Calendar class="text-blue-500 w-3 h-3" />
                                    <span>{{
                                        formatDisplayDate(
                                            selectedAnnouncement.start_date
                                        )
                                    }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Title -->
                        <h3 class="text-lg font-bold text-gray-900 mb-3">
                            {{ selectedAnnouncement.title }}
                        </h3>

                        <!-- Content -->
                        <div
                            class="text-sm text-gray-700 leading-relaxed whitespace-pre-line"
                        >
                            {{ selectedAnnouncement.content }}
                        </div>
                    </div>

                    <!-- Footer -->
                    <div
                        class="border-t border-gray-200 px-4 py-3 bg-gray-50 rounded-b-lg"
                    >
                        <div
                            class="flex items-center justify-between text-xs text-gray-500"
                        >
                            <div class="flex items-center gap-1">
                                <Clock class="w-3 h-3" />
                                <span
                                    >Created:
                                    {{
                                        formatDateTime(
                                            selectedAnnouncement.created_at
                                        )
                                    }}</span
                                >
                            </div>
                            <div class="flex items-center gap-1">
                                <User class="w-3 h-3" />
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
import Pagination from "@/Components/Pagination.vue";
import { ref, computed, onMounted, onUnmounted, watch } from "vue";
import {
    Megaphone,
    RefreshCw,
    Calendar,
    CalendarCheck,
    Clock,
    ChevronLeft,
    ChevronRight,
    X,
    User,
    CalendarX,
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

// Filters
const filters = ref({
    status: "",
    dateRange: "",
});

// Pagination
const currentPage = ref(1);
const itemsPerPage = 10;

// Computed properties
const hasActiveFilters = computed(() => {
    return (
        filters.value.status || filters.value.dateRange || selectedDate.value
    );
});

const filteredAnnouncements = computed(() => {
    let filtered = [...props.announcements];

    // Apply status filter
    if (filters.value.status) {
        filtered = filtered.filter(
            (announcement) => announcement.status === filters.value.status
        );
    }

    // Apply date range filter
    if (filters.value.dateRange) {
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        filtered = filtered.filter((announcement) => {
            const startDate = new Date(announcement.start_date);
            const endDate = new Date(announcement.end_date);
            startDate.setHours(0, 0, 0, 0);
            endDate.setHours(0, 0, 0, 0);

            switch (filters.value.dateRange) {
                case "today":
                    return today >= startDate && today <= endDate;
                case "upcoming":
                    return startDate > today;
                case "past":
                    return endDate < today;
                default:
                    return true;
            }
        });
    }

    // Apply selected date filter
    if (selectedDate.value) {
        filtered = filtered.filter((announcement) => {
            const startDate = new Date(announcement.start_date);
            const endDate = new Date(announcement.end_date);
            const selected = new Date(selectedDate.value);

            startDate.setHours(0, 0, 0, 0);
            endDate.setHours(0, 0, 0, 0);
            selected.setHours(0, 0, 0, 0);

            return selected >= startDate && selected <= endDate;
        });
    }

    return filtered;
});

// Other computed properties (pagination, calendar, etc.) remain the same...
const totalPages = computed(() => {
    return Math.ceil(filteredAnnouncements.value.length / itemsPerPage);
});

const startIndex = computed(() => {
    return (currentPage.value - 1) * itemsPerPage;
});

const endIndex = computed(() => {
    return Math.min(
        startIndex.value + itemsPerPage,
        filteredAnnouncements.value.length
    );
});

const paginatedAnnouncements = computed(() => {
    return filteredAnnouncements.value.slice(startIndex.value, endIndex.value);
});

const paginationData = computed(() => {
    return {
        data: paginatedAnnouncements.value,
        current_page: currentPage.value,
        last_page: totalPages.value,
        per_page: itemsPerPage,
        from: startIndex.value + 1,
        to: endIndex.value,
        total: filteredAnnouncements.value.length,
        links: paginationLinks.value,
    };
});

const paginationLinks = computed(() => {
    const links = [];
    links.push({
        url: currentPage.value > 1 ? `?page=${currentPage.value - 1}` : null,
        label: "Previous",
        active: false,
    });

    for (let i = 1; i <= totalPages.value; i++) {
        links.push({
            url: `?page=${i}`,
            label: i.toString(),
            active: i === currentPage.value,
        });
    }

    links.push({
        url:
            currentPage.value < totalPages.value
                ? `?page=${currentPage.value + 1}`
                : null,
        label: "Next",
        active: false,
    });

    return links;
});

const daysOfWeek = ["S", "M", "T", "W", "T", "F", "S"];

const dateUtils = {
    getToday() {
        const now = new Date();
        return new Date(now.getTime() - now.getTimezoneOffset() * 60000)
            .toISOString()
            .split("T")[0];
    },

    createDateString(year, month, day) {
        const date = new Date(year, month, day);
        return new Date(date.getTime() - date.getTimezoneOffset() * 60000)
            .toISOString()
            .split("T")[0];
    },

    isSameDate(date1, date2) {
        if (!date1 || !date2) return false;
        return (
            new Date(date1).toDateString() === new Date(date2).toDateString()
        );
    },

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
};

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

    const startDay = firstDayOfMonth.getDay();
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

// Methods
const resetAllFilters = () => {
    filters.value = {
        status: "",
        dateRange: "",
    };
    selectedDate.value = null;
    currentPage.value = 1;
};

const clearDateFilter = () => {
    selectedDate.value = null;
    currentPage.value = 1;
};

const getStatusClasses = (status) => {
    const statusClasses = {
        Active: "bg-green-100 text-green-800 border border-green-200",
        Inactive: "bg-red-100 text-red-800 border border-red-200",
        Scheduled: "bg-blue-100 text-blue-800 border border-blue-200",
    };
    return (
        statusClasses[status] ||
        "bg-gray-100 text-gray-800 border border-gray-200"
    );
};

const getDayClasses = (day) => {
    const classes = [];

    if (!day.isCurrentMonth) {
        classes.push("text-gray-400");
    } else {
        classes.push("text-gray-700 hover:bg-blue-50");
    }

    if (day.isToday) {
        classes.push("bg-blue-100 text-blue-800 font-semibold");
    }

    if (dateUtils.isSameDate(day.date, selectedDate.value)) {
        classes.push("bg-blue-600 text-white font-semibold");
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
    if (!dateString) return "Recently";
    try {
        const date = new Date(dateString);
        const now = new Date();
        const diffTime = now - date;
        const diffSeconds = Math.floor(diffTime / 1000);
        const diffMinutes = Math.floor(diffSeconds / 60);
        const diffHours = Math.floor(diffMinutes / 60);
        const diffDays = Math.floor(diffHours / 24);

        if (diffSeconds < 60) {
            return "Just now";
        } else if (diffMinutes < 60) {
            return `${diffMinutes} minute${diffMinutes > 1 ? "s" : ""} ago`;
        } else if (diffHours < 24) {
            return `${diffHours} hour${diffHours > 1 ? "s" : ""} ago`;
        } else if (diffDays === 1) {
            return "Yesterday";
        } else if (diffDays < 7) {
            return `${diffDays} days ago`;
        } else if (diffDays < 30) {
            const weeks = Math.floor(diffDays / 7);
            return `${weeks} week${weeks > 1 ? "s" : ""} ago`;
        } else {
            const months = Math.floor(diffDays / 30);
            return `${months} month${months > 1 ? "s" : ""} ago`;
        }
    } catch (error) {
        console.error("Error formatting relative time:", error);
        return "Recently";
    }
};

const hasAnnouncementOnDate = (date) => {
    return props.announcements.some((announcement) => {
        const startDate = new Date(announcement.start_date);
        const endDate = new Date(announcement.end_date);
        const checkDate = new Date(date);

        startDate.setHours(0, 0, 0, 0);
        endDate.setHours(0, 0, 0, 0);
        checkDate.setHours(0, 0, 0, 0);

        return checkDate >= startDate && checkDate <= endDate;
    });
};

const selectDate = (date) => {
    selectedDate.value = date;
    currentPage.value = 1;
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
    currentPage.value = 1;
};

const openAnnouncementModal = (announcement) => {
    selectedAnnouncement.value = announcement;
    document.body.style.overflow = "hidden";
};

const closeAnnouncementModal = () => {
    selectedAnnouncement.value = null;
    document.body.style.overflow = "auto";
};

const handleEscapeKey = (event) => {
    if (event.key === "Escape" && selectedAnnouncement.value) {
        closeAnnouncementModal();
    }
};

onMounted(() => {
    window.addEventListener("keydown", handleEscapeKey);
});

onUnmounted(() => {
    window.removeEventListener("keydown", handleEscapeKey);
});
</script>

<style scoped>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.max-h-96 {
    max-height: 24rem;
}

.max-h-48::-webkit-scrollbar,
.max-h-96::-webkit-scrollbar {
    width: 4px;
}

.max-h-48::-webkit-scrollbar-track,
.max-h-96::-webkit-scrollbar-track {
    background: #f1f5f9;
}

.max-h-48::-webkit-scrollbar-thumb,
.max-h-96::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 2px;
}

.max-h-48::-webkit-scrollbar-thumb:hover,
.max-h-96::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

table {
    border-collapse: collapse;
}

th,
td {
    border-color: #e5e7eb;
}
</style>
