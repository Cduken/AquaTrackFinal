<template>
    <Navigation />
    <div class="min-h-[80vh]">
        <!-- Left Side - Login Form -->
        <div class="w-full flex items-center  justify-center p-8 bg-white">
            <div class="w-full max-w-md ">
                <!-- Logo & Header -->
                <div class="text-center mb-8">

                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        Welcome to <span class="text-blue-600">AquaTrack</span>
                    </h1>
                    <p class="text-gray-600">Water Management System</p>
                </div>

                <!-- Role Selector Dropdown -->
                <div class="mb-6 relative">
                    <button
                        @click="toggleRoleDropdown"
                        class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors"
                        type="button"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-lg flex items-center justify-center"
                                :class="roleColors[selectedRole].bg"
                            >
                                <v-icon
                                    :name="roleIcons[selectedRole]"
                                    :class="roleColors[selectedRole].icon"
                                    class="text-xl"
                                />
                            </div>
                            <div class="text-left">
                                <p class="text-sm font-medium text-gray-900">{{ roleLabels[selectedRole] }}</p>
                                <p class="text-xs text-gray-500">{{ roleDescriptions[selectedRole] }}</p>
                            </div>
                        </div>
                        <v-icon
                            name="hi-dots-vertical"
                            class="text-gray-400"
                        />
                    </button>

                    <!-- Dropdown Menu -->
                    <transition
                        enter-active-class="transition ease-out duration-200"
                        enter-from-class="opacity-0 translate-y-1"
                        enter-to-class="opacity-100 translate-y-0"
                        leave-active-class="transition ease-in duration-150"
                        leave-from-class="opacity-100 translate-y-0"
                        leave-to-class="opacity-0 translate-y-1"
                    >
                        <div
                            v-if="showRoleDropdown"
                            class="absolute z-10 w-full mt-2 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden"
                        >
                            <button
                                v-for="role in roles"
                                :key="role.value"
                                @click="selectRole(role.value)"
                                type="button"
                                class="w-full flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition-colors"
                                :class="{ 'bg-blue-50': selectedRole === role.value }"
                            >
                                <div
                                    class="w-10 h-10 rounded-lg flex items-center justify-center"
                                    :class="roleColors[role.value].bg"
                                >
                                    <v-icon
                                        :name="role.icon"
                                        :class="roleColors[role.value].icon"
                                        class="text-xl"
                                    />
                                </div>
                                <div class="text-left flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{ role.label }}</p>
                                    <p class="text-xs text-gray-500">{{ role.description }}</p>
                                </div>
                                <v-icon
                                    v-if="selectedRole === role.value"
                                    name="hi-check"
                                    class="text-blue-600"
                                />
                            </button>
                        </div>
                    </transition>
                </div>

                <!-- Verification Form (for Admin/Staff) -->
                <div v-if="requiresVerification && !codeVerified" class="space-y-6 mb-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <v-icon name="hi-lock-closed" class="text-blue-600 text-sm" />
                            </div>
                            <div class="flex-1">
                                <h3 class="text-sm font-medium text-blue-900 mb-1">Verification Required</h3>
                                <p class="text-xs text-blue-700">Enter the {{ roleLabels[selectedRole] }} verification code to proceed</p>
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <input
                            id="verificationCode"
                            v-model="verificationCode"
                            type="password"
                            required
                            class="w-full px-4 py-3 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 peer text-center tracking-widest"
                            placeholder=" "
                        />
                        <label
                            for="verificationCode"
                            class="absolute left-3 -top-2.5 text-sm text-blue-600 bg-white px-1"
                        >
                            Verification Code
                        </label>
                    </div>

                    <div v-if="verificationError" class="text-red-500 text-sm">
                        {{ verificationError }}
                    </div>

                    <button
                        @click="verifyCode"
                        type="button"
                        :disabled="isVerifying"
                        class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium py-3 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 shadow-lg shadow-blue-500/30"
                        :class="{'opacity-75 cursor-not-allowed': isVerifying}"
                    >
                        <template v-if="isVerifying">
                            <div class="spinner-small"></div>
                            Verifying...
                        </template>
                        <template v-else>
                            Verify Code
                        </template>
                    </button>
                </div>

                <!-- Login Form -->
                <form v-if="!requiresVerification || codeVerified" @submit.prevent="submit" class="space-y-6">
                    <!-- Account Number / Email -->
                    <div class="relative">
                        <template v-if="selectedRole === 'customer'">
                            <input
                                id="account_number"
                                v-model="form.account_number"
                                type="text"
                                :disabled="formDisabled"
                                @input="formatAccountNumber"
                                required
                                class="w-full px-4 py-3 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 peer"
                                placeholder=" "
                                :maxlength="11"
                            />
                            <label
                                for="account_number"
                                class="absolute left-3 -top-2.5 text-sm text-blue-600 bg-white px-1"
                            >
                                Account Number
                            </label>
                        </template>
                        <template v-else>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                required
                                class="w-full px-4 py-3 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 peer"
                                placeholder=" "
                                :disabled="formDisabled"
                            />
                            <label
                                for="email"
                                class="absolute left-3 -top-2.5 text-sm text-blue-600 bg-white px-1"
                            >
                                Email Address
                            </label>
                        </template>
                    </div>

                    <!-- Password -->
                    <div class="relative">
                        <input
                            id="password"
                            v-model="form.password"
                            :type="showPassword ? 'text' : 'password'"
                            required
                            class="w-full px-4 py-3 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 peer"
                            placeholder=" "
                            :disabled="formDisabled"
                        />
                        <label
                            for="password"
                            class="absolute left-3 -top-2.5 text-sm text-blue-600 bg-white px-1"
                        >
                            Password
                        </label>
                        <button
                            type="button"
                            class="absolute right-3 top-3.5 text-gray-400 hover:text-gray-600 transition-colors"
                            @click="showPassword = !showPassword"
                        >
                            <v-icon :name="showPassword ? 'bi-eye-slash' : 'bi-eye'" />
                        </button>
                    </div>

                    <!-- Lockout Warning -->
                    <div
                        v-if="lockoutSeconds > 0"
                        class="border-l-4 border-red-500 bg-red-50 p-4 rounded-r-lg"
                    >
                        <div class="flex items-start">
                            <v-icon name="hi-exclamation" class="text-red-500 mr-3 mt-0.5" />
                            <div>
                                <h3 class="text-red-800 font-medium text-sm">Security Lockout</h3>
                                <p class="text-red-700 text-sm mt-1">
                                    Please wait <span class="font-semibold">{{ lockoutSeconds }}s</span> before trying again.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        :disabled="form.processing || formDisabled || isSubmitting"
                        class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium py-3 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 shadow-lg shadow-blue-500/30"
                        :class="{'opacity-75 cursor-not-allowed': form.processing || formDisabled || isSubmitting}"
                    >
                        <template v-if="form.processing || isSubmitting">
                            <div class="spinner-small"></div>
                            Signing in...
                        </template>
                        <template v-else>
                            <v-icon name="md-login-outlined" class="text-lg" />
                            Sign In
                        </template>
                    </button>
                </form>

                <!-- Help Text -->
                <div class="mt-6 text-sm text-blue-800 bg-blue-50 border border-blue-200 p-4 rounded-lg">
                    <p class="font-medium mb-1">Need Help?</p>
                    <p>Contact the Systems Development & Administration Office for password recovery or account assistance.</p>
                </div>
            </div>
        </div>


    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { OhVueIcon, addIcons } from "oh-vue-icons";
import {
    HiUser, HiCog, HiUsers, HiDotsVertical, BiEye, BiEyeSlash,
    MdLoginOutlined, HiLockClosed, HiExclamation, IoWater, HiCheck
} from "oh-vue-icons/icons";
import Navigation from "@/Components/Header/Navigation.vue";
import Swal from "sweetalert2";
import axios from "axios";

addIcons(HiUser, HiCog, HiUsers, HiDotsVertical, BiEye, BiEyeSlash, MdLoginOutlined, HiLockClosed, HiExclamation, IoWater, HiCheck);

const selectedRole = ref("customer");
const showPassword = ref(false);
const showRoleDropdown = ref(false);
const verificationCode = ref("");
const verificationError = ref("");
const isVerifying = ref(false);
const isSubmitting = ref(false);
const formDisabled = ref(false);
const lockoutSeconds = ref(0);
const codeVerified = ref(false);
let lockoutTimer = null;

const securedRoles = ["admin", "staff"];
const requiresVerification = computed(() =>
    securedRoles.includes(selectedRole.value.toLowerCase())
);

const roles = [
    { value: 'customer', label: 'Concessioner', description: 'Report and track water issues', icon: 'hi-user' },
    { value: 'admin', label: 'Administrator', description: 'Manage system and users', icon: 'hi-cog' },
    { value: 'staff', label: 'Staff', description: 'Handle meter readings', icon: 'hi-users' },
];

const roleLabels = {
    customer: 'Concessioner',
    admin: 'Administrator',
    staff: 'Staff'
};

const roleDescriptions = {
    customer: 'Report and track water issues',
    admin: 'Manage system and users',
    staff: 'Handle meter readings'
};

const roleIcons = {
    customer: 'hi-user',
    admin: 'hi-cog',
    staff: 'hi-users'
};

const roleColors = {
    customer: { bg: 'bg-blue-100', icon: 'text-blue-600' },
    admin: { bg: 'bg-purple-100', icon: 'text-purple-600' },
    staff: { bg: 'bg-teal-100', icon: 'text-teal-600' }
};

const form = useForm({
    email: "",
    account_number: "",
    password: "",
    remember: false,
    role: "customer",
});

const toggleRoleDropdown = () => {
    showRoleDropdown.value = !showRoleDropdown.value;
};

const selectRole = (role) => {
    selectedRole.value = role;
    form.role = role.toLowerCase();
    showRoleDropdown.value = false;
    codeVerified.value = false;
    verificationCode.value = "";
    verificationError.value = "";
    form.reset();
};

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
    const dropdown = event.target.closest('.relative');
    if (!dropdown) {
        showRoleDropdown.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});

watch(selectedRole, (newRole) => {
    form.role = newRole.toLowerCase();
});

const page = usePage();
watch(
    () => page.props.flash,
    (flash) => {
        if (flash.logout_success) {
            Swal.fire({
                position: "top-end",
                icon: "success",
                title: "Logged Out",
                text: "You have been successfully logged out.",
                showConfirmButton: false,
                timer: 2000,
                toast: true,
            });
        }
    },
    { immediate: true }
);

const updateCsrfToken = (newToken) => {
    const metaTag = document.querySelector('meta[name="csrf-token"]');
    if (metaTag && newToken) metaTag.setAttribute("content", newToken);
};

const getCsrfToken = () =>
    document.querySelector('meta[name="csrf-token"]')?.content || "";

const verifyCode = async () => {
    verificationError.value = "";
    isVerifying.value = true;
    const startTime = Date.now();

    try {
        await new Promise((resolve) => setTimeout(resolve, 2000));

        const response = await axios.post(
            route("verify-code"),
            {
                role: selectedRole.value.toLowerCase(),
                code: verificationCode.value,
            },
            {
                headers: {
                    "X-CSRF-TOKEN": getCsrfToken(),
                    "X-Requested-With": "XMLHttpRequest",
                },
            }
        );

        if (response.data.csrf_token) {
            updateCsrfToken(response.data.csrf_token);
        }

        if (response.data.verified) {
            codeVerified.value = true;
            Swal.fire({
                position: "top-end",
                icon: "success",
                title: "Verification Successful!",
                text: `You can now proceed to login as ${roleLabels[selectedRole.value]}`,
                showConfirmButton: false,
                timer: 2000,
                toast: true,
                background: "#f8f9fa",
                width: "400px",
                customClass: { title: "text-lg font-medium" },
            });
        }
    } catch (error) {
        if (error.response?.status === 419) {
            updateCsrfToken(error.response.data.csrf_token);
            verificationError.value =
                "Session expired. Please refresh the page and try again.";
            Swal.fire({
                position: "top-end",
                icon: "error",
                title: "Session Expired",
                text: verificationError.value,
                showConfirmButton: true,
                confirmButtonText: "Refresh",
                toast: true,
                background: "#f8f9fa",
                width: "400px",
                customClass: { title: "text-lg font-medium" },
            }).then((result) => {
                if (result.isConfirmed) window.location.reload();
            });
        } else {
            verificationError.value =
                error.response?.data?.message ||
                error.response?.data?.errors?.code?.[0] ||
                "The verification code you entered is incorrect";
            Swal.fire({
                position: "top-end",
                icon: "error",
                title: "Verification Failed",
                text: verificationError.value,
                showConfirmButton: false,
                timer: 2000,
                toast: true,
                background: "#f8f9fa",
                width: "400px",
                customClass: { title: "text-lg font-medium" },
            });
        }
    } finally {
        const elapsed = Date.now() - startTime;
        const minimumDelay = 2000;
        if (elapsed < minimumDelay) {
            await new Promise((r) => setTimeout(r, minimumDelay - elapsed));
        }
        isVerifying.value = false;
    }
};

const formatAccountNumber = (event) => {
    let input = event.target.value.replace(/[^A-Z0-9-]/gi, "").toUpperCase();
    let cleanInput = input.replace(/-/g, "");

    if (cleanInput.length >= 8) {
        let formatted = cleanInput.slice(0, 3) + "-" + cleanInput.slice(3, 5) + "-" + cleanInput.slice(5, 8);
        if (cleanInput.length >= 9) {
            formatted += cleanInput.slice(8, 9);
        }
        form.account_number = formatted;
    } else if (cleanInput.length > 5) {
        form.account_number = cleanInput.slice(0, 3) + "-" + cleanInput.slice(3, 5) + "-" + cleanInput.slice(5);
    } else if (cleanInput.length > 3) {
        form.account_number = cleanInput.slice(0, 3) + "-" + cleanInput.slice(3, 5);
    } else {
        form.account_number = cleanInput;
    }
};

const submit = async () => {
    isSubmitting.value = true;
    await new Promise((resolve) => setTimeout(resolve, 2000));

    form.post(route("login"), {
        preserveState: true,
        preserveScroll: true,
        onFinish: () => {
            form.reset("password");
            isSubmitting.value = false;
        },
        onSuccess: () => {
            const userName = page.props.auth?.user?.name || "there";
            Swal.fire({
                position: "top-end",
                icon: "success",
                title: `Welcome back, ${userName}!`,
                text: `You've successfully logged in as ${roleLabels[selectedRole.value] || "customer"}`,
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
                toast: true,
                background: "#f8f9fa",
                width: "300px",
                customClass: {
                    title: "text-sm font-medium",
                    content: "text-xs",
                    popup: "p-2",
                },
            });
        },
        onError: (errors) => {
            isSubmitting.value = false;

            if (errors.throttle) {
                lockoutSeconds.value = errors.remaining_time || 30;
                formDisabled.value = true;

                if (lockoutTimer) clearInterval(lockoutTimer);
                lockoutTimer = setInterval(() => {
                    lockoutSeconds.value--;
                    if (lockoutSeconds.value <= 0) {
                        clearInterval(lockoutTimer);
                        formDisabled.value = false;
                    }
                }, 1000);
                return;
            }

            let errorMessage =
                errors.email ||
                errors.account_number ||
                "Invalid credentials. Please try again";
            Swal.fire({
                position: "top-end",
                icon: "error",
                title: "Login Failed",
                text: errorMessage,
                showConfirmButton: false,
                timer: 3000,
                toast: true,
                width: "300px",
                customClass: {
                    title: "text-sm",
                    htmlContainer: "text-xs",
                },
            });
        },
    });
};
</script>

<style scoped>
.spinner-small {
    width: 16px;
    height: 16px;
    border: 2px solid transparent;
    border-top: 2px solid currentColor;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
