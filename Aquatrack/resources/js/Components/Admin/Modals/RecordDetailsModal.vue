<template>
    <transition name="modal">
        <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
            <!-- Overlay -->
            <div
                class="fixed inset-0 bg-black/60 transition-opacity duration-300"
                @click="emit('close')"
            />

            <!-- Modal Container -->
            <div class="flex min-h-full items-center justify-center p-0">
                <div
                    class="relative w-full transform transition-all duration-300"
                    :class="isMaximized ? 'w-full h-full' : 'max-w-4xl'"
                >
                    <div
                        class="bg-white dark:bg-gray-900 shadow-2xl overflow-hidden flex flex-col"
                        :class="[
                            isMaximized
                                ? 'h-screen rounded-none'
                                : 'max-h-[90vh] rounded-lg',
                        ]"
                    >
                        <!-- Header -->
                        <div class="border-b px-6 py-4 flex-shrink-0">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="flex">

                                        <User class="w-6 h-6  text-black" />

                                        <h2
                                            class="text-xl font-semibold ml-2 text-black"
                                        >

                                            Concessioner's Record Details
                                        </h2>

                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <!-- Maximize Button -->
                                    <button
                                        @click="toggleMaximize"
                                        class="p-2 text-black hover:text-gray-600 hover:bg-gray-200/10 transition-all duration-200"
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
                                        class="p-2 text-black hover:text-gray-600 hover:bg-gray-200/10 transition-all duration-200"
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
                            v-if="loading"
                            class="flex-1 flex items-center justify-center py-16"
                        >
                            <div class="text-center">
                                <div
                                    class="w-12 h-12 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"
                                ></div>
                                <p
                                    class="text-gray-600 dark:text-gray-400 font-medium"
                                >
                                    Loading record details...
                                </p>
                            </div>
                        </div>

                        <!-- Content -->
                        <div
                            v-else-if="record"
                            class="flex-1 overflow-y-auto"
                            :class="isMaximized ? 'p-8' : 'p-6'"
                        >
                            <div class="space-y-6 w-full">
                                <!-- Status Banner -->
                                <div
                                    class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 p-6 border border-blue-100 dark:border-blue-800"
                                    :class="
                                        isMaximized
                                            ? 'rounded-md'
                                            : 'rounded-lg'
                                    "
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
                                                <!-- User Avatar -->
                                                <div
                                                    class="w-12 h-12 rounded-full border-2 border-white dark:border-gray-800 shadow-lg overflow-hidden bg-white"
                                                >
                                                    <img
                                                        v-if="
                                                            record.user
                                                                .avatar_url
                                                        "
                                                        :src="
                                                            record.user
                                                                .avatar_url
                                                        "
                                                        :alt="record.user.name"
                                                        class="w-full h-full object-cover"
                                                    />
                                                    <div
                                                        v-else
                                                        class="w-full h-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg"
                                                    >
                                                        {{
                                                            getUserInitials(
                                                                record.user
                                                            )
                                                        }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <p
                                                        class="text-sm text-gray-500 dark:text-gray-400"
                                                    >
                                                        Customer
                                                    </p>
                                                    <p
                                                        class="text-lg font-semibold text-gray-900 dark:text-white"
                                                    >
                                                        {{ record.user.name }}
                                                        {{
                                                            record.user.lastname
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
                                                <div
                                                    class="p-3 bg-white dark:bg-gray-800 shadow-sm"
                                                    :class="
                                                        isMaximized
                                                            ? 'rounded-md'
                                                            : 'rounded-lg'
                                                    "
                                                >
                                                    <v-icon
                                                        name="bi-cash-coin"
                                                        class="text-green-600 dark:text-green-400 w-6 h-6"
                                                    />
                                                </div>
                                                <div>
                                                    <p
                                                        class="text-sm text-gray-500 dark:text-gray-400"
                                                    >
                                                        Total Amount
                                                    </p>
                                                    <p
                                                        class="text-lg font-semibold text-gray-900 dark:text-white"
                                                    >
                                                        ₱{{ totalAmount }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span
                                                class="px-4 py-2.5 text-sm font-semibold border shadow-sm"
                                                :class="[
                                                    statusClass,
                                                    isMaximized
                                                        ? 'rounded-md'
                                                        : 'rounded-full',
                                                ]"
                                            >
                                                {{ statusLabel }}
                                            </span>
                                            <p
                                                class="text-sm text-gray-500 dark:text-gray-400 mt-4"
                                            >
                                                {{ getDaysStatus }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Main Grid -->
                                <div
                                    class="grid grid-cols-1 xl:grid-cols-2 gap-6"
                                    :class="isMaximized ? 'gap-8' : ''"
                                >
                                    <!-- Left Column -->
                                    <div
                                        class="space-y-6"
                                        :class="isMaximized ? 'space-y-8' : ''"
                                    >
                                        <!-- Customer Information -->
                                        <div
                                            class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm"
                                            :class="
                                                isMaximized
                                                    ? 'rounded-md'
                                                    : 'rounded-lg'
                                            "
                                        >
                                            <div
                                                class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 border-b border-gray-200 dark:border-gray-600"
                                            >
                                                <h3
                                                    class="text-lg font-semibold text-gray-900 dark:text-white flex items-center"
                                                >
                                                    <v-icon
                                                        name="bi-person-circle"
                                                        class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2"
                                                    />
                                                    Customer Information
                                                </h3>
                                            </div>
                                            <div
                                                class="p-6 space-y-4"
                                                :class="
                                                    isMaximized
                                                        ? 'p-8 space-y-6'
                                                        : ''
                                                "
                                            >
                                                <div
                                                    class="flex justify-center items-center p-6"
                                                >
                                                    <div
                                                        class="w-24 h-24 rounded-full border-2 border-white dark:border-gray-800 shadow-md overflow-hidden bg-white flex items-center justify-center"
                                                    >
                                                        <img
                                                            v-if="
                                                                record.user
                                                                    .avatar_url
                                                            "
                                                            :src="
                                                                record.user
                                                                    .avatar_url
                                                            "
                                                            :alt="
                                                                record.user.name
                                                            "
                                                            class="w-full h-full object-cover rounded-full"
                                                        />
                                                        <div
                                                            v-else
                                                            class="w-full h-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-xl rounded-full"
                                                        >
                                                            {{
                                                                getUserInitials(
                                                                    record.user
                                                                )
                                                            }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div
                                                    class="grid grid-cols-1 md:grid-cols-2 gap-4"
                                                    :class="
                                                        isMaximized
                                                            ? 'gap-6'
                                                            : ''
                                                    "
                                                >
                                                    <div
                                                        class="flex items-start space-x-3 p-3 bg-gray-50 dark:bg-gray-700/30"
                                                        :class="
                                                            isMaximized
                                                                ? 'rounded-md'
                                                                : 'rounded-lg'
                                                        "
                                                    >
                                                        <div
                                                            class="p-2 bg-blue-100 dark:bg-blue-900"
                                                            :class="
                                                                isMaximized
                                                                    ? 'rounded-md'
                                                                    : 'rounded-lg'
                                                            "
                                                        >
                                                            <v-icon
                                                                name="bi-person-badge"
                                                                class="text-blue-600 dark:text-blue-400 w-4 h-4"
                                                            />
                                                        </div>
                                                        <div>
                                                            <p
                                                                class="text-sm text-gray-500 dark:text-gray-400"
                                                            >
                                                                Full Name
                                                            </p>
                                                            <p
                                                                class="font-medium text-gray-900 dark:text-white"
                                                                :class="
                                                                    isMaximized
                                                                        ? 'text-base'
                                                                        : ''
                                                                "
                                                            >
                                                                {{
                                                                    record.user
                                                                        .name
                                                                }}
                                                                {{
                                                                    record.user
                                                                        .lastname
                                                                }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="flex items-start space-x-3 p-3 bg-gray-50 dark:bg-gray-700/30"
                                                        :class="
                                                            isMaximized
                                                                ? 'rounded-md'
                                                                : 'rounded-lg'
                                                        "
                                                    >
                                                        <div
                                                            class="p-2 bg-blue-100 dark:bg-blue-900"
                                                            :class="
                                                                isMaximized
                                                                    ? 'rounded-md'
                                                                    : 'rounded-lg'
                                                            "
                                                        >
                                                            <v-icon
                                                                name="bi-credit-card"
                                                                class="text-blue-600 dark:text-blue-400 w-4 h-4"
                                                            />
                                                        </div>
                                                        <div>
                                                            <p
                                                                class="text-sm text-gray-500 dark:text-gray-400"
                                                            >
                                                                Account Number
                                                            </p>
                                                            <p
                                                                class="font-medium text-gray-900 dark:text-white"
                                                                :class="
                                                                    isMaximized
                                                                        ? 'text-base'
                                                                        : ''
                                                                "
                                                            >
                                                                {{
                                                                    record.user
                                                                        .account_number
                                                                }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="flex items-start space-x-3 p-3 bg-gray-50 dark:bg-gray-700/30"
                                                        :class="
                                                            isMaximized
                                                                ? 'rounded-md'
                                                                : 'rounded-lg'
                                                        "
                                                    >
                                                        <div
                                                            class="p-2 bg-blue-100 dark:bg-blue-900"
                                                            :class="
                                                                isMaximized
                                                                    ? 'rounded-md'
                                                                    : 'rounded-lg'
                                                            "
                                                        >
                                                            <v-icon
                                                                name="bi-upc-scan"
                                                                class="text-blue-600 dark:text-blue-400 w-4 h-4"
                                                            />
                                                        </div>
                                                        <div>
                                                            <p
                                                                class="text-sm text-gray-500 dark:text-gray-400"
                                                            >
                                                                Serial Number
                                                            </p>
                                                            <p
                                                                class="font-medium text-gray-900 dark:text-white"
                                                                :class="
                                                                    isMaximized
                                                                        ? 'text-base'
                                                                        : ''
                                                                "
                                                            >
                                                                {{
                                                                    record.user
                                                                        .serial_number
                                                                }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="flex items-start space-x-3 p-3 bg-gray-50 dark:bg-gray-700/30"
                                                        :class="
                                                            isMaximized
                                                                ? 'rounded-md'
                                                                : 'rounded-lg'
                                                        "
                                                    >
                                                        <div
                                                            class="p-2 bg-blue-100 dark:bg-blue-900"
                                                            :class="
                                                                isMaximized
                                                                    ? 'rounded-md'
                                                                    : 'rounded-lg'
                                                            "
                                                        >
                                                            <v-icon
                                                                name="bi-telephone"
                                                                class="text-blue-600 dark:text-blue-400 w-4 h-4"
                                                            />
                                                        </div>
                                                        <div>
                                                            <p
                                                                class="text-sm text-gray-500 dark:text-gray-400"
                                                            >
                                                                Contact Number
                                                            </p>
                                                            <p
                                                                class="font-medium text-gray-900 dark:text-white"
                                                                :class="
                                                                    isMaximized
                                                                        ? 'text-base'
                                                                        : ''
                                                                "
                                                            >
                                                                {{
                                                                    record.user
                                                                        .phone ||
                                                                    "N/A"
                                                                }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div
                                                    class="flex items-start space-x-3 p-3 bg-gray-50 dark:bg-gray-700/30"
                                                    :class="
                                                        isMaximized
                                                            ? 'rounded-md'
                                                            : 'rounded-lg'
                                                    "
                                                >
                                                    <div
                                                        class="p-2 bg-blue-100 dark:bg-blue-900"
                                                        :class="
                                                            isMaximized
                                                                ? 'rounded-md'
                                                                : 'rounded-lg'
                                                        "
                                                    >
                                                        <v-icon
                                                            name="bi-geo-alt"
                                                            class="text-blue-600 dark:text-blue-400 w-4 h-4"
                                                        />
                                                    </div>
                                                    <div class="flex-1">
                                                        <p
                                                            class="text-sm text-gray-500 dark:text-gray-400"
                                                        >
                                                            Address
                                                        </p>
                                                        <p
                                                            class="font-medium text-gray-900 dark:text-white"
                                                            :class="
                                                                isMaximized
                                                                    ? 'text-base'
                                                                    : ''
                                                            "
                                                        >
                                                            {{
                                                                record.user
                                                                    .zone
                                                            }},
                                                            {{
                                                                record.user
                                                                    .barangay
                                                            }},
                                                            {{
                                                                record.user
                                                                    .municipality
                                                            }},
                                                            {{
                                                                record.user
                                                                    .province
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
                                        :class="isMaximized ? 'space-y-8' : ''"
                                    >
                                        <!-- Reading Information -->
                                        <div
                                            class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm"
                                            :class="
                                                isMaximized
                                                    ? 'rounded-md'
                                                    : 'rounded-lg'
                                            "
                                        >
                                            <div
                                                class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 border-b border-gray-200 dark:border-gray-600"
                                            >
                                                <h3
                                                    class="text-lg font-semibold text-gray-900 dark:text-white flex items-center"
                                                >
                                                    <v-icon
                                                        name="bi-speedometer2"
                                                        class="w-5 h-5 text-green-600 dark:text-green-400 mr-2"
                                                    />
                                                    Reading Information
                                                </h3>
                                            </div>
                                            <div
                                                class="p-6"
                                                :class="
                                                    isMaximized ? 'p-8' : ''
                                                "
                                            >
                                                <div
                                                    class="grid grid-cols-2 gap-4"
                                                    :class="
                                                        isMaximized
                                                            ? 'gap-6'
                                                            : ''
                                                    "
                                                >
                                                    <div
                                                        class="flex items-start space-x-3 p-3 bg-gray-50 dark:bg-gray-700/30"
                                                        :class="
                                                            isMaximized
                                                                ? 'rounded-md'
                                                                : 'rounded-lg'
                                                        "
                                                    >
                                                        <div
                                                            class="p-2 bg-green-100 dark:bg-green-900"
                                                            :class="
                                                                isMaximized
                                                                    ? 'rounded-md'
                                                                    : 'rounded-lg'
                                                            "
                                                        >
                                                            <v-icon
                                                                name="bi-calendar-check"
                                                                class="text-green-600 dark:text-green-400 w-4 h-4"
                                                            />
                                                        </div>
                                                        <div>
                                                            <p
                                                                class="text-sm text-gray-500 dark:text-gray-400"
                                                            >
                                                                Reading Date
                                                            </p>
                                                            <p
                                                                class="font-medium text-gray-900 dark:text-white"
                                                                :class="
                                                                    isMaximized
                                                                        ? 'text-base'
                                                                        : ''
                                                                "
                                                            >
                                                                {{
                                                                    formatDate(
                                                                        record.reading_date
                                                                    )
                                                                }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="flex items-start space-x-3 p-3 bg-gray-50 dark:bg-gray-700/30"
                                                        :class="
                                                            isMaximized
                                                                ? 'rounded-md'
                                                                : 'rounded-lg'
                                                        "
                                                    >
                                                        <div
                                                            class="p-2 bg-green-100 dark:bg-green-900"
                                                            :class="
                                                                isMaximized
                                                                    ? 'rounded-md'
                                                                    : 'rounded-lg'
                                                            "
                                                        >
                                                            <v-icon
                                                                name="bi-calendar-x"
                                                                class="text-green-600 dark:text-green-400 w-4 h-4"
                                                            />
                                                        </div>
                                                        <div>
                                                            <p
                                                                class="text-sm text-gray-500 dark:text-gray-400"
                                                            >
                                                                Due Date
                                                            </p>
                                                            <p
                                                                class="font-medium text-gray-900 dark:text-white"
                                                                :class="{
                                                                    'text-red-600':
                                                                        record.status ===
                                                                            'Overdue' &&
                                                                        surcharge,
                                                                    'text-base':
                                                                        isMaximized,
                                                                }"
                                                            >
                                                                {{
                                                                    formatDate(
                                                                        record.due_date
                                                                    )
                                                                }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="flex items-start space-x-3 p-3 bg-gray-50 dark:bg-gray-700/30"
                                                        :class="
                                                            isMaximized
                                                                ? 'rounded-md'
                                                                : 'rounded-lg'
                                                        "
                                                    >
                                                        <div
                                                            class="p-2 bg-green-100 dark:bg-green-900"
                                                            :class="
                                                                isMaximized
                                                                    ? 'rounded-md'
                                                                    : 'rounded-lg'
                                                            "
                                                        >
                                                            <v-icon
                                                                name="bi-water"
                                                                class="text-green-600 dark:text-green-400 w-4 h-4"
                                                            />
                                                        </div>
                                                        <div>
                                                            <p
                                                                class="text-sm text-gray-500 dark:text-gray-400"
                                                            >
                                                                Current Reading
                                                            </p>
                                                            <p
                                                                class="font-medium text-gray-900 dark:text-white"
                                                                :class="
                                                                    isMaximized
                                                                        ? 'text-base'
                                                                        : ''
                                                                "
                                                            >
                                                                {{
                                                                    record.reading
                                                                }}
                                                                m³
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="flex items-start space-x-3 p-3 bg-gray-50 dark:bg-gray-700/30"
                                                        :class="
                                                            isMaximized
                                                                ? 'rounded-md'
                                                                : 'rounded-lg'
                                                        "
                                                    >
                                                        <div
                                                            class="p-2 bg-green-100 dark:bg-green-900"
                                                            :class="
                                                                isMaximized
                                                                    ? 'rounded-md'
                                                                    : 'rounded-lg'
                                                            "
                                                        >
                                                            <v-icon
                                                                name="bi-graph-up"
                                                                class="text-green-600 dark:text-green-400 w-4 h-4"
                                                            />
                                                        </div>
                                                        <div>
                                                            <p
                                                                class="text-sm text-gray-500 dark:text-gray-400"
                                                            >
                                                                Consumption
                                                            </p>
                                                            <p
                                                                class="font-medium text-gray-900 dark:text-white"
                                                                :class="
                                                                    isMaximized
                                                                        ? 'text-base'
                                                                        : ''
                                                                "
                                                            >
                                                                {{
                                                                    record.consumption
                                                                }}
                                                                m³
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Payment Information -->
                                        <div
                                            class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm"
                                            :class="
                                                isMaximized
                                                    ? 'rounded-md'
                                                    : 'rounded-lg'
                                            "
                                        >
                                            <div
                                                class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 border-b border-gray-200 dark:border-gray-600"
                                            >
                                                <h3
                                                    class="text-lg font-semibold text-gray-900 dark:text-white flex items-center"
                                                >
                                                    <v-icon
                                                        name="bi-cash-stack"
                                                        class="w-5 h-5 text-purple-600 dark:text-purple-400 mr-2"
                                                    />
                                                    Payment Information
                                                </h3>
                                            </div>
                                            <div
                                                class="p-6"
                                                :class="
                                                    isMaximized ? 'p-8' : ''
                                                "
                                            >
                                                <div
                                                    class="space-y-4"
                                                    :class="
                                                        isMaximized
                                                            ? 'space-y-6'
                                                            : ''
                                                    "
                                                >
                                                    <div
                                                        v-if="
                                                            surcharge &&
                                                            record.status !==
                                                                'Paid'
                                                        "
                                                        class="flex items-center justify-between p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800"
                                                        :class="
                                                            isMaximized
                                                                ? 'rounded-md'
                                                                : 'rounded-lg'
                                                        "
                                                    >
                                                        <div
                                                            class="flex items-center space-x-3"
                                                        >
                                                            <div
                                                                class="p-2 bg-white dark:bg-gray-800"
                                                                :class="
                                                                    isMaximized
                                                                        ? 'rounded-md'
                                                                        : 'rounded-lg'
                                                                "
                                                            >
                                                                <v-icon
                                                                    name="bi-exclamation-triangle"
                                                                    class="text-red-600 dark:text-red-400 w-5 h-5"
                                                                />
                                                            </div>
                                                            <div>
                                                                <p
                                                                    class="text-sm text-gray-500 dark:text-gray-400"
                                                                >
                                                                    Surcharge
                                                                </p>
                                                                <p
                                                                    class="text-lg font-semibold text-red-600 dark:text-red-400"
                                                                >
                                                                    ₱{{
                                                                        surcharge
                                                                    }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div
                                                        class="flex items-center justify-between p-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border border-green-200 dark:border-green-800"
                                                        :class="
                                                            isMaximized
                                                                ? 'rounded-md'
                                                                : 'rounded-lg'
                                                        "
                                                    >
                                                        <div
                                                            class="flex items-center space-x-3"
                                                        >
                                                            <div
                                                                class="p-2 bg-white dark:bg-gray-800"
                                                                :class="
                                                                    isMaximized
                                                                        ? 'rounded-md'
                                                                        : 'rounded-lg'
                                                                "
                                                            >
                                                                <v-icon
                                                                    name="bi-receipt"
                                                                    class="text-green-600 dark:text-green-400 w-5 h-5"
                                                                />
                                                            </div>
                                                            <div>
                                                                <p
                                                                    class="text-sm text-gray-500 dark:text-gray-400"
                                                                >
                                                                    Total Amount
                                                                </p>
                                                                <p
                                                                    class="text-2xl font-bold text-gray-900 dark:text-white"
                                                                >
                                                                    ₱{{
                                                                        totalAmount
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
        </div>
    </transition>
</template>

<script setup>
import { ref, computed } from "vue";
import { XIcon, Maximize2Icon, Minimize2Icon, User } from "lucide-vue-next";

const props = defineProps({
    show: {
        type: Boolean,
        required: true,
    },
    record: {
        type: Object,
        default: null,
    },
    loading: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["close"]);

// State
const isMaximized = ref(false);

// Computed Properties
const statusClass = computed(() => {
    if (!props.record?.status)
        return "bg-gray-100 text-gray-800 border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600";

    const classes = {
        paid: "bg-green-100 text-green-800 border-green-200 dark:bg-green-900 dark:text-green-200 dark:border-green-700",
        pending:
            "bg-yellow-100 text-yellow-800 border-yellow-200 dark:bg-yellow-900 dark:text-yellow-200 dark:border-yellow-700",
        overdue:
            "bg-red-100 text-red-800 border-red-200 dark:bg-red-900 dark:text-red-200 dark:border-red-700",
    };

    return (
        classes[props.record.status.toLowerCase()] ||
        "bg-gray-100 text-gray-800 border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600"
    );
});

const statusLabel = computed(() => {
    if (!props.record?.status) return "Unknown";
    return (
        props.record.status.charAt(0).toUpperCase() +
        props.record.status.slice(1)
    );
});

const surcharge = computed(() => {
    if (!props.record?.surcharge || props.record.status === "Paid") {
        return null;
    }
    return props.record.surcharge;
});

const totalAmount = computed(() => {
    const baseAmount = parseFloat(props.record?.amount) || 0;
    const surchargeAmount = parseFloat(surcharge.value) || 0;
    return (baseAmount + surchargeAmount).toFixed(2);
});

const getDaysStatus = computed(() => {
    // Don't show due date status if record is paid
    if (props.record?.status === "Paid") return "Payment Completed";

    if (!props.record?.due_date) return "N/A";

    const dueDate = new Date(props.record.due_date);
    const today = new Date();
    const diffTime = dueDate - today;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays > 0) {
        return `Due in ${diffDays} day${diffDays !== 1 ? "s" : ""}`;
    } else if (diffDays === 0) {
        return "Due today";
    } else {
        return `Overdue by ${Math.abs(diffDays)} day${
            Math.abs(diffDays) !== 1 ? "s" : ""
        }`;
    }
});

// Methods
const toggleMaximize = () => {
    isMaximized.value = !isMaximized.value;
};

const formatDate = (dateString) => {
    if (!dateString) return "N/A";
    const options = { year: "numeric", month: "long", day: "numeric" };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

const getUserInitials = (user) => {
    if (!user?.name) return "?";
    return user.name
        .split(" ")
        .map((n) => n[0])
        .join("")
        .toUpperCase()
        .substring(0, 2);
};
</script>

<style scoped>
/* Smooth modal transitions */
.modal-enter-active,
.modal-leave-active {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.modal-enter-from {
    opacity: 0;
}

.modal-leave-to {
    opacity: 0;
}

.modal-enter-active .modal-container,
.modal-leave-active .modal-container {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.modal-enter-from .modal-container {
    opacity: 0;
    transform: scale(0.95) translateY(-20px);
}

.modal-leave-to .modal-container {
    opacity: 0;
    transform: scale(0.95) translateY(20px);
}

/* Backdrop transition */
.modal-enter-active .backdrop,
.modal-leave-active .backdrop {
    transition: opacity 0.3s ease;
}

.modal-enter-from .backdrop,
.modal-leave-to .backdrop {
    opacity: 0;
}

/* Content slide-in animation */
.content-enter-active {
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1) 0.1s;
}

.content-enter-from {
    opacity: 0;
    transform: translateY(30px);
}

/* Form elements stagger animation */
.form-item {
    transition: all 0.3s ease;
}

.modal-enter-active .form-item:nth-child(1) {
    transition-delay: 0.1s;
}
.modal-enter-active .form-item:nth-child(2) {
    transition-delay: 0.15s;
}
.modal-enter-active .form-item:nth-child(3) {
    transition-delay: 0.2s;
}
.modal-enter-active .form-item:nth-child(4) {
    transition-delay: 0.25s;
}
.modal-enter-active .form-item:nth-child(5) {
    transition-delay: 0.3s;
}

/* Maximize transition */
.maximize-enter-active,
.maximize-leave-active {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.maximize-enter-from,
.maximize-leave-to {
    opacity: 0;
    transform: scale(0.9);
}

/* Password preview fade transition */
.fade-enter-active,
.fade-leave-active {
    transition: all 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}

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
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
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

/* Remove default select arrow in some browsers */
select {
    background-image: none;
}

/* Smooth button hover effects */
button {
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Input focus animations */
input:focus,
select:focus {
    transition: all 0.2s ease;
}

/* Radio button selection animation */
input[type="radio"]:checked + div {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Loading spinner animation */
@keyframes spin-slow {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}

.animate-spin-slow {
    animation: spin-slow 1.5s linear infinite;
}

/* Pulse animation for loading states */
@keyframes pulse-subtle {
    0%,
    100% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
}

.animate-pulse-subtle {
    animation: pulse-subtle 2s ease-in-out infinite;
}
</style>
