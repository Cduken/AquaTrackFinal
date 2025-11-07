<template>
    <Head>
        <title>{{ title }}</title>
        <meta
            name="description"
            content="Report water issues and service problems in Clarin, Bohol"
        />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    </Head>

    <div
        class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900"
    >
        <Navigation />

        <!-- Page Header -->
        <section class="pt-24 md:pt-20 pb-8 md:pb-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="text-center space-y-3 md:space-y-4">


                    <h1
                        class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl font-bold text-white tracking-tight"
                    >
                        Report <span class="bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-transparent">Water Issue</span>
                    </h1>
                    <p
                        class="text-sm sm:text-base md:text-lg lg:text-xl text-slate-300 max-w-3xl mx-auto leading-relaxed px-2"
                    >
                        Submit a detailed report about water service issues in
                        your area. Our team will address it promptly.
                    </p>
                </div>
            </div>
        </section>

        <!-- Main Content Section -->
        <section class="pb-12 md:pb-20 px-3 sm:px-4 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div
                    class="grid grid-cols-1 lg:grid-cols-12 gap-6 md:gap-8 lg:gap-12 items-start"
                >
                    <!-- Left Side - Illustration & Features - Hidden on mobile -->
                    <div
                        class="lg:col-span-5 space-y-6 md:space-y-8 hidden lg:block"
                    >
                        <!-- Illustration Card -->
                        <div class="relative">
                            <div
                                class="absolute -top-6 -right-6 w-24 h-24 bg-yellow-500/20 rounded-full blur-2xl animate-pulse"
                            ></div>
                            <div
                                class="absolute -bottom-8 -left-8 w-32 h-32 bg-blue-500/20 rounded-full blur-2xl animate-pulse"
                                style="animation-delay: 1s"
                            ></div>

                            <div
                                class="relative z-10 bg-gradient-to-br from-blue-500/10 via-cyan-500/10 to-blue-500/10 rounded-2xl lg:rounded-3xl p-6 lg:p-10 border border-white/10 backdrop-blur-sm shadow-2xl"
                            >
                                <img
                                    src="/images/report-img.png"
                                    alt="Water Issue Report Illustration"
                                    class="w-full h-auto object-contain drop-shadow-2xl"
                                />
                            </div>
                        </div>

                        <!-- Feature Cards -->
                        <div class="space-y-4">
                            <h3 class="text-white text-lg font-semibold mb-4">
                                Why Report with Us?
                            </h3>

                            <div
                                class="group flex items-start gap-4 p-4 bg-white/5 hover:bg-white/10 rounded-xl lg:rounded-2xl border border-white/10 hover:border-white/20 backdrop-blur-sm transition-all duration-300"
                            >
                                <div
                                    class="w-10 h-10 lg:w-12 lg:h-12 bg-gradient-to-br from-green-500/20 to-emerald-500/20 rounded-lg lg:rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300"
                                >
                                    <Zap
                                        :size="20"
                                        class="text-green-400 lg:text-green-400"
                                    />
                                </div>
                                <div class="flex-1">
                                    <h4
                                        class="text-white font-semibold text-sm lg:text-base mb-1"
                                    >
                                        Quick Response Time
                                    </h4>
                                    <p
                                        class="text-slate-300 text-xs lg:text-sm leading-relaxed"
                                    >
                                        Our team typically responds within 24-48
                                        hours to address your concerns
                                    </p>
                                </div>
                            </div>

                            <div
                                class="group flex items-start gap-4 p-4 bg-white/5 hover:bg-white/10 rounded-xl lg:rounded-2xl border border-white/10 hover:border-white/20 backdrop-blur-sm transition-all duration-300"
                            >
                                <div
                                    class="w-10 h-10 lg:w-12 lg:h-12 bg-gradient-to-br from-blue-500/20 to-cyan-500/20 rounded-lg lg:rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300"
                                >
                                    <Cloud
                                        :size="20"
                                        class="text-blue-400 lg:text-blue-400"
                                    />
                                </div>
                                <div class="flex-1">
                                    <h4
                                        class="text-white font-semibold text-sm lg:text-base mb-1"
                                    >
                                        Offline Support
                                    </h4>
                                    <p
                                        class="text-slate-300 text-xs lg:text-sm leading-relaxed"
                                    >
                                        Submit reports even without internet
                                        connection - they'll sync automatically
                                    </p>
                                </div>
                            </div>

                            <div
                                class="group flex items-start gap-4 p-4 bg-white/5 hover:bg-white/10 rounded-xl lg:rounded-2xl border border-white/10 hover:border-white/20 backdrop-blur-sm transition-all duration-300"
                            >
                                <div
                                    class="w-10 h-10 lg:w-12 lg:h-12 bg-gradient-to-br from-purple-500/20 to-pink-500/20 rounded-lg lg:rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300"
                                >
                                    <Shield
                                        :size="20"
                                        class="text-purple-400 lg:text-purple-400"
                                    />
                                </div>
                                <div class="flex-1">
                                    <h4
                                        class="text-white font-semibold text-sm lg:text-base mb-1"
                                    >
                                        Secure & Private
                                    </h4>
                                    <p
                                        class="text-slate-300 text-xs lg:text-sm leading-relaxed"
                                    >
                                        Your information is protected and kept
                                        confidential at all times
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side - Form Container -->
                    <div class="lg:col-span-7 w-full">
                        <!-- Progress Steps -->
                        <div
                            class="mb-4 md:mb-6 bg-white/10 backdrop-blur-xl rounded-2xl lg:rounded-3xl border border-white/20 shadow-2xl p-4 md:p-6"
                        >
                            <div
                                class="flex items-center justify-between relative"
                            >
                                <div
                                    class="absolute top-3 md:top-4 left-0 right-0 h-0.5 bg-slate-600/50 -z-10"
                                ></div>
                                <div
                                    class="absolute top-3 md:top-4 left-0 h-0.5 bg-blue-500 -z-10 transition-all duration-500"
                                    :style="{ width: `${progressWidth}%` }"
                                ></div>

                                <div
                                    v-for="step in steps"
                                    :key="step.id"
                                    class="flex flex-col items-center"
                                >
                                    <div
                                        class="w-6 h-6 md:w-8 md:h-8 rounded-full flex items-center justify-center text-xs md:text-sm font-semibold transition-all duration-300"
                                        :class="[
                                            currentStep >= step.id
                                                ? 'bg-blue-500 text-white shadow-lg shadow-blue-500/25'
                                                : 'bg-slate-700 text-slate-400 border border-slate-600',
                                        ]"
                                    >
                                        <span v-if="currentStep > step.id"
                                            >✓</span
                                        >
                                        <span v-else>{{ step.id }}</span>
                                    </div>
                                    <span
                                        class="text-xs mt-1 md:mt-2 text-slate-300 font-medium hidden xs:block"
                                        >{{ step.name }}</span
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Form Box -->
                        <div
                            class="bg-white/10 backdrop-blur-xl rounded-2xl lg:rounded-3xl border border-white/20 shadow-2xl overflow-hidden"
                        >
                            <div
                                class="p-4 md:p-6 lg:p-8 max-h-[70vh] md:max-h-[600px] overflow-y-auto custom-scrollbar"
                            >
                                <ReportForm
                                    ref="reportFormRef"
                                    :zones="zones"
                                    :current-step="currentStep"
                                    @submitted="handleReportSuccess"
                                    @offlineReportsSynced="
                                        handleOfflineReportsSynced
                                    "
                                    @form-state-change="handleFormStateChange"
                                />
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div
                            class="mt-4 md:mt-6 bg-white/10 backdrop-blur-xl rounded-2xl lg:rounded-3xl border border-white/20 shadow-2xl p-4 md:p-4"
                        >
                            <div
                                class="flex justify-between items-center gap-2"
                            >
                                <button
                                    v-if="currentStep > 1"
                                    @click="prevStep"
                                    class="px-4 md:px-6 py-2 md:py-2 border border-white/20 hover:border-white/40 text-white font-medium rounded-lg md:rounded-xl transition-all duration-200 flex items-center space-x-1 md:space-x-2 shadow-lg text-sm md:text-base"
                                >
                                    <ChevronLeft :size="14" class="md:size-4" />
                                    <span>Back</span>
                                </button>
                                <div v-else class="flex-1"></div>

                                <button
                                    v-if="currentStep < steps.length"
                                    @click="nextStep"
                                    :disabled="!canProceedToNextStep"
                                    class="px-4 md:px-6 py-2 md:py-2 bg-blue-900/20 border border-blue-500/30 hover:bg-blue-300/10 text-white font-sm rounded-lg md:rounded-xl transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-1 md:space-x-2 shadow-lg text-sm md:text-base"
                                >
                                    <span>Next</span>
                                    <ChevronRight
                                        :size="14"
                                        class="md:size-4"
                                    />
                                </button>

                                <button
                                    v-if="currentStep === steps.length"
                                    @click="submitReport"
                                    :disabled="!isFormValid || isSubmitting"
                                    class="px-4 md:px-6 py-2 md:py-3 bg-green-600/30 border border-green-500/30 hover:bg-green-700/70 text-white font-medium rounded-lg md:rounded-xl transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-1 md:space-x-2 shadow-lg text-sm md:text-base"
                                >
                                    <span v-if="!isSubmitting"
                                        >Submit Report</span
                                    >
                                    <span v-else class="text-xs md:text-sm"
                                        >Submitting...</span
                                    >
                                    <Send
                                        :size="14"
                                        class="md:size-4"
                                        v-if="!isSubmitting"
                                    />
                                </button>
                            </div>
                        </div>

                        <!-- Help Text -->
                        <div
                            class="mt-4 md:mt-6 flex items-start gap-2 md:gap-3 p-3 md:p-4 bg-blue-500/10 border border-blue-500/20 rounded-lg md:rounded-xl backdrop-blur-sm"
                        >
                            <Info
                                :size="16"
                                class="text-blue-400 flex-shrink-0 mt-0.5 md:mt-1"
                            />
                            <p
                                class="text-xs md:text-sm text-blue-100 leading-relaxed"
                            >
                                <span class="font-semibold">Need help?</span> If
                                you have questions about filling out this form,
                                please contact our support team at
                                <span
                                    class="text-blue-300 font-medium break-all"
                                    >support@aquatrack.com</span
                                >
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Success Modal -->
        <GlobalReportSuccessModal
            v-if="showSuccessModal && trackingInfo"
            :tracking-info="trackingInfo"
            @close="closeSuccessModal"
            @submit-another="submitAnotherReport"
            @track-report="handleTrackReport"
        />

        <!-- Fallback Success Message -->
        <div
            v-if="showSuccessModal && !trackingInfo"
            class="fixed inset-0 flex items-center justify-center z-50 bg-black/50"
        >
            <div class="bg-white rounded-2xl p-6 max-w-md mx-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                    Submission Successful
                </h3>
                <p class="text-gray-600 mb-4">
                    Your report has been submitted successfully.
                </p>
                <button
                    @click="closeSuccessModal"
                    class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors"
                >
                    OK
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link } from "@inertiajs/vue3";
import Navigation from "@/Components/Header/Navigation.vue";
import ReportForm from "@/Components/Reports/ReportForm.vue";
import GlobalReportSuccessModal from "@/Components/Modals/GlobalReportSuccessModal.vue";
import { ref, computed, onMounted } from "vue";
import {
    ArrowLeft,
    Zap,
    Cloud,
    Shield,
    Info,
    ChevronLeft,
    ChevronRight,
    Send,
} from "lucide-vue-next";

defineProps({
    title: {
        type: String,
        default: "Report Water Issue - AquaTrack",
    },
    zones: {
        type: Object,
        required: true,
    },
});

// Success modal state
const showSuccessModal = ref(false);
const trackingInfo = ref(null);

// Form state
const currentStep = ref(1);
const isSubmitting = ref(false);
const isRecording = ref(false);
const locationStatus = ref("idle");
const isOnline = ref(navigator.onLine);
const reportFormRef = ref(null);

const steps = [
    { id: 1, name: "Basic Info" },
    { id: 2, name: "Issue Details" },
    { id: 3, name: "Media" },
    { id: 4, name: "Review" },
];

// Computed properties
const progressWidth = computed(() => {
    return ((currentStep.value - 1) / (steps.length - 1)) * 100;
});

const canProceedToNextStep = computed(() => {
    if (!reportFormRef.value) return false;

    switch (currentStep.value) {
        case 1:
            return reportFormRef.value.canProceedToStep2;
        case 2:
            return reportFormRef.value.canProceedToStep3;
        case 3:
            return reportFormRef.value.canProceedToStep4;
        default:
            return true;
    }
});

const isFormValid = computed(() => {
    return reportFormRef.value?.isFormValid || false;
});

// Navigation methods
const nextStep = () => {
    if (currentStep.value < steps.length) {
        currentStep.value++;
    }
};

const prevStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--;
    }
};

const submitReport = async () => {
    if (reportFormRef.value) {
        isSubmitting.value = true;
        try {
            await reportFormRef.value.submitReport();
        } finally {
            isSubmitting.value = false;
        }
    }
};

// Event handlers
const handleReportSuccess = (data) => {
    if (!data.trackingCode) return;

    trackingInfo.value = {
        code: data.trackingCode,
        date: data.dateSubmitted || new Date().toISOString(),
        isOffline: data.isOffline || false,
    };

    showSuccessModal.value = true;
};

const handleOfflineReportsSynced = (data) => {
    if (!data.trackingCode) return;

    trackingInfo.value = {
        code: data.trackingCode,
        date: data.dateSubmitted || new Date().toISOString(),
        isOffline: true,
    };
    showSuccessModal.value = true;
};

const handleTrackReport = () => {
    if (trackingInfo.value?.code) {
        navigator.clipboard
            .writeText(trackingInfo.value.code)
            .then(() => {
                Swal.fire({
                    icon: "success",
                    title: "Tracking Code Copied!",
                    text: `Tracking code "${trackingInfo.value.code}" copied to clipboard!`,
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 4000,
                    customClass: {
                        popup: "rounded-xl shadow-lg",
                    },
                });
            })
            .catch(() => {
                Swal.fire({
                    icon: "info",
                    title: "Your Tracking Code",
                    html: `
                        <div class="text-center">
                            <p class="mb-3">Your tracking code is:</p>
                            <div class="bg-gray-100 p-3 rounded-lg mb-3">
                                <code class="text-lg font-bold text-blue-600">${trackingInfo.value.code}</code>
                            </div>
                            <p class="text-sm text-gray-600">Please save this code to track your report later.</p>
                        </div>
                    `,
                    confirmButtonColor: "#3085d6",
                    customClass: {
                        popup: "rounded-2xl shadow-2xl",
                    },
                });
            });
    }
};

const closeSuccessModal = () => {
    showSuccessModal.value = false;
};

const submitAnotherReport = () => {
    showSuccessModal.value = false;
    currentStep.value = 1;
    isSubmitting.value = false;

    if (reportFormRef.value?.resetForm) {
        reportFormRef.value.resetForm();
    }
};

// Listen for form state updates from ReportForm
const handleFormStateChange = (state) => {
    currentStep.value = state.currentStep;
    isSubmitting.value = state.isSubmitting;
    isRecording.value = state.isRecording;
    locationStatus.value = state.locationStatus;
    isOnline.value = state.isOnline;
};
</script>
