<template>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-medium text-gray-700">
                {{ title }}
            </h3>
            <div :class="`p-1 rounded ${iconBgColor} ${iconColor}`">
                <component :is="iconComponent" class="w-4 h-4" />
            </div>
        </div>
        <div class="flex items-end">
            <p class="text-2xl font-bold text-gray-900 mr-2">
                {{ value }}
            </p>
            <span v-if="unit" class="text-sm text-gray-500 mb-1">{{
                unit
            }}</span>
        </div>
        <p class="text-xs text-gray-500 mt-2">
            {{ description }}
        </p>
        <p
            v-if="trend !== null"
            :class="`text-xs mt-2 font-medium ${
                trend >= 0 ? 'text-green-600' : 'text-red-600'
            }`"
        >
            {{ trend >= 0 ? "+" : "" }}{{ trend }}% vs last month
        </p>
    </div>
</template>

<script setup>
import {
    TrendingUp,
    TrendingDown,
    Minus,
    Zap,
    CheckCircle2,
    Gauge,
    BarChart3,
} from "lucide-vue-next";
import { computed } from "vue";

const props = defineProps({
    title: String,
    value: [String, Number],
    unit: String,
    description: String,
    trend: Number,
    icon: String,
    iconBgColor: {
        type: String,
        default: "bg-gray-100",
    },
    iconColor: {
        type: String,
        default: "text-gray-500",
    },
});

const iconMap = {
    trendUp: TrendingUp,
    trendDown: TrendingDown,
    average: BarChart3,
    peak: Zap,
    check: CheckCircle2,
    gauge: Gauge,
    minus: Minus,
};

const iconComponent = computed(() => iconMap[props.icon] || BarChart3);
</script>
