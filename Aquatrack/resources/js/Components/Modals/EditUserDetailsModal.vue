<template>
    <transition name="modal">
        <div v-if="show" class="fixed inset-0 z-[1000] overflow-hidden">
            <!-- Backdrop -->
            <div
                class="absolute inset-0 bg-black/60  transition-opacity duration-300"
                @click="emit('close')"
            ></div>

            <!-- Modal Container -->
            <div class="fixed inset-0 flex items-center justify-center p-4">
                <!-- Modal Panel with transform for animation -->
                <div
                    class="relative w-full max-w-4xl transform transition-all duration-500 ease-out"
                    :class="{ 'max-w-6xl': isMaximized }"
                >
                    <div
                        class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl overflow-hidden"
                    >
                        <!-- Header -->
                        <div
                            class="relative px-4 py-4 border-b"
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div
                                        class="p-2.5 bg-white/20 rounded-xl backdrop-blur-sm"
                                    >
                                        <UserCog class="w-6 h-6 text-black" />
                                    </div>
                                    <div>
                                        <h2
                                            class="text-xl font-sm text-black"
                                        >
                                            Edit User Details
                                        </h2>

                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <!-- Maximize Button -->
                                    <button
                                        @click="toggleMaximize"
                                        class="p-2 text-black/80 hover:text-gray-700 hover:bg-white/10 rounded-lg transition-all duration-200"
                                        :title="
                                            isMaximized
                                                ? 'Minimize'
                                                : 'Maximize'
                                        "
                                    >
                                        <Maximize2
                                            v-if="!isMaximized"
                                            class="w-5 h-5"
                                        />
                                        <Minimize2 v-else class="w-5 h-5" />
                                    </button>
                                    <!-- Close Button -->
                                    <button
                                        @click="emit('close')"
                                        class="p-2 text-black/80 hover:text-gray-400 hover:bg-white/10 rounded-lg transition-all duration-200"
                                    >
                                        <X class="w-5 h-5" />
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div
                            class="overflow-y-auto p-8"
                            :class="{ 'p-10': isMaximized }"
                            style="max-height: calc(100vh - 200px)"
                        >
                            <!-- Loading State -->
                            <div
                                v-if="editLoading"
                                class="flex flex-col items-center justify-center py-12"
                            >
                                <Loader2
                                    class="w-12 h-12 text-blue-600 animate-spin mb-4"
                                />
                                <p
                                    class="text-gray-600 dark:text-gray-400 font-medium"
                                >
                                    Saving changes...
                                </p>
                            </div>

                            <!-- Error Message -->
                            <div
                                v-if="editErrorMessage"
                                class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl flex items-start space-x-3"
                            >
                                <AlertCircle
                                    class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5"
                                />
                                <div>
                                    <p
                                        class="text-sm font-medium text-red-800 dark:text-red-200"
                                    >
                                        {{ editErrorMessage }}
                                    </p>
                                </div>
                            </div>

                            <div
                                v-if="user && !editLoading"
                                class="space-y-8"
                                :class="{ 'space-y-10': isMaximized }"
                            >
                                <!-- Profile Information -->
                                <div class="space-y-4">
                                    <div class="flex items-center space-x-2">
                                        <UserCircle
                                            class="w-5 h-5 text-gray-700 dark:text-gray-300"
                                        />
                                        <h3
                                            class="text-lg font-semibold text-gray-900 dark:text-white"
                                        >
                                            Profile Information
                                        </h3>
                                    </div>
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-2 gap-5"
                                        :class="{
                                            'md:grid-cols-3': isMaximized,
                                        }"
                                    >
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                            >
                                                First Name
                                            </label>
                                            <div class="relative">
                                                <User
                                                    class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                                                />
                                                <input
                                                    v-model="editFormData.name"
                                                    type="text"
                                                    class="w-full pl-11 pr-4 py-3 border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                                    :class="
                                                        editErrors.name
                                                            ? 'border-red-500'
                                                            : 'border-gray-300 dark:border-gray-600'
                                                    "
                                                    placeholder="John"
                                                />
                                            </div>
                                            <p
                                                v-if="editErrors.name"
                                                class="text-red-500 text-xs mt-1.5"
                                            >
                                                {{ editErrors.name }}
                                            </p>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                            >
                                                Last Name
                                            </label>
                                            <div class="relative">
                                                <User
                                                    class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                                                />
                                                <input
                                                    v-model="
                                                        editFormData.lastname
                                                    "
                                                    type="text"
                                                    class="w-full pl-11 pr-4 py-3 border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                                    :class="
                                                        editErrors.lastname
                                                            ? 'border-red-500'
                                                            : 'border-gray-300 dark:border-gray-600'
                                                    "
                                                    placeholder="Doe"
                                                />
                                            </div>
                                            <p
                                                v-if="editErrors.lastname"
                                                class="text-red-500 text-xs mt-1.5"
                                            >
                                                {{ editErrors.lastname }}
                                            </p>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                            >
                                                Email Address
                                            </label>
                                            <div class="relative">
                                                <Mail
                                                    class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                                                />
                                                <input
                                                    v-model="editFormData.email"
                                                    type="email"
                                                    class="w-full pl-11 pr-4 py-3 border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                                    :class="
                                                        editErrors.email
                                                            ? 'border-red-500'
                                                            : 'border-gray-300 dark:border-gray-600'
                                                    "
                                                    placeholder="john.doe@company.com"
                                                />
                                            </div>
                                            <p
                                                v-if="editErrors.email"
                                                class="text-red-500 text-xs mt-1.5"
                                            >
                                                {{ editErrors.email }}
                                            </p>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                            >
                                                Phone Number
                                            </label>
                                            <div class="relative">
                                                <Phone
                                                    class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                                                />
                                                <input
                                                    v-model="editFormData.phone"
                                                    type="tel"
                                                    class="w-full pl-11 pr-4 py-3 border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                                    :class="
                                                        editErrors.phone
                                                            ? 'border-red-500'
                                                            : 'border-gray-300 dark:border-gray-600'
                                                    "
                                                    placeholder="09XXXXXXXXX or +63XXXXXXXXXX"
                                                />
                                            </div>
                                            <p
                                                v-if="editErrors.phone"
                                                class="text-red-500 text-xs mt-1.5"
                                            >
                                                {{ editErrors.phone }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Account Information -->
                                <div class="space-y-4">
                                    <div class="flex items-center space-x-2">
                                        <Shield
                                            class="w-5 h-5 text-gray-700 dark:text-gray-300"
                                        />
                                        <h3
                                            class="text-lg font-semibold text-gray-900 dark:text-white"
                                        >
                                            Account Information
                                        </h3>
                                    </div>
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-2 gap-5"
                                    >
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                            >
                                                Account Number
                                            </label>
                                            <div class="relative">
                                                <CreditCard
                                                    class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                                                />
                                                <input
                                                    v-model="
                                                        editFormData.account_number
                                                    "
                                                    type="text"
                                                    readonly
                                                    class="w-full pl-11 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 cursor-not-allowed"
                                                    placeholder="XXX-XX-XXX"
                                                />
                                                <Lock
                                                    class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                                                />
                                            </div>
                                            <p
                                                class="text-xs text-gray-500 dark:text-gray-400 mt-1.5"
                                            >
                                                Account number cannot be changed
                                            </p>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                            >
                                                Password Reset
                                            </label>
                                            <button
                                                @click="resetPassword"
                                                :disabled="resetLoading"
                                                class="w-full flex items-center justify-center space-x-2 px-4 py-3 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white rounded-lg font-medium transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-amber-500/30"
                                            >
                                                <Loader2
                                                    v-if="resetLoading"
                                                    class="w-4 h-4 animate-spin"
                                                />
                                                <KeyRound
                                                    v-else
                                                    class="w-4 h-4"
                                                />
                                                <span>{{
                                                    resetLoading
                                                        ? "Resetting..."
                                                        : "Reset Password"
                                                }}</span>
                                            </button>
                                            <p
                                                class="text-xs text-gray-500 dark:text-gray-400 mt-1.5"
                                            >
                                                Reset user's password to default
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Water Meter Information -->
                                <div class="space-y-4">
                                    <div class="flex items-center space-x-2">
                                        <Droplet
                                            class="w-5 h-5 text-gray-700 dark:text-gray-300"
                                        />
                                        <h3
                                            class="text-lg font-semibold text-gray-900 dark:text-white"
                                        >
                                            Water Meter Information
                                        </h3>
                                    </div>
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-2 gap-5"
                                        :class="{
                                            'md:grid-cols-3': isMaximized,
                                        }"
                                    >
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                            >
                                                Serial Number
                                            </label>
                                            <div class="relative">
                                                <Hash
                                                    class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                                                />
                                                <input
                                                    v-model="
                                                        editFormData.serial_number
                                                    "
                                                    type="text"
                                                    maxlength="9"
                                                    @input="restrictToNumbers"
                                                    class="w-full pl-11 pr-16 py-3 border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                                    :class="
                                                        editErrors.serial_number
                                                            ? 'border-red-500'
                                                            : 'border-gray-300 dark:border-gray-600'
                                                    "
                                                    placeholder="123456789"
                                                />
                                                <div
                                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-medium"
                                                    :class="
                                                        editFormData
                                                            .serial_number
                                                            .length === 9
                                                            ? 'text-green-500'
                                                            : 'text-gray-500'
                                                    "
                                                >
                                                    {{
                                                        editFormData
                                                            .serial_number
                                                            .length
                                                    }}/9
                                                </div>
                                            </div>
                                            <p
                                                v-if="editErrors.serial_number"
                                                class="text-red-500 text-xs mt-1.5"
                                            >
                                                {{ editErrors.serial_number }}
                                            </p>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                            >
                                                Meter Size
                                            </label>
                                            <div class="relative">
                                                <Ruler
                                                    class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                                                />
                                                <input
                                                    v-model="editFormData.size"
                                                    type="text"
                                                    class="w-full pl-11 pr-4 py-3 border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                                    :class="
                                                        editErrors.size
                                                            ? 'border-red-500'
                                                            : 'border-gray-300 dark:border-gray-600'
                                                    "
                                                    placeholder="Size"
                                                />
                                            </div>
                                            <p
                                                v-if="editErrors.size"
                                                class="text-red-500 text-xs mt-1.5"
                                            >
                                                {{ editErrors.size }}
                                            </p>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                            >
                                                Brand
                                            </label>
                                            <div class="relative">
                                                <Tag
                                                    class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                                                />
                                                <input
                                                    v-model="editFormData.brand"
                                                    type="text"
                                                    class="w-full pl-11 pr-4 py-3 border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                                    :class="
                                                        editErrors.brand
                                                            ? 'border-red-500'
                                                            : 'border-gray-300 dark:border-gray-600'
                                                    "
                                                    placeholder="Brand name"
                                                />
                                            </div>
                                            <p
                                                v-if="editErrors.brand"
                                                class="text-red-500 text-xs mt-1.5"
                                            >
                                                {{ editErrors.brand }}
                                            </p>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                            >
                                                Installation Date
                                            </label>
                                            <div class="relative">
                                                <Calendar
                                                    class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                                                />
                                                <input
                                                    v-model="
                                                        editFormData.date_installed
                                                    "
                                                    type="date"
                                                    class="w-full pl-11 pr-4 py-3 border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                                    :class="
                                                        editErrors.date_installed
                                                            ? 'border-red-500'
                                                            : 'border-gray-300 dark:border-gray-600'
                                                    "
                                                />
                                            </div>
                                            <p
                                                v-if="editErrors.date_installed"
                                                class="text-red-500 text-xs mt-1.5"
                                            >
                                                {{ editErrors.date_installed }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Location Information -->
                                <div class="space-y-4">
                                    <div class="flex items-center space-x-2">
                                        <MapPin
                                            class="w-5 h-5 text-gray-700 dark:text-gray-300"
                                        />
                                        <h3
                                            class="text-lg font-semibold text-gray-900 dark:text-white"
                                        >
                                            Location Information
                                        </h3>
                                    </div>
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-2 gap-5"
                                    >
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                            >
                                                Zone
                                            </label>
                                            <div class="relative">
                                                <MapPin
                                                    class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 z-10 pointer-events-none"
                                                />
                                                <select
                                                    v-model="editFormData.zone"
                                                    class="w-full pl-11 pr-4 py-3 border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all appearance-none"
                                                    :class="
                                                        editErrors.zone
                                                            ? 'border-red-500'
                                                            : 'border-gray-300 dark:border-gray-600'
                                                    "
                                                >
                                                    <option
                                                        v-for="(
                                                            zone, zoneName
                                                        ) in zones"
                                                        :key="zoneName"
                                                        :value="zoneName"
                                                    >
                                                        {{ zoneName }}
                                                    </option>
                                                </select>
                                                <ChevronDown
                                                    class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none"
                                                />
                                            </div>
                                            <p
                                                v-if="editErrors.zone"
                                                class="text-red-500 text-xs mt-1.5"
                                            >
                                                {{ editErrors.zone }}
                                            </p>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                            >
                                                Barangay
                                            </label>
                                            <div class="relative">
                                                <MapPin
                                                    class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 z-10 pointer-events-none"
                                                />
                                                <select
                                                    v-model="
                                                        editFormData.barangay
                                                    "
                                                    class="w-full pl-11 pr-4 py-3 border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all appearance-none"
                                                    :class="
                                                        editErrors.barangay
                                                            ? 'border-red-500'
                                                            : 'border-gray-300 dark:border-gray-600'
                                                    "
                                                >
                                                    <option
                                                        v-for="barangay in getBarangays(
                                                            editFormData.zone
                                                        )"
                                                        :key="barangay"
                                                        :value="barangay"
                                                    >
                                                        {{ barangay }}
                                                    </option>
                                                </select>
                                                <ChevronDown
                                                    class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none"
                                                />
                                            </div>
                                            <p
                                                v-if="editErrors.barangay"
                                                class="text-red-500 text-xs mt-1.5"
                                            >
                                                {{ editErrors.barangay }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div
                            class="flex items-center justify-end space-x-3 px-8 py-2 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50"
                        >
                            <button
                                @click="emit('close')"
                                type="button"
                                class="px-6 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-all duration-200"
                            >
                                Cancel
                            </button>
                            <button
                                @click="submitForm"
                                :disabled="editLoading"
                                type="button"
                                class="px-6 py-3 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-lg shadow-blue-500/30"
                            >
                                <Loader2
                                    v-if="editLoading"
                                    class="w-4 h-4 animate-spin"
                                />
                                <Save v-else class="w-4 h-4" />
                                <span>{{
                                    editLoading ? "Saving..." : "Save Changes"
                                }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup>
import { ref, reactive, watch } from "vue";
import { router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import {
    User,
    UserCircle,
    Mail,
    Phone,
    CreditCard,
    Calendar,
    Tag,
    Hash,
    Ruler,
    MapPin,
    X,
    Maximize2,
    Minimize2,
    Shield,
    Droplet,
    ChevronDown,
    Lock,
    KeyRound,
    Save,
    Loader2,
    AlertCircle,
    UserCog,
} from "lucide-vue-next";

const props = defineProps({
    show: {
        type: Boolean,
        required: true,
    },
    user: {
        type: Object,
        default: null,
    },
    zones: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(["close", "saved"]);

const isMaximized = ref(false);
const editLoading = ref(false);
const resetLoading = ref(false);
const editErrorMessage = ref("");
const editErrors = reactive({});

const editFormData = reactive({
    name: "",
    lastname: "",
    email: "",
    phone: "",
    account_number: "",
    serial_number: "",
    size: "",
    brand: "",
    date_installed: "",
    zone: "",
    barangay: "",
});

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

watch(
    () => props.user,
    (newUser) => {
        if (newUser) {
            Object.assign(editFormData, {
                name: newUser.name || "",
                lastname: newUser.lastname || "",
                email: newUser.email || "",
                phone: newUser.phone || "",
                account_number: newUser.account_number || "",
                serial_number: newUser.serial_number || "",
                size: newUser.size || "",
                brand: newUser.brand || "",
                date_installed: newUser.date_installed
                    ? formatDateForInput(newUser.date_installed)
                    : "",
                zone: newUser.zone || "",
                barangay: newUser.barangay || "",
            });
        }
    },
    { immediate: true }
);

const formatDateForInput = (dateString) => {
    const date = new Date(dateString);
    return date.toISOString().split("T")[0];
};

const restrictToNumbers = (event) => {
    editFormData.serial_number = editFormData.serial_number.replace(
        /[^0-9]/g,
        ""
    );
};

const getBarangays = (zone) => {
    return props.zones[zone] || [];
};

const resetPassword = async () => {
    if (!props.user?.id) return;

    const { value: newPassword } = await Swal.fire({
        title: "Reset Password",
        input: "text",
        inputLabel: "Enter new password",
        inputPlaceholder: "Enter the new password",
        showCancelButton: true,
        confirmButtonText: "Reset Password",
        cancelButtonText: "Cancel",
        confirmButtonColor: "#3b82f6",
        inputValidator: (value) => {
            if (!value) {
                return "You need to enter a password!";
            }
            if (value.length < 6) {
                return "Password must be at least 6 characters!";
            }
        },
    });

    if (newPassword) {
        resetLoading.value = true;

        try {
            await router.post(
                `/admin/users/${props.user.id}/reset-password`,
                {
                    password: newPassword,
                },
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        Swal.fire({
                            icon: "success",
                            title: "Password Reset",
                            text: "Password has been reset successfully.",
                            toast: true,
                            position: "top-end",
                            showConfirmButton: false,
                            timer: 3000,
                        });
                        resetLoading.value = false;
                    },
                    onError: (err) => {
                        Swal.fire({
                            icon: "error",
                            title: "Reset Failed",
                            text:
                                err.message ||
                                "Failed to reset password. Please try again.",
                        });
                        resetLoading.value = false;
                    },
                }
            );
        } catch (error) {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "An unexpected error occurred.",
            });
            resetLoading.value = false;
        }
    }
};

const submitForm = async () => {
    Swal.fire({
        title: "Save Changes?",
        text: "Do you want to save these changes?",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#3b82f6",
        cancelButtonColor: "#6b7280",
        confirmButtonText: "Yes, save it!",
        cancelButtonText: "Cancel",
    }).then(async (result) => {
        if (result.isConfirmed) {
            editLoading.value = true;
            editErrorMessage.value = "";
            Object.keys(editErrors).forEach((key) => delete editErrors[key]);

            try {
                await router.put(
                    `/admin/users/${props.user.id}`,
                    editFormData,
                    {
                        preserveScroll: true,
                        onSuccess: () => {
                            Swal.fire({
                                icon: "success",
                                title: "Saved!",
                                text: "User information has been updated successfully.",
                                toast: true,
                                position: "top-end",
                                showConfirmButton: false,
                                timer: 3000,
                            });

                            emit("saved");
                            emit("close");
                            editLoading.value = false;
                        },
                        onError: (err) => {
                            console.log("Update errors:", err);
                            if (err.errors) {
                                Object.keys(err.errors).forEach((key) => {
                                    editErrors[key] = err.errors[key];
                                });
                            }
                            editErrorMessage.value =
                                err.message ||
                                "Failed to update user. Please check the form.";
                            editLoading.value = false;
                        },
                    }
                );
            } catch (error) {
                editErrorMessage.value = "An unexpected error occurred.";
                editLoading.value = false;
            }
        }
    });
};
</script>

<style scoped>
/* Modal transition - slide from top to center */
.modal-enter-active {
    transition: opacity 0.3s ease;
}

.modal-leave-active {
    transition: opacity 0.2s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-enter-active > div > div {
    transition: transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1),
        opacity 0.5s ease;
}

.modal-enter-from > div > div {
    transform: translateY(-100%) scale(0.9);
    opacity: 0;
}

.modal-leave-to > div > div {
    transform: scale(0.95);
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

/* Remove default select arrow */
select {
    background-image: none;
}
</style>
