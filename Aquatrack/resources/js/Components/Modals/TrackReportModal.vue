// TrackReportModal.vue
<script setup>
import { useForm } from "@inertiajs/vue3";
import { ref, watch, nextTick, onMounted, onUnmounted } from "vue";
import axios from "axios";
import QRCode from "qrcode";
import { computed } from "vue";
import html2canvas from "html2canvas";
import jsQR from "jsqr";
import {
    Search,
    X,
    CheckCircle,
    Calendar,
    MapPin,
    Download,
    Image,
    AlertTriangle,
    Play,
    ZoomIn,
    Camera,
    ArrowLeft,
    Droplet,
    QrCode,
    User,
    FileText,
} from "lucide-vue-next";

const props = defineProps({
    show: { type: Boolean, default: false },
    initialTrackingCode: { type: String, default: "" },
    isDeleted: { type: Boolean, default: false },
    deletionReason: { type: String, default: null },
});

const emit = defineEmits(["close"]);
const reportDetails = ref(null);
const isLoading = ref(false);
const errorMessage = ref(null);
const qrCodeCanvas = ref(null);
const showLoadingDelay = ref(false);
const modalVisible = ref(false);
const modalRef = ref(null);

// QR Scanner state
const showQrScanner = ref(false);
const videoRef = ref(null);
const canvasRef = ref(null);
const stream = ref(null);
const scanning = ref(false);
const scannerError = ref(null);

// Media modal state
const mediaModal = ref({
    show: false,
    src: "",
    type: "image",
    currentIndex: 0,
    allImages: [],
});

const form = useForm({ tracking_code: props.initialTrackingCode });

onMounted(() => {
    if (props.show) {
        modalVisible.value = true;
        if (props.initialTrackingCode) {
            form.tracking_code = props.initialTrackingCode;
            trackReport();
        }
    }
});

onUnmounted(() => stopCamera());

watch(
    () => props.show,
    (newVal) => {
        modalVisible.value = newVal;
        if (newVal && props.initialTrackingCode) {
            form.tracking_code = props.initialTrackingCode;
            trackReport();
        } else if (!newVal) resetModal();
    }
);

const trackReport = async () => {
    if (!form.tracking_code || !form.tracking_code.trim()) {
        errorMessage.value = "Please enter a valid tracking code.";
        return;
    }

    isLoading.value = true;
    showLoadingDelay.value = true;
    errorMessage.value = null;
    reportDetails.value = null;

    try {
        const response = await axios.get(route("reports.find"), {
            params: { tracking_code: form.tracking_code },
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
        showLoadingDelay.value = false;
    }
};

// QR Scanner functions
const startQrScanner = async () => {
    try {
        scannerError.value = null;
        showQrScanner.value = true;

        stream.value = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: "environment" },
        });

        await nextTick();

        if (videoRef.value) {
            videoRef.value.srcObject = stream.value;
            videoRef.value.play();
            scanning.value = true;
            scanQrCode();
        }
    } catch (error) {
        scannerError.value = "Camera access denied. Please check permissions.";
        showQrScanner.value = false;
    }
};

const stopCamera = () => {
    if (stream.value) {
        stream.value.getTracks().forEach((track) => track.stop());
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
            form.tracking_code = code.data;
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

const generateQRCode = () => {
    if (!reportDetails.value?.tracking_code || !qrCodeCanvas.value) return;

    QRCode.toCanvas(
        qrCodeCanvas.value,
        reportDetails.value.tracking_code,
        {
            width: 120,
            margin: 1,
            color: { dark: "#000000", light: "#ffffff" },
        },
        (error) => {
            if (error) console.error("QR Code generation error:", error);
        }
    );
};

const resetModal = () => {
    form.reset();
    reportDetails.value = null;
    errorMessage.value = null;
    mediaModal.value.show = false;
};

const resetForm = () => {
    form.reset();
    reportDetails.value = null;
    errorMessage.value = null;
    mediaModal.value.show = false;
};

watch(
    reportDetails,
    () => {
        if (reportDetails.value) {
            nextTick(() => {
                generateQRCode();
            });
        }
    },
    { deep: true }
);

const closeModal = () => {
    closeQrScanner();
    resetModal();
    modalVisible.value = false;
    setTimeout(() => {
        emit("close");
    }, 200);
};

const openMediaModal = (src, type, index = 0) => {
    if (type === "image") {
        const images = reportDetails.value.photos
            .filter((media) => !isVideoFile(media))
            .map((media) => "/storage/" + media.path);

        mediaModal.value = {
            show: true,
            src: src,
            type: "image",
            currentIndex: images.indexOf(src),
            allImages: images,
        };
    } else {
        mediaModal.value = {
            show: true,
            src: src,
            type: "video",
            currentIndex: 0,
            allImages: [],
        };
    }
};

const nextImage = () => {
    if (mediaModal.value.allImages.length > 0) {
        mediaModal.value.currentIndex =
            (mediaModal.value.currentIndex + 1) %
            mediaModal.value.allImages.length;
        mediaModal.value.src =
            mediaModal.value.allImages[mediaModal.value.currentIndex];
    }
};

const prevImage = () => {
    if (mediaModal.value.allImages.length > 0) {
        mediaModal.value.currentIndex =
            (mediaModal.value.currentIndex -
                1 +
                mediaModal.value.allImages.length) %
            mediaModal.value.allImages.length;
        mediaModal.value.src =
            mediaModal.value.allImages[mediaModal.value.currentIndex];
    }
};

const closeMediaModal = () => {
    mediaModal.value.show = false;
    document.querySelectorAll("video").forEach((video) => {
        video.pause();
    });
};

const handleKeydown = (event) => {
    if (mediaModal.value.show && mediaModal.value.type === "image") {
        if (event.key === "Escape") {
            closeMediaModal();
        } else if (event.key === "ArrowRight") {
            nextImage();
        } else if (event.key === "ArrowLeft") {
            prevImage();
        }
    }
};

onMounted(() => {
    window.addEventListener("keydown", handleKeydown);
});

onUnmounted(() => {
    window.removeEventListener("keydown", handleKeydown);
});

const getVideoMimeType = (src) => {
    const extension = src.split(".").pop().toLowerCase();
    switch (extension) {
        case "mp4":
            return "video/mp4";
        case "webm":
            return "video/webm";
        case "ogg":
            return "video/ogg";
        default:
            return "video/mp4";
    }
};

const formattedStatus = computed(() => {
    if (!reportDetails.value?.status) return "";
    switch (reportDetails.value.status) {
        case "in_progress":
            return "In Progress";
        case "pending":
            return "Pending";
        case "resolved":
            return "Resolved";
        default:
            return (
                reportDetails.value.status.charAt(0).toUpperCase() +
                reportDetails.value.status.slice(1).toLowerCase()
            );
    }
});

const formattedPriority = computed(() => {
    if (!reportDetails.value?.priority) return "Not Specified";
    return (
        reportDetails.value.priority.charAt(0).toUpperCase() +
        reportDetails.value.priority.slice(1)
    );
});

const isVideoFile = (media) => {
    return (
        media.type === "video" ||
        (media.mime_type && media.mime_type.includes("video"))
    );
};
</script>

<template>
    <Transition name="modal-backdrop">
        <div
            v-if="modalVisible"
            class="fixed inset-0 z-[500] flex items-center justify-center p-4"
        >
            <div
                class="fixed inset-0 bg-black/80"
                @click="closeModal"
            ></div>

            <Transition name="modal-content">
                <div
                    v-if="modalVisible"
                    ref="modalRef"
                    class="relative w-full max-w-5xl max-h-[100vh] bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-100"
                >
                    <!-- Header -->
                    <div class="px-6 py-5 border-b border-gray-100 bg-white">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div
                                    class="w-10 h-10 bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl flex items-center justify-center shadow-sm"
                                >
                                    <Droplet :size="20" class="text-white" />
                                </div>
                                <div>
                                    <h3
                                        class="text-xl font-semibold text-gray-900"
                                    >
                                        Track Water Report
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        Find and monitor your submitted reports
                                    </p>
                                </div>
                            </div>
                            <button
                                @click="closeModal"
                                class="p-2 hover:bg-gray-100 rounded-xl transition-colors"
                            >
                                <X
                                    :size="20"
                                    class="text-gray-400 hover:text-gray-600"
                                />
                            </button>
                        </div>
                    </div>

                    <div class="max-h-[calc(95vh-140px)] overflow-y-auto">
                        <div class="p-6 space-y-6">
                            <!-- Search Section -->
                            <div
                                class="bg-gradient-to-br from-gray-50 to-blue-50 rounded-2xl p-6 border border-gray-200"
                            >
                                <div class="flex items-center space-x-3 mb-4">
                                    <div
                                        class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center"
                                    >
                                        <Search :size="16" class="text-white" />
                                    </div>
                                    <div>
                                        <h4
                                            class="text-lg font-semibold text-gray-900"
                                        >
                                            Search Report
                                        </h4>
                                        <p class="text-sm text-gray-600">
                                            Enter your tracking code to find
                                            your report
                                        </p>
                                    </div>
                                </div>

                                <form
                                    @submit.prevent="trackReport"
                                    class="space-y-4"
                                >
                                    <div class="space-y-3">
                                        <label
                                            class="block text-sm font-medium text-gray-700"
                                            >Tracking Code</label
                                        >
                                        <div class="relative">
                                            <input
                                                v-model="form.tracking_code"
                                                type="text"
                                                required
                                                class="w-full h-12 pl-11 pr-12 text-sm bg-white border border-gray-300 rounded-xl focus:border-blue-500 focus:ring-3 focus:ring-blue-500/20 transition-all placeholder:text-gray-400 font-mono shadow-sm"
                                                placeholder="Enter tracking code"
                                            />
                                            <div
                                                class="absolute left-3 top-1/2 -translate-y-1/2"
                                            >
                                                <QrCode
                                                    :size="18"
                                                    class="text-gray-400"
                                                />
                                            </div>
                                            <button
                                                type="button"
                                                @click="startQrScanner"
                                                class="absolute right-3 top-1/2 -translate-y-1/2 p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all"
                                                title="Scan QR Code"
                                            >
                                                <Camera :size="18" />
                                            </button>
                                        </div>
                                    </div>

                                    <Transition name="error-slide">
                                        <div
                                            v-if="errorMessage"
                                            class="flex items-center space-x-3 p-4 bg-red-50 border border-red-200 rounded-xl"
                                        >
                                            <AlertTriangle
                                                :size="18"
                                                class="text-red-500 flex-shrink-0"
                                            />
                                            <span
                                                class="text-red-700 text-sm font-medium"
                                                >{{ errorMessage }}</span
                                            >
                                        </div>
                                    </Transition>

                                    <button
                                        type="submit"
                                        :disabled="
                                            isLoading ||
                                            !form.tracking_code.trim()
                                        "
                                        class="w-full h-12 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 disabled:from-gray-300 disabled:to-gray-400 text-white font-medium rounded-xl transition-all flex items-center justify-center space-x-2 shadow-sm hover:shadow-md disabled:shadow-none"
                                    >
                                        <Search :size="18" />
                                        <span class="text-sm">{{
                                            isLoading
                                                ? "Searching..."
                                                : "Track Report"
                                        }}</span>
                                    </button>
                                </form>
                            </div>

                            <!-- Loading State -->
                            <Transition name="loading-fade">
                                <div
                                    v-if="isLoading"
                                    class="flex flex-col items-center justify-center py-12 space-y-4"
                                >
                                    <div class="relative">
                                        <div
                                            class="w-16 h-16 border-4 border-blue-100 rounded-full"
                                        ></div>
                                        <div
                                            class="absolute inset-0 w-16 h-16 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"
                                        ></div>
                                    </div>
                                    <div class="text-center">
                                        <h4
                                            class="text-base font-semibold text-gray-700"
                                        >
                                            Searching for report
                                        </h4>
                                        <p class="text-sm text-gray-500">
                                            Please wait while we find your
                                            report details
                                        </p>
                                    </div>
                                </div>
                            </Transition>

                            <!-- Report Details -->
                            <Transition name="content-slide">
                                <div v-if="reportDetails" class="space-y-6">
                                    <!-- Status Header -->
                                    <div
                                        class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl p-6 border border-green-200"
                                    >
                                        <div
                                            class="flex items-center justify-between"
                                        >
                                            <div
                                                class="flex items-center space-x-3"
                                            >
                                                <div
                                                    class="w-10 h-10 bg-green-500 rounded-xl flex items-center justify-center"
                                                >
                                                    <CheckCircle
                                                        :size="20"
                                                        class="text-white"
                                                    />
                                                </div>
                                                <div>
                                                    <h4
                                                        class="text-lg font-semibold text-gray-900"
                                                    >
                                                        Report Found
                                                    </h4>
                                                    <p
                                                        class="text-sm text-gray-600"
                                                    >
                                                        Tracking Code:
                                                        <span
                                                            class="font-mono font-semibold text-blue-600"
                                                            >{{
                                                                reportDetails.tracking_code
                                                            }}</span
                                                        >
                                                    </p>
                                                </div>
                                            </div>
                                            <span
                                                class="px-4 py-2 rounded-lg text-sm font-semibold"
                                                :class="{
                                                    'bg-green-100 text-green-700 border border-green-200':
                                                        reportDetails.status ===
                                                        'resolved',
                                                    'bg-yellow-100 text-yellow-700 border border-yellow-200':
                                                        reportDetails.status ===
                                                        'pending',
                                                    'bg-blue-100 text-blue-700 border border-blue-200':
                                                        reportDetails.status ===
                                                        'in_progress',
                                                }"
                                            >
                                                {{ formattedStatus }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Main Content Grid -->
                                    <div
                                        class="grid grid-cols-1 lg:grid-cols-3 gap-6"
                                    >
                                        <!-- Left Column - Basic Info -->
                                        <div class="lg:col-span-2 space-y-6">
                                            <!-- Reporter & Priority -->
                                            <div
                                                class="grid grid-cols-1 md:grid-cols-2 gap-4"
                                            >
                                                <div
                                                    class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm"
                                                >
                                                    <div
                                                        class="flex items-center space-x-2 mb-3"
                                                    >
                                                        <User
                                                            :size="16"
                                                            class="text-blue-500"
                                                        />
                                                        <span
                                                            class="text-xs font-semibold text-gray-500 uppercase tracking-wide"
                                                            >Reporter</span
                                                        >
                                                    </div>
                                                    <p
                                                        class="font-semibold text-gray-900 text-lg"
                                                    >
                                                        {{
                                                            reportDetails.reporter_name
                                                        }}
                                                    </p>
                                                </div>
                                                <div
                                                    class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm"
                                                >
                                                    <div
                                                        class="flex items-center space-x-2 mb-3"
                                                    >
                                                        <AlertTriangle
                                                            :size="16"
                                                            class="text-amber-500"
                                                        />
                                                        <span
                                                            class="text-xs font-semibold text-gray-500 uppercase tracking-wide"
                                                            >Priority</span
                                                        >
                                                    </div>
                                                    <span
                                                        class="inline-flex px-3 py-1.5 rounded-lg text-sm font-semibold"
                                                        :class="{
                                                            'bg-red-100 text-red-700 border border-red-200':
                                                                reportDetails.priority ===
                                                                'high',
                                                            'bg-amber-100 text-amber-700 border border-amber-200':
                                                                reportDetails.priority ===
                                                                'medium',
                                                            'bg-green-100 text-green-700 border border-green-200':
                                                                reportDetails.priority ===
                                                                'low',
                                                        }"
                                                    >
                                                        {{ formattedPriority }}
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Description -->
                                            <div
                                                class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm"
                                            >
                                                <div
                                                    class="flex items-center space-x-2 mb-4"
                                                >
                                                    <FileText
                                                        :size="16"
                                                        class="text-blue-500"
                                                    />
                                                    <span
                                                        class="text-xs font-semibold text-gray-500 uppercase tracking-wide"
                                                        >Issue Description</span
                                                    >
                                                </div>
                                                <p
                                                    class="text-gray-700 leading-relaxed whitespace-pre-line"
                                                >
                                                    {{
                                                        reportDetails.description
                                                    }}
                                                </p>
                                            </div>

                                            <!-- Location -->
                                            <div
                                                class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm"
                                            >
                                                <div
                                                    class="flex items-center space-x-2 mb-4"
                                                >
                                                    <MapPin
                                                        :size="16"
                                                        class="text-blue-500"
                                                    />
                                                    <span
                                                        class="text-xs font-semibold text-gray-500 uppercase tracking-wide"
                                                        >Location Details</span
                                                    >
                                                </div>
                                                <p class="text-gray-700">
                                                    {{ reportDetails.purok }},
                                                    {{
                                                        reportDetails.barangay
                                                    }},
                                                    {{
                                                        reportDetails.municipality
                                                    }},
                                                    {{ reportDetails.province }}
                                                </p>
                                                <p
                                                    class="text-sm text-gray-500 mt-1"
                                                >
                                                    Zone:
                                                    {{ reportDetails.zone }}
                                                </p>
                                            </div>

                                            <!-- Media -->
                                            <div
                                                v-if="
                                                    reportDetails.photos &&
                                                    reportDetails.photos.length
                                                "
                                                class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm"
                                            >
                                                <div
                                                    class="flex items-center space-x-2 mb-4"
                                                >
                                                    <Image
                                                        :size="16"
                                                        class="text-blue-500"
                                                    />
                                                    <span
                                                        class="text-xs font-semibold text-gray-500 uppercase tracking-wide"
                                                        >Media Evidence</span
                                                    >
                                                </div>
                                                <div
                                                    class="grid grid-cols-3 sm:grid-cols-4 gap-3"
                                                >
                                                    <div
                                                        v-for="(
                                                            media, index
                                                        ) in reportDetails.photos"
                                                        :key="index"
                                                        class="relative group aspect-square rounded-lg overflow-hidden border border-gray-200 hover:border-blue-300 transition-all hover:shadow-md cursor-pointer bg-gray-50"
                                                        @click="
                                                            openMediaModal(
                                                                '/storage/' +
                                                                    media.path,
                                                                isVideoFile(
                                                                    media
                                                                )
                                                                    ? 'video'
                                                                    : 'image',
                                                                index
                                                            )
                                                        "
                                                    >
                                                        <template
                                                            v-if="
                                                                isVideoFile(
                                                                    media
                                                                )
                                                            "
                                                        >
                                                            <div
                                                                class="w-full h-full bg-gray-800 flex items-center justify-center relative"
                                                            >
                                                                <div
                                                                    class="absolute inset-0 bg-black/30 flex items-center justify-center"
                                                                >
                                                                    <Play
                                                                        :size="
                                                                            24
                                                                        "
                                                                        class="text-white"
                                                                        fill="currentColor"
                                                                    />
                                                                </div>
                                                            </div>
                                                        </template>
                                                        <template v-else>
                                                            <img
                                                                :src="
                                                                    '/storage/' +
                                                                    media.path
                                                                "
                                                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                                            />
                                                            <div
                                                                class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-all flex items-center justify-center"
                                                            >
                                                                <div
                                                                    class="opacity-0 group-hover:opacity-100 transition-opacity"
                                                                >
                                                                    <ZoomIn
                                                                        :size="
                                                                            20
                                                                        "
                                                                        class="text-white bg-black/50 p-1.5 rounded-full"
                                                                    />
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Right Column - QR Code -->
                                        <div class="space-y-6">
                                            <div
                                                class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm"
                                            >
                                                <div
                                                    class="flex items-center space-x-2 mb-4"
                                                >
                                                    <QrCode
                                                        :size="16"
                                                        class="text-blue-500"
                                                    />
                                                    <span
                                                        class="text-xs font-semibold text-gray-500 uppercase tracking-wide"
                                                        >Tracking QR</span
                                                    >
                                                </div>
                                                <div
                                                    class="flex justify-center p-4 bg-gray-50 rounded-lg"
                                                >
                                                    <canvas
                                                        ref="qrCodeCanvas"
                                                        class="rounded-lg"
                                                    ></canvas>
                                                </div>
                                                <p
                                                    class="text-xs text-gray-500 text-center mt-3"
                                                >
                                                    Scan this QR code to quickly
                                                    access this report
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </Transition>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="border-t border-gray-100 px-6 py-4 bg-gray-50">
                        <div class="flex justify-between items-center">
                            <button
                                v-if="reportDetails"
                                @click="resetForm"
                                class="flex items-center space-x-2 px-4 py-2.5 border border-gray-300 hover:border-gray-400 bg-white hover:bg-gray-50 text-gray-700 font-medium rounded-xl transition-all shadow-sm"
                            >
                                <ArrowLeft :size="16" />
                                <span class="text-sm">Search Again</span>
                            </button>
                            <button
                                v-else
                                @click="closeModal"
                                class="flex items-center space-x-2 px-4 py-2.5 border border-gray-300 hover:border-gray-400 bg-white hover:bg-gray-50 text-gray-700 font-medium rounded-xl transition-all shadow-sm"
                            >
                                <X :size="16" />
                                <span class="text-sm">Cancel</span>
                            </button>

                            <button
                                v-if="reportDetails"
                                @click="downloadReportAsImage"
                                class="flex items-center space-x-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition-all shadow-sm download-btn"
                            >
                                <Download :size="16" />
                                <span class="text-sm">Download Report</span>
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </div>
    </Transition>

    <!-- QR Scanner Modal -->
    <Transition name="modal-backdrop">
        <div
            v-if="showQrScanner"
            class="fixed inset-0 z-[1000] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
        >
            <div
                class="relative w-full max-w-sm bg-white rounded-2xl overflow-hidden shadow-2xl border border-gray-200"
            >
                <div class="px-5 py-4 border-b border-gray-200 bg-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <Camera :size="20" class="text-blue-600" />
                            <h3 class="text-lg font-semibold text-gray-900">
                                Scan QR Code
                            </h3>
                        </div>
                        <button
                            @click="closeQrScanner"
                            class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
                        >
                            <X
                                :size="18"
                                class="text-gray-400 hover:text-gray-600"
                            />
                        </button>
                    </div>
                </div>

                <div class="p-5">
                    <div
                        v-if="scannerError"
                        class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl"
                    >
                        <p
                            class="text-red-700 text-sm flex items-center space-x-2"
                        >
                            <AlertTriangle
                                :size="16"
                                class="text-red-500 flex-shrink-0"
                            />
                            <span>{{ scannerError }}</span>
                        </p>
                    </div>

                    <div
                        class="relative bg-black rounded-xl overflow-hidden aspect-square border-2 border-gray-300"
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
                                class="w-48 h-48 border-2 border-white/50 rounded-xl relative"
                            >
                                <div
                                    class="absolute -top-1 -left-1 w-5 h-5 border-l-2 border-t-2 border-white rounded-tl"
                                ></div>
                                <div
                                    class="absolute -top-1 -right-1 w-5 h-5 border-r-2 border-t-2 border-white rounded-tr"
                                ></div>
                                <div
                                    class="absolute -bottom-1 -left-1 w-5 h-5 border-l-2 border-b-2 border-white rounded-bl"
                                ></div>
                                <div
                                    class="absolute -bottom-1 -right-1 w-5 h-5 border-r-2 border-b-2 border-white rounded-br"
                                ></div>
                                <div
                                    class="absolute inset-x-0 top-0 h-0.5 bg-blue-400 animate-scan-line"
                                ></div>
                            </div>
                        </div>
                        <canvas ref="canvasRef" class="hidden"></canvas>
                    </div>

                    <p class="mt-4 text-sm text-gray-600 text-center">
                        Position QR code within the frame to scan
                    </p>
                </div>
            </div>
        </div>
    </Transition>

    <!-- Media Modal -->
    <Transition name="modal-backdrop">
        <div
            v-if="mediaModal.show"
            class="fixed inset-0 z-[1000] flex items-center justify-center p-4 bg-black/90 backdrop-blur-sm"
            @click="closeMediaModal"
        >
            <button
                @click.stop="closeMediaModal"
                class="absolute top-6 right-6 text-white hover:text-gray-300 transition-colors p-3 rounded-full hover:bg-white/10 z-10"
            >
                <X :size="24" />
            </button>

            <button
                v-if="
                    mediaModal.type === 'image' &&
                    mediaModal.allImages.length > 1
                "
                @click.stop="prevImage"
                class="absolute left-6 top-1/2 transform -translate-y-1/2 text-white hover:text-gray-300 transition-colors p-4 rounded-full hover:bg-white/10 z-10"
            >
                <ArrowLeft :size="24" />
            </button>

            <button
                v-if="
                    mediaModal.type === 'image' &&
                    mediaModal.allImages.length > 1
                "
                @click.stop="nextImage"
                class="absolute right-6 top-1/2 transform -translate-y-1/2 text-white hover:text-gray-300 transition-colors p-4 rounded-full hover:bg-white/10 z-10"
            >
                <ArrowLeft :size="24" class="rotate-180" />
            </button>

            <div
                v-if="
                    mediaModal.type === 'image' &&
                    mediaModal.allImages.length > 1
                "
                class="absolute top-6 left-1/2 transform -translate-x-1/2 text-white bg-black/50 px-4 py-2 rounded-full text-sm z-10"
            >
                {{ mediaModal.currentIndex + 1 }} /
                {{ mediaModal.allImages.length }}
            </div>

            <div
                class="relative w-full max-w-6xl max-h-[90vh] mx-4"
                @click.stop
            >
                <div
                    v-if="mediaModal.type === 'video'"
                    class="aspect-video w-full"
                >
                    <video
                        controls
                        autoplay
                        class="w-full h-full rounded-2xl shadow-2xl"
                    >
                        <source
                            :src="mediaModal.src"
                            :type="getVideoMimeType(mediaModal.src)"
                        />
                    </video>
                </div>
                <img
                    v-else
                    :src="mediaModal.src"
                    class="max-h-[80vh] w-full object-contain rounded-2xl shadow-2xl"
                />
            </div>
        </div>
    </Transition>
</template>

<style scoped>
.modal-backdrop-enter-active,
.modal-backdrop-leave-active {
    transition: opacity 0.3s ease;
}

.modal-backdrop-enter-from,
.modal-backdrop-leave-to {
    opacity: 0;
}

.modal-content-enter-active,
.modal-content-leave-active {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.modal-content-enter-from,
.modal-content-leave-to {
    opacity: 0;
    transform: scale(0.95) translateY(-10px);
}

.content-slide-enter-active,
.content-slide-leave-active {
    transition: all 0.4s ease;
}

.content-slide-enter-from,
.content-slide-leave-to {
    opacity: 0;
    transform: translateY(20px);
}

.loading-fade-enter-active,
.loading-fade-leave-active {
    transition: all 0.3s ease;
}

.loading-fade-enter-from,
.loading-fade-leave-to {
    opacity: 0;
    transform: scale(0.9);
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

.group:hover .group-hover\:scale-105 {
    transform: scale(1.05);
}

.group:hover .group-hover\:opacity-100 {
    opacity: 1;
}
</style>
