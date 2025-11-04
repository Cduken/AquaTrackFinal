<template>
    <CustomerLayout>
        <div class="bg-gray-50 px-2 py-1">
            <!-- Header -->
            <div class="mb-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-semibold text-gray-900">
                            Dashboard Overview
                        </h1>
                        <p class="text-gray-600 mt-1">
                            Welcome back,
                            <span class="font-semibold text-gray-800">{{
                                customerName
                            }}</span>
                        </p>
                    </div>
                    <div class="text-right">
                        <div
                            class="bg-white px-4 py-2 rounded-lg border border-gray-200 shadow-xs"
                        >
                            <p
                                class="text-xs text-gray-500 font-medium uppercase tracking-wide"
                            >
                                Billing Period
                            </p>
                            <p class="text-sm font-semibold text-gray-800">
                                {{
                                    new Date().toLocaleDateString("en-US", {
                                        month: "long",
                                        year: "numeric",
                                    })
                                }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Overdue Alert -->
            <div v-if="overdueBills > 0" class="mb-6">
                <div
                    class="bg-gradient-to-r from-red-500 to-red-600 rounded-xl p-4 shadow-sm"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-2 h-2 bg-white rounded-full animate-pulse"
                            ></div>
                            <div>
                                <p class="text-white font-semibold text-sm">
                                    {{ overdueBills }} overdue bill{{
                                        overdueBills > 1 ? "s" : ""
                                    }}
                                </p>
                                <p class="text-red-100 text-xs">
                                    Total amount due: ₱{{
                                        overdueAmount.toFixed(2)
                                    }}
                                </p>
                            </div>
                        </div>
                        <button
                            @click="$inertia.visit(route('customer.usage'))"
                            class="bg-white/20 hover:bg-white/30 text-white px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 flex items-center gap-2"
                        >
                            <span>View Bills</span>
                            <ArrowRight class="w-3 h-3" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="space-y-6">
                <!-- Key Metrics Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Monthly Usage -->
                    <div class="bg-white rounded-lg p-4 border border-gray-300">
                        <div class="flex items-center justify-between mb-3">
                            <div
                                class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center"
                            >
                                <Droplets class="w-5 h-5 text-blue-600" />
                            </div>
                            <div
                                class="bg-blue-50 text-blue-700 px-2 py-1 rounded text-xs font-medium"
                            >
                                m³
                            </div>
                        </div>
                        <h3
                            class="text-gray-500 text-xs uppercase tracking-wide mb-1"
                        >
                            Monthly Usage
                        </h3>
                        <p class="text-2xl font-semibold text-gray-900">
                            {{ monthlyUsage }}
                        </p>
                    </div>

                    <!-- Current Bill -->
                    <div class="bg-white rounded-lg p-4 border border-gray-300">
                        <div class="flex items-center justify-between mb-3">
                            <div
                                class="w-10 h-10 rounded-lg flex items-center justify-center"
                                :class="{
                                    'bg-red-100':
                                        currentBillStatus === 'Overdue',
                                    'bg-yellow-100':
                                        currentBillStatus === 'Due Soon',
                                    'bg-green-100':
                                        currentBillStatus === 'Paid',
                                    'bg-gray-100':
                                        currentBillStatus === 'No Bills',
                                }"
                            >
                              <div
                                class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center"
                            >
                                <CreditCard
                                    class="w-5 h-10 text-green-700"
                                    :class="{
                                        'text-red-600':
                                            currentBillStatus === 'Overdue',
                                        'text-yellow-600':
                                            currentBillStatus === 'Due Soon',
                                        'text-green-600':
                                            currentBillStatus === 'Paid',
                                        'text-gray-600':
                                            currentBillStatus === 'No Bills',
                                    }"
                                />
                              </div>
                            </div>
                            <span
                                class="px-2 py-1 rounded text-xs font-medium"
                                :class="{
                                    'bg-red-50 text-red-700':
                                        currentBillStatus === 'Overdue',
                                    'bg-yellow-50 text-yellow-700':
                                        currentBillStatus === 'Due Soon',
                                    'bg-green-50 text-green-700':
                                        currentBillStatus === 'Paid',
                                    'bg-gray-50 text-gray-700':
                                        currentBillStatus === 'No Bills',
                                }"
                            >
                                {{ currentBillStatusText }}
                            </span>
                        </div>
                        <h3
                            class="text-gray-500 text-xs uppercase tracking-wide mb-1"
                        >
                            Current Bill
                        </h3>
                        <p class="text-2xl font-semibold text-gray-900">
                            ₱{{ currentBill.toFixed(2) }}
                        </p>
                    </div>

                    <!-- Announcements -->
                    <div class="bg-white rounded-lg p-4 border border-gray-300">
                        <div class="flex items-center justify-between mb-3">
                            <div
                                class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center"
                            >
                                <Bell class="w-5 h-5 text-purple-600" />
                            </div>
                            <div
                                v-if="announcements > 0"
                                class="w-2 h-2 bg-purple-500 rounded-full animate-pulse"
                            ></div>
                        </div>
                        <h3
                            class="text-gray-500 text-xs uppercase tracking-wide mb-1"
                        >
                            Announcements
                        </h3>
                        <p class="text-2xl font-semibold text-gray-900">
                            {{ announcements }}
                        </p>
                    </div>
                </div>

                <!-- Analytics Section -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column - Charts -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Meter Readings Chart -->
                        <div
                            class="bg-white rounded-lg p-5 border border-gray-200"
                        >
                            <div class="mb-5">
                                <h2
                                    class="text-base font-semibold text-gray-900"
                                >
                                    Meter Readings History
                                </h2>
                                <p class="text-gray-600 text-sm">
                                    Monthly consumption tracking
                                </p>
                            </div>
                            <div class="h-72">
                                <canvas ref="yieldChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Reports Pie Chart -->
                    <div class="bg-white rounded-lg p-5 border border-gray-200">
                        <div class="mb-5">
                            <h2 class="text-base font-semibold text-gray-900">
                                Reports Status
                            </h2>
                            <p class="text-gray-600 text-sm">
                                Your reports distribution
                            </p>
                        </div>

                        <!-- Pie Chart Container -->
                        <div class="flex justify-center mb-5">
                            <div class="relative w-40 h-40">
                                <canvas ref="reportsChart"></canvas>
                            </div>
                        </div>

                        <!-- Status Legend - Minimalist -->
                        <div class="space-y-2">
                            <div
                                v-for="(
                                    status, index
                                ) in reportStatusData.labels"
                                :key="status"
                                class="flex items-center justify-between"
                            >
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-2.5 h-2.5 rounded-full"
                                        :style="`background-color: ${reportStatusData.colors[index]}`"
                                    ></div>
                                    <span class="text-sm text-gray-700">{{
                                        status
                                    }}</span>
                                </div>
                                <span
                                    class="text-sm font-semibold text-gray-900"
                                >
                                    {{ reportStatusData.data[index] }}
                                </span>
                            </div>
                        </div>

                        <!-- No Reports State -->
                        <div
                            v-if="!hasReportStatusData"
                            class="text-center py-6"
                        >
                            <div
                                class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2"
                            >
                                <FileText class="w-6 h-6 text-gray-400" />
                            </div>
                            <p class="text-gray-500 text-sm">
                                No reports submitted
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>

<script setup>
import CustomerLayout from "@/Layouts/CustomerLayout.vue";
import { ref, onMounted, computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import Chart from "chart.js/auto";
import {
    Droplets,
    Bell,
    ArrowRight,
    FileText,
    CreditCard,
} from "lucide-vue-next";

const page = usePage();

// Debug: Check what data is being received
console.log("All page props:", page.props);
console.log("Report stats:", page.props.reportStats);

const announcements = page.props.announcements ?? 0;
const monthlyUsage = page.props.monthlyUsage ?? 0;
const currentBill = page.props.currentBill ?? 0;
const yearlyConsumption = page.props.yearlyConsumption ?? 0;
const areaAverage = page.props.areaAverage ?? 0;
const chartData = page.props.chartData ?? [];
const customerName = page.props.customerName ?? "Customer";
const billingData = page.props.billingData ?? [];
const reportStats = page.props.reportStats ?? {
    pending: 0,
    in_progress: 0,
    resolved: 0,
    total: 0,
};

const yieldChart = ref(null);
const reportsChart = ref(null);

// Compute billing statistics
const overdueBills = computed(() => {
    return billingData.filter((bill) => bill.status === "Overdue").length;
});

const overdueAmount = computed(() => {
    return billingData
        .filter((bill) => bill.status === "Overdue")
        .reduce((total, bill) => total + parseFloat(bill.amount || 0), 0);
});

const currentBillStatus = computed(() => {
    const currentBillData =
        billingData.find((bill) => bill.is_current) || billingData[0];
    if (!currentBillData) return "No Bills";
    return currentBillData.status || "Pending";
});

const currentBillStatusText = computed(() => {
    const currentBillData =
        billingData.find((bill) => bill.is_current) || billingData[0];
    if (!currentBillData) return "No bills";

    switch (currentBillStatus.value) {
        case "Overdue":
            return "Overdue";
        case "Due Soon":
            if (currentBillData.due_date) {
                const dueDate = new Date(currentBillData.due_date);
                const today = new Date();
                const daysUntilDue = Math.ceil(
                    (dueDate - today) / (1000 * 60 * 60 * 24)
                );
                if (daysUntilDue > 0) {
                    return `Due in ${daysUntilDue}d`;
                }
            }
            return "Due Soon";
        case "Paid":
            return "Paid";
        default:
            return "Pending";
    }
});

// Reports data computations for pie chart
const totalReports = computed(() => {
    return reportStats.total || 0;
});

const reportStatusData = computed(() => ({
    labels: ["Pending", "In Progress", "Resolved"],
    data: [
        reportStats.pending || 0,
        reportStats.in_progress || 0,
        reportStats.resolved || 0,
    ],
    colors: ["#F59E0B", "#3B82F6", "#10B981"],
}));

const hasReportStatusData = computed(() => {
    return reportStatusData.value.data.some((count) => count > 0);
});

const calculatePercentage = (count) => {
    return totalReports.value > 0
        ? Math.round((count / totalReports.value) * 100)
        : 0;
};

onMounted(() => {
    const labels = chartData.map((item) => item.month);
    const readingData = chartData.map((item) => item.reading);

    // Meter Readings Chart
    if (yieldChart.value && readingData.length > 0) {
        new Chart(yieldChart.value, {
            type: "bar",
            data: {
                labels: labels,
                datasets: [
                    {
                        label: "Meter Readings",
                        data: readingData,
                        backgroundColor: "rgba(99, 102, 241, 0.15)",
                        borderColor: "rgb(99, 102, 241)",
                        borderWidth: 2,
                        borderRadius: 6,
                        barThickness: 28,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        backgroundColor: "rgba(255, 255, 255, 0.95)",
                        titleColor: "#1f2937",
                        bodyColor: "#374151",
                        borderColor: "#e5e7eb",
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: true,
                        usePointStyle: true,
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: "rgba(0, 0, 0, 0.05)",
                        },
                        ticks: {
                            font: {
                                size: 11,
                                family: "'Inter', sans-serif",
                            },
                            callback: function (value) {
                                return value + " m³";
                            },
                        },
                    },
                    x: {
                        grid: {
                            display: false,
                        },
                        ticks: {
                            font: {
                                size: 11,
                                family: "'Inter', sans-serif",
                            },
                        },
                    },
                },
            },
        });
    }

    // Reports Pie Chart
    if (reportsChart.value && hasReportStatusData.value) {
        new Chart(reportsChart.value, {
            type: "pie",
            data: {
                labels: reportStatusData.value.labels,
                datasets: [
                    {
                        data: reportStatusData.value.data,
                        backgroundColor: reportStatusData.value.colors,
                        borderColor: "#ffffff",
                        borderWidth: 2,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        backgroundColor: "rgba(0, 0, 0, 0.8)",
                        padding: 8,
                        titleFont: {
                            size: 12,
                        },
                        bodyFont: {
                            size: 11,
                        },
                        cornerRadius: 4,
                        displayColors: false,
                        callbacks: {
                            label: function (context) {
                                // Show count like in the image: "7" instead of "Pending: 7"
                                return context.raw.toString();
                            },
                        },
                    },
                },
                cutout: "0%",
            },
        });
    }
});
</script>

<style scoped>
.shadow-xs {
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}

/* Smooth transitions for interactive elements */
* {
    transition-property: color, background-color, border-color, transform,
        box-shadow;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}

/* Custom hover effects */
.group:hover {
    transform: translateY(-2px);
}
</style>
