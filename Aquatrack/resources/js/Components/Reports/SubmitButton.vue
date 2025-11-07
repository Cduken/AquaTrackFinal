//SubmitButton.vue
<template>
    <div class="pt-6">
        <button
            type="button"
            @click="$emit('submit')"
            :disabled="
                formProcessing ||
                isSubmitting ||
                isRecording ||
                !isFormValid ||
                locationStatus === 'loading'
            "
            class="w-full py-3 px-6 bg-gradient-to-r rounded-xl from-green-600 to-green-700 text-white font-semibold hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed shadow-lg hover:shadow-xl"
        >
            <span v-if="isSubmitting" class="flex items-center justify-center">
                <svg
                    class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                    fill="none"
                    viewBox="0 0 24 24"
                >
                    <circle
                        class="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="4"
                    ></circle>
                    <path
                        class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                    ></path>
                </svg>
                {{ isOnline ? "Submitting Report..." : "Saving Offline..." }}
            </span>
            <span
                v-else-if="isRecording"
                class="flex items-center justify-center text-yellow-200"
            >
                <div
                    class="w-3 h-3 bg-yellow-400 rounded-full mr-3 animate-pulse"
                ></div>
                Stop recording to submit
            </span>
            <span v-else class="flex items-center justify-center text-lg">
                <svg
                    class="w-5 h-5 mr-3"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                </svg>
                {{ isOnline ? "Submit Report" : "Save Report Offline" }}
            </span>
        </button>

        <!-- Help text -->
        <p v-if="!isFormValid" class="text-center text-slate-400 text-sm mt-3">
            Please complete all required fields to submit your report
        </p>
    </div>
</template>

<script setup>
defineProps({
    isSubmitting: Boolean,
    isRecording: Boolean,
    isFormValid: Boolean,
    locationStatus: String,
    isOnline: Boolean,
    formProcessing: Boolean,
});

defineEmits(["submit"]);
</script>
