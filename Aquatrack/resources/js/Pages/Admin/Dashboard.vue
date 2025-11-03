<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import StatCard from "@/Components/Admin/Dashboard/StatCard.vue";

import EmptyState from "@/Components/Admin/Dashboard/EmptyState.vue";
import ReportItem from "@/Components/Admin/Dashboard/ReportItem.vue";
import StatusItem from "@/Components/Admin/Dashboard/StatusItem.vue";
import CustomerItem from "@/Components/Admin/Dashboard/CustomerItem.vue";
import Pagination from "@/Components/Pagination.vue";
import { ref, onMounted, computed, watch } from "vue";
import { usePage, Link, router } from "@inertiajs/vue3";
import Chart from "chart.js/auto";
import {
    formatTimeAgo,
    getCurrentMonthName,
    getCurrentYear,
} from "@/utils/formatters";
import {
    createWaterChartConfig,
    createDoughnutChartConfig,
} from "@/utils/chartConfig";

// Reactive state
const state = ref({
    isLoading: false,
    chartInstances: {
        consumption: null,
        area: null,
        reportStatus: null,
    },
});

// Refs
const consumptionChart = ref(null);
const areaChart = ref(null);
const reportStatusChart = ref(null);
const page = usePage();

const STATUS_CONFIG = {
    pending: {
        color: "text-yellow-600 bg-yellow-50 border-yellow-200",
        text: "Pending",
    },
    in_progress: {
        color: "text-blue-600 bg-blue-50 border-blue-200",
        text: "In Progress",
    },
    resolved: {
        color: "text-green-600 bg-green-50 border-green-200",
        text: "Resolved",
    },
};

const PRIORITY_COLORS = {
    high: "text-red-600 bg-red-50",
    medium: "text-yellow-600 bg-yellow-50",
    low: "text-blue-600 bg-blue-50",
};

// Computed properties
const dashboardData = computed(() => ({
    totalUsers: page.props.total_users ?? 0,
    totalStaffs: page.props.total_staffs ?? 0,
    totalReports: page.props.total_reports ?? 0,
    totalCustomers: page.props.total_customers ?? 0,
    monthlyConsumption: page.props.monthly_consumption ?? Array(12).fill(0),
    currentMonthConsumption: page.props.current_month_consumption ?? 0,
    averageConsumption: page.props.average_consumption ?? 0,
    peakUsage: page.props.peak_usage ?? 0,
    growthPercentage: page.props.growth_percentage ?? 0,
    resolutionRate: page.props.resolution_rate ?? 0,
    recentActivities: page.props.recent_activities ?? [],
    recentCustomers: page.props.recent_customers ?? [],
    reportStats: page.props.report_stats ?? {
        pending: 0,
        in_progress: 0,
        resolved: 0,
        total: 0,
    },
    consumptionByArea: page.props.consumption_by_area ?? [],
    recentReports: page.props.recent_reports ?? [],
    canViewActivityLog:
        page.props.auth?.user?.can?.("view-activity-log") ?? false,
}));

const statCards = computed(() => [
    {
        title: "Total Users",
        value: dashboardData.value.totalUsers.toLocaleString(),
        description: "All system users",
        icon: "users",
        iconBgColor: "bg-blue-50",
        iconColor: "text-blue-600",
        change: "+12%",
        changeType: "positive",
    },
    {
        title: "Active Staff",
        value: dashboardData.value.totalStaffs.toLocaleString(),
        description: "System staff members",
        icon: "staff",
        iconBgColor: "bg-green-50",
        iconColor: "text-green-600",
        change: "+5%",
        changeType: "positive",
    },
    {
        title: "Customers",
        value: dashboardData.value.totalCustomers.toLocaleString(),
        description: "Water service customers",
        icon: "customers",
        iconBgColor: "bg-purple-50",
        iconColor: "text-purple-600",
        change: "+8%",
        changeType: "positive",
    },
    {
        title: "Reports",
        value: dashboardData.value.totalReports.toLocaleString(),
        description: "Water issue reports",
        icon: "reports",
        iconBgColor: "bg-amber-50",
        iconColor: "text-amber-600",
        change: "-3%",
        changeType: "negative",
    },
]);

const reportStatusData = computed(() => ({
    labels: ["Pending", "In Progress", "Resolved"],
    data: [
        dashboardData.value.reportStats.pending || 0,
        dashboardData.value.reportStats.in_progress || 0,
        dashboardData.value.reportStats.resolved || 0,
    ],
    colors: ["#F59E0B", "#3B82F6", "#10B981"],
}));

const hasReportStatusData = computed(() => {
    return reportStatusData.value.data.some((count) => count > 0);
});

const hasAreaData = computed(() => {
    return (
        dashboardData.value.consumptionByArea.length > 0 &&
        dashboardData.value.consumptionByArea.some(
            (item) => item.consumption > 0
        )
    );
});

// Methods

const getStatusConfig = (status) => {
    return STATUS_CONFIG[status] || STATUS_CONFIG.pending;
};

const getPriorityColor = (priority) => {
    return PRIORITY_COLORS[priority] || PRIORITY_COLORS.low;
};

const safeRecentReports = computed(() => {
    return dashboardData.value.recentReports || [];
});

// Chart management
const initializeCharts = () => {
    initializeConsumptionChart();
    initializeAreaChart();
    initializeReportStatusChart();
};

const initializeConsumptionChart = () => {
    if (!consumptionChart.value) return;
    destroyChart("consumption");
    const config = createWaterChartConfig(
        dashboardData.value.monthlyConsumption
    );
    state.value.chartInstances.consumption = new Chart(
        consumptionChart.value,
        config
    );
};

const createPieChartConfig = (labels, data, colors) => {
    return {
        type: "pie",
        data: {
            labels: labels,
            datasets: [
                {
                    data: data,
                    backgroundColor: colors,
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
                    padding: 12,
                    titleFont: {
                        size: 13,
                        weight: "600",
                    },
                    bodyFont: {
                        size: 12,
                    },
                    cornerRadius: 6,
                    displayColors: true,
                    boxWidth: 12,
                    boxHeight: 12,
                    boxPadding: 6,
                },
            },
        },
    };
};

const initializeAreaChart = () => {
    if (!areaChart.value) return;
    destroyChart("area");
    if (hasAreaData.value) {
        state.value.chartInstances.area = new Chart(
            areaChart.value,
            createPieChartConfig(
                dashboardData.value.consumptionByArea.map((item) => item.name),
                dashboardData.value.consumptionByArea.map(
                    (item) => item.consumption
                ),
                ["#3B82F6", "#10B981", "#F59E0B", "#EF4444", "#8B5CF6"]
            )
        );
    }
};

const initializeReportStatusChart = () => {
    if (!reportStatusChart.value) return;
    destroyChart("reportStatus");
    if (hasReportStatusData.value) {
        state.value.chartInstances.reportStatus = new Chart(
            reportStatusChart.value,
            createPieChartConfig(
                reportStatusData.value.labels,
                reportStatusData.value.data,
                reportStatusData.value.colors
            )
        );
    }
};

const destroyChart = (chartName) => {
    if (state.value.chartInstances[chartName]) {
        state.value.chartInstances[chartName].destroy();
        state.value.chartInstances[chartName] = null;
    }
};

const destroyAllCharts = () => {
    Object.keys(state.value.chartInstances).forEach((chartName) => {
        destroyChart(chartName);
    });
};

// Lifecycle
onMounted(() => {
    setTimeout(initializeCharts, 500);
});

onMounted(() => {
    return destroyAllCharts;
});

// Watchers
watch(
    () => [
        dashboardData.value.monthlyConsumption,
        dashboardData.value.consumptionByArea,
        dashboardData.value.reportStats,
    ],
    () => {
        setTimeout(initializeCharts, 100);
    },
    { deep: true }
);
</script>

<template>
    <AdminLayout>
        <div class="max-w-[1600px] mx-auto px-5 py-5">
            <!-- Stats Grid -->
            <div
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8"
            >
                <StatCard
                    v-for="stat in statCards"
                    :key="stat.title"
                    :title="stat.title"
                    :value="stat.value"
                    :description="stat.description"
                    :icon="stat.icon"
                    :icon-bg-color="stat.iconBgColor"
                    :icon-color="stat.iconColor"
                    :change="stat.change"
                    :change-type="stat.changeType"
                />
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column - Main Charts (2 columns wide) -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Water Consumption Chart -->
                    <div
                        class="bg-white rounded-lg border border-gray-300 shadow-sm overflow-hidden"
                    >
                        <div class="px-6 py-5 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2
                                        class="text-lg font-semibold text-gray-900"
                                    >
                                        Water Consumption
                                    </h2>
                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ getCurrentYear() }} Overview
                                    </p>
                                </div>
                                <div
                                    class="flex items-center text-sm text-gray-600"
                                >
                                    <div
                                        class="w-3 h-3 rounded-full bg-indigo-500 mr-2"
                                    ></div>
                                    <span>Consumption (m³)</span>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="h-80">
                                <canvas ref="consumptionChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Reports -->
                    <div
                        class="bg-white rounded-lg border border-gray-300 shadow-sm overflow-hidden flex flex-col"
                        style="height: 500px; min-height: 630px"
                    >
                        <div
                            class="px-6 py-5 border-b border-gray-200 flex-shrink-0"
                        >
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-semibold text-gray-900">
                                    Recent Reports
                                </h2>
                                <Link
                                    :href="route('admin.reports')"
                                    class="text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors"
                                >
                                    View All →
                                </Link>
                            </div>
                        </div>
                        <div class="flex-1 overflow-hidden p-6">
                            <div class="h-full overflow-y-auto">
                                <div class="space-y-3 min-h-full">
                                    <ReportItem
                                        v-for="report in safeRecentReports"
                                        :key="report.id"
                                        :report="report"
                                    />
                                    <EmptyState
                                        v-if="safeRecentReports.length === 0"
                                        icon="AlertCircle"
                                        title="No reports found"
                                        description="Reports will appear here once submitted"
                                        class="h-full flex items-center justify-center"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Sidebar (1 column wide) -->
                <div class="space-y-6">
                    <!-- Report Status -->
                    <div
                        class="bg-white rounded-lg border border-gray-300 shadow-sm overflow-hidden"
                    >
                        <div class="px-6 py-5 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">
                                Report Status
                            </h2>
                        </div>
                        <div class="p-6">
                            <div
                                class="h-56 mb-6 relative flex items-center justify-center"
                            >
                                <canvas ref="reportStatusChart"></canvas>
                                <EmptyState
                                    v-if="!hasReportStatusData"
                                    title="No status data"
                                    description="Report status data will appear here"
                                    size="sm"
                                    class="absolute inset-0 flex items-center justify-center bg-white"
                                />
                            </div>
                            <div
                                class="space-y-3 pt-4 border-t border-gray-200"
                            >
                                <StatusItem
                                    v-for="(
                                        status, index
                                    ) in reportStatusData.labels"
                                    :key="status"
                                    :status="status"
                                    :count="reportStatusData.data[index]"
                                    :color="reportStatusData.colors[index]"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Reports by Zone -->
                    <div
                        class="bg-white rounded-lg border border-gray-300 shadow-sm overflow-hidden"
                    >
                        <div class="px-6 py-5 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">
                                Reports by Zone
                            </h2>
                        </div>
                        <div class="p-6">
                            <div
                                class="h-56 relative flex items-center justify-center"
                            >
                                <canvas ref="areaChart"></canvas>
                                <EmptyState
                                    v-if="!hasAreaData"
                                    title="No zone data"
                                    description="Zone consumption data will appear here"
                                    size="sm"
                                    class="absolute inset-0 flex items-center justify-center bg-white"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Recent Customers -->
                    <div
                        class="bg-white rounded-lg border border-gray-300 shadow-sm overflow-hidden flex flex-col"
                        style="height: 260px; min-height: 200px"
                    >
                        <div
                            class="px-6 py-5 border-b border-gray-200 flex-shrink-0"
                        >
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-semibold text-gray-900">
                                    Recent Customers
                                </h2>
                                <span
                                    v-if="dashboardData.recentCustomers.length"
                                    class="text-xs font-medium text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full"
                                >
                                    {{ dashboardData.recentCustomers.length }}
                                </span>
                            </div>
                        </div>
                        <div class="flex-1 overflow-hidden p-6">
                            <div class="h-full overflow-y-auto scrollbar-hide">
                                <div class="space-y-4">
                                    <CustomerItem
                                        v-for="customer in dashboardData.recentCustomers"
                                        :key="customer.email"
                                        :customer="customer"
                                    />
                                    <EmptyState
                                        v-if="
                                            dashboardData.recentCustomers
                                                .length === 0
                                        "
                                        title="No recent customers"
                                        size="sm"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scope>
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
</style>
