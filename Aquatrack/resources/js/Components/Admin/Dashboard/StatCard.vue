<template>
    <div
        class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-all duration-300"
    >
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">
                    {{ title }}
                </p>
                <p class="text-2xl font-bold text-gray-900">
                    {{ value }}
                </p>
                <p class="text-xs text-gray-400 mt-1">
                    {{ description }}
                </p>
            </div>
            <div :class="`p-3 rounded-lg ${iconBgColor} ${iconColor}`">
                <component :is="iconComponent" class="w-6 h-6" />
            </div>
        </div>
    </div>
</template>

<script setup>
import {
    Users,
    UserCog,
    Users2,
    Flag,
    DollarSign,
    Package,
    ShoppingCart,
} from "lucide-vue-next";
import { computed } from "vue";

const props = defineProps({
    title: String,
    value: [String, Number],
    description: String,
    icon: String,
    iconBgColor: {
        type: String,
        default: "bg-gray-50",
    },
    iconColor: {
        type: String,
        default: "text-gray-500",
    },
});

const iconMap = {
    users: Users,
    staff: UserCog,
    customers: Users2,
    reports: Flag,
    dollar: DollarSign,
    products: Package,
    orders: ShoppingCart,
};

const iconComponent = computed(() => iconMap[props.icon] || Users);
</script>
