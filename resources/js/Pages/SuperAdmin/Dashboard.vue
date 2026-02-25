<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    stats: Object,
});
</script>

<template>
    <AppLayout title="Super Admin">
        <template #header>
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Super Admin Dashboard</h2>
                <p class="text-sm text-gray-500">Manage landlords, offices, and platform health.</p>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto space-y-6 sm:px-6 lg:px-8">
                <section class="grid gap-4 md:grid-cols-4">
                    <div class="rounded-2xl bg-white p-5 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Total Users</p>
                        <p class="mt-3 text-2xl font-semibold text-gray-900">{{ props.stats.users }}</p>
                    </div>
                    <div class="rounded-2xl bg-white p-5 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Landlords</p>
                        <p class="mt-3 text-2xl font-semibold text-gray-900">{{ props.stats.owners }}</p>
                    </div>
                    <div class="rounded-2xl bg-white p-5 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Total Offices</p>
                        <p class="mt-3 text-2xl font-semibold text-gray-900">{{ props.stats.offices }}</p>
                    </div>
                    <div class="rounded-2xl bg-white p-5 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Active Offices</p>
                        <p class="mt-3 text-2xl font-semibold text-gray-900">{{ props.stats.active_offices }}</p>
                    </div>
                </section>

                <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <Link :href="route('super-admin.landlords')" class="rounded-2xl bg-white p-6 shadow-sm transition hover:-translate-y-0.5">
                        <h3 class="text-lg font-semibold text-gray-900">Manage Landlords</h3>
                        <p class="mt-2 text-sm text-gray-500">View, approve, and manage landlord accounts.</p>
                    </Link>
                    <Link :href="route('super-admin.offices')" class="rounded-2xl bg-white p-6 shadow-sm transition hover:-translate-y-0.5">
                        <h3 class="text-lg font-semibold text-gray-900">Manage Offices</h3>
                        <p class="mt-2 text-sm text-gray-500">Review, activate, or deactivate office listings.</p>
                    </Link>
                    <Link :href="route('super-admin.advertisements')" class="rounded-2xl bg-white p-6 shadow-sm transition hover:-translate-y-0.5">
                        <h3 class="text-lg font-semibold text-gray-900">Review Advertisements</h3>
                        <p class="mt-2 text-sm text-gray-500">Approve premium placements and manage refunds.</p>
                    </Link>
                </section>
            </div>
        </div>
    </AppLayout>
</template>
