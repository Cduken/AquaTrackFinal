// TrackReport.vue
<script setup>
import { Head, Link } from "@inertiajs/vue3";
import Navigation from "@/Components/Header/Navigation.vue";
import { ref, onMounted, nextTick } from "vue";
import axios from "axios";
import QRCode from "qrcode";
import jsQR from "jsqr";
import {
    Search,
    CheckCircle,
    Calendar,
    MapPin,
    Download,
    Image,
    AlertTriangle,
    Play,
    ZoomIn,
    Camera,
    Droplet,
    QrCode,
    User,
    FileText,
    Shield,
    Clock,
    Eye,
    TrendingUp,
    ArrowRight,
    Phone,
    MapPin as MapPinIcon,
    Code,
    X,
} from "lucide-vue-next";
import Footer from "@/Components/Footer.vue";

defineProps({
    title: String,
    description: String,
});

const reportDetails = ref(null);
const isLoading = ref(false);
const errorMessage = ref(null);
const qrCodeCanvas = ref(null);
const trackingCode = ref("");
const showQrScanner = ref(false);
const scannerError = ref(null);

// QR Scanner state
const videoRef = ref(null);
const canvasRef = ref(null);
const stream = ref(null);
const scanning = ref(false);

// Check if there's a tracking code in the URL query parameters
onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search);
    const codeFromUrl = urlParams.get("tracking_code");
    if (codeFromUrl) {
        trackingCode.value = codeFromUrl;
        // Use setTimeout to ensure the component is fully mounted
        setTimeout(() => {
            trackReport();
        }, 100);
    }
});

const trackReport = async () => {
    if (!trackingCode.value || !trackingCode.value.trim()) {
        errorMessage.value = "Please enter a valid tracking code.";
        return;
    }

    isLoading.value = true;
    errorMessage.value = null;
    reportDetails.value = null;

    try {
        const response = await axios.get(route("reports.find"), {
            params: { tracking_code: trackingCode.value },
        });

        if (response.data.success === false) {
            if (response.data.deleted) {
                errorMessage.value =
                    response.data.reason || "This report has been deleted.";
            } else {
                errorMessage.value =
                    response.data.message || "Report not found.";
            }
        } else if (response.data.success === true && response.data.data) {
            reportDetails.value = response.data.data;
            if (response.data.data.additional_tracking_codes) {
                reportDetails.value.allTrackingCodes = [
                    reportDetails.value.tracking_code,
                    ...JSON.parse(response.data.data.additional_tracking_codes),
                ];
            } else {
                reportDetails.value.allTrackingCodes = [
                    reportDetails.value.tracking_code,
                ];
            }

            // Generate QR code after data loads
            await nextTick();
            await generateQRCode();

            // Scroll to results after a short delay
            setTimeout(() => {
                const resultsSection =
                    document.getElementById("report-results");
                if (resultsSection) {
                    resultsSection.scrollIntoView({
                        behavior: "smooth",
                        block: "start",
                    });
                }
            }, 500);
        } else {
            errorMessage.value = "Unexpected response structure.";
        }
    } catch (error) {
        console.error("Track report error:", error);
        if (error.response) {
            if (error.response.status === 404) {
                errorMessage.value =
                    "Report not found with this tracking code.";
            } else if (error.response.status === 410) {
                errorMessage.value =
                    error.response.data.reason ||
                    "This report has been deleted.";
            } else {
                errorMessage.value = "Search failed. Please try again.";
            }
        } else {
            errorMessage.value = "Network error. Please check your connection.";
        }
    } finally {
        isLoading.value = false;
    }
};

const generateQRCode = async () => {
    if (!reportDetails.value?.tracking_code || !qrCodeCanvas.value) {
        console.log("Missing data for QR generation:", {
            hasTrackingCode: !!reportDetails.value?.tracking_code,
            hasCanvas: !!qrCodeCanvas.value,
        });
        return;
    }

    try {
        console.log(
            "Generating QR code for:",
            reportDetails.value.tracking_code
        );
        await QRCode.toCanvas(
            qrCodeCanvas.value,
            reportDetails.value.tracking_code,
            {
                width: 160,
                margin: 2,
                color: { dark: "#1e40af", light: "#ffffff" },
            }
        );
        console.log("QR code generated successfully");
    } catch (error) {
        console.error("QR Code generation error:", error);
    }
};

// QR Scanner functions
const startQrScanner = async () => {
    try {
        scannerError.value = null;
        showQrScanner.value = true;

        await nextTick(); // Wait for the modal to render

        stream.value = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: "environment" },
        });

        if (videoRef.value) {
            videoRef.value.srcObject = stream.value;
            await videoRef.value.play();
            scanning.value = true;
            scanQrCode();
        }
    } catch (error) {
        console.error("Camera error:", error);
        scannerError.value = "Camera access denied. Please check permissions.";
        showQrScanner.value = false;
    }
};

const stopCamera = () => {
    if (stream.value) {
        stream.value.getTracks().forEach((track) => {
            track.stop();
        });
        stream.value = null;
    }
    scanning.value = false;
};

const closeQrScanner = () => {
    stopCamera();
    showQrScanner.value = false;
    scannerError.value = null;
};

const scanQrCode = () => {
    if (!scanning.value || !videoRef.value || !canvasRef.value) return;

    const video = videoRef.value;
    const canvas = canvasRef.value;
    const context = canvas.getContext("2d");

    if (video.readyState === video.HAVE_ENOUGH_DATA) {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        const imageData = context.getImageData(
            0,
            0,
            canvas.width,
            canvas.height
        );
        const code = jsQR(imageData.data, imageData.width, imageData.height);

        if (code) {
            trackingCode.value = code.data;
            closeQrScanner();
            nextTick(() => {
                trackReport();
            });
            return;
        }
    }

    if (scanning.value) {
        requestAnimationFrame(scanQrCode);
    }
};

const resetForm = () => {
    trackingCode.value = "";
    reportDetails.value = null;
    errorMessage.value = null;
};

const formattedStatus = (status) => {
    if (!status) return "";
    switch (status) {
        case "in_progress":
            return "In Progress";
        case "pending":
            return "Pending";
        case "resolved":
            return "Resolved";
        default:
            return (
                status.charAt(0).toUpperCase() + status.slice(1).toLowerCase()
            );
    }
};

const formattedPriority = (priority) => {
    if (!priority) return "Not Specified";
    return priority.charAt(0).toUpperCase() + priority.slice(1);
};

const statusColor = (status) => {
    switch (status) {
        case "resolved":
            return "text-green-600 bg-green-50 border-green-200";
        case "in_progress":
            return "text-blue-600 bg-blue-50 border-blue-200";
        case "pending":
            return "text-yellow-600 bg-yellow-50 border-yellow-200";
        default:
            return "text-gray-600 bg-gray-50 border-gray-200";
    }
};

const priorityColor = (priority) => {
    switch (priority) {
        case "high":
            return "text-red-600 bg-red-50 border-red-200";
        case "medium":
            return "text-orange-600 bg-orange-50 border-orange-200";
        case "low":
            return "text-green-600 bg-green-50 border-green-200";
        default:
            return "text-gray-600 bg-gray-50 border-gray-200";
    }
};
</script>

<template>
    <Head>
        <title>{{ title || "Track Water Report - AquaTrack" }}</title>
        <meta
            name="description"
            :content="
                description ||
                'Track your water service reports and monitor issue resolution progress in real-time'
            "
        />
    </Head>

    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
        <!-- Fixed background gradient -->
        <div
            class="fixed inset-0 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 -z-10"
        ></div>

        <!-- Background Elements -->
        <div class="fixed inset-0 overflow-hidden -z-5">
            <div
                class="absolute -top-40 -right-40 w-80 h-80 bg-blue-500/10 rounded-full blur-3xl"
            ></div>
            <div
                class="absolute -bottom-40 -left-40 w-80 h-80 bg-cyan-500/10 rounded-full blur-3xl"
            ></div>
            <div
                class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-indigo-500/5 rounded-full blur-3xl"
            ></div>
        </div>

        <Navigation />

        <!-- Hero Section - Adjusted text sizes -->
        <section class="pt-20 pb-16 px-4 sm:px-6 lg:px-8 relative">
            <div class="max-w-7xl mx-auto relative">
                <div class="text-center">
                    <div
                        class="inline-flex items-center px-3 py-1.5 rounded-full bg-blue-500/10 border border-blue-500/20 mb-4 text-xs"
                    >
                        <Shield class="w-3 h-3 text-blue-400 mr-1.5" />
                        <span class="font-medium text-blue-300"
                            >Real-time Tracking</span
                        >
                    </div>

                    <h1
                        class="text-4xl md:text-6xl font-bold text-white mb-4 leading-tight"
                    >
                        Track Your
                        <span
                            class="bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-transparent"
                            >Water Report</span
                        >
                    </h1>

                    <p
                        class="text-lg text-slate-300 max-w-3xl mx-auto mb-6 leading-relaxed"
                    >
                        Monitor your water service reports in real-time and stay
                        updated on issue resolution progress with our premium
                        tracking system.
                    </p>

                    <!-- Search Section - Adjusted sizes -->
                    <div class="max-w-3xl mx-auto">
                        <div
                            class="bg-white/10 backdrop-blur-lg rounded-xl border border-white/20 p-6 shadow-2xl"
                        >
                            <div class="flex items-center space-x-3 mb-4">
                                <div
                                    class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center"
                                >
                                    <Search class="w-5 h-5 text-blue-400" />
                                </div>
                                <div class="text-left">
                                    <h3
                                        class="text-lg font-semibold text-white"
                                    >
                                        Find Your Report
                                    </h3>
                                    <p class="text-slate-300 text-xs">
                                        Enter your tracking code to get started
                                    </p>
                                </div>
                            </div>

                            <form
                                @submit.prevent="trackReport"
                                class="space-y-3"
                            >
                                <div class="space-y-2">
                                <div class="flex items-center space-x-2">
                                    <label
                                        class="block text-xs font-medium text-gray-400"
                                        >Tracking Code</label
                                    >
                                    <Code class="text-gray-400 w-4 h-4" />
                                    </div>
                                    <div class="relative">
                                        <input
                                            v-model="trackingCode"
                                            type="text"
                                            required
                                            class="w-full h-12 pl-10 pr-10 text-base bg-white/5 border border-white/20 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-500/30 transition-all placeholder:text-slate-400 font-mono text-white backdrop-blur-sm"
                                            placeholder="AQT20241201-ABCD"
                                        />
                                        <div
                                            class="absolute left-3 top-1/2 -translate-y-1/2"
                                        >
                                            <QrCode
                                                class="w-4 h-4 text-slate-400"
                                            />
                                        </div>
                                        <button
                                            type="button"
                                            @click="startQrScanner"
                                            class="absolute right-2 top-1/2 -translate-y-1/2 p-1.5 text-slate-400 hover:text-blue-400 hover:bg-blue-500/20 rounded transition-all"
                                            title="Scan QR Code"
                                        >
                                            <Camera class="w-4 h-4" />
                                        </button>
                                    </div>
                                </div>

                                <Transition name="error-slide">
                                    <div
                                        v-if="errorMessage"
                                        class="flex items-center space-x-2 p-3 bg-red-500/10 border border-red-500/20 rounded-lg backdrop-blur-sm"
                                    >
                                        <AlertTriangle
                                            class="w-4 h-4 text-red-400 flex-shrink-0"
                                        />
                                        <span
                                            class="text-red-200 text-xs font-medium"
                                            >{{ errorMessage }}</span
                                        >
                                    </div>
                                </Transition>

                                <button
                                    type="submit"
                                    :disabled="
                                        isLoading || !trackingCode.trim()
                                    "
                                    class="w-full h-12 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 disabled:from-gray-600 disabled:to-gray-700 text-white font-semibold rounded-lg transition-all flex items-center justify-center space-x-2 shadow-lg hover:shadow-xl disabled:shadow-none group text-sm"
                                >
                                    <Search class="w-4 h-4" />
                                    <span>
                                        {{
                                            isLoading
                                                ? "Tracking Report..."
                                                : "Track Report"
                                        }}
                                    </span>
                                    <ArrowRight
                                        class="w-4 h-4 transform group-hover:translate-x-0.5 transition-transform"
                                    />
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section - Adjusted sizes -->
        <section v-if="!reportDetails" class="py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-2xl md:text-3xl font-bold text-white mb-3">
                        Premium Tracking Experience
                    </h2>
                    <p class="text-lg text-slate-300 max-w-2xl mx-auto">
                        Everything you need to stay informed about your water
                        service reports
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div
                        class="bg-white/5 backdrop-blur-sm rounded-xl border border-white/10 p-6 hover:border-blue-500/30 transition-all group"
                    >
                        <div
                            class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center mb-4 group-hover:scale-105 transition-transform"
                        >
                            <Clock class="w-6 h-6 text-blue-400" />
                        </div>
                        <h3 class="text-lg font-semibold text-white mb-2">
                            Real-time Updates
                        </h3>
                        <p class="text-slate-300 text-sm leading-relaxed">
                            Get instant notifications about your report status
                            and progress updates as they happen.
                        </p>
                    </div>

                    <div
                        class="bg-white/5 backdrop-blur-sm rounded-xl border border-white/10 p-6 hover:border-cyan-500/30 transition-all group"
                    >
                        <div
                            class="w-12 h-12 bg-cyan-500/20 rounded-lg flex items-center justify-center mb-4 group-hover:scale-105 transition-transform"
                        >
                            <Eye class="w-6 h-6 text-cyan-400" />
                        </div>
                        <h3 class="text-lg font-semibold text-white mb-2">
                            Transparent Progress
                        </h3>
                        <p class="text-slate-300 text-sm leading-relaxed">
                            See exactly where your report stands in the
                            resolution process with detailed status tracking.
                        </p>
                    </div>

                    <div
                        class="bg-white/5 backdrop-blur-sm rounded-xl border border-white/10 p-6 hover:border-indigo-500/30 transition-all group"
                    >
                        <div
                            class="w-12 h-12 bg-indigo-500/20 rounded-lg flex items-center justify-center mb-4 group-hover:scale-105 transition-transform"
                        >
                            <TrendingUp class="w-6 h-6 text-indigo-400" />
                        </div>
                        <h3 class="text-lg font-semibold text-white mb-2">
                            Priority Handling
                        </h3>
                        <p class="text-slate-300 text-sm leading-relaxed">
                            High-priority issues get escalated automatically for
                            faster resolution times.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Report Details Section - Adjusted sizes -->
        <Transition name="content-slide">
            <section
                v-if="reportDetails"
                id="report-results"
                class="py-12 px-4 sm:px-6 lg:px-8"
            >
                <div class="max-w-6xl mx-auto">
                    <!-- Status Header -->
                    <div
                        class="bg-gradient-to-r from-green-500/10 to-emerald-500/10 backdrop-blur-sm rounded-xl p-6 border border-green-500/20 mb-6"
                    >
                        <div
                            class="flex flex-col lg:flex-row items-center justify-between space-y-3 lg:space-y-0"
                        >
                            <div class="flex items-center space-x-3">
                                <div
                                    class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center"
                                >
                                    <CheckCircle
                                        class="w-6 h-6 text-green-400"
                                    />
                                </div>
                                <div class="text-center lg:text-left">
                                    <h2 class="text-xl font-bold text-white">
                                        Report Found
                                    </h2>
                                    <p class="text-slate-300 text-sm">
                                        Tracking Code:
                                        <span
                                            class="font-mono font-semibold text-blue-400"
                                        >
                                            {{ reportDetails.tracking_code }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <span
                                    class="px-3 py-1 rounded text-xs font-semibold border"
                                    :class="statusColor(reportDetails.status)"
                                >
                                    {{ formattedStatus(reportDetails.status) }}
                                </span>
                                <span
                                    class="px-3 py-1 rounded text-xs font-semibold border"
                                    :class="
                                        priorityColor(reportDetails.priority)
                                    "
                                >
                                    {{
                                        formattedPriority(
                                            reportDetails.priority
                                        )
                                    }}
                                    Priority
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Main Content Grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Left Column - Basic Info -->
                        <div class="lg:col-span-2 space-y-4">
                            <!-- Reporter & Priority Cards -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div
                                    class="bg-white/5 backdrop-blur-sm rounded-xl p-4 border border-white/10"
                                >
                                    <div
                                        class="flex items-center space-x-2 mb-3"
                                    >
                                        <User class="w-4 h-4 text-blue-400" />
                                        <span
                                            class="text-xs font-semibold text-slate-300 uppercase tracking-wide"
                                            >Reporter</span
                                        >
                                    </div>
                                    <p class="font-semibold text-white text-lg">
                                        {{ reportDetails.reporter_name }}
                                    </p>
                                    <p
                                        v-if="reportDetails.reporter_phone"
                                        class="text-slate-400 text-xs mt-1 flex items-center"
                                    >
                                        <Phone class="w-3 h-3 mr-1" />
                                        {{ reportDetails.reporter_phone }}
                                    </p>
                                </div>

                                <div
                                    class="bg-white/5 backdrop-blur-sm rounded-xl p-4 border border-white/10"
                                >
                                    <div
                                        class="flex items-center space-x-2 mb-3"
                                    >
                                        <Calendar
                                            class="w-4 h-4 text-cyan-400"
                                        />
                                        <span
                                            class="text-xs font-semibold text-slate-300 uppercase tracking-wide"
                                            >Report Date</span
                                        >
                                    </div>
                                    <p class="font-semibold text-white text-lg">
                                        {{
                                            new Date(
                                                reportDetails.created_at
                                            ).toLocaleDateString()
                                        }}
                                    </p>
                                    <p class="text-slate-400 text-xs mt-1">
                                        {{
                                            new Date(
                                                reportDetails.created_at
                                            ).toLocaleTimeString()
                                        }}
                                    </p>
                                </div>
                            </div>

                            <!-- Description -->
                            <div
                                class="bg-white/5 backdrop-blur-sm rounded-xl p-4 border border-white/10"
                            >
                                <div class="flex items-center space-x-2 mb-3">
                                    <FileText class="w-4 h-4 text-blue-400" />
                                    <span
                                        class="text-xs font-semibold text-slate-300 uppercase tracking-wide"
                                        >Issue Description</span
                                    >
                                </div>
                                <p
                                    class="text-slate-200 leading-relaxed whitespace-pre-line text-base"
                                >
                                    {{ reportDetails.description }}
                                </p>
                            </div>

                            <!-- Location -->
                            <div
                                class="bg-white/5 backdrop-blur-sm rounded-xl p-4 border border-white/10"
                            >
                                <div class="flex items-center space-x-2 mb-3">
                                    <MapPinIcon class="w-4 h-4 text-blue-400" />
                                    <span
                                        class="text-xs font-semibold text-slate-300 uppercase tracking-wide"
                                        >Location Details</span
                                    >
                                </div>
                                <div class="space-y-1">
                                    <p class="text-white text-base">
                                        {{ reportDetails.purok }},
                                        {{ reportDetails.barangay }}
                                    </p>
                                    <p class="text-slate-300 text-sm">
                                        {{ reportDetails.municipality }},
                                        {{ reportDetails.province }}
                                    </p>
                                    <p
                                        class="text-slate-400 text-xs flex items-center"
                                    >
                                        <MapPin class="w-3 h-3 mr-1" />
                                        Zone: {{ reportDetails.zone }}
                                    </p>
                                </div>
                            </div>

                            <!-- Media Gallery -->
                            <div
                                v-if="
                                    reportDetails.photos &&
                                    reportDetails.photos.length
                                "
                                class="bg-white/5 backdrop-blur-sm rounded-xl p-4 border border-white/10"
                            >
                                <div class="flex items-center space-x-2 mb-4">
                                    <Image class="w-4 h-4 text-blue-400" />
                                    <span
                                        class="text-xs font-semibold text-slate-300 uppercase tracking-wide"
                                        >Media Evidence</span
                                    >
                                </div>
                                <div
                                    class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3"
                                >
                                    <div
                                        v-for="(
                                            media, index
                                        ) in reportDetails.photos"
                                        :key="index"
                                        class="relative group aspect-square rounded-lg overflow-hidden border border-white/10 hover:border-blue-500/50 transition-all cursor-pointer bg-white/5"
                                    >
                                        <img
                                            :src="'/storage/' + media.path"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                        />
                                        <div
                                            class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-all flex items-center justify-center"
                                        >
                                            <ZoomIn
                                                class="w-5 h-5 text-white opacity-0 group-hover:opacity-100 transition-opacity"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column - QR Code & Actions -->
                        <div class="space-y-4">
                            <!-- QR Code -->
                            <div
                                class="bg-white/5 backdrop-blur-sm rounded-xl p-4 border border-white/10"
                            >
                                <div class="flex items-center space-x-2 mb-3">
                                    <QrCode class="w-4 h-4 text-blue-400" />
                                    <span
                                        class="text-xs font-semibold text-slate-300 uppercase tracking-wide"
                                        >Tracking QR</span
                                    >
                                </div>
                                <div
                                    class="flex justify-center p-4 bg-white/5 rounded-lg"
                                >
                                    <canvas
                                        ref="qrCodeCanvas"
                                        class="rounded bg-white p-1"
                                        width="140"
                                        height="140"
                                    ></canvas>
                                </div>
                                <p
                                    class="text-slate-400 text-xs text-center mt-3"
                                >
                                    Scan this QR code to quickly access this
                                    report anytime
                                </p>
                            </div>

                            <!-- Quick Actions -->
                            <div
                                class="bg-white/5 backdrop-blur-sm rounded-xl p-4 border border-white/10"
                            >
                                <h4
                                    class="text-base font-semibold text-white mb-3"
                                >
                                    Quick Actions
                                </h4>
                                <div class="space-y-2">
                                    <button
                                        @click="resetForm"
                                        class="w-full flex items-center justify-between p-3 bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 rounded-lg transition-all group text-sm"
                                    >
                                        <span
                                            class="text-slate-300 group-hover:text-white"
                                            >Search Another Report</span
                                        >
                                        <ArrowRight
                                            class="w-3 h-3 text-slate-400 group-hover:text-white transform group-hover:translate-x-0.5 transition-transform"
                                        />
                                    </button>

                                    <Link
                                        :href="route('select-roles')"
                                        class="w-full flex items-center justify-between p-3 bg-blue-500/10 hover:bg-blue-500/20 border border-blue-500/20 hover:border-blue-500/30 rounded-lg transition-all group text-sm"
                                    >
                                        <span
                                            class="text-blue-300 group-hover:text-blue-200"
                                            >Report New Issue</span
                                        >
                                        <ArrowRight
                                            class="w-3 h-3 text-blue-400 group-hover:text-blue-300 transform group-hover:translate-x-0.5 transition-transform"
                                        />
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </Transition>

        <!-- CTA Section - Adjusted sizes -->
        <section v-if="!reportDetails" class="py-16 px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto text-center">
                <div
                    class="bg-gradient-to-r from-blue-500/10 to-cyan-500/10 backdrop-blur-sm rounded-2xl p-8 border border-white/10"
                >
                    <h2 class="text-2xl md:text-3xl font-bold text-white mb-3">
                        Need to Report an Issue?
                    </h2>
                    <p class="text-lg text-slate-300 mb-6 max-w-2xl mx-auto">
                        Quickly submit a new water service issue and get a
                        tracking code to monitor its progress.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <Link
                            :href="route('select-roles')"
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold rounded-lg transition-all shadow-lg hover:shadow-xl text-sm"
                        >
                            <Droplet class="w-4 h-4 mr-2" />
                            Report New Issue
                            <ArrowRight
                                class="w-4 h-4 ml-1 transform group-hover:translate-x-0.5 transition-transform"
                            />
                        </Link>
                        <Link
                            href="/"
                            class="inline-flex items-center px-6 py-3 border border-white/20 hover:border-white/30 text-slate-300 hover:text-white font-semibold rounded-lg transition-all backdrop-blur-sm text-sm"
                        >
                            Return to Home
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <Footer />

        <!-- QR Scanner Modal - Adjusted sizes -->
        <Transition name="modal-backdrop">
            <div
                v-if="showQrScanner"
                class="fixed inset-0 z-[1000] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
            >
                <div
                    class="relative w-full max-w-xs bg-white rounded-xl overflow-hidden shadow-2xl border border-gray-200"
                >
                    <div class="px-4 py-3 border-b border-gray-200 bg-white">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <Camera class="w-4 h-4 text-blue-600" />
                                <h3
                                    class="text-base font-semibold text-gray-900"
                                >
                                    Scan QR Code
                                </h3>
                            </div>
                            <button
                                @click="closeQrScanner"
                                class="p-1.5 hover:bg-gray-100 rounded transition-colors"
                            >
                                <X
                                    class="w-4 h-4 text-gray-400 hover:text-gray-600"
                                />
                            </button>
                        </div>
                    </div>

                    <div class="p-4">
                        <div
                            v-if="scannerError"
                            class="mb-3 p-3 bg-red-50 border border-red-200 rounded-lg"
                        >
                            <p
                                class="text-red-700 text-xs flex items-center space-x-1.5"
                            >
                                <AlertTriangle
                                    class="w-3.5 h-3.5 text-red-500 flex-shrink-0"
                                />
                                <span>{{ scannerError }}</span>
                            </p>
                        </div>

                        <div
                            class="relative bg-black rounded-lg overflow-hidden aspect-square border border-gray-300"
                        >
                            <video
                                ref="videoRef"
                                autoplay
                                playsinline
                                muted
                                class="w-full h-full object-cover"
                            ></video>
                            <div
                                class="absolute inset-0 flex items-center justify-center"
                            >
                                <div
                                    class="w-40 h-40 border border-white/50 rounded-lg relative"
                                >
                                    <div
                                        class="absolute -top-0.5 -left-0.5 w-4 h-4 border-l border-t border-white rounded-tl"
                                    ></div>
                                    <div
                                        class="absolute -top-0.5 -right-0.5 w-4 h-4 border-r border-t border-white rounded-tr"
                                    ></div>
                                    <div
                                        class="absolute -bottom-0.5 -left-0.5 w-4 h-4 border-l border-b border-white rounded-bl"
                                    ></div>
                                    <div
                                        class="absolute -bottom-0.5 -right-0.5 w-4 h-4 border-r border-b border-white rounded-br"
                                    ></div>
                                    <div
                                        class="absolute inset-x-0 top-0 h-0.5 bg-blue-400 animate-scan-line"
                                    ></div>
                                </div>
                            </div>
                            <canvas ref="canvasRef" class="hidden"></canvas>
                        </div>

                        <p class="mt-3 text-xs text-gray-600 text-center">
                            Position QR code within the frame to scan
                        </p>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
/* Fixed background solution */
.fixed-bg {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
    z-index: -10;
}

.content-slide-enter-active,
.content-slide-leave-active {
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.content-slide-enter-from,
.content-slide-leave-to {
    opacity: 0;
    transform: translateY(30px);
}

.error-slide-enter-active,
.error-slide-leave-active {
    transition: all 0.3s ease;
}

.error-slide-enter-from,
.error-slide-leave-to {
    opacity: 0;
    transform: translateX(-10px);
}

.modal-backdrop-enter-active,
.modal-backdrop-leave-active {
    transition: opacity 0.3s ease;
}

.modal-backdrop-enter-from,
.modal-backdrop-leave-to {
    opacity: 0;
}

@keyframes scan-line {
    0% {
        top: 0;
        opacity: 0;
    }
    50% {
        opacity: 1;
    }
    100% {
        top: 100%;
        opacity: 0;
    }
}

.animate-scan-line {
    animation: scan-line 2s ease-in-out infinite;
}
</style>
