<template>
    <transition name="modal">
        <div v-if="show" class="fixed inset-0 z-[1000] overflow-y-auto">
            <!-- Overlay -->
            <div
                class="fixed inset-0 bg-black/50 transition-opacity duration-300"
                @click="closeModal"
            ></div>

            <!-- Centered modal container -->
            <div class="flex items-center justify-center min-h-full p-4">
                <!-- Modal panel -->
                <div
                    class="relative w-full max-w-4xl transform transition-all duration-300 ease-out"
                    :class="{
                        'max-w-6xl': isMaximized,
                        'scale-100': !isMaximized && show,
                        'scale-100': isMaximized,
                    }"
                >
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden "
                    >
                        <!-- Header -->
                        <div
                            class="flex items-center justify-between px-8 py-6 bg-[#062F64] relative overflow-hidden"
                        >

                            <div
                                class="flex items-center space-x-3 relative z-10"
                            >
                                <div
                                    class="p-2 bg-white/10 rounded-xl backdrop-blur-sm"
                                >
                                    <v-icon
                                        :name="
                                            isEditing
                                                ? 'ri-edit-box-fill'
                                                : 'bi-megaphone-fill'
                                        "
                                        class="text-amber-300"
                                        scale="1.5"
                                    />
                                </div>
                                <div>
                                    <h2
                                        class="text-white font-semibold text-2xl"
                                    >
                                        {{
                                            isEditing
                                                ? "Edit Announcement"
                                                : "Create New Announcement"
                                        }}
                                    </h2>
                                    <p class="text-blue-100 text-sm mt-1">
                                        {{
                                            isEditing
                                                ? "Update your announcement details"
                                                : "Share important updates with your community"
                                        }}
                                    </p>
                                </div>
                            </div>
                            <div
                                class="flex items-center space-x-3 relative z-10"
                            >
                                <!-- Maximize/Minimize Button -->
                                <button
                                    @click="toggleMaximize"
                                    class="text-white hover:text-amber-200 transition-all duration-200 p-2 rounded-xl hover:bg-white/10 backdrop-blur-sm"
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
                                    class="text-white hover:text-red-200 transition-all duration-200 p-2 rounded-xl hover:bg-white/10 backdrop-blur-sm"
                                >
                                    <v-icon name="bi-x-lg" class="h-6 w-6" />
                                </button>
                            </div>
                        </div>

                        <!-- Content -->
                        <div
                            class="max-h-[70vh] overflow-y-auto p-8"
                            :class="{ 'max-h-[80vh]': isMaximized }"
                        >
                            <div class="space-y-6">
                                <!-- Title Field -->
                                <div class="group">
                                    <label
                                        class="flex items-center text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3"
                                    >
                                        <!-- <div
                                            class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg mr-3 group-hover:scale-110 transition-transform duration-200"
                                        >
                                            <v-icon
                                                name="bi-card-heading"
                                                class="text-blue-600 dark:text-blue-400"
                                            />
                                        </div> -->
                                        Title
                                        <span class="text-red-500 ml-1">*</span>
                                    </label>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none"
                                        >
                                            <v-icon
                                                name="bi-textarea-t"
                                                class="w-5 h-5 text-gray-400"
                                            />
                                        </div>
                                        <input
                                            type="text"
                                            id="title"
                                            v-model="form.title"
                                            class="w-full p-4 pl-12 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                            placeholder="Enter announcement title"
                                        />
                                    </div>
                                    <p
                                        v-if="errors.title"
                                        class="mt-2 text-sm text-red-600 flex items-start gap-2 animate-pulse"
                                    >
                                        <v-icon
                                            name="bi-exclamation-circle-fill"
                                            class="w-4 h-4 mt-0.5 flex-shrink-0"
                                        />
                                        {{ errors.title }}
                                    </p>
                                </div>

                                <!-- Content Field -->
                                <div class="group">
                                    <label
                                        class="flex items-center text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3"
                                    >
                                        <!-- <div
                                            class="p-2 bg-green-100 dark:bg-green-900 rounded-lg mr-3 group-hover:scale-110 transition-transform duration-200"
                                        >
                                            <v-icon
                                                name="bi-card-text"
                                                class="text-green-600 dark:text-green-400"
                                            />
                                        </div> -->
                                        Content
                                        <span class="text-red-500 ml-1">*</span>
                                    </label>
                                    <textarea
                                        id="content"
                                        v-model="form.content"
                                        rows="5"
                                        class="w-full p-4 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 resize-vertical min-h-[120px]"
                                        placeholder="Write announcement content here..."
                                    ></textarea>
                                    <p
                                        v-if="errors.content"
                                        class="mt-2 text-sm text-red-600 flex items-start gap-2 animate-pulse"
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
                                    class="grid grid-cols-1 md:grid-cols-2 gap-6"
                                >
                                    <div class="group">
                                        <label
                                            class="flex items-center text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3"
                                        >
                                            <!-- <div
                                                class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg mr-3 group-hover:scale-110 transition-transform duration-200"
                                            >
                                                <v-icon
                                                    name="bi-calendar3"
                                                    class="text-purple-600 dark:text-purple-400"
                                                />
                                            </div> -->
                                            Start Date
                                        </label>
                                        <div class="relative">
                                            <div
                                                class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none"
                                            >
                                                <v-icon
                                                    name="bi-calendar-date"
                                                    class="w-5 h-5 text-gray-400"
                                                />
                                            </div>
                                            <input
                                                type="date"
                                                id="start_date"
                                                v-model="form.start_date"
                                                class="w-full p-4 pl-12 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                            />
                                        </div>
                                    </div>
                                    <div class="group">
                                        <label
                                            class="flex items-center text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3"
                                        >
                                            <!-- <div
                                                class="p-2 bg-orange-100 dark:bg-orange-900 rounded-lg mr-3 group-hover:scale-110 transition-transform duration-200"
                                            >
                                                <v-icon
                                                    name="bi-calendar3"
                                                    class="text-orange-600 dark:text-orange-400"
                                                />
                                            </div> -->
                                            End Date
                                        </label>
                                        <div class="relative">
                                            <div
                                                class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none"
                                            >
                                                <v-icon
                                                    name="bi-calendar-date"
                                                    class="w-5 h-5 text-gray-400"
                                                />
                                            </div>
                                            <input
                                                type="date"
                                                id="end_date"
                                                v-model="form.end_date"
                                                :min="form.start_date"
                                                class="w-full p-4 pl-12 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Field -->
                                <div class="group">
                                    <label
                                        class="flex items-center text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3"
                                    >
                                        <!-- <div
                                            class="p-2 bg-red-100 dark:bg-red-900 rounded-lg mr-3 group-hover:scale-110 transition-transform duration-200"
                                        >
                                            <v-icon
                                                name="bi-circle-fill"
                                                class="text-red-600 dark:text-red-400"
                                            />
                                        </div> -->
                                        Status
                                        <span class="text-red-500 ml-1">*</span>
                                    </label>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none"
                                        >
                                            <v-icon
                                                :name="
                                                    form.status === 'active'
                                                        ? 'bi-toggle-on'
                                                        : 'bi-toggle-off'
                                                "
                                                class="h-6 w-6 transition-all duration-300"
                                                :class="
                                                    form.status === 'active'
                                                        ? 'text-green-500 scale-110'
                                                        : 'text-gray-400'
                                                "
                                            />
                                        </div>
                                        <select
                                            id="status"
                                            v-model="form.status"
                                            class="w-full p-4 pl-12 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 appearance-none cursor-pointer"
                                        >
                                            <option value="active">
                                                Active
                                            </option>
                                            <option value="inactive">
                                                Inactive
                                            </option>
                                        </select>
                                        <div
                                            class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none"
                                        >
                                            <v-icon
                                                name="bi-chevron-down"
                                                class="w-4 h-4 text-gray-400"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div
                                class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700"
                            >
                                <button
                                    type="button"
                                    @click="closeModal"
                                    class="px-6 py-3 text-base font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl transition-all duration-200 hover:scale-105 active:scale-95 border border-gray-300 dark:border-gray-600"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="button"
                                    @click="submitForm"
                                    :disabled="isSubmitting"
                                    class="px-8 py-3 text-base font-medium text-white bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 disabled:opacity-50 disabled:cursor-not-allowed rounded-xl transition-all duration-200 hover:scale-105 active:scale-95 shadow-lg hover:shadow-xl flex items-center justify-center min-w-[140px] relative overflow-hidden"
                                >
                                    <!-- Button shine effect -->
                                    <div
                                        class="absolute inset-0 -translate-x-full group-hover:translate-x-full transition-transform duration-1000"
                                        :class="{
                                            'group-hover:translate-x-full':
                                                !isSubmitting,
                                        }"
                                    >
                                        <div
                                            class="w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent"
                                        ></div>
                                    </div>

                                    <span
                                        v-if="isSubmitting"
                                        class="flex items-center relative z-10"
                                    >
                                        <svg
                                            class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
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
                                    <span
                                        v-else
                                        class="flex items-center relative z-10"
                                    >
                                        <v-icon
                                            :name="
                                                isEditing
                                                    ? 'ri-save-3-fill'
                                                    : 'bi-save-fill'
                                            "
                                            class="mr-2 w-5 h-5"
                                        />
                                        {{
                                            isEditing
                                                ? "Update Announcement"
                                                : "Create Announcement"
                                        }}
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
    transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-enter-active .transform,
.modal-leave-active .transform {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.modal-enter-from .transform {
    opacity: 0;
    transform: scale(0.9) translateY(-10px);
}

.modal-leave-to .transform {
    opacity: 0;
    transform: scale(0.95) translateY(10px);
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
    border-radius: 4px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background-color: #cbd5e1;
    border-radius: 4px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background-color: #94a3b8;
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

/* Enhanced focus states */
input:focus,
textarea:focus,
select:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    transform: translateY(-1px);
}

/* Smooth transitions for all interactive elements */
button,
input,
select,
textarea {
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Status toggle icon animation */
.bi-toggle-on,
.bi-toggle-off {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Button group hover effects */
.group:hover .group-hover\:scale-110 {
    transform: scale(1.1);
}

/* Backdrop blur support */
@supports (backdrop-filter: blur(10px)) {
    .backdrop-blur-sm {
        backdrop-filter: blur(8px);
    }
}
</style>
