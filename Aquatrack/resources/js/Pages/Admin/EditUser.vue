<template>
    <AdminLayout>
        <!-- Loading State -->
        <div v-if="loading" class="min-h-screen flex items-center justify-center bg-gray-50">
            <div class="text-center">
                <div class="relative">
                    <Loader2 class="w-12 h-12 text-blue-600 animate-spin mx-auto mb-4" />
                    <div class="absolute inset-0 bg-blue-600/10 rounded-full animate-ping"></div>
                </div>
                <p class="text-gray-600 mt-2">Loading user information...</p>
            </div>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
            <div class="text-center max-w-md">
                <div class="relative inline-block mb-4">
                    <AlertCircle class="w-16 h-16 text-red-500 mx-auto" />
                    <div class="absolute -inset-4 bg-red-100 rounded-full animate-pulse"></div>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">User Not Found</h3>
                <p class="text-gray-600 mb-6">{{ error }}</p>
                <Link
                    :href="route('admin.users')"
                    class="inline-flex items-center px-5 py-3 bg-blue-600 text-white font-medium hover:bg-blue-700 transition-colors"
                >
                    <ArrowLeft class="w-4 h-4 mr-2" />
                    Back to Users
                </Link>
            </div>
        </div>

        <!-- Edit User Content -->
        <div v-else-if="user" class="min-h-screen bg-gray-50">
            <!-- Header Section -->
            <div class="bg-white shadow-sm border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="py-4">
                        <!-- Main Header -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center flex-wrap gap-2 mb-2">
                                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Edit User</h1>
                                    <span :class="statusClass" class="px-3 py-1 text-xs font-semibold">
                                        {{ user.enabled ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <div class="flex items-center text-gray-500">
                                    <User class="w-4 h-4 mr-1.5" />
                                    <span class="text-sm">{{ user.name }} {{ user.lastname }}</span>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-center space-x-3">
                                <Link
                                    :href="route('admin.users.show', user.id)"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition-colors"
                                >
                                    <Eye class="w-4 h-4 mr-2" />
                                    View Details
                                </Link>
                                <Link
                                    :href="route('admin.users')"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-600 bg-white border border-blue-200 hover:bg-blue-50 transition-colors"
                                >
                                    <ArrowLeft class="w-4 h-4 mr-2" />
                                    Back to Users
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="bg-white shadow-sm border border-gray-200">
                    <!-- Loading State -->
                    <div v-if="editLoading" class="p-8 text-center">
                        <Loader2 class="w-8 h-8 text-blue-600 animate-spin mx-auto mb-2" />
                        <p class="text-gray-600">Saving changes...</p>
                    </div>

                    <!-- Error Message -->
                    <div v-else-if="editErrorMessage" class="p-4 bg-red-50 border-b border-red-200">
                        <div class="flex items-start space-x-3">
                            <AlertCircle class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" />
                            <div>
                                <p class="text-sm font-medium text-red-800">{{ editErrorMessage }}</p>
                            </div>
                        </div>
                    </div>

                    <form @submit.prevent="submitForm" class="divide-y divide-gray-200">
                        <!-- Profile Information -->
                        <div class="p-6 space-y-6">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <User class="w-5 h-5 text-gray-600 mr-2" />
                                Profile Information
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        First Name *
                                    </label>
                                    <input
                                        v-model="formData.name"
                                        type="text"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        :class="errors.name ? 'border-red-500' : 'border-gray-300'"
                                    />
                                    <p v-if="errors.name" class="text-red-500 text-xs mt-1">{{ errors.name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Last Name *
                                    </label>
                                    <input
                                        v-model="formData.lastname"
                                        type="text"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        :class="errors.lastname ? 'border-red-500' : 'border-gray-300'"
                                    />
                                    <p v-if="errors.lastname" class="text-red-500 text-xs mt-1">{{ errors.lastname }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Email Address
                                    </label>
                                    <input
                                        v-model="formData.email"
                                        type="email"
                                        class="w-full px-3 py-2 border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        :class="errors.email ? 'border-red-500' : 'border-gray-300'"
                                    />
                                    <p v-if="errors.email" class="text-red-500 text-xs mt-1">{{ errors.email }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Phone Number
                                    </label>
                                    <input
                                        v-model="formData.phone"
                                        type="tel"
                                        class="w-full px-3 py-2 border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        :class="errors.phone ? 'border-red-500' : 'border-gray-300'"
                                        placeholder="09XXXXXXXXX or +63XXXXXXXXX"
                                    />
                                    <p v-if="errors.phone" class="text-red-500 text-xs mt-1">{{ errors.phone }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Water Meter Information -->
                        <div class="p-6 space-y-6">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <Droplet class="w-5 h-5 text-gray-600 mr-2" />
                                Water Meter Information
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Serial Number *
                                    </label>
                                    <input
                                        v-model="formData.serial_number"
                                        type="text"
                                        maxlength="9"
                                        required
                                        @input="restrictToNumbers"
                                        class="w-full px-3 py-2 border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        :class="errors.serial_number ? 'border-red-500' : 'border-gray-300'"
                                    />
                                    <p v-if="errors.serial_number" class="text-red-500 text-xs mt-1">{{ errors.serial_number }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Meter Size *
                                    </label>
                                    <input
                                        v-model="formData.size"
                                        type="text"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        :class="errors.size ? 'border-red-500' : 'border-gray-300'"
                                    />
                                    <p v-if="errors.size" class="text-red-500 text-xs mt-1">{{ errors.size }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Brand *
                                    </label>
                                    <input
                                        v-model="formData.brand"
                                        type="text"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        :class="errors.brand ? 'border-red-500' : 'border-gray-300'"
                                    />
                                    <p v-if="errors.brand" class="text-red-500 text-xs mt-1">{{ errors.brand }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Installation Date *
                                    </label>
                                    <input
                                        v-model="formData.date_installed"
                                        type="date"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        :class="errors.date_installed ? 'border-red-500' : 'border-gray-300'"
                                    />
                                    <p v-if="errors.date_installed" class="text-red-500 text-xs mt-1">{{ errors.date_installed }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Location Information -->
                        <div class="p-6 space-y-6">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <MapPin class="w-5 h-5 text-gray-600 mr-2" />
                                Location Information
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Zone *
                                    </label>
                                    <select
                                        v-model="formData.zone"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        :class="errors.zone ? 'border-red-500' : 'border-gray-300'"
                                    >
                                        <option value="">Select Zone</option>
                                        <option v-for="(zone, zoneName) in zones" :key="zoneName" :value="zoneName">
                                            {{ zoneName }}
                                        </option>
                                    </select>
                                    <p v-if="errors.zone" class="text-red-500 text-xs mt-1">{{ errors.zone }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Barangay *
                                    </label>
                                    <select
                                        v-model="formData.barangay"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        :class="errors.barangay ? 'border-red-500' : 'border-gray-300'"
                                    >
                                        <option value="">Select Barangay</option>
                                        <option v-for="barangay in getBarangays(formData.zone)" :key="barangay" :value="barangay">
                                            {{ barangay }}
                                        </option>
                                    </select>
                                    <p v-if="errors.barangay" class="text-red-500 text-xs mt-1">{{ errors.barangay }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="p-6 bg-gray-50 flex items-center justify-end space-x-3">
                            <Link
                                :href="route('admin.users')"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition-colors"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="editLoading"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center space-x-2"
                            >
                                <Loader2 v-if="editLoading" class="w-4 h-4 animate-spin" />
                                <Save v-else class="w-4 h-4" />
                                <span>{{ editLoading ? 'Saving...' : 'Save Changes' }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {
    ArrowLeft,
    Loader2,
    AlertCircle,
    User,
    Eye,
    Droplet,
    MapPin,
    Save
} from 'lucide-vue-next';

const props = defineProps({
    user: {
        type: Object,
        default: null
    },
    error: {
        type: String,
        default: null
    },
    zones: {
        type: Object,
        default: () => ({})
    }
});

const loading = ref(false);
const editLoading = ref(false);
const editErrorMessage = ref('');
const errors = reactive({});

const formData = reactive({
    name: props.user?.name || '',
    lastname: props.user?.lastname || '',
    email: props.user?.email || '',
    phone: props.user?.phone || '',
    serial_number: props.user?.serial_number || '',
    size: props.user?.size || '',
    brand: props.user?.brand || '',
    date_installed: props.user?.date_installed ? formatDateForInput(props.user.date_installed) : '',
    zone: props.user?.zone || '',
    barangay: props.user?.barangay || ''
});

const statusClass = computed(() => {
    return props.user?.enabled
        ? 'bg-green-100 text-green-800'
        : 'bg-red-100 text-red-800';
});

const formatDateForInput = (dateString) => {
    const date = new Date(dateString);
    return date.toISOString().split('T')[0];
};

const restrictToNumbers = () => {
    formData.serial_number = formData.serial_number.replace(/[^0-9]/g, '');
};

const getBarangays = (zone) => {
    return props.zones[zone] || [];
};

const submitForm = async () => {
    editLoading.value = true;
    editErrorMessage.value = '';
    Object.keys(errors).forEach(key => delete errors[key]);

    try {
        await router.put(`/admin/users/${props.user.id}`, formData, {
            preserveScroll: true,
            onSuccess: () => {
                router.visit(route('admin.users'), {
                    preserveScroll: true,
                    onSuccess: () => {
                        // Success message will be shown on users list page
                    }
                });
            },
            onError: (err) => {
                if (err.errors) {
                    Object.keys(errors).forEach(key => delete errors[key]);
                    Object.keys(err.errors).forEach(key => {
                        errors[key] = err.errors[key];
                    });
                }
                editErrorMessage.value = err.message || 'Failed to update user. Please check the form.';
            }
        });
    } catch (error) {
        editErrorMessage.value = 'An unexpected error occurred.';
    } finally {
        editLoading.value = false;
    }
};
</script>
