<script setup>
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    landlords: Object,
});

const updateRole = (userId, currentRole) => {
    const newRole = prompt(`Change role for this user (current: ${currentRole}). Enter: renter, owner, or admin`);
    if (newRole && ['renter', 'owner', 'admin'].includes(newRole)) {
        router.patch(route('super-admin.users.update-role', userId), { role: newRole });
    }
};
</script>

<template>
    <AppLayout title="Manage Landlords">
        <template #header>
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Manage Landlords</h2>
                <p class="text-sm text-gray-500">View and manage all landlord accounts on the platform.</p>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto space-y-4 sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-2xl bg-white shadow-sm">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Offices</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Joined</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="landlord in props.landlords.data" :key="landlord.id" class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-gray-900">{{ landlord.name }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ landlord.email }}</td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-600">{{ landlord.role }}</span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ landlord.offices_count }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ landlord.created_at }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <button
                                        type="button"
                                        class="text-indigo-600 hover:text-indigo-900"
                                        @click="updateRole(landlord.id, landlord.role)"
                                    >
                                        Change Role
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="props.landlords.links.length" class="flex flex-wrap gap-2">
                    <a
                        v-for="link in props.landlords.links"
                        :key="link.url || link.label"
                        :href="link.url || ''"
                        v-html="link.label"
                        class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-600"
                        :class="{
                            'bg-indigo-600 text-white border-indigo-600': link.active,
                            'opacity-50 pointer-events-none': !link.url,
                        }"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
