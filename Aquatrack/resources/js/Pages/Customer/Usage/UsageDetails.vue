<template>
    <CustomerLayout>
        <!-- Header with Back Navigation -->
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4 ">


                    <div>
                        <h1
                            class="text-2xl font-semibold text-gray-900 dark:text-white"
                        >
                            Usage Details
                        </h1>
                        <p
                            class="text-gray-600 dark:text-gray-400 text-sm mt-1"
                        >
                            {{ usage.month }} • Water consumption overview
                        </p>
                    </div>


                </div>

                 <Link
                        :href="route('customer.usage')"
                        class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors"
                    >
                        <ArrowLeft class="w-4 h-4 mr-2" />
                        Back to Usage History
                    </Link>
            </div>
        </div>

        <!-- Main Content -->
        <div class="p-4">
            <div v-if="usage" class="space-y-6">
                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Water Consumption -->
                    <div
                        class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700"
                    >
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center"
                            >
                                <Droplets
                                    class="w-6 h-6 text-blue-600 dark:text-blue-400"
                                />
                            </div>
                            <div>
                                <p
                                    class="text-sm font-medium text-gray-600 dark:text-gray-400"
                                >
                                    Total Consumption
                                </p>
                                <p
                                    class="text-xl font-bold text-gray-900 dark:text-white"
                                >
                                    {{ usage.usage }} m³
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Amount -->
                    <div
                        class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700"
                    >
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center"
                            >
                                <DollarSign
                                    class="w-6 h-6 text-green-600 dark:text-green-400"
                                />
                            </div>
                            <div>
                                <p
                                    class="text-sm font-medium text-gray-600 dark:text-gray-400"
                                >
                                    Amount Due
                                </p>
                                <p
                                    class="text-xl font-bold text-green-600 dark:text-green-400"
                                >
                                    ₱{{ usage.amount }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div
                        class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700"
                    >
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-12 h-12 bg-amber-100 dark:bg-amber-900 rounded-lg flex items-center justify-center"
                            >
                                <Clock
                                    class="w-6 h-6 text-amber-600 dark:text-amber-400"
                                />
                            </div>
                            <div>
                                <p
                                    class="text-sm font-medium text-gray-600 dark:text-gray-400"
                                >
                                    Status
                                </p>
                                <span
                                    :class="statusClasses(usage.status)"
                                    class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium"
                                >
                                    <component
                                        :is="getStatusIcon(usage.status)"
                                        class="w-4 h-4 mr-1.5"
                                    />
                                    {{ usage.status }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Information -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Billing Information -->
                    <div
                        class="bg-white dark:bg-gray-800 p-5 rounded-lg border border-gray-200 dark:border-gray-700"
                    >
                        <h3
                            class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2"
                        >
                            <Receipt
                                class="w-5 h-5 text-blue-600 dark:text-blue-400"
                            />
                            Billing Information
                        </h3>
                        <div class="space-y-4">
                            <div
                                class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700"
                            >
                                <span
                                    class="text-sm text-gray-600 dark:text-gray-400"
                                    >Billing Month</span
                                >
                                <span
                                    class="text-sm font-medium text-gray-900 dark:text-white"
                                    >{{ usage.month }}</span
                                >
                            </div>
                            <div
                                class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700"
                            >
                                <span
                                    class="text-sm text-gray-600 dark:text-gray-400"
                                    >Amount</span
                                >
                                <span
                                    class="text-sm font-bold text-green-600 dark:text-green-400"
                                    >₱{{ usage.amount }}</span
                                >
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span
                                    class="text-sm text-gray-600 dark:text-gray-400"
                                    >Status</span
                                >
                                <span
                                    :class="statusClasses(usage.status)"
                                    class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium"
                                >
                                    <component
                                        :is="getStatusIcon(usage.status)"
                                        class="w-3 h-3 mr-1.5"
                                    />
                                    {{ usage.status }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Consumption Details -->
                    <div
                        class="bg-white dark:bg-gray-800 p-5 rounded-lg border border-gray-200 dark:border-gray-700"
                    >
                        <h3
                            class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2"
                        >
                            <Gauge
                                class="w-5 h-5 text-green-600 dark:text-green-400"
                            />
                            Consumption Details
                        </h3>
                        <div class="space-y-4">
                            <div
                                class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700"
                            >
                                <span
                                    class="text-sm text-gray-600 dark:text-gray-400"
                                    >Previous Reading</span
                                >
                                <span
                                    class="text-sm font-medium text-gray-900 dark:text-white"
                                    >{{ previousReading || "N/A m³" }}</span
                                >
                            </div>
                            <div
                                class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700"
                            >
                                <span
                                    class="text-sm text-gray-600 dark:text-gray-400"
                                    >Current Reading</span
                                >
                                <span
                                    class="text-sm font-medium text-blue-600 dark:text-blue-400"
                                    >{{ currentReading || "N/A m³" }}</span
                                >
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span
                                    class="text-sm text-gray-600 dark:text-gray-400"
                                    >Reading Date</span
                                >
                                <span
                                    class="text-sm font-medium text-gray-900 dark:text-white"
                                    >{{ readingDate }}</span
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Due Date Information -->
                    <div
                        class="bg-white dark:bg-gray-800 p-5 rounded-lg border border-gray-200 dark:border-gray-700"
                    >
                        <h3
                            class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2"
                        >
                            <Calendar
                                class="w-5 h-5 text-purple-600 dark:text-purple-400"
                            />
                            Due Date Information
                        </h3>
                        <div class="space-y-4">
                            <div
                                class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700"
                            >
                                <span
                                    class="text-sm text-gray-600 dark:text-gray-400"
                                    >Due Date</span
                                >
                                <span
                                    class="text-sm font-medium"
                                    :class="dueDateTextClass"
                                >
                                    {{ dueDateFormatted }}
                                </span>
                            </div>
                            <div v-if="isOverdue || isDueSoon" class="py-2">
                                <div
                                    v-if="isOverdue"
                                    class="flex items-center gap-2 text-red-600 dark:text-red-400"
                                >
                                    <AlertCircle class="w-4 h-4" />
                                    <span class="text-sm font-medium"
                                        >This bill is overdue</span
                                    >
                                </div>
                                <div
                                    v-else-if="isDueSoon"
                                    class="flex items-center gap-2 text-amber-600 dark:text-amber-400"
                                >
                                    <Clock class="w-4 h-4" />
                                    <span class="text-sm font-medium"
                                        >Due in {{ daysUntilDue }} days</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div
                        class="bg-white dark:bg-gray-800 p-5 rounded-lg border border-gray-200 dark:border-gray-700"
                    >
                        <h3
                            class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2"
                        >
                            <Info
                                class="w-5 h-5 text-gray-600 dark:text-gray-400"
                            />
                            Additional Information
                        </h3>
                        <div
                            class="space-y-3 text-sm text-gray-600 dark:text-gray-400"
                        >
                            <p>
                                This usage record shows your water consumption
                                and billing details for the specified period.
                            </p>
                            <p
                                v-if="usage.status === 'Pending'"
                                class="text-amber-600 dark:text-amber-400"
                            >
                                Please ensure payment is made by the due date to
                                avoid late fees.
                            </p>
                            <p
                                v-if="usage.status === 'Overdue'"
                                class="text-red-600 dark:text-red-400"
                            >
                                This bill is overdue. Please make payment
                                immediately to avoid service interruption.
                            </p>
                            <p
                                v-if="usage.status === 'Paid'"
                                class="text-green-600 dark:text-green-400"
                            >
                                This bill has been paid. Thank you for your
                                timely payment.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading State -->
            <div v-else class="flex justify-center items-center h-64">
                <div class="text-center">
                    <div
                        class="animate-spin rounded-full h-12 w-12 border-4 border-blue-200 border-t-blue-600 mb-4"
                    ></div>
                    <p class="text-gray-500 dark:text-gray-400 font-medium">
                        Loading usage details...
                    </p>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>

<script setup>
import CustomerLayout from "@/Layouts/CustomerLayout.vue";
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";
import {
    ArrowLeft,
    Droplets,
    DollarSign,
    Clock,
    Receipt,
    Gauge,
    Calendar,
    Info,
    CheckCircle2,
    AlertCircle,
    AlertTriangle,
} from "lucide-vue-next";

const props = defineProps({
    usage: {
        type: Object,
        default: null,
    },
    previousReadings: {
        type: Array,
        default: () => [],
    },
});

// Helper methods to get data
const previousReading = computed(() => {
    if (!props.usage || !props.previousReadings.length) return "N/A m³";

    const current = props.previousReadings.find(
        (r) =>
            r.billing_month === props.usage.month.split(" ")[0] &&
            r.year === props.usage.month.split(" ")[1]
    );

    return current && current.previous_reading !== null
        ? `${current.previous_reading} m³`
        : "N/A m³";
});

const currentReading = computed(() => {
    if (!props.usage || !props.previousReadings.length) return "N/A m³";

    const current = props.previousReadings.find(
        (r) =>
            r.billing_month === props.usage.month.split(" ")[0] &&
            r.year === props.usage.month.split(" ")[1]
    );

    return current ? `${current.reading} m³` : "N/A m³";
});

const readingDate = computed(() => {
    if (!props.usage || !props.previousReadings.length)
        return new Date().toLocaleDateString("en-US", {
            year: "numeric",
            month: "long",
            day: "numeric",
        });

    const current = props.previousReadings.find(
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

const dueDateTextClass = computed(() => {
    if (isOverdue.value) return "text-red-600 dark:text-red-400";
    if (isDueSoon.value) return "text-amber-600 dark:text-amber-400";
    return "text-gray-900 dark:text-white";
});

const getStatusIcon = (status) => {
    switch (status) {
        case "Paid":
            return CheckCircle2;
        case "Overdue":
            return AlertCircle;
        case "Pending":
            return Clock;
        default:
            return AlertCircle;
    }
};

const statusClasses = (status) => {
    const baseClasses =
        "inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium";

    switch (status) {
        case "Pending":
            return `${baseClasses} bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300 border border-yellow-200 dark:border-yellow-800`;
        case "Paid":
            return `${baseClasses} bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 border border-green-200 dark:border-green-800`;
        case "Overdue":
            return `${baseClasses} bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 border border-red-200 dark:border-red-800`;
        default:
            return `${baseClasses} bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300 border border-gray-200 dark:border-gray-700`;
    }
};
</script>
