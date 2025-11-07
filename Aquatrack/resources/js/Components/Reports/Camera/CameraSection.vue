    <template>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <label class="block text-sm font-semibold text-gray-200">
                Capture Evidence
                <span class="text-red-500">*</span>
            </label>
            <div
                class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full"
            >
                {{ mediaCount.total }}/{{ MAX_PHOTOS + MAX_VIDEOS }} files
            </div>
        </div>

        <!-- Camera Interface -->
        <CameraInterface
            ref="cameraInterfaceRef"
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
            @retry-camera="$emit('retry-camera')"
            @initialize-camera="$emit('initialize-camera')"
            @switch-camera="$emit('switch-camera')"
            @capture-photo="$emit('capture-photo')"
            @start-recording="$emit('start-recording')"
            @stop-recording="$emit('stop-recording')"
            @stop-camera="$emit('stop-camera')"
        />

        <!-- Media Gallery -->
        <MediaGallery
            :form="form"
            @remove-media="$emit('remove-media', $event)"
            @clear-all-media="$emit('clear-all-media')"
        />

        <!-- Media requirement message -->
        <div
            v-if="form.photos.length === 0"
            class="p-4 bg-blue-50 border border-blue-200 rounded-xl"
        >
            <div class="flex items-center text-blue-700 text-sm">
                <v-icon name="hi-information-circle" class="w-5 h-5 mr-2" />
                At least one photo or video is required to submit your report
            </div>
        </div>

        <p v-if="form.errors.photos" class="text-xs text-red-500 mt-2">
            {{ form.errors.photos }}
        </p>
    </div>
</template>

<script setup>
import { ref } from "vue";
import CameraInterface from "./CameraInterface.vue";
import MediaGallery from "./MediaGallery.vue";

// FIX: Add ref to camera interface
const cameraInterfaceRef = ref(null);

// FIX: Expose method to set video stream
const setVideoStream = (stream) => {
    if (cameraInterfaceRef.value) {
        cameraInterfaceRef.value.setVideoStream(stream);
    }
};

// FIX: Expose method to get video element
const getVideoElement = () => {
    if (cameraInterfaceRef.value) {
        return cameraInterfaceRef.value.getVideoElement();
    }
    return null;
};

defineProps({
    form: Object,
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
    MAX_VIDEO_DURATION: {
        type: Number,
        required: true
    }
});

defineEmits([
    "retry-camera",
    "initialize-camera",
    "switch-camera",
    "capture-photo",
    "start-recording",
    "stop-recording",
    "stop-camera",
    "remove-media",
    "clear-all-media",
]);

// FIX: Expose methods to parent
defineExpose({
    setVideoStream,
    getVideoElement,
});
</script>
