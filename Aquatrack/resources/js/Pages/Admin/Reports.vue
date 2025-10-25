//Pages/Admin/Reports.vue
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
                                    >{{ reports.total || 0 }}</span
                                >
                                Total Reports
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
                                        placeholder="Search reports, names, issues..."
                                        @keyup.enter="getReports"
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
                                            User Type
                                        </h6>
                                        <div class="space-y-2">
                                            <div
                                                class="flex items-center"
                                                v-for="userTypeOption in userTypeOptions"
                                                :key="userTypeOption.value"
                                            >
                                                <input
                                                    :id="`userType-${userTypeOption.value}`"
                                                    type="radio"
                                                    name="userType"
                                                    :value="
                                                        userTypeOption.value
                                                    "
                                                    :checked="
                                                        filters.userType ===
                                                        userTypeOption.value
                                                    "
                                                    @change="
                                                        updateUserTypeFilter(
                                                            userTypeOption.value
                                                        )
                                                    "
                                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                                />
                                                <label
                                                    :for="`userType-${userTypeOption.value}`"
                                                    class="ml-2 text-sm text-gray-700 dark:text-gray-300"
                                                >
                                                    {{ userTypeOption.label }}
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Quick Stats Section -->
                                        <div
                                            class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600"
                                        >
                                            <h6
                                                class="mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                            >
                                                Quick Stats
                                            </h6>
                                            <div
                                                class="grid grid-cols-2 gap-2 text-xs"
                                            >
                                                <div
                                                    class="flex items-center text-green-600 dark:text-green-400"
                                                >
                                                    <CheckCircle
                                                        class="w-3 h-3 mr-1"
                                                    />
                                                    {{ resolvedReportsCount }}
                                                    Resolved
                                                </div>
                                                <div
                                                    class="flex items-center text-blue-600 dark:text-blue-400"
                                                >
                                                    <RefreshCw
                                                        class="w-3 h-3 mr-1"
                                                    />
                                                    {{ inProgressReportsCount }}
                                                    In Progress
                                                </div>
                                                <div
                                                    class="flex items-center text-yellow-600 dark:text-yellow-400"
                                                >
                                                    <Clock
                                                        class="w-3 h-3 mr-1"
                                                    />
                                                    {{ pendingReportsCount }}
                                                    Pending
                                                </div>
                                                <div
                                                    class="flex items-center text-gray-600 dark:text-gray-400"
                                                >
                                                    <FileText
                                                        class="w-3 h-3 mr-1"
                                                    />
                                                    {{ reports.total }} Total
                                                </div>
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

                            <!-- Export Button -->
                            <button
                                @click="exportReports"
                                class="flex items-center px-4 py-2 text-sm font-medium text-green-700 bg-green-50 border border-green-200 rounded-sm hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800 dark:hover:bg-green-900/30"
                            >
                                <Download class="w-4 h-4 mr-2" />
                                Export
                            </button>
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
                                    Reporter
                                </th>
                                <th
                                    class="px-6 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                >
                                    User Type
                                </th>
                                <th
                                    class="px-6 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                >
                                    Location
                                </th>
                                <th
                                    class="px-6 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                >
                                    Issue Type
                                </th>
                                <th
                                    class="px-6 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                >
                                    Priority
                                </th>
                                <th
                                    class="px-6 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                >
                                    Status
                                </th>
                                <th
                                    class="px-6 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                >
                                    Date
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
                                v-for="report in filteredReports"
                                :key="report.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150"
                                :class="getStatusRowClass(report.status)"
                            >
                                <!-- Reporter Column -->
                                <td class="px-6 py-2">
                                    <div class="flex items-center">
                                        <!-- <div class="flex-shrink-0 h-8 w-8 mr-3">
                                            <div
                                                class="h-8 w-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold text-xs"
                                            >
                                                {{
                                                    getReporterInitials(report)
                                                }}
                                            </div>
                                        </div> -->
                                        <div>
                                            <div
                                                class="font-medium text-gray-900 dark:text-white text-xs"
                                            >
                                                <span
                                                    v-if="
                                                        isMergedReport(report)
                                                    "
                                                >
                                                    <span
                                                        class="cursor-pointer text-blue-600 hover:underline"
                                                        @click="
                                                            showAllReporters(
                                                                report
                                                            )
                                                        "
                                                    >
                                                        {{
                                                            getTruncatedReporters(
                                                                report
                                                            )
                                                        }}
                                                        <span
                                                            v-if="
                                                                hasManyReporters(
                                                                    report
                                                                )
                                                            "
                                                            class="ml-1"
                                                        >
                                                            ...
                                                        </span>
                                                    </span>
                                                </span>
                                                <span v-else>
                                                    {{
                                                        report.reporter_name ||
                                                        report.user?.name ||
                                                        "N/A"
                                                    }}
                                                </span>
                                            </div>
                                            <div
                                                class="text-xs text-gray-500 dark:text-gray-400"
                                            >
                                                {{ report.tracking_code }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- User Type -->
                                <td class="px-6 py-2">
                                    <span
                                        :class="
                                            userTypeClasses(
                                                report.formatted_user_types
                                            )
                                        "
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-sm"
                                    >
                                        <span
                                            class="w-1.5 h-1.5 rounded-full mr-1.5"
                                            :class="
                                                getUserTypeDotClass(
                                                    report.formatted_user_types
                                                )
                                            "
                                        ></span>
                                        {{
                                            report.formatted_user_types ||
                                            "Guest"
                                        }}
                                    </span>
                                </td>

                                <!-- Location -->
                                <td
                                    class="px-6 py-2 text-xs text-gray-900 dark:text-white"
                                >
                                    <div class="font-medium">
                                        {{ report.zone || "N/A" }}
                                    </div>
                                    <div
                                        class="text-gray-500 dark:text-gray-400"
                                    >
                                        {{ report.barangay }},
                                        {{ report.purok }}
                                    </div>
                                </td>

                                <!-- Issue Type -->
                                <td class="px-6 py-2">
                                    <div class="text-xs">
                                        <div
                                            class="font-medium text-gray-900 dark:text-white"
                                        >
                                            {{
                                                report.water_issue_type ===
                                                "others"
                                                    ? report.custom_water_issue ||
                                                      "Custom Issue"
                                                    : report.water_issue_type
                                            }}
                                        </div>
                                        <div
                                            class="text-gray-500 dark:text-gray-400 truncate max-w-xs"
                                        >
                                            {{
                                                truncateText(
                                                    report.description,
                                                    30
                                                )
                                            }}
                                        </div>
                                    </div>
                                </td>

                                <!-- Priority -->
                                <td class="px-6 py-2">
                                    <span
                                        :class="
                                            priorityClasses(report.priority)
                                        "
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-sm"
                                    >
                                        <span
                                            class="w-1.5 h-1.5 rounded-full mr-1.5"
                                            :class="
                                                priorityDotClasses(
                                                    report.priority
                                                )
                                            "
                                        ></span>
                                        {{ report.priority || "N/A" }}
                                    </span>
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-2">
                                    <span
                                        :class="statusClasses(report.status)"
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-sm"
                                    >
                                        <span
                                            class="w-1.5 h-1.5 rounded-full mr-1.5"
                                            :class="
                                                statusDotClasses(report.status)
                                            "
                                        ></span>
                                        {{ formatStatus(report.status) }}
                                    </span>
                                </td>

                                <!-- Date -->
                                <td
                                    class="px-6 py-2 text-xs text-gray-900 dark:text-white"
                                >
                                    <div>
                                        {{ formatDate(report.created_at) }}
                                    </div>
                                    <div
                                        class="text-gray-500 dark:text-gray-400"
                                    >
                                        {{ formatTime(report.created_at) }}
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-3 text-right">
                                    <div class="flex justify-end">
                                        <div class="relative">
                                            <button
                                                @click="
                                                    toggleActionMenu(report.id)
                                                "
                                                class="p-1.5 text-gray-600 hover:text-gray-900 hover:bg-gray-200 dark:hover:text-gray-300 rounded-lg transition-colors"
                                                :data-action-button="report.id"
                                            >
                                                <MoreHorizontal
                                                    class="w-4 h-4"
                                                />
                                            </button>

                                            <!-- Action Menu Dropdown -->
                                            <div
                                                v-if="
                                                    activeActionMenu ===
                                                    report.id
                                                "
                                                class="fixed z-[1000] mt-1 w-48 bg-white dark:bg-gray-700 rounded-lg shadow-lg border border-gray-200 dark:border-gray-600"
                                                :style="
                                                    getActionDropdownStyle(
                                                        report.id
                                                    )
                                                "
                                            >
                                                <div class="py-1">
                                                    <button
                                                        @click="
                                                            openModal(report)
                                                        "
                                                        class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                                    >
                                                        <Eye
                                                            class="w-4 h-4 mr-3"
                                                        />
                                                        View Details
                                                    </button>
                                                    <button
                                                        v-if="props.canEdit"
                                                        @click="
                                                            openStatusModal(
                                                                report
                                                            )
                                                        "
                                                        class="flex items-center w-full px-4 py-2 text-sm text-yellow-600 dark:text-yellow-400 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                                    >
                                                        <Edit
                                                            class="w-4 h-4 mr-3"
                                                        />
                                                        Change Status
                                                    </button>
                                                    <button
                                                        v-if="
                                                            report.status !==
                                                                'resolved' &&
                                                            props.canEdit
                                                        "
                                                        @click="
                                                            quickResolve(report)
                                                        "
                                                        class="flex items-center w-full px-4 py-2 text-sm text-green-600 dark:text-green-400 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                                    >
                                                        <CheckCircle
                                                            class="w-4 h-4 mr-3"
                                                        />
                                                        Mark Resolved
                                                    </button>
                                                    <button
                                                        v-if="props.canDelete"
                                                        @click="
                                                            confirmDelete(
                                                                report
                                                            )
                                                        "
                                                        class="flex items-center w-full px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                                    >
                                                        <Trash2
                                                            class="w-4 h-4 mr-3"
                                                        />
                                                        Delete Report
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <!-- Empty State -->
                            <tr v-if="filteredReports.length === 0">
                                <td colspan="8" class="px-6 py-8 text-center">
                                    <div
                                        class="flex flex-col items-center justify-center space-y-2"
                                    >
                                        <FileText
                                            class="w-12 h-12 text-gray-300"
                                        />
                                        <span
                                            class="font-medium text-gray-500 dark:text-gray-400"
                                            >No reports found</span
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
                <Pagination :data="reports" />
            </div>

            <!-- Modals -->
            <ReportDetailsModal
                :show="showModal"
                :report="selectedReport"
                @close="closeModal"
            />

            <!-- Status Change Modal -->
            <div
                v-if="showStatusModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 transition-opacity duration-300"
                @click="closeStatusModal"
            >
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md mx-4 transform transition-all duration-300 scale-100"
                    @click.stop
                >
                    <!-- Modal Header -->
                    <div
                        class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700"
                    >
                        <h3
                            class="text-lg font-semibold text-gray-900 dark:text-white"
                        >
                            Change Report Status
                        </h3>
                        <button
                            @click="closeStatusModal"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                        >
                            <svg
                                class="w-5 h-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6">
                        <div class="mb-6">
                            <p
                                class="text-sm text-gray-600 dark:text-gray-400 mb-2"
                            >
                                Report Details:
                            </p>
                            <div
                                class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4"
                            >
                                <p
                                    class="text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    {{ statusReport?.tracking_code }} -
                                    {{ statusReport?.reporter_name }}
                                </p>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400 mt-1"
                                >
                                    Current Status:
                                    <span
                                        :class="
                                            statusClasses(statusReport?.status)
                                        "
                                        class="ml-1 px-2 py-1 rounded-full text-xs"
                                    >
                                        {{ formatStatus(statusReport?.status) }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Select New Status:
                            </label>

                            <div class="grid grid-cols-1 gap-3">
                                <label
                                    v-for="status in statusOptions.filter(
                                        (s) => s.value
                                    )"
                                    :key="status.value"
                                    :class="[
                                        'flex items-center p-4 border-2 rounded-lg cursor-pointer transition-all duration-200',
                                        selectedStatus === status.value
                                            ? getStatusBorderClass(status.value)
                                            : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500',
                                    ]"
                                >
                                    <input
                                        type="radio"
                                        :value="status.value"
                                        v-model="selectedStatus"
                                        class="hidden"
                                    />
                                    <div class="flex items-center w-full">
                                        <div
                                            class="w-4 h-4 rounded-full border-2 mr-3 flex items-center justify-center"
                                            :class="
                                                selectedStatus === status.value
                                                    ? getStatusRadioClass(
                                                          status.value
                                                      )
                                                    : 'border-gray-300 dark:border-gray-500'
                                            "
                                        >
                                            <div
                                                v-if="
                                                    selectedStatus ===
                                                    status.value
                                                "
                                                class="w-2 h-2 rounded-full"
                                                :class="
                                                    getStatusDotClass(
                                                        status.value
                                                    )
                                                "
                                            ></div>
                                        </div>
                                        <div class="flex items-center flex-1">
                                            <span
                                                :class="
                                                    statusClasses(status.value)
                                                "
                                                class="px-3 py-1 rounded-full text-sm font-medium mr-3"
                                            >
                                                {{ status.label }}
                                            </span>
                                            <span
                                                class="text-xs text-gray-500 dark:text-gray-400 flex-1"
                                            >
                                                {{
                                                    getStatusDescription(
                                                        status.value
                                                    )
                                                }}
                                            </span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div
                        class="flex justify-end space-x-3 p-6 border-t border-gray-200 dark:border-gray-700"
                    >
                        <button
                            @click="closeStatusModal"
                            :disabled="updatingStatus"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            @click="confirmStatusChange"
                            :disabled="
                                !selectedStatus ||
                                selectedStatus === statusReport?.status ||
                                updatingStatus
                            "
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center"
                        >
                            <span
                                v-if="updatingStatus"
                                class="flex items-center"
                            >
                                <RefreshCw class="w-4 h-4 mr-2 animate-spin" />
                                Updating...
                            </span>
                            <span v-else class="flex items-center">
                                <CheckCircle class="w-4 h-4 mr-2" />
                                Update Status
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Reporter Details Modal -->
            <div
                v-if="showReporterModal"
                class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50"
            >
                <div
                    class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-xl w-full max-w-md"
                >
                    <h3
                        class="text-lg font-semibold text-gray-900 dark:text-white mb-4"
                    >
                        All Reporters
                    </h3>
                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        <div
                            v-for="(reporter, index) in selectedReportReporters"
                            :key="index"
                            class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"
                        >
                            <span
                                class="text-sm font-medium text-gray-900 dark:text-white"
                            >
                                {{ reporter.name }}
                            </span>
                            <span
                                :class="getReporterTypeClasses(reporter.type)"
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                            >
                                {{ reporter.type || "Guest" }}
                            </span>
                        </div>
                    </div>
                    <button
                        @click="closeReporterModal"
                        class="mt-4 w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import ReportDetailsModal from "@/Components/Modals/ReportDetailsModal.vue";
import Pagination from "@/Components/Pagination.vue";
import { ref, computed, watch, onMounted, onUnmounted, nextTick } from "vue";
import { router } from "@inertiajs/vue3";
import { debounce } from "lodash";
import Swal from "sweetalert2";
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
    Trash2,
    Download,
} from "lucide-vue-next";

const props = defineProps({
    reports: Object,
    filters: Object,
    canEdit: Boolean,
    canDelete: Boolean,
    selectedReport: Object,
    swal: Object,
    report_id: [String, Number],
});

// Reactive state
const filters = ref({
    userType: props.filters?.userType || "all",
    status: props.filters?.status || "",
    search: props.filters?.search || "",
});

const showModal = ref(false);
const showFilterDropdown = ref(false);
const isResetting = ref(false);
const activeActionMenu = ref(null);
const filterDropdownStyle = ref({});
const filterButton = ref(null);
const showReporterModal = ref(false);
const selectedReportReporters = ref([]);
const selectedReport = ref(props.selectedReport || null);
const updatingStatus = ref(false);

// Status modal state
const showStatusModal = ref(false);
const statusReport = ref(null);
const selectedStatus = ref("");

// Constants
const statusOptions = [
    { value: "", label: "All Status" },
    { value: "pending", label: "Pending" },
    { value: "in_progress", label: "In Progress" },
    { value: "resolved", label: "Resolved" },
];

const userTypeOptions = [
    { value: "all", label: "All Users" },
    { value: "guest", label: "Guest" },
    { value: "authenticated", label: "Registered" },
];

// Computed properties
const pendingReportsCount = computed(() => {
    return props.reports.data.filter((report) => report.status === "pending")
        .length;
});

const inProgressReportsCount = computed(() => {
    return props.reports.data.filter(
        (report) => report.status === "in_progress"
    ).length;
});

const resolvedReportsCount = computed(() => {
    return props.reports.data.filter((report) => report.status === "resolved")
        .length;
});

const filteredReports = computed(() => {
    return props.reports.data.filter((report) => {
        if (filters.value.userType === "guest" && report.user_id) return false;
        if (filters.value.userType === "authenticated" && !report.user_id)
            return false;
        if (
            filters.value.status &&
            !report.status.startsWith("Deleted") &&
            report.status !== filters.value.status
        )
            return false;

        if (filters.value.search) {
            const searchTerm = filters.value.search.toLowerCase();
            const matchesId = report.id.toString().includes(searchTerm);
            const matchesTrackingCode = (report.tracking_code || "")
                .toLowerCase()
                .includes(searchTerm);
            const matchesName = (
                report.reporter_name ||
                report.user?.name ||
                ""
            )
                .toLowerCase()
                .includes(searchTerm);
            const matchesIssueType = (
                report.water_issue_type === "others"
                    ? report.custom_water_issue || "Custom Issue"
                    : report.water_issue_type || ""
            )
                .toLowerCase()
                .includes(searchTerm);
            if (
                !matchesId &&
                !matchesTrackingCode &&
                !matchesName &&
                !matchesIssueType
            )
                return false;
        }
        return true;
    });
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

const getActionDropdownStyle = (reportId) => {
    if (typeof window === "undefined") return {};
    const button = document.querySelector(`[data-action-button="${reportId}"]`);
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

const toggleActionMenu = async (reportId) => {
    if (activeActionMenu.value === reportId) {
        activeActionMenu.value = null;
    } else {
        activeActionMenu.value = reportId;
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
};

const updateUserTypeFilter = (userType) => {
    filters.value.userType = userType;
    showFilterDropdown.value = false;
};

const resetFilters = () => {
    isResetting.value = true;
    setTimeout(() => {
        filters.value = {
            userType: "all",
            status: "",
            search: "",
        };
        showFilterDropdown.value = false;
        isResetting.value = false;
    }, 500);
};

// Data fetching
const getReports = () => {
    router.get(route("admin.reports"), filters.value, {
        preserveState: true,
        replace: true,
    });
};

// Watch for filter changes
watch(
    filters,
    debounce(() => {
        getReports();
    }, 300),
    { deep: true }
);

// Modal functions
const openModal = (report) => {
    selectedReport.value = report;
    showModal.value = true;
    activeActionMenu.value = null;
};

const closeModal = () => {
    showModal.value = false;
    selectedReport.value = null;
};

// Status Modal Functions
const openStatusModal = (report) => {
    if (report.status.startsWith("Deleted")) {
        Swal.fire({
            icon: "warning",
            title: "Cannot Change Status",
            text: "This report has been deleted and cannot have its status changed.",
        });
        return;
    }

    statusReport.value = report;
    selectedStatus.value = report.status;
    showStatusModal.value = true;
    activeActionMenu.value = null;
};

const closeStatusModal = () => {
    showStatusModal.value = false;
    statusReport.value = null;
    selectedStatus.value = "";
    updatingStatus.value = false;
};

const confirmStatusChange = async () => {
    if (
        !selectedStatus.value ||
        !statusReport.value ||
        selectedStatus.value === statusReport.value.status
    ) {
        return;
    }

    updatingStatus.value = true;
    try {
        await router.post(
            route("admin.reports.updateStatus", statusReport.value.id),
            {
                status: selectedStatus.value,
            },
            {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        toast: true,
                        position: "top-end",
                        icon: "success",
                        title: `Status updated to "${formatStatus(
                            selectedStatus.value
                        )}"`,
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                    });
                    closeStatusModal();
                },
                onError: (error) => {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text:
                            error.message ||
                            "Failed to update status. Please try again.",
                    });
                },
            }
        );
    } catch (error) {
        console.error("Update error:", error);
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "An unexpected error occurred. Please try again.",
        });
    } finally {
        updatingStatus.value = false;
    }
};

// Status modal styling helpers
const getStatusBorderClass = (status) => {
    switch (status) {
        case "pending":
            return "border-gray-400 bg-gray-50 dark:bg-gray-700";
        case "in_progress":
            return "border-blue-400 bg-blue-50 dark:bg-blue-900/20";
        case "resolved":
            return "border-green-400 bg-green-50 dark:bg-green-900/20";
        default:
            return "border-gray-400 bg-gray-50 dark:bg-gray-700";
    }
};

const getStatusRadioClass = (status) => {
    switch (status) {
        case "pending":
            return "border-gray-500";
        case "in_progress":
            return "border-blue-500";
        case "resolved":
            return "border-green-500";
        default:
            return "border-gray-500";
    }
};

const getStatusDotClass = (status) => {
    switch (status) {
        case "pending":
            return "bg-gray-500";
        case "in_progress":
            return "bg-blue-500";
        case "resolved":
            return "bg-green-500";
        default:
            return "bg-gray-500";
    }
};

const getStatusDescription = (status) => {
    switch (status) {
        case "pending":
            return "Report is waiting for review";
        case "in_progress":
            return "Team is working on this issue";
        case "resolved":
            return "Issue has been resolved";
        default:
            return "";
    }
};

// Quick resolve function
const quickResolve = (report) => {
    activeActionMenu.value = null;
    if (report.status !== "resolved") {
        statusReport.value = report;
        selectedStatus.value = "resolved";
        confirmStatusChange();
    }
};

// Reporter Modal Functions
const showAllReporters = (report) => {
    if (isMergedReport(report)) {
        const reporters = report.reporter_name
            .split(",")
            .map((name) => name.trim());
        const userTypes = report.user_types
            ? JSON.parse(report.user_types)
            : [];

        selectedReportReporters.value = reporters.map((reporterName, index) => {
            let reporterType = "Guest";
            if (index === 0 && report.user_id) {
                reporterType = "Registered";
            } else if (userTypes[index]) {
                reporterType = userTypes[index];
            }
            return {
                name: reporterName,
                type: reporterType,
            };
        });

        showReporterModal.value = true;
    }
};

const closeReporterModal = () => {
    showReporterModal.value = false;
    selectedReportReporters.value = [];
};

// Delete report function
const confirmDelete = (report) => {
    activeActionMenu.value = null;

    Swal.fire({
        title: "Are you sure?",
        text: `You are about to delete the report from ${
            report.reporter_name || report.user?.name || "Unknown"
        }. This action cannot be undone.`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            deleteReport(report);
        }
    });
};

const deleteReport = async (report) => {
    try {
        await router.delete(route("admin.reports.destroy", report.id), {
            preserveScroll: true,
            onSuccess: () => {
                Swal.fire({
                    toast: true,
                    position: "top-end",
                    icon: "success",
                    title: "Report deleted successfully",
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                });
            },
            onError: (error) => {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Failed to delete report. Please try again.",
                });
            },
        });
    } catch (error) {
        console.error("Delete error:", error);
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "An unexpected error occurred. Please try again.",
        });
    }
};

// Export function
const exportReports = async () => {
    try {
        const { value: format } = await Swal.fire({
            title: "Export Reports",
            text: "Choose export format",
            icon: "question",
            input: "select",
            inputOptions: {
                csv: "CSV (Excel)",
                json: "JSON Data",
            },
            inputPlaceholder: "Select format",
            showCancelButton: true,
            confirmButtonText: "Export",
            cancelButtonText: "Cancel",
        });

        if (!format) return;

        // Show loading
        Swal.fire({
            title: "Generating Report...",
            text: "Please wait while we prepare your export",
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            },
        });

        // Generate export data
        const exportData = filteredReports.value.map((report) => ({
            ID: report.id,
            "Tracking Code": report.tracking_code,
            "Reporter Name": report.reporter_name || report.user?.name || "N/A",
            "User Type": report.formatted_user_types || "Guest",
            Zone: report.zone || "N/A",
            Barangay: report.barangay || "N/A",
            Purok: report.purok || "N/A",
            "Issue Type":
                report.water_issue_type === "others"
                    ? report.custom_water_issue || "Custom Issue"
                    : report.water_issue_type,
            Priority: report.priority || "N/A",
            Status: formatStatus(report.status),
            "Date Created": formatDate(report.created_at),
            "Time Created": formatTime(report.created_at),
            Description: report.description || "",
            "Reporter Phone": report.reporter_phone || "N/A",
        }));

        // Generate file based on format
        if (format === "csv") {
            exportToCSV(exportData);
        } else if (format === "json") {
            exportToJSON(exportData);
        }

        Swal.fire({
            icon: "success",
            title: "Export Complete!",
            text: `Reports exported as ${format.toUpperCase()} successfully`,
            timer: 2000,
            showConfirmButton: false,
        });
    } catch (error) {
        console.error("Export failed:", error);
        Swal.fire({
            icon: "error",
            title: "Export Failed",
            text: "Failed to generate export. Please try again.",
        });
    }
};

// CSV Export Function
const exportToCSV = (data) => {
    if (data.length === 0) return;

    const headers = Object.keys(data[0]);
    const csvContent = [
        headers.join(","),
        ...data.map((row) =>
            headers
                .map((header) => {
                    const value = row[header] || "";
                    // Escape commas and quotes in CSV
                    return `"${String(value).replace(/"/g, '""')}"`;
                })
                .join(",")
        ),
    ].join("\n");

    const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
    const link = document.createElement("a");
    const url = URL.createObjectURL(blob);
    link.setAttribute("href", url);
    link.setAttribute(
        "download",
        `aquatrack-reports-${new Date().toISOString().split("T")[0]}.csv`
    );
    link.style.visibility = "hidden";
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};

// JSON Export Function
const exportToJSON = (data) => {
    const jsonContent = JSON.stringify(data, null, 2);
    const blob = new Blob([jsonContent], {
        type: "application/json;charset=utf-8;",
    });
    const link = document.createElement("a");
    const url = URL.createObjectURL(blob);
    link.setAttribute("href", url);
    link.setAttribute(
        "download",
        `aquatrack-reports-${new Date().toISOString().split("T")[0]}.json`
    );
    link.style.visibility = "hidden";
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};

// Utility functions
const isMergedReport = (report) => {
    return report.reporter_name && report.reporter_name.includes(",");
};

const hasManyReporters = (report) => {
    return report.reporter_name.split(",").length > 2;
};

const getTruncatedReporters = (report) => {
    const reporters = report.reporter_name.split(",");
    return reporters
        .slice(0, 2)
        .map((name) => name.trim())
        .join(", ");
};

const getReporterInitials = (report) => {
    if (isMergedReport(report)) {
        return "MR"; // Multiple Reporters
    }
    const name = report.reporter_name || report.user?.name || "N/A";
    return name
        .split(" ")
        .map((n) => n[0])
        .join("")
        .toUpperCase()
        .substring(0, 2);
};

const truncateText = (text, length) => {
    if (!text) return "";
    return text.length > length ? text.substring(0, length) + "..." : text;
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

// Styling helpers
const userTypeClasses = (userTypes) => {
    if (
        userTypes &&
        userTypes.includes("Registered") &&
        userTypes.includes("Guest")
    ) {
        return "bg-gradient-to-r from-purple-100 to-gray-100 text-purple-800 dark:from-purple-900 dark:to-gray-900 dark:text-purple-200";
    } else if (userTypes && userTypes.includes("Registered")) {
        return "bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200";
    } else {
        return "bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200";
    }
};

const getUserTypeDotClass = (userTypes) => {
    if (
        userTypes &&
        userTypes.includes("Registered") &&
        userTypes.includes("Guest")
    ) {
        return "bg-purple-400";
    } else if (userTypes && userTypes.includes("Registered")) {
        return "bg-purple-400";
    } else {
        return "bg-gray-400";
    }
};

const priorityClasses = (priority) => {
    return {
        "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200":
            priority === "high",
        "bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200":
            priority === "medium",
        "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200":
            priority === "low",
        "bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200":
            !priority,
    };
};

const priorityDotClasses = (priority) => {
    return {
        "bg-red-400": priority === "high",
        "bg-yellow-400": priority === "medium",
        "bg-green-400": priority === "low",
        "bg-gray-400": !priority,
    };
};

const statusClasses = (status) => {
    return {
        "bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200":
            status === "pending",
        "bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200":
            status === "in_progress",
        "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200":
            status === "resolved",
        "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200":
            status.startsWith("Deleted"),
    };
};

const statusDotClasses = (status) => {
    if (status.startsWith("Deleted")) return "bg-red-400";
    switch (status) {
        case "pending":
            return "bg-gray-400";
        case "in_progress":
            return "bg-blue-400";
        case "resolved":
            return "bg-green-400";
        default:
            return "bg-gray-400";
    }
};

const getStatusRowClass = (status) => {
    return status === "pending"
        ? ""
        : status === "in_progress"
        ? "bg-blue-50 dark:bg-blue-900/20"
        : status === "resolved"
        ? "bg-green-50 dark:bg-green-900/20"
        : status.startsWith("Deleted")
        ? "bg-red-50 dark:bg-red-900/20"
        : "";
};

const getReporterTypeClasses = (type) => {
    switch (type) {
        case "Registered":
            return "bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200";
        case "Guest":
            return "bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200";
        default:
            return "bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200";
    }
};

const formatStatus = (status) => {
    if (status.startsWith("Deleted")) return status;
    const statusMap = {
        pending: "Pending",
        in_progress: "In Progress",
        resolved: "Resolved",
    };
    return statusMap[status] || status;
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

.cursor-pointer {
    cursor: pointer;
}
</style>
