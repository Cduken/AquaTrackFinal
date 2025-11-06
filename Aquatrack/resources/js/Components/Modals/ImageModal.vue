<template>
    <transition
        enter-active-class="ease-out duration-500"
        enter-from-class="opacity-0 scale-95"
        enter-to-class="opacity-100 scale-100"
        leave-active-class="ease-in duration-300"
        leave-from-class="opacity-100 scale-100"
        leave-to-class="opacity-0 scale-95"
    >
        <div v-if="show" class="fixed inset-0 z-[9999] overflow-y-auto">
            <!-- Enhanced Overlay with Gradient -->
            <div
                class="fixed inset-0 bg-gradient-to-br from-black/90 via-purple-900/20 to-black/90 backdrop-blur-xl transition-opacity duration-500"
                @click="close"
            />

            <!-- Modal Container -->
            <div class="flex min-h-full items-center justify-center p-4">
                <div
                    class="relative w-full max-w-6xl xl:max-w-7xl transform transition-all duration-500"
                >
                    <!-- Alternative: Integrated Close Button -->
                    <button
                        @click="close"
                        class="absolute top-6 right-6 z-50 p-3  text-white rounded-xl hover:bg-black/70 hover:scale-110 transition-all duration-300 border border-white/20 hover:border-white/40 shadow-lg group"
                        title="Close (Esc)"
                    >
                        <v-icon
                            name="bi-x"
                            class="w-5 h-5 group-hover:rotate-90 transition-transform duration-300"
                        />
                    </button>

                    <!-- Enhanced Navigation Buttons -->
                    <button
                        v-if="images.length > 1"
                        @click="prev"
                        class="absolute left-6 top-1/2 transform -translate-y-1/2 z-10 p-4 bg-white/10 backdrop-blur-md text-white rounded-xl hover:bg-white/20 hover:scale-110 transition-all duration-300 border border-white/20 hover:border-white/40 group"
                        title="Previous (←)"
                    >
                        <v-icon
                            name="bi-chevron-left"
                            class="w-6 h-6 group-hover:-translate-x-0.5 transition-transform duration-300"
                        />
                    </button>

                    <button
                        v-if="images.length > 1"
                        @click="next"
                        class="absolute right-6 top-1/2 transform -translate-y-1/2 z-10 p-4 bg-white/10 backdrop-blur-md text-white rounded-xl hover:bg-white/20 hover:scale-110 transition-all duration-300 border border-white/20 hover:border-white/40 group"
                        title="Next (→)"
                    >
                        <v-icon
                            name="bi-chevron-right"
                            class="w-6 h-6 group-hover:translate-x-0.5 transition-transform duration-300"
                        />
                    </button>

                    <!-- Enhanced Image Counter -->
                    <div
                        v-if="images.length > 1"
                        class="absolute top-6 left-1/2 transform -translate-x-1/2 z-10 px-6 py-3 bg-white/10 backdrop-blur-md text-white rounded-full text-sm font-medium border border-white/20"
                    >
                        <span class="flex items-center space-x-2">
                            <v-icon name="bi-image" class="w-4 h-4" />
                            <span
                                >{{ currentIndex + 1 }} of
                                {{ images.length }}</span
                            >
                        </span>
                    </div>

                    <!-- Image Display Container with Enhanced Design -->
                    <div
                        class="relative bg-transparent rounded-3xl overflow-hidden shadow-2xl"
                    >
                        <!-- Loading State -->
                        <div
                            v-if="!imageLoaded"
                            class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-gray-900 to-gray-800 z-10"
                        >
                            <div class="text-center">
                                <div
                                    class="w-16 h-16 border-4 border-white/20 border-t-white rounded-full animate-spin mx-auto mb-4"
                                ></div>
                                <p class="text-white/80 font-medium">
                                    Loading image...
                                </p>
                            </div>
                        </div>

                        <!-- Main Image with Enhanced Effects -->
                        <div class="relative overflow-hidden rounded-3xl">
                            <img
                                :src="currentImage"
                                :alt="`Report image ${currentIndex + 1}`"
                                class="w-full h-auto max-h-[75vh] object-contain transition-opacity duration-500"
                                :class="
                                    imageLoaded ? 'opacity-100' : 'opacity-0'
                                "
                                @load="onImageLoad"
                                @error="onImageError"
                                @click.stop
                            />


                        </div>


                    </div>

                    <!-- Additional Controls -->
                    <div
                        class="flex items-center justify-center space-x-4 mt-6"
                    >
                        <!-- Download Button -->
                        <button
                            @click="downloadImage"
                            class="px-6 py-3 bg-white/10 backdrop-blur-md text-white rounded-xl hover:bg-white/20 transition-all duration-300 border border-white/20 hover:border-white/40 flex items-center space-x-2 group"
                        >
                            <v-icon
                                name="bi-download"
                                class="w-4 h-4 group-hover:scale-110 transition-transform duration-300"
                            />
                            <span class="text-sm font-medium">Download</span>
                        </button>

                        <!-- Zoom Controls -->
                        <div
                            class="flex items-center space-x-2 bg-white/10 backdrop-blur-md rounded-xl p-1 border border-white/20"
                        >
                            <button
                                @click="zoomOut"
                                class="p-2 text-white hover:bg-white/20 rounded-lg transition-all duration-200"
                                :disabled="zoomLevel <= 0.5"
                            >
                                <v-icon name="bi-dash" class="w-4 h-4" />
                            </button>
                            <span
                                class="text-white text-sm font-medium min-w-12 text-center"
                            >
                                {{ Math.round(zoomLevel * 100) }}%
                            </span>
                            <button
                                @click="zoomIn"
                                class="p-2 text-white hover:bg-white/20 rounded-lg transition-all duration-200"
                                :disabled="zoomLevel >= 3"
                            >
                                <v-icon name="bi-plus" class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted } from "vue";

const props = defineProps({
    show: {
        type: Boolean,
        required: true,
    },
    images: {
        type: Array,
        default: () => [],
    },
    currentIndex: {
        type: Number,
        default: 0,
    },
});

const emit = defineEmits(["close", "next", "prev"]);

// Enhanced state management
const imageLoaded = ref(false);
const showInfo = ref(true);
const isFullscreen = ref(false);
const zoomLevel = ref(1);

const currentImage = computed(() => {
    return props.images[props.currentIndex];
});

// Enhanced methods
const close = () => {
    resetState();
    emit("close");
};

const next = () => {
    resetImageState();
    emit("next");
};

const prev = () => {
    resetImageState();
    emit("prev");
};

const goToImage = (index) => {
    resetImageState();
    emit("update:currentIndex", index);
};

const toggleInfo = () => {
    showInfo.value = !showInfo.value;
};

const toggleFullscreen = () => {
    isFullscreen.value = !isFullscreen.value;
    // Implement fullscreen logic here if needed
};

const zoomIn = () => {
    if (zoomLevel.value < 3) {
        zoomLevel.value = Math.min(zoomLevel.value + 0.25, 3);
    }
};

const zoomOut = () => {
    if (zoomLevel.value > 0.5) {
        zoomLevel.value = Math.max(zoomLevel.value - 0.25, 0.5);
    }
};

const resetZoom = () => {
    zoomLevel.value = 1;
};

const onImageLoad = () => {
    imageLoaded.value = true;
    resetZoom();
};

const onImageError = () => {
    imageLoaded.value = true;
    console.error("Failed to load image:", currentImage.value);
};

const resetImageState = () => {
    imageLoaded.value = false;
    resetZoom();
};

const resetState = () => {
    imageLoaded.value = false;
    showInfo.value = true;
    isFullscreen.value = false;
    zoomLevel.value = 1;
};

const downloadImage = () => {
    if (!currentImage.value) return;

    const link = document.createElement("a");
    link.href = currentImage.value;
    link.download = `report-image-${props.currentIndex + 1}.jpg`;
    link.target = "_blank";
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};

// Enhanced keyboard navigation
const handleKeydown = (e) => {
    if (!props.show) return;

    switch (e.key) {
        case "Escape":
            close();
            break;
        case "ArrowLeft":
            prev();
            break;
        case "ArrowRight":
            next();
            break;
        case " ":
            e.preventDefault();
            next();
            break;
        case "+":
        case "=":
            e.preventDefault();
            zoomIn();
            break;
        case "-":
            e.preventDefault();
            zoomOut();
            break;
        case "0":
            e.preventDefault();
            resetZoom();
            break;
        case "i":
        case "I":
            e.preventDefault();
            toggleInfo();
            break;
        case "f":
        case "F":
            e.preventDefault();
            toggleFullscreen();
            break;
    }
};

// Enhanced mouse wheel for zoom
const handleWheel = (e) => {
    if (!props.show) return;

    if (e.ctrlKey) {
        e.preventDefault();
        if (e.deltaY < 0) {
            zoomIn();
        } else {
            zoomOut();
        }
    }
};

// Enhanced event listeners
onMounted(() => {
    document.addEventListener("keydown", handleKeydown);
    document.addEventListener("wheel", handleWheel, { passive: false });
});

onUnmounted(() => {
    document.removeEventListener("keydown", handleKeydown);
    document.removeEventListener("wheel", handleWheel);
});
</script>

<style scoped>
/* Custom scrollbar for thumbnails */
.scrollbar-thin::-webkit-scrollbar {
    height: 6px;
}

.scrollbar-thin::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
}

.scrollbar-thin::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 10px;
}

.scrollbar-thin::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
}

/* Smooth scrolling */
.overflow-x-auto {
    scroll-behavior: smooth;
}

/* Image zoom transform */
img {
    transform: scale(v-bind(zoomLevel));
    transform-origin: center center;
    transition: transform 0.3s ease;
}
</style>
