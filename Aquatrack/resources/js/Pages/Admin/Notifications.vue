//Pages/Admin/Notifications.vue
<template>
    <AdminLayout>
        <div class="mx-auto w-full">
            <div
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden"
            >
                <!-- Search and Filter Section -->
                <div class="p-2 border-b border-gray-200 dark:border-gray-700">
                    <div
                        class="flex flex-col md:flex-row md:items-center justify-between gap-4"
                    >
                        <div class="flex items-center">
                            <h5
                                class="text-sm font-semibold text-gray-500 dark:text-gray-400"
                            >
                                <span
                                    class="font-bold text-black dark:text-white"
                                    >{{ notifications.length || 0 }}</span
                                >
                                Total Notifications
                            </h5>
                        </div>

                        <div
                            class="flex flex-col md:flex-row items-center space-x-3"
                        >
                            <div class="w-full md:w-auto">
                                <div class="relative">
                                    <div
                                        class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                                    >
                                        <Search
                                            class="w-4 h-4 text-gray-900 dark:text-gray-400"
                                        />
                                    </div>
                                    <input
                                        v-model="filters.search"
                                        type="text"
                                        class="block w-full md:w-auto pl-10 text-sm text-gray-900 border border-gray-300 rounded-sm bg-white focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="Search notifications..."
                                        @keyup.enter="applyFilters"
                                    />
                                </div>
                            </div>

                            <!-- Filter Dropdown -->
                            <div class="relative">
                                <button
                                    @click="toggleFilterDropdown"
                                    ref="filterButton"
                                    class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600"
                                    type="button"
                                >
                                    <Filter class="w-4 h-4 mr-2" />
                                    Filter
                                    <ChevronDown class="ml-1 w-4 h-4" />
                                </button>

                                <div
                                    v-if="showFilterDropdown"
                                    class="fixed z-[1000] mt-1 w-56 bg-white rounded-lg shadow-lg border border-gray-200 dark:bg-gray-700 dark:border-gray-600"
                                    :style="filterDropdownStyle"
                                    @click.stop
                                >
                                    <div class="p-4">
                                        <h6
                                            class="mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                        >
                                            Status
                                        </h6>
                                        <div class="space-y-2">
                                            <div
                                                class="flex items-center"
                                                v-for="statusOption in statusOptions"
                                                :key="statusOption.value"
                                            >
                                                <input
                                                    :id="`status-${statusOption.value}`"
                                                    type="radio"
                                                    name="status"
                                                    :value="statusOption.value"
                                                    :checked="
                                                        filters.status ===
                                                        statusOption.value
                                                    "
                                                    @change="
                                                        updateStatusFilter(
                                                            statusOption.value
                                                        )
                                                    "
                                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                                />
                                                <label
                                                    :for="`status-${statusOption.value}`"
                                                    class="ml-2 text-sm text-gray-700 dark:text-gray-300"
                                                >
                                                    {{ statusOption.label }}
                                                </label>
                                            </div>
                                        </div>

                                        <h6
                                            class="mb-2 mt-4 text-sm font-medium text-gray-900 dark:text-white"
                                        >
                                            Type
                                        </h6>
                                        <div class="space-y-2">
                                            <div
                                                class="flex items-center"
                                                v-for="typeOption in typeOptions"
                                                :key="typeOption.value"
                                            >
                                                <input
                                                    :id="`type-${typeOption.value}`"
                                                    type="checkbox"
                                                    :value="typeOption.value"
                                                    v-model="filters.types"
                                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                                />
                                                <label
                                                    :for="`type-${typeOption.value}`"
                                                    class="ml-2 text-sm text-gray-700 dark:text-gray-300"
                                                >
                                                    {{ typeOption.label }}
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Quick Stats Section -->
                                        <div
                                            class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600"
                                        >
                                            <h6
                                                class="mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                            >
                                                Quick Stats
                                            </h6>
                                            <div
                                                class="grid grid-cols-2 gap-2 text-xs"
                                            >
                                                <div
                                                    class="flex items-center text-blue-600 dark:text-blue-400"
                                                >
                                                    <Bell
                                                        class="w-3 h-3 mr-1"
                                                    />
                                                    {{ unreadCount }} Unread
                                                </div>
                                                <div
                                                    class="flex items-center text-green-600 dark:text-green-400"
                                                >
                                                    <CheckCircle
                                                        class="w-3 h-3 mr-1"
                                                    />
                                                    {{ readCount }} Read
                                                </div>
                                                <div
                                                    class="flex items-center text-purple-600 dark:text-purple-400"
                                                >
                                                    <Clock
                                                        class="w-3 h-3 mr-1"
                                                    />
                                                    {{
                                                        recentNotificationsCount
                                                    }}
                                                    Recent
                                                </div>
                                                <div
                                                    class="flex items-center text-gray-600 dark:text-gray-400"
                                                >
                                                    <Archive
                                                        class="w-3 h-3 mr-1"
                                                    />
                                                    {{
                                                        notifications.length
                                                    }}
                                                    Total
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="flex items-center justify-between p-4 border-t border-gray-200 dark:border-gray-600"
                                    >
                                        <button
                                            @click="clearFilters"
                                            class="flex items-center text-sm text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200"
                                        >
                                            <RefreshCw
                                                class="w-4 h-4 mr-1"
                                                :class="{
                                                    'animate-spin': isResetting,
                                                }"
                                            />
                                            Reset
                                        </button>
                                        <button
                                            @click="markAllAsRead"
                                            class="flex items-center text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                        >
                                            <CheckCircle class="w-4 h-4 mr-1" />
                                            Mark All Read
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Bulk Actions -->
                            <div
                                class="relative"
                                v-if="selectedNotifications.length > 0"
                            >
                                <button
                                    @click="toggleBulkActions"
                                    class="flex items-center px-4 py-2 text-sm font-medium text-green-700 bg-green-50 border border-green-200 rounded-sm hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800 dark:hover:bg-green-900/30"
                                >
                                    <CheckSquare class="w-4 h-4 mr-2" />
                                    {{ selectedNotifications.length }} Selected
                                    <ChevronDown class="ml-1 w-4 h-4" />
                                </button>

                                <div
                                    v-if="showBulkActions"
                                    class="fixed z-[1000] mt-1 w-48 bg-white rounded-lg shadow-lg border border-gray-200 dark:bg-gray-700 dark:border-gray-600"
                                    :style="bulkActionsStyle"
                                >
                                    <div class="py-1">
                                        <button
                                            @click="bulkMarkAsRead"
                                            class="flex items-center w-full px-4 py-2 text-sm text-blue-600 dark:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                        >
                                            <CheckCircle class="w-4 h-4 mr-3" />
                                            Mark as Read
                                        </button>
                                        <button
                                            @click="bulkDelete"
                                            class="flex items-center w-full px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                        >
                                            <Trash2 class="w-4 h-4 mr-3" />
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notifications Grid -->
                <div class="p-4">
                    <div
                        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"
                    >
                        <div
                            v-for="notification in filteredNotifications"
                            :key="notification.id"
                            class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-all duration-200 cursor-pointer transform hover:-translate-y-1"
                            :class="{
                                'border-l-4 border-blue-500 bg-blue-50 dark:bg-blue-900/20':
                                    notification.unread,
                                'border-l-4 border-green-500':
                                    !notification.unread &&
                                    notification.type === 'announcement',
                                'border-l-4 border-orange-500':
                                    !notification.unread &&
                                    notification.type === 'overdue_warning',
                                'ring-2 ring-blue-200 dark:ring-blue-800':
                                    selectedNotifications.includes(
                                        notification.id
                                    ),
                            }"
                            @click="
                                toggleNotificationSelection(notification.id)
                            "
                        >
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center space-x-2">
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
                                    <span
                                        class="text-xs font-medium px-2 py-1 rounded-full capitalize"
                                        :class="getTypeBadgeClass(notification)"
                                    >
                                        {{
                                            formatNotificationType(
                                                notification.type
                                            )
                                        }}
                                    </span>
                                </div>
                                <div class="flex items-center space-x-1">
                                    <input
                                        type="checkbox"
                                        :checked="
                                            selectedNotifications.includes(
                                                notification.id
                                            )
                                        "
                                        @click.stop="
                                            toggleNotificationSelection(
                                                notification.id
                                            )
                                        "
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                    />
                                    <button
                                        @click.stop="
                                            toggleNotificationMenu(
                                                notification.id
                                            )
                                        "
                                        class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded"
                                    >
                                        <MoreHorizontal class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>

                            <!-- Notification Content -->
                            <div class="mb-3">
                                <h3
                                    class="text-sm font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2"
                                >
                                    {{ getNotificationTitle(notification) }}
                                </h3>
                                <p
                                    class="text-xs text-gray-600 dark:text-gray-300 line-clamp-3"
                                >
                                    {{ notification.message }}
                                </p>
                            </div>

                            <!-- Notification Metadata -->
                            <div
                                class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400"
                            >
                                <div class="flex items-center space-x-2">
                                    <Clock class="w-3 h-3" />
                                    <span>{{
                                        getRelativeTime(
                                            getDateField(notification)
                                        )
                                    }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span
                                        v-if="notification.unread"
                                        class="flex items-center text-blue-600 dark:text-blue-400"
                                    >
                                        <Circle
                                            class="w-2 h-2 fill-current mr-1"
                                        />
                                        Unread
                                    </span>
                                    <span
                                        v-else
                                        class="flex items-center text-green-600 dark:text-green-400"
                                    >
                                        <CheckCircle class="w-3 h-3 mr-1" />
                                        Read
                                    </span>
                                </div>
                            </div>

                            <!-- Action Menu Dropdown -->
                            <div
                                v-if="
                                    activeNotificationMenu === notification.id
                                "
                                class="absolute right-2 top-12 z-10 w-32 bg-white dark:bg-gray-700 rounded-md shadow-lg border border-gray-200 dark:border-gray-600"
                            >
                                <div class="py-1">
                                    <button
                                        @click.stop="
                                            markAsRead(notification.id)
                                        "
                                        class="flex items-center w-full px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600"
                                    >
                                        <CheckCircle class="w-4 h-4 mr-2" />
                                        Mark Read
                                    </button>
                                    <button
                                        @click.stop="
                                            deleteNotification(notification.id)
                                        "
                                        class="flex items-center w-full px-3 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-600"
                                    >
                                        <Trash2 class="w-4 h-4 mr-2" />
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div
                            v-if="filteredNotifications.length === 0"
                            class="col-span-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-8 text-center"
                        >
                            <div
                                class="flex flex-col items-center justify-center space-y-3"
                            >
                                <div
                                    class="p-4 bg-gray-100 dark:bg-gray-700 rounded-full"
                                >
                                    <Bell
                                        class="w-8 h-8 text-gray-400 dark:text-gray-500"
                                    />
                                </div>
                                <h3
                                    class="text-lg font-semibold text-gray-500 dark:text-gray-400"
                                >
                                    No notifications found
                                </h3>
                                <p
                                    class="text-sm text-gray-400 dark:text-gray-500 max-w-md"
                                >
                                    {{
                                        filters.search ||
                                        filters.status ||
                                        filters.types.length > 0
                                            ? "Try adjusting your filters or search keywords."
                                            : "All caught up! No new notifications."
                                    }}
                                </p>
                                <button
                                    v-if="
                                        filters.search ||
                                        filters.status ||
                                        filters.types.length > 0
                                    "
                                    @click="clearFilters"
                                    class="mt-2 px-4 py-2 text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                >
                                    Clear filters
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Load More Button (if paginated) -->
                <div
                    v-if="hasMoreNotifications"
                    class="p-4 border-t border-gray-200 dark:border-gray-700 text-center"
                >
                    <button
                        @click="loadMore"
                        class="px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-md hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800 dark:hover:bg-blue-900/30"
                    >
                        Load More Notifications
                    </button>
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
import {
    Search,
    Filter,
    ChevronDown,
    RefreshCw,
    Bell,
    CheckCircle,
    Clock,
    Archive,
    CheckSquare,
    Trash2,
    MoreHorizontal,
    Circle,
    AlertTriangle,
    Megaphone,
    Calendar,
    UserCheck,
} from "lucide-vue-next";

const page = usePage();

const props = defineProps({
    notifications: Array,
    errors: Object,
    filters: Object,
});

// Reactive state
const filters = ref({
    search: props.filters?.search || "",
    status: props.filters?.status || "",
    types: props.filters?.types || [],
    sort: props.filters?.sort || "created_at",
    order: props.filters?.order || "desc",
});

const showFilterDropdown = ref(false);
const showBulkActions = ref(false);
const isResetting = ref(false);
const activeNotificationMenu = ref(null);
const filterDropdownStyle = ref({});
const bulkActionsStyle = ref({});
const filterButton = ref(null);
const bulkActionsButton = ref(null);
const selectedNotifications = ref([]);

// Constants
const statusOptions = [
    { value: "", label: "All Status" },
    { value: "unread", label: "Unread" },
    { value: "read", label: "Read" },
];

const typeOptions = [
    { value: "announcement", label: "Announcements" },
    { value: "overdue_warning", label: "Overdue Warnings" },
    { value: "report_status", label: "Report Updates" },
    { value: "system", label: "System" },
];

// Computed properties
const filteredNotifications = computed(() => {
    let filtered = [...props.notifications];

    // Apply search filter
    if (filters.value.search) {
        const searchTerm = filters.value.search.toLowerCase();
        filtered = filtered.filter(
            (notification) =>
                notification.type.toLowerCase().includes(searchTerm) ||
                notification.message.toLowerCase().includes(searchTerm)
        );
    }

    // Apply status filter
    if (filters.value.status === "unread") {
        filtered = filtered.filter((n) => n.unread);
    } else if (filters.value.status === "read") {
        filtered = filtered.filter((n) => !n.unread);
    }

    // Apply type filter
    if (filters.value.types.length > 0) {
        filtered = filtered.filter((n) => filters.value.types.includes(n.type));
    }

    // Apply sorting
    filtered.sort((a, b) => {
        let aValue = a[filters.value.sort];
        let bValue = b[filters.value.sort];

        if (
            filters.value.sort === "created_at" ||
            filters.value.sort === "updated_at"
        ) {
            aValue = new Date(aValue).getTime();
            bValue = new Date(bValue).getTime();
        }

        if (typeof aValue === "string") {
            aValue = aValue.toLowerCase();
            bValue = bValue.toLowerCase();
        }

        if (filters.value.order === "asc") {
            return aValue > bValue ? 1 : -1;
        } else {
            return aValue < bValue ? 1 : -1;
        }
    });

    return filtered;
});

const unreadCount = computed(
    () => props.notifications.filter((n) => n.unread).length
);
const readCount = computed(
    () => props.notifications.length - unreadCount.value
);
const recentNotificationsCount = computed(() => {
    const oneWeekAgo = new Date();
    oneWeekAgo.setDate(oneWeekAgo.getDate() - 7);
    return props.notifications.filter((notification) => {
        const notificationDate = new Date(getDateField(notification));
        return notificationDate >= oneWeekAgo;
    }).length;
});

const hasMoreNotifications = computed(() => {
    // You can implement pagination logic here
    return false;
});

// Dropdown handlers
const toggleFilterDropdown = async () => {
    showFilterDropdown.value = !showFilterDropdown.value;
    if (showFilterDropdown.value && filterButton.value) {
        await nextTick();
        const rect = filterButton.value.getBoundingClientRect();
        const dropdownWidth = 224;
        filterDropdownStyle.value = {
            left: `${rect.right - dropdownWidth}px`,
            top: `${rect.bottom + 8}px`,
            position: "fixed",
        };
    }
};

const toggleBulkActions = async () => {
    showBulkActions.value = !showBulkActions.value;
    if (showBulkActions.value && bulkActionsButton.value) {
        await nextTick();
        const rect = bulkActionsButton.value.getBoundingClientRect();
        const dropdownWidth = 192;
        bulkActionsStyle.value = {
            left: `${rect.right - dropdownWidth}px`,
            top: `${rect.bottom + 8}px`,
            position: "fixed",
        };
    }
};

const toggleNotificationMenu = (notificationId) => {
    if (activeNotificationMenu.value === notificationId) {
        activeNotificationMenu.value = null;
    } else {
        activeNotificationMenu.value = notificationId;
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

// Click outside handler
const handleClickOutside = (event) => {
    const isFilterClick =
        filterButton.value?.contains(event.target) ||
        (showFilterDropdown.value &&
            event.target.closest(".fixed.z-\\[1000\\]"));

    const isBulkActionsClick =
        bulkActionsButton.value?.contains(event.target) ||
        (showBulkActions.value && event.target.closest(".fixed.z-\\[1000\\]"));

    if (!isFilterClick) {
        showFilterDropdown.value = false;
    }

    if (!isBulkActionsClick) {
        showBulkActions.value = false;
    }

    if (!event.target.closest(".absolute.right-2")) {
        activeNotificationMenu.value = null;
    }
};

onMounted(() => {
    document.addEventListener("click", handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener("click", handleClickOutside);
});

// Filter methods
const updateStatusFilter = (status) => {
    filters.value.status = status;
    showFilterDropdown.value = false;
    applyFilters();
};

const applyFilters = () => {
    latestRequestId++;
    debouncedFetchNotifications(filters.value, latestRequestId);
};

const clearFilters = () => {
    isResetting.value = true;
    setTimeout(() => {
        filters.value = {
            search: "",
            status: "",
            types: [],
            sort: "created_at",
            order: "desc",
        };
        selectedNotifications.value = [];
        isResetting.value = false;
        applyFilters();
    }, 500);
};

// Data fetching
let latestRequestId = 0;

const debouncedFetchNotifications = debounce((filters, requestId) => {
    router.get("/admin/notifications", pickBy(filters), {
        preserveState: true,
        replace: true,
        onSuccess: () => {
            if (requestId === latestRequestId) {
                // Handle success if needed
            }
        },
        onError: (errors) => {
            if (requestId === latestRequestId) {
                console.error("Fetch error:", errors);
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Failed to fetch notifications. Please try again.",
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 3000,
                });
            }
        },
    });
}, 300);

// Watch filter changes
watch(
    () => ({
        search: filters.value.search,
        status: filters.value.status,
        types: filters.value.types,
        sort: filters.value.sort,
        order: filters.value.order,
    }),
    (newFilters) => {
        latestRequestId++;
        debouncedFetchNotifications(newFilters, latestRequestId);
    },
    { deep: true }
);

// Notification actions
const markAsRead = async (notificationId) => {
    try {
        await router.post(
            route("notifications.mark-read"),
            { notification_id: notificationId },
            {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        toast: true,
                        position: "top-end",
                        icon: "success",
                        title: "Notification marked as read",
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                    });
                },
            }
        );
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Failed to mark notification as read.",
        });
    }
};

const markAllAsRead = async () => {
    try {
        await router.post(
            route("notifications.mark-all-read"),
            {},
            {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        toast: true,
                        position: "top-end",
                        icon: "success",
                        title: "All notifications marked as read",
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                    });
                    showFilterDropdown.value = false;
                },
            }
        );
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Failed to mark all notifications as read.",
        });
    }
};

const bulkMarkAsRead = async () => {
    if (selectedNotifications.value.length === 0) return;

    try {
        await router.post(
            route("notifications.bulk-mark-read"),
            { notification_ids: selectedNotifications.value },
            {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        toast: true,
                        position: "top-end",
                        icon: "success",
                        title: `${selectedNotifications.value.length} notifications marked as read`,
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                    });
                    selectedNotifications.value = [];
                    showBulkActions.value = false;
                },
            }
        );
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Failed to mark notifications as read.",
        });
    }
};

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
        try {
            await router.delete(
                route("notifications.destroy", notificationId),
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        Swal.fire({
                            toast: true,
                            position: "top-end",
                            icon: "success",
                            title: "Notification deleted",
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true,
                        });
                    },
                }
            );
        } catch (error) {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Failed to delete notification.",
            });
        }
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
        try {
            await router.post(
                route("notifications.bulk-delete"),
                { notification_ids: selectedNotifications.value },
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        Swal.fire({
                            toast: true,
                            position: "top-end",
                            icon: "success",
                            title: `${selectedNotifications.value.length} notifications deleted`,
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true,
                        });
                        selectedNotifications.value = [];
                        showBulkActions.value = false;
                    },
                }
            );
        } catch (error) {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Failed to delete notifications.",
            });
        }
    }
};

const loadMore = () => {
    // Implement load more functionality if using pagination
    console.log("Load more notifications");
};

// Helper functions
const getNotificationIcon = (notification) => {
    switch (notification.type) {
        case "announcement":
            return Megaphone;
        case "overdue_warning":
            return AlertTriangle;
        case "report_status":
            return UserCheck;
        case "system":
            return Bell;
        default:
            return Bell;
    }
};

const getNotificationIconClass = (notification) => {
    switch (notification.type) {
        case "announcement":
            return "bg-blue-100 dark:bg-blue-900/30";
        case "overdue_warning":
            return "bg-orange-100 dark:bg-orange-900/30";
        case "report_status":
            return "bg-green-100 dark:bg-green-900/30";
        case "system":
            return "bg-purple-100 dark:bg-purple-900/30";
        default:
            return "bg-gray-100 dark:bg-gray-700";
    }
};

const getNotificationIconColor = (notification) => {
    switch (notification.type) {
        case "announcement":
            return "text-blue-600 dark:text-blue-400";
        case "overdue_warning":
            return "text-orange-600 dark:text-orange-400";
        case "report_status":
            return "text-green-600 dark:text-green-400";
        case "system":
            return "text-purple-600 dark:text-purple-400";
        default:
            return "text-gray-600 dark:text-gray-400";
    }
};

const getNotificationTitle = (notification) => {
    const typeMap = {
        announcement: "New Announcement",
        overdue_warning: "Overdue Warning",
        report_status: "Report Status Update",
        system: "System Notification",
    };
    return typeMap[notification.type] || "Notification";
};

const getTypeBadgeClass = (notification) => {
    switch (notification.type) {
        case "announcement":
            return "bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200";
        case "overdue_warning":
            return "bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200";
        case "report_status":
            return "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200";
        case "system":
            return "bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200";
        default:
            return "bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300";
    }
};

const formatNotificationType = (type) => {
    return type.replace(/_/g, " ");
};

const getDateField = (notification) => {
    if (notification.type === "overdue_warning") return notification.due_date;
    if (notification.type === "announcement") return notification.created_at;
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

.line-clamp-2 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
}

.line-clamp-3 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 3;
}

.cursor-pointer {
    cursor: pointer;
}
</style>
