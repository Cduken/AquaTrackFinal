<template>
    <div v-if="show" class="fixed inset-0 overflow-y-auto z-50">
        <div
            class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0"
        >
            <!-- Background overlay -->
            <div
                class="fixed inset-0 transition-opacity bg-black/50"
                @click="closeModal"
            ></div>

            <!-- Modal panel -->
            <div
                class="relative inline-block w-full max-w-2xl px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 shadow-xl sm:my-8 sm:align-middle sm:p-6"
            >
                <!-- Header -->
                <div class="mb-6">
                    <h2
                        class="text-lg font-medium text-gray-900 dark:text-white"
                    >
                        {{
                            isEditing
                                ? "Edit Water Rate"
                                : "Create New Water Rate"
                        }}
                    </h2>
                </div>

                <form @submit.prevent="submitForm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <!-- Tier Name -->
                        <div>
                            <label
                                for="name"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                            >
                                Tier Name
                            </label>
                            <input
                                id="name"
                                type="text"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                v-model="form.name"
                                required
                                autofocus
                                placeholder="e.g., Tier 1, Residential Basic"
                            />
                            <div
                                v-if="form.errors.name"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.name }}
                            </div>
                        </div>

                        <!-- Order -->
                        <div>
                            <label
                                for="order"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                            >
                                Application Order
                            </label>
                            <input
                                id="order"
                                type="number"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                v-model="form.order"
                                required
                                min="0"
                            />
                            <div
                                v-if="form.errors.order"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.order }}
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <!-- Min Consumption -->
                        <div>
                            <label
                                for="min_consumption"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                            >
                                Minimum Consumption (m³)
                            </label>
                            <input
                                id="min_consumption"
                                type="number"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                v-model="form.min_consumption"
                                required
                                min="0"
                            />
                            <div
                                v-if="form.errors.min_consumption"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.min_consumption }}
                            </div>
                        </div>

                        <!-- Max Consumption -->
                        <div>
                            <label
                                for="max_consumption"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                            >
                                Maximum Consumption (m³)
                            </label>
                            <input
                                id="max_consumption"
                                type="number"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                v-model="form.max_consumption"
                                min="0"
                                placeholder="Leave empty for unlimited"
                            />
                            <div
                                v-if="form.errors.max_consumption"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.max_consumption }}
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <!-- Fixed Charge -->
                        <div>
                            <label
                                for="fixed_charge"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                            >
                                Fixed Charge (₱)
                            </label>
                            <input
                                id="fixed_charge"
                                type="number"
                                step="0.01"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                v-model="form.fixed_charge"
                                required
                                min="0"
                            />
                            <div
                                v-if="form.errors.fixed_charge"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.fixed_charge }}
                            </div>
                        </div>

                        <!-- Rate per Cubic -->
                        <div>
                            <label
                                for="rate_per_cubic"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                            >
                                Rate per Cubic Meter (₱)
                            </label>
                            <input
                                id="rate_per_cubic"
                                type="number"
                                step="0.01"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                v-model="form.rate_per_cubic"
                                required
                                min="0"
                            />
                            <div
                                v-if="form.errors.rate_per_cubic"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.rate_per_cubic }}
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="flex items-center mb-6">
                        <input
                            id="is_active"
                            type="checkbox"
                            class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                            v-model="form.is_active"
                        />
                        <label
                            for="is_active"
                            class="ml-2 text-sm text-gray-700 dark:text-gray-300"
                        >
                            Active Rate
                        </label>
                    </div>

                    <div
                        class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-600"
                    >
                        <button
                            type="button"
                            @click="closeModal"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{ isEditing ? "Update Rate" : "Create Rate" }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import { watch } from "vue";

const props = defineProps({
    show: Boolean,
    rate: Object,
    isEditing: Boolean,
});

const emit = defineEmits(["close", "saved"]);

const form = useForm({
    name: "",
    min_consumption: 0,
    max_consumption: null,
    rate_per_cubic: 0,
    fixed_charge: 0,
    order: 0,
    is_active: true,
});

// Watch for rate changes and update form
watch(
    () => props.rate,
    (newRate) => {
        if (newRate) {
            form.name = newRate.name;
            form.min_consumption = newRate.min_consumption;
            form.max_consumption = newRate.max_consumption;
            form.rate_per_cubic = newRate.rate_per_cubic;
            form.fixed_charge = newRate.fixed_charge;
            form.order = newRate.order;
            form.is_active = newRate.is_active;
        }
    },
    { immediate: true }
);

const closeModal = () => {
    form.reset();
    form.clearErrors();
    emit("close");
};

const submitForm = () => {
    if (props.isEditing) {
        form.put(route("admin.water-rates.update", props.rate.id), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
                emit("saved");
            },
        });
    } else {
        form.post(route("admin.water-rates.store"), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
                emit("saved");
            },
        });
    }
};
</script>
