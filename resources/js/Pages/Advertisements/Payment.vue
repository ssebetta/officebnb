<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    advertisement: Object,
    stripe_public_key: String,
});

const form = useForm({
    stripe_reference: '',
    payment_method: props.advertisement.payment_method,
});

const submit = () => {
    form.post(route('advertisements.process-payment', props.advertisement.id));
};

const paymentMethods = {
    stripe: {
        title: 'Credit/Debit Card',
        description: 'Visa, Mastercard, American Express',
        icon: '💳',
    },
    mobile_money: {
        title: 'Mobile Money',
        description: 'M-Pesa, Airtel Money',
        icon: '📱',
    },
};
</script>

<template>
    <Head title="Advertisement Payment" />

    <div class="min-h-screen bg-slate-50">
        <div class="mx-auto max-w-2xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="mb-8">
                <Link :href="route('listings.manage')" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
                    ← Back to listings
                </Link>
            </div>

            <div class="grid gap-8 md:grid-cols-3">
                <!-- Order Summary -->
                <div class="md:col-span-1">
                    <div class="sticky top-4 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-600">Order Summary</h3>

                        <div class="mt-6 space-y-4 border-t border-slate-200 pt-6">
                            <div class="flex items-center justify-between">
                                <p class="text-sm text-slate-600">
                                    {{ advertisement.type === 'premium' ? 'Premium' : 'Basic' }} Advertisement
                                </p>
                                <p class="font-semibold text-slate-900">${{ advertisement.amount_display }}</p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4">
                                <p class="text-xs text-slate-600">Duration</p>
                                <p class="text-2xl font-bold text-slate-900">
                                    {{ new Date(advertisement.end_date).getTime() - new Date(advertisement.start_date).getTime() > 0
                                        ? Math.ceil(
                                              (new Date(advertisement.end_date).getTime() -
                                                  new Date(advertisement.start_date).getTime()) /
                                                  (1000 * 60 * 60 * 24),
                                          ) + 1
                                        : 1 }}
                                    days
                                </p>
                                <p class="mt-1 text-xs text-slate-600">
                                    {{ advertisement.start_date }} to {{ advertisement.end_date }}
                                </p>
                            </div>

                            <div class="flex items-center justify-between border-t border-slate-200 pt-4">
                                <p class="font-semibold text-slate-900">Total</p>
                                <p class="text-2xl font-bold text-indigo-600">${{ advertisement.amount_display }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Form -->
                <div class="md:col-span-2">
                    <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                        <h2 class="text-2xl font-semibold text-slate-900">Complete Payment</h2>
                        <p class="mt-2 text-slate-600">Choose your preferred payment method below</p>

                        <form @submit.prevent="submit" class="mt-8 space-y-6">
                            <!-- Payment Method Selection -->
                            <div class="space-y-4">
                                <label class="block text-sm font-semibold text-slate-900">Payment Method</label>
                                <div class="grid grid-cols-2 gap-4">
                                    <label
                                        v-for="(method, key) in paymentMethods"
                                        :key="key"
                                        class="relative cursor-pointer"
                                    >
                                        <input
                                            v-model="form.payment_method"
                                            type="radio"
                                            :value="key"
                                            class="sr-only"
                                        />
                                        <div
                                            class="rounded-2xl border-2 p-4 transition"
                                            :class="
                                                form.payment_method === key
                                                    ? 'border-indigo-600 bg-indigo-50'
                                                    : 'border-slate-200 hover:border-slate-300'
                                            "
                                        >
                                            <div class="text-2xl">{{ method.icon }}</div>
                                            <p class="mt-2 font-semibold text-slate-900">{{ method.title }}</p>
                                            <p class="text-xs text-slate-600">{{ method.description }}</p>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Stripe Card Element (if Stripe selected) -->
                            <div v-if="form.payment_method === 'stripe'" class="space-y-4">
                                <label class="block text-sm font-semibold text-slate-900">Card Details</label>
                                <div class="rounded-xl border border-slate-200 p-4">
                                    <p class="text-sm text-slate-600">Enter your card details to complete the payment</p>
                                    <p class="mt-2 text-xs text-slate-500">
                                        Card processing handled securely by Stripe
                                    </p>
                                </div>
                                <input
                                    v-model="form.stripe_reference"
                                    type="text"
                                    placeholder="Stripe reference (optional)"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
                                />
                            </div>

                            <!-- Mobile Money Info (if Mobile Money selected) -->
                            <div v-else class="space-y-4">
                                <label class="block text-sm font-semibold text-slate-900">Pay with Mobile Money</label>
                                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                    <p class="text-sm text-slate-600">
                                        You will be redirected to complete the payment using M-Pesa, Airtel Money, or other mobile money services.
                                    </p>
                                </div>
                            </div>

                            <!-- Terms -->
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <label class="flex items-start gap-3">
                                    <input type="checkbox" class="mt-1 rounded border-slate-300" required />
                                    <span class="text-sm text-slate-600">
                                        I agree to the terms and understand this is a paid advertisement
                                    </span>
                                </label>
                            </div>

                            <!-- Submit -->
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full rounded-xl bg-indigo-600 px-6 py-3 font-semibold text-white transition hover:bg-indigo-700 disabled:opacity-50"
                            >
                                {{ form.processing ? 'Processing Payment...' : `Pay $${advertisement.amount_display}` }}
                            </button>

                            <div v-if="form.errors.payment" class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                                {{ form.errors.payment }}
                            </div>
                        </form>

                        <!-- Cancel Link -->
                        <div class="mt-6 text-center">
                            <Link
                                :href="route('listings.manage')"
                                class="text-sm font-semibold text-slate-600 hover:text-slate-900"
                            >
                                Cancel and return to listings
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
