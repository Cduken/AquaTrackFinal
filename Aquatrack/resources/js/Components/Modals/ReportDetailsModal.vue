//Components/ReportDetailsModal.vue
<template>
    <!-- Single transition wrapper for both overlay and panel -->
    <transition name="modal">
        <div v-if="show" class="fixed inset-0 z-[1000] overflow-y-auto">
            <!-- Overlay -->
            <div
                class="fixed inset-0 bg-black/50 transition-opacity duration-300"
                @click="emit('close')"
            ></div>

            <!-- Centered modal container - becomes fullscreen when maximized -->
            <div
                class="flex min-h-full items-center justify-center p-4"
                :class="{ 'p-0 items-stretch': isMaximized }"
            >
                <!-- Modal panel -->
                <div
                    class="relative w-full max-w-4xl transform transition-all duration-300 scale-100"
                    :class="{
                        'max-w-none w-full h-full max-h-none rounded-none':
                            isMaximized,
                        'max-h-[90vh]': !isMaximized,
                    }"
                >
                    <div
                        class="bg-white rounded-lg shadow-xl overflow-hidden flex flex-col"
                        :class="{
                            'rounded-none h-full': isMaximized,
                            'max-h-[90vh]': !isMaximized,
                        }"
                    >
                        <!-- Header -->
                        <div
                            class="flex items-center justify-between py-4 border-b flex-shrink-0"
                        >
                            <div class="px-6">
                                <div>
                                    <h2
                                        class="text-black font-semibold text-lg"
                                        :class="{ 'text-xl': isMaximized }"
                                    >
                                        Report Details
                                    </h2>
                                </div>
                            </div>

                            <div class="flex items-center space-x-2 px-6">
                                <!-- Maximize/Minimize Button -->
                                <button
                                    @click="toggleMaximize"
                                    class="text-black transition-colors duration-200 p-2 rounded-lg hover:text-gray-600 hover:bg-gray-100"
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
                                        class="h-5 w-5"
                                    />
                                </button>
                                <!-- Close Button -->
                                <button
                                    @click="emit('close')"
                                    class="text-black transition-colors duration-200 p-2 rounded-lg hover:text-gray-600 hover:bg-gray-100"
                                >
                                    <v-icon name="bi-x-lg" class="h-5 w-5" />
                                </button>
                            </div>
                        </div>

                        <!-- Content -->
                        <div
                            class="flex-1 overflow-y-auto p-6"
                            :class="{
                                'p-8': isMaximized,
                                'max-h-[calc(90vh-120px)]': !isMaximized,
                            }"
                        >
                            <div
                                v-if="report"
                                class="space-y-6"
                                :class="{ 'space-y-8': isMaximized }"
                            >
                                <!-- Status and Priority Badges -->
                                <div
                                    class="flex flex-wrap gap-3"
                                    :class="{ 'gap-4': isMaximized }"
                                >
                                    <div
                                        class="flex items-center bg-gray-50 px-3 py-2 rounded-lg border"
                                        :class="{ 'px-4 py-3': isMaximized }"
                                    >
                                        <v-icon
                                            name="bi-circle-fill"
                                            class="mr-2 text-blue-500"
                                            :class="{ 'h-5 w-5': isMaximized }"
                                        />
                                        <span
                                            class="text-sm font-medium text-gray-700"
                                            :class="{
                                                'text-base': isMaximized,
                                            }"
                                            >ID: {{ report.id }}</span
                                        >
                                    </div>
                                    <span
                                        class="px-3 py-2 text-sm font-semibold rounded-lg"
                                        :class="[
                                            statusClass,
                                            {
                                                'px-4 py-3 text-base':
                                                    isMaximized,
                                            },
                                        ]"
                                    >
                                        {{ statusLabel }}
                                    </span>
                                    <span
                                        class="px-3 py-2 text-sm font-semibold rounded-lg"
                                        :class="[
                                            priorityClass,
                                            {
                                                'px-4 py-3 text-base':
                                                    isMaximized,
                                            },
                                        ]"
                                    >
                                        {{ report.priority || "N/A" }} Priority
                                    </span>
                                    <div
                                        class="flex items-center bg-gray-50 px-3 py-2 rounded-lg border"
                                        :class="{ 'px-4 py-3': isMaximized }"
                                    >
                                        <v-icon
                                            name="bi-calendar"
                                            class="mr-2 text-gray-500"
                                            :class="{ 'h-5 w-5': isMaximized }"
                                        />
                                        <span
                                            class="text-sm text-gray-700"
                                            :class="{
                                                'text-base': isMaximized,
                                            }"
                                        >
                                            {{
                                                report.created_at
                                                    ? new Date(
                                                          report.created_at
                                                      ).toLocaleDateString()
                                                    : "N/A"
                                            }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Main Grid Layout -->
                                <div
                                    class="grid grid-cols-1 lg:grid-cols-2 gap-6"
                                    :class="{ 'gap-8': isMaximized }"
                                >
                                    <!-- Left Column -->
                                    <div
                                        class="space-y-6"
                                        :class="{ 'space-y-8': isMaximized }"
                                    >
                                        <!-- Location Information -->
                                        <div
                                            class="bg-white border border-gray-200 rounded-lg shadow-sm"
                                            :class="{
                                                'rounded-xl': isMaximized,
                                            }"
                                        >
                                            <div
                                                class="bg-gray-50 px-4 py-3 border-b border-gray-200"
                                                :class="{
                                                    'px-6 py-4': isMaximized,
                                                }"
                                            >
                                                <h3
                                                    class="text-md font-semibold text-gray-900 flex items-center"
                                                    :class="{
                                                        'text-lg': isMaximized,
                                                    }"
                                                >
                                                    <v-icon
                                                        name="bi-geo-alt"
                                                        class="mr-2 text-blue-600"
                                                        :class="{
                                                            'h-5 w-5':
                                                                isMaximized,
                                                        }"
                                                    />
                                                    Location Information
                                                </h3>
                                            </div>
                                            <div
                                                class="p-4 space-y-3"
                                                :class="{
                                                    'p-6 space-y-4':
                                                        isMaximized,
                                                }"
                                            >
                                                <div
                                                    class="grid grid-cols-2 gap-4"
                                                    :class="{
                                                        'gap-6': isMaximized,
                                                    }"
                                                >
                                                    <div>
                                                        <label
                                                            class="text-xs text-gray-500 font-medium"
                                                            :class="{
                                                                'text-sm':
                                                                    isMaximized,
                                                            }"
                                                            >Municipality</label
                                                        >
                                                        <p
                                                            class="text-sm font-medium text-gray-900 mt-1"
                                                            :class="{
                                                                'text-base mt-2':
                                                                    isMaximized,
                                                            }"
                                                        >
                                                            {{
                                                                report.municipality ||
                                                                "N/A"
                                                            }}
                                                        </p>
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="text-xs text-gray-500 font-medium"
                                                            :class="{
                                                                'text-sm':
                                                                    isMaximized,
                                                            }"
                                                            >Province</label
                                                        >
                                                        <p
                                                            class="text-sm font-medium text-gray-900 mt-1"
                                                            :class="{
                                                                'text-base mt-2':
                                                                    isMaximized,
                                                            }"
                                                        >
                                                            {{
                                                                report.province ||
                                                                "N/A"
                                                            }}
                                                        </p>
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="text-xs text-gray-500 font-medium"
                                                            :class="{
                                                                'text-sm':
                                                                    isMaximized,
                                                            }"
                                                            >Barangay</label
                                                        >
                                                        <p
                                                            class="text-sm font-medium text-gray-900 mt-1"
                                                            :class="{
                                                                'text-base mt-2':
                                                                    isMaximized,
                                                            }"
                                                        >
                                                            {{
                                                                report.barangay ||
                                                                "N/A"
                                                            }}
                                                        </p>
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="text-xs text-gray-500 font-medium"
                                                            :class="{
                                                                'text-sm':
                                                                    isMaximized,
                                                            }"
                                                            >Purok/Street</label
                                                        >
                                                        <p
                                                            class="text-sm font-medium text-gray-900 mt-1"
                                                            :class="{
                                                                'text-base mt-2':
                                                                    isMaximized,
                                                            }"
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
                                                    class="bg-blue-50 p-3 rounded-lg border border-blue-200 mt-3"
                                                    :class="{
                                                        'p-4 mt-4': isMaximized,
                                                    }"
                                                >
                                                    <div
                                                        class="flex items-center justify-between"
                                                    >
                                                        <div>
                                                            <label
                                                                class="text-xs text-blue-700 font-medium"
                                                                :class="{
                                                                    'text-sm':
                                                                        isMaximized,
                                                                }"
                                                                >GPS
                                                                Coordinates</label
                                                            >
                                                            <div
                                                                class="flex gap-3 mt-1"
                                                                :class="{
                                                                    'gap-4 mt-2':
                                                                        isMaximized,
                                                                }"
                                                            >
                                                                <span
                                                                    class="text-sm font-medium text-blue-900"
                                                                    :class="{
                                                                        'text-base':
                                                                            isMaximized,
                                                                    }"
                                                                >
                                                                    Lat:
                                                                    {{
                                                                        getLatitude
                                                                    }}
                                                                </span>
                                                                <span
                                                                    class="text-sm font-medium text-blue-900"
                                                                    :class="{
                                                                        'text-base':
                                                                            isMaximized,
                                                                    }"
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
                                                            class="text-blue-600 text-lg"
                                                            :class="{
                                                                'text-xl':
                                                                    isMaximized,
                                                            }"
                                                        />
                                                    </div>
                                                </div>

                                                <!-- Map Section -->
                                                <div
                                                    class="mt-4"
                                                    :class="{
                                                        'mt-5': isMaximized,
                                                    }"
                                                >
                                                    <div
                                                        v-if="
                                                            report.latitude &&
                                                            report.longitude
                                                        "
                                                        ref="mapContainer"
                                                        class="w-full rounded-lg border border-gray-300 shadow-sm relative"
                                                        :class="{
                                                            'h-80 rounded-xl':
                                                                isMaximized,
                                                            'h-48': !isMaximized,
                                                        }"
                                                    >
                                                        <!-- Loading indicator -->
                                                        <div
                                                            v-if="!mapLoaded"
                                                            class="absolute inset-0 flex items-center justify-center bg-gray-100 bg-opacity-80 z-10"
                                                        >
                                                            <div
                                                                class="text-center"
                                                            >
                                                                <v-icon
                                                                    name="bi-compass"
                                                                    class="text-blue-500 text-2xl mb-2 animate-spin"
                                                                    :class="{
                                                                        'text-3xl':
                                                                            isMaximized,
                                                                    }"
                                                                />
                                                                <p
                                                                    class="text-sm text-gray-600"
                                                                    :class="{
                                                                        'text-base':
                                                                            isMaximized,
                                                                    }"
                                                                >
                                                                    Loading
                                                                    map...
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div
                                                        v-else
                                                        class="w-full rounded-lg border border-gray-300 bg-gray-50 flex items-center justify-center"
                                                        :class="{
                                                            'h-80 rounded-xl':
                                                                isMaximized,
                                                            'h-48': !isMaximized,
                                                        }"
                                                    >
                                                        <div
                                                            class="text-center text-gray-500"
                                                        >
                                                            <v-icon
                                                                name="bi-geo-alt"
                                                                class="text-3xl mb-2"
                                                                :class="{
                                                                    'text-4xl':
                                                                        isMaximized,
                                                                }"
                                                            />
                                                            <p
                                                                class="text-sm"
                                                                :class="{
                                                                    'text-base':
                                                                        isMaximized,
                                                                }"
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
                                            class="bg-white border border-gray-200 rounded-lg shadow-sm"
                                            :class="{
                                                'rounded-xl': isMaximized,
                                            }"
                                        >
                                            <div
                                                class="bg-gray-50 px-4 py-3 border-b border-gray-200"
                                                :class="{
                                                    'px-6 py-4': isMaximized,
                                                }"
                                            >
                                                <h3
                                                    class="text-md font-semibold text-gray-900 flex items-center"
                                                    :class="{
                                                        'text-lg': isMaximized,
                                                    }"
                                                >
                                                    <v-icon
                                                        name="bi-person"
                                                        class="mr-2 text-blue-600"
                                                        :class="{
                                                            'h-5 w-5':
                                                                isMaximized,
                                                        }"
                                                    />
                                                    Reporter Information
                                                </h3>
                                            </div>
                                            <div
                                                class="p-4 space-y-3"
                                                :class="{
                                                    'p-6 space-y-4':
                                                        isMaximized,
                                                }"
                                            >
                                                <div
                                                    class="grid grid-cols-1 gap-3"
                                                    :class="{
                                                        'gap-4': isMaximized,
                                                    }"
                                                >
                                                    <div>
                                                        <label
                                                            class="text-xs text-gray-500 font-medium"
                                                            :class="{
                                                                'text-sm':
                                                                    isMaximized,
                                                            }"
                                                            >Reporter
                                                            Name</label
                                                        >
                                                        <p
                                                            class="text-sm font-medium text-gray-900 mt-1"
                                                            :class="{
                                                                'text-base mt-2':
                                                                    isMaximized,
                                                            }"
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
                                                            class="text-xs text-gray-500 font-medium"
                                                            :class="{
                                                                'text-sm':
                                                                    isMaximized,
                                                            }"
                                                            >Phone Number</label
                                                        >
                                                        <p
                                                            class="text-sm font-medium text-gray-900 mt-1"
                                                            :class="{
                                                                'text-base mt-2':
                                                                    isMaximized,
                                                            }"
                                                        >
                                                            {{
                                                                report.reporter_phone
                                                            }}
                                                        </p>
                                                    </div>
                                                    <div v-if="report.user">
                                                        <label
                                                            class="text-xs text-gray-500 font-medium"
                                                            :class="{
                                                                'text-sm':
                                                                    isMaximized,
                                                            }"
                                                            >Registered
                                                            User</label
                                                        >
                                                        <p
                                                            class="text-sm font-medium text-gray-900 mt-1"
                                                            :class="{
                                                                'text-base mt-2':
                                                                    isMaximized,
                                                            }"
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
                                    <div
                                        class="space-y-6"
                                        :class="{ 'space-y-8': isMaximized }"
                                    >
                                        <!-- Issue Information -->
                                        <div
                                            class="bg-white border border-gray-200 rounded-lg shadow-sm"
                                            :class="{
                                                'rounded-xl': isMaximized,
                                            }"
                                        >
                                            <div
                                                class="bg-gray-50 px-4 py-3 border-b border-gray-200"
                                                :class="{
                                                    'px-6 py-4': isMaximized,
                                                }"
                                            >
                                                <h3
                                                    class="text-md font-semibold text-gray-900 flex items-center"
                                                    :class="{
                                                        'text-lg': isMaximized,
                                                    }"
                                                >
                                                    <v-icon
                                                        name="bi-droplet"
                                                        class="mr-2 text-blue-600"
                                                        :class="{
                                                            'h-5 w-5':
                                                                isMaximized,
                                                        }"
                                                    />
                                                    Issue Details
                                                </h3>
                                            </div>
                                            <div
                                                class="p-4 space-y-3"
                                                :class="{
                                                    'p-6 space-y-4':
                                                        isMaximized,
                                                }"
                                            >
                                                <div>
                                                    <label
                                                        class="text-xs text-gray-500 font-medium"
                                                        :class="{
                                                            'text-sm':
                                                                isMaximized,
                                                        }"
                                                        >Issue Type</label
                                                    >
                                                    <p
                                                        class="text-sm font-medium text-gray-900 mt-1"
                                                        :class="{
                                                            'text-base mt-2':
                                                                isMaximized,
                                                        }"
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
                                                        class="text-xs text-gray-500 font-medium"
                                                        :class="{
                                                            'text-sm':
                                                                isMaximized,
                                                        }"
                                                        >Description</label
                                                    >
                                                    <div
                                                        class="bg-gray-50 p-3 rounded-lg border border-gray-200 mt-1"
                                                        :class="{
                                                            'p-4 mt-2':
                                                                isMaximized,
                                                        }"
                                                    >
                                                        <p
                                                            class="text-sm text-gray-700 whitespace-pre-line"
                                                            :class="{
                                                                'text-base':
                                                                    isMaximized,
                                                            }"
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
                                            class="bg-white border border-gray-200 rounded-lg shadow-sm"
                                            :class="{
                                                'rounded-xl': isMaximized,
                                            }"
                                        >
                                            <div
                                                class="bg-gray-50 px-4 py-3 border-b border-gray-200"
                                                :class="{
                                                    'px-6 py-4': isMaximized,
                                                }"
                                            >
                                                <h3
                                                    class="text-md font-semibold text-gray-900 flex items-center"
                                                    :class="{
                                                        'text-lg': isMaximized,
                                                    }"
                                                >
                                                    <v-icon
                                                        name="bi-images"
                                                        class="mr-2 text-blue-600"
                                                        :class="{
                                                            'h-5 w-5':
                                                                isMaximized,
                                                        }"
                                                    />
                                                    Media ({{
                                                        report.photos.length
                                                    }})
                                                </h3>
                                            </div>
                                            <div
                                                class="p-4"
                                                :class="{ 'p-6': isMaximized }"
                                            >
                                                <div
                                                    class="grid grid-cols-2 sm:grid-cols-3 gap-3"
                                                    :class="{
                                                        'gap-4': isMaximized,
                                                    }"
                                                >
                                                    <div
                                                        v-for="(
                                                            media, index
                                                        ) in report.photos"
                                                        :key="index"
                                                        class="relative group overflow-hidden rounded-lg border border-gray-200 shadow-sm transition-all duration-300 hover:shadow-md hover:border-blue-300 cursor-pointer"
                                                        :class="{
                                                            'h-40 rounded-xl':
                                                                isMaximized,
                                                            'h-32': !isMaximized,
                                                        }"
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
                                                                        class="text-white text-4xl"
                                                                        :class="{
                                                                            'text-5xl':
                                                                                isMaximized,
                                                                        }"
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
                                                                    class="text-white bg-black/50 p-1.5 rounded-full"
                                                                    :class="{
                                                                        'p-2': isMaximized,
                                                                    }"
                                                                />
                                                            </div>
                                                        </template>

                                                        <div
                                                            class="absolute bottom-1 left-1 bg-black bg-opacity-50 text-white text-xs px-1 rounded"
                                                            :class="{
                                                                'text-sm bottom-2 left-2':
                                                                    isMaximized,
                                                            }"
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
                                            class="bg-white border border-gray-200 rounded-lg shadow-sm"
                                            :class="{
                                                'rounded-xl': isMaximized,
                                            }"
                                        >
                                            <div
                                                class="bg-gray-50 px-4 py-3 border-b border-gray-200"
                                                :class="{
                                                    'px-6 py-4': isMaximized,
                                                }"
                                            >
                                                <h3
                                                    class="text-md font-semibold text-gray-900 flex items-center"
                                                    :class="{
                                                        'text-lg': isMaximized,
                                                    }"
                                                >
                                                    <v-icon
                                                        name="bi-info-circle"
                                                        class="mr-2 text-blue-600"
                                                        :class="{
                                                            'h-5 w-5':
                                                                isMaximized,
                                                        }"
                                                    />
                                                    Additional Information
                                                </h3>
                                            </div>
                                            <div
                                                class="p-4 space-y-3"
                                                :class="{
                                                    'p-6 space-y-4':
                                                        isMaximized,
                                                }"
                                            >
                                                <div
                                                    class="grid grid-cols-2 gap-4"
                                                    :class="{
                                                        'gap-6': isMaximized,
                                                    }"
                                                >
                                                    <div>
                                                        <label
                                                            class="text-xs text-gray-500 font-medium"
                                                            :class="{
                                                                'text-sm':
                                                                    isMaximized,
                                                            }"
                                                            >Submitted
                                                            Date</label
                                                        >
                                                        <p
                                                            class="text-sm font-medium text-gray-900 mt-1"
                                                            :class="{
                                                                'text-base mt-2':
                                                                    isMaximized,
                                                            }"
                                                        >
                                                            {{
                                                                report.created_at
                                                                    ? new Date(
                                                                          report.created_at
                                                                      ).toLocaleString()
                                                                    : "N/A"
                                                            }}
                                                        </p>
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="text-xs text-gray-500 font-medium"
                                                            :class="{
                                                                'text-sm':
                                                                    isMaximized,
                                                            }"
                                                            >User Type</label
                                                        >
                                                        <p
                                                            class="text-sm font-medium text-gray-900 mt-1"
                                                            :class="{
                                                                'text-base mt-2':
                                                                    isMaximized,
                                                            }"
                                                        >
                                                            {{
                                                                report.formatted_user_types ||
                                                                "Guest"
                                                            }}
                                                        </p>
                                                    </div>

                                                    <div>
                                                        <label
                                                            class="text-xs text-gray-500 font-medium"
                                                            :class="{
                                                                'text-sm':
                                                                    isMaximized,
                                                            }"
                                                            >Tracking
                                                            Code</label
                                                        >
                                                        <p
                                                            class="text-sm font-bold text-blue-900 mt-1"
                                                            :class="{
                                                                'text-base mt-2':
                                                                    isMaximized,
                                                            }"
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
                                    class="text-4xl mb-2"
                                    :class="{ 'text-5xl': isMaximized }"
                                />
                                <p
                                    class="text-sm"
                                    :class="{ 'text-base': isMaximized }"
                                >
                                    No report data available.
                                </p>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div
                            class="border-t border-gray-200 px-6 py-4 bg-gray-50 flex justify-end flex-shrink-0"
                            :class="{ 'px-8 py-2': isMaximized }"
                        >
                            <button
                                @click="emit('close')"
                                type="button"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                                :class="{ 'px-5 py-2 text-base': isMaximized }"
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

    <!-- Video Modal -->
    <transition name="modal">
        <div
            v-if="showVideoModal"
            class="fixed inset-0 z-[2000] flex items-center justify-center bg-black/90"
        >
            <div class="relative w-full max-w-4xl mx-4">
                <button
                    @click="closeVideoModal"
                    class="absolute -top-10 right-0 text-white hover:text-gray-300 transition-colors duration-200 p-1 rounded-full hover:bg-white/10"
                >
                    <v-icon name="bi-x-lg" scale="1.5" />
                </button>

                <div
                    class="aspect-video w-full bg-black rounded-lg overflow-hidden"
                >
                    <video
                        v-if="currentVideo"
                        controls
                        autoplay
                        class="w-full h-full"
                    >
                        <source :src="currentVideo" type="video/mp4" />
                        Your browser does not support the video tag.
                    </video>
                </div>
            </div>
        </div>
    </transition>

    <!-- Image Modal -->
    <transition name="modal">
        <div
            v-if="showImageModal"
            class="fixed inset-0 z-[2000] flex items-center justify-center bg-black/90"
            @click="closeImageModal"
        >
            <!-- Close button -->
            <button
                @click.stop="closeImageModal"
                class="absolute top-4 right-4 text-white hover:text-gray-300 transition-colors duration-200 p-2 rounded-full hover:bg-white/10 z-10"
            >
                <v-icon name="bi-x-lg" scale="1.5" />
            </button>

            <!-- Navigation buttons -->
            <button
                v-if="allImages.length > 1"
                @click.stop="prevImage"
                class="absolute left-4 top-1/2 transform -translate-y-1/2 text-white hover:text-gray-300 transition-colors duration-200 p-3 rounded-full hover:bg-white/10 z-10"
            >
                <v-icon name="bi-chevron-left" scale="1.5" />
            </button>

            <button
                v-if="allImages.length > 1"
                @click.stop="nextImage"
                class="absolute right-4 top-1/2 transform -translate-y-1/2 text-white hover:text-gray-300 transition-colors duration-200 p-3 rounded-full hover:bg-white/10 z-10"
            >
                <v-icon name="bi-chevron-right" scale="1.5" />
            </button>

            <!-- Image counter -->
            <div
                v-if="allImages.length > 1"
                class="absolute top-4 left-1/2 transform -translate-x-1/2 text-white bg-black/50 px-3 py-1 rounded-full text-sm z-10"
            >
                {{ currentImageIndex + 1 }} / {{ allImages.length }}
            </div>

            <!-- Image container -->
            <div
                class="relative w-full max-w-6xl max-h-[90vh] mx-4"
                @click.stop
            >
                <img
                    :src="currentImage"
                    :alt="`Report photo ${currentImageIndex + 1}`"
                    class="w-full h-full object-contain max-h-[80vh] rounded-lg"
                    @load="imageLoaded = true"
                />

                <!-- Loading indicator -->
                <div
                    v-if="!imageLoaded"
                    class="absolute inset-0 flex items-center justify-center"
                >
                    <v-icon
                        name="bi-arrow-clockwise"
                        class="text-white text-2xl animate-spin"
                    />
                </div>
            </div>
        </div>
    </transition>
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
const showVideoModal = ref(false);
const currentVideo = ref(null);
const map = ref(null);
const mapContainer = ref(null);
const heading = ref(0);
const mapLoaded = ref(false);
const imageLoaded = ref(false);
const showImageModal = ref(false);
const currentImage = ref(null);
const currentImageIndex = ref(0);
const allImages = ref([]);
const isMaximized = ref(false);

const toggleMaximize = async () => {
    isMaximized.value = !isMaximized.value;

    // Wait for the DOM to update
    await nextTick();

    // Re-initialize map when toggling maximize to ensure proper sizing
    if (map.value) {
        // Use a longer delay to ensure the container has fully resized
        setTimeout(() => {
            if (map.value) {
                try {
                    map.value.invalidateSize();
                    // Re-center the map to ensure it's properly displayed
                    if (
                        props.report &&
                        props.report.latitude &&
                        props.report.longitude
                    ) {
                        const lat = Number(props.report.latitude);
                        const lon = Number(props.report.longitude);
                        if (!isNaN(lat) && !isNaN(lon)) {
                            map.value.setView([lat, lon], map.value.getZoom());
                        }
                    }
                } catch (error) {
                    console.error("Error resizing map:", error);
                    // If invalidateSize fails, reinitialize the map completely
                    reinitializeMap();
                }
            } else {
                // If map doesn't exist, reinitialize it
                reinitializeMap();
            }
        }, 500); // Increased delay for better reliability
    } else {
        // If map doesn't exist, initialize it
        reinitializeMap();
    }
};

// Reinitialize map completely
const reinitializeMap = () => {
    destroyMap();
    setTimeout(() => {
        initializeMap();
    }, 300);
};

// Destroy map completely
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

// Initialize Leaflet map
const initializeMap = () => {
    if (
        props.report &&
        props.report.latitude !== undefined &&
        props.report.latitude !== null &&
        props.report.longitude !== undefined &&
        props.report.longitude !== null &&
        mapContainer.value
    ) {
        const lat = Number(props.report.latitude);
        const lon = Number(props.report.longitude);

        if (
            props.report.heading !== undefined &&
            props.report.heading !== null
        ) {
            heading.value = Number(props.report.heading);
        }

        if (
            !isNaN(lat) &&
            !isNaN(lon) &&
            Math.abs(lat) <= 90 &&
            Math.abs(lon) <= 180
        ) {
            const L = window.L;
            if (L) {
                // Clear any existing map first
                destroyMap();

                setTimeout(() => {
                    if (mapContainer.value && !map.value) {
                        try {
                            map.value = L.map(mapContainer.value).setView(
                                [lat, lon],
                                15
                            );

                            L.tileLayer(
                                "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
                                {
                                    maxZoom: 19,
                                    attribution:
                                        '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                                }
                            ).addTo(map.value);

                            // Create marker
                            const markerIcon = L.icon({
                                iconUrl:
                                    "data:image/svg+xml;base64," +
                                    btoa(`
                                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="40" viewBox="0 0 30 40">
                                        <g transform="rotate(${heading.value} 15 15)">
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

                            L.marker([lat, lon], { icon: markerIcon }).addTo(
                                map.value
                            );

                            // Force a resize after a short delay to ensure the map renders correctly
                            setTimeout(() => {
                                if (map.value) {
                                    map.value.invalidateSize();
                                    mapLoaded.value = true;
                                }
                            }, 400);
                        } catch (error) {
                            console.error("Error initializing map:", error);
                            mapLoaded.value = true;
                        }
                    }
                }, 200);
            }
        }
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
    currentImage.value = allImages.value[currentImageIndex.value];
    imageLoaded.value = false;
    showImageModal.value = true;
};

const closeImageModal = () => {
    showImageModal.value = false;
    currentImage.value = null;
    currentImageIndex.value = 0;
    allImages.value = [];
};

const nextImage = () => {
    if (allImages.value.length > 0) {
        currentImageIndex.value =
            (currentImageIndex.value + 1) % allImages.value.length;
        currentImage.value = allImages.value[currentImageIndex.value];
    }
};

const prevImage = () => {
    if (allImages.value.length > 0) {
        currentImageIndex.value =
            (currentImageIndex.value - 1 + allImages.value.length) %
            allImages.value.length;
        currentImage.value = allImages.value[currentImageIndex.value];
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
    return (
        file.type === "video" ||
        (file.mime_type && file.mime_type.includes("video"))
    );
};

const statusClass = computed(() => {
    if (!props.report || !props.report.status)
        return "bg-gray-100 text-gray-800";
    switch (props.report.status.toLowerCase()) {
        case "in_progress":
            return "bg-blue-100 text-blue-800 border border-blue-200";
        case "resolved":
            return "bg-green-100 text-green-800 border border-green-200";
        case "pending":
            return "bg-yellow-100 text-yellow-800 border border-yellow-200";
        default:
            return "bg-gray-100 text-gray-800 border border-gray-200";
    }
});

const priorityClass = computed(() => {
    if (!props.report || !props.report.priority)
        return "bg-gray-100 text-gray-800 border border-gray-200";
    switch (props.report.priority.toLowerCase()) {
        case "high":
            return "bg-red-100 text-red-800 border border-red-200";
        case "medium":
            return "bg-yellow-100 text-yellow-800 border border-yellow-200";
        case "low":
            return "bg-green-100 text-green-800 border border-green-200";
        default:
            return "bg-gray-100 text-gray-800 border border-gray-200";
    }
});

const statusLabel = computed(() => {
    if (!props.report || !props.report.status) return "Unknown";
    const statusMap = {
        pending: "Pending",
        in_progress: "In Progress",
        resolved: "Resolved",
    };
    return statusMap[props.report.status.toLowerCase()] || props.report.status;
});

// Safely get coordinates with fallback
const getLatitude = computed(() => {
    return props.report &&
        props.report.latitude !== undefined &&
        props.report.latitude !== null
        ? Number(props.report.latitude).toFixed(6)
        : "N/A";
});

const getLongitude = computed(() => {
    return props.report &&
        props.report.longitude !== undefined &&
        props.report.longitude !== null
        ? Number(props.report.longitude).toFixed(6)
        : "N/A";
});

// Keyboard navigation
const handleKeydown = (event) => {
    if (showImageModal.value) {
        if (event.key === "Escape") {
            closeImageModal();
        } else if (event.key === "ArrowRight") {
            nextImage();
        } else if (event.key === "ArrowLeft") {
            prevImage();
        }
    }
};

// Watch for changes in show prop to initialize map
watch(
    () => props.show,
    (newVal) => {
        if (newVal && props.report) {
            setTimeout(() => {
                initializeMap();
            }, 300);
        } else {
            destroyMap();
            // Reset maximize state when modal closes
            isMaximized.value = false;
        }
    }
);

// Watch for maximize state changes to handle map resizing
watch(
    () => isMaximized.value,
    () => {
        if (props.show) {
            // Use a longer delay to ensure the container has fully resized
            setTimeout(() => {
                reinitializeMap();
            }, 400);
        }
    }
);

onMounted(() => {
    window.addEventListener("keydown", handleKeydown);
    if (props.show && props.report) {
        initializeMap();
    }
});

onUnmounted(() => {
    window.removeEventListener("keydown", handleKeydown);
    destroyMap();
});
</script>

<style scoped>
/* Custom scrollbar for the content */
.overflow-y-auto {
    scrollbar-width: thin;
    scrollbar-color: #e2e8f0 #f8fafc;
}

.overflow-y-auto::-webkit-scrollbar {
    width: 8px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f8fafc;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background-color: #e2e8f0;
    border-radius: 4px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background-color: #cbd5e1;
}

/* Modal transition styles */
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-enter-active .transform,
.modal-leave-active .transform {
    transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.5, 1);
}

.modal-enter-from .transform {
    transform: scale(0.95) translateY(-10px);
}

.modal-leave-to .transform {
    transform: scale(0.95) translateY(-10px);
}
</style>
