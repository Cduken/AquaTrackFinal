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
                <div class="text-white space-y-6 hidden md:block">
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
                            sure you have your account number ready for quick
                            access.
                        </p>
                    </div>


                </div>

                <!-- Right Side - Login Form -->
                <div class="w-full">
                    <div
                        class="bg-transparent backdrop-blur-xl border border-white/20 rounded-2xl shadow-2xl p-8"
                    >
                        <!-- Header -->
                        <div class="text-start mb-8">
                            <h2 class="text-2xl font-light text-white mb-2">
                                Welcome Back!
                                <!-- <span
                                    class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-400"
                                    >AquaTrack</span
                                > -->
                            </h2>
                        </div>

                        <!-- Login Form -->
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Account Number -->
                            <div class="relative">
                                <User
                                    class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400 w-5 h-5 transition-all duration-200"
                                />
                                <input
                                    id="account_number"
                                    v-model="form.account_number"
                                    type="text"
                                    :disabled="formDisabled"
                                    @input="formatAccountNumber"
                                    required
                                    class="w-full pl-12 pr-4 py-4 text-base bg-white/5 border border-white/10 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-white placeholder-transparent peer"
                                    placeholder="Account Number"
                                    :maxlength="11"
                                />
                                <label
                                    for="account_number"
                                    class="absolute left-12 top-1/2 transform -translate-y-1/2 text-slate-400 text-sm transition-all duration-200 pointer-events-none peer-focus:top-2 peer-focus:-translate-y-2 peer-focus:text-xs peer-focus:text-blue-400"
                                    :class="
                                        form.account_number
                                            ? 'top-2 -translate-y-2 text-xs text-blue-400'
                                            : ''
                                    "
                                >
                                    Account Number
                                </label>
                            </div>

                            <!-- Password -->
                            <div class="relative">
                                <Lock
                                    class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400 w-5 h-5 transition-all duration-200"
                                />
                                <input
                                    id="password"
                                    v-model="form.password"
                                    :type="showPassword ? 'text' : 'password'"
                                    required
                                    class="w-full pl-12 pr-12 py-4 text-base bg-white/5 border border-white/10 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-white placeholder-transparent peer"
                                    placeholder="Password"
                                    :disabled="formDisabled"
                                />
                                <label
                                    for="password"
                                    class="absolute left-12 top-1/2 transform -translate-y-1/2 text-slate-400 text-sm transition-all duration-200 pointer-events-none peer-focus:top-2 peer-focus:-translate-y-2 peer-focus:text-xs peer-focus:text-blue-400"
                                    :class="
                                        form.password
                                            ? 'top-2 -translate-y-2 text-xs text-blue-400'
                                            : ''
                                    "
                                >
                                    Password
                                </label>
                                <button
                                    type="button"
                                    class="absolute right-4 top-1/2 transform -translate-y-1/2 text-slate-400 hover:text-white transition-colors"
                                    @click="showPassword = !showPassword"
                                >
                                    <Eye v-if="!showPassword" class="w-5 h-5" />
                                    <EyeOff v-else class="w-5 h-5" />
                                </button>
                            </div>

                            <!-- Lockout Warning -->
                            <div
                                v-if="lockoutSeconds > 0"
                                class="border-l-4 border-red-500 bg-red-500/20 p-4 rounded-r-xl"
                            >
                                <div class="flex items-start">
                                    <AlertCircle
                                        class="text-red-400 mr-3 mt-0.5 w-5 h-5"
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
                                class="w-full bg-gradient-to-r from-blue-600/20 to-cyan-600/20 border border-blue-500/20 hover:from-blue-600/40 hover:to-cyan-600/50 text-white font-medium py-3 px-4 rounded-full flex items-center justify-center gap-2 transition duration-300 disabled:opacity-75 disabled:cursor-not-allowed"
                            >
                                <LogIn
                                    v-if="!(form.processing || isSubmitting)"
                                    class="w-5 h-5"
                                />
                                <div v-else class="spinner-small"></div>
                                {{
                                    form.processing || isSubmitting
                                        ? "Signing in..."
                                        : "Sign In"
                                }}
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
import { ref, onMounted, onUnmounted } from "vue";
import { useForm, usePage, router } from "@inertiajs/vue3";
import { User, Lock, Eye, EyeOff, LogIn, AlertCircle } from "lucide-vue-next";
import Navigation from "@/Components/Header/Navigation.vue";
import Swal from "sweetalert2";

const showPassword = ref(false);
const isSubmitting = ref(false);
const formDisabled = ref(false);
const lockoutSeconds = ref(0);
let lockoutTimer = null;

// Check if there's a persisted lockout state
const checkPersistedLockout = () => {
    const lockoutData = localStorage.getItem("login_lockout");
    if (lockoutData) {
        const { expiresAt, accountNumber } = JSON.parse(lockoutData);
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
            localStorage.removeItem("login_lockout");
        }
    }
};

// Persist lockout state to localStorage
const persistLockoutState = (seconds, accountNumber) => {
    const expiresAt = Date.now() + seconds * 1000;
    localStorage.setItem(
        "login_lockout",
        JSON.stringify({
            expiresAt,
            accountNumber,
            timestamp: Date.now(),
        })
    );
};

// Clear persisted lockout state
const clearPersistedLockout = () => {
    localStorage.removeItem("login_lockout");
};

const startLockoutTimer = () => {
    if (lockoutTimer) clearInterval(lockoutTimer);

    lockoutTimer = setInterval(() => {
        lockoutSeconds.value--;

        // Update persisted state
        const lockoutData = localStorage.getItem("login_lockout");
        if (lockoutData) {
            const data = JSON.parse(lockoutData);
            data.expiresAt = Date.now() + lockoutSeconds.value * 1000;
            localStorage.setItem("login_lockout", JSON.stringify(data));
        }

        if (lockoutSeconds.value <= 0) {
            clearInterval(lockoutTimer);
            formDisabled.value = false;
            clearPersistedLockout();
        }
    }, 1000);
};

const form = useForm({
    account_number: "",
    password: "",
    remember: false,
    role: "customer",
});

const page = usePage();

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
        if (cleanInput.length >= 9) formatted += cleanInput.slice(8, 9);
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
                title: `Welcome back, ${userName}!`,
                text: "You've successfully logged in to your account",
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

                // Persist the lockout state
                persistLockoutState(lockoutSeconds.value, form.account_number);

                startLockoutTimer();
                return;
            }

            let errorMessage =
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

// Check for persisted lockout when component mounts
onMounted(() => {
    checkPersistedLockout();
});

onUnmounted(() => {
    if (lockoutTimer) clearInterval(lockoutTimer);
});
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
</style>
