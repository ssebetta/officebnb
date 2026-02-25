<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    stats: Object,
    recentUsers: Array,
    recentBookings: Array,
});
</script>

<template>
    <AppLayout title="Admin overview">
        <template #header>
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Admin overview</h2>
                <p class="text-sm text-gray-500">Track marketplace health and recent activity.</p>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-6xl mx-auto space-y-6 sm:px-6 lg:px-8">
                <section class="grid gap-4 md:grid-cols-4">
                    <div class="rounded-2xl bg-white p-5 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Users</p>
                        <p class="mt-3 text-2xl font-semibold text-gray-900">{{ props.stats.users }}</p>
                    </div>
                    <div class="rounded-2xl bg-white p-5 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Owners</p>
                        <p class="mt-3 text-2xl font-semibold text-gray-900">{{ props.stats.owners }}</p>
                    </div>
                    <div class="rounded-2xl bg-white p-5 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Offices</p>
                        <p class="mt-3 text-2xl font-semibold text-gray-900">{{ props.stats.offices }}</p>
                    </div>
                    <div class="rounded-2xl bg-white p-5 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Bookings</p>
                        <p class="mt-3 text-2xl font-semibold text-gray-900">{{ props.stats.bookings }}</p>
                    </div>
                </section>

                <section class="grid gap-4 lg:grid-cols-2">
                    <div class="rounded-2xl bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900">Recent users</h3>
                        <div class="mt-4 space-y-3">
                            <div v-for="user in props.recentUsers" :key="user.id" class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ user.name }}</p>
                                    <p class="text-xs text-gray-500">{{ user.email }}</p>
                                </div>
                                <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">{{ user.role }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900">Recent bookings</h3>
                        <div class="mt-4 space-y-3">
                            <div v-for="booking in props.recentBookings" :key="booking.id" class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ booking.office }}</p>
                                    <p class="text-xs text-gray-500">{{ booking.user }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-gray-900">${{ booking.total_amount.toLocaleString() }}</p>
                                    <p class="text-xs text-gray-500">{{ booking.status }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </AppLayout>
</template>
