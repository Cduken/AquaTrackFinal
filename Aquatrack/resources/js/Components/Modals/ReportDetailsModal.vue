<template>
    <transition
        enter-active-class="ease-out duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto ">
            <!-- Overlay -->
            <div
                class="fixed inset-0 bg-black/50 transition-opacity duration-300"
                @click="emit('close')"
            />

            <!-- Modal Container -->
            <div class="flex min-h-full items-stretch p-0">
                <div
                    class="relative w-full transform transition-all duration-300"
                >
                    <div
                        class="bg-white flex flex-col"
                        :class="
                            isMaximized
                                ? 'h-screen'
                                : 'h-[90vh] max-w-4xl mx-auto mt-8 rounded-1xl shadow-2xl'
                        "
                    >
                        <!-- Fixed Header -->
                        <div
                            class="flex items-center justify-between py-4 border-b border-gray-200 flex-shrink-0 bg-white px-6"
                        >
                            <h2 class="text-xl font-semibold text-gray-900">
                                Report Details
                            </h2>
                            <div class="flex items-center space-x-2">
                                <button
                                    @click="toggleMaximize"
                                    class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
                                    :title="
                                        isMaximized ? 'Minimize' : 'Maximize'
                                    "
                                >
                                    <v-icon
                                        :name="
                                            isMaximized
                                                ? 'bi-fullscreen-exit'
                                                : 'bi-fullscreen'
                                        "
                                        class="w-5 h-5"
                                    />
                                </button>
                                <button
                                    @click="emit('close')"
                                    class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
                                >
                                    <v-icon name="bi-x-lg" class="w-5 h-5" />
                                </button>
                            </div>
                        </div>

                        <!-- Scrollable Content -->
                        <div class="flex-1 overflow-y-auto p-4">
                            <div
                                v-if="report"
                                class="space-y-6 max-w-7xl mx-auto"
                            >
                                <!-- Status and Priority Badges -->
                                <div class="flex flex-wrap gap-4">
                                    <div
                                        class="flex items-center bg-gray-50 px-4 py-3 rounded-lg border border-gray-200"
                                    >
                                        <v-icon
                                            name="bi-circle-fill"
                                            class="w-5 h-5 text-blue-500 mr-2"
                                        />
                                        <span
                                            class="text-base font-medium text-gray-700"
                                            >ID: {{ report.id }}</span
                                        >
                                    </div>
                                    <span
                                        class="px-4 py-3 text-base font-semibold rounded-lg border"
                                        :class="statusClass"
                                    >
                                        {{ statusLabel }}
                                    </span>
                                    <span
                                        class="px-4 py-3 text-base font-semibold rounded-lg border"
                                        :class="priorityClass"
                                    >
                                        {{ formatPriority(report.priority) }}
                                    </span>
                                    <div
                                        class="flex items-center bg-gray-50 px-4 py-3 rounded-lg border border-gray-200"
                                    >
                                        <v-icon
                                            name="bi-calendar"
                                            class="w-5 h-5 text-gray-500 mr-2"
                                        />
                                        <span class="text-base text-gray-700">
                                            {{ formatDate(report.created_at) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Main Grid Layout -->
                                <div
                                    class="grid grid-cols-1 xl:grid-cols-2 gap-8"
                                >
                                    <!-- Left Column -->
                                    <div class="space-y-8">
                                        <!-- Location Information -->
                                        <div
                                            class="bg-white border border-gray-200 rounded-xl shadow-sm"
                                        >
                                            <div
                                                class="bg-gray-50 px-6 py-4 border-b border-gray-200"
                                            >
                                                <h3
                                                    class="text-lg font-semibold text-gray-900 flex items-center"
                                                >
                                                    <v-icon
                                                        name="bi-geo-alt"
                                                        class="w-5 h-5 text-blue-600 mr-2"
                                                    />
                                                    Location Information
                                                </h3>
                                            </div>
                                            <div class="p-6 space-y-4">
                                                <div
                                                    class="grid grid-cols-2 gap-6"
                                                >
                                                    <div>
                                                        <label
                                                            class="text-sm text-gray-500 font-medium"
                                                            >Municipality</label
                                                        >
                                                        <p
                                                            class="text-base font-medium text-gray-900 mt-2"
                                                        >
                                                            {{
                                                                report.municipality ||
                                                                "N/A"
                                                            }}
                                                        </p>
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="text-sm text-gray-500 font-medium"
                                                            >Province</label
                                                        >
                                                        <p
                                                            class="text-base font-medium text-gray-900 mt-2"
                                                        >
                                                            {{
                                                                report.province ||
                                                                "N/A"
                                                            }}
                                                        </p>
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="text-sm text-gray-500 font-medium"
                                                            >Barangay</label
                                                        >
                                                        <p
                                                            class="text-base font-medium text-gray-900 mt-2"
                                                        >
                                                            {{
                                                                report.barangay ||
                                                                "N/A"
                                                            }}
                                                        </p>
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="text-sm text-gray-500 font-medium"
                                                            >Purok/Street</label
                                                        >
                                                        <p
                                                            class="text-base font-medium text-gray-900 mt-2"
                                                        >
                                                            {{
                                                                report.purok ||
                                                                "N/A"
                                                            }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <!-- GPS Coordinates -->
                                                <div
                                                    class="bg-blue-50 p-4 rounded-lg border border-blue-200 mt-4"
                                                >
                                                    <div
                                                        class="flex items-center justify-between"
                                                    >
                                                        <div>
                                                            <label
                                                                class="text-sm text-blue-700 font-medium"
                                                                >GPS
                                                                Coordinates</label
                                                            >
                                                            <div
                                                                class="flex gap-4 mt-2"
                                                            >
                                                                <span
                                                                    class="text-base font-medium text-blue-900"
                                                                >
                                                                    Lat:
                                                                    {{
                                                                        getLatitude
                                                                    }}
                                                                </span>
                                                                <span
                                                                    class="text-base font-medium text-blue-900"
                                                                >
                                                                    Lon:
                                                                    {{
                                                                        getLongitude
                                                                    }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <v-icon
                                                            name="bi-geo-alt-fill"
                                                            class="text-blue-600 text-xl"
                                                        />
                                                    </div>
                                                </div>

                                                <!-- Map Section -->
                                                <div class="mt-5">
                                                    <div
                                                        v-if="
                                                            report.latitude &&
                                                            report.longitude
                                                        "
                                                        ref="mapContainer"
                                                        class="w-full rounded-xl border border-gray-300 shadow-sm relative h-80"
                                                    >
                                                        <div
                                                            v-if="!mapLoaded"
                                                            class="absolute inset-0 flex items-center justify-center bg-gray-100 bg-opacity-80 z-10"
                                                        >
                                                            <div
                                                                class="text-center"
                                                            >
                                                                <v-icon
                                                                    name="bi-compass"
                                                                    class="text-blue-500 text-3xl mb-2 animate-spin"
                                                                />
                                                                <p
                                                                    class="text-base text-gray-600"
                                                                >
                                                                    Loading
                                                                    map...
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div
                                                        v-else
                                                        class="w-full rounded-xl border border-gray-300 bg-gray-50 flex items-center justify-center h-80"
                                                    >
                                                        <div
                                                            class="text-center text-gray-500"
                                                        >
                                                            <v-icon
                                                                name="bi-geo-alt"
                                                                class="text-4xl mb-2"
                                                            />
                                                            <p
                                                                class="text-base"
                                                            >
                                                                No GPS
                                                                coordinates
                                                                available
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Reporter Information -->
                                        <div
                                            class="bg-white border border-gray-200 rounded-xl shadow-sm"
                                        >
                                            <div
                                                class="bg-gray-50 px-6 py-4 border-b border-gray-200"
                                            >
                                                <h3
                                                    class="text-lg font-semibold text-gray-900 flex items-center"
                                                >
                                                    <v-icon
                                                        name="bi-person"
                                                        class="w-5 h-5 text-blue-600 mr-2"
                                                    />
                                                    Reporter Information
                                                </h3>
                                            </div>
                                            <div class="p-6 space-y-4">
                                                <div
                                                    class="grid grid-cols-1 gap-4"
                                                >
                                                    <div>
                                                        <label
                                                            class="text-sm text-gray-500 font-medium"
                                                            >Reporter
                                                            Name</label
                                                        >
                                                        <p
                                                            class="text-base font-medium text-gray-900 mt-2"
                                                        >
                                                            {{
                                                                report.reporter_name ||
                                                                "N/A"
                                                            }}
                                                        </p>
                                                    </div>
                                                    <div
                                                        v-if="
                                                            report.reporter_phone
                                                        "
                                                    >
                                                        <label
                                                            class="text-sm text-gray-500 font-medium"
                                                            >Phone Number</label
                                                        >
                                                        <p
                                                            class="text-base font-medium text-gray-900 mt-2"
                                                        >
                                                            {{
                                                                report.reporter_phone
                                                            }}
                                                        </p>
                                                    </div>
                                                    <div v-if="report.user">
                                                        <label
                                                            class="text-sm text-gray-500 font-medium"
                                                            >Registered
                                                            User</label
                                                        >
                                                        <p
                                                            class="text-base font-medium text-gray-900 mt-2"
                                                        >
                                                            {{
                                                                report.user
                                                                    ?.name ||
                                                                "N/A"
                                                            }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Right Column -->
                                    <div class="space-y-8">
                                        <!-- Issue Information -->
                                        <div
                                            class="bg-white border border-gray-200 rounded-xl shadow-sm"
                                        >
                                            <div
                                                class="bg-gray-50 px-6 py-4 border-b border-gray-200"
                                            >
                                                <h3
                                                    class="text-lg font-semibold text-gray-900 flex items-center"
                                                >
                                                    <v-icon
                                                        name="bi-droplet"
                                                        class="w-5 h-5 text-blue-600 mr-2"
                                                    />
                                                    Issue Details
                                                </h3>
                                            </div>
                                            <div class="p-6 space-y-4">
                                                <div>
                                                    <label
                                                        class="text-sm text-gray-500 font-medium"
                                                        >Issue Type</label
                                                    >
                                                    <p
                                                        class="text-base font-medium text-gray-900 mt-2"
                                                    >
                                                        {{
                                                            report.water_issue_type ===
                                                            "others"
                                                                ? report.custom_water_issue ||
                                                                  "Custom Issue"
                                                                : report.water_issue_type ||
                                                                  "N/A"
                                                        }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <label
                                                        class="text-sm text-gray-500 font-medium"
                                                        >Description</label
                                                    >
                                                    <div
                                                        class="bg-gray-50 p-4 rounded-lg border border-gray-200 mt-2"
                                                    >
                                                        <p
                                                            class="text-base text-gray-700 whitespace-pre-line"
                                                        >
                                                            {{
                                                                report.description ||
                                                                "No description provided"
                                                            }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Media Section -->
                                        <div
                                            v-if="
                                                report.photos &&
                                                report.photos.length
                                            "
                                            class="bg-white border border-gray-200 rounded-xl shadow-sm"
                                        >
                                            <div
                                                class="bg-gray-50 px-6 py-4 border-b border-gray-200"
                                            >
                                                <h3
                                                    class="text-lg font-semibold text-gray-900 flex items-center"
                                                >
                                                    <v-icon
                                                        name="bi-images"
                                                        class="w-5 h-5 text-blue-600 mr-2"
                                                    />
                                                    Media ({{
                                                        report.photos.length
                                                    }})
                                                </h3>
                                            </div>
                                            <div class="p-6">
                                                <div
                                                    class="grid grid-cols-2 lg:grid-cols-3 gap-4"
                                                >
                                                    <div
                                                        v-for="(
                                                            media, index
                                                        ) in report.photos"
                                                        :key="index"
                                                        class="relative group overflow-hidden rounded-xl border border-gray-200 shadow-sm transition-all duration-300 hover:shadow-md hover:border-blue-300 cursor-pointer h-40"
                                                        @click="
                                                            openMediaModal(
                                                                media,
                                                                index
                                                            )
                                                        "
                                                    >
                                                        <!-- Video Thumbnail -->
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
                                                                <video
                                                                    muted
                                                                    loop
                                                                    class="absolute inset-0 w-full h-full object-cover group-hover:opacity-100 transition-opacity duration-300"
                                                                >
                                                                    <source
                                                                        :src="
                                                                            '/storage/' +
                                                                            media.path
                                                                        "
                                                                        type="video/mp4"
                                                                    />
                                                                </video>
                                                                <div
                                                                    class="absolute inset-0 flex items-center justify-center bg-black/20 group-hover:bg-black/10 transition-all z-10"
                                                                >
                                                                    <v-icon
                                                                        name="bi-play-circle-fill"
                                                                        class="text-white text-5xl"
                                                                    />
                                                                </div>
                                                            </div>
                                                        </template>

                                                        <!-- Image Thumbnail -->
                                                        <template v-else>
                                                            <img
                                                                :src="
                                                                    '/storage/' +
                                                                    media.path
                                                                "
                                                                :alt="`Report photo ${
                                                                    index + 1
                                                                }`"
                                                                class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                                                                loading="lazy"
                                                            />
                                                            <div
                                                                class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300 flex items-center justify-center opacity-0 group-hover:opacity-100"
                                                            >
                                                                <v-icon
                                                                    name="bi-zoom-in"
                                                                    class="text-white bg-black/50 p-2 rounded-full"
                                                                />
                                                            </div>
                                                        </template>

                                                        <div
                                                            class="absolute bottom-2 left-2 bg-black bg-opacity-50 text-white text-sm px-1 rounded"
                                                        >
                                                            {{
                                                                (
                                                                    media.size /
                                                                    1024 /
                                                                    1024
                                                                ).toFixed(2)
                                                            }}
                                                            MB
                                                            <span
                                                                v-if="
                                                                    isVideoFile(
                                                                        media
                                                                    )
                                                                "
                                                                >(Video)</span
                                                            >
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Additional Information -->
                                        <div
                                            class="bg-white border border-gray-200 rounded-xl shadow-sm"
                                        >
                                            <div
                                                class="bg-gray-50 px-6 py-4 border-b border-gray-200"
                                            >
                                                <h3
                                                    class="text-lg font-semibold text-gray-900 flex items-center"
                                                >
                                                    <v-icon
                                                        name="bi-info-circle"
                                                        class="w-5 h-5 text-blue-600 mr-2"
                                                    />
                                                    Additional Information
                                                </h3>
                                            </div>
                                            <div class="p-6 space-y-4">
                                                <div
                                                    class="grid grid-cols-2 gap-6"
                                                >
                                                    <div>
                                                        <label
                                                            class="text-sm text-gray-500 font-medium"
                                                            >Submitted
                                                            Date</label
                                                        >
                                                        <p
                                                            class="text-base font-medium text-gray-900 mt-2"
                                                        >
                                                            {{
                                                                formatDateTime(
                                                                    report.created_at
                                                                )
                                                            }}
                                                        </p>
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="text-sm text-gray-500 font-medium"
                                                            >User Type</label
                                                        >
                                                        <p
                                                            class="text-base font-medium text-gray-900 mt-2"
                                                        >
                                                            {{
                                                                report.formatted_user_types ||
                                                                "Guest"
                                                            }}
                                                        </p>
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="text-sm text-gray-500 font-medium"
                                                            >Tracking
                                                            Code</label
                                                        >
                                                        <p
                                                            class="text-base font-bold text-blue-900 mt-2"
                                                        >
                                                            {{
                                                                report.tracking_code ||
                                                                "N/A"
                                                            }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center text-gray-500 py-8">
                                <v-icon
                                    name="bi-exclamation-circle"
                                    class="text-5xl mb-2"
                                />
                                <p class="text-base">
                                    No report data available.
                                </p>
                            </div>
                        </div>

                        <!-- Fixed Footer -->
                        <div
                            class="border-t border-gray-200 px-6 py-4 bg-gray-50 flex justify-end flex-shrink-0"
                        >
                            <button
                                @click="emit('close')"
                                type="button"
                                class="inline-flex items-center px-5 py-2 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                            >
                                <v-icon name="bi-x-lg" class="mr-2" />
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>

    <!-- Media Modals -->
    <VideoModal
        :show="showVideoModal"
        :video="currentVideo"
        @close="closeVideoModal"
    />

    <ImageModal
        :show="showImageModal"
        :images="allImages"
        :current-index="currentImageIndex"
        @close="closeImageModal"
        @next="nextImage"
        @prev="prevImage"
    />
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from "vue";

const props = defineProps({
    show: {
        type: Boolean,
        required: true,
    },
    report: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(["close"]);

// State
const showVideoModal = ref(false);
const currentVideo = ref(null);
const map = ref(null);
const mapContainer = ref(null);
const mapLoaded = ref(false);
const showImageModal = ref(false);
const currentImageIndex = ref(0);
const allImages = ref([]);
const isMaximized = ref(false); // Start minimized

// Computed
const statusClass = computed(() => {
    if (!props.report?.status)
        return "bg-gray-100 text-gray-800 border-gray-200";

    const classes = {
        pending: "bg-yellow-100 text-yellow-800 border-yellow-200",
        in_progress: "bg-blue-100 text-blue-800 border-blue-200",
        resolved: "bg-green-100 text-green-800 border-green-200",
    };

    return (
        classes[props.report.status.toLowerCase()] ||
        "bg-gray-100 text-gray-800 border-gray-200"
    );
});

const priorityClass = computed(() => {
    if (!props.report?.priority)
        return "bg-gray-100 text-gray-800 border-gray-200";

    const classes = {
        high: "bg-red-100 text-red-800 border-red-200",
        medium: "bg-yellow-100 text-yellow-800 border-yellow-200",
        low: "bg-green-100 text-green-800 border-green-200",
    };

    return (
        classes[props.report.priority.toLowerCase()] ||
        "bg-gray-100 text-gray-800 border-gray-200"
    );
});

const statusLabel = computed(() => {
    if (!props.report?.status) return "Unknown";

    const labels = {
        pending: "Pending",
        in_progress: "In Progress",
        resolved: "Resolved",
    };

    return labels[props.report.status.toLowerCase()] || props.report.status;
});

const getLatitude = computed(() => {
    return props.report?.latitude
        ? Number(props.report.latitude).toFixed(6)
        : "N/A";
});

const getLongitude = computed(() => {
    return props.report?.longitude
        ? Number(props.report.longitude).toFixed(6)
        : "N/A";
});

// Methods
const toggleMaximize = async () => {
    isMaximized.value = !isMaximized.value;
    await nextTick();

    if (map.value) {
        setTimeout(() => {
            map.value?.invalidateSize();
        }, 300);
    }
};

const openMediaModal = (media, index = 0) => {
    if (isVideoFile(media)) {
        openVideoModal(media.path);
    } else {
        openImageModal(media.path, index);
    }
};

const openImageModal = (imagePath, index = 0) => {
    allImages.value = props.report.photos
        .filter((media) => !isVideoFile(media))
        .map((media) => "/storage/" + media.path);

    currentImageIndex.value = allImages.value.indexOf("/storage/" + imagePath);
    showImageModal.value = true;
};

const closeImageModal = () => {
    showImageModal.value = false;
    currentImageIndex.value = 0;
    allImages.value = [];
};

const nextImage = () => {
    if (allImages.value.length > 0) {
        currentImageIndex.value =
            (currentImageIndex.value + 1) % allImages.value.length;
    }
};

const prevImage = () => {
    if (allImages.value.length > 0) {
        currentImageIndex.value =
            (currentImageIndex.value - 1 + allImages.value.length) %
            allImages.value.length;
    }
};

const openVideoModal = (videoPath) => {
    currentVideo.value = "/storage/" + videoPath;
    showVideoModal.value = true;
};

const closeVideoModal = () => {
    showVideoModal.value = false;
    currentVideo.value = null;
};

const isVideoFile = (file) => {
    return file.type === "video" || file.mime_type?.includes("video");
};

const formatPriority = (priority) => {
    if (!priority) return "N/A Priority";
    return priority.charAt(0).toUpperCase() + priority.slice(1) + " Priority";
};

const formatDate = (dateString) => {
    return dateString ? new Date(dateString).toLocaleDateString() : "N/A";
};

const formatDateTime = (dateString) => {
    return dateString ? new Date(dateString).toLocaleString() : "N/A";
};

// Map Functions
const initializeMap = () => {
    if (
        !props.report?.latitude ||
        !props.report?.longitude ||
        !mapContainer.value
    )
        return;

    const lat = Number(props.report.latitude);
    const lon = Number(props.report.longitude);

    if (isNaN(lat) || isNaN(lon) || Math.abs(lat) > 90 || Math.abs(lon) > 180)
        return;

    const L = window.L;
    if (!L) return;

    destroyMap();

    setTimeout(() => {
        if (!mapContainer.value || map.value) return;

        try {
            map.value = L.map(mapContainer.value).setView([lat, lon], 15);

            L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                maxZoom: 19,
                attribution:
                    '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            }).addTo(map.value);

            const heading = props.report.heading
                ? Number(props.report.heading)
                : 0;

            const markerIcon = L.icon({
                iconUrl:
                    "data:image/svg+xml;base64," +
                    btoa(`
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="40" viewBox="0 0 30 40">
                        <g transform="rotate(${heading} 15 15)">
                            <path d="M15,1.5 C8.1,1.5 2.5,7.1 2.5,14 C2.5,23.8 15,38.5 15,38.5 C15,38.5 27.5,23.8 27.5,14 C27.5,7.1 21.9,1.5 15,1.5 Z" fill="#dc2626" stroke="#ffffff" stroke-width="2"/>
                            <circle cx="15" cy="13" r="5" fill="#ffffff"/>
                            <polygon points="15,5 12,15 15,12 18,15" fill="#ffffff"/>
                        </g>
                    </svg>
                `),
                iconSize: [30, 40],
                iconAnchor: [15, 40],
                popupAnchor: [0, -40],
            });

            L.marker([lat, lon], { icon: markerIcon }).addTo(map.value);

            setTimeout(() => {
                map.value?.invalidateSize();
                mapLoaded.value = true;
            }, 400);
        } catch (error) {
            console.error("Error initializing map:", error);
            mapLoaded.value = true;
        }
    }, 200);
};

const destroyMap = () => {
    if (map.value) {
        try {
            map.value.remove();
        } catch (error) {
            console.error("Error removing map:", error);
        }
        map.value = null;
    }
    mapLoaded.value = false;
};

// Watchers
watch(
    () => props.show,
    (newVal) => {
        if (newVal && props.report) {
            setTimeout(() => initializeMap(), 300);
        } else {
            destroyMap();
            isMaximized.value = false;
        }
    }
);

watch(
    () => isMaximized.value,
    () => {
        if (props.show) {
            setTimeout(() => map.value?.invalidateSize(), 400);
        }
    }
);

// Lifecycle
onMounted(() => {
    if (props.show && props.report) {
        initializeMap();
    }
});

onUnmounted(() => {
    destroyMap();
});
</script>
