<template>
    <div
        class="p-4 hover:bg-gray-50 transition-colors flex items-start gap-4 group"
    >
        <div
            :class="`p-2.5 rounded-lg ${activityConfig.bg} shadow-sm group-hover:shadow transition-all duration-200`"
        >
            <component
                :is="activityConfig.icon"
                class="w-5 h-5"
                :class="activityConfig.text"
            />
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-800 truncate">
                {{ activityConfig.title }}
            </p>
            <p class="text-xs text-gray-500 mt-1 truncate">
                {{ activity.description }}
                <span
                    v-if="activity.properties?.tracking_code"
                    class="font-medium text-gray-700"
                >
                    ({{ activity.properties.tracking_code }})
                </span>
            </p>
            <p class="text-xs text-gray-400 mt-1 truncate">
                <span v-if="activity.causer_name"
                    >By {{ activity.causer_name }}</span
                >
                <span v-else>By System</span>
                <span
                    v-if="activity.properties?.old_status"
                    class="ml-2 text-gray-600"
                >
                    Status: {{ activity.properties.old_status }} →
                    {{ activity.properties.new_status }}
                </span>
            </p>
        </div>
        <div class="text-xs text-gray-400 whitespace-nowrap">
            {{ formatTimeAgo(activity.created_at) }}
        </div>
    </div>
</template>

<script setup>
import {
    PlusCircle,
    Edit,
    Trash2,
    LogIn,
    LogOut,
    ShieldCheck,
    ShieldAlert,
    Sparkles,
    Flag,
    RefreshCw,
    User,
    Settings,
} from "lucide-vue-next";
import { computed } from "vue";
import { formatTimeAgo } from "@/utils/formatters";

const props = defineProps({
    activity: {
        type: Object,
        required: true,
    },
});

const activityConfigMap = {
    created: {
        icon: PlusCircle,
        bg: "bg-blue-50",
        text: "text-blue-500",
        title: "New record created",
    },
    updated: {
        icon: Edit,
        bg: "bg-green-50",
        text: "text-green-500",
        title: "Record updated",
    },
    deleted: {
        icon: Trash2,
        bg: "bg-red-50",
        text: "text-red-500",
        title: "Record deleted",
    },
    logged_in: {
        icon: LogIn,
        bg: "bg-purple-50",
        text: "text-purple-500",
        title: "User login",
    },
    logged_out: {
        icon: LogOut,
        bg: "bg-gray-50",
        text: "text-gray-500",
        title: "User logout",
    },
    verification_success: {
        icon: ShieldCheck,
        bg: "bg-teal-50",
        text: "text-teal-500",
        title: "Verification passed",
    },
    verification_failed: {
        icon: ShieldAlert,
        bg: "bg-amber-50",
        text: "text-amber-500",
        title: "Verification failed",
    },
    report_created: {
        icon: Flag,
        bg: "bg-amber-50",
        text: "text-amber-500",
        title: "New Report Submitted",
    },
    report_status_changed: {
        icon: RefreshCw,
        bg: "bg-blue-50",
        text: "text-blue-500",
        title: "Report Status Updated",
    },
    user_created: {
        icon: User,
        bg: "bg-indigo-50",
        text: "text-indigo-500",
        title: "User Created",
    },
    settings_updated: {
        icon: Settings,
        bg: "bg-orange-50",
        text: "text-orange-500",
        title: "Settings Updated",
    },
    default: {
        icon: Sparkles,
        bg: "bg-indigo-50",
        text: "text-indigo-500",
        title: "System activity",
    },
};

const activityConfig = computed(() => {
    const activity = props.activity;

    if (activity.subject_type === "App\\Models\\Report") {
        return activity.event === "report_created"
            ? activityConfigMap.report_created
            : activityConfigMap.report_status_changed;
    }

    return activityConfigMap[activity.event] || activityConfigMap.default;
});
</script>
