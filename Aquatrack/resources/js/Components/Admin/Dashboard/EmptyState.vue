<!-- Components/Admin/Dashboard/EmptyState.vue -->
<template>
    <div :class="`text-center flex flex-col text-gray-500 ${sizeClasses} ${customClass}`">
        <component
            v-if="iconComponent"
            :is="iconComponent"
            :class="`mx-auto text-gray-300 mb-3 ${iconSizeClasses}`"
        />
        <p class="text-sm">{{ title }}</p>
        <p v-if="description" class="text-xs text-gray-400 mt-1">
            {{ description }}
        </p>
    </div>
</template>

<script setup>
import { AlertCircle, History } from "lucide-vue-next";
import { computed } from "vue";

const props = defineProps({
    icon: String,
    title: String,
    description: String,
    size: {
        type: String,
        default: "md",
    },
    customClass: String,
});

const iconComponent = computed(() => {
    const icons = {
        AlertCircle,
        History,
    };
    return icons[props.icon];
});

const sizeClasses = computed(() => {
    const sizes = {
        sm: "py-6",
        md: "py-12",
        lg: "py-16",
    };
    return sizes[props.size];
});

const iconSizeClasses = computed(() => {
    const sizes = {
        sm: "w-8 h-8",
        md: "w-12 h-12",
        lg: "w-16 h-16",
    };
    return sizes[props.size];
});
</script>
