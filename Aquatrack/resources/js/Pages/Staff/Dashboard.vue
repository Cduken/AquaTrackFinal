//Pages/Staff/Dashboard.vue
<template>
    <StaffLayout>
        <div class="min-h-screen p-4">
            <!-- Compact Header -->
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            Good {{ timeOfDay }},
                            {{ dashboardData.staffName || "Staff" }}
                        </h1>
                        <p class="text-sm text-gray-600 mt-0.5">
                            {{ currentDate }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <div
                            class="px-3 py-1.5 bg-white rounded-lg shadow-sm border border-gray-200"
                        >
                            <div class="flex items-center gap-2">
                                <div
                                    class="w-2 h-2 bg-green-500 rounded-full animate-pulse"
                                ></div>
                                <span class="text-xs font-medium text-gray-700"
                                    >Active</span
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Compact Metrics Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
                <!-- Today's Readings -->
                <div
                    class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow"
                >
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2 bg-blue-50 rounded-lg">
                            <Droplets class="w-4 h-4 text-blue-600" />
                        </div>
                        <span
                            class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full"
                        >
                            Today
                        </span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900">
                        {{ dashboardData.todaysReadings || 0 }}
                    </h3>
                    <p class="text-xs text-gray-600 mt-1">Meter Readings</p>
                    <div class="mt-3 flex items-center gap-2">
                        <div class="flex-1 bg-gray-100 rounded-full h-1">
                            <div
                                class="bg-blue-500 h-1 rounded-full transition-all duration-500"
                                :style="{
                                    width: `${Math.min(
                                        ((dashboardData.todaysReadings || 0) /
                                            dashboardData.dailyTarget) *
                                            100,
                                        100
                                    )}%`,
                                }"
                            ></div>
                        </div>
                        <span class="text-xs text-gray-500">
                            {{
                                Math.round(
                                    ((dashboardData.todaysReadings || 0) /
                                        dashboardData.dailyTarget) *
                                        100
                                )
                            }}%
                        </span>
                    </div>
                </div>

                <!-- Weekly -->
                <div
                    class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow"
                >
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2 bg-purple-50 rounded-lg">
                            <CalendarDays class="w-4 h-4 text-purple-600" />
                        </div>
                        <span
                            class="text-xs font-medium text-purple-600 bg-purple-50 px-2 py-0.5 rounded-full"
                        >
                            Week
                        </span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900">
                        {{ dashboardData.weeklyReadings || 0 }}
                    </h3>
                    <p class="text-xs text-gray-600 mt-1">Total Readings</p>
                    <div
                        class="mt-3 flex items-center gap-1"
                        :class="
                            dashboardData.weeklyTrend >= 0
                                ? 'text-green-600'
                                : 'text-red-600'
                        "
                    >
                        <TrendingUp
                            v-if="dashboardData.weeklyTrend >= 0"
                            class="w-3 h-3"
                        />
                        <TrendingDown v-else class="w-3 h-3" />
                        <span class="text-xs font-medium">
                            {{ dashboardData.weeklyTrend >= 0 ? "+" : ""
                            }}{{ dashboardData.weeklyTrend }}% vs last week
                        </span>
                    </div>
                </div>

                <!-- Monthly -->
                <div
                    class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow"
                >
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2 bg-emerald-50 rounded-lg">
                            <Calendar class="w-4 h-4 text-emerald-600" />
                        </div>
                        <span
                            class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full"
                        >
                            Month
                        </span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900">
                        {{ dashboardData.monthlyReadings || 0 }}
                    </h3>
                    <p class="text-xs text-gray-600 mt-1">Monthly Total</p>
                    <div
                        class="mt-3 flex items-center gap-1"
                        :class="
                            dashboardData.monthlyTrend >= 0
                                ? 'text-green-600'
                                : 'text-red-600'
                        "
                    >
                        <TrendingUp
                            v-if="dashboardData.monthlyTrend >= 0"
                            class="w-3 h-3"
                        />
                        <TrendingDown v-else class="w-3 h-3" />
                        <span class="text-xs font-medium">
                            {{ dashboardData.monthlyTrend >= 0 ? "+" : ""
                            }}{{ dashboardData.monthlyTrend }}% vs last month
                        </span>
                    </div>
                </div>

                <!-- Performance -->
                <div
                    class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 shadow-sm text-white hover:shadow-md transition-shadow"
                >
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2 bg-white/20 rounded-lg">
                            <Target class="w-4 h-4" />
                        </div>
                        <span
                            class="text-xs font-medium bg-white/20 px-2 py-0.5 rounded-full"
                        >
                            Goal
                        </span>
                    </div>
                    <h3 class="text-2xl font-bold">
                        {{
                            Math.round(
                                ((dashboardData.todaysReadings || 0) /
                                    dashboardData.dailyTarget) *
                                    100
                            )
                        }}%
                    </h3>
                    <p class="text-xs text-blue-100 mt-1">Daily Target</p>
                    <p class="text-xs text-blue-200 mt-2">
                        {{ dashboardData.dailyTarget }} readings goal
                    </p>
                </div>
            </div>

            <!-- Analytics and Status Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
                <!-- Weekly Trend Chart Card -->
                <div
                    class="bg-white rounded-xl p-4 shadow-sm border border-gray-100"
                >
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold text-gray-900">
                            Weekly Trend
                        </h3>
                        <Activity class="w-4 h-4 text-gray-400" />
                    </div>
                    <div class="flex items-end justify-between h-20 gap-1">
                        <div
                            v-for="(
                                day, index
                            ) in dashboardData.weeklyChartData || []"
                            :key="index"
                            class="flex-1 bg-blue-100 rounded-t transition-all hover:bg-blue-200"
                            :style="{ height: `${day.percentage}%` }"
                            :title="`${day.day}: ${day.readings} readings`"
                        ></div>
                    </div>
                    <div
                        class="flex items-center justify-between mt-2 text-xs text-gray-500"
                    >
                        <span
                            v-for="day in dashboardData.weeklyChartData || []"
                            :key="day.day"
                        >
                            {{ day.day }}
                        </span>
                    </div>
                </div>

                <!-- Status Overview - Compact -->
                <div
                    class="bg-white rounded-xl p-4 shadow-sm border border-gray-100"
                >
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold text-gray-900">
                            Status Overview
                        </h3>
                        <PieChart class="w-4 h-4 text-gray-400" />
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div
                                    class="w-2 h-2 bg-green-500 rounded-full"
                                ></div>
                                <span class="text-xs text-gray-600">Paid</span>
                            </div>
                            <span class="text-sm font-semibold text-gray-900">
                                {{ dashboardData.statusCounts?.paid || 0 }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div
                                    class="w-2 h-2 bg-yellow-500 rounded-full"
                                ></div>
                                <span class="text-xs text-gray-600"
                                    >Pending</span
                                >
                            </div>
                            <span class="text-sm font-semibold text-gray-900">
                                {{ dashboardData.statusCounts?.pending || 0 }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div
                                    class="w-2 h-2 bg-red-500 rounded-full"
                                ></div>
                                <span class="text-xs text-gray-600"
                                    >Overdue</span
                                >
                            </div>
                            <span class="text-sm font-semibold text-gray-900">
                                {{ dashboardData.statusCounts?.overdue || 0 }}
                            </span>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-gray-100">
                        <div
                            class="flex gap-1 h-2 rounded-full overflow-hidden"
                        >
                            <div
                                class="bg-green-500"
                                :style="{
                                    width: `${getStatusPercentage('paid')}%`,
                                }"
                            ></div>
                            <div
                                class="bg-yellow-500"
                                :style="{
                                    width: `${getStatusPercentage('pending')}%`,
                                }"
                            ></div>
                            <div
                                class="bg-red-500"
                                :style="{
                                    width: `${getStatusPercentage('overdue')}%`,
                                }"
                            ></div>
                        </div>
                    </div>
                </div>

                <!-- Performance Metrics -->
                <div
                    class="bg-white rounded-xl p-4 shadow-sm border border-gray-100"
                >
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold text-gray-900">
                            Performance
                        </h3>
                        <Zap class="w-4 h-4 text-gray-400" />
                    </div>
                    <div class="space-y-3">
                        <div
                            class="flex items-center justify-between p-2 bg-blue-50 rounded-lg"
                        >
                            <div class="flex items-center gap-2">
                                <CheckCircle2 class="w-4 h-4 text-blue-600" />
                                <span class="text-xs text-gray-700"
                                    >Completion Rate</span
                                >
                            </div>
                            <span class="text-sm font-bold text-blue-600">
                                {{ dashboardData.completionRate || 0 }}%
                            </span>
                        </div>
                        <div
                            class="flex items-center justify-between p-2 bg-green-50 rounded-lg"
                        >
                            <div class="flex items-center gap-2">
                                <Clock class="w-4 h-4 text-green-600" />
                                <span class="text-xs text-gray-700"
                                    >Avg. Reading Time</span
                                >
                            </div>
                            <span class="text-sm font-bold text-green-600">
                                {{ dashboardData.avgReadingTime || "0m" }}
                            </span>
                        </div>
                        <div
                            class="flex items-center justify-between p-2 bg-purple-50 rounded-lg"
                        >
                            <div class="flex items-center gap-2">
                                <Award class="w-4 h-4 text-purple-600" />
                                <span class="text-xs text-gray-700"
                                    >Efficiency Score</span
                                >
                            </div>
                            <span class="text-sm font-bold text-purple-600">
                                {{ dashboardData.efficiencyScore || 0 }}/10
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity - Compact -->
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden"
            >
                <div
                    class="px-4 py-3 border-b border-gray-100 flex items-center justify-between"
                >
                    <div class="flex items-center gap-2">
                        <div class="p-1.5 bg-blue-50 rounded-lg">
                            <ListChecks class="w-4 h-4 text-blue-600" />
                        </div>
                        <h2 class="text-sm font-semibold text-gray-900">
                            Recent Activity
                        </h2>
                    </div>
                    <span
                        class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full"
                    >
                        {{ dashboardData.recentActivity?.length || 0 }}
                        activities
                    </span>
                </div>

                <div class="p-4">
                    <div class="space-y-2">
                        <div
                            v-for="activity in dashboardData.recentActivity ||
                            []"
                            :key="activity.id"
                            class="group p-3 rounded-lg border border-gray-100 hover:border-blue-200 hover:bg-blue-50/30 transition-all"
                        >
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0">
                                    <div
                                        class="w-10 h-10 rounded-lg flex items-center justify-center overflow-hidden border border-gray-200 bg-gradient-to-br from-blue-500 to-blue-600"
                                    >
                                        <img
                                            v-if="activity.customer_avatar"
                                            :src="activity.customer_avatar"
                                            :alt="activity.customer_name"
                                            class="w-full h-full object-cover"
                                        />
                                        <User
                                            v-else
                                            class="w-5 h-5 text-white"
                                        />
                                    </div>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div
                                        class="flex items-center justify-between mb-1"
                                    >
                                        <span
                                            class="font-semibold text-sm text-gray-900 truncate"
                                        >
                                            {{
                                                activity.customer_name ||
                                                "Unknown"
                                            }}
                                        </span>
                                        <span
                                            :class="
                                                getStatusBadgeClass(
                                                    activity.status
                                                )
                                            "
                                            class="text-xs px-2 py-0.5 rounded-full font-medium whitespace-nowrap ml-2"
                                        >
                                            {{ activity.status }}
                                        </span>
                                    </div>

                                    <div
                                        class="flex items-center gap-3 text-xs text-gray-600"
                                    >
                                        <span class="flex items-center gap-1">
                                            <Gauge class="w-3 h-3" />
                                            {{ activity.consumption || 0 }} m³
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <Hash class="w-3 h-3" />
                                            {{ activity.serial_number }}
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <Clock class="w-3 h-3" />
                                            {{ activity.reading_time }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div
                            v-if="
                                !dashboardData.recentActivity ||
                                dashboardData.recentActivity.length === 0
                            "
                            class="text-center py-8"
                        >
                            <div
                                class="flex flex-col items-center text-gray-400"
                            >
                                <div class="p-3 bg-gray-50 rounded-xl mb-3">
                                    <Inbox class="w-6 h-6" />
                                </div>
                                <p class="text-sm font-medium text-gray-500">
                                    No recent activity
                                </p>
                                <p class="text-xs text-gray-400 mt-1">
                                    Activities will appear here
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </StaffLayout>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import StaffLayout from "@/Layouts/StaffLayout.vue";
import {
    Droplets,
    CalendarDays,
    Calendar,
    Target,
    TrendingUp,
    TrendingDown,
    BarChart3,
    Activity,
    PieChart,
    Zap,
    CheckCircle2,
    Clock,
    ListChecks,
    User,
    Gauge,
    Hash,
    Inbox,
    Award,
} from "lucide-vue-next";

const dashboardData = ref({
    dailyTarget: 50, // Default target
});

const timeOfDay = computed(() => {
    const hour = new Date().getHours();
    if (hour < 12) return "morning";
    if (hour < 18) return "afternoon";
    return "evening";
});

const currentDate = computed(() => {
    return new Date().toLocaleDateString("en-US", {
        weekday: "long",
        month: "short",
        day: "numeric",
        year: "numeric",
    });
});

const getStatusPercentage = (statusType) => {
    const total = Object.values(dashboardData.value.statusCounts || {}).reduce(
        (a, b) => a + b,
        0
    );
    if (total === 0) return 0;
    return (
        ((dashboardData.value.statusCounts?.[statusType] || 0) / total) * 100
    );
};

const getStatusBadgeClass = (status) => {
    const classes = {
        Paid: "bg-green-100 text-green-700",
        Pending: "bg-yellow-100 text-yellow-700",
        Overdue: "bg-red-100 text-red-700",
    };
    return classes[status] || "bg-gray-100 text-gray-700";
};

const fetchDashboardData = async () => {
    try {
        const response = await axios.get(route("staff.dashboard.data"));
        dashboardData.value = response.data;
    } catch (error) {
        console.error("Error fetching dashboard data:", error);
        dashboardData.value = {
            todaysReadings: 0,
            weeklyReadings: 0,
            monthlyReadings: 0,
            recentActivity: [],
            completedToday: 0,
            pendingCount: 0,
            statusCounts: { paid: 0, pending: 0, overdue: 0 },
            dailyTarget: 50,
            weeklyTrend: 0,
            monthlyTrend: 0,
            completionRate: 0,
            avgReadingTime: "0m",
            efficiencyScore: 0,
        };
    }
};

onMounted(() => {
    fetchDashboardData();
});
</script>

<style scoped>
@keyframes pulse {
    0%,
    100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
