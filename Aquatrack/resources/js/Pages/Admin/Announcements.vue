//Pages/Admin/Announcements.vue
<template>
    <AdminLayout>
        <div class="mx-auto w-full">
            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-2 relative">
                <!-- Table Section -->
                <div
                    :class="[
                        'transition-all duration-500 ease-in-out',
                        showCalendar ? 'xl:col-span-2' : 'xl:col-span-3',
                    ]"
                >
                    <div
                        class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden"
                    >
                        <!-- Search and Filter Section -->
                        <div
                            class="p-2 border-b border-gray-200 dark:border-gray-700"
                        >
                            <div
                                class="flex flex-col md:flex-row md:items-center justify-between gap-4"
                            >
                                <div class="flex items-center px-2 space-x-4">
                                    <h5
                                        class="text-sm font-semibold text-gray-500"
                                    >
                                        <span class="font-bold text-black">{{
                                            announcements.total || 0
                                        }}</span>
                                        Total Announcements
                                    </h5>
                                </div>

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
                                                placeholder="Search announcements..."
                                                @input="handleSearch"
                                            />
                                        </div>
                                    </div>
                                    <button
                                        @click="openCreateModal"
                                        type="button"
                                        class="flex items-center px-4 py-2 text-sm font-medium border bg-blue-100/40 border-blue-500/20 text-blue-500 rounded-sm hover:bg-blue-100/80 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200 ease-in-out"
                                    >
                                        <Plus class="w-4 h-4 mr-2" />
                                        Create Announcement
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
                                            class="absolute z-50 mt-1 w-56 bg-white rounded-lg shadow-lg border border-gray-200 dark:bg-gray-700 dark:border-gray-600"
                                            :style="filterDropdownStyle"
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
                                                    >
                                                        <input
                                                            id="status-all"
                                                            type="radio"
                                                            v-model="
                                                                filters.status
                                                            "
                                                            value=""
                                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                                        />
                                                        <label
                                                            for="status-all"
                                                            class="ml-2 text-sm text-gray-700 dark:text-gray-300"
                                                        >
                                                            All Status
                                                        </label>
                                                    </div>
                                                    <div
                                                        class="flex items-center"
                                                    >
                                                        <input
                                                            id="status-active"
                                                            type="radio"
                                                            v-model="
                                                                filters.status
                                                            "
                                                            value="active"
                                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                                        />
                                                        <label
                                                            for="status-active"
                                                            class="ml-2 text-sm text-gray-700 dark:text-gray-300"
                                                        >
                                                            Active
                                                        </label>
                                                    </div>
                                                    <div
                                                        class="flex items-center"
                                                    >
                                                        <input
                                                            id="status-inactive"
                                                            type="radio"
                                                            v-model="
                                                                filters.status
                                                            "
                                                            value="inactive"
                                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                                        />
                                                        <label
                                                            for="status-inactive"
                                                            class="ml-2 text-sm text-gray-700 dark:text-gray-300"
                                                        >
                                                            Inactive
                                                        </label>
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
                                                            'animate-spin':
                                                                isResetting,
                                                        }"
                                                    />
                                                    Reset
                                                </button>
                                                <button
                                                    @click="applyFilters"
                                                    class="px-3 py-1.5 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                >
                                                    Apply
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
                                            ID
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-6 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                                            @click="sortBy('title')"
                                        >
                                            <div class="flex items-center">
                                                Title
                                                <ChevronsUpDown
                                                    v-if="
                                                        filters.sort === 'title'
                                                    "
                                                    class="w-4 h-4 ml-1"
                                                />
                                            </div>
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-6 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                        >
                                            Content Preview
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-6 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                        >
                                            Start Date
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-6 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                        >
                                            End Date
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-6 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                        >
                                            Status
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
                                        v-for="announcement in announcements.data"
                                        :key="announcement.id"
                                        class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150"
                                    >
                                        <!-- ID Column -->
                                        <td
                                            class="px-6 py-2 text-xs text-gray-900 dark:text-white whitespace-nowrap"
                                        >
                                            {{ announcement.id }}
                                        </td>

                                        <!-- Title Column -->
                                        <td
                                            class="px-6 py-2 text-xs text-gray-900 dark:text-white whitespace-nowrap"
                                        >
                                            {{ announcement.title }}
                                        </td>

                                        <!-- Content Preview -->
                                        <td
                                            class="px-6 py-2 text-xs text-gray-900 dark:text-white"
                                        >
                                            <span class="truncate max-w-xs">{{
                                                truncateContent(
                                                    announcement.content,
                                                    50
                                                )
                                            }}</span>
                                        </td>

                                        <!-- Start Date -->
                                        <td
                                            class="px-6 py-2 text-xs text-gray-900 dark:text-white whitespace-nowrap"
                                        >
                                            {{
                                                formatDate(
                                                    announcement.start_date
                                                ) || "N/A"
                                            }}
                                        </td>

                                        <!-- End Date -->
                                        <td
                                            class="px-6 py-2 text-xs text-gray-900 dark:text-white whitespace-nowrap"
                                        >
                                            {{
                                                formatDate(
                                                    announcement.end_date
                                                ) || "N/A"
                                            }}
                                        </td>

                                        <!-- Status -->
                                        <td class="px-6 py-2">
                                            <span
                                                :class="
                                                    statusClasses(
                                                        announcement.status
                                                    )
                                                "
                                                class="inline-flex items-center border px-2 py-0.5 rounded-full text-xs font-sm"
                                            >
                                                <span
                                                    class="w-1.5 h-1.5 rounded-full mr-1.5"
                                                    :class="
                                                        announcement.status.toLowerCase() ===
                                                        'active'
                                                            ? 'bg-green-400'
                                                            : 'bg-yellow-400'
                                                    "
                                                ></span>
                                                {{ announcement.status }}
                                            </span>
                                        </td>

                                        <!-- Actions -->
                                        <td class="px-6 py-3 text-right">
                                            <div
                                                class="flex justify-end space-x-2"
                                            >
                                                <button
                                                    @click="
                                                        editAnnouncement(
                                                            announcement
                                                        )
                                                    "
                                                    class="p-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors"
                                                    title="Edit"
                                                >
                                                    <Edit class="w-4 h-4" />
                                                </button>
                                                <button
                                                    @click="
                                                        deleteAnnouncement(
                                                            announcement.id
                                                        )
                                                    "
                                                    class="p-1.5 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors"
                                                    title="Delete"
                                                >
                                                    <Trash2 class="w-4 h-4" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Empty State -->
                                    <tr
                                        v-if="
                                            !announcements.data ||
                                            announcements.data.length === 0
                                        "
                                    >
                                        <td
                                            colspan="7"
                                            class="px-6 py-8 text-center"
                                        >
                                            <div
                                                class="flex flex-col items-center justify-center space-y-2"
                                            >
                                                <Speaker
                                                    class="w-12 h-12 text-gray-300"
                                                />
                                                <span
                                                    class="font-medium text-gray-500 dark:text-gray-400"
                                                    >No announcements
                                                    found</span
                                                >
                                                <span
                                                    class="text-xs text-gray-400 dark:text-gray-500"
                                                >
                                                    Try adjusting your filters
                                                    or search keywords.
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination Component -->
                        <Pagination :data="announcements" />
                    </div>
                </div>

                <!-- Calendar Component with Toggle -->
                <div
                    :class="[
                        'transition-all duration-500 ease-in-out transform',
                        showCalendar
                            ? 'xl:col-span-1 opacity-100 translate-x-0 max-h-[800px]'
                            : 'xl:col-span-0 opacity-0 -translate-x-full max-h-0 overflow-hidden',
                    ]"
                >
                    <div class="relative h-full">
                        <!-- Calendar Toggle Button -->
                        <button
                            @click="toggleCalendar"
                            class="absolute -left-2 top-4 z-20 p-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full shadow-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300 hover:shadow-xl"
                            :title="
                                showCalendar ? 'Hide Calendar' : 'Show Calendar'
                            "
                        >
                            <ChevronRight
                                class="w-4 h-4 text-gray-600 dark:text-gray-400 transition-transform duration-300"
                                :class="{
                                    'rotate-0': showCalendar,
                                    'rotate-180': !showCalendar,
                                }"
                            />
                        </button>

                        <AnnouncementCalendar
                            :announcements="announcements.data || []"
                        />
                    </div>
                </div>

                <!-- Show Calendar Button when Calendar is Hidden -->
                <div
                    v-if="!showCalendar"
                    class="fixed right-4 top-1/2 transform -translate-y-1/2 z-10"
                >
                    <button
                        @click="toggleCalendar"
                        class="p-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full shadow-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300 hover:shadow-xl"
                        title="Show Calendar"
                    >
                        <ChevronLeft
                            class="w-5 h-5 text-gray-600 dark:text-gray-400 transition-transform duration-300"
                        />
                    </button>
                </div>
            </div>

            <!-- Announcement Modal Component -->
            <AnnouncementModal
                :show="showModal"
                :editing="editing"
                :form="form"
                :errors="errors"
                @close="closeModal"
                @submit="editing ? updateAnnouncement() : createAnnouncement()"
            />
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import AnnouncementModal from "@/Components/Modals/AnnouncementModal.vue";
import Pagination from "@/Components/Pagination.vue";
import AnnouncementCalendar from "@/Components/Admin/Calendar/AnnouncementCalendar.vue";
import { useForm } from "@inertiajs/vue3";
import { ref, reactive, onMounted, onUnmounted, nextTick } from "vue";
import { pickBy, debounce } from "lodash";
import Swal from "sweetalert2";
import { router } from "@inertiajs/vue3";
import {
    Search,
    Plus,
    Filter,
    ChevronDown,
    ChevronRight,
    ChevronLeft,
    RefreshCw,
    Edit,
    Trash2,
    Speaker,
    ChevronsUpDown,
} from "lucide-vue-next";

const props = defineProps({
    announcements: Object,
    errors: Object,
    filters: Object,
});

// Refs
const showModal = ref(false);
const editing = ref(false);
const showFilterDropdown = ref(false);
const isResetting = ref(false);
const currentId = ref(null);
const filterButton = ref(null);
const filterDropdownStyle = ref({});
const showCalendar = ref(true);

// Reactive data
const filters = reactive({
    search: props.filters?.search || "",
    status: props.filters?.status || "",
    sort: props.filters?.sort || "id",
    order: props.filters?.order || "desc",
    per_page: props.filters?.per_page || 10,
});

const form = useForm({
    title: "",
    content: "",
    status: "active",
    start_date: null,
    end_date: null,
});

// Methods
const toggleCalendar = () => {
    showCalendar.value = !showCalendar.value;
};

const toggleFilterDropdown = async () => {
    showFilterDropdown.value = !showFilterDropdown.value;

    if (showFilterDropdown.value && filterButton.value) {
        await nextTick();
        const rect = filterButton.value.getBoundingClientRect();
        filterDropdownStyle.value = {
            left: `${rect.left}px`,
            top: `${rect.bottom + window.scrollY + 8}px`,
            position: "fixed",
        };
    }
};

const applyFilters = () => {
    showFilterDropdown.value = false;
    fetchAnnouncements();
};

const handleSearch = debounce(() => {
    fetchAnnouncements();
}, 300);

const resetFilters = () => {
    isResetting.value = true;

    Object.assign(filters, {
        search: "",
        status: "",
        sort: "id",
        order: "desc",
        per_page: 10,
    });

    showFilterDropdown.value = false;
    setTimeout(() => {
        isResetting.value = false;
        fetchAnnouncements();
    }, 500);
};

const fetchAnnouncements = () => {
    const filteredParams = pickBy(
        filters,
        (value) => value !== "" && value !== null && value !== undefined
    );

    router.get("/admin/announcements", filteredParams, {
        preserveState: true,
        replace: true,
        onError: (errors) => {
            console.error("Fetch error:", errors);
            showError("Failed to fetch announcements. Please try again.");
        },
    });
};

// Helper functions
const statusClasses = (status) => {
    const statusLower = status.toLowerCase();
    return {
        "bg-green-100 border border-green-200 text-green-800 dark:bg-green-900 dark:text-green-200":
            statusLower === "active",
        "bg-yellow-100 border border-yellow-200 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200":
            statusLower === "inactive",
    };
};

const formatDate = (dateString) => {
    if (!dateString) return "";
    return new Date(dateString).toLocaleDateString(undefined, {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
};

const truncateContent = (content, length) => {
    if (!content) return "";
    return content.length > length
        ? content.substring(0, length) + "..."
        : content;
};

const sortBy = (column) => {
    if (filters.sort === column) {
        filters.order = filters.order === "asc" ? "desc" : "asc";
    } else {
        filters.sort = column;
        filters.order = "asc";
    }
    fetchAnnouncements();
};

const showError = (message) => {
    Swal.fire({
        icon: "error",
        title: "Error",
        text: message,
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
    });
};

const showSuccess = (message) => {
    Swal.fire({
        position: "top-end",
        icon: "success",
        title: message,
        showConfirmButton: false,
        timer: 2000,
        toast: true,
    });
};

// Modal control functions
const openCreateModal = () => {
    editing.value = false;
    form.reset();
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    editing.value = false;
    form.reset();
    currentId.value = null;
};

// CRUD operations
const editAnnouncement = (announcement) => {
    editing.value = true;
    currentId.value = announcement.id;
    form.title = announcement.title;
    form.content = announcement.content;
    form.status = announcement.status.toLowerCase();
    form.start_date = announcement.start_date;
    form.end_date = announcement.end_date;
    showModal.value = true;
};

const createAnnouncement = () => {
    form.post(route("announcements.store"), {
        onSuccess: () => {
            showModal.value = false;
            form.reset();
            showSuccess("Announcement Created!");
            fetchAnnouncements();
        },
        onError: () => {
            showError("There was an error creating the announcement.");
        },
    });
};

const updateAnnouncement = () => {
    form.put(route("announcements.update", currentId.value), {
        onSuccess: () => {
            showModal.value = false;
            editing.value = false;
            form.reset();
            currentId.value = null;
            showSuccess("Update Successful!");
            fetchAnnouncements();
        },
        onError: () => {
            showError("There was an error updating the announcement.");
        },
    });
};

const deleteAnnouncement = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "This announcement will be permanently deleted!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(route("announcements.destroy", id), {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "The announcement has been deleted.",
                        "success"
                    );
                    fetchAnnouncements();
                },
                onError: () => {
                    showError("Something went wrong.");
                },
            });
        }
    });
};

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
    if (filterButton.value && !filterButton.value.contains(event.target)) {
        showFilterDropdown.value = false;
    }
};

// Lifecycle
onMounted(() => {
    document.addEventListener("click", handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener("click", handleClickOutside);
});
</script>

<style scoped>
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

/* Smooth transitions for calendar */
.calendar-enter-active,
.calendar-leave-active {
    transition: all 0.5s ease-in-out;
}

.calendar-enter-from {
    opacity: 0;
    transform: translateX(100%);
}

.calendar-leave-to {
    opacity: 0;
    transform: translateX(-100%);
}
</style>
