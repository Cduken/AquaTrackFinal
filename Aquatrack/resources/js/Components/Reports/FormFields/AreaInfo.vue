<template>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">
                Barangay <span class="text-red-500">*</span>
            </label>
            <select
                :value="form.barangay"
                @change="updateField('barangay', $event.target.value)"
                required
                class="w-full px-4 py-3 rounded-xl border border-gray-300 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm bg-white"
            >
                <option value="" disabled selected>Select Barangay</option>
                <option
                    v-for="barangay in allBarangays"
                    :key="barangay"
                    :value="barangay"
                >
                    {{ barangay }}
                </option>
            </select>
            <p
                v-if="form.errors.barangay"
                class="text-xs text-red-500 mt-1"
            >
                {{ form.errors.barangay }}
            </p>
        </div>

        <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">
                Zone <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                :value="form.zone"
                readonly
                class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-gray-700 cursor-not-allowed focus:outline-none text-sm font-medium"
                placeholder="Zone will be auto-filled"
            />
            <p
                v-if="form.errors.zone"
                class="text-xs text-red-500 mt-1"
            >
                {{ form.errors.zone }}
            </p>
        </div>
    </div>

    <div class="space-y-2">
        <label class="block text-sm font-semibold text-gray-700">
            Purok/Street <span class="text-red-500">*</span>
        </label>
        <input
            type="text"
            :value="form.purok"
            @input="updateField('purok', $event.target.value)"
            required
            class="w-full px-4 py-3 rounded-xl border border-gray-300 text-gray-900 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm bg-white"
            placeholder="Enter purok number or street name"
        />
        <p v-if="form.errors.purok" class="text-xs text-red-500 mt-1">
            {{ form.errors.purok }}
        </p>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    form: Object,
    zones: Object
});

const emit = defineEmits(['update:field']);

const allBarangays = computed(() => {
    return Object.values(props.zones).flat();
});

const updateField = (field, value) => {
    emit('update:field', { field, value });
};
</script>
