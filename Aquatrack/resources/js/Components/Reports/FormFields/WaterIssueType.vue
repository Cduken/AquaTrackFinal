<template>
    <div class="space-y-2">
        <label class="block text-sm font-semibold text-gray-700">
            Water Issue Type <span class="text-red-500">*</span>
        </label>
        <select
            :value="form.water_issue_type"
            @change="selectWaterIssue($event)"
            class="w-full px-4 py-3 rounded-xl border border-gray-300 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm bg-white"
            required
        >
            <option disabled value="">Select water issue type</option>
            <option
                v-for="issue in waterIssueTypes"
                :key="issue"
                :value="issue"
            >
                {{ issue }}
            </option>
            <option value="others">Others (please specify)</option>
        </select>

        <div v-if="form.water_issue_type === 'others'" class="mt-3">
            <input
                type="text"
                :value="form.custom_water_issue"
                @input="updateField('custom_water_issue', $event.target.value)"
                class="w-full px-4 py-3 rounded-xl border border-gray-300 text-gray-900 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm bg-white"
                placeholder="Please specify the water issue"
                maxlength="100"
                required
            />
        </div>

        <p
            v-if="form.errors.water_issue_type"
            class="text-xs text-red-500 mt-1"
        >
            {{ form.errors.water_issue_type }}
        </p>
    </div>
</template>

<script setup>
const props = defineProps({
    form: Object
});

const emit = defineEmits(['update:field']);

const waterIssueTypes = [
    "Burst pipe",
    "Rusty water",
    "Low water pressure",
    "No water supply",
    "Clogged pipes",
    "Smelly water",
    "Cloudy or dirty water",
    "Hot water issues",
    "Running toilet",
];

const selectWaterIssue = (event) => {
    const value = event.target.value || "";
    emit('update:field', { field: 'water_issue_type', value });
    if (value !== "others") {
        emit('update:field', { field: 'custom_water_issue', value: "" });
    }
};

const updateField = (field, value) => {
    emit('update:field', { field, value });
};
</script>
