<!-- Components/Admin/Dashboard/StatusItem.vue -->
<template>
    <div class="flex items-center justify-between text-sm">
        <div class="flex items-center gap-2">
            <div
                class="w-3 h-3 rounded-full"
                :style="{ backgroundColor: color }"
            ></div>
            <span class="text-gray-600">{{ status }}</span>
        </div>
        <span class="font-semibold text-gray-900">{{ count }}</span>
    </div>
</template>

<script setup>
defineProps({
    status: {
        type: String,
        required: true
    },
    count: {
        type: Number,
        required: true
    },
    color: {
        type: String,
        required: true
    }
})
</script>
