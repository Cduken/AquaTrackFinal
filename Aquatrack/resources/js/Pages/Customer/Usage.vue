<template>
    <CustomerLayout>
        <!-- Header Section -->
        <div class=" border-b border-gray-100 dark:border-gray-700">
            <div
                class="flex flex-col md:flex-row md:items-center justify-between gap-3"
            >
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">
                        Water Usage History
                    </h1>
                    <p class="text-gray-600 text-sm">
                        Track your monthly water consumption and billing
                        information
                    </p>
                </div>

                <!-- Overdue Alert -->
                <div
                    v-if="overdueBills > 0"
                    class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded p-2"
                >
                    <div class="flex items-center gap-1.5">
                        <v-icon
                            name="bi-exclamation-triangle-fill"
                            class="w-3.5 h-3.5 text-red-600 dark:text-red-400"
                        />
                        <span
                            class="text-red-700 dark:text-red-300 text-xs font-medium"
                        >
                            {{ overdueBills }} overdue bill{{
                                overdueBills > 1 ? "s" : ""
                            }}
                            • ₱{{ overdueAmount }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="mt-4">
            <!-- Chart Section -->
            <div
                class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-800 p-4 rounded-lg border border-blue-100 dark:border-gray-600 mb-4"
            >
                <div
                    class="flex flex-col sm:flex-row sm:items-center justify-between mb-4"
                >
                    <div>
                        <h3
                            class="text-sm font-semibold text-gray-800 dark:text-white"
                        >
                            Monthly Usage Trend
                        </h3>
                        <p
                            class="text-xs text-gray-600 dark:text-gray-400 mt-1"
                        >
                            Water consumption and billing overview
                        </p>
                    </div>

                    <div class="flex items-center gap-1 mt-2 sm:mt-0">
                        <button
                            v-for="period in timePeriods"
                            :key="period"
                            @click="selectedPeriod = period"
                            :class="[
                                'px-3 py-1 text-xs rounded-lg transition-all duration-200 border',
                                selectedPeriod === period
                                    ? 'bg-white text-blue-600 dark:bg-gray-600 dark:text-blue-300 font-medium border-blue-200 dark:border-gray-500 shadow-sm'
                                    : 'text-gray-600 dark:text-gray-400 hover:bg-white/50 dark:hover:bg-gray-600/50 border-transparent',
                            ]"
                        >
                            {{ period }}
                        </button>
                    </div>
                </div>

                <div class="h-56">
                    <canvas ref="usageChart"></canvas>
                </div>
            </div>

            <!-- Billing History -->
            <div
                class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden"
            >
                <div
                    class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50"
                >
                    <div
                        class="flex flex-col sm:flex-row sm:items-center justify-between gap-2"
                    >
                        <h3
                            class="text-sm font-semibold text-gray-800 dark:text-white"
                        >
                            Billing History
                        </h3>

                        <div class="flex items-center gap-2">
                            <div class="relative">
                                <div
                                    class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none"
                                >
                                    <v-icon
                                        name="hi-search"
                                        class="h-3.5 w-3.5 text-gray-400"
                                    />
                                </div>
                                <input
                                    type="text"
                                    v-model="searchQuery"
                                    placeholder="Search months..."
                                    class="pl-9 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white w-full sm:w-48 transition-colors"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                    @click="sortTable('month')"
                                >
                                    <div class="flex items-center gap-1">
                                        Month
                                        <v-icon
                                            :name="
                                                sortField === 'month'
                                                    ? sortDirection === 'asc'
                                                        ? 'bi-arrow-up'
                                                        : 'bi-arrow-down'
                                                    : 'bi-arrow-down-up'
                                            "
                                            class="w-3 h-3 text-gray-500"
                                        />
                                    </div>
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                    @click="sortTable('usage')"
                                >
                                    <div class="flex items-center gap-1">
                                        Usage
                                        <v-icon
                                            :name="
                                                sortField === 'usage'
                                                    ? sortDirection === 'asc'
                                                        ? 'bi-arrow-up'
                                                        : 'bi-arrow-down'
                                                    : 'bi-arrow-down-up'
                                            "
                                            class="w-3 h-3 text-gray-500"
                                        />
                                    </div>
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                    @click="sortTable('amount')"
                                >
                                    <div class="flex items-center gap-1">
                                        Amount
                                        <v-icon
                                            :name="
                                                sortField === 'amount'
                                                    ? sortDirection === 'asc'
                                                        ? 'bi-arrow-up'
                                                        : 'bi-arrow-down'
                                                    : 'bi-arrow-down-up'
                                            "
                                            class="w-3 h-3 text-gray-500"
                                        />
                                    </div>
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide"
                                >
                                    Due Date
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide"
                                >
                                    Status
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody
                            class="divide-y divide-gray-200 dark:divide-gray-700"
                        >
                            <tr
                                v-for="(usage, index) in paginatedUsageData"
                                :key="index"
                                class="hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors duration-150 group"
                            >
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div
                                        class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-blue-700 dark:group-hover:text-blue-300"
                                    >
                                        {{ usage.month }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div
                                        class="text-sm text-gray-900 dark:text-white"
                                    >
                                        {{ usage.usage }} m³
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div
                                        class="text-sm font-semibold text-gray-900 dark:text-white"
                                    >
                                        ₱{{ usage.amount }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div
                                        class="text-xs text-gray-600 dark:text-gray-400"
                                    >
                                        {{ usage.due_date_formatted || "N/A" }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span
                                        :class="statusClasses(usage.status)"
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium transition-colors"
                                    >
                                        <v-icon
                                            :name="getStatusIcon(usage.status)"
                                            class="w-3 h-3 mr-1"
                                        />
                                        {{ usage.status }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap">
                                    <div class="flex space-x-1 ml-3">
                                        <Link
                                            :href="
                                                route(
                                                    'customer.usage.details',
                                                    usage.month
                                                )
                                            "
                                            class="inline-flex items-center text-xs py-1 border text-blue-500 border-blue-200 bg-blue-100  px-2 rounded-full dark:text-gray-400 hover:bg-blue-200 dark:hover:text-white transition-colors"
                                        >

                                        <Eye class="w-4 h-4 mr-1 text-xs text-blue-500" />

                                            View
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div
                    v-if="filteredUsageData.length === 0"
                    class="text-center py-8"
                >
                    <div
                        class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3"
                    >
                        <v-icon
                            name="hi-clipboard-list"
                            class="w-8 h-8 text-gray-400 dark:text-gray-500"
                        />
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        No usage records found
                    </p>
                </div>

                <!-- Pagination -->
                <div
                    v-if="filteredUsageData.length > 0"
                    class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50"
                >
                    <div
                        class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3"
                    >
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Showing
                            <span class="font-semibold"
                                >{{ startIndex + 1 }}-{{ endIndex }}</span
                            >
                            of
                            <span class="font-semibold">{{
                                filteredUsageData.length
                            }}</span>
                            bills
                        </div>
                        <div
                            class="flex items-center justify-center sm:justify-end space-x-1"
                        >
                            <button
                                @click="prevPage"
                                :disabled="currentPage === 1"
                                class="px-3 py-1.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center gap-1"
                            >
                                <v-icon
                                    name="bi-chevron-left"
                                    class="w-4 h-4"
                                />
                                Previous
                            </button>
                            <div class="flex items-center space-x-1">
                                <button
                                    v-for="page in totalPages"
                                    :key="page"
                                    @click="currentPage = page"
                                    :class="[
                                        'px-3 py-1.5 text-sm rounded-lg min-w-10 transition-all duration-200',
                                        currentPage === page
                                            ? 'bg-blue-600 text-white shadow-sm transform scale-105'
                                            : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600',
                                    ]"
                                >
                                    {{ page }}
                                </button>
                            </div>
                            <button
                                @click="nextPage"
                                :disabled="currentPage === totalPages"
                                class="px-3 py-1.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center gap-1"
                            >
                                Next
                                <v-icon
                                    name="bi-chevron-right"
                                    class="w-4 h-4"
                                />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <CustomerUsageModal
            :show="showUsageModal"
            :usage="selectedUsage"
            :userId="selectedUserId"
            @close="showUsageModal = false"
        />
    </CustomerLayout>
</template>

<script setup>
import CustomerLayout from "@/Layouts/CustomerLayout.vue";
import CustomerUsageModal from "@/Components/Customer/CustomerUsageModal.vue";
import { ref, onMounted, computed, watch } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import Chart from "chart.js/auto";
import { Eye } from "lucide-vue-next";

const page = usePage();
const usageData = ref(page.props.usageData ?? []);
const chartData = ref(page.props.chartData ?? []);

// Modal state
const showUsageModal = ref(false);
const selectedUsage = ref(null);
const selectedUserId = ref(null);

// UI State
const searchQuery = ref("");
const sortField = ref("month");
const sortDirection = ref("desc");
const selectedPeriod = ref("6M");
const timePeriods = ["3M", "6M", "1Y", "All"];

// Pagination
const currentPage = ref(1);
const itemsPerPage = 6;

// Refs
const usageChart = ref(null);
let chartInstance = null;

// Computed properties
const overdueBills = computed(() => {
    return usageData.value.filter((item) => item.status === "Overdue").length;
});

const overdueAmount = computed(() => {
    if (usageData.value.length === 0) return "0.00";
    const total = usageData.value
        .filter((item) => item.status === "Overdue")
        .reduce((acc, item) => acc + parseFloat(item.amount), 0);
    return total.toFixed(2);
});

// Pagination Computed Properties
const totalPages = computed(() => {
    return Math.ceil(filteredUsageData.value.length / itemsPerPage);
});

const startIndex = computed(() => {
    return (currentPage.value - 1) * itemsPerPage;
});

const endIndex = computed(() => {
    return Math.min(
        startIndex.value + itemsPerPage,
        filteredUsageData.value.length
    );
});

const paginatedUsageData = computed(() => {
    return filteredUsageData.value.slice(startIndex.value, endIndex.value);
});

const filteredUsageData = computed(() => {
    let data = [...usageData.value];

    // Filter by search query
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        data = data.filter((item) => item.month.toLowerCase().includes(query));
    }

    // Sort data
    data.sort((a, b) => {
        let modifier = 1;
        if (sortDirection.value === "desc") modifier = -1;

        if (sortField.value === "month") {
            const dateA = new Date(`1 ${a.month}`);
            const dateB = new Date(`1 ${b.month}`);
            return (dateA - dateB) * modifier;
        } else {
            if (a[sortField.value] < b[sortField.value]) return -1 * modifier;
            if (a[sortField.value] > b[sortField.value]) return 1 * modifier;
            return 0;
        }
    });

    return data;
});

// Methods
const getStatusIcon = (status) => {
    switch (status) {
        case "Paid":
            return "bi-check-circle-fill";
        case "Overdue":
            return "bi-exclamation-triangle-fill";
        case "Pending":
            return "bi-clock-fill";
        default:
            return "bi-question-circle-fill";
    }
};

const statusClasses = (status) => {
    const baseClasses =
        "inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium transition-colors";

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

const sortTable = (field) => {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === "asc" ? "desc" : "asc";
    } else {
        sortField.value = field;
        sortDirection.value = "desc";
    }
    currentPage.value = 1;
};

const openUsageModal = (usage) => {
    selectedUsage.value = usage;
    selectedUserId.value = page.props.auth.user.id;
    showUsageModal.value = true;
};

// Pagination methods
const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
    }
};

const prevPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
    }
};

// Initialize chart
onMounted(() => {
    const labels = chartData.value.map((item) => item.month);
    const usageValues = chartData.value.map((item) => item.usage);
    const amountValues = chartData.value.map((item) => item.amount);

    chartInstance = new Chart(usageChart.value, {
        type: "bar",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Water Usage (m³)",
                    data: usageValues,
                    backgroundColor: "rgba(59, 130, 246, 0.8)",
                    borderColor: "rgb(59, 130, 246)",
                    borderWidth: 2,
                    borderRadius: 6,
                    barPercentage: 0.7,
                },
                {
                    label: "Bill Amount (₱)",
                    data: amountValues,
                    backgroundColor: "rgba(139, 92, 246, 0.8)",
                    borderColor: "rgb(139, 92, 246)",
                    borderWidth: 2,
                    borderRadius: 6,
                    barPercentage: 0.7,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: "top",
                    labels: {
                        usePointStyle: true,
                        padding: 15,
                        font: {
                            size: 11,
                            family: "'Inter', sans-serif",
                        },
                    },
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const label = context.dataset.label || "";
                            const value = context.raw;
                            return `${label}: ${
                                label.includes("Usage")
                                    ? value + " m³"
                                    : "₱" + value
                            }`;
                        },
                    },
                    backgroundColor: "rgba(255, 255, 255, 0.95)",
                    titleColor: "#1f2937",
                    bodyColor: "#374151",
                    borderColor: "#e5e7eb",
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: true,
                    usePointStyle: true,
                    titleFont: {
                        size: 11,
                        family: "'Inter', sans-serif",
                    },
                    bodyFont: {
                        size: 11,
                        family: "'Inter', sans-serif",
                    },
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false,
                        color: "rgba(0, 0, 0, 0.05)",
                    },
                    ticks: {
                        padding: 8,
                        font: {
                            size: 10,
                            family: "'Inter', sans-serif",
                        },
                    },
                },
                x: {
                    grid: {
                        display: false,
                    },
                    ticks: {
                        padding: 8,
                        font: {
                            size: 10,
                            family: "'Inter', sans-serif",
                        },
                    },
                },
            },
        },
    });
});

// Update chart when period changes
watch(selectedPeriod, (newPeriod) => {
    console.log("Period changed to:", newPeriod);
    if (chartInstance) {
        chartInstance.update();
    }
});

// Reset to first page when search query changes
watch(searchQuery, () => {
    currentPage.value = 1;
});
</script>

<style scoped>
/* Custom styles for better visual appeal */
.min-w-10 {
    min-width: 2.5rem;
}

/* Smooth transitions for all interactive elements */
* {
    transition-property: color, background-color, border-color, transform,
        box-shadow;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}
</style>
