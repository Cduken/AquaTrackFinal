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
                                    >{{ records.total || 0 }}</span
                                >
                                Total Records
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
                                        placeholder="Search customers, account numbers..."
                                        @keyup.enter="getRecords"
                                    />
                                </div>
                            </div>

                            <!-- Status Filter -->
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
                                            Date Range
                                        </h6>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label
                                                    class="text-xs text-gray-600 dark:text-gray-400"
                                                    >Month</label
                                                >
                                                <select
                                                    v-model="filters.month"
                                                    class="w-full p-2 text-xs text-gray-900 border border-gray-300 rounded bg-white focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                                                >
                                                    <option value="">
                                                        All Months
                                                    </option>
                                                    <option
                                                        v-for="month in months"
                                                        :key="month.value"
                                                        :value="month.value"
                                                    >
                                                        {{ month.name }}
                                                    </option>
                                                </select>
                                            </div>
                                            <div>
                                                <label
                                                    class="text-xs text-gray-600 dark:text-gray-400"
                                                    >Year</label
                                                >
                                                <select
                                                    v-model="filters.year"
                                                    class="w-full p-2 text-xs text-gray-900 border border-gray-300 rounded bg-white focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                                                >
                                                    <option value="">
                                                        All Years
                                                    </option>
                                                    <option
                                                        v-for="year in years"
                                                        :key="year"
                                                        :value="year"
                                                    >
                                                        {{ year }}
                                                    </option>
                                                </select>
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

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead
                            class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600"
                        >
                            <tr>
                                <th
                                    class="px-6 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                >
                                    Customer
                                </th>
                                <th
                                    class="px-6 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                >
                                    Account Details
                                </th>
                                <th
                                    class="px-6 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                >
                                    Reading
                                </th>
                                <th
                                    class="px-6 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                >
                                    Amount
                                </th>
                                <th
                                    class="px-6 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                >
                                    Due Date
                                </th>
                                <th
                                    class="px-6 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                >
                                    Status
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
                                v-for="record in records.data"
                                :key="record.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150"
                            >
                                <!-- Customer Column -->
                                <td class="px-6 py-2">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8 mr-3">
                                            <div
                                                class="h-8 w-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold text-xs"
                                            >
                                                {{
                                                    getUserInitials(record.user)
                                                }}
                                            </div>
                                        </div>
                                        <div>
                                            <div
                                                class="font-medium text-gray-900 dark:text-white text-xs"
                                            >
                                                {{ record.user.name }}
                                                {{ record.user.lastname }}
                                            </div>
                                            <div
                                                class="text-xs text-gray-500 dark:text-gray-400"
                                            >
                                                {{
                                                    record.user.email ||
                                                    "No email"
                                                }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Account Details -->
                                <td
                                    class="px-6 py-2 text-xs text-gray-900 dark:text-white"
                                >
                                    <div class="font-medium">
                                        {{
                                            record.user.account_number || "N/A"
                                        }}
                                    </div>
                                    <div
                                        class="text-gray-500 dark:text-gray-400"
                                    >
                                        Serial: {{ record.user.serial_number }}
                                    </div>
                                </td>

                                <!-- Reading Data -->
                                <td
                                    class="px-6 py-2 text-xs text-gray-900 dark:text-white"
                                >
                                    <div class="font-medium">
                                        {{ record.reading }} m³
                                    </div>
                                    <div
                                        class="text-gray-500 dark:text-gray-400"
                                    >
                                        {{ record.consumption }} m³
                                    </div>
                                </td>

                                <!-- Amount -->
                                <td class="px-6 py-2">
                                    <div class="text-xs">
                                        <div
                                            v-if="record.status === 'Paid'"
                                            class="font-medium text-green-600 dark:text-green-400"
                                        >
                                            ₱{{ record.amount }}
                                        </div>
                                        <div
                                            v-else-if="record.surcharge"
                                            class="space-y-1"
                                        >
                                            <div
                                                class="line-through text-gray-400"
                                            >
                                                ₱{{
                                                    (
                                                        record.amount -
                                                        record.surcharge
                                                    ).toFixed(2)
                                                }}
                                            </div>
                                            <div
                                                class="font-medium text-red-600 dark:text-red-400"
                                            >
                                                ₱{{ record.amount }}
                                            </div>
                                            <div
                                                class="text-red-600 dark:text-red-400"
                                            >
                                                +₱{{
                                                    record.surcharge
                                                }}
                                                surcharge
                                            </div>
                                        </div>
                                        <div
                                            v-else
                                            class="font-medium text-gray-900 dark:text-white"
                                        >
                                            ₱{{ record.amount }}
                                        </div>
                                    </div>
                                </td>

                                <!-- Due Date -->
                                <td
                                    class="px-6 py-2 text-xs text-gray-900 dark:text-white"
                                >
                                    <div>{{ formatDate(record.due_date) }}</div>
                                    <div
                                        class="text-gray-500 dark:text-gray-400"
                                    >
                                        {{ getDaysUntilDue(record.due_date) }}
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-2">
                                    <span
                                        :class="statusClasses(record.status)"
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-sm"
                                    >
                                        <span
                                            class="w-1.5 h-1.5 rounded-full mr-1.5"
                                            :class="
                                                statusDotClasses(record.status)
                                            "
                                        ></span>
                                        {{ record.status }}
                                    </span>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-3 text-right">
                                    <div class="flex justify-end">
                                        <div class="relative">
                                            <button
                                                @click="
                                                    toggleActionMenu(record.id)
                                                "
                                                class="p-1.5 text-gray-600 hover:text-gray-900 hover:bg-gray-200 dark:hover:text-gray-300 rounded-lg transition-colors"
                                                :data-action-button="record.id"
                                            >
                                                <MoreHorizontal
                                                    class="w-4 h-4"
                                                />
                                            </button>

                                            <!-- Action Menu Dropdown -->
                                            <div
                                                v-if="
                                                    activeActionMenu ===
                                                    record.id
                                                "
                                                class="fixed z-[1000] mt-1 w-48 bg-white dark:bg-gray-700 rounded-lg shadow-lg border border-gray-200 dark:border-gray-600"
                                                :style="
                                                    getActionDropdownStyle(
                                                        record.id
                                                    )
                                                "
                                            >
                                                <div class="py-1">
                                                    <button
                                                        @click="
                                                            showRecordDetails(
                                                                record
                                                            )
                                                        "
                                                        class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                                    >
                                                        <Eye
                                                            class="w-4 h-4 mr-3"
                                                        />
                                                        View Details
                                                    </button>
                                                    <button
                                                        @click="
                                                            showEditModal(
                                                                record
                                                            )
                                                        "
                                                        class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                                    >
                                                        <Edit
                                                            class="w-4 h-4 mr-3"
                                                        />
                                                        Edit Record
                                                    </button>
                                                    <button
                                                        v-if="
                                                            record.status !==
                                                            'Paid'
                                                        "
                                                        @click="
                                                            updateRecordStatus(
                                                                record,
                                                                'Paid'
                                                            )
                                                        "
                                                        class="flex items-center w-full px-4 py-2 text-sm text-green-600 dark:text-green-400 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                                    >
                                                        <CheckCircle
                                                            class="w-4 h-4 mr-3"
                                                        />
                                                        Mark as Paid
                                                    </button>
                                                    <button
                                                        v-if="
                                                            record.status !==
                                                            'Pending'
                                                        "
                                                        @click="
                                                            updateRecordStatus(
                                                                record,
                                                                'Pending'
                                                            )
                                                        "
                                                        class="flex items-center w-full px-4 py-2 text-sm text-yellow-600 dark:text-yellow-400 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                                    >
                                                        <Clock
                                                            class="w-4 h-4 mr-3"
                                                        />
                                                        Mark as Pending
                                                    </button>
                                                    <button
                                                        v-if="
                                                            record.status !==
                                                            'Overdue'
                                                        "
                                                        @click="
                                                            updateRecordStatus(
                                                                record,
                                                                'Overdue'
                                                            )
                                                        "
                                                        class="flex items-center w-full px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                                    >
                                                        <AlertCircle
                                                            class="w-4 h-4 mr-3"
                                                        />
                                                        Mark as Overdue
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <!-- Empty State -->
                            <tr v-if="records.data.length === 0">
                                <td colspan="7" class="px-6 py-8 text-center">
                                    <div
                                        class="flex flex-col items-center justify-center space-y-2"
                                    >
                                        <FileText
                                            class="w-12 h-12 text-gray-300"
                                        />
                                        <span
                                            class="font-medium text-gray-500 dark:text-gray-400"
                                            >No records found</span
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
                <Pagination :data="records" />
            </div>

            <!-- Modals -->
            <RecordDetailsModal
                :show="showRecordModal"
                :record="selectedRecord"
                :loading="loadingRecord"
                @close="closeRecordModal"
            />

            <RecordEditModal
                :show="showEditRecordModal"
                :record="selectedRecord"
                :loading="loadingRecord"
                @close="closeEditModal"
                @saved="handleRecordUpdated"
            />
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { router } from "@inertiajs/vue3";
import { ref, watch, onMounted, onUnmounted, nextTick, computed } from "vue";
import { pickBy, debounce } from "lodash";
import Swal from "sweetalert2";
import RecordDetailsModal from "@/Components/Admin/Modals/RecordDetailsModal.vue";
import RecordEditModal from "@/Components/Admin/Modals/RecordEditModal.vue";
import {
    Search,
    Filter,
    ChevronDown,
    RefreshCw,
    Eye,
    Edit,
    MoreHorizontal,
    FileText,
    CheckCircle,
    Clock,
    AlertCircle,
} from "lucide-vue-next";
import Users from "./Users.vue";

// Props
const props = defineProps({

    records: Object,
    filters: Object,
});

// Reactive data
const loading = ref(false);
const showFilterDropdown = ref(false);
const isResetting = ref(false);
const activeActionMenu = ref(null);
const filterDropdownStyle = ref({});
const filterButton = ref(null);
const showRecordModal = ref(false);
const selectedRecord = ref(null);
const loadingRecord = ref(false);
const showEditRecordModal = ref(false);

// Local filters
const filters = ref({
    search: props.filters.search || "",
    status: props.filters.status || "",
    month: props.filters.month || "",
    year: props.filters.year || "",
    perPage: props.filters.perPage || 10,
});

// Constants
const statusOptions = [
    { value: "", label: "All Status" },
    { value: "Paid", label: "Paid" },
    { value: "Pending", label: "Pending" },
    { value: "Overdue", label: "Overdue" },
];

const months = [
    { name: "January", value: "01" },
    { name: "February", value: "02" },
    { name: "March", value: "03" },
    { name: "April", value: "04" },
    { name: "May", value: "05" },
    { name: "June", value: "06" },
    { name: "July", value: "07" },
    { name: "August", value: "08" },
    { name: "September", value: "09" },
    { name: "October", value: "10" },
    { name: "November", value: "11" },
    { name: "December", value: "12" },
];

const currentYear = new Date().getFullYear();
const years = Array.from({ length: 6 }, (_, i) => currentYear - i);

// Computed properties
const paidRecordsCount = computed(() => {
    return (
        props.records.data?.filter((record) => record.status === "Paid")
            .length || 0
    );
});

const pendingRecordsCount = computed(() => {
    return (
        props.records.data?.filter((record) => record.status === "Pending")
            .length || 0
    );
});

const overdueRecordsCount = computed(() => {
    return (
        props.records.data?.filter((record) => record.status === "Overdue")
            .length || 0
    );
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

const getActionDropdownStyle = (recordId) => {
    if (typeof window === "undefined") return {};
    const button = document.querySelector(`[data-action-button="${recordId}"]`);
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

const toggleActionMenu = async (recordId) => {
    if (activeActionMenu.value === recordId) {
        activeActionMenu.value = null;
    } else {
        activeActionMenu.value = recordId;
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
const updateStatusFilter = (status) => {
    filters.value.status = status;
    showFilterDropdown.value = false;
    getRecords();
};

const resetFilters = () => {
    isResetting.value = true;
    setTimeout(() => {
        filters.value = {
            search: "",
            status: "",
            month: "",
            year: "",
            perPage: 10,
        };
        showFilterDropdown.value = false;
        isResetting.value = false;
        getRecords();
    }, 500);
};

// Data fetching with cache busting
const getRecords = () => {
    loading.value = true;

    // Add cache busting timestamp to prevent browser caching
    const cacheBuster = new Date().getTime();
    const params = {
        ...pickBy(filters.value),
        _t: cacheBuster, // Cache busting parameter
    };

    router.get(route("admin.records.index"), params, {
        preserveState: true,
        replace: true,
        only: ["records", "filters"], // Only update these props
        onSuccess: () => {
            loading.value = false;
            console.log("Records fetched successfully");
        },
        onError: () => {
            loading.value = false;
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Failed to fetch records. Please try again.",
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
            });
        },
    });
};

// Watch for filter changes - use immediate: true to trigger on initial load
watch(
    () => ({
        search: filters.value.search,
        status: filters.value.status,
        month: filters.value.month,
        year: filters.value.year,
        perPage: filters.value.perPage,
    }),
    (newFilters) => {
        getRecords();
    },
    { deep: true, immediate: true }
);

// Utility methods
const getUserInitials = (user) => {
    if (!user) return "?";
    return (
        `${user.name?.[0] || ""}${user.lastname?.[0] || ""}`.toUpperCase() ||
        "?"
    );
};

const formatDate = (dateString) => {
    if (!dateString) return "N/A";
    const options = { year: "numeric", month: "short", day: "numeric" };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

const getDaysUntilDue = (dueDate) => {
    if (!dueDate) return "";
    const today = new Date();
    const due = new Date(dueDate);
    const diffTime = due - today;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    if (diffDays === 0) return "Due today";
    if (diffDays === 1) return "Due tomorrow";
    if (diffDays > 1) return `Due in ${diffDays} days`;
    if (diffDays === -1) return "Overdue by 1 day";
    return `Overdue by ${Math.abs(diffDays)} days`;
};

// Status styling
const statusClasses = (status) => {
    return {
        "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200":
            status === "Paid",
        "bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200":
            status === "Pending",
        "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200":
            status === "Overdue",
    };
};

const statusDotClasses = (status) => {
    return {
        "bg-green-400": status === "Paid",
        "bg-yellow-400": status === "Pending",
        "bg-red-400": status === "Overdue",
    };
};

// Modal methods
const showRecordDetails = async (record) => {
    selectedRecord.value = record;
    loadingRecord.value = true;
    showRecordModal.value = true;
    activeActionMenu.value = null;

    try {
        const response = await fetch(route("admin.records.details", record.id));
        if (response.ok) {
            selectedRecord.value = await response.json();
        }
    } catch (error) {
        console.error("Error fetching record details:", error);
        selectedRecord.value = record;
    } finally {
        loadingRecord.value = false;
    }
};

const closeRecordModal = () => {
    showRecordModal.value = false;
    selectedRecord.value = null;
};

const showEditModal = (record) => {
    selectedRecord.value = record;
    showEditRecordModal.value = true;
    activeActionMenu.value = null;
};

const closeEditModal = () => {
    showEditRecordModal.value = false;
    selectedRecord.value = null;
};

const handleRecordUpdated = () => {
    // Force a complete refresh of the data
    getRecords();
    closeEditModal();
};

// Status update with proper timing for SweetAlert
const updateRecordStatus = async (record, status) => {
    activeActionMenu.value = null;

    try {
        // Show loading state immediately
        loading.value = true;

        // First show the SweetAlert notification
        const toast = Swal.fire({
            icon: "success",
            title: "Status Updated",

            toast: true,
            position: "bottom-end", // Changed to bottom-end
            showConfirmButton: false,
            timer: 2000, // Show for 2 seconds
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener("mouseenter", Swal.stopTimer);
                toast.addEventListener("mouseleave", Swal.resumeTimer);
            },
        });

        // Wait for the SweetAlert to show for 1.5 seconds before refreshing
        setTimeout(async () => {
            try {
                await router.put(
                    route("admin.records.update", record.id),
                    { status },
                    {
                        preserveScroll: true,
                        preserveState: true,
                    }
                );

                // Refresh the records after successful update
                getRecords();
            } catch (error) {
                console.error("Error updating record status:", error);
                loading.value = false;

                // Show error message
                Swal.fire({
                    icon: "error",
                    title: "Update Failed",
                    text: "Failed to update record status. Please try again.",
                    toast: true,
                    position: "bottom-end",
                    showConfirmButton: false,
                    timer: 3000,
                });
            }
        }, 1500); // Wait 1.5 seconds before making the API call
    } catch (error) {
        console.error("Error in update process:", error);
        loading.value = false;

        Swal.fire({
            icon: "error",
            title: "Update Failed",
            text: "Failed to update record status. Please try again.",
            toast: true,
            position: "bottom-end",
            showConfirmButton: false,
            timer: 3000,
        });
    }
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
</style>
