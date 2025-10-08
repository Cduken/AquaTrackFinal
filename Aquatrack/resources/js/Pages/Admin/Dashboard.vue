//Pages/Admin/Dashboard.vue
<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import StatCard from "@/Components/Admin/Dashboard/StatCard.vue";
import ActivityItem from "@/Components/Admin/Dashboard/ActivityItem.vue";
import { ref, onMounted, computed, watch, h, resolveComponent } from "vue";
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
import { History, ChevronRight, RefreshCw, AlertCircle } from "lucide-vue-next";

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
        description: `Resolution: ${dashboardData.value.resolutionRate}%`,
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
    if (!areaChart.value || dashboardData.value.consumptionByArea.length === 0)
        return;

    destroyChart("area");

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
};

const initializeReportStatusChart = () => {
    if (!reportStatusChart.value) return;

    destroyChart("reportStatus");

    state.value.chartInstances.reportStatus = new Chart(
        reportStatusChart.value,
        createDoughnutChartConfig(
            reportStatusData.value.labels,
            reportStatusData.value.data,
            reportStatusData.value.colors
        )
    );
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

const DashboardSection = {
    props: {
        title: String,
        action: Object,
        badge: [String, Number],
        padding: {
            type: Boolean,
            default: true,
        },
    },
    setup(props, { slots }) {
        return () =>
            h(
                "div",
                {
                    class: `bg-white rounded-xl border border-gray-200 ${
                        props.padding ? "p-6" : "p-4"
                    }`,
                },
                [
                    // Header
                    h(
                        "div",
                        { class: "flex items-center justify-between mb-4" },
                        [
                            h("div", { class: "flex items-center gap-2" }, [
                                h(
                                    "h2",
                                    {
                                        class: "text-lg font-semibold text-gray-900",
                                    },
                                    props.title
                                ),
                                props.badge &&
                                    h(
                                        "span",
                                        {
                                            class: "text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full",
                                        },
                                        `${props.badge} new`
                                    ),
                            ]),
                            props.action &&
                                h(
                                    Link,
                                    {
                                        href: props.action.href,
                                        class: "text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors flex items-center gap-1",
                                    },
                                    [
                                        props.action.label,
                                        h(ChevronRight, { class: "w-4 h-4" }),
                                    ]
                                ),
                        ]
                    ),
                    // Content
                    slots.default?.(),
                ]
            );
    },
};

// Report Item Component
const ReportItem = {
    props: {
        report: Object,
    },
    setup(props) {
        return () =>
            h(
                "div",
                {
                    class: "flex items-center justify-between p-4 border border-gray-100 rounded-lg hover:bg-gray-50 transition-colors group",
                },
                [
                    // Customer Info
                    h(
                        "div",
                        { class: "flex items-center gap-4 flex-1 min-w-0" },
                        [
                            h("div", { class: "flex-shrink-0" }, [
                                h(
                                    "div",
                                    {
                                        class: "w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center group-hover:bg-blue-100 transition-colors",
                                    },
                                    [
                                        h(
                                            "span",
                                            {
                                                class: "text-sm font-semibold text-blue-600",
                                            },
                                            props.report.customer?.charAt(0) ||
                                                "U"
                                        ),
                                    ]
                                ),
                            ]),
                            h("div", { class: "min-w-0 flex-1" }, [
                                h(
                                    "p",
                                    {
                                        class: "text-sm font-medium text-gray-900 truncate",
                                    },
                                    props.report.customer || "Unknown Customer"
                                ),
                                h(
                                    "p",
                                    {
                                        class: "text-xs text-gray-500 truncate mt-1",
                                    },
                                    `${props.report.type} • ${props.report.zone}`
                                ),
                            ]),
                        ]
                    ),
                    // Status & Priority
                    h(
                        "div",
                        { class: "flex items-center gap-3 flex-shrink-0" },
                        [
                            h(
                                "span",
                                {
                                    class: [
                                        "px-3 py-1 text-xs font-medium rounded-full border",
                                        getStatusConfig(props.report.status)
                                            .color,
                                    ],
                                },
                                getStatusConfig(props.report.status).text
                            ),
                            props.report.priority &&
                                h(
                                    "span",
                                    {
                                        class: [
                                            "px-2 py-1 text-xs font-medium rounded-full",
                                            getPriorityColor(
                                                props.report.priority
                                            ),
                                        ],
                                    },
                                    props.report.priority
                                ),
                        ]
                    ),
                ]
            );
    },
};

// Status Item Component
const StatusItem = {
    props: {
        status: String,
        count: Number,
        color: String,
    },
    setup(props) {
        return () =>
            h("div", { class: "flex items-center justify-between text-sm" }, [
                h("div", { class: "flex items-center gap-2" }, [
                    h("div", {
                        class: "w-3 h-3 rounded-full",
                        style: { backgroundColor: props.color },
                    }),
                    h("span", { class: "text-gray-600" }, props.status),
                ]),
                h(
                    "span",
                    { class: "font-semibold text-gray-900" },
                    props.count
                ),
            ]);
    },
};

// Customer Item Component
const CustomerItem = {
    props: {
        customer: Object,
    },
    setup(props) {
        return () =>
            h("div", { class: "flex items-center gap-3 group" }, [
                h(
                    "div",
                    {
                        class: "w-8 h-8 bg-blue-50 rounded-full flex items-center justify-center group-hover:bg-blue-100 transition-colors",
                    },
                    [
                        h(
                            "span",
                            { class: "text-sm font-medium text-blue-600" },
                            props.customer.name?.charAt(0) || "U"
                        ),
                    ]
                ),
                h("div", { class: "flex-1 min-w-0" }, [
                    h(
                        "p",
                        { class: "text-sm font-medium text-gray-900 truncate" },
                        props.customer.name || "Unknown User"
                    ),
                    h(
                        "p",
                        { class: "text-xs text-gray-500 truncate" },
                        props.customer.email || "No email"
                    ),
                ]),
                h(
                    "div",
                    { class: "text-xs text-gray-400 whitespace-nowrap" },
                    props.customer.joined || "Recently"
                ),
            ]);
    },
};

// Empty State Component
const EmptyState = {
    props: {
        icon: String,
        title: String,
        description: String,
        size: {
            type: String,
            default: "md",
        },
        class: String,
    },
    setup(props) {
        const sizes = {
            sm: "py-6",
            md: "py-12",
            lg: "py-16",
        };

        const iconSize = {
            sm: "w-8 h-8",
            md: "w-12 h-12",
            lg: "w-16 h-16",
        };

        return () =>
            h(
                "div",
                {
                    class: `text-center text-gray-500 ${sizes[props.size]} ${
                        props.class
                    }`,
                },
                [
                    props.icon &&
                        h(resolveComponent(props.icon), {
                            class: `mx-auto text-gray-300 mb-3 ${
                                iconSize[props.size]
                            }`,
                        }),
                    h("p", { class: "text-sm" }, props.title),
                    props.description &&
                        h(
                            "p",
                            { class: "text-xs text-gray-400 mt-1" },
                            props.description
                        ),
                ]
            );
    },
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
                    <div class="h-48 mb-4">
                        <canvas ref="reportStatusChart"></canvas>
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

                <!-- Reports by Zone -->
                <DashboardSection title="Reports by Zone">
                    <div class="h-48">
                        <canvas ref="areaChart"></canvas>
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
