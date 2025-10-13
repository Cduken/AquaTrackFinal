<template>
    <CustomerLayout>
        <div class="w-full bg-white rounded-lg shadow-sm">
            <!-- Header Section -->
            <div class="border-b border-gray-100 p-6">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-50 rounded-lg">
                        <v-icon
                            name="bi-megaphone-fill"
                            scale="1.5"
                            class="text-blue-600"
                        />
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">
                            Announcements
                        </h1>
                        <p class="text-gray-500 text-sm">
                            Stay updated with the latest news and updates
                        </p>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">
                        Latest Announcements
                    </h2>
                    <div class="text-sm text-gray-500">
                        {{ announcements.length }} items
                    </div>
                </div>

                <!-- Announcements List -->
                <div class="space-y-4">
                    <!-- Announcement Item -->
                    <div
                        v-for="announcement in announcements"
                        :key="announcement.id"
                        @click="openAnnouncementModal(announcement)"
                        class="border border-gray-200 rounded-lg p-5 hover:shadow-md transition-all duration-200 cursor-pointer group hover:border-blue-200 hover:translate-y-[-2px]"
                    >
                        <div class="flex justify-between items-start mb-3">
                            <h3
                                class="text-lg font-semibold text-gray-800 group-hover:text-blue-600 transition-colors"
                            >
                                {{ announcement.title }}
                            </h3>
                            <span
                                :class="{
                                    'bg-green-100 text-green-800':
                                        announcement.status === 'Active',
                                    'bg-red-100 text-red-800':
                                        announcement.status === 'Inactive',
                                    'bg-gray-100 text-gray-800':
                                        announcement.status === 'Scheduled',
                                }"
                                class="px-3 py-1 rounded-full text-xs font-medium"
                            >
                                {{ announcement.status }}
                            </span>
                        </div>
                        <p
                            class="text-gray-700 mb-4 leading-relaxed line-clamp-3"
                        >
                            {{ announcement.content }}
                        </p>
                        <div
                            class="flex justify-between items-center text-sm text-gray-500"
                        >
                            <div class="flex items-center gap-1">
                                <v-icon
                                    name="bi-calendar-event"
                                    class="text-gray-400"
                                />
                                <span
                                    >From:
                                    {{
                                        formatDate(announcement.start_date)
                                    }}</span
                                >
                            </div>
                            <div class="flex items-center gap-1">
                                <v-icon
                                    name="bi-calendar-check"
                                    class="text-gray-400"
                                />
                                <span
                                    >To:
                                    {{
                                        formatDate(announcement.end_date)
                                    }}</span
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div
                        v-if="announcements.length === 0"
                        class="text-center py-12"
                    >
                        <div
                            class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4"
                        >
                            <v-icon
                                name="bi-megaphone"
                                scale="2.5"
                                class="text-gray-400"
                            />
                        </div>
                        <h3 class="text-lg font-medium text-gray-600 mb-2">
                            No announcements yet
                        </h3>
                        <p class="text-gray-500 max-w-md mx-auto">
                            Check back later for updates and important
                            information.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Announcement Modal -->
        <div
            v-if="selectedAnnouncement"
            class="fixed inset-0 z-50 overflow-y-auto"
        >
            <!-- Backdrop -->
            <div
                class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
                @click="closeAnnouncementModal"
            ></div>

            <!-- Modal Container -->
            <div class="flex min-h-full items-center justify-center p-4">
                <!-- Modal Content -->
                <div
                    class="relative bg-white rounded-2xl shadow-xl w-full max-w-2xl transform transition-all"
                >
                    <!-- Header -->
                    <div class="border-b border-gray-100 p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center"
                                >
                                    <v-icon
                                        name="bi-megaphone-fill"
                                        class="text-blue-600 text-lg"
                                    />
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900">
                                        Announcement Details
                                    </h2>
                                    <p class="text-gray-500 text-sm">
                                        Complete information
                                    </p>
                                </div>
                            </div>
                            <button
                                @click="closeAnnouncementModal"
                                class="w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center transition-colors"
                            >
                                <v-icon
                                    name="bi-x"
                                    class="text-gray-500 text-xl"
                                />
                            </button>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-6 max-h-[60vh] overflow-y-auto">
                        <!-- Status and Dates -->
                        <div
                            class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100"
                        >
                            <span
                                :class="{
                                    'bg-green-100 text-green-800':
                                        selectedAnnouncement.status ===
                                        'Active',
                                    'bg-red-100 text-red-800':
                                        selectedAnnouncement.status ===
                                        'Inactive',
                                    'bg-gray-100 text-gray-800':
                                        selectedAnnouncement.status ===
                                        'Scheduled',
                                }"
                                class="px-4 py-2 rounded-full text-sm font-semibold"
                            >
                                {{ selectedAnnouncement.status }}
                            </span>
                            <div class="text-right text-sm text-gray-600">
                                <div class="flex items-center gap-4">
                                    <div class="flex items-center gap-2">
                                        <v-icon
                                            name="bi-calendar-event"
                                            class="text-blue-500"
                                        />
                                        <span>{{
                                            formatDate(
                                                selectedAnnouncement.start_date
                                            )
                                        }}</span>
                                    </div>
                                    <div class="text-gray-300">•</div>
                                    <div class="flex items-center gap-2">
                                        <v-icon
                                            name="bi-calendar-check"
                                            class="text-green-500"
                                        />
                                        <span>{{
                                            formatDate(
                                                selectedAnnouncement.end_date
                                            )
                                        }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Title -->
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">
                            {{ selectedAnnouncement.title }}
                        </h3>

                        <!-- Content -->
                        <div class="prose prose-lg max-w-none">
                            <p
                                class="text-gray-700 leading-relaxed whitespace-pre-line"
                            >
                                {{ selectedAnnouncement.content }}
                            </p>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div
                        class="border-t border-gray-100 px-6 py-4 bg-gray-50 rounded-b-2xl"
                    >
                        <div
                            class="flex items-center justify-between text-sm text-gray-500"
                        >
                            <div class="flex items-center gap-2">
                                <v-icon name="bi-clock" />
                                <span
                                    >Created:
                                    {{
                                        formatDateTime(
                                            selectedAnnouncement.created_at
                                        )
                                    }}</span
                                >
                            </div>
                            <div class="flex items-center gap-2">
                                <v-icon name="bi-person" />
                                <span
                                    >By:
                                    {{
                                        selectedAnnouncement.author ||
                                        "Administrator"
                                    }}</span
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>

<script setup>
import CustomerLayout from "@/Layouts/CustomerLayout.vue";
import { ref } from "vue";

const props = defineProps({
    announcements: {
        type: Array,
        required: true,
        default: () => [],
    },
});

const selectedAnnouncement = ref(null);

// Function to format dates
const formatDate = (dateString) => {
    if (!dateString) return "Not specified";

    try {
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return "Invalid date";

        const options = { year: "numeric", month: "short", day: "numeric" };
        return date.toLocaleDateString(undefined, options);
    } catch (error) {
        console.error("Error formatting date:", error);
        return "Date error";
    }
};

// Function to format date and time
const formatDateTime = (dateString) => {
    if (!dateString) return "Not available";

    try {
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return "Invalid date";

        const options = {
            year: "numeric",
            month: "short",
            day: "numeric",
            hour: "2-digit",
            minute: "2-digit",
        };
        return date.toLocaleDateString(undefined, options);
    } catch (error) {
        console.error("Error formatting date time:", error);
        return "Date error";
    }
};

// Open announcement modal
const openAnnouncementModal = (announcement) => {
    selectedAnnouncement.value = announcement;
    document.body.style.overflow = "hidden";
};

// Close announcement modal
const closeAnnouncementModal = () => {
    selectedAnnouncement.value = null;
    document.body.style.overflow = "auto";
};

// Close modal on escape key
const handleEscapeKey = (event) => {
    if (event.key === "Escape") {
        closeAnnouncementModal();
    }
};

// Add event listener for escape key
if (typeof window !== "undefined") {
    window.addEventListener("keydown", handleEscapeKey);
}
</script>

<style scoped>
.transition-shadow {
    transition: box-shadow 0.2s ease-in-out;
}

.transition-all {
    transition: all 0.2s ease-in-out;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
