<template>
    <CustomerLayout>
        <!-- Content Section -->
        <div class="bg-gray-50 px-2 py-1">
            <!-- Header Section -->
            <div class="mb-4">
                <h1 class="text-xl font-semibold text-gray-900">
                    Water Reports
                </h1>
                <p class="text-gray-600 text-sm">
                    Track and manage your water reports
                </p>
            </div>

            <!-- Filters and Search -->
            <div class="flex flex-col md:flex-row gap-3 mb-6">
                <div class="flex-1">
                    <div class="relative">
                        <div
                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                        >
                            <v-icon
                                name="bi-search"
                                class="text-gray-400 w-4 h-4"
                            />
                        </div>
                        <input
                            v-model="filters.search"
                            type="text"
                            placeholder="Search reports..."
                            class="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                            @keyup.enter="getReports"
                        />
                    </div>
                </div>
                <button
                    @click="showAddModal = true"
                    class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white border border-transparent rounded-lg font-medium text-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                >
                    <v-icon name="bi-plus-lg" class="mr-2 w-4 h-4" />
                    Submit New Report
                </button>

                <div class="flex items-center gap-2">
                    <select
                        v-model="filters.status"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        @change="getReports"
                    >
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="resolved">Resolved</option>
                    </select>
                    <button
                        @click="resetFilters"
                        class="p-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                        title="Reset Filters"
                    >
                        <v-icon
                            name="bi-arrow-clockwise"
                            class="text-gray-600 w-4 h-4"
                        />
                    </button>
                </div>
            </div>

            <!-- Reports Table Container -->
            <div
                class="bg-white rounded-lg border border-gray-200 overflow-hidden flex flex-col"
                style="height: 600px; min-height: 600px"
            >
                <!-- Table with Scrollable Body -->
                <div class="flex-1 overflow-x-auto overflow-y-auto">
                    <table class="w-full">
                        <thead
                            class="bg-gray-50 border-b border-gray-200 sticky top-0 z-10"
                        >
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Tracking Code
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Location
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Issue Type
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Description
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Status
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Date
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr
                                v-for="report in reports.data"
                                :key="report.id"
                                class="hover:bg-gray-50 transition-colors"
                            >
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium bg-blue-50 text-blue-700 border border-blue-200"
                                    >
                                        {{ report.tracking_code }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <div
                                            class="flex items-center text-sm font-medium text-gray-900"
                                        >
                                            <v-icon
                                                name="bi-geo-alt"
                                                class="text-gray-400 mr-1.5 w-3.5 h-3.5"
                                            />
                                            {{ report.zone }}
                                        </div>
                                        <div class="text-sm text-gray-500 mt-1">
                                            {{ report.barangay }},
                                            {{ report.purok }}
                                        </div>
                                    </div>
                                </td>
                                <td
                                    class="px-4 py-4 whitespace-nowrap text-sm text-gray-900"
                                >
                                    <div class="font-medium">
                                        {{
                                            report.water_issue_type === "others"
                                                ? report.custom_water_issue ||
                                                  "Custom Issue"
                                                : report.water_issue_type
                                        }}
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-700">
                                    <div class="line-clamp-2 max-w-xs">
                                        {{ report.description }}
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span
                                        :class="statusClasses(report.status)"
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                                    >
                                        <span
                                            class="w-2 h-2 rounded-full mr-2"
                                            :class="
                                                statusDotClasses(report.status)
                                            "
                                        ></span>
                                        {{ formatStatus(report.status) }}
                                    </span>
                                </td>
                                <td
                                    class="px-4 py-4 whitespace-nowrap text-sm text-gray-600"
                                >
                                    <div>
                                        {{ formatDate(report.created_at) }}
                                    </div>
                                    <div class="text-gray-400 text-xs">
                                        {{ formatTime(report.created_at) }}
                                    </div>
                                </td>
                                <td
                                    class="px-4 py-4 whitespace-nowrap text-sm font-medium"
                                >
                                    <button
                                        class="text-blue-600 hover:text-blue-800 p-1.5 rounded hover:bg-blue-50 transition-colors"
                                        title="View Details"
                                        @click="openReportDetails(report)"
                                    >
                                        <v-icon
                                            name="bi-eye-fill"
                                            class="w-4 h-4"
                                        />
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="reports.data.length === 0">
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div
                                        class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4"
                                    >
                                        <v-icon
                                            name="bi-file-earmark-excel"
                                            class="text-gray-400 w-8 h-8"
                                        />
                                    </div>
                                    <h3
                                        class="text-base font-semibold text-gray-900 mb-1"
                                    >
                                        No reports found
                                    </h3>
                                    <p
                                        class="text-gray-500 text-sm max-w-md mx-auto"
                                    >
                                        <span v-if="hasActiveFilters">
                                            No reports match your current
                                            filters. Try adjusting your search
                                            criteria.
                                        </span>
                                        <span v-else>
                                            You haven't submitted any water
                                            quality reports yet. Click "Submit
                                            New Report" to get started.
                                        </span>
                                    </p>
                                    <button
                                        v-if="hasActiveFilters"
                                        @click="resetFilters"
                                        class="mt-3 inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    >
                                        <v-icon
                                            name="bi-arrow-clockwise"
                                            class="mr-2 w-4 h-4"
                                        />
                                        Reset Filters
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination - Fixed at Bottom -->
                <div
                    v-if="reports.data.length > 0"
                    class="flex-shrink-0 border-t border-gray-200 bg-white"
                >
                    <Pagination :data="reports" />
                </div>
            </div>
        </div>

        <!-- Add Report Modal -->
        <CustomerReportModal
            :isOpen="showAddModal"
            :zones="zones"
            @update:isOpen="showAddModal = $event"
            @update:successData="handleReportAdded"
            @update:showSuccessModal="showSuccessModal = $event"
        />

        <!-- Report Details Modal -->
        <ReportDetailsModal
            :show="showDetailsModal"
            :report="selectedReport"
            @close="showDetailsModal = false"
        />
    </CustomerLayout>
</template>

<script setup>
import CustomerLayout from "@/Layouts/CustomerLayout.vue";
import ReportDetailsModal from "@/Components/Modals/ReportDetailsModal.vue";
import Pagination from "@/Components/Pagination.vue";
import { router, usePage } from "@inertiajs/vue3";
import { ref, computed, watch, onMounted } from "vue";
import { debounce } from "lodash";
import Swal from "sweetalert2";
import CustomerReportModal from "@/Components/Customer/CustomerReportModal.vue";

const page = usePage();

const props = defineProps({
    reports: {
        type: Object,
        required: true,
        default: () => ({
            data: [],
            meta: {},
            links: {},
        }),
    },
    zones: {
        type: Object,
        required: true,
        default: () => ({}),
    },
    swal: Object,
    filters: Object,
});

// Reactive state
const filters = ref({
    status: props.filters?.status || "",
    search: props.filters?.search || "",
});

const showAddModal = ref(false);
const showDetailsModal = ref(false);
const showSuccessModal = ref(false);
const selectedReport = ref(null);

// Computed properties
const hasActiveFilters = computed(() => {
    return filters.value.search || filters.value.status;
});

// Methods
const getReports = () => {
    router.get(route("customer.reports"), filters.value, {
        preserveState: true,
        replace: true,
    });
};

const resetFilters = () => {
    filters.value = {
        status: "",
        search: "",
    };
};

const openReportDetails = (report) => {
    selectedReport.value = report;
    showDetailsModal.value = true;
};

const handleReportAdded = (successData) => {
    showAddModal.value = false;
    // Refresh the reports list
    getReports();
};

// Status formatting methods
const statusClasses = (status) => {
    return {
        "bg-gray-100 text-gray-800": status === "pending",
        "bg-blue-100 text-blue-800": status === "in_progress",
        "bg-green-100 text-green-800": status === "resolved",
    };
};

const statusDotClasses = (status) => {
    return {
        "bg-gray-500": status === "pending",
        "bg-blue-500": status === "in_progress",
        "bg-green-500": status === "resolved",
    };
};

const formatStatus = (status) => {
    const statusMap = {
        pending: "Pending",
        in_progress: "In Progress",
        resolved: "Resolved",
    };
    return statusMap[status] || status;
};

const formatDate = (dateString) => {
    if (!dateString) return "N/A";
    const options = { year: "numeric", month: "short", day: "numeric" };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

const formatTime = (dateString) => {
    if (!dateString) return "";
    return new Date(dateString).toLocaleTimeString([], {
        hour: "2-digit",
        minute: "2-digit",
    });
};

// Watch for filter changes with debounce
watch(
    filters,
    debounce(() => {
        getReports();
    }, 300),
    { deep: true }
);

// Watch for SweetAlert notifications
watch(
    () => page.props.swal,
    (newSwal) => {
        if (newSwal) {
            Swal.fire({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
                icon: newSwal.icon,
                title: newSwal.title,
                text: newSwal.text,
                ...(newSwal.trackingCode && {
                    footer: `Tracking Code: ${newSwal.trackingCode}`,
                }),
                didOpen: (toast) => {
                    toast.addEventListener("mouseenter", Swal.stopTimer);
                    toast.addEventListener("mouseleave", Swal.resumeTimer);
                },
            });
        }
    },
    { immediate: true }
);

onMounted(() => {
    if (page.props.swal) {
        Swal.fire({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 5000,
            timerProgressBar: true,
            icon: page.props.swal.icon,
            title: page.props.swal.title,
            text: page.props.swal.text,
            ...(page.props.swal.trackingCode && {
                footer: `Tracking Code: ${page.props.swal.trackingCode}`,
            }),
            didOpen: (toast) => {
                toast.addEventListener("mouseenter", Swal.stopTimer);
                toast.addEventListener("mouseleave", Swal.resumeTimer);
            },
        });
    }
});
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Custom scrollbar styling */
.overflow-y-auto {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 transparent;
}

.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: transparent;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background-color: #cbd5e0;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background-color: #a0aec0;
}
</style>
