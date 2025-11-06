// resources/js/Pages/ReportIssuePage.vue
<script setup>
import { Head, Link } from "@inertiajs/vue3";
import Navigation from "@/Components/Header/Navigation.vue";
import Footer from "@/Components/Footer.vue";
import ReportForm from "@/Components/Reports/ReportForm.vue";
import GlobalReportSuccessModal from "@/Components/Modals/GlobalReportSuccessModal.vue";
import { ref } from "vue";

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

const handleReportSuccess = (data) => {
    // Show success modal instead of redirecting
    trackingInfo.value = {
        code: data.trackingCode,
        date: data.dateSubmitted,
    };
    showSuccessModal.value = true;
};

const handleOfflineReportsSynced = (data) => {
    trackingInfo.value = {
        code: data.trackingCode,
        date: data.dateSubmitted,
    };
    showSuccessModal.value = true;
};

const handleTrackReport = () => {
    if (trackingInfo.value?.code) {
        // Simply copy the tracking code to clipboard and show message
        navigator.clipboard
            .writeText(trackingInfo.value.code)
            .then(() => {
                alert(
                    `Tracking code "${trackingInfo.value.code}" copied to clipboard! You can use this code to track your report later.`
                );
            })
            .catch(() => {
                // Fallback if clipboard fails
                alert(
                    `Your tracking code is: ${trackingInfo.value.code}\nPlease save this code to track your report later.`
                );
            });
    }
};

const closeSuccessModal = () => {
    showSuccessModal.value = false;
    // Optionally reset the form or do nothing to keep the form filled
};

const submitAnotherReport = () => {
    showSuccessModal.value = false;
    // You could optionally reset the form here
    // If you want to reset the form, you'll need to add a ref to ReportForm
};
</script>

<template>
    <Head>
        <title>{{ title }}</title>
        <meta
            name="description"
            content="Report water issues and service problems in Clarin, Bohol"
        />
    </Head>

    <div
        class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900"
    >
        <Navigation />

        <!-- Page Header -->
        <section class="pt-24 pb-8 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="text-center">
                    <Link
                        :href="route('home')"
                        class="inline-flex items-center text-sm text-blue-400 hover:text-blue-300 mb-6 transition-colors"
                    >
                        <svg
                            class="w-4 h-4 mr-2"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"
                            />
                        </svg>
                        Back to Home
                    </Link>

                    <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
                        Report Water Issue
                    </h1>
                    <p class="text-xl text-slate-300 max-w-2xl mx-auto">
                        Submit a detailed report about water service issues in
                        your area. Our team will address it promptly.
                    </p>
                </div>
            </div>
        </section>

        <!-- Report Form Section -->
        <section class="pb-16 px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto">
                <div
                    class="bg-white/10 backdrop-blur-lg rounded-2xl border border-white/20 shadow-2xl overflow-hidden"
                >
                    <!-- Form Header -->
                    <div
                        class="bg-gradient-to-r from-blue-600 to-cyan-600 px-6 py-6"
                    >
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center"
                            >
                                <svg
                                    class="w-6 h-6 text-white"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                                    />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-white">
                                    Water Issue Report Form
                                </h2>
                                <p class="text-blue-100">
                                    Please provide accurate information for
                                    faster resolution
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Form Content -->
                    <div class="p-6">
                        <ReportForm
                            :zones="zones"
                            @submitted="handleReportSuccess"
                            @offlineReportsSynced="handleOfflineReportsSynced"
                        />
                    </div>
                </div>

                <!-- Additional Info -->
                <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div
                        class="text-center p-6 bg-white/5 rounded-xl border border-white/10"
                    >
                        <div
                            class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center mx-auto mb-4"
                        >
                            <svg
                                class="w-6 h-6 text-blue-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-white mb-2">
                            Quick Response
                        </h3>
                        <p class="text-slate-300 text-sm">
                            Our team typically responds within 24-48 hours
                        </p>
                    </div>

                    <div
                        class="text-center p-6 bg-white/5 rounded-xl border border-white/10"
                    >
                        <div
                            class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center mx-auto mb-4"
                        >
                            <svg
                                class="w-6 h-6 text-green-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                                />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-white mb-2">
                            Secure & Private
                        </h3>
                        <p class="text-slate-300 text-sm">
                            Your information is protected and kept confidential
                        </p>
                    </div>

                    <div
                        class="text-center p-6 bg-white/5 rounded-xl border border-white/10"
                    >
                        <div
                            class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center mx-auto mb-4"
                        >
                            <svg
                                class="w-6 h-6 text-purple-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"
                                />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-white mb-2">
                            Offline Support
                        </h3>
                        <p class="text-slate-300 text-sm">
                            Submit reports even without internet connection
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <Footer />

        <!-- Success Modal -->
        <GlobalReportSuccessModal
            v-if="showSuccessModal && trackingInfo"
            :show="showSuccessModal"
            :tracking-code="trackingInfo.code"
            :date-submitted="trackingInfo.date"
            @close="closeSuccessModal"
            @track-report="handleTrackReport"
        />
    </div>
</template>

<style scoped>
/* Add any custom styles here if needed */
</style>
