<template>
    <transition name="modal">
        <div v-if="show" class="fixed inset-0 z-[1000] overflow-y-auto">
            <!-- Backdrop -->
            <div
                class="absolute inset-0 bg-black/60 transition-opacity duration-300"
                @click="emit('close')"
            ></div>
            <!-- Modal Container -->
            <div class="flex min-h-full items-center justify-center p-4">
                <!-- Modal Panel -->
                <div
                    class="relative w-full transform transition-all duration-300"
                    :class="isMaximized ? 'max-w-full h-full' : 'max-w-4xl'"
                >
                    <div
                        class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl overflow-hidden flex flex-col"
                        :class="
                            isMaximized
                                ? 'h-screen w-full'
                                : 'max-h-[90vh] w-full'
                        "
                    >
                        <!-- Header -->
                        <div class="relative px-4 py-4 border-b">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="p-2.5 bg-blue-100 rounded-xl">
                                        <v-icon
                                            name="bi-person"
                                            class="text-blue-600"
                                            scale="1.2"
                                        />
                                    </div>
                                    <div>
                                        <h2
                                            class="text-xl font-semibold text-gray-900 dark:text-white ml-3"
                                        >
                                            User Details
                                        </h2>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <!-- Maximize Button -->
                                    <button
                                        @click="toggleMaximize"
                                        class="p-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-all duration-200"
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
                                        @click="emit('close')"
                                        class="p-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-all duration-200"
                                    >
                                        <component
                                            :is="XIcon"
                                            class="w-5 h-5"
                                        />
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div
                            class="flex-1 overflow-y-auto p-6"
                            :class="isMaximized ? 'p-8' : ''"
                        >
                            <div
                                v-if="user"
                                class="space-y-6 mx-auto"
                                :class="
                                    isMaximized
                                        ? 'max-w-full w-full px-4'
                                        : 'max-w-4xl'
                                "
                            >
                                <!-- Profile Header -->
                                <div
                                    class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-6 border border-blue-100 dark:border-blue-800"
                                >
                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <div
                                                    class="h-20 w-20 rounded-full border-4 border-white dark:border-gray-800 shadow-lg overflow-hidden bg-white"
                                                >
                                                    <img
                                                        v-if="user.avatar_url"
                                                        :src="user.avatar_url"
                                                        :alt="user.name"
                                                        class="h-full w-full object-cover"
                                                    />
                                                    <div
                                                        v-else
                                                        class="h-full w-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-xl"
                                                    >
                                                        {{
                                                            userInitials(
                                                                user.name
                                                            )
                                                        }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="ml-6">
                                                <h2
                                                    class="text-2xl font-bold text-gray-900 dark:text-white"
                                                >
                                                    {{ user.name }}
                                                    {{ user.lastname }}
                                                </h2>
                                                <p
                                                    class="text-gray-600 dark:text-gray-400 mt-1"
                                                >
                                                    {{ user.email }}
                                                </p>
                                                <div
                                                    class="flex items-center mt-3 space-x-3"
                                                >
                                                    <span
                                                        :class="[
                                                            roleClasses(
                                                                user.role
                                                            ),
                                                            'inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold',
                                                        ]"
                                                    >
                                                        <v-icon
                                                            :name="
                                                                roleIcon(
                                                                    user.role
                                                                )
                                                            "
                                                            class="w-4 h-4 mr-1"
                                                        />
                                                        {{
                                                            capitalizeRole(
                                                                user.role
                                                            )
                                                        }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span
                                                :class="[
                                                    statusClasses(user.enabled),
                                                    'inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold',
                                                ]"
                                            >
                                                <v-icon
                                                    :name="
                                                        user.enabled
                                                            ? 'bi-check-circle'
                                                            : 'bi-x-circle'
                                                    "
                                                    class="w-4 h-4 mr-1"
                                                />
                                                {{
                                                    user.enabled
                                                        ? "Active"
                                                        : "Inactive"
                                                }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Profile Information -->
                                <div class="space-y-4">
                                    <div class="flex items-center space-x-2">
                                        <v-icon
                                            name="bi-person-badge"
                                            class="text-blue-600"
                                        />
                                        <h3
                                            class="text-lg font-semibold text-gray-900 dark:text-white"
                                        >
                                            Personal Information
                                        </h3>
                                    </div>
                                    <div
                                        class="grid gap-4"
                                        :class="
                                            isMaximized
                                                ? 'grid-cols-1 xl:grid-cols-4 lg:grid-cols-2'
                                                : 'grid-cols-1 md:grid-cols-2'
                                        "
                                    >
                                        <div
                                            class="flex items-start space-x-3 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg"
                                        >
                                            <div
                                                class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg"
                                            >
                                                <v-icon
                                                    name="bi-person"
                                                    class="text-blue-600 dark:text-blue-400"
                                                />
                                            </div>
                                            <div>
                                                <p
                                                    class="text-sm text-gray-500 dark:text-gray-400"
                                                >
                                                    Full Name
                                                </p>
                                                <p
                                                    class="font-medium text-gray-900 dark:text-white"
                                                >
                                                    {{ user.name }}
                                                    {{ user.lastname || "" }}
                                                </p>
                                            </div>
                                        </div>
                                        <div
                                            class="flex items-start space-x-3 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg"
                                        >
                                            <div
                                                class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg"
                                            >
                                                <v-icon
                                                    name="bi-telephone"
                                                    class="text-blue-600 dark:text-blue-400"
                                                />
                                            </div>
                                            <div>
                                                <p
                                                    class="text-sm text-gray-500 dark:text-gray-400"
                                                >
                                                    Phone Number
                                                </p>
                                                <p
                                                    class="font-medium text-gray-900 dark:text-white"
                                                >
                                                    {{ user.phone || "N/A" }}
                                                </p>
                                            </div>
                                        </div>
                                        <div
                                            class="flex items-start space-x-3 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg"
                                        >
                                            <div
                                                class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg"
                                            >
                                                <v-icon
                                                    name="bi-tag"
                                                    class="text-blue-600 dark:text-blue-400"
                                                />
                                            </div>
                                            <div>
                                                <p
                                                    class="text-sm text-gray-500 dark:text-gray-400"
                                                >
                                                    Account Number
                                                </p>
                                                <p
                                                    class="font-medium text-gray-900 dark:text-white"
                                                >
                                                    {{
                                                        user.account_number ||
                                                        "N/A"
                                                    }}
                                                </p>
                                            </div>
                                        </div>
                                        <div
                                            class="flex items-start space-x-3 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg"
                                        >
                                            <div
                                                class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg"
                                            >
                                                <v-icon
                                                    name="bi-person-check"
                                                    class="text-blue-600 dark:text-blue-400"
                                                />
                                            </div>
                                            <div>
                                                <p
                                                    class="text-sm text-gray-500 dark:text-gray-400"
                                                >
                                                    User ID
                                                </p>
                                                <p
                                                    class="font-medium text-gray-900 dark:text-white"
                                                >
                                                    {{ user.id }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Water Meter Information -->
                                <div class="space-y-4">
                                    <div class="flex items-center space-x-2">
                                        <v-icon
                                            name="bi-droplet"
                                            class="text-blue-600"
                                        />
                                        <h3
                                            class="text-lg font-semibold text-gray-900 dark:text-white"
                                        >
                                            Water Meter Information
                                        </h3>
                                    </div>
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-2 gap-4"
                                    >
                                        <div
                                            class="flex items-start space-x-3 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg"
                                        >
                                            <div
                                                class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg"
                                            >
                                                <v-icon
                                                    name="bi-upc-scan"
                                                    class="text-blue-600 dark:text-blue-400"
                                                />
                                            </div>
                                            <div>
                                                <p
                                                    class="text-sm text-gray-500 dark:text-gray-400"
                                                >
                                                    Serial Number
                                                </p>
                                                <p
                                                    class="font-medium text-gray-900 dark:text-white"
                                                >
                                                    {{
                                                        user.serial_number ||
                                                        "N/A"
                                                    }}
                                                </p>
                                            </div>
                                        </div>
                                        <div
                                            class="flex items-start space-x-3 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg"
                                        >
                                            <div
                                                class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg"
                                            >
                                                <v-icon
                                                    name="bi-rulers"
                                                    class="text-blue-600 dark:text-blue-400"
                                                />
                                            </div>
                                            <div>
                                                <p
                                                    class="text-sm text-gray-500 dark:text-gray-400"
                                                >
                                                    Meter Size
                                                </p>
                                                <p
                                                    class="font-medium text-gray-900 dark:text-white"
                                                >
                                                    {{ user.size || "N/A" }}
                                                </p>
                                            </div>
                                        </div>
                                        <div
                                            class="flex items-start space-x-3 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg"
                                        >
                                            <div
                                                class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg"
                                            >
                                                <v-icon
                                                    name="bi-building"
                                                    class="text-blue-600 dark:text-blue-400"
                                                />
                                            </div>
                                            <div>
                                                <p
                                                    class="text-sm text-gray-500 dark:text-gray-400"
                                                >
                                                    Brand
                                                </p>
                                                <p
                                                    class="font-medium text-gray-900 dark:text-white"
                                                >
                                                    {{ user.brand || "N/A" }}
                                                </p>
                                            </div>
                                        </div>
                                        <div
                                            class="flex items-start space-x-3 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg"
                                        >
                                            <div
                                                class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg"
                                            >
                                                <v-icon
                                                    name="bi-calendar-check"
                                                    class="text-blue-600 dark:text-blue-400"
                                                />
                                            </div>
                                            <div>
                                                <p
                                                    class="text-sm text-gray-500 dark:text-gray-400"
                                                >
                                                    Date Installed
                                                </p>
                                                <p
                                                    class="font-medium text-gray-900 dark:text-white"
                                                >
                                                    {{
                                                        user.date_installed
                                                            ? formatDate(
                                                                  user.date_installed
                                                              )
                                                            : "N/A"
                                                    }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Location Information -->
                                <div class="space-y-4">
                                    <div class="flex items-center space-x-2">
                                        <v-icon
                                            name="bi-geo-alt"
                                            class="text-blue-600"
                                        />
                                        <h3
                                            class="text-lg font-semibold text-gray-900 dark:text-white"
                                        >
                                            Location Information
                                        </h3>
                                    </div>
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-2 gap-4"
                                    >
                                        <div
                                            class="flex items-start space-x-3 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg"
                                        >
                                            <div
                                                class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg"
                                            >
                                                <v-icon
                                                    name="bi-geo"
                                                    class="text-blue-600 dark:text-blue-400"
                                                />
                                            </div>
                                            <div>
                                                <p
                                                    class="text-sm text-gray-500 dark:text-gray-400"
                                                >
                                                    Zone
                                                </p>
                                                <p
                                                    class="font-medium text-gray-900 dark:text-white"
                                                >
                                                    {{ user.zone || "N/A" }}
                                                </p>
                                            </div>
                                        </div>
                                        <div
                                            class="flex items-start space-x-3 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg"
                                        >
                                            <div
                                                class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg"
                                            >
                                                <v-icon
                                                    name="bi-signpost"
                                                    class="text-blue-600 dark:text-blue-400"
                                                />
                                            </div>
                                            <div>
                                                <p
                                                    class="text-sm text-gray-500 dark:text-gray-400"
                                                >
                                                    Barangay
                                                </p>
                                                <p
                                                    class="font-medium text-gray-900 dark:text-white"
                                                >
                                                    {{ user.barangay || "N/A" }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup>
import { ref, watch } from "vue";
import { XIcon, Maximize2Icon, Minimize2Icon } from "lucide-vue-next";

const props = defineProps({
    show: {
        type: Boolean,
        required: true,
    },
    user: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(["close"]);

const isMaximized = ref(false);

watch(
    () => props.show,
    (newVal) => {
        if (!newVal) {
            isMaximized.value = false;
        }
    }
);

const toggleMaximize = () => {
    isMaximized.value = !isMaximized.value;
};

const userInitials = (name) => {
    if (!name) return "?";
    return name
        .split(" ")
        .map((n) => n[0])
        .join("")
        .toUpperCase()
        .substring(0, 2);
};

const roleClasses = (role) => {
    return {
        "bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200":
            role === "admin",
        "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200":
            role === "customer",
        "bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200":
            role === "staff",
    };
};

const statusClasses = (enabled) => {
    return {
        "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200":
            enabled,
        "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200": !enabled,
    };
};

const roleIcon = (role) => {
    if (role === "admin") return "bi-shield-check";
    if (role === "staff") return "bi-person-circle";
    if (role === "customer") return "bi-people";
    return "bi-person";
};

const capitalizeRole = (role) => {
    if (!role) return "";
    return role.charAt(0).toUpperCase() + role.slice(1);
};

const formatDate = (dateString) => {
    if (!dateString) return "N/A";
    return new Date(dateString).toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};
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

/* Content slide-in animation */
.content-enter-active {
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1) 0.1s;
}

.content-enter-from {
    opacity: 0;
    transform: translateY(30px);
}

/* Form elements stagger animation */
.form-item {
    transition: all 0.3s ease;
}

.modal-enter-active .form-item:nth-child(1) {
    transition-delay: 0.1s;
}
.modal-enter-active .form-item:nth-child(2) {
    transition-delay: 0.15s;
}
.modal-enter-active .form-item:nth-child(3) {
    transition-delay: 0.2s;
}
.modal-enter-active .form-item:nth-child(4) {
    transition-delay: 0.25s;
}
.modal-enter-active .form-item:nth-child(5) {
    transition-delay: 0.3s;
}

/* Maximize transition */
.maximize-enter-active,
.maximize-leave-active {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.maximize-enter-from,
.maximize-leave-to {
    opacity: 0;
    transform: scale(0.9);
}

/* Password preview fade transition */
.fade-enter-active,
.fade-leave-active {
    transition: all 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(-10px);
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
    border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
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

/* Remove default select arrow in some browsers */
select {
    background-image: none;
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

/* Loading spinner animation */
@keyframes spin-slow {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}

.animate-spin-slow {
    animation: spin-slow 1.5s linear infinite;
}

/* Pulse animation for loading states */
@keyframes pulse-subtle {
    0%,
    100% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
}

.animate-pulse-subtle {
    animation: pulse-subtle 2s ease-in-out infinite;
}
</style>
