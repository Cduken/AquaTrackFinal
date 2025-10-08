<template>
    <div class="space-y-2">
        <label class="block text-sm font-semibold text-gray-700">
            Description <span class="text-red-500">*</span>
        </label>
        <textarea
            :value="form.description"
            @input="updateField('description', $event.target.value)"
            rows="4"
            required
            class="w-full px-4 py-3 rounded-xl border border-gray-300 text-gray-900 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm bg-white resize-none"
            placeholder="Describe the water quality issue in detail..."
        ></textarea>
        <p
            v-if="form.errors.description"
            class="text-xs text-red-500 mt-1"
        >
            {{ form.errors.description }}
        </p>
    </div>
</template>

<script setup>
const props = defineProps({
    form: Object
});

const emit = defineEmits(['update:field']);

const updateField = (field, value) => {
    emit('update:field', { field, value });
};
</script>
