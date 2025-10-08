<!-- Enhanced LocationStatus.vue -->
<template>
    <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <v-icon
                    name="hi-location-marker"
                    class="w-5 h-5 mr-2"
                    :class="{
                        'text-green-500': locationStatus === 'success',
                        'text-yellow-500': locationStatus === 'loading',
                        'text-red-500': locationStatus === 'error',
                        'text-blue-500':
                            locationStatus === 'offline_cached_location',
                        'text-orange-500':
                            locationStatus === 'offline_default_location',
                    }"
                />
                <span class="text-sm font-medium text-gray-700">
                    GPS Location
                </span>
            </div>
            <div class="text-xs text-gray-500">
                <span v-if="locationStatus === 'success'">Live</span>
                <span v-else-if="locationStatus === 'loading'"
                    >Acquiring...</span
                >
                <span v-else-if="locationStatus === 'error'">Failed</span>
                <span v-else-if="locationStatus === 'offline_cached_location'"
                    >Cached</span
                >
                <span v-else-if="locationStatus === 'offline_default_location'"
                    >Default</span
                >
                <span v-else>Pending</span>
            </div>
        </div>

        <!-- Success - Live GPS -->
        <div
            v-if="locationStatus === 'success'"
            class="mt-2 text-xs text-gray-600"
        >
            <div>Lat: {{ form.latitude?.toFixed(6) }}</div>
            <div>Lng: {{ form.longitude?.toFixed(6) }}</div>
            <div class="text-green-600 font-medium">✓ Live GPS Acquired</div>
        </div>

        <!-- Offline - Cached GPS -->
        <div
            v-if="locationStatus === 'offline_cached_location'"
            class="mt-2 text-xs"
        >
            <div>Lat: {{ form.latitude?.toFixed(6) }}</div>
            <div>Lng: {{ form.longitude?.toFixed(6) }}</div>
            <div class="text-blue-600 font-medium">✓ Using Cached GPS</div>
        </div>

        <!-- Offline - Default Location -->
        <div
            v-if="locationStatus === 'offline_default_location'"
            class="mt-2 text-xs"
        >
            <div>Lat: {{ form.latitude?.toFixed(4) }}</div>
            <div>Lng: {{ form.longitude?.toFixed(4) }}</div>
            <div class="text-orange-600">⚠ Using Default Location</div>
        </div>

        <!-- Loading -->
        <div
            v-if="locationStatus === 'loading'"
            class="mt-2 text-xs text-yellow-600"
        >
            Acquiring GPS coordinates...
        </div>

        <!-- Error -->
        <div v-if="locationStatus === 'error'" class="mt-2">
            <div class="text-xs text-red-600 mb-2">
                Failed to get GPS location
            </div>
            <button
                @click="$emit('get-location')"
                class="text-xs bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition-colors"
            >
                Retry Location
            </button>
        </div>
    </div>
</template>

<script setup>
defineProps({
    locationStatus: String,
    form: Object,
});

defineEmits(["get-location"]);
</script>
