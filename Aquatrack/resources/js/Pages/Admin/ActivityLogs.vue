//Pages/Admin/ActivityLogs.vue
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
                            <h5
                                class="text-sm font-semibold text-gray-500 dark:text-gray-400"
                            >
                                <span
                                    class="font-bold text-black dark:text-white"
                                    >{{ totalActivities }}</span
                                >
                                Total Activities
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
                                        v-model="searchTerm"
                                        type="text"
                                        class="block w-full md:w-auto pl-10 text-sm text-gray-900 border border-gray-300 rounded-sm bg-white focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="Search activities, users..."
                                        @keyup.enter="applyFilters"
                                    />
                                </div>
                            </div>

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

                                <div
                                    v-if="showFilterDropdown"
                                    class="fixed z-[1000] mt-1 w-64 bg-white rounded-lg shadow-lg border border-gray-200 dark:bg-gray-700 dark:border-gray-600"
                                    :style="filterDropdownStyle"
                                    @click.stop
                                >
                                    <div class="p-4">
                                        <h6
                                            class="mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                        >
                                            Event Type
                                        </h6>
                                        <div
                                            class="space-y-2 max-h-32 overflow-y-auto"
                                        >
                                            <div
                                                class="flex items-center"
                                                v-for="(
                                                    label, value
                                                ) in eventTypes"
                                                :key="value"
                                            >
                                                <input
                                                    :id="`event-${value}`"
                                                    type="radio"
                                                    name="event"
                                                    :value="value"
                                                    :checked="
                                                        eventType === value
                                                    "
                                                    @change="
                                                        updateEventType(value)
                                                    "
                                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                                />
                                                <label
                                                    :for="`event-${value}`"
                                                    class="ml-2 text-sm text-gray-700 dark:text-gray-300 truncate"
                                                >
                                                    {{ label }}
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
                                                    class="text-xs text-gray-500 dark:text-gray-400"
                                                    >From</label
                                                >
                                                <input
                                                    v-model="dateFrom"
                                                    type="date"
                                                    class="w-full p-2 text-xs text-gray-900 border border-gray-300 rounded bg-white focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    class="text-xs text-gray-500 dark:text-gray-400"
                                                    >To</label
                                                >
                                                <input
                                                    v-model="dateTo"
                                                    type="date"
                                                    class="w-full p-2 text-xs text-gray-900 border border-gray-300 rounded bg-white focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                                                />
                                            </div>
                                        </div>

                                        <!-- Quick Stats Section -->
                                        <div
                                            class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600"
                                        >
                                            <h6
                                                class="mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                            >
                                                Activity Stats
                                            </h6>
                                            <div
                                                class="grid grid-cols-2 gap-2 text-xs"
                                            >
                                                <div
                                                    class="flex items-center text-blue-600 dark:text-blue-400"
                                                >
                                                    <Activity
                                                        class="w-3 h-3 mr-1"
                                                    />
                                                    {{ totalActivities }} Total
                                                </div>
                                                <div
                                                    class="flex items-center text-green-600 dark:text-green-400"
                                                >
                                                    <PlusCircle
                                                        class="w-3 h-3 mr-1"
                                                    />
                                                    {{
                                                        createdEventsCount
                                                    }}
                                                    Created
                                                </div>
                                                <div
                                                    class="flex items-center text-yellow-600 dark:text-yellow-400"
                                                >
                                                    <Edit
                                                        class="w-3 h-3 mr-1"
                                                    />
                                                    {{
                                                        updatedEventsCount
                                                    }}
                                                    Updated
                                                </div>
                                                <div
                                                    class="flex items-center text-purple-600 dark:text-purple-400"
                                                >
                                                    <Users
                                                        class="w-3 h-3 mr-1"
                                                    />
                                                    {{
                                                        userActivitiesCount
                                                    }}
                                                    User
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="flex items-center justify-between p-4 border-t border-gray-200 dark:border-gray-600"
                                    >
                                        <button
                                            @click="clearFilters"
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

                            <!-- View Toggle -->
                            <div
                                class="flex items-center space-x-1 border border-gray-300 dark:border-gray-600 rounded-sm p-1"
                            >
                                <button
                                    @click="viewMode = 'grid'"
                                    :class="[
                                        'p-1.5 rounded transition-colors',
                                        viewMode === 'grid'
                                            ? 'bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400'
                                            : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300',
                                    ]"
                                >
                                    <LayoutGrid class="w-4 h-4" />
                                </button>
                                <button
                                    @click="viewMode = 'list'"
                                    :class="[
                                        'p-1.5 rounded transition-colors',
                                        viewMode === 'list'
                                            ? 'bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400'
                                            : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300',
                                    ]"
                                >
                                    <List class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activity Timeline -->
                <div class="p-4">
                    <!-- Grid View -->
                    <div
                        v-if="viewMode === 'grid'"
                        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"
                    >
                        <div
                            v-for="activity in activities.data"
                            :key="activity.id"
                            class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-all duration-200 cursor-pointer transform hover:-translate-y-1"
                            @click="showActivityDetails(activity)"
                        >
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center space-x-2">
                                    <div
                                        class="p-2 rounded-lg"
                                        :class="getActivityBgColor(activity)"
                                    >
                                        <component
                                            :is="getActivityIcon(activity)"
                                            class="w-4 h-4"
                                            :class="
                                                getActivityIconColor(activity)
                                            "
                                        />
                                    </div>
                                    <span
                                        class="text-xs font-medium px-2 py-1 rounded-full capitalize"
                                        :class="getTypeBadgeClass(activity)"
                                    >
                                        {{ formatEventType(activity.event) }}
                                    </span>
                                </div>
                                <span
                                    class="text-xs text-gray-500 dark:text-gray-400"
                                >
                                    {{ formatTimeAgo(activity.created_at) }}
                                </span>
                            </div>

                            <!-- Activity Content -->
                            <div class="mb-3">
                                <h3
                                    class="text-sm font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2"
                                >
                                    {{ getActivityTitle(activity) }}
                                </h3>
                                <p
                                    class="text-xs text-gray-600 dark:text-gray-300 line-clamp-3"
                                >
                                    {{ activity.description }}
                                    <span
                                        v-if="
                                            activity.properties?.tracking_code
                                        "
                                        class="font-medium text-blue-600 dark:text-blue-400 ml-1"
                                    >
                                        ({{
                                            activity.properties.tracking_code
                                        }})
                                    </span>
                                </p>
                            </div>

                            <!-- Activity Metadata -->
                            <div
                                class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400"
                            >
                                <div class="flex items-center space-x-2">
                                    <User class="w-3 h-3" />
                                    <span>
                                        {{ activity.causer_name || "System" }}
                                    </span>
                                </div>
                                <div
                                    class="flex items-center space-x-2"
                                    v-if="activity.properties?.old_status"
                                >
                                    <ArrowRight class="w-3 h-3" />
                                    <span class="text-xs">
                                        {{ activity.properties.old_status }} →
                                        {{ activity.properties.new_status }}
                                    </span>
                                </div>
                            </div>

                            <!-- Additional Info -->
                            <div
                                class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-600"
                            >
                                <div
                                    class="flex items-center justify-between text-xs"
                                >
                                    <span
                                        class="text-gray-500 dark:text-gray-400"
                                    >
                                        {{ formatDate(activity.created_at) }}
                                    </span>
                                    <span
                                        class="text-gray-400 dark:text-gray-500"
                                    >
                                        ID: {{ activity.id }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- List View -->
                    <div v-else class="space-y-3">
                        <div
                            v-for="activity in activities.data"
                            :key="activity.id"
                            class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-all duration-200"
                        >
                            <div class="flex items-start space-x-4">
                                <!-- Timeline Icon -->
                                <div class="flex flex-col items-center">
                                    <div
                                        class="p-2 rounded-lg"
                                        :class="getActivityBgColor(activity)"
                                    >
                                        <component
                                            :is="getActivityIcon(activity)"
                                            class="w-4 h-4"
                                            :class="
                                                getActivityIconColor(activity)
                                            "
                                        />
                                    </div>
                                    <div
                                        class="w-0.5 h-full bg-gray-200 dark:bg-gray-600 mt-2"
                                    ></div>
                                </div>

                                <!-- Activity Content -->
                                <div class="flex-1 min-w-0">
                                    <div
                                        class="flex items-center justify-between mb-2"
                                    >
                                        <h3
                                            class="text-sm font-semibold text-gray-900 dark:text-white"
                                        >
                                            {{ getActivityTitle(activity) }}
                                        </h3>
                                        <span
                                            class="text-xs text-gray-500 dark:text-gray-400"
                                        >
                                            {{
                                                formatTimeAgo(
                                                    activity.created_at
                                                )
                                            }}
                                        </span>
                                    </div>

                                    <p
                                        class="text-sm text-gray-600 dark:text-gray-300 mb-2"
                                    >
                                        {{ activity.description }}
                                        <span
                                            v-if="
                                                activity.properties
                                                    ?.tracking_code
                                            "
                                            class="font-medium text-blue-600 dark:text-blue-400 ml-1"
                                        >
                                            ({{
                                                activity.properties
                                                    .tracking_code
                                            }})
                                        </span>
                                    </p>

                                    <div
                                        class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400"
                                    >
                                        <div
                                            class="flex items-center space-x-4"
                                        >
                                            <div
                                                class="flex items-center space-x-1"
                                            >
                                                <User class="w-3 h-3" />
                                                <span>{{
                                                    activity.causer_name ||
                                                    "System"
                                                }}</span>
                                            </div>
                                            <div
                                                class="flex items-center space-x-1"
                                                v-if="
                                                    activity.properties
                                                        ?.old_status
                                                "
                                            >
                                                <ArrowRight class="w-3 h-3" />
                                                <span>
                                                    {{
                                                        activity.properties
                                                            .old_status
                                                    }}
                                                    →
                                                    {{
                                                        activity.properties
                                                            .new_status
                                                    }}
                                                </span>
                                            </div>
                                        </div>
                                        <span>{{
                                            formatDate(activity.created_at)
                                        }}</span>
                                    </div>
                                </div>

                                <!-- Quick Actions -->
                                <div class="flex items-center space-x-2">
                                    <button
                                        @click="showActivityDetails(activity)"
                                        class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded transition-colors"
                                        title="View Details"
                                    >
                                        <Eye class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div
                        v-if="activities.data.length === 0"
                        class="col-span-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-8 text-center"
                    >
                        <div
                            class="flex flex-col items-center justify-center space-y-3"
                        >
                            <div
                                class="p-4 bg-gray-100 dark:bg-gray-700 rounded-full"
                            >
                                <Activity
                                    class="w-8 h-8 text-gray-400 dark:text-gray-500"
                                />
                            </div>
                            <h3
                                class="text-lg font-semibold text-gray-500 dark:text-gray-400"
                            >
                                No activities found
                            </h3>
                            <p
                                class="text-sm text-gray-400 dark:text-gray-500 max-w-md"
                            >
                                {{
                                    searchTerm ||
                                    eventType !== "all" ||
                                    dateFrom ||
                                    dateTo
                                        ? "Try adjusting your filters or search keywords."
                                        : "No activities recorded yet."
                                }}
                            </p>
                            <button
                                v-if="
                                    searchTerm ||
                                    eventType !== 'all' ||
                                    dateFrom ||
                                    dateTo
                                "
                                @click="clearFilters"
                                class="mt-2 px-4 py-2 text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                            >
                                Clear filters
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <Pagination :data="activities" />

                <!-- Activity Details Modal -->
                <div
                    v-if="showDetailsModal"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 transition-opacity duration-300"
                    @click="closeDetailsModal"
                >
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-2xl mx-4 transform transition-all duration-300 scale-100"
                        @click.stop
                    >
                        <!-- Modal Header -->
                        <div
                            class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700"
                        >
                            <h3
                                class="text-lg font-semibold text-gray-900 dark:text-white"
                            >
                                Activity Details
                            </h3>
                            <button
                                @click="closeDetailsModal"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                            >
                                <X class="w-5 h-5" />
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <div
                            class="p-6 max-h-96 overflow-y-auto"
                            v-if="selectedActivity"
                        >
                            <div class="space-y-4">
                                <!-- Basic Info -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label
                                            class="text-xs text-gray-500 dark:text-gray-400 font-medium"
                                            >Event Type</label
                                        >
                                        <p
                                            class="text-sm font-medium text-gray-900 dark:text-white mt-1"
                                        >
                                            {{
                                                formatEventType(
                                                    selectedActivity.event
                                                )
                                            }}
                                        </p>
                                    </div>
                                    <div>
                                        <label
                                            class="text-xs text-gray-500 dark:text-gray-400 font-medium"
                                            >Activity ID</label
                                        >
                                        <p
                                            class="text-sm font-medium text-gray-900 dark:text-white mt-1"
                                        >
                                            {{ selectedActivity.id }}
                                        </p>
                                    </div>
                                    <div>
                                        <label
                                            class="text-xs text-gray-500 dark:text-gray-400 font-medium"
                                            >Performed By</label
                                        >
                                        <p
                                            class="text-sm font-medium text-gray-900 dark:text-white mt-1"
                                        >
                                            {{
                                                selectedActivity.causer_name ||
                                                "System"
                                            }}
                                        </p>
                                    </div>
                                    <div>
                                        <label
                                            class="text-xs text-gray-500 dark:text-gray-400 font-medium"
                                            >Timestamp</label
                                        >
                                        <p
                                            class="text-sm font-medium text-gray-900 dark:text-white mt-1"
                                        >
                                            {{
                                                formatDate(
                                                    selectedActivity.created_at
                                                )
                                            }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div>
                                    <label
                                        class="text-xs text-gray-500 dark:text-gray-400 font-medium"
                                        >Description</label
                                    >
                                    <p
                                        class="text-sm text-gray-700 dark:text-gray-300 mt-1 bg-gray-50 dark:bg-gray-700 p-3 rounded-lg"
                                    >
                                        {{ selectedActivity.description }}
                                    </p>
                                </div>

                                <!-- Properties -->
                                <!-- <div
                                    v-if="
                                        selectedActivity.properties &&
                                        Object.keys(selectedActivity.properties)
                                            .length > 0
                                    "
                                >
                                    <label
                                        class="text-xs text-gray-500 dark:text-gray-400 font-medium"
                                        >Additional Data</label
                                    >
                                    <div
                                        class="mt-1 bg-gray-50 dark:bg-gray-700 p-3 rounded-lg"
                                    >
                                        <pre
                                            class="text-xs text-gray-700 dark:text-gray-300 whitespace-pre-wrap"
                                            >{{
                                                JSON.stringify(
                                                    selectedActivity.properties,
                                                    null,
                                                    2
                                                )
                                            }}</pre
                                        >
                                    </div>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { usePage, router } from "@inertiajs/vue3";
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from "vue";
import { debounce } from "lodash";
import {
    Search,
    Filter,
    ChevronDown,
    RefreshCw,
    Activity,
    PlusCircle,
    Edit,
    Users,
    Download,
    LayoutGrid,
    List,
    Eye,
    X,
    User,
    ArrowRight,
    Calendar,
    FileText,
    Shield,
    Trash2,
    CheckCircle,
    AlertTriangle,
} from "lucide-vue-next";

const page = usePage();
const activities = computed(() => page.props.activities);
const filters = computed(() => page.props.filters);
const eventTypes = computed(() => page.props.event_types);

// Reactive state
const eventType = ref(filters.value.event_type || "all");
const dateFrom = ref(filters.value.date_from || "");
const dateTo = ref(filters.value.date_to || "");
const searchTerm = ref(filters.value.search || "");
const showFilterDropdown = ref(false);
const isResetting = ref(false);
const viewMode = ref("grid");
const showDetailsModal = ref(false);
const selectedActivity = ref(null);
const filterDropdownStyle = ref({});
const filterButton = ref(null);

// Activity configuration with Lucide icons
const activityConfig = {
    created: {
        icon: PlusCircle,
        bg: "bg-blue-100 dark:bg-blue-900/30",
        text: "text-blue-600 dark:text-blue-400",
        title: "New record created",
        badge: "bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200",
    },
    updated: {
        icon: Edit,
        bg: "bg-green-100 dark:bg-green-900/30",
        text: "text-green-600 dark:text-green-400",
        title: "Record updated",
        badge: "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200",
    },
    deleted: {
        icon: Trash2,
        bg: "bg-red-100 dark:bg-red-900/30",
        text: "text-red-600 dark:text-red-400",
        title: "Record deleted",
        badge: "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200",
    },
    logged_in: {
        icon: User,
        bg: "bg-purple-100 dark:bg-purple-900/30",
        text: "text-purple-600 dark:text-purple-400",
        title: "User login",
        badge: "bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200",
    },
    logged_out: {
        icon: User,
        bg: "bg-gray-100 dark:bg-gray-900/30",
        text: "text-gray-600 dark:text-gray-400",
        title: "User logout",
        badge: "bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200",
    },
    verification_success: {
        icon: CheckCircle,
        bg: "bg-teal-100 dark:bg-teal-900/30",
        text: "text-teal-600 dark:text-teal-400",
        title: "Verification passed",
        badge: "bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-200",
    },
    verification_failed: {
        icon: AlertTriangle,
        bg: "bg-amber-100 dark:bg-amber-900/30",
        text: "text-amber-600 dark:text-amber-400",
        title: "Verification failed",
        badge: "bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200",
    },
    default: {
        icon: Activity,
        bg: "bg-indigo-100 dark:bg-indigo-900/30",
        text: "text-indigo-600 dark:text-indigo-400",
        title: "System activity",
        badge: "bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200",
    },
    report_created: {
        icon: FileText,
        bg: "bg-amber-100 dark:bg-amber-900/30",
        text: "text-amber-600 dark:text-amber-400",
        title: "New Report Submitted",
        badge: "bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200",
    },
    report_status_changed: {
        icon: RefreshCw,
        bg: "bg-blue-100 dark:bg-blue-900/30",
        text: "text-blue-600 dark:text-blue-400",
        title: "Report Status Updated",
        badge: "bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200",
    },
};

// Helper functions
const getActivityConfig = (activity) => {
    if (activity.subject_type === "App\\Models\\Report") {
        return activity.event === "report_created"
            ? activityConfig.report_created
            : activityConfig.report_status_changed;
    }
    return activityConfig[activity.event] || activityConfig.default;
};

const getActivityIcon = (activity) => {
    return getActivityConfig(activity).icon;
};

const getActivityBgColor = (activity) => {
    return getActivityConfig(activity).bg;
};

const getActivityIconColor = (activity) => {
    return getActivityConfig(activity).text;
};

const getActivityTitle = (activity) => {
    return getActivityConfig(activity).title;
};

const getTypeBadgeClass = (activity) => {
    return getActivityConfig(activity).badge;
};

const formatEventType = (event) => {
    return event.replace(/_/g, " ").replace(/\b\w/g, (l) => l.toUpperCase());
};

const formatTimeAgo = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const seconds = Math.floor((now - date) / 1000);

    if (seconds < 60) return "Just now";
    if (seconds < 3600) return `${Math.floor(seconds / 60)}m ago`;
    if (seconds < 86400) return `${Math.floor(seconds / 3600)}h ago`;
    if (seconds < 604800) return `${Math.floor(seconds / 86400)}d ago`;

    return date.toLocaleDateString("en-US", {
        month: "short",
        day: "numeric",
        year: date.getFullYear() !== now.getFullYear() ? "numeric" : undefined,
    });
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

// Computed properties for statistics
const totalActivities = computed(() => activities.value?.total || 0);
const createdEventsCount = computed(
    () =>
        activities.value?.data?.filter((a) => a.event === "created").length || 0
);
const updatedEventsCount = computed(
    () =>
        activities.value?.data?.filter((a) => a.event === "updated").length || 0
);
const userActivitiesCount = computed(
    () =>
        activities.value?.data?.filter((a) => a.event.includes("logged"))
            .length || 0
);

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

const updateEventType = (type) => {
    eventType.value = type;
    showFilterDropdown.value = false;
    applyFilters();
};

// Filter functions
const applyFilters = debounce(() => {
    router.get(
        route("admin.activity-logs"),
        {
            event_type: eventType.value,
            date_from: dateFrom.value,
            date_to: dateTo.value,
            search: searchTerm.value,
        },
        {
            preserveState: true,
            replace: true,
        }
    );
}, 300);

const clearFilters = () => {
    isResetting.value = true;
    setTimeout(() => {
        eventType.value = "all";
        dateFrom.value = "";
        dateTo.value = "";
        searchTerm.value = "";
        isResetting.value = false;
        applyFilters();
    }, 500);
};



// Activity details modal
const showActivityDetails = (activity) => {
    selectedActivity.value = activity;
    showDetailsModal.value = true;
};

const closeDetailsModal = () => {
    showDetailsModal.value = false;
    selectedActivity.value = null;
};

// Click outside handler
const handleClickOutside = (event) => {
    if (!event.target.closest(".relative")) {
        showFilterDropdown.value = false;
    }
};

// Watch for filter changes
watch([eventType, dateFrom, dateTo, searchTerm], () => {
    applyFilters();
});

onMounted(() => {
    document.addEventListener("click", handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener("click", handleClickOutside);
});
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

.line-clamp-2 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
}

.line-clamp-3 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 3;
}

.cursor-pointer {
    cursor: pointer;
}
</style>
