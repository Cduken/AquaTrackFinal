<template>
    <nav
        class="bg-white border-b border-gray-200 px-2 py-[5px] shadow-sm dark:bg-gray-800 dark:border-gray-700 fixed right-0 top-0 z-40 transition-all duration-300 ease-in-out"
        :class="[
            isSidebarOpen ? 'left-64' : 'left-16',
            isMobileMenuOpen ? 'left-0' : '',
        ]"
        :style="navStyle"
    >
        <div class="flex justify-between items-center">
            <!-- Left section with breadcrumbs, mobile menu -->
            <div class="flex items-center flex-1 max-w-3xl">
                <!-- Mobile menu button -->
                <button
                    @click="$emit('toggle-mobile-menu')"
                    class="p-2 mr-3 text-gray-600 rounded-lg cursor-pointer md:hidden hover:text-gray-900 hover:bg-gray-100 focus:bg-gray-100 dark:focus:bg-gray-700 focus:ring-2 focus:ring-gray-100 dark:focus:ring-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white transition-colors duration-200"
                >
                    <Menu v-if="!isMobileMenuOpen" :size="24" />
                    <X v-else :size="24" />
                </button>

                <!-- Breadcrumbs -->
                <div class="hidden md:flex items-center space-x-2 text-lg ">

                    <span
                        class="text-gray-900 dark:text-white font-semibold capitalize"
                    >
                        {{ currentPageName }}
                    </span>
                </div>

                <!-- Mobile Breadcrumbs (simplified) -->
                <div class="flex md:hidden items-center space-x-2 text-sm ml-2">
                    <span
                        class="text-gray-900 dark:text-white font-semibold capitalize"
                    >
                        {{ currentPageName }}
                    </span>
                </div>
            </div>

            <!-- Right section with notifications and user menu -->
            <div class="flex items-center space-x-3 ml-4">
                <!-- Notifications Button -->
                <div class="relative">
                    <button
                        type="button"
                        @click="showNotificationModal = true"
                        class="relative p-2.5 text-gray-500 rounded-xl hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-all duration-200 group"
                    >

                        <Bell
                            :size="18"
                            class="group-hover:scale-110 transition-transform"
                        />
                        <span
                            v-if="unreadCount > 0"
                            class="absolute -top-0.5 -right-0.5 flex items-center justify-center min-w-[20px] h-5 px-1 text-xs font-bold text-white bg-gradient-to-r from-red-500 to-red-600 rounded-full border-2 border-white dark:border-gray-800 shadow-lg animate-pulse"
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
                        class="flex items-center space-x-3 text-sm rounded-xl p-2 hover:bg-gray-100 dark:hover:bg-gray-700 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-all duration-200 group"
                        ref="userButton"
                    >
                        <img
                            v-if="user.avatar_url"
                            class="w-6 h-6 rounded-full border-2 border-gray-200 dark:border-gray-600 shadow-sm group-hover:border-blue-400 transition-all"
                            :src="user.avatar_url"
                            :alt="userDisplayName"
                        />
                        <div
                            v-else
                            class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-600 to-blue-500 text-white flex items-center justify-center font-semibold text-sm border-2 border-gray-200 dark:border-gray-600 shadow-sm group-hover:border-blue-400 transition-all"
                        >
                            {{ userInitials }}
                        </div>
                        <div class="text-left hidden sm:block">
                            <p
                                class="text-sm font-semibold text-gray-900 dark:text-white"
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
                        <ChevronDown
                            :size="16"
                            class="text-gray-400 transition-transform duration-200"
                            :class="{ 'rotate-180': isUserDropdownOpen }"
                        />
                    </button>

                    <!-- User Dropdown Menu -->
                    <div
                        v-show="isUserDropdownOpen"
                        v-click-outside="closeUserDropdown"
                        class="absolute right-0 top-full mt-2 z-50 w-64 text-base bg-white rounded-2xl shadow-2xl dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden transform transition-all duration-200"
                        :class="{
                            'scale-100 opacity-100': isUserDropdownOpen,
                            'scale-95 opacity-0': !isUserDropdownOpen,
                        }"
                    >
                        <div
                            class="py-4 px-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-600 border-b border-gray-100 dark:border-gray-600"
                        >
                            <div class="flex items-center space-x-3">
                                <img
                                    v-if="user.avatar_url"
                                    class="w-12 h-12 rounded-full border-2 border-white dark:border-gray-700 shadow-md"
                                    :src="user.avatar_url"
                                    :alt="userDisplayName"
                                />
                                <div
                                    v-else
                                    class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-600 to-blue-500 text-white flex items-center justify-center font-semibold text-base border-2 border-white dark:border-gray-700 shadow-md"
                                >
                                    {{ userInitials }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p
                                        class="text-sm font-semibold text-gray-900 dark:text-white truncate"
                                    >
                                        {{ userDisplayName }}
                                    </p>
                                    <p
                                        class="text-xs text-gray-600 dark:text-gray-300 truncate"
                                    >
                                        {{ user?.email }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="py-2">
                            <Link
                                href="/profile"
                                class="flex items-center gap-3 py-3 px-4 text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors duration-200 group"
                                @click="closeUserDropdown"
                            >
                                <User
                                    :size="18"
                                    class="text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors"
                                />
                                <span class="font-medium"
                                    >Profile Settings</span
                                >
                            </Link>
                        </div>
                        <div
                            class="border-t border-gray-100 dark:border-gray-700 pt-2 pb-2"
                        >
                            <button
                                @click.prevent="handleLogout"
                                class="flex items-center w-full gap-3 py-3 px-4 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-200 group"
                            >
                                <LogOut
                                    :size="18"
                                    class="group-hover:translate-x-0.5 transition-transform"
                                />
                                <span class="font-medium">Sign out</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notification Modal -->
        <Transition name="modal">
            <div
                v-if="showNotificationModal"
                class="fixed inset-0 z-50 overflow-y-auto"
            >
                <!-- Background Overlay -->
                <div
                    class="fixed inset-0 bg-black/60  transition-opacity"
                    @click="showNotificationModal = false"
                ></div>

                <!-- Modal Container -->
                <div class="flex items-center justify-center min-h-screen p-4">
                    <div
                        class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden transform transition-all"
                    >
                        <!-- Header -->
                        <div
                            class="bg-[#172554] px-6 py-5"
                        >
                            <div class="flex justify-between items-center">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="p-2 bg-white/20 rounded-lg backdrop-blur-sm"
                                    >
                                        <Bell :size="24" class="text-white" />
                                    </div>
                                    <div>
                                        <h3
                                            class="text-xl font-bold text-white"
                                        >
                                            Notifications
                                        </h3>
                                        <p
                                            v-if="
                                                modalNotifications.length > 0 &&
                                                !modalLoading
                                            "
                                            class="text-sm text-blue-100"
                                        >
                                            {{ modalUnreadCount }} unread
                                            notification{{
                                                modalUnreadCount !== 1
                                                    ? "s"
                                                    : ""
                                            }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button
                                        v-if="
                                            modalNotifications.length > 0 &&
                                            modalUnreadCount > 0
                                        "
                                        @click="markAllAsReadInModal"
                                        class="flex items-center space-x-1.5 text-sm bg-white/20 hover:bg-white/30 text-white px-3 py-1.5 rounded-lg transition-all duration-200 backdrop-blur-sm"
                                        :disabled="markingAllAsRead"
                                    >
                                        <CheckCheck :size="16" />
                                        <span v-if="!markingAllAsRead"
                                            >Mark All Read</span
                                        >
                                        <span v-else>Marking...</span>
                                    </button>
                                    <button
                                        @click="showNotificationModal = false"
                                        class="p-2 text-white hover:bg-white/20 rounded-lg transition-colors"
                                    >
                                        <X :size="20" />
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Notifications List -->
                        <div
                            class="max-h-[500px] overflow-y-auto bg-gray-50 dark:bg-gray-900"
                        >
                            <div
                                v-if="modalLoading"
                                class="flex flex-col items-center justify-center py-12"
                            >
                                <div class="relative">
                                    <div
                                        class="w-16 h-16 border-4 border-blue-200 dark:border-blue-900 rounded-full"
                                    ></div>
                                    <div
                                        class="w-16 h-16 border-4 border-blue-600 border-t-transparent rounded-full animate-spin absolute top-0 left-0"
                                    ></div>
                                </div>
                                <p
                                    class="mt-4 text-gray-600 dark:text-gray-400 font-medium"
                                >
                                    Loading notifications...
                                </p>
                            </div>

                            <div
                                v-else-if="modalNotifications.length === 0"
                                class="flex flex-col items-center justify-center py-12 px-4"
                            >
                                <div
                                    class="w-20 h-20 bg-gradient-to-br from-blue-100 to-indigo-100 dark:from-blue-900 dark:to-indigo-900 rounded-full flex items-center justify-center mb-4"
                                >
                                    <BellOff
                                        :size="40"
                                        class="text-blue-600 dark:text-blue-400"
                                    />
                                </div>
                                <p
                                    class="text-gray-900 dark:text-white font-semibold text-lg"
                                >
                                    All caught up!
                                </p>
                                <p
                                    class="text-gray-500 dark:text-gray-400 text-sm mt-1"
                                >
                                    No notifications at the moment
                                </p>
                            </div>

                            <div v-else class="p-4 space-y-3">
                                <div
                                    v-for="notification in modalNotifications"
                                    :key="notification.id"
                                    class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 hover:shadow-md cursor-pointer transition-all duration-200 group"
                                    :class="{
                                        'ring-2 ring-blue-400 dark:ring-blue-500 bg-blue-50 dark:bg-blue-900/20':
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
                                    <div class="flex items-start space-x-4">
                                        <div class="flex-shrink-0">
                                            <div
                                                class="w-10 h-10 rounded-xl bg-gradient-to-br flex items-center justify-center text-xl"
                                                :class="
                                                    getNotificationIconBg(
                                                        notification.type
                                                    )
                                                "
                                            >
                                                {{
                                                    getNotificationIcon(
                                                        notification.type
                                                    )
                                                }}
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div
                                                class="flex items-start justify-between mb-1"
                                            >
                                                <p
                                                    class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors"
                                                >
                                                    {{ notification.title }}
                                                </p>
                                                <div
                                                    class="flex items-center space-x-2 ml-2"
                                                >
                                                    <span
                                                        v-if="
                                                            notification.unread
                                                        "
                                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300"
                                                    >
                                                        New
                                                    </span>
                                                    <span
                                                        :class="
                                                            getStatusBadgeClass(
                                                                notification.type
                                                            )
                                                        "
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
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
                                                class="text-sm text-gray-600 dark:text-gray-300 line-clamp-2 mb-2"
                                            >
                                                {{
                                                    notification.message ||
                                                    "No message available"
                                                }}
                                            </p>
                                            <div
                                                class="flex justify-between items-center"
                                            >
                                                <div
                                                    class="flex items-center space-x-1.5 text-xs text-gray-500 dark:text-gray-400"
                                                >
                                                    <Clock :size="14" />
                                                    <span>{{
                                                        formatTime(
                                                            notification.created_at
                                                        )
                                                    }}</span>
                                                </div>
                                                <button
                                                    v-if="notification.unread"
                                                    @click.stop="
                                                        markAsReadInModal(
                                                            notification.id
                                                        )
                                                    "
                                                    class="flex items-center space-x-1 text-xs text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 font-medium transition-colors"
                                                >
                                                    <CheckCircle2 :size="14" />
                                                    <span>Mark read</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div
                            class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 px-6 py-4 flex justify-end space-x-3"
                        >
                            <button
                                type="button"
                                class="flex items-center space-x-2 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl font-medium shadow-lg hover:shadow-xl transition-all duration-200"
                                @click="viewAllNotifications"
                            >
                                <List :size="18" />
                                <span>View All Notifications</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </nav>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted, watch } from "vue";
import { usePage, Link, router } from "@inertiajs/vue3";
import {
    Home,
    ChevronRight,
    ChevronDown,
    Bell,
    BellOff,
    User,
    LogOut,
    Menu,
    X,
    Clock,
    CheckCircle2,
    CheckCheck,
    List,
} from "lucide-vue-next";
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

const getNotificationIconBg = (type) => {
    const backgrounds = {
        info: "from-blue-100 to-blue-200 dark:from-blue-900 dark:to-blue-800",
        report_update:
            "from-purple-100 to-purple-200 dark:from-purple-900 dark:to-purple-800",
        announcement:
            "from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600",
        bill_overdue:
            "from-red-100 to-red-200 dark:from-red-900 dark:to-red-800",
        bill_due_soon:
            "from-orange-100 to-orange-200 dark:from-orange-900 dark:to-orange-800",
        bill_final_reminder:
            "from-yellow-100 to-yellow-200 dark:from-yellow-900 dark:to-yellow-800",
        bill_due_today:
            "from-red-100 to-red-200 dark:from-red-900 dark:to-red-800",
        alert: "from-red-100 to-red-200 dark:from-red-900 dark:to-red-800",
        reminder:
            "from-yellow-100 to-yellow-200 dark:from-yellow-900 dark:to-yellow-800",
    };
    return (
        backgrounds[type] ||
        "from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600"
    );
};

const getStatusBadgeClass = (type) => {
    const classes = {
        info: "bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300",
        report_update:
            "bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300",
        announcement:
            "bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300",
        bill_overdue:
            "bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300",
        bill_due_soon:
            "bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300",
        bill_final_reminder:
            "bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300",
        bill_due_today:
            "bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300",
        alert: "bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300",
        reminder:
            "bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300",
    };
    return (
        classes[type] ||
        "bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300"
    );
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
    if (notification.unread) {
        markAsReadInModal(notification.id);
    }

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
}

.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Custom scrollbar */
.max-h-\[500px\]::-webkit-scrollbar {
    width: 8px;
}

.max-h-\[500px\]::-webkit-scrollbar-track {
    background: transparent;
}

.max-h-\[500px\]::-webkit-scrollbar-thumb {
    background: linear-gradient(
        to bottom,
        rgb(191, 219, 254),
        rgb(147, 197, 253)
    );
    border-radius: 4px;
}

.max-h-\[500px\]::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(
        to bottom,
        rgb(147, 197, 253),
        rgb(96, 165, 250)
    );
}

.dark .max-h-\[500px\]::-webkit-scrollbar-thumb {
    background: linear-gradient(to bottom, rgb(30, 58, 138), rgb(29, 78, 216));
}

.dark .max-h-\[500px\]::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to bottom, rgb(29, 78, 216), rgb(37, 99, 235));
}

/* Modal animations */
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-enter-active .relative,
.modal-leave-active .relative {
    transition: transform 0.3s ease;
}

.modal-enter-from .relative,
.modal-leave-to .relative {
    transform: scale(0.95);
}

/* Badge pulse animation */
@keyframes badge-pulse {
    0%,
    100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.9;
        transform: scale(1.05);
    }
}

.animate-pulse {
    animation: badge-pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Smooth rotation for chevron */
.rotate-180 {
    transform: rotate(180deg);
}
</style>
