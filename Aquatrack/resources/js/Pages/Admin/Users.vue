<template>
    <AdminLayout>
        <div class="mx-auto w-full">
            <!-- Table Section -->
            <div
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden"
            >
                <!-- Search and Filter Section -->
                <div class="p-2 border-b border-gray-200 dark:border-gray-700">
                    <div
                        class="flex flex-col md:flex-row md:items-center justify-between gap-4"
                    >
                        <div class="flex items-center">
                            <h5 class="text-sm font-semibold text-gray-500">
                                <span class="font-bold text-black">{{
                                    users.total
                                }}</span>
                                Total Users
                            </h5>
                        </div>

                        <div class=""></div>

                        <!-- Action Buttons -->
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
                                class="flex items-center px-4 py-2 text-sm font-medium border border-blue-500 text-blue-500 rounded-sm hover:border-blue-700 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200 ease-in-out"
                            >
                                <Plus class="w-4 h-4 mr-2" />
                                Add User
                            </button>

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

                                <!-- Filter Dropdown Content -->
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
                                            <RefreshCw
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
                                            Close
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead
                            class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600"
                        >
                            <tr>
                                <th
                                    scope="col"
                                    class="px-6 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                >
                                    User
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                >
                                    Account No.
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                >
                                    Serial No.
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                >
                                    Role(s)
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                >
                                    Status
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                >
                                    Created
                                </th>
                                <th
                                    scope="col"
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
                                <!-- User Column -->
                                <td class="px-6 py-2">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8 mr-3">
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
                                                    getAvatarColor(user.name)
                                                "
                                            >
                                                {{ userInitials(user.name) }}
                                            </div>
                                        </div>
                                        <div>
                                            <div
                                                class="font-medium text-gray-900 dark:text-white text-xs"
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

                                <!-- Account Number -->
                                <td
                                    class="px-6 py-2 text-xs text-gray-900 dark:text-white whitespace-nowrap"
                                >
                                    {{ user.account_number || "N/A" }}
                                </td>

                                <!-- Serial Number -->
                                <td
                                    class="px-6 py-2 text-xs text-gray-900 dark:text-white whitespace-nowrap"
                                >
                                    {{ user.serial_number || "N/A" }}
                                </td>

                                <!-- Role -->
                                <td class="px-6 py-2">
                                    <span
                                        :class="roleClasses(user.role)"
                                        class="inline-flex items-center border px-2 py-0.5 rounded-full text-xs font-sm"
                                    >
                                        {{ capitalizeRole(user.role) }}
                                    </span>
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-3">
                                    <span
                                        :class="statusClasses(user.enabled)"
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-sm"
                                    >
                                        <span
                                            class="w-1.5 h-1.5 rounded-full mr-1.5"
                                            :class="
                                                user.enabled
                                                    ? 'bg-green-400'
                                                    : 'bg-yellow-400'
                                            "
                                        ></span>
                                        {{
                                            user.enabled ? "Active" : "Inactive"
                                        }}
                                    </span>
                                </td>

                                <!-- Created Date -->
                                <td
                                    class="px-6 py-2 text-xs text-gray-900 dark:text-white whitespace-nowrap"
                                >
                                    {{ formatDate(user.created_at) }}
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-3 text-right">
                                    <div class="flex justify-end">
                                        <div class="relative">
                                            <button
                                                @click="
                                                    toggleActionMenu(user.id)
                                                "
                                                class="p-1.5 text-gray-600 hover:text-gray-900 hover:bg-gray-200 dark:hover:text-gray-300 rounded-lg transition-colors"
                                                :data-action-button="user.id"
                                            >
                                                <MoreHorizontal
                                                    class="w-4 h-4"
                                                />
                                            </button>

                                            <!-- Action Menu Dropdown -->
                                            <div
                                                v-if="
                                                    activeActionMenu === user.id
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
                                                        @click="viewUser(user)"
                                                        class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                                    >
                                                        <Eye
                                                            class="w-4 h-4 mr-3"
                                                        />
                                                        View Details
                                                    </button>
                                                    <button
                                                        @click="editUser(user)"
                                                        class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                                    >
                                                        <Edit
                                                            class="w-4 h-4 mr-3"
                                                        />
                                                        Edit User
                                                    </button>
                                                    <button
                                                        @click="
                                                            toggleUserStatus(
                                                                user
                                                            )
                                                        "
                                                        class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                                    >
                                                        <Lock
                                                            v-if="user.enabled"
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
                                                        @click="
                                                            confirmDelete(user)
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
                                <td colspan="7" class="px-6 py-8 text-center">
                                    <div
                                        class="flex flex-col items-center justify-center space-y-2"
                                    >
                                        <Users
                                            class="w-12 h-12 text-gray-300"
                                        />
                                        <span
                                            class="font-medium text-gray-500 dark:text-gray-400"
                                            >No users found</span
                                        >
                                        <span
                                            class="text-xs text-gray-400 dark:text-gray-500"
                                        >
                                            Try adjusting your filters or search
                                            keywords.
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div
                    class="border-t border-gray-200 dark:border-gray-700 px-6 py-2"
                >
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700 dark:text-gray-300">
                            Showing
                            <span class="font-semibold">{{
                                users.from || 0
                            }}</span>
                            to
                            <span class="font-semibold">{{
                                users.to || 0
                            }}</span>
                            of
                            <span class="font-semibold">{{
                                users.total || 0
                            }}</span>
                            results
                        </div>

                        <div class="flex items-center space-x-2">
                            <button
                                @click="prevPage"
                                :disabled="!users.prev_page_url"
                                class="flex items-center px-3 py-1 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 hover:text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 transition-colors"
                            >
                                Previous
                            </button>

                            <!-- Page Numbers -->
                            <div class="flex items-center space-x-1">
                                <button
                                    v-for="page in getVisiblePages()"
                                    :key="page"
                                    @click="goToPage(page)"
                                    :class="[
                                        'px-3 py-1 text-sm font-medium rounded-md transition-colors',
                                        page === users.current_page
                                            ? 'bg-blue-50  text-blue-600 border border-blue-600'
                                            : 'text-gray-500 bg-white border border-gray-300 hover:bg-gray-50 hover:text-gray-700 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600',
                                    ]"
                                    :disabled="page === '...'"
                                >
                                    {{ page }}
                                </button>
                            </div>

                            <button
                                @click="nextPage"
                                :disabled="!users.next_page_url"
                                class="flex items-center px-3 py-1 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 hover:text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 transition-colors"
                            >
                                Next
                            </button>
                        </div>
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
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { ref, watch, onMounted, onUnmounted, nextTick } from "vue";
import { router } from "@inertiajs/vue3";
import { pickBy, debounce } from "lodash";
import Swal from "sweetalert2";
import CreateUserModal from "@/Components/Admin/Modals/CreateUserModal.vue";
import UserDetailsModal from "@/Components/Modals/UserDetailsModal.vue";
import EditUserDetailsModal from "@/Components/Modals/EditUserDetailsModal.vue";
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
    UserStar,
} from "lucide-vue-next";

const props = defineProps({
    users: Object,
    filters: Object,
    zones: Object,
});

console.log("Total users: ", props.users.total);

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
    updateDateTime();
    const interval = setInterval(updateDateTime, 1000);
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
const activeActionMenu = ref(null);
const filterDropdownStyle = ref({});
const filterButton = ref(null);

// Modal refs
const showUserModal = ref(false);
const showEditModal = ref(false);
const selectedUser = ref(null);

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

// Toggle filter dropdown with positioning
const toggleFilterDropdown = async () => {
    showFilterDropdown.value = !showFilterDropdown.value;

    if (showFilterDropdown.value) {
        await nextTick();
        if (filterButton.value) {
            const rect = filterButton.value.getBoundingClientRect();
            filterDropdownStyle.value = {
                left: `${rect.left}px`,
                top: `${rect.bottom + 8}px`,
            };
        }
    }
};

// Get action dropdown position
const getActionDropdownStyle = (userId) => {
    if (typeof window === "undefined") return {};

    const button = document.querySelector(`[data-action-button="${userId}"]`);
    if (!button) return {};

    const rect = button.getBoundingClientRect();
    const viewportHeight = window.innerHeight;

    // Calculate if dropdown should open upwards
    const spaceBelow = viewportHeight - rect.bottom;
    const dropdownHeight = 176; // Approximate height of 4 menu items

    let top = rect.bottom + 4;

    // If not enough space below, open upwards
    if (spaceBelow < dropdownHeight && rect.top > dropdownHeight) {
        top = rect.top - dropdownHeight - 4;
    }

    return {
        left: `${rect.right - 192}px`, // 192px is dropdown width (48 * 4)
        top: `${top}px`,
    };
};

// Toggle action menu
const toggleActionMenu = async (userId) => {
    if (activeActionMenu.value === userId) {
        activeActionMenu.value = null;
    } else {
        activeActionMenu.value = userId;
        // Wait for DOM update and recalculate position
        await nextTick();
    }
};

// Close dropdowns when clicking outside - FIXED VERSION
const handleClickOutside = (event) => {
    // Check if click is inside filter dropdown or filter button
    const isFilterClick =
        (filterButton.value && filterButton.value.contains(event.target)) ||
        (showFilterDropdown.value &&
            event.target.closest(".fixed.z-\\[1000\\]"));

    if (!isFilterClick) {
        showFilterDropdown.value = false;
    }

    // Check if click is inside action dropdowns
    const actionButtons = document.querySelectorAll("[data-action-button]");
    let isClickInsideActionMenu = false;

    actionButtons.forEach((button) => {
        if (button.contains(event.target)) {
            isClickInsideActionMenu = true;
        }
    });

    // Check if click is inside any action dropdown
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

// Filter update methods
const updateRoleFilter = (role) => {
    filters.value.role = role;
    // Auto-apply the filter immediately when radio button is clicked
    latestRequestId++;
    debouncedFetchUsers(filters.value, latestRequestId);
};

const updateEnabledFilter = (enabled) => {
    filters.value.enabled = enabled;
    // Auto-apply the filter immediately when radio button is clicked
    latestRequestId++;
    debouncedFetchUsers(filters.value, latestRequestId);
};

// Rest of your existing methods remain the same...
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

    let hash = 0;
    for (let i = 0; i < name.length; i++) {
        hash = name.charCodeAt(i) + ((hash << 5) - hash);
    }

    const index = Math.abs(hash) % colors.length;
    return colors[index];
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

// Debounced fetch with request tracking
const debouncedFetchUsers = debounce((filters, requestId) => {
    router.get("/admin/users", pickBy(filters), {
        preserveState: true,
        replace: true,
        onSuccess: () => {
            if (requestId === latestRequestId) {
                selectedUsers.value = [];
            }
        },
        onError: (errors) => {
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

let latestRequestId = 0;

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

const toggleUserStatus = (user) => {
    const newStatus = !user.enabled;
    activeActionMenu.value = null; // Close menu

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
    activeActionMenu.value = null; // Close menu
};

const viewUser = (user) => {
    selectedUser.value = user;
    showUserModal.value = true;
    activeActionMenu.value = null; // Close menu
};

const handleUserUpdated = () => {
    fetchUsers();
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
    activeActionMenu.value = null; // Close menu

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
        onError: (errors) => {
            console.log("Error details:", errors);
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
        "bg-green-100 border border-green-200 text-green-800 dark:bg-green-900 dark:text-green-200":
            enabled,
        "bg-yellow-100 border border-yellow-200 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200":
            !enabled,
    };
};

const capitalizeRole = (role) => {
    if (!role) return "";
    return role.charAt(0).toUpperCase() + role.slice(1);
};

// Pagination methods
const prevPage = () => {
    if (props.users.prev_page_url) {
        router.visit(props.users.prev_page_url, {
            preserveState: true,
        });
    }
};

const nextPage = () => {
    if (props.users.next_page_url) {
        router.visit(props.users.next_page_url, {
            preserveState: true,
        });
    }
};

const goToPage = (page) => {
    if (page === "..." || page === props.users.current_page) return;

    const url = new URL(props.users.first_page_url);
    url.searchParams.set("page", page);
    router.visit(url.toString(), {
        preserveState: true,
    });
};

const getVisiblePages = () => {
    if (!props.users.last_page) return [];

    const current = props.users.current_page;
    const last = props.users.last_page;
    const delta = 2; // Number of pages to show on each side of current page
    const range = [];
    const rangeWithDots = [];

    for (let i = 1; i <= last; i++) {
        if (
            i === 1 ||
            i === last ||
            (i >= current - delta && i <= current + delta)
        ) {
            range.push(i);
        }
    }

    let l;
    for (let i of range) {
        if (l) {
            if (i - l === 2) {
                rangeWithDots.push(l + 1);
            } else if (i - l !== 1) {
                rangeWithDots.push("...");
            }
        }
        rangeWithDots.push(i);
        l = i;
    }

    return rangeWithDots;
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
