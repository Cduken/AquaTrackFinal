// Customer/ReportDetailsPage.vue
<template>
    <CustomerLayout>
        <!-- Loading State -->
        <div v-if="loading" class="min-h-screen flex items-center justify-center">
            <div class="text-center">
                <Loader2 class="w-12 h-12 text-blue-600 animate-spin mx-auto mb-4" />
                <p class="text-gray-600">Loading report details...</p>
            </div>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="min-h-screen flex items-center justify-center">
            <div class="text-center">
                <AlertCircle class="w-12 h-12 text-red-500 mx-auto mb-4" />
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Report Not Found</h3>
                <p class="text-gray-600 mb-4">{{ error }}</p>
                <Link
                    :href="route('admin.reports')"
                    class="inline-flex items-center px-4 py-2  bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors"
                >
                    <ArrowLeft class="w-4 h-4 mr-2" />
                    Back to Reports
                </Link>
            </div>
        </div>

        <!-- Report Details Content -->
        <div v-else-if="report" class="min-h-screen">
            <!-- Header -->
            <div class=" border-b border-gray-200">
                <div class="max-w-full mx-auto ">
                    <div class="">
                        <!-- Back Button and Title -->
                        <div class="flex items-center gap-4 mb-4">
                            <Link
                                :href="route('admin.reports')"
                                class="inline-flex items-center border-b border-gray-200  text-gray-600 mb-2 hover:text-gray-900 transition-colors"
                            >
                                <ArrowLeft class="w-5 h-5 mr-2" />
                                Back to Reports
                            </Link>
                        </div>

                        <!-- Main Header -->
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">Report Details</h1>
                                <p class="text-gray-600 mt-1">
                                    Submitted on {{ formatDate(report.created_at) }}
                                </p>
                            </div>

                            <!-- Status and Actions -->
                            <div class="flex items-center gap-4">
                                <span :class="statusClass" class="px-4 py-2 rounded-full text-sm font-semibold">
                                    {{ statusLabel }}
                                </span>

                            </div>
                        </div>

                        <!-- Tracking Code -->
                        <div class="mt-4 p-4 bg-blue-50 rounded border border-blue-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-blue-700 font-medium">Tracking Code</p>
                                    <p class="text-lg font-bold text-blue-900">{{ report.tracking_code }}</p>
                                </div>
                                <Copy class="w-5 h-5 text-blue-600" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="max-w-7xl mx-auto mt-4">
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                    <!-- Left Column -->
                    <div class="xl:col-span-2 space-y-6">
                        <!-- Issue Information Card -->
                        <div class="bg-white rounded border border-gray-200">
                            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                                    <AlertTriangle class="w-5 h-5 text-orange-500 mr-2" />
                                    Issue Details
                                </h2>
                            </div>
                            <div class="p-6 space-y-4">
                                <!-- Issue Type -->
                                <div class="flex items-start gap-4 p-4 bg-gray-50 rounded">
                                    <Tag class="w-5 h-5 text-gray-600 mt-0.5" />
                                    <div>
                                        <p class="text-sm text-gray-600">Issue Type</p>
                                        <p class="font-medium text-gray-900">
                                            {{ report.water_issue_type === 'others'
                                                ? report.custom_water_issue || 'Custom Issue'
                                                : report.water_issue_type
                                            }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div>
                                    <p class="text-sm text-gray-600 font-medium mb-2">Description</p>
                                    <div class="bg-gray-50 p-4 rounded border border-gray-200">
                                        <p class="text-gray-700 whitespace-pre-line">
                                            {{ report.description || 'No description provided' }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Priority -->
                                <div class="flex items-start gap-4 p-4 bg-gray-50 rounded">
                                    <AlertCircle class="w-5 h-5 text-gray-600 mt-0.5" />
                                    <div>
                                        <p class="text-sm text-gray-600">Priority</p>
                                        <p class="font-medium text-gray-900">
                                            {{ formatPriority(report.priority) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Location Information Card -->
                        <div class="bg-white rounded border border-gray-200">
                            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                                    <MapPin class="w-5 h-5 text-blue-500 mr-2" />
                                    Location Information
                                </h2>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Municipality -->
                                    <div class="flex items-start gap-3 p-3 bg-gray-50 rounded">
                                        <Building class="w-4 h-4 text-gray-600 mt-0.5" />
                                        <div>
                                            <p class="text-sm text-gray-600">Municipality</p>
                                            <p class="font-medium text-gray-900">
                                                {{ report.municipality || 'N/A' }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Province -->
                                    <div class="flex items-start gap-3 p-3 bg-gray-50 rounded">
                                        <Map class="w-4 h-4 text-gray-600 mt-0.5" />
                                        <div>
                                            <p class="text-sm text-gray-600">Province</p>
                                            <p class="font-medium text-gray-900">
                                                {{ report.province || 'N/A' }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Barangay -->
                                    <div class="flex items-start gap-3 p-3 bg-gray-50 rounded">
                                        <Navigation class="w-4 h-4 text-gray-600 mt-0.5" />
                                        <div>
                                            <p class="text-sm text-gray-600">Barangay</p>
                                            <p class="font-medium text-gray-900">
                                                {{ report.barangay || 'N/A' }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Purok/Street -->
                                    <div class="flex items-start gap-3 p-3 bg-gray-50 rounded">
                                        <Signpost class="w-4 h-4 text-gray-600 mt-0.5" />
                                        <div>
                                            <p class="text-sm text-gray-600">Purok/Street</p>
                                            <p class="font-medium text-gray-900">
                                                {{ report.purok || 'N/A' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- GPS Coordinates -->
                                <!-- <div v-if="report.latitude && report.longitude" class="mt-4 p-4 bg-blue-50 rounded border border-blue-200">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm text-blue-700 font-medium">GPS Coordinates</p>
                                            <div class="flex gap-6 mt-2">
                                                <div>
                                                    <p class="text-xs text-blue-600">Latitude</p>
                                                    <p class="font-medium text-blue-900">{{ getLatitude }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-blue-600">Longitude</p>
                                                    <p class="font-medium text-blue-900">{{ getLongitude }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <MapPin class="w-5 h-5 text-blue-600" />
                                    </div>
                                </div> -->

                                <!-- Map Section -->
                                <div class="mt-4">
                                    <div v-if="report.latitude && report.longitude" class="w-full h-80 rounded border border-gray-300 bg-gray-100">
                                        <!-- Map will be rendered here -->
                                        <div id="map" class="w-full h-full rounded"></div>
                                    </div>
                                    <div v-else class="w-full h-80 rounded border border-gray-300 bg-gray-100 flex items-center justify-center">
                                        <div class="text-center text-gray-500">
                                            <MapPin class="w-12 h-12 mx-auto mb-2" />
                                            <p>No GPS coordinates available</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <!-- Reporter Information Card -->
                        <div class="bg-white rounded border border-gray-200">
                            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                                    <User class="w-5 h-5 text-purple-500 mr-2" />
                                    Reporter Information
                                </h2>
                            </div>
                            <div class="p-6 space-y-4">
                                <!-- Reporter Name -->
                                <div class="flex items-start gap-3 p-3 bg-gray-50 rounded">
                                    <User class="w-4 h-4 text-gray-600 mt-0.5" />
                                    <div>
                                        <p class="text-sm text-gray-600">Reporter Name</p>
                                        <p class="font-medium text-gray-900">
                                            {{ report.reporter_name || 'N/A' }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Phone Number -->
                                <div v-if="report.reporter_phone" class="flex items-start gap-3 p-3 bg-gray-50 rounded">
                                    <Phone class="w-4 h-4 text-gray-600 mt-0.5" />
                                    <div>
                                        <p class="text-sm text-gray-600">Phone Number</p>
                                        <p class="font-medium text-gray-900">
                                            {{ report.reporter_phone }}
                                        </p>
                                    </div>
                                </div>

                                <!-- User Type -->
                                <div class="flex items-start gap-3 p-3 bg-gray-50 rounded">
                                    <BadgeInfo class="w-4 h-4 text-gray-600 mt-0.5" />
                                    <div>
                                        <p class="text-sm text-gray-600">User Type</p>
                                        <p class="font-medium text-gray-900">
                                            {{ report.formatted_user_types || 'Guest' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Media Section -->
                        <div v-if="report.photos && report.photos.length" class="bg-white rounded border border-gray-200">
                            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                                    <ImageIcon class="w-5 h-5 text-green-500 mr-2" />
                                    Media ({{ report.photos.length }})
                                </h2>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-2 gap-3">
                                    <div
                                        v-for="(media, index) in report.photos"
                                        :key="index"
                                        class="relative group cursor-pointer h-32 rounded border border-gray-200 overflow-hidden"
                                        @click="openMedia(media, index)"
                                    >
                                        <!-- Video Thumbnail -->
                                        <template v-if="isVideoFile(media)">
                                            <div class="w-full h-full bg-gray-800 flex items-center justify-center">
                                                <video
                                                    muted
                                                    loop
                                                    class="w-full h-full object-cover"
                                                >
                                                    <source :src="getMediaPath(media.path)" type="video/mp4">
                                                </video>
                                                <div class="absolute inset-0 flex items-center justify-center bg-black/20">
                                                    <Play class="w-8 h-8 text-white" />
                                                </div>
                                            </div>
                                        </template>

                                        <!-- Image Thumbnail -->
                                        <template v-else>
                                            <img
                                                :src="getMediaPath(media.path)"
                                                :alt="`Report photo ${index + 1}`"
                                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                                loading="lazy"
                                                @error="handleImageError"
                                            />
                                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-all duration-300 flex items-center justify-center opacity-0 group-hover:opacity-100">
                                                <ZoomIn class="w-6 h-6 text-white" />
                                            </div>
                                        </template>

                                        <!-- File Info -->
                                        <div class="absolute bottom-1 left-1 bg-black/70 text-white text-xs px-1 rounded">
                                            {{ (media.size / 1024 / 1024).toFixed(2) }} MB
                                            <span v-if="isVideoFile(media)">(Video)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Timeline Card -->
                        <div class="bg-white rounded border border-gray-200">
                            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                                    <Clock class="w-5 h-5 text-gray-600 mr-2" />
                                    Report Timeline
                                </h2>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    <!-- Submitted -->
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                <CheckCircle class="w-4 h-4 text-blue-600" />
                                            </div>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">Report Submitted</p>
                                            <p class="text-sm text-gray-600">{{ formatDateTime(report.created_at) }}</p>
                                        </div>
                                    </div>

                                    <!-- Status Updates -->
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                                <Clock class="w-4 h-4 text-gray-600" />
                                            </div>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">Current Status</p>
                                            <p class="text-sm text-gray-600">{{ statusLabel }}</p>
                                            <p class="text-xs text-gray-500">Last updated</p>
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
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import CustomerLayout from '@/Layouts/CustomerLayout.vue';
import ImageModal from '@/Components/Modals/ImageModal.vue';
import VideoModal from '@/Components/Modals/VideoModal.vue';
import {
    ArrowLeft,
    Eye,
    Loader2,
    AlertCircle,
    Printer,
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
    CheckCircle
} from 'lucide-vue-next';

const props = defineProps({
    report: {
        type: Object,
        default: null
    },
    error: {
        type: String,
        default: null
    }
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
    if (!props.report?.status) return 'bg-gray-100 text-gray-800';

    const classes = {
        pending: 'bg-yellow-100 text-yellow-800',
        in_progress: 'bg-blue-100 text-blue-800',
        resolved: 'bg-green-100 text-green-800',
    };

    return classes[props.report.status.toLowerCase()] || 'bg-gray-100 text-gray-800';
});

const statusLabel = computed(() => {
    if (!props.report?.status) return 'Unknown';

    const labels = {
        pending: 'Pending',
        in_progress: 'In Progress',
        resolved: 'Resolved',
    };

    return labels[props.report.status.toLowerCase()] || props.report.status;
});

const getLatitude = computed(() => {
    return props.report?.latitude
        ? Number(props.report.latitude).toFixed(6)
        : 'N/A';
});

const getLongitude = computed(() => {
    return props.report?.longitude
        ? Number(props.report.longitude).toFixed(6)
        : 'N/A';
});

// Methods
const formatPriority = (priority) => {
    if (!priority) return 'N/A Priority';
    return priority.charAt(0).toUpperCase() + priority.slice(1) + ' Priority';
};

const formatDate = (dateString) => {
    return dateString ? new Date(dateString).toLocaleDateString() : 'N/A';
};

const formatDateTime = (dateString) => {
    return dateString ? new Date(dateString).toLocaleString() : 'N/A';
};

const getMediaPath = (path) => {
    const cleanPath = path.replace(/^\/?storage\/?/, '');
    return `/storage/${cleanPath}`;
};

const handleImageError = (event) => {
    console.error('Failed to load image:', event.target.src);
    event.target.src = '/images/placeholder.jpg';
};

const isVideoFile = (file) => {
    return (
        file.type === 'video' ||
        file.mime_type?.includes('video') ||
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
    const imageMedia = props.report.photos.filter(media => !isVideoFile(media));
    allImages.value = imageMedia.map(media => getMediaPath(media.path));

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
        currentImageIndex.value = (currentImageIndex.value + 1) % allImages.value.length;
    }
};

const prevImage = () => {
    if (allImages.value.length > 0) {
        currentImageIndex.value = (currentImageIndex.value - 1 + allImages.value.length) % allImages.value.length;
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

const printReport = () => {
    window.print();
};

// Map Functions
const initializeMap = () => {
    if (!props.report?.latitude || !props.report?.longitude) return;

    const lat = Number(props.report.latitude);
    const lon = Number(props.report.longitude);

    if (isNaN(lat) || isNaN(lon) || Math.abs(lat) > 90 || Math.abs(lon) > 180) return;

    const L = window.L;
    if (!L) return;

    destroyMap();

    setTimeout(() => {
        try {
            map.value = L.map('map').setView([lat, lon], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map.value);

            const heading = props.report.heading ? Number(props.report.heading) : 0;

            const markerIcon = L.icon({
                iconUrl: 'data:image/svg+xml;base64,' + btoa(`
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
            console.error('Error initializing map:', error);
        }
    }, 100);
};

const destroyMap = () => {
    if (map.value) {
        try {
            map.value.remove();
        } catch (error) {
            console.error('Error removing map:', error);
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
/* Print styles */
@media print {
    .no-print {
        display: none !important;
    }

    body {
        background: white !important;
    }

    .bg-gray-50 {
        background: white !important;
    }

    .border {
        border-color: #000 !important;
    }
}
</style>
