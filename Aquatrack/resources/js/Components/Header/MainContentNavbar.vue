<template>
    <nav
        class="bg-white border-b border-gray-200 px-6 py-[13px] shadow-sm dark:bg-gray-800 dark:border-gray-700 fixed right-0 top-0 z-40 transition-all duration-300 ease-in-out"
        :class="[
            isSidebarOpen ? 'left-64' : 'left-16',
            isMobileMenuOpen ? 'left-0' : '',
        ]"
        :style="navStyle"
    >
        <div class="flex justify-between items-center">
            <!-- Left section with breadcrumbs, mobile menu and search -->
            <div class="flex items-center flex-1 max-w-3xl">
                <!-- Mobile menu button -->
                <button
                    @click="$emit('toggle-mobile-menu')"
                    class="p-2 mr-3 text-gray-600 rounded-lg cursor-pointer md:hidden hover:text-gray-900 hover:bg-gray-100 focus:bg-gray-100 dark:focus:bg-gray-700 focus:ring-2 focus:ring-gray-100 dark:focus:ring-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white transition-colors duration-200"
                >
                    <Bars3Icon v-if="!isMobileMenuOpen" class="w-6 h-6" />
                    <XMarkIcon v-else class="w-6 h-6" />
                </button>

                <!-- Breadcrumbs -->
                <div class="hidden md:flex items-center space-x-2 text-sm">
                    <Link
                        :href="dashboardRoute"
                        class="flex items-center text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors duration-200"
                    >
                        <HomeIcon class="w-4 h-4 mr-1" />
                        <span class="font-medium capitalize">{{
                            userRole
                        }}</span>
                    </Link>
                    <ChevronRightIcon class="w-3 h-3 text-gray-400" />
                    <span
                        class="text-gray-900 dark:text-white font-semibold capitalize"
                        >{{ currentPageName }}</span
                    >
                </div>

                <!-- Mobile Breadcrumbs (simplified) -->
                <div class="flex md:hidden items-center space-x-2 text-sm ml-2">
                    <span
                        class="text-gray-900 dark:text-white font-semibold capitalize"
                        >{{ currentPageName }}</span
                    >
                </div>
            </div>

            <!-- Right section with notifications and user menu -->
            <div class="flex items-center space-x-3 ml-4">
                <!-- Notifications Button - Now opens modal instead of dropdown -->
                <div class="relative">
                    <button
                        type="button"
                        @click="showNotificationModal = true"
                        class="relative p-2 text-gray-500 rounded-full hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600 transition-all duration-200"
                    >
                        <BellIcon class="w-6 h-6" />
                        <span
                            v-if="unreadCount > 0"
                            class="absolute -top-1 -right-1 flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full border-2 border-white dark:border-gray-800 animate-pulse"
                        >
                            {{ unreadCount > 99 ? "99+" : unreadCount }}
                        </span>
                    </button>
                </div>

                <!-- User Dropdown -->
                <div class="relative">
                    <button
                        type="button"
                        @click.stop="toggleUserDropdown"
                        class="flex items-center space-x-3 text-sm rounded-full p-2 hover:bg-gray-100 dark:hover:bg-gray-700 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600 transition-all duration-200"
                        ref="userButton"
                    >
                        <img
                            v-if="user.avatar_url"
                            class="w-8 h-8 rounded-full border-2 border-gray-200 dark:border-gray-600 shadow-sm"
                            :src="user.avatar_url"
                            :alt="userDisplayName"
                        />
                        <div
                            v-else
                            class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-600 to-blue-500 text-white flex items-center justify-center font-medium text-sm border-2 border-gray-200 dark:border-gray-600 shadow-sm"
                        >
                            {{ userInitials }}
                        </div>
                        <div class="text-left hidden sm:block">
                            <p
                                class="text-sm font-medium text-gray-900 dark:text-white"
                            >
                                {{ userDisplayName }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{
                                    userRole.charAt(0).toUpperCase() +
                                    userRole.slice(1)
                                }}
                            </p>
                        </div>
                        <ChevronDownIcon class="w-4 h-4 text-gray-400" />
                    </button>

                    <!-- User Dropdown Menu -->
                    <div
                        v-show="isUserDropdownOpen"
                        v-click-outside="closeUserDropdown"
                        class="absolute right-0 top-full mt-2 z-50 w-64 text-base bg-white rounded-xl shadow-xl dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden transform transition-all duration-200"
                        :class="{
                            'scale-100 opacity-100': isUserDropdownOpen,
                            'scale-95 opacity-0': !isUserDropdownOpen,
                        }"
                    >
                        <div
                            class="py-4 px-4 bg-gradient-to-r from-gray-50 to-blue-50 dark:from-gray-700 dark:to-gray-600 border-b border-gray-100 dark:border-gray-600"
                        >
                            <div class="flex items-center space-x-3">
                                <img
                                    v-if="user.avatar_url"
                                    class="w-12 h-12 rounded-full border-2 border-white dark:border-gray-700 shadow-sm"
                                    :src="user.avatar_url"
                                    :alt="userDisplayName"
                                />
                                <div
                                    v-else
                                    class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-600 to-blue-500 text-white flex items-center justify-center font-medium border-2 border-white dark:border-gray-700 shadow-sm"
                                >
                                    {{ userInitials }}
                                </div>
                                <div>
                                    <p
                                        class="text-sm font-semibold text-gray-900 dark:text-white"
                                    >
                                        {{ userDisplayName }}
                                    </p>
                                    <p
                                        class="text-xs text-gray-600 dark:text-gray-300"
                                    >
                                        {{ user?.email }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="py-2">
                            <Link
                                href="/profile"
                                class="flex items-center gap-3 py-3 px-4 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200"
                                @click="closeUserDropdown"
                            >
                                <UserIcon
                                    class="w-4 h-4 text-gray-500 dark:text-gray-400"
                                />
                                <span>Profile Settings</span>
                            </Link>
                        </div>
                        <div
                            class="border-t border-gray-100 dark:border-gray-700 pt-2"
                        >
                            <button
                                @click.prevent="handleLogout"
                                class="flex items-center w-full gap-3 py-3 px-4 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-200"
                            >
                                <ArrowRightOnRectangleIcon class="w-4 h-4" />
                                <span>Sign out</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notification Modal -->
        <div
            v-if="showNotificationModal"
            class="fixed inset-0 z-50 overflow-y-auto"
        >
            <!-- Background -->
            <div
                class="fixed inset-0 bg-black bg-opacity-50"
                @click="showNotificationModal = false"
            ></div>

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
                                <span
                                    v-if="
                                        modalNotifications.length > 0 &&
                                        !modalLoading
                                    "
                                    class="ml-2 text-sm text-gray-500"
                                >
                                    ({{ modalUnreadCount }} unread)
                                </span>
                            </h3>
                            <div class="flex items-center space-x-2">
                                <button
                                    v-if="
                                        modalNotifications.length > 0 &&
                                        modalUnreadCount > 0
                                    "
                                    @click="markAllAsReadInModal"
                                    class="text-xs bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600 transition-colors"
                                    :disabled="markingAllAsRead"
                                >
                                    <span v-if="markingAllAsRead">...</span>
                                    <span v-else>Mark All Read</span>
                                </button>
                                <button
                                    @click="showNotificationModal = false"
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
                        </div>

                        <!-- Notifications List -->
                        <div class="max-h-96 overflow-y-auto">
                            <div v-if="modalLoading" class="text-center py-4">
                                <div
                                    class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto"
                                ></div>
                                <p class="mt-2 text-gray-500">
                                    Loading notifications...
                                </p>
                            </div>

                            <div
                                v-else-if="modalNotifications.length === 0"
                                class="text-center py-8 text-gray-500"
                            >
                                <div class="text-4xl mb-2">🔔</div>
                                <p>No notifications available</p>
                                <p class="text-sm mt-1">
                                    You're all caught up!
                                </p>
                            </div>

                            <div v-else class="space-y-3">
                                <div
                                    v-for="notification in modalNotifications"
                                    :key="notification.id"
                                    class="p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-all"
                                    :class="{
                                        'bg-blue-50 border-blue-200':
                                            notification.unread,
                                        'border-l-4 border-l-orange-500':
                                            notification.important,
                                    }"
                                    @click="
                                        handleNotificationClickInModal(
                                            notification
                                        )
                                    "
                                >
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0 text-xl">
                                            {{
                                                getNotificationIcon(
                                                    notification.type
                                                )
                                            }}
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
                                                <div
                                                    class="flex items-center space-x-2"
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
                                                        :class="
                                                            getStatusBadgeClass(
                                                                notification.type
                                                            )
                                                        "
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                    >
                                                        {{
                                                            formatNotificationType(
                                                                notification.type
                                                            )
                                                        }}
                                                    </span>
                                                </div>
                                            </div>
                                            <p
                                                class="text-sm text-gray-600 mt-1"
                                            >
                                                {{
                                                    notification.message ||
                                                    "No message available"
                                                }}
                                            </p>
                                            <div
                                                class="flex justify-between items-center mt-2"
                                            >
                                                <p
                                                    class="text-xs text-gray-400"
                                                >
                                                    {{
                                                        formatTime(
                                                            notification.created_at
                                                        )
                                                    }}
                                                </p>
                                                <button
                                                    v-if="notification.unread"
                                                    @click.stop="
                                                        markAsReadInModal(
                                                            notification.id
                                                        )
                                                    "
                                                    class="text-xs text-green-600 hover:text-green-800 transition-colors"
                                                >
                                                    Mark read
                                                </button>
                                            </div>
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
                        <!-- <button
                            type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                            @click="showNotificationModal = false"
                        >
                            Close
                        </button> -->
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted, watch } from "vue";
import { usePage, Link, router } from "@inertiajs/vue3";
import {
    HomeIcon,
    ChevronRightIcon,
    ChevronDownIcon,
    BellIcon,
    UserIcon,
    ArrowRightOnRectangleIcon,
    Bars3Icon,
    XMarkIcon,
} from "@heroicons/vue/24/outline";
import Swal from "sweetalert2";

const props = defineProps({
    isSidebarOpen: { type: Boolean, default: true },
    isMobileMenuOpen: { type: Boolean, default: false },
});

const emit = defineEmits(["toggle-sidebar", "toggle-mobile-menu", "logout"]);

const { props: pageProps } = usePage();

// User data
const user = computed(() => pageProps.auth?.user ?? {});
const userRole = computed(() => user.value?.role || "customer");
const userDisplayName = computed(() => user.value?.name || "Unknown User");
const userInitials = computed(() => {
    if (!user.value?.name) return "??";
    return user.value.name
        .split(" ")
        .map((n) => n.charAt(0))
        .slice(0, 2)
        .join("")
        .toUpperCase();
});

// Modal state
const showNotificationModal = ref(false);
const modalNotifications = ref([]);
const modalLoading = ref(false);
const markingAllAsRead = ref(false);

// Computed
const modalUnreadCount = computed(() => {
    return modalNotifications.value.filter((n) => n.unread).length;
});

const unreadCount = computed(() => {
    return modalUnreadCount.value;
});

// Breadcrumbs data
const currentPageName = computed(() => {
    const path = window.location.pathname;
    const segments = path.split("/").filter((segment) => segment);
    const roleSegments = ["admin", "staff", "customer"];
    const filteredSegments = segments.filter(
        (segment) => !roleSegments.includes(segment)
    );

    if (filteredSegments.length === 0) return "Dashboard";
    const lastSegment = filteredSegments[filteredSegments.length - 1];
    const pageNames = {
        dashboard: "Dashboard",
        reports: "Reports",
        announcements: "Announcements",
        billing: "Billing",
        profile: "Profile",
        settings: "Settings",
        users: "User Management",
        customers: "Customers",
        staff: "Staff Management",
        analytics: "Analytics",
        "water-quality": "Water Quality",
        payments: "Payments",
        notifications: "Notifications",
    };
    return (
        pageNames[lastSegment] ||
        lastSegment
            .split("-")
            .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
            .join(" ")
    );
});

// Routes
const dashboardRoute = computed(() =>
    userRole.value === "admin"
        ? "/admin/dashboard"
        : userRole.value === "staff"
        ? "/staff/dashboard"
        : "/customer/dashboard"
);
const notificationRoute = computed(() =>
    userRole.value === "admin"
        ? "/admin/notifications"
        : userRole.value === "staff"
        ? "/staff/notifications"
        : "/customer/notifications"
);

// Window width state
const windowWidth = ref(
    typeof window !== "undefined" ? window.innerWidth : 1024
);
const navStyle = computed(() => ({
    left:
        windowWidth.value < 768 ? "0" : props.isSidebarOpen ? "16rem" : "4rem",
}));

const handleResize = () => {
    windowWidth.value = window.innerWidth;
};

// Notification methods
const getNotificationIcon = (type) => {
    const icons = {
        info: "ℹ️",
        report_update: "🔄",
        announcement: "📢",
        bill_overdue: "🚨",
        bill_due_soon: "📅",
        bill_final_reminder: "⏰",
        bill_due_today: "⚠️",
        alert: "⚠️",
        reminder: "⏰",
        system: "⚙️",
        order: "📦",
    };
    return icons[type] || "🔔";
};

const getStatusBadgeClass = (type) => {
    const classes = {
        info: "bg-blue-100 text-blue-800",
        report_update: "bg-purple-100 text-purple-800",
        announcement: "bg-gray-100 text-gray-800",
        bill_overdue: "bg-red-100 text-red-800",
        bill_due_soon: "bg-orange-100 text-orange-800",
        bill_final_reminder: "bg-yellow-100 text-yellow-800",
        bill_due_today: "bg-red-100 text-red-800",
        alert: "bg-red-100 text-red-800",
        reminder: "bg-yellow-100 text-yellow-800",
    };
    return classes[type] || "bg-gray-100 text-gray-800";
};

const formatNotificationType = (type) => {
    const types = {
        info: "Information",
        report_update: "Report Update",
        announcement: "Announcement",
        bill_overdue: "Overdue",
        bill_due_soon: "Due Soon",
        bill_final_reminder: "Final Reminder",
        bill_due_today: "Due Today",
        alert: "Alert",
        reminder: "Reminder",
    };
    return types[type] || type;
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

// Modal methods
const fetchModalNotifications = async () => {
    modalLoading.value = true;
    try {
        const response = await fetch("/api/notifications", {
            headers: {
                Accept: "application/json",
                "X-Requested-With": "XMLHttpRequest",
            },
        });

        if (response.ok) {
            const data = await response.json();
            modalNotifications.value = data.notifications || [];
        } else {
            console.error("Failed to fetch notifications");
            modalNotifications.value = [];
        }
    } catch (error) {
        console.error("Error fetching notifications:", error);
        modalNotifications.value = [];
    } finally {
        modalLoading.value = false;
    }
};

const markAsReadInModal = async (id) => {
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
            // Update local state
            const notification = modalNotifications.value.find(
                (n) => n.id === id
            );
            if (notification) {
                notification.unread = false;
            }
        }
    } catch (error) {
        console.error("Failed to mark notification as read:", error);
    }
};

const markAllAsReadInModal = async () => {
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
            // Update all notifications to read
            modalNotifications.value.forEach((notification) => {
                notification.unread = false;
            });
        }
    } catch (error) {
        console.error("Failed to mark all notifications as read:", error);
    } finally {
        markingAllAsRead.value = false;
    }
};

const handleNotificationClickInModal = (notification) => {
    // Mark as read when clicked
    if (notification.unread) {
        markAsReadInModal(notification.id);
    }

    // Navigate based on action_url
    if (notification.action_url) {
        router.visit(notification.action_url);
        showNotificationModal.value = false;
    }
};

const viewAllNotifications = () => {
    router.visit(notificationRoute.value);
    showNotificationModal.value = false;
};

// User dropdown methods
const isUserDropdownOpen = ref(false);
const userButton = ref(null);

const toggleUserDropdown = () => {
    isUserDropdownOpen.value = !isUserDropdownOpen.value;
};

const closeUserDropdown = () => {
    isUserDropdownOpen.value = false;
};

const handleLogout = async () => {
    const result = await Swal.fire({
        title: "Are you sure?",
        text: "Do you want to log out?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, log out!",
        cancelButtonText: "Cancel",
    });
    if (result.isConfirmed) {
        closeUserDropdown();
        emit("logout");
    }
};

// Watch for modal open state
watch(showNotificationModal, (isOpen) => {
    if (isOpen) {
        fetchModalNotifications();
    }
});

// Click outside directive
const vClickOutside = {
    beforeMount(el, binding) {
        el.clickOutsideEvent = (event) => {
            if (!(el === event.target || el.contains(event.target)))
                binding.value();
        };
        document.addEventListener("click", el.clickOutsideEvent, {
            passive: true,
        });
    },
    unmounted(el) {
        document.removeEventListener("click", el.clickOutsideEvent);
    },
};

// Lifecycle
onMounted(() => {
    window.addEventListener("resize", handleResize);
    // Load initial notifications for badge count
    fetchModalNotifications();
});

onUnmounted(() => {
    window.removeEventListener("resize", handleResize);
});
</script>

<style scoped>
@media (max-width: 767px) {
    nav {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    .text-\[25px\] {
        font-size: 1.25rem;
    }
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

button:active {
    transform: scale(0.98);
}
button:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
}

.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

.max-h-96::-webkit-scrollbar {
    width: 6px;
}
.max-h-96::-webkit-scrollbar-track {
    background: transparent;
}
.max-h-96::-webkit-scrollbar-thumb {
    background-color: rgba(156, 163, 175, 0.5);
    border-radius: 3px;
}
.max-h-96::-webkit-scrollbar-thumb:hover {
    background-color: rgba(156, 163, 175, 0.8);
}
.dark .max-h-96::-webkit-scrollbar-thumb {
    background-color: rgba(75, 85, 99, 0.5);
}
.dark .max-h-96::-webkit-scrollbar-thumb:hover {
    background-color: rgba(75, 85, 99, 0.8);
}

.group:hover .animate-pulse {
    animation: none;
}

@keyframes badge-pulse {
    0%,
    100% {
        opacity: 1;
    }
    50% {
        opacity: 0.8;
    }
}

.animate-pulse {
    animation: badge-pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
