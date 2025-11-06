<template>
    <transition
        enter-active-class="ease-out duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="show" class="fixed inset-0 z-[60] overflow-y-auto">
            <!-- Overlay -->
            <div
                class="fixed inset-0 bg-black/80 transition-opacity duration-300"
                @click="close"
            />

            <!-- Modal Container -->
            <div class="flex min-h-full items-center justify-center p-4">
                <div
                    class="relative w-full max-w-4xl transform transition-all duration-300"
                >
                    <!-- Close Button -->
                    <button
                        @click="close"
                        class="absolute -top-12 right-0 z-10 p-2 text-white hover:text-gray-300 transition-colors duration-200"
                    >
                        <v-icon name="bi-x" class="w-6 h-6" />
                    </button>

                    <!-- Video Player -->
                    <div class="bg-black rounded-2xl overflow-hidden">
                        <video
                            :src="video"
                            controls
                            autoplay
                            class="w-full h-auto max-h-[80vh]"
                            @click.stop
                        >
                            Your browser does not support the video tag.
                        </video>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup>
const props = defineProps({
    show: {
        type: Boolean,
        required: true,
    },
    video: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(["close"]);

const close = () => {
    emit("close");
};

// Keyboard navigation
const handleKeydown = (e) => {
    if (!props.show) return;

    if (e.key === "Escape") {
        close();
    }
};

// Add event listener when component mounts
import { onMounted, onUnmounted } from "vue";

onMounted(() => {
    document.addEventListener("keydown", handleKeydown);
});

onUnmounted(() => {
    document.removeEventListener("keydown", handleKeydown);
});
</script>
