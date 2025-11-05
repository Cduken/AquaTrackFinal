<template>
    <!-- Single transition wrapper for both overlay and panel -->
    <transition name="modal">
        <div v-if="isOpen" class="fixed inset-0 z-[1000] overflow-hidden">
            <!-- Overlay -->
            <div
                class="absolute inset-0 bg-black/80 backdrop-blur-xs transition-opacity duration-300"
                @click.self="closeModal"
            ></div>

            <!-- Sliding panel container -->
            <div
                class="fixed inset-y-0 right-0 w-full max-w-2xl flex"
                :class="{ 'max-w-full': isMaximized }"
            >
                <!-- Panel with transform class for animation -->
                <div
                    class="relative w-full h-full transform transition-transform duration-300 ease-in-out"
                >
                    <div
                        class="h-full flex flex-col bg-white shadow-2xl overflow-hidden"
                    >
                        <!-- Header -->
                        <div
                            class="flex items-center justify-between px-6 py-5 bg-[#062F64]"
                        >
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-white/10 rounded-full">
                                    <v-icon
                                        name="oi-report"
                                        class="text-amber-300"
                                        scale="1.5"
                                    />
                                </div>
                                <div>
                                    <span
                                        class="text-white font-semibold text-xl tracking-wide"
                                        >Water Quality Issue Report</span
                                    >
                                    <p class="text-blue-100 text-xs mt-1">
                                        Official Report Submission for Clarin,
                                        Bohol
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <!-- GPS Status Indicator -->
                                <div class="flex items-center mr-2">
                                    <div
                                        class="flex items-center px-3 py-1 rounded-full text-xs font-medium"
                                        :class="{
                                            'bg-green-500/20 text-green-300':
                                                locationStatus === 'success',
                                            'bg-yellow-500/20 text-yellow-300':
                                                locationStatus === 'loading',
                                            'bg-red-500/20 text-red-300':
                                                locationStatus === 'error',
                                            'bg-gray-500/20 text-gray-300':
                                                locationStatus === 'idle',
                                            'bg-blue-500/20 text-blue-300':
                                                locationStatus ===
                                                'offline_cached_location',
                                            'bg-orange-500/20 text-orange-300':
                                                locationStatus ===
                                                'offline_default_location',
                                        }"
                                    >
                                        <div
                                            class="w-2 h-2 rounded-full mr-2 animate-pulse"
                                            :class="{
                                                'bg-green-400':
                                                    locationStatus ===
                                                    'success',
                                                'bg-yellow-400':
                                                    locationStatus ===
                                                    'loading',
                                                'bg-red-400':
                                                    locationStatus === 'error',
                                                'bg-gray-400':
                                                    locationStatus === 'idle',
                                                'bg-blue-400':
                                                    locationStatus ===
                                                    'offline_cached_location',
                                                'bg-orange-400':
                                                    locationStatus ===
                                                    'offline_default_location',
                                            }"
                                        ></div>
                                        <span
                                            v-if="locationStatus === 'success'"
                                            >GPS Ready</span
                                        >
                                        <span
                                            v-else-if="
                                                locationStatus === 'loading'
                                            "
                                            >Getting GPS...</span
                                        >
                                        <span
                                            v-else-if="
                                                locationStatus === 'error'
                                            "
                                            >GPS Required</span
                                        >
                                        <span
                                            v-else-if="
                                                locationStatus ===
                                                'offline_cached_location'
                                            "
                                            >Cached GPS</span
                                        >
                                        <span
                                            v-else-if="
                                                locationStatus ===
                                                'offline_default_location'
                                            "
                                            >Default Location</span
                                        >
                                        <span v-else>GPS Offline</span>
                                    </div>
                                </div>

                                <!-- Maximize/Minimize Button -->
                                <button
                                    @click="toggleMaximize"
                                    class="text-white hover:text-gray-200 transition-all duration-200 p-2 rounded-full hover:bg-white/10"
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
                                    @click="closeModal"
                                    class="text-white hover:text-gray-200 transition-all duration-200 p-2 rounded-full hover:bg-white/10"
                                >
                                    <v-icon name="bi-x-lg" class="h-6 w-6" />
                                </button>
                            </div>
                        </div>

                        <!-- Content -->
                        <div
                            class="flex-1 overflow-y-auto p-6"
                            :class="{ 'p-8': isMaximized }"
                        >
                            <!-- Form Errors Indicator -->
                            <div
                                v-if="hasErrors"
                                class="mb-6 p-4 bg-red-50 border border-red-200 text-red-600 rounded-xl text-sm shadow-sm"
                                :class="{ 'p-6': isMaximized }"
                            >
                                <div class="flex items-start">
                                    <v-icon
                                        name="bi-exclamation-triangle"
                                        class="mr-2 mt-0.5 flex-shrink-0"
                                    />
                                    <div>
                                        <p
                                            class="font-medium"
                                            :class="{
                                                'text-base': isMaximized,
                                            }"
                                        >
                                            Please correct the following issues
                                            before submitting:
                                        </p>
                                        <ul
                                            class="list-disc list-inside mt-1 pl-1"
                                            :class="{
                                                'text-base mt-2': isMaximized,
                                            }"
                                        >
                                            <li
                                                v-for="(
                                                    error, key
                                                ) in form.errors"
                                                :key="key"
                                            >
                                                {{ error }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Location Status Warnings -->
                            <div
                                v-if="
                                    locationStatus === 'offline_cached_location'
                                "
                                class="mb-6 p-4 bg-blue-50 border border-blue-200 text-blue-700 rounded-xl text-sm shadow-sm"
                            >
                                <div class="flex items-start">
                                    <v-icon
                                        name="bi-info-circle"
                                        class="mr-2 mt-0.5 flex-shrink-0"
                                    />
                                    <div>
                                        <p class="font-medium">
                                            Using Cached GPS Location
                                        </p>
                                        <p class="mt-1">
                                            Using previously saved GPS
                                            coordinates for better accuracy.
                                            <button
                                                type="button"
                                                @click="getLocation"
                                                class="underline font-medium hover:text-blue-800"
                                            >
                                                Refresh location
                                            </button>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div
                                v-if="
                                    locationStatus ===
                                    'offline_default_location'
                                "
                                class="mb-6 p-4 bg-amber-50 border border-amber-200 text-amber-700 rounded-xl text-sm shadow-sm"
                            >
                                <div class="flex items-start">
                                    <v-icon
                                        name="bi-geo-alt"
                                        class="mr-2 mt-0.5 flex-shrink-0"
                                    />
                                    <div>
                                        <p class="font-medium">
                                            Using Default Location
                                        </p>
                                        <p class="mt-1">
                                            GPS signal unavailable. Using center
                                            of Clarin, Bohol.
                                            <button
                                                type="button"
                                                @click="getLocation"
                                                class="underline font-medium hover:text-amber-800"
                                            >
                                                Retry GPS detection
                                            </button>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div
                                v-if="locationStatus === 'error'"
                                class="mb-6 p-4 bg-red-50 border border-red-200 text-red-600 rounded-xl text-sm shadow-sm"
                            >
                                <div class="flex items-start">
                                    <v-icon
                                        name="bi-exclamation-triangle"
                                        class="mr-2 mt-0.5 flex-shrink-0"
                                    />
                                    <div>
                                        <p class="font-medium">
                                            Location Access Required
                                        </p>
                                        <p class="mt-1">
                                            Please enable GPS/location services
                                            to submit your report.
                                            <button
                                                type="button"
                                                @click="getLocation"
                                                class="underline font-medium hover:text-red-800"
                                            >
                                                Retry location detection
                                            </button>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <form
                                @submit.prevent="submitReport"
                                class="space-y-6"
                                :class="{ 'space-y-8': isMaximized }"
                            >
                                <!-- Rest of your form content remains the same -->
                                <!-- Location Information Section -->
                                <div
                                    class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden"
                                >
                                    <div
                                        class="bg-gray-50 px-5 py-3 border-b border-gray-200"
                                        :class="{ 'px-6 py-4': isMaximized }"
                                    >
                                        <h3
                                            class="text-lg font-semibold text-gray-800 flex items-center"
                                            :class="{ 'text-xl': isMaximized }"
                                        >
                                            <v-icon
                                                name="bi-geo-alt"
                                                class="text-blue-600 mr-2"
                                                :scale="
                                                    isMaximized ? '1.1' : '1'
                                                "
                                            />
                                            Location Information
                                        </h3>
                                        <p
                                            class="text-sm text-gray-600 mt-1"
                                            :class="{
                                                'text-base mt-2': isMaximized,
                                            }"
                                        >
                                            Provide details about where the
                                            issue is located
                                        </p>
                                    </div>
                                    <div
                                        class="p-5 space-y-4"
                                        :class="{
                                            'p-6 space-y-6': isMaximized,
                                        }"
                                    >
                                        <!-- Municipality and Province -->
                                        <div
                                            class="grid grid-cols-1 md:grid-cols-2 gap-4"
                                            :class="{ 'gap-6': isMaximized }"
                                        >
                                            <div>
                                                <label
                                                    for="municipality"
                                                    class="block text-sm font-medium text-gray-700 mb-1"
                                                    :class="{
                                                        'text-base':
                                                            isMaximized,
                                                    }"
                                                >
                                                    Municipality
                                                </label>
                                                <input
                                                    type="text"
                                                    id="municipality"
                                                    v-model="form.municipality"
                                                    readonly
                                                    class="w-full p-3 rounded-lg text-gray-900 bg-gray-50 border border-gray-300 text-sm"
                                                    :class="{
                                                        'p-4 text-base':
                                                            isMaximized,
                                                    }"
                                                />
                                            </div>
                                            <div>
                                                <label
                                                    for="province"
                                                    class="block text-sm font-medium text-gray-700 mb-1"
                                                    :class="{
                                                        'text-base':
                                                            isMaximized,
                                                    }"
                                                >
                                                    Province
                                                </label>
                                                <input
                                                    type="text"
                                                    id="province"
                                                    v-model="form.province"
                                                    readonly
                                                    class="w-full p-3 rounded-lg text-gray-900 bg-gray-50 border border-gray-300 text-sm"
                                                    :class="{
                                                        'p-4 text-base':
                                                            isMaximized,
                                                    }"
                                                />
                                            </div>
                                        </div>

                                        <!-- Barangay and Zone -->
                                        <div
                                            class="grid grid-cols-1 md:grid-cols-2 gap-4"
                                            :class="{ 'gap-6': isMaximized }"
                                        >
                                            <div>
                                                <label
                                                    for="barangay"
                                                    class="block text-sm font-medium text-gray-700 mb-1"
                                                    :class="{
                                                        'text-base':
                                                            isMaximized,
                                                    }"
                                                >
                                                    Barangay
                                                    <span class="text-red-500"
                                                        >*</span
                                                    >
                                                </label>
                                                <div class="relative">
                                                    <select
                                                        id="barangay"
                                                        v-model="form.barangay"
                                                        required
                                                        class="w-full p-3 pr-10 rounded-lg text-gray-900 bg-white border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm appearance-none transition-all"
                                                        :class="{
                                                            'p-4 pr-12 text-base':
                                                                isMaximized,
                                                        }"
                                                    >
                                                        <option
                                                            value=""
                                                            disabled
                                                            selected
                                                        >
                                                            Select Barangay
                                                        </option>
                                                        <option
                                                            v-for="barangay in allBarangays"
                                                            :key="barangay"
                                                            :value="barangay"
                                                        >
                                                            {{ barangay }}
                                                        </option>
                                                    </select>
                                                    <div
                                                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700"
                                                    >
                                                        <v-icon
                                                            name="bi-chevron-down"
                                                        />
                                                    </div>
                                                </div>
                                                <p
                                                    v-if="form.errors.barangay"
                                                    class="mt-1 text-xs text-red-600 flex items-center"
                                                    :class="{
                                                        'text-sm': isMaximized,
                                                    }"
                                                >
                                                    <v-icon
                                                        name="bi-exclamation-circle"
                                                        class="mr-1"
                                                    />
                                                    {{ form.errors.barangay }}
                                                </p>
                                            </div>
                                            <div>
                                                <label
                                                    for="zone"
                                                    class="block text-sm font-medium text-gray-700 mb-1"
                                                    :class="{
                                                        'text-base':
                                                            isMaximized,
                                                    }"
                                                >
                                                    Zone
                                                </label>
                                                <input
                                                    type="text"
                                                    id="zone"
                                                    v-model="form.zone"
                                                    readonly
                                                    class="w-full p-3 rounded-lg text-gray-900 bg-gray-50 border border-gray-300 text-sm"
                                                    :class="{
                                                        'p-4 text-base':
                                                            isMaximized,
                                                    }"
                                                    placeholder="Zone will be auto-filled"
                                                />
                                                <p
                                                    v-if="form.errors.zone"
                                                    class="mt-1 text-xs text-red-600 flex items-center"
                                                    :class="{
                                                        'text-sm': isMaximized,
                                                    }"
                                                >
                                                    <v-icon
                                                        name="bi-exclamation-circle"
                                                        class="mr-1"
                                                    />
                                                    {{ form.errors.zone }}
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Purok -->
                                        <div>
                                            <label
                                                for="purok"
                                                class="block text-sm font-medium text-gray-700 mb-1"
                                                :class="{
                                                    'text-base': isMaximized,
                                                }"
                                            >
                                                Purok/Street
                                                <span class="text-red-500"
                                                    >*</span
                                                >
                                            </label>
                                            <input
                                                type="text"
                                                id="purok"
                                                v-model="form.purok"
                                                required
                                                class="w-full p-3 rounded-lg text-gray-900 bg-white border border-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all"
                                                :class="{
                                                    'p-4 text-base':
                                                        isMaximized,
                                                }"
                                                placeholder="Enter purok number or street name"
                                            />
                                            <p
                                                v-if="form.errors.purok"
                                                class="mt-1 text-xs text-red-600 flex items-center"
                                                :class="{
                                                    'text-sm': isMaximized,
                                                }"
                                            >
                                                <v-icon
                                                    name="bi-exclamation-circle"
                                                    class="mr-1"
                                                />
                                                {{ form.errors.purok }}
                                            </p>
                                        </div>

                                        <!-- GPS Coordinates Display -->
                                        <div
                                            v-if="
                                                form.latitude && form.longitude
                                            "
                                            class="p-3 rounded-lg border text-sm"
                                            :class="{
                                                'bg-green-50 border-green-200 text-green-700':
                                                    locationStatus ===
                                                    'success',
                                                'bg-blue-50 border-blue-200 text-blue-700':
                                                    locationStatus ===
                                                    'offline_cached_location',
                                                'bg-amber-50 border-amber-200 text-amber-700':
                                                    locationStatus ===
                                                    'offline_default_location',
                                                'bg-gray-50 border-gray-200 text-gray-700':
                                                    locationStatus === 'idle' ||
                                                    locationStatus ===
                                                        'loading',
                                            }"
                                        >
                                            <div class="flex items-center">
                                                <v-icon
                                                    :name="
                                                        getLocationStatusIcon()
                                                    "
                                                    class="mr-2"
                                                />
                                                <div>
                                                    <span class="font-medium">{{
                                                        getLocationStatusText()
                                                    }}</span>
                                                    <div
                                                        class="text-xs mt-1 font-mono"
                                                    >
                                                        Lat:
                                                        {{
                                                            form.latitude.toFixed(
                                                                6
                                                            )
                                                        }}, Lon:
                                                        {{
                                                            form.longitude.toFixed(
                                                                6
                                                            )
                                                        }}
                                                    </div>
                                                    <div
                                                        v-if="locationAccuracy"
                                                        class="text-xs mt-1"
                                                    >
                                                        Accuracy:
                                                        {{ locationAccuracy }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Reporter Information Section -->
                                <div
                                    class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden"
                                >
                                    <div
                                        class="bg-gray-50 px-5 py-3 border-b border-gray-200"
                                        :class="{ 'px-6 py-4': isMaximized }"
                                    >
                                        <h3
                                            class="text-lg font-semibold text-gray-800 flex items-center"
                                            :class="{ 'text-xl': isMaximized }"
                                        >
                                            <v-icon
                                                name="bi-person"
                                                class="text-blue-600 mr-2"
                                                :scale="
                                                    isMaximized ? '1.1' : '1'
                                                "
                                            />
                                            Reporter Information
                                        </h3>
                                        <p
                                            class="text-sm text-gray-600 mt-1"
                                            :class="{
                                                'text-base mt-2': isMaximized,
                                            }"
                                        >
                                            Your contact details for follow-up
                                            if needed
                                        </p>
                                    </div>
                                    <div
                                        class="p-5 space-y-4"
                                        :class="{
                                            'p-6 space-y-6': isMaximized,
                                        }"
                                    >
                                        <div>
                                            <label
                                                for="reporter_name"
                                                class="block text-sm font-medium text-gray-700 mb-1"
                                                :class="{
                                                    'text-base': isMaximized,
                                                }"
                                            >
                                                Full Name
                                            </label>
                                            <input
                                                type="text"
                                                id="reporter_name"
                                                v-model="form.reporter_name"
                                                readonly
                                                class="w-full p-3 rounded-lg text-gray-900 bg-gray-50 border border-gray-300 text-sm"
                                                :class="{
                                                    'p-4 text-base':
                                                        isMaximized,
                                                }"
                                            />
                                        </div>
                                        <div>
                                            <label
                                                for="reporter_phone"
                                                class="block text-sm font-medium text-gray-700 mb-1"
                                                :class="{
                                                    'text-base': isMaximized,
                                                }"
                                            >
                                                Phone Number (Optional)
                                            </label>
                                            <div class="relative">
                                                <input
                                                    type="tel"
                                                    id="reporter_phone"
                                                    v-model="
                                                        form.reporter_phone
                                                    "
                                                    @input="restrictPhoneInput"
                                                    class="w-full p-3 rounded-lg text-gray-900 bg-white border border-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all"
                                                    :class="{
                                                        'p-4 pl-14 text-base':
                                                            isMaximized,
                                                    }"
                                                    pattern="[0-9]{1,11}"
                                                    maxlength="11"
                                                    placeholder="Enter phone number"
                                                />
                                            </div>
                                            <p
                                                class="mt-1 text-xs text-gray-500"
                                                :class="{
                                                    'text-sm mt-2': isMaximized,
                                                }"
                                            >
                                                Enter your 11-digit mobile
                                                number start of '09' (optional)
                                            </p>
                                            <p
                                                v-if="
                                                    form.errors.reporter_phone
                                                "
                                                class="mt-1 text-xs text-red-600 flex items-center"
                                                :class="{
                                                    'text-sm': isMaximized,
                                                }"
                                            >
                                                <v-icon
                                                    name="bi-exclamation-circle"
                                                    class="mr-1"
                                                />
                                                {{ form.errors.reporter_phone }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Issue Details Section -->
                                <div
                                    class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden"
                                >
                                    <div
                                        class="bg-gray-50 px-5 py-3 border-b border-gray-200"
                                        :class="{ 'px-6 py-4': isMaximized }"
                                    >
                                        <h3
                                            class="text-lg font-semibold text-gray-800 flex items-center"
                                            :class="{ 'text-xl': isMaximized }"
                                        >
                                            <v-icon
                                                name="bi-droplet"
                                                class="text-blue-600 mr-2"
                                                :scale="
                                                    isMaximized ? '1.1' : '1'
                                                "
                                            />
                                            Issue Details
                                        </h3>
                                        <p
                                            class="text-sm text-gray-600 mt-1"
                                            :class="{
                                                'text-base mt-2': isMaximized,
                                            }"
                                        >
                                            Describe the water quality issue
                                            you're reporting
                                        </p>
                                    </div>
                                    <div
                                        class="p-5 space-y-4"
                                        :class="{
                                            'p-6 space-y-6': isMaximized,
                                        }"
                                    >
                                        <!-- Water Issue Type -->
                                        <div>
                                            <label
                                                for="water_issue_type"
                                                class="block text-sm font-medium text-gray-700 mb-1"
                                                :class="{
                                                    'text-base': isMaximized,
                                                }"
                                            >
                                                Issue Type
                                                <span class="text-red-500"
                                                    >*</span
                                                >
                                            </label>
                                            <div class="relative">
                                                <select
                                                    id="water_issue_type"
                                                    v-model="
                                                        form.water_issue_type
                                                    "
                                                    required
                                                    class="w-full p-3 pr-10 rounded-lg text-gray-900 bg-white border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm appearance-none transition-all"
                                                    :class="{
                                                        'p-4 pr-12 text-base':
                                                            isMaximized,
                                                    }"
                                                >
                                                    <option
                                                        value=""
                                                        disabled
                                                        selected
                                                    >
                                                        Select Issue Type
                                                    </option>
                                                    <option
                                                        v-for="issue in waterIssueTypes"
                                                        :key="issue"
                                                        :value="issue"
                                                    >
                                                        {{ issue }}
                                                    </option>
                                                    <option value="others">
                                                        Others
                                                    </option>
                                                </select>
                                                <div
                                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700"
                                                >
                                                    <v-icon
                                                        name="bi-chevron-down"
                                                    />
                                                </div>
                                            </div>
                                            <p
                                                v-if="
                                                    form.errors.water_issue_type
                                                "
                                                class="mt-1 text-xs text-red-600 flex items-center"
                                                :class="{
                                                    'text-sm': isMaximized,
                                                }"
                                            >
                                                <v-icon
                                                    name="bi-exclamation-circle"
                                                    class="mr-1"
                                                />
                                                {{
                                                    form.errors.water_issue_type
                                                }}
                                            </p>
                                        </div>

                                        <!-- Custom Issue Description -->
                                        <div
                                            v-if="
                                                form.water_issue_type ===
                                                'others'
                                            "
                                        >
                                            <label
                                                for="custom_water_issue"
                                                class="block text-sm font-medium text-gray-700 mb-1"
                                                :class="{
                                                    'text-base': isMaximized,
                                                }"
                                            >
                                                Custom Issue Description
                                                <span class="text-red-500"
                                                    >*</span
                                                >
                                            </label>
                                            <input
                                                type="text"
                                                id="custom_water_issue"
                                                v-model="
                                                    form.custom_water_issue
                                                "
                                                class="w-full p-3 rounded-lg text-gray-900 bg-white border border-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all"
                                                :class="{
                                                    'p-4 text-base':
                                                        isMaximized,
                                                }"
                                                placeholder="Describe the custom water issue"
                                                maxlength="100"
                                                required
                                            />
                                            <p
                                                v-if="
                                                    form.errors
                                                        .custom_water_issue
                                                "
                                                class="mt-1 text-xs text-red-600 flex items-center"
                                                :class="{
                                                    'text-sm': isMaximized,
                                                }"
                                            >
                                                <v-icon
                                                    name="bi-exclamation-circle"
                                                    class="mr-1"
                                                />
                                                {{
                                                    form.errors
                                                        .custom_water_issue
                                                }}
                                            </p>
                                        </div>

                                        <!-- Description -->
                                        <div>
                                            <label
                                                for="description"
                                                class="block text-sm font-medium text-gray-700 mb-1"
                                                :class="{
                                                    'text-base': isMaximized,
                                                }"
                                            >
                                                Detailed Description
                                                <span class="text-red-500"
                                                    >*</span
                                                >
                                            </label>
                                            <div
                                                class="bg-white p-4 rounded-lg border border-gray-300 shadow-inner"
                                                :class="{ 'p-5': isMaximized }"
                                            >
                                                <textarea
                                                    id="description"
                                                    v-model="form.description"
                                                    rows="4"
                                                    required
                                                    class="w-full p-3 rounded-lg text-gray-900 bg-white border-none placeholder:text-gray-400 focus:ring-2 focus:ring-blue-500 text-sm resize-none"
                                                    :class="{
                                                        'p-6 text-base':
                                                            isMaximized,
                                                    }"
                                                    placeholder="Please provide a detailed description of the water quality issue, including when it started, how it's affecting you, and any other relevant details..."
                                                    maxlength="500"
                                                ></textarea>
                                                <div
                                                    class="flex justify-between items-center mt-2 text-xs text-gray-500"
                                                    :class="{
                                                        'text-sm mt-3':
                                                            isMaximized,
                                                    }"
                                                >
                                                    <div
                                                        v-if="form.description"
                                                        class="flex items-center"
                                                    >
                                                        <v-icon
                                                            name="bi-check-circle"
                                                            class="text-green-500 mr-1"
                                                        />
                                                        <span
                                                            >{{
                                                                form.description
                                                                    .length
                                                            }}/500
                                                            characters</span
                                                        >
                                                    </div>

                                                    <button
                                                        type="button"
                                                        @click="
                                                            form.description =
                                                                ''
                                                        "
                                                        v-if="form.description"
                                                        class="text-red-500 hover:text-red-700 text-xs"
                                                        :class="{
                                                            'text-sm':
                                                                isMaximized,
                                                        }"
                                                    >
                                                        Clear
                                                    </button>
                                                </div>
                                                <p
                                                    v-if="
                                                        form.errors.description
                                                    "
                                                    class="mt-1 text-xs text-red-600 flex items-center"
                                                    :class="{
                                                        'text-sm': isMaximized,
                                                    }"
                                                >
                                                    <v-icon
                                                        name="bi-exclamation-circle"
                                                        class="mr-1"
                                                    />
                                                    {{
                                                        form.errors.description
                                                    }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Evidence Section -->
                                <div
                                    class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden"
                                >
                                    <div
                                        class="bg-gray-50 px-5 py-3 border-b border-gray-200"
                                        :class="{ 'px-6 py-4': isMaximized }"
                                    >
                                        <h3
                                            class="text-lg font-semibold text-gray-800 flex items-center"
                                            :class="{ 'text-xl': isMaximized }"
                                        >
                                            <v-icon
                                                name="bi-camera"
                                                class="text-blue-600 mr-2"
                                                :scale="
                                                    isMaximized ? '1.1' : '1'
                                                "
                                            />
                                            Evidence
                                            <span
                                                class="text-xs font-normal text-gray-500 ml-2"
                                                :class="{
                                                    'text-sm': isMaximized,
                                                }"
                                            >
                                                ({{ form.photos.length }}/{{
                                                    MAX_TOTAL
                                                }})
                                            </span>
                                        </h3>
                                        <p
                                            class="text-sm text-gray-600 mt-1"
                                            :class="{
                                                'text-base mt-2': isMaximized,
                                            }"
                                        >
                                            Visual documentation helps us
                                            address the issue more effectively
                                        </p>
                                    </div>
                                    <div
                                        class="p-5 space-y-4"
                                        :class="{
                                            'p-6 space-y-6': isMaximized,
                                        }"
                                    >
                                        <!-- Media Counters -->
                                        <div
                                            class="flex items-center justify-between text-sm"
                                            :class="{
                                                'text-base': isMaximized,
                                            }"
                                        >
                                            <span
                                                class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full flex items-center"
                                                :class="{
                                                    'px-4 py-2': isMaximized,
                                                }"
                                            >
                                                <v-icon
                                                    name="bi-image"
                                                    class="mr-1"
                                                />
                                                Photos:
                                                {{
                                                    form.photos.filter((file) =>
                                                        file.type.startsWith(
                                                            "image"
                                                        )
                                                    ).length
                                                }}/{{ MAX_PHOTOS }}
                                            </span>
                                            <span
                                                class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full flex items-center"
                                                :class="{
                                                    'px-4 py-2': isMaximized,
                                                }"
                                            >
                                                <v-icon
                                                    name="bi-camera-video"
                                                    class="mr-1"
                                                />
                                                Videos:
                                                {{
                                                    form.photos.filter((file) =>
                                                        file.type.startsWith(
                                                            "video"
                                                        )
                                                    ).length
                                                }}/{{ MAX_VIDEOS }}
                                            </span>
                                        </div>

                                        <!-- Camera Status Banner -->
                                        <div
                                            v-if="cameraError"
                                            class="p-3 bg-red-50 border border-red-200 rounded-lg"
                                            :class="{ 'p-4': isMaximized }"
                                        >
                                            <div
                                                class="flex items-center text-red-600 text-sm"
                                                :class="{
                                                    'text-base': isMaximized,
                                                }"
                                            >
                                                <v-icon
                                                    name="bi-exclamation-triangle-fill"
                                                    class="w-4 h-4 mr-2"
                                                />
                                                {{ cameraError }}
                                            </div>
                                            <button
                                                type="button"
                                                @click="retryCamera"
                                                class="mt-2 text-xs text-blue-600 hover:text-blue-700 underline flex items-center"
                                                :class="{
                                                    'text-sm mt-3': isMaximized,
                                                }"
                                            >
                                                <v-icon
                                                    name="bi-arrow-repeat"
                                                    class="mr-1"
                                                />
                                                Try Again
                                            </button>
                                        </div>

                                        <!-- Camera Interface -->
                                        <div
                                            class="bg-gray-800 rounded-xl overflow-hidden shadow-lg"
                                        >
                                            <!-- Camera Not Active State -->
                                            <div
                                                v-if="!isCameraActive"
                                                class="p-6 text-center"
                                                :class="{ 'p-8': isMaximized }"
                                            >
                                                <div class="mb-5">
                                                    <div
                                                        class="w-16 h-16 mx-auto bg-blue-600 rounded-full flex items-center justify-center mb-4 shadow-md"
                                                        :class="{
                                                            'w-20 h-20 mb-5':
                                                                isMaximized,
                                                        }"
                                                    >
                                                        <v-icon
                                                            name="hi-camera"
                                                            class="w-8 h-8 text-white"
                                                            :class="{
                                                                'w-10 h-10':
                                                                    isMaximized,
                                                            }"
                                                        />
                                                    </div>
                                                    <p
                                                        class="text-gray-300 text-sm"
                                                        :class="{
                                                            'text-base':
                                                                isMaximized,
                                                        }"
                                                    >
                                                        Capture photos or videos
                                                        to document the issue
                                                    </p>
                                                </div>
                                                <button
                                                    type="button"
                                                    @click="initializeCamera"
                                                    :disabled="isCameraLoading"
                                                    class="inline-flex items-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-medium shadow-md transition-all"
                                                    :class="{
                                                        'px-6 py-3 text-base':
                                                            isMaximized,
                                                    }"
                                                >
                                                    <v-icon
                                                        v-if="isCameraLoading"
                                                        name="bi-arrow-repeat"
                                                        class="animate-spin mr-2 h-4 w-4"
                                                        :class="{
                                                            'h-5 w-5 mr-3':
                                                                isMaximized,
                                                        }"
                                                    />
                                                    <v-icon
                                                        v-else
                                                        name="hi-camera"
                                                        class="mr-2 h-4 w-4"
                                                        :class="{
                                                            'h-5 w-5 mr-3':
                                                                isMaximized,
                                                        }"
                                                    />
                                                    {{
                                                        isCameraLoading
                                                            ? "Starting Camera..."
                                                            : "Open Camera"
                                                    }}
                                                </button>
                                            </div>

                                            <!-- Active Camera View -->
                                            <div v-else class="relative">
                                                <div
                                                    class="bg-black"
                                                    style="aspect-ratio: 4/3"
                                                >
                                                    <video
                                                        ref="videoElement"
                                                        class="w-full h-full object-cover"
                                                        autoplay
                                                        playsinline
                                                        muted
                                                    ></video>
                                                    <div
                                                        v-if="!isCameraReady"
                                                        class="absolute inset-0 bg-black bg-opacity-75 flex items-center justify-center"
                                                    >
                                                        <div
                                                            class="text-center text-white"
                                                        >
                                                            <div
                                                                class="inline-block animate-spin w-8 h-8 border-2 border-white border-t-transparent rounded-full mb-3"
                                                            ></div>
                                                            <p class="text-sm">
                                                                {{
                                                                    cameraStatus
                                                                }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div
                                                        v-if="isCameraReady"
                                                        class="absolute top-3 left-3 bg-green-500 text-white px-2 py-1 rounded-full text-xs shadow-md"
                                                        :class="{
                                                            'top-4 left-4 px-3 py-1.5 text-sm':
                                                                isMaximized,
                                                        }"
                                                    >
                                                        LIVE
                                                    </div>
                                                    <div
                                                        v-if="isRecording"
                                                        class="absolute top-3 right-3 bg-red-500 text-white px-2 py-1 rounded-full text-xs shadow-md flex items-center"
                                                        :class="{
                                                            'top-4 right-4 px-3 py-1.5 text-sm':
                                                                isMaximized,
                                                        }"
                                                    >
                                                        <div
                                                            class="w-2 h-2 bg-white rounded-full mr-1 animate-pulse"
                                                        ></div>
                                                        REC
                                                        {{
                                                            formatTime(
                                                                recordingTime
                                                            )
                                                        }}
                                                    </div>
                                                </div>
                                                <div
                                                    class="bg-gray-900 p-4 flex justify-center gap-4"
                                                    :class="{
                                                        'p-5 gap-5':
                                                            isMaximized,
                                                    }"
                                                >
                                                    <button
                                                        type="button"
                                                        @click="capturePhoto"
                                                        :disabled="
                                                            !isCameraReady ||
                                                            form.photos.filter(
                                                                (file) =>
                                                                    file.type.startsWith(
                                                                        'image'
                                                                    )
                                                            ).length >=
                                                                MAX_PHOTOS ||
                                                            isCapturing ||
                                                            isRecording
                                                        "
                                                        class="p-3 bg-blue-500 hover:bg-blue-600 rounded-full text-white shadow-md transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                                                        :class="{
                                                            'p-4': isMaximized,
                                                        }"
                                                    >
                                                        <v-icon
                                                            name="hi-camera"
                                                            class="w-6 h-6"
                                                            :class="{
                                                                'w-7 h-7':
                                                                    isMaximized,
                                                            }"
                                                        />
                                                    </button>
                                                    <button
                                                        type="button"
                                                        @click="
                                                            isRecording
                                                                ? stopVideoRecording()
                                                                : startVideoRecording()
                                                        "
                                                        :disabled="
                                                            !isCameraReady ||
                                                            (form.photos.filter(
                                                                (file) =>
                                                                    file.type.startsWith(
                                                                        'video'
                                                                    )
                                                            ).length >=
                                                                MAX_VIDEOS &&
                                                                !isRecording)
                                                        "
                                                        :class="[
                                                            'p-3 rounded-full text-white shadow-md transition-all disabled:opacity-50 disabled:cursor-not-allowed bg-red-500 hover:bg-red-600',
                                                            {
                                                                'p-4': isMaximized,
                                                            },
                                                        ]"
                                                    >
                                                        <div
                                                            v-if="isRecording"
                                                            class="w-5 h-5 bg-white rounded-sm"
                                                        ></div>
                                                        <v-icon
                                                            v-else
                                                            name="hi-video-camera"
                                                            class="w-6 h-6"
                                                            :class="{
                                                                'w-7 h-7':
                                                                    isMaximized,
                                                            }"
                                                        />
                                                    </button>
                                                    <button
                                                        type="button"
                                                        @click="stopCamera"
                                                        :disabled="isRecording"
                                                        class="p-3 bg-red-600 hover:bg-red-700 rounded-full text-white shadow-md transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                                                        :class="{
                                                            'p-4': isMaximized,
                                                        }"
                                                    >
                                                        <v-icon
                                                            name="hi-solid-x"
                                                            class="w-6 h-6"
                                                            :class="{
                                                                'w-7 h-7':
                                                                    isMaximized,
                                                            }"
                                                        />
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Media Gallery -->
                                        <div
                                            v-if="
                                                form.photo_previews.length > 0
                                            "
                                            class="mt-5"
                                        >
                                            <div
                                                class="flex items-center justify-between mb-3"
                                            >
                                                <h4
                                                    class="text-sm font-semibold text-gray-900"
                                                    :class="{
                                                        'text-base':
                                                            isMaximized,
                                                    }"
                                                >
                                                    Captured Media ({{
                                                        form.photo_previews
                                                            .length
                                                    }})
                                                </h4>
                                                <button
                                                    type="button"
                                                    @click="clearAllMedia"
                                                    class="text-xs text-red-600 hover:text-red-700 flex items-center"
                                                    :class="{
                                                        'text-sm': isMaximized,
                                                    }"
                                                >
                                                    <v-icon
                                                        name="hi-trash"
                                                        class="mr-1"
                                                    />
                                                    Clear All
                                                </button>
                                            </div>
                                            <div
                                                class="grid grid-cols-3 gap-3"
                                                :class="{
                                                    'grid-cols-4 gap-4':
                                                        isMaximized,
                                                }"
                                            >
                                                <div
                                                    v-for="(
                                                        preview, index
                                                    ) in form.photo_previews"
                                                    :key="'media-' + index"
                                                    class="relative group aspect-square"
                                                >
                                                    <div
                                                        class="absolute -top-2 -right-2 z-10"
                                                    >
                                                        <span
                                                            v-if="
                                                                form.photos[
                                                                    index
                                                                ].type.startsWith(
                                                                    'image'
                                                                )
                                                            "
                                                            class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full"
                                                            :class="{
                                                                'text-sm px-3 py-1.5':
                                                                    isMaximized,
                                                            }"
                                                        >
                                                            Photo
                                                        </span>
                                                        <span
                                                            v-else
                                                            class="bg-purple-500 text-white text-xs px-2 py-1 rounded-full"
                                                            :class="{
                                                                'text-sm px-3 py-1.5':
                                                                    isMaximized,
                                                            }"
                                                        >
                                                            Video
                                                        </span>
                                                    </div>
                                                    <img
                                                        v-if="
                                                            form.photos[
                                                                index
                                                            ].type.startsWith(
                                                                'image'
                                                            )
                                                        "
                                                        :src="preview"
                                                        class="w-full h-full object-cover rounded-lg border border-gray-200 shadow-sm"
                                                    />
                                                    <video
                                                        v-else
                                                        :src="preview"
                                                        class="w-full h-full object-cover rounded-lg border border-gray-200 shadow-sm"
                                                        muted
                                                        preload="metadata"
                                                    ></video>
                                                    <button
                                                        @click="
                                                            removeMedia(index)
                                                        "
                                                        class="absolute top-1 right-1 bg-red-500 p-1 rounded-full text-white opacity-0 group-hover:opacity-100 transition-opacity shadow-md"
                                                        :class="{
                                                            'top-2 right-2 p-1.5':
                                                                isMaximized,
                                                        }"
                                                    >
                                                        <v-icon
                                                            name="hi-trash"
                                                            class="w-3 h-3"
                                                            :class="{
                                                                'w-4 h-4':
                                                                    isMaximized,
                                                            }"
                                                        />
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <p
                                            v-if="form.errors.photos"
                                            class="mt-2 text-xs text-red-600 flex items-center"
                                            :class="{
                                                'text-sm mt-3': isMaximized,
                                            }"
                                        >
                                            <v-icon
                                                name="bi-exclamation-circle"
                                                class="mr-1"
                                            />
                                            {{ form.errors.photos }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div
                                    class="border-t border-gray-200 px-4 py-5 bg-gray-50 rounded-b-xl"
                                    :class="{ 'px-6 py-6': isMaximized }"
                                >
                                    <button
                                        type="submit"
                                        :disabled="
                                            form.processing ||
                                            isSubmitting ||
                                            isRecording ||
                                            !isFormValid
                                        "
                                        class="w-full inline-flex justify-center items-center rounded-xl border border-transparent shadow-sm px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed"
                                        :class="{
                                            'px-8 py-4 text-lg': isMaximized,
                                        }"
                                    >
                                        <v-icon
                                            v-if="isSubmitting"
                                            name="bi-arrow-repeat"
                                            class="animate-spin mr-2 h-5 w-5"
                                            :class="{
                                                'h-6 w-6 mr-3': isMaximized,
                                            }"
                                        />
                                        <v-icon
                                            v-else-if="isRecording"
                                            name="bi-record-circle"
                                            class="mr-2 h-5 w-5"
                                            :class="{
                                                'h-6 w-6 mr-3': isMaximized,
                                            }"
                                        />
                                        <v-icon
                                            v-else
                                            name="bi-send"
                                            class="mr-2 h-5 w-5"
                                            :class="{
                                                'h-6 w-6 mr-3': isMaximized,
                                            }"
                                        />
                                        <span v-if="isSubmitting"
                                            >Submitting Report...</span
                                        >
                                        <span v-else-if="isRecording"
                                            >Stop recording to submit</span
                                        >
                                        <span v-else>Submit Report</span>
                                    </button>
                                    <p
                                        class="text-xs text-center text-gray-500 mt-3"
                                        :class="{ 'text-sm mt-4': isMaximized }"
                                    >
                                        By submitting this report, you
                                        acknowledge that the information
                                        provided will be reviewed by the
                                        appropriate authorities and may be used
                                        to address water quality issues in your
                                        area.
                                    </p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import { ref, computed, watch, onMounted, onUnmounted, nextTick } from "vue";
import Swal from "sweetalert2";
import { usePage } from "@inertiajs/vue3";

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
    zones: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits([
    "update:isOpen",
    "update:successData",
    "update:showSuccessModal",
]);

// State for maximize
const isMaximized = ref(false);

// Get authenticated user
const { props: pageProps } = usePage();
const user = pageProps.auth?.user;

// Water issue types matching ReportForm.vue and backend
const waterIssueTypes = [
    "Burst pipe",
    "Rusty water",
    "Low water pressure",
    "No water supply",
    "Clogged pipes",
    "Smelly water",
    "Cloudy or dirty water",
    "Hot water issues",
    // "Running toilet (Nagasige’g agas nga kasilyas)",
];

// Form setup with authenticated user's name and fields from ReportForm.vue
const form = useForm({
    municipality: "Clarin",
    province: "Bohol",
    zone: "",
    barangay: "",
    purok: "",
    water_issue_type: "",
    custom_water_issue: "",
    description: "",
    photos: [],
    photo_previews: [],
    reporter_name: user?.name || "Authenticated User",
    reporter_phone: "",
    latitude: null,
    longitude: null,
});

const isSubmitting = ref(false);
const locationStatus = ref("idle"); // 'idle', 'loading', 'success', 'error', 'offline_cached_location', 'offline_default_location'
const locationAccuracy = ref(null);
const lastKnownLocation = ref(null);
const locationWatchId = ref(null);
const locationCacheTimeout = 24 * 60 * 60 * 1000; // 24 hours

// Camera state
const isCameraActive = ref(false);
const isCameraReady = ref(false);
const isCameraLoading = ref(false);
const isCapturing = ref(false);
const isRecording = ref(false);
const recordingTime = ref(0);
const cameraError = ref("");
const cameraStatus = ref("Initializing...");
const videoElement = ref(null);

let stream = null;
let mediaRecorder = null;
let recordedChunks = [];

const MAX_PHOTOS = 3;
const MAX_VIDEOS = 2;
const MAX_TOTAL = MAX_PHOTOS + MAX_VIDEOS;
const MAX_PHOTO_SIZE = 5 * 1024 * 1024; // 5 MB
const MAX_VIDEO_SIZE = 10 * 1024 * 1024; // 10 MB
const MAX_VIDEO_DURATION = 5; // 5 seconds

const allBarangays = computed(() => Object.values(props.zones).flat());
const barangayToZone = computed(() => {
    const mapping = {};
    Object.entries(props.zones).forEach(([zone, barangays]) => {
        barangays.forEach((barangay) => {
            mapping[barangay] = zone;
        });
    });
    return mapping;
});
const hasErrors = computed(() => Object.keys(form.errors).length > 0);

// Enhanced form validation
const isFormValid = computed(() => {
    const basicValidation =
        form.reporter_name &&
        form.barangay &&
        form.purok &&
        form.description &&
        form.water_issue_type &&
        (form.water_issue_type !== "others" || form.custom_water_issue) &&
        form.photos.length > 0;

    const hasValidLocation =
        form.latitude && form.longitude && locationStatus.value !== "error";

    return basicValidation && hasValidLocation;
});

// Reset maximize state when modal is closed
watch(
    () => props.isOpen,
    (newVal) => {
        if (!newVal) {
            isMaximized.value = false;
        }
    }
);

const toggleMaximize = () => {
    isMaximized.value = !isMaximized.value;
};

const formatTime = (seconds) => {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${secs.toString().padStart(2, "0")}`;
};

// ==================== ENHANCED GPS LOCATION SYSTEM ====================

const loadCachedLocation = () => {
    try {
        const cached = localStorage.getItem("lastKnownLocation");
        if (cached) {
            const location = JSON.parse(cached);
            const cacheAge = Date.now() - location.timestamp;

            if (cacheAge < locationCacheTimeout) {
                lastKnownLocation.value = location;
                return location;
            } else {
                localStorage.removeItem("lastKnownLocation");
            }
        }
    } catch (e) {
        console.warn("Failed to load cached location:", e);
    }
    return null;
};

const saveLocationToCache = (coords) => {
    try {
        const locationData = {
            latitude: coords.latitude,
            longitude: coords.longitude,
            accuracy: coords.accuracy,
            timestamp: Date.now(),
        };
        localStorage.setItem("lastKnownLocation", JSON.stringify(locationData));
        lastKnownLocation.value = locationData;
    } catch (e) {
        console.warn("Failed to save location to cache:", e);
    }
};

const useDefaultLocation = () => {
    form.latitude = 9.9616;
    form.longitude = 124.025;
    locationStatus.value = "offline_default_location";
    locationAccuracy.value = null;

    Swal.fire({
        icon: "warning",
        title: "Using Default Location",
        text: "GPS signal unavailable. Using center of Clarin, Bohol.",
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 4000,
    });
};

// Enhanced getLocation function with caching system - MATCHING ReportForm.vue
const getLocation = async () => {
    // Clear any existing tracking first
    stopLocationTracking();

    // Try to use cached location first
    const cachedLocation = loadCachedLocation();
    if (cachedLocation) {
        form.latitude = cachedLocation.latitude;
        form.longitude = cachedLocation.longitude;
        locationAccuracy.value = cachedLocation.accuracy
            ? `${cachedLocation.accuracy.toFixed(1)}m`
            : null;
        locationStatus.value = "offline_cached_location";

        // Start tracking for better accuracy
        startLocationTracking();

        return cachedLocation;
    }

    if (!navigator.geolocation) {
        locationStatus.value = "error";
        useDefaultLocation();
        return null;
    }

    locationStatus.value = "loading";

    return new Promise((resolve) => {
        // CHANGED: Use the SAME settings as ReportForm.vue
        const locationOptions = {
            enableHighAccuracy: false, // ← CHANGED from true to false
            timeout: 10000, // ← CHANGED from 30000 to 10000
            maximumAge: 30000, // ← CHANGED from 0 to 30000
        };

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const coords = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    accuracy: position.coords.accuracy,
                    timestamp: Date.now(),
                };

                console.log("GPS Acquired:", {
                    lat: coords.latitude,
                    lon: coords.longitude,
                    accuracy: coords.accuracy,
                });

                saveLocationToCache(coords);
                form.latitude = coords.latitude;
                form.longitude = coords.longitude;
                locationAccuracy.value = coords.accuracy
                    ? `${coords.accuracy.toFixed(1)}m`
                    : null;
                locationStatus.value = "success";

                // Start continuous tracking
                startLocationTracking();

                Swal.fire({
                    icon: "success",
                    title: "Location Acquired!",
                    text: `Accuracy: ${
                        coords.accuracy
                            ? coords.accuracy.toFixed(1) + "m"
                            : "Good"
                    }`,
                    toast: true,
                    position: "bottom-end",
                    showConfirmButton: false,
                    timer: 4000,
                });

                resolve(coords);
            },
            (error) => {
                console.error("Location error:", error);

                // Try to use any cached location as fallback
                const freshCache = loadCachedLocation();
                if (freshCache) {
                    form.latitude = freshCache.latitude;
                    form.longitude = freshCache.longitude;
                    locationAccuracy.value = freshCache.accuracy
                        ? `${freshCache.accuracy.toFixed(1)}m`
                        : null;
                    locationStatus.value = "offline_cached_location";

                    // Still start tracking for better accuracy
                    startLocationTracking();

                    resolve(freshCache);
                } else {
                    useDefaultLocation();
                    resolve(null);
                }
            },
            locationOptions
        );
    });
};
const startLocationTracking = () => {
    if (!navigator.geolocation) return;

    try {
        if (locationWatchId.value) {
            navigator.geolocation.clearWatch(locationWatchId.value);
        }

        locationWatchId.value = navigator.geolocation.watchPosition(
            (position) => {
                const coords = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    accuracy: position.coords.accuracy,
                    timestamp: Date.now(),
                };

                saveLocationToCache(coords);

                // Always update with better accuracy data
                form.latitude = coords.latitude;
                form.longitude = coords.longitude;
                locationAccuracy.value = coords.accuracy
                    ? `${coords.accuracy.toFixed(1)}m`
                    : null;
                locationStatus.value = "success";

                console.log("GPS Updated:", {
                    lat: coords.latitude,
                    lon: coords.longitude,
                    accuracy: coords.accuracy,
                });
            },
            (error) => {
                console.warn("Location tracking error:", error.message);
            },
            {
                // CHANGED: Use the EXACT SAME settings as ReportForm.vue
                enableHighAccuracy: false, // ← CHANGED from true to false
                maximumAge: 30000, // ← CHANGED from 15000 to 30000
                timeout: 10000, // ← CHANGED from 15000 to 10000
            }
        );
    } catch (error) {
        console.error("Failed to start location tracking:", error);
    }
};

const stopLocationTracking = () => {
    if (locationWatchId.value) {
        navigator.geolocation.clearWatch(locationWatchId.value);
        locationWatchId.value = null;
    }
};

const getLocationStatusIcon = () => {
    switch (locationStatus.value) {
        case "success":
            return "bi-check-circle";
        case "offline_cached_location":
            return "bi-clock-history";
        case "offline_default_location":
            return "bi-geo-alt";
        case "loading":
            return "bi-arrow-repeat";
        case "error":
            return "bi-exclamation-triangle";
        default:
            return "bi-geo";
    }
};

const getLocationStatusText = () => {
    switch (locationStatus.value) {
        case "success":
            return "Live GPS Location";
        case "offline_cached_location":
            return "Cached GPS Location";
        case "offline_default_location":
            return "Default Area Location";
        case "loading":
            return "Acquiring GPS...";
        case "error":
            return "GPS Error";
        default:
            return "Location Unknown";
    }
};

// ==================== SUBMIT REPORT FUNCTION ====================

const submitReport = async () => {
    // Check for required media
    if (form.photos.length === 0) {
        Swal.fire({
            icon: "error",
            title: "Media Required",
            text: "At least one photo or video is required.",
            timer: 3000,
        });
        return;
    }

    // Enhanced location validation
    if (!form.latitude || !form.longitude) {
        Swal.fire({
            icon: "error",
            title: "Location Required",
            text: "Please wait for GPS location to be acquired.",
            timer: 4000,
        });
        getLocation();
        return;
    }

    // Check if recording is in progress
    if (isRecording.value) {
        Swal.fire({
            icon: "warning",
            title: "Recording in Progress",
            text: "Please stop recording before submitting.",
            timer: 3000,
        });
        return;
    }

    // Validate required fields
    if (!form.water_issue_type) {
        Swal.fire({
            icon: "error",
            title: "Issue Type Required",
            text: "Please select a water issue type.",
            timer: 3000,
        });
        return;
    }

    if (form.water_issue_type === "others" && !form.custom_water_issue.trim()) {
        Swal.fire({
            icon: "error",
            title: "Custom Issue Required",
            text: "Please provide a description for the custom water issue.",
            timer: 3000,
        });
        return;
    }

    // Validate other required fields
    if (!form.barangay || !form.purok || !form.description) {
        Swal.fire({
            icon: "error",
            title: "Missing Information",
            text: "Please fill in all required fields.",
            timer: 3000,
        });
        return;
    }

    // If using default location, show confirmation
    if (locationStatus.value === "offline_default_location") {
        const result = await Swal.fire({
            icon: "warning",
            title: "Using Area Center Location",
            text: "GPS signal is weak/unavailable. Your report will use the Clarin, Bohol center location. For more precise location, try moving to an area with better GPS signal.",
            showCancelButton: true,
            confirmButtonText: "Continue with Area Center",
            cancelButtonText: "Try GPS Again",
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#6b7280",
        });

        if (!result.isConfirmed) {
            getLocation();
            return;
        }
    }

    isSubmitting.value = true;

    console.log("Form data before submission:", {
        ...form.data(),
        photos_count: form.photos.length,
        location_status: locationStatus.value,
        location_accuracy: locationAccuracy.value,
    });

    try {
        await form.post(route("reports.store"), {
            preserveScroll: true,
            onSuccess: (response) => {
                isSubmitting.value = false;
                const trackingCode =
                    response.props.trackingCode ||
                    response.props.swal?.trackingCode;

                Swal.fire({
                    toast: true,
                    position: "top-end",
                    icon: "success",
                    message: "Report submitted successfully!",
                    title: `Report submitted (${trackingCode})`,
                    showConfirmButton: false,
                    timer: 3000,
                });

                emit("update:successData", {
                    trackingCode: trackingCode,
                    dateSubmitted: new Date().toISOString(),
                });
                emit("update:showSuccessModal", true);
                closeModal();
            },
            onError: (errors) => {
                isSubmitting.value = false;
                console.error("Form submission errors:", errors);

                let errorMessage = "Please correct the errors and try again.";
                if (errors.message) {
                    errorMessage = errors.message;
                } else if (errors.photos) {
                    errorMessage = errors.photos;
                } else if (errors.description) {
                    errorMessage = errors.description;
                }

                Swal.fire({
                    icon: "error",
                    title: "Submission Failed",
                    text: errorMessage,
                    timer: 4000,
                });
            },
        });
    } catch (error) {
        isSubmitting.value = false;
        console.error("Submission error:", error);
        Swal.fire({
            icon: "error",
            title: "Submission Error",
            text: "An unexpected error occurred. Please try again.",
            timer: 3000,
        });
    }
};

const closeModal = () => {
    stopCamera();
    stopLocationTracking();
    form.reset();
    form.clearErrors();
    locationStatus.value = "idle";
    locationAccuracy.value = null;
    emit("update:isOpen", false);
};

// ==================== CAMERA FUNCTIONS ====================

const initializeCamera = async () => {
    cameraError.value = "";
    isCameraLoading.value = true;
    try {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            throw new Error("Camera not supported by this browser");
        }
        isCameraActive.value = true;
        await nextTick();
        const constraints = { video: true, audio: true };
        stream = await navigator.mediaDevices.getUserMedia(constraints);
        videoElement.value.srcObject = stream;
        isCameraReady.value = true;
    } catch (error) {
        cameraError.value = error.message || "Failed to access camera";
    } finally {
        isCameraLoading.value = false;
    }
};

const retryCamera = () => {
    cameraError.value = "";
    initializeCamera();
};

const stopCamera = () => {
    if (isRecording.value) stopVideoRecording();
    if (stream) {
        stream.getTracks().forEach((track) => track.stop());
        stream = null;
    }
    if (videoElement.value) {
        videoElement.value.srcObject = null;
    }
    isCameraActive.value = false;
    isCameraReady.value = false;
};

const capturePhoto = async () => {
    if (
        !isCameraReady.value ||
        form.photos.filter((file) => file.type.startsWith("image")).length >=
            MAX_PHOTOS ||
        isCapturing.value ||
        isRecording.value
    )
        return;

    isCapturing.value = true;
    try {
        const canvas = document.createElement("canvas");
        canvas.width = videoElement.value.videoWidth;
        canvas.height = videoElement.value.videoHeight;
        const ctx = canvas.getContext("2d");

        ctx.drawImage(videoElement.value, 0, 0);

        const timestamp = new Date().toLocaleString("en-US", {
            year: "numeric",
            month: "short",
            day: "numeric",
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit",
            hour12: true,
        });
        ctx.font = "16px Arial";
        ctx.fillStyle = "white";
        ctx.textAlign = "left";
        ctx.textBaseline = "bottom";
        ctx.fillText(timestamp, 10, canvas.height - 10);

        ctx.fillStyle = "rgba(0, 0, 0, 0.5)";
        ctx.fillRect(
            0,
            canvas.height - 30,
            ctx.measureText(timestamp).width + 20,
            20
        );

        ctx.fillStyle = "white";
        ctx.fillText(timestamp, 10, canvas.height - 10);

        const blob = await new Promise((resolve) =>
            canvas.toBlob(resolve, "image/jpeg", 0.95)
        );
        if (blob.size > MAX_PHOTO_SIZE) throw new Error("Photo size too large");
        const file = new File([blob], `water-report-${Date.now()}.jpg`, {
            type: "image/jpeg",
        });
        form.photos.push(file);
        form.photo_previews.push(URL.createObjectURL(blob));
        Swal.fire({
            toast: true,
            position: "top-end",
            icon: "success",
            title: `Photo ${
                form.photos.filter((file) => file.type.startsWith("image"))
                    .length
            } captured!`,
            timer: 2000,
        });
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Capture Failed",
            text: error.message,
            timer: 3000,
        });
    } finally {
        isCapturing.value = false;
    }
};

const startVideoRecording = async () => {
    if (
        !isCameraReady.value ||
        form.photos.filter((file) => file.type.startsWith("video")).length >=
            MAX_VIDEOS ||
        !stream
    )
        return;

    recordedChunks = [];
    recordingTime.value = 0;
    const options = { mimeType: "video/webm" };
    mediaRecorder = new MediaRecorder(stream, options);

    let recordingInterval = null;

    mediaRecorder.ondataavailable = (event) => {
        if (event.data.size > 0) recordedChunks.push(event.data);
    };

    mediaRecorder.onstop = () => {
        if (recordingInterval) {
            clearInterval(recordingInterval);
        }

        const blob = new Blob(recordedChunks, { type: "video/webm" });
        if (blob.size > MAX_VIDEO_SIZE) {
            Swal.fire({
                icon: "error",
                title: "Video Too Large",
                text: "Video exceeds 25MB limit",
                timer: 3000,
            });
            return;
        }
        const file = new File([blob], `water-report-video-${Date.now()}.webm`, {
            type: "video/webm",
        });
        form.photos.push(file);
        form.photo_previews.push(URL.createObjectURL(blob));
        Swal.fire({
            toast: true,
            position: "top-end",
            icon: "success",
            title: `Video ${
                form.photos.filter((file) => file.type.startsWith("video"))
                    .length
            } recorded!`,
            timer: 2000,
        });
    };

    mediaRecorder.start();
    isRecording.value = true;

    recordingInterval = setInterval(() => {
        recordingTime.value++;
        if (recordingTime.value >= MAX_VIDEO_DURATION) {
            stopVideoRecording();
        }
    }, 1000);
};

const stopVideoRecording = () => {
    if (!isRecording.value || !mediaRecorder) return;
    mediaRecorder.stop();
    isRecording.value = false;
};

const removeMedia = (index) => {
    URL.revokeObjectURL(form.photo_previews[index]);
    form.photos.splice(index, 1);
    form.photo_previews.splice(index, 1);
};

const clearAllMedia = () => {
    Swal.fire({
        title: "Clear All Media?",
        text: `This will remove all ${form.photos.length} media files.`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc2626",
        cancelButtonText: "Cancel",
        confirmButtonText: "Clear All",
    }).then((result) => {
        if (result.isConfirmed) {
            form.photo_previews.forEach((url) => URL.revokeObjectURL(url));
            form.photos = [];
            form.photo_previews = [];
            Swal.fire({
                toast: true,
                position: "top-end",
                icon: "success",
                title: "Media Cleared",
                timer: 2000,
            });
        }
    });
};

const restrictPhoneInput = (event) => {
    let value = event.target.value.replace(/[^0-9]/g, "");
    if (value.length > 11) value = value.slice(0, 11);
    form.reporter_phone = value;
};

// ==================== WATCHERS AND LIFECYCLE ====================

// Watch for barangay changes to auto-fill zone
watch(
    () => form.barangay,
    (newBarangay) => {
        form.zone = barangayToZone.value[newBarangay] || "";
    }
);

// Watch for water issue type changes
watch(
    () => form.water_issue_type,
    (newIssueType) => {
        if (newIssueType !== "others") {
            form.custom_water_issue = "";
        }
    }
);

// Initialize location when modal opens with enhanced system
watch(
    () => props.isOpen,
    (newVal) => {
        if (newVal) {
            // Start location tracking immediately
            startLocationTracking();

            // Small delay to ensure modal is fully rendered, then get location
            setTimeout(() => {
                getLocation();
            }, 500);
        } else {
            isMaximized.value = false;
            stopLocationTracking();
        }
    }
);

onMounted(() => {
    form.reporter_name = user?.name || "Authenticated User";
});

onUnmounted(() => {
    stopCamera();
    stopLocationTracking();
    form.photo_previews.forEach((url) => URL.revokeObjectURL(url));
});
</script>

<style scoped>
.overflow-y-auto {
    scrollbar-width: thin;
    scrollbar-color: #e2e8f0 #f8fafc;
}

.overflow-y-auto::-webkit-scrollbar {
    width: 8px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f8fafc;
    border-radius: 4px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background-color: #cbd5e1;
    border-radius: 4px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background-color: #94a3b8;
}

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
    transform: translateX(100%);
}

.modal-leave-to .transform {
    transform: translateX(100%);
}

@keyframes pulse {
    0%,
    100% {
        opacity: 1;
    }

    50% {
        opacity: 0.7;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
