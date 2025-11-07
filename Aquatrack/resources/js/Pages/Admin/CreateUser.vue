<template>
    <AdminLayout>
        <div class="mx-auto w-full max-w-6xl">
            <!-- Header Section -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                            Register New User
                        </h1>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            Create a new staff member or customer account
                        </p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button
                            @click="goBack"
                            class="px-6 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>

            <!-- Form Section -->
            <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700">
                <form @submit.prevent="handleSubmit">
                    <div class="p-8 space-y-8">
                        <!-- Role Selection -->
                        <div class="space-y-4">
                            <div class="flex items-center space-x-2">
                                <Users class="w-5 h-5 text-gray-700 dark:text-gray-300" />
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    User Type
                                </h3>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <label
                                    class="relative flex items-center p-4 border border-gray-300 dark:border-gray-600 cursor-pointer transition-all"
                                    :class="
                                        userData.role === 'staff'
                                            ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                                            : 'border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500'
                                    "
                                >
                                    <input
                                        type="radio"
                                        v-model="userData.role"
                                        value="staff"
                                        class="sr-only"
                                    />
                                    <div class="flex items-center space-x-3 flex-1">
                                        <div
                                            class="p-2"
                                            :class="
                                                userData.role === 'staff'
                                                    ? 'bg-blue-100 dark:bg-blue-800'
                                                    : 'bg-gray-100 dark:bg-gray-800'
                                            "
                                        >
                                            <Briefcase
                                                class="w-5 h-5"
                                                :class="
                                                    userData.role === 'staff'
                                                        ? 'text-blue-600 dark:text-blue-400'
                                                        : 'text-gray-600 dark:text-gray-400'
                                                "
                                            />
                                        </div>
                                        <div class="flex-1">
                                            <p
                                                class="font-medium"
                                                :class="
                                                    userData.role === 'staff'
                                                        ? 'text-blue-900 dark:text-blue-100'
                                                        : 'text-gray-900 dark:text-white'
                                                "
                                            >
                                                Staff Member
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                Internal employee
                                            </p>
                                        </div>
                                        <CheckCircle2
                                            v-if="userData.role === 'staff'"
                                            class="w-5 h-5 text-blue-600 dark:text-blue-400"
                                        />
                                    </div>
                                </label>
                                <label
                                    class="relative flex items-center p-4 border border-gray-300 dark:border-gray-600 cursor-pointer transition-all"
                                    :class="
                                        userData.role === 'customer'
                                            ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                                            : 'border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500'
                                    "
                                >
                                    <input
                                        type="radio"
                                        v-model="userData.role"
                                        value="customer"
                                        class="sr-only"
                                    />
                                    <div class="flex items-center space-x-3 flex-1">
                                        <div
                                            class="p-2"
                                            :class="
                                                userData.role === 'customer'
                                                    ? 'bg-blue-100 dark:bg-blue-800'
                                                    : 'bg-gray-100 dark:bg-gray-800'
                                            "
                                        >
                                            <User
                                                class="w-5 h-5"
                                                :class="
                                                    userData.role === 'customer'
                                                        ? 'text-blue-600 dark:text-blue-400'
                                                        : 'text-gray-600 dark:text-gray-400'
                                                "
                                            />
                                        </div>
                                        <div class="flex-1">
                                            <p
                                                class="font-medium"
                                                :class="
                                                    userData.role === 'customer'
                                                        ? 'text-blue-900 dark:text-blue-100'
                                                        : 'text-gray-900 dark:text-white'
                                                "
                                            >
                                                Customer
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                Service subscriber
                                            </p>
                                        </div>
                                        <CheckCircle2
                                            v-if="userData.role === 'customer'"
                                            class="w-5 h-5 text-blue-600 dark:text-blue-400"
                                        />
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Personal Information -->
                        <div class="space-y-4">
                            <div class="flex items-center space-x-2">
                                <UserCircle class="w-5 h-5 text-gray-700 dark:text-gray-300" />
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    Personal Information
                                </h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        First Name
                                    </label>
                                    <div class="relative">
                                        <User class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                                        <input
                                            v-model="userData.name"
                                            type="text"
                                            required
                                            class="w-full pl-11 pr-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                            placeholder="Enter First Name"
                                        />
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Last Name
                                    </label>
                                    <div class="relative">
                                        <User class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                                        <input
                                            v-model="userData.lastname"
                                            type="text"
                                            required
                                            class="w-full pl-11 pr-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                            placeholder="Enter Last Name"
                                            @input="updateGeneratedPassword"
                                        />
                                    </div>
                                </div>
                                <div v-if="userData.role === 'staff'">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Email Address
                                    </label>
                                    <div class="relative">
                                        <Mail class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                                        <input
                                            v-model="userData.email"
                                            type="email"
                                            :required="userData.role === 'staff'"
                                            class="w-full pl-11 pr-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                            placeholder="Enter Email Address"
                                        />
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Phone Number
                                    </label>
                                    <div class="relative">
                                        <Phone class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                                        <input
                                            v-model="userData.phone"
                                            type="tel"
                                            class="w-full pl-11 pr-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                            placeholder="Enter Phone Number (Optional)"
                                            @input="validatePhoneNumber"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Specific Fields -->
                        <div v-if="userData.role === 'customer'" class="space-y-8">
                            <!-- Equipment Information -->
                            <div class="space-y-4">
                                <div class="flex items-center space-x-2">
                                    <Package class="w-5 h-5 text-gray-700 dark:text-gray-300" />
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        Equipment Information
                                    </h3>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Account Number
                                        </label>
                                        <div class="relative">
                                            <CreditCard class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                                            <input
                                                v-model="userData.accountNumber"
                                                type="text"
                                                required
                                                class="w-full pl-11 pr-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                                placeholder="Enter Account Number"
                                                @input="formatAccountNumber"
                                                :maxlength="11"
                                            />
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1.5">
                                            Format: XXX-XX-XXX or XXX-XX-XXXX
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Installation Date
                                        </label>
                                        <div class="relative">
                                            <Calendar class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                                            <input
                                                v-model="userData.date_installed"
                                                type="date"
                                                required
                                                class="w-full pl-11 pr-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                            />
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Brand
                                        </label>
                                        <div class="relative">
                                            <Tag class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                                            <input
                                                v-model="userData.brand"
                                                type="text"
                                                required
                                                class="w-full pl-11 pr-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                                placeholder="Enter Brand Name"
                                            />
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Serial Number
                                        </label>
                                        <div class="relative">
                                            <Hash class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                                            <input
                                                v-model="userData.serial_number"
                                                type="text"
                                                required
                                                class="w-full pl-11 pr-16 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                                placeholder="Enter Serial Number"
                                                @input="validateSerialNumber"
                                                maxlength="9"
                                            />
                                            <div class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-medium text-gray-500">
                                                {{ userData.serial_number ? userData.serial_number.length : 0 }}/9
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Size
                                        </label>
                                        <div class="relative">
                                            <Ruler class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                                            <input
                                                v-model="userData.size"
                                                type="text"
                                                required
                                                class="w-full pl-11 pr-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                                placeholder="Size"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Location -->
                            <div class="space-y-4">
                                <div class="flex items-center space-x-2">
                                    <MapPin class="w-5 h-5 text-gray-700 dark:text-gray-300" />
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        Location
                                    </h3>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Zone
                                        </label>
                                        <div class="relative">
                                            <MapPin class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 z-10 pointer-events-none" />
                                            <select
                                                v-model="userData.zone"
                                                @change="updateBarangays"
                                                required
                                                class="w-full pl-11 pr-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all appearance-none"
                                            >
                                                <option value="" disabled>Select zone</option>
                                                <option
                                                    v-for="(zone, index) in Object.keys(zones)"
                                                    :key="index"
                                                    :value="zone"
                                                >
                                                    {{ zone }}
                                                </option>
                                            </select>
                                            <ChevronDown class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" />
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Barangay
                                        </label>
                                        <div class="relative">
                                            <MapPin class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 z-10 pointer-events-none" />
                                            <select
                                                v-model="userData.barangay"
                                                required
                                                class="w-full pl-11 pr-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all appearance-none disabled:opacity-50 disabled:cursor-not-allowed"
                                                :disabled="!userData.zone"
                                            >
                                                <option value="" disabled>Select barangay</option>
                                                <option
                                                    v-for="(barangay, index) in filteredBarangays"
                                                    :key="index"
                                                    :value="barangay"
                                                >
                                                    {{ barangay }}
                                                </option>
                                            </select>
                                            <ChevronDown class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Password Preview -->
                        <div
                            v-if="userData.lastname"
                            class="relative bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-200 dark:border-blue-700 p-6"
                        >
                            <div class="flex items-start space-x-3">
                                <div class="p-2 bg-blue-100 dark:bg-blue-800">
                                    <Key class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-blue-900 dark:text-blue-100">
                                        Generated Password Preview
                                    </p>
                                    <p class="text-lg font-mono font-medium text-gray-900 dark:text-white mt-2">
                                        {{ generatedPasswordPreview }}_XXXX
                                    </p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                        XXXX will be replaced with user ID after creation
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="flex items-center justify-end space-x-3 px-8 py-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                        <button
                            @click="goBack"
                            type="button"
                            class="px-6 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200"
                        >
                            Cancel
                        </button>
                        <button
                            @click="handleSubmit"
                            :disabled="isSubmitting"
                            type="button"
                            class="px-6 py-3 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 flex items-center space-x-2"
                        >
                            <Loader2
                                v-if="isSubmitting"
                                class="w-4 h-4 animate-spin"
                            />
                            <UserPlus v-else class="w-4 h-4" />
                            <span>{{ isSubmitting ? "Creating..." : "Create User" }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import {
    User,
    Users,
    Mail,
    Phone,
    CreditCard,
    Calendar,
    Tag,
    Hash,
    Ruler,
    MapPin,
    Key,
    UserCircle,
    Package,
    ChevronDown,
    CheckCircle2,
    Briefcase,
    UserPlus,
    Loader2,
} from "lucide-vue-next";

const props = defineProps({
    zones: {
        type: Object,
        required: true,
    },
});

const userData = ref({
    name: "",
    lastname: "",
    accountNumber: "",
    email: "",
    phone: "",
    role: "customer",
    zone: "",
    barangay: "",
    date_installed: "",
    brand: "",
    serial_number: "",
    size: "",
});

const isSubmitting = ref(false);
const filteredBarangays = ref([]);

const updateBarangays = () => {
    if (userData.value.zone && props.zones[userData.value.zone]) {
        filteredBarangays.value = props.zones[userData.value.zone];
    } else {
        filteredBarangays.value = [];
    }
    userData.value.barangay = "";
};

const formatAccountNumber = (e) => {
    let input = e.target.value.replace(/[^A-Z0-9-]/gi, "").toUpperCase();
    let cleanInput = input.replace(/-/g, "");

    if (cleanInput.length >= 8) {
        let formatted =
            cleanInput.slice(0, 3) +
            "-" +
            cleanInput.slice(3, 5) +
            "-" +
            cleanInput.slice(5, 8);

        if (cleanInput.length >= 9) {
            formatted += cleanInput.slice(8, 9);
        }

        userData.value.accountNumber = formatted;
    } else if (cleanInput.length > 5) {
        userData.value.accountNumber =
            cleanInput.slice(0, 3) +
            "-" +
            cleanInput.slice(3, 5) +
            "-" +
            cleanInput.slice(5);
    } else if (cleanInput.length > 3) {
        userData.value.accountNumber =
            cleanInput.slice(0, 3) + "-" + cleanInput.slice(3, 5);
    } else {
        userData.value.accountNumber = cleanInput;
    }
};

const validatePhoneNumber = (e) => {
    let value = e.target.value;

    if (!value || value.trim() === "") {
        userData.value.phone = "";
        return;
    }

    value = value.replace(/[^0-9+]/g, "");

    if (value.startsWith("+")) {
        value = "+" + value.substring(1).replace(/\D/g, "").substring(0, 11);
    } else {
        value = value.substring(0, 11);
    }

    userData.value.phone = value;

    const phoneInput = e.target;
    const cleanPhone = value.replace(/\D/g, "");
    const phoneRegex = /^(\+63\d{10}|09\d{9})$/;

    if (cleanPhone && !phoneRegex.test(cleanPhone)) {
        phoneInput.classList.add("border-red-500");
        phoneInput.classList.remove("border-gray-300");
    } else {
        phoneInput.classList.remove("border-red-500");
        phoneInput.classList.add("border-gray-300");
    }
};

const validateSerialNumber = (e) => {
    let value = e.target.value;
    value = value.replace(/\D/g, "");
    value = value.substring(0, 9);
    userData.value.serial_number = value;
};

const generatedPasswordPreview = computed(() => {
    if (userData.value.lastname.length >= 3) {
        return userData.value.lastname.substring(0, 3).toUpperCase();
    }
    return userData.value.lastname.toUpperCase();
});

const updateGeneratedPassword = () => {
    // For visual feedback only
};

const goBack = () => {
    router.get(route('admin.users'));
};

const handleSubmit = async () => {
    isSubmitting.value = true;

    try {
        if (userData.value.role === "customer") {
            const cleanAccountNumber = userData.value.accountNumber.replace(/-/g, "");
            if (cleanAccountNumber.length < 8 || cleanAccountNumber.length > 9) {
                Swal.fire({
                    icon: "error",
                    title: "Validation Error",
                    text: "Account number must be exactly 8 or 9 characters (format: XXX-XX-XXX or XXX-XX-XXXX)",
                });
                isSubmitting.value = false;
                return;
            }

            const accountNumberRegex = /^[0-9]{3}-[0-9]{2}-[0-9]{3}[A-Za-z]?$/;
            if (!accountNumberRegex.test(userData.value.accountNumber)) {
                Swal.fire({
                    icon: "error",
                    title: "Validation Error",
                    text: "Account number must be in format 123-12-123 or 123-12-123A",
                });
                isSubmitting.value = false;
                return;
            }

            if (userData.value.serial_number.length !== 9) {
                Swal.fire({
                    icon: "error",
                    title: "Validation Error",
                    text: "Serial number must be exactly 9 digits",
                });
                isSubmitting.value = false;
                return;
            }

            if (
                !userData.value.accountNumber ||
                !userData.value.date_installed ||
                !userData.value.brand ||
                !userData.value.size ||
                !userData.value.zone ||
                !userData.value.barangay
            ) {
                Swal.fire({
                    icon: "error",
                    title: "Validation Error",
                    text: "Please fill all required customer fields",
                });
                isSubmitting.value = false;
                return;
            }
        } else if (userData.value.role === "staff") {
            if (!userData.value.email) {
                Swal.fire({
                    icon: "error",
                    title: "Validation Error",
                    text: "Email is required for staff members",
                });
                isSubmitting.value = false;
                return;
            }
        }

        if (userData.value.phone && userData.value.phone.trim() !== "") {
            const phoneRegex = /^(\+63\d{10}|09\d{9})$/;
            const cleanPhone = userData.value.phone.replace(/\D/g, "");

            if (!phoneRegex.test(cleanPhone)) {
                Swal.fire({
                    icon: "error",
                    title: "Validation Error",
                    text: "Phone number must be in format 09123456789 or +639123456789",
                });
                isSubmitting.value = false;
                return;
            }
        }

        const submitData = {
            name: userData.value.name,
            lastname: userData.value.lastname,
            email: userData.value.email,
            phone: userData.value.phone ? userData.value.phone.replace(/\D/g, "") : null,
            role: userData.value.role,
        };

        if (userData.value.role === "customer") {
            submitData.account_number = userData.value.accountNumber;
            submitData.zone = userData.value.zone;
            submitData.barangay = userData.value.barangay;
            submitData.date_installed = userData.value.date_installed;
            submitData.brand = userData.value.brand;
            submitData.serial_number = userData.value.serial_number;
            submitData.size = userData.value.size;
        } else {
            submitData.zone = null;
            submitData.barangay = null;
            submitData.date_installed = null;
            submitData.brand = null;
            submitData.serial_number = null;
            submitData.size = null;
        }

        router.post(route("admin.users.store"), submitData, {
            onSuccess: (response) => {
                const generatedPassword = response.props.flash?.generated_password;

                if (generatedPassword) {
                    showPasswordModal(generatedPassword);
                } else {
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        text: "User created successfully!",
                        timer: 3000,
                        showConfirmButton: false,
                    }).then(() => {
                        router.get(route('admin.users'));
                    });
                }
            },
            onError: (errors) => {
                let errorMessage = "Failed to create user. Please check the form and try again.";

                if (errors && typeof errors === "object") {
                    const firstErrorKey = Object.keys(errors)[0];
                    if (firstErrorKey && errors[firstErrorKey]) {
                        errorMessage = Array.isArray(errors[firstErrorKey])
                            ? errors[firstErrorKey][0]
                            : errors[firstErrorKey];
                    }
                } else if (errors && errors.message) {
                    errorMessage = errors.message;
                } else if (typeof errors === "string") {
                    errorMessage = errors;
                }

                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: errorMessage,
                });
            },
        });
    } catch (error) {
        console.error("Error in handleSubmit:", error);
        Swal.fire({
            icon: "error",
            title: "Submission Error",
            text: "An error occurred while submitting the form",
        });
    } finally {
        isSubmitting.value = false;
    }
};

const showPasswordModal = (password) => {
    Swal.fire({
        icon: "success",
        title: "User created successfully!",
        html: `
            <div class="text-left flex items-center flex-col justify-center">
                <p class="mb-2 font-medium">Generated Password:</p>
                <input
                    id="swal-password"
                    class="swal2-input text-center font-mono text-lg"
                    value="${password}"
                    readonly
                    style="width: 70%; padding: 0.5rem;"
                />
                <p class="mt-2 text-sm text-gray-500">Please provide this password to the user</p>
            </div>
        `,
        showConfirmButton: true,
        confirmButtonText: "Copy Password",
        showCancelButton: true,
        cancelButtonText: "Close",
        focusConfirm: false,
        customClass: {
            popup: "!text-left",
            confirmButton: "!bg-blue-600 !hover:bg-blue-700",
        },
        didOpen: () => {
            const confirmBtn = Swal.getConfirmButton();
            if (confirmBtn) {
                confirmBtn.addEventListener("click", () => {
                    navigator.clipboard.writeText(password);
                    Swal.fire({
                        icon: "success",
                        title: "Copied!",
                        text: "Password copied to clipboard",
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 2000,
                    }).then(() => {
                        router.get(route('admin.users'));
                    });
                });
            }
        },
    });
};

onMounted(() => {
    // Reset form when component mounts
    userData.value = {
        name: "",
        lastname: "",
        accountNumber: "",
        email: "",
        phone: "",
        role: "customer",
        zone: "",
        barangay: "",
        date_installed: "",
        brand: "",
        serial_number: "",
        size: "",
    };
    filteredBarangays.value = [];
});
</script>

<style scoped>
/* Remove excessive border radius */
input, select, button {
    border-radius: 0;
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
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
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
</style>
