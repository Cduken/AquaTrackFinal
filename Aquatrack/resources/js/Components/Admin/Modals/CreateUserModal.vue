<template>
    <transition name="modal">
        <div v-if="show" class="fixed inset-0 z-[1000] overflow-hidden">
            <!-- Overlay -->
            <div
                class="absolute inset-0 bg-black/50 transition-opacity duration-300"
                @click="$emit('close')"
            ></div>

            <!-- Sliding panel container -->
            <div class="fixed inset-y-0 right-0 w-full max-w-2xl flex" :class="{'max-w-full': isMaximized}">
                <!-- Panel with transform class for animation -->
                <div
                    class="relative w-full h-full transform transition-transform duration-300 ease-in-out"
                >
                    <div
                        class="h-full flex flex-col bg-white dark:bg-gray-800 shadow-xl"
                    >
                        <!-- Header -->
                        <div
                            class="flex items-center justify-between px-6 py-5 bg-gradient-to-r from-[#062F64] to-[#1E4272]"
                        >
                            <div class="flex items-center space-x-2">
                                <v-icon
                                    name="hi-user"
                                    class="text-amber-300"
                                    scale="1.5"
                                />
                                <span class="text-white font-[200] text-xl"
                                    >Register New User</span
                                >
                            </div>
                            <div class="flex items-center space-x-2">
                                <!-- Maximize/Minimize Button -->
                                <button
                                    @click="toggleMaximize"
                                    class="text-white hover:text-gray-200 transition-colors duration-200 p-1 rounded-full hover:bg-white/10"
                                    :title="isMaximized ? 'Minimize' : 'Maximize'"
                                >
                                    <v-icon
                                        :name="isMaximized ? 'bi-fullscreen-exit' : 'bi-fullscreen'"
                                        class="h-5 w-5"
                                    />
                                </button>
                                <!-- Close Button -->
                                <button
                                    @click="$emit('close')"
                                    class="text-white hover:text-gray-200 transition-colors duration-200 p-1 rounded-full hover:bg-white/10"
                                >
                                    <v-icon name="bi-x-lg" class="h-6 w-6" />
                                </button>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 overflow-y-auto p-6" :class="{'p-8': isMaximized}">
                            <form @submit.prevent="handleSubmit">
                                <div class="space-y-6" :class="{'space-y-8': isMaximized}">
                                    <!-- Role Selection Section -->
                                    <div
                                        class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 p-5 rounded-lg"
                                        :class="{'p-6': isMaximized}"
                                    >
                                        <h3
                                            class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center"
                                            :class="{'text-xl mb-5': isMaximized}"
                                        >
                                            <v-icon
                                                name="hi-user-group"
                                                class="mr-2"
                                                :scale="isMaximized ? '1.2' : '1'"
                                            />
                                            User Type
                                        </h3>
                                        <div class="flex space-x-4">
                                            <label
                                                class="inline-flex items-center space-x-2 cursor-pointer"
                                            >
                                                <input
                                                    type="radio"
                                                    v-model="userData.role"
                                                    value="staff"
                                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                                    :class="{'w-5 h-5': isMaximized}"
                                                />
                                                <span
                                                    class="text-gray-900 dark:text-white flex items-center gap-1"
                                                    :class="{'text-base': isMaximized}"
                                                >
                                                    <v-icon
                                                        name="hi-user-group"
                                                        class="w-4 h-4"
                                                        :class="{'w-5 h-5': isMaximized}"
                                                    />
                                                    Staff
                                                </span>
                                            </label>
                                            <label
                                                class="inline-flex items-center space-x-2 cursor-pointer"
                                            >
                                                <input
                                                    type="radio"
                                                    v-model="userData.role"
                                                    value="customer"
                                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                                    :class="{'w-5 h-5': isMaximized}"
                                                />
                                                <span
                                                    class="text-gray-900 dark:text-white flex items-center gap-1"
                                                    :class="{'text-base': isMaximized}"
                                                >
                                                    <v-icon
                                                        name="hi-user"
                                                        class="w-4 h-4"
                                                        :class="{'w-5 h-5': isMaximized}"
                                                    />
                                                    Customer
                                                </span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Personal Information Section -->
                                    <div
                                        class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 p-5 rounded-lg"
                                        :class="{'p-6': isMaximized}"
                                    >
                                        <h3
                                            class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center"
                                            :class="{'text-xl mb-5': isMaximized}"
                                        >
                                            <v-icon
                                                name="hi-user"
                                                class="mr-2"
                                                :scale="isMaximized ? '1.2' : '1'"
                                            />
                                            Personal Information
                                        </h3>
                                        <div
                                            class="grid grid-cols-1 md:grid-cols-2 gap-4"
                                            :class="{'gap-6': isMaximized}"
                                        >
                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                                    :class="{'text-base': isMaximized}"
                                                    >First Name</label
                                                >
                                                <div class="relative">
                                                    <div
                                                        class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                                                    >
                                                        <v-icon
                                                            name="hi-user"
                                                            class="w-4 h-4 text-gray-500 dark:text-gray-400"
                                                            :class="{'w-5 h-5': isMaximized}"
                                                        />
                                                    </div>
                                                    <input
                                                        v-model="userData.name"
                                                        type="text"
                                                        required
                                                        class="w-full p-2 pl-10 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                                                        :class="{'p-3 pl-12 text-base': isMaximized}"
                                                        placeholder="First name"
                                                    />
                                                </div>
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                                    :class="{'text-base': isMaximized}"
                                                    >Last Name</label
                                                >
                                                <div class="relative">
                                                    <div
                                                        class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                                                    >
                                                        <v-icon
                                                            name="hi-user"
                                                            class="w-4 h-4 text-gray-500 dark:text-gray-400"
                                                            :class="{'w-5 h-5': isMaximized}"
                                                        />
                                                    </div>
                                                    <input
                                                        v-model="
                                                            userData.lastname
                                                        "
                                                        type="text"
                                                        required
                                                        class="w-full p-2 pl-10 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                                                        :class="{'p-3 pl-12 text-base': isMaximized}"
                                                        placeholder="Last name"
                                                        @input="
                                                            updateGeneratedPassword
                                                        "
                                                    />
                                                </div>
                                            </div>
                                            <div
                                                v-if="userData.role === 'staff'"
                                            >
                                                <label
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                                    :class="{'text-base': isMaximized}"
                                                    >Email</label
                                                >
                                                <div class="relative">
                                                    <div
                                                        class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                                                    >
                                                        <v-icon
                                                            name="hi-mail"
                                                            class="w-4 h-4 text-gray-500 dark:text-gray-400"
                                                            :class="{'w-5 h-5': isMaximized}"
                                                        />
                                                    </div>
                                                    <input
                                                        v-model="userData.email"
                                                        type="email"
                                                        :required="
                                                            userData.role ===
                                                            'staff'
                                                        "
                                                        class="w-full p-2 pl-10 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                                                        :class="{'p-3 pl-12 text-base': isMaximized}"
                                                        placeholder="name@company.com"
                                                    />
                                                </div>
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                                    :class="{'text-base': isMaximized}"
                                                    >Phone</label
                                                >
                                                <div class="relative">
                                                    <div
                                                        class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                                                    >
                                                        <v-icon
                                                            name="hi-phone"
                                                            class="w-4 h-4 text-gray-500 dark:text-gray-400"
                                                            :class="{'w-5 h-5': isMaximized}"
                                                        />
                                                    </div>
                                                    <input
                                                        v-model="userData.phone"
                                                        type="tel"
                                                        required
                                                        class="w-full p-2 pl-10 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                                                        :class="{'p-3 pl-12 text-base': isMaximized}"
                                                        placeholder="09123456789"
                                                        @input="
                                                            validatePhoneNumber
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Customer Specific Fields -->
                                    <div
                                        v-if="userData.role === 'customer'"
                                        class="space-y-6"
                                        :class="{'space-y-8': isMaximized}"
                                    >
                                        <!-- Equipment Information Section -->
                                        <div
                                            class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 p-5 rounded-lg"
                                            :class="{'p-6': isMaximized}"
                                        >
                                            <h3
                                                class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center"
                                                :class="{'text-xl mb-5': isMaximized}"
                                            >
                                                <v-icon
                                                    name="hi-cube"
                                                    class="mr-2"
                                                    :scale="isMaximized ? '1.2' : '1'"
                                                />
                                                Equipment Information
                                            </h3>
                                            <div
                                                class="grid grid-cols-1 md:grid-cols-2 gap-4"
                                                :class="{'gap-6': isMaximized, 'md:grid-cols-3': isMaximized}"
                                            >
                                                <div>
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                                        :class="{'text-base': isMaximized}"
                                                        >Account Number</label
                                                    >
                                                    <div class="relative">
                                                        <div
                                                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                                                        >
                                                            <v-icon
                                                                name="hi-credit-card"
                                                                class="w-4 h-4 text-gray-500 dark:text-gray-400"
                                                                :class="{'w-5 h-5': isMaximized}"
                                                            />
                                                        </div>
                                                        <input
                                                            v-model="
                                                                userData.accountNumber
                                                            "
                                                            type="text"
                                                            required
                                                            class="w-full p-2 pl-10 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                                                            :class="{'p-3 pl-12 text-base': isMaximized}"
                                                            placeholder="Account Number"
                                                            @input="
                                                                formatAccountNumber
                                                            "
                                                            :maxlength="11"
                                                        />
                                                    </div>
                                                    <p
                                                        class="text-xs text-gray-500 mt-1"
                                                        :class="{'text-sm': isMaximized}"
                                                    >
                                                        Format: XXX-XX-XXX or
                                                        XXX-XX-XXXX
                                                    </p>
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                                        :class="{'text-base': isMaximized}"
                                                        >Date Installed</label
                                                    >
                                                    <div class="relative">
                                                        <div
                                                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                                                        >
                                                            <v-icon
                                                                name="hi-calendar"
                                                                class="w-4 h-4 text-gray-500 dark:text-gray-400"
                                                                :class="{'w-5 h-5': isMaximized}"
                                                            />
                                                        </div>
                                                        <input
                                                            v-model="
                                                                userData.date_installed
                                                            "
                                                            type="date"
                                                            required
                                                            class="w-full p-2 pl-10 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                                                            :class="{'p-3 pl-12 text-base': isMaximized}"
                                                        />
                                                    </div>
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                                        :class="{'text-base': isMaximized}"
                                                        >Brand</label
                                                    >
                                                    <div class="relative">
                                                        <div
                                                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                                                        >
                                                            <v-icon
                                                                name="hi-tag"
                                                                class="w-4 h-4 text-gray-500 dark:text-gray-400"
                                                                :class="{'w-5 h-5': isMaximized}"
                                                            />
                                                        </div>
                                                        <input
                                                            v-model="
                                                                userData.brand
                                                            "
                                                            type="text"
                                                            required
                                                            class="w-full p-2 pl-10 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                                                            :class="{'p-3 pl-12 text-base': isMaximized}"
                                                            placeholder="Brand name"
                                                        />
                                                    </div>
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                                        :class="{'text-base': isMaximized}"
                                                        >Serial Number</label
                                                    >
                                                    <div class="relative">
                                                        <div
                                                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                                                        >
                                                            <v-icon
                                                                name="hi-document-text"
                                                                class="w-4 h-4 text-gray-500 dark:text-gray-400"
                                                                :class="{'w-5 h-5': isMaximized}"
                                                            />
                                                        </div>
                                                        <input
                                                            v-model="
                                                                userData.serial_number
                                                            "
                                                            type="text"
                                                            required
                                                            class="w-full p-2 pl-10 pr-12 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                                                            :class="{'p-3 pl-12 pr-14 text-base': isMaximized}"
                                                            placeholder="123456789"
                                                            @input="
                                                                validateSerialNumber
                                                            "
                                                            maxlength="9"
                                                        />
                                                        <div
                                                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-xs text-gray-500"
                                                            :class="{'pr-4 text-sm': isMaximized}"
                                                        >
                                                            {{
                                                                userData.serial_number
                                                                    ? userData
                                                                          .serial_number
                                                                          .length
                                                                    : 0
                                                            }}/9
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                                        :class="{'text-base': isMaximized}"
                                                        >Size</label
                                                    >
                                                    <div class="relative">
                                                        <div
                                                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                                                        >
                                                            <v-icon
                                                                name="hi-arrows-expand"
                                                                class="w-4 h-4 text-gray-500 dark:text-gray-400"
                                                                :class="{'w-5 h-5': isMaximized}"
                                                            />
                                                        </div>
                                                        <input
                                                            v-model="
                                                                userData.size
                                                            "
                                                            type="text"
                                                            required
                                                            class="w-full p-2 pl-10 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                                                            :class="{'p-3 pl-12 text-base': isMaximized}"
                                                            placeholder="Size"
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Location Section -->
                                        <div
                                            class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 p-5 rounded-lg"
                                            :class="{'p-6': isMaximized}"
                                        >
                                            <h3
                                                class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center"
                                                :class="{'text-xl mb-5': isMaximized}"
                                            >
                                                <v-icon
                                                    name="hi-location-marker"
                                                    class="mr-2"
                                                    :scale="isMaximized ? '1.2' : '1'"
                                                />
                                                Location
                                            </h3>
                                            <div
                                                class="grid grid-cols-1 md:grid-cols-2 gap-4"
                                                :class="{'gap-6': isMaximized}"
                                            >
                                                <div>
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                                        :class="{'text-base': isMaximized}"
                                                        >Zone</label
                                                    >
                                                    <div class="relative">
                                                        <div
                                                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                                                        >
                                                            <v-icon
                                                                name="hi-location-marker"
                                                                class="w-4 h-4 text-gray-500 dark:text-gray-400"
                                                                :class="{'w-5 h-5': isMaximized}"
                                                            />
                                                        </div>
                                                        <select
                                                            v-model="
                                                                userData.zone
                                                            "
                                                            @change="
                                                                updateBarangays
                                                            "
                                                            required
                                                            class="w-full p-2 pl-10 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                                                            :class="{'p-3 pl-12 text-base': isMaximized}"
                                                        >
                                                            <option
                                                                value=""
                                                                disabled
                                                                selected
                                                            >
                                                                Select zone
                                                            </option>
                                                            <option
                                                                v-for="(
                                                                    zone, index
                                                                ) in Object.keys(
                                                                    zones
                                                                )"
                                                                :key="index"
                                                                :value="zone"
                                                            >
                                                                {{ zone }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                                        :class="{'text-base': isMaximized}"
                                                        >Barangay</label
                                                    >
                                                    <div class="relative">
                                                        <div
                                                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                                                        >
                                                            <v-icon
                                                                name="hi-location-marker"
                                                                class="w-4 h-4 text-gray-500 dark:text-gray-400"
                                                                :class="{'w-5 h-5': isMaximized}"
                                                            />
                                                        </div>
                                                        <select
                                                            v-model="
                                                                userData.barangay
                                                            "
                                                            required
                                                            class="w-full p-2 pl-10 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                                                            :class="{'p-3 pl-12 text-base': isMaximized}"
                                                            :disabled="
                                                                !userData.zone
                                                            "
                                                        >
                                                            <option
                                                                value=""
                                                                disabled
                                                                selected
                                                            >
                                                                Select barangay
                                                            </option>
                                                            <option
                                                                v-for="(
                                                                    barangay,
                                                                    index
                                                                ) in filteredBarangays"
                                                                :key="index"
                                                                :value="
                                                                    barangay
                                                                "
                                                            >
                                                                {{ barangay }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Generated Password Preview -->
                                    <Transition name="fade">
                                        <div
                                            v-if="userData.lastname"
                                            class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 p-4 rounded-lg"
                                            :class="{'p-5': isMaximized}"
                                        >
                                            <div
                                                class="flex items-center gap-2"
                                            >
                                                <v-icon
                                                    name="hi-key"
                                                    class="w-5 h-5 text-blue-600 dark:text-blue-400"
                                                    :class="{'w-6 h-6': isMaximized}"
                                                />
                                                <div>
                                                    <p
                                                        class="text-sm font-medium text-blue-700 dark:text-blue-300"
                                                        :class="{'text-base': isMaximized}"
                                                    >
                                                        Generated Password
                                                        Preview
                                                    </p>
                                                    <p
                                                        class="text-sm text-gray-700 dark:text-gray-300 mt-1 font-mono"
                                                        :class="{'text-base mt-2': isMaximized}"
                                                    >
                                                        {{
                                                            generatedPasswordPreview
                                                        }}_XXXX
                                                        <span
                                                            class="text-xs text-gray-500 dark:text-gray-400"
                                                            :class="{'text-sm': isMaximized}"
                                                            >(XXXX will be
                                                            replaced with user
                                                            ID after
                                                            creation)</span
                                                        >
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </Transition>
                                </div>

                                <!-- Action Buttons -->
                                <div
                                    class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-600"
                                    :class="{'mt-8 pt-6 space-x-4': isMaximized}"
                                >
                                    <button
                                        type="button"
                                        @click="$emit('close')"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 rounded-md transition-colors duration-200"
                                        :class="{'px-6 py-3 text-base': isMaximized}"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        :disabled="isSubmitting"
                                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed rounded-md transition-colors duration-200 flex items-center justify-center min-w-[120px]"
                                        :class="{'px-6 py-3 text-base min-w-[140px]': isMaximized}"
                                    >
                                        <span
                                            v-if="isSubmitting"
                                            class="flex items-center"
                                        >
                                            <svg
                                                class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                                :class="{'h-5 w-5': isMaximized}"
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
                                            Creating...
                                        </span>
                                        <span v-else class="flex items-center">
                                            <v-icon
                                                name="hi-plus"
                                                class="mr-1 w-4 h-4"
                                                :class="{'w-5 h-5': isMaximized}"
                                            />
                                            Create User
                                        </span>
                                    </button>
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
import { ref, computed, watch, onMounted } from "vue";
import Swal from "sweetalert2";
import { OhVueIcon, addIcons } from "oh-vue-icons";
import {
    HiUser,
    HiX,
    HiCreditCard,
    HiMail,
    HiPhone,
    HiCube,
    HiCalendar,
    HiTag,
    HiDocumentText,
    HiArrowsExpand,
    HiLocationMarker,
    HiUserGroup,
    HiKey,
    HiRefresh,
    HiPlus,
} from "oh-vue-icons/icons";
import { BiFullscreen, BiFullscreenExit } from "oh-vue-icons/icons";

addIcons(
    HiUser,
    HiX,
    HiCreditCard,
    HiMail,
    HiPhone,
    HiCube,
    HiCalendar,
    HiTag,
    HiDocumentText,
    HiArrowsExpand,
    HiLocationMarker,
    HiUserGroup,
    HiKey,
    HiRefresh,
    HiPlus,
    BiFullscreen,
    BiFullscreenExit
);



const props = defineProps({
    show: Boolean,
    zones: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(["close", "submit"]);

const userData = ref({
    name: "",
    lastname: "",
    accountNumber: "",
    email: "",
    phone: "",
    role: "customer",
    zone: "",
    barangay: "",
    date_installed: "",
    brand: "",
    serial_number: "",
    size: "",
});

const isSubmitting = ref(false);
const filteredBarangays = ref([]);
const isMaximized = ref(false);

const toggleMaximize = () => {
    isMaximized.value = !isMaximized.value;
};

const updateBarangays = () => {
    if (userData.value.zone && props.zones[userData.value.zone]) {
        filteredBarangays.value = props.zones[userData.value.zone];
    } else {
        filteredBarangays.value = [];
    }
    userData.value.barangay = "";
};

const formatAccountNumber = (e) => {
    let input = e.target.value.replace(/[^A-Z0-9-]/gi, "").toUpperCase();

    // Remove existing dashes for processing
    let cleanInput = input.replace(/-/g, "");

    // Format based on length (supports 8-9 alphanumeric characters)
    if (cleanInput.length >= 8) {
        // Start with basic format: 123-12-123 (10 characters total)
        let formatted =
            cleanInput.slice(0, 3) +
            "-" +
            cleanInput.slice(3, 5) +
            "-" +
            cleanInput.slice(5, 8);

        // Add optional 9th character if present (11 characters total)
        if (cleanInput.length >= 9) {
            formatted += cleanInput.slice(8, 9);
        }

        userData.value.accountNumber = formatted;
    } else if (cleanInput.length > 5) {
        // Partial format: 123-12-123 (without all digits)
        userData.value.accountNumber =
            cleanInput.slice(0, 3) +
            "-" +
            cleanInput.slice(3, 5) +
            "-" +
            cleanInput.slice(5);
    } else if (cleanInput.length > 3) {
        // Partial format: 123-12
        userData.value.accountNumber =
            cleanInput.slice(0, 3) + "-" + cleanInput.slice(3, 5);
    } else {
        userData.value.accountNumber = cleanInput;
    }
};

const validatePhoneNumber = (e) => {
    let value = e.target.value;
    value = value.replace(/[^0-9+]/g, "");

    if (value.startsWith("+")) {
        value = "+" + value.substring(1).replace(/\D/g, "").substring(0, 11);
    } else {
        value = value.substring(0, 11);
    }

    userData.value.phone = value;
};

const validateSerialNumber = (e) => {
    let value = e.target.value;
    value = value.replace(/\D/g, "");
    value = value.substring(0, 9);
    userData.value.serial_number = value;
};

const generatedPasswordPreview = computed(() => {
    if (userData.value.lastname.length >= 3) {
        return userData.value.lastname.substring(0, 3).toUpperCase();
    }
    return userData.value.lastname.toUpperCase();
});

const updateGeneratedPassword = () => {
    // For visual feedback only
};

watch(
    () => props.show,
    (newVal, oldVal) => {
        // When modal changes from open to closed, reset the form and maximize state
        if (oldVal === true && newVal === false) {
            resetForm();
            isSubmitting.value = false;
            isMaximized.value = false; // Reset to normal size when closing
        }
    }
);

onMounted(() => {
    resetForm();
});

const resetForm = () => {
    userData.value = {
        name: "",
        lastname: "",
        accountNumber: "",
        email: "",
        phone: "",
        role: "customer",
        zone: "",
        barangay: "",
        date_installed: "",
        brand: "",
        serial_number: "",
        size: "",
    };
    filteredBarangays.value = [];
};

const handleSubmit = async () => {
    isSubmitting.value = true;

    try {
        // Validate required fields based on role
        if (userData.value.role === "customer") {
            const cleanAccountNumber = userData.value.accountNumber.replace(
                /-/g,
                ""
            );
            if (
                cleanAccountNumber.length < 8 ||
                cleanAccountNumber.length > 9
            ) {
                Swal.fire({
                    icon: "error",
                    title: "Validation Error",
                    text: "Account number must be exactly 8 or 9 characters (format: XXX-XX-XXX or XXX-XX-XXXX)",
                });
                isSubmitting.value = false;
                return;
            }

            // Validate the format matches our pattern
            const accountNumberRegex = /^[0-9]{3}-[0-9]{2}-[0-9]{3}[A-Za-z]?$/;
            if (!accountNumberRegex.test(userData.value.accountNumber)) {
                Swal.fire({
                    icon: "error",
                    title: "Validation Error",
                    text: "Account number must be in format 123-12-123 or 123-12-123A",
                });
                isSubmitting.value = false;
                return;
            }

            if (userData.value.serial_number.length !== 9) {
                Swal.fire({
                    icon: "error",
                    title: "Validation Error",
                    text: "Serial number must be exactly 9 digits",
                });
                isSubmitting.value = false;
                return;
            }

            if (
                !userData.value.accountNumber ||
                !userData.value.date_installed ||
                !userData.value.brand ||
                !userData.value.size ||
                !userData.value.zone ||
                !userData.value.barangay
            ) {
                alert("Please fill all required customer fields");
                isSubmitting.value = false;
                return;
            }
        } else if (userData.value.role === "staff") {
            // Validate staff fields
            if (!userData.value.email) {
                Swal.fire({
                    icon: "error",
                    title: "Validation Error",
                    text: "Email is required for staff members",
                });
                isSubmitting.value = false;
                return;
            }
        }

        // Show spinner for 2 seconds before proceeding
        await new Promise((resolve) => setTimeout(resolve, 2000));

        // Prepare data - explicitly set customer fields to null for staff
        const submitData = {
            name: userData.value.name,
            lastname: userData.value.lastname,
            email: userData.value.email,
            phone: userData.value.phone.replace(/\D/g, ""),
            role: userData.value.role,
        };

        // Handle fields based on role
        if (userData.value.role === "customer") {
            submitData.account_number = userData.value.accountNumber;
            submitData.zone = userData.value.zone;
            submitData.barangay = userData.value.barangay;
            submitData.date_installed = userData.value.date_installed;
            submitData.brand = userData.value.brand;
            submitData.serial_number = userData.value.serial_number;
            submitData.size = userData.value.size;
        } else {
            // For staff, don't send account_number at all instead of sending null
            submitData.zone = null;
            submitData.barangay = null;
            submitData.date_installed = null;
            submitData.brand = null;
            submitData.serial_number = null;
            submitData.size = null;
            // Don't include account_number for staff
        }

        console.log("Submitting data:", submitData); // Debug log

        emit("submit", submitData);
    } catch (error) {
        console.error("Error in handleSubmit:", error);
        Swal.fire({
            icon: "error",
            title: "Submission Error",
            text: "An error occurred while submitting the form",
        });
    } finally {
        isSubmitting.value = false;
    }
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
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
