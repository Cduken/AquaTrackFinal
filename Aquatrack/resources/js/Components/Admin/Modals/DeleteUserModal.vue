<!-- DeleteUserModal.vue -->
<template>
    <transition
        enter-active-class="transition-opacity duration-300 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-200 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="show"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4"
            @click.self="$emit('close')"
        >
            <transition
                enter-active-class="transition-all duration-300 ease-out"
                enter-from-class="opacity-0 scale-95 translate-y-4"
                enter-to-class="opacity-100 scale-100 translate-y-0"
                leave-active-class="transition-all duration-200 ease-in"
                leave-from-class="opacity-100 scale-100 translate-y-0"
                leave-to-class="opacity-0 scale-95 translate-y-4"
            >
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
                    <!-- Header -->
                    <div class="flex items-center p-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full bg-red-100 mr-3">
                            <TriangleAlert class="w-4 h-4 text-red-600" />
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Delete User</h3>
                    </div>

                    <!-- Body -->
                    <div class="p-6">
                        <p class="text-gray-600 dark:text-gray-300 mb-2">
                            Are you sure you want to delete this user? This action cannot be undone.
                        </p>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-md p-3 mb-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8 mr-3">
                                    <img
                                        v-if="user?.avatar_url"
                                        :src="user.avatar_url"
                                        :alt="user.name"
                                        class="h-8 w-8 rounded-full object-cover"
                                    />
                                    <div
                                        v-else
                                        class="h-8 w-8 rounded-full flex items-center justify-center text-white font-semibold text-xs bg-blue-500"
                                    >
                                        {{ userInitials(user?.name) }}
                                    </div>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white text-sm">
                                        {{ user?.name }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ user?.email || "N/A" }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="text-sm text-red-600 dark:text-red-400">
                            Warning: This will permanently delete the user account and all associated data.
                        </p>
                    </div>

                    <!-- Footer -->
                    <div class="flex justify-end space-x-3 p-4 border-t border-gray-200 dark:border-gray-700">
                        <button
                            @click="$emit('close')"
                            :disabled="isProcessing"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600"
                        >
                            Cancel
                        </button>
                        <button
                            @click="handleConfirm"
                            :disabled="isProcessing"
                            class="flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed min-w-[120px]"
                        >
                            <Loader2 v-if="isProcessing" class="w-4 h-4 mr-2 animate-spin" />
                            {{ isProcessing ? "Deleting..." : "Delete User" }}
                        </button>
                    </div>
                </div>
            </transition>
        </div>
    </transition>
</template>

<script setup>
import { ref } from "vue";
import { TriangleAlert, Loader2 } from "lucide-vue-next";

const props = defineProps({
    show: Boolean,
    user: Object,
});

const emit = defineEmits(["close", "confirm"]);

const isProcessing = ref(false);

const userInitials = (name) => {
    if (!name) return "?";
    return name
        .split(" ")
        .map((n) => n[0])
        .join("")
        .toUpperCase()
        .substring(0, 2);
};

const handleConfirm = async () => {
    if (isProcessing.value) return;

    isProcessing.value = true;

    // Simulate processing for 2 seconds
    await new Promise(resolve => setTimeout(resolve, 2000));

    emit("confirm");
    isProcessing.value = false;
};
</script>
