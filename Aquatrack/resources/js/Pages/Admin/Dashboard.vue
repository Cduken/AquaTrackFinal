//Pages/Admin/Dashboard.vue
<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import StatCard from "@/Components/Admin/Dashboard/StatCard.vue";
import ActivityItem from "@/Components/Admin/Dashboard/ActivityItem.vue";
import DashboardSection from "@/Components/Admin/Dashboard/DashboardSection.vue";
import EmptyState from "@/Components/Admin/Dashboard/EmptyState.vue";
import ReportItem from "@/Components/Admin/Dashboard/ReportItem.vue";
import StatusItem from "@/Components/Admin/Dashboard/StatusItem.vue";
import CustomerItem from "@/Components/Admin/Dashboard/CustomerItem.vue";
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
import { RefreshCw } from "lucide-vue-next";

// Reactive state
const state = ref({
    isLoading: false,
    timeFilter: "month",
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

// Constants
const FILTER_OPTIONS = [
    { value: "day", label: "Today" },
    { value: "week", label: "This Week" },
    { value: "month", label: "This Month" },
    { value: "year", label: "This Year" },
];

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
const refreshData = async () => {
    state.value.isLoading = true;

    // Show loading animation for 1.5 seconds
    await new Promise((resolve) => setTimeout(resolve, 1500));

    router.reload();
    state.value.isLoading = false;
};

const handleFilterChange = (filter) => {
    state.value.timeFilter = filter;
    refreshData();
};

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

const initializeAreaChart = () => {
    if (!areaChart.value) return;

    destroyChart("area");

    if (hasAreaData.value) {
        state.value.chartInstances.area = new Chart(areaChart.value, {
            ...createDoughnutChartConfig(
                dashboardData.value.consumptionByArea.map((item) => item.name),
                dashboardData.value.consumptionByArea.map(
                    (item) => item.consumption
                ),
                ["#3B82F6", "#10B981", "#F59E0B", "#EF4444", "#8B5CF6"]
            ),
            options: {
                ...createDoughnutChartConfig().options,
                plugins: {
                    ...createDoughnutChartConfig().options.plugins,
                    tooltip: {
                        callbacks: {
                            label: (context) => {
                                const total =
                                    dashboardData.value.consumptionByArea.reduce(
                                        (sum, item) => sum + item.consumption,
                                        0
                                    );
                                const percentage = (
                                    (context.parsed / total) *
                                    100
                                ).toFixed(1);
                                return `${context.label}: ${context.parsed} reports (${percentage}%)`;
                            },
                        },
                    },
                },
            },
        });
    }
};

const initializeReportStatusChart = () => {
    if (!reportStatusChart.value) return;

    destroyChart("reportStatus");

    if (hasReportStatusData.value) {
        state.value.chartInstances.reportStatus = new Chart(
            reportStatusChart.value,
            createDoughnutChartConfig(
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
        <!-- Header Section -->
        <div class="mb-8">
            <div
                class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4"
            >
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        Dashboard Overview
                    </h1>
                    <p class="text-gray-500 mt-1">
                        Welcome back! Here's what's happening with your water
                        management system.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <!-- Filter Buttons -->
                    <div
                        class="flex items-center bg-white border border-gray-200 rounded-lg p-1"
                    >
                        <button
                            v-for="option in FILTER_OPTIONS"
                            :key="option.value"
                            @click="handleFilterChange(option.value)"
                            :class="[
                                'px-3 py-1.5 text-sm font-medium rounded-md transition-all',
                                state.timeFilter === option.value
                                    ? 'bg-blue-50 text-blue-600 border border-blue-200'
                                    : 'text-gray-600 hover:text-gray-900',
                            ]"
                        >
                            {{ option.label }}
                        </button>
                    </div>

                    <button
                        @click="refreshData"
                        :disabled="state.isLoading"
                        class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <RefreshCw
                            class="w-4 h-4 transition-transform duration-1500"
                            :class="{ 'animate-spin': state.isLoading }"
                        />
                        {{ state.isLoading ? "Refreshing..." : "Refresh" }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
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
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
            <!-- Left Column - Main Charts -->
            <div class="xl:col-span-2 space-y-6">
                <!-- Water Consumption Chart -->
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <div
                        class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4"
                    >
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">
                                Water Consumption
                            </h2>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ getCurrentYear() }} Overview
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <div
                                class="flex items-center text-sm text-gray-600"
                            >
                                <div
                                    class="w-2 h-2 rounded-full bg-indigo-500 mr-2"
                                ></div>
                                Consumption (m³)
                            </div>
                        </div>
                    </div>
                    <div class="h-80">
                        <canvas ref="consumptionChart"></canvas>
                    </div>
                </div>

                <!-- Recent Reports -->
                <DashboardSection
                    title="Recent Reports"
                    :action="{
                        label: 'View All',
                        href: route('admin.reports'),
                    }"
                >
                    <div class="space-y-3">
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
                        />
                    </div>
                </DashboardSection>
            </div>

            <!-- Right Column - Sidebar -->
            <div class="space-y-6">
                <!-- Report Status -->
                <DashboardSection title="Report Status">
                    <div class="h-48 mb-4 relative">
                        <canvas ref="reportStatusChart"></canvas>
                        <EmptyState
                            v-if="!hasReportStatusData"
                            title="No status data"
                            description="Report status data will appear here"
                            size="sm"
                            class="absolute inset-0 flex items-center justify-center bg-white"
                        />
                    </div>
                    <div class="space-y-3">
                        <StatusItem
                            v-for="(status, index) in reportStatusData.labels"
                            :key="status"
                            :status="status"
                            :count="reportStatusData.data[index]"
                            :color="reportStatusData.colors[index]"
                        />
                    </div>
                </DashboardSection>

                 <!-- Reports by Zone -->
                <DashboardSection title="Reports by Zone">
                    <div class="h-48 relative">
                        <canvas ref="areaChart"></canvas>
                        <EmptyState
                            v-if="!hasAreaData"
                            title="No zone data"
                            description="Zone consumption data will appear here"
                            size="sm"
                            class="absolute inset-0 flex items-center justify-center bg-white"
                        />
                    </div>
                </DashboardSection>

                <!-- Recent Customers -->
                <DashboardSection
                    title="Recent Customers"
                    :badge="dashboardData.recentCustomers.length"
                >
                    <div class="space-y-4">
                        <CustomerItem
                            v-for="customer in dashboardData.recentCustomers"
                            :key="customer.email"
                            :customer="customer"
                        />
                        <EmptyState
                            v-if="dashboardData.recentCustomers.length === 0"
                            title="No recent customers"
                            size="sm"
                        />
                    </div>
                </DashboardSection>


            </div>
        </div>

        <!-- Recent Activity Section -->
        <DashboardSection
            title="Recent Activity"
            :action="
                dashboardData.canViewActivityLog
                    ? { label: 'View All', href: route('admin.activity-logs') }
                    : null
            "
            class="bg-white rounded-xl border border-gray-200"
            :padding="false"
        >
            <div class="divide-y divide-gray-100">
                <ActivityItem
                    v-for="activity in dashboardData.recentActivities"
                    :key="activity.id"
                    :activity="activity"
                />
                <EmptyState
                    v-if="dashboardData.recentActivities.length === 0"
                    icon="History"
                    title="No recent activities"
                    description="Activities will appear here as they occur"
                    class="p-8"
                />
            </div>
        </DashboardSection>
    </AdminLayout>
</template>
