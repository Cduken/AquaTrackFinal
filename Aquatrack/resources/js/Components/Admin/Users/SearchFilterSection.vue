<template>
    <AdminLayout>
        <div class="mx-auto w-full">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden">
                <SearchFilterSection
                    :total-users="users.total"
                    :filters="filters"
                    :show-filter-dropdown="showFilterDropdown"
                    :filter-dropdown-style="filterDropdownStyle"
                    :is-resetting="isResetting"
                    @update:search="filters.search = $event"
                    @search="fetchUsers"
                    @add-user="showCreateModal = true"
                    @toggle-filter="toggleFilterDropdown"
                    @update-role="updateRoleFilter"
                    @update-status="updateEnabledFilter"
                    @reset-filters="resetFilters"
                    @close-filter="showFilterDropdown = false"
                    ref="filterButton"
                />

                <UsersTable
                    :users="users.data"
                    :active-action-menu="activeActionMenu"
                    @toggle-action-menu="toggleActionMenu"
                    @view-user="viewUser"
                    @edit-user="editUser"
                    @toggle-status="toggleUserStatus"
                    @delete-user="confirmDelete"
                    :get-action-dropdown-style="getActionDropdownStyle"
                />

                <Pagination
                    :pagination-data="users"
                    @prev-page="prevPage"
                    @next-page="nextPage"
                    @go-to-page="goToPage"
                />
            </div>

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
import { ref, watch, onMounted, onUnmounted } from "vue";
import { router } from "@inertiajs/vue3";
import { pickBy, debounce } from "lodash";
import Swal from "sweetalert2";

// Components
import CreateUserModal from "@/Components/Admin/Modals/CreateUserModal.vue";
import UserDetailsModal from "@/Components/Modals/UserDetailsModal.vue";
import EditUserDetailsModal from "@/Components/Modals/EditUserDetailsModal.vue";
import SearchFilterSection from "@/Components/Admin/Users/SearchFilterSection.vue";
import UsersTable from "@/Components/Admin/Users/UsersTable.vue";
import Pagination from "@/Components/Admin/Users/Pagination.vue";

const props = defineProps({
    users: Object,
    filters: Object,
    zones: Object,
});

// State Management
const filters = ref({
    search: props.filters.search || "",
    role: props.filters.role || "",
    sort: props.filters.sort || "id",
    order: props.filters.order || "desc",
    per_page: props.filters.per_page || 10,
    enabled: props.filters.enabled !== undefined ? props.filters.enabled : "",
    action: props.filters.action || "",
});

const showCreateModal = ref(false);
const showFilterDropdown = ref(false);
const isResetting = ref(false);
const activeActionMenu = ref(null);
const filterDropdownStyle = ref({});
const filterButton = ref(null);
const showUserModal = ref(false);
const showEditModal = ref(false);
const selectedUser = ref(null);
const zones = ref(props.zones || {});

let latestRequestId = 0;

// Dropdown Positioning
const DROPDOWN_CONFIG = {
    FILTER_WIDTH: 224,
    ACTION_WIDTH: 192,
    ACTION_HEIGHT: 176,
    OFFSET: 8,
};

const toggleFilterDropdown = async () => {
    showFilterDropdown.value = !showFilterDropdown.value;

    if (showFilterDropdown.value && filterButton.value?.$el) {
        await nextTick();
        const rect = filterButton.value.$el.getBoundingClientRect();
        const left = rect.right - DROPDOWN_CONFIG.FILTER_WIDTH;

        filterDropdownStyle.value = {
            left: `${left}px`,
            top: `${rect.bottom + DROPDOWN_CONFIG.OFFSET}px`,
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

    let top = rect.bottom + DROPDOWN_CONFIG.OFFSET / 2;

    if (spaceBelow < DROPDOWN_CONFIG.ACTION_HEIGHT && rect.top > DROPDOWN_CONFIG.ACTION_HEIGHT) {
        top = rect.top - DROPDOWN_CONFIG.ACTION_HEIGHT - DROPDOWN_CONFIG.OFFSET / 2;
    }

    return {
        left: `${rect.right - DROPDOWN_CONFIG.ACTION_WIDTH}px`,
        top: `${top}px`,
    };
};

const toggleActionMenu = async (userId) => {
    activeActionMenu.value = activeActionMenu.value === userId ? null : userId;
    if (activeActionMenu.value) await nextTick();
};

// Click Outside Handler
const handleClickOutside = (event) => {
    const filterEl = filterButton.value?.$el;
    const isFilterClick = filterEl?.contains(event.target) ||
                         event.target.closest('.fixed.z-\\[1000\\]');

    if (!isFilterClick) showFilterDropdown.value = false;

    const isActionClick = Array.from(document.querySelectorAll("[data-action-button]"))
        .some(btn => btn.contains(event.target)) ||
        Array.from(document.querySelectorAll('.fixed.z-\\[1000\\]'))
        .some(dropdown => dropdown.contains(event.target));

    if (!isActionClick) activeActionMenu.value = null;
};

// Lifecycle Hooks
onMounted(() => {
    document.addEventListener("click", handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener("click", handleClickOutside);
});

// Filter Management
const updateRoleFilter = (role) => {
    filters.value.role = role;
    latestRequestId++;
    debouncedFetchUsers(filters.value, latestRequestId);
};

const updateEnabledFilter = (enabled) => {
    filters.value.enabled = enabled;
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
            action: "",
        };
        showFilterDropdown.value = false;
        isResetting.value = false;
    }, 1500);
};

// Data Fetching
const debouncedFetchUsers = debounce((filters, requestId) => {
    router.get("/admin/users", pickBy(filters), {
        preserveState: true,
        replace: true,
        onSuccess: () => {
            if (requestId === latestRequestId) {
                activeActionMenu.value = null;
            }
        },
        onError: (errors) => {
            if (requestId === latestRequestId) {
                console.error("Fetch error:", errors);
                showToast("error", "Error", "Failed to fetch users. Please try again.");
            }
        },
    });
}, 300);

const fetchUsers = () => {
    latestRequestId++;
    debouncedFetchUsers(filters.value, latestRequestId);
};

// Watchers
watch(
    () => ({
        search: filters.value.search,
        role: filters.value.role,
        sort: filters.value.sort,
        order: filters.value.order,
        per_page: filters.value.per_page,
        enabled: filters.value.enabled,
    }),
    () => {
        latestRequestId++;
        debouncedFetchUsers(filters.value, latestRequestId);
    },
    { deep: true }
);

// User Actions
const viewUser = (user) => {
    selectedUser.value = user;
    showUserModal.value = true;
    activeActionMenu.value = null;
};

const editUser = (user) => {
    selectedUser.value = user;
    showEditModal.value = true;
    activeActionMenu.value = null;
};

const toggleUserStatus = (user) => {
    const newStatus = !user.enabled;
    activeActionMenu.value = null;

    Swal.fire({
        title: `${newStatus ? "Activate" : "Deactivate"} User`,
        text: `Are you sure you want to ${newStatus ? "activate" : "deactivate"} ${user.name}?`,
        icon: "question",
        showCancelButton: true,
        confirmButtonText: `Yes, ${newStatus ? "activate" : "deactivate"}`,
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            router.post("/admin/users/toggle-status", {
                user_ids: [user.id],
                enabled: newStatus,
            }, {
                preserveState: true,
                onSuccess: () => {
                    showToast(
                        newStatus ? "success" : "info",
                        newStatus ? "User Activated" : "User Deactivated",
                        `User ${user.name} has been ${newStatus ? "activated" : "deactivated"}.`
                    );
                    fetchUsers();
                },
                onError: (errors) => {
                    showToast("error", "Error", `Failed to update status. ${errors.message || ""}`);
                },
            });
        }
    });
};

const confirmDelete = (user) => {
    activeActionMenu.value = null;

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
                    Swal.fire("Deleted!", "The user has been deleted.", "success");
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
                showPasswordDialog(generatedPassword);
            } else {
                showToast("warning", "User created", "User was created successfully but password could not be displayed");
            }
            fetchUsers();
        },
        onError: (errors) => {
            const errorMessage = extractErrorMessage(errors);
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

const handleUserUpdated = () => {
    fetchUsers();
    showToast("success", "User Updated", "User information has been updated successfully.");
};

// Pagination
const prevPage = () => {
    if (props.users.prev_page_url) {
        router.visit(props.users.prev_page_url, { preserveState: true });
    }
};

const nextPage = () => {
    if (props.users.next_page_url) {
        router.visit(props.users.next_page_url, { preserveState: true });
    }
};

const goToPage = (page) => {
    if (page === "..." || page === props.users.current_page) return;

    const url = new URL(props.users.first_page_url);
    url.searchParams.set("page", page);
    router.visit(url.toString(), { preserveState: true });
};

// Utility Functions
const showToast = (icon, title, text) => {
    Swal.fire({
        icon,
        title,
        text,
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
    });
};

const showPasswordDialog = (password) => {
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
            confirmBtn?.addEventListener("click", () => {
                navigator.clipboard.writeText(password);
                showToast("success", "Copied!", "Password copied to clipboard");
            });
        },
    });
};

const extractErrorMessage = (errors) => {
    if (errors && typeof errors === "object") {
        const firstErrorKey = Object.keys(errors)[0];
        if (firstErrorKey && errors[firstErrorKey]) {
            return Array.isArray(errors[firstErrorKey])
                ? errors[firstErrorKey][0]
                : errors[firstErrorKey];
        }
    }
    return errors?.message || errors || "Failed to create user. Please check the form and try again.";
};
</script>

<style scoped>
.animate-spin {
    animation: spin 1.5s linear;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
