<template>
    <div
        class="flex items-center justify-between border-t border-gray-200 px-4 py-3 sm:px-6"
    >
        <!-- Mobile view -->
        <div class="flex flex-1 justify-between sm:hidden">
            <button
                @click="prevPage"
                :disabled="!items.prev_page_url"
                :class="{
                    'opacity-50 cursor-not-allowed': !items.prev_page_url,
                }"
                class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                Previous
            </button>
            <button
                @click="nextPage"
                :disabled="!items.next_page_url"
                :class="{
                    'opacity-50 cursor-not-allowed': !items.next_page_url,
                }"
                class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                Next
            </button>
        </div>

        <!-- Desktop view -->
        <div
            class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between"
        >
            <div>
                <p class="text-sm text-gray-700">
                    Showing
                    <span class="font-medium">{{ items.from }}</span>
                    to
                    <span class="font-medium">{{ items.to }}</span>
                    of
                    <span class="font-medium">{{ items.total }}</span>
                    {{ itemName }}
                </p>
            </div>
            <div>
                <nav
                    class="isolate inline-flex -space-x-px rounded-md shadow-sm"
                >
                    <button
                        @click="prevPage"
                        :disabled="!items.prev_page_url"
                        :class="{
                            'opacity-50 cursor-not-allowed':
                                !items.prev_page_url,
                        }"
                        class="relative inline-flex items-center rounded-l-md px-3 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
                    >
                        <span class="sr-only">Previous</span>
                        <MoveLeft class="h-4 w-4" />
                    </button>

                    <!-- Page numbers would go here if you want to add them -->

                    <button
                        @click="nextPage"
                        :disabled="!items.next_page_url"
                        :class="{
                            'opacity-50 cursor-not-allowed':
                                !items.next_page_url,
                        }"
                        class="relative inline-flex items-center rounded-r-md px-3 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
                    >
                        <span class="sr-only">Next</span>
                        <MoveRight class="h-4 w-4" />
                    </button>
                </nav>
            </div>
        </div>
    </div>
</template>

<script setup>
import { router } from "@inertiajs/vue3";
import { MoveLeft, MoveRight } from "lucide-vue-next";

const props = defineProps({
    items: Object,
    itemName: {
        type: String,
        default: "items",
    },
    previousText: {
        type: String,
        default: "Previous",
    },
    nextText: {
        type: String,
        default: "Next",
    },
});

const prevPage = () => {
    if (props.items.prev_page_url) {
        router.visit(props.items.prev_page_url, {
            preserveState: true,
        });
    }
};

const nextPage = () => {
    if (props.items.next_page_url) {
        router.visit(props.items.next_page_url, {
            preserveState: true,
        });
    }
};
</script>
