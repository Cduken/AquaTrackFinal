<template>
    <AdminLayout>
        <div class="mx-auto w-full">
            <!-- Header Section -->
            <div class="flex items-center mb-6">
                <div class="flex items-center">
                    <!-- <v-icon
                        name="fa-users"
                        class="mr-3 text-purple-500"
                        scale="1.5"
                    /> -->
                    <div class="flex flex-col">
                        <h1
                            class="text-2xl font-bold text-gray-800 dark:text-white"
                        >
                            User Management
                        </h1>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            {{ currentDateTime }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Section -->
            <div
                class="bg-white dark:bg-gray-800 rounded-sm shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden"
            >
                <div class="dark:border-gray-700 p-4 mb-6">
                    <div
                        class="flex flex-col md:flex-row md:items-center justify-between gap-4"
                    >
                        <!-- Search -->
                        <div class="w-full md:w-auto">
                            <div class="relative">
                                <div
                                    class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                                >
                                    <v-icon
                                        name="hi-solid-search"
                                        class="w-4 h-4 text-gray-500 dark:text-gray-400"
                                    />
                                </div>
                                <input
                                    v-model="filters.search"
                                    type="text"
                                    class="block w-full md:w-[500px] p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-white focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Name, email, phone..."
                                    @keyup.enter="fetchUsers"
                                />
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center space-x-2">
                            <button
                                @click="showCreateModal = true"
                                type="button"
                                class="flex items-center px-4 py-2 text-sm font-medium border border-blue-500 text-blue-500 bg-white rounded-sm hover:bg-blue-500 hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200 ease-in-out"
                            >
                                <v-icon
                                    name="hi-solid-plus"
                                    class="w-4 h-4 mr-2"
                                />
                                Add User
                            </button>
                            <!-- Filter Dropdown -->
                            <div class="relative">
                                <button
                                    @click="
                                        showFilterDropdown = !showFilterDropdown
                                    "
                                    class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600"
                                    type="button"
                                >
                                    <v-icon
                                        name="hi-solid-filter"
                                        class="w-4 h-4 mr-2"
                                    />
                                    Filter
                                    <v-icon
                                        name="hi-chevron-down"
                                        class="ml-1 w-4 h-4"
                                    />
                                </button>

                                <!-- Filter Dropdown Content -->
                                <div
                                    v-if="showFilterDropdown"
                                    class="absolute z-10 top-full right-0 mt-1 w-56 bg-white rounded-lg shadow-lg border border-gray-200 dark:bg-gray-700 dark:border-gray-600"
                                >
                                    <div class="p-4">
                                        <h6
                                            class="mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                        >
                                            Role
                                        </h6>
                                        <div class="space-y-2">
                                            <div class="flex items-center">
                                                <input
                                                    id="role-all"
                                                    type="radio"
                                                    name="role"
                                                    value=""
                                                    :checked="!filters.role"
                                                    @change="
                                                        updateRoleFilter('')
                                                    "
                                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                                />
                                                <label
                                                    for="role-all"
                                                    class="ml-2 text-sm text-gray-700 dark:text-gray-300"
                                                    >All Roles</label
                                                >
                                            </div>
                                            <div class="flex items-center">
                                                <input
                                                    id="role-customer"
                                                    type="radio"
                                                    name="role"
                                                    value="customer"
                                                    :checked="
                                                        filters.role ===
                                                        'customer'
                                                    "
                                                    @change="
                                                        updateRoleFilter(
                                                            'customer'
                                                        )
                                                    "
                                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                                />
                                                <label
                                                    for="role-customer"
                                                    class="ml-2 text-sm text-gray-700 dark:text-gray-300"
                                                    >Customer</label
                                                >
                                            </div>
                                            <div class="flex items-center">
                                                <input
                                                    id="role-staff"
                                                    type="radio"
                                                    name="role"
                                                    value="staff"
                                                    :checked="
                                                        filters.role === 'staff'
                                                    "
                                                    @change="
                                                        updateRoleFilter(
                                                            'staff'
                                                        )
                                                    "
                                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                                />
                                                <label
                                                    for="role-staff"
                                                    class="ml-2 text-sm text-gray-700 dark:text-gray-300"
                                                    >Staff</label
                                                >
                                            </div>
                                            <div class="flex items-center">
                                                <input
                                                    id="role-admin"
                                                    type="radio"
                                                    name="role"
                                                    value="admin"
                                                    :checked="
                                                        filters.role === 'admin'
                                                    "
                                                    @change="
                                                        updateRoleFilter(
                                                            'admin'
                                                        )
                                                    "
                                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                                />
                                                <label
                                                    for="role-admin"
                                                    class="ml-2 text-sm text-gray-700 dark:text-gray-300"
                                                    >Admin</label
                                                >
                                            </div>
                                        </div>

                                        <h6
                                            class="mb-2 mt-4 text-sm font-medium text-gray-900 dark:text-white"
                                        >
                                            Status
                                        </h6>
                                        <div class="space-y-2">
                                            <div class="flex items-center">
                                                <input
                                                    id="status-all"
                                                    type="radio"
                                                    name="status"
                                                    value=""
                                                    :checked="!filters.enabled"
                                                    @change="
                                                        updateEnabledFilter('')
                                                    "
                                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                                />
                                                <label
                                                    for="status-all"
                                                    class="ml-2 text-sm text-gray-700 dark:text-gray-300"
                                                    >All Status</label
                                                >
                                            </div>
                                            <div class="flex items-center">
                                                <input
                                                    id="status-active"
                                                    type="radio"
                                                    name="status"
                                                    value="1"
                                                    :checked="
                                                        filters.enabled === '1'
                                                    "
                                                    @change="
                                                        updateEnabledFilter('1')
                                                    "
                                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                                />
                                                <label
                                                    for="status-active"
                                                    class="ml-2 text-sm text-gray-700 dark:text-gray-300"
                                                    >Active</label
                                                >
                                            </div>
                                            <div class="flex items-center">
                                                <input
                                                    id="status-inactive"
                                                    type="radio"
                                                    name="status"
                                                    value="0"
                                                    :checked="
                                                        filters.enabled === '0'
                                                    "
                                                    @change="
                                                        updateEnabledFilter('0')
                                                    "
                                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                                />
                                                <label
                                                    for="status-inactive"
                                                    class="ml-2 text-sm text-gray-700 dark:text-gray-300"
                                                    >Inactive</label
                                                >
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
                                            <v-icon
                                                name="hi-refresh"
                                                class="w-4 h-4 mr-1"
                                                :class="{
                                                    'animate-spin': isResetting,
                                                }"
                                            />
                                            Reset
                                        </button>
                                        <button
                                            @click="showFilterDropdown = false"
                                            class="px-3 py-1.5 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        >
                                            Apply
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Add User Button -->
                        </div>
                    </div>
                </div>
                <table
                    class="w-full text-sm text-left text-gray-500 dark:text-gray-400"
                >
                    <thead
                        class="text-xs text-white uppercase bg-blue-700 dark:bg-gray-700 dark:text-gray-400"
                    >
                        <tr>
                            <th scope="col" class="px-6 py-3 font-medium">
                                User
                            </th>
                            <th scope="col" class="px-6 py-3 font-medium">
                                Account No.
                            </th>
                            <th scope="col" class="px-6 py-3 font-medium">
                                Serial No.
                            </th>
                            <th scope="col" class="px-6 py-3 font-medium">
                                Role(s)
                            </th>
                            <th scope="col" class="px-6 py-3 font-medium">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 font-medium">
                                Created
                            </th>

                            <th
                                scope="col"
                                class="px-6 py-3 font-medium text-center"
                            >
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody
                        class="divide-y divide-gray-200 dark:divide-gray-700"
                    >
                        <tr
                            v-for="user in users.data"
                            :key="user.id"
                            class="bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700"
                        >
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 mr-3">
                                        <img
                                            v-if="user.avatar_url"
                                            :src="user.avatar_url"
                                            :alt="user.name"
                                            class="h-10 w-10 rounded-full object-cover"
                                        />
                                        <div
                                            v-else
                                            class="h-10 w-10 rounded-full flex items-center justify-center text-white font-semibold text-sm"
                                            :class="getAvatarColor(user.name)"
                                        >
                                            {{ userInitials(user.name) }}
                                        </div>
                                    </div>
                                    <div>
                                        <div
                                            class="font-medium text-gray-900 dark:text-white"
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

                            <td class="px-6 py-4 text-gray-800 whitespace-nowrap">
                                {{ user.account_number || "N/A" }}
                            </td>

                            <td class="px-6 py-4 text-gray-800 whitespace-nowrap">
                                {{ user.serial_number || "N/A" }}
                            </td>

                            <td class="px-6 py-4">
                                <span
                                    :class="roleClasses(user.role)"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                >
                                    <v-icon
                                        :name="roleIcon(user.role)"
                                        class="w-3 h-3 mr-1"
                                    />
                                    {{ capitalizeRole(user.role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    :class="statusClasses(user.enabled)"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                >
                                    <v-icon
                                        :name="
                                            user.enabled
                                                ? 'hi-check-circle'
                                                : 'hi-x-circle'
                                        "
                                        class="w-3 h-3 mr-1"
                                    />
                                    {{ user.enabled ? "Active" : "Pending" }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-800  whitespace-nowrap">
                                {{ formatDate(user.created_at) }}
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex justify-end space-x-2">
                                    <button
                                        @click="viewUser(user)"
                                        class="p-1.5 text-blue-600 bg-blue-500 rounded-sm hover:bg-blue-600 transition-colors"
                                        title="View"
                                    >
                                        <v-icon
                                            name="hi-eye"
                                            class="w-4 h-4 text-white"
                                        />
                                    </button>
                                    <button
                                        @click="editUser(user)"
                                        class="p-1.5 text-yellow-500 bg-yellow-500 rounded-sm h hover:bg-yellow-600 transition-colors"
                                        title="Edit"
                                    >
                                        <v-icon
                                            name="hi-pencil"
                                            class="w-4 h-4 text-white"
                                        />
                                    </button>
                                    <button
                                        @click="toggleUserStatus(user)"
                                        :class="
                                            user.enabled
                                                ? 'text-orange-500 hover:text-orange-700 bg-orange-500 hover:bg-orange-600'
                                                : 'text-green-500 hover:text-green-700 bg-green-500 hover:bg-green-600'
                                        "
                                        class="p-1.5 rounded transition-colors"
                                        :title="
                                            user.enabled
                                                ? 'Deactivate'
                                                : 'Activate'
                                        "
                                    >
                                        <v-icon
                                            :name="
                                                user.enabled
                                                    ? 'hi-lock-closed'
                                                    : 'hi-lock-open'
                                            "
                                            class="w-4 h-4 text-white"
                                        />
                                    </button>
                                    <button
                                        @click="confirmDelete(user)"
                                        class="p-1.5 text-white bg-red-500 hover:bg-red-600 rounded transition-colors"
                                        title="Delete"
                                    >
                                        <v-icon
                                            name="hi-trash"
                                            class="w-4 h-4"
                                        />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="users.data.length === 0">
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div
                                    class="flex flex-col items-center justify-center space-y-3"
                                >
                                    <v-icon
                                        name="hi-user-group"
                                        class="w-12 h-12 text-gray-300"
                                    />
                                    <span
                                        class="font-medium text-gray-500 dark:text-gray-400"
                                        >No users found</span
                                    >
                                    <span
                                        class="text-sm text-gray-400 dark:text-gray-500"
                                        >Try adjusting your filters or search
                                        keywords.</span
                                    >
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                <Pagination
                    :items="users"
                    item-name="users"
                    previous-text="Previous"
                    next-text="Next"
                />
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
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { ref, watch, onMounted, onUnmounted } from "vue";
import { router } from "@inertiajs/vue3";
import { pickBy, debounce } from "lodash";
import Swal from "sweetalert2";
import CreateUserModal from "@/Components/Admin/Modals/CreateUserModal.vue";
import UserDetailsModal from "@/Components/Modals/UserDetailsModal.vue";
import EditUserDetailsModal from "@/Components/Modals/EditUserDetailsModal.vue";

const props = defineProps({
    users: Object,
    filters: Object,
    zones: Object,
});

const currentDateTime = ref("");

const updateDateTime = () => {
    const date = new Date().toLocaleString("en-US", {
        timeZone: "Asia/Manila",
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
    });
    currentDateTime.value = date;
};

onMounted(() => {
    updateDateTime(); // Initial update
    const interval = setInterval(updateDateTime, 1000); // Update every second
    // Store interval ID to clear it later
    onUnmounted(() => clearInterval(interval));
});

const filters = ref({
    search: props.filters.search || "",
    role: props.filters.role || "",
    sort: props.filters.sort || "id",
    order: props.filters.order || "desc",
    per_page: props.filters.per_page || 10,
    enabled: props.filters.enabled !== undefined ? props.filters.enabled : "",
    action: props.filters.action || "",
});

const selectedUsers = ref([]);
const showCreateModal = ref(false);
const showFilterDropdown = ref(false);
const isResetting = ref(false);

// Add these refs for the modal
const showUserModal = ref(false);
const showEditModal = ref(false);
const selectedUser = ref(null);

// Define zones as a fallback in case props.zones is not provided
const zones = ref(
    props.zones || {
        "Zone 1": ["Poblacion Sur"],
        "Zone 2": ["Poblacion Centro"],
        "Zone 3": ["Poblacion Centro"],
        "Zone 4": ["Poblacion Norte"],
        "Zone 5": ["Candajec", "Buangan"],
        "Zone 6": ["Bonbon"],
        "Zone 7": ["Bonbon"],
        "Zone 8": ["Nahawan"],
        "Zone 9": ["Caboy", "Villaflor", "Cantuyoc"],
        "Zone 10": ["Bacani", "Mataub", "Comaang", "Tangaran"],
        "Zone 11": ["Cantuyoc", "Nahawan"],
        "Zone 12": ["Lajog", "Buacao"],
    }
);

const formatDate = (dateString) => {
    if (!dateString) return "N/A";
    const date = new Date(dateString);
    return date.toLocaleDateString("en-US", {
        month: "short",
        day: "numeric",
        year: "numeric",
    });
};

// Generate avatar color based on user name
const getAvatarColor = (name) => {
    if (!name) return "bg-gray-400";

    const colors = [
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

    // Simple hash function to get consistent color for same name
    let hash = 0;
    for (let i = 0; i < name.length; i++) {
        hash = name.charCodeAt(i) + ((hash << 5) - hash);
    }

    const index = Math.abs(hash) % colors.length;
    return colors[index];
};

// Handle click outside to close dropdown
const handleClickOutside = (event) => {
    const filterButton = document.querySelector(
        '[aria-label="Filter dropdown button"]'
    );
    const dropdown = document.querySelector('[aria-label="Filter dropdown"]');

    if (
        filterButton &&
        !filterButton.contains(event.target) &&
        dropdown &&
        !dropdown.contains(event.target)
    ) {
        showFilterDropdown.value = false;
    }
};

console.log("Users data:", props.users.data);

// Add event listener for click outside
onMounted(() => {
    document.addEventListener("click", handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener("click", handleClickOutside);
});

// Debounced fetch with request tracking
const debouncedFetchUsers = debounce((filters, requestId) => {
    router.get("/admin/users", pickBy(filters), {
        preserveState: true,
        replace: true,
        onSuccess: () => {
            // Only update state if this is the latest request
            if (requestId === latestRequestId) {
                selectedUsers.value = [];
            }
        },
        onError: (errors) => {
            // Only update state if this is the latest request
            if (requestId === latestRequestId) {
                console.error("Fetch error:", errors);
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Failed to fetch users. Please try again.",
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 3000,
                });
            }
        },
    });
}, 300);

// Store the latest request ID to prevent race conditions
let latestRequestId = 0;

// Watch only relevant filter properties
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
        latestRequestId++; // Increment request ID for each new fetch
        debouncedFetchUsers(newFilters, latestRequestId);
    },
    { deep: true }
);

// Watch selectedUsers to reset action filter
watch(selectedUsers, (value) => {
    if (value.length === 0 && filters.value.action) {
        filters.value.action = "";
        latestRequestId++;
        debouncedFetchUsers(filters.value, latestRequestId);
    }
});

const sortBy = (column) => {
    if (filters.value.sort === column) {
        filters.value.order = filters.value.order === "asc" ? "desc" : "asc";
    } else {
        filters.value.sort = column;
        filters.value.order = "asc";
    }
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
            action: "",
        };
        selectedUsers.value = [];
        showFilterDropdown.value = false;
        isResetting.value = false;
    }, 1500);
};

const fetchUsers = () => {
    latestRequestId++;
    debouncedFetchUsers(filters.value, latestRequestId);
};

// Update filter checkboxes to be mutually exclusive
const updateRoleFilter = (role) => {
    filters.value.role = filters.value.role === role ? "" : role;
    latestRequestId++;
    debouncedFetchUsers(filters.value, latestRequestId);
};

const updateEnabledFilter = (enabled) => {
    filters.value.enabled = filters.value.enabled === enabled ? "" : enabled;
    latestRequestId++;
    debouncedFetchUsers(filters.value, latestRequestId);
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

const toggleUserStatus = (user) => {
    const newStatus = !user.enabled;

    Swal.fire({
        title: `${newStatus ? "Activate" : "Deactivate"} User`,
        text: `Are you sure you want to ${
            newStatus ? "activate" : "deactivate"
        } ${user.name}?`,
        icon: "question",
        showCancelButton: true,
        confirmButtonText: `Yes, ${newStatus ? "activate" : "deactivate"}`,
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(
                "/admin/users/toggle-status",
                {
                    user_ids: [user.id],
                    enabled: newStatus,
                },
                {
                    preserveState: true,
                    onSuccess: () => {
                        Swal.fire({
                            icon: newStatus ? "success" : "info",
                            title: newStatus
                                ? "User Activated"
                                : "User Deactivated",
                            text: `User ${user.name} has been ${
                                newStatus ? "activated" : "deactivated"
                            }.`,
                            toast: true,
                            position: "top-end",
                            showConfirmButton: false,
                            timer: 2000,
                        });
                        fetchUsers();
                    },
                    onError: (errors) => {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: `Failed to update status. ${
                                errors.message ||
                                "Please check the console for details."
                            }`,
                            toast: true,
                            position: "top-end",
                            showConfirmButton: false,
                            timer: 2000,
                        });
                        console.error("Update error:", errors);
                    },
                }
            );
        }
    });
};

const editUser = (user) => {
    selectedUser.value = user;
    showEditModal.value = true;
};

const viewUser = (user) => {
    selectedUser.value = user;
    showUserModal.value = true;
};

// Add function to handle successful user update
const handleUserUpdated = () => {
    // Refresh the users list
    fetchUsers();

    // Show success message
    Swal.fire({
        icon: "success",
        title: "User Updated",
        text: "User information has been updated successfully.",
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
    });
};

const confirmDelete = (user) => {
    Swal.fire({
        title: "Are you sure?",
        text: `You are about to delete ${user.name}. This action cannot be undone.`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(`/admin/users/${user.id}`, {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "The user has been deleted.",
                        "success"
                    );
                    fetchUsers();
                },
                onError: () => {
                    Swal.fire("Error!", "Something went wrong.", "error");
                },
            });
        }
    });
};

const submitCreateUser = (userData) => {
    router.post(route("admin.users.store"), userData, {
        preserveScroll: true,
        onSuccess: (response) => {
            showCreateModal.value = false;
            const generatedPassword = response.props.flash?.generated_password;

            if (generatedPassword) {
                Swal.fire({
                    icon: "success",
                    title: "User created successfully!",
                    html: `
                        <div class="text-left flex items-center flex-col justify-center">
                            <p class="mb-2 font-medium">Generated Password:</p>
                            <input
                                id="swal-password"
                                class="swal2-input text-center font-mono text-lg"
                                value="${generatedPassword}"
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
                                navigator.clipboard.writeText(
                                    generatedPassword
                                );
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
            } else {
                Swal.fire({
                    icon: "warning",
                    title: "User created",
                    text: "User was created successfully but password could not be displayed",
                    toast: true,
                    position: "top-end",
                    timer: 3000,
                });
            }
            fetchUsers();
        },
        /**
         * Handle create user request error
         * @param {Object} errors - Validation errors
         */
        onError: (errors) => {
            console.log("Error details:", errors); // Debug log

            let errorMessage =
                "Failed to create user. Please check the form and try again.";

            // Handle Laravel validation errors
            if (errors && typeof errors === "object") {
                // Get the first error message from the validation errors
                const firstErrorKey = Object.keys(errors)[0];
                if (firstErrorKey && errors[firstErrorKey]) {
                    errorMessage = Array.isArray(errors[firstErrorKey])
                        ? errors[firstErrorKey][0]
                        : errors[firstErrorKey];
                }
            }
            // Handle server error messages
            else if (errors && errors.message) {
                errorMessage = errors.message;
            }
            // Handle string errors
            else if (typeof errors === "string") {
                errorMessage = errors;
            }

            Swal.fire({
                icon: "error",
                title: "Creation Failed",
                text: errorMessage,
                confirmButtonText: "OK",
                customClass: {
                    confirmButton: "!bg-red-600 !hover:bg-red-700",
                },
            });
        },
    });
};

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
        "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200":
            enabled,
        "bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200":
            !enabled,
    };
};

const roleIcon = (role) => {
    if (role === "admin") return "hi-shield-check";
    if (role === "staff") return "hi-user-circle";
    if (role === "customer") return "hi-users";
    return "hi-user";
};

const capitalizeRole = (role) => {
    if (!role) return "";
    return role.charAt(0).toUpperCase() + role.slice(1);
};
</script>

<style scoped>
.cursor-pointer {
    cursor: pointer;
}

tr {
    transition: opacity 0.2s ease;
}

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
