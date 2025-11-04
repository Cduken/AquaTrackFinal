<template>
    <!-- Single transition wrapper for both overlay and panel -->
    <transition name="modal">
        <div v-if="show" class="fixed inset-0 z-[1000] overflow-hidden">
            <!-- Overlay -->
            <div
                class="absolute inset-0 bg-black/50 transition-opacity duration-300"
                @click="emit('close')"
            ></div>

            <!-- Sliding panel container -->
            <div
                class="fixed inset-y-0 right-0 w-full max-w-2xl flex"
                :class="{ 'max-w-full': isMaximized }"
            >
                <!-- Panel with transform class for animation -->
                <div
                    class="relative w-full h-full transform transition-transform duration-300 ease-in-out"
                >
                    <div
                        class="h-full flex flex-col bg-white dark:bg-gray-900 shadow-2xl"
                    >
                        <!-- Enhanced Header -->
                        <div
                            class="flex items-center justify-between px-6 py-5 bg-gradient-to-r from-[#062F64] via-[#1E4272] to-[#2A4A7A] relative overflow-hidden"
                        >
                            <!-- Background Pattern -->
                            <div class="absolute inset-0 opacity-10">
                                <div
                                    class="absolute inset-0 bg-[radial-gradient(circle_at_1px_1px,rgba(255,255,255,0.15)_1px,transparent_0)] bg-[length:20px_20px]"
                                ></div>
                            </div>

                            <div
                                class="flex items-center space-x-3 relative z-10"
                            >
                                <div
                                    class="w-12 h-12 rounded-xl bg-white/10 backdrop-blur-sm flex items-center justify-center border border-white/20"
                                >
                                    <v-icon
                                        name="bi-droplet-half"
                                        class="text-amber-300"
                                        scale="1.6"
                                    />
                                </div>
                                <div>
                                    <span
                                        class="text-white font-semibold text-xl tracking-tight"
                                        >Usage Details</span
                                    >
                                    <p class="text-blue-100 text-sm mt-0.5">
                                        Water consumption overview
                                    </p>
                                </div>
                            </div>
                            <div
                                class="flex items-center space-x-2 relative z-10"
                            >
                                <!-- Maximize/Minimize Button -->
                                <button
                                    @click="toggleMaximize"
                                    class="text-white hover:text-amber-200 transition-all duration-200 p-2 rounded-xl hover:bg-white/10 backdrop-blur-sm "
                                    :title="
                                        isMaximized ? 'Minimize' : 'Maximize'
                                    "
                                >
                                    <v-icon
                                        :name="
                                            isMaximized
                                                ? 'bi-fullscreen-exit'
                                                : 'bi-fullscreen'
                                        "
                                        class="h-5 w-5"
                                    />
                                </button>
                                <!-- Close Button -->
                                <button
                                    @click="emit('close')"
                                    class="text-white hover:text-amber-200 transition-all duration-200 p-2 rounded-xl hover:bg-white/10 backdrop-blur-sm "
                                >
                                    <v-icon name="bi-x-lg" class="h-6 w-6" />
                                </button>
                            </div>
                        </div>

                        <!-- Enhanced Content -->
                        <div
                            class="flex-1 overflow-y-auto bg-gray-50/50 dark:bg-gray-800/50"
                            :class="{ 'p-8': isMaximized, 'p-6': !isMaximized }"
                        >
                            <div
                                v-if="usage"
                                class="space-y-6"
                                :class="{ 'space-y-8': isMaximized }"
                            >
                                <!-- Billing Information Card -->
                                <div
                                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 transition-all duration-200 hover:shadow-md"
                                >
                                    <div
                                        class="flex items-center justify-between mb-4"
                                    >
                                        <h3
                                            class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2"
                                        >
                                            <v-icon
                                                name="bi-receipt"
                                                class="text-blue-600 dark:text-blue-400"
                                            />
                                            Billing Information
                                        </h3>
                                    </div>
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-2 gap-4"
                                        :class="{
                                            'gap-6': isMaximized,
                                            'md:grid-cols-3': isMaximized,
                                        }"
                                    >
                                        <div class="space-y-2">
                                            <p
                                                class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center gap-1"
                                            >
                                                <v-icon
                                                    name="bi-calendar-month"
                                                    class="w-4 h-4"
                                                />
                                                Billing Month
                                            </p>
                                            <p
                                                class="font-semibold text-gray-900 dark:text-white text-lg"
                                            >
                                                {{ usage.month }}
                                            </p>
                                        </div>
                                        <div class="space-y-2">
                                            <p
                                                class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center gap-1"
                                            >
                                                <span class="text-lg font-bold"
                                                    >₱</span
                                                >
                                                Amount
                                            </p>
                                            <p
                                                class="font-bold text-gray-900 dark:text-white text-xl text-green-600 dark:text-green-400"
                                            >
                                                ₱{{ usage.amount }}
                                            </p>
                                        </div>
                                        <div
                                            v-if="isMaximized"
                                            class="space-y-2"
                                        >
                                            <p
                                                class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center gap-1"
                                            >
                                                <v-icon
                                                    name="bi-clock-history"
                                                    class="w-4 h-4"
                                                />
                                                Status
                                            </p>
                                            <span
                                                :class="[
                                                    statusClasses(usage.status),
                                                    'inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium border transition-all duration-200',
                                                    {
                                                        'px-4 py-2 text-base':
                                                            isMaximized,
                                                    },
                                                ]"
                                            >
                                                <v-icon
                                                    :name="
                                                        usage.status === 'Paid'
                                                            ? 'bi-check-circle-fill'
                                                            : 'bi-clock-fill'
                                                    "
                                                    class="w-4 h-4 mr-2"
                                                    :class="{
                                                        'w-5 h-5 mr-2':
                                                            isMaximized,
                                                    }"
                                                />
                                                {{ usage.status }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Consumption Details Card -->
                                <div
                                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 transition-all duration-200 hover:shadow-md"
                                >
                                    <div
                                        class="flex items-center justify-between mb-4"
                                    >
                                        <h3
                                            class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2"
                                        >
                                            <v-icon
                                                name="bi-speedometer2"
                                                class="text-green-600 dark:text-green-400"
                                            />
                                            Consumption Details
                                        </h3>
                                    </div>
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-2 gap-4"
                                        :class="{
                                            'gap-6': isMaximized,
                                            'md:grid-cols-3': isMaximized,
                                        }"
                                    >
                                        <div class="space-y-2">
                                            <p
                                                class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center gap-1"
                                            >
                                                <v-icon
                                                    name="bi-arrow-left-circle"
                                                    class="w-4 h-4"
                                                />
                                                Previous Reading
                                            </p>
                                            <p
                                                class="font-bold text-gray-900 dark:text-white text-lg"
                                            >
                                                {{
                                                    previousReading || "N/A m³"
                                                }}
                                            </p>
                                        </div>
                                        <div class="space-y-2">
                                            <p
                                                class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center gap-1"
                                            >
                                                <v-icon
                                                    name="bi-arrow-right-circle"
                                                    class="w-4 h-4"
                                                />
                                                Current Reading
                                            </p>
                                            <p
                                                class="font-bold text-gray-900 dark:text-white text-lg text-blue-600 dark:text-blue-400"
                                            >
                                                {{ currentReading || "N/A m³" }}
                                            </p>
                                        </div>
                                        <div
                                            v-if="isMaximized"
                                            class="space-y-2"
                                        >
                                            <p
                                                class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center gap-1"
                                            >
                                                <v-icon
                                                    name="bi-calendar-date"
                                                    class="w-4 h-4"
                                                />
                                                Reading Date
                                            </p>
                                            <p
                                                class="font-medium text-gray-900 dark:text-white text-base"
                                            >
                                                {{ readingDate }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Water Consumption & Status Card -->
                                <div
                                    class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-2xl shadow-sm border border-blue-100 dark:border-blue-800 p-6 transition-all duration-200 hover:shadow-md"
                                >
                                    <div
                                        class="flex items-center justify-between mb-4"
                                    >
                                        <h3
                                            class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2"
                                        >
                                            <v-icon
                                                name="bi-droplet"
                                                class="text-blue-600 dark:text-blue-400"
                                            />
                                            Water Consumption Summary
                                        </h3>
                                    </div>
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-2 gap-6"
                                        :class="{
                                            'md:grid-cols-3 gap-8': isMaximized,
                                        }"
                                    >
                                        <!-- Water Consumption -->
                                        <div
                                            class="text-center p-4 bg-white/50 dark:bg-gray-800/50 rounded-xl border border-blue-200/50 dark:border-blue-700/50"
                                        >
                                            <div
                                                class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mx-auto mb-3"
                                            >
                                                <v-icon
                                                    name="bi-water"
                                                    class="text-blue-600 dark:text-blue-400 text-xl"
                                                />
                                            </div>
                                            <h4
                                                class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-1"
                                            >
                                                Total Consumption
                                            </h4>
                                            <p
                                                class="text-2xl font-bold text-blue-600 dark:text-blue-400"
                                                :class="{
                                                    'text-3xl': isMaximized,
                                                }"
                                            >
                                                {{ usage.usage }} m³
                                            </p>
                                        </div>

                                        <!-- Status -->
                                        <div
                                            class="text-center p-4 bg-white/50 dark:bg-gray-800/50 rounded-xl border border-amber-200/50 dark:border-amber-700/50"
                                        >
                                            <div
                                                class="w-12 h-12 bg-amber-100 dark:bg-amber-900 rounded-full flex items-center justify-center mx-auto mb-3"
                                            >
                                                <v-icon
                                                    name="bi-clock"
                                                    class="text-amber-600 dark:text-amber-400 text-xl"
                                                />
                                            </div>
                                            <h4
                                                class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-1"
                                            >
                                                Payment Status
                                            </h4>
                                            <span
                                                :class="[
                                                    statusClasses(usage.status),
                                                    'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border',
                                                    {
                                                        'px-4 py-1.5 text-base':
                                                            isMaximized,
                                                    },
                                                ]"
                                            >
                                                <v-icon
                                                    :name="
                                                        usage.status === 'Paid'
                                                            ? 'bi-check-circle-fill'
                                                            : 'bi-clock-fill'
                                                    "
                                                    class="w-4 h-4 mr-1"
                                                    :class="{
                                                        'w-5 h-5 mr-2':
                                                            isMaximized,
                                                    }"
                                                />
                                                {{ usage.status }}
                                            </span>
                                        </div>

                                        <!-- Due Date - Show only in maximized view -->
                                        <div
                                            v-if="isMaximized"
                                            class="text-center p-4 bg-white/50 dark:bg-gray-800/50 rounded-xl border border-red-200/50 dark:border-red-700/50"
                                        >
                                            <div
                                                class="w-12 h-12 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center mx-auto mb-3"
                                            >
                                                <v-icon
                                                    name="bi-calendar-x"
                                                    class="text-red-600 dark:text-red-400 text-xl"
                                                />
                                            </div>
                                            <h4
                                                class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-1"
                                            >
                                                Due Date
                                            </h4>
                                            <p
                                                class="text-2xl font-semibold text-gray-900 dark:text-white"
                                                :class="{
                                                    'text-red-600 dark:text-red-400':
                                                        isOverdue,
                                                    'text-amber-600 dark:text-amber-400':
                                                        isDueSoon,
                                                }"
                                            >
                                                {{ dueDateFormatted }}
                                            </p>
                                            <p
                                                v-if="isOverdue"
                                                class="text-sm text-red-500 dark:text-red-400 mt-1 font-medium"
                                            >
                                                Overdue
                                            </p>
                                            <p
                                                v-else-if="isDueSoon"
                                                class="text-sm text-amber-500 dark:text-amber-400 mt-1 font-medium"
                                            >
                                                Due in {{ daysUntilDue }} days
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Due Date & Reading Date - Only show in normal view -->
                                <div
                                    v-if="!isMaximized"
                                    class="grid grid-cols-1 md:grid-cols-2 gap-6"
                                >
                                    <div
                                        class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700"
                                    >
                                        <h4
                                            class="text-md font-semibold text-gray-900 dark:text-white mb-2 flex items-center gap-2"
                                        >
                                            <v-icon
                                                name="bi-calendar-event"
                                                class="text-orange-600 dark:text-orange-400"
                                            />
                                            Reading Date
                                        </h4>
                                        <p
                                            class="text-md text-gray-900 dark:text-white"
                                        >
                                            {{ readingDate }}
                                        </p>
                                    </div>
                                    <div
                                        class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700"
                                    >
                                        <h4
                                            class="text-md font-semibold text-gray-900 dark:text-white mb-2 flex items-center gap-2"
                                        >
                                            <v-icon
                                                name="bi-calendar-x"
                                                class="text-red-600 dark:text-red-400"
                                            />
                                            Due Date
                                        </h4>
                                        <p
                                            class="text-md text-gray-900 dark:text-white"
                                            :class="{
                                                'text-red-600 dark:text-red-400':
                                                    isOverdue,
                                                'text-amber-600 dark:text-amber-400':
                                                    isDueSoon,
                                            }"
                                        >
                                            {{ dueDateFormatted }}
                                        </p>
                                        <p
                                            v-if="isOverdue"
                                            class="text-xs text-red-500 dark:text-red-400 mt-1 font-medium"
                                        >
                                            This bill is overdue
                                        </p>
                                        <p
                                            v-else-if="isDueSoon"
                                            class="text-xs text-amber-500 dark:text-amber-400 mt-1 font-medium"
                                        >
                                            Due in {{ daysUntilDue }} days
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Enhanced Loading state -->
                            <div
                                v-else
                                class="flex justify-center items-center h-64"
                            >
                                <div class="text-center">
                                    <div
                                        class="animate-spin rounded-full h-16 w-16 border-4 border-blue-200 border-t-blue-600 mb-4"
                                    ></div>
                                    <p
                                        class="text-gray-500 dark:text-gray-400 font-medium"
                                    >
                                        Loading usage details...
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Enhanced Footer -->
                        <div
                            class="border-t border-gray-200/50 dark:border-gray-700/50 px-6 py-4 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm"
                            :class="{ 'px-8 py-5': isMaximized }"
                        >
                            <div class="flex justify-between items-center">
                                <div
                                    class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400"
                                >
                                    <v-icon
                                        name="bi-info-circle"
                                        class="w-4 h-4"
                                    />
                                    <span
                                        >Last updated:
                                        {{
                                            new Date().toLocaleDateString()
                                        }}</span
                                    >
                                </div>
                                <div class="flex space-x-3">
                                    <button
                                        @click="emit('close')"
                                        class="inline-flex items-center justify-center rounded-xl border border-gray-300 dark:border-gray-600 shadow-sm px-5 py-2.5 bg-white dark:bg-gray-700 text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 hover:shadow-md"
                                        :class="{
                                            'px-6 py-3 text-base': isMaximized,
                                        }"
                                    >
                                        <v-icon name="bi-x-lg" class="mr-2" />
                                        Close
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import axios from "axios";

const props = defineProps({
    show: {
        type: Boolean,
        required: true,
    },
    usage: {
        type: Object,
        default: null,
    },
    userId: {
        type: Number,
        default: null,
    },
});

const emit = defineEmits(["close"]);

// State for maximize
const isMaximized = ref(false);

const previousReadings = ref([]);
const loading = ref(false);

// Reset maximize state when modal is closed
watch(
    () => props.show,
    (newVal) => {
        if (!newVal) {
            isMaximized.value = false;
        }
    }
);

const toggleMaximize = () => {
    isMaximized.value = !isMaximized.value;
};

// Fetch previous readings when modal opens
watch(
    () => props.show,
    async (newVal) => {
        if (newVal && props.userId) {
            loading.value = true;
            try {
                const response = await axios.get(
                    `/staff/readings/${props.userId}/previous`
                );
                previousReadings.value = response.data;
            } catch (error) {
                console.error("Error fetching previous readings:", error);
            } finally {
                loading.value = false;
            }
        }
    }
);

const statusClasses = (status) => {
    return {
        "bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-900/30 dark:text-amber-300 dark:border-amber-700/50":
            status === "Pending",
        "bg-green-100 text-green-800 border-green-200 dark:bg-green-900/30 dark:text-green-300 dark:border-green-700/50":
            status === "Paid",
        "bg-red-100 text-red-800 border-red-200 dark:bg-red-900/30 dark:text-red-300 dark:border-red-700/50":
            status === "Overdue",
    };
};

// Due Date Computed Properties
const dueDateFormatted = computed(() => {
    if (!props.usage || !props.usage.due_date_formatted) return "N/A";
    return props.usage.due_date_formatted;
});

const isOverdue = computed(() => {
    if (!props.usage || !props.usage.due_date) return false;

    const dueDate = new Date(props.usage.due_date);
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    return dueDate < today && props.usage.status !== "Paid";
});

const isDueSoon = computed(() => {
    if (!props.usage || !props.usage.due_date || props.usage.status === "Paid")
        return false;

    const dueDate = new Date(props.usage.due_date);
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const daysUntilDue = Math.ceil((dueDate - today) / (1000 * 60 * 60 * 24));
    return daysUntilDue <= 7 && daysUntilDue >= 0;
});

const daysUntilDue = computed(() => {
    if (!props.usage || !props.usage.due_date) return 0;

    const dueDate = new Date(props.usage.due_date);
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    return Math.ceil((dueDate - today) / (1000 * 60 * 60 * 24));
});

// Helper methods to get data
const previousReading = computed(() => {
    if (!props.usage || !previousReadings.value.length) return "N/A m³";

    const current = previousReadings.value.find(
        (r) =>
            r.billing_month === props.usage.month.split(" ")[0] &&
            r.year === props.usage.month.split(" ")[1]
    );

    return current && current.previous_reading !== null
        ? `${current.previous_reading} m³`
        : "N/A m³";
});

const currentReading = computed(() => {
    if (!props.usage || !previousReadings.value.length) return "N/A m³";

    const current = previousReadings.value.find(
        (r) =>
            r.billing_month === props.usage.month.split(" ")[0] &&
            r.year === props.usage.month.split(" ")[1]
    );

    return current ? `${current.reading} m³` : "N/A m³";
});

const readingDate = computed(() => {
    if (!props.usage || !previousReadings.value.length)
        return new Date().toLocaleDateString("en-US", {
            year: "numeric",
            month: "long",
            day: "numeric",
        });

    const current = previousReadings.value.find(
        (r) =>
            r.billing_month === props.usage.month.split(" ")[0] &&
            r.year === props.usage.month.split(" ")[1]
    );

    if (current && current.reading_date) {
        return new Date(current.reading_date).toLocaleDateString("en-US", {
            year: "numeric",
            month: "long",
            day: "numeric",
        });
    }

    return new Date().toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
});

const previousMonthUsage = computed(() => {
    if (!props.usage || !previousReadings.value.length) return "N/A m³";

    // Get the month before the current usage
    const currentDate = new Date();
    const currentMonth = props.usage.month.split(" ")[0];
    const currentYear = props.usage.month.split(" ")[1];

    const monthNames = [
        "January",
        "February",
        "March",
        "April",
        "May",
        "June",
        "July",
        "August",
        "September",
        "October",
        "November",
        "December",
    ];

    const currentMonthIndex = monthNames.indexOf(currentMonth);
    let prevMonthIndex = currentMonthIndex - 1;
    let prevYear = currentYear;

    if (prevMonthIndex < 0) {
        prevMonthIndex = 11;
        prevYear = parseInt(currentYear) - 1;
    }

    const prevMonth = monthNames[prevMonthIndex];
    const prevReading = previousReadings.value.find(
        (r) => r.billing_month === prevMonth && r.year === prevYear.toString()
    );

    return prevReading ? `${prevReading.consumption} m³` : "N/A m³";
});

const averageUsage = computed(() => {
    if (!previousReadings.value.length) return "N/A m³";

    const lastSix = previousReadings.value.slice(0, 6);
    if (lastSix.length === 0) return "N/A m³";

    const total = lastSix.reduce(
        (sum, reading) => sum + parseFloat(reading.consumption),
        0
    );
    const average = total / lastSix.length;

    return `${average.toFixed(1)} m³`;
});
</script>

<style scoped>
/* Enhanced scrollbar */
.overflow-y-auto {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f8fafc;
}

.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f8fafc;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background-color: #cbd5e1;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background-color: #94a3b8;
}

.dark .overflow-y-auto::-webkit-scrollbar-track {
    background: #1f2937;
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb {
    background-color: #4b5563;
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background-color: #6b7280;
}

/* Modal transition styles */
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-enter-active .transform,
.modal-leave-active .transform {
    transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.5, 1);
}

.modal-enter-from .transform {
    transform: translateX(100%);
}

.modal-leave-to .transform {
    transform: translateX(100%);
}

/* Custom animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.bg-white,
.bg-gray-50 {
    animation: fadeIn 0.3s ease-out;
}
</style>
