<template>
    <transition name="modal">
        <div v-if="show" class="fixed inset-0 z-50 overflow-hidden">
            <!-- Overlay -->
            <div
                class="fixed inset-0 bg-black/50 transition-opacity duration-300"
                @click="closeModal"
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
                    <div class="h-full flex flex-col bg-white shadow-xl">
                        <!-- Header -->
                        <div
                            class="flex items-center justify-between px-6 py-4 bg-blue-600 border-b border-blue-500"
                        >
                            <div class="flex items-center space-x-3">
                                <div
                                    class="w-8 h-8 bg-white/20 rounded flex items-center justify-center"
                                >
                                    <svg
                                        class="w-5 h-5 text-white"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                        />
                                    </svg>
                                </div>
                                <span class="text-white font-semibold text-lg"
                                    >Meter Reading Card</span
                                >
                            </div>
                            <div class="flex items-center space-x-2">
                                <!-- Maximize/Minimize Button -->
                                <button
                                    @click="toggleMaximize"
                                    class="text-white hover:bg-white/20 transition-colors duration-200 p-2 rounded"
                                    :title="
                                        isMaximized ? 'Minimize' : 'Maximize'
                                    "
                                >
                                    <svg
                                        class="w-5 h-5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            v-if="!isMaximized"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"
                                        />
                                        <path
                                            v-else
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"
                                        />
                                    </svg>
                                </button>
                                <!-- Close Button -->
                                <button
                                    @click="closeModal"
                                    class="text-white hover:bg-white/20 transition-colors duration-200 p-2 rounded"
                                >
                                    <svg
                                        class="w-6 h-6"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Content -->
                        <div
                            class="flex-1 overflow-y-auto p-6"
                            :class="{ 'p-8': isMaximized }"
                        >
                            <!-- Customer Summary Card -->
                            <div
                                class="bg-blue-50 border border-blue-200 p-5 mb-6"
                                :class="{ 'p-6': isMaximized }"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center gap-4">
                                        <!-- Customer Avatar -->
                                        <div class="flex-shrink-0">
                                            <div
                                                class="w-14 h-14 rounded-lg flex items-center justify-center overflow-hidden border-2 border-white bg-blue-500"
                                                :class="{
                                                    'w-16 h-16': isMaximized,
                                                }"
                                            >
                                                <img
                                                    v-if="user.avatar_url"
                                                    :src="user.avatar_url"
                                                    :alt="
                                                        user.name +
                                                        ' ' +
                                                        user.lastname
                                                    "
                                                    class="w-full h-full object-cover"
                                                    @error="handleImageError"
                                                />
                                                <div
                                                    v-else
                                                    class="w-full h-full flex items-center justify-center text-white font-semibold"
                                                    :class="{
                                                        'text-lg': isMaximized,
                                                        'text-base':
                                                            !isMaximized,
                                                    }"
                                                >
                                                    {{
                                                        getInitials(
                                                            user.name,
                                                            user.lastname
                                                        )
                                                    }}
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <h2
                                                class="font-bold text-gray-900 text-xl mb-1"
                                                :class="{
                                                    'text-2xl': isMaximized,
                                                }"
                                            >
                                                {{ user.name }}
                                                {{ user.lastname }}
                                            </h2>
                                            <p
                                                class="text-gray-600 mb-2"
                                                :class="{
                                                    'text-base': isMaximized,
                                                }"
                                            >
                                                {{ user.address }}
                                            </p>
                                            <div
                                                class="flex gap-4"
                                                :class="{
                                                    'gap-6': isMaximized,
                                                }"
                                            >
                                                <span
                                                    class="flex items-center gap-1 text-sm text-gray-500"
                                                    :class="{
                                                        'text-base':
                                                            isMaximized,
                                                    }"
                                                >
                                                    <svg
                                                        class="w-4 h-4 text-blue-500"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                                        />
                                                    </svg>
                                                    {{ user.account_number }}
                                                </span>
                                                <span
                                                    class="flex items-center gap-1 text-sm text-gray-500"
                                                    :class="{
                                                        'text-base':
                                                            isMaximized,
                                                    }"
                                                >
                                                    <svg
                                                        class="w-4 h-4 text-blue-500"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                                                        />
                                                    </svg>
                                                    {{ user.phone }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div
                                            class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 mb-2"
                                            :class="{
                                                'px-4 py-1.5': isMaximized,
                                            }"
                                        >
                                            <svg
                                                class="w-4 h-4 mr-1"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                                />
                                            </svg>
                                            Active
                                        </div>
                                        <p
                                            class="text-xs text-gray-500"
                                            :class="{ 'text-sm': isMaximized }"
                                        >
                                            Installed:
                                            {{
                                                formatDate(
                                                    user.date_installed
                                                ) || "N/A"
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Meter Details Grid -->
                            <div
                                class="grid grid-cols-2 gap-3 mb-6"
                                :class="{ 'gap-4 grid-cols-4': isMaximized }"
                            >
                                <div
                                    class="bg-gray-50 border border-gray-200 p-3"
                                    :class="{ 'p-4': isMaximized }"
                                >
                                    <div
                                        class="flex items-center gap-2 text-gray-700 mb-1"
                                    >
                                        <svg
                                            class="w-4 h-4 text-blue-500"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"
                                            />
                                        </svg>
                                        <span
                                            class="text-sm font-medium"
                                            :class="{
                                                'text-base': isMaximized,
                                            }"
                                            >Brand</span
                                        >
                                    </div>
                                    <div
                                        class="font-semibold text-gray-900"
                                        :class="{ 'text-lg': isMaximized }"
                                    >
                                        {{ user.brand || "Not specified" }}
                                    </div>
                                </div>

                                <div
                                    class="bg-gray-50 border border-gray-200 p-3"
                                    :class="{ 'p-4': isMaximized }"
                                >
                                    <div
                                        class="flex items-center gap-2 text-gray-700 mb-1"
                                    >
                                        <svg
                                            class="w-4 h-4 text-blue-500"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M8 16l2.879-2.879m0 0a3 3 0 104.243-4.242 3 3 0 00-4.243 4.242zM21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />
                                        </svg>
                                        <span
                                            class="text-sm font-medium"
                                            :class="{
                                                'text-base': isMaximized,
                                            }"
                                            >Serial No.</span
                                        >
                                    </div>
                                    <div
                                        class="font-semibold text-gray-900"
                                        :class="{ 'text-lg': isMaximized }"
                                    >
                                        {{ user.serial_number || "N/A" }}
                                    </div>
                                </div>

                                <div
                                    class="bg-gray-50 border border-gray-200 p-3"
                                    :class="{ 'p-4': isMaximized }"
                                >
                                    <div
                                        class="flex items-center gap-2 text-gray-700 mb-1"
                                    >
                                        <svg
                                            class="w-4 h-4 text-blue-500"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"
                                            />
                                        </svg>
                                        <span
                                            class="text-sm font-medium"
                                            :class="{
                                                'text-base': isMaximized,
                                            }"
                                            >Size</span
                                        >
                                    </div>
                                    <div
                                        class="font-semibold text-gray-900"
                                        :class="{ 'text-lg': isMaximized }"
                                    >
                                        {{ user.size || "N/A" }} mm
                                    </div>
                                </div>

                                <div
                                    class="bg-gray-50 border border-gray-200 p-3"
                                    :class="{ 'p-4': isMaximized }"
                                >
                                    <div
                                        class="flex items-center gap-2 text-gray-700 mb-1"
                                    >
                                        <svg
                                            class="w-4 h-4 text-blue-500"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                            />
                                        </svg>
                                        <span
                                            class="text-sm font-medium"
                                            :class="{
                                                'text-base': isMaximized,
                                            }"
                                            >Last Reading</span
                                        >
                                    </div>
                                    <div
                                        class="font-semibold text-gray-900"
                                        :class="{ 'text-lg': isMaximized }"
                                    >
                                        {{ lastReadingDate || "No records" }}
                                    </div>
                                </div>
                            </div>

                            <!-- Meter Reading Form -->
                            <div
                                class="bg-white border border-gray-200 p-5 mb-6"
                                :class="{ 'p-6': isMaximized }"
                            >
                                <h3
                                    class="text-lg font-semibold text-gray-900 mb-4 flex items-center"
                                    :class="{ 'text-xl mb-5': isMaximized }"
                                >
                                    <svg
                                        class="w-5 h-5 mr-2 text-blue-600"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                        />
                                    </svg>
                                    New Meter Reading
                                </h3>

                                <!-- Year Transition Warning -->
                                <div
                                    v-if="showYearTransitionWarning"
                                    class="bg-yellow-50 border border-yellow-200 p-4 mb-4 flex items-start gap-3"
                                    :class="{ 'p-5': isMaximized }"
                                >
                                    <div
                                        class="p-2 bg-yellow-100 rounded mt-0.5"
                                    >
                                        <svg
                                            class="w-5 h-5 text-yellow-600"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                            />
                                        </svg>
                                    </div>
                                    <div>
                                        <div
                                            class="font-medium text-yellow-800"
                                            :class="{ 'text-lg': isMaximized }"
                                        >
                                            Year Transition Detected
                                        </div>
                                        <p
                                            class="text-sm text-yellow-700 mt-1"
                                            :class="{
                                                'text-base mt-2': isMaximized,
                                            }"
                                        >
                                            You're entering a reading for
                                            January after December. Please
                                            ensure this is correct as it
                                            represents a new billing year.
                                        </p>
                                    </div>
                                </div>

                                <div
                                    class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4"
                                    :class="{
                                        'gap-6 mb-5 md:grid-cols-4':
                                            isMaximized,
                                    }"
                                >
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 mb-1"
                                            :class="{
                                                'text-base': isMaximized,
                                            }"
                                        >
                                            Previous Reading (m³)
                                        </label>
                                        <div class="relative">
                                            <input
                                                v-model="
                                                    newReading.previous_reading
                                                "
                                                type="number"
                                                step="0.01"
                                                :class="[
                                                    'w-full p-2 border border-gray-300 rounded text-gray-900 focus:ring-blue-500 focus:border-blue-500 pr-10',
                                                    hasPreviousReadings
                                                        ? 'bg-gray-50'
                                                        : 'bg-white',
                                                    {
                                                        'p-3 pr-12 text-base':
                                                            isMaximized,
                                                    },
                                                ]"
                                                :disabled="
                                                    hasPreviousReadings ||
                                                    isSubmitting
                                                "
                                                @input="
                                                    calculateConsumptionAndAmount
                                                "
                                                placeholder="Enter previous reading"
                                            />
                                            <span
                                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm"
                                                :class="{
                                                    'text-base right-4':
                                                        isMaximized,
                                                }"
                                                >m³</span
                                            >
                                        </div>
                                        <p
                                            v-if="!hasPreviousReadings"
                                            class="text-xs text-gray-500 mt-1"
                                            :class="{ 'text-sm': isMaximized }"
                                        >
                                            Enter the initial meter reading
                                        </p>
                                        <p
                                            v-else
                                            class="text-xs text-gray-500 mt-1"
                                            :class="{ 'text-sm': isMaximized }"
                                        >
                                            Last recorded reading (automatically
                                            populated)
                                        </p>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 mb-1"
                                            :class="{
                                                'text-base': isMaximized,
                                            }"
                                        >
                                            Current Reading (m³)
                                        </label>
                                        <div class="relative">
                                            <input
                                                v-model="newReading.reading"
                                                type="number"
                                                step="0.01"
                                                class="w-full p-2 border border-gray-300 rounded bg-white text-gray-900 focus:ring-blue-500 focus:border-blue-500 pr-10"
                                                :class="[
                                                    {
                                                        'border-red-300':
                                                            readingError,
                                                        'p-3 pr-12 text-base':
                                                            isMaximized,
                                                    },
                                                ]"
                                                :disabled="isSubmitting"
                                                @input="
                                                    calculateConsumptionAndAmount
                                                "
                                                placeholder="Enter current reading"
                                            />
                                            <span
                                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm"
                                                :class="{
                                                    'text-base right-4':
                                                        isMaximized,
                                                }"
                                                >m³</span
                                            >
                                        </div>
                                        <p
                                            v-if="readingError"
                                            class="text-xs text-red-500 mt-1"
                                            :class="{ 'text-sm': isMaximized }"
                                        >
                                            {{ readingError }}
                                        </p>
                                        <p
                                            v-else
                                            class="text-xs text-gray-500 mt-1"
                                            :class="{ 'text-sm': isMaximized }"
                                        >
                                            Must be higher than previous reading
                                        </p>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 mb-1"
                                            :class="{
                                                'text-base': isMaximized,
                                            }"
                                        >
                                            Consumption (m³)
                                        </label>
                                        <div class="relative">
                                            <input
                                                v-model="newReading.consumption"
                                                type="number"
                                                step="0.01"
                                                class="w-full p-2 border border-gray-300 rounded bg-gray-50 text-gray-900 focus:ring-blue-500 focus:border-blue-500 pr-10"
                                                :class="{
                                                    'p-3 pr-12 text-base':
                                                        isMaximized,
                                                }"
                                                disabled
                                            />
                                            <span
                                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm"
                                                :class="{
                                                    'text-base right-4':
                                                        isMaximized,
                                                }"
                                                >m³</span
                                            >
                                        </div>
                                        <p
                                            class="text-xs text-gray-500 mt-1"
                                            :class="{ 'text-sm': isMaximized }"
                                        >
                                            Calculated automatically
                                        </p>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 mb-1"
                                            :class="{
                                                'text-base': isMaximized,
                                            }"
                                        >
                                            Amount (₱)
                                        </label>
                                        <div class="relative">
                                            <input
                                                v-model="newReading.amount"
                                                type="number"
                                                step="0.01"
                                                class="w-full p-2 border border-gray-300 rounded bg-gray-50 text-gray-900 focus:ring-blue-500 focus:border-blue-500 pr-10"
                                                :class="{
                                                    'p-3 pr-12 text-base':
                                                        isMaximized,
                                                }"
                                                disabled
                                            />
                                            <span
                                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm"
                                                :class="{
                                                    'text-base right-4':
                                                        isMaximized,
                                                }"
                                                >₱</span
                                            >
                                        </div>
                                        <p
                                            class="text-xs text-gray-500 mt-1"
                                            :class="{ 'text-sm': isMaximized }"
                                        >
                                            Based on dynamic water rates
                                        </p>
                                    </div>
                                </div>

                                <!-- Summary Card -->
                                <div
                                    v-if="newReading.consumption > 0"
                                    class="bg-blue-50 border border-blue-200 p-4"
                                    :class="{ 'p-5': isMaximized }"
                                >
                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <div>
                                            <div
                                                class="text-sm text-blue-700 font-medium"
                                                :class="{
                                                    'text-base': isMaximized,
                                                }"
                                            >
                                                Billing Summary
                                            </div>
                                            <div
                                                class="text-xl font-bold text-blue-800 mt-1"
                                                :class="{
                                                    'text-2xl mt-2':
                                                        isMaximized,
                                                }"
                                            >
                                                ₱{{
                                                    newReading.amount.toFixed(2)
                                                }}
                                            </div>
                                            <div
                                                class="text-sm text-blue-600 mt-1"
                                                :class="{
                                                    'text-base mt-2':
                                                        isMaximized,
                                                }"
                                            >
                                                {{ newReading.consumption }} m³
                                                consumption
                                            </div>
                                        </div>
                                        <div class="p-2 bg-blue-100 rounded">
                                            <svg
                                                class="w-6 h-6 text-blue-600"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"
                                                />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Previous Readings Section -->
                            <div
                                class="bg-white border border-gray-200 p-5"
                                :class="{ 'p-6': isMaximized }"
                            >
                                <div
                                    class="flex items-center justify-between mb-4"
                                >
                                    <h3
                                        class="text-lg font-semibold text-gray-900"
                                        :class="{ 'text-xl': isMaximized }"
                                    >
                                        Reading History
                                    </h3>

                                    <!-- Year Filter -->
                                    <div class="flex items-center gap-2">
                                        <svg
                                            class="w-4 h-4 text-gray-500"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"
                                            />
                                        </svg>
                                        <select
                                            v-model="selectedYear"
                                            class="p-2 border border-gray-300 rounded bg-white text-gray-900 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                            :class="{
                                                'p-3 text-base': isMaximized,
                                            }"
                                        >
                                            <option value="">All Years</option>
                                            <option
                                                v-for="year in availableYears"
                                                :key="year"
                                                :value="year"
                                            >
                                                {{ year }}
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div
                                    v-if="isLoadingPreviousReadings"
                                    class="text-center py-8"
                                >
                                    <div
                                        class="inline-flex items-center text-blue-600"
                                    >
                                        <svg
                                            class="animate-spin -ml-1 mr-3 h-5 w-5"
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                        >
                                            <circle
                                                class="opacity-25"
                                                cx="12"
                                                cy="12"
                                                r="10"
                                                stroke="currentColor"
                                                stroke-width="4"
                                            ></circle>
                                            <path
                                                class="opacity-75"
                                                fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                            ></path>
                                        </svg>
                                        <span
                                            :class="{
                                                'text-base': isMaximized,
                                            }"
                                            >Loading reading history...</span
                                        >
                                    </div>
                                </div>

                                <div v-else>
                                    <div
                                        v-if="paginatedReadings.length === 0"
                                        class="text-center py-8 text-gray-500 bg-gray-50 rounded"
                                        :class="{ 'py-10': isMaximized }"
                                    >
                                        <svg
                                            class="w-8 h-8 mb-2 opacity-50 mx-auto"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />
                                        </svg>
                                        <p :class="{ 'text-lg': isMaximized }">
                                            No previous readings found
                                        </p>
                                        <p
                                            class="text-sm mt-1"
                                            :class="{
                                                'text-base mt-2': isMaximized,
                                            }"
                                        >
                                            Start by submitting a new reading
                                            above
                                        </p>
                                    </div>

                                    <!-- Reading History Table -->
                                    <div
                                        v-else
                                        class="overflow-hidden border border-gray-200 rounded"
                                        :class="{ 'border-2': isMaximized }"
                                    >
                                        <!-- Table Header -->
                                        <div
                                            class="grid grid-cols-12 gap-4 px-4 py-3 bg-gray-50 border-b border-gray-200 text-xs font-medium text-gray-700 uppercase tracking-wider"
                                            :class="{
                                                'px-6 py-4 text-sm':
                                                    isMaximized,
                                            }"
                                        >
                                            <div class="col-span-3">Period</div>
                                            <div class="col-span-2 text-right">
                                                Reading
                                            </div>
                                            <div class="col-span-2 text-right">
                                                Consumption
                                            </div>
                                            <div class="col-span-2 text-right">
                                                Amount
                                            </div>
                                            <div class="col-span-2 text-right">
                                                Status
                                            </div>
                                            <div class="col-span-1 text-center">
                                                Actions
                                            </div>
                                        </div>

                                        <!-- Table Body -->
                                        <div class="divide-y divide-gray-200">
                                            <div
                                                v-for="(
                                                    reading, index
                                                ) in paginatedReadings"
                                                :key="reading.id"
                                                class="grid grid-cols-12 gap-4 px-4 py-3 hover:bg-gray-50 transition-colors duration-150 group"
                                                :class="{
                                                    'px-6 py-4': isMaximized,
                                                }"
                                            >
                                                <!-- Period -->
                                                <div class="col-span-3">
                                                    <div
                                                        class="font-medium text-gray-900"
                                                        :class="{
                                                            'text-lg':
                                                                isMaximized,
                                                        }"
                                                    >
                                                        {{
                                                            reading.billing_month
                                                        }}
                                                        {{ reading.year }}
                                                    </div>
                                                    <div
                                                        class="text-sm text-gray-600"
                                                        :class="{
                                                            'text-base':
                                                                isMaximized,
                                                        }"
                                                    >
                                                        {{
                                                            formatDate(
                                                                reading.reading_date
                                                            )
                                                        }}
                                                    </div>
                                                </div>

                                                <!-- Reading -->
                                                <div
                                                    class="col-span-2 text-right"
                                                >
                                                    <div
                                                        class="font-semibold text-blue-600 mt-2"
                                                        :class="{
                                                            'text-lg':
                                                                isMaximized,
                                                        }"
                                                    >
                                                        {{ reading.reading }} m³
                                                    </div>
                                                </div>

                                                <!-- Consumption -->
                                                <div
                                                    class="col-span-2 text-right"
                                                >
                                                    <div
                                                        class="text-gray-700 mt-2"
                                                        :class="{
                                                            'text-lg':
                                                                isMaximized,
                                                        }"
                                                    >
                                                        {{
                                                            reading.consumption
                                                        }}
                                                        m³
                                                    </div>
                                                </div>

                                                <!-- Amount -->
                                                <div
                                                    class="col-span-2 text-right"
                                                >
                                                    <div
                                                        class="font-medium text-green-600 mt-2"
                                                        :class="{
                                                            'text-lg':
                                                                isMaximized,
                                                        }"
                                                    >
                                                        ₱{{
                                                            reading.amount.toFixed(
                                                                2
                                                            )
                                                        }}
                                                    </div>
                                                </div>

                                                <!-- Status -->
                                                <div
                                                    class="col-span-2 text-right mt-2"
                                                >
                                                    <span
                                                        :class="[
                                                            'px-3 py-1 rounded text-xs font-medium transition-all duration-200',
                                                            reading.status ===
                                                            'Paid'
                                                                ? 'bg-green-100 text-green-800 hover:bg-green-200'
                                                                : reading.status ===
                                                                  'Pending'
                                                                ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200'
                                                                : 'bg-red-100 text-red-800 hover:bg-red-200',
                                                            {
                                                                'px-4 py-1.5 text-sm':
                                                                    isMaximized,
                                                            },
                                                        ]"
                                                    >
                                                        {{ reading.status }}
                                                    </span>
                                                </div>

                                                <!-- Actions -->
                                                <div
                                                    class="col-span-1 flex justify-center"
                                                >
                                                    <button
                                                        @click="
                                                            openEditModal(
                                                                reading
                                                            )
                                                        "
                                                        class="p-1.5 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded transition-colors duration-200"
                                                        title="Edit Reading"
                                                    >
                                                        <svg
                                                            class="w-4 h-4"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                                            />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pagination -->
                                    <div
                                        v-if="totalPages > 1"
                                        class="mt-6 border-t border-gray-200 pt-4"
                                    >
                                        <div
                                            class="flex items-center justify-between"
                                        >
                                            <div class="text-sm text-gray-700">
                                                Showing {{ startItem }} to
                                                {{ endItem }} of
                                                {{
                                                    filteredPreviousReadings.length
                                                }}
                                                results
                                            </div>
                                            <div
                                                class="flex items-center space-x-2"
                                            >
                                                <button
                                                    @click="previousPage"
                                                    :disabled="
                                                        currentPage === 1
                                                    "
                                                    class="px-3 py-1 border border-gray-300 rounded text-sm disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
                                                >
                                                    Previous
                                                </button>
                                                <div class="flex space-x-1">
                                                    <button
                                                        v-for="page in visiblePages"
                                                        :key="page"
                                                        @click="goToPage(page)"
                                                        :class="[
                                                            'px-3 py-1 border text-sm rounded',
                                                            currentPage === page
                                                                ? 'bg-blue-600 text-white border-blue-600'
                                                                : 'border-gray-300 text-gray-700 hover:bg-gray-50',
                                                        ]"
                                                    >
                                                        {{ page }}
                                                    </button>
                                                </div>
                                                <button
                                                    @click="nextPage"
                                                    :disabled="
                                                        currentPage ===
                                                        totalPages
                                                    "
                                                    class="px-3 py-1 border border-gray-300 rounded text-sm disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
                                                >
                                                    Next
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div
                            class="border-t border-gray-200 p-6 bg-white"
                            :class="{ 'p-7': isMaximized }"
                        >
                            <div class="flex gap-3 justify-end">
                                <button
                                    @click="submitReading"
                                    type="button"
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed rounded transition-colors duration-200 flex items-center min-w-[120px] justify-center"
                                    :class="{
                                        'px-6 py-3 text-base min-w-[140px]':
                                            isMaximized,
                                    }"
                                    :disabled="isSubmitting || !isFormValid"
                                >
                                    <span
                                        v-if="!isSubmitting"
                                        class="flex items-center"
                                    >
                                        <svg
                                            class="w-4 h-4 mr-1"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M5 13l4 4L19 7"
                                            />
                                        </svg>
                                        Submit Reading
                                    </span>
                                    <span v-else class="flex items-center">
                                        <svg
                                            class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                            :class="{
                                                'h-5 w-5 mr-3': isMaximized,
                                            }"
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                        >
                                            <circle
                                                class="opacity-25"
                                                cx="12"
                                                cy="12"
                                                r="10"
                                                stroke="currentColor"
                                                stroke-width="4"
                                            ></circle>
                                            <path
                                                class="opacity-75"
                                                fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                            ></path>
                                        </svg>
                                        Submitting...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>

    <!-- Edit Reading Modal -->
    <EditMeterRecordModal
        :show="showEditModal"
        :record="selectedReading"
        :user="user"
        @close="closeEditModal"
        @saved="handleReadingUpdated"
    />
</template>

<script setup>
import { ref, computed, onMounted, watch, nextTick } from "vue";
import Swal from "sweetalert2";
import EditMeterRecordModal from "./EditMeterRecordModal.vue";

const props = defineProps({
    show: {
        type: Boolean,
        required: true,
    },
    user: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(["close", "reading-submitted"]);

// State for maximize
const isMaximized = ref(false);

// Pagination state
const currentPage = ref(1);
const itemsPerPage = 3;

const months = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December",
];

const newReading = ref({
    billing_month: "",
    reading_date: "",
    reading: "",
    previous_reading: 0,
    consumption: 0,
    amount: 0,
});

const previousReadings = ref([]);
const selectedYear = ref("");
const availableYears = computed(() => {
    const years = new Set(
        previousReadings.value.map((reading) => reading.year)
    );
    return [...years].sort((a, b) => b - a);
});

const filteredPreviousReadings = computed(() => {
    return sortedPreviousReadings.value.filter(
        (reading) => !selectedYear.value || reading.year === selectedYear.value
    );
});

const sortedPreviousReadings = computed(() => {
    return [...previousReadings.value].sort((a, b) => {
        const dateA = new Date(`${a.billing_month} 1, ${a.year}`);
        const dateB = new Date(`${b.billing_month} 1, ${b.year}`);
        return dateB - dateA;
    });
});

// Pagination computed properties
const totalPages = computed(() => {
    return Math.ceil(filteredPreviousReadings.value.length / itemsPerPage);
});

const paginatedReadings = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return filteredPreviousReadings.value.slice(start, end);
});

const startItem = computed(() => {
    return (currentPage.value - 1) * itemsPerPage + 1;
});

const endItem = computed(() => {
    const end = currentPage.value * itemsPerPage;
    return end > filteredPreviousReadings.value.length
        ? filteredPreviousReadings.value.length
        : end;
});

const visiblePages = computed(() => {
    const pages = [];
    const total = totalPages.value;
    let start = Math.max(1, currentPage.value - 1);
    let end = Math.min(total, currentPage.value + 1);

    if (currentPage.value === 1) {
        end = Math.min(total, 3);
    } else if (currentPage.value === total) {
        start = Math.max(1, total - 2);
    }

    for (let i = start; i <= end; i++) {
        pages.push(i);
    }
    return pages;
});

const isLoadingPreviousReadings = ref(false);
const isSubmitting = ref(false);
const showYearTransitionWarning = ref(false);

// Edit modal state
const showEditModal = ref(false);
const selectedReading = ref(null);

// Water rates state
const waterRates = ref([]);

// Reset maximize state when modal is closed
watch(
    () => props.show,
    (newVal) => {
        if (newVal) {
            initializeForm();
        } else {
            isMaximized.value = false;
        }
    }
);

// Reset pagination when year filter changes
watch(selectedYear, () => {
    currentPage.value = 1;
});

const toggleMaximize = () => {
    isMaximized.value = !isMaximized.value;
};

// Computed property to check if previous readings exist
const hasPreviousReadings = computed(() => {
    return previousReadings.value && previousReadings.value.length > 0;
});

// Computed property for last reading date
const lastReadingDate = computed(() => {
    if (previousReadings.value.length === 0) return null;
    const latest = sortedPreviousReadings.value[0];
    return formatDate(latest.reading_date);
});

// Computed property for reading validation error
const readingError = computed(() => {
    if (!newReading.value.reading) return "";
    const current = parseFloat(newReading.value.reading);
    const previous = parseFloat(newReading.value.previous_reading);

    if (current < previous) {
        return `Current reading must be higher than previous reading (${previous} m³)`;
    }

    return "";
});

// Watch for changes in previous readings to update the previous reading value
watch(
    previousReadings,
    (newVal) => {
        if (newVal.length > 0) {
            updatePreviousReading();
        } else {
            newReading.value.previous_reading = 0;
        }
    },
    { deep: true }
);

// Pagination methods
const previousPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
    }
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
    }
};

const goToPage = (page) => {
    currentPage.value = page;
};

const updatePreviousReading = () => {
    if (!newReading.value.billing_month) {
        newReading.value.previous_reading = 0;
        return;
    }

    const selectedDate = new Date(newReading.value.reading_date);

    // Find the most recent reading before the selected date
    const readingsBeforeCurrent = previousReadings.value.filter((reading) => {
        const readingDate = new Date(
            `${reading.billing_month} 1, ${reading.year}`
        );
        return readingDate < selectedDate;
    });

    // Check for year transition (December to January)
    const hasDecemberReading = previousReadings.value.some(
        (r) =>
            r.billing_month === "December" &&
            parseInt(r.year) === selectedDate.getFullYear() - 1
    );
    showYearTransitionWarning.value =
        months.indexOf(newReading.value.billing_month) === 0 &&
        hasDecemberReading;

    if (readingsBeforeCurrent.length > 0) {
        // Get the latest reading before the selected date
        const latestReading = readingsBeforeCurrent.reduce(
            (latest, current) => {
                const latestDate = new Date(
                    `${latest.billing_month} 1, ${latest.year}`
                );
                const currentDate = new Date(
                    `${current.billing_month} 1, ${current.year}`
                );
                return currentDate > latestDate ? current : latest;
            }
        );

        newReading.value.previous_reading = latestReading.reading;
    } else {
        // If no readings before selected date, use the first reading
        const firstReading = [...previousReadings.value].sort((a, b) => {
            const dateA = new Date(`${a.billing_month} 1, ${a.year}`);
            const dateB = new Date(`${b.billing_month} 1, ${b.year}`);
            return dateA - dateB;
        })[0];

        newReading.value.previous_reading = firstReading
            ? firstReading.reading
            : 0;
    }

    // Recalculate consumption and amount
    calculateConsumptionAndAmount();
};

// Handle image loading errors
const handleImageError = (event) => {
    event.target.style.display = "none";
};

const formatDate = (dateString) => {
    if (!dateString) return "N/A";
    try {
        const date = new Date(dateString);
        return date.toLocaleDateString("en-US", {
            year: "numeric",
            month: "short",
            day: "numeric",
        });
    } catch (error) {
        return "Invalid date";
    }
};

const isFormValid = computed(() => {
    const current = parseFloat(newReading.value.reading) || 0;
    const previous = parseFloat(newReading.value.previous_reading) || 0;

    return (
        newReading.value.billing_month &&
        newReading.value.reading_date &&
        newReading.value.reading !== "" &&
        current >= previous &&
        previous >= 0
    );
});

const closeModal = () => {
    emit("close");
};

// Update the fetchWaterRates method in your Vue component
const fetchWaterRates = async () => {
    try {
        const response = await axios.get(route("staff.water-rates"));
        if (
            response.data &&
            response.data.waterRates &&
            response.data.waterRates.length > 0
        ) {
            waterRates.value = response.data.waterRates;
            return true; // Success
        } else {
            // No water rates configured
            console.error("No water rates configured in system");
            await Swal.fire({
                icon: "error",
                title: "No Water Rates Configured",
                text: "Please contact administrator to set up water rates before submitting readings.",
                confirmButtonText: "OK",
            });
            return false; // Failure
        }
    } catch (error) {
        console.error("Error fetching water rates:", error);
        await Swal.fire({
            icon: "error",
            title: "Failed to Load Water Rates",
            text: "Unable to fetch water rates configuration. Please try again or contact administrator.",
            confirmButtonText: "OK",
        });
        return false; // Failure
    }
};

// Default rates fallback
// const getDefaultRates = () => {
//     return [
//         {
//             min_consumption: 1,
//             max_consumption: 10,
//             rate_per_cubic: 0,
//             fixed_charge: 132.0,
//         },
//         {
//             min_consumption: 11,
//             max_consumption: 20,
//             rate_per_cubic: 14.0,
//             fixed_charge: 0,
//         },
//         {
//             min_consumption: 21,
//             max_consumption: 30,
//             rate_per_cubic: 14.85,
//             fixed_charge: 0,
//         },
//         {
//             min_consumption: 31,
//             max_consumption: 40,
//             rate_per_cubic: 16.0,
//             fixed_charge: 0,
//         },
//         {
//             min_consumption: 41,
//             max_consumption: null,
//             rate_per_cubic: 17.25,
//             fixed_charge: 0,
//         },
//     ];
// };

// Dynamic bill calculation using water rates
const calculateBillAmount = (consumption) => {
    if (consumption <= 0) return 0;

    // If no water rates are available, return 0 and show error
    if (!waterRates.value || waterRates.value.length === 0) {
        console.error("No water rates available for calculation");
        return 0;
    }

    const rates = waterRates.value;
    let totalAmount = 0;
    let remainingConsumption = consumption;

    // Sort rates by min_consumption to ensure correct order
    const sortedRates = [...rates].sort(
        (a, b) => a.min_consumption - b.min_consumption
    );

    for (const rate of sortedRates) {
        if (remainingConsumption <= 0) break;

        let tierConsumption = remainingConsumption;

        // If this tier has a max consumption limit
        if (rate.max_consumption !== null) {
            const availableInTier =
                rate.max_consumption - rate.min_consumption + 1;
            tierConsumption = Math.min(remainingConsumption, availableInTier);
        }

        // Add fixed charge for this tier if applicable
        if (tierConsumption > 0 && rate.fixed_charge > 0) {
            totalAmount += rate.fixed_charge;
        }

        // Calculate consumption charge
        totalAmount += tierConsumption * rate.rate_per_cubic;
        remainingConsumption -= tierConsumption;
    }

    return parseFloat(totalAmount.toFixed(2));
};
const calculateConsumptionAndAmount = () => {
    const currentReading = parseFloat(newReading.value.reading) || 0;
    const previousReading = parseFloat(newReading.value.previous_reading) || 0;

    // Calculate consumption
    newReading.value.consumption = Math.max(
        0,
        currentReading - previousReading
    );

    // Calculate amount using dynamic water rates
    if (newReading.value.consumption > 0) {
        newReading.value.amount = calculateBillAmount(
            newReading.value.consumption
        );
    } else {
        newReading.value.amount = 0;
    }
};

const fetchPreviousReadings = async () => {
    isLoadingPreviousReadings.value = true;
    try {
        previousReadings.value = [];

        const response = await axios.get(
            route("staff.reading.previous", { userId: props.user.id }),
            {
                params: {
                    _t: new Date().getTime(),
                },
            }
        );

        if (response.data.error) {
            if (response.status === 404) {
                Swal.fire({
                    icon: "error",
                    title: "Customer not found",
                    text: "The customer record could not be located",
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Error loading readings",
                    text: "There was a problem loading the previous meter readings",
                });
            }
            previousReadings.value = [];
        } else {
            previousReadings.value = [...(response.data || [])];
        }
    } catch (error) {
        console.error("Error fetching previous readings:", error);
        Swal.fire({
            icon: "error",
            title: "Failed to load readings",
            text: "Please try again later",
        });
        previousReadings.value = [];
    } finally {
        isLoadingPreviousReadings.value = false;
    }
};

const submitReading = async () => {
    if (!isFormValid.value) return;

    // Check if we have water rates
    if (!waterRates.value || waterRates.value.length === 0) {
        await Swal.fire({
            icon: "error",
            title: "No Water Rates",
            text: "Water rates are not configured. Please contact administrator.",
            confirmButtonText: "OK",
        });
        return;
    }

    const current = parseFloat(newReading.value.reading);
    const previous = parseFloat(newReading.value.previous_reading);

    if (current < previous) {
        Swal.fire({
            icon: "error",
            title: "Invalid Reading",
            text: "Current reading cannot be less than previous reading",
        });
        return;
    }

    // Additional validation for year transition
    const selectedDate = new Date(newReading.value.reading_date);
    const selectedYear = selectedDate.getFullYear();
    const currentMonthIndex = months.indexOf(newReading.value.billing_month);
    const hasDecemberReading = previousReadings.value.some(
        (r) =>
            r.billing_month === "December" &&
            parseInt(r.year) === selectedYear - 1
    );

    if (currentMonthIndex === 0 && hasDecemberReading) {
        const result = await Swal.fire({
            title: "Year Transition Confirmation",
            html: `You're entering a reading for <strong>January ${selectedYear}</strong> after a <strong>December ${
                selectedYear - 1
            }</strong> reading.<br><br>
                   This indicates a new billing year. Is this correct?`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, new year",
            cancelButtonText: "No, cancel",
        });

        if (!result.isConfirmed) {
            return;
        }
    }

    isSubmitting.value = true;
    try {
        const result = await Swal.fire({
            title: "Confirm Submission",
            html: `Submit meter reading for ${props.user.name} ${
                props.user.lastname
            }?<br>
                   <strong>Billing Month:</strong> ${
                       newReading.value.billing_month
                   }<br>
                   <strong>Reading:</strong> ${newReading.value.reading} m³<br>
                   ${
                       newReading.value.consumption > 0
                           ? `<strong>Consumption:</strong> ${newReading.value.consumption} m³<br>`
                           : ""
                   }
                   ${
                       newReading.value.amount > 0
                           ? `<strong>Amount:</strong> ₱${newReading.value.amount.toFixed(
                                 2
                             )}`
                           : ""
                   }`,
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, submit it!",
            cancelButtonText: "Cancel",
        });

        if (result.isConfirmed) {
            const response = await axios.post(route("staff.reading.store"), {
                user_id: props.user.id,
                billing_month: newReading.value.billing_month,
                reading_date: newReading.value.reading_date,
                reading: newReading.value.reading,
                previous_reading: newReading.value.previous_reading,
            });

            if (response.data.error) {
                throw new Error(response.data.error);
            }

            // Clear the current reading input but keep other values
            newReading.value.reading = "";
            newReading.value.consumption = 0;
            newReading.value.amount = 0;
            showYearTransitionWarning.value = false;

            // Force a complete refresh of previous readings
            await fetchPreviousReadings();

            // Wait for Vue to update the DOM with new data
            await nextTick();

            // Update the previous reading for the next entry
            updatePreviousReading();

            // Show success message
            await Swal.fire({
                icon: "success",
                title: "Success!",
                text: "Meter reading submitted successfully",
                timer: 2000,
                showConfirmButton: false,
            });

            // Emit event to parent to refresh search results
            emit("reading-submitted");
        }
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Submission Failed",
            text:
                error.response?.data?.error ||
                error.message ||
                "There was an error submitting the meter reading",
        });
    } finally {
        isSubmitting.value = false;
    }
};

// Helper function to get user initials
const getInitials = (firstName, lastName) => {
    const first = firstName ? firstName.charAt(0).toUpperCase() : "";
    const last = lastName ? lastName.charAt(0).toUpperCase() : "";
    return first + last;
};

// Edit modal functions
const openEditModal = (reading) => {
    selectedReading.value = reading;
    showEditModal.value = true;
};

const closeEditModal = () => {
    showEditModal.value = false;
    selectedReading.value = null;
};

const handleReadingUpdated = async (updatedReading) => {
    // Refresh the entire readings list to get updated data
    await fetchPreviousReadings();

    // Update the previous reading calculation
    updatePreviousReading();

    closeEditModal();
};

const initializeForm = async () => {
    // Fetch water rates first - if fails, don't proceed
    const ratesLoaded = await fetchWaterRates();
    if (!ratesLoaded) {
        // Close modal if no rates are configured
        emit("close");
        return;
    }

    // Then fetch previous readings
    await fetchPreviousReadings();

    const today = new Date();
    const currentMonth = months[today.getMonth()];

    newReading.value = {
        billing_month: currentMonth,
        reading_date: today.toISOString().split("T")[0],
        reading: "",
        previous_reading: hasPreviousReadings.value ? 0 : "",
        consumption: 0,
        amount: 0,
    };

    // Update the previous reading based on fetched data
    updatePreviousReading();

    // Set default year filter to current year
    selectedYear.value = today.getFullYear().toString();

    // Reset to first page
    currentPage.value = 1;
};

onMounted(() => {
    if (props.show) {
        initializeForm();
    }
});
</script>

<style scoped>
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

/* Custom scrollbar */
.overflow-y-auto {
    scrollbar-width: thin;
    scrollbar-color: #e2e8f0 #f8fafc;
}

.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f8fafc;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background-color: #e2e8f0;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background-color: #cbd5e1;
}
</style>
