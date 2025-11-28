<template>
    <StaffLayout>
        <div class="min-h-screen lg:p-3">
            <!-- Header -->
            <div class="mb-5">
                <div class="flex items-center gap-2 mb-1">
                    <Droplet class="w-5 h-5 text-blue-600" />
                    <h1 class="text-lg font-bold text-gray-900">
                        Meter Readings
                    </h1>
                </div>
                <p class="text-gray-500 text-xs">
                    Record water consumption
                </p>
            </div>

            <!-- Search Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 mb-4">
                <div class="flex items-center gap-2 mb-3">
                    <Search class="w-4 h-4 text-blue-600" />
                    <h2 class="font-semibold text-gray-900 text-sm">Find Customer</h2>
                </div>

                <!-- Search Input -->
                <div class="relative mb-2">
                    <Search class="absolute left-2 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <input
                        v-model="searchQuery"
                        type="text"
                        class="w-full pl-8 pr-8 py-2.5 bg-gray-50 border border-gray-200 rounded text-sm text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Name, account, or meter serial..."
                        @input="debouncedSearch"
                    />
                    <button
                        v-if="searchQuery"
                        @click="clearSearch"
                        class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                    >
                        <X class="w-4 h-4" />
                    </button>
                </div>

                <!-- Search Button -->
                <button
                    @click="searchUsers"
                    :disabled="isSearching || !searchQuery.trim()"
                    class="w-full py-2.5 bg-blue-600 text-white rounded text-sm font-medium flex items-center justify-center gap-1 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <Search v-if="!isSearching" class="w-4 h-4" />
                    <Loader2 v-else class="w-4 h-4 animate-spin" />
                    <span>{{ isSearching ? "Searching..." : "Search" }}</span>
                </button>
            </div>

            <!-- Loading State -->
            <div v-if="isSearching" class="flex justify-center py-8">
                <div class="text-center">
                    <Loader2 class="w-6 h-6 text-blue-600 animate-spin mx-auto mb-2" />
                    <p class="text-gray-600 text-xs">Searching customers...</p>
                </div>
            </div>

            <!-- Search Results -->
            <div v-if="searchPerformed && !isSearching" class="w-full">
                <!-- Results Found -->
                <div v-if="searchResults.length > 0">
                    <div class="flex items-center gap-1 mb-3">
                        <Users class="w-4 h-4 text-gray-500" />
                        <p class="text-xs font-medium text-gray-600">
                            {{ searchResults.length }} found
                        </p>
                    </div>

                    <!-- Results List -->
                    <div class="space-y-2">
                        <div
                            v-for="user in searchResults"
                            :key="user.id"
                            @click="openReadingForm(user)"
                            class="bg-white rounded border border-gray-200 p-3 hover:border-blue-300 cursor-pointer active:bg-gray-50"
                        >
                            <!-- User Header -->
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <!-- Avatar -->
                                    <div class="flex-shrink-0">
                                        <div
                                            v-if="user.avatar_url"
                                            class="w-8 h-8 rounded overflow-hidden border border-gray-200"
                                        >
                                            <img
                                                :src="user.avatar_url"
                                                :alt="user.name"
                                                class="w-full h-full object-cover"
                                            />
                                        </div>
                                        <div
                                            v-else
                                            class="w-8 h-8 bg-blue-500 rounded flex items-center justify-center"
                                        >
                                            <User class="w-4 h-4 text-white" />
                                        </div>
                                    </div>

                                    <!-- Name and Account -->
                                    <div class="min-w-0 flex-1">
                                        <h3 class="font-semibold text-gray-900 text-sm truncate">
                                            {{ user.name }} {{ user.lastname }}
                                        </h3>

                                    </div>
                                </div>

                                <!-- Action Indicator -->
                                <ChevronRight class="w-4 h-4 text-gray-400 flex-shrink-0" />
                            </div>

                            <!-- Details -->
                            <div class="space-y-1.5 text-xs">
                                <!-- Address -->
                                <div class="flex items-start gap-1.5">
                                    <MapPin class="w-3 h-3 text-gray-400 mt-0.5 flex-shrink-0" />
                                    <p class="text-gray-900 truncate">{{ user.address }}</p>
                                </div>

                                <!-- Phone -->
                                <div v-if="user.phone" class="flex items-center gap-1.5">
                                    <Phone class="w-3 h-3 text-gray-400" />
                                    <p class="text-gray-900">{{ user.phone }}</p>
                                </div>

                                <!-- Meter Details -->
                                <div v-if="user.serial_number" class="flex items-center gap-1.5">
                                    <Cpu class="w-3 h-3 text-gray-400" />
                                    <p class="text-gray-900">{{ user.serial_number }}</p>
                                    <template v-if="user.brand || user.size">
                                        <span class="text-gray-300">•</span>
                                        <p class="text-gray-600">
                                            {{ user.brand }} {{ user.size ? `${user.size}mm` : '' }}
                                        </p>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- No Results -->
                <div v-else class="text-center py-8">
                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                        <SearchX class="w-5 h-5 text-gray-400" />
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900 mb-1">
                        No customers found
                    </h3>
                    <p class="text-gray-500 text-xs">
                        Try different search terms
                    </p>
                </div>
            </div>

            <!-- Empty State -->
            <div
                v-if="!searchPerformed && !isSearching"
                class="text-center py-12"
            >
                <div class="max-w-xs mx-auto">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <Search class="w-5 h-5 text-blue-600" />
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900 mb-1">
                        Search for Customers
                    </h3>
                    <p class="text-gray-500 text-xs mb-3">
                        Enter customer details to record meter readings
                    </p>
                    <div class="flex flex-wrap justify-center gap-2 text-xs text-gray-500">
                        <div class="flex items-center gap-1">
                            <User class="w-3 h-3" />
                            <span>Name</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <Hash class="w-3 h-3" />
                            <span>Account</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <Cpu class="w-3 h-3" />
                            <span>Serial</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Meter Reading Modal -->
        <MeterReadingModal
            v-if="showReadingForm"
            :show="showReadingForm"
            :user="selectedUser"
            @close="closeReadingForm"
            @reading-submitted="handleReadingSubmitted"
        />
    </StaffLayout>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { debounce } from "lodash";
import {
    Droplet,
    Search,
    X,
    Loader2,
    Users,
    User,
    Hash,
    MapPin,
    ChevronRight,
    SearchX,
    Phone,
    Cpu
} from "lucide-vue-next";
import StaffLayout from "@/Layouts/StaffLayout.vue";
import MeterReadingModal from "@/Components/Staff/Modals/MeterReadingModal.vue";

// Search functionality
const searchQuery = ref("");
const searchResults = ref([]);
const isSearching = ref(false);
const searchPerformed = ref(false);

// Meter reading form
const showReadingForm = ref(false);
const selectedUser = ref({});

const searchUsers = async () => {
    if (!searchQuery.value.trim()) {
        searchResults.value = [];
        searchPerformed.value = false;
        return;
    }

    isSearching.value = true;
    searchPerformed.value = true;

    try {
        const response = await axios.get(route("staff.reading.search"), {
            params: { query: searchQuery.value },
        });

        if (response.data && Array.isArray(response.data)) {
            searchResults.value = response.data;
        } else {
            searchResults.value = [];
        }
    } catch (error) {
        console.error("Error searching users:", error);
        searchResults.value = [];
    } finally {
        isSearching.value = false;
    }
};

const debouncedSearch = debounce(searchUsers, 300);

const clearSearch = () => {
    searchQuery.value = "";
    searchResults.value = [];
    searchPerformed.value = false;
};


const openReadingForm = (user) => {
    selectedUser.value = {
        id: user.id,
        name: user.name,
        lastname: user.lastname,
        account_number: user.account_number,
        address: user.address,
        phone: user.phone,
        date_installed: user.date_installed || null,
        brand: user.brand || null,
        serial_number: user.serial_number || null,
        size: user.size || null,
    };
    showReadingForm.value = true;
};

const closeReadingForm = () => {
    showReadingForm.value = false;
    selectedUser.value = {};
};

const handleReadingSubmitted = async () => {
    // If we have a current search, refresh it
    if (searchQuery.value.trim()) {
        await searchUsers();
    }
};

onMounted(() => {
    return () => {
        debouncedSearch.cancel();
    };
});
</script>

<style scoped>
/* Ensure proper touch targets for mobile */
button, [role="button"] {
    min-height: 44px;
}
</style>
