<template>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                <tr>
                    <th v-for="header in TABLE_HEADERS" :key="header.key"
                        scope="col"
                        :class="['px-6 py-2 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider',
                                header.align === 'right' ? 'text-right' : 'text-left']">
                        {{ header.label }}
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <UserRow
                    v-for="user in users"
                    :key="user.id"
                    :user="user"
                    :active-action-menu="activeActionMenu"
                    :get-action-dropdown-style="getActionDropdownStyle"
                    @toggle-action-menu="$emit('toggle-action-menu', $event)"
                    @view="$emit('view-user', $event)"
                    @edit="$emit('edit-user', $event)"
                    @toggle-status="$emit('toggle-status', $event)"
                    @delete="$emit('delete-user', $event)"
                />

                <tr v-if="users.length === 0">
                    <td colspan="7" class="px-6 py-8 text-center">
                        <EmptyState />
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import UserRow from "./UserRow.vue";
import EmptyState from "./EmptyState.vue";

const TABLE_HEADERS = [
    { key: 'user', label: 'User', align: 'left' },
    { key: 'account', label: 'Account No.', align: 'left' },
    { key: 'serial', label: 'Serial No.', align: 'left' },
    { key: 'role', label: 'Role(s)', align: 'left' },
    { key: 'status', label: 'Status', align: 'left' },
    { key: 'created', label: 'Created', align: 'left' },
    { key: 'actions', label: 'Actions', align: 'right' },
];

defineProps({
    users: Array,
    activeActionMenu: [String, Number, null],
    getActionDropdownStyle: Function,
});

defineEmits(['toggle-action-menu', 'view-user', 'edit-user', 'toggle-status', 'delete-user']);
</script>
