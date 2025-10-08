<template>
    <CustomerLayout>
        <div class="p-6">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                    Notifications
                </h1>
                <div class="flex space-x-2">
                    <button
                        @click="markAllAsRead"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                        :disabled="markingAllAsRead"
                    >
                        <span v-if="markingAllAsRead">Processing...</span>
                        <span v-else>Mark All as Read</span>
                    </button>
                    <!-- <button
                        @click="refreshNotifications"
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
                        :disabled="refreshing"
                    >
                        <span v-if="refreshing">...</span>
                        <span v-else>Refresh</span>
                    </button> -->
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="flex space-x-4 mb-6 border-b dark:border-gray-700">
                <button
                    v-for="filter in filters"
                    :key="filter.key"
                    @click="setFilter(filter.key)"
                    :class="[
                        'px-4 py-2 font-medium transition-colors border-b-2',
                        activeFilter === filter.key
                            ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                            : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300',
                    ]"
                >
                    {{ filter.label }}
                    <span
                        v-if="filter.key === 'unread' && unreadCount > 0"
                        class="ml-1 bg-red-500 text-white text-xs rounded-full px-2 py-0.5"
                    >
                        {{ unreadCount }}
                    </span>
                </button>
            </div>

            <!-- Debug Info (remove in production) -->
            <!-- <div v-if="debugMode" class="mb-4 p-3 bg-yellow-100 rounded-lg">
                <p class="text-sm">
                    Debug: {{ displayedNotifications.length }} notifications
                    loaded
                </p>
                <p class="text-xs">
                    Total: {{ notifications.length }}, Unread: {{ unreadCount }}
                </p>
            </div> -->

            <!-- Notifications List -->
            <div class="space-y-4">
                <!-- Empty State -->
                <div
                    v-if="displayedNotifications.length === 0"
                    class="text-center py-12 text-gray-500 dark:text-gray-400"
                >
                    <div class="text-6xl mb-4">🔔</div>
                    <h3 class="text-xl font-medium mb-2">No notifications</h3>
                    <p>
                        You're all caught up! New notifications will appear
                        here.
                    </p>
                </div>

                <!-- Notification Items -->
                <div
                    v-for="notification in displayedNotifications"
                    :key="notification.id"
                    @click="handleNotificationClick(notification)"
                    :class="[
                        'p-4 rounded-lg border transition-all duration-200 cursor-pointer',
                        notification.unread
                            ? 'bg-blue-50 border-blue-200 dark:bg-blue-900/20 dark:border-blue-800'
                            : 'bg-white border-gray-200 dark:bg-gray-800 dark:border-gray-700',
                        notification.important &&
                            'border-l-4 border-l-orange-500',
                    ]"
                >
                    <div class="flex items-start justify-between">
                        <div class="flex items-start space-x-3 flex-1">
                            <!-- Notification Icon -->
                            <div class="flex-shrink-0">
                                <span class="text-2xl">
                                    {{ getNotificationIcon(notification.type) }}
                                </span>
                            </div>

                            <!-- Notification Content -->
                            <div class="flex-1 min-w-0">
                                <div
                                    class="flex items-center justify-between mb-1"
                                >
                                    <h3
                                        class="font-semibold text-gray-900 dark:text-white"
                                    >
                                        {{ notification.title }}
                                    </h3>
                                    <span
                                        v-if="notification.unread"
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                                    >
                                        New
                                    </span>
                                </div>
                                <p
                                    class="text-gray-600 dark:text-gray-300 mb-2"
                                >
                                    {{ notification.message }}
                                </p>
                                <span
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                >
                                    {{ formatTime(notification.created_at) }}
                                </span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex space-x-2 ml-4" @click.stop>
                            <button
                                v-if="notification.unread"
                                @click="markAsRead(notification.id)"
                                class="px-3 py-1 text-sm bg-green-100 text-green-700 rounded hover:bg-green-200 dark:bg-green-900/30 dark:text-green-300 transition-colors"
                                :disabled="markingAsRead === notification.id"
                            >
                                <span v-if="markingAsRead === notification.id"
                                    >...</span
                                >
                                <span v-else>Mark Read</span>
                            </button>
                            <button
                                @click="deleteNotification(notification.id)"
                                class="px-3 py-1 text-sm bg-red-100 text-red-700 rounded hover:bg-red-200 dark:bg-red-900/30 dark:text-red-300 transition-colors"
                                :disabled="
                                    deletingNotification === notification.id
                                "
                            >
                                <span
                                    v-if="
                                        deletingNotification === notification.id
                                    "
                                    >...</span
                                >
                                <span v-else>Delete</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { router } from "@inertiajs/vue3";
import CustomerLayout from "@/Layouts/CustomerLayout.vue";

// Props from Laravel controller
const props = defineProps({
    notifications: {
        type: Array,
        default: () => [],
    },
});

// Reactive data
const activeFilter = ref("all");
const markingAsRead = ref(null);
const markingAllAsRead = ref(false);
const deletingNotification = ref(null);
const refreshing = ref(false);
const debugMode = ref(true); // Set to false in production

// Filter options
const filters = [
    { key: "all", label: "All Notifications" },
    { key: "unread", label: "Unread" },
    { key: "important", label: "Important" },
];

// Computed properties
const displayedNotifications = computed(() => {
    let filtered = props.notifications || [];

    console.log("Filtering notifications:", {
        total: filtered.length,
        activeFilter: activeFilter.value,
        unreadCount: filtered.filter((n) => n.unread).length,
    });

    switch (activeFilter.value) {
        case "unread":
            filtered = filtered.filter((n) => n.unread);
            break;
        case "important":
            filtered = filtered.filter((n) => n.important);
            break;
        default:
            break;
    }

    return filtered;
});

const unreadCount = computed(() => {
    return props.notifications.filter((n) => n.unread).length;
});

// Methods
const getNotificationIcon = (type) => {
    const icons = {
        order: "📦",
        system: "⚙️",
        alert: "⚠️",
        reminder: "⏰",
        info: "ℹ️",
        bill_overdue: "🚨",
        bill_due_soon: "📅",
        bill_final_reminder: "⏰",
        bill_due_today: "⚠️",
        new_report: "📋",
        report_update: "🔄",
        announcement: "📢",
    };
    return icons[type] || "🔔";
};

const handleNotificationClick = (notification) => {
    // Mark as read when clicked if unread
    if (notification.unread) {
        markAsRead(notification.id);
    }

    // Handle navigation based on action_url
    if (notification.action_url) {
        router.visit(notification.action_url);
    }
};

const setFilter = (filter) => {
    activeFilter.value = filter;
};

const refreshNotifications = async () => {
    refreshing.value = true;
    try {
        await router.reload();
    } catch (error) {
        console.error("Failed to refresh notifications:", error);
    } finally {
        refreshing.value = false;
    }
};

const markAsRead = async (id) => {
    markingAsRead.value = id;

    try {
        const response = await fetch(`/customer/notifications/${id}/read`, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
            },
        });

        if (response.ok) {
            // Reload the page to get updated notifications
            router.reload();
        } else {
            console.error("Failed to mark notification as read");
        }
    } catch (error) {
        console.error("Failed to mark notification as read:", error);
    } finally {
        markingAsRead.value = null;
    }
};

const markAllAsRead = async () => {
    markingAllAsRead.value = true;

    try {
        const response = await fetch("/customer/notifications/mark-all-read", {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
            },
        });

        if (response.ok) {
            // Reload the page to get updated notifications
            router.reload();
        } else {
            console.error("Failed to mark all notifications as read");
        }
    } catch (error) {
        console.error("Failed to mark all notifications as read:", error);
    } finally {
        markingAllAsRead.value = false;
    }
};

const deleteNotification = async (id) => {
    deletingNotification.value = id;

    try {
        const response = await fetch(`/customer/notifications/${id}`, {
            method: "DELETE",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
            },
        });

        if (response.ok) {
            // Reload the page to get updated notifications
            router.reload();
        } else {
            console.error("Failed to delete notification");
        }
    } catch (error) {
        console.error("Failed to delete notification:", error);
    } finally {
        deletingNotification.value = null;
    }
};

const formatTime = (timestamp) => {
    if (!timestamp) return "Recently";

    const now = new Date();
    const notificationTime = new Date(timestamp);
    const diff = now - notificationTime;

    if (diff < 60 * 1000) {
        return "Just now";
    }

    if (diff < 60 * 60 * 1000) {
        const minutes = Math.floor(diff / (60 * 1000));
        return `${minutes} minute${minutes > 1 ? "s" : ""} ago`;
    }

    if (diff < 24 * 60 * 60 * 1000) {
        const hours = Math.floor(diff / (60 * 60 * 1000));
        return `${hours} hour${hours > 1 ? "s" : ""} ago`;
    }

    const days = Math.floor(diff / (24 * 60 * 60 * 1000));
    return `${days} day${days > 1 ? "s" : ""} ago`;
};

// Debug on mount
onMounted(() => {
    console.log("Notifications page mounted:", {
        props: props.notifications,
        count: props.notifications.length,
        unread: props.notifications.filter((n) => n.unread).length,
    });
});
</script>
