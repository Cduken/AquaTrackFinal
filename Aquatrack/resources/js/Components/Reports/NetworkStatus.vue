//NetworkStatus.vue
<template>
    <div class="space-y-3 mb-6">
        <!-- Network Status Indicator -->
        <div
            v-if="!isOnline"
            class="p-4 bg-yellow-500/20 border border-yellow-500/30 rounded-xl backdrop-blur-sm"
        >
            <div class="flex items-center text-yellow-300">
                <div
                    class="w-5 h-5 mr-3 bg-yellow-500 rounded-full flex items-center justify-center"
                >
                    <svg
                        class="w-3 h-3 text-white"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"
                        />
                    </svg>
                </div>
                <span class="font-medium">You are currently offline</span>
            </div>
            <p class="text-sm text-yellow-200/80 mt-2 ml-8">
                Your report will be saved locally and automatically submitted
                when you're back online.
            </p>
        </div>

        <!-- Offline Reports Queue Indicator -->
        <div
            v-if="offlineReportsCount > 0"
            class="p-4 bg-blue-500/20 border border-blue-500/30 rounded-xl backdrop-blur-sm"
        >
            <div class="flex items-center justify-between">
                <div class="flex items-center text-blue-300">
                    <div
                        class="w-5 h-5 mr-3 bg-blue-500 rounded-full flex items-center justify-center"
                    >
                        <svg
                            class="w-3 h-3 text-white"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"
                            />
                        </svg>
                    </div>
                    <span class="font-medium"
                        >{{ offlineReportsCount }} report(s) waiting to
                        sync</span
                    >
                </div>
                <button
                    @click="$emit('manual-sync')"
                    :disabled="!isOnline || isSyncing"
                    class="text-sm bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2 transition-all duration-200"
                >
                    <svg
                        v-if="isSyncing"
                        class="animate-spin w-4 h-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                        />
                    </svg>
                    <span>{{ isSyncing ? "Syncing..." : "Sync Now" }}</span>
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
