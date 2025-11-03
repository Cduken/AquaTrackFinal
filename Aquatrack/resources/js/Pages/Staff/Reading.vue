<template>
    <StaffLayout>
        <div
            class="min-h-screen py-1 px-2 sm:px-1 lg:px-1"
        >
            <div class="max-w-full mx-auto">
                <!-- Header Section -->
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-2">

                        <div>
                            <h1
                                class="text-2xl font-bold text-gray-900"
                            >
                                Meter Readings
                            </h1>
                            <p class="text-sm text-slate-500 mt-0.5">
                                Record and manage water consumption data
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Search Section -->
                <div
                    class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden mb-6"
                >
                    <div class="p-6 sm:p-8">
                        <div class="flex items-center gap-2 mb-6">
                            <Search
                                class="w-5 h-5 text-blue-600"
                                :stroke-width="2"
                            />
                            <h2 class="text-lg font-semibold text-slate-900">
                                Find Customer
                            </h2>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3">
                            <div class="relative flex-1">
                                <Search
                                    class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400"
                                    :stroke-width="2"
                                />
                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    class="w-full pl-12 pr-12 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white transition-all duration-200"
                                    placeholder="Search by name, account, or serial number..."
                                    @input="debouncedSearch"
                                />
                                <button
                                    v-if="searchQuery"
                                    @click="clearSearch"
                                    type="button"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors"
                                >
                                    <X class="w-4 h-4" :stroke-width="2" />
                                </button>
                            </div>
                            <button
                                @click="searchUsers"
                                type="button"
                                class="px-6 py-3.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-2xl hover:from-blue-700 hover:to-blue-800 transition-all duration-200 flex items-center justify-center gap-2 shadow-sm shadow-blue-600/20 hover:shadow-md hover:shadow-blue-600/30 min-w-[120px] font-medium text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="isSearching"
                            >
                                <Search
                                    v-if="!isSearching"
                                    class="w-4 h-4"
                                    :stroke-width="2"
                                />
                                <Loader2
                                    v-else
                                    class="w-4 h-4 animate-spin"
                                    :stroke-width="2"
                                />
                                <span>{{
                                    isSearching ? "Searching..." : "Search"
                                }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Loading State -->
                <div
                    v-if="isSearching"
                    class="flex items-center justify-center py-16"
                >
                    <div class="text-center">
                        <div class="relative inline-flex mb-4">
                            <div
                                class="absolute inset-0 bg-blue-500 blur-2xl opacity-20 rounded-full animate-pulse"
                            ></div>
                            <div
                                class="relative bg-white p-4 rounded-2xl shadow-sm border border-slate-200/60"
                            >
                                <Loader2
                                    class="w-8 h-8 text-blue-600 animate-spin"
                                    :stroke-width="2"
                                />
                            </div>
                        </div>
                        <p class="text-sm font-medium text-slate-600">
                            Searching customers...
                        </p>
                    </div>
                </div>

                <!-- Search Results -->
                <div v-if="searchPerformed && !isSearching">
                    <!-- Results Found -->
                    <div v-if="searchResults.length > 0" class="space-y-4">
                        <div class="flex items-center gap-2 px-1">
                            <Users
                                class="w-4 h-4 text-slate-500"
                                :stroke-width="2"
                            />
                            <p class="text-sm font-medium text-slate-600">
                                Found {{ searchResults.length }} customer{{
                                    searchResults.length !== 1 ? "s" : ""
                                }}
                            </p>
                        </div>

                        <div class="grid gap-3">
                            <div
                                v-for="user in searchResults"
                                :key="user.id"
                                @click="openReadingForm(user)"
                                class="group bg-white rounded-2xl shadow-sm border border-slate-200/60 hover:border-blue-300 hover:shadow-md transition-all duration-200 cursor-pointer overflow-hidden"
                            >
                                <div class="p-5">
                                    <div class="flex items-start gap-4">
                                        <!-- Avatar -->
                                        <div class="flex-shrink-0">
                                            <div
                                                v-if="user.avatar_url"
                                                class="relative"
                                            >
                                                <img
                                                    :src="user.avatar_url"
                                                    :alt="user.name"
                                                    class="w-12 h-12 rounded-xl object-cover border border-slate-200"
                                                />
                                            </div>
                                            <div
                                                v-else
                                                class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center"
                                            >
                                                <User
                                                    class="w-6 h-6 text-white"
                                                    :stroke-width="2"
                                                />
                                            </div>
                                        </div>

                                        <!-- User Info -->
                                        <div class="flex-1 min-w-0">
                                            <h4
                                                class="font-semibold text-slate-900 group-hover:text-blue-700 transition-colors mb-2 truncate"
                                            >
                                                {{ user.name }}
                                                {{ user.lastname }}
                                            </h4>
                                            <div class="space-y-1.5">
                                                <div
                                                    class="flex items-center gap-2 text-xs text-slate-600"
                                                >
                                                    <Hash
                                                        class="w-3.5 h-3.5 text-slate-400"
                                                        :stroke-width="2"
                                                    />
                                                    <span class="truncate">{{
                                                        user.account_number
                                                    }}</span>
                                                </div>
                                                <div
                                                    class="flex items-center gap-2 text-xs text-slate-600"
                                                >
                                                    <MapPin
                                                        class="w-3.5 h-3.5 text-slate-400"
                                                        :stroke-width="2"
                                                    />
                                                    <span class="truncate">{{
                                                        user.address
                                                    }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Action Arrow -->
                                        <div class="flex-shrink-0 self-center">
                                            <div
                                                class="w-8 h-8 rounded-xl bg-slate-50 group-hover:bg-blue-50 flex items-center justify-center transition-colors"
                                            >
                                                <ChevronRight
                                                    class="w-4 h-4 text-slate-400 group-hover:text-blue-600 group-hover:translate-x-0.5 transition-all"
                                                    :stroke-width="2"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- No Results -->
                    <div v-else class="text-center py-16">
                        <div class="relative inline-flex mb-4">
                            <div
                                class="absolute inset-0 bg-slate-200 blur-2xl opacity-30 rounded-full"
                            ></div>
                            <div
                                class="relative bg-white p-4 rounded-2xl shadow-sm border border-slate-200/60"
                            >
                                <SearchX
                                    class="w-8 h-8 text-slate-400"
                                    :stroke-width="2"
                                />
                            </div>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 mb-1">
                            No customers found
                        </h3>
                        <p class="text-sm text-slate-500">
                            Try adjusting your search terms
                        </p>
                    </div>
                </div>

                <!-- Quick Tips -->
                <div
                    v-if="!searchPerformed && !isSearching"
                    class="bg-gradient-to-br from-slate-50 to-blue-50/50 rounded-3xl border border-slate-200/60 overflow-hidden"
                >
                    <div class="p-6 sm:p-8">
                        <div class="flex items-center gap-2 mb-4">
                            <Info
                                class="w-5 h-5 text-blue-600"
                                :stroke-width="2"
                            />
                            <h3 class="text-lg font-semibold text-slate-900">
                                Quick Tips
                            </h3>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-start gap-3">
                                <div
                                    class="w-1.5 h-1.5 rounded-full bg-blue-600 mt-2 flex-shrink-0"
                                ></div>
                                <p
                                    class="text-sm text-slate-600 leading-relaxed"
                                >
                                    Search by customer name, account number, or
                                    meter serial number
                                </p>
                            </div>
                            <div class="flex items-start gap-3">
                                <div
                                    class="w-1.5 h-1.5 rounded-full bg-blue-600 mt-2 flex-shrink-0"
                                ></div>
                                <p
                                    class="text-sm text-slate-600 leading-relaxed"
                                >
                                    Click on a customer card to record their
                                    meter reading
                                </p>
                            </div>
                            <div class="flex items-start gap-3">
                                <div
                                    class="w-1.5 h-1.5 rounded-full bg-blue-600 mt-2 flex-shrink-0"
                                ></div>
                                <p
                                    class="text-sm text-slate-600 leading-relaxed"
                                >
                                    Recent readings will be shown in the
                                    customer profile
                                </p>
                            </div>
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
    Info,
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

const handleReadingSubmitted = () => {
    searchUsers();
};

onMounted(() => {
    return () => {
        debouncedSearch.cancel();
    };
});
</script>

<style scoped>
/* Smooth scroll behavior */
html {
    scroll-behavior: smooth;
}

/* Custom gradient animations */
@keyframes gradient-shift {
    0%,
    100% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
}

/* Enhanced hover effects */
.group:hover {
    transform: translateY(-1px);
}

/* Smooth transitions for all interactive elements */
* {
    transition-property: color, background-color, border-color, transform,
        box-shadow;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}
</style>
