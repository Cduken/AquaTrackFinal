<template>
    <transition
        enter-active-class="ease-out duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
            <!-- Overlay -->
            <div
                class="fixed inset-0 bg-black/60 transition-opacity duration-300"
                @click="emit('close')"
            />

            <!-- Modal Container -->
            <div class="flex min-h-full items-center justify-center p-4">
                <div
                    class="relative w-full transform transition-all duration-300"
                    :class="isMaximized ? 'max-w-full h-full' : 'max-w-4xl'"
                >
                    <div
                        class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl overflow-hidden flex flex-col"
                        :class="isMaximized ? 'h-screen' : 'max-h-[90vh]'"
                    >
                        <!-- Header -->
                        <div class="bg-[#062F64] px-6 py-4 flex-shrink-0">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div>
                                        <h2
                                            class="text-xl font-bold text-white"
                                        >
                                            Report Details
                                        </h2>
                                        <p
                                            class="text-blue-100 text-sm mt-1"
                                            v-if="report"
                                        >
                                            {{ formatDate(report.created_at) }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <!-- Maximize Button -->
                                    <button
                                        @click="toggleMaximize"
                                        class="p-2 text-white hover:text-blue-200 hover:bg-white/10 rounded-lg transition-all duration-200"
                                        :title="
                                            isMaximized
                                                ? 'Minimize'
                                                : 'Maximize'
                                        "
                                    >
                                        <component
                                            :is="
                                                isMaximized
                                                    ? Minimize2Icon
                                                    : Maximize2Icon
                                            "
                                            class="w-5 h-5"
                                        />
                                    </button>
                                    <!-- Close Button -->
                                    <button
                                        @click="emit('close')"
                                        class="p-2 text-white hover:text-blue-200 hover:bg-white/10 rounded-lg transition-all duration-200"
                                    >
                                        <component
                                            :is="XIcon"
                                            class="w-5 h-5"
                                        />
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Loading State -->
                        <div
                            v-if="!report"
                            class="flex-1 flex items-center justify-center py-16"
                        >
                            <div class="text-center">
                                <div
                                    class="w-12 h-12 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"
                                ></div>
                                <p
                                    class="text-gray-600 dark:text-gray-400 font-medium"
                                >
                                    Loading report details...
                                </p>
                            </div>
                        </div>

                        <!-- Content -->
                        <div
                            v-else
                            class="flex-1 overflow-y-auto p-6"
                            :class="isMaximized ? 'p-8' : ''"
                        >
                            <div
                                class="space-y-6"
                                :class="isMaximized ? 'max-w-full mx-auto' : ''"
                            >
                                <!-- Status Banner -->
                                <div
                                    class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-6 border border-blue-100 dark:border-blue-800"
                                >
                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <div
                                            class="flex items-center space-x-4"
                                        >
                                            <div
                                                class="flex items-center space-x-3"
                                            >
                                                <!-- <div
                                                    class="w-12 h-12 rounded-full border-2 border-white dark:border-gray-800 shadow-lg overflow-hidden bg-white flex items-center justify-center"
                                                >
                                                    <v-icon
                                                        name="bi-file-earmark-text"
                                                        class="text-blue-600 dark:text-blue-400 w-6 h-6"
                                                    />
                                                </div> -->

                                                    <div>
                                                        <p
                                                            class="text-sm text-gray-500 dark:text-gray-400"
                                                        >
                                                            Report
                                                        </p>
                                                        <p
                                                            v-if="
                                                                $page.props.auth
                                                                    .user &&
                                                                $page.props.auth
                                                                    .user
                                                                    .role !==
                                                                    'customer'
                                                            "
                                                            class="text-lg font-semibold text-gray-900 dark:text-white"
                                                        >
                                                            ID: {{ report.id }}
                                                        </p>
                                                        <p
                                                            class="text-sm text-gray-600 dark:text-gray-400"
                                                        >
                                                            {{
                                                                formatDateTime(
                                                                    report.created_at
                                                                )
                                                            }}
                                                        </p>
                                                    </div>

                                            </div>
                                            <div
                                                class="h-8 w-px bg-gray-300 dark:bg-gray-600"
                                            ></div>
                                            <div
                                                class="flex items-center space-x-3"
                                            >
                                                <div>
                                                    <p
                                                        class="text-sm text-gray-500 dark:text-gray-400"
                                                    >
                                                        Priority
                                                    </p>
                                                    <p
                                                        class="text-lg font-semibold text-gray-900 dark:text-white"
                                                    >
                                                        {{
                                                            formatPriority(
                                                                report.priority
                                                            )
                                                        }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span
                                                class="px-4 py-2.5 text-sm font-semibold rounded-full border shadow-sm"
                                                :class="statusClass"
                                            >
                                                {{ statusLabel }}
                                            </span>
                                            <p
                                                class="text-sm text-gray-500 dark:text-gray-400 mt-4"
                                            >
                                                Tracking:
                                                {{
                                                    report.tracking_code ||
                                                    "N/A"
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Main Grid Layout -->
                                <div
                                    class="grid grid-cols-1 xl:grid-cols-2 gap-6"
                                    :class="isMaximized ? 'gap-8' : ''"
                                >
                                    <!-- Left Column -->
                                    <div
                                        class="space-y-6"
                                        :class="isMaximized ? 'space-y-8' : ''"
                                    >
                                        <!-- Location Information -->
                                        <div
                                            class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm"
                                        >
                                            <div
                                                class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-600"
                                            >
                                                <h3
                                                    class="text-lg font-semibold text-gray-900 dark:text-white flex items-center"
                                                >
                                                    <v-icon
                                                        name="bi-geo-alt"
                                                        class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2"
                                                    />
                                                    Location Information
                                                </h3>
                                            </div>
                                            <div class="p-6 space-y-4">
                                                <div
                                                    class="grid grid-cols-1 md:grid-cols-2 gap-4"
                                                >
                                                    <div
                                                        class="flex items-start space-x-3 p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg"
                                                    >
                                                        <div
                                                            class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg"
                                                        >
                                                            <v-icon
                                                                name="bi-building"
                                                                class="text-blue-600 dark:text-blue-400 w-4 h-4"
                                                            />
                                                        </div>
                                                        <div>
                                                            <p
                                                                class="text-sm text-gray-500 dark:text-gray-400"
                                                            >
                                                                Municipality
                                                            </p>
                                                            <p
                                                                class="font-medium text-gray-900 dark:text-white"
                                                            >
                                                                {{
                                                                    report.municipality ||
                                                                    "N/A"
                                                                }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="flex items-start space-x-3 p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg"
                                                    >
                                                        <div
                                                            class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg"
                                                        >
                                                            <v-icon
                                                                name="bi-map"
                                                                class="text-blue-600 dark:text-blue-400 w-4 h-4"
                                                            />
                                                        </div>
                                                        <div>
                                                            <p
                                                                class="text-sm text-gray-500 dark:text-gray-400"
                                                            >
                                                                Province
                                                            </p>
                                                            <p
                                                                class="font-medium text-gray-900 dark:text-white"
                                                            >
                                                                {{
                                                                    report.province ||
                                                                    "N/A"
                                                                }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="flex items-start space-x-3 p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg"
                                                    >
                                                        <div
                                                            class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg"
                                                        >
                                                            <v-icon
                                                                name="bi-geo"
                                                                class="text-blue-600 dark:text-blue-400 w-4 h-4"
                                                            />
                                                        </div>
                                                        <div>
                                                            <p
                                                                class="text-sm text-gray-500 dark:text-gray-400"
                                                            >
                                                                Barangay
                                                            </p>
                                                            <p
                                                                class="font-medium text-gray-900 dark:text-white"
                                                            >
                                                                {{
                                                                    report.barangay ||
                                                                    "N/A"
                                                                }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="flex items-start space-x-3 p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg"
                                                    >
                                                        <div
                                                            class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg"
                                                        >
                                                            <v-icon
                                                                name="bi-signpost"
                                                                class="text-blue-600 dark:text-blue-400 w-4 h-4"
                                                            />
                                                        </div>
                                                        <div>
                                                            <p
                                                                class="text-sm text-gray-500 dark:text-gray-400"
                                                            >
                                                                Purok/Street
                                                            </p>
                                                            <p
                                                                class="font-medium text-gray-900 dark:text-white"
                                                            >
                                                                {{
                                                                    report.purok ||
                                                                    "N/A"
                                                                }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- GPS Coordinates -->
                                                <div
                                                    class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-800"
                                                >
                                                    <div
                                                        class="flex items-center justify-between"
                                                    >
                                                        <div>
                                                            <label
                                                                class="text-sm text-blue-700 dark:text-blue-300 font-medium"
                                                                >GPS
                                                                Coordinates</label
                                                            >
                                                            <div
                                                                class="flex gap-4 mt-2"
                                                            >
                                                                <span
                                                                    class="text-base font-medium text-blue-900 dark:text-blue-100"
                                                                >
                                                                    Lat:
                                                                    {{
                                                                        getLatitude
                                                                    }}
                                                                </span>
                                                                <span
                                                                    class="text-base font-medium text-blue-900 dark:text-blue-100"
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
                                                            class="text-blue-600 dark:text-blue-400 text-xl"
                                                        />
                                                    </div>
                                                </div>

                                                <!-- Map Section -->
                                                <div class="mt-4">
                                                    <div
                                                        v-if="
                                                            report.latitude &&
                                                            report.longitude
                                                        "
                                                        ref="mapContainer"
                                                        class="w-full rounded-xl border border-gray-300 dark:border-gray-600 shadow-sm relative h-80"
                                                    >
                                                        <div
                                                            v-if="!mapLoaded"
                                                            class="absolute inset-0 flex items-center justify-center bg-gray-100 dark:bg-gray-800 bg-opacity-80 z-10"
                                                        >
                                                            <div
                                                                class="text-center"
                                                            >
                                                                <v-icon
                                                                    name="bi-compass"
                                                                    class="text-blue-500 dark:text-blue-400 text-3xl mb-2 animate-spin"
                                                                />
                                                                <p
                                                                    class="text-base text-gray-600 dark:text-gray-400"
                                                                >
                                                                    Loading
                                                                    map...
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div
                                                        v-else
                                                        class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 flex items-center justify-center h-80"
                                                    >
                                                        <div
                                                            class="text-center text-gray-500 dark:text-gray-400"
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
                                            class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm"
                                        >
                                            <div
                                                class="bg-gradient-to-r from-purple-50 to-blue-50 dark:from-purple-900/20 dark:to-blue-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-600"
                                            >
                                                <h3
                                                    class="text-lg font-semibold text-gray-900 dark:text-white flex items-center"
                                                >
                                                    <v-icon
                                                        name="bi-person"
                                                        class="w-5 h-5 text-purple-600 dark:text-purple-400 mr-2"
                                                    />
                                                    Reporter Information
                                                </h3>
                                            </div>
                                            <div class="p-6 space-y-4">
                                                <div
                                                    class="grid grid-cols-1 gap-4"
                                                >
                                                    <div
                                                        class="flex items-start space-x-3 p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg"
                                                    >
                                                        <div
                                                            class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg"
                                                        >
                                                            <v-icon
                                                                name="bi-person-badge"
                                                                class="text-purple-600 dark:text-purple-400 w-4 h-4"
                                                            />
                                                        </div>
                                                        <div>
                                                            <p
                                                                class="text-sm text-gray-500 dark:text-gray-400"
                                                            >
                                                                Reporter Name
                                                            </p>
                                                            <p
                                                                class="font-medium text-gray-900 dark:text-white"
                                                            >
                                                                {{
                                                                    report.reporter_name ||
                                                                    "N/A"
                                                                }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div
                                                        v-if="
                                                            report.reporter_phone
                                                        "
                                                        class="flex items-start space-x-3 p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg"
                                                    >
                                                        <div
                                                            class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg"
                                                        >
                                                            <v-icon
                                                                name="bi-telephone"
                                                                class="text-purple-600 dark:text-purple-400 w-4 h-4"
                                                            />
                                                        </div>
                                                        <div>
                                                            <p
                                                                class="text-sm text-gray-500 dark:text-gray-400"
                                                            >
                                                                Phone Number
                                                            </p>
                                                            <p
                                                                class="font-medium text-gray-900 dark:text-white"
                                                            >
                                                                {{
                                                                    report.reporter_phone
                                                                }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div
                                                        v-if="report.user"
                                                        class="flex items-start space-x-3 p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg"
                                                    >
                                                        <div
                                                            class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg"
                                                        >
                                                            <v-icon
                                                                name="bi-person-check"
                                                                class="text-purple-600 dark:text-purple-400 w-4 h-4"
                                                            />
                                                        </div>
                                                        <div>
                                                            <p
                                                                class="text-sm text-gray-500 dark:text-gray-400"
                                                            >
                                                                Registered User
                                                            </p>
                                                            <p
                                                                class="font-medium text-gray-900 dark:text-white"
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
                                    </div>

                                    <!-- Right Column -->
                                    <div
                                        class="space-y-6"
                                        :class="isMaximized ? 'space-y-8' : ''"
                                    >
                                        <!-- Issue Information -->
                                        <div
                                            class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm"
                                        >
                                            <div
                                                class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-600"
                                            >
                                                <h3
                                                    class="text-lg font-semibold text-gray-900 dark:text-white flex items-center"
                                                >
                                                    <v-icon
                                                        name="bi-droplet"
                                                        class="w-5 h-5 text-green-600 dark:text-green-400 mr-2"
                                                    />
                                                    Issue Details
                                                </h3>
                                            </div>
                                            <div class="p-6 space-y-4">
                                                <div
                                                    class="flex items-start space-x-3 p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg"
                                                >
                                                    <div
                                                        class="p-2 bg-green-100 dark:bg-green-900 rounded-lg"
                                                    >
                                                        <v-icon
                                                            name="bi-tag"
                                                            class="text-green-600 dark:text-green-400 w-4 h-4"
                                                        />
                                                    </div>
                                                    <div>
                                                        <p
                                                            class="text-sm text-gray-500 dark:text-gray-400"
                                                        >
                                                            Issue Type
                                                        </p>
                                                        <p
                                                            class="font-medium text-gray-900 dark:text-white"
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
                                                </div>
                                                <div>
                                                    <label
                                                        class="text-sm text-gray-500 dark:text-gray-400 font-medium"
                                                        >Description</label
                                                    >
                                                    <div
                                                        class="bg-gray-50 dark:bg-gray-700/30 p-4 rounded-lg border border-gray-200 dark:border-gray-600 mt-2"
                                                    >
                                                        <p
                                                            class="text-base text-gray-700 dark:text-gray-300 whitespace-pre-line"
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
                                            class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm"
                                        >
                                            <div
                                                class="bg-gradient-to-r from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-600"
                                            >
                                                <h3
                                                    class="text-lg font-semibold text-gray-900 dark:text-white flex items-center"
                                                >
                                                    <v-icon
                                                        name="bi-images"
                                                        class="w-5 h-5 text-orange-600 dark:text-orange-400 mr-2"
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
                                                        class="relative group overflow-hidden rounded-xl border border-gray-200 dark:border-gray-600 shadow-sm transition-all duration-300 hover:shadow-md hover:border-blue-300 dark:hover:border-blue-500 cursor-pointer h-40"
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
                                                                            getMediaPath(
                                                                                media.path
                                                                            )
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
                                                                    getMediaPath(
                                                                        media.path
                                                                    )
                                                                "
                                                                :alt="`Report photo ${
                                                                    index + 1
                                                                }`"
                                                                class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                                                                loading="lazy"
                                                                @error="
                                                                    handleImageError
                                                                "
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
                                            class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm"
                                        >
                                            <div
                                                class="bg-gradient-to-r from-gray-50 to-blue-50 dark:from-gray-900/20 dark:to-blue-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-600"
                                            >
                                                <h3
                                                    class="text-lg font-semibold text-gray-900 dark:text-white flex items-center"
                                                >
                                                    <v-icon
                                                        name="bi-info-circle"
                                                        class="w-5 h-5 text-gray-600 dark:text-gray-400 mr-2"
                                                    />
                                                    Additional Information
                                                </h3>
                                            </div>
                                            <div class="p-6 space-y-4">
                                                <div
                                                    class="grid grid-cols-1 md:grid-cols-2 gap-4"
                                                >
                                                    <div
                                                        class="flex items-start space-x-3 p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg"
                                                    >
                                                        <div
                                                            class="p-2 bg-gray-100 dark:bg-gray-800 rounded-lg"
                                                        >
                                                            <v-icon
                                                                name="bi-calendar"
                                                                class="text-gray-600 dark:text-gray-400 w-4 h-4"
                                                            />
                                                        </div>
                                                        <div>
                                                            <p
                                                                class="text-sm text-gray-500 dark:text-gray-400"
                                                            >
                                                                Submitted Date
                                                            </p>
                                                            <p
                                                                class="font-medium text-gray-900 dark:text-white"
                                                            >
                                                                {{
                                                                    formatDateTime(
                                                                        report.created_at
                                                                    )
                                                                }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="flex items-start space-x-3 p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg"
                                                    >
                                                        <div
                                                            class="p-2 bg-gray-100 dark:bg-gray-800 rounded-lg"
                                                        >
                                                            <v-icon
                                                                name="bi-person"
                                                                class="text-gray-600 dark:text-gray-400 w-4 h-4"
                                                            />
                                                        </div>
                                                        <div>
                                                            <p
                                                                class="text-sm text-gray-500 dark:text-gray-400"
                                                            >
                                                                User Type
                                                            </p>
                                                            <p
                                                                class="font-medium text-gray-900 dark:text-white"
                                                            >
                                                                {{
                                                                    report.formatted_user_types ||
                                                                    "Guest"
                                                                }}
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
import { XIcon, Maximize2Icon, Minimize2Icon } from "lucide-vue-next";
import ImageModal from "./ImageModal.vue";
import VideoModal from "./VideoModal.vue";

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
const isMaximized = ref(false);

// Computed
const statusClass = computed(() => {
    if (!props.report?.status)
        return "bg-gray-100 text-gray-800 border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600";

    const classes = {
        pending:
            "bg-yellow-100 text-yellow-800 border-yellow-200 dark:bg-yellow-900 dark:text-yellow-200 dark:border-yellow-700",
        in_progress:
            "bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-900 dark:text-blue-200 dark:border-blue-700",
        resolved:
            "bg-green-100 text-green-800 border-green-200 dark:bg-green-900 dark:text-green-200 dark:border-green-700",
    };

    return (
        classes[props.report.status.toLowerCase()] ||
        "bg-gray-100 text-gray-800 border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600"
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

const getMediaPath = (path) => {
    // Remove any duplicate '/storage/' prefixes
    const cleanPath = path.replace(/^\/?storage\/?/, "");
    return `/storage/${cleanPath}`;
};

const handleImageError = (event) => {
    console.error("Failed to load image:", event.target.src);
    // You can set a placeholder image here if needed
    event.target.src = "/images/placeholder.jpg";
};

const openMediaModal = (media, index = 0) => {
    console.log("Opening media modal:", media, index);

    if (isVideoFile(media)) {
        openVideoModal(media.path);
    } else {
        openImageModal(media.path, index);
    }
};

const openImageModal = (imagePath, index = 0) => {
    console.log("Opening image modal:", imagePath, index);

    // Filter only images (not videos)
    const imageMedia = props.report.photos.filter(
        (media) => !isVideoFile(media)
    );
    allImages.value = imageMedia.map((media) => getMediaPath(media.path));

    // Find the correct index in the filtered images array
    const targetImagePath = getMediaPath(imagePath);
    currentImageIndex.value = allImages.value.indexOf(targetImagePath);

    // If not found, use the provided index
    if (currentImageIndex.value === -1) {
        currentImageIndex.value = index;
    }

    showImageModal.value = true;
    console.log(
        "Image modal opened with images:",
        allImages.value,
        "current index:",
        currentImageIndex.value
    );
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
    console.log("Opening video modal:", videoPath);
    currentVideo.value = getMediaPath(videoPath);
    showVideoModal.value = true;
};

const closeVideoModal = () => {
    showVideoModal.value = false;
    currentVideo.value = null;
};

const isVideoFile = (file) => {
    return (
        file.type === "video" ||
        file.mime_type?.includes("video") ||
        file.original_name?.match(/\.(mp4|mov|avi|webm)$/i) ||
        file.path?.match(/\.(mp4|mov|avi|webm)$/i)
    );
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

<style scoped>
/* Custom scrollbar */
.overflow-y-auto {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f1f5f9;
}

.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Dark mode scrollbar */
.dark .overflow-y-auto {
    scrollbar-color: #475569 #1e293b;
}

.dark .overflow-y-auto::-webkit-scrollbar-track {
    background: #1e293b;
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb {
    background: #475569;
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #64748b;
}
</style>
