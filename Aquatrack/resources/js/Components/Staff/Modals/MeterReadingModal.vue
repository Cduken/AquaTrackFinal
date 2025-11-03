<template>
    <transition name="modal">
        <div v-if="show" class="fixed inset-0 z-[1000] overflow-hidden">
            <!-- Overlay -->
            <div
                class="absolute inset-0 bg-black/50 transition-opacity duration-300"
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
                    <div
                        class="h-full flex flex-col bg-white dark:bg-gray-800 shadow-xl"
                    >
                        <!-- Header -->
                        <div
                            class="flex items-center justify-between px-6 py-5 bg-[#062F64]"
                        >
                            <div class="flex items-center space-x-2">
                                <v-icon
                                    name="bi-speedometer2"
                                    class="text-amber-300"
                                    scale="1.5"
                                />
                                <span class="text-white font-[200] text-xl"
                                    >Meter Reading Card</span
                                >
                            </div>
                            <div class="flex items-center space-x-2">
                                <!-- Maximize/Minimize Button -->
                                <button
                                    @click="toggleMaximize"
                                    class="text-white hover:text-gray-200 transition-colors duration-200 p-1 rounded-full hover:bg-white/10"
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
                                    class="text-white hover:text-gray-200 transition-colors duration-200 p-1 rounded-full hover:bg-white/10"
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
                            <!-- Customer Summary Card -->
                            <div
                                class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 p-5 rounded-lg mb-6"
                                :class="{ 'p-6': isMaximized }"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center gap-4">
                                        <!-- Customer Avatar -->
                                        <div class="flex-shrink-0">
                                            <div
                                                class="w-14 h-14 rounded-xl flex items-center justify-center overflow-hidden border-2 border-white shadow-lg bg-gradient-to-br from-blue-500 to-blue-600"
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
                                                class="text-xl font-bold text-gray-800 dark:text-white"
                                                :class="{
                                                    'text-2xl': isMaximized,
                                                }"
                                            >
                                                {{ user.name }}
                                                {{ user.lastname }}
                                            </h2>
                                            <p
                                                class="text-gray-600 dark:text-gray-300 mt-1"
                                                :class="{
                                                    'text-base mt-2':
                                                        isMaximized,
                                                }"
                                            >
                                                {{ user.address }}
                                            </p>
                                            <div
                                                class="flex gap-4 mt-2"
                                                :class="{
                                                    'gap-6 mt-3': isMaximized,
                                                }"
                                            >
                                                <span
                                                    class="flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400"
                                                    :class="{
                                                        'text-base':
                                                            isMaximized,
                                                    }"
                                                >
                                                    <v-icon
                                                        name="bi-credit-card"
                                                        class="text-blue-500"
                                                    />
                                                    {{ user.account_number }}
                                                </span>
                                                <span
                                                    class="flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400"
                                                    :class="{
                                                        'text-base':
                                                            isMaximized,
                                                    }"
                                                >
                                                    <v-icon
                                                        name="bi-telephone"
                                                        class="text-blue-500"
                                                    />
                                                    {{ user.phone }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 mb-2"
                                            :class="{
                                                'px-4 py-1.5 text-base':
                                                    isMaximized,
                                            }"
                                        >
                                            <v-icon
                                                name="bi-check-circle"
                                                class="mr-1"
                                            />
                                            Active
                                        </div>
                                        <p
                                            class="text-xs text-gray-500 dark:text-gray-400"
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
                                :class="{
                                    'gap-4': isMaximized,
                                    'grid-cols-4': isMaximized,
                                }"
                            >
                                <div
                                    class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg border border-blue-100 dark:border-blue-700"
                                    :class="{ 'p-4': isMaximized }"
                                >
                                    <div
                                        class="flex items-center gap-2 text-blue-600 dark:text-blue-400 mb-1"
                                    >
                                        <v-icon
                                            name="bi-tag"
                                            class="text-blue-500"
                                        />
                                        <span
                                            class="text-xs font-medium"
                                            :class="{ 'text-sm': isMaximized }"
                                            >Brand</span
                                        >
                                    </div>
                                    <div
                                        class="text-sm font-semibold text-gray-800 dark:text-white"
                                        :class="{ 'text-base': isMaximized }"
                                    >
                                        {{ user.brand || "Not specified" }}
                                    </div>
                                </div>

                                <div
                                    class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg border border-blue-100 dark:border-blue-700"
                                    :class="{ 'p-4': isMaximized }"
                                >
                                    <div
                                        class="flex items-center gap-2 text-blue-600 dark:text-blue-400 mb-1"
                                    >
                                        <v-icon
                                            name="bi-upc-scan"
                                            class="text-blue-500"
                                        />
                                        <span
                                            class="text-xs font-medium"
                                            :class="{ 'text-sm': isMaximized }"
                                            >Serial No.</span
                                        >
                                    </div>
                                    <div
                                        class="text-sm font-semibold text-gray-800 dark:text-white"
                                        :class="{ 'text-base': isMaximized }"
                                    >
                                        {{ user.serial_number || "N/A" }}
                                    </div>
                                </div>

                                <div
                                    class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg border border-blue-100 dark:border-blue-700"
                                    :class="{ 'p-4': isMaximized }"
                                >
                                    <div
                                        class="flex items-center gap-2 text-blue-600 dark:text-blue-400 mb-1"
                                    >
                                        <v-icon
                                            name="bi-rulers"
                                            class="text-blue-500"
                                        />
                                        <span
                                            class="text-xs font-medium"
                                            :class="{ 'text-sm': isMaximized }"
                                            >Size</span
                                        >
                                    </div>
                                    <div
                                        class="text-sm font-semibold text-gray-800 dark:text-white"
                                        :class="{ 'text-base': isMaximized }"
                                    >
                                        {{ user.size || "N/A" }} mm
                                    </div>
                                </div>

                                <div
                                    class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg border border-blue-100 dark:border-blue-700"
                                    :class="{ 'p-4': isMaximized }"
                                >
                                    <div
                                        class="flex items-center gap-2 text-blue-600 dark:text-blue-400 mb-1"
                                    >
                                        <v-icon
                                            name="bi-calendar-check"
                                            class="text-blue-500"
                                        />
                                        <span
                                            class="text-xs font-medium"
                                            :class="{ 'text-sm': isMaximized }"
                                            >Last Reading</span
                                        >
                                    </div>
                                    <div
                                        class="text-sm font-semibold text-gray-800 dark:text-white"
                                        :class="{ 'text-base': isMaximized }"
                                    >
                                        {{ lastReadingDate || "No records" }}
                                    </div>
                                </div>
                            </div>

                            <!-- Meter Reading Form -->
                            <div
                                class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-5 mb-6"
                                :class="{ 'p-6': isMaximized }"
                            >
                                <h3
                                    class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center"
                                    :class="{ 'text-xl mb-5': isMaximized }"
                                >
                                    <v-icon
                                        name="bi-speedometer2"
                                        class="mr-2"
                                        :scale="isMaximized ? '1.1' : '1'"
                                    />
                                    New Meter Reading
                                </h3>

                                <!-- Year Transition Warning -->
                                <div
                                    v-if="showYearTransitionWarning"
                                    class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg border border-yellow-200 dark:border-yellow-600 mb-4 flex items-start gap-3"
                                    :class="{ 'p-5': isMaximized }"
                                >
                                    <div
                                        class="p-2 bg-yellow-100 dark:bg-yellow-800 rounded-lg mt-0.5"
                                    >
                                        <v-icon
                                            name="bi-exclamation-triangle"
                                            class="text-yellow-600 dark:text-yellow-400"
                                        />
                                    </div>
                                    <div>
                                        <div
                                            class="font-medium text-yellow-800 dark:text-yellow-300"
                                            :class="{ 'text-lg': isMaximized }"
                                        >
                                            Year Transition Detected
                                        </div>
                                        <p
                                            class="text-sm text-yellow-700 dark:text-yellow-400 mt-1"
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
                                        'gap-6 mb-5': isMaximized,
                                        'md:grid-cols-4': isMaximized,
                                    }"
                                >
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
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
                                                    'w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 pr-10',
                                                    hasPreviousReadings
                                                        ? 'bg-gray-50 dark:bg-gray-600'
                                                        : 'bg-white dark:bg-gray-700',
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
                                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 text-sm"
                                                :class="{
                                                    'text-base right-4':
                                                        isMaximized,
                                                }"
                                                >m³</span
                                            >
                                        </div>
                                        <p
                                            v-if="!hasPreviousReadings"
                                            class="text-xs text-gray-500 dark:text-gray-400 mt-1"
                                            :class="{ 'text-sm': isMaximized }"
                                        >
                                            Enter the initial meter reading
                                        </p>
                                        <p
                                            v-else
                                            class="text-xs text-gray-500 dark:text-gray-400 mt-1"
                                            :class="{ 'text-sm': isMaximized }"
                                        >
                                            Last recorded reading (automatically
                                            populated)
                                        </p>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
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
                                                class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 pr-10"
                                                :class="[
                                                    {
                                                        'border-red-300 dark:border-red-500':
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
                                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 text-sm"
                                                :class="{
                                                    'text-base right-4':
                                                        isMaximized,
                                                }"
                                                >m³</span
                                            >
                                        </div>
                                        <p
                                            v-if="readingError"
                                            class="text-xs text-red-500 dark:text-red-400 mt-1"
                                            :class="{ 'text-sm': isMaximized }"
                                        >
                                            {{ readingError }}
                                        </p>
                                        <p
                                            v-else
                                            class="text-xs text-gray-500 dark:text-gray-400 mt-1"
                                            :class="{ 'text-sm': isMaximized }"
                                        >
                                            Must be higher than previous reading
                                        </p>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
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
                                                class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-600 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 pr-10"
                                                :class="{
                                                    'p-3 pr-12 text-base':
                                                        isMaximized,
                                                }"
                                                disabled
                                            />
                                            <span
                                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 text-sm"
                                                :class="{
                                                    'text-base right-4':
                                                        isMaximized,
                                                }"
                                                >m³</span
                                            >
                                        </div>
                                        <p
                                            class="text-xs text-gray-500 dark:text-gray-400 mt-1"
                                            :class="{ 'text-sm': isMaximized }"
                                        >
                                            Calculated automatically
                                        </p>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
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
                                                class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-600 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 pr-10"
                                                :class="{
                                                    'p-3 pr-12 text-base':
                                                        isMaximized,
                                                }"
                                                disabled
                                            />
                                            <span
                                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 text-sm"
                                                :class="{
                                                    'text-base right-4':
                                                        isMaximized,
                                                }"
                                                >₱</span
                                            >
                                        </div>
                                        <p
                                            class="text-xs text-gray-500 dark:text-gray-400 mt-1"
                                            :class="{ 'text-sm': isMaximized }"
                                        >
                                            Based on consumption rate
                                        </p>
                                    </div>
                                </div>

                                <!-- Summary Card -->
                                <div
                                    v-if="newReading.consumption > 0"
                                    class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-700"
                                    :class="{ 'p-5': isMaximized }"
                                >
                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <div>
                                            <div
                                                class="text-sm text-blue-700 dark:text-blue-300 font-medium"
                                                :class="{
                                                    'text-base': isMaximized,
                                                }"
                                            >
                                                Billing Summary
                                            </div>
                                            <div
                                                class="text-xl font-bold text-blue-800 dark:text-blue-200 mt-1"
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
                                                class="text-sm text-blue-600 dark:text-blue-400 mt-1"
                                                :class="{
                                                    'text-base mt-2':
                                                        isMaximized,
                                                }"
                                            >
                                                {{ newReading.consumption }} m³
                                                consumption
                                            </div>
                                        </div>
                                        <div
                                            class="p-2 bg-blue-100 dark:bg-blue-800 rounded-lg"
                                            :class="{ 'p-3': isMaximized }"
                                        >
                                            <v-icon
                                                name="bi-receipt"
                                                class="text-blue-600 dark:text-blue-300 text-xl"
                                                :class="{
                                                    'text-2xl': isMaximized,
                                                }"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Previous Readings Section -->
                            <div
                                class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-5"
                                :class="{ 'p-6': isMaximized }"
                            >
                                <div
                                    class="flex items-center justify-between mb-4"
                                >
                                    <h3
                                        class="text-lg font-semibold text-gray-800 dark:text-white"
                                        :class="{ 'text-xl': isMaximized }"
                                    >
                                        Reading History
                                    </h3>

                                    <!-- Year Filter -->
                                    <div class="flex items-center gap-2">
                                        <v-icon
                                            name="bi-filter"
                                            class="text-gray-500 dark:text-gray-400"
                                        />
                                        <select
                                            v-model="selectedYear"
                                            class="p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 text-sm"
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
                                        class="inline-flex items-center text-blue-600 dark:text-blue-400"
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
                                        class="text-center py-8 text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-600 rounded-lg"
                                        :class="{ 'py-10': isMaximized }"
                                    >
                                        <v-icon
                                            name="bi-clock-history"
                                            class="text-2xl mb-2 opacity-50"
                                            :class="{
                                                'text-3xl mb-3': isMaximized,
                                            }"
                                        />
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
                                        class="overflow-hidden border border-gray-200 dark:border-gray-600 rounded-lg"
                                        :class="{ 'border-2': isMaximized }"
                                    >
                                        <!-- Table Header -->
                                        <div
                                            class="grid grid-cols-12 gap-4 px-4 py-3 bg-gray-50 dark:bg-gray-600 border-b border-gray-200 dark:border-gray-500 text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider"
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
                                        <div
                                            class="divide-y divide-gray-200 dark:divide-gray-600"
                                        >
                                            <div
                                                v-for="(
                                                    reading, index
                                                ) in paginatedReadings"
                                                :key="reading.id"
                                                class="grid grid-cols-12 gap-4 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-600/50 transition-colors duration-150 group"
                                                :class="{
                                                    'px-6 py-4': isMaximized,
                                                }"
                                            >
                                                <!-- Period -->
                                                <div class="col-span-3">
                                                    <div
                                                        class="font-medium text-gray-800 dark:text-white"
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
                                                        class="text-sm text-gray-600 dark:text-gray-300"
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
                                                        class="font-semibold text-blue-600 dark:text-blue-400 mt-2"
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
                                                        class="text-gray-700 dark:text-gray-300 mt-2"
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
                                                        class="font-medium text-green-600 dark:text-green-400 mt-2"
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
                                                            'px-3 py-1 rounded-full text-xs font-medium transition-all duration-200 ',
                                                            reading.status ===
                                                            'Paid'
                                                                ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 hover:bg-green-200 dark:hover:bg-green-800'
                                                                : reading.status ===
                                                                  'Pending'
                                                                ? 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 hover:bg-yellow-200 dark:hover:bg-yellow-800'
                                                                : 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 hover:bg-red-200 dark:hover:bg-red-800',
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
                                                        class="flex items-center justify-center p-2 rounded-lg transition-all duration-200 group/btn"
                                                        :class="[
                                                            '',
                                                            {
                                                                'p-2.5':
                                                                    isMaximized,
                                                            },
                                                        ]"
                                                        title="Edit Reading"
                                                    >
                                                        <button
                                                            @click="
                                                                editAnnouncement(
                                                                    announcement
                                                                )
                                                            "
                                                            class="p-1.5 text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors duration-200"
                                                            title="Edit"
                                                        >
                                                            <Edit
                                                                :class="{
                                                                    'h-4 w-4':
                                                                        !isMaximized,
                                                                    'h-5 w-5':
                                                                        isMaximized,
                                                                }"
                                                            />
                                                        </button>

                                                        <span
                                                            class="sr-only"
                                                            :class="{
                                                                'text-sm':
                                                                    isMaximized,
                                                            }"
                                                        >
                                                            Edit reading for
                                                            {{
                                                                reading.billing_month
                                                            }}
                                                            {{ reading.year }}
                                                        </span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pagination Component -->
                                    <div
                                        v-if="totalPages > 1"
                                        class="mt-6 border-t border-gray-200 dark:border-gray-600 pt-4"
                                    >
                                        <Pagination
                                            :data="paginationData"
                                            @pagination-change-page="
                                                onPageChange
                                            "
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div
                            class="border-t border-gray-200 dark:border-gray-600 p-6 bg-white dark:bg-gray-800"
                            :class="{ 'p-7': isMaximized }"
                        >
                            <div class="flex gap-3 justify-end">
                                <button
                                    @click="submitReading"
                                    type="button"
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed rounded-md transition-colors duration-200 flex items-center min-w-[120px] justify-center"
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
                                        <v-icon
                                            name="bi-check-circle"
                                            class="mr-1"
                                        />
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
import { Edit } from 'lucide-vue-next'
import Pagination from "@/Components/Pagination.vue";

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

const paginationData = computed(() => ({
    data: filteredPreviousReadings.value,
    current_page: currentPage.value,
    per_page: itemsPerPage,
    total: filteredPreviousReadings.value.length,
    last_page: totalPages.value,
    from: (currentPage.value - 1) * itemsPerPage + 1,
    to: Math.min(
        currentPage.value * itemsPerPage,
        filteredPreviousReadings.value.length
    ),
}));

// Pagination methods
const onPageChange = (page) => {
    currentPage.value = page;
};

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

// Reset maximize state when modal is closed
watch(
    () => props.show,
    (newVal) => {
        if (newVal) {
            // When modal opens, refresh the readings
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
    // If there are previous readings, use the latest one
    if (hasPreviousReadings.value) {
        if (!newReading.value.billing_month) {
            newReading.value.previous_reading = 0;
            return;
        }

        const selectedDate = new Date(newReading.value.reading_date);

        // Find the most recent reading before the selected date
        const readingsBeforeCurrent = previousReadings.value.filter(
            (reading) => {
                const readingDate = new Date(
                    `${reading.billing_month} 1, ${reading.year}`
                );
                return readingDate < selectedDate;
            }
        );

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
    } else {
        // If no previous readings, keep the user's input or default to 0
        newReading.value.previous_reading =
            newReading.value.previous_reading || 0;
    }

    // Recalculate consumption and amount
    calculateConsumptionAndAmount();
};

// Handle image loading errors
const handleImageError = (event) => {
    // If image fails to load, replace with fallback by removing the src
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

const calculateConsumptionAndAmount = () => {
    const currentReading = parseFloat(newReading.value.reading) || 0;
    const previousReading = parseFloat(newReading.value.previous_reading) || 0;

    // Calculate consumption
    newReading.value.consumption = Math.max(
        0,
        currentReading - previousReading
    );

    // Calculate amount only if there's consumption
    if (newReading.value.consumption > 0) {
        const consumption = newReading.value.consumption;
        let amount = 0;

        if (consumption <= 10) {
            amount = 132.0;
        } else if (consumption <= 20) {
            amount = 132.0 + (consumption - 10) * 14;
        } else if (consumption <= 30) {
            amount = 132.0 + 10 * 14 + (consumption - 20) * 14.85;
        } else if (consumption <= 40) {
            amount = 132.0 + 10 * 14 + 10 * 14.85 + (consumption - 30) * 16;
        } else {
            amount =
                132.0 +
                10 * 14 +
                10 * 14.85 +
                10 * 16 +
                (consumption - 40) * 17.25;
        }

        newReading.value.amount = parseFloat(amount.toFixed(2));
    } else {
        newReading.value.amount = 0;
    }
};

const fetchPreviousReadings = async () => {
    isLoadingPreviousReadings.value = true;
    try {
        const response = await axios.get(
            route("staff.reading.previous", { userId: props.user.id })
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
            previousReadings.value = response.data || [];
        }
    } catch (error) {
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

    // Validate that current reading is not less than previous reading
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
        // This is January and there's a December reading from the previous year - confirm this is intentional
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

            // Force a refresh of previous readings - wait for it to complete
            await fetchPreviousReadings();

            // Wait for Vue to update the DOM
            await nextTick();

            // Reset the form fields
            newReading.value.reading = "";
            newReading.value.consumption = 0;
            newReading.value.amount = 0;
            showYearTransitionWarning.value = false;

            // Update the previous reading for the next entry
            updatePreviousReading();

            // Show success message but don't close the modal
            await Swal.fire({
                icon: "success",
                title: "Success!",
                text: "Meter reading submitted successfully",
                timer: 2000,
                showConfirmButton: false,
            });

            // Emit event to parent if needed
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
    // Fetch previous readings first
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
    transform: translateX(100%);
}

.modal-leave-to .transform {
    transform: translateX(100%);
}

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

/* Dark mode scrollbar */
.dark .overflow-y-auto {
    scrollbar-color: #4b5563 #1f2937;
}

.dark .overflow-y-auto::-webkit-scrollbar-track {
    background: #1f2937;
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb {
    background-color: #4b5563;
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background-color: #6b7280;
}
</style>
