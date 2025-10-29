<template>
    <Transition
        enter-active-class="ease-out duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="show"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 transition-opacity duration-300"
            @click="close"
        >
            <Transition
                enter-active-class="ease-out duration-300"
                enter-from-class="opacity-0 scale-95"
                enter-to-class="opacity-100 scale-100"
                leave-active-class="ease-in duration-200"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-95"
            >
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-xl mx-4 transform transition-all duration-300"
                    @click.stop
                >
                    <!-- Modal Header -->
                    <div
                        class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700"
                    >
                        <h3
                            class="text-lg font-semibold text-gray-900 dark:text-white"
                        >
                            Change Report Status
                        </h3>
                        <button
                            @click="close"
                            :disabled="isProcessing"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <svg
                                class="w-5 h-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6">
                        <div class="mb-6">
                            <p
                                class="text-sm text-gray-600 dark:text-gray-400 mb-2"
                            >
                                Report Details:
                            </p>
                            <div
                                class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4"
                            >
                                <p
                                    class="text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    {{ report?.tracking_code }} -
                                    {{ report?.reporter_name }}
                                </p>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400 mt-1"
                                >
                                    Current Status:
                                    <span
                                        :class="statusClasses(report?.status)"
                                        class="ml-1 px-2 py-1 rounded-full text-xs"
                                    >
                                        {{ formatStatus(report?.status) }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Select New Status:
                            </label>

                            <div class="grid grid-cols-1 gap-3">
                                <label
                                    v-for="status in statusOptions"
                                    :key="status.value"
                                    :class="[
                                        'flex items-center p-4 border-2 rounded-lg cursor-pointer transition-all duration-200',
                                        selectedStatus === status.value
                                            ? getStatusBorderClass(status.value)
                                            : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500',
                                        isProcessing
                                            ? 'pointer-events-none opacity-60'
                                            : '',
                                    ]"
                                >
                                    <input
                                        type="radio"
                                        :value="status.value"
                                        v-model="selectedStatus"
                                        class="hidden"
                                        :disabled="isProcessing"
                                    />
                                    <div class="flex items-center w-full">
                                        <div
                                            class="w-4 h-4 rounded-full border-2 mr-3 flex items-center justify-center"
                                            :class="
                                                selectedStatus === status.value
                                                    ? getStatusRadioClass(
                                                          status.value
                                                      )
                                                    : 'border-gray-300 dark:border-gray-500'
                                            "
                                        >
                                            <div
                                                v-if="
                                                    selectedStatus ===
                                                    status.value
                                                "
                                                class="w-2 h-2 rounded-full"
                                                :class="
                                                    getStatusDotClass(
                                                        status.value
                                                    )
                                                "
                                            ></div>
                                        </div>
                                        <div class="flex items-center flex-1">
                                            <span
                                                :class="
                                                    statusClasses(status.value)
                                                "
                                                class="px-3 py-1 rounded-full text-sm font-medium mr-3"
                                            >
                                                {{ status.label }}
                                            </span>
                                            <span
                                                class="text-xs text-gray-500 dark:text-gray-400 flex-1"
                                            >
                                                {{
                                                    getStatusDescription(
                                                        status.value
                                                    )
                                                }}
                                            </span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div
                        class="flex justify-end space-x-3 p-6 border-t border-gray-200 dark:border-gray-700"
                    >
                        <button
                            @click="close"
                            :disabled="isProcessing"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-150"
                        >
                            Cancel
                        </button>
                        <button
                            @click="handleConfirm"
                            :disabled="
                                !selectedStatus ||
                                selectedStatus === report?.status ||
                                isProcessing
                            "
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-150 flex items-center min-w-[120px] justify-center"
                        >
                            <span v-if="isProcessing" class="flex items-center">
                                <RefreshCw class="w-4 h-4 mr-2 animate-spin" />
                                Updating...
                            </span>
                            <span
                                v-else-if="showSuccess"
                                class="flex items-center"
                            >
                                <CheckCircle class="w-4 h-4 mr-2" />
                                Updated!
                            </span>
                            <span v-else class="flex items-center">
                                <CheckCircle class="w-4 h-4 mr-2" />
                                Update Status
                            </span>
                        </button>
                    </div>
                </div>
            </Transition>
        </div>
    </Transition>
</template>

<script setup>
import { ref, watch, computed } from "vue";
import { RefreshCw, CheckCircle } from "lucide-vue-next";

// Props
const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    report: {
        type: Object,
        default: null,
    },
    updating: {
        type: Boolean,
        default: false,
    },
});

// Emits
const emit = defineEmits(["close", "confirm"]);

// Local state
const selectedStatus = ref("");
const isProcessing = ref(false);
const showSuccess = ref(false);

// Constants
const statusOptions = [
    { value: "pending", label: "Pending" },
    { value: "in_progress", label: "In Progress" },
    { value: "resolved", label: "Resolved" },
];

// Computed properties
const statusClasses = computed(() => (status) => {
    return {
        "bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200":
            status === "pending",
        "bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200":
            status === "in_progress",
        "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200":
            status === "resolved",
        "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200":
            status?.startsWith("Deleted"),
    };
});

// Watchers
watch(
    () => props.report,
    (newReport) => {
        if (newReport) {
            selectedStatus.value = newReport.status;
        }
    },
    { immediate: true }
);

watch(
    () => props.show,
    (newVal) => {
        if (!newVal) {
            // Reset states when modal closes
            setTimeout(() => {
                selectedStatus.value = "";
                isProcessing.value = false;
                showSuccess.value = false;
            }, 300);
        }
    }
);

// Methods
const close = () => {
    if (!isProcessing.value) {
        emit("close");
    }
};

const handleConfirm = async () => {
    if (
        selectedStatus.value &&
        selectedStatus.value !== props.report?.status &&
        !isProcessing.value
    ) {
        isProcessing.value = true;

        // Show spinning icon for 2 seconds
        await new Promise((resolve) => setTimeout(resolve, 2000));

        // Show success state briefly
        showSuccess.value = true;
        await new Promise((resolve) => setTimeout(resolve, 500));

        // Emit the confirm event with the selected status
        emit("confirm", selectedStatus.value);

        // Reset states
        isProcessing.value = false;
        showSuccess.value = false;
    }
};

const getStatusBorderClass = (status) => {
    switch (status) {
        case "pending":
            return "border-gray-400 bg-gray-50 dark:bg-gray-700";
        case "in_progress":
            return "border-blue-400 bg-blue-50 dark:bg-blue-900/20";
        case "resolved":
            return "border-green-400 bg-green-50 dark:bg-green-900/20";
        default:
            return "border-gray-400 bg-gray-50 dark:bg-gray-700";
    }
};

const getStatusRadioClass = (status) => {
    switch (status) {
        case "pending":
            return "border-gray-500";
        case "in_progress":
            return "border-blue-500";
        case "resolved":
            return "border-green-500";
        default:
            return "border-gray-500";
    }
};

const getStatusDotClass = (status) => {
    switch (status) {
        case "pending":
            return "bg-gray-500";
        case "in_progress":
            return "bg-blue-500";
        case "resolved":
            return "bg-green-500";
        default:
            return "bg-gray-500";
    }
};

const getStatusDescription = (status) => {
    switch (status) {
        case "pending":
            return "Report is waiting for review";
        case "in_progress":
            return "Team is working on this issue";
        case "resolved":
            return "Issue has been resolved";
        default:
            return "";
    }
};

const formatStatus = (status) => {
    if (status?.startsWith("Deleted")) return status;
    const statusMap = {
        pending: "Pending",
        in_progress: "In Progress",
        resolved: "Resolved",
    };
    return statusMap[status] || status;
};
</script>

<style scoped>
.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

/* Smooth transitions for state changes */
button {
    transition: all 0.2s ease-in-out;
}

label {
    transition: all 0.2s ease-in-out;
}
</style>
