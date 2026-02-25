<script setup>
import { router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    stats: Object,
    recentBookings: Array,
    invitations: Array,
    ownerCount: Number,
});

const page = usePage();

const becomeOwner = () => {
    router.post(route('account.become-owner'));
};

const acceptInvitation = (invitation) => {
    router.post(route('office-invitations.accept', invitation.id));
};

const declineInvitation = (invitation) => {
    router.delete(route('office-invitations.decline', invitation.id));
};
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto space-y-6 sm:px-6 lg:px-8">
                <section class="grid gap-4 md:grid-cols-3">
                    <div class="rounded-2xl bg-white p-5 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Bookings</p>
                        <p class="mt-3 text-2xl font-semibold text-gray-900">{{ props.stats?.bookings ?? 0 }}</p>
                        <p class="mt-1 text-sm text-gray-500">Your active and past stays.</p>
                    </div>
                    <div class="rounded-2xl bg-white p-5 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Listings</p>
                        <p class="mt-3 text-2xl font-semibold text-gray-900">{{ props.stats?.listings ?? 0 }}</p>
                        <p class="mt-1 text-sm text-gray-500">Spaces you have published.</p>
                    </div>
                    <div class="rounded-2xl bg-white p-5 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Community</p>
                        <p class="mt-3 text-2xl font-semibold text-gray-900">{{ props.ownerCount }}</p>
                        <p class="mt-1 text-sm text-gray-500">Trusted office hosts.</p>
                    </div>
                </section>

                <section class="grid gap-4 lg:grid-cols-[2fr_1fr]">
                    <div class="rounded-2xl bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">Recent bookings</h3>
                            <a class="text-sm font-semibold text-indigo-600" :href="route('bookings.index')">View all</a>
                        </div>

                        <div v-if="!props.recentBookings?.length" class="mt-4 text-sm text-gray-500">
                            You have no bookings yet. Start exploring available offices.
                        </div>

                        <div v-else class="mt-4 space-y-3">
                            <div
                                v-for="booking in props.recentBookings"
                                :key="booking.id"
                                class="flex items-center justify-between rounded-xl border border-gray-100 px-4 py-3"
                            >
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ booking.office }}</p>
                                    <p class="text-xs text-gray-500">{{ booking.start_date }} to {{ booking.end_date }}</p>
                                </div>
                                <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                                    {{ booking.status }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900">Host tools</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Publish spaces, review booking requests, and manage revenue.
                        </p>
                        <div class="mt-4 space-y-3">
                            <a
                                class="block rounded-xl border border-gray-200 px-4 py-3 text-sm font-semibold text-gray-700"
                                :href="route('listings.manage')"
                            >
                                Manage listings
                            </a>
                            <a
                                class="block rounded-xl border border-gray-200 px-4 py-3 text-sm font-semibold text-gray-700"
                                :href="route('bookings.manage')"
                            >
                                Review booking requests
                            </a>
                        </div>
                        <button
                            v-if="page.props.auth.user.role === 'renter'"
                            type="button"
                            class="mt-4 w-full rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white"
                            @click="becomeOwner"
                        >
                            Become a host
                        </button>
                    </div>

                    <div class="rounded-2xl bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">Invitations</h3>
                            <a class="text-sm font-semibold text-indigo-600" :href="route('office-invitations.index')">View all</a>
                        </div>

                        <div v-if="!props.invitations?.length" class="mt-4 text-sm text-gray-500">
                            No pending invitations.
                        </div>

                        <div v-else class="mt-4 space-y-3">
                            <div
                                v-for="invitation in props.invitations"
                                :key="invitation.id"
                                class="rounded-xl border border-gray-100 px-4 py-3"
                            >
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ invitation.office_title }}</p>
                                    <p class="text-xs text-gray-500">Invited by {{ invitation.invited_by }}</p>
                                </div>
                                <div class="mt-3 flex items-center gap-2">
                                    <button
                                        class="rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white"
                                        type="button"
                                        @click="acceptInvitation(invitation)"
                                    >
                                        Accept
                                    </button>
                                    <button
                                        class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-600"
                                        type="button"
                                        @click="declineInvitation(invitation)"
                                    >
                                        Decline
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </AppLayout>
</template>
