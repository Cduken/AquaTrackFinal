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
                @click="confirmClose"
            />

            <!-- Modal Container -->
            <div class="flex min-h-full items-stretch  p-0">
                <div
                    class="relative w-full transform transition-all duration-300"
                >
                    <div
                        class="bg-white flex flex-col"
                        :class="
                            isMaximized
                                ? 'h-screen'
                                : 'h-[90vh] max-w-4xl mx-auto mt-8  shadow-2xl'
                        "
                    >
                        <!-- Fixed Header -->
                        <div
                            class="flex items-center justify-between py-4 border-b border-gray-200 flex-shrink-0 bg-white px-6"
                        >
                            <h2 class="text-xl font-semibold text-gray-900">
                                Edit Water Meter Record
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
                                    <component
                                        :is="
                                            isMaximized
                                                ? Minimize2Icon
                                                : Maximize2Icon
                                        "
                                        class="w-5 h-5"
                                    />
                                </button>
                                <!-- Close Button -->
                                <button
                                    @click="confirmClose"
                                    class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
                                >
                                    <component :is="XIcon" class="w-5 h-5" />
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
                            <form
                                @submit.prevent="submitForm"
                                class="max-w-4xl mx-auto"
                            >
                                <!-- Processing Overlay -->
                                <div
                                    v-if="form.processing"
                                    class="absolute inset-0 bg-white/50 flex items-center justify-center z-10 rounded-xl"
                                >
                                    <div class="text-center">
                                        <div
                                            class="w-10 h-10 border-3 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-2"
                                        ></div>
                                        <p class="text-gray-600">
                                            Updating record...
                                        </p>
                                    </div>
                                </div>

                                <div class="space-y-6">
                                    <!-- Record Information -->
                                    <div
                                        class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden"
                                    >
                                        <div
                                            class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200"
                                        >
                                            <h3
                                                class="text-lg font-semibold text-gray-900 flex items-center"
                                            >
                                                <component
                                                    :is="UserIcon"
                                                    class="w-5 h-5 text-blue-600 mr-2"
                                                />
                                                Record Information
                                            </h3>
                                        </div>
                                        <div class="p-6">
                                            <div
                                                class="grid grid-cols-1 md:grid-cols-2 gap-6"
                                            >
                                                <div class="space-y-1">
                                                    <label
                                                        class="text-sm text-gray-500 font-medium block"
                                                        >Customer Name</label
                                                    >
                                                    <p
                                                        class="text-base font-medium text-gray-900 p-3 bg-gray-50 rounded-lg border border-gray-100"
                                                    >
                                                        {{ record.user.name }}
                                                        {{
                                                            record.user.lastname
                                                        }}
                                                    </p>
                                                </div>
                                                <div class="space-y-1">
                                                    <label
                                                        class="text-sm text-gray-500 font-medium block"
                                                        >Account Number</label
                                                    >
                                                    <p
                                                        class="text-base font-medium text-gray-900 p-3 bg-gray-50 rounded-lg border border-gray-100"
                                                    >
                                                        {{
                                                            record.user
                                                                .account_number
                                                        }}
                                                    </p>
                                                </div>
                                                <div class="space-y-1">
                                                    <label
                                                        class="text-sm text-gray-500 font-medium block"
                                                        >Reading Date</label
                                                    >
                                                    <p
                                                        class="text-base font-medium text-gray-900 p-3 bg-gray-50 rounded-lg border border-gray-100"
                                                    >
                                                        {{
                                                            formatDate(
                                                                record.reading_date
                                                            )
                                                        }}
                                                    </p>
                                                </div>
                                                <div class="space-y-1">
                                                    <label
                                                        class="text-sm text-gray-500 font-medium block"
                                                        >Due Date</label
                                                    >
                                                    <p
                                                        class="text-base font-medium text-gray-900 p-3 bg-gray-50 rounded-lg border border-gray-100"
                                                    >
                                                        {{ dueDate }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Meter Reading Details -->
                                    <div
                                        class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden"
                                    >
                                        <div
                                            class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200"
                                        >
                                            <h3
                                                class="text-lg font-semibold text-gray-900 flex items-center"
                                            >
                                                <component
                                                    :is="GaugeIcon"
                                                    class="w-5 h-5 text-blue-600 mr-2"
                                                />
                                                Meter Reading Details
                                            </h3>
                                        </div>
                                        <div class="p-6">
                                            <div
                                                class="grid grid-cols-1 md:grid-cols-2 gap-6"
                                            >
                                                <div class="space-y-1">
                                                    <label
                                                        for="current-reading"
                                                        class="text-sm text-gray-500 font-medium block"
                                                    >
                                                        Current Reading (m³)
                                                        <span
                                                            class="text-red-500"
                                                            >*</span
                                                        >
                                                    </label>
                                                    <input
                                                        id="current-reading"
                                                        type="number"
                                                        v-model="form.reading"
                                                        class="w-full p-3 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                                        step="0.01"
                                                        min="0"
                                                        required
                                                        placeholder="Enter reading in cubic meters"
                                                        @input="validateReading"
                                                    />
                                                    <p
                                                        v-if="
                                                            form.errors.reading
                                                        "
                                                        class="text-red-500 text-sm mt-1"
                                                    >
                                                        {{
                                                            form.errors.reading
                                                        }}
                                                    </p>
                                                </div>
                                                <div class="space-y-1">
                                                    <label
                                                        class="text-sm text-gray-500 font-medium block"
                                                        >Consumption (m³)</label
                                                    >
                                                    <p
                                                        class="text-base font-medium text-gray-900 p-3 bg-gray-50 rounded-lg border border-gray-100"
                                                    >
                                                        {{ record.consumption }}
                                                        m³
                                                    </p>
                                                </div>
                                                <div class="space-y-1">
                                                    <label
                                                        for="amount"
                                                        class="text-sm text-gray-500 font-medium block"
                                                    >
                                                        Amount (₱)
                                                        <span
                                                            class="text-red-500"
                                                            >*</span
                                                        >
                                                    </label>
                                                    <input
                                                        id="amount"
                                                        type="number"
                                                        v-model="form.amount"
                                                        class="w-full p-3 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                                        step="0.01"
                                                        min="0"
                                                        required
                                                        placeholder="Enter amount in pesos"
                                                        @input="validateAmount"
                                                    />
                                                    <p
                                                        v-if="
                                                            form.errors.amount
                                                        "
                                                        class="text-red-500 text-sm mt-1"
                                                    >
                                                        {{ form.errors.amount }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Payment Status -->
                                    <div
                                        class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden"
                                    >
                                        <div
                                            class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200"
                                        >
                                            <h3
                                                class="text-lg font-semibold text-gray-900 flex items-center"
                                            >
                                                <component
                                                    :is="CreditCardIcon"
                                                    class="w-5 h-5 text-blue-600 mr-2"
                                                />
                                                Payment Status
                                            </h3>
                                        </div>
                                        <div class="p-6">
                                            <div
                                                class="grid grid-cols-1 md:grid-cols-2 gap-4"
                                            >
                                                <div
                                                    v-for="statusOption in statusOptions"
                                                    :key="statusOption.value"
                                                    class="relative"
                                                >
                                                    <input
                                                        type="radio"
                                                        :id="`status-${statusOption.value}`"
                                                        :value="
                                                            statusOption.value
                                                        "
                                                        v-model="form.status"
                                                        class="hidden peer"
                                                        required
                                                    />
                                                    <label
                                                        :for="`status-${statusOption.value}`"
                                                        class="flex flex-col items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer transition-all duration-200 peer-checked:border-2 hover:bg-gray-50"
                                                        :class="
                                                            statusOption.classes
                                                        "
                                                    >
                                                        <component
                                                            :is="
                                                                statusOption.iconComponent
                                                            "
                                                            class="w-8 h-8 mb-2"
                                                        />
                                                        <span
                                                            class="font-medium text-gray-900"
                                                            >{{
                                                                statusOption.label
                                                            }}</span
                                                        >
                                                        <span
                                                            class="text-sm text-gray-500 text-center mt-1"
                                                        >
                                                            {{
                                                                statusOption.description
                                                            }}
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                            <p
                                                v-if="form.errors.status"
                                                class="text-red-500 text-sm mt-2"
                                            >
                                                {{ form.errors.status }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div
                                    class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200"
                                >
                                    <button
                                        type="button"
                                        @click="confirmClose"
                                        class="px-6 py-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors"
                                        :disabled="form.processing"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        :disabled="form.processing"
                                        class="px-6 py-3 text-base font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center"
                                    >
                                        <component
                                            v-if="form.processing"
                                            :is="LoaderCircleIcon"
                                            class="animate-spin mr-2 w-5 h-5"
                                        />
                                        {{
                                            form.processing
                                                ? "Updating..."
                                                : "Update Record"
                                        }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import {
    XIcon,
    Maximize2Icon,
    Minimize2Icon,
    UserIcon,
    GaugeIcon,
    CreditCardIcon,
    CircleCheckIcon,
    ClockIcon,
    LoaderCircleIcon,
} from "lucide-vue-next";

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

const emit = defineEmits(["close", "saved"]);

// State
const isMaximized = ref(false);
const hasUnsavedChanges = ref(false);

// Status options with icon components
const statusOptions = [
    {
        value: "Paid",
        label: "Paid",
        description: "Payment has been completed",
        iconComponent: CircleCheckIcon,
        classes: "peer-checked:border-green-500 text-green-600",
    },
    {
        value: "Pending",
        label: "Pending",
        description: "Awaiting payment confirmation",
        iconComponent: ClockIcon,
        classes: "peer-checked:border-yellow-500 text-yellow-600",
    },
];

// Initialize form
const form = useForm({
    reading: props.record?.reading || 0,
    amount: props.record?.amount || 0,
    status: props.record?.status || "Pending",
});

// Watch for record changes
watch(
    () => props.record,
    (newRecord) => {
        if (newRecord) {
            form.reading = newRecord.reading;
            form.amount = newRecord.amount;
            form.status = newRecord.status;
            hasUnsavedChanges.value = false;
        }
    },
    { immediate: true }
);

// Watch form for changes
watch(
    form,
    () => {
        if (props.record) {
            hasUnsavedChanges.value = true;
        }
    },
    { deep: true }
);

// Computed
const dueDate = computed(() => {
    if (!props.record?.reading_date) return "N/A";
    const readingDate = new Date(props.record.reading_date);
    readingDate.setDate(readingDate.getDate() + 15);
    return formatDate(readingDate.toISOString().split("T")[0]);
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

const validateReading = () => {
    if (form.reading < 0) {
        form.errors.reading = "Reading cannot be negative.";
    } else if (form.reading === "") {
        form.errors.reading = "Reading is required.";
    } else {
        form.errors.reading = "";
    }
};

const validateAmount = () => {
    if (form.amount < 0) {
        form.errors.amount = "Amount cannot be negative.";
    } else if (form.amount === "") {
        form.errors.amount = "Amount is required.";
    } else {
        form.errors.amount = "";
    }
};

const confirmClose = () => {
    if (hasUnsavedChanges.value && !form.processing) {
        Swal.fire({
            title: "Unsaved Changes",
            text: "You have unsaved changes. Are you sure you want to close?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, close",
            cancelButtonText: "Cancel",
        }).then((result) => {
            if (result.isConfirmed) {
                emit("close");
            }
        });
    } else {
        emit("close");
    }
};

const submitForm = () => {
    form.put(route("admin.records.update", props.record.id), {
        preserveScroll: true,
        onSuccess: () => {
            hasUnsavedChanges.value = false;
            emit("saved");
            Swal.fire({
                icon: "success",
                title: "Record Updated",
                text: "The water meter record has been successfully updated.",
                timer: 2000,
                showConfirmButton: false,
            });
        },
        onError: () => {
            Swal.fire({
                icon: "error",
                title: "Update Failed",
                text: "Please check the form for errors and try again.",
                timer: 3000,
                showConfirmButton: false,
            });
        },
    });
};
</script>
