//EditRecords.vue
<template>
    <transition name="modal">
        <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
            <!-- Overlay -->
            <div
                class="fixed inset-0 bg-black/60 transition-opacity duration-300"
                @click="confirmClose"
            />

            <!-- Modal Container -->
            <div class="flex min-h-full items-center justify-center p-0">
                <div
                    class="relative w-full transform transition-all duration-300"
                    :class="isMaximized ? 'w-full h-full' : 'max-w-4xl'"
                >
                    <div
                        class="bg-white dark:bg-gray-900 shadow-2xl overflow-hidden flex flex-col"
                        :class="
                            isMaximized
                                ? 'h-screen rounded-none'
                                : 'max-h-[90vh] rounded-lg'
                        "
                    >
                        <!-- Header -->
                        <div class="border-b px-6 py-4 flex-shrink-0">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="flex items-center">

                                    <SquarePen class="w-6 h-6 mr-2 text-black" />
                                        <h2
                                            class="text-xl font-semibold text-black"
                                        >
                                            Edit Water Meter Record
                                        </h2>

                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <!-- Maximize Button -->
                                    <button
                                        @click="toggleMaximize"
                                        class="p-2 text-black hover:text-blue-600 hover:bg-gray-200/10 transition-all duration-200"
                                        :title="
                                            isMaximized
                                                ? 'Minimize'
                                                : 'Maximize'
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
                                        class="p-2 text-black hover:text-gray-600 hover:bg-gray-200/10 transition-all duration-200"
                                    >
                                        <component
                                            :is="XIcon"
                                            class="w-5 h-5"
                                        />
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Loading State -->
                        <div
                            v-if="loading"
                            class="flex-1 flex items-center justify-center py-16"
                        >
                            <div class="text-center">
                                <div
                                    class="w-12 h-12 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"
                                ></div>
                                <p
                                    class="text-gray-600 dark:text-gray-400 font-medium"
                                >
                                    Loading record details...
                                </p>
                            </div>
                        </div>

                        <!-- Content -->
                        <div
                            v-else-if="record"
                            class="flex-1 overflow-y-auto"
                            :class="isMaximized ? 'p-8' : 'p-6'"
                        >
                            <form
                                @submit.prevent="submitForm"
                                class="space-y-6"
                                :class="
                                    isMaximized
                                        ? 'w-full max-w-7xl mx-auto'
                                        : ''
                                "
                            >
                                <!-- Processing Overlay -->


                                <!-- Customer Profile Banner -->
                                <div
                                    class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 p-6 border border-blue-100 dark:border-blue-800"
                                    :class="isMaximized ? '' : 'rounded-lg'"
                                >
                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <!-- Customer Info (Left side) -->
                                        <div
                                            class="flex items-center space-x-3"
                                        >
                                            <div
                                                class="w-12 h-12 rounded-full border-2 border-white dark:border-gray-800 shadow-lg overflow-hidden bg-white"
                                            >
                                                <img
                                                    v-if="
                                                        record.user.avatar_url
                                                    "
                                                    :src="
                                                        record.user.avatar_url
                                                    "
                                                    :alt="record.user.name"
                                                    class="w-full h-full object-cover"
                                                />
                                                <div
                                                    v-else
                                                    class="w-full h-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg"
                                                >
                                                    {{
                                                        getUserInitials(
                                                            record.user
                                                        )
                                                    }}
                                                </div>
                                            </div>
                                            <div>
                                                <p
                                                    class="text-sm text-gray-500 dark:text-gray-400"
                                                >
                                                    Customer
                                                </p>
                                                <p
                                                    class="text-lg font-semibold text-gray-900 dark:text-white"
                                                >
                                                    {{ record.user.name }}
                                                    {{ record.user.lastname }}
                                                </p>
                                                <p
                                                    class="text-sm text-gray-600 dark:text-gray-400"
                                                >
                                                    {{
                                                        record.user
                                                            .account_number
                                                    }}
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Current Amount (Right side) -->
                                        <div
                                            class="flex items-center space-x-3"
                                        >
                                            <div class="text-right">
                                                <p
                                                    class="text-sm text-gray-500 dark:text-gray-400"
                                                >
                                                    Current Amount
                                                </p>
                                                <p
                                                    class="text-lg font-semibold text-gray-900 dark:text-white"
                                                >
                                                    ₱{{ record.amount }}
                                                </p>
                                            </div>
                                            <div
                                                class="p-3 bg-white dark:bg-gray-800 shadow-sm"
                                            >
                                                <component
                                                    :is="CreditCardIcon"
                                                    class="text-green-600 dark:text-green-400 w-6 h-6"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Grid Layout for Maximized View -->
                                <div
                                    :class="
                                        isMaximized
                                            ? 'grid grid-cols-2 gap-6'
                                            : 'space-y-6'
                                    "
                                >
                                    <!-- Record Information -->
                                    <div
                                        class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm"
                                        :class="isMaximized ? '' : 'rounded-lg'"
                                    >
                                        <div
                                            class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 border-b border-gray-200 dark:border-gray-600"
                                        >
                                            <h3
                                                class="text-lg font-semibold text-gray-900 dark:text-white flex items-center"
                                            >
                                                <component
                                                    :is="UserIcon"
                                                    class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2"
                                                />
                                                Record Information
                                            </h3>
                                        </div>
                                        <div class="p-6">
                                            <div class="space-y-4">
                                                <div class="space-y-2">
                                                    <label
                                                        class="text-sm text-gray-500 dark:text-gray-400 font-medium block"
                                                        >Customer Name</label
                                                    >
                                                    <p
                                                        class="text-base font-medium text-gray-900 dark:text-white p-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600"
                                                    >
                                                        {{ record.user.name }}
                                                        {{
                                                            record.user.lastname
                                                        }}
                                                    </p>
                                                </div>
                                                <div class="space-y-2">
                                                    <label
                                                        class="text-sm text-gray-500 dark:text-gray-400 font-medium block"
                                                        >Account Number</label
                                                    >
                                                    <p
                                                        class="text-base font-medium text-gray-900 dark:text-white p-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600"
                                                    >
                                                        {{
                                                            record.user
                                                                .account_number
                                                        }}
                                                    </p>
                                                </div>
                                                <div class="space-y-2">
                                                    <label
                                                        class="text-sm text-gray-500 dark:text-gray-400 font-medium block"
                                                        >Reading Date</label
                                                    >
                                                    <p
                                                        class="text-base font-medium text-gray-900 dark:text-white p-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600"
                                                    >
                                                        {{
                                                            formatDate(
                                                                record.reading_date
                                                            )
                                                        }}
                                                    </p>
                                                </div>
                                                <div class="space-y-2">
                                                    <label
                                                        class="text-sm text-gray-500 dark:text-gray-400 font-medium block"
                                                        >Due Date</label
                                                    >
                                                    <p
                                                        class="text-base font-medium text-gray-900 dark:text-white p-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600"
                                                    >
                                                        {{ dueDate }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Meter Reading Details -->
                                    <div
                                        class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm"
                                        :class="isMaximized ? '' : 'rounded-lg'"
                                    >
                                        <div
                                            class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-600"
                                        >
                                            <h3
                                                class="text-lg font-semibold text-gray-900 dark:text-white flex items-center"
                                            >
                                                <component
                                                    :is="GaugeIcon"
                                                    class="w-5 h-5 text-green-600 dark:text-green-400 mr-2"
                                                />
                                                Meter Reading Details
                                            </h3>
                                        </div>
                                        <div class="p-6">
                                            <div class="space-y-4">
                                                <div class="space-y-2">
                                                    <label
                                                        for="current-reading"
                                                        class="text-sm text-gray-500 dark:text-gray-400 font-medium block"
                                                    >
                                                        Current Reading (m³)
                                                        <span
                                                            class="text-red-500"
                                                            >*</span
                                                        >
                                                    </label>
                                                    <div class="relative">
                                                        <input
                                                            id="current-reading"
                                                            type="number"
                                                            v-model="
                                                                form.reading
                                                            "
                                                            class="w-full p-3 pl-11 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                                            step="0.01"
                                                            min="0"
                                                            required
                                                            placeholder="0.00"
                                                            @input="
                                                                validateReading
                                                            "
                                                        />
                                                        <div
                                                            class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"
                                                        >
                                                            <component
                                                                :is="GaugeIcon"
                                                                class="w-5 h-5"
                                                            />
                                                        </div>
                                                    </div>
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
                                                <div class="space-y-2">
                                                    <label
                                                        class="text-sm text-gray-500 dark:text-gray-400 font-medium block"
                                                        >Consumption (m³)</label
                                                    >
                                                    <p
                                                        class="text-base font-medium text-gray-900 dark:text-white p-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600"
                                                    >
                                                        {{
                                                            record.consumption
                                                        }}
                                                        m³
                                                    </p>
                                                </div>
                                                <div class="space-y-2">
                                                    <label
                                                        for="amount"
                                                        class="text-sm text-gray-500 dark:text-gray-400 font-medium block"
                                                    >
                                                        Amount (₱)
                                                        <span
                                                            class="text-red-500"
                                                            >*</span
                                                        >
                                                    </label>
                                                    <div class="relative">
                                                        <input
                                                            id="amount"
                                                            type="number"
                                                            v-model="
                                                                form.amount
                                                            "
                                                            class="w-full p-3 pl-11 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                                            step="0.01"
                                                            min="0"
                                                            required
                                                            placeholder="0.00"
                                                            @input="
                                                                validateAmount
                                                            "
                                                        />
                                                        <div
                                                            class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"
                                                        >
                                                            <component
                                                                :is="
                                                                    CreditCardIcon
                                                                "
                                                                class="w-5 h-5"
                                                            />
                                                        </div>
                                                    </div>
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
                                        class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm"
                                        :class="
                                            isMaximized
                                                ? 'col-span-2'
                                                : 'rounded-lg'
                                        "
                                    >
                                        <div
                                            class="bg-gradient-to-r from-purple-50 to-blue-50 dark:from-purple-900/20 dark:to-blue-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-600"
                                        >
                                            <h3
                                                class="text-lg font-semibold text-gray-900 dark:text-white flex items-center"
                                            >
                                                <component
                                                    :is="CreditCardIcon"
                                                    class="w-5 h-5 text-purple-600 dark:text-purple-400 mr-2"
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
                                                        class="flex flex-col items-center p-6 border-2 border-gray-200 dark:border-gray-600 cursor-pointer transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700/50 peer-checked:border-2"
                                                        :class="
                                                            statusOption.classes
                                                        "
                                                    >
                                                        <component
                                                            :is="
                                                                statusOption.iconComponent
                                                            "
                                                            class="w-8 h-8 mb-3"
                                                        />
                                                        <span
                                                            class="font-semibold text-gray-900 dark:text-white text-lg"
                                                        >
                                                            {{
                                                                statusOption.label
                                                            }}
                                                        </span>
                                                        <span
                                                            class="text-sm text-gray-500 dark:text-gray-400 text-center mt-2"
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
                                                class="text-red-500 text-sm mt-3"
                                            >
                                                {{ form.errors.status }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div
                                    class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700"
                                >
                                    <button
                                        type="button"
                                        @click="confirmClose"
                                        class="px-8 py-3 text-base font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-all duration-200 disabled:opacity-50"
                                        :disabled="form.processing"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        :disabled="form.processing"
                                        class="px-8 py-3 text-base font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 flex items-center shadow-lg shadow-blue-500/30"
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
import { ref, computed, watch, onMounted, onUnmounted } from "vue";
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
    SquarePen,
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
        classes:
            "peer-checked:border-green-500 peer-checked:bg-green-50 dark:peer-checked:bg-green-900/20 text-green-600",
    },
    {
        value: "Pending",
        label: "Pending",
        description: "Awaiting payment confirmation",
        iconComponent: ClockIcon,
        classes:
            "peer-checked:border-yellow-500 peer-checked:bg-yellow-50 dark:peer-checked:bg-yellow-900/20 text-yellow-600",
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

// Handle body overflow
const updateBodyOverflow = () => {
    if (isMaximized.value) {
        document.body.style.overflow = "hidden";
    } else {
        document.body.style.overflow = "";
    }
};

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
    updateBodyOverflow();
};

const formatDate = (dateString) => {
    if (!dateString) return "N/A";
    const options = { year: "numeric", month: "long", day: "numeric" };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

const getUserInitials = (user) => {
    if (!user?.name) return "?";
    return user.name
        .split(" ")
        .map((n) => n[0])
        .join("")
        .toUpperCase()
        .substring(0, 2);
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
            confirmButtonColor: "#3b82f6",
        }).then((result) => {
            if (result.isConfirmed) {
                document.body.style.overflow = "";
                emit("close");
            }
        });
    } else {
        document.body.style.overflow = "";
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
                toast: true,
                position: "top-end",
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

// Cleanup on unmount
onUnmounted(() => {
    document.body.style.overflow = "";
});
</script>

<style scoped>
/* Smooth modal transitions */
.modal-enter-active,
.modal-leave-active {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.modal-enter-from {
    opacity: 0;
}

.modal-leave-to {
    opacity: 0;
}

.modal-enter-active .modal-container,
.modal-leave-active .modal-container {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.modal-enter-from .modal-container {
    opacity: 0;
    transform: scale(0.95) translateY(-20px);
}

.modal-leave-to .modal-container {
    opacity: 0;
    transform: scale(0.95) translateY(20px);
}

/* Backdrop transition */
.modal-enter-active .backdrop,
.modal-leave-active .backdrop {
    transition: opacity 0.3s ease;
}

.modal-enter-from .backdrop,
.modal-leave-to .backdrop {
    opacity: 0;
}

/* Custom scrollbar */
.overflow-y-auto {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f1f5f9;
}

.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Dark mode scrollbar */
.dark .overflow-y-auto {
    scrollbar-color: #475569 #1e293b;
}

.dark .overflow-y-auto::-webkit-scrollbar-track {
    background: #1e293b;
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb {
    background: #475569;
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #64748b;
}

/* Smooth button hover effects */
button {
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Input focus animations */
input:focus,
select:focus {
    transition: all 0.2s ease;
}

/* Radio button selection animation */
input[type="radio"]:checked + div {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
