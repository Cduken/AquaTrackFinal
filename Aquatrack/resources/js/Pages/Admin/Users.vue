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
                        <div class="flex items-center px-2 space-x-4">
                            <h5 class="text-sm font-semibold text-gray-500">
                                <span class="font-bold text-black">{{
                                    users.total
                                }}</span>
                                Total Users
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
                                        placeholder="Search"
                                        @keyup.enter="fetchUsers"
                                    />
                                </div>
                            </div>

                            <button
                                @click="showCreateModal = true"
                                type="button"
                                class="flex items-center px-4 py-2 text-sm font-medium border border-blue-500/20 bg-blue-100/40 text-blue-500 rounded-sm hover:bg-blue-100/80 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200 ease-in-out"
                            >
                                <Plus class="w-4 h-4 mr-2" />
                                Add User
                            </button>

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
                                            Role
                                        </h6>
                                        <div class="space-y-2">
                                            <div
                                                class="flex items-center"
                                                v-for="roleOption in roleOptions"
                                                :key="roleOption.value"
                                            >
                                                <input
                                                    :id="`role-${roleOption.value}`"
                                                    type="radio"
                                                    name="role"
                                                    :value="roleOption.value"
                                                    :checked="
                                                        filters.role ===
                                                        roleOption.value
                                                    "
                                                    @change="
                                                        updateRoleFilter(
                                                            roleOption.value
                                                        )
                                                    "
                                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                                />
                                                <label
                                                    :for="`role-${roleOption.value}`"
                                                    class="ml-2 text-sm text-gray-700 dark:text-gray-300"
                                                >
                                                    {{ roleOption.label }}
                                                </label>
                                            </div>
                                        </div>

                                        <h6
                                            class="mb-2 mt-4 text-sm font-medium text-gray-900 dark:text-white"
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
                                                        filters.enabled ===
                                                        statusOption.value
                                                    "
                                                    @change="
                                                        updateEnabledFilter(
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
                                    </div>
                                    <div
                                        class="flex items-center justify-between p-4 border-t border-gray-200 dark:border-gray-600"
                                    >
                                        <button
                                            @click="resetFilters"
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
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table Container with Fixed Height -->
                <div
                    class="flex flex-col"
                    style="height: 613px; min-height: 600px"
                >
                    <!-- Table with Scrollable Body -->
                    <div class="flex-1 overflow-x-auto overflow-y-auto">
                        <table class="w-full">
                            <thead
                                class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 sticky top-0 z-10"
                            >
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                    >
                                        User
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                    >
                                        Account No.
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                    >
                                        Serial No.
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                    >
                                        Role(s)
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                    >
                                        Status
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                    >
                                        Created
                                    </th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                    >
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody
                                class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"
                            >
                                <tr
                                    v-for="user in users.data"
                                    :key="user.id"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150"
                                >
                                    <td class="px-6 py-3">
                                        <div class="flex items-center">
                                            <div
                                                class="flex-shrink-0 h-8 w-8 mr-3"
                                            >
                                                <img
                                                    v-if="user.avatar_url"
                                                    :src="user.avatar_url"
                                                    :alt="user.name"
                                                    class="h-8 w-8 rounded-full object-cover"
                                                />
                                                <div
                                                    v-else
                                                    class="h-8 w-8 rounded-full flex items-center justify-center text-white font-semibold text-xs"
                                                    :class="
                                                        getAvatarColor(
                                                            user.name
                                                        )
                                                    "
                                                >
                                                    {{
                                                        userInitials(user.name)
                                                    }}
                                                </div>
                                            </div>
                                            <div>
                                                <div
                                                    class="font-medium text-gray-900 dark:text-white text-sm"
                                                >
                                                    {{ user.name }}
                                                </div>
                                                <div
                                                    class="text-xs text-gray-500 dark:text-gray-400"
                                                >
                                                    {{ user.email || "N/A" }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td
                                        class="px-6 py-3 text-sm text-gray-900 dark:text-white whitespace-nowrap"
                                    >
                                        {{ user.account_number || "N/A" }}
                                    </td>

                                    <td
                                        class="px-6 py-3 text-sm text-gray-900 dark:text-white whitespace-nowrap"
                                    >
                                        {{ user.serial_number || "N/A" }}
                                    </td>

                                    <td class="px-6 py-3">
                                        <span
                                            :class="roleClasses(user.role)"
                                            class="inline-flex items-center border px-2.5 py-1 rounded-full text-xs font-medium"
                                        >
                                            {{ capitalizeRole(user.role) }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-3">
                                        <span
                                            :class="statusClasses(user.enabled)"
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                        >
                                            <span
                                                class="w-1.5 h-1.5 rounded-full mr-1.5"
                                                :class="
                                                    user.enabled
                                                        ? 'bg-green-400'
                                                        : 'bg-red-400'
                                                "
                                            ></span>
                                            {{
                                                user.enabled
                                                    ? "Active"
                                                    : "Inactive"
                                            }}
                                        </span>
                                    </td>

                                    <td
                                        class="px-6 py-3 text-sm text-gray-900 dark:text-white whitespace-nowrap"
                                    >
                                        {{ formatDate(user.created_at) }}
                                    </td>

                                    <td class="px-6 py-3 text-right">
                                        <div class="flex justify-end">
                                            <div class="relative">
                                                <button
                                                    @click="
                                                        toggleActionMenu(
                                                            user.id
                                                        )
                                                    "
                                                    class="p-1.5 text-gray-600 hover:text-gray-900 hover:bg-gray-200 dark:hover:text-gray-300 rounded-lg transition-colors"
                                                    :data-action-button="
                                                        user.id
                                                    "
                                                >
                                                    <MoreHorizontal
                                                        class="w-4 h-4"
                                                    />
                                                </button>

                                                <div
                                                    v-if="
                                                        activeActionMenu ===
                                                        user.id
                                                    "
                                                    class="fixed z-[1000] mt-1 w-48 bg-white dark:bg-gray-700 rounded-lg shadow-lg border border-gray-200 dark:border-gray-600"
                                                    :style="
                                                        getActionDropdownStyle(
                                                            user.id
                                                        )
                                                    "
                                                >
                                                    <div class="py-1">
                                                        <button
                                                            @click="
                                                                viewUser(user)
                                                            "
                                                            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                                        >
                                                            <Eye
                                                                class="w-4 h-4 mr-3"
                                                            />
                                                            View Details
                                                        </button>
                                                        <button
                                                            v-if="
                                                                user.role !==
                                                                'admin'
                                                            "
                                                            @click="
                                                                editUser(user)
                                                            "
                                                            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                                        >
                                                            <Edit
                                                                class="w-4 h-4 mr-3"
                                                            />
                                                            Edit User
                                                        </button>
                                                        <button
                                                            v-if="
                                                                user.role !==
                                                                'admin'
                                                            "
                                                            @click="
                                                                toggleUserStatus(
                                                                    user
                                                                )
                                                            "
                                                            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                                        >
                                                            <Lock
                                                                v-if="
                                                                    user.enabled
                                                                "
                                                                class="w-4 h-4 mr-3"
                                                            />
                                                            <Unlock
                                                                v-else
                                                                class="w-4 h-4 mr-3"
                                                            />
                                                            {{
                                                                user.enabled
                                                                    ? "Deactivate"
                                                                    : "Activate"
                                                            }}
                                                        </button>
                                                        <button
                                                            v-if="
                                                                user.role !==
                                                                'admin'
                                                            "
                                                            @click="
                                                                confirmDelete(
                                                                    user
                                                                )
                                                            "
                                                            class="flex items-center w-full px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                                        >
                                                            <Trash2
                                                                class="w-4 h-4 mr-3"
                                                            />
                                                            Delete User
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Empty State -->
                                <tr v-if="users.data.length === 0">
                                    <td
                                        colspan="7"
                                        class="px-6 py-24 text-center"
                                    >
                                        <div
                                            class="flex flex-col items-center justify-center space-y-4"
                                        >
                                            <Users
                                                class="w-20 h-20 text-gray-300"
                                            />
                                            <span
                                                class="text-2xl font-medium text-gray-500 dark:text-gray-400"
                                                >No users found</span
                                            >
                                            <span
                                                class="text-sm text-gray-400 dark:text-gray-500"
                                            >
                                                Try adjusting your filters or
                                                search keywords.
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination - Fixed at Bottom -->
                    <div
                        class="flex-shrink-0 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800"
                    >
                        <Pagination :data="users" />
                    </div>
                </div>
            </div>

            <!-- Modals -->
            <CreateUserModal
                :show="showCreateModal"
                :zones="zones"
                @close="showCreateModal = false"
                @submit="submitCreateUser"
            />

            <UserDetailsModal
                :show="showUserModal"
                :user="selectedUser"
                @close="showUserModal = false"
            />

            <EditUserDetailsModal
                :show="showEditModal"
                :user="selectedUser"
                :zones="zones"
                @close="showEditModal = false"
                @saved="handleUserUpdated"
            />

            <!-- Status Toggle Modal -->
            <StatusToggleModal
                :show="showStatusModal"
                :user="selectedUser"
                :action="statusAction"
                @close="closeStatusModal"
                @confirm="handleStatusToggle"
            />

            <!-- Delete User Modal -->
            <DeleteUserModal
                :show="showDeleteModal"
                :user="selectedUser"
                @close="closeDeleteModal"
                @confirm="handleDeleteUser"
            />
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { ref, watch, onMounted, onUnmounted, nextTick } from "vue";
import { router } from "@inertiajs/vue3";
import { pickBy, debounce } from "lodash";
import Swal from "sweetalert2";
import CreateUserModal from "@/Components/Admin/Modals/CreateUserModal.vue";
import UserDetailsModal from "@/Components/Modals/UserDetailsModal.vue";
import EditUserDetailsModal from "@/Components/Modals/EditUserDetailsModal.vue";
import StatusToggleModal from "@/Components/Admin/Modals/StatusToggleModal.vue";
import DeleteUserModal from "@/Components/Admin/Modals/DeleteUserModal.vue";
import {
    Search,
    Plus,
    Filter,
    ChevronDown,
    RefreshCw,
    Eye,
    Edit,
    Lock,
    Unlock,
    Trash2,
    Users,
    MoreHorizontal,
} from "lucide-vue-next";

const props = defineProps({
    users: Object,
    filters: Object,
    zones: Object,
});

// Reactive state
const filters = ref({
    search: props.filters.search || "",
    role: props.filters.role || "",
    sort: props.filters.sort || "id",
    order: props.filters.order || "desc",
    per_page: props.filters.per_page || 10,
    enabled: props.filters.enabled !== undefined ? props.filters.enabled : "",
});

const showCreateModal = ref(false);
const showFilterDropdown = ref(false);
const isResetting = ref(false);
const activeActionMenu = ref(null);
const filterDropdownStyle = ref({});
const filterButton = ref(null);
const showUserModal = ref(false);
const showEditModal = ref(false);
const showStatusModal = ref(false);
const showDeleteModal = ref(false);
const selectedUser = ref(null);
const statusAction = ref("");

// Constants
const roleOptions = [
    { value: "", label: "All Roles" },
    { value: "customer", label: "Customer" },
    { value: "staff", label: "Staff" },
    { value: "admin", label: "Admin" },
];

const statusOptions = [
    { value: "", label: "All Status" },
    { value: "1", label: "Active" },
    { value: "0", label: "Inactive" },
];

const avatarColors = [
    "bg-blue-500",
    "bg-green-500",
    "bg-purple-500",
    "bg-pink-500",
    "bg-indigo-500",
    "bg-yellow-500",
    "bg-red-500",
    "bg-teal-500",
    "bg-orange-500",
];

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

const getActionDropdownStyle = (userId) => {
    if (typeof window === "undefined") return {};

    const button = document.querySelector(`[data-action-button="${userId}"]`);
    if (!button) return {};

    const rect = button.getBoundingClientRect();
    const viewportHeight = window.innerHeight;
    const spaceBelow = viewportHeight - rect.bottom;
    const dropdownHeight = 176;

    let top = rect.bottom + 4;

    if (spaceBelow < dropdownHeight && rect.top > dropdownHeight) {
        top = rect.top - dropdownHeight - 4;
    }

    return {
        left: `${rect.right - 192}px`,
        top: `${top}px`,
    };
};

const toggleActionMenu = async (userId) => {
    if (activeActionMenu.value === userId) {
        activeActionMenu.value = null;
    } else {
        activeActionMenu.value = userId;
        await nextTick();
    }
};

// Click outside handler
const handleClickOutside = (event) => {
    const isFilterClick =
        filterButton.value?.contains(event.target) ||
        (showFilterDropdown.value &&
            event.target.closest(".fixed.z-\\[1000\\]"));

    if (!isFilterClick) {
        showFilterDropdown.value = false;
    }

    const actionButtons = document.querySelectorAll("[data-action-button]");
    let isClickInsideActionMenu = false;

    actionButtons.forEach((button) => {
        if (button.contains(event.target)) {
            isClickInsideActionMenu = true;
        }
    });

    const actionDropdowns = document.querySelectorAll(".fixed.z-\\[1000\\]");
    actionDropdowns.forEach((dropdown) => {
        if (dropdown.contains(event.target)) {
            isClickInsideActionMenu = true;
        }
    });

    if (!isClickInsideActionMenu) {
        activeActionMenu.value = null;
    }
};

onMounted(() => {
    document.addEventListener("click", handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener("click", handleClickOutside);
});

// Filter methods
const updateRoleFilter = (role) => {
    filters.value.role = role;
    fetchUsers();
};

const updateEnabledFilter = (enabled) => {
    filters.value.enabled = enabled;
    fetchUsers();
};

// Utility methods
const formatDate = (dateString) => {
    if (!dateString) return "N/A";
    const date = new Date(dateString);
    return date.toLocaleDateString("en-US", {
        month: "short",
        day: "numeric",
        year: "numeric",
    });
};

const getAvatarColor = (name) => {
    if (!name) return "bg-gray-400";
    let hash = 0;
    for (let i = 0; i < name.length; i++) {
        hash = name.charCodeAt(i) + ((hash << 5) - hash);
    }
    const index = Math.abs(hash) % avatarColors.length;
    return avatarColors[index];
};

const userInitials = (name) => {
    if (!name) return "?";
    return name
        .split(" ")
        .map((n) => n[0])
        .join("")
        .toUpperCase()
        .substring(0, 2);
};

// Data fetching
let latestRequestId = 0;

const debouncedFetchUsers = debounce((filters, requestId) => {
    router.get("/admin/users", pickBy(filters), {
        preserveState: true,
        replace: true,
        onSuccess: () => {
            if (requestId === latestRequestId) {
                // Success handling
            }
        },
        onError: () => {
            if (requestId === latestRequestId) {
                showError("Failed to fetch users. Please try again.");
            }
        },
    });
}, 300);

const fetchUsers = () => {
    latestRequestId++;
    debouncedFetchUsers(filters.value, latestRequestId);
};

const resetFilters = () => {
    isResetting.value = true;
    setTimeout(() => {
        filters.value = {
            search: "",
            role: "",
            sort: "id",
            order: "desc",
            per_page: 10,
            enabled: "",
        };
        showFilterDropdown.value = false;
        isResetting.value = false;
    }, 1500);
};

// User actions
const toggleUserStatus = (user) => {
    selectedUser.value = user;
    statusAction.value = user.enabled ? "deactivate" : "activate";
    showStatusModal.value = true;
    activeActionMenu.value = null;
};

const closeStatusModal = () => {
    showStatusModal.value = false;
    selectedUser.value = null;
};

const handleStatusToggle = () => {
    const user = selectedUser.value;
    const newStatus = !user.enabled;

    router.post(
        "/admin/users/toggle-status",
        {
            user_ids: [user.id],
            enabled: newStatus,
        },
        {
            preserveState: true,
            onSuccess: () => {
                closeStatusModal();
                showSuccess(
                    `User ${user.name} has been ${
                        newStatus ? "activated" : "deactivated"
                    }.`
                );
                fetchUsers(); // This will refresh the table data
            },
            onError: () => {
                closeStatusModal();
                showError("Failed to update status. Please try again.");
            },
        }
    );
};

const confirmDelete = (user) => {
    selectedUser.value = user;
    showDeleteModal.value = true;
    activeActionMenu.value = null;
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    selectedUser.value = null;
};

const handleDeleteUser = () => {
    const user = selectedUser.value;

    router.delete(`/admin/users/${user.id}`, {
        preserveState: true,
        onSuccess: () => {
            closeDeleteModal();
            showSuccess("The user has been deleted successfully.");
            fetchUsers(); // This will refresh the table data
        },
        onError: () => {
            closeDeleteModal();
            showError("Failed to delete user. Please try again.");
        },
    });
};

const editUser = (user) => {
    selectedUser.value = user;
    showEditModal.value = true;
    activeActionMenu.value = null;
};

const viewUser = (user) => {
    selectedUser.value = user;
    showUserModal.value = true;
    activeActionMenu.value = null;
};

const handleUserUpdated = () => {
    fetchUsers();
    showSuccess("User information has been updated successfully.");
};

const submitCreateUser = (userData) => {
    router.post(route("admin.users.store"), userData, {
        preserveScroll: true,
        onSuccess: (response) => {
            showCreateModal.value = false;
            const generatedPassword = response.props.flash?.generated_password;

            if (generatedPassword) {
                showPasswordModal(generatedPassword);
            } else {
                showSuccess("User created successfully!");
            }
            fetchUsers();
        },
        onError: (errors) => {
            let errorMessage =
                "Failed to create user. Please check the form and try again.";

            if (errors && typeof errors === "object") {
                const firstErrorKey = Object.keys(errors)[0];
                if (firstErrorKey && errors[firstErrorKey]) {
                    errorMessage = Array.isArray(errors[firstErrorKey])
                        ? errors[firstErrorKey][0]
                        : errors[firstErrorKey];
                }
            } else if (errors && errors.message) {
                errorMessage = errors.message;
            } else if (typeof errors === "string") {
                errorMessage = errors;
            }

            showError(errorMessage);
        },
    });
};

// Helper functions for notifications
const showSuccess = (message) => {
    Swal.fire({
        icon: "success",
        title: "Success",
        text: message,
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
    });
};

const showError = (message) => {
    Swal.fire({
        icon: "error",
        title: "Error",
        text: message,
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
    });
};

const showPasswordModal = (password) => {
    Swal.fire({
        icon: "success",
        title: "User created successfully!",
        html: `
            <div class="text-left flex items-center flex-col justify-center">
                <p class="mb-2 font-medium">Generated Password:</p>
                <input
                    id="swal-password"
                    class="swal2-input text-center font-mono text-lg"
                    value="${password}"
                    readonly
                    style="width: 70%; padding: 0.5rem;"
                />
                <p class="mt-2 text-sm text-gray-500">Please provide this password to the user</p>
            </div>
        `,
        showConfirmButton: true,
        confirmButtonText: "Copy Password",
        showCancelButton: true,
        cancelButtonText: "Close",
        focusConfirm: false,
        customClass: {
            popup: "!text-left",
            confirmButton: "!bg-blue-600 !hover:bg-blue-700",
        },
        didOpen: () => {
            const confirmBtn = Swal.getConfirmButton();
            if (confirmBtn) {
                confirmBtn.addEventListener("click", () => {
                    navigator.clipboard.writeText(password);
                    Swal.fire({
                        icon: "success",
                        title: "Copied!",
                        text: "Password copied to clipboard",
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 2000,
                    });
                });
            }
        },
    });
};

// Styling helpers
const roleClasses = (role) => {
    return {
        "bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200":
            role === "admin",
        "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200":
            role === "customer",
        "bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200":
            role === "staff",
    };
};

const statusClasses = (enabled) => {
    return {
        "bg-green-100 border border-green-200 text-green-800 dark:bg-green-900 dark:text-green-200":
            enabled,
        "bg-red-100 border border-red-200 text-red-800 dark:bg-red-900 dark:text-red-200":
            !enabled,
    };
};

const capitalizeRole = (role) => {
    if (!role) return "";
    return role.charAt(0).toUpperCase() + role.slice(1);
};

// Watch for filter changes
watch(
    () => ({
        search: filters.value.search,
        role: filters.value.role,
        sort: filters.value.sort,
        order: filters.value.order,
        per_page: filters.value.per_page,
        enabled: filters.value.enabled,
    }),
    (newFilters) => {
        latestRequestId++;
        debouncedFetchUsers(newFilters, latestRequestId);
    },
    { deep: true }
);
</script>

<style scoped>
.animate-spin {
    animation: spin 1.5s linear;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
</style>
