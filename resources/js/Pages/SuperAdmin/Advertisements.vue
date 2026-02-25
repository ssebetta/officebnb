<script setup>
import { Link, router } from '@inertiajs/vue3';
import { computed, reactive } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    advertisements: Object,
    filters: Object,
});

const filters = reactive({
    status: props.filters?.status || 'all',
});

const statusOptions = [
    { value: 'all', label: 'All' },
    { value: 'pending_payment', label: 'Pending review' },
    { value: 'active', label: 'Active' },
    { value: 'expired', label: 'Expired' },
    { value: 'cancelled', label: 'Cancelled' },
];

const updateFilters = () => {
    router.get(route('super-admin.advertisements'), filters, { preserveState: true, replace: true });
};

const approveAd = (adId) => {
    router.patch(route('super-admin.advertisements.approve', adId));
};

const refundAd = (adId, officeTitle) => {
    if (confirm(`Refund advertisement for "${officeTitle}"?`)) {
        router.patch(route('super-admin.advertisements.refund', adId));
    }
};

const statusBadgeClass = (status) => {
    switch (status) {
        case 'active':
            return 'bg-emerald-50 text-emerald-600';
        case 'pending_payment':
            return 'bg-amber-50 text-amber-700';
        case 'expired':
            return 'bg-slate-100 text-slate-600';
        case 'cancelled':
            return 'bg-rose-50 text-rose-600';
        default:
            return 'bg-slate-100 text-slate-600';
    }
};

const paymentStatusLabel = (payment) => {
    if (!payment) return 'No payment';
    return payment.status.charAt(0).toUpperCase() + payment.status.slice(1);
};

const adRows = computed(() => props.advertisements?.data || []);
</script>

<template>
    <AppLayout title="Advertisement Review">
        <template #header>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Advertisement Review</h2>
                    <p class="text-sm text-gray-500">Approve premium placements and handle refunds.</p>
                </div>
                <Link :href="route('super-admin.dashboard')" class="text-sm font-semibold text-indigo-600">Back to dashboard</Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto space-y-4 sm:px-6 lg:px-8">
                <div class="flex flex-wrap items-center gap-2">
                    <button
                        v-for="option in statusOptions"
                        :key="option.value"
                        type="button"
                        class="rounded-full border px-3 py-1 text-xs font-semibold transition"
                        :class="filters.status === option.value ? 'border-indigo-600 bg-indigo-50 text-indigo-700' : 'border-gray-200 text-gray-600 hover:border-gray-300'"
                        @click="filters.status = option.value; updateFilters()"
                    >
                        {{ option.label }}
                    </button>
                </div>

                <div v-if="!adRows.length" class="rounded-2xl bg-white p-6 text-sm text-gray-500 shadow-sm">
                    No advertisements match those filters.
                </div>

                <div v-else class="overflow-hidden rounded-2xl bg-white shadow-sm">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Office</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Dates</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Payment</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="ad in adRows" :key="ad.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img
                                            v-if="ad.image_url"
                                            :src="ad.image_url"
                                            :alt="ad.office?.title"
                                            class="h-12 w-16 rounded-lg object-cover"
                                        />
                                        <div>
                                            <Link
                                                v-if="ad.office"
                                                :href="route('offices.show', ad.office.slug)"
                                                class="text-sm font-semibold text-gray-900 hover:text-indigo-600"
                                            >
                                                {{ ad.office.title }}
                                            </Link>
                                            <p class="text-xs text-gray-500">{{ ad.office?.city }}, {{ ad.office?.country }}</p>
                                            <p class="text-xs text-gray-400">Owner: {{ ad.owner?.name }} ({{ ad.owner?.email }})</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <span class="rounded-full bg-indigo-50 px-2 py-1 text-xs font-semibold text-indigo-600">
                                        {{ ad.type === 'premium' ? 'Premium' : 'Basic' }}
                                    </span>
                                    <p class="mt-2 text-xs text-gray-500">${{ ad.amount.toFixed(2) }}</p>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <p>{{ ad.start_date }} → {{ ad.end_date }}</p>
                                    <p class="text-xs text-gray-400">Created: {{ ad.created_at }}</p>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <p class="text-xs text-gray-500">Method: {{ ad.payment_method }}</p>
                                    <p class="text-sm font-semibold text-gray-800">{{ paymentStatusLabel(ad.payment) }}</p>
                                    <p class="text-xs text-gray-400">{{ ad.payment?.transaction_id || '—' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold" :class="statusBadgeClass(ad.status)">
                                        {{ ad.status.replace('_', ' ') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex flex-col gap-2">
                                        <button
                                            v-if="ad.status === 'pending_payment'"
                                            type="button"
                                            class="text-indigo-600 hover:text-indigo-900"
                                            @click="approveAd(ad.id)"
                                        >
                                            Approve
                                        </button>
                                        <button
                                            v-if="ad.status === 'active' || ad.status === 'pending_payment'"
                                            type="button"
                                            class="text-rose-600 hover:text-rose-900"
                                            @click="refundAd(ad.id, ad.office?.title || 'office')"
                                        >
                                            Refund
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="props.advertisements?.links?.length" class="flex flex-wrap gap-2">
                    <Link
                        v-for="link in props.advertisements.links"
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
