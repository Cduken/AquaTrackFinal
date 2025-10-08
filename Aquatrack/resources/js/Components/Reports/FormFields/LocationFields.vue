<template>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">
                Municipality <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                :value="form.municipality"
                readonly
                class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-gray-700 cursor-not-allowed focus:outline-none text-sm font-medium"
            />
        </div>

        <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">
                Province
            </label>
            <input
                type="text"
                :value="form.province"
                readonly
                class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-gray-700 cursor-not-allowed focus:outline-none text-sm font-medium"
            />
        </div>
    </div>
</template>

<script setup>
defineProps({
    form: Object,
});
</script>
