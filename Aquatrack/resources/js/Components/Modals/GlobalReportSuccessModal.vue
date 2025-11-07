<!-- GlobalReportSuccessModal.vue -->
<script setup>
import { ref, onMounted, watch, computed, nextTick } from "vue";
import QRCode from "qrcode";
import { router } from "@inertiajs/vue3";
import { Check, Download, AlertTriangle } from "lucide-vue-next";

const props = defineProps({
    trackingInfo: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(["close", "submit-another", "track-report"]);
const qrCodeCanvas = ref(null);
const qrError = ref(null);
const isVisible = ref(false);

// Format date properly
const formattedDate = computed(() => {
    if (!props.trackingInfo?.date) return "";
    const date = new Date(props.trackingInfo.date);
    return date.toLocaleString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
});

// Get tracking code
const trackingCode = computed(() => {
    return props.trackingInfo?.code || "";
});

// Start animation when component is mounted
const startAnimation = () => {
    isVisible.value = true;
};

// Redirect to track report page with the tracking code
const trackThisReport = () => {
    if (trackingCode.value) {
        // Close the modal first
        emit("close");

        // Then redirect to the track report page with the tracking code
        router.visit(`/track-report?tracking_code=${trackingCode.value}`);
    }
};

const downloadQRCode = () => {
    if (!qrCodeCanvas.value) return;

    // Create a new canvas that includes the QR code, tracking code, and date
    const canvas = document.createElement("canvas");
    const ctx = canvas.getContext("2d");

    // Set canvas dimensions (QR code + text area)
    canvas.width = 250;
    canvas.height = 320;

    // Fill background
    ctx.fillStyle = "#ffffff";
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    // Draw QR code (centered)
    const qrSize = 180;
    const qrX = (canvas.width - qrSize) / 2;
    ctx.drawImage(qrCodeCanvas.value, qrX, 20, qrSize, qrSize);

    // Draw title
    ctx.fillStyle = "#1E3A8A";
    ctx.font = "bold 16px Arial";
    ctx.textAlign = "center";
    ctx.fillText("AquaTrack Water Report", canvas.width / 2, 220);

    // Draw tracking code
    ctx.fillStyle = "#000000";
    ctx.font = "bold 14px Arial";
    ctx.fillText("Tracking Code:", canvas.width / 2, 245);
    ctx.font = "bold 16px Arial";
    ctx.fillText(trackingCode.value, canvas.width / 2, 265);

    // Draw date
    ctx.font = "12px Arial";
    ctx.fillText("Date Submitted:", canvas.width / 2, 285);
    ctx.font = "12px Arial";
    ctx.fillText(formattedDate.value, canvas.width / 2, 300);

    // Draw border
    ctx.strokeStyle = "#E5E7EB";
    ctx.lineWidth = 1;
    ctx.strokeRect(5, 5, canvas.width - 10, canvas.height - 10);

    // Create download link
    const link = document.createElement("a");
    link.download = `aquatrack-report-${trackingCode.value}.png`;
    link.href = canvas.toDataURL("image/png");
    link.click();
};

const generateQRCode = async () => {
    qrError.value = null;
    try {
        if (!trackingCode.value || !qrCodeCanvas.value) {
            throw new Error("Missing tracking code or canvas element");
        }

        await QRCode.toCanvas(qrCodeCanvas.value, trackingCode.value, {
            width: 180,
            margin: 1,
            color: {
                dark: "#000000",
                light: "#ffffff",
            },
        });
    } catch (error) {
        console.error("QR Code generation error:", error);
        qrError.value = error.message;
    }
};

// Watch for changes and generate QR code
watch(
    () => trackingCode.value,
    async (code) => {
        if (code) {
            await nextTick(); // Wait for DOM update
            generateQRCode();
        }
    },
    { immediate: true }
);

// Initialize when component mounts
onMounted(() => {
    if (trackingCode.value) {
        nextTick().then(() => {
            generateQRCode();
            // Start animation after a small delay
            setTimeout(startAnimation, 100);
        });
    }
});
</script>

<template>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div
            class="flex items-center justify-center min-h-screen p-4 text-center"
        >
            <!-- Background overlay with fade-in animation -->
            <div
                class="fixed inset-0 transition-opacity duration-500"
                :class="isVisible ? 'opacity-100' : 'opacity-0'"
                aria-hidden="true"
            >
                <div
                    class="absolute inset-0 bg-black/80 backdrop-blur-sm"
                ></div>
            </div>

            <!-- Modal container with drop-down animation -->
            <div
                class="inline-block w-full max-w-xl bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all duration-700 ease-out relative"
                :class="[
                    isVisible
                        ? 'opacity-100 translate-y-0 scale-100'
                        : 'opacity-0 -translate-y-20 scale-95',
                ]"
            >
                <!-- Modal content -->
                <div class="bg-white px-6 py-6">
                    <div class="text-center">
                        <!-- Success icon with animation -->
                        <div
                            class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-2 transition-all duration-1000"
                            :class="
                                isVisible
                                    ? 'scale-100 rotate-0'
                                    : 'scale-0 rotate-180'
                            "
                        >
                            <Check
                                :size="24"
                                class="text-green-900 transition-all duration-1000"
                                :class="isVisible ? 'scale-100' : 'scale-0'"
                            />
                        </div>

                        <!-- Heading with animation -->
                        <div
                            class="transition-all duration-700 delay-300"
                            :class="
                                isVisible
                                    ? 'opacity-100 translate-y-0'
                                    : 'opacity-0 translate-y-4'
                            "
                        >
                            <h3
                                class="text-xl font-semibold text-gray-900 mb-1"
                            >
                                Report Submitted Successfully!
                            </h3>
                            <p class="text-xs text-gray-500 mb-4">
                                Your tracking code has been generated
                            </p>
                        </div>

                        <!-- Tracking info with animation -->
                        <div
                            class="bg-[#F1F5F9] rounded-lg p-3 mb-4 text-center transition-all duration-700 delay-500"
                            :class="
                                isVisible
                                    ? 'opacity-100 translate-y-0'
                                    : 'opacity-0 translate-y-4'
                            "
                        >
                            <div class="flex items-center justify-center">
                                <div>
                                    <p
                                        class="text-xs font-sm text-gray-500 mb-4"
                                    >
                                        Your Tracking Code
                                    </p>
                                    <p
                                        class="text-xl font-semibold text-[#0B95DA]"
                                    >
                                        {{ trackingCode }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- QR Code with animation -->
                        <div
                            class="mb-4 flex flex-col items-center transition-all duration-700 delay-700"
                            :class="
                                isVisible
                                    ? 'opacity-100 scale-100'
                                    : 'opacity-0 scale-90'
                            "
                        >
                            <div
                                class="p-2 bg-white rounded-xl border-2 border-[#D7DFEA]"
                            >
                                <canvas
                                    ref="qrCodeCanvas"
                                    width="180"
                                    height="180"
                                    class="w-full h-full mb-2"
                                ></canvas>
                                <div
                                    v-if="qrError"
                                    class="text-red-500 text-xs mt-2"
                                >
                                    QR Code Error: {{ qrError }}
                                </div>
                                <p
                                    class="text-xs text-center mb-2 text-gray-500"
                                >
                                    Scan to track your report
                                </p>
                            </div>
                        </div>

                        <!-- Important notice with animation -->
                        <div
                            class="bg-amber-50 border-l-4 border-amber-400 p-3 rounded-r mb-5 text-left transition-all duration-700 delay-900"
                            :class="
                                isVisible
                                    ? 'opacity-100 translate-y-0'
                                    : 'opacity-0 translate-y-4'
                            "
                        >
                            <div class="flex items-start">
                                <AlertTriangle
                                    :size="16"
                                    class="text-amber-500 flex-shrink-0 mt-0.5"
                                />
                                <div class="ml-2">
                                    <p
                                        class="text-xs font-medium text-amber-800"
                                    >
                                        Important
                                    </p>
                                    <p class="text-xs text-amber-700">
                                        Please be reminded that this QR Code is
                                        very important, once you lose it, you
                                        will not be able to track your report.
                                        Save this QR code and
                                        <button
                                            @click="trackThisReport"
                                            class="text-blue-600 hover:underline focus:outline-none"
                                        >
                                            click here
                                        </button>
                                        to track your report.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Download button with animation -->
                        <div
                            class="flex flex-col sm:flex-row gap-2 transition-all duration-700 delay-1100"
                            :class="
                                isVisible
                                    ? 'opacity-100 translate-y-0'
                                    : 'opacity-0 translate-y-4'
                            "
                        >
                            <button
                                @click="downloadQRCode"
                                type="button"
                                class="flex-1 inline-flex items-center justify-center px-4 py-2 border border-[#0A7EB8] bg-[#0A7EB8] text-white text-sm font-medium rounded-lg shadow-sm hover:bg-[#086899] hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200"
                            >
                                <Download :size="16" class="mr-2" />
                                Download QR
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
