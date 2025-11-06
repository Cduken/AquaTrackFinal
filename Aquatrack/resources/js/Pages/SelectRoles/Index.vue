//Pages/SelectRoles/Index.vue
<template>
    <div
        class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900"
    >
        <Navigation />

        <div class="min-h-[80vh] flex items-center justify-center px-4 py-8">
            <div
                class="w-full max-w-6xl grid grid-cols-1 lg:grid-cols-2 gap-12 items-center"
            >
                <!-- Left Side - Welcome Content -->
                <div class="text-white space-y-6">
                    <div class="space-y-4">
                        <h1
                            class="text-4xl lg:text-5xl font-bold leading-tight"
                        >
                            Login to
                            <span
                                class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-400"
                                >AquaTrack</span
                            >
                        </h1>

                        <p
                            class="text-slate-300 text-lg leading-relaxed max-w-lg"
                        >
                            Welcome to AquaTrack Water Management System. Access
                            your account to manage water services, report
                            issues, and track consumption with ease.
                        </p>

                        <p class="text-slate-400 text-sm max-w-lg">
                            <strong class="text-blue-300">Pro tip:</strong> Make
                            sure you select the correct role and have your
                            verification code ready if required.
                        </p>
                    </div>

                    <!-- Features List -->
                    <div class="space-y-4 pt-4">
                        <div class="flex items-center gap-3 text-slate-300">
                            <div class="w-2 h-2 bg-cyan-400 rounded-full"></div>
                            <span class="text-sm"
                                >Secure role-based access</span
                            >
                        </div>
                        <div class="flex items-center gap-3 text-slate-300">
                            <div class="w-2 h-2 bg-cyan-400 rounded-full"></div>
                            <span class="text-sm"
                                >Real-time issue tracking</span
                            >
                        </div>
                        <div class="flex items-center gap-3 text-slate-300">
                            <div class="w-2 h-2 bg-cyan-400 rounded-full"></div>
                            <span class="text-sm"
                                >Water consumption analytics</span
                            >
                        </div>
                    </div>
                </div>

                <!-- Right Side - Login Form -->
                <div class="w-full">
                    <div
                        class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl shadow-2xl p-8"
                    >
                        <!-- Header -->
                        <div class="text-center mb-8">
                            <div
                                class="w-16 h-16 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg"
                            >
                                <v-icon
                                    name="io-water"
                                    class="text-white text-2xl"
                                />
                            </div>
                            <h2 class="text-2xl font-bold text-white mb-2">
                                Welcome to
                                <span
                                    class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-400"
                                    >AquaTrack</span
                                >
                            </h2>
                            <p class="text-slate-300">
                                Water Management System
                            </p>
                        </div>

                        <!-- Role Selector Dropdown -->
                        <div class="mb-6 relative">
                            <button
                                @click="toggleRoleDropdown"
                                class="w-full flex items-center justify-between px-4 py-4 bg-white/5 border border-white/10 rounded-xl hover:bg-white/10 transition-all duration-300 group"
                                type="button"
                            >
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-12 h-12 rounded-xl flex items-center justify-center backdrop-blur-sm"
                                        :class="roleColors[selectedRole].bg"
                                    >
                                        <v-icon
                                            :name="roleIcons[selectedRole]"
                                            :class="
                                                roleColors[selectedRole].icon
                                            "
                                            class="text-xl"
                                        />
                                    </div>
                                    <div class="text-left">
                                        <p
                                            class="text-sm font-medium text-white"
                                        >
                                            {{ roleLabels[selectedRole] }}
                                        </p>
                                        <p class="text-xs text-slate-400">
                                            {{ roleDescriptions[selectedRole] }}
                                        </p>
                                    </div>
                                </div>
                                <v-icon
                                    name="hi-chevron-down"
                                    class="text-slate-400 group-hover:text-white transition-colors duration-300"
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
                                    class="absolute z-10 w-full mt-2 bg-slate-800/95 backdrop-blur-md border border-white/10 rounded-xl shadow-2xl overflow-hidden"
                                >
                                    <button
                                        v-for="role in roles"
                                        :key="role.value"
                                        @click="selectRole(role.value)"
                                        type="button"
                                        class="w-full flex items-center gap-3 px-4 py-3 hover:bg-white/5 transition-all duration-200 border-b border-white/5 last:border-b-0"
                                        :class="{
                                            'bg-blue-500/20':
                                                selectedRole === role.value,
                                        }"
                                    >
                                        <div
                                            class="w-10 h-10 rounded-lg flex items-center justify-center backdrop-blur-sm"
                                            :class="roleColors[role.value].bg"
                                        >
                                            <v-icon
                                                :name="role.icon"
                                                :class="
                                                    roleColors[role.value].icon
                                                "
                                                class="text-lg"
                                            />
                                        </div>
                                        <div class="text-left flex-1">
                                            <p
                                                class="text-sm font-medium text-white"
                                            >
                                                {{ role.label }}
                                            </p>
                                            <p class="text-xs text-slate-400">
                                                {{ role.description }}
                                            </p>
                                        </div>
                                        <v-icon
                                            v-if="selectedRole === role.value"
                                            name="hi-check"
                                            class="text-cyan-400"
                                        />
                                    </button>
                                </div>
                            </transition>
                        </div>

                        <!-- Verification Form (for Admin/Staff) -->
                        <div
                            v-if="requiresVerification && !codeVerified"
                            class="space-y-6 mb-6"
                        >
                            <div
                                class="bg-blue-500/20 border border-blue-400/30 rounded-xl p-4 backdrop-blur-sm"
                            >
                                <div class="flex items-start gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full bg-blue-500/20 flex items-center justify-center flex-shrink-0"
                                    >
                                        <v-icon
                                            name="hi-lock-closed"
                                            class="text-blue-400 text-sm"
                                        />
                                    </div>
                                    <div class="flex-1">
                                        <h3
                                            class="text-sm font-medium text-blue-300 mb-1"
                                        >
                                            Verification Required
                                        </h3>
                                        <p class="text-xs text-blue-200">
                                            Enter the
                                            {{
                                                roleLabels[selectedRole]
                                            }}
                                            verification code to proceed
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="relative">
                                <input
                                    id="verificationCode"
                                    v-model="verificationCode"
                                    type="password"
                                    required
                                    class="w-full px-4 py-3 text-base bg-white/5 border border-white/10 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent peer text-center tracking-widest text-white placeholder-slate-400"
                                    placeholder="Enter verification code"
                                />
                            </div>

                            <div
                                v-if="verificationError"
                                class="text-red-400 text-sm text-center"
                            >
                                {{ verificationError }}
                            </div>

                            <button
                                @click="verifyCode"
                                type="button"
                                :disabled="isVerifying"
                                class="w-full bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-medium py-3 px-4 rounded-xl transition-all duration-200 flex items-center justify-center gap-2 shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50"
                                :class="{
                                    'opacity-75 cursor-not-allowed':
                                        isVerifying,
                                }"
                            >
                                <template v-if="isVerifying">
                                    <div class="spinner-small"></div>
                                    Verifying...
                                </template>
                                <template v-else> Verify Code </template>
                            </button>
                        </div>

                        <!-- Login Form -->
                        <form
                            v-if="!requiresVerification || codeVerified"
                            @submit.prevent="submit"
                            class="space-y-6"
                        >
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
                                        class="w-full px-4 py-3 text-base bg-white/5 border border-white/10 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent peer text-white placeholder-slate-400"
                                        placeholder="Account Number"
                                        :maxlength="11"
                                    />
                                </template>
                                <template v-else>
                                    <input
                                        id="email"
                                        v-model="form.email"
                                        type="email"
                                        required
                                        class="w-full px-4 py-3 text-base bg-white/5 border border-white/10 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent peer text-white placeholder-slate-400"
                                        placeholder="Email Address"
                                        :disabled="formDisabled"
                                    />
                                </template>
                            </div>

                            <!-- Password -->
                            <div class="relative">
                                <input
                                    id="password"
                                    v-model="form.password"
                                    :type="showPassword ? 'text' : 'password'"
                                    required
                                    class="w-full px-4 py-3 text-base bg-white/5 border border-white/10 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent peer text-white placeholder-slate-400 pr-12"
                                    placeholder="Password"
                                    :disabled="formDisabled"
                                />
                                <button
                                    type="button"
                                    class="absolute right-3 top-3 text-slate-400 hover:text-white transition-colors"
                                    @click="showPassword = !showPassword"
                                >
                                    <v-icon
                                        :name="
                                            showPassword
                                                ? 'bi-eye-slash'
                                                : 'bi-eye'
                                        "
                                    />
                                </button>
                            </div>

                            <!-- Remember Me & Forgot Password -->
                            <div
                                class="flex items-center justify-between text-sm"
                            >
                                <label class="flex items-center text-slate-300">
                                    <input
                                        type="checkbox"
                                        v-model="form.remember"
                                        class="rounded bg-white/5 border-white/10 text-blue-500 focus:ring-blue-500 focus:ring-offset-slate-800"
                                    />
                                    <span class="ml-2">Remember me</span>
                                </label>
                                <a
                                    href="#"
                                    class="text-blue-400 hover:text-blue-300 transition-colors"
                                >
                                    Recovery Password?
                                </a>
                            </div>

                            <!-- Lockout Warning -->
                            <div
                                v-if="lockoutSeconds > 0"
                                class="border-l-4 border-red-500 bg-red-500/20 p-4 rounded-r-xl"
                            >
                                <div class="flex items-start">
                                    <v-icon
                                        name="hi-exclamation"
                                        class="text-red-400 mr-3 mt-0.5"
                                    />
                                    <div>
                                        <h3
                                            class="text-red-300 font-medium text-sm"
                                        >
                                            Security Lockout
                                        </h3>
                                        <p class="text-red-200 text-sm mt-1">
                                            Please wait
                                            <span class="font-semibold"
                                                >{{ lockoutSeconds }}s</span
                                            >
                                            before trying again.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button
                                type="submit"
                                :disabled="
                                    form.processing ||
                                    formDisabled ||
                                    isSubmitting
                                "
                                class="w-full bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-medium py-3 px-4 rounded-xl transition-all duration-200 flex items-center justify-center gap-2 shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:scale-105"
                                :class="{
                                    'opacity-75 cursor-not-allowed':
                                        form.processing ||
                                        formDisabled ||
                                        isSubmitting,
                                }"
                            >
                                <template
                                    v-if="form.processing || isSubmitting"
                                >
                                    <div class="spinner-small"></div>
                                    Signing in...
                                </template>
                                <template v-else>
                                    <v-icon
                                        name="md-login-outlined"
                                        class="text-lg"
                                    />
                                    Sign In
                                </template>
                            </button>




                        </form>

                        <!-- Help Text -->
                        <div
                            class="mt-6 text-sm text-blue-300 bg-blue-500/20 border border-blue-400/30 p-4 rounded-xl backdrop-blur-sm"
                        >
                            <p class="font-medium mb-1">Need Help?</p>
                            <p class="text-blue-200 text-xs">
                                Contact the Systems Development & Administration
                                Office for password recovery or account
                                assistance.
                            </p>
                        </div>
                    </div>
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
    HiUser,
    HiCog,
    HiUsers,
    HiChevronDown,
    BiEye,
    BiEyeSlash,
    MdLoginOutlined,
    HiLockClosed,
    HiExclamation,
    IoWater,
    HiCheck,
    FaGoogle,
    FaLinkedin,
} from "oh-vue-icons/icons";
import Navigation from "@/Components/Header/Navigation.vue";
import Swal from "sweetalert2";
import axios from "axios";

addIcons(
    HiUser,
    HiCog,
    HiUsers,
    HiChevronDown,
    BiEye,
    BiEyeSlash,
    MdLoginOutlined,
    HiLockClosed,
    HiExclamation,
    IoWater,
    HiCheck,
    FaGoogle,
    FaLinkedin
);

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
    {
        value: "customer",
        label: "Concessioner",
        description: "Report and track water issues",
        icon: "hi-user",
    },
    {
        value: "admin",
        label: "Administrator",
        description: "Manage system and users",
        icon: "hi-cog",
    },
    {
        value: "staff",
        label: "Staff",
        description: "Handle meter readings",
        icon: "hi-users",
    },
];

const roleLabels = {
    customer: "Concessioner",
    admin: "Administrator",
    staff: "Staff",
};

const roleDescriptions = {
    customer: "Report and track water issues",
    admin: "Manage system and users",
    staff: "Handle meter readings",
};

const roleIcons = {
    customer: "hi-user",
    admin: "hi-cog",
    staff: "hi-users",
};

const roleColors = {
    customer: { bg: "bg-blue-500/20", icon: "text-blue-400" },
    admin: { bg: "bg-purple-500/20", icon: "text-purple-400" },
    staff: { bg: "bg-teal-500/20", icon: "text-teal-400" },
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
    const dropdown = event.target.closest(".relative");
    if (!dropdown) {
        showRoleDropdown.value = false;
    }
};

onMounted(() => {
    document.addEventListener("click", handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener("click", handleClickOutside);
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
                text: `You can now proceed to login as ${
                    roleLabels[selectedRole.value]
                }`,
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
        let formatted =
            cleanInput.slice(0, 3) +
            "-" +
            cleanInput.slice(3, 5) +
            "-" +
            cleanInput.slice(5, 8);
        if (cleanInput.length >= 9) {
            formatted += cleanInput.slice(8, 9);
        }
        form.account_number = formatted;
    } else if (cleanInput.length > 5) {
        form.account_number =
            cleanInput.slice(0, 3) +
            "-" +
            cleanInput.slice(3, 5) +
            "-" +
            cleanInput.slice(5);
    } else if (cleanInput.length > 3) {
        form.account_number =
            cleanInput.slice(0, 3) + "-" + cleanInput.slice(3, 5);
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
                text: `You've successfully logged in as ${
                    roleLabels[selectedRole.value] || "customer"
                }`,
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
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}

/* Custom scrollbar for dropdown */
.scrollbar-thin {
    scrollbar-width: thin;
    scrollbar-color: rgba(100, 116, 139, 0.5) transparent;
}

.scrollbar-thin::-webkit-scrollbar {
    width: 6px;
}

.scrollbar-thin::-webkit-scrollbar-track {
    background: transparent;
}

.scrollbar-thin::-webkit-scrollbar-thumb {
    background: rgba(100, 116, 139, 0.5);
    border-radius: 3px;
}

.scrollbar-thin::-webkit-scrollbar-thumb:hover {
    background: rgba(100, 116, 139, 0.7);
}
</style>
