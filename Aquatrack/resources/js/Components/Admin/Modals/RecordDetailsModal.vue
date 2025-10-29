<template>
    <transition
        enter-active-class="ease-out duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
            <!-- Overlay -->
            <div
                class="fixed inset-0 bg-black/50 transition-opacity duration-300"
                @click="emit('close')"
            />

            <!-- Modal Container -->
            <div class="flex min-h-full items-stretch p-0">
                <div
                    class="relative w-full transform transition-all duration-300"
                >
                    <div
                        class="bg-white flex flex-col"
                        :class="
                            isMaximized
                                ? 'h-screen'
                                : 'h-[90vh] max-w-6xl mx-auto mt-8 rounded-xl shadow-2xl'
                        "
                    >
                        <!-- Fixed Header -->
                        <div
                            class="flex items-center justify-between py-4 border-b border-gray-200 flex-shrink-0 bg-white px-6"
                        >
                            <h2 class="text-xl font-semibold text-gray-900">
                                Concessioner's Record Details
                            </h2>
                            <div class="flex items-center space-x-2">
                                <!-- Maximize/Minimize Button -->
                                <button
                                    @click="toggleMaximize"
                                    class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
                                    :title="
                                        isMaximized ? 'Minimize' : 'Maximize'
                                    "
                                >
                                    <v-icon
                                        :name="
                                            isMaximized
                                                ? 'bi-fullscreen-exit'
                                                : 'bi-fullscreen'
                                        "
                                        class="w-5 h-5"
                                    />
                                </button>
                                <!-- Close Button -->
                                <button
                                    @click="emit('close')"
                                    class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
                                >
                                    <v-icon name="bi-x-lg" class="w-5 h-5" />
                                </button>
                            </div>
                        </div>

                        <!-- Loading State -->
                        <div
                            v-if="loading"
                            class="flex-1 flex items-center justify-center"
                        >
                            <div class="text-center">
                                <div
                                    class="w-10 h-10 border-3 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"
                                ></div>
                                <p class="text-gray-500">
                                    Loading record details...
                                </p>
                            </div>
                        </div>

                        <!-- Scrollable Content -->
                        <div
                            v-else-if="record"
                            class="flex-1 overflow-y-auto p-6"
                        >
                            <div class="space-y-6 max-w-7xl mx-auto">
                                <!-- Status and Info Badges -->
                                <div class="flex flex-wrap gap-4">
                                    <div
                                        class="flex items-center bg-gray-50 px-4 py-3 rounded-lg border border-gray-200"
                                    >
                                        <v-icon
                                            name="bi-circle-fill"
                                            class="w-5 h-5 text-blue-500 mr-2"
                                        />
                                        <span
                                            class="text-base font-medium text-gray-700"
                                            >Record ID: {{ record.id }}</span
                                        >
                                    </div>
                                    <span
                                        class="px-4 py-3 text-base font-semibold rounded-lg border"
                                        :class="statusClass"
                                    >
                                        {{ statusLabel }}
                                    </span>
                                    <div
                                        class="flex items-center bg-gray-50 px-4 py-3 rounded-lg border border-gray-200"
                                    >
                                        <v-icon
                                            name="bi-calendar"
                                            class="w-5 h-5 text-gray-500 mr-2"
                                        />
                                        <span class="text-base text-gray-700">
                                            {{
                                                formatDate(record.reading_date)
                                            }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Main Grid Layout -->
                                <div
                                    class="grid grid-cols-1 xl:grid-cols-2 gap-8"
                                >
                                    <!-- Left Column -->
                                    <div class="space-y-8">
                                        <!-- Customer Information -->
                                        <div
                                            class="bg-white border border-gray-200 rounded-xl shadow-sm"
                                        >
                                            <div
                                                class="bg-gray-50 px-6 py-4 border-b border-gray-200"
                                            >
                                                <h3
                                                    class="text-lg font-semibold text-gray-900 flex items-center"
                                                >
                                                    <v-icon
                                                        name="bi-person"
                                                        class="w-5 h-5 text-blue-600 mr-2"
                                                    />
                                                    Customer Information
                                                </h3>
                                            </div>
                                            <div class="p-6 space-y-4">
                                                <div
                                                    class="grid grid-cols-1 gap-4"
                                                >
                                                    <div>
                                                        <label
                                                            class="text-sm text-gray-500 font-medium"
                                                            >Full Name</label
                                                        >
                                                        <p
                                                            class="text-base font-medium text-gray-900 mt-2"
                                                        >
                                                            {{
                                                                record.user.name
                                                            }}
                                                            {{
                                                                record.user
                                                                    .lastname
                                                            }}
                                                        </p>
                                                    </div>
                                                    <div
                                                        class="grid grid-cols-2 gap-4"
                                                    >
                                                        <div>
                                                            <label
                                                                class="text-sm text-gray-500 font-medium"
                                                                >Account
                                                                Number</label
                                                            >
                                                            <p
                                                                class="text-base font-medium text-gray-900 mt-2"
                                                            >
                                                                {{
                                                                    record.user
                                                                        .account_number
                                                                }}
                                                            </p>
                                                        </div>
                                                        <div>
                                                            <label
                                                                class="text-sm text-gray-500 font-medium"
                                                                >Serial
                                                                Number</label
                                                            >
                                                            <p
                                                                class="text-base font-medium text-gray-900 mt-2"
                                                            >
                                                                {{
                                                                    record.user
                                                                        .serial_number
                                                                }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="text-sm text-gray-500 font-medium"
                                                            >Address</label
                                                        >
                                                        <p
                                                            class="text-base font-medium text-gray-900 mt-2"
                                                        >
                                                            {{
                                                                record.user
                                                                    .zone
                                                            }},
                                                            {{
                                                                record.user
                                                                    .barangay
                                                            }},
                                                            {{
                                                                record.user
                                                                    .municipality
                                                            }},
                                                            {{
                                                                record.user
                                                                    .province
                                                            }}
                                                        </p>
                                                    </div>
                                                    <div
                                                        class="grid grid-cols-2 gap-4"
                                                    >
                                                        <div>
                                                            <label
                                                                class="text-sm text-gray-500 font-medium"
                                                                >Contact
                                                                Number</label
                                                            >
                                                            <p
                                                                class="text-base font-medium text-gray-900 mt-2"
                                                            >
                                                                {{
                                                                    record.user
                                                                        .phone ||
                                                                    "N/A"
                                                                }}
                                                            </p>
                                                        </div>
                                                        <div>
                                                            <label
                                                                class="text-sm text-gray-500 font-medium"
                                                                >Email</label
                                                            >
                                                            <p
                                                                class="text-base font-medium text-gray-900 mt-2"
                                                            >
                                                                {{
                                                                    record.user
                                                                        .email ||
                                                                    "N/A"
                                                                }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Right Column -->
                                    <div class="space-y-8">
                                        <!-- Reading Information -->
                                        <div
                                            class="bg-white border border-gray-200 rounded-xl shadow-sm"
                                        >
                                            <div
                                                class="bg-gray-50 px-6 py-4 border-b border-gray-200"
                                            >
                                                <h3
                                                    class="text-lg font-semibold text-gray-900 flex items-center"
                                                >
                                                    <v-icon
                                                        name="bi-speedometer2"
                                                        class="w-5 h-5 text-blue-600 mr-2"
                                                    />
                                                    Reading Information
                                                </h3>
                                            </div>
                                            <div class="p-6 space-y-4">
                                                <div
                                                    class="grid grid-cols-2 gap-4"
                                                >
                                                    <div>
                                                        <label
                                                            class="text-sm text-gray-500 font-medium"
                                                            >Reading Date</label
                                                        >
                                                        <p
                                                            class="text-base font-medium text-gray-900 mt-2"
                                                        >
                                                            {{
                                                                formatDate(
                                                                    record.reading_date
                                                                )
                                                            }}
                                                        </p>
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="text-sm text-gray-500 font-medium"
                                                            >Due Date</label
                                                        >
                                                        <p
                                                            class="text-base font-medium text-gray-900 mt-2"
                                                            :class="{
                                                                'text-red-600':
                                                                    record.status ===
                                                                        'Overdue' &&
                                                                    surcharge,
                                                            }"
                                                        >
                                                            {{
                                                                formatDate(
                                                                    record.due_date
                                                                )
                                                            }}
                                                        </p>
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="text-sm text-gray-500 font-medium"
                                                            >Current
                                                            Reading</label
                                                        >
                                                        <p
                                                            class="text-base font-medium text-gray-900 mt-2"
                                                        >
                                                            {{
                                                                record.reading
                                                            }}
                                                            m³
                                                        </p>
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="text-sm text-gray-500 font-medium"
                                                            >Consumption</label
                                                        >
                                                        <p
                                                            class="text-base font-medium text-gray-900 mt-2"
                                                        >
                                                            {{
                                                                record.consumption
                                                            }}
                                                            m³
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Payment Information -->
                                        <div
                                            class="bg-white border border-gray-200 rounded-xl shadow-sm"
                                        >
                                            <div
                                                class="bg-gray-50 px-6 py-4 border-b border-gray-200"
                                            >
                                                <h3
                                                    class="text-lg font-semibold text-gray-900 flex items-center"
                                                >
                                                    <v-icon
                                                        name="bi-cash-coin"
                                                        class="w-5 h-5 text-blue-600 mr-2"
                                                    />
                                                    Payment Information
                                                </h3>
                                            </div>
                                            <div class="p-6 space-y-4">
                                                <div
                                                    class="grid grid-cols-2 gap-4"
                                                >
                                                    <div>
                                                        <label
                                                            class="text-sm text-gray-500 font-medium"
                                                            >Base Amount</label
                                                        >
                                                        <p
                                                            class="text-base font-medium text-gray-900 mt-2"
                                                        >
                                                            ₱{{ record.amount }}
                                                        </p>
                                                    </div>
                                                    <div
                                                        v-if="
                                                            surcharge &&
                                                            record.status !==
                                                                'Paid'
                                                        "
                                                    >
                                                        <label
                                                            class="text-sm text-gray-500 font-medium"
                                                            >Surcharge</label
                                                        >
                                                        <p
                                                            class="text-base font-medium text-red-600 mt-2"
                                                        >
                                                            ₱{{ surcharge }}
                                                        </p>
                                                    </div>
                                                    <div class="col-span-2">
                                                        <label
                                                            class="text-sm text-gray-500 font-medium"
                                                            >Total Amount</label
                                                        >
                                                        <p
                                                            class="text-2xl font-bold text-gray-900 mt-2"
                                                        >
                                                            ₱{{ totalAmount }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Status Information -->
                                        <div
                                            class="bg-white border border-gray-200 rounded-xl shadow-sm"
                                        >
                                            <div
                                                class="bg-gray-50 px-6 py-4 border-b border-gray-200"
                                            >
                                                <h3
                                                    class="text-lg font-semibold text-gray-900 flex items-center"
                                                >
                                                    <v-icon
                                                        name="bi-info-circle"
                                                        class="w-5 h-5 text-blue-600 mr-2"
                                                    />
                                                    Status Information
                                                </h3>
                                            </div>
                                            <div class="p-6 space-y-4">
                                                <div
                                                    class="grid grid-cols-2 gap-4"
                                                >
                                                    <div>
                                                        <label
                                                            class="text-sm text-gray-500 font-medium"
                                                            >Payment
                                                            Status</label
                                                        >
                                                        <div class="mt-2">
                                                            <span
                                                                class="px-3 py-1.5 text-sm font-semibold rounded-full"
                                                                :class="
                                                                    statusClass
                                                                "
                                                            >
                                                                {{
                                                                    statusLabel
                                                                }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="text-sm text-gray-500 font-medium"
                                                            >Days Status</label
                                                        >
                                                        <p
                                                            class="text-base font-medium text-gray-900 mt-2"
                                                            :class="{
                                                                'text-red-600':
                                                                    record.status ===
                                                                    'Overdue',
                                                            }"
                                                        >
                                                            {{ getDaysStatus }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Fixed Footer -->
                        <div
                            class="border-t border-gray-200 px-6 py-4 bg-gray-50 flex justify-end flex-shrink-0"
                        >
                            <button
                                @click="emit('close')"
                                type="button"
                                class="inline-flex items-center px-5 py-2 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                            >
                                <v-icon name="bi-x-lg" class="mr-2" />
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup>
import { ref, computed } from "vue";

const props = defineProps({
    show: {
        type: Boolean,
        required: true,
    },
    record: {
        type: Object,
        default: null,
    },
    loading: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["close"]);

// State
const isMaximized = ref(false);

// Computed Properties
const statusClass = computed(() => {
    if (!props.record?.status)
        return "bg-gray-100 text-gray-800 border-gray-200";

    const classes = {
        paid: "bg-green-100 text-green-800 border-green-200",
        pending: "bg-yellow-100 text-yellow-800 border-yellow-200",
        overdue: "bg-red-100 text-red-800 border-red-200",
    };

    return (
        classes[props.record.status.toLowerCase()] ||
        "bg-gray-100 text-gray-800 border-gray-200"
    );
});

const statusLabel = computed(() => {
    if (!props.record?.status) return "Unknown";
    return (
        props.record.status.charAt(0).toUpperCase() +
        props.record.status.slice(1)
    );
});

const surcharge = computed(() => {
    if (!props.record?.surcharge || props.record.status === "Paid") {
        return null;
    }
    return props.record.surcharge;
});

const totalAmount = computed(() => {
    const baseAmount = parseFloat(props.record?.amount) || 0;
    const surchargeAmount = parseFloat(surcharge.value) || 0;
    return (baseAmount + surchargeAmount).toFixed(2);
});

const getDaysStatus = computed(() => {
    if (!props.record?.due_date) return "N/A";

    const dueDate = new Date(props.record.due_date);
    const today = new Date();
    const diffTime = dueDate - today;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays > 0) {
        return `Due in ${diffDays} day${diffDays !== 1 ? "s" : ""}`;
    } else if (diffDays === 0) {
        return "Due today";
    } else {
        return `Overdue by ${Math.abs(diffDays)} day${
            Math.abs(diffDays) !== 1 ? "s" : ""
        }`;
    }
});

// Methods
const toggleMaximize = () => {
    isMaximized.value = !isMaximized.value;
};

const formatDate = (dateString) => {
    if (!dateString) return "N/A";
    const options = { year: "numeric", month: "long", day: "numeric" };
    return new Date(dateString).toLocaleDateString(undefined, options);
};
</script>
