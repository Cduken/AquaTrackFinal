//Pages/Admin/ReportDetailsPage.vue // Customer/ReportDetailsPage.vue
<template>
    <CustomerLayout>
        <!-- Loading State -->
        <div
            v-if="loading"
            class="min-h-screen flex items-center justify-center bg-gray-50"
        >
            <div class="text-center">
                <div class="relative">
                    <Loader2
                        class="w-12 h-12 text-blue-600 animate-spin mx-auto mb-4"
                    />
                    <div
                        class="absolute inset-0 bg-blue-600/10 rounded-full animate-ping"
                    ></div>
                </div>
                <p class="text-gray-600 mt-2">Loading report details...</p>
            </div>
        </div>

        <!-- Error State -->
        <div
            v-else-if="error"
            class="min-h-screen flex items-center justify-center bg-gray-50 px-4"
        >
            <div class="text-center max-w-md">
                <div class="relative inline-block mb-4">
                    <AlertCircle class="w-16 h-16 text-red-500 mx-auto" />
                    <div
                        class="absolute -inset-4 bg-red-100 rounded-full animate-pulse"
                    ></div>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">
                    Report Not Found
                </h3>
                <p class="text-gray-600 mb-6">{{ error }}</p>
                <Link
                    :href="route('admin.reports')"
                    class="inline-flex items-center px-5 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium rounded-sm shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105"
                >
                    <ArrowLeft class="w-4 h-4 mr-2" />
                    Back to Reports
                </Link>
            </div>
        </div>

        <!-- Report Details Content -->
        <div v-else-if="report" class="min-h-screen">
            <!-- Header Section -->
            <div class="border-gray-200">
                <div class="max-w-7xl mx-auto">
                    <div class="">
                        <!-- Main Header -->
                        <div
                            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
                        >
                            <div class="flex-1">
                                <div
                                    class="flex items-center flex-wrap gap-2 mb-2"
                                >
                                    <h1
                                        class="text-2xl sm:text-3xl font-bold text-gray-900"
                                    >
                                        Report Details
                                    </h1>
                                    <span
                                        :class="statusClass"
                                        class="px-3 py-1 rounded-full text-xs font-semibold shadow-sm"
                                    >
                                        {{ statusLabel }}
                                    </span>
                                </div>
                                <div class="flex items-center text-gray-500">
                                    <Clock class="w-4 h-4 mr-1.5" />
                                    <span class="text-sm"
                                        >Submitted on
                                        {{
                                            formatDate(report.created_at)
                                        }}</span
                                    >
                                </div>
                            </div>

                            <div
                                class="flex justify-start lg:justify-end mt-2 sm:mt-0"
                            >
                                <Link
                                    :href="route('admin.reports')"
                                    class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors duration-200 group"
                                >
                                    <div
                                        class="flex items-center justify-center w-8 h-8 bg-blue-100 rounded-lg mr-2 group-hover:bg-blue-200 transition-colors"
                                    >
                                        <ArrowLeft class="w-4 h-4" />
                                    </div>
                                    Back to Reports
                                </Link>
                            </div>
                        </div>

                        <!-- Tracking Code -->
                        <div
                            class="mt-4 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-sm border border-blue-200 shadow-sm"
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    <p
                                        class="text-sm text-blue-700 font-medium mb-1"
                                    >
                                        Tracking Code
                                    </p>
                                    <p
                                        class="text-xl font-bold text-blue-900 font-mono"
                                    >
                                        {{ report.tracking_code }}
                                    </p>
                                </div>
                                <button
                                    @click="copyTrackingCode"
                                    class="flex items-center justify-center w-10 h-10 bg-white rounded-lg shadow-sm border border-blue-200 hover:bg-blue-50 transition-colors"
                                >
                                    <Copy class="w-5 h-5 text-blue-600" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="max-w-7xl mx-auto mt-4">
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                    <!-- Left Column -->
                    <div class="xl:col-span-2 space-y-6">
                        <!-- Issue Information Card -->
                        <div
                            class="bg-white rounded-sm shadow-sm border border-gray-200 overflow-hidden"
                        >
                            <div
                                class="px-5 py-4 bg-gradient-to-r from-orange-50 to-amber-50 border-b border-orange-100"
                            >
                                <h2
                                    class="text-lg font-semibold text-gray-900 flex items-center"
                                >
                                    <div
                                        class="flex items-center justify-center w-8 h-8 bg-orange-100 rounded-lg mr-3"
                                    >
                                        <AlertTriangle
                                            class="w-5 h-5 text-orange-600"
                                        />
                                    </div>
                                    Issue Details
                                </h2>
                            </div>
                            <div class="p-5 space-y-4">
                                <!-- Issue Type -->
                                <div
                                    class="flex items-start gap-4 p-4 bg-orange-50 rounded-sm border border-orange-100"
                                >
                                    <div
                                        class="flex items-center justify-center w-10 h-10 bg-orange-100 rounded-lg flex-shrink-0"
                                    >
                                        <Tag class="w-5 h-5 text-orange-600" />
                                    </div>
                                    <div>
                                        <p
                                            class="text-sm text-orange-700 font-medium mb-1"
                                        >
                                            Issue Type
                                        </p>
                                        <p class="font-semibold text-gray-900">
                                            {{
                                                report.water_issue_type ===
                                                "others"
                                                    ? report.custom_water_issue ||
                                                      "Custom Issue"
                                                    : report.water_issue_type
                                            }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div>
                                    <p
                                        class="text-sm text-gray-700 font-medium mb-3"
                                    >
                                        Description
                                    </p>
                                    <div
                                        class="bg-gray-50 p-4 rounded-sm border border-gray-200"
                                    >
                                        <p
                                            class="text-gray-700 whitespace-pre-line leading-relaxed"
                                        >
                                            {{
                                                report.description ||
                                                "No description provided"
                                            }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Priority -->
                                <div
                                    class="flex items-start gap-4 p-4 bg-gray-50 rounded-sm border border-gray-200"
                                >
                                    <div
                                        class="flex items-center justify-center w-10 h-10 bg-gray-100 rounded-lg flex-shrink-0"
                                    >
                                        <AlertCircle
                                            class="w-5 h-5 text-gray-600"
                                        />
                                    </div>
                                    <div>
                                        <p
                                            class="text-sm text-gray-700 font-medium mb-1"
                                        >
                                            Priority
                                        </p>
                                        <p class="font-semibold text-gray-900">
                                            {{
                                                formatPriority(report.priority)
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Location Information Card -->
                        <div
                            class="bg-white rounded-sm shadow-sm border border-gray-200 overflow-hidden"
                        >
                            <div
                                class="px-5 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-blue-100"
                            >
                                <h2
                                    class="text-lg font-semibold text-gray-900 flex items-center"
                                >
                                    <div
                                        class="flex items-center justify-center w-8 h-8 bg-blue-100 rounded-lg mr-3"
                                    >
                                        <MapPin class="w-5 h-5 text-blue-600" />
                                    </div>
                                    Location Information
                                </h2>
                            </div>
                            <div class="p-5">
                                <div
                                    class="grid grid-cols-1 sm:grid-cols-2 gap-4"
                                >
                                    <!-- Municipality -->
                                    <div
                                        class="flex items-start gap-3 p-3 bg-blue-50 rounded-sm border border-blue-100"
                                    >
                                        <Building
                                            class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0"
                                        />
                                        <div>
                                            <p
                                                class="text-sm text-blue-700 font-medium"
                                            >
                                                Municipality
                                            </p>
                                            <p
                                                class="font-semibold text-gray-900"
                                            >
                                                {{
                                                    report.municipality || "N/A"
                                                }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Province -->
                                    <div
                                        class="flex items-start gap-3 p-3 bg-blue-50 rounded-sm border border-blue-100"
                                    >
                                        <Map
                                            class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0"
                                        />
                                        <div>
                                            <p
                                                class="text-sm text-blue-700 font-medium"
                                            >
                                                Province
                                            </p>
                                            <p
                                                class="font-semibold text-gray-900"
                                            >
                                                {{ report.province || "N/A" }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Barangay -->
                                    <div
                                        class="flex items-start gap-3 p-3 bg-blue-50 rounded-sm border border-blue-100"
                                    >
                                        <Navigation
                                            class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0"
                                        />
                                        <div>
                                            <p
                                                class="text-sm text-blue-700 font-medium"
                                            >
                                                Barangay
                                            </p>
                                            <p
                                                class="font-semibold text-gray-900"
                                            >
                                                {{ report.barangay || "N/A" }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Purok/Street -->
                                    <div
                                        class="flex items-start gap-3 p-3 bg-blue-50 rounded-sm border border-blue-100"
                                    >
                                        <Signpost
                                            class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0"
                                        />
                                        <div>
                                            <p
                                                class="text-sm text-blue-700 font-medium"
                                            >
                                                Purok/Street
                                            </p>
                                            <p
                                                class="font-semibold text-gray-900"
                                            >
                                                {{ report.purok || "N/A" }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Map Section -->
                                <div class="mt-6">
                                    <div
                                        v-if="
                                            report.latitude && report.longitude
                                        "
                                        class="w-full h-80 rounded-sm border border-gray-300 bg-gray-100 overflow-hidden shadow-sm"
                                    >
                                        <div
                                            id="map"
                                            class="w-full h-full rounded-sm"
                                        ></div>
                                    </div>
                                    <div
                                        v-else
                                        class="w-full h-80 rounded-sm border border-gray-300 bg-gray-100 flex items-center justify-center"
                                    >
                                        <div class="text-center text-gray-500">
                                            <MapPin
                                                class="w-12 h-12 mx-auto mb-3 text-gray-400"
                                            />
                                            <p class="font-medium">
                                                No GPS coordinates available
                                            </p>
                                            <p class="text-sm mt-1">
                                                Location data is missing for
                                                this report
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <!-- Reporter Information Card -->
                        <div
                            class="bg-white rounded-sm shadow-sm border border-gray-200 overflow-hidden"
                        >
                            <div
                                class="px-5 py-4 bg-gradient-to-r from-purple-50 to-violet-50 border-b border-purple-100"
                            >
                                <h2
                                    class="text-lg font-semibold text-gray-900 flex items-center"
                                >
                                    <div
                                        class="flex items-center justify-center w-8 h-8 bg-purple-100 rounded-lg mr-3"
                                    >
                                        <User class="w-5 h-5 text-purple-600" />
                                    </div>
                                    Reporter Information
                                </h2>
                            </div>
                            <div class="p-5 space-y-4">
                                <!-- Reporter Name -->
                                <div
                                    class="flex items-start gap-3 p-3 bg-purple-50 rounded-sm border border-purple-100"
                                >
                                    <User
                                        class="w-5 h-5 text-purple-600 mt-0.5 flex-shrink-0"
                                    />
                                    <div>
                                        <p
                                            class="text-sm text-purple-700 font-medium"
                                        >
                                            Reporter Name
                                        </p>
                                        <p class="font-semibold text-gray-900">
                                            {{ report.reporter_name || "N/A" }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Phone Number -->
                                <div
                                    v-if="report.reporter_phone"
                                    class="flex items-start gap-3 p-3 bg-purple-50 rounded-sm border border-purple-100"
                                >
                                    <Phone
                                        class="w-5 h-5 text-purple-600 mt-0.5 flex-shrink-0"
                                    />
                                    <div>
                                        <p
                                            class="text-sm text-purple-700 font-medium"
                                        >
                                            Phone Number
                                        </p>
                                        <p class="font-semibold text-gray-900">
                                            {{ report.reporter_phone }}
                                        </p>
                                    </div>
                                </div>

                                <!-- User Type -->
                                <div
                                    class="flex items-start gap-3 p-3 bg-purple-50 rounded-sm border border-purple-100"
                                >
                                    <BadgeInfo
                                        class="w-5 h-5 text-purple-600 mt-0.5 flex-shrink-0"
                                    />
                                    <div>
                                        <p
                                            class="text-sm text-purple-700 font-medium"
                                        >
                                            User Type
                                        </p>
                                        <p class="font-semibold text-gray-900">
                                            {{
                                                report.formatted_user_types ||
                                                "Guest"
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Media Section -->
                        <div
                            v-if="report.photos && report.photos.length"
                            class="bg-white rounded-sm shadow-sm border border-gray-200 overflow-hidden"
                        >
                            <div
                                class="px-5 py-4 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-green-100"
                            >
                                <h2
                                    class="text-lg font-semibold text-gray-900 flex items-center"
                                >
                                    <div
                                        class="flex items-center justify-center w-8 h-8 bg-green-100 rounded-lg mr-3"
                                    >
                                        <ImageIcon
                                            class="w-5 h-5 text-green-600"
                                        />
                                    </div>
                                    Media ({{ report.photos.length }})
                                </h2>
                            </div>
                            <div class="p-5">
                                <div
                                    class="grid grid-cols-2 sm:grid-cols-3 gap-3"
                                >
                                    <div
                                        v-for="(media, index) in report.photos"
                                        :key="index"
                                        class="relative group cursor-pointer h-28 rounded-sm border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 hover:scale-105"
                                        @click="openMedia(media, index)"
                                    >
                                        <!-- Video Thumbnail -->
                                        <template v-if="isVideoFile(media)">
                                            <div
                                                class="w-full h-full bg-gray-800 flex items-center justify-center"
                                            >
                                                <video
                                                    muted
                                                    loop
                                                    class="w-full h-full object-cover"
                                                >
                                                    <source
                                                        :src="
                                                            getMediaPath(
                                                                media.path
                                                            )
                                                        "
                                                        type="video/mp4"
                                                    />
                                                </video>
                                                <div
                                                    class="absolute inset-0 flex items-center justify-center bg-black/30"
                                                >
                                                    <div
                                                        class="flex items-center justify-center w-10 h-10 bg-white/90 rounded-full"
                                                    >
                                                        <Play
                                                            class="w-5 h-5 text-gray-800"
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- Image Thumbnail -->
                                        <template v-else>
                                            <img
                                                :src="getMediaPath(media.path)"
                                                :alt="`Report photo ${
                                                    index + 1
                                                }`"
                                                class="w-full h-full object-cover"
                                                loading="lazy"
                                                @error="handleImageError"
                                            />
                                            <div
                                                class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-all duration-300 flex items-center justify-center opacity-0 group-hover:opacity-100"
                                            >
                                                <div
                                                    class="flex items-center justify-center w-10 h-10 bg-white/90 rounded-full"
                                                >
                                                    <ZoomIn
                                                        class="w-5 h-5 text-gray-800"
                                                    />
                                                </div>
                                            </div>
                                        </template>

                                        <!-- File Info -->
                                        <div
                                            class="absolute bottom-1 left-1 bg-black/80 text-white text-xs px-2 py-1 rounded-lg backdrop-blur-sm"
                                        >
                                            {{
                                                (
                                                    media.size /
                                                    1024 /
                                                    1024
                                                ).toFixed(2)
                                            }}
                                            MB
                                            <span v-if="isVideoFile(media)"
                                                >(Video)</span
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Timeline Card -->
                        <div
                            class="bg-white rounded-sm shadow-sm border border-gray-200 overflow-hidden"
                        >
                            <div
                                class="px-5 py-4 bg-gradient-to-r from-gray-50 to-slate-50 border-b border-gray-200"
                            >
                                <h2
                                    class="text-lg font-semibold text-gray-900 flex items-center"
                                >
                                    <div
                                        class="flex items-center justify-center w-8 h-8 bg-gray-100 rounded-lg mr-3"
                                    >
                                        <Clock class="w-5 h-5 text-gray-600" />
                                    </div>
                                    Report Timeline
                                </h2>
                            </div>
                            <div class="p-5">
                                <div class="space-y-5">
                                    <!-- Submitted -->
                                    <div class="flex items-start gap-4">
                                        <div class="flex-shrink-0 relative">
                                            <div
                                                class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center shadow-sm"
                                            >
                                                <CheckCircle
                                                    class="w-5 h-5 text-blue-600"
                                                />
                                            </div>
                                            <div
                                                class="absolute top-10 bottom-0 left-1/2 w-0.5 bg-blue-200 transform -translate-x-1/2"
                                            ></div>
                                        </div>
                                        <div class="flex-1 pb-5">
                                            <p
                                                class="font-semibold text-gray-900"
                                            >
                                                Report Submitted
                                            </p>
                                            <p
                                                class="text-sm text-gray-600 mt-1"
                                            >
                                                {{
                                                    formatDateTime(
                                                        report.created_at
                                                    )
                                                }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Status Updates -->
                                    <div class="flex items-start gap-4">
                                        <div class="flex-shrink-0">
                                            <div
                                                class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center shadow-sm"
                                            >
                                                <Clock
                                                    class="w-5 h-5 text-gray-600"
                                                />
                                            </div>
                                        </div>
                                        <div>
                                            <p
                                                class="font-semibold text-gray-900"
                                            >
                                                Current Status
                                            </p>
                                            <p
                                                class="text-sm text-gray-600 mt-1"
                                            >
                                                {{ statusLabel }}
                                            </p>
                                            <p
                                                class="text-xs text-gray-500 mt-1"
                                            >
                                                Last updated
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
    </CustomerLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { Link, router } from "@inertiajs/vue3";

import ImageModal from "@/Components/Modals/ImageModal.vue";
import VideoModal from "@/Components/Modals/VideoModal.vue";
import {
    ArrowLeft,
    Eye,
    Loader2,
    AlertCircle,
    Copy,
    AlertTriangle,
    Tag,
    MapPin,
    Building,
    Map,
    Navigation,
    Signpost,
    User,
    Phone,
    BadgeInfo,
    ImageIcon,
    Play,
    ZoomIn,
    Clock,
    CheckCircle,
} from "lucide-vue-next";

import CustomerLayout from "@/Layouts/CustomerLayout.vue";

const props = defineProps({
    report: {
        type: Object,
        default: null,
    },
    error: {
        type: String,
        default: null,
    },
});

// Reactive state
const loading = ref(false);
const showVideoModal = ref(false);
const showImageModal = ref(false);
const currentVideo = ref(null);
const currentImageIndex = ref(0);
const allImages = ref([]);
const map = ref(null);

// Computed properties
const statusClass = computed(() => {
    if (!props.report?.status) return "bg-gray-100 text-gray-800";

    const classes = {
        pending: "bg-yellow-100 text-yellow-800 border border-yellow-200",
        in_progress: "bg-blue-100 text-blue-800 border border-blue-200",
        resolved: "bg-green-100 text-green-800 border border-green-200",
    };

    return (
        classes[props.report.status.toLowerCase()] ||
        "bg-gray-100 text-gray-800 border border-gray-200"
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
const formatPriority = (priority) => {
    if (!priority) return "N/A Priority";
    return priority.charAt(0).toUpperCase() + priority.slice(1) + " Priority";
};

const formatDate = (dateString) => {
    return dateString
        ? new Date(dateString).toLocaleDateString("en-US", {
              year: "numeric",
              month: "long",
              day: "numeric",
          })
        : "N/A";
};

const formatDateTime = (dateString) => {
    return dateString
        ? new Date(dateString).toLocaleString("en-US", {
              year: "numeric",
              month: "short",
              day: "numeric",
              hour: "2-digit",
              minute: "2-digit",
          })
        : "N/A";
};

const getMediaPath = (path) => {
    const cleanPath = path.replace(/^\/?storage\/?/, "");
    return `/storage/${cleanPath}`;
};

const handleImageError = (event) => {
    console.error("Failed to load image:", event.target.src);
    event.target.src = "/images/placeholder.jpg";
};

const isVideoFile = (file) => {
    return (
        file.type === "video" ||
        file.mime_type?.includes("video") ||
        file.original_name?.match(/\.(mp4|mov|avi|webm)$/i) ||
        file.path?.match(/\.(mp4|mov|avi|webm)$/i)
    );
};

const openMedia = (media, index = 0) => {
    if (isVideoFile(media)) {
        openVideoModal(media.path);
    } else {
        openImageModal(media.path, index);
    }
};

const openImageModal = (imagePath, index = 0) => {
    const imageMedia = props.report.photos.filter(
        (media) => !isVideoFile(media)
    );
    allImages.value = imageMedia.map((media) => getMediaPath(media.path));

    const targetImagePath = getMediaPath(imagePath);
    currentImageIndex.value = allImages.value.indexOf(targetImagePath);

    if (currentImageIndex.value === -1) {
        currentImageIndex.value = index;
    }

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
    currentVideo.value = getMediaPath(videoPath);
    showVideoModal.value = true;
};

const closeVideoModal = () => {
    showVideoModal.value = false;
    currentVideo.value = null;
};

const copyTrackingCode = async () => {
    try {
        await navigator.clipboard.writeText(props.report.tracking_code);
        // You could add a toast notification here
        console.log("Tracking code copied to clipboard");
    } catch (err) {
        console.error("Failed to copy tracking code: ", err);
    }
};

// Map Functions
const initializeMap = () => {
    if (!props.report?.latitude || !props.report?.longitude) return;

    const lat = Number(props.report.latitude);
    const lon = Number(props.report.longitude);

    if (isNaN(lat) || isNaN(lon) || Math.abs(lat) > 90 || Math.abs(lon) > 180)
        return;

    const L = window.L;
    if (!L) return;

    destroyMap();

    setTimeout(() => {
        try {
            map.value = L.map("map").setView([lat, lon], 15);

            L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                maxZoom: 19,
                attribution: "&copy; OpenStreetMap contributors",
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
        } catch (error) {
            console.error("Error initializing map:", error);
        }
    }, 100);
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
};

// Lifecycle
onMounted(() => {
    if (props.report) {
        initializeMap();
    }
});

onUnmounted(() => {
    destroyMap();
});
</script>

<style scoped>
/* Custom scrollbar for webkit browsers */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
}

::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
