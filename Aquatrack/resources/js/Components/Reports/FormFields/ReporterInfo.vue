<template>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">
                Full Name <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                :value="form.reporter_name"
                @input="updateField('reporter_name', $event.target.value)"
                required
                class="w-full px-4 py-3 rounded-xl border border-gray-300 text-gray-900 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm bg-white"
                placeholder="Enter your full name"
            />
            <p
                v-if="form.errors.reporter_name"
                class="text-xs text-red-500 mt-1"
            >
                {{ form.errors.reporter_name }}
            </p>
        </div>

        <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">
                Phone Number <span class="text-gray-400 text-xs"> (Optional) </span>
            </label>
            <input
                type="tel"
                :value="form.reporter_phone"
                @input="restrictPhoneInput($event)"
                class="w-full px-4 py-3 rounded-xl border border-gray-300 text-gray-900 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm bg-white"
                pattern="[0-9]{1,11}"
                maxlength="11"
                placeholder="Your contact number"
            />
            <p
                v-if="form.errors.reporter_phone"
                class="text-xs text-red-500 mt-1"
            >
                {{ form.errors.reporter_phone }}
            </p>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    form: Object,
});

const emit = defineEmits(["update:field"]);

const updateField = (field, value) => {
    emit("update:field", { field, value });
};

const restrictPhoneInput = (event) => {
    let value = event.target.value.replace(/[^0-9]/g, "");
    if (value.length > 11) {
        value = value.slice(0, 11);
    }
    emit("update:field", { field: "reporter_phone", value });
};
</script>
