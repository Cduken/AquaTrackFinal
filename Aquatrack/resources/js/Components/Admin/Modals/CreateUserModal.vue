//CreateUserModal.vue
<template>
    <transition name="modal">
        <div v-if="show" class="fixed inset-0 z-[1000] overflow-hidden">
            <!-- Backdrop -->
            <div
                class="absolute inset-0 bg-black/60 transition-opacity duration-300"
                @click="$emit('close')"
            ></div>

            <!-- Modal Container -->
            <div class="fixed inset-0 flex items-center justify-center p-4">
                <!-- Modal Panel with transform for animation -->
                <div
                    class="relative w-full max-w-4xl transform transition-all duration-500 ease-out"
                    :class="{ 'max-w-6xl': isMaximized }"
                >
                    <div
                        class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl overflow-hidden"
                    >
                        <!-- Header -->
                        <div class="relative px-8 py-4 bg-[#172554]">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="p-2.5 bg-white/20 rounded-xl backdrop-blur-sm"
                                    >
                                        <User class="w-6 h-6 text-white" />
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-sm text-white">
                                            Register User
                                        </h2>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <!-- Maximize Button -->
                                    <button
                                        @click="toggleMaximize"
                                        class="p-2 text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200"
                                        :title="
                                            isMaximized
                                                ? 'Minimize'
                                                : 'Maximize'
                                        "
                                    >
                                        <Maximize2
                                            v-if="!isMaximized"
                                            class="w-5 h-5"
                                        />
                                        <Minimize2 v-else class="w-5 h-5" />
                                    </button>
                                    <!-- Close Button -->
                                    <button
                                        @click="$emit('close')"
                                        class="p-2 text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200"
                                    >
                                        <X class="w-5 h-5" />
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div
                            class="overflow-y-auto p-8"
                            :class="{ 'p-10': isMaximized }"
                            style="max-height: calc(100vh - 200px)"
                        >
                            <form @submit.prevent="handleSubmit">
                                <div
                                    class="space-y-8"
                                    :class="{ 'space-y-10': isMaximized }"
                                >
                                    <!-- Role Selection -->
                                    <div class="space-y-4">
                                        <div
                                            class="flex items-center space-x-2"
                                        >
                                            <Users
                                                class="w-5 h-5 text-gray-700 dark:text-gray-300"
                                            />
                                            <h3
                                                class="text-lg font-semibold text-gray-900 dark:text-white"
                                            >
                                                User Type
                                            </h3>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <label
                                                class="relative flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all"
                                                :class="
                                                    userData.role === 'staff'
                                                        ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                                                        : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600'
                                                "
                                            >
                                                <input
                                                    type="radio"
                                                    v-model="userData.role"
                                                    value="staff"
                                                    class="sr-only"
                                                />
                                                <div
                                                    class="flex items-center space-x-3 flex-1"
                                                >
                                                    <div
                                                        class="p-2 rounded-lg"
                                                        :class="
                                                            userData.role ===
                                                            'staff'
                                                                ? 'bg-blue-100 dark:bg-blue-800'
                                                                : 'bg-gray-100 dark:bg-gray-800'
                                                        "
                                                    >
                                                        <Briefcase
                                                            class="w-5 h-5"
                                                            :class="
                                                                userData.role ===
                                                                'staff'
                                                                    ? 'text-blue-600 dark:text-blue-400'
                                                                    : 'text-gray-600 dark:text-gray-400'
                                                            "
                                                        />
                                                    </div>
                                                    <div class="flex-1">
                                                        <p
                                                            class="font-medium"
                                                            :class="
                                                                userData.role ===
                                                                'staff'
                                                                    ? 'text-blue-900 dark:text-blue-100'
                                                                    : 'text-gray-900 dark:text-white'
                                                            "
                                                        >
                                                            Staff Member
                                                        </p>
                                                        <p
                                                            class="text-xs text-gray-500 dark:text-gray-400"
                                                        >
                                                            Internal employee
                                                        </p>
                                                    </div>
                                                    <CheckCircle2
                                                        v-if="
                                                            userData.role ===
                                                            'staff'
                                                        "
                                                        class="w-5 h-5 text-blue-600 dark:text-blue-400"
                                                    />
                                                </div>
                                            </label>
                                            <label
                                                class="relative flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all"
                                                :class="
                                                    userData.role === 'customer'
                                                        ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                                                        : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600'
                                                "
                                            >
                                                <input
                                                    type="radio"
                                                    v-model="userData.role"
                                                    value="customer"
                                                    class="sr-only"
                                                />
                                                <div
                                                    class="flex items-center space-x-3 flex-1"
                                                >
                                                    <div
                                                        class="p-2 rounded-lg"
                                                        :class="
                                                            userData.role ===
                                                            'customer'
                                                                ? 'bg-blue-100 dark:bg-blue-800'
                                                                : 'bg-gray-100 dark:bg-gray-800'
                                                        "
                                                    >
                                                        <User
                                                            class="w-5 h-5"
                                                            :class="
                                                                userData.role ===
                                                                'customer'
                                                                    ? 'text-blue-600 dark:text-blue-400'
                                                                    : 'text-gray-600 dark:text-gray-400'
                                                            "
                                                        />
                                                    </div>
                                                    <div class="flex-1">
                                                        <p
                                                            class="font-medium"
                                                            :class="
                                                                userData.role ===
                                                                'customer'
                                                                    ? 'text-blue-900 dark:text-blue-100'
                                                                    : 'text-gray-900 dark:text-white'
                                                            "
                                                        >
                                                            Customer
                                                        </p>
                                                        <p
                                                            class="text-xs text-gray-500 dark:text-gray-400"
                                                        >
                                                            Service subscriber
                                                        </p>
                                                    </div>
                                                    <CheckCircle2
                                                        v-if="
                                                            userData.role ===
                                                            'customer'
                                                        "
                                                        class="w-5 h-5 text-blue-600 dark:text-blue-400"
                                                    />
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Personal Information -->
                                    <div class="space-y-4">
                                        <div
                                            class="flex items-center space-x-2"
                                        >
                                            <UserCircle
                                                class="w-5 h-5 text-gray-700 dark:text-gray-300"
                                            />
                                            <h3
                                                class="text-lg font-semibold text-gray-900 dark:text-white"
                                            >
                                                Personal Information
                                            </h3>
                                        </div>
                                        <div
                                            class="grid grid-cols-1 md:grid-cols-2 gap-5"
                                        >
                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                                >
                                                    First Name
                                                </label>
                                                <div class="relative">
                                                    <User
                                                        class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                                                    />
                                                    <input
                                                        v-model="userData.name"
                                                        type="text"
                                                        required
                                                        class="w-full pl-11 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                                        placeholder="Enter First Name"
                                                    />
                                                </div>
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                                >
                                                    Last Name
                                                </label>
                                                <div class="relative">
                                                    <User
                                                        class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                                                    />
                                                    <input
                                                        v-model="
                                                            userData.lastname
                                                        "
                                                        type="text"
                                                        required
                                                        class="w-full pl-11 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                                        placeholder="Enter Last Name"
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
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                                >
                                                    Email Address
                                                </label>
                                                <div class="relative">
                                                    <Mail
                                                        class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                                                    />
                                                    <input
                                                        v-model="userData.email"
                                                        type="email"
                                                        :required="
                                                            userData.role ===
                                                            'staff'
                                                        "
                                                        class="w-full pl-11 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                                        placeholder="Enter Email Address"
                                                    />
                                                </div>
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                                >
                                                    Phone Number
                                                </label>
                                                <div class="relative">
                                                    <Phone
                                                        class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                                                    />
                                                    <input
                                                        v-model="userData.phone"
                                                        type="tel"
                                                        class="w-full pl-11 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                                        placeholder="Enter Phone Number (Optional)"
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
                                        class="space-y-8"
                                    >
                                        <!-- Equipment Information -->
                                        <div class="space-y-4">
                                            <div
                                                class="flex items-center space-x-2"
                                            >
                                                <Package
                                                    class="w-5 h-5 text-gray-700 dark:text-gray-300"
                                                />
                                                <h3
                                                    class="text-lg font-semibold text-gray-900 dark:text-white"
                                                >
                                                    Equipment Information
                                                </h3>
                                            </div>
                                            <div
                                                class="grid grid-cols-1 md:grid-cols-2 gap-5"
                                                :class="{
                                                    'md:grid-cols-3':
                                                        isMaximized,
                                                }"
                                            >
                                                <div>
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                                    >
                                                        Account Number
                                                    </label>
                                                    <div class="relative">
                                                        <CreditCard
                                                            class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                                                        />
                                                        <input
                                                            v-model="
                                                                userData.accountNumber
                                                            "
                                                            type="text"
                                                            required
                                                            class="w-full pl-11 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                                            placeholder="Enter Account Number"
                                                            @input="
                                                                formatAccountNumber
                                                            "
                                                            :maxlength="11"
                                                        />
                                                    </div>
                                                    <p
                                                        class="text-xs text-gray-500 mt-1.5"
                                                    >
                                                        Format: XXX-XX-XXX or
                                                        XXX-XX-XXXX
                                                    </p>
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                                    >
                                                        Installation Date
                                                    </label>
                                                    <div class="relative">
                                                        <Calendar
                                                            class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                                                        />
                                                        <input
                                                            v-model="
                                                                userData.date_installed
                                                            "
                                                            type="date"
                                                            required
                                                            class="w-full pl-11 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                                        />
                                                    </div>
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                                    >
                                                        Brand
                                                    </label>
                                                    <div class="relative">
                                                        <Tag
                                                            class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                                                        />
                                                        <input
                                                            v-model="
                                                                userData.brand
                                                            "
                                                            type="text"
                                                            required
                                                            class="w-full pl-11 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                                            placeholder="Enter Brand Name"
                                                        />
                                                    </div>
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                                    >
                                                        Serial Number
                                                    </label>
                                                    <div class="relative">
                                                        <Hash
                                                            class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                                                        />
                                                        <input
                                                            v-model="
                                                                userData.serial_number
                                                            "
                                                            type="text"
                                                            required
                                                            class="w-full pl-11 pr-16 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                                            placeholder="Enter Serial Number"
                                                            @input="
                                                                validateSerialNumber
                                                            "
                                                            maxlength="9"
                                                        />
                                                        <div
                                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-medium text-gray-500"
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
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                                    >
                                                        Size
                                                    </label>
                                                    <div class="relative">
                                                        <Ruler
                                                            class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                                                        />
                                                        <input
                                                            v-model="
                                                                userData.size
                                                            "
                                                            type="text"
                                                            required
                                                            class="w-full pl-11 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                                            placeholder="Size"
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Location -->
                                        <div class="space-y-4">
                                            <div
                                                class="flex items-center space-x-2"
                                            >
                                                <MapPin
                                                    class="w-5 h-5 text-gray-700 dark:text-gray-300"
                                                />
                                                <h3
                                                    class="text-lg font-semibold text-gray-900 dark:text-white"
                                                >
                                                    Location
                                                </h3>
                                            </div>
                                            <div
                                                class="grid grid-cols-1 md:grid-cols-2 gap-5"
                                            >
                                                <div>
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                                    >
                                                        Zone
                                                    </label>
                                                    <div class="relative">
                                                        <MapPin
                                                            class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 z-10 pointer-events-none"
                                                        />
                                                        <select
                                                            v-model="
                                                                userData.zone
                                                            "
                                                            @change="
                                                                updateBarangays
                                                            "
                                                            required
                                                            class="w-full pl-11 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all appearance-none"
                                                        >
                                                            <option
                                                                value=""
                                                                disabled
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
                                                        <ChevronDown
                                                            class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none"
                                                        />
                                                    </div>
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                                                    >
                                                        Barangay
                                                    </label>
                                                    <div class="relative">
                                                        <MapPin
                                                            class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 z-10 pointer-events-none"
                                                        />
                                                        <select
                                                            v-model="
                                                                userData.barangay
                                                            "
                                                            required
                                                            class="w-full pl-11 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all appearance-none disabled:opacity-50 disabled:cursor-not-allowed"
                                                            :disabled="
                                                                !userData.zone
                                                            "
                                                        >
                                                            <option
                                                                value=""
                                                                disabled
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
                                                        <ChevronDown
                                                            class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none"
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Password Preview -->
                                    <transition name="fade">
                                        <div
                                            v-if="userData.lastname"
                                            class="relative overflow-hidden rounded-xl bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-200 dark:border-blue-700 p-5"
                                        >
                                            <div
                                                class="flex items-start space-x-3"
                                            >
                                                <div
                                                    class="p-2 bg-blue-100 dark:bg-blue-800 rounded-lg"
                                                >
                                                    <Key
                                                        class="w-5 h-5 text-blue-600 dark:text-blue-400"
                                                    />
                                                </div>
                                                <div class="flex-1">
                                                    <p
                                                        class="text-sm font-semibold text-blue-900 dark:text-blue-100"
                                                    >
                                                        Generated Password
                                                        Preview
                                                    </p>
                                                    <p
                                                        class="text-lg font-mono font-medium text-gray-900 dark:text-white mt-2"
                                                    >
                                                        {{
                                                            generatedPasswordPreview
                                                        }}_XXXX
                                                    </p>
                                                    <p
                                                        class="text-xs text-gray-600 dark:text-gray-400 mt-1"
                                                    >
                                                        XXXX will be replaced
                                                        with user ID after
                                                        creation
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </transition>
                                </div>
                            </form>
                        </div>

                        <!-- Footer - Updated to match EditUserModal -->
                        <div
                            class="flex items-center justify-end space-x-3 px-8 py-2 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50"
                        >
                            <button
                                @click="$emit('close')"
                                type="button"
                                class="px-6 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-all duration-200"
                            >
                                Cancel
                            </button>
                            <button
                                @click="handleSubmit"
                                :disabled="isSubmitting"
                                type="button"
                                class="px-6 py-3 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed rounded-lg transition-all duration-200 flex items-center space-x-2 shadow-lg shadow-blue-500/30"
                            >
                                <Loader2
                                    v-if="isSubmitting"
                                    class="w-4 h-4 animate-spin"
                                />
                                <UserPlus v-else class="w-4 h-4" />
                                <span>{{
                                    isSubmitting ? "Creating..." : "Create User"
                                }}</span>
                            </button>
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
import {
    User,
    Users,
    Mail,
    Phone,
    CreditCard,
    Calendar,
    Tag,
    Hash,
    Ruler,
    MapPin,
    Key,
    X,
    Maximize2,
    Minimize2,
    UserCircle,
    Package,
    ChevronDown,
    CheckCircle2,
    Briefcase,
    UserPlus,
    Loader2,
} from "lucide-vue-next";

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
    let cleanInput = input.replace(/-/g, "");

    if (cleanInput.length >= 8) {
        let formatted =
            cleanInput.slice(0, 3) +
            "-" +
            cleanInput.slice(3, 5) +
            "-" +
            cleanInput.slice(5, 8);

        if (cleanInput.length >= 9) {
            formatted += cleanInput.slice(8, 9);
        }

        userData.value.accountNumber = formatted;
    } else if (cleanInput.length > 5) {
        userData.value.accountNumber =
            cleanInput.slice(0, 3) +
            "-" +
            cleanInput.slice(3, 5) +
            "-" +
            cleanInput.slice(5);
    } else if (cleanInput.length > 3) {
        userData.value.accountNumber =
            cleanInput.slice(0, 3) + "-" + cleanInput.slice(3, 5);
    } else {
        userData.value.accountNumber = cleanInput;
    }
};

const validatePhoneNumber = (e) => {
    let value = e.target.value;

    // If empty, just clear the field
    if (!value || value.trim() === "") {
        userData.value.phone = "";
        return;
    }

    value = value.replace(/[^0-9+]/g, "");

    if (value.startsWith("+")) {
        value = "+" + value.substring(1).replace(/\D/g, "").substring(0, 11);
    } else {
        value = value.substring(0, 11);
    }

    userData.value.phone = value;

    // Add real-time validation feedback only if there's input
    const phoneInput = e.target;
    const cleanPhone = value.replace(/\D/g, "");
    const phoneRegex = /^(\+63\d{10}|09\d{9})$/;

    if (cleanPhone && !phoneRegex.test(cleanPhone)) {
        phoneInput.classList.add("border-red-500");
        phoneInput.classList.remove("border-gray-300");
    } else {
        phoneInput.classList.remove("border-red-500");
        phoneInput.classList.add("border-gray-300");
    }
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
        if (oldVal === true && newVal === false) {
            resetForm();
            isSubmitting.value = false;
            isMaximized.value = false;
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
                Swal.fire({
                    icon: "error",
                    title: "Validation Error",
                    text: "Please fill all required customer fields",
                });
                isSubmitting.value = false;
                return;
            }
        } else if (userData.value.role === "staff") {
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

        // Validate phone number format only if provided
        if (userData.value.phone && userData.value.phone.trim() !== "") {
            const phoneRegex = /^(\+63\d{10}|09\d{9})$/;
            const cleanPhone = userData.value.phone.replace(/\D/g, "");

            if (!phoneRegex.test(cleanPhone)) {
                Swal.fire({
                    icon: "error",
                    title: "Validation Error",
                    text: "Phone number must be in format 09123456789 or +639123456789",
                });
                isSubmitting.value = false;
                return;
            }
        }

        await new Promise((resolve) => setTimeout(resolve, 2000));

        const submitData = {
            name: userData.value.name,
            lastname: userData.value.lastname,
            email: userData.value.email,
            phone: userData.value.phone
                ? userData.value.phone.replace(/\D/g, "")
                : null, // Set to null if empty
            role: userData.value.role,
        };

        if (userData.value.role === "customer") {
            submitData.account_number = userData.value.accountNumber;
            submitData.zone = userData.value.zone;
            submitData.barangay = userData.value.barangay;
            submitData.date_installed = userData.value.date_installed;
            submitData.brand = userData.value.brand;
            submitData.serial_number = userData.value.serial_number;
            submitData.size = userData.value.size;
        } else {
            submitData.zone = null;
            submitData.barangay = null;
            submitData.date_installed = null;
            submitData.brand = null;
            submitData.serial_number = null;
            submitData.size = null;
        }

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
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* Modal transition - slide from top to center */
.modal-enter-active {
    transition: opacity 0.3s ease;
}

.modal-leave-active {
    transition: opacity 0.2s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-enter-active > div > div {
    transition: transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1),
        opacity 0.5s ease;
}

.modal-enter-from > div > div {
    transform: translateY(-100%) scale(0.9);
    opacity: 0;
}

.modal-leave-to > div > div {
    transform: scale(0.95);
    opacity: 0;
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

/* Remove default select arrow in some browsers */
select {
    background-image: none;
}
</style>
