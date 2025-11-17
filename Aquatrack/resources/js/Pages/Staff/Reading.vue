<template>
    <StaffLayout>
        <div class="min-h-screen px-4">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-3">
                    <Droplet class="w-8 h-8 text-blue-600" />
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            Meter Readings
                        </h1>
                        <p class="text-gray-500 mt-1">
                            Record and manage water consumption data
                        </p>
                    </div>
                </div>
            </div>

            <!-- Search Card -->
            <div
                class="bg-white shadow-sm border border-gray-200 rounded-lg mb-8"
            >
                <div class="p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <Search class="w-5 h-5 text-blue-600" />
                        <h2 class="text-lg font-semibold text-gray-900">
                            Find Customer
                        </h2>
                    </div>

                    <div class="flex flex-col lg:flex-row gap-4">
                        <div class="relative flex-1">
                            <Search
                                class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                            />
                            <input
                                v-model="searchQuery"
                                type="text"
                                class="w-full pl-11 pr-11 py-4 bg-gray-50 border border-gray-300 rounded-lg text-base text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="Search by name, account number, or meter serial number..."
                                @input="debouncedSearch"
                            />
                            <button
                                v-if="searchQuery"
                                @click="clearSearch"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
                            >
                                <X class="w-5 h-5" />
                            </button>
                        </div>
                        <button
                            @click="searchUsers"
                            :disabled="isSearching"
                            class="px-8 py-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center gap-2 min-w-[140px] font-medium text-base disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <Search v-if="!isSearching" class="w-5 h-5" />
                            <Loader2 v-else class="w-5 h-5 animate-spin" />
                            <span>{{
                                isSearching ? "Searching..." : "Search"
                            }}</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="isSearching" class="flex justify-center py-16">
                <div class="text-center">
                    <Loader2
                        class="w-10 h-10 text-blue-600 animate-spin mx-auto mb-4"
                    />
                    <p class="text-gray-600 text-lg">Searching customers...</p>
                </div>
            </div>

            <!-- Search Results -->
            <div v-if="searchPerformed && !isSearching" class="w-full">
                <!-- Results Found -->
                <div v-if="searchResults.length > 0" class="space-y-6">
                    <div class="flex items-center gap-2 px-2">
                        <Users class="w-5 h-5 text-gray-500" />
                        <p class="text-base font-medium text-gray-600">
                            Found {{ searchResults.length }} customer{{
                                searchResults.length !== 1 ? "s" : ""
                            }}
                        </p>
                    </div>

                    <!-- Results Grid - Full Width -->
                    <div class="space-y-4 width-full">
                        <div
                            v-for="user in searchResults"
                            :key="user.id"
                            @click="openReadingForm(user)"
                            class="w-full bg-white border border-gray-200 rounded-xl hover:border-blue-400 hover:shadow-xl transition-all duration-300 cursor-pointer group overflow-hidden"
                        >
                            <div class="p-6">
                                <div class="flex items-start gap-5">
                                    <!-- Avatar -->
                                    <div class="flex-shrink-0">
                                        <div
                                            v-if="user.avatar_url"
                                            class="w-14 h-14 rounded-xl overflow-hidden border-2 border-gray-100 group-hover:border-blue-100 transition-colors"
                                        >
                                            <img
                                                :src="user.avatar_url"
                                                :alt="user.name"
                                                class="w-full h-full object-cover"
                                            />
                                        </div>
                                        <div
                                            v-else
                                            class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center group-hover:from-blue-600 group-hover:to-blue-700 transition-all"
                                        >
                                            <User class="w-7 h-7 text-white" />
                                        </div>
                                    </div>

                                    <!-- User Info -->
                                    <div class="flex-1 min-w-0">
                                        <!-- Name -->
                                        <h4
                                            class="font-bold text-gray-900 text-xl mb-3 group-hover:text-blue-800 transition-colors"
                                        >
                                            {{ user.name }} {{ user.lastname }}
                                        </h4>

                                        <!-- Main Info Grid -->
                                        <div
                                            class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4"
                                        >
                                            <!-- Account & Contact -->
                                            <div class="space-y-3">
                                                <!-- Account Number -->
                                                <div
                                                    class="flex items-center gap-3"
                                                >
                                                    <div
                                                        class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center group-hover:bg-blue-100 transition-colors"
                                                    >
                                                        <Hash
                                                            class="w-4 h-4 text-blue-600"
                                                        />
                                                    </div>
                                                    <div>
                                                        <p
                                                            class="text-xs text-gray-500 font-medium uppercase tracking-wide"
                                                        >
                                                            Account No.
                                                        </p>
                                                        <p
                                                            class="text-sm font-semibold text-gray-900"
                                                        >
                                                            {{
                                                                user.account_number
                                                            }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <!-- Phone -->
                                                <div
                                                    class="flex items-center gap-3"
                                                    v-if="user.phone"
                                                >
                                                    <div
                                                        class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center group-hover:bg-green-100 transition-colors"
                                                    >
                                                        <Phone
                                                            class="w-4 h-4 text-green-600"
                                                        />
                                                    </div>
                                                    <div>
                                                        <p
                                                            class="text-xs text-gray-500 font-medium uppercase tracking-wide"
                                                        >
                                                            Phone
                                                        </p>
                                                        <p
                                                            class="text-sm font-semibold text-gray-900"
                                                        >
                                                            {{ user.phone }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Address -->
                                            <div class="flex items-start gap-3">
                                                <div
                                                    class="w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center group-hover:bg-purple-100 transition-colors flex-shrink-0 mt-1"
                                                >
                                                    <MapPin
                                                        class="w-4 h-4 text-purple-600"
                                                    />
                                                </div>
                                                <div>
                                                    <p
                                                        class="text-xs text-gray-500 font-medium uppercase tracking-wide"
                                                    >
                                                        Address
                                                    </p>
                                                    <p
                                                        class="text-sm font-semibold text-gray-900 leading-relaxed"
                                                    >
                                                        {{ user.address }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Meter Details -->
                                        <div
                                            class="pt-4 border-t border-gray-100"
                                        >
                                            <p
                                                class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-3"
                                            >
                                                Meter Details
                                            </p>
                                            <div class="flex flex-wrap gap-4">
                                                <!-- Serial Number -->
                                                <div
                                                    v-if="user.serial_number"
                                                    class="flex items-center gap-2"
                                                >
                                                    <div
                                                        class="w-6 h-6 bg-blue-100 rounded-md flex items-center justify-center"
                                                    >
                                                        <Cpu
                                                            class="w-3 h-3 text-blue-600"
                                                        />
                                                    </div>
                                                    <div>
                                                        <p
                                                            class="text-xs text-gray-500"
                                                        >
                                                            Serial No.
                                                        </p>
                                                        <p
                                                            class="text-sm font-medium text-gray-900"
                                                        >
                                                            {{
                                                                user.serial_number
                                                            }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <!-- Brand -->
                                                <div
                                                    v-if="user.brand"
                                                    class="flex items-center gap-2"
                                                >
                                                    <div
                                                        class="w-6 h-6 bg-green-100 rounded-md flex items-center justify-center"
                                                    >
                                                        <Tag
                                                            class="w-3 h-3 text-green-600"
                                                        />
                                                    </div>
                                                    <div>
                                                        <p
                                                            class="text-xs text-gray-500"
                                                        >
                                                            Brand
                                                        </p>
                                                        <p
                                                            class="text-sm font-medium text-gray-900"
                                                        >
                                                            {{ user.brand }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <!-- Size -->
                                                <div
                                                    v-if="user.size"
                                                    class="flex items-center gap-2"
                                                >
                                                    <div
                                                        class="w-6 h-6 bg-orange-100 rounded-md flex items-center justify-center"
                                                    >
                                                        <Ruler
                                                            class="w-3 h-3 text-orange-600"
                                                        />
                                                    </div>
                                                    <div>
                                                        <p
                                                            class="text-xs text-gray-500"
                                                        >
                                                            Size
                                                        </p>
                                                        <p
                                                            class="text-sm font-medium text-gray-900"
                                                        >
                                                            {{ user.size }}mm
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Indicator -->
                                    <div class="flex-shrink-0 self-start">
                                        <div
                                            class="w-12 h-12 rounded-xl bg-gradient-to-br from-gray-50 to-gray-100 group-hover:from-blue-50 group-hover:to-blue-100 flex items-center justify-center transition-all duration-300 shadow-sm group-hover:shadow-md"
                                        >
                                            <ChevronRight
                                                class="w-6 h-6 text-gray-400 group-hover:text-blue-600 group-hover:translate-x-1 transition-all duration-300"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Hover Effect Border -->
                            <div
                                class="h-1 bg-gradient-to-r from-transparent via-blue-500 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                            ></div>
                        </div>
                    </div>
                </div>

                <!-- No Results -->
                <div v-else class="text-center py-16">
                    <SearchX class="w-16 h-16 text-gray-400 mx-auto mb-4" />
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">
                        No customers found
                    </h3>
                    <p class="text-gray-500 text-base">
                        Try adjusting your search terms or check for spelling
                        errors
                    </p>
                </div>
            </div>

            <!-- Empty State -->
            <div
                v-if="!searchPerformed && !isSearching"
                class="text-center py-16"
            >
                <div class="max-w-lg mx-auto">
                    <div
                        class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6"
                    >
                        <Search class="w-10 h-10 text-blue-600" />
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">
                        Search for Customers
                    </h3>
                    <p class="text-gray-500 text-base mb-6">
                        Enter a customer's name, account number, or meter serial
                        number to find and record their meter reading
                    </p>
                    <div
                        class="flex flex-col sm:flex-row gap-4 justify-center items-center text-sm text-gray-500"
                    >
                        <div class="flex items-center gap-2">
                            <User class="w-4 h-4" />
                            <span>Customer Name</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <Hash class="w-4 h-4" />
                            <span>Account Number</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <Droplet class="w-4 h-4" />
                            <span>Meter Serial</span>
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
    Phone, Cpu, Tag, Ruler
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

    // Optional: Also refresh the current user data in the modal if it's open
    if (showReadingForm.value && selectedUser.value.id) {
        // You might want to refetch user data here if needed
    }
};

onMounted(() => {
    return () => {
        debouncedSearch.cancel();
    };
});
</script>

<style scoped>
/* Custom scrollbar for better appearance */
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
