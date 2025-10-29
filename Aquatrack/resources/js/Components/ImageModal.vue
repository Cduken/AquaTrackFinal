<template>
    <transition
        enter-active-class="ease-out duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="show" class="fixed inset-0 z-[2000] flex items-center justify-center bg-black/90" @click="$emit('close')">
            <button @click.stop="$emit('close')" class="absolute top-4 right-4 text-white hover:text-gray-300 transition-colors p-2 rounded-full hover:bg-white/10 z-10">
                <v-icon name="bi-x-lg" class="w-6 h-6" />
            </button>

            <button v-if="images.length > 1" @click.stop="$emit('prev')" class="absolute left-4 top-1/2 transform -translate-y-1/2 text-white hover:text-gray-300 transition-colors p-3 rounded-full hover:bg-white/10 z-10">
                <v-icon name="bi-chevron-left" class="w-6 h-6" />
            </button>

            <button v-if="images.length > 1" @click.stop="$emit('next')" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-white hover:text-gray-300 transition-colors p-3 rounded-full hover:bg-white/10 z-10">
                <v-icon name="bi-chevron-right" class="w-6 h-6" />
            </button>

            <div v-if="images.length > 1" class="absolute top-4 left-1/2 transform -translate-x-1/2 text-white bg-black/50 px-3 py-1 rounded-full text-sm z-10">
                {{ currentIndex + 1 }} / {{ images.length }}
            </div>

            <div class="relative w-full max-w-6xl max-h-[90vh] mx-4" @click.stop>
                <img :src="images[currentIndex]" class="w-full h-full object-contain max-h-[80vh] rounded-lg" />
            </div>
        </div>
    </transition>
</template>

<script setup>
defineProps({
    show: Boolean,
    images: Array,
    currentIndex: Number
});
defineEmits(['close', 'next', 'prev']);
</script>
