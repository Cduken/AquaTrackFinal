<template>
    <AdminLayout>
        <div class="mx-auto w-full">
            <!-- Floating Reporter Details Modal -->
            <div
                v-if="showReporterModal"
                class="fixed inset-0 z-[9999] pointer-events-none"
            >
                <!-- Click outside to close - invisible overlay -->
                <div
                    class="fixed inset-0 bg-transparent"
                    @click="closeReporterModal"
                ></div>

                <!-- Floating modal content -->
                <div
                    class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-md pointer-events-auto"
                >
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 mx-4"
                    >
                        <!-- Header -->
                        <div
                            class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700"
                        >
                            <h3
                                class="text-lg font-semibold text-gray-900 dark:text-white"
                            >
                                Report Contributors
                            </h3>
                            <button
                                @click="closeReporterModal"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
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
                                    ></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Content -->
                        <div class="max-h-96 overflow-y-auto p-4">
                            <div class="space-y-3">
                                <div
                                    v-for="(
                                        reporter, index
                                    ) in selectedReportReporters"
                                    :key="index"
                                    class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"
                                >
                                    <div class="flex items-center space-x-3">
                                        <!-- Avatar -->
                                        <div class="flex-shrink-0">
                                            <!-- Show user avatar for registered users -->
                                            <img
                                                v-if="
                                                    reporter.avatar &&
                                                    reporter.isRegistered
                                                "
                                                :src="reporter.avatar"
                                                :alt="reporter.name"
                                                class="h-8 w-8 rounded-full object-cover"
                                            />
                                            <!-- Show colored initial for guests or users without avatar -->
                                            <div
                                                v-else
                                                class="h-8 w-8 rounded-full flex items-center justify-center text-white font-semibold text-xs"
                                                :class="
                                                    getReporterAvatarColorForModal(
                                                        reporter.name
                                                    )
                                                "
                                            >
                                                {{
                                                    getReporterInitialsForModal(
                                                        reporter.name
                                                    )
                                                }}
                                            </div>
                                        </div>

                                        <!-- Name and Type -->
                                        <div>
                                            <div
                                                class="font-medium text-gray-900 dark:text-white text-sm"
                                            >
                                                {{ reporter.name }}
                                            </div>
                                            <div
                                                class="flex items-center space-x-2 mt-1"
                                            >
                                                <span
                                                    :class="
                                                        getReporterTypeClasses(
                                                            reporter.type
                                                        )
                                                    "
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                                >
                                                    <span
                                                        class="w-1.5 h-1.5 rounded-full mr-1.5"
                                                        :class="
                                                            reporter.type ===
                                                            'Registered'
                                                                ? 'bg-purple-400'
                                                                : 'bg-gray-400'
                                                        "
                                                    ></span>
                                                    {{ reporter.type }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Badge for main reporter -->
                                    <span
                                        v-if="
                                            index === 0 &&
                                            selectedReportReporters.length > 1
                                        "
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200"
                                    >
                                        Initial Reporter
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden"
            >
                <!-- Search and Filter Section -->
                <div class="p-2 border-b border-gray-200 dark:border-gray-700">
                    <div
                        class="flex flex-col md:flex-row md:items-center justify-between gap-4"
                    >
                        <div class="flex items-center px-2 space-x-4">
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
                                            <!-- Profile Avatar -->
                                            <div
                                                class="flex-shrink-0 h-8 w-8 mr-3"
                                            >
                                                <!-- For merged reports (multiple reporters) - Show stacked avatars -->
                                                <span
                                                    v-if="
                                                        isMergedReport(report)
                                                    "
                                                >
                                                    <div
                                                        class="relative h-8 w-8"
                                                    >
                                                        <!-- First reporter avatar -->
                                                        <div
                                                            class="absolute top-0 left-0 z-10"
                                                        >
                                                            <img
                                                                v-if="
                                                                    report.user &&
                                                                    report.user
                                                                        .avatar_url
                                                                "
                                                                :src="
                                                                    report.user
                                                                        .avatar_url
                                                                "
                                                                :alt="
                                                                    getFirstReporterName(
                                                                        report
                                                                    )
                                                                "
                                                                class="h-6 w-6 rounded-full object-cover border-2 border-white dark:border-gray-800"
                                                            />
                                                            <div
                                                                v-else
                                                                class="h-6 w-6 rounded-full flex items-center justify-center text-white font-semibold text-xs border-2 border-white dark:border-gray-800"
                                                                :class="
                                                                    getReporterAvatarColor(
                                                                        report
                                                                    )
                                                                "
                                                            >
                                                                {{
                                                                    getFirstReporterInitials(
                                                                        report
                                                                    )
                                                                }}
                                                            </div>
                                                        </div>
                                                        <!-- Second reporter avatar (overlapping) -->
                                                        <div
                                                            class="absolute bottom-0 right-0 z-0"
                                                        >
                                                            <div
                                                                class="h-6 w-6 rounded-full flex items-center justify-center text-white font-semibold text-xs bg-gradient-to-br from-purple-500 to-pink-600 border-2 border-white dark:border-gray-800"
                                                            >
                                                                +{{
                                                                    getAdditionalReportersCount(
                                                                        report
                                                                    )
                                                                }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </span>

                                                <!-- For single reporter with user avatar -->
                                                <span
                                                    v-else-if="
                                                        report.user &&
                                                        report.user.avatar_url
                                                    "
                                                >
                                                    <img
                                                        :src="
                                                            report.user
                                                                .avatar_url
                                                        "
                                                        :alt="report.user.name"
                                                        class="h-8 w-8 rounded-full object-cover"
                                                    />
                                                </span>

                                                <!-- For single reporter without avatar (guest or no avatar) -->
                                                <span v-else>
                                                    <div
                                                        class="h-8 w-8 rounded-full flex items-center justify-center text-white font-semibold text-xs"
                                                        :class="
                                                            getReporterAvatarColor(
                                                                report
                                                            )
                                                        "
                                                    >
                                                        {{
                                                            getReporterInitials(
                                                                report
                                                            )
                                                        }}
                                                    </div>
                                                </span>
                                            </div>

                                            <div>
                                                <div
                                                    class="font-medium text-gray-900 dark:text-white text-xs"
                                                >
                                                    <span
                                                        v-if="
                                                            isMergedReport(
                                                                report
                                                            )
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
                                                                getFirstTwoReporters(
                                                                    report
                                                                )
                                                            }}
                                                            <span
                                                                v-if="
                                                                    getAdditionalReportersCount(
                                                                        report
                                                                    ) > 0
                                                                "
                                                                class="text-gray-600 dark:text-gray-400"
                                                            >
                                                                (+{{
                                                                    getAdditionalReportersCount(
                                                                        report
                                                                    )
                                                                }})
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
                                                    getDisplayUserType(report)
                                                )
                                            "
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-sm"
                                        >
                                            <span
                                                class="w-1.5 h-1.5 rounded-full mr-1.5"
                                                :class="
                                                    getUserTypeDotClass(
                                                        getDisplayUserType(
                                                            report
                                                        )
                                                    )
                                                "
                                            ></span>
                                            {{ getDisplayUserType(report) }}
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
                                            :class="
                                                statusClasses(report.status)
                                            "
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-sm"
                                        >
                                            <span
                                                class="w-1.5 h-1.5 rounded-full mr-1.5"
                                                :class="
                                                    statusDotClasses(
                                                        report.status
                                                    )
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
                                                        toggleActionMenu(
                                                            report.id
                                                        )
                                                    "
                                                    class="p-1.5 text-gray-600 hover:text-gray-900 hover:bg-gray-200 dark:hover:text-gray-300 rounded-lg transition-colors"
                                                    :data-action-button="
                                                        report.id
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
                                                        <Link
                                                            :href="
                                                                route(
                                                                    'admin.reports.show',
                                                                    report.id
                                                                )
                                                            "
                                                            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                                        >
                                                            <Eye
                                                                class="w-4 h-4 mr-3"
                                                            />
                                                            View Details
                                                        </Link>
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
                                                                props.canDelete
                                                            "
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
                                    <td
                                        colspan="8"
                                        class="px-6 py-24 text-center"
                                    >
                                        <div
                                            class="flex flex-col items-center justify-center space-y-4"
                                        >
                                            <FileText
                                                class="w-20 h-20 text-gray-300"
                                            />
                                            <span
                                                class="text-2xl font-medium text-gray-500 dark:text-gray-400"
                                            >
                                                No reports found
                                            </span>
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
                        <Pagination :data="reports" />
                    </div>
                </div>
            </div>

            <!-- Modals -->
            <ReportDetailsModal
                :show="showModal"
                :report="selectedReport"
                @close="closeModal"
            />

            <StatusChangeModal
                :show="showStatusModal"
                :report="statusReport"
                :updating="updatingStatus"
                @close="closeStatusModal"
                @confirm="confirmStatusChange"
            />
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import ReportDetailsModal from "@/Components/Modals/ReportDetailsModal.vue";
import StatusChangeModal from "@/Components/Admin/Reports/StatusChangeModal.vue";
import Pagination from "@/Components/Pagination.vue";
import { ref, computed, watch, onMounted, onUnmounted, nextTick } from "vue";
import { Link, router } from "@inertiajs/vue3";
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

// Add this method to your methods section
const getFirstTwoReporters = (report) => {
    if (!isMergedReport(report)) return report.reporter_name;

    const reporters = report.reporter_name.split(",").map((r) => r.trim());
    return reporters.slice(0, 2).join(", ");
};

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
    showStatusModal.value = true;
    activeActionMenu.value = null;
};

const closeStatusModal = () => {
    showStatusModal.value = false;
    statusReport.value = null;
    updatingStatus.value = false;
};

const confirmStatusChange = async (newStatus) => {
    if (
        !newStatus ||
        !statusReport.value ||
        newStatus === statusReport.value.status
    ) {
        return;
    }

    updatingStatus.value = true;
    try {
        await router.post(
            route("admin.reports.updateStatus", statusReport.value.id),
            {
                status: newStatus,
            },
            {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        toast: true,
                        position: "top-end",
                        icon: "success",
                        title: `Status updated to "${formatStatus(newStatus)}"`,
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

// Quick resolve function
const quickResolve = (report) => {
    activeActionMenu.value = null;
    if (report.status !== "resolved") {
        statusReport.value = report;
        confirmStatusChange("resolved");
    }
};

// Avatar colors for consistent coloring
const avatarColors = [
    "bg-gradient-to-br from-blue-500 to-blue-600",
    "bg-gradient-to-br from-green-500 to-green-600",
    "bg-gradient-to-br from-purple-500 to-purple-600",
    "bg-gradient-to-br from-pink-500 to-pink-600",
    "bg-gradient-to-br from-indigo-500 to-indigo-600",
    "bg-gradient-to-br from-yellow-500 to-yellow-600",
    "bg-gradient-to-br from-red-500 to-red-600",
    "bg-gradient-to-br from-teal-500 to-teal-600",
    "bg-gradient-to-br from-orange-500 to-orange-600",
];

// Get avatar color based on reporter name
const getReporterAvatarColor = (report) => {
    const name = report.reporter_name || report.user?.name || "Guest";

    // Simple hash function to get consistent color for same name
    let hash = 0;
    for (let i = 0; i < name.length; i++) {
        hash = name.charCodeAt(i) + ((hash << 5) - hash);
    }

    const index = Math.abs(hash) % avatarColors.length;
    return avatarColors[index];
};

// Get initials for single reporter
const getReporterInitials = (report) => {
    if (isMergedReport(report)) {
        return "MR"; // Multiple Reporters
    }

    const name = report.reporter_name || report.user?.name || "N/A";
    if (name === "N/A") return "N/A";

    const names = name.split(" ").filter((n) => n.trim() !== "");
    if (names.length === 0) return "N/A";
    if (names.length === 1) return names[0].charAt(0).toUpperCase();

    return (
        names[0].charAt(0) + names[names.length - 1].charAt(0)
    ).toUpperCase();
};

// Reporter methods
// Add these new helper methods for merged avatars
const getFirstReporterName = (report) => {
    if (!isMergedReport(report))
        return report.reporter_name || report.user?.name || "N/A";
    const reporters = report.reporter_name.split(",").map((r) => r.trim());
    return reporters[0] || "N/A";
};

const getFirstReporterInitials = (report) => {
    const name = getFirstReporterName(report);
    if (name === "N/A") return "N/A";
    const names = name.split(" ").filter((n) => n.trim() !== "");
    if (names.length === 0) return "N/A";
    if (names.length === 1) return names[0].charAt(0).toUpperCase();
    return (
        names[0].charAt(0) + names[names.length - 1].charAt(0)
    ).toUpperCase();
};

// Update this method to count reporters beyond the first 2
const getAdditionalReportersCount = (report) => {
    if (!isMergedReport(report)) return 0;
    const reporters = report.reporter_name.split(",").map((r) => r.trim());
    return Math.max(0, reporters.length - 2); // Count reporters beyond first 2
};

// Update the existing isMergedReport function to be more accurate
const isMergedReport = (report) => {
    return (
        report.reporter_name &&
        report.reporter_name.includes(",") &&
        report.reporter_name.split(",").length > 1
    );
};

const getTruncatedReporters = (report) => {
    if (!isMergedReport(report)) return report.reporter_name;

    const reporters = report.reporter_name.split(",").map((r) => r.trim());
    return reporters.slice(0, 2).join(", ");
};

const hasManyReporters = (report) => {
    if (!isMergedReport(report)) return false;
    const reporters = report.reporter_name.split(",").map((r) => r.trim());
    return reporters.length > 2;
};

const showAllReporters = async (report) => {
    if (isMergedReport(report)) {
        const reporters = report.reporter_name
            .split(",")
            .map((name) => name.trim())
            .filter((name) => name);

        // Parse user types from the report - FIXED for accuracy
        const userTypes = report.user_types
            ? JSON.parse(report.user_types)
            : [];

        // If user_types is missing or incomplete, build it
        const accurateUserTypes = [];
        for (let i = 0; i < reporters.length; i++) {
            if (userTypes[i]) {
                accurateUserTypes.push(userTypes[i]);
            } else {
                // Fallback: check if reporter is registered
                const isRegistered = await checkReporterRegistration(
                    reporters[i]
                );
                accurateUserTypes.push(isRegistered ? "Registered" : "Guest");
            }
        }

        selectedReportReporters.value = reporters.map((reporterName, index) => {
            const reporterType = accurateUserTypes[index] || "Guest";

            return {
                name: reporterName,
                type: reporterType,
                avatar: null, // Will be fetched if needed
                isRegistered: reporterType === "Registered",
                index: index,
            };
        });

        showReporterModal.value = true;
    }
};

// Helper function to check reporter registration
const checkReporterRegistration = async (reporterName) => {
    try {
        const response = await axios.post(
            route("admin.reports.getReportersData"),
            {
                reporters: [reporterName],
            }
        );
        return response.data.reporters[0]?.isRegistered || false;
    } catch (error) {
        console.error("Failed to check reporter registration:", error);
        return false;
    }
};

const closeReporterModal = () => {
    showReporterModal.value = false;
    selectedReportReporters.value = [];
};

// Add these new helper methods for the modal
const getReporterAvatarColorForModal = (name) => {
    // Simple hash function to get consistent color for same name
    let hash = 0;
    for (let i = 0; i < name.length; i++) {
        hash = name.charCodeAt(i) + ((hash << 5) - hash);
    }
    const index = Math.abs(hash) % avatarColors.length;
    return avatarColors[index];
};

const getReporterInitialsForModal = (name) => {
    if (name === "N/A") return "N/A";
    const names = name.split(" ").filter((n) => n.trim() !== "");
    if (names.length === 0) return "N/A";
    if (names.length === 1) return names[0].charAt(0).toUpperCase();
    return (
        names[0].charAt(0) + names[names.length - 1].charAt(0)
    ).toUpperCase();
};

// Delete report function
const confirmDelete = async (report) => {
    activeActionMenu.value = null;

    // First confirmation
    const confirmResult = await Swal.fire({
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
    });

    if (!confirmResult.isConfirmed) return;

    // Reason prompt
    const { value: reason } = await Swal.fire({
        title: "Reason for Deletion",
        input: "text",
        inputLabel: "Please provide a reason for deleting this report",
        inputPlaceholder: "Enter the reason...",
        showCancelButton: true,
        confirmButtonText: "Confirm Delete",
        cancelButtonText: "Cancel",
        inputValidator: (value) => {
            if (!value) {
                return "Please provide a reason for deletion!";
            }
        },
    });

    if (reason) {
        deleteReport(report, reason);
    }
};

// Update deleteReport to accept reason parameter
const deleteReport = async (report, reason) => {
    try {
        await router.delete(route("admin.reports.destroy", report.id), {
            data: { reason: reason },
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
                console.error("Delete error:", error);
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text:
                        error.message ||
                        "Failed to delete report. Please try again.",
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

// Export function - UPDATED WITH PDF OPTION
const exportReports = async () => {
    try {
        const { value: format } = await Swal.fire({
            title: "Export Reports",
            text: "Choose export format",
            icon: "question",
            input: "select",
            inputOptions: {
                csv: "CSV (Excel)",
                pdf: "PDF Document",
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

        if (format === "csv") {
            // Generate export data for CSV
            const exportData = filteredReports.value.map((report) => ({
                ID: report.id,
                "Tracking Code": report.tracking_code,
                "Reporter Name":
                    report.reporter_name || report.user?.name || "N/A",
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

            exportToCSV(exportData);
        } else if (format === "pdf") {
            // Export to PDF using server-side generation
            await exportToPDF();
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

// PDF Export Function - Server-side generation
const exportToPDF = async () => {
    try {
        // Build query parameters from current filters
        const params = new URLSearchParams();

        if (filters.value.search) params.append("search", filters.value.search);
        if (filters.value.status) params.append("status", filters.value.status);
        if (filters.value.userType && filters.value.userType !== "all") {
            params.append("userType", filters.value.userType);
        }

        // Add timestamp to prevent caching
        params.append("_t", new Date().getTime());

        // Create a temporary link to trigger the download
        const link = document.createElement("a");
        link.href = route("admin.reports.export.pdf") + "?" + params.toString();
        link.setAttribute("download", "");
        link.style.display = "none";

        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    } catch (error) {
        console.error("PDF export error:", error);
        throw new Error("Failed to generate PDF");
    }
};

// Utility functions
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

// Enhanced styling helpers for Hybrid type
const userTypeClasses = (userType) => {
    if (userType === "Hybrid") {
        return "bg-gradient-to-r from-blue-100 to-cyan-100 text-blue-800 dark:from-blue-900 dark:to-cyan-900 dark:text-blue-200 border border-blue-200 dark:border-blue-700";
    } else if (userType === "Registered") {
        return "bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200";
    } else {
        return "bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200";
    }
};

const getUserTypeDotClass = (userType) => {
    if (userType === "Hybrid") {
        return "bg-gradient-to-r from-blue-400 to-cyan-400";
    } else if (userType === "Registered") {
        return "bg-purple-400";
    } else {
        return "bg-gray-400";
    }
};

const getDisplayUserType = (report) => {
    // Always use backend formatted_user_types if available and valid
    if (
        report.formatted_user_types &&
        report.formatted_user_types !== "Guest"
    ) {
        return report.formatted_user_types;
    }

    // Fallback to client-side detection only if needed
    if (isMergedReport(report)) {
        const hasRegistered = report.user_id !== null;
        const hasMultipleReporters =
            report.reporter_name && report.reporter_name.split(",").length > 1;

        if (hasRegistered && hasMultipleReporters) {
            return "Hybrid";
        }
    }

    return report.formatted_user_types || "Guest";
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
            status?.startsWith("Deleted"),
    };
};

const statusDotClasses = (status) => {
    if (status?.startsWith("Deleted")) return "bg-red-400";
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
        : status?.startsWith("Deleted")
        ? "bg-red-50 dark:bg-red-900/20"
        : "";
};

const getReporterTypeClasses = (type) => {
    switch (type) {
        case "Registered":
            return "bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200";
        case "Guest":
            return "bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200";
        case "Hybrid":
            return "bg-gradient-to-r from-blue-100 to-cyan-100 text-blue-800 dark:from-blue-900 dark:to-cyan-900 dark:text-blue-200";
        default:
            return "bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200";
    }
};

const formatStatus = (status) => {
    if (status?.startsWith("Deleted")) return status;
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

/* Modal animations */
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-content-enter-active,
.modal-content-leave-active {
    transition: all 0.3s ease;
}

.modal-content-enter-from,
.modal-content-leave-to {
    opacity: 0;
    transform: scale(0.95);
}

.cursor-pointer {
    cursor: pointer;
}

/* Hover effect for reporter names */
.reporter-link:hover {
    text-decoration: underline;
    color: #2563eb; /* blue-600 */
}

.dark .reporter-link:hover {
    color: #60a5fa; /* blue-400 */
}

/* Custom scrollbar styling */
.scrollbar-custom {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 transparent;
}

.scrollbar-custom::-webkit-scrollbar {
    width: 6px;
}

.scrollbar-custom::-webkit-scrollbar-track {
    background: transparent;
    border-radius: 3px;
}

.scrollbar-custom::-webkit-scrollbar-thumb {
    background-color: #cbd5e0;
    border-radius: 3px;
}

.dark .scrollbar-custom::-webkit-scrollbar-thumb {
    background-color: #4b5563;
}

.scrollbar-custom::-webkit-scrollbar-thumb:hover {
    background-color: #a0aec0;
}

.dark .scrollbar-custom::-webkit-scrollbar-thumb:hover {
    background-color: #6b7280;
}
</style>
