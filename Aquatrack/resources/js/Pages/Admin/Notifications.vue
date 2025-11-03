<template>
    <AdminLayout>
        <div class="mx-auto w-full">
            <div
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden"
            >
                <!-- Header Section with Search and Filters on Right -->
                <div
                    class="px-4 py-2 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50"
                >
                    <div
                        class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4"
                    >
                        <!-- Left: Title and Count -->
                        <div class="flex items-center">
                            <h5
                                class="text-sm font-semibold text-gray-500 dark:text-gray-400"
                            >
                                <span
                                    class="font-bold text-black dark:text-white"
                                    >{{ notificationCount }}</span
                                >
                                Total Notifications
                            </h5>
                        </div>

                        <!-- Right: Search and Filters -->
                        <div
                            class="flex flex-col xl:flex-row gap-3 w-full xl:w-auto"
                        >
                            <!-- Search -->
                            <div class="relative w-full xl:w-64">
                                <div
                                    class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                                >
                                    <Search class="w-4 h-4 text-gray-400" />
                                </div>
                                <input
                                    v-model="filters.search"
                                    type="text"
                                    class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="Search notifications..."
                                    @keyup.enter="getNotifications"
                                />
                            </div>

                            <!-- All three filters in one row -->
                            <div
                                class="flex flex-row items-center gap-2 w-full xl:w-auto"
                            >
                                <!-- Type Filter -->
                                <div class="flex-1 min-w-[120px]">
                                    <select
                                        v-model="filters.type"
                                        @change="getNotifications"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
                                    >
                                        <option
                                            value=""
                                            class="text-gray-500 dark:text-gray-400"
                                        >
                                            All Types
                                        </option>
                                        <option value="reports">Reports</option>
                                        <option value="records">Records</option>
                                    </select>
                                </div>

                                <!-- Sort Filter -->
                                <div class="flex-1 min-w-[140px]">
                                    <select
                                        v-model="filters.sort"
                                        @change="getNotifications"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
                                    >
                                        <option
                                            value=""
                                            disabled
                                            selected
                                            class="text-gray-500 dark:text-gray-400"
                                        >
                                            Sort by
                                        </option>
                                        <option value="created_at">
                                            Newest First
                                        </option>
                                        <option value="created_at_asc">
                                            Oldest First
                                        </option>
                                    </select>
                                </div>

                                <!-- Reset Filters -->
                                <button
                                    @click="resetFilters"
                                    class="flex items-center px-3 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700 whitespace-nowrap"
                                >
                                    <RefreshCw class="w-4 h-4 mr-2" />
                                    Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notifications Table Container with Fixed Height -->
                <div
                    class="flex flex-col"
                    style="height: 613px; min-height: 600px"
                >
                    <!-- Table with Scrollable Body - Fixed Height -->
                    <div
                        class="flex-1 overflow-x-auto overflow-y-auto"
                        style="max-height: 560px"
                    >
                        <table class="w-full">
                            <thead
                                class="bg-gray-50 dark:bg-gray-700/50 sticky top-0 z-10"
                            >
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                    >
                                        <div
                                            class="flex items-center space-x-2"
                                        >
                                            <input
                                                type="checkbox"
                                                :checked="allSelected"
                                                @change="toggleSelectAll"
                                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                            />
                                            <span>Notification</span>
                                        </div>
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                    >
                                        Type
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                    >
                                        Date
                                    </th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                    >
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody
                                class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"
                            >
                                <tr
                                    v-for="notification in notifications.data"
                                    :key="notification.id"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-start space-x-3">
                                            <input
                                                type="checkbox"
                                                :checked="
                                                    selectedNotifications.includes(
                                                        notification.id
                                                    )
                                                "
                                                @change="
                                                    toggleNotificationSelection(
                                                        notification.id
                                                    )
                                                "
                                                class="mt-1 w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                            />
                                            <div class="flex-shrink-0">
                                                <div
                                                    class="p-2 rounded-lg"
                                                    :class="
                                                        getNotificationIconClass(
                                                            notification
                                                        )
                                                    "
                                                >
                                                    <component
                                                        :is="
                                                            getNotificationIcon(
                                                                notification
                                                            )
                                                        "
                                                        class="w-4 h-4"
                                                        :class="
                                                            getNotificationIconColor(
                                                                notification
                                                            )
                                                        "
                                                    />
                                                </div>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <!-- Clickable Notification Content -->
                                                <div
                                                    class="flex items-start justify-between cursor-pointer"
                                                    @click="
                                                        handleNotificationClick(
                                                            notification
                                                        )
                                                    "
                                                >
                                                    <div>
                                                        <p
                                                            class="text-sm font-medium text-gray-900 dark:text-white line-clamp-1 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                                                        >
                                                            {{
                                                                getNotificationTitle(
                                                                    notification
                                                                )
                                                            }}
                                                        </p>
                                                        <p
                                                            class="text-sm text-gray-600 dark:text-gray-400 mt-1 line-clamp-2"
                                                        >
                                                            {{
                                                                notification.message
                                                            }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                                            :class="
                                                getTypeBadgeClass(notification)
                                            "
                                        >
                                            {{
                                                formatNotificationType(
                                                    notification.type
                                                )
                                            }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400"
                                    >
                                        <div
                                            class="flex items-center space-x-1"
                                        >
                                            <Clock class="w-4 h-4" />
                                            <span>{{
                                                getRelativeTime(
                                                    getDateField(notification)
                                                )
                                            }}</span>
                                        </div>
                                        <div
                                            class="text-xs text-gray-400 dark:text-gray-500 mt-1"
                                        >
                                            {{
                                                formatDate(
                                                    getDateField(notification)
                                                )
                                            }}
                                        </div>
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
                                    >
                                        <div
                                            class="flex items-center justify-end space-x-2"
                                        >
                                            <button
                                                @click="
                                                    viewNotification(
                                                        notification
                                                    )
                                                "
                                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 transition-colors p-1 rounded hover:bg-blue-50 dark:hover:bg-blue-900/20"
                                                title="View Details"
                                            >
                                                <Eye class="w-4 h-4" />
                                            </button>
                                            <button
                                                @click="
                                                    deleteNotification(
                                                        notification.id
                                                    )
                                                "
                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors p-1 rounded hover:bg-red-50 dark:hover:bg-red-900/20"
                                                title="Delete"
                                            >
                                                <Trash2 class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Empty State -->
                                <tr v-if="notifications.data.length === 0">
                                    <td
                                        colspan="4"
                                        class="px-6 py-24 text-center"
                                    >
                                        <div
                                            class="flex flex-col items-center justify-center space-y-4"
                                        >
                                            <div
                                                class="p-4 bg-gray-100 dark:bg-gray-700 rounded-full"
                                            >
                                                <Bell
                                                    class="w-20 h-20 text-gray-300 dark:text-gray-500"
                                                />
                                            </div>
                                            <h3
                                                class="text-2xl font-medium text-gray-500 dark:text-gray-400"
                                            >
                                                No notifications found
                                            </h3>
                                            <p
                                                class="text-sm text-gray-400 dark:text-gray-500 max-w-md"
                                            >
                                                {{
                                                    filters.search ||
                                                    filters.type
                                                        ? "Try adjusting your filters or search keywords."
                                                        : "All caught up! No notifications to display."
                                                }}
                                            </p>
                                            <button
                                                v-if="
                                                    filters.search ||
                                                    filters.type
                                                "
                                                @click="resetFilters"
                                                class="mt-2 px-4 py-2 text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                            >
                                                Clear all filters
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination - Fixed at Bottom with 10 items info -->
                    <div
                        class="flex-shrink-0 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800"
                    >
                        <Pagination :data="notifications" />
                    </div>
                </div>

                <!-- Bulk Actions Bar -->
                <div
                    v-if="selectedNotifications.length > 0"
                    class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-blue-50 dark:bg-blue-900/20"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <CheckSquare
                                class="w-4 h-4 text-blue-600 dark:text-blue-400"
                            />
                            <span
                                class="text-sm font-medium text-blue-800 dark:text-blue-300"
                            >
                                {{ selectedNotifications.length }}
                                notification(s) selected
                            </span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <button
                                @click="bulkDelete"
                                class="px-3 py-1.5 text-sm text-white bg-red-600 border border-red-700 rounded-md hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800 transition-colors"
                            >
                                Delete Selected
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notification Detail Modal -->
        <div
            v-if="showNotificationModal"
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title"
            role="dialog"
            aria-modal="true"
        >
            <!-- Backdrop -->
            <div
                class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
                @click="closeModal"
            ></div>

            <!-- Modal Container -->
            <div
                class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0"
            >
                <!-- Modal Panel -->
                <div
                    class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
                    :class="{
                        'opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95':
                            !modalVisible,
                        'opacity-100 translate-y-0 sm:scale-100': modalVisible,
                    }"
                >
                    <!-- Modal Header -->
                    <div
                        class="bg-white dark:bg-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700"
                    >
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div
                                    class="p-2 rounded-lg"
                                    :class="
                                        selectedNotification
                                            ? getNotificationIconClass(
                                                  selectedNotification
                                              )
                                            : ''
                                    "
                                >
                                    <component
                                        :is="
                                            selectedNotification
                                                ? getNotificationIcon(
                                                      selectedNotification
                                                  )
                                                : Bell
                                        "
                                        class="w-5 h-5"
                                        :class="
                                            selectedNotification
                                                ? getNotificationIconColor(
                                                      selectedNotification
                                                  )
                                                : 'text-gray-600 dark:text-gray-400'
                                        "
                                    />
                                </div>
                                <div>
                                    <h3
                                        class="text-lg font-semibold text-gray-900 dark:text-white"
                                        id="modal-title"
                                    >
                                        {{
                                            selectedNotification
                                                ? getNotificationTitle(
                                                      selectedNotification
                                                  )
                                                : ""
                                        }}
                                    </h3>
                                    <p
                                        class="text-sm text-gray-500 dark:text-gray-400 mt-1"
                                    >
                                        {{
                                            selectedNotification
                                                ? formatNotificationType(
                                                      selectedNotification.type
                                                  )
                                                : ""
                                        }}
                                    </p>
                                </div>
                            </div>
                            <button
                                @click="closeModal"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700"
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
                    </div>

                    <!-- Modal Content -->
                    <div class="bg-white dark:bg-gray-800 px-6 py-4">
                        <div class="space-y-4">
                            <!-- Notification Message -->
                            <div>
                                <h4
                                    class="text-sm font-medium text-gray-900 dark:text-white mb-2"
                                >
                                    Message
                                </h4>
                                <p
                                    class="text-sm text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3"
                                >
                                    {{
                                        selectedNotification
                                            ? selectedNotification.message
                                            : ""
                                    }}
                                </p>
                            </div>

                            <!-- Notification Details -->
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span
                                        class="font-medium text-gray-500 dark:text-gray-400"
                                        >Type:</span
                                    >
                                    <p
                                        class="text-gray-900 dark:text-white mt-1"
                                    >
                                        {{
                                            selectedNotification
                                                ? formatNotificationType(
                                                      selectedNotification.type
                                                  )
                                                : ""
                                        }}
                                    </p>
                                </div>
                                <div>
                                    <span
                                        class="font-medium text-gray-500 dark:text-gray-400"
                                        >Date:</span
                                    >
                                    <p
                                        class="text-gray-900 dark:text-white mt-1"
                                    >
                                        {{
                                            selectedNotification
                                                ? formatDate(
                                                      getDateField(
                                                          selectedNotification
                                                      )
                                                  )
                                                : ""
                                        }}
                                    </p>
                                </div>
                                <div>
                                    <span
                                        class="font-medium text-gray-500 dark:text-gray-400"
                                        >Created:</span
                                    >
                                    <p
                                        class="text-gray-900 dark:text-white mt-1"
                                    >
                                        {{
                                            selectedNotification
                                                ? getRelativeTime(
                                                      selectedNotification.created_at
                                                  )
                                                : ""
                                        }}
                                    </p>
                                </div>
                                <div
                                    v-if="
                                        selectedNotification &&
                                        selectedNotification.updated_at !==
                                            selectedNotification.created_at
                                    "
                                >
                                    <span
                                        class="font-medium text-gray-500 dark:text-gray-400"
                                        >Updated:</span
                                    >
                                    <p
                                        class="text-gray-900 dark:text-white mt-1"
                                    >
                                        {{
                                            selectedNotification
                                                ? getRelativeTime(
                                                      selectedNotification.updated_at
                                                  )
                                                : ""
                                        }}
                                    </p>
                                </div>
                            </div>
                            <!-- Additional Data (if any) -->
                            <div
                                v-if="
                                    selectedNotification &&
                                    selectedNotification.data
                                "
                                class="border-t border-gray-200 dark:border-gray-700 pt-4"
                            >
                                <h4
                                    class="text-sm font-medium text-gray-900 dark:text-white mb-2"
                                >
                                    Additional Information
                                </h4>
                                <pre
                                    class="text-xs text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 overflow-auto max-h-32"
                                    >{{
                                        JSON.stringify(
                                            selectedNotification.data,
                                            null,
                                            2
                                        )
                                    }}</pre
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div
                        class="bg-gray-50 dark:bg-gray-700/50 px-6 py-3 border-t border-gray-200 dark:border-gray-700"
                    >
                        <div class="flex items-center justify-between">
                            <span
                                class="text-xs text-gray-500 dark:text-gray-400"
                            >
                                Notification ID:
                                {{
                                    selectedNotification
                                        ? selectedNotification.id
                                        : ""
                                }}
                            </span>
                            <div class="flex items-center space-x-2">
                                <!-- Go to Action Button -->
                                <button
                                    v-if="
                                        selectedNotification &&
                                        selectedNotification.action_url
                                    "
                                    @click="goToAction(selectedNotification)"
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-700 rounded-md hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800 transition-colors"
                                >
                                    {{ getActionLabel(selectedNotification) }}
                                </button>
                                <button
                                    v-if="selectedNotification"
                                    @click="
                                        deleteNotification(
                                            selectedNotification.id
                                        )
                                    "
                                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-red-700 rounded-md hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800 transition-colors"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from "vue";
import { router } from "@inertiajs/vue3";
import { pickBy, debounce } from "lodash";
import { usePage } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import Pagination from "@/Components/Pagination.vue";
import {
    Search,
    RefreshCw,
    Bell,
    Clock,
    CheckSquare,
    Trash2,
    AlertTriangle,
    Megaphone,
    UserCheck,
    Eye,
} from "lucide-vue-next";

const page = usePage();

const props = defineProps({
    notifications: Object,
    errors: Object,
    filters: Object,
});

const filters = ref({
    search: props.filters?.search || "",
    type: props.filters?.type || "",
    sort: props.filters?.sort || "created_at", // Default to "Newest First"
});

const selectedNotifications = ref([]);
const showNotificationModal = ref(false);
const modalVisible = ref(false);
const selectedNotification = ref(null);

// Computed properties
const allSelected = computed(() => {
    const data = props.notifications.data || [];
    return (
        data.length > 0 && selectedNotifications.value.length === data.length
    );
});

const notificationCount = computed(() => {
    return props.notifications.total || 0;
});

// Filter methods - following the same pattern as your records
const getNotifications = () => {
    router.get(route("admin.notifications.index"), pickBy(filters.value), {
        preserveState: true,
        replace: true,
    });
};

const resetFilters = () => {
    filters.value = {
        search: "",
        type: "",
        sort: "",
    };
    selectedNotifications.value = [];
    getNotifications();
};

// Watch for filter changes with debounce - same as your records
watch(
    () => ({
        search: filters.value.search,
        type: filters.value.type,
        sort: filters.value.sort,
    }),
    debounce((newFilters) => {
        getNotifications();
    }, 300),
    { deep: true, immediate: true }
);

// Click handler for notifications
const handleNotificationClick = (notification) => {
    // First mark as read if needed
    if (notification.unread) {
        markAsRead(notification.id);
    }

    // Then navigate to the action URL
    if (notification.action_url) {
        router.visit(notification.action_url);
    } else {
        // Fallback: open the modal
        viewNotification(notification);
    }
};

// Mark notification as read
const markAsRead = async (notificationId) => {
    try {
        await router.post(
            route("admin.notifications.mark-read", notificationId)
        );
    } catch (error) {
        console.error("Failed to mark notification as read:", error);
    }
};

// Go to action from modal
const goToAction = (notification) => {
    if (notification.action_url) {
        closeModal();
        router.visit(notification.action_url);
    }
};

// Get action label for button
const getActionLabel = (notification) => {
    if (!notification.action_url) return "Page";

    const url = notification.action_url;
    if (url.includes("/reports")) return "Reports";
    if (url.includes("/announcements")) return "Announcements";
    if (url.includes("/usage")) return "Usage";
    if (url.includes("/records")) return "Records";
    if (url.includes("/dashboard")) return "Dashboard";

    return "Page";
};

// Modal handlers
const viewNotification = async (notification) => {
    selectedNotification.value = notification;
    showNotificationModal.value = true;

    await nextTick();
    setTimeout(() => {
        modalVisible.value = true;
    }, 50);
};

const closeModal = () => {
    modalVisible.value = false;
    setTimeout(() => {
        showNotificationModal.value = false;
        selectedNotification.value = null;
    }, 300);
};

// Selection handlers
const toggleSelectAll = () => {
    if (allSelected.value) {
        selectedNotifications.value = [];
    } else {
        selectedNotifications.value = props.notifications.data.map((n) => n.id);
    }
};

const toggleNotificationSelection = (notificationId) => {
    const index = selectedNotifications.value.indexOf(notificationId);
    if (index > -1) {
        selectedNotifications.value.splice(index, 1);
    } else {
        selectedNotifications.value.push(notificationId);
    }
};

// Notification actions
const deleteNotification = async (notificationId) => {
    const result = await Swal.fire({
        title: "Delete Notification?",
        text: "This action cannot be undone.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "Cancel",
    });

    if (result.isConfirmed) {
        router.delete(route("admin.notifications.destroy", notificationId), {
            preserveScroll: true,
            onSuccess: () => {
                const selectedIndex =
                    selectedNotifications.value.indexOf(notificationId);
                if (selectedIndex > -1) {
                    selectedNotifications.value.splice(selectedIndex, 1);
                }

                if (
                    selectedNotification.value &&
                    selectedNotification.value.id === notificationId
                ) {
                    closeModal();
                }

                Swal.fire({
                    toast: true,
                    position: "top-end",
                    icon: "success",
                    title: "Notification deleted",
                    showConfirmButton: false,
                    timer: 2000,
                });
            },
            onError: (errors) => {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Failed to delete notification.",
                });
            },
        });
    }
};

const bulkDelete = async () => {
    if (selectedNotifications.value.length === 0) return;

    const result = await Swal.fire({
        title: `Delete ${selectedNotifications.value.length} Notifications?`,
        text: "This action cannot be undone.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete them!",
        cancelButtonText: "Cancel",
    });

    if (result.isConfirmed) {
        router.post(
            route("admin.notifications.bulk-delete"),
            { notification_ids: selectedNotifications.value },
            {
                preserveScroll: true,
                onSuccess: () => {
                    selectedNotifications.value = [];
                    Swal.fire({
                        toast: true,
                        position: "top-end",
                        icon: "success",
                        title: `Notifications deleted successfully`,
                        showConfirmButton: false,
                        timer: 2000,
                    });
                },
                onError: (errors) => {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Failed to delete notifications.",
                    });
                },
            }
        );
    }
};

// Helper functions
const getNotificationIcon = (notification) => {
    if (notification.id?.startsWith("announcement_")) {
        return Megaphone;
    }
    if (notification.id?.startsWith("new_report_")) {
        return UserCheck;
    }

    switch (notification.type) {
        case "records":
            return AlertTriangle;
        case "reports":
            return Bell;
        default:
            return Bell;
    }
};

const getNotificationIconClass = (notification) => {
    if (notification.id?.startsWith("announcement_")) {
        return "bg-blue-100 dark:bg-blue-900/30";
    }
    if (notification.id?.startsWith("new_report_")) {
        return "bg-green-100 dark:bg-green-900/30";
    }

    switch (notification.type) {
        case "records":
            return "bg-orange-100 dark:bg-orange-900/30";
        case "reports":
            return "bg-green-100 dark:bg-green-900/30";
        default:
            return "bg-gray-100 dark:bg-gray-700";
    }
};

const getNotificationIconColor = (notification) => {
    if (notification.id?.startsWith("announcement_")) {
        return "text-blue-600 dark:text-blue-400";
    }
    if (notification.id?.startsWith("new_report_")) {
        return "text-green-600 dark:text-green-400";
    }

    switch (notification.type) {
        case "records":
            return "text-orange-600 dark:text-orange-400";
        case "reports":
            return "text-green-600 dark:text-green-400";
        default:
            return "text-gray-600 dark:text-gray-400";
    }
};

const getNotificationTitle = (notification) => {
    return notification.title || "Notification";
};

const getTypeBadgeClass = (notification) => {
    if (notification.id?.startsWith("announcement_")) {
        return "bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200";
    }
    if (notification.id?.startsWith("new_report_")) {
        return "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200";
    }

    switch (notification.type) {
        case "records":
            return "bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200";
        case "reports":
            return "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200";
        default:
            return "bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300";
    }
};

const formatNotificationType = (type) => {
    if (!type) return "Unknown";

    const typeMap = {
        records: "Records",
        reports: "Reports",
        announcements: "Announcements",
        alert: "Alert",
        reminder: "Reminder",
    };

    return (
        typeMap[type] ||
        type.replace(/_/g, " ").replace(/\b\w/g, (l) => l.toUpperCase())
    );
};

const getDateField = (notification) => {
    return notification.updated_at || notification.created_at;
};

const getRelativeTime = (dateString) => {
    if (!dateString) return "";
    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);

    if (diffInSeconds < 60) return "Just now";
    if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`;
    if (diffInSeconds < 86400)
        return `${Math.floor(diffInSeconds / 3600)}h ago`;
    if (diffInSeconds < 604800)
        return `${Math.floor(diffInSeconds / 86400)}d ago`;

    return date.toLocaleDateString("en-US", {
        month: "short",
        day: "numeric",
        year: date.getFullYear() !== now.getFullYear() ? "numeric" : undefined,
    });
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

// Close modal on escape key
const handleEscape = (event) => {
    if (event.key === "Escape" && showNotificationModal.value) {
        closeModal();
    }
};

onMounted(() => {
    document.addEventListener("keydown", handleEscape);
});

onUnmounted(() => {
    document.removeEventListener("keydown", handleEscape);
});
</script>

<style scoped>
.line-clamp-1 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 1;
}

.line-clamp-2 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
}

/* Smooth transitions for modal */
.fixed {
    transition: opacity 0.3s ease-in-out;
}

.transform {
    transition: all 0.3s ease-in-out;
}

/* Ensure table rows have consistent height */
tbody tr {
    height: 80px;
}

/* Make notification content clearly clickable */
.cursor-pointer {
    cursor: pointer;
}
@media (max-width: 640px) {
    .filters-row {
        flex-wrap: wrap;
    }
}
</style>
