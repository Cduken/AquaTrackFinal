<template>
    <div>
        <!-- Network Status Indicator -->
        <div
            v-if="!isOnline"
            class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg"
        >
            <div class="flex items-center text-yellow-800">
                <v-icon name="hi-exclamation" class="w-5 h-5 mr-2" />
                <span class="font-medium">You are currently offline</span>
            </div>
            <p class="text-sm text-yellow-700 mt-1">
                Your report will be saved locally and automatically submitted
                when you're back online.
            </p>
        </div>

        <!-- Offline Reports Queue Indicator -->
        <div
            v-if="offlineReportsCount > 0"
            class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg"
        >
            <div class="flex items-center justify-between">
                <div class="flex items-center text-blue-800">
                    <v-icon name="hi-database" class="w-5 h-5 mr-2" />
                    <span class="font-medium"
                        >{{ offlineReportsCount }} report(s) waiting to
                        sync</span
                    >
                </div>
                <button
                    @click="$emit('manual-sync')"
                    :disabled="!isOnline || isSyncing"
                    class="text-sm bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 disabled:opacity-50 flex items-center"
                >
                    <v-icon
                        v-if="isSyncing"
                        name="bi-arrow-repeat"
                        class="animate-spin mr-1 w-3 h-3"
                    />
                    {{ isSyncing ? "Syncing..." : "Sync Now" }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    isOnline: Boolean,
    isSyncing: Boolean,
    offlineReportsCount: Number,
});

defineEmits(["manual-sync"]);
</script>
