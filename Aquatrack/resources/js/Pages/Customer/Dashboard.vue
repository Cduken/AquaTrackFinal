<template>
    <CustomerLayout>
        <div class="min-h-screen bg-gray-50/40">
            <!-- Header -->
            <div class=" ">
                <div class="py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-xl font-semibold text-gray-900">
                                Dashboard Overview
                            </h1>
                            <p class="text-gray-500 text-sm mt-0.5">
                                Welcome back,
                                <span class="font-medium text-gray-700">{{ customerName }}</span>
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">
                                Billing Period
                            </p>
                            <p class="text-gray-800 font-medium text-sm">
                                {{ new Date().toLocaleDateString("en-US", { month: "long", year: "numeric" }) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Overdue Alert -->
            <div v-if="overdueBills > 0" class="bg-red-500 px-4 py-3 my-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-white rounded-full"></div>
                        <span class="text-white text-sm font-medium">
                            {{ overdueBills }} overdue bill{{ overdueBills > 1 ? 's' : '' }} • ₱{{ overdueAmount.toFixed(2) }}
                        </span>
                    </div>
                    <button
                        @click="$inertia.visit(route('customer.usage'))"
                        class="text-white hover:text-gray-100 text-sm font-medium flex items-center space-x-1 transition-colors"
                    >
                        <span>View</span>
                        <ArrowRight class="w-3 h-3" />
                    </button>
                </div>
            </div>

            <!-- Main Content -->
            <div class="">
                <!-- Key Metrics Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <!-- Monthly Usage -->
                    <div class="bg-white rounded-lg border border-gray-100 p-4 shadow-xs hover:shadow-sm transition-all duration-200">
                        <div class="flex items-start justify-between mb-3">
                            <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                                <Droplets class="w-5 h-5 text-blue-600" />
                            </div>
                            <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded">
                                m³
                            </span>
                        </div>
                        <h3 class="text-gray-500 text-xs font-medium uppercase tracking-wide mb-1">
                            Monthly Usage
                        </h3>
                        <p class="text-2xl font-semibold text-gray-900 mb-2">
                            {{ monthlyUsage }}
                        </p>
                        <div class="flex items-center">
                            <div class="flex items-center text-xs font-medium px-2 py-1 rounded-full"
                                :class="monthlyUsage > 15 ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-600'">
                                <TrendingUp v-if="monthlyUsage > 15" class="w-3 h-3 mr-1" />
                                <TrendingDown v-else class="w-3 h-3 mr-1" />
                                {{ monthlyUsage > 15 ? 'Above average' : 'Below average' }}
                            </div>
                        </div>
                    </div>

                    <!-- Current Bill -->
                    <div class="bg-white rounded-lg border border-gray-100 p-4 shadow-xs hover:shadow-sm transition-all duration-200">
                        <div class="flex items-start justify-between mb-3">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                                :class="{
                                    'bg-red-50': currentBillStatus === 'Overdue',
                                    'bg-yellow-50': currentBillStatus === 'Due Soon',
                                    'bg-green-50': currentBillStatus === 'Paid',
                                    'bg-gray-50': currentBillStatus === 'No Bills',
                                }">
                                <Receipt class="w-5 h-5"
                                    :class="{
                                        'text-red-600': currentBillStatus === 'Overdue',
                                        'text-yellow-600': currentBillStatus === 'Due Soon',
                                        'text-green-600': currentBillStatus === 'Paid',
                                        'text-gray-600': currentBillStatus === 'No Bills',
                                    }" />
                            </div>
                            <span class="text-xs font-medium px-2 py-1 rounded"
                                :class="{
                                    'text-red-700 bg-red-50': currentBillStatus === 'Overdue',
                                    'text-yellow-700 bg-yellow-50': currentBillStatus === 'Due Soon',
                                    'text-green-700 bg-green-50': currentBillStatus === 'Paid',
                                    'text-gray-700 bg-gray-50': currentBillStatus === 'No Bills',
                                }">
                                {{ currentBillStatusText }}
                            </span>
                        </div>
                        <h3 class="text-gray-500 text-xs font-medium uppercase tracking-wide mb-1">
                            Current Bill
                        </h3>
                        <p class="text-2xl font-semibold text-gray-900 mb-3">
                            ₱{{ currentBill.toFixed(2) }}
                        </p>
                        <div class="w-full bg-gray-100 rounded-full h-1.5">
                            <div class="h-1.5 rounded-full transition-all duration-500"
                                :class="{
                                    'bg-red-500': currentBillStatus === 'Overdue',
                                    'bg-yellow-500': currentBillStatus === 'Due Soon',
                                    'bg-green-500': currentBillStatus === 'Paid',
                                    'bg-gray-400': currentBillStatus === 'No Bills',
                                }"
                                :style="`width: ${Math.min((currentBill / 1000) * 100, 100)}%`">
                            </div>
                        </div>
                    </div>

                    <!-- Announcements -->
                    <div class="bg-white rounded-lg border border-gray-100 p-4 shadow-xs hover:shadow-sm transition-all duration-200">
                        <div class="flex items-start justify-between mb-3">
                            <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center">
                                <Bell class="w-5 h-5 text-purple-600" />
                            </div>
                            <div v-if="announcements > 0" class="w-2 h-2 bg-purple-500 rounded-full"></div>
                        </div>
                        <h3 class="text-gray-500 text-xs font-medium uppercase tracking-wide mb-1">
                            Announcements
                        </h3>
                        <p class="text-2xl font-semibold text-gray-900 mb-1">
                            {{ announcements }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ announcements === 0 ? 'No new updates' : 'Unread notifications' }}
                        </p>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">
                    <!-- Consumption Chart -->
                    <div class="bg-white rounded-lg border border-gray-100 p-5 shadow-xs">
                        <div class="flex items-center justify-between mb-5">
                            <h2 class="text-base font-semibold text-gray-900">Water Consumption</h2>
                            <div class="flex items-center space-x-3 text-xs text-gray-500">
                                <div class="flex items-center">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                                    <span class="font-medium">Your Usage</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-2 h-2 bg-gray-400 rounded-full mr-2"></div>
                                    <span class="font-medium">Area Average</span>
                                </div>
                            </div>
                        </div>
                        <div class="h-64">
                            <canvas ref="waterChart"></canvas>
                        </div>
                    </div>

                    <!-- Meter Readings -->
                    <div class="bg-white rounded-lg border border-gray-100 p-5 shadow-xs">
                        <h2 class="text-base font-semibold text-gray-900 mb-5">Meter Readings</h2>
                        <div class="h-64">
                            <canvas ref="yieldChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Stats Summary -->
                <div class="bg-white rounded-lg border border-gray-100 p-5 shadow-xs">
                    <h2 class="text-base font-semibold text-gray-900 mb-4">Usage Summary</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 bg-blue-50/50 rounded-lg border border-blue-100">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-sm font-medium text-blue-700">Year Total</h3>
                                <Calendar class="w-4 h-4 text-blue-600" />
                            </div>
                            <p class="text-xl font-semibold text-blue-900">{{ yearlyConsumption }} m³</p>
                            <p class="text-xs text-blue-600 mt-1">Total consumption</p>
                        </div>

                        <div class="p-4 bg-gray-50/50 rounded-lg border border-gray-100">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-sm font-medium text-gray-700">Area Average</h3>
                                <Users class="w-4 h-4 text-gray-600" />
                            </div>
                            <p class="text-xl font-semibold text-gray-900">{{ areaAverage }} m³</p>
                            <p class="text-xs text-gray-600 mt-1">Area average</p>
                        </div>

                        <div class="p-4 rounded-lg border"
                            :class="yearlyConsumption > areaAverage
                                ? 'bg-red-50/50 border-red-100'
                                : 'bg-green-50/50 border-green-100'">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-sm font-medium"
                                    :class="yearlyConsumption > areaAverage ? 'text-red-700' : 'text-green-700'">
                                    Your Status
                                </h3>
                                <TrendingUp v-if="yearlyConsumption > areaAverage" class="w-4 h-4 text-red-600" />
                                <TrendingDown v-else class="w-4 h-4 text-green-600" />
                            </div>
                            <p class="text-xl font-semibold"
                                :class="yearlyConsumption > areaAverage ? 'text-red-900' : 'text-green-900'">
                                {{ yearlyConsumption > areaAverage ? 'Above' : 'Below' }} Average
                            </p>
                            <p class="text-xs mt-1"
                                :class="yearlyConsumption > areaAverage ? 'text-red-600' : 'text-green-600'">
                                Compared to area
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
    Receipt,
    Bell,
    ArrowRight,
    TrendingUp,
    TrendingDown,
    Calendar,
    Users,
} from "lucide-vue-next";

const page = usePage();
const announcements = page.props.announcements ?? 0;
const monthlyUsage = page.props.monthlyUsage ?? 0;
const currentBill = page.props.currentBill ?? 0;
const yearlyConsumption = page.props.yearlyConsumption ?? 0;
const areaAverage = page.props.areaAverage ?? 0;
const chartData = page.props.chartData ?? [];
const customerName = page.props.customerName ?? "Customer";
const billingData = page.props.billingData ?? [];

const waterChart = ref(null);
const yieldChart = ref(null);

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
    const currentBillData = billingData.find((bill) => bill.is_current) || billingData[0];
    if (!currentBillData) return "No Bills";
    return currentBillData.status || "Pending";
});

const currentBillStatusText = computed(() => {
    const currentBillData = billingData.find((bill) => bill.is_current) || billingData[0];
    if (!currentBillData) return "No bills";

    switch (currentBillStatus.value) {
        case "Overdue":
            return "Overdue";
        case "Due Soon":
            if (currentBillData.due_date) {
                const dueDate = new Date(currentBillData.due_date);
                const today = new Date();
                const daysUntilDue = Math.ceil((dueDate - today) / (1000 * 60 * 60 * 24));
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

onMounted(() => {
    const labels = chartData.map((item) => item.month);
    const consumptionData = chartData.map((item) => item.consumption);
    const readingData = chartData.map((item) => item.reading);
    const areaAvgData = Array(consumptionData.length).fill(areaAverage / 12);

    // Water Consumption Chart
    if (waterChart.value) {
        new Chart(waterChart.value, {
            type: "line",
            data: {
                labels: labels,
                datasets: [
                    {
                        label: "Your Consumption",
                        data: consumptionData,
                        borderColor: "rgb(59, 130, 246)",
                        backgroundColor: "rgba(59, 130, 246, 0.05)",
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: "rgb(59, 130, 246)",
                        pointBorderColor: "#fff",
                        pointBorderWidth: 2,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                    },
                    {
                        label: "Area Average",
                        data: areaAvgData,
                        borderColor: "rgb(156, 163, 175)",
                        backgroundColor: "transparent",
                        borderWidth: 1,
                        tension: 0.4,
                        borderDash: [5, 5],
                        pointRadius: 0,
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
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        grid: {
                            color: "rgba(0, 0, 0, 0.03)",
                        },
                        ticks: {
                            font: {
                                size: 11,
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
                            },
                        },
                    },
                },
            },
        });
    }

    // Meter Readings Chart
    if (yieldChart.value) {
        new Chart(yieldChart.value, {
            type: "bar",
            data: {
                labels: labels,
                datasets: [
                    {
                        label: "Meter Readings",
                        data: readingData,
                        backgroundColor: "rgba(99, 102, 241, 0.1)",
                        borderColor: "rgb(99, 102, 241)",
                        borderWidth: 1,
                        borderRadius: 4,
                        barThickness: "flex",
                        maxBarThickness: 32,
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
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        grid: {
                            color: "rgba(0, 0, 0, 0.03)",
                        },
                        ticks: {
                            font: {
                                size: 11,
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
                            },
                        },
                    },
                },
            },
        });
    }
});
</script>

<style scoped>
.shadow-xs {
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}
</style>
