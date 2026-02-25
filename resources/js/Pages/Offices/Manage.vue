<script setup>
import { Link, router, useForm } from '@inertiajs/vue3';
import { reactive, ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    offices: Array,
});

const inviteForm = useForm({
    email: '',
});

const inviteEmails = reactive({});
const activeInviteOfficeId = ref(null);

const removeOffice = (office) => {
    if (confirm('Remove this listing?')) {
        router.delete(route('listings.destroy', office.slug));
    }
};

const sendInvite = (office) => {
    activeInviteOfficeId.value = office.id;
    inviteForm.email = inviteEmails[office.id] || '';

    inviteForm.post(route('office-invitations.store', office.slug), {
        preserveScroll: true,
        onSuccess: () => {
            inviteEmails[office.id] = '';
            inviteForm.reset('email');
        },
    });
};

const resendInvite = (invitationId) => {
    router.post(route('office-invitations.resend', invitationId), {}, {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout title="Manage listings">
        <template #header>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Manage listings</h2>
                    <p class="text-sm text-gray-500">Track performance and respond to booking activity.</p>
                </div>
                <Link class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white" :href="route('listings.create')">
                    New listing
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-6xl mx-auto space-y-4 sm:px-6 lg:px-8">
                <div v-if="!props.offices.length" class="rounded-2xl bg-white p-6 text-sm text-gray-500 shadow-sm">
                    No listings yet. Create your first office.
                </div>

                <div v-else class="space-y-3">
                    <div v-for="office in props.offices" :key="office.id" class="flex flex-col gap-4 rounded-2xl bg-white p-5 shadow-sm lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ office.title }}</p>
                            <p class="text-xs text-gray-500">{{ office.city }}, {{ office.country }}</p>
                            <p class="mt-2 text-xs text-gray-500">Bookings: {{ office.bookings_count }}</p>
                            <div v-if="office.managers?.length" class="mt-2 flex flex-wrap gap-2">
                                <span
                                    v-for="manager in office.managers"
                                    :key="manager.id"
                                    class="rounded-full bg-gray-100 px-2.5 py-1 text-[0.65rem] font-semibold text-gray-600"
                                >
                                    {{ manager.name || manager.email }}
                                </span>
                            </div>
                            <div v-if="office.pending_invitations?.length" class="mt-2">
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Pending invitations:</p>
                                <div class="mt-1 flex flex-wrap gap-2">
                                    <div
                                        v-for="invitation in office.pending_invitations"
                                        :key="invitation.id"
                                        class="flex items-center gap-1.5 rounded-full bg-yellow-50 border border-yellow-200 px-2.5 py-1"
                                    >
                                        <span class="text-[0.65rem] font-semibold text-yellow-700">{{ invitation.email }}</span>
                                        <button
                                            type="button"
                                            class="text-[0.6rem] text-yellow-600 hover:text-yellow-800 font-semibold underline"
                                            @click="resendInvite(invitation.id)"
                                        >
                                            Resend
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <Link class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-600" :href="route('offices.show', office.slug)">
                                View
                            </Link>
                            <Link class="rounded-lg border border-indigo-100 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700" :href="route('advertisements.create', office.slug)">
                                Advertise
                            </Link>
                            <Link class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-600" :href="route('listings.edit', office.slug)">
                                Edit
                            </Link>
                            <button class="rounded-lg border border-red-100 px-3 py-1.5 text-xs font-semibold text-red-600" type="button" @click="removeOffice(office)">
                                Remove
                            </button>
                        </div>

                        <div class="w-full lg:max-w-sm">
                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Invite manager</label>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <input
                                    v-model="inviteEmails[office.id]"
                                    type="email"
                                    placeholder="manager@example.com"
                                    class="flex-1 rounded-lg border border-gray-200 px-3 py-2 text-sm"
                                />
                                <button
                                    type="button"
                                    class="rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white"
                                    :disabled="inviteForm.processing && activeInviteOfficeId === office.id"
                                    @click="sendInvite(office)"
                                >
                                    Send invite
                                </button>
                            </div>
                            <p v-if="inviteForm.errors.email && activeInviteOfficeId === office.id" class="mt-1 text-xs text-red-600">
                                {{ inviteForm.errors.email }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
