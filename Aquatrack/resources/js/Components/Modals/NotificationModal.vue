<template>
    <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto">
        <!-- Background -->
        <div class="fixed inset-0 bg-black bg-opacity-50" @click="close"></div>

        <!-- Modal -->
        <div
            class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
        >
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
            >
                <!-- Header -->
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900">
                            Notifications
                        </h3>
                        <button
                            @click="close"
                            class="text-gray-400 hover:text-gray-500"
                        >
                            <svg
                                class="h-6 w-6"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
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

                    <!-- Notifications List -->
                    <div class="max-h-96 overflow-y-auto">
                        <div v-if="loading" class="text-center py-4">
                            <div
                                class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto"
                            ></div>
                            <p class="mt-2 text-gray-500">
                                Loading notifications...
                            </p>
                        </div>

                        <div
                            v-else-if="notifications.length === 0"
                            class="text-center py-8 text-gray-500"
                        >
                            <div class="text-4xl mb-2">🔔</div>
                            <p>No notifications available</p>
                        </div>

                        <div v-else class="space-y-3">
                            <div
                                v-for="notification in notifications"
                                :key="notification.id"
                                class="p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer"
                                @click="handleNotificationClick(notification)"
                            >
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0 text-xl">
                                        {{ getIcon(notification.type) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div
                                            class="flex items-center justify-between"
                                        >
                                            <p
                                                class="text-sm font-medium text-gray-900"
                                            >
                                                {{ notification.title }}
                                            </p>
                                            <span
                                                v-if="notification.unread"
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                                            >
                                                New
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">
                                            {{
                                                notification.message ||
                                                "No message"
                                            }}
                                        </p>
                                        <p class="text-xs text-gray-400 mt-1">
                                            {{
                                                formatTime(
                                                    notification.created_at
                                                )
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div
                    class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse"
                >
                    <button
                        type="button"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm"
                        @click="viewAllNotifications"
                    >
                        View All Notifications
                    </button>
                    <button
                        type="button"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        @click="close"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from "vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    isOpen: Boolean,
});

const emit = defineEmits(["close"]);

const notifications = ref([]);
const loading = ref(false);

// Methods
const getIcon = (type) => {
    const icons = {
        info: "ℹ️",
        report_update: "🔄",
        announcement: "📢",
        bill_overdue: "🚨",
        bill_due_soon: "📅",
        bill_final_reminder: "⏰",
        bill_due_today: "⚠️",
    };
    return icons[type] || "🔔";
};

const formatTime = (timestamp) => {
    if (!timestamp) return "";
    const date = new Date(timestamp);
    const now = new Date();
    const diff = now - date;

    if (diff < 60000) return "Just now";
    if (diff < 3600000) return `${Math.floor(diff / 60000)}m ago`;
    if (diff < 86400000) return `${Math.floor(diff / 3600000)}h ago`;
    return `${Math.floor(diff / 86400000)}d ago`;
};

const close = () => {
    emit("close");
};

const viewAllNotifications = () => {
    router.visit("/customer/notifications");
    close();
};

const handleNotificationClick = (notification) => {
    if (notification.action_url) {
        router.visit(notification.action_url);
        close();
    }
};

// Fetch notifications when modal opens
const fetchNotifications = async () => {
    loading.value = true;
    try {
        console.log("🔔 FETCHING NOTIFICATIONS FROM API...");
        const response = await fetch("/api/notifications");
        console.log("🔔 API Response status:", response.status);

        if (response.ok) {
            const data = await response.json();
            console.log("🔔 API Data received:", data);
            notifications.value = data.notifications || [];
            console.log("🔔 Notifications loaded:", notifications.value.length);

            // Log each notification for debugging
            notifications.value.forEach((notif, index) => {
                console.log(`🔔 Notification ${index + 1}:`, {
                    id: notif.id,
                    title: notif.title,
                    message: notif.message,
                    type: notif.type,
                    unread: notif.unread,
                });
            });
        } else {
            console.error(
                "🔔 API Error:",
                response.status,
                response.statusText
            );
            notifications.value = [];
        }
    } catch (error) {
        console.error("🔔 Fetch Error:", error);
        notifications.value = [];
    } finally {
        loading.value = false;
    }
};

// Watch for modal open state
watch(
    () => props.isOpen,
    (isOpen) => {
        if (isOpen) {
            console.log("🔔 MODAL OPENED - Starting fetch...");
            fetchNotifications();
        } else {
            console.log("🔔 MODAL CLOSED");
            notifications.value = []; // Clear when closed
        }
    }
);
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}
</style>
