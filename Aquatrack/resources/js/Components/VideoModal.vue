<template>
    <transition
        enter-active-class="ease-out duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="show" class="fixed inset-0 z-[2000] flex items-center justify-center bg-black/90">
            <div class="relative w-full max-w-4xl mx-4">
                <button
                    @click="$emit('close')"
                    class="absolute -top-10 right-0 text-white hover:text-gray-300 transition-colors p-1 rounded-full hover:bg-white/10"
                >
                    <v-icon name="bi-x-lg" class="w-6 h-6" />
                </button>
                <div class="aspect-video w-full bg-black rounded-lg overflow-hidden">
                    <video v-if="video" controls autoplay class="w-full h-full">
                        <source :src="video" type="video/mp4" />
                    </video>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup>
defineProps({
    show: Boolean,
    video: String
});
defineEmits(['close']);
</script>
