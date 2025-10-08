<template>
    <div
        v-if="Object.keys(errors).length > 0"
        class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm"
    >
        <div class="flex items-center mb-2">
            <v-icon name="hi-exclamation-triangle" class="w-5 h-5 mr-2" />
            <span class="font-medium">Please fix the following errors:</span>
        </div>
        <ul class="list-disc list-inside mt-2 space-y-1">
            <li v-for="(error, key) in errors" :key="key">
                {{ error }}
            </li>
        </ul>
    </div>
</template>

<script setup>
defineProps({
    errors: {
        type: Object,
        default: () => ({})
    }
});
</script>
