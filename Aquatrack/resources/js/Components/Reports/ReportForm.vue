<template>
    <div class="w-full p-6 bg-white">
        <NetworkStatus
            :is-online="isOnline"
            :is-syncing="isSyncing"
            :offline-reports-count="offlineReportsCount"
            @manual-sync="triggerManualSync"
        />

        <FormErrors v-if="hasErrors" :errors="form.errors" />

        <form @submit.prevent="submitReport" class="space-y-6">
            <LocationFields :form="form" />
            <ReporterInfo :form="form" @update:field="updateFormField" />
            <AreaInfo
                :form="form"
                :zones="props.zones"
                @update:field="updateFormField"
            />
            <WaterIssueType :form="form" @update:field="updateFormField" />
            <DescriptionField :form="form" @update:field="updateFormField" />

            <CameraSection
                ref="cameraSectionRef"
                :form="form"
                :is-camera-active="isCameraActive"
                :is-camera-ready="isCameraReady"
                :is-camera-loading="isCameraLoading"
                :is-switching-camera="isSwitchingCamera"
                :is-capturing="isCapturing"
                :is-recording="isRecording"
                :recording-time="recordingTime"
                :has-multiple-cameras="hasMultipleCameras"
                :camera-error="cameraError"
                :camera-status="cameraStatus"
                :media-count="mediaCount"
                :MAX_PHOTOS="MAX_PHOTOS"
                :MAX_VIDEOS="MAX_VIDEOS"
                :MAX_VIDEO_DURATION="MAX_VIDEO_DURATION"
                @retry-camera="retryCamera"
                @initialize-camera="initializeCamera"
                @switch-camera="switchCamera"
                @capture-photo="capturePhoto"
                @start-recording="startVideoRecording"
                @stop-recording="stopVideoRecording"
                @stop-camera="stopCamera"
                @remove-media="removeMedia"
                @clear-all-media="clearAllMedia"
            />

            <LocationStatus
                :location-status="locationStatus"
                :form="form"
                :is-online="isOnline"
                @get-location="getLocation"
            />

            <SubmitButton
                :is-submitting="isSubmitting"
                :is-recording="isRecording"
                :is-form-valid="isFormValid"
                :location-status="locationStatus"
                :is-online="isOnline"
                :form-processing="form.processing"
            />
        </form>
    </div>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import { ref, computed, watch, onMounted, onUnmounted, nextTick } from "vue";
import axios from "axios";
import Swal from "sweetalert2";
import localforage from "localforage";

// Components
import NetworkStatus from "./NetworkStatus.vue";
import FormErrors from "./FormErrors.vue";
import LocationFields from "./FormFields/LocationFields.vue";
import ReporterInfo from "./FormFields/ReporterInfo.vue";
import AreaInfo from "./FormFields/AreaInfo.vue";
import WaterIssueType from "./FormFields/WaterIssueType.vue";
import DescriptionField from "./FormFields/DescriptionField.vue";
import CameraSection from "./Camera/CameraSection.vue";
import LocationStatus from "./LocationStatus.vue";
import SubmitButton from "./SubmitButton.vue";

const emit = defineEmits(["submitted", "offlineReportsSynced", "cancel"]);

const props = defineProps({
    zones: {
        type: Object,
        required: true,
    },
});

// Network and sync state
const isOnline = ref(navigator.onLine);
const isSyncing = ref(false);
const offlineReportsCount = ref(0);

// Form state
const isSubmitting = ref(false);
const locationStatus = ref("idle");

// GPS State
const lastKnownLocation = ref(null);
const locationWatchId = ref(null);
const locationCacheTimeout = 24 * 60 * 60 * 1000;

// Camera state
const isCameraActive = ref(false);
const isCameraReady = ref(false);
const isCameraLoading = ref(false);
const isSwitchingCamera = ref(false);
const isCapturing = ref(false);
const isRecording = ref(false);
const recordingTime = ref(0);
const availableCameras = ref([]);
const currentCameraIndex = ref(0);
const cameraError = ref("");
const cameraStatus = ref("Initializing...");

// Camera refs
const cameraSectionRef = ref(null);

// Camera stream and recording variables
let stream = null;
let mediaRecorder = null;
let recordedChunks = [];
let recordingInterval = null;

// Constants
const MAX_PHOTOS = 3;
const MAX_VIDEOS = 2;
const MAX_TOTAL = MAX_PHOTOS + MAX_VIDEOS;
const MAX_PHOTO_SIZE = 5 * 1024 * 1024;
const MAX_VIDEO_DURATION = 5;
const MAX_VIDEO_SIZE = 10 * 1024 * 1024;

const waterIssueTypes = [
    "Burst pipe",
    "Rusty water",
    "Low water pressure",
    "No water supply",
    "Clogged pipes",
    "Smelly water",
    "Cloudy or dirty water",
    "Hot water issues",
];

// Configure localforage
const offlineReportsStore = localforage.createInstance({
    name: "aquatrack",
    storeName: "offline_reports",
});

// Form data
const form = useForm({
    municipality: "Clarin",
    province: "Bohol",
    zone: "",
    barangay: "",
    purok: "",
    description: "",
    photos: [],
    photo_previews: [],
    reporter_name: "",
    reporter_phone: "",
    latitude: null,
    longitude: null,
    water_issue_type: "",
    custom_water_issue: "",
});

// Computed properties
const allBarangays = computed(() => {
    return Object.values(props.zones).flat();
});

const barangayToZone = computed(() => {
    const mapping = {};
    Object.entries(props.zones).forEach(([zone, barangays]) => {
        barangays.forEach((barangay) => {
            mapping[barangay] = zone;
        });
    });
    return mapping;
});

const hasErrors = computed(() => {
    return Object.keys(form.errors).length > 0;
});

const isFormValid = computed(() => {
    const basicValidation =
        form.reporter_name &&
        form.barangay &&
        form.purok &&
        form.description &&
        form.water_issue_type &&
        (form.water_issue_type !== "others" || form.custom_water_issue) &&
        form.photos.length > 0;

    const invalidLocations = ["loading"];

    return basicValidation && !invalidLocations.includes(locationStatus.value);
});

const hasMultipleCameras = computed(() => availableCameras.value.length > 1);

const mediaCount = computed(() => ({
    images: form.photos.filter((file) => file.type.startsWith("image")).length,
    videos: form.photos.filter((file) => file.type.startsWith("video")).length,
    total: form.photos.length,
}));

// Methods
const updateFormField = ({ field, value }) => {
    form[field] = value;
};

// Network methods
const updateOnlineStatus = () => {
    const wasOffline = !isOnline.value;
    isOnline.value = navigator.onLine;

    if (isOnline.value) {
        if (wasOffline && locationStatus.value === "offline_default_location") {
            setTimeout(() => {
                getLocation();
            }, 2000);
        }
        triggerAutoSync();
    }
};

const loadOfflineReportsCount = async () => {
    try {
        const queue =
            (await offlineReportsStore.getItem("offline_reports_queue")) || [];
        offlineReportsCount.value = queue.length;
    } catch (error) {
        console.error("Error loading offline reports count:", error);
    }
};

const triggerAutoSync = () => {
    if (isOnline.value && offlineReportsCount.value > 0) {
        setTimeout(syncOfflineReports, 2000);
    }
};

const triggerManualSync = async () => {
    if (isOnline.value) {
        syncOfflineReports();
    }
};

// GPS Methods
const loadCachedLocation = () => {
    try {
        const cached = localStorage.getItem("lastKnownLocation");
        if (cached) {
            const location = JSON.parse(cached);
            const cacheAge = Date.now() - location.timestamp;

            if (cacheAge < locationCacheTimeout) {
                lastKnownLocation.value = location;
                return location;
            } else {
                localStorage.removeItem("lastKnownLocation");
            }
        }
    } catch (e) {
        console.warn("Failed to load cached location:", e);
    }
    return null;
};

const saveLocationToCache = (coords) => {
    try {
        const locationData = {
            latitude: coords.latitude,
            longitude: coords.longitude,
            accuracy: coords.accuracy,
            timestamp: Date.now(),
        };
        localStorage.setItem("lastKnownLocation", JSON.stringify(locationData));
        lastKnownLocation.value = locationData;
    } catch (e) {
        console.warn("Failed to save location to cache:", e);
    }
};

const getLocation = async () => {
    const cachedLocation = loadCachedLocation();
    if (cachedLocation) {
        form.latitude = cachedLocation.latitude;
        form.longitude = cachedLocation.longitude;
        locationStatus.value = "offline_cached_location";

        if (!isOnline.value) {
            Swal.fire({
                icon: "info",
                title: "Using Cached Location",
                text: "Using previously saved GPS location.",
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
            });
        }
        return cachedLocation;
    }

    if (!isOnline.value) {
        if (navigator.geolocation) {
            locationStatus.value = "loading";

            return new Promise((resolve) => {
                const offlineGpsOptions = {
                    enableHighAccuracy: false, // ← CHANGED TO FALSE
                    maximumAge: 30000, // ← Increased to 30 seconds
                    timeout: 10000,
                };

                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const coords = {
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude,
                            accuracy: position.coords.accuracy,
                            timestamp: Date.now(),
                        };

                        saveLocationToCache(coords);
                        form.latitude = coords.latitude;
                        form.longitude = coords.longitude;
                        locationStatus.value = "success";

                        Swal.fire({
                            icon: "success",
                            title: "GPS Acquired Offline!",
                            text: "Location saved for future offline use.",
                            toast: true,
                            position: "bottom-end",
                            showConfirmButton: false,
                            timer: 4000,
                        });

                        resolve(coords);
                    },
                    (error) => {
                        useDefaultLocation();
                        resolve(null);
                    },
                    offlineGpsOptions
                );
            });
        } else {
            useDefaultLocation();
            return null;
        }
    }

    locationStatus.value = "loading";

    return new Promise((resolve) => {
        const onlineGpsOptions = {
            enableHighAccuracy: false, // ← CHANGED TO FALSE
            maximumAge: 30000, // ← Increased to 30 seconds
            timeout: 10000,
        };

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const coords = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    accuracy: position.coords.accuracy,
                    timestamp: Date.now(),
                };

                saveLocationToCache(coords);
                form.latitude = coords.latitude;
                form.longitude = coords.longitude;
                locationStatus.value = "success";

                Swal.fire({
                    icon: "success",
                    title: "Location Acquired!",
                    text: `Accuracy: ${
                        coords.accuracy
                            ? coords.accuracy.toFixed(1) + "m"
                            : "Good"
                    }`,
                    toast: true,
                    position: "bottom-end",
                    showConfirmButton: false,
                    timer: 4000,
                });

                resolve(coords);
            },
            (error) => {
                const freshCache = loadCachedLocation();
                if (freshCache) {
                    form.latitude = freshCache.latitude;
                    form.longitude = freshCache.longitude;
                    locationStatus.value = "offline_cached_location";
                    resolve(freshCache);
                } else {
                    useDefaultLocation();
                    resolve(null);
                }
            },
            onlineGpsOptions
        );
    });
};

const useDefaultLocation = () => {
    form.latitude = 9.9616;
    form.longitude = 124.025;
    locationStatus.value = "offline_default_location";

    Swal.fire({
        icon: "warning",
        title: "Using Default Location",
        text: "GPS signal unavailable. Using center of Clarin, Bohol.",
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 4000,
    });
};

const startLocationTracking = () => {
    if (!navigator.geolocation) return;

    try {
        if (locationWatchId.value) {
            navigator.geolocation.clearWatch(locationWatchId.value);
        }

        locationWatchId.value = navigator.geolocation.watchPosition(
            (position) => {
                const coords = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    accuracy: position.coords.accuracy,
                    timestamp: Date.now(),
                };

                saveLocationToCache(coords);

                if (locationStatus.value !== "success") {
                    form.latitude = coords.latitude;
                    form.longitude = coords.longitude;
                    locationStatus.value = "success";
                }
            },
            (error) => {
                console.warn("Location tracking error:", error.message);
            },
            {
                enableHighAccuracy: false,
                maximumAge: 30000,
                timeout: 10000,
            }
        );
    } catch (error) {
        console.error("Failed to start location tracking:", error);
    }
};

const stopLocationTracking = () => {
    if (locationWatchId.value) {
        navigator.geolocation.clearWatch(locationWatchId.value);
        locationWatchId.value = null;
    }
};

// Camera methods
const getCameras = async () => {
    try {
        cameraStatus.value = "Detecting cameras...";
        const devices = await navigator.mediaDevices.enumerateDevices();
        availableCameras.value = devices.filter(
            (device) => device.kind === "videoinput"
        );

        if (
            availableCameras.value.length > 0 &&
            !availableCameras.value[0].label
        ) {
            try {
                const tempStream = await navigator.mediaDevices.getUserMedia({
                    video: true,
                });
                tempStream.getTracks().forEach((track) => track.stop());
                const devicesWithLabels =
                    await navigator.mediaDevices.enumerateDevices();
                availableCameras.value = devicesWithLabels.filter(
                    (device) => device.kind === "videoinput"
                );
            } catch (e) {
                console.warn("Could not get camera labels:", e);
            }
        }

        return availableCameras.value;
    } catch (error) {
        console.error("Error getting cameras:", error);
        throw new Error("Could not detect camera devices");
    }
};

const initializeCamera = async () => {
    cameraError.value = "";
    isCameraLoading.value = true;

    try {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            throw new Error("Camera not supported by this browser");
        }

        await getCameras();

        if (availableCameras.value.length === 0) {
            throw new Error("No camera devices found");
        }

        if (stream) {
            stream.getTracks().forEach((track) => track.stop());
            stream = null;
        }

        await startCameraStream();
    } catch (error) {
        console.error("Camera initialization failed:", error);
        handleCameraError(error);
    } finally {
        isCameraLoading.value = false;
    }
};

const startCameraStream = async () => {
    try {
        cameraStatus.value = "Starting camera...";

        if (stream) {
            stream.getTracks().forEach((track) => track.stop());
            stream = null;
        }

        isCameraActive.value = true;
        await nextTick();

        const constraints = {
            video: {
                width: { ideal: 1280, max: 1920 },
                height: { ideal: 720, max: 1080 },
                frameRate: { ideal: 30, max: 60 },
            },
            audio: true,
        };

        if (availableCameras.value.length > 0) {
            if (currentCameraIndex.value < availableCameras.value.length) {
                constraints.video.deviceId = {
                    exact: availableCameras.value[currentCameraIndex.value]
                        .deviceId,
                };
            }
        }

        stream = await navigator.mediaDevices.getUserMedia(constraints);

        if (cameraSectionRef.value) {
            cameraSectionRef.value.setVideoStream(stream);
        }

        isCameraReady.value = true;
        cameraStatus.value = "Camera ready";
    } catch (error) {
        console.error("Camera stream error:", error);
        throw error;
    }
};

const handleCameraError = (error) => {
    isCameraActive.value = false;
    isCameraReady.value = false;

    if (stream) {
        stream.getTracks().forEach((track) => track.stop());
        stream = null;
    }

    let errorMessage = "Camera initialization failed";

    if (error.name === "NotAllowedError") {
        errorMessage =
            "Camera access denied. Please allow permissions and try again.";
    } else if (error.name === "NotFoundError") {
        errorMessage = "No camera found on your device.";
    } else if (error.name === "NotReadableError") {
        errorMessage = "Camera is busy or being used by another application.";
    }

    cameraError.value = errorMessage;
};

const retryCamera = () => {
    cameraError.value = "";
    initializeCamera();
};

const switchCamera = async () => {
    if (
        availableCameras.value.length <= 1 ||
        !isCameraReady.value ||
        isRecording.value
    )
        return;

    try {
        isSwitchingCamera.value = true;
        isCameraReady.value = false;

        currentCameraIndex.value =
            (currentCameraIndex.value + 1) % availableCameras.value.length;
        await startCameraStream();
    } catch (error) {
        console.error("Camera switch failed:", error);
        handleCameraError(error);
    } finally {
        isSwitchingCamera.value = false;
    }
};

const capturePhoto = async () => {
    if (
        !isCameraReady.value ||
        mediaCount.value.images >= MAX_PHOTOS ||
        isCapturing.value ||
        isRecording.value
    ) {
        return;
    }

    try {
        isCapturing.value = true;

        const canvas = document.createElement("canvas");
        const ctx = canvas.getContext("2d");

        const video = cameraSectionRef.value?.getVideoElement();

        if (!video) {
            throw new Error("Camera video element not found");
        }

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        addWatermark(ctx, canvas.width, canvas.height);

        const blob = await new Promise((resolve, reject) => {
            canvas.toBlob(
                (blob) => {
                    if (blob) {
                        resolve(blob);
                    } else {
                        reject(new Error("Failed to create image blob"));
                    }
                },
                "image/jpeg",
                0.85
            );
        });

        if (blob.size > MAX_PHOTO_SIZE) {
            throw new Error("Photo size too large");
        }

        const filename = `water-report-${Date.now()}.jpg`;
        const file = new File([blob], filename, {
            type: "image/jpeg",
            lastModified: Date.now(),
        });

        form.photos.push(file);
        form.photo_previews.push(URL.createObjectURL(blob));

        Swal.fire({
            icon: "success",
            title: `Photo ${mediaCount.value.images} captured!`,
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 2000,
        });
    } catch (error) {
        console.error("Photo capture failed:", error);
        Swal.fire({
            icon: "error",
            title: "Capture Failed",
            text: error.message || "Failed to capture photo. Please try again.",
            timer: 3000,
        });
    } finally {
        isCapturing.value = false;
    }
};

const addWatermark = (ctx, width, height) => {
    const now = new Date();

    const dateStr = now
        .toLocaleDateString("en-US", {
            year: "numeric",
            month: "long",
            day: "numeric",
        })
        .replace(/(\d+)\/(\d+)\/(\d+)/, "$3-$1-$2");

    const timeStr = now.toLocaleTimeString("en-US", {
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        hour12: false,
    });

    const locationInfo = getLocationInfo();

    const lines = [`${dateStr} ${timeStr}`, locationInfo];

    const padding = 10;
    const fontSize = Math.max(12, width * 0.012);
    const lineHeight = fontSize * 1.2;
    const fontFamily = "Arial, sans-serif";

    ctx.font = `bold ${fontSize}px ${fontFamily}`;
    ctx.textAlign = "left";
    ctx.textBaseline = "bottom";

    let maxTextWidth = 0;
    lines.forEach((line) => {
        const textMetrics = ctx.measureText(line);
        maxTextWidth = Math.max(maxTextWidth, textMetrics.width);
    });

    const bgPadding = 6;
    const bgWidth = maxTextWidth + bgPadding * 2;
    const bgHeight = lines.length * lineHeight + bgPadding * 2;
    const bgX = padding;
    const bgY = height - padding - bgHeight;

    ctx.fillStyle = "rgba(0, 0, 0, 0.7)";
    ctx.fillRect(bgX, bgY, bgWidth, bgHeight);

    ctx.fillStyle = "white";
    lines.forEach((line, index) => {
        const yPos =
            bgY +
            bgPadding +
            lineHeight * (index + 1) -
            (lineHeight - fontSize) / 2;
        ctx.fillText(line, bgX + bgPadding, yPos);
    });

    ctx.strokeStyle = "rgba(255, 255, 255, 0.4)";
    ctx.lineWidth = 1;
    ctx.strokeRect(bgX, bgY, bgWidth, bgHeight);
};

const getLocationInfo = () => {
    const locationParts = [];

    if (form.purok) {
        locationParts.push(`Purok ${form.purok}`);
    }

    if (form.barangay) {
        locationParts.push(form.barangay);
    }

    if (form.latitude && form.longitude) {
        const lat = Number(form.latitude).toFixed(6);
        const lon = Number(form.longitude).toFixed(6);

        let accuracyIndicator = "";
        if (locationStatus.value === "success") {
            accuracyIndicator = "GPS";
        } else if (locationStatus.value === "offline_cached_location") {
            accuracyIndicator = "Cached";
        } else {
            accuracyIndicator = "Approx";
        }

        locationParts.push(`${accuracyIndicator}: ${lat}, ${lon}`);
    } else {
        locationParts.push(form.municipality || "Clarin");
    }

    return locationParts.join(" • ") || "Location unknown";
};

const startVideoRecording = async () => {
    if (
        !isCameraReady.value ||
        mediaCount.value.videos >= MAX_VIDEOS ||
        !stream
    ) {
        return;
    }

    try {
        recordedChunks = [];
        recordingTime.value = 0;

        const options = {
            mimeType: "video/webm;codecs=vp9,opus",
            videoBitsPerSecond: 1000000,
        };

        if (!MediaRecorder.isTypeSupported(options.mimeType)) {
            options.mimeType = "video/webm;codecs=vp8,opus";
        }

        mediaRecorder = new MediaRecorder(stream, options);

        mediaRecorder.ondataavailable = (event) => {
            if (event.data.size > 0) {
                recordedChunks.push(event.data);
            }
        };

        mediaRecorder.onstop = async () => {
            let blob = new Blob(recordedChunks, { type: "video/webm" });

            blob = await addWatermarkToVideo(blob);

            if (blob.size > MAX_VIDEO_SIZE) {
                Swal.fire({
                    icon: "error",
                    title: "Video Too Large",
                    text: `Video size exceeds ${
                        MAX_VIDEO_SIZE / 1024 / 1024
                    }MB limit.`,
                    timer: 3000,
                });
                return;
            }

            const filename = `water-report-video-${Date.now()}.webm`;
            const file = new File([blob], filename, {
                type: "video/webm",
                lastModified: Date.now(),
            });

            form.photos.push(file);
            form.photo_previews.push(URL.createObjectURL(blob));

            Swal.fire({
                icon: "success",
                title: `Video ${mediaCount.value.videos} recorded!`,
                text: `Duration: 5 seconds | Size: ${(
                    blob.size /
                    1024 /
                    1024
                ).toFixed(2)}MB`,
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
            });
        };

        mediaRecorder.start(1000);
        isRecording.value = true;

        recordingInterval = setInterval(() => {
            recordingTime.value++;

            if (recordingTime.value >= MAX_VIDEO_DURATION) {
                stopVideoRecording();
            }
        }, 1000);
    } catch (error) {
        console.error("Failed to start recording:", error);
        Swal.fire({
            icon: "error",
            title: "Recording Failed",
            text: "Failed to start video recording. Please try again.",
            timer: 3000,
        });
    }
};

const addWatermarkToVideo = async (blob) => {
    return blob;
};

const stopVideoRecording = () => {
    if (!isRecording.value || !mediaRecorder) return;

    try {
        if (mediaRecorder.state !== "inactive") {
            mediaRecorder.stop();
        }

        isRecording.value = false;

        if (recordingInterval) {
            clearInterval(recordingInterval);
            recordingInterval = null;
        }

        if (recordingTime.value < MAX_VIDEO_DURATION) {
            Swal.fire({
                icon: "info",
                title: "Recording Stopped",
                text: `Video recorded for ${recordingTime.value} seconds.`,
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 2000,
            });
        }
    } catch (error) {
        console.error("Failed to stop recording:", error);
    }
};

const stopCamera = () => {
    if (isRecording.value) {
        stopVideoRecording();
    }

    if (stream) {
        stream.getTracks().forEach((track) => track.stop());
        stream = null;
    }

    isCameraActive.value = false;
    isCameraReady.value = false;
    isCameraLoading.value = false;
    cameraError.value = "";
};

const removeMedia = (index) => {
    if (index >= 0 && index < form.photo_previews.length) {
        URL.revokeObjectURL(form.photo_previews[index]);
        form.photos.splice(index, 1);
        form.photo_previews.splice(index, 1);
    }
};

const clearAllMedia = () => {
    const totalMedia = form.photos.length;
    if (totalMedia === 0) return;

    Swal.fire({
        title: "Clear All Media?",
        text: `This will remove all ${form.photos.length} media files.`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc2626",
        cancelButtonColor: "#6b7280",
        confirmButtonText: "Clear All",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            form.photo_previews.forEach((url) => URL.revokeObjectURL(url));
            form.photos = [];
            form.photo_previews = [];

            Swal.fire({
                icon: "success",
                title: "Media Cleared",
                toast: true,
                position: "top-end",
                timer: 2000,
                showConfirmButton: false,
            });
        }
    });
};

const showSyncResults = (successfulSyncs, failedSyncs, syncTrackingData) => {
    if (successfulSyncs.length > 0 || failedSyncs.length > 0) {
        let message = "";

        if (successfulSyncs.length > 0) {
            message += ` ${successfulSyncs.length} report(s) synced successfully.\n`;
            if (syncTrackingData.length > 0) {
                message += `Tracking code: ${
                    syncTrackingData[syncTrackingData.length - 1].trackingCode
                }\n`;
            }
        }

        if (failedSyncs.length > 0) {
            message += ` ${failedSyncs.length} report(s) failed to sync.\n`;
        }

        Swal.fire({
            icon: successfulSyncs.length > 0 ? "success" : "error",
            title: successfulSyncs.length > 0 ? "Sync Complete" : "Sync Issues",
            text: message,
            timer: 5000,
        });
    }
};

const syncOfflineReports = async () => {
    if (!isOnline.value || isSyncing.value) return;

    isSyncing.value = true;

    try {
        const queue =
            (await offlineReportsStore.getItem("offline_reports_queue")) || [];

        if (queue.length === 0) {
            isSyncing.value = false;
            return;
        }

        const successfulSyncs = [];
        const failedSyncs = [];
        const syncTrackingData = [];

        for (const report of queue) {
            try {
                if (!isValidOfflineReport(report)) {
                    failedSyncs.push({ id: report.id, reason: "Invalid data" });
                    continue;
                }

                const formData = new FormData();

                Object.keys(report.formData).forEach((key) => {
                    const value = report.formData[key];
                    if (value !== null && value !== undefined && value !== "") {
                        if (key === "photos") {
                            return;
                        } else if (Array.isArray(value)) {
                            value.forEach((item) =>
                                formData.append(`${key}[]`, item)
                            );
                        } else {
                            formData.append(key, value.toString());
                        }
                    }
                });

                if (!formData.has("latitude") || !formData.has("longitude")) {
                    formData.append(
                        "latitude",
                        report.formData.latitude || "9.9616"
                    );
                    formData.append(
                        "longitude",
                        report.formData.longitude || "124.0250"
                    );
                }

                let hasValidPhotos = false;
                for (const photo of report.photos) {
                    try {
                        const response = await fetch(photo.dataUrl);
                        const blob = await response.blob();

                        if (blob.size === 0 || blob.size > MAX_PHOTO_SIZE) {
                            continue;
                        }

                        const file = new File([blob], photo.name, {
                            type: photo.type,
                        });
                        formData.append("photos[]", file);
                        hasValidPhotos = true;
                    } catch (photoError) {
                        console.warn(
                            `Failed to process photo in report ${report.id}:`,
                            photoError
                        );
                    }
                }

                if (!hasValidPhotos) {
                    failedSyncs.push({
                        id: report.id,
                        reason: "No valid photos",
                    });
                    continue;
                }

                const csrfToken = document
                    .querySelector('meta[name="csrf-token"]')
                    ?.getAttribute("content");
                if (csrfToken) {
                    formData.append("_token", csrfToken);
                }

                const response = await axios.post(
                    route("reports.store"),
                    formData,
                    {
                        headers: {
                            "Content-Type": "multipart/form-data",
                            "X-Requested-With": "XMLHttpRequest",
                        },
                        timeout: 30000,
                    }
                );

                if (response.data.success) {
                    successfulSyncs.push(report.id);
                    if (response.data.trackingCode) {
                        syncTrackingData.push({
                            trackingCode: response.data.trackingCode,
                            dateSubmitted: new Date().toISOString(),
                            originalReportId: report.id,
                        });
                    }
                } else {
                    failedSyncs.push({
                        id: report.id,
                        reason: "Server rejected",
                        details: response.data,
                    });
                }
            } catch (error) {
                console.error(`Error syncing report ${report.id}:`, error);

                if (error.response) {
                    failedSyncs.push({
                        id: report.id,
                        reason: `Server error: ${error.response.status}`,
                        details: error.response.data,
                        validationErrors: error.response.data.errors,
                    });
                } else if (error.request) {
                    failedSyncs.push({
                        id: report.id,
                        reason: "No server response",
                    });
                } else {
                    failedSyncs.push({ id: report.id, reason: error.message });
                }
            }
        }

        const updatedQueue = queue.filter(
            (report) => !successfulSyncs.includes(report.id)
        );
        await offlineReportsStore.setItem(
            "offline_reports_queue",
            updatedQueue
        );
        offlineReportsCount.value = updatedQueue.length;

        if (syncTrackingData.length > 0) {
            const latestSync = syncTrackingData[syncTrackingData.length - 1];

            emit("offlineReportsSynced", {
                trackingCode: latestSync.trackingCode,
                dateSubmitted: latestSync.dateSubmitted,
                totalSynced: syncTrackingData.length,
            });
        }

        showSyncResults(successfulSyncs, failedSyncs, syncTrackingData);
    } catch (error) {
        console.error("Error during offline reports sync:", error);
        Swal.fire({
            icon: "error",
            title: "Sync Failed",
            text: "An error occurred during sync. Please try again.",
            timer: 5000,
        });
    } finally {
        isSyncing.value = false;
    }
};

const isValidOfflineReport = (report) => {
    if (!report || !report.formData || !report.photos) {
        return false;
    }

    const requiredFields = [
        "reporter_name",
        "barangay",
        "purok",
        "description",
        "water_issue_type",
        "latitude",
        "longitude",
    ];

    for (const field of requiredFields) {
        if (
            !report.formData[field] ||
            report.formData[field].toString().trim() === ""
        ) {
            return false;
        }
    }

    if (
        report.formData.water_issue_type === "others" &&
        (!report.formData.custom_water_issue ||
            report.formData.custom_water_issue.trim() === "")
    ) {
        return false;
    }

    if (!Array.isArray(report.photos) || report.photos.length === 0) {
        return false;
    }

    for (const photo of report.photos) {
        if (!photo.dataUrl || !photo.name || !photo.type) {
            return false;
        }
    }

    return true;
};

const fileToBase64 = (file) => {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => resolve(reader.result);
        reader.onerror = (error) => reject(error);
    });
};

const saveReportOffline = async () => {
    try {
        const reportId =
            "offline_" +
            Date.now() +
            "_" +
            Math.random().toString(36).substr(2, 9);

        const requiredFields = [
            "reporter_name",
            "barangay",
            "purok",
            "description",
            "water_issue_type",
        ];

        const missingFields = [];
        for (const field of requiredFields) {
            if (!form[field] || form[field].toString().trim() === "") {
                missingFields.push(field);
            }
        }

        if (missingFields.length > 0) {
            throw new Error(
                `Required fields missing: ${missingFields.join(", ")}`
            );
        }

        if (
            form.water_issue_type === "others" &&
            (!form.custom_water_issue || form.custom_water_issue.trim() === "")
        ) {
            throw new Error(
                "Custom water issue description is required when selecting 'Others'"
            );
        }

        if (form.photos.length === 0) {
            throw new Error("At least one photo is required");
        }

        const photosWithData = [];
        for (const photo of form.photos) {
            if (!photo || photo.size === 0 || photo.size > MAX_PHOTO_SIZE) {
                continue;
            }

            try {
                const dataUrl = await fileToBase64(photo);
                photosWithData.push({
                    name: photo.name || `photo-${Date.now()}.jpg`,
                    type: photo.type || "image/jpeg",
                    size: photo.size,
                    dataUrl: dataUrl,
                });
            } catch (photoError) {
                console.warn("Failed to convert photo to base64:", photoError);
            }
        }

        if (photosWithData.length === 0) {
            throw new Error("No valid photos to save");
        }

        const latitude = form.latitude || 9.9616;
        const longitude = form.longitude || 124.025;
        const locationSource =
            locationStatus.value === "offline_cached_location"
                ? "cached_gps"
                : locationStatus.value === "success"
                ? "live_gps"
                : "default";

        const offlineReport = {
            id: reportId,
            formData: {
                municipality: form.municipality || "Clarin",
                province: form.province || "Bohol",
                zone: form.zone || "",
                barangay: form.barangay.trim(),
                purok: form.purok.trim(),
                description: form.description.trim(),
                reporter_name: form.reporter_name.trim(),
                reporter_phone: (form.reporter_phone || "").trim(),
                latitude: latitude,
                longitude: longitude,
                water_issue_type: form.water_issue_type,
                custom_water_issue: (form.custom_water_issue || "").trim(),
                location_source: locationSource,
            },
            photos: photosWithData,
            createdAt: new Date().toISOString(),
            synced: false,
        };

        const queue =
            (await offlineReportsStore.getItem("offline_reports_queue")) || [];
        queue.push(offlineReport);
        await offlineReportsStore.setItem("offline_reports_queue", queue);
        offlineReportsCount.value = queue.length;

        return reportId;
    } catch (error) {
        console.error("Error saving report offline:", error);
        throw error;
    }
};

const submitReport = async () => {
    if (!form.water_issue_type) {
        Swal.fire({
            icon: "error",
            title: "Water Issue Required",
            text: "Please select a water issue type.",
            confirmButtonColor: "#3085d6",
        });
        return;
    }
    if (form.water_issue_type === "others" && !form.custom_water_issue) {
        Swal.fire({
            icon: "error",
            title: "Custom Issue Required",
            text: "Please specify the water issue when selecting 'Others'.",
            confirmButtonColor: "#3085d6",
        });
        return;
    }
    if (form.photos.length === 0) {
        Swal.fire({
            icon: "error",
            title: "Media Required",
            text: "Please capture at least one photo or video for your report.",
            confirmButtonColor: "#3085d6",
        });
        return;
    }

    if (isOnline.value && locationStatus.value !== "success") {
        Swal.fire({
            icon: "error",
            title: "Location Required",
            text: "Please wait for GPS location to be acquired for accurate reporting.",
            confirmButtonColor: "#3085d6",
        });
        return;
    }

    if (
        !isOnline.value &&
        locationStatus.value === "offline_default_location"
    ) {
        const result = await Swal.fire({
            icon: "warning",
            title: "Using Area Center Location",
            text: "GPS signal is weak/unavailable. Your report will use the Clarin, Bohol center location. For more precise location, try moving to an area with better GPS signal.",
            showCancelButton: true,
            confirmButtonText: "Continue with Area Center",
            cancelButtonText: "Try GPS Again",
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#6b7280",
        });

        if (!result.isConfirmed) {
            getLocation();
            return;
        }
    }

    isSubmitting.value = true;

    try {
        Swal.fire({
            title: isOnline.value
                ? "Submitting Report..."
                : "Saving Report Offline...",
            text: isOnline.value
                ? "Please wait while we submit your report to the server"
                : "Please wait while we save your report locally",
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            },
        });

        await new Promise((resolve) => setTimeout(resolve, 2000));

        if (isOnline.value) {
            const formData = new FormData();
            Object.keys(form.data()).forEach((key) => {
                if (key !== "photos" && key !== "photo_previews") {
                    formData.append(key, form[key]);
                }
            });

            form.photos.forEach((file, index) => {
                formData.append(`photos[${index}]`, file);
            });

            const response = await axios.post(
                route("reports.store"),
                formData,
                {
                    headers: { "Content-Type": "multipart/form-data" },
                }
            );

            isSubmitting.value = false;
            Swal.close();

            if (response.data.success) {
                emit("submitted", {
                    trackingCode: response.data.trackingCode,
                    dateSubmitted: new Date().toISOString(),
                });
            }

            form.reset();
            form.photos = [];
            form.photo_previews = [];
        } else {
            const reportId = await saveReportOffline();
            isSubmitting.value = false;
            Swal.close();

            let successMessage =
                "Your report has been saved locally and will be submitted automatically when you're back online.";

            if (locationStatus.value === "offline_cached_location") {
                successMessage =
                    "Your report with cached GPS coordinates has been saved locally and will be submitted when online.";
            } else if (locationStatus.value === "offline_default_location") {
                successMessage =
                    "Your report with default location has been saved locally and will be submitted when online.";
            }

            Swal.fire({
                position: "top-end",
                title: "Report Saved Offline!",
                text: successMessage,
                icon: "success",
                toast: true,
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
            });

            form.reset();
            form.photos = [];
            form.photo_previews = [];
        }
    } catch (error) {
        isSubmitting.value = false;
        Swal.close();

        if (isOnline.value) {
            if (error.response?.data?.errors) {
                form.errors = error.response.data.errors;
                window.scrollTo({ top: 0, behavior: "smooth" });
            }

            Swal.fire({
                icon: "error",
                title: "Submission Failed",
                text: "Please check the form for errors.",
                confirmButtonColor: "#3085d6",
            });
        } else {
            Swal.fire({
                icon: "error",
                title: "Save Failed",
                text: "Failed to save report offline. Please try again.",
                confirmButtonColor: "#3085d6",
            });
        }
    }
};

// Watchers
watch(
    () => form.barangay,
    (newBarangay) => {
        form.zone = barangayToZone.value[newBarangay] || "";
    }
);

// Lifecycle
onMounted(async () => {
    window.addEventListener("online", updateOnlineStatus);
    window.addEventListener("offline", updateOnlineStatus);
    loadOfflineReportsCount();

    startLocationTracking();

    setTimeout(async () => {
        await getLocation();

        if (
            locationStatus.value === "offline_default_location" &&
            isOnline.value
        ) {
            setTimeout(async () => {
                await getLocation();
            }, 5000);
        }
    }, 1000);
});

onUnmounted(() => {
    stopLocationTracking();
    stopCamera();
    window.removeEventListener("online", updateOnlineStatus);
    window.removeEventListener("offline", updateOnlineStatus);
    form.photo_previews.forEach((url) => URL.revokeObjectURL(url));
});
</script>
