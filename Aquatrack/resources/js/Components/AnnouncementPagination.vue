// Components/Pagination.vue
<template>
    <div
        class="border-t border-gray-200 dark:border-gray-700 px-6 py-2"
    >
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700 dark:text-gray-300">
                Showing
                <span class="font-semibold">{{ data.from || 0 }}</span>
                to
                <span class="font-semibold">{{ data.to || 0 }}</span>
                of
                <span class="font-semibold">{{ data.total || 0 }}</span>
                results
            </div>

            <div class="flex items-center space-x-2">
                <button
                    @click="prevPage"
                    :disabled="!data.prev_page_url"
                    class="flex items-center px-3 py-1 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 hover:text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 transition-colors"
                >
                    Previous
                </button>

                <!-- Page Numbers -->
                <div class="flex items-center space-x-1">
                    <button
                        v-for="page in getVisiblePages()"
                        :key="page"
                        @click="goToPage(page)"
                        :class="[
                            'px-3 py-1 text-sm font-medium rounded-md transition-colors',
                            page === data.current_page
                                ? 'bg-blue-50 text-blue-600 border border-blue-600'
                                : 'text-gray-500 bg-white border border-gray-300 hover:bg-gray-50 hover:text-gray-700 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600',
                        ]"
                        :disabled="page === '...'"
                    >
                        {{ page }}
                    </button>
                </div>

                <button
                    @click="nextPage"
                    :disabled="!data.next_page_url"
                    class="flex items-center px-3 py-1 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 hover:text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 transition-colors"
                >
                    Next
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { router } from "@inertiajs/vue3";

const props = defineProps({
    data: {
        type: Object,
        required: true
    }
});

const prevPage = () => {
    if (props.data.prev_page_url) {
        router.visit(props.data.prev_page_url, {
            preserveState: true,
        });
    }
};

const nextPage = () => {
    if (props.data.next_page_url) {
        router.visit(props.data.next_page_url, {
            preserveState: true,
        });
    }
};

const goToPage = (page) => {
    if (page === '...' || page === props.data.current_page) return;

    const url = new URL(props.data.first_page_url);
    url.searchParams.set('page', page);
    router.visit(url.toString(), {
        preserveState: true,
    });
};

const getVisiblePages = () => {
    if (!props.data.last_page) return [];

    const current = props.data.current_page;
    const last = props.data.last_page;
    const delta = 2; // Number of pages to show on each side of current page
    const range = [];
    const rangeWithDots = [];

    for (let i = 1; i <= last; i++) {
        if (i === 1 || i === last || (i >= current - delta && i <= current + delta)) {
            range.push(i);
        }
    }

    let l;
    for (let i of range) {
        if (l) {
            if (i - l === 2) {
                rangeWithDots.push(l + 1);
            } else if (i - l !== 1) {
                rangeWithDots.push('...');
            }
        }
        rangeWithDots.push(i);
        l = i;
    }

    return rangeWithDots;
};
</script>
