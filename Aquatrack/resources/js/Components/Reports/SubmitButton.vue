<template>
    <div class="pt-6 border-t border-gray-200">
        <button
            type="submit"
            :disabled="
                formProcessing ||
                isSubmitting ||
                isRecording ||
                !isFormValid ||
                locationStatus === 'loading'
            "
            class="w-full py-4 px-6 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed"
        >
            <span v-if="isSubmitting" class="flex items-center justify-center">
                <v-icon
                    name="bi-arrow-repeat"
                    class="animate-spin -ml-1 mr-3 h-5 w-5"
                />
                {{ isOnline ? "Submitting Report..." : "Saving Offline..." }}
            </span>
            <span
                v-else-if="isRecording"
                class="flex items-center justify-center text-yellow-200"
            >
                <div
                    class="w-2 h-2 bg-yellow-300 rounded-full mr-3 animate-pulse"
                ></div>
                Stop recording to submit
            </span>
            <span v-else class="flex items-center justify-center">
                <v-icon name="hi-paper-airplane" class="w-5 h-5 mr-2" />
                {{ isOnline ? "Submit Report" : "Save Report Offline" }}
            </span>
        </button>
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
</script>
