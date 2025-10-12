<!-- Components/Admin/Dashboard/DashboardSection.vue -->
<template>
    <div
        :class="`bg-white rounded-xl border border-gray-200 ${
            padding ? 'p-6' : 'p-4'
        }`"
    >
        <!-- Header -->
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <h2 class="text-lg font-semibold text-gray-900">
                    {{ title }}
                </h2>
                <span
                    v-if="badge"
                    class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full"
                >
                    {{ badge }} new
                </span>
            </div>
            <Link
                v-if="action"
                :href="action.href"
                class="text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors flex items-center gap-1"
            >
                {{ action.label }}
                <ChevronRight class="w-4 h-4" />
            </Link>
        </div>

        <!-- Content -->
        <slot />
    </div>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
import { ChevronRight } from "lucide-vue-next";

defineProps({
    title: String,
    action: Object,
    badge: [String, Number],
    padding: {
        type: Boolean,
        default: true,
    },
});
</script>
