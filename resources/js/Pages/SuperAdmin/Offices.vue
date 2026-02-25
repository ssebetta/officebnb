<script setup>
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    offices: Object,
});

const toggleStatus = (officeSlug) => {
    router.patch(route('super-admin.offices.toggle', officeSlug));
};

const deleteOffice = (officeSlug, officeTitle) => {
    if (confirm(`Delete "${officeTitle}"? This cannot be undone.`)) {
        router.delete(route('super-admin.offices.delete', officeSlug));
    }
};
</script>

<template>
    <AppLayout title="Manage Offices">
        <template #header>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Manage Offices</h2>
                    <p class="text-sm text-gray-500">Moderate all office listings on the platform.</p>
                </div>
                <Link class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white" :href="route('super-admin.offices.create')">
                    Create office for user
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto space-y-4 sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-2xl bg-white shadow-sm">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Office</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Location</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Owner</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Rate</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Bookings</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="office in props.offices.data" :key="office.id" class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4">
                                    <Link :href="route('offices.show', office.slug)" class="text-sm font-semibold text-gray-900 hover:text-indigo-600">
                                        {{ office.title }}
                                    </Link>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ office.city }}, {{ office.country }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ office.owner?.name }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">${{ office.daily_rate }}/day</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ office.bookings_count }}</td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span
                                        class="rounded-full px-3 py-1 text-xs font-semibold"
                                        :class="office.is_active ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600'"
                                    >
                                        {{ office.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <div class="flex items-center gap-2">
                                        <button
                                            type="button"
                                            class="text-indigo-600 hover:text-indigo-900"
                                            @click="toggleStatus(office.slug)"
                                        >
                                            {{ office.is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                        <button
                                            type="button"
                                            class="text-red-600 hover:text-red-900"
                                            @click="deleteOffice(office.slug, office.title)"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="props.offices.links.length" class="flex flex-wrap gap-2">
                    <a
                        v-for="link in props.offices.links"
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
