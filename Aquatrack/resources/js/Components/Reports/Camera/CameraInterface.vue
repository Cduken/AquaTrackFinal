<template>
    <div class="space-y-4">
        <!-- Camera Status Banner -->
        <div
            v-if="cameraError"
            class="p-4 bg-red-50 border border-red-200 rounded-xl"
        >
            <div class="flex items-center text-red-700 text-sm">
                <v-icon
                    name="bi-exclamation-triangle-fill"
                    class="w-5 h-5 mr-2"
                />
                {{ cameraError }}
            </div>
            <button
                type="button"
                @click="$emit('retry-camera')"
                class="mt-2 text-sm text-blue-600 hover:text-blue-800 underline"
            >
                Try Again
            </button>
        </div>

        <!-- Camera Interface -->
        <div
            class="bg-transparent rounded-xl overflow-hidden border border-gray-200/20 backdrop-blur-xl shadow-lg"
        >
            <!-- Camera Not Active State -->
            <div v-if="!isCameraActive" class="p-8 text-center">
                <div class="mb-6">
                    <div
                        class="w-20 h-20 mx-auto bg-transparent border border-gray-200/20 rounded-full flex items-center justify-center mb-4 shadow-lg"
                    >
                        <v-icon name="hi-camera" class="w-10 h-10 text-white" />
                    </div>
                    <h3 class="text-gray-200 text-xl font-semibold mb-3">
                        Camera Required
                    </h3>
                    <p class="text-gray-200 text-sm mb-6 max-w-md mx-auto">
                        Take photos and videos to document the water quality
                        issue. Visual evidence helps us resolve issues faster.
                    </p>
                </div>

                <button
                    type="button"
                    @click="$emit('initialize-camera')"
                    :disabled="isCameraLoading"
                    class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-slate-800 via-slate-900 to-slate-950 hover:from-slate-900 hover:via-slate-950 hover:to-slate-800 disabled:from-slate-600 disabled:to-slate-800 disabled:cursor-not-allowed text-white font-semibold rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl"
                >
                    <v-icon
                        v-if="isCameraLoading"
                        name="bi-arrow-repeat"
                        class="animate-spin -ml-1 mr-3 h-5 w-5"
                    />
                    <v-icon v-else name="hi-camera" class="w-5 h-5 mr-3" />
                    {{ isCameraLoading ? "Starting Camera..." : "Open Camera" }}
                </button>

                <div class="mt-6 text-xs text-gray-400 space-y-1">
                    <p>Your browser will ask for camera permission</p>
                    <p>
                        Make sure to allow access to capture photos and videos
                    </p>
                </div>
            </div>

            <!-- Active Camera View -->
            <div v-else class="relative">
                <!-- Video Element Container -->
                <div
                    class="relative bg-black rounded-lg overflow-hidden"
                    style="aspect-ratio: 4/3"
                >
                    <!-- FIX: Proper video element with ref -->
                    <video
                        ref="videoElement"
                        class="w-full h-full object-cover"
                        autoplay
                        playsinline
                        muted
                    ></video>

                    <!-- Camera Loading Overlay -->
                    <div
                        v-if="!isCameraReady"
                        class="absolute inset-0 bg-black bg-opacity-75 flex items-center justify-center"
                    >
                        <div class="text-center text-white">
                            <div
                                class="inline-block animate-spin w-8 h-8 border-2 border-white border-t-transparent rounded-full mb-3"
                            ></div>
                            <p class="text-sm">{{ cameraStatus }}</p>
                        </div>
                    </div>

                    <!-- Camera Ready Indicator -->
                    <div v-if="isCameraReady" class="absolute top-4 left-4">
                        <div
                            class="flex items-center bg-green-500 text-white px-3 py-1.5 rounded-full text-xs font-medium shadow-lg"
                        >
                            <div
                                class="w-2 h-2 bg-white rounded-full mr-2 animate-pulse"
                            ></div>
                            LIVE
                        </div>
                    </div>

                    <!-- Recording Indicator -->

                    <div
                        v-if="isRecording"
                        class="absolute top-4 left-1/2 transform -translate-x-1/2"
                    >
                        <div
                            class="flex items-center bg-red-500 text-white px-4 py-2 rounded-full text-sm font-semibold shadow-lg"
                        >
                            <div
                                class="w-3 h-3 bg-white rounded-full mr-2 animate-pulse"
                            ></div>
                            REC {{ formatTime(recordingTime) }} / 5s
                        </div>
                        <!-- Progress bar -->
                        <div
                            class="absolute -bottom-1 left-0 w-full h-1 bg-red-200 rounded-full"
                        >
                            <div
                                class="h-full bg-red-500 rounded-full transition-all duration-1000 ease-linear"
                                :style="{
                                    width: `${
                                        (recordingTime / MAX_VIDEO_DURATION) *
                                        100
                                    }%`,
                                }"
                            ></div>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Camera Controls -->
                <div class="bg-transparent blackdrop-blue-xl p-6">
                    <div class="flex items-center justify-center gap-4">
                        <!-- Switch Camera Button -->
                        <button
                            type="button"
                            @click="$emit('switch-camera')"
                            v-if="
                                hasMultipleCameras &&
                                isCameraReady &&
                                !isRecording
                            "
                            class="p-3 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all duration-200 text-gray-700 disabled:opacity-50"
                            :disabled="!isCameraReady || isSwitchingCamera"
                        >
                            <v-icon
                                v-if="isSwitchingCamera"
                                name="eo-loading"
                                class="animate-spin w-6 h-6"
                            />
                            <v-icon
                                v-else
                                name="bi-arrow-repeat"
                                class="w-6 h-6"
                            />
                        </button>

                        <!-- Photo Capture Button -->
                        <button
                            type="button"
                            @click="$emit('capture-photo')"
                            :disabled="
                                !isCameraReady ||
                                mediaCount.images >= MAX_PHOTOS ||
                                isCapturing ||
                                isRecording
                            "
                            class="p-4 bg-gradient-to-br from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 rounded-full transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg"
                        >
                            <v-icon
                                name="hi-camera"
                                class="w-6 h-6 text-white"
                            />
                        </button>

                        <!-- Video Recording Button -->
                        <!-- Video Recording Button -->
                        <button
                            type="button"
                            @click="
                                $emit(
                                    isRecording
                                        ? 'stop-recording'
                                        : 'start-recording'
                                )
                            "
                            :disabled="
                                !isCameraReady ||
                                (mediaCount.videos >= MAX_VIDEOS &&
                                    !isRecording)
                            "
                            class="p-4 rounded-full transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg relative group"
                            :class="
                                isRecording
                                    ? 'bg-gradient-to-br from-red-500 to-red-600 hover:from-red-600 hover:to-red-700'
                                    : 'bg-gradient-to-br from-red-500 to-red-600 hover:from-red-600 hover:to-red-700'
                            "
                        >
                            <div
                                v-if="isRecording"
                                class="w-6 h-6 bg-white rounded-sm"
                            ></div>
                            <v-icon
                                v-else
                                name="hi-video-camera"
                                class="w-6 h-6 text-white"
                            />

                            <!-- Tooltip for time limit -->
                            <div
                                v-if="!isRecording"
                                class="absolute -top-10 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap"
                            >
                                Max 5 seconds
                            </div>
                        </button>

                        <!-- Close Camera Button -->
                        <button
                            type="button"
                            @click="$emit('stop-camera')"
                            :disabled="isRecording"
                            class="p-3 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all duration-200 text-gray-700 disabled:opacity-50"
                        >
                            <v-icon name="hi-solid-x" class="w-6 h-6" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick } from "vue";

// FIX: Proper video element reference
const videoElement = ref(null);

const setVideoStream = (newStream) => {
    if (videoElement.value && newStream) {
        videoElement.value.srcObject = newStream;
        videoElement.value.play().catch((error) => {
            console.warn("Video play failed:", error);
        });
    }
};

// FIX: Get video element for capture
const getVideoElement = () => {
    return videoElement.value;
};

defineProps({
    isCameraActive: Boolean,
    isCameraReady: Boolean,
    isCameraLoading: Boolean,
    isSwitchingCamera: Boolean,
    isCapturing: Boolean,
    isRecording: Boolean,
    recordingTime: Number,
    hasMultipleCameras: Boolean,
    cameraError: String,
    cameraStatus: String,
    mediaCount: Object,
    MAX_PHOTOS: Number,
    MAX_VIDEOS: Number,
    MAX_VIDEO_DURATION: Number,
});

defineEmits([
    "retry-camera",
    "initialize-camera",
    "switch-camera",
    "capture-photo",
    "start-recording",
    "stop-recording",
    "stop-camera",
]);

// In CameraInterface.vue
const formatTime = (seconds) => {
    return `${seconds}s`;
};

// FIX: Expose methods to parent
defineExpose({
    videoElement,
    setVideoStream,
    getVideoElement,
});
</script>
