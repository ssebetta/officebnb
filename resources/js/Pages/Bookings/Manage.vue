<script setup>
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    bookings: Array,
});

const updateStatus = (bookingId, status) => {
    router.patch(route('bookings.update-status', bookingId), { status });
};
</script>

<template>
    <AppLayout title="Manage bookings">
        <template #header>
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Manage bookings</h2>
                <p class="text-sm text-gray-500">Approve or decline incoming requests.</p>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-5xl mx-auto space-y-4 sm:px-6 lg:px-8">
                <div v-if="!props.bookings.length" class="rounded-2xl bg-white p-6 text-sm text-gray-500 shadow-sm">
                    No booking requests yet.
                </div>

                <div v-else class="space-y-3">
                    <div v-for="booking in props.bookings" :key="booking.id" class="rounded-2xl bg-white p-5 shadow-sm">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ booking.office?.title }}</p>
                                <p class="text-xs text-gray-500">{{ booking.office?.city }}, {{ booking.office?.country }}</p>
                                <p class="mt-2 text-xs text-gray-500">Guest: {{ booking.guest?.name }} - {{ booking.guest?.email }}</p>
                                <p class="mt-1 text-xs text-gray-500">{{ booking.start_date }} to {{ booking.end_date }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-900">${{ booking.total_amount.toLocaleString() }}</p>
                                <p class="text-xs text-gray-500">{{ booking.currency }}</p>
                            </div>
                        </div>
                        <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
                            <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">{{ booking.status }}</span>
                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-600"
                                    @click="updateStatus(booking.id, 'approved')"
                                >
                                    Approve
                                </button>
                                <button
                                    type="button"
                                    class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-600"
                                    @click="updateStatus(booking.id, 'rejected')"
                                >
                                    Reject
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
