<template>
    <CustomerLayout>
        <div class="bg-gray-50 px-2 py-0">
            <div class="overflow-hidden">
                <!-- Header Section with Search and Filters on right -->
                <div class="py-2 border-b border-gray-200 bg-gray-50">
                    <div
                        class="flex flex-col md:flex-row md:items-center justify-between gap-4"
                    >
                        <!-- Left side - Title and description -->
                        <div class="flex-1">
                            <h1 class="text-xl font-semibold text-gray-900">
                                Notifications
                            </h1>
                            <p class="text-sm text-gray-600 mt-1">
                                Stay updated with your account activities
                            </p>
                        </div>

                        <!-- Right side - Search and Filters -->
                        <div
                            class="flex flex-col sm:flex-row items-start sm:items-center gap-3"
                        >
                            <!-- Search -->
                            <div class="w-full sm:w-auto">
                                <div class="relative">
                                    <div
                                        class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                                    >
                                        <Search class="w-4 h-4 text-gray-400" />
                                    </div>
                                    <input
                                        v-model="filters.search"
                                        type="text"
                                        class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:w-64"
                                        placeholder="Search notifications..."
                                        @keyup.enter="applyFilters"
                                    />
                                </div>
                            </div>

                            <!-- Filter Controls -->
                            <div class="flex flex-wrap items-center gap-2">
                                <!-- Type Filter -->
                                <div class="relative">
                                    <select
                                        v-model="filters.type"
                                        @change="applyFilters"
                                        class="w-[115px] spx-3 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    >
                                        <option value="">All Types</option>
                                        <option value="records">Records</option>
                                        <option value="reports">Reports</option>
                                        <option value="announcements">
                                            Announcements
                                        </option>
                                    </select>
                                </div>

                                <!-- Status Filter -->
                                <div class="relative">
                                    <select
                                        v-model="filters.status"
                                        @change="applyFilters"
                                        class="w-[110px] px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    >
                                        <option value="">All Status</option>
                                        <option value="unread">Unread</option>
                                        <option value="read">Read</option>
                                    </select>
                                </div>

                                <!-- Sort -->
                                <div class="relative">
                                    <select
                                        v-model="filters.sort"
                                        @change="applyFilters"
                                        class="w-[130px] px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    >
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
                                    @click="clearFilters"
                                    class="flex items-center px-3 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50"
                                >
                                    <RefreshCw class="w-4 h-4 mr-2" />

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
                    <!-- Table with Scrollable Body -->
                    <div class="flex-1 overflow-x-auto overflow-y-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 sticky top-0 z-10">
                                <tr>
                                    <th
                                        class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        <div
                                            class="flex items-center space-x-2"
                                        >
                                            <input
                                                type="checkbox"
                                                :checked="allSelected"
                                                @change="toggleSelectAll"
                                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500"
                                            />
                                            <span>Notification</span>
                                        </div>
                                    </th>
                                    <th
                                        class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Type
                                    </th>
                                    <th
                                        class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Date
                                    </th>
                                    <th
                                        class="px-2 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr
                                    v-for="notification in paginatedNotifications"
                                    :key="notification.id"
                                    class="hover:bg-gray-50 transition-colors"
                                >
                                    <td class="px-2 py-4 whitespace-nowrap">
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
                                                class="mt-1 w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500"
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
                                                            class="text-sm font-medium text-gray-900 line-clamp-1 hover:text-blue-600 transition-colors"
                                                        >
                                                            {{
                                                                getNotificationTitle(
                                                                    notification
                                                                )
                                                            }}
                                                        </p>
                                                        <p
                                                            class="text-sm text-gray-600 mt-1 line-clamp-2"
                                                        >
                                                            {{
                                                                notification.message
                                                            }}
                                                        </p>
                                                        <div
                                                            class="flex items-center space-x-2 mt-1"
                                                        >
                                                            <span
                                                                v-if="
                                                                    notification.unread
                                                                "
                                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                                                            >
                                                                New
                                                            </span>
                                                            <span
                                                                v-if="
                                                                    notification.important
                                                                "
                                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800"
                                                            >
                                                                Important
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-2 py-4 whitespace-nowrap">
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
                                        class="px-2 py-4 whitespace-nowrap text-sm text-gray-600"
                                    >
                                        <div
                                            class="flex items-center space-x-1"
                                        >
                                            <Clock class="w-4 h-4" />
                                            <span>{{
                                                getRelativeTime(
                                                    notification.created_at
                                                )
                                            }}</span>
                                        </div>
                                        <div class="text-xs text-gray-400 mt-1">
                                            {{
                                                formatDate(
                                                    notification.created_at
                                                )
                                            }}
                                        </div>
                                    </td>
                                    <td
                                        class="px-2 py-4 whitespace-nowrap text-right text-sm font-medium"
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
                                                class="text-blue-600 hover:text-blue-900 transition-colors p-1 rounded hover:bg-blue-50"
                                                title="View Details"
                                            >
                                                <Eye class="w-4 h-4" />
                                            </button>
                                            <button
                                                v-if="notification.unread"
                                                @click="
                                                    markAsRead(notification.id)
                                                "
                                                class="text-green-600 hover:text-green-900 transition-colors p-1 rounded hover:bg-green-50"
                                                title="Mark as Read"
                                                :disabled="
                                                    markingAsRead ===
                                                    notification.id
                                                "
                                            >
                                                <Check class="w-4 h-4" />
                                            </button>
                                            <button
                                                @click="
                                                    deleteNotification(
                                                        notification.id
                                                    )
                                                "
                                                class="text-red-600 hover:text-red-900 transition-colors p-1 rounded hover:bg-red-50"
                                                title="Delete"
                                                :disabled="
                                                    deletingNotification ===
                                                    notification.id
                                                "
                                            >
                                                <Trash2 class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Empty State -->
                                <tr v-if="filteredNotifications.length === 0">
                                    <td
                                        colspan="4"
                                        class="px-2 py-24 text-center"
                                    >
                                        <div
                                            class="flex flex-col items-center justify-center space-y-4"
                                        >
                                            <div
                                                class="p-4 bg-gray-100 rounded-full"
                                            >
                                                <Bell
                                                    class="w-20 h-20 text-gray-300"
                                                />
                                            </div>
                                            <h3
                                                class="text-2xl font-medium text-gray-500"
                                            >
                                                No notifications found
                                            </h3>
                                            <p
                                                class="text-sm text-gray-400 max-w-md"
                                            >
                                                {{
                                                    filters.search ||
                                                    filters.type ||
                                                    filters.status
                                                        ? "Try adjusting your filters or search keywords."
                                                        : "All caught up! No notifications to display."
                                                }}
                                            </p>
                                            <button
                                                v-if="
                                                    filters.search ||
                                                    filters.type ||
                                                    filters.status
                                                "
                                                @click="clearFilters"
                                                class="mt-2 px-4 py-2 text-sm text-blue-600 hover:text-blue-800"
                                            >
                                                Clear all filters
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div
                        v-if="filteredNotifications.length > 0"
                        class="flex-shrink-0 border-t border-gray-200 bg-white"
                    >
                        <div
                            class="flex items-center justify-between px-2 py-4"
                        >
                            <div class="text-sm text-gray-600">
                                Showing {{ startIndex + 1 }} to
                                {{ endIndex }} of
                                {{ filteredNotifications.length }} notifications
                            </div>
                            <div class="flex items-center space-x-2">
                                <button
                                    @click="prevPage"
                                    :disabled="currentPage === 1"
                                    class="px-3 py-1.5 text-sm rounded border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    Previous
                                </button>
                                <div class="flex space-x-1">
                                    <button
                                        v-for="page in totalPages"
                                        :key="page"
                                        @click="currentPage = page"
                                        :class="[
                                            'px-3 py-1.5 text-sm rounded min-w-8',
                                            currentPage === page
                                                ? 'bg-blue-600 text-white'
                                                : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50',
                                        ]"
                                    >
                                        {{ page }}
                                    </button>
                                </div>
                                <button
                                    @click="nextPage"
                                    :disabled="currentPage === totalPages"
                                    class="px-3 py-1.5 text-sm rounded border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    Next
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bulk Actions Bar -->
                <div
                    v-if="selectedNotifications.length > 0"
                    class="px-2 py-4 border-t border-gray-200 bg-blue-50"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <CheckSquare class="w-4 h-4 text-blue-600" />
                            <span class="text-sm font-medium text-blue-800">
                                {{ selectedNotifications.length }}
                                notification(s) selected
                            </span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <button
                                @click="bulkDelete"
                                class="px-3 py-1.5 text-sm text-white bg-red-600 border border-red-700 rounded-md hover:bg-red-700 transition-colors"
                                :disabled="bulkActionLoading"
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
                    class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
                >
                    <!-- Modal Header -->
                    <div class="bg-white px-2 py-4 border-b border-gray-200">
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
                                                : 'text-gray-600'
                                        "
                                    />
                                </div>
                                <div>
                                    <h3
                                        class="text-lg font-semibold text-gray-900"
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
                                    <p class="text-sm text-gray-500 mt-1">
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
                                class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-full hover:bg-gray-100"
                            >
                                <X class="w-5 h-5" />
                            </button>
                        </div>
                    </div>

                    <!-- Modal Content -->
                    <div class="bg-white px-2 py-4">
                        <div class="space-y-4">
                            <!-- Notification Message -->
                            <div>
                                <h4
                                    class="text-sm font-medium text-gray-900 mb-2"
                                >
                                    Message
                                </h4>
                                <p
                                    class="text-sm text-gray-600 bg-gray-50 rounded-lg p-3"
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
                                    <span class="font-medium text-gray-500"
                                        >Type:</span
                                    >
                                    <p class="text-gray-900 mt-1">
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
                                    <span class="font-medium text-gray-500"
                                        >Date:</span
                                    >
                                    <p class="text-gray-900 mt-1">
                                        {{
                                            selectedNotification
                                                ? formatDate(
                                                      selectedNotification.created_at
                                                  )
                                                : ""
                                        }}
                                    </p>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-500"
                                        >Status:</span
                                    >
                                    <p class="text-gray-900 mt-1">
                                        {{
                                            selectedNotification
                                                ? selectedNotification.unread
                                                    ? "Unread"
                                                    : "Read"
                                                : ""
                                        }}
                                    </p>
                                </div>
                                <div
                                    v-if="
                                        selectedNotification &&
                                        selectedNotification.important
                                    "
                                >
                                    <span class="font-medium text-gray-500"
                                        >Priority:</span
                                    >
                                    <p class="text-gray-900 mt-1">Important</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="bg-gray-50 px-2 py-3 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">
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
                                        getActionUrl(selectedNotification)
                                    "
                                    @click="goToAction(selectedNotification)"
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-700 rounded-md hover:bg-blue-700 transition-colors"
                                >
                                    {{ getActionLabel(selectedNotification) }}
                                </button>

                                <button
                                    v-if="
                                        selectedNotification &&
                                        selectedNotification.unread
                                    "
                                    @click="markAsRead(selectedNotification.id)"
                                    class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-green-700 rounded-md hover:bg-green-700 transition-colors"
                                >
                                    Mark as Read
                                </button>
                                <button
                                    v-if="selectedNotification"
                                    @click="
                                        deleteNotification(
                                            selectedNotification.id
                                        )
                                    "
                                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-red-700 rounded-md hover:bg-red-700 transition-colors"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>

<script setup>
import CustomerLayout from "@/Layouts/CustomerLayout.vue";
import { ref, computed, onMounted, onUnmounted } from "vue";
import { router } from "@inertiajs/vue3";
import {
    Search,
    RefreshCw,
    Bell,
    Clock,
    CheckSquare,
    Trash2,
    AlertTriangle,
    Megaphone,
    Eye,
    Check,
    X,
} from "lucide-vue-next";
import Swal from "sweetalert2";

const props = defineProps({
    notifications: {
        type: Array,
        default: () => [],
    },
});

// Reactive state
const filters = ref({
    search: "",
    type: "",
    status: "",
    sort: "created_at",
});

const selectedNotifications = ref([]);
const showNotificationModal = ref(false);
const selectedNotification = ref(null);
const markingAsRead = ref(null);
const deletingNotification = ref(null);
const bulkActionLoading = ref(false);

// Pagination
const currentPage = ref(1);
const itemsPerPage = 10;

// Computed properties
const filteredNotifications = computed(() => {
    let filtered = [...props.notifications];

    // Apply search filter
    if (filters.value.search) {
        const searchTerm = filters.value.search.toLowerCase();
        filtered = filtered.filter(
            (notification) =>
                notification.type.toLowerCase().includes(searchTerm) ||
                notification.message.toLowerCase().includes(searchTerm) ||
                (notification.title &&
                    notification.title.toLowerCase().includes(searchTerm))
        );
    }

    // Apply type filter
    if (filters.value.type) {
        filtered = filtered.filter((n) => n.type === filters.value.type);
    }

    // Apply status filter
    if (filters.value.status) {
        if (filters.value.status === "unread") {
            filtered = filtered.filter((n) => n.unread);
        } else if (filters.value.status === "read") {
            filtered = filtered.filter((n) => !n.unread);
        }
    }

    // Apply sorting
    filtered.sort((a, b) => {
        let aValue, bValue;

        switch (filters.value.sort) {
            case "created_at_asc":
                aValue = new Date(a.created_at).getTime();
                bValue = new Date(b.created_at).getTime();
                return aValue - bValue;
            case "type":
                aValue = a.type.toLowerCase();
                bValue = b.type.toLowerCase();
                return aValue.localeCompare(bValue);
            default: // 'created_at' - newest first
                aValue = new Date(a.created_at).getTime();
                bValue = new Date(b.created_at).getTime();
                return bValue - aValue;
        }
    });

    return filtered;
});

// Pagination computed properties
const totalPages = computed(() => {
    return Math.ceil(filteredNotifications.value.length / itemsPerPage);
});

const startIndex = computed(() => {
    return (currentPage.value - 1) * itemsPerPage;
});

const endIndex = computed(() => {
    return Math.min(
        startIndex.value + itemsPerPage,
        filteredNotifications.value.length
    );
});

const paginatedNotifications = computed(() => {
    return filteredNotifications.value.slice(startIndex.value, endIndex.value);
});

const allSelected = computed(() => {
    return (
        paginatedNotifications.value.length > 0 &&
        selectedNotifications.value.length ===
            paginatedNotifications.value.length
    );
});

// Pagination methods
const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
    }
};

const prevPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
    }
};

// Click handler for notifications
const handleNotificationClick = (notification) => {
    // First mark as read if needed
    if (notification.unread) {
        markAsRead(notification.id);
    }

    // Then navigate to the action URL
    const actionUrl = getActionUrl(notification);
    if (actionUrl) {
        router.visit(actionUrl);
    } else {
        // Fallback: open the modal
        viewNotification(notification);
    }
};

// Get action URL based on notification type
const getActionUrl = (notification) => {
    // Check if notification has direct action_url
    if (notification.action_url) {
        return notification.action_url;
    }

    // Fallback based on notification type
    switch (notification.type) {
        case "bill_overdue":
        case "bill_due_soon":
        case "bill_final_reminder":
        case "bill_due_today":
            return "/customer/usage";
        case "new_report":
        case "report_update":
            return "/customer/reports";
        case "announcement":
            return "/customer/announcements";
        default:
            return null;
    }
};

// Get action label for button
const getActionLabel = (notification) => {
    const url = getActionUrl(notification);
    if (!url) return "Page";

    if (url.includes("/reports")) return "Reports";
    if (url.includes("/announcements")) return "Announcements";
    if (url.includes("/usage")) return "Usage";
    if (url.includes("/bills")) return "Bills";
    if (url.includes("/dashboard")) return "Dashboard";

    return "Page";
};

// Go to action from modal
const goToAction = (notification) => {
    const actionUrl = getActionUrl(notification);
    if (actionUrl) {
        closeModal();
        router.visit(actionUrl);
    }
};

// Mark notification as read
const markAsRead = async (notificationId) => {
    markingAsRead.value = notificationId;
    try {
        await fetch(`/customer/notifications/${notificationId}/read`, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
            },
        });
        router.reload();
    } catch (error) {
        console.error("Failed to mark notification as read:", error);
    } finally {
        markingAsRead.value = null;
    }
};

// Modal handlers
const viewNotification = async (notification) => {
    selectedNotification.value = notification;
    showNotificationModal.value = true;
};

const closeModal = () => {
    showNotificationModal.value = false;
    selectedNotification.value = null;
};

// Selection handlers
const toggleSelectAll = () => {
    if (allSelected.value) {
        // Remove all paginated notifications from selection
        const paginatedIds = paginatedNotifications.value.map((n) => n.id);
        selectedNotifications.value = selectedNotifications.value.filter(
            (id) => !paginatedIds.includes(id)
        );
    } else {
        // Add all paginated notifications to selection
        const newSelection = paginatedNotifications.value.map((n) => n.id);
        selectedNotifications.value = [
            ...new Set([...selectedNotifications.value, ...newSelection]),
        ];
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

// Filter methods
const applyFilters = () => {
    currentPage.value = 1; // Reset to first page when filtering
    selectedNotifications.value = []; // Clear selection when filtering
};

const clearFilters = () => {
    filters.value = {
        search: "",
        type: "",
        status: "",
        sort: "created_at",
    };
    currentPage.value = 1;
    selectedNotifications.value = [];
};

// Notification actions
const deleteNotification = async (notificationId) => {
    const result = await Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "Cancel",
        reverseButtons: false,
        customClass: {
            confirmButton: "swal2-confirm",
            cancelButton: "swal2-cancel",
        },
    });

    if (!result.isConfirmed) {
        return;
    }

    deletingNotification.value = notificationId;
    try {
        await router.delete(`/customer/notifications/${notificationId}`);
    } catch (error) {
        console.error("Failed to delete notification:", error);
        deletingNotification.value = null;

        // Show error message
        Swal.fire({
            title: "Error!",
            text: "Failed to delete notification. Please try again.",
            icon: "error",
            confirmButtonColor: "#3085d6",
        });
    }
};

const bulkDelete = async () => {
    if (selectedNotifications.value.length === 0) return;

    const result = await Swal.fire({
        title: `Delete ${selectedNotifications.value.length} notifications?`,
        text: "This action cannot be undone!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: `Yes, delete ${selectedNotifications.value.length} notifications!`,
        cancelButtonText: "Cancel",
        reverseButtons: false,
        customClass: {
            confirmButton: "swal2-confirm",
            cancelButton: "swal2-cancel",
        },
    });

    if (!result.isConfirmed) {
        return;
    }

    bulkActionLoading.value = true;
    try {
        const formData = new FormData();
        formData.append(
            "notification_ids",
            JSON.stringify(selectedNotifications.value)
        );

        await router.post("/customer/notifications/bulk-delete", formData);
    } catch (error) {
        console.error("Failed to delete notifications:", error);
        bulkActionLoading.value = false;

        // Show error message
        Swal.fire({
            title: "Error!",
            text: "Failed to delete notifications. Please try again.",
            icon: "error",
            confirmButtonColor: "#3085d6",
        });
    }
};

// Helper functions
// Update getNotificationIcon function
const getNotificationIcon = (notification) => {
    switch (notification.type) {
        case "announcements":
            return Megaphone;
        case "records":
            return AlertTriangle;
        case "reports":
            return Bell;
        case "system":
            return Bell;
        default:
            return Bell;
    }
};

// Update getNotificationIconClass function
const getNotificationIconClass = (notification) => {
    switch (notification.type) {
        case "announcements":
            return "bg-blue-100";
        case "records":
            return "bg-orange-100";
        case "reports":
            return "bg-green-100";
        case "system":
            return "bg-purple-100";
        default:
            return "bg-gray-100";
    }
};

// Update getNotificationIconColor function
const getNotificationIconColor = (notification) => {
    switch (notification.type) {
        case "announcements":
            return "text-blue-600";
        case "records":
            return "text-orange-600";
        case "reports":
            return "text-green-600";
        case "system":
            return "text-purple-600";
        default:
            return "text-gray-600";
    }
};

// Update getTypeBadgeClass function
const getTypeBadgeClass = (notification) => {
    switch (notification.type) {
        case "announcements":
            return "bg-blue-100 text-blue-800";
        case "records":
            return "bg-orange-100 text-orange-800";
        case "reports":
            return "bg-green-100 text-green-800";
        case "system":
            return "bg-purple-100 text-purple-800";
        default:
            return "bg-gray-100 text-gray-800";
    }
};

// Update formatNotificationType function to properly format the new types
const formatNotificationType = (type) => {
    const typeMap = {
        records: "Records",
        reports: "Reports",
        announcements: "Announcements",
        system: "System",
    };

    return typeMap[type] || type.replace(/_/g, " ");
};

const getNotificationTitle = (notification) => {
    return notification.title || "Notification";
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

/* Make notification content clearly clickable */
.cursor-pointer {
    cursor: pointer;
}
</style>
