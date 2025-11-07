<template>
    <div
        class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 flex items-center justify-center p-6"
    >
        <div class="w-full max-w-4xl">
            <!-- Combined Container -->
            <div
                class="relative bg-gradient-to-br from-slate-800 via-slate-700 to-slate-900 rounded-3xl p-8 lg:p-12 shadow-2xl border border-slate-700"
            >
                <!-- Decorative elements -->
                <div
                    class="absolute top-4 left-4 w-24 h-24 bg-blue-500/10 rounded-full blur-3xl"
                ></div>
                <div
                    class="absolute bottom-4 right-4 w-28 h-28 bg-purple-500/10 rounded-full blur-3xl"
                ></div>
                <div
                    class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-48 h-48 bg-cyan-500/5 rounded-full blur-3xl"
                ></div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                    <!-- Left Side - Logo Section -->
                    <div class="relative z-10 text-center">
                        <div
                            class="w-52 h-52 mx-auto mb-6 bg-white/5 backdrop-blur-sm rounded-3xl p-6 flex items-center justify-center border border-white/10"
                        >
                            <img
                                src="/images/MainLogo.png"
                                alt="AquaTrack Logo"
                                class="w-full h-full object-contain drop-shadow-2xl"
                            />
                        </div>
                        <h1 class="text-3xl font-bold text-white mb-2">
                            Aqua<span
                                class="bg-gradient-to-r from-blue-500 to-cyan-500 bg-clip-text text-transparent"
                                >Track</span
                            >
                        </h1>

                        <p class="text-slate-400 text-xs mt-2 max-w-xs mx-auto">
                            Secure administrative access for authorized
                            personnel
                        </p>
                    </div>

                    <!-- Right Side - Login Form -->
                    <div class="w-full">
                        <!-- Header -->
                        <div class="mb-6 text-center lg:text-left">
                            <div
                                class="flex items-center gap-3 mb-3 justify-center lg:justify-start"
                            >
                                <div>
                                    <h2 class="text-2xl font-bold text-white">
                                        Welcome Back
                                    </h2>
                                    <p class="text-slate-400 text-xs mt-1">
                                        Sign in to your account!
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Login Form -->
                        <div class="space-y-4">
                            <!-- Role Selection Dropdown -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-300 mb-2"
                                >
                                    Access Level
                                </label>
                                <div class="relative">
                                    <button
                                        type="button"
                                        @click="dropdownOpen = !dropdownOpen"
                                        class="w-full flex items-center justify-between p-3.5 bg-slate-800/50 border border-slate-600 rounded-xl text-white backdrop-blur-sm transition-all duration-200 hover:bg-slate-800/70"
                                    >
                                        <div class="flex items-center gap-3">
                                            <Users
                                                v-if="selectedRole === 'staff'"
                                                class="w-5 h-5 text-blue-400"
                                            />
                                            <Settings
                                                v-else
                                                class="w-5 h-5 text-purple-400"
                                            />
                                            <div class="text-left">
                                                <span
                                                    class="text-sm font-medium block capitalize"
                                                    >{{ selectedRole }}</span
                                                >
                                                <span
                                                    class="text-xs text-slate-400"
                                                    >{{
                                                        selectedRole === "staff"
                                                            ? "Meter Operations"
                                                            : "System Management"
                                                    }}</span
                                                >
                                            </div>
                                        </div>
                                        <ChevronDown
                                            class="w-4 h-4 text-slate-400 transition-transform duration-200"
                                            :class="{
                                                'rotate-180': dropdownOpen,
                                            }"
                                        />
                                    </button>

                                    <!-- Dropdown Menu -->
                                    <div
                                        v-if="dropdownOpen"
                                        class="absolute z-50 w-full mt-1 bg-slate-800 border border-slate-600 rounded-xl shadow-lg overflow-hidden"
                                    >
                                        <button
                                            type="button"
                                            @click="
                                                selectRole('staff');
                                                dropdownOpen = false;
                                            "
                                            class="w-full flex items-center gap-3 p-3 text-left hover:bg-slate-700/50 transition-colors duration-150"
                                            :class="{
                                                'bg-blue-500/20':
                                                    selectedRole === 'staff',
                                            }"
                                        >
                                            <Users
                                                class="w-5 h-5 text-blue-400"
                                            />
                                            <div>
                                                <span
                                                    class="text-sm font-medium text-white block"
                                                    >Staff</span
                                                >
                                                <span
                                                    class="text-xs text-slate-300"
                                                    >Meter Operations</span
                                                >
                                            </div>
                                        </button>
                                        <button
                                            type="button"
                                            @click="
                                                selectRole('admin');
                                                dropdownOpen = false;
                                            "
                                            class="w-full flex items-center gap-3 p-3 text-left hover:bg-slate-700/50 transition-colors duration-150"
                                            :class="{
                                                'bg-purple-500/20':
                                                    selectedRole === 'admin',
                                            }"
                                        >
                                            <Settings
                                                class="w-5 h-5 text-purple-400"
                                            />
                                            <div>
                                                <span
                                                    class="text-sm font-medium text-white block"
                                                    >Admin</span
                                                >
                                                <span
                                                    class="text-xs text-slate-300"
                                                    >System Management</span
                                                >
                                            </div>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Email Input -->
                            <div class="relative">
                                <div class="relative">
                                    <Mail
                                        class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 transition-colors duration-200 z-10"
                                        :class="{
                                            '!text-blue-400': isEmailFocused,
                                        }"
                                    />
                                    <input
                                        id="email"
                                        v-model="form.email"
                                        type="email"
                                        required
                                        class="w-full pl-10 pr-4 py-4 bg-slate-800/50 border border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-white placeholder-transparent backdrop-blur-sm transition-all duration-200"
                                        placeholder=""
                                        :disabled="formDisabled"
                                        @focus="isEmailFocused = true"
                                        @blur="isEmailFocused = false"
                                    />
                                    <label
                                        for="email"
                                        class="absolute left-10 top-1/2 -translate-y-1/2 text-slate-400 text-sm transition-all duration-200 pointer-events-none"
                                        :class="{
                                            'top-2 left-9 text-xs text-blue-400':
                                                form.email || isEmailFocused,
                                        }"
                                    >
                                        Email Address
                                    </label>
                                </div>
                            </div>

                            <!-- Password Input -->
                            <div class="relative">
                                <div class="relative">
                                    <Lock
                                        class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 transition-colors duration-200 z-10"
                                        :class="{
                                            '!text-blue-400': isPasswordFocused,
                                        }"
                                    />
                                    <input
                                        id="password"
                                        v-model="form.password"
                                        :type="
                                            showPassword ? 'text' : 'password'
                                        "
                                        required
                                        class="w-full pl-10 pr-12 py-4 bg-slate-800/50 border border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-white placeholder-transparent backdrop-blur-sm transition-all duration-200"
                                        placeholder=""
                                        :disabled="formDisabled"
                                        @focus="isPasswordFocused = true"
                                        @blur="isPasswordFocused = false"
                                    />
                                    <label
                                        for="password"
                                        class="absolute left-10 top-1/2 -translate-y-1/2 text-slate-400 text-sm transition-all duration-200 pointer-events-none"
                                        :class="{
                                            'top-2 left-9 text-xs text-blue-400':
                                                form.password ||
                                                isPasswordFocused,
                                        }"
                                    >
                                        Password
                                    </label>
                                    <button
                                        type="button"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-200 transition-colors duration-200 z-10"
                                        @click="showPassword = !showPassword"
                                        :class="{
                                            '!text-blue-400': isPasswordFocused,
                                        }"
                                    >
                                        <Eye
                                            v-if="!showPassword"
                                            class="w-4 h-4"
                                        />
                                        <EyeOff v-else class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>

                            <!-- Lockout Warning -->
                            <div
                                v-if="lockoutSeconds > 0"
                                class="bg-red-500/10 border border-red-500/30 text-red-200 px-3 py-2 rounded-xl text-xs backdrop-blur-sm"
                            >
                                <div class="flex items-start gap-2">
                                    <AlertCircle
                                        class="w-4 h-4 text-red-400 flex-shrink-0 mt-0.5"
                                    />
                                    <div>
                                        <p class="font-medium">
                                            Security Lockout
                                        </p>
                                        <p class="text-xs mt-1 opacity-90">
                                            Please wait
                                            <span class="font-semibold"
                                                >{{
                                                    lockoutSeconds
                                                }}
                                                seconds</span
                                            >
                                            before trying again.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button
                                type="button"
                                @click="submit"
                                :disabled="
                                    form.processing ||
                                    formDisabled ||
                                    isSubmitting
                                "
                                class="w-full bg-gradient-to-r from-blue-600/20 to-cyan-600/20 border border-blue-500/20 hover:from-blue-600/40 hover:to-cyan-600/40 text-white font-md text-sm py-3 px-4 rounded-full transition-all duration-200 flex items-center justify-center gap-2 disabled:hover:scale-100 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <template
                                    v-if="form.processing || isSubmitting"
                                >
                                    <Loader2 class="w-4 h-4 animate-spin" />
                                    Authenticating...
                                </template>
                                <template v-else>
                                    <LogIn class="w-4 h-4" />
                                    Sign In as
                                    {{
                                        selectedRole === "admin"
                                            ? "Admin"
                                            : "Staff"
                                    }}
                                </template>
                            </button>
                        </div>

                        <!-- Security Notice -->
                        <div
                            class="mt-6 bg-orange-500/10 border border-orange-500/30 rounded-xl p-3 backdrop-blur-sm"
                        >
                            <div class="flex items-start gap-2">
                                <ShieldAlert
                                    class="w-4 h-4 text-orange-400 flex-shrink-0 mt-0.5"
                                />
                                <div>
                                    <p
                                        class="text-orange-300 font-semibold text-xs mb-1"
                                    >
                                        Security Notice
                                    </p>
                                    <p
                                        class="text-orange-200 text-xs leading-relaxed opacity-90"
                                    >
                                        This portal is for authorized personnel
                                        only. Unauthorized access attempts will
                                        be logged and may result in legal
                                        action.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onUnmounted, watch, onMounted, onBeforeUnmount } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import {
    Lock,
    Mail,
    Eye,
    EyeOff,
    LogIn,
    AlertCircle,
    ShieldAlert,
    Loader2,
    Users,
    Settings,
    ChevronDown,
} from "lucide-vue-next";
import Swal from "sweetalert2";

const selectedRole = ref("staff");
const showPassword = ref(false);
const isSubmitting = ref(false);
const formDisabled = ref(false);
const lockoutSeconds = ref(0);
const dropdownOpen = ref(false);
const isEmailFocused = ref(false);
const isPasswordFocused = ref(false);
let lockoutTimer = null;

const form = useForm({
    email: "",
    password: "",
    remember: false,
    role: "staff",
});

const page = usePage();

// Check if there's a persisted lockout state
const checkPersistedLockout = () => {
    const lockoutData = localStorage.getItem("staff_login_lockout");
    if (lockoutData) {
        const { expiresAt, email } = JSON.parse(lockoutData);
        const now = Date.now();

        if (now < expiresAt) {
            // Lockout is still active
            const remainingSeconds = Math.ceil((expiresAt - now) / 1000);
            lockoutSeconds.value = remainingSeconds;
            formDisabled.value = true;

            // Start the countdown timer
            startLockoutTimer();
        } else {
            // Lockout has expired, clear it
            localStorage.removeItem("staff_login_lockout");
        }
    }
};

// Persist lockout state to localStorage
const persistLockoutState = (seconds, email) => {
    const expiresAt = Date.now() + seconds * 1000;
    localStorage.setItem(
        "staff_login_lockout",
        JSON.stringify({
            expiresAt,
            email,
            timestamp: Date.now(),
        })
    );
};

// Clear persisted lockout state
const clearPersistedLockout = () => {
    localStorage.removeItem("staff_login_lockout");
};

const startLockoutTimer = () => {
    if (lockoutTimer) clearInterval(lockoutTimer);

    lockoutTimer = setInterval(() => {
        lockoutSeconds.value--;

        // Update persisted state
        const lockoutData = localStorage.getItem("staff_login_lockout");
        if (lockoutData) {
            const data = JSON.parse(lockoutData);
            data.expiresAt = Date.now() + lockoutSeconds.value * 1000;
            localStorage.setItem("staff_login_lockout", JSON.stringify(data));
        }

        if (lockoutSeconds.value <= 0) {
            clearInterval(lockoutTimer);
            formDisabled.value = false;
            clearPersistedLockout();
        }
    }, 1000);
};

const selectRole = (role) => {
    selectedRole.value = role;
    form.role = role;
};

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
    if (!event.target.closest(".relative")) {
        dropdownOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener("click", handleClickOutside);
    checkPersistedLockout();
});

onBeforeUnmount(() => {
    document.removeEventListener("click", handleClickOutside);
});

watch(selectedRole, (newRole) => {
    form.role = newRole;
});

const submit = async () => {
    isSubmitting.value = true;
    await new Promise((resolve) => setTimeout(resolve, 1000));

    form.post(route("login"), {
        preserveState: true,
        preserveScroll: true,
        onFinish: () => {
            form.reset("password");
            isSubmitting.value = false;
        },
        onSuccess: () => {
            clearPersistedLockout();
            const userName = page.props.auth?.user?.name || "there";
            Swal.fire({
                position: "top-end",
                icon: "success",
                title: `Welcome, ${userName}!`,
                text: `You've successfully logged in as ${selectedRole.value}`,
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
                toast: true,
                background: "#ffffff",
                customClass: {
                    title: "text-black",
                    popup: "bg-slate-800 border border-slate-700",
                },
            });
        },
        onError: (errors) => {
            isSubmitting.value = false;

            if (errors.throttle) {
                lockoutSeconds.value = errors.remaining_time || 30;
                formDisabled.value = true;

                // Persist the lockout state
                persistLockoutState(lockoutSeconds.value, form.email);

                startLockoutTimer();
                return;
            }

            let errorMessage =
                errors.email || "Invalid credentials. Please try again";
            Swal.fire({
                position: "top-end",
                icon: "error",
                title: "Access Denied",
                text: errorMessage,
                showConfirmButton: false,
                timer: 3000,
                toast: true,
                background: "#ffffff",
                customClass: {
                    title: "text-gray-600",
                    popup: "bg-slate-800 border border-slate-700",
                },
            });
        },
    });
};

onUnmounted(() => {
    if (lockoutTimer) clearInterval(lockoutTimer);
    document.removeEventListener("click", handleClickOutside);
});
</script>

<style scoped>
@keyframes spin {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

/* Custom focus styles */
input:focus {
    background-color: rgba(30, 41, 59, 0.8);
}

/* Ensure the floating labels work properly */
.relative input:focus + label,
.relative input:not(:placeholder-shown) + label {
    top: 10px;
    left: 36px;
    font-size: 0.75rem;
    color: #60a5fa;
}
</style>
