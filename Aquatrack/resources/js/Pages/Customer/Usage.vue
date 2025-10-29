<template>
    <CustomerLayout>
        <div
            class="w-full bg-white dark:bg-gray-800 rounded-lg shadow-xs border border-gray-200 dark:border-gray-700 overflow-hidden"
        >
            <!-- Header Section -->
            <div class="p-5 border-b border-gray-200 dark:border-gray-700">
                <div
                    class="flex flex-col md:flex-row md:items-center justify-between gap-4"
                >
                    <div>
                        <h2
                            class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-3"
                        >
                            <div
                                class="p-2 bg-blue-100 dark:bg-blue-800 rounded-lg"
                            >
                                <v-icon
                                    name="bi-graph-up"
                                    class="w-5 h-5 text-blue-600 dark:text-blue-300"
                                />
                            </div>
                            Water Usage History
                        </h2>
                        <p
                            class="text-gray-600 dark:text-gray-300 text-sm mt-1"
                        >
                            Track your monthly water consumption and billing
                            information
                        </p>
                    </div>

                    <!-- Overdue Alert -->
                    <div
                        v-if="overdueBills > 0"
                        class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3"
                    >
                        <div class="flex items-center gap-2">
                            <v-icon
                                name="bi-exclamation-triangle-fill"
                                class="w-4 h-4 text-red-600 dark:text-red-400"
                            />
                            <span
                                class="text-red-700 dark:text-red-300 text-sm font-medium"
                            >
                                {{ overdueBills }} overdue bill{{
                                    overdueBills > 1 ? "s" : ""
                                }}
                                - Total: ₱{{ overdueAmount }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="p-5">
                <!-- Chart Section -->
                <div
                    class="bg-gray-50 dark:bg-gray-700/30 p-5 rounded-lg mb-6 border border-gray-200 dark:border-gray-600"
                >
                    <div
                        class="flex flex-col sm:flex-row sm:items-center justify-between mb-5"
                    >
                        <h3
                            class="text-base font-semibold text-gray-800 dark:text-white"
                        >
                            Monthly Usage and Billing Trend
                        </h3>

                        <div class="flex items-center gap-2 mt-3 sm:mt-0">
                            <button
                                v-for="period in timePeriods"
                                :key="period"
                                @click="selectedPeriod = period"
                                :class="[
                                    'px-3 py-1.5 text-xs rounded-lg transition-colors',
                                    selectedPeriod === period
                                        ? 'bg-blue-100 text-blue-700 dark:bg-blue-800 dark:text-blue-200 font-medium'
                                        : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-600',
                                ]"
                            >
                                {{ period }}
                            </button>
                        </div>
                    </div>

                    <div class="h-64 md:h-72">
                        <canvas ref="usageChart"></canvas>
                    </div>
                </div>

                <!-- Usage Table -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden"
                >
                    <div
                        class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3"
                    >
                        <h3
                            class="text-base font-semibold text-gray-800 dark:text-white"
                        >
                            Billing History
                        </h3>

                        <div class="relative">
                            <div
                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
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
                                class="pl-9 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white w-full sm:w-48"
                            />
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table
                            class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
                        >
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                                        @click="sortTable('month')"
                                    >
                                        <div class="flex items-center">
                                            Month
                                            <v-icon
                                                :name="
                                                    sortField === 'month'
                                                        ? sortDirection ===
                                                          'asc'
                                                            ? 'bi-arrow-up'
                                                            : 'bi-arrow-down'
                                                        : 'bi-arrow-down-up'
                                                "
                                                class="w-3 h-3 ml-1"
                                            />
                                        </div>
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                                        @click="sortTable('usage')"
                                    >
                                        <div class="flex items-center">
                                            Usage (m³)
                                            <v-icon
                                                :name="
                                                    sortField === 'usage'
                                                        ? sortDirection ===
                                                          'asc'
                                                            ? 'bi-arrow-up'
                                                            : 'bi-arrow-down'
                                                        : 'bi-arrow-down-up'
                                                "
                                                class="w-3 h-3 ml-1"
                                            />
                                        </div>
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                                        @click="sortTable('amount')"
                                    >
                                        <div class="flex items-center">
                                            Amount (₱)
                                            <v-icon
                                                :name="
                                                    sortField === 'amount'
                                                        ? sortDirection ===
                                                          'asc'
                                                            ? 'bi-arrow-up'
                                                            : 'bi-arrow-down'
                                                        : 'bi-arrow-down-up'
                                                "
                                                class="w-3 h-3 ml-1"
                                            />
                                        </div>
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                    >
                                        Due Date
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                    >
                                        Status
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                    >
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody
                                class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"
                            >
                                <tr
                                    v-for="(usage, index) in filteredUsageData"
                                    :key="index"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                                >
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div
                                            class="text-sm font-medium text-gray-900 dark:text-white"
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
                                            class="text-sm font-medium text-gray-900 dark:text-white"
                                        >
                                            ₱{{ usage.amount }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div
                                            class="text-xs text-gray-600 dark:text-gray-400"
                                        >
                                            {{
                                                usage.due_date_formatted ||
                                                "N/A"
                                            }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span
                                            :class="statusClasses(usage.status)"
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                        >
                                            <v-icon
                                                :name="
                                                    getStatusIcon(usage.status)
                                                "
                                                class="w-3 h-3 mr-1"
                                            />
                                            {{ usage.status }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-4 py-3 whitespace-nowrap text-sm font-medium"
                                    >
                                        <div class="flex space-x-1">
                                            <button
                                                @click="openUsageModal(usage)"
                                                class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 p-1 rounded-md hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors"
                                                title="View Details"
                                            >
                                                <v-icon
                                                    name="bi-eye-fill"
                                                    class="w-3.5 h-3.5"
                                                />
                                            </button>
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
                        <v-icon
                            name="hi-clipboard-list"
                            class="w-10 h-10 text-gray-300 dark:text-gray-600 mx-auto"
                        />
                        <p
                            class="mt-3 text-sm text-gray-500 dark:text-gray-400"
                        >
                            No usage records found
                        </p>
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
import { usePage } from "@inertiajs/vue3";
import Chart from "chart.js/auto";

const page = usePage();
const usageData = ref(page.props.usageData ?? []);
const chartData = ref(page.props.chartData ?? []);

// Add these to your component state
const showUsageModal = ref(false);
const selectedUsage = ref(null);
const selectedUserId = ref(null);

// Function to open the modal
const openUsageModal = (usage) => {
    selectedUsage.value = usage;
    selectedUserId.value = page.props.auth.user.id;
    showUsageModal.value = true;
};

// UI State
const searchQuery = ref("");
const sortField = ref("month");
const sortDirection = ref("desc");
const selectedPeriod = ref("6M");
const timePeriods = ["3M", "6M", "1Y", "All"];

// Refs
const usageChart = ref(null);
let chartInstance = null;

// Computed properties
const currentMonthUsage = computed(() => {
    if (usageData.value.length === 0) return "0";
    return usageData.value[0].usage;
});

const averageUsage = computed(() => {
    if (usageData.value.length === 0) return "0";
    const sum = usageData.value.reduce(
        (acc, item) => acc + parseFloat(item.usage),
        0
    );
    return (sum / usageData.value.length).toFixed(1);
});

const totalAmountDue = computed(() => {
    if (usageData.value.length === 0) return "0.00";
    const total = usageData.value
        .filter(
            (item) => item.status === "Pending" || item.status === "Overdue"
        )
        .reduce((acc, item) => acc + parseFloat(item.amount), 0);
    return total.toFixed(2);
});

const pendingBills = computed(() => {
    return usageData.value.filter((item) => item.status === "Pending").length;
});

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

const currentMonthChange = computed(() => {
    if (usageData.value.length < 2) return "No previous data";

    const current = parseFloat(usageData.value[0].usage);
    const previous = parseFloat(usageData.value[1].usage);
    const change = ((current - previous) / previous) * 100;

    if (change > 0) {
        return `↑ ${Math.abs(change).toFixed(1)}% from last month`;
    } else if (change < 0) {
        return `↓ ${Math.abs(change).toFixed(1)}% from last month`;
    } else {
        return "No change from last month";
    }
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
            // Convert months to dates for proper sorting
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
        "inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium";

    switch (status) {
        case "Pending":
            return `${baseClasses} bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300`;
        case "Paid":
            return `${baseClasses} bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300`;
        case "Overdue":
            return `${baseClasses} bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300`;
        default:
            return `${baseClasses} bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300`;
    }
};

const sortTable = (field) => {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === "asc" ? "desc" : "asc";
    } else {
        sortField.value = field;
        sortDirection.value = "desc";
    }
};

// Initialize chart
onMounted(() => {
    // Prepare labels and data for chart
    const labels = chartData.value.map((item) => item.month);
    const usageValues = chartData.value.map((item) => item.usage);
    const amountValues = chartData.value.map((item) => item.amount);

    // Usage Chart with dynamic data
    chartInstance = new Chart(usageChart.value, {
        type: "bar",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Water Usage (m³)",
                    data: usageValues,
                    backgroundColor: "rgba(59, 130, 246, 0.7)",
                    borderColor: "rgb(59, 130, 246)",
                    borderWidth: 1,
                    borderRadius: 4,
                    barPercentage: 0.6,
                },
                {
                    label: "Bill Amount (₱)",
                    data: amountValues,
                    backgroundColor: "rgba(156, 163, 175, 0.7)",
                    borderColor: "rgb(156, 163, 175)",
                    borderWidth: 1,
                    borderRadius: 4,
                    barPercentage: 0.6,
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
                    cornerRadius: 6,
                    displayColors: true,
                    usePointStyle: true,
                    titleFont: {
                        size: 11,
                    },
                    bodyFont: {
                        size: 11,
                    },
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false,
                    },
                    ticks: {
                        padding: 8,
                        font: {
                            size: 10,
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
                        },
                    },
                },
            },
        },
    });
});

// Update chart when period changes
watch(selectedPeriod, (newPeriod) => {
    // In a real app, you would fetch new data based on the selected period
    console.log("Period changed to:", newPeriod);
    // For demo purposes, we'll just update the chart title
    if (chartInstance) {
        chartInstance.options.plugins.title = {
            display: true,
            text: `Water Usage - ${newPeriod}`,
        };
        chartInstance.update();
    }
});
</script>

<style scoped>
/* Custom shadow utilities */
.shadow-xs {
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

/* Custom chart styles */
canvas {
    border-radius: 0.5rem;
}
</style>
