<!-- AnnouncementCalendar.vue - Fixed Version -->
<template>
    <div
        class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-xl p-6"
    >
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                Announcements Calendar
            </h3>
            <div class="flex space-x-2">
                <button
                    @click="prevMonth"
                    class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded"
                >
                    <ChevronLeft class="w-4 h-4" />
                </button>
                <button
                    @click="nextMonth"
                    class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded"
                >
                    <ChevronRight class="w-4 h-4" />
                </button>
            </div>
        </div>

        <!-- Month/Year Header -->
        <div class="text-center mb-4">
            <h4 class="text-md font-medium text-gray-900 dark:text-white">
                {{ currentMonthYear }}
            </h4>
        </div>

        <!-- Calendar Grid -->
        <div class="grid grid-cols-7 gap-1 mb-2">
            <div
                v-for="day in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']"
                :key="day"
                class="text-center text-xs font-medium text-gray-500 dark:text-gray-400 py-1"
            >
                {{ day }}
            </div>
        </div>

        <div class="grid grid-cols-7 gap-1">
            <div
                v-for="day in calendarDays"
                :key="day.date"
                :class="[
                    'p-2 text-xs border rounded min-h-12 cursor-pointer transition-colors relative',
                    day.isCurrentMonth
                        ? 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700'
                        : 'bg-gray-50 dark:bg-gray-700 border-gray-100 dark:border-gray-600 text-gray-400',
                    day.hasStartingAnnouncements
                        ? 'ring-1 ring-blue-500 dark:ring-blue-400 shadow-sm'
                        : day.hasActiveAnnouncements
                        ? 'ring-1 ring-blue-200 dark:ring-blue-600'
                        : '',
                    isToday(day.date)
                        ? 'border-2 border-blue-500 dark:border-blue-400 bg-blue-50 dark:bg-blue-900/20'
                        : '',
                ]"
                @click="selectDate(day.date)"
            >
                <div class="flex justify-between items-start">
                    <span
                        :class="[
                            isToday(day.date)
                                ? 'font-bold text-blue-700 dark:text-blue-300'
                                : day.isCurrentMonth
                                ? 'text-gray-900 dark:text-white'
                                : 'text-gray-400',
                            day.hasStartingAnnouncements ? 'font-semibold' : '',
                        ]"
                    >
                        {{ day.day }}
                    </span>
                    <!-- Announcement Count Badge -->
                    <span
                        v-if="day.startingAnnouncementCount > 0"
                        class="bg-blue-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center font-medium"
                        :title="`${day.startingAnnouncementCount} announcement(s) starting today`"
                    >
                        {{ day.startingAnnouncementCount }}
                    </span>
                </div>

                <!-- Active Announcement Indicator (dot) -->
                <div
                    v-if="
                        day.hasActiveAnnouncements &&
                        !day.hasStartingAnnouncements
                    "
                    class="absolute bottom-1 left-1/2 transform -translate-x-1/2"
                >
                    <div class="w-1.5 h-1.5 bg-blue-400 rounded-full"></div>
                </div>

                <!-- Starting Announcement Indicator (dot) -->
                <div
                    v-if="day.hasStartingAnnouncements"
                    class="absolute bottom-1 left-1/2 transform -translate-x-1/2"
                >
                    <div class="w-1.5 h-1.5 bg-blue-600 rounded-full"></div>
                </div>
            </div>
        </div>

        <!-- Selected Date Announcements -->
        <div v-if="selectedDateAnnouncements.length > 0" class="mt-6">
            <h5 class="text-sm font-medium text-gray-900 dark:text-white mb-3">
                Announcements for
                {{ formatSelectedDate(selectedDate) }}
            </h5>
            <div class="space-y-2 max-h-60 overflow-y-auto">
                <div
                    v-for="announcement in selectedDateAnnouncements"
                    :key="announcement.id"
                    class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600"
                >
                    <div class="flex justify-between items-start mb-1">
                        <h6
                            class="text-sm font-medium text-gray-900 dark:text-white"
                        >
                            {{ announcement.title }}
                        </h6>
                        <div class="flex items-center space-x-2">

                            <span
                                :class="statusClasses(announcement.status)"
                                class="text-xs px-2 py-0.5 rounded-full"
                            >
                                {{ announcement.status }}
                            </span>
                        </div>
                    </div>
                    <p
                        class="text-xs text-gray-600 dark:text-gray-300 mb-2 line-clamp-2"
                    >
                        {{ announcement.content }}
                    </p>
                    <div
                        class="flex justify-between text-xs text-gray-500 dark:text-gray-400"
                    >
                        <span
                            >Start:
                            {{ formatDate(announcement.start_date) }}</span
                        >
                        <span
                            >End: {{ formatDate(announcement.end_date) }}</span
                        >
                    </div>
                </div>
            </div>
        </div>

        <!-- No announcements for selected date -->
        <div v-else-if="selectedDate" class="mt-6 text-center py-4">
            <Calendar class="w-8 h-8 text-gray-300 mx-auto mb-2" />
            <p class="text-sm text-gray-500 dark:text-gray-400">
                No announcements for this date
            </p>
        </div>

        <!-- Calendar Legend -->
        <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-600">
            <div class="flex items-center justify-between space-y-2 text-xs">
                <div class="flex items-center space-x-4">

                    <div class="flex items-center space-x-1">
                        <div class="w-3 h-3 bg-blue-300 rounded-full"></div>
                        <span class="text-gray-600 dark:text-gray-400"
                            >Active period</span
                        >
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-1">
                        <div
                            class="w-3 h-3 border-2 border-blue-500 rounded"
                        ></div>
                        <span class="text-gray-600 dark:text-gray-400"
                            >Today</span
                        >
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { ChevronLeft, ChevronRight, Calendar } from "lucide-vue-next";

const props = defineProps({
    announcements: {
        type: Array,
        required: true,
    },
});

// Calendar state
const currentDate = ref(new Date());
const selectedDate = ref(null);

// Helper function to create date without timezone issues
const createDate = (dateString) => {
    if (!dateString) return null;

    // Handle both YYYY-MM-DD format and other date formats
    const date = new Date(dateString);

    // If the date is invalid, return null
    if (isNaN(date.getTime())) return null;

    // Create a date without time component
    return new Date(date.getFullYear(), date.getMonth(), date.getDate());
};

// Helper function to compare dates (date-only comparison)
const isSameDate = (date1, date2) => {
    if (!date1 || !date2) return false;
    return (
        date1.getFullYear() === date2.getFullYear() &&
        date1.getMonth() === date2.getMonth() &&
        date1.getDate() === date2.getDate()
    );
};

// Helper function to check if date is between range (inclusive)
const isDateInRange = (date, startDate, endDate) => {
    if (!date || !startDate || !endDate) return false;
    return date >= startDate && date <= endDate;
};

// Get today's date
const today = createDate(new Date());

// Calendar computed properties
const currentMonthYear = computed(() => {
    return currentDate.value.toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
    });
});

const calendarDays = computed(() => {
    const year = currentDate.value.getFullYear();
    const month = currentDate.value.getMonth();

    // First day of the month
    const firstDay = new Date(year, month, 1);
    // Last day of the month
    const lastDay = new Date(year, month + 1, 0);

    // Start from the first Sunday of the week that contains the first day
    const startDate = new Date(firstDay);
    startDate.setDate(startDate.getDate() - startDate.getDay());

    // End at the last Saturday of the week that contains the last day
    const endDate = new Date(lastDay);
    endDate.setDate(endDate.getDate() + (6 - endDate.getDay()));

    const days = [];
    const current = new Date(startDate);

    while (current <= endDate) {
        const currentDateObj = createDate(current);
        const dateStr = formatDateForDisplay(current);

        // Debug logging for October 31, 2025
        if (
            current.getDate() === 31 &&
            current.getMonth() === 9 &&
            current.getFullYear() === 2025
        ) {
            console.log("October 31, 2025 processing:", {
                current: currentDateObj,
                announcements: props.announcements,
            });
        }

        // Find announcements that start on this date
        const startingAnnouncements = props.announcements.filter(
            (announcement) => {
                if (!announcement.start_date) return false;
                const startDateObj = createDate(announcement.start_date);
                if (!startDateObj) return false;

                return isSameDate(currentDateObj, startDateObj);
            }
        );

        // Find announcements that are active on this date (including ongoing ones)
        const activeAnnouncements = props.announcements.filter(
            (announcement) => {
                if (!announcement.start_date || !announcement.end_date)
                    return false;

                const startDateObj = createDate(announcement.start_date);
                const endDateObj = createDate(announcement.end_date);

                if (!startDateObj || !endDateObj) return false;

                return isDateInRange(currentDateObj, startDateObj, endDateObj);
            }
        );

        days.push({
            date: dateStr,
            day: current.getDate(),
            isCurrentMonth: current.getMonth() === month,
            hasStartingAnnouncements: startingAnnouncements.length > 0,
            hasActiveAnnouncements: activeAnnouncements.length > 0,
            startingAnnouncementCount: startingAnnouncements.length,
            activeAnnouncementCount: activeAnnouncements.length,
            dateObject: currentDateObj,
            // Debug info
            debug: {
                date: currentDateObj,
                startingCount: startingAnnouncements.length,
                activeCount: activeAnnouncements.length,
                startingTitles: startingAnnouncements.map((a) => a.title),
                activeTitles: activeAnnouncements.map((a) => a.title),
            },
        });

        current.setDate(current.getDate() + 1);
    }

    return days;
});

const selectedDateAnnouncements = computed(() => {
    if (!selectedDate.value) return [];

    const selectedDateObj = createDate(selectedDate.value);

    return props.announcements.filter((announcement) => {
        if (!announcement.start_date || !announcement.end_date) return false;

        const startDateObj = createDate(announcement.start_date);
        const endDateObj = createDate(announcement.end_date);

        if (!startDateObj || !endDateObj) return false;

        return isDateInRange(selectedDateObj, startDateObj, endDateObj);
    });
});

// Calendar methods
const prevMonth = () => {
    currentDate.value = new Date(
        currentDate.value.getFullYear(),
        currentDate.value.getMonth() - 1,
        1
    );
};

const nextMonth = () => {
    currentDate.value = new Date(
        currentDate.value.getFullYear(),
        currentDate.value.getMonth() + 1,
        1
    );
};

const selectDate = (date) => {
    selectedDate.value = date;
};

const isToday = (dateString) => {
    const dateToCheck = createDate(dateString);
    return isSameDate(dateToCheck, today);
};

const isStartDate = (announcement, selectedDateStr) => {
    const selectedDateObj = createDate(selectedDateStr);
    const startDateObj = createDate(announcement.start_date);
    return isSameDate(selectedDateObj, startDateObj);
};

const formatSelectedDate = (dateString) => {
    return new Date(dateString).toLocaleDateString("en-US", {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};

const statusClasses = (status) => {
    return {
        "bg-green-100 border border-green-200 text-green-800 dark:bg-green-900 dark:text-green-200":
            status.toLowerCase() === "active",
        "bg-yellow-100 border border-yellow-200 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200":
            status.toLowerCase() === "inactive",
    };
};

const formatDate = (dateString) => {
    if (!dateString) return "";
    return new Date(dateString).toLocaleDateString(undefined, {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
};

// Helper to format date for display (YYYY-MM-DD)
const formatDateForDisplay = (date) => {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const day = String(date.getDate()).padStart(2, "0");
    return `${year}-${month}-${day}`;
};

// Initialize selected date to today
onMounted(() => {
    selectedDate.value = formatDateForDisplay(new Date());

    // Debug: Log all announcements to check dates
    console.log("All announcements:", props.announcements);
    console.log("Today:", today);
});
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
