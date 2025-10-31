<template>
    <CustomerLayout>
        <!-- Content Section -->
        <div class="p-2">
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
                            type="text"
                            placeholder="Search reports..."
                            class="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
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
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option>All Status</option>
                        <option>Pending</option>
                        <option>In Progress</option>
                        <option>Resolved</option>
                    </select>
                    <button
                        class="p-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                    >
                        <v-icon
                            name="bi-filter"
                            class="text-gray-600 w-4 h-4"
                        />
                    </button>
                </div>
            </div>

            <!-- Reports Table -->
            <div
                class="bg-white rounded-lg border border-gray-200 overflow-hidden"
            >
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="w-12 px-3 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    ID
                                </th>
                                <th
                                    class="w-28 px-3 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Tracking Code
                                </th>
                                <th
                                    class="w-36 px-3 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Location
                                </th>
                                <th
                                    class="px-3 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Description
                                </th>
                                <th
                                    class="w-24 px-3 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Status
                                </th>
                                <th
                                    class="w-24 px-3 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Date
                                </th>
                                <th
                                    class="w-16 px-3 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr
                                v-for="report in paginatedReports"
                                :key="report.id"
                                class="hover:bg-gray-50 transition-colors"
                            >
                                <td
                                    class="px-3 py-3 whitespace-nowrap text-sm font-medium text-gray-900"
                                >
                                    {{ report.id }}
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100"
                                    >
                                        {{ report.tracking_code }}
                                    </span>
                                </td>
                                <td
                                    class="px-3 py-3 whitespace-nowrap text-sm text-gray-700"
                                >
                                    <div class="flex items-center">
                                        <v-icon
                                            name="bi-geo-alt"
                                            class="text-gray-400 mr-1.5 w-3.5 h-3.5"
                                        />
                                        {{ report.barangay }},
                                        {{ report.municipality }}
                                    </div>
                                </td>
                                <td class="px-3 py-3 text-sm text-gray-700">
                                    <div class="line-clamp-2">
                                        {{ report.description }}
                                    </div>
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap">
                                    <span
                                        :class="statusClasses(report.status)"
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                    >
                                        <span
                                            class="w-1.5 h-1.5 rounded-full mr-1.5"
                                            :class="{
                                                'bg-gray-500':
                                                    report.status === 'pending',
                                                'bg-blue-500':
                                                    report.status ===
                                                    'in_progress',
                                                'bg-green-500':
                                                    report.status ===
                                                    'resolved',
                                            }"
                                        ></span>
                                        {{ formatStatus(report.status) }}
                                    </span>
                                </td>
                                <td
                                    class="px-3 py-3 whitespace-nowrap text-sm text-gray-600"
                                >
                                    {{ formatDate(report.created_at) }}
                                </td>
                                <td
                                    class="px-3 py-3 whitespace-nowrap text-sm font-medium"
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
                                <td colspan="7" class="px-6 py-8 text-center">
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
                                        You haven't submitted any water quality
                                        reports yet. Click "Submit New Report"
                                        to get started.
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div
                    v-if="reports.data.length > 0"
                    class="px-4 py-3 flex items-center justify-between border-t border-gray-200 bg-gray-50"
                >
                    <div class="text-xs text-gray-600">
                        Showing
                        <span class="font-medium">{{ startIndex + 1 }}</span>
                        to
                        <span class="font-medium">{{ endIndex }}</span>
                        of
                        <span class="font-medium">{{
                            reports.data.length
                        }}</span>
                        results
                    </div>
                    <div class="flex items-center space-x-1">
                        <button
                            @click="prevPage"
                            :disabled="currentPage === 1"
                            class="px-2.5 py-1.5 text-xs font-medium rounded border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Previous
                        </button>
                        <div class="flex space-x-1">
                            <button
                                v-for="page in totalPages"
                                :key="page"
                                @click="currentPage = page"
                                :class="[
                                    'px-3 py-1.5 text-xs font-medium rounded',
                                    currentPage === page
                                        ? 'bg-blue-600 text-white'
                                        : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50',
                                ]"
                            >
                                {{ page }}
                            </button>
                        </div>
                        <button
                            @click="nextPage"
                            :disabled="currentPage === totalPages"
                            class="px-2.5 py-1.5 text-xs font-medium rounded border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Next
                        </button>
                    </div>
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
import { Link, usePage } from "@inertiajs/vue3";
import { ref, computed, watch, onMounted } from "vue";
import Swal from "sweetalert2";
import { useToast } from "vue-toastification";
import CustomerReportModal from "@/Components/Customer/CustomerReportModal.vue";

const toast = useToast();
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
});

// Pagination variables
const currentPage = ref(1);
const itemsPerPage = 10;

// Computed properties for pagination
const totalPages = computed(() => {
    return Math.ceil(props.reports.data.length / itemsPerPage);
});

const startIndex = computed(() => {
    return (currentPage.value - 1) * itemsPerPage;
});

const endIndex = computed(() => {
    return Math.min(startIndex.value + itemsPerPage, props.reports.data.length);
});

const paginatedReports = computed(() => {
    return props.reports.data.slice(startIndex.value, endIndex.value);
});

// Pagination methods
const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
    }
};

const prevPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
    }
};

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

const showAddModal = ref(false);
const showDetailsModal = ref(false);
const showSuccessModal = ref(false);
const selectedReport = ref(null);

const openReportDetails = (report) => {
    selectedReport.value = report;
    showDetailsModal.value = true;
};

const handleReportAdded = (successData) => {
    showAddModal.value = false;
    // Optionally, you can use successData (e.g., trackingCode, dateSubmitted) for further handling
};

const statusClasses = (status) => {
    return {
        "inline-flex items-center px-2 py-1 rounded-full text-xs font-medium": true,
        "bg-blue-50 text-blue-700 border border-blue-100":
            status === "in_progress",
        "bg-green-50 text-green-700 border border-green-100":
            status === "resolved",
        "bg-gray-50 text-gray-700 border border-gray-100": status === "pending",
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
    const options = { year: "numeric", month: "short", day: "numeric" };
    return new Date(dateString).toLocaleDateString("en-US", options);
};
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Improved responsive behavior */
@media (max-width: 767px) {
    .overflow-x-auto {
        -webkit-overflow-scrolling: touch;
    }

    /* Hide table on mobile and show cards instead */
    table {
        display: none;
    }

    .mobile-cards {
        display: block;
    }
}

/* Mobile cards view */
.mobile-cards {
    display: none;
}

@media (max-width: 767px) {
    .mobile-cards {
        display: block;
    }

    .mobile-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 0.75rem;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }
}
</style>
