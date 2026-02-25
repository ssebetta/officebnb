<script setup>
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    bookings: Array,
});

const showPaymentModal = ref(false);
const selectedBooking = ref(null);
const activeTab = ref('stripe');

const paymentForm = useForm({
    payment_method: 'stripe',
    payment_details: '',
});

const openPaymentModal = (booking) => {
    selectedBooking.value = booking;
    showPaymentModal.value = true;
    activeTab.value = 'stripe';
    paymentForm.payment_method = 'stripe';
    paymentForm.payment_details = '';
};

const closePaymentModal = () => {
    showPaymentModal.value = false;
    selectedBooking.value = null;
    paymentForm.reset();
};

const selectTab = (method) => {
    activeTab.value = method;
    paymentForm.payment_method = method;
};

const submitPayment = () => {
    if (!selectedBooking.value) return;
    
    paymentForm.post(route('payments.store', selectedBooking.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            closePaymentModal();
        },
    });
};
</script>

<template>
    <AppLayout title="My bookings">
        <template #header>
            <div>
                <h2 class="text-xl font-semibold text-gray-900">My bookings</h2>
                <p class="text-sm text-gray-500">Track approvals, invoices, and upcoming stays.</p>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-5xl mx-auto space-y-4 sm:px-6 lg:px-8">
                <div v-if="!props.bookings.length" class="rounded-2xl bg-white p-6 text-sm text-gray-500 shadow-sm">
                    You have no bookings yet. Explore available offices to get started.
                </div>

                <div v-else class="space-y-3">
                    <div v-for="booking in props.bookings" :key="booking.id" class="rounded-2xl bg-white p-5 shadow-sm">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ booking.office?.title }}</p>
                                <p class="text-xs text-gray-500">{{ booking.office?.city }}, {{ booking.office?.country }}</p>
                                <p class="mt-2 text-xs text-gray-500">{{ booking.start_date }} to {{ booking.end_date }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-900">${{ booking.total_amount.toLocaleString() }}</p>
                                <p class="text-xs text-gray-500">{{ booking.currency }}</p>
                            </div>
                        </div>
                        <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-2">
                                <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">{{ booking.status }}</span>
                                <span
                                    v-if="booking.payment?.status === 'paid'"
                                    class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-600"
                                >
                                    Paid
                                </span>
                                <span
                                    v-else-if="booking.payment?.status === 'pending'"
                                    class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-600"
                                >
                                    Payment Pending
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <Link class="text-xs font-semibold text-indigo-600" :href="route('offices.show', booking.office?.slug)">
                                    View office
                                </Link>
                                <button
                                    v-if="booking.status === 'approved' && booking.payment?.status !== 'paid'"
                                    type="button"
                                    class="rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white"
                                    @click="openPaymentModal(booking)"
                                >
                                    Pay now
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Modal -->
        <div v-if="showPaymentModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4" @click.self="closePaymentModal">
            <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Select Payment Method</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-600" @click="closePaymentModal">✕</button>
                </div>

                <div class="mt-4">
                    <p class="text-sm text-gray-500">Booking: {{ selectedBooking?.office?.title }}</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900">${{ selectedBooking?.total_amount?.toLocaleString() }} {{ selectedBooking?.currency }}</p>
                </div>

                <form class="mt-6 space-y-4" @submit.prevent="submitPayment">
                    <!-- Payment Method Tabs -->
                    <div class="grid grid-cols-4 gap-2">
                        <button
                            type="button"
                            class="rounded-xl border px-3 py-2 text-xs font-semibold transition"
                            :class="activeTab === 'stripe' ? 'border-indigo-600 bg-indigo-50 text-indigo-600' : 'border-gray-200 text-gray-600'"
                            @click="selectTab('stripe')"
                        >
                            Stripe
                        </button>
                        <button
                            type="button"
                            class="rounded-xl border px-3 py-2 text-xs font-semibold transition"
                            :class="activeTab === 'mobile_money' ? 'border-indigo-600 bg-indigo-50 text-indigo-600' : 'border-gray-200 text-gray-600'"
                            @click="selectTab('mobile_money')"
                        >
                            Mobile Money
                        </button>
                        <button
                            type="button"
                            class="rounded-xl border px-3 py-2 text-xs font-semibold transition"
                            :class="activeTab === 'bank_transfer' ? 'border-indigo-600 bg-indigo-50 text-indigo-600' : 'border-gray-200 text-gray-600'"
                            @click="selectTab('bank_transfer')"
                        >
                            Bank
                        </button>
                        <button
                            type="button"
                            class="rounded-xl border px-3 py-2 text-xs font-semibold transition"
                            :class="activeTab === 'pay_later' ? 'border-indigo-600 bg-indigo-50 text-indigo-600' : 'border-gray-200 text-gray-600'"
                            @click="selectTab('pay_later')"
                        >
                            Pay Later
                        </button>
                    </div>

                    <!-- Tab Content -->
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                        <div v-if="activeTab === 'stripe'">
                            <p class="text-sm font-semibold text-gray-900">Pay with Stripe</p>
                            <p class="mt-2 text-xs text-gray-500">Securely pay with credit or debit card.</p>
                            <div class="mt-3">
                                <input
                                    v-model="paymentForm.payment_details"
                                    type="text"
                                    placeholder="Card number (demo)"
                                    class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm"
                                />
                            </div>
                        </div>

                        <div v-else-if="activeTab === 'mobile_money'">
                            <p class="text-sm font-semibold text-gray-900">Mobile Money</p>
                            <p class="mt-2 text-xs text-gray-500">Pay via M-Pesa, MTN, Airtel Money, etc.</p>
                            <div class="mt-3">
                                <input
                                    v-model="paymentForm.payment_details"
                                    type="text"
                                    placeholder="Phone number"
                                    class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm"
                                />
                            </div>
                        </div>

                        <div v-else-if="activeTab === 'bank_transfer'">
                            <p class="text-sm font-semibold text-gray-900">Bank Transfer</p>
                            <p class="mt-2 text-xs text-gray-500">Transfer to:</p>
                            <div class="mt-2 space-y-1 text-xs text-gray-600">
                                <p><strong>Bank:</strong> OfficeBnB Bank</p>
                                <p><strong>Account:</strong> 1234567890</p>
                                <p><strong>Reference:</strong> {{ selectedBooking?.id }}</p>
                            </div>
                            <div class="mt-3">
                                <input
                                    v-model="paymentForm.payment_details"
                                    type="text"
                                    placeholder="Transaction reference"
                                    class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm"
                                />
                            </div>
                        </div>

                        <div v-else-if="activeTab === 'pay_later'">
                            <p class="text-sm font-semibold text-gray-900">Pay Later</p>
                            <p class="mt-2 text-xs text-gray-500">Confirm booking now, pay within 7 days.</p>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2">
                        <button
                            type="button"
                            class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-600"
                            @click="closePaymentModal"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white"
                            :disabled="paymentForm.processing"
                        >
                            {{ activeTab === 'pay_later' ? 'Confirm Booking' : 'Complete Payment' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
