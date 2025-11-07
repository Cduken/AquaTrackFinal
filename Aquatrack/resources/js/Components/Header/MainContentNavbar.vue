<template>
    <nav
        class="bg-white border-b border-gray-200 px-4 py-[9px] shadow-lg dark:bg-gray-800 dark:border-gray-700 fixed right-0 top-0 z-40 transition-all duration-300"
        :class="[
            isSidebarOpen ? 'left-64' : 'left-16',
            isMobileMenuOpen ? 'left-0' : '',
        ]"
        :style="navStyle"
    >
        <div class="flex justify-between items-center">
            <!-- Left section -->
            <div class="flex items-center flex-1">
                <!-- Mobile menu button -->
                <button
                    @click="$emit('toggle-mobile-menu')"
                    class="p-2 mr-2 text-gray-600 rounded-lg md:hidden hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 transition-colors"
                >
                    <Menu v-if="!isMobileMenuOpen" :size="20" />
                    <X v-else :size="20" />
                </button>

                <!-- Page title -->
                <div class="flex items-center">
                    <span
                        class="text-gray-900 dark:text-white font-semibold text-lg"
                    >
                        {{ currentPageName }}
                    </span>
                </div>
            </div>

            <!-- Right section -->
            <div class="flex items-center space-x-2">
                <!-- Notifications -->
                <div v-if="showNotifications" class="relative">
                    <button
                        type="button"
                        @click="openNotificationModal"
                        class="relative p-2 text-gray-500 rounded-lg hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 transition-colors"
                    >
                        <Bell :size="20" />
                        <span
                            v-if="unreadCount > 0"
                            class="absolute -top-1 -right-1 flex items-center justify-center min-w-[18px] h-4.5 px-1 text-xs font-medium text-white bg-red-500 rounded-full border border-white dark:border-gray-800"
                        >
                            {{ unreadCount > 99 ? "99+" : unreadCount }}
                        </span>
                    </button>
                </div>

                <!-- User dropdown -->
                <div class="relative">
                    <button
                        type="button"
                        @click.stop="toggleUserDropdown"
                        class="flex items-center space-x-2 text-sm rounded-lg p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                        ref="userButton"
                    >
                        <div class="flex items-center space-x-2">
                            <div class="flex items-center justify-center">
                                <img
                                    v-if="user.avatar_url"
                                    class="w-8 h-8 rounded-full border border-gray-200 dark:border-gray-600"
                                    :src="user.avatar_url"
                                    :alt="userDisplayName"
                                />
                                <div
                                    v-else
                                    class="w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center font-medium text-sm"
                                >
                                    {{ userInitials }}
                                </div>
                            </div>
                            <div class="text-left hidden sm:block">
                                <p
                                    class="text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    {{ userDisplayName }}
                                </p>
                            </div>
                        </div>
                        <ChevronDown
                            :size="16"
                            class="text-gray-500 transition-transform duration-200"
                            :class="{ 'rotate-180': isUserDropdownOpen }"
                        />
                    </button>

                    <!-- Dropdown menu -->
                    <div
                        v-show="isUserDropdownOpen"
                        v-click-outside="closeUserDropdown"
                        class="absolute right-0 top-full mt-1 z-50 w-56 bg-white rounded-lg shadow-lg dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden"
                    >
                        <div
                            class="py-3 px-4 border-b border-gray-100 dark:border-gray-600"
                        >
                            <div class="flex items-center space-x-3">
                                <div class="flex items-center justify-center">
                                    <img
                                        v-if="user.avatar_url"
                                        class="w-10 h-10 rounded-full border border-gray-200 dark:border-gray-600"
                                        :src="user.avatar_url"
                                        :alt="userDisplayName"
                                    />
                                    <div
                                        v-else
                                        class="w-10 h-10 rounded-full bg-blue-500 text-white flex items-center justify-center font-medium"
                                    >
                                        {{ userInitials }}
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p
                                        class="text-sm font-medium text-gray-900 dark:text-white truncate"
                                    >
                                        {{ userDisplayName }}
                                    </p>
                                    <p
                                        class="text-xs text-gray-500 dark:text-gray-400 truncate"
                                    >
                                        {{ user?.email }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="py-1">
                            <Link
                                href="/profile"
                                class="flex items-center gap-2 py-2 px-4 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                @click="closeUserDropdown"
                            >
                                <User :size="16" />
                                <span>Profile</span>
                            </Link>
                        </div>
                        <div
                            class="border-t border-gray-100 dark:border-gray-700 py-1"
                        >
                            <button
                                @click.prevent="handleLogout"
                                class="flex items-center w-full gap-2 py-2 px-4 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                            >
                                <LogOut :size="16" />
                                <span>Sign out</span>
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
                <div
                    class="fixed inset-0 bg-black/50 transition-opacity"
                    @click="showNotificationModal = false"
                ></div>

                <div
                    class="flex items-start justify-center min-h-screen p-4 pt-20"
                >
                    <div
                        class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md overflow-hidden"
                    >
                        <!-- Header -->
                        <div
                            class="border-b border-gray-200 dark:border-gray-700 px-4 py-3"
                        >
                            <div class="flex justify-between items-center">
                                <div class="flex items-center space-x-2">
                                    <Bell
                                        :size="18"
                                        class="text-gray-700 dark:text-gray-300"
                                    />
                                    <h3
                                        class="text-base font-semibold text-gray-900 dark:text-white"
                                    >
                                        Notifications
                                    </h3>
                                </div>
                                <button
                                    @click="showNotificationModal = false"
                                    class="p-1 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 rounded transition-colors"
                                >
                                    <X :size="18" />
                                </button>
                            </div>
                        </div>

                        <!-- Notifications List -->
                        <div class="max-h-96 overflow-y-auto">
                            <div
                                v-if="modalLoading"
                                class="flex flex-col items-center justify-center py-8"
                            >
                                <div
                                    class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500"
                                ></div>
                                <p
                                    class="mt-2 text-gray-600 dark:text-gray-400 text-sm"
                                >
                                    Loading...
                                </p>
                            </div>

                            <div
                                v-else-if="modalNotifications.length === 0"
                                class="flex flex-col items-center justify-center py-8 px-4"
                            >
                                <BellOff
                                    :size="32"
                                    class="text-gray-400 dark:text-gray-500 mb-2"
                                />
                                <p
                                    class="text-gray-900 dark:text-white font-medium text-sm"
                                >
                                    No notifications
                                </p>
                                <p
                                    class="text-gray-500 dark:text-gray-400 text-xs mt-1"
                                >
                                    You're all caught up
                                </p>
                            </div>

                            <div
                                v-else
                                class="divide-y divide-gray-100 dark:divide-gray-700"
                            >
                                <div
                                    v-for="notification in modalNotifications"
                                    :key="notification.id"
                                    class="p-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer"
                                    @click="
                                        handleNotificationClickInModal(
                                            notification
                                        )
                                    "
                                >
                                    <div class="flex items-start space-x-3">
                                        <div
                                            class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center"
                                            :class="
                                                getNotificationIconBg(
                                                    notification.type
                                                )
                                            "
                                        >
                                            <component
                                                :is="
                                                    getNotificationIcon(
                                                        notification.type
                                                    )
                                                "
                                                :size="14"
                                                class="text-white"
                                            />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p
                                                class="text-sm font-medium text-gray-900 dark:text-white mb-1"
                                            >
                                                {{ notification.title }}
                                            </p>
                                            <p
                                                class="text-xs text-gray-600 dark:text-gray-300 mb-1"
                                            >
                                                {{
                                                    notification.message ||
                                                    "No message available"
                                                }}
                                            </p>
                                            <div
                                                class="flex items-center space-x-1 text-xs text-gray-500 dark:text-gray-400"
                                            >
                                                <Clock :size="12" />
                                                <span>{{
                                                    formatTime(
                                                        notification.created_at
                                                    )
                                                }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div
                            class="border-t border-gray-200 dark:border-gray-700 px-4 py-3"
                        >
                            <button
                                type="button"
                                class="w-full flex items-center justify-center space-x-2 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded text-sm font-medium transition-colors"
                                @click="viewAllNotifications"
                            >
                                <List :size="14" />
                                <span>View All</span>
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
    Bell,
    BellOff,
    User,
    LogOut,
    Menu,
    X,
    Clock,
    List,
    ChevronDown,
    Info,
    RefreshCw,
    Megaphone,
    AlertTriangle,
    Calendar,
    AlertCircle,
} from "lucide-vue-next";
import Swal from "sweetalert2";

const props = defineProps({
    isSidebarOpen: { type: Boolean, default: true },
    isMobileMenuOpen: { type: Boolean, default: false },
});

const emit = defineEmits(["toggle-mobile-menu", "logout"]);

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

// Current page name - Only show main page names, ignore dynamic routes
const currentPageName = computed(() => {
    const path = window.location.pathname;

    // Define main routes and their display names
    const mainRoutes = {
        // Dashboard routes
        "/admin/dashboard": "Dashboard",
        "/staff/dashboard": "Dashboard",
        "/customer/dashboard": "Dashboard",

        // Report routes - show "Reports" for all report-related pages
        "/admin/reports": "Reports",
        "/staff/reports": "Reports",
        "/customer/reports": "Reports",
        "/admin/reports/create": "Reports",
        "/staff/reports/create": "Reports",
        "/customer/reports/create": "Reports",

        // Announcement routes
        "/admin/announcements": "Announcements",
        "/staff/announcements": "Announcements",
        "/customer/announcements": "Announcements",

        // Billing routes
        "/admin/billing": "Billing",
        "/staff/billing": "Billing",
        "/customer/billing": "Billing",

        // Profile routes
        "/admin/profile": "Profile",
        "/staff/profile": "Profile",
        "/customer/profile": "Profile",

        // Notification routes
        "/admin/notifications": "Notifications",
        "/staff/notifications": "Notifications",
        "/customer/notifications": "Notifications",

        // User management routes (admin only)
        "/admin/users": "User Management",
        "/admin/customers": "Customers",
        "/admin/staff": "Staff Management",

        // Analytics routes
        "/admin/analytics": "Analytics",
        "/staff/analytics": "Analytics",

        // Water quality routes
        "/admin/water-quality": "Water Quality",
        "/staff/water-quality": "Water Quality",
        "/customer/water-quality": "Water Quality",

        // Payment routes
        "/admin/payments": "Payments",
        "/staff/payments": "Payments",
        "/customer/payments": "Payments",

        // Settings routes
        "/admin/settings": "Settings",
        "/staff/settings": "Settings",
        "/customer/settings": "Settings",
    };

    // Remove role prefix and find the base path
    const segments = path.split("/").filter((segment) => segment);
    const roleSegments = ["admin", "staff", "customer"];

    // Find the base path without dynamic segments
    let basePath = "";
    for (let i = 0; i < segments.length; i++) {
        const segment = segments[i];

        // Skip role segments
        if (roleSegments.includes(segment)) {
            basePath += `/${segment}`;
            continue;
        }

        // If we hit a dynamic segment (number or UUID), stop and use what we have
        if (
            /^\d+$/.test(segment) ||
            /^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i.test(
                segment
            )
        ) {
            break;
        }

        basePath += `/${segment}`;

        // If this is a create/edit route, include it in base path
        if (segment === "create" || segment === "edit") {
            break;
        }
    }

    // Try to find exact match first
    if (mainRoutes[basePath]) {
        return mainRoutes[basePath];
    }

    // Try to match without role prefix
    const pathWithoutRole = basePath.replace(/^\/(admin|staff|customer)/, "");
    if (pathWithoutRole && pathWithoutRole !== basePath) {
        const possiblePaths = [
            `/admin${pathWithoutRole}`,
            `/staff${pathWithoutRole}`,
            `/customer${pathWithoutRole}`,
        ];

        for (const possiblePath of possiblePaths) {
            if (mainRoutes[possiblePath]) {
                return mainRoutes[possiblePath];
            }
        }
    }

    // Fallback: extract main section from path
    const mainSection = segments.find(
        (segment) =>
            !roleSegments.includes(segment) &&
            !/^\d+$/.test(segment) &&
            !/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i.test(
                segment
            )
    );

    if (mainSection) {
        // Format the main section nicely
        return mainSection
            .split("-")
            .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
            .join(" ");
    }

    return "Dashboard";
});

// Notification state
const showNotificationModal = ref(false);
const modalNotifications = ref([]);
const modalLoading = ref(false);

// Computed properties
const unreadCount = computed(
    () => modalNotifications.value.filter((n) => n.unread).length
);

const showNotifications = computed(() => {
    const allowedRoles = ["admin", "customer"];
    return allowedRoles.includes(userRole.value);
});

const notificationRoute = computed(() =>
    userRole.value === "admin"
        ? "/admin/notifications"
        : userRole.value === "staff"
        ? "/staff/notifications"
        : "/customer/notifications"
);

// Window width for responsive behavior
const windowWidth = ref(window.innerWidth);
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
        info: Info,
        report_update: RefreshCw,
        announcement: Megaphone,
        bill_overdue: AlertTriangle,
        bill_due_soon: Calendar,
        bill_final_reminder: AlertCircle,
        bill_due_today: AlertTriangle,
        alert: AlertTriangle,
        reminder: Calendar,
        system: Info,
        order: Bell,
    };
    return icons[type] || Bell;
};

const getNotificationIconBg = (type) => {
    const backgrounds = {
        info: "bg-blue-500",
        report_update: "bg-purple-500",
        announcement: "bg-gray-500",
        bill_overdue: "bg-red-500",
        bill_due_soon: "bg-orange-500",
        bill_final_reminder: "bg-yellow-500",
        bill_due_today: "bg-red-500",
        alert: "bg-red-500",
        reminder: "bg-yellow-500",
    };
    return backgrounds[type] || "bg-gray-500";
};

const formatTime = (timestamp) => {
    if (!timestamp) return "Recently";
    const now = new Date();
    const notificationTime = new Date(timestamp);
    const diff = now - notificationTime;

    if (diff < 60 * 1000) return "Just now";
    if (diff < 60 * 60 * 1000) {
        const minutes = Math.floor(diff / (60 * 1000));
        return `${minutes}m ago`;
    }
    if (diff < 24 * 60 * 60 * 1000) {
        const hours = Math.floor(diff / (60 * 60 * 1000));
        return `${hours}h ago`;
    }
    const days = Math.floor(diff / (24 * 60 * 60 * 1000));
    return `${days}d ago`;
};

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
            modalNotifications.value = [];
        }
    } catch {
        modalNotifications.value = [];
    } finally {
        modalLoading.value = false;
    }
};

const markAllAsRead = async () => {
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
            // Update local state to reflect all notifications are read
            modalNotifications.value = modalNotifications.value.map(
                (notification) => ({
                    ...notification,
                    unread: false,
                })
            );
        }
    } catch {
        // Silent fail
    }
};

const openNotificationModal = async () => {
    showNotificationModal.value = true;
    await markAllAsRead(); // This will clear the badge count
};

const handleNotificationClickInModal = (notification) => {
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
    if (isOpen) fetchModalNotifications();
});

// Click outside directive
const vClickOutside = {
    beforeMount(el, binding) {
        el.clickOutsideEvent = (event) => {
            if (!(el === event.target || el.contains(event.target)))
                binding.value();
        };
        document.addEventListener("click", el.clickOutsideEvent);
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
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.2s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.rotate-180 {
    transform: rotate(180deg);
}

@media (max-width: 767px) {
    nav {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
    }
}
</style>
