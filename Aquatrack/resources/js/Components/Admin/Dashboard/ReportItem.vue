<!-- Components/Admin/Dashboard/ReportItem.vue -->
<template>
    <div class="flex items-center justify-between p-4 border border-gray-100 rounded-lg hover:bg-gray-50 transition-colors group">
        <!-- Customer Info -->
        <div class="flex items-center gap-4 flex-1 min-w-0">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                    <span class="text-sm font-semibold text-blue-600">
                        {{ report.customer?.charAt(0) || 'U' }}
                    </span>
                </div>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-medium text-gray-900 truncate">
                    {{ report.customer || 'Unknown Customer' }}
                </p>
                <p class="text-xs text-gray-500 truncate mt-1">
                    {{ report.type }} • {{ report.zone }}
                </p>
            </div>
        </div>

        <!-- Status & Priority -->
        <div class="flex items-center gap-3 flex-shrink-0">
            <span
                :class="[
                    'px-3 py-1 text-xs font-medium rounded-full border',
                    statusConfig.color
                ]"
            >
                {{ statusConfig.text }}
            </span>
            <span
                v-if="report.priority"
                :class="[
                    'px-2 py-1 text-xs font-medium rounded-full',
                    priorityColor
                ]"
            >
                {{ report.priority }}
            </span>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    report: {
        type: Object,
        required: true
    }
})

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
}

const PRIORITY_COLORS = {
    high: "text-red-600 bg-red-50",
    medium: "text-yellow-600 bg-yellow-50",
    low: "text-blue-600 bg-blue-50",
}

const statusConfig = computed(() => {
    return STATUS_CONFIG[props.report.status] || STATUS_CONFIG.pending
})

const priorityColor = computed(() => {
    return PRIORITY_COLORS[props.report.priority] || PRIORITY_COLORS.low
})
</script>
