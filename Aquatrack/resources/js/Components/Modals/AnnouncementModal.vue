<template>
    <transition name="modal">
        <div v-if="show" class="fixed inset-0 z-[1000] overflow-hidden">
            <!-- Overlay -->
            <div
                class="absolute inset-0 bg-black/50 transition-opacity duration-300"
                @click="closeModal"
            ></div>

            <!-- Sliding panel container -->
            <div
                class="fixed inset-y-0 right-0 w-full max-w-2xl flex"
                :class="{ 'max-w-full': isMaximized }"
            >
                <!-- Panel with transform class for animation -->
                <div
                    class="relative w-full h-full transform transition-transform duration-300 ease-in-out"
                >
                    <div
                        class="h-full flex flex-col bg-white dark:bg-gray-800 shadow-xl"
                    >
                        <!-- Header -->
                        <div
                            class="flex items-center justify-between px-6 py-5 bg-gradient-to-r from-[#062F64] to-[#1E4272]"
                        >
                            <div class="flex items-center space-x-2">
                                <v-icon
                                    :name="
                                        isEditing
                                            ? 'ri-edit-box-fill'
                                            : 'bi-megaphone-fill'
                                    "
                                    class="text-amber-300"
                                    scale="1.5"
                                />
                                <span class="text-white font-[200] text-xl">{{
                                    isEditing
                                        ? "Edit Announcement"
                                        : "Create New Announcement"
                                }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <!-- Maximize/Minimize Button -->
                                <button
                                    @click="toggleMaximize"
                                    class="text-white hover:text-gray-200 transition-colors duration-200 p-1 rounded-full hover:bg-white/10"
                                    :title="
                                        isMaximized ? 'Minimize' : 'Maximize'
                                    "
                                >
                                    <v-icon
                                        :name="
                                            isMaximized
                                                ? 'bi-fullscreen-exit'
                                                : 'bi-fullscreen'
                                        "
                                        class="h-5 w-5"
                                    />
                                </button>
                                <!-- Close Button -->
                                <button
                                    @click="closeModal"
                                    class="text-white hover:text-gray-200 transition-colors duration-200 p-1 rounded-full hover:bg-white/10"
                                >
                                    <v-icon name="bi-x-lg" class="h-6 w-6" />
                                </button>
                            </div>
                        </div>

                        <!-- Content -->
                        <div
                            class="flex-1 overflow-y-auto p-6"
                            :class="{ 'p-8': isMaximized }"
                        >
                            <div
                                class="space-y-6"
                                :class="{ 'space-y-8': isMaximized }"
                            >
                                <!-- Title Field -->
                                <div
                                    class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 p-5 rounded-lg"
                                    :class="{ 'p-6': isMaximized }"
                                >
                                    <h3
                                        class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center"
                                        :class="{ 'text-xl mb-5': isMaximized }"
                                    >
                                        <v-icon
                                            name="bi-card-heading"
                                            class="mr-2"
                                            :scale="isMaximized ? '1.2' : '1'"
                                        />
                                        Title
                                        <span class="text-red-500 ml-1">*</span>
                                    </h3>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                                        >
                                            <v-icon
                                                name="bi-textarea-t"
                                                class="w-4 h-4 text-gray-500 dark:text-gray-400"
                                                :class="{
                                                    'w-5 h-5': isMaximized,
                                                }"
                                            />
                                        </div>
                                        <input
                                            type="text"
                                            id="title"
                                            v-model="form.title"
                                            class="w-full p-2 pl-10 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                                            :class="{
                                                'p-3 pl-12 text-base':
                                                    isMaximized,
                                            }"
                                            placeholder="Enter announcement title"
                                        />
                                    </div>
                                    <p
                                        v-if="errors.title"
                                        class="mt-2 text-sm text-red-600 flex items-start gap-2"
                                    >
                                        <v-icon
                                            name="bi-exclamation-circle-fill"
                                            class="w-4 h-4 mt-0.5 flex-shrink-0"
                                        />
                                        {{ errors.title }}
                                    </p>
                                </div>

                                <!-- Content Field -->
                                <div
                                    class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 p-5 rounded-lg"
                                    :class="{ 'p-6': isMaximized }"
                                >
                                    <h3
                                        class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center"
                                        :class="{ 'text-xl mb-5': isMaximized }"
                                    >
                                        <v-icon
                                            name="bi-card-text"
                                            class="mr-2"
                                            :scale="isMaximized ? '1.2' : '1'"
                                        />
                                        Content
                                        <span class="text-red-500 ml-1">*</span>
                                    </h3>
                                    <textarea
                                        id="content"
                                        v-model="form.content"
                                        rows="4"
                                        class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                                        :class="{
                                            'p-3 text-base': isMaximized,
                                        }"
                                        placeholder="Write announcement content here..."
                                    ></textarea>
                                    <p
                                        v-if="errors.content"
                                        class="mt-2 text-sm text-red-600 flex items-start gap-2"
                                    >
                                        <v-icon
                                            name="bi-exclamation-circle-fill"
                                            class="w-4 h-4 mt-0.5 flex-shrink-0"
                                        />
                                        {{ errors.content }}
                                    </p>
                                </div>

                                <!-- Date Range Fields -->
                                <div
                                    class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 p-5 rounded-lg"
                                    :class="{ 'p-6': isMaximized }"
                                >
                                    <h3
                                        class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center"
                                        :class="{ 'text-xl mb-5': isMaximized }"
                                    >
                                        <v-icon
                                            name="bi-calendar-date"
                                            class="mr-2"
                                            :scale="isMaximized ? '1.2' : '1'"
                                        />
                                        Date Range
                                    </h3>
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-2 gap-4"
                                        :class="{ 'gap-6': isMaximized }"
                                    >
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                                :class="{
                                                    'text-base': isMaximized,
                                                }"
                                                >Start Date</label
                                            >
                                            <div class="relative">
                                                <div
                                                    class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                                                >
                                                    <v-icon
                                                        name="bi-calendar3"
                                                        class="w-4 h-4 text-gray-500 dark:text-gray-400"
                                                        :class="{
                                                            'w-5 h-5':
                                                                isMaximized,
                                                        }"
                                                    />
                                                </div>
                                                <input
                                                    type="date"
                                                    id="start_date"
                                                    v-model="form.start_date"
                                                    class="w-full p-2 pl-10 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                                                    :class="{
                                                        'p-3 pl-12 text-base':
                                                            isMaximized,
                                                    }"
                                                />
                                            </div>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                                :class="{
                                                    'text-base': isMaximized,
                                                }"
                                                >End Date</label
                                            >
                                            <div class="relative">
                                                <div
                                                    class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                                                >
                                                    <v-icon
                                                        name="bi-calendar3"
                                                        class="w-4 h-4 text-gray-500 dark:text-gray-400"
                                                        :class="{
                                                            'w-5 h-5':
                                                                isMaximized,
                                                        }"
                                                    />
                                                </div>
                                                <input
                                                    type="date"
                                                    id="end_date"
                                                    v-model="form.end_date"
                                                    :min="form.start_date"
                                                    class="w-full p-2 pl-10 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                                                    :class="{
                                                        'p-3 pl-12 text-base':
                                                            isMaximized,
                                                    }"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Field -->
                                <div
                                    class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 p-5 rounded-lg"
                                    :class="{ 'p-6': isMaximized }"
                                >
                                    <h3
                                        class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center"
                                        :class="{ 'text-xl mb-5': isMaximized }"
                                    >
                                        <v-icon
                                            name="bi-circle-fill"
                                            class="mr-2"
                                            :scale="isMaximized ? '1.2' : '1'"
                                        />
                                        Status
                                        <span class="text-red-500 ml-1">*</span>
                                    </h3>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                                        >
                                            <v-icon
                                                :name="
                                                    form.status === 'active'
                                                        ? 'bi-toggle-on'
                                                        : 'bi-toggle-off'
                                                "
                                                class="h-5 w-5 transition-colors duration-200"
                                                :class="
                                                    form.status === 'active'
                                                        ? 'text-green-500'
                                                        : 'text-gray-400'
                                                "
                                            />
                                        </div>
                                        <select
                                            id="status"
                                            v-model="form.status"
                                            class="w-full p-2 pl-10 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                                            :class="{
                                                'p-3 pl-12 text-base':
                                                    isMaximized,
                                            }"
                                        >
                                            <option value="active">
                                                Active
                                            </option>
                                            <option value="inactive">
                                                Inactive
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div
                                class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-600"
                                :class="{ 'mt-8 pt-6 space-x-4': isMaximized }"
                            >
                                <button
                                    type="button"
                                    @click="closeModal"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 rounded-md transition-colors duration-200"
                                    :class="{
                                        'px-6 py-3 text-base': isMaximized,
                                    }"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="button"
                                    @click="submitForm"
                                    :disabled="isSubmitting"
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed rounded-md transition-colors duration-200 flex items-center justify-center min-w-[120px]"
                                    :class="{
                                        'px-6 py-3 text-base min-w-[140px]':
                                            isMaximized,
                                    }"
                                >
                                    <span
                                        v-if="isSubmitting"
                                        class="flex items-center"
                                    >
                                        <svg
                                            class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                            :class="{ 'h-5 w-5': isMaximized }"
                                            xmlns="http://www.w3.org/2000/svg"
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
                                        {{
                                            isEditing
                                                ? "Updating..."
                                                : "Creating..."
                                        }}
                                    </span>
                                    <span v-else class="flex items-center">
                                        <v-icon
                                            :name="
                                                isEditing
                                                    ? 'ri-save-3-fill'
                                                    : 'bi-save-fill'
                                            "
                                            class="mr-1 w-4 h-4"
                                            :class="{ 'w-5 h-5': isMaximized }"
                                        />
                                        {{ isEditing ? "Update" : "Create" }}
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup>
import { ref, computed, watch } from "vue";

const props = defineProps({
    show: Boolean,
    editing: {
        type: Boolean,
        required: true,
        default: false,
    },
    form: Object,
    errors: Object,
});

const emit = defineEmits(["close", "submit"]);

const isEditing = computed(() => props.editing);
const isSubmitting = ref(false);
const isMaximized = ref(false);

const toggleMaximize = () => {
    isMaximized.value = !isMaximized.value;
};

const closeModal = () => {
    emit("close");
};

const submitForm = async () => {
    isSubmitting.value = true;

    try {
        // Simulate API call delay
        await new Promise((resolve) => setTimeout(resolve, 1000));
        emit("submit");
    } finally {
        isSubmitting.value = false;
    }
};

// Reset maximize state when modal closes
watch(
    () => props.show,
    (newVal, oldVal) => {
        if (oldVal === true && newVal === false) {
            isMaximized.value = false;
            isSubmitting.value = false;
        }
    }
);
</script>

<style scoped>
/* Modal transition styles */
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-enter-active .transform,
.modal-leave-active .transform {
    transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.5, 1);
}

.modal-enter-from .transform {
    transform: translateX(100%);
}

.modal-leave-to .transform {
    transform: translateX(100%);
}

/* Custom scrollbar for the content */
.overflow-y-auto {
    scrollbar-width: thin;
    scrollbar-color: #e2e8f0 #f8fafc;
}

.overflow-y-auto::-webkit-scrollbar {
    width: 8px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f8fafc;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background-color: #e2e8f0;
    border-radius: 4px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background-color: #cbd5e1;
}

/* Dark mode scrollbar */
.dark .overflow-y-auto {
    scrollbar-color: #4b5563 #1f2937;
}

.dark .overflow-y-auto::-webkit-scrollbar-track {
    background: #1f2937;
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb {
    background-color: #4b5563;
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background-color: #6b7280;
}

/* Custom date input styling */
input[type="date"]::-webkit-calendar-picker-indicator {
    opacity: 0;
    position: absolute;
    width: 100%;
    height: 100%;
    left: 0;
    cursor: pointer;
}

/* Smooth transitions for interactive elements */
button,
input,
select,
textarea {
    transition: all 0.2s ease;
}

/* Focus states */
input:focus,
textarea:focus,
select:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
}

/* Status toggle icon animation */
.bi-toggle-on,
.bi-toggle-off {
    transition: color 0.3s ease, transform 0.2s ease;
}
</style>
