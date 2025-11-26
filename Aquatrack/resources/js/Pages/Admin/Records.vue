//Pages/Admin/Records.vue
<template>
    <AdminLayout>
        <div class="mx-auto w-full">
            <div class="bg-white border border-gray-200 overflow-hidden">
                <!-- Search and Filter Section -->
                <div class="p-2 border-b border-gray-200">
                    <div
                        class="flex flex-col md:flex-row md:items-center justify-between gap-4"
                    >
                        <div class="flex items-center px-2 space-x-4">
                            <h5 class="text-sm font-semibold text-gray-500">
                                <span class="font-bold text-black">{{
                                    records.total || 0
                                }}</span>
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
                                        <Search class="w-4 h-4 text-gray-900" />
                                    </div>
                                    <input
                                        v-model="filters.search"
                                        type="text"
                                        class="block w-full md:w-auto pl-10 text-sm text-gray-900 border border-gray-300 rounded-sm bg-white focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Search customers, account numbers..."
                                        @keyup.enter="getRecords"
                                    />
                                </div>
                            </div>

                            <!-- Export Dropdown -->
                            <div class="relative">
                                <button
                                    @click="toggleExportDropdown"
                                    ref="exportButton"
                                    :disabled="exportLoading"
                                    class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                    type="button"
                                >
                                    <Download
                                        class="w-4 h-4 mr-2"
                                        :class="{
                                            'animate-spin': exportLoading,
                                        }"
                                    />
                                    {{
                                        exportLoading
                                            ? "Exporting..."
                                            : "Export"
                                    }}
                                    <ChevronDown class="ml-1 w-4 h-4" />
                                </button>

                                <!-- Export Options Dropdown -->
                                <div
                                    v-if="showExportDropdown"
                                    class="fixed z-[1000] mt-1 w-48 bg-white rounded-lg shadow-lg border border-gray-200"
                                    :style="exportDropdownStyle"
                                    @click.stop
                                >
                                    <div class="py-1">
                                        <button
                                            @click="exportRecords('csv')"
                                            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                                        >
                                            <FileText class="w-4 h-4 mr-3" />
                                            Export as CSV
                                        </button>
                                        <button
                                            @click="exportRecords('excel')"
                                            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                                        >
                                            <FileText class="w-4 h-4 mr-3" />
                                            Export as Excel
                                        </button>
                                        <button
                                            @click="exportRecords('pdf')"
                                            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                                        >
                                            <FileText class="w-4 h-4 mr-3" />
                                            Export as PDF
                                        </button>
                                        <button
                                            @click="printRecords"
                                            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                                        >
                                            <Printer class="w-4 h-4 mr-3" />
                                            Print Records
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Filter -->
                            <div class="relative">
                                <button
                                    @click="toggleFilterDropdown"
                                    ref="filterButton"
                                    class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    type="button"
                                >
                                    <Filter class="w-4 h-4 mr-2" />
                                    Filter
                                    <ChevronDown class="ml-1 w-4 h-4" />
                                </button>

                                <div
                                    v-if="showFilterDropdown"
                                    class="fixed z-[1000] mt-1 w-64 bg-white rounded-lg shadow-lg border border-gray-200"
                                    :style="filterDropdownStyle"
                                    @click.stop
                                >
                                    <div class="p-4">
                                        <h6
                                            class="mb-2 text-sm font-medium text-gray-900"
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
                                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500"
                                                />
                                                <label
                                                    :for="`status-${statusOption.value}`"
                                                    class="ml-2 text-sm text-gray-700"
                                                >
                                                    {{ statusOption.label }}
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Zone Filter -->
                                        <h6
                                            class="mb-2 mt-4 text-sm font-medium text-gray-900"
                                        >
                                            Zone
                                        </h6>
                                        <div
                                            class="space-y-2 max-h-32 overflow-y-auto"
                                        >
                                            <div
                                                class="flex items-center"
                                                v-for="zoneOption in zoneOptions"
                                                :key="zoneOption.value"
                                            >
                                                <input
                                                    :id="`zone-${zoneOption.value}`"
                                                    type="radio"
                                                    name="zone"
                                                    :value="zoneOption.value"
                                                    :checked="
                                                        filters.zone ===
                                                        zoneOption.value
                                                    "
                                                    @change="
                                                        updateZoneFilter(
                                                            zoneOption.value
                                                        )
                                                    "
                                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500"
                                                />
                                                <label
                                                    :for="`zone-${zoneOption.value}`"
                                                    class="ml-2 text-sm text-gray-700"
                                                >
                                                    {{ zoneOption.label }}
                                                </label>
                                            </div>
                                        </div>

                                        <h6
                                            class="mb-2 mt-4 text-sm font-medium text-gray-900"
                                        >
                                            Date Range
                                        </h6>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label
                                                    class="text-xs text-gray-600"
                                                    >Month</label
                                                >
                                                <select
                                                    v-model="filters.month"
                                                    class="w-full p-2 text-xs text-gray-900 border border-gray-300 rounded bg-white focus:ring-blue-500 focus:border-blue-500"
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
                                                    class="text-xs text-gray-600"
                                                    >Year</label
                                                >
                                                <select
                                                    v-model="filters.year"
                                                    class="w-full p-2 text-xs text-gray-900 border border-gray-300 rounded bg-white focus:ring-blue-500 focus:border-blue-500"
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
                                        class="flex items-center justify-between p-4 border-t border-gray-200"
                                    >
                                        <button
                                            @click="resetFilters"
                                            class="flex items-center text-sm text-gray-600 hover:text-gray-800"
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
                                class="bg-gray-50 border-b border-gray-200 sticky top-0 z-10"
                            >
                                <tr>
                                    <th
                                        class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Customer
                                    </th>
                                    <th
                                        class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Account Details
                                    </th>
                                    <th
                                        class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Reading
                                    </th>
                                    <th
                                        class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Amount
                                    </th>
                                    <th
                                        class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Due Date
                                    </th>
                                    <th
                                        class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Status
                                    </th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr
                                    v-for="record in records.data"
                                    :key="record.id"
                                    class="hover:bg-gray-50 transition-colors duration-150"
                                >
                                    <!-- Customer Column -->
                                    <td class="px-6 py-2">
                                        <div class="flex items-center">
                                            <div
                                                class="flex-shrink-0 h-8 w-8 mr-3"
                                            >
                                                <img
                                                    v-if="
                                                        record.user.avatar_url
                                                    "
                                                    :src="
                                                        record.user.avatar_url
                                                    "
                                                    :alt="record.user.name"
                                                    class="h-8 w-8 rounded-full object-cover"
                                                />
                                                <div
                                                    v-else
                                                    class="h-8 w-8 rounded-full flex items-center justify-center text-white font-semibold text-xs"
                                                    :class="
                                                        getAvatarColor(
                                                            record.user.name
                                                        )
                                                    "
                                                >
                                                    {{
                                                        getUserInitials(
                                                            record.user
                                                        )
                                                    }}
                                                </div>
                                            </div>
                                            <div>
                                                <div
                                                    class="font-medium text-gray-900 text-xs"
                                                >
                                                    {{ record.user.name }}
                                                    {{ record.user.lastname }}
                                                </div>
                                                <div
                                                    class="text-xs text-gray-500"
                                                >
                                                    {{ record.user.email }}
                                                </div>
                                                <div
                                                    v-if="record.user.zone"
                                                    class="text-xs text-blue-600 font-medium mt-1"
                                                >
                                                    Zone {{ record.user.zone }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Account Details -->
                                    <td class="px-6 py-2 text-xs text-gray-900">
                                        <div class="font-medium">
                                            {{
                                                record.user.account_number ||
                                                "N/A"
                                            }}
                                        </div>
                                        <div class="text-gray-500">
                                            Serial:
                                            {{ record.user.serial_number }}
                                        </div>
                                    </td>

                                    <!-- Reading Data -->
                                    <td class="px-6 py-2 text-xs text-gray-900">
                                        <div class="font-medium">
                                            {{ record.reading }} m³
                                        </div>
                                        <div class="text-gray-500">
                                            Consumption:
                                            {{ record.consumption }} m³
                                        </div>
                                    </td>

                                    <!-- Amount -->
                                    <td class="px-6 py-2">
                                        <div class="text-xs">
                                            <div
                                                v-if="record.status === 'Paid'"
                                                class="font-medium text-green-600"
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
                                                    class="font-medium text-red-600"
                                                >
                                                    ₱{{ record.amount }}
                                                </div>
                                                <div class="text-red-600">
                                                    +₱{{ record.surcharge }}
                                                    surcharge
                                                </div>
                                            </div>
                                            <div
                                                v-else
                                                class="font-medium text-gray-900"
                                            >
                                                ₱{{ record.amount }}
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Due Date -->
                                    <td class="px-6 py-2 text-xs text-gray-900">
                                        <div>
                                            {{ formatDate(record.due_date) }}
                                        </div>
                                        <div class="text-gray-500">
                                            {{
                                                getDaysUntilDue(record.due_date)
                                            }}
                                        </div>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-2">
                                        <span
                                            :class="
                                                statusClasses(record.status)
                                            "
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-sm"
                                        >
                                            <span
                                                class="w-1.5 h-1.5 rounded-full mr-1.5"
                                                :class="
                                                    statusDotClasses(
                                                        record.status
                                                    )
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
                                                        toggleActionMenu(
                                                            record.id
                                                        )
                                                    "
                                                    class="p-1.5 text-gray-600 hover:text-gray-900 hover:bg-gray-200 rounded-lg transition-colors"
                                                    :data-action-button="
                                                        record.id
                                                    "
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
                                                    class="fixed z-[1000] mt-1 w-48 bg-white rounded-lg shadow-lg border border-gray-200"
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
                                                            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
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
                                                            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
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
                                                            class="flex items-center w-full px-4 py-2 text-sm text-green-600 hover:bg-gray-100 transition-colors"
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
                                                            class="flex items-center w-full px-4 py-2 text-sm text-yellow-600 hover:bg-gray-100 transition-colors"
                                                        >
                                                            <Clock
                                                                class="w-4 h-4 mr-3"
                                                            />
                                                            Mark as Pending
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Empty State -->
                                <tr v-if="records.data.length === 0">
                                    <td
                                        colspan="7"
                                        class="px-6 py-24 text-center"
                                    >
                                        <div
                                            class="flex flex-col items-center justify-center space-y-4"
                                        >
                                            <FileText
                                                class="w-20 h-20 text-gray-300"
                                            />
                                            <span
                                                class="text-2xl font-medium text-gray-500"
                                            >
                                                No records found
                                            </span>
                                            <span class="text-sm text-gray-400">
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
                        class="flex-shrink-0 border-t border-gray-200 bg-white"
                    >
                        <Pagination :data="records" />
                    </div>
                </div>
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

            <!-- Hidden iframe for printing -->
            <iframe id="printFrame" class="hidden"></iframe>
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
    Download,
    Printer,
} from "lucide-vue-next";

// Props
const props = defineProps({
    records: Object,
    filters: Object,
});

// Reactive data
const loading = ref(false);
const showFilterDropdown = ref(false);
const showExportDropdown = ref(false);
const isResetting = ref(false);
const activeActionMenu = ref(null);
const filterDropdownStyle = ref({});
const exportDropdownStyle = ref({});
const filterButton = ref(null);
const exportButton = ref(null);
const showRecordModal = ref(false);
const selectedRecord = ref(null);
const loadingRecord = ref(false);
const showEditRecordModal = ref(false);
const exportLoading = ref(false);

// Local filters - initialize from props but don't trigger immediate watch
const filters = ref({
    search: props.filters.search || "",
    status: props.filters.status || "",
    month: props.filters.month || "",
    year: props.filters.year || "",
    zone: props.filters.zone || "",
    perPage: props.filters.perPage || 10,
});

// Track if component is mounted to prevent initial automatic calls
const isMounted = ref(false);

// Constants
const statusOptions = [
    { value: "", label: "All Status" },
    { value: "Paid", label: "Paid" },
    { value: "Pending", label: "Pending" },
    { value: "Overdue", label: "Overdue" },
];

const zoneOptions = [
    { value: "", label: "All Zones" },
    { value: "Zone 1", label: "Zone 1" },
    { value: "Zone 2", label: "Zone 2" },
    { value: "Zone 3", label: "Zone 3" },
    { value: "Zone 4", label: "Zone 4" },
    { value: "Zone 5", label: "Zone 5" },
    { value: "Zone 6", label: "Zone 6" },
    { value: "Zone 7", label: "Zone 7" },
    { value: "Zone 8", label: "Zone 8" },
    { value: "Zone 9", label: "Zone 9" },
    { value: "Zone 10", label: "Zone 10" },
    { value: "Zone 11", label: "Zone 11" },
    { value: "Zone 12", label: "Zone 12" },
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

// Export functions
const toggleExportDropdown = async () => {
    showExportDropdown.value = !showExportDropdown.value;
    if (showExportDropdown.value && exportButton.value) {
        await nextTick();
        const rect = exportButton.value.getBoundingClientRect();
        const dropdownWidth = 192;
        exportDropdownStyle.value = {
            left: `${rect.right - dropdownWidth}px`,
            top: `${rect.bottom + 8}px`,
            position: "fixed",
        };
    }
};

const exportRecords = async (format) => {
    try {
        exportLoading.value = true;
        showExportDropdown.value = false;

        // Create form data with filters
        const formData = new FormData();
        formData.append("format", format);

        // Add filters
        if (filters.value.search)
            formData.append("search", filters.value.search);
        if (filters.value.status)
            formData.append("status", filters.value.status);
        if (filters.value.month) formData.append("month", filters.value.month);
        if (filters.value.year) formData.append("year", filters.value.year);
        if (filters.value.zone) formData.append("zone", filters.value.zone);

        // Use fetch with POST method
        const response = await fetch(route("admin.records.export"), {
            method: "POST",
            body: formData,
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
        });

        if (response.ok) {
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement("a");
            a.style.display = "none";
            a.href = url;

            // Set filename based on format
            const fileName = `billing_records_${
                new Date().toISOString().split("T")[0]
            }.${format}`;
            a.download = fileName;

            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);

            Swal.fire({
                icon: "success",
                title: "Export Completed",
                text: `Your ${format.toUpperCase()} file has been downloaded.`,
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
            });
        } else {
            throw new Error("Export failed");
        }

        exportLoading.value = false;
    } catch (error) {
        console.error("Export error:", error);
        Swal.fire({
            icon: "error",
            title: "Export Failed",
            text: "Failed to export records. Please try again.",
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
        });
        exportLoading.value = false;
    }
};

const printRecords = () => {
    showExportDropdown.value = false;

    // Calculate totals
    const totalRecords = props.records.total;
    const paidRecords = props.records.data.filter(
        (r) => r.status === "Paid"
    ).length;
    const pendingRecords = props.records.data.filter(
        (r) => r.status === "Pending"
    ).length;
    const overdueRecords = props.records.data.filter(
        (r) => r.status === "Overdue"
    ).length;

    // Calculate total amounts
    const totalAmount = props.records.data.reduce(
        (sum, record) => sum + parseFloat(record.amount),
        0
    );
    const paidAmount = props.records.data
        .filter((r) => r.status === "Paid")
        .reduce((sum, record) => sum + parseFloat(record.amount), 0);
    const pendingAmount = props.records.data
        .filter((r) => r.status === "Pending")
        .reduce((sum, record) => sum + parseFloat(record.amount), 0);
    const overdueAmount = props.records.data
        .filter((r) => r.status === "Overdue")
        .reduce((sum, record) => sum + parseFloat(record.amount), 0);

    // Create a print-friendly version of the table
    const printWindow = window.open("", "_blank");
    const printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Billing Records Report</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
                .header h1 { margin: 0; color: #333; }
                .header .subtitle { color: #666; margin-top: 5px; }
                .summary { margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 5px; }
                .summary-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-top: 10px; }
                .summary-item { padding: 10px; background: white; border-radius: 5px; border: 1px solid #ddd; }
                .summary-value { font-size: 16px; font-weight: bold; color: #333; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 12px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f8f9fa; font-weight: bold; }
                .status-paid { color: green; font-weight: bold; }
                .status-pending { color: orange; font-weight: bold; }
                .status-overdue { color: red; font-weight: bold; }
                .text-right { text-align: right; }
                .text-center { text-align: center; }
                .amount { font-weight: bold; }
                @media print {
                    body { margin: 10px; font-size: 12px; }
                    .no-print { display: none; }
                    table { font-size: 10px; }
                    th, td { padding: 6px; }
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Water Billing Records Report</h1>
                <div class="subtitle">Generated on ${new Date().toLocaleDateString()} at ${new Date().toLocaleTimeString()}</div>
            </div>

            <div class="summary">
                <strong>Report Summary:</strong>
                <div class="summary-grid">
                    <div class="summary-item">
                        <div>Total Records: <span class="summary-value">${totalRecords}</span></div>
                        <div>Paid Records: <span class="summary-value">${paidRecords}</span></div>
                        <div>Pending Records: <span class="summary-value">${pendingRecords}</span></div>
                        <div>Overdue Records: <span class="summary-value">${overdueRecords}</span></div>
                    </div>
                    <div class="summary-item">
                        <div>Total Amount: <span class="summary-value">₱${totalAmount.toFixed(
                            2
                        )}</span></div>
                        <div>Paid Amount: <span class="summary-value">₱${paidAmount.toFixed(
                            2
                        )}</span></div>
                        <div>Pending Amount: <span class="summary-value">₱${pendingAmount.toFixed(
                            2
                        )}</span></div>
                        <div>Overdue Amount: <span class="summary-value">₱${overdueAmount.toFixed(
                            2
                        )}</span></div>
                    </div>
                </div>
            </div>

            <div class="filters">
                <strong>Filters Applied:</strong><br>
                Search: ${filters.value.search || "None"} |
                Status: ${filters.value.status || "All"} |
                Zone: ${filters.value.zone || "All"} |
                Month: ${
                    filters.value.month
                        ? months.find((m) => m.value === filters.value.month)
                              ?.name
                        : "All"
                } |
                Year: ${filters.value.year || "All"}
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Account No.</th>
                        <th>Customer Name</th>
                        <th>Zone</th>
                        <th>Reading Date</th>
                        <th>Due Date</th>
                        <th class="text-center">Reading (m³)</th>
                        <th class="text-center">Consumption (m³)</th>
                        <th class="text-right">Amount (₱)</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    ${props.records.data
                        .map(
                            (record) => `
                        <tr>
                            <td>${record.user.account_number || "N/A"}</td>
                            <td>${record.user.name} ${record.user.lastname}</td>
                            <td>${record.user.zone || "N/A"}</td>
                            <td>${formatDate(record.reading_date)}</td>
                            <td>${formatDate(record.due_date)}</td>
                            <td class="text-center">${record.reading}</td>
                            <td class="text-center">${record.consumption}</td>
                            <td class="text-right amount">₱${parseFloat(
                                record.amount
                            ).toFixed(2)}</td>
                            <td class="text-center status-${record.status.toLowerCase()}">${
                                record.status
                            }</td>
                        </tr>
                    `
                        )
                        .join("")}
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="7" class="text-right" style="font-weight: bold;">Grand Total:</td>
                        <td class="text-right amount">₱${totalAmount.toFixed(
                            2
                        )}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>

            <div class="no-print" style="margin-top: 20px; text-align: center;">
                <button onclick="window.print()" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; margin: 5px;">
                    Print Report
                </button>
                <button onclick="window.close()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer; margin: 5px;">
                    Close Window
                </button>
            </div>

            <div style="margin-top: 30px; text-align: center; font-size: 11px; color: #666;">
                <p>Generated by Water Billing System | ${new Date().toLocaleDateString()}</p>
            </div>
        </body>
        </html>
    `;

    printWindow.document.write(printContent);
    printWindow.document.close();

    // Auto-print after content loads
    printWindow.onload = function () {
        printWindow.focus();
        // Auto-print after a short delay to ensure content is rendered
        setTimeout(() => {
            printWindow.print();
        }, 500);
    };
};

// Dropdown handlers
const toggleFilterDropdown = async () => {
    showFilterDropdown.value = !showFilterDropdown.value;
    if (showFilterDropdown.value && filterButton.value) {
        await nextTick();
        const rect = filterButton.value.getBoundingClientRect();
        const dropdownWidth = 256;
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

// Filter methods
const updateStatusFilter = (status) => {
    filters.value.status = status;
    showFilterDropdown.value = false;
    getRecords();
};

const updateZoneFilter = (zone) => {
    filters.value.zone = zone;
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
            zone: "",
            perPage: 10,
        };
        showFilterDropdown.value = false;
        isResetting.value = false;
        getRecords();
    }, 500);
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

    const isExportClick =
        exportButton.value?.contains(event.target) ||
        (showExportDropdown.value &&
            event.target.closest(".fixed.z-\\[1000\\]"));
    if (!isExportClick) {
        showExportDropdown.value = false;
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
    isMounted.value = true;
    document.addEventListener("click", handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener("click", handleClickOutside);
});

// Data fetching with cache busting - only call when filters change after mount
const getRecords = () => {
    if (!isMounted.value) return; // Don't call API until component is mounted

    loading.value = true;

    // Add cache busting timestamp to prevent browser caching
    const cacheBuster = new Date().getTime();
    const params = {
        ...pickBy(filters.value),
        _t: cacheBuster, // Cache busting parameter
    };

    console.log('Sending filters to backend:', params); // Debug log

    router.get(route("admin.records.index"), params, {
        preserveState: true,
        replace: true,
        only: ["records", "filters"], // Only update these props
        onSuccess: () => {
            loading.value = false;
            console.log("Records fetched successfully with zone filter:", filters.value.zone);
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

// Watch for filter changes - only trigger after component is mounted
watch(
    () => ({
        search: filters.value.search,
        status: filters.value.status,
        month: filters.value.month,
        year: filters.value.year,
        zone: filters.value.zone,
        perPage: filters.value.perPage,
    }),
    debounce((newFilters) => {
        if (isMounted.value) {
            getRecords();
        }
    }, 300),
    { deep: true }
);

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

// Add getAvatarColor function
const getAvatarColor = (name) => {
    if (!name) return "bg-gray-400";
    let hash = 0;
    for (let i = 0; i < name.length; i++) {
        hash = name.charCodeAt(i) + ((hash << 5) - hash);
    }
    const index = Math.abs(hash) % avatarColors.length;
    return avatarColors[index];
};

// Utility methods
const getUserInitials = (user) => {
    if (!user) return "?";
    const firstName = user.name || "";
    const lastName = user.lastname || "";
    return `${firstName[0] || ""}${lastName[0] || ""}`.toUpperCase() || "?";
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
        "bg-green-100 text-green-800": status === "Paid",
        "bg-yellow-100 text-yellow-800": status === "Pending",
        "bg-red-100 text-red-800": status === "Overdue",
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
