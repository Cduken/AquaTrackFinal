<template>
    <div v-if="form.photo_previews.length > 0" class="space-y-4">
        <div class="flex items-center justify-between">
            <h4 class="text-sm font-semibold text-gray-700">
                Captured Media ({{ form.photo_previews.length }})
            </h4>
            <button
                type="button"
                @click="$emit('clear-all-media')"
                class="text-sm text-red-600 hover:text-red-800 font-medium"
            >
                Clear All
            </button>
        </div>

        <div
            class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3"
        >
            <div
                v-for="(preview, index) in form.photo_previews"
                :key="'media-' + index"
                class="relative group aspect-square"
            >
                <div
                    v-if="
                        form.photos[index].type.startsWith('image')
                    "
                >
                    <img
                        :src="preview"
                        class="w-full h-full object-cover rounded-lg border-2 border-gray-200 group-hover:border-blue-400 transition-all duration-200"
                        :alt="'Photo ' + (index + 1)"
                    />
                    <div
                        class="absolute top-2 left-2 bg-blue-500 text-white text-xs px-2 py-1 rounded-full flex items-center"
                    >
                        <v-icon
                            name="hi-photograph"
                            class="w-3 h-3 mr-1"
                        />
                        {{
                            form.photos
                                .filter((file) =>
                                    file.type.startsWith("image")
                                )
                                .indexOf(form.photos[index]) + 1
                        }}
                    </div>
                </div>
                <div v-else>
                    <video
                        :src="preview"
                        class="w-full h-full object-cover rounded-lg border-2 border-gray-200 group-hover:border-green-400 transition-all duration-200"
                        muted
                        preload="metadata"
                    ></video>
                    <div
                        class="absolute top-2 left-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full flex items-center"
                    >
                        <v-icon
                            name="hi-video-camera"
                            class="w-3 h-3 mr-1"
                        />
                        {{
                            form.photos
                                .filter((file) =>
                                    file.type.startsWith("video")
                                )
                                .indexOf(form.photos[index]) + 1
                        }}
                    </div>
                </div>
                <button
                    @click="$emit('remove-media', index)"
                    type="button"
                    class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-all duration-200"
                >
                    <v-icon name="hi-trash" class="w-3 h-3" />
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    form: Object
});

defineEmits(['remove-media', 'clear-all-media']);
</script>
