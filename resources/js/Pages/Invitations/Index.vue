<script setup>
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    invitations: Array,
});

const acceptInvitation = (invitation) => {
    router.post(route('office-invitations.accept', invitation.id));
};

const declineInvitation = (invitation) => {
    router.delete(route('office-invitations.decline', invitation.id));
};
</script>

<template>
    <AppLayout title="Invitations">
        <template #header>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Invitations</h2>
                    <p class="text-sm text-gray-500">Accept or decline office manager invites.</p>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-5xl mx-auto space-y-4 sm:px-6 lg:px-8">
                <div v-if="!props.invitations.length" class="rounded-2xl bg-white p-6 text-sm text-gray-500 shadow-sm">
                    You have no pending invitations.
                </div>

                <div v-else class="space-y-3">
                    <div
                        v-for="invitation in props.invitations"
                        :key="invitation.id"
                        class="flex flex-wrap items-center justify-between gap-3 rounded-2xl bg-white p-5 shadow-sm"
                    >
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ invitation.office_title }}</p>
                            <p class="text-xs text-gray-500">Invited by {{ invitation.invited_by }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button class="rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white" type="button" @click="acceptInvitation(invitation)">
                                Accept
                            </button>
                            <button class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-600" type="button" @click="declineInvitation(invitation)">
                                Decline
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
