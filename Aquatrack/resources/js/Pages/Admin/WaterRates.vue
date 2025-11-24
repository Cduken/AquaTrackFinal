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
                            <h5 class="text-sm font-semibold text-gray-500">
                                <span class="font-bold text-black">{{
                                    waterRates.length
                                }}</span>
                                Total Water Rates
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
                                        placeholder="Search"
                                        @keyup.enter="fetchRates"
                                    />
                                </div>
                            </div>

                            <button
                                @click="showCreateModal = true"
                                type="button"
                                class="flex items-center px-4 py-2 text-sm font-medium border border-blue-500/20 bg-blue-100/40 text-blue-500 rounded-sm hover:bg-blue-100/80 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200 ease-in-out"
                            >
                                <Plus class="w-4 h-4 mr-2" />
                                Add New Rate
                            </button>

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
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                    >
                                        Tier
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                    >
                                        Consumption Range
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                    >
                                        Fixed Charge
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                    >
                                        Rate per m³
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
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
                                    v-for="rate in filteredRates"
                                    :key="rate.id"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150"
                                >
                                    <td class="px-6 py-3">
                                        <div class="flex items-center">
                                            <div
                                                class="flex-shrink-0 h-8 w-8 mr-3"
                                            >
                                                <div
                                                    class="h-8 w-8 rounded-full flex items-center justify-center text-white font-semibold text-xs"
                                                    :class="
                                                        getTierColor(rate.order)
                                                    "
                                                >
                                                    {{ rate.order }}
                                                </div>
                                            </div>
                                            <div>
                                                <div
                                                    class="font-medium text-gray-900 dark:text-white text-sm"
                                                >
                                                    {{ rate.name }}
                                                </div>
                                                <div
                                                    class="text-xs text-gray-500 dark:text-gray-400"
                                                >
                                                    Order: {{ rate.order }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td
                                        class="px-6 py-3 text-sm text-gray-900 dark:text-white whitespace-nowrap"
                                    >
                                        {{ formatNumber(rate.min_consumption) }}
                                        -
                                        {{
                                            rate.max_consumption !== null
                                                ? formatNumber(
                                                      rate.max_consumption
                                                  )
                                                : "∞"
                                        }}
                                        m³
                                    </td>

                                    <td
                                        class="px-6 py-3 text-sm text-gray-900 dark:text-white whitespace-nowrap"
                                    >
                                        <span v-if="rate.fixed_charge > 0">
                                            ₱{{
                                                formatNumber(
                                                    rate.fixed_charge,
                                                    2
                                                )
                                            }}
                                        </span>
                                        <span v-else class="text-gray-400">
                                            -
                                        </span>
                                    </td>

                                    <td
                                        class="px-6 py-3 text-sm text-gray-900 dark:text-white whitespace-nowrap"
                                    >
                                        <span v-if="rate.rate_per_cubic > 0">
                                            ₱{{
                                                formatNumber(
                                                    rate.rate_per_cubic,
                                                    2
                                                )
                                            }}
                                            <span
                                                class="text-xs text-gray-500 font-normal"
                                            >
                                                per m³
                                            </span>
                                        </span>
                                        <span v-else class="text-gray-400">
                                            -
                                        </span>
                                    </td>

                                    <td class="px-6 py-3">
                                        <span
                                            :class="
                                                statusClasses(rate.is_active)
                                            "
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                        >
                                            <span
                                                class="w-1.5 h-1.5 rounded-full mr-1.5"
                                                :class="
                                                    rate.is_active
                                                        ? 'bg-green-400'
                                                        : 'bg-red-400'
                                                "
                                            ></span>
                                            {{
                                                rate.is_active
                                                    ? "Active"
                                                    : "Inactive"
                                            }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-3 text-right">
                                        <div class="flex justify-end">
                                            <div class="relative">
                                                <button
                                                    @click="
                                                        toggleActionMenu(
                                                            rate.id
                                                        )
                                                    "
                                                    class="p-1.5 text-gray-600 hover:text-gray-900 hover:bg-gray-200 dark:hover:text-gray-300 rounded-lg transition-colors"
                                                    :data-action-button="
                                                        rate.id
                                                    "
                                                >
                                                    <MoreHorizontal
                                                        class="w-4 h-4"
                                                    />
                                                </button>

                                                <div
                                                    v-if="
                                                        activeActionMenu ===
                                                        rate.id
                                                    "
                                                    class="fixed z-[1000] mt-1 w-48 bg-white dark:bg-gray-700 rounded-lg shadow-lg border border-gray-200 dark:border-gray-600"
                                                    :style="
                                                        getActionDropdownStyle(
                                                            rate.id
                                                        )
                                                    "
                                                    @click.stop
                                                >
                                                    <div class="py-1">
                                                        <button
                                                            @click="
                                                                editRate(rate)
                                                            "
                                                            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                                        >
                                                            <Edit
                                                                class="w-4 h-4 mr-3"
                                                            />
                                                            Edit
                                                        </button>
                                                        <button
                                                            @click="
                                                                toggleRateStatus(
                                                                    rate
                                                                )
                                                            "
                                                            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                                        >
                                                            <Lock
                                                                v-if="
                                                                    rate.is_active
                                                                "
                                                                class="w-4 h-4 mr-3"
                                                            />
                                                            <Unlock
                                                                v-else
                                                                class="w-4 h-4 mr-3"
                                                            />
                                                            {{
                                                                rate.is_active
                                                                    ? "Deactivate"
                                                                    : "Activate"
                                                            }}
                                                        </button>
                                                        <button
                                                            @click="
                                                                deleteRate(rate)
                                                            "
                                                            class="flex items-center w-full px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                                        >
                                                            <Trash2
                                                                class="w-4 h-4 mr-3"
                                                            />
                                                            Delete
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Empty State -->
                                <tr v-if="filteredRates.length === 0">
                                    <td
                                        colspan="6"
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
                                                >No water rates found</span
                                            >
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
                        <div
                            class="px-6 py-3 text-sm text-gray-500 dark:text-gray-400"
                        >
                            Showing {{ filteredRates.length }} of
                            {{ waterRates.length }} rates
                        </div>
                    </div>
                </div>
            </div>

            <!-- Create/Edit Modal -->
            <WaterRateModal
                :show="showCreateModal || showEditModal"
                :rate="editingRate"
                :is-editing="showEditModal"
                @close="closeModal"
                @saved="handleRateSaved"
            />
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import WaterRateModal from "@/Components/Admin/Modals/WaterRateModal.vue";
import { ref, onMounted, onUnmounted, nextTick, computed } from "vue";
import { router } from "@inertiajs/vue3";
import { debounce } from "lodash";
import Swal from "sweetalert2";
import {
    Search,
    Plus,
    Filter,
    ChevronDown,
    RefreshCw,
    Edit,
    Lock,
    Unlock,
    Trash2,
    FileText,
    MoreHorizontal,
} from "lucide-vue-next";

const props = defineProps({
    waterRates: {
        type: Array,
        default: () => [],
    },
});

// Reactive state
const filters = ref({
    search: "",
    status: "",
});

const showCreateModal = ref(false);
const showEditModal = ref(false);
const showFilterDropdown = ref(false);
const isResetting = ref(false);
const activeActionMenu = ref(null);
const filterDropdownStyle = ref({});
const filterButton = ref(null);
const editingRate = ref(null);

// Constants
const statusOptions = [
    { value: "", label: "All Status" },
    { value: "1", label: "Active" },
    { value: "0", label: "Inactive" },
];

const tierColors = [
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

// Computed
const filteredRates = computed(() => {
    let filtered = [...props.waterRates];

    // Apply search filter
    if (filters.value.search) {
        const searchTerm = filters.value.search.toLowerCase();
        filtered = filtered.filter(
            (rate) =>
                rate.name.toLowerCase().includes(searchTerm) ||
                rate.order.toString().includes(searchTerm)
        );
    }

    // Apply status filter
    if (filters.value.status !== "") {
        const statusBool = filters.value.status === "1";
        filtered = filtered.filter((rate) => rate.is_active === statusBool);
    }

    return filtered;
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

const getActionDropdownStyle = (rateId) => {
    if (typeof window === "undefined") return {};

    const button = document.querySelector(`[data-action-button="${rateId}"]`);
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

const toggleActionMenu = async (rateId) => {
    if (activeActionMenu.value === rateId) {
        activeActionMenu.value = null;
    } else {
        activeActionMenu.value = rateId;
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
};

// Utility methods
const formatNumber = (number, decimals = 0) => {
    return parseFloat(number).toLocaleString("en-US", {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals,
    });
};

const getTierColor = (order) => {
    if (!order) return "bg-gray-400";
    const index = (order - 1) % tierColors.length;
    return tierColors[index];
};

// Rate actions - KEEPING ALL ORIGINAL FUNCTIONALITY
const editRate = (rate) => {
    editingRate.value = { ...rate };
    showEditModal.value = true;
    activeActionMenu.value = null;
};

const closeModal = () => {
    showCreateModal.value = false;
    showEditModal.value = false;
    editingRate.value = null;
};

const handleRateSaved = () => {
    closeModal();
    refreshRates();
};

const toggleRateStatus = async (rate) => {
    activeActionMenu.value = null;

    const result = await Swal.fire({
        title: `${rate.is_active ? "Deactivate" : "Activate"} Rate?`,
        text: `Are you sure you want to ${
            rate.is_active ? "deactivate" : "activate"
        } "${rate.name}"?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: `Yes, ${
            rate.is_active ? "deactivate" : "activate"
        } it!`,
        cancelButtonText: "Cancel",
        confirmButtonColor: rate.is_active ? "#f59e0b" : "#10b981",
    });

    if (result.isConfirmed) {
        try {
            await router.put(
                route("admin.water-rates.update-status", rate.id),
                {
                    is_active: !rate.is_active,
                },
                {
                    preserveScroll: true,
                }
            );

            Swal.fire({
                icon: "success",
                title: "Success!",
                text: `Rate ${
                    rate.is_active ? "deactivated" : "activated"
                } successfully.`,
                toast: true,
                position: "bottom-end",
                showConfirmButton: false,
                timer: 3000,
            });

            refreshRates();
        } catch (error) {
            Swal.fire({
                icon: "error",
                title: "Error!",
                text: "Failed to update rate status.",
                toast: true,
                position: "bottom-end",
                showConfirmButton: false,
                timer: 3000,
            });
        }
    }
};

const deleteRate = async (rate) => {
    activeActionMenu.value = null;

    const result = await Swal.fire({
        title: "Delete Rate?",
        text: `Are you sure you want to delete "${rate.name}"? This action cannot be undone.`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "Cancel",
        confirmButtonColor: "#dc2626",
    });

    if (result.isConfirmed) {
        try {
            await router.delete(route("admin.water-rates.destroy", rate.id), {
                preserveScroll: true,
            });

            Swal.fire({
                icon: "success",
                title: "Deleted!",
                text: "Rate deleted successfully.",
                toast: true,
                position: "bottom-end",
                showConfirmButton: false,
                timer: 3000,
            });

            refreshRates();
        } catch (error) {
            Swal.fire({
                icon: "error",
                title: "Error!",
                text: "Failed to delete rate.",
                toast: true,
                position: "bottom-end",
                showConfirmButton: false,
                timer: 3000,
            });
        }
    }
};

const refreshRates = () => {
    router.reload({ only: ["waterRates"] });
};

const resetFilters = () => {
    isResetting.value = true;
    setTimeout(() => {
        filters.value = {
            search: "",
            status: "",
        };
        showFilterDropdown.value = false;
        isResetting.value = false;
    }, 1500);
};

// Styling helpers
const statusClasses = (isActive) => {
    return {
        "bg-green-100 border border-green-200 text-green-800 dark:bg-green-900 dark:text-green-200":
            isActive,
        "bg-red-100 border border-red-200 text-red-800 dark:bg-red-900 dark:text-red-200":
            !isActive,
    };
};
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
</style>
