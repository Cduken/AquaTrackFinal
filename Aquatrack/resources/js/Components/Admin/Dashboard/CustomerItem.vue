<!-- Components/Admin/Dashboard/CustomerItem.vue -->
<template>
    <div class="flex items-center gap-3 group">
        <div class="w-8 h-8 bg-blue-50 rounded-full flex items-center justify-center group-hover:bg-blue-100 transition-colors">
            <span class="text-sm font-medium text-blue-600">
                {{ customer.name?.charAt(0) || 'U' }}
            </span>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-900 truncate">
                {{ customer.name || 'Unknown User' }}
            </p>
            <p class="text-xs text-gray-500 truncate">
                {{ customer.email || 'No email' }}
            </p>
        </div>
        <div class="text-xs text-gray-400 whitespace-nowrap">
            {{ customer.joined || 'Recently' }}
        </div>
    </div>
</template>

<script setup>
defineProps({
    customer: {
        type: Object,
        required: true
    }
})
</script>
