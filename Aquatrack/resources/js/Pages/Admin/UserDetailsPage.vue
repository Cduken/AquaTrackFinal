<template>
    <AdminLayout>
        <!-- Loading State -->
        <div v-if="loading" class="min-h-screen flex items-center justify-center bg-gray-50">
            <div class="text-center">
                <div class="relative">
                    <Loader2 class="w-12 h-12 text-blue-600 animate-spin mx-auto mb-4" />
                    <div class="absolute inset-0 bg-blue-600/10 rounded-full animate-ping"></div>
                </div>
                <p class="text-gray-600 mt-2">Loading user details...</p>
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
                    class="inline-flex items-center px-5 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium rounded-sm shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105"
                >
                    <ArrowLeft class="w-4 h-4 mr-2" />
                    Back to Users
                </Link>
            </div>
        </div>

        <!-- User Details Content -->
        <div v-else-if="user" class="min-h-screen bg-gray-50">
            <!-- Header Section -->
            <div class="bg-white shadow-sm border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-6">
                    <div class="py-4">
                        <!-- Main Header -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center flex-wrap gap-2 mb-2">
                                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">User Details</h1>
                                    <span :class="statusClass" class="px-3 py-1 rounded-full text-xs font-semibold shadow-sm">
                                        {{ user.enabled ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <div class="flex items-center text-gray-500">
                                    <Calendar class="w-4 h-4 mr-1.5" />
                                    <span class="text-sm">Member since {{ formatDate(user.created_at) }}</span>
                                </div>
                            </div>

                            <!-- Back Button -->
                            <div class="flex justify-start lg:justify-end  sm:mt-0">
                                <Link
                                    :href="route('admin.users')"
                                    class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors duration-200 group"
                                >
                                    <div class="flex items-center justify-center w-8 h-8 bg-blue-100 rounded-lg mr-2 group-hover:bg-blue-200 transition-colors">
                                        <ArrowLeft class="w-4 h-4" />
                                    </div>
                                    Back to Users
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="max-w-7xl mx-auto6">
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                    <!-- Left Column -->
                    <div class="xl:col-span-2 space-y-6">
                        <!-- Profile Information Card -->
                        <div class="bg-white rounded-sm shadow-sm border border-gray-200 overflow-hidden">
                            <div class="px-5 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-blue-100">
                                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                                    <div class="flex items-center justify-center w-8 h-8 bg-blue-100 rounded-lg mr-3">
                                        <User class="w-5 h-5 text-blue-600" />
                                    </div>
                                    Profile Information
                                </h2>
                            </div>
                            <div class="p-5">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Full Name -->
                                    <div class="flex items-start gap-3 p-3 bg-blue-50 rounded-sm border border-blue-100">
                                        <User class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" />
                                        <div>
                                            <p class="text-sm text-blue-700 font-medium">Full Name</p>
                                            <p class="font-semibold text-gray-900">
                                                {{ user.name }} {{ user.lastname }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Email -->
                                    <div class="flex items-start gap-3 p-3 bg-blue-50 rounded-sm border border-blue-100">
                                        <Mail class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" />
                                        <div>
                                            <p class="text-sm text-blue-700 font-medium">Email</p>
                                            <p class="font-semibold text-gray-900">
                                                {{ user.email || 'N/A' }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Phone Number -->
                                    <div class="flex items-start gap-3 p-3 bg-blue-50 rounded-sm border border-blue-100">
                                        <Phone class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" />
                                        <div>
                                            <p class="text-sm text-blue-700 font-medium">Phone Number</p>
                                            <p class="font-semibold text-gray-900">
                                                {{ user.phone || 'N/A' }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Account Number -->
                                    <div class="flex items-start gap-3 p-3 bg-blue-50 rounded-sm border border-blue-100">
                                        <CreditCard class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" />
                                        <div>
                                            <p class="text-sm text-blue-700 font-medium">Account Number</p>
                                            <p class="font-semibold text-gray-900">
                                                {{ user.account_number || 'N/A' }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- User ID -->
                                    <div class="flex items-start gap-3 p-3 bg-blue-50 rounded-sm border border-blue-100">
                                        <IdCard class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" />
                                        <div>
                                            <p class="text-sm text-blue-700 font-medium">User ID</p>
                                            <p class="font-semibold text-gray-900">
                                                {{ user.id }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Role -->
                                    <div class="flex items-start gap-3 p-3 bg-blue-50 rounded-sm border border-blue-100">
                                        <Shield class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" />
                                        <div>
                                            <p class="text-sm text-blue-700 font-medium">Role</p>
                                            <p class="font-semibold text-gray-900">
                                                {{ capitalizeRole(user.role) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Water Meter Information Card -->
                        <div class="bg-white rounded-sm shadow-sm border border-gray-200 overflow-hidden">
                            <div class="px-5 py-4 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-green-100">
                                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                                    <div class="flex items-center justify-center w-8 h-8 bg-green-100 rounded-lg mr-3">
                                        <Droplets class="w-5 h-5 text-green-600" />
                                    </div>
                                    Water Meter Information
                                </h2>
                            </div>
                            <div class="p-5">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Serial Number -->
                                    <div class="flex items-start gap-3 p-3 bg-green-50 rounded-sm border border-green-100">
                                        <Barcode class="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" />
                                        <div>
                                            <p class="text-sm text-green-700 font-medium">Serial Number</p>
                                            <p class="font-semibold text-gray-900">
                                                {{ user.serial_number || 'N/A' }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Meter Size -->
                                    <div class="flex items-start gap-3 p-3 bg-green-50 rounded-sm border border-green-100">
                                        <Ruler class="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" />
                                        <div>
                                            <p class="text-sm text-green-700 font-medium">Meter Size</p>
                                            <p class="font-semibold text-gray-900">
                                                {{ user.size || 'N/A' }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Brand -->
                                    <div class="flex items-start gap-3 p-3 bg-green-50 rounded-sm border border-green-100">
                                        <Building class="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" />
                                        <div>
                                            <p class="text-sm text-green-700 font-medium">Brand</p>
                                            <p class="font-semibold text-gray-900">
                                                {{ user.brand || 'N/A' }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Date Installed -->
                                    <div class="flex items-start gap-3 p-3 bg-green-50 rounded-sm border border-green-100">
                                        <Calendar class="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" />
                                        <div>
                                            <p class="text-sm text-green-700 font-medium">Date Installed</p>
                                            <p class="font-semibold text-gray-900">
                                                {{ user.date_installed ? formatDate(user.date_installed) : 'N/A' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <!-- User Profile Card -->
                        <div class="bg-white rounded-sm shadow-sm border border-gray-200 overflow-hidden">
                            <div class="px-5 py-4 bg-gradient-to-r from-purple-50 to-violet-50 border-b border-purple-100">
                                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                                    <div class="flex items-center justify-center w-8 h-8 bg-purple-100 rounded-lg mr-3">
                                        <UserCircle class="w-5 h-5 text-purple-600" />
                                    </div>
                                    User Profile
                                </h2>
                            </div>
                            <div class="p-5">
                                <div class="flex flex-col items-center text-center">
                                    <div class="relative">
                                        <div class="h-24 w-24 rounded-full border-4 border-white shadow-lg overflow-hidden bg-white">
                                            <img
                                                v-if="user.avatar_url"
                                                :src="user.avatar_url"
                                                :alt="user.name"
                                                class="h-full w-full object-cover"
                                            />
                                            <div
                                                v-else
                                                class="h-full w-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-xl"
                                            >
                                                {{ userInitials(user.name) }}
                                            </div>
                                        </div>
                                        <div :class="statusDotClass" class="absolute bottom-2 right-2 w-4 h-4 border-2 border-white rounded-full"></div>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-900 mt-4">
                                        {{ user.name }} {{ user.lastname }}
                                    </h3>
                                    <p class="text-gray-600 mt-1">{{ user.email }}</p>
                                    <div class="flex items-center mt-3 space-x-2">
                                        <span :class="roleClass" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold">
                                            <Shield class="w-3 h-3 mr-1" />
                                            {{ capitalizeRole(user.role) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Location Information Card -->
                        <div class="bg-white rounded-sm shadow-sm border border-gray-200 overflow-hidden">
                            <div class="px-5 py-4 bg-gradient-to-r from-orange-50 to-amber-50 border-b border-orange-100">
                                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                                    <div class="flex items-center justify-center w-8 h-8 bg-orange-100 rounded-lg mr-3">
                                        <MapPin class="w-5 h-5 text-orange-600" />
                                    </div>
                                    Location Information
                                </h2>
                            </div>
                            <div class="p-5">
                                <div class="space-y-4">
                                    <!-- Zone -->
                                    <div class="flex items-start gap-3 p-3 bg-orange-50 rounded-sm border border-orange-100">
                                        <Map class="w-5 h-5 text-orange-600 mt-0.5 flex-shrink-0" />
                                        <div>
                                            <p class="text-sm text-orange-700 font-medium">Zone</p>
                                            <p class="font-semibold text-gray-900">
                                                {{ user.zone || 'N/A' }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Barangay -->
                                    <div class="flex items-start gap-3 p-3 bg-orange-50 rounded-sm border border-orange-100">
                                        <Navigation class="w-5 h-5 text-orange-600 mt-0.5 flex-shrink-0" />
                                        <div>
                                            <p class="text-sm text-orange-700 font-medium">Barangay</p>
                                            <p class="font-semibold text-gray-900">
                                                {{ user.barangay || 'N/A' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Account Timeline Card -->
                        <div class="bg-white rounded-sm shadow-sm border border-gray-200 overflow-hidden">
                            <div class="px-5 py-4 bg-gradient-to-r from-gray-50 to-slate-50 border-b border-gray-200">
                                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                                    <div class="flex items-center justify-center w-8 h-8 bg-gray-100 rounded-lg mr-3">
                                        <Clock class="w-5 h-5 text-gray-600" />
                                    </div>
                                    Account Timeline
                                </h2>
                            </div>
                            <div class="p-5">
                                <div class="space-y-4">
                                    <div class="flex items-start gap-4">
                                        <div class="flex-shrink-0 relative">
                                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center shadow-sm">
                                                <UserPlus class="w-5 h-5 text-blue-600" />
                                            </div>
                                            <div class="absolute top-10 bottom-0 left-1/2 w-0.5 bg-blue-200 transform -translate-x-1/2"></div>
                                        </div>
                                        <div class="flex-1 pb-4">
                                            <p class="font-semibold text-gray-900">Account Created</p>
                                            <p class="text-sm text-gray-600 mt-1">{{ formatDateTime(user.created_at) }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center shadow-sm">
                                                <Activity class="w-5 h-5 text-gray-600" />
                                            </div>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">Current Status</p>
                                            <p class="text-sm text-gray-600 mt-1">{{ user.enabled ? 'Active' : 'Inactive' }}</p>
                                            <p class="text-xs text-gray-500 mt-1">Account status</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {
    ArrowLeft,
    Loader2,
    AlertCircle,
    User,
    Mail,
    Phone,
    CreditCard,
    IdCard,
    Shield,
    Droplets,
    Barcode,
    Ruler,
    Building,
    Calendar,
    UserCircle,
    MapPin,
    Map,
    Navigation,
    Clock,
    UserPlus,
    Activity
} from 'lucide-vue-next';

const props = defineProps({
    user: {
        type: Object,
        default: null
    },
    error: {
        type: String,
        default: null
    }
});

const loading = ref(false);

// Computed properties - FIXED: Use props.user instead of user.value
const statusClass = computed(() => {
    return props.user?.enabled
        ? 'bg-green-100 text-green-800 border border-green-200'
        : 'bg-red-100 text-red-800 border border-red-200';
});

const roleClass = computed(() => {
    const role = props.user?.role;
    return {
        'bg-blue-100 text-blue-800': role === 'admin',
        'bg-green-100 text-green-800': role === 'customer',
        'bg-purple-100 text-purple-800': role === 'staff',
    };
});

const statusDotClass = computed(() => {
    return props.user?.enabled ? 'bg-green-500' : 'bg-red-500';
});

// Methods
const userInitials = (name) => {
    if (!name) return '?';
    return name
        .split(' ')
        .map((n) => n[0])
        .join('')
        .toUpperCase()
        .substring(0, 2);
};

const capitalizeRole = (role) => {
    if (!role) return '';
    return role.charAt(0).toUpperCase() + role.slice(1);
};

const formatDate = (dateString) => {
    return dateString ? new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    }) : 'N/A';
};

const formatDateTime = (dateString) => {
    return dateString ? new Date(dateString).toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }) : 'N/A';
};
</script>

<style scoped>
/* Custom scrollbar */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
}

::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
