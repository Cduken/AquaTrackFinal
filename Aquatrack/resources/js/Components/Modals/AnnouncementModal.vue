<template>
    <transition name="modal">
        <div v-if="show" class="fixed inset-0 z-[1000] overflow-y-auto">
            <!-- Overlay -->
            <div
                class="fixed inset-0 bg-black/50 transition-opacity duration-300"
                @click="closeModal"
            ></div>

            <!-- Centered modal container -->
            <div
                class="flex items-center justify-center min-h-full p-4"
                :class="{ 'p-0': isMaximized }"
            >
                <!-- Modal panel -->
                <div
                    class="relative transform transition-all duration-300 ease-out"
                    :class="{
                        'w-full h-full': isMaximized,
                        'w-full max-w-4xl': !isMaximized,
                    }"
                >
                    <div
                        :class="[
                            'bg-white dark:bg-gray-800 shadow-2xl overflow-hidden flex flex-col',
                            isMaximized
                                ? 'h-screen'
                                : 'rounded-lg max-h-[90vh]',
                        ]"
                    >
                        <!-- Header -->
                        <div
                            class="flex items-center justify-between px-6 py-4 border-b"
                        >
                            <div class="flex items-center space-x-3">
                                <div class="flex items-center">
                                    <Megaphone
                                        class="w-6 h-6 text-black mr-2"
                                    />
                                    <h2
                                        class="text-black font-semibold text-xl"
                                    >
                                        {{
                                            isEditing
                                                ? "Edit Announcement"
                                                : "Create New Announcement"
                                        }}
                                    </h2>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <!-- Maximize/Minimize Button -->
                                <button
                                    @click="toggleMaximize"
                                    class="text-black hover:text-gray-600 hover:bg-gray-200/10 p-2 rounded transition-colors"
                                    :title="
                                        isMaximized ? 'Minimize' : 'Maximize'
                                    "
                                >
                                    <Maximize2
                                        v-if="!isMaximized"
                                        class="w-5 h-5"
                                    />
                                    <Minimize2 v-else class="w-5 h-5" />
                                </button>
                                <!-- Close Button -->
                                <button
                                    @click="closeModal"
                                    class="text-black hover:text-gray-600 hover:bg-gray-200/10 p-2 rounded transition-colors"
                                >
                                    <X class="w-5 h-5" />
                                </button>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 overflow-y-auto p-6">
                            <div class="space-y-6">
                                <!-- Title Field -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                    >
                                        Title
                                        <span class="text-red-500 ml-1">*</span>
                                    </label>
                                    <div class="relative">
                                        <Type
                                            class="absolute left-3 top-3.5 w-5 h-5 text-gray-400 pointer-events-none"
                                        />
                                        <input
                                            type="text"
                                            v-model="form.title"
                                            class="w-full pl-10 p-3 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                                            placeholder="Enter announcement title"
                                        />
                                    </div>
                                    <p
                                        v-if="errors.title"
                                        class="mt-1 text-sm text-red-600 flex items-center gap-1"
                                    >
                                        <AlertCircle class="w-4 h-4" />
                                        {{ errors.title }}
                                    </p>
                                </div>

                                <!-- Content Field -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                    >
                                        Content
                                        <span class="text-red-500 ml-1">*</span>
                                    </label>
                                    <div class="relative">
                                        <FileText
                                            class="absolute left-3 top-3.5 w-5 h-5 text-gray-400 pointer-events-none"
                                        />
                                        <textarea
                                            v-model="form.content"
                                            rows="6"
                                            class="w-full pl-10 p-3 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors resize-vertical"
                                            placeholder="Write announcement content here..."
                                        ></textarea>
                                    </div>
                                    <p
                                        v-if="errors.content"
                                        class="mt-1 text-sm text-red-600 flex items-center gap-1"
                                    >
                                        <AlertCircle class="w-4 h-4" />
                                        {{ errors.content }}
                                    </p>
                                </div>

                                <!-- Date Range Fields -->
                                <div
                                    class="grid grid-cols-1 md:grid-cols-2 gap-4"
                                >
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                        >
                                            Start Date
                                        </label>
                                        <div class="relative">
                                            <Calendar
                                                class="absolute left-3 top-3.5 w-5 h-5 text-gray-400 pointer-events-none"
                                            />
                                            <input
                                                type="date"
                                                v-model="form.start_date"
                                                class="w-full pl-10 p-3 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                                            />
                                        </div>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                        >
                                            End Date
                                        </label>
                                        <div class="relative">
                                            <Calendar
                                                class="absolute left-3 top-3.5 w-5 h-5 text-gray-400 pointer-events-none"
                                            />
                                            <input
                                                type="date"
                                                v-model="form.end_date"
                                                :min="form.start_date"
                                                class="w-full pl-10 p-3 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Field -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                    >
                                        Status
                                        <span class="text-red-500 ml-1">*</span>
                                    </label>
                                    <div class="relative">
                                        <ToggleLeft
                                            class="absolute left-3 top-3.5 w-5 h-5 pointer-events-none transition-colors"
                                            :class="{
                                                'text-green-600':
                                                    form.status === 'active',
                                                'text-gray-400':
                                                    form.status !== 'active',
                                            }"
                                        />
                                        <select
                                            v-model="form.status"
                                            class="w-full pl-10 p-3 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors appearance-none"
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
                                class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700"
                            >
                                <button
                                    type="button"
                                    @click="closeModal"
                                    class="px-6 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition-colors border border-gray-300 dark:border-gray-600"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="button"
                                    @click="submitForm"
                                    :disabled="isSubmitting"
                                    class="px-6 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed rounded transition-colors flex items-center gap-2"
                                >
                                    <Loader2
                                        v-if="isSubmitting"
                                        class="w-4 h-4 animate-spin"
                                    />
                                    <Save v-else class="w-4 h-4" />
                                    {{
                                        isSubmitting
                                            ? isEditing
                                                ? "Updating..."
                                                : "Creating..."
                                            : isEditing
                                            ? "Update"
                                            : "Create"
                                    }}
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
import {
    X,
    Maximize2,
    Minimize2,
    AlertCircle,
    Loader2,
    Save,
    Megaphone,
    Type,
    FileText,
    Calendar,
    ToggleLeft,
} from "lucide-vue-next";

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
    emit("submit");
    // Note: The parent component should handle resetting isSubmitting
};

// Reset maximize state when modal closes
watch(
    () => props.show,
    (newVal) => {
        if (!newVal) {
            isMaximized.value = false;
            isSubmitting.value = false;
        }
    }
);

// Prevent body scroll when modal is open
watch(
    () => props.show,
    (newVal) => {
        if (newVal) {
            document.body.style.overflow = "hidden";
        } else {
            document.body.style.overflow = "";
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

/* Custom scrollbar */
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Dark mode scrollbar */
.dark .overflow-y-auto::-webkit-scrollbar-track {
    background: #374151;
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb {
    background: #6b7280;
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}
</style>
