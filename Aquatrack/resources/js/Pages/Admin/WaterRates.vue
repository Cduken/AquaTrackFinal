<template>
    <AdminLayout>
        <div class="mx-auto w-full max-w-7xl">
            <div
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm h-64"
            >
                <!-- Header -->
                <div class="p-3 border-b border-gray-200 dark:border-gray-700">
                    <div
                        class="flex flex-col sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div>

                            <p
                                class="mt-1 text-sm text-gray-600 dark:text-gray-400"
                            >
                                Water rates consumption
                            </p>
                        </div>
                        <button
                            @click="showCreateModal = true"
                            class="flex items-center px-4 py-2 text-sm font-medium border border-blue-500/20 bg-blue-100/40 text-blue-500 rounded-sm hover:bg-blue-100/80 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200 ease-in-out"
                        >
                            <Plus class="w-4 h-4 mr-1" />
                            Add New Rate
                        </button>
                    </div>
                </div>

                <!-- Rates Table -->
                <div class="p-2 h-[500px]">
                    <div
                        class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700"
                    >
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b border-gray-200 dark:border-gray-600"
                                    >
                                        Tier Name
                                    </th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b border-gray-200 dark:border-gray-600"
                                    >
                                        Consumption Range
                                    </th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b border-gray-200 dark:border-gray-600"
                                    >
                                        Fixed Charge
                                    </th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b border-gray-200 dark:border-gray-600"
                                    >
                                        Rate per m³
                                    </th>
                                    <!-- <th
                                        class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b border-gray-200 dark:border-gray-600"
                                    >
                                        Status
                                    </th> -->
                                    <th
                                        class="px-6 py-4 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b border-gray-200 dark:border-gray-600"
                                    >
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody
                                class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"
                            >
                                <tr
                                    v-for="rate in waterRates"
                                    :key="rate.id"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150"
                                >
                                    <td class="px-6 py-4">
                                        <div
                                            class="flex items-center space-x-3"
                                        >
                                            <div
                                                class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center"
                                            >
                                                <span
                                                    class="text-xs font-semibold text-white"
                                                    >{{ rate.order }}</span
                                                >
                                            </div>
                                            <div>
                                                <div
                                                    class="text-sm font-semibold text-gray-900 dark:text-white"
                                                >
                                                    {{ rate.name }}
                                                </div>
                                                <div
                                                    class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"
                                                >
                                                    Order: {{ rate.order }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div
                                            class="text-sm text-gray-900 dark:text-white font-medium"
                                        >
                                            {{
                                                formatNumber(
                                                    rate.min_consumption
                                                )
                                            }}
                                            -
                                            {{
                                                rate.max_consumption !== null
                                                    ? formatNumber(
                                                          rate.max_consumption
                                                      )
                                                    : "∞"
                                            }}
                                            m³
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div
                                            class="text-sm font-semibold text-gray-900 dark:text-white"
                                            v-if="rate.fixed_charge > 0"
                                        >
                                            ₱{{
                                                formatNumber(
                                                    rate.fixed_charge,
                                                    2
                                                )
                                            }}
                                        </div>
                                        <div
                                            class="text-sm text-gray-500 dark:text-gray-400"
                                            v-else
                                        >
                                            -
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div
                                            class="text-sm font-semibold text-gray-900 dark:text-white"
                                            v-if="rate.rate_per_cubic > 0"
                                        >
                                            ₱{{
                                                formatNumber(
                                                    rate.rate_per_cubic,
                                                    2
                                                )
                                            }}
                                            <span
                                                class="text-xs text-gray-500 font-normal"
                                                >per m³</span
                                            >
                                        </div>
                                        <div
                                            class="text-sm text-gray-500 dark:text-gray-400"
                                            v-else
                                        >
                                            -
                                        </div>
                                    </td>
                                    <!-- <td class="px-6 py-4">
                                        <span
                                            :class="
                                                rate.is_active
                                                    ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 border border-green-200 dark:border-green-800'
                                                    : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 border border-red-200 dark:border-red-800'
                                            "
                                            class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold transition-colors"
                                        >
                                            <span
                                                class="w-2 h-2 rounded-full mr-2"
                                                :class="
                                                    rate.is_active
                                                        ? 'bg-green-500'
                                                        : 'bg-red-500'
                                                "
                                            ></span>
                                            {{
                                                rate.is_active
                                                    ? "Active"
                                                    : "Inactive"
                                            }}
                                        </span>
                                    </td> -->
                                    <td class="px-6 py-4">
                                        <div class="flex justify-end">
                                            <!-- Dropdown Menu -->
                                            <div
                                                class="relative"
                                                ref="dropdownContainer"
                                            >
                                                <button
                                                    @click="
                                                        toggleDropdown(rate.id)
                                                    "
                                                    class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200"
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
                                                            d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"
                                                        />
                                                    </svg>
                                                </button>

                                                <!-- Dropdown Menu -->
                                                <div
                                                    v-if="
                                                        activeDropdown ===
                                                        rate.id
                                                    "
                                                    class="absolute right-0 top-10 z-10 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1"
                                                >
                                                    <!-- Edit Option -->
                                                    <button
                                                        @click="editRate(rate)"
                                                        class="flex items-center w-full px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150"
                                                    >
                                                        <svg
                                                            class="w-4 h-4 mr-3 text-blue-500"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                                            />
                                                        </svg>
                                                        Edit
                                                    </button>

                                                    <!-- Activate/Deactivate Option -->
                                                    <!-- <button
                                                        @click="
                                                            toggleRateStatus(
                                                                rate
                                                            )
                                                        "
                                                        :class="
                                                            rate.is_active
                                                                ? 'text-orange-600 dark:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-900/20'
                                                                : 'text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20'
                                                        "
                                                        class="flex items-center w-full px-4 py-2.5 text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150"
                                                    >
                                                        <svg
                                                            class="w-4 h-4 mr-3"
                                                            :class="
                                                                rate.is_active
                                                                    ? 'text-orange-500'
                                                                    : 'text-green-500'
                                                            "
                                                            fill="none"
                                                            stroke="currentColor"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                                            />
                                                        </svg>
                                                        {{
                                                            rate.is_active
                                                                ? "Deactivate"
                                                                : "Activate"
                                                        }}
                                                    </button> -->

                                                    <!-- Delete Option -->
                                                    <button
                                                        @click="
                                                            deleteRate(rate)
                                                        "
                                                        class="flex items-center w-full px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-150"
                                                    >
                                                        <svg
                                                            class="w-4 h-4 mr-3 text-red-500"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                            />
                                                        </svg>
                                                        Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Empty State -->
                                <tr v-if="waterRates.length === 0">
                                    <td
                                        colspan="6"
                                        class="px-6 py-24 text-center"
                                    >
                                        <div
                                            class="flex flex-col items-center justify-center space-y-4"
                                        >
                                            <div
                                                class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center"
                                            >
                                                <FileText
                                                    class="w-10 h-10 text-gray-400"
                                                />
                                            </div>
                                            <div>
                                                <span
                                                    class="text-xl font-semibold text-gray-500 dark:text-gray-400 block mb-2"
                                                >
                                                    No water rates configured
                                                </span>
                                                <span
                                                    class="text-sm text-gray-400 dark:text-gray-500"
                                                >
                                                    Get started by creating your
                                                    first pricing tier.
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
    </AdminLayout>
</template>

<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import WaterRateModal from "@/Components/Admin/Modals/WaterRateModal.vue";
import { router } from "@inertiajs/vue3";
import { ref, onMounted, onUnmounted } from "vue";
import { Plus, FileText } from "lucide-vue-next";
import Swal from "sweetalert2";

// Props
const props = defineProps({
    waterRates: {
        type: Array,
        default: () => [],
    },
});

// Reactive data
const showCreateModal = ref(false);
const showEditModal = ref(false);
const editingRate = ref(null);
const activeDropdown = ref(null);
const dropdownContainer = ref(null);

// Format number with commas and decimals
const formatNumber = (number, decimals = 0) => {
    return parseFloat(number).toLocaleString("en-US", {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals,
    });
};

// Dropdown methods
const toggleDropdown = (rateId) => {
    activeDropdown.value = activeDropdown.value === rateId ? null : rateId;
};

const closeDropdown = () => {
    activeDropdown.value = null;
};

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
    if (
        dropdownContainer.value &&
        !dropdownContainer.value.contains(event.target)
    ) {
        closeDropdown();
    }
};

// Methods
const editRate = (rate) => {
    editingRate.value = { ...rate };
    showEditModal.value = true;
    closeDropdown();
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
    closeDropdown();

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
    closeDropdown();

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

// Event listeners
onMounted(() => {
    document.addEventListener("click", handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener("click", handleClickOutside);
});
</script>
