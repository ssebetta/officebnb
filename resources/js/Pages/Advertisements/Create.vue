<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    office: Object,
});

const form = useForm({
    type: 'premium',
    image_url: props.office.image_urls[0] || '',
    image_file: null,
    start_date: new Date().toISOString().split('T')[0],
    end_date: new Date(Date.now() + 86400000).toISOString().split('T')[0],
    payment_method: 'stripe',
});

const daysSelected = computed(() => {
    if (!form.start_date || !form.end_date) return 0;
    const start = new Date(form.start_date);
    const end = new Date(form.end_date);
    return Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
});

const dailyRate = computed(() => {
    return form.type === 'premium' ? 9.99 : 3.99;
});

const totalPrice = computed(() => {
    return (dailyRate.value * daysSelected.value).toFixed(2);
});

const handleImageUpload = (event) => {
    const file = event.target.files[0];
    form.image_file = file || null;
    if (file) {
        form.image_url = '';
    }
};

const submit = () => {
    form.post(route('advertisements.store', props.office.slug), { forceFormData: true });
};
</script>

<template>
    <Head title="Advertise Your Office" />

    <div class="min-h-screen bg-slate-50">
        <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="mb-8">
                <Link :href="route('listings.manage')" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
                    ← Back to listings
                </Link>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                <h1 class="text-3xl font-semibold text-slate-900">Advertise {{ office.title }}</h1>
                <p class="mt-2 text-slate-600">Boost your office visibility with a premium or basic advertisement</p>

                <form @submit.prevent="submit" class="mt-8 space-y-8">
                    <!-- Advertisement Type -->
                    <div class="space-y-4">
                        <label class="block text-sm font-semibold text-slate-900">Advertisement Type</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="relative cursor-pointer">
                                <input v-model="form.type" type="radio" value="premium" class="sr-only" />
                                <div class="rounded-2xl border-2 p-4" :class="form.type === 'premium' ? 'border-indigo-600 bg-indigo-50' : 'border-slate-200'">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-semibold text-slate-900">Premium</p>
                                            <p class="text-sm text-slate-600">Featured in hero carousel & priority in search</p>
                                        </div>
                                        <span class="text-lg font-bold text-indigo-600">$9.99/day</span>
                                    </div>
                                </div>
                            </label>

                            <label class="relative cursor-pointer">
                                <input v-model="form.type" type="radio" value="basic" class="sr-only" />
                                <div class="rounded-2xl border-2 p-4" :class="form.type === 'basic' ? 'border-indigo-600 bg-indigo-50' : 'border-slate-200'">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-semibold text-slate-900">Basic</p>
                                            <p class="text-sm text-slate-600">Priority in search results</p>
                                        </div>
                                        <span class="text-lg font-bold text-slate-600">$3.99/day</span>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Image Selection -->
                    <div class="space-y-4">
                        <label class="block text-sm font-semibold text-slate-900">Advertisement Image</label>
                        <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-4">
                            <label class="flex cursor-pointer flex-col items-center gap-2 text-sm text-slate-600">
                                <span class="font-semibold text-slate-800">Upload a new image</span>
                                <span class="text-xs">JPEG/PNG up to 5MB</span>
                                <input type="file" accept="image/*" class="hidden" @change="handleImageUpload" />
                            </label>
                        </div>
                        <p class="text-xs text-slate-500">Or pick from your existing office photos:</p>
                        <div class="grid grid-cols-3 gap-4 sm:grid-cols-4">
                            <label v-for="image in office.image_urls" :key="image" class="relative cursor-pointer">
                                <input
                                    v-model="form.image_url"
                                    type="radio"
                                    :value="image"
                                    @change="form.image_file = null"
                                    class="sr-only"
                                />
                                <img
                                    :src="image"
                                    :alt="office.title"
                                    class="h-24 w-full rounded-xl object-cover ring-2 ring-offset-2 transition"
                                    :class="form.image_url === image ? 'ring-indigo-600' : 'ring-slate-200'"
                                />
                            </label>
                        </div>
                    </div>

                    <!-- Date Range -->
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                        <div class="space-y-1.5 sm:col-span-1">
                            <label class="text-sm font-semibold text-slate-900">Start Date</label>
                            <input
                                v-model="form.start_date"
                                type="date"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-slate-900 focus:border-indigo-600 focus:outline-none focus:ring-1 focus:ring-indigo-600"
                            />
                        </div>
                        <div class="space-y-1.5 sm:col-span-1">
                            <label class="text-sm font-semibold text-slate-900">End Date</label>
                            <input
                                v-model="form.end_date"
                                type="date"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-slate-900 focus:border-indigo-600 focus:outline-none focus:ring-1 focus:ring-indigo-600"
                            />
                        </div>
                        <div class="flex items-end sm:col-span-1">
                            <div class="rounded-xl bg-slate-100 px-3 py-2 text-center">
                                <p class="text-xs text-slate-600">Duration</p>
                                <p class="text-lg font-semibold text-slate-900">{{ daysSelected }}d</p>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="space-y-4">
                        <label class="block text-sm font-semibold text-slate-900">Payment Method</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="relative cursor-pointer">
                                <input v-model="form.payment_method" type="radio" value="stripe" class="sr-only" />
                                <div class="rounded-2xl border-2 p-4" :class="form.payment_method === 'stripe' ? 'border-indigo-600 bg-indigo-50' : 'border-slate-200'">
                                    <p class="font-semibold text-slate-900">Stripe</p>
                                    <p class="text-sm text-slate-600">Credit card</p>
                                </div>
                            </label>

                            <label class="relative cursor-pointer">
                                <input v-model="form.payment_method" type="radio" value="mobile_money" class="sr-only" />
                                <div class="rounded-2xl border-2 p-4" :class="form.payment_method === 'mobile_money' ? 'border-indigo-600 bg-indigo-50' : 'border-slate-200'">
                                    <p class="font-semibold text-slate-900">Mobile Money</p>
                                    <p class="text-sm text-slate-600">M-Pesa, Airtel Money</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Price Summary -->
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-slate-600">{{ form.type === 'premium' ? 'Premium' : 'Basic' }} advertisement</p>
                                <p class="text-sm text-slate-600">{{ daysSelected }} days × ${{ dailyRate.toFixed(2) }}/day</p>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-slate-900">${{ totalPrice }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="flex gap-4">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="flex-1 rounded-xl bg-indigo-600 px-6 py-3 font-semibold text-white transition hover:bg-indigo-700 disabled:opacity-50"
                        >
                            {{ form.processing ? 'Processing...' : 'Continue to Payment' }}
                        </button>
                        <Link
                            :href="route('listings.manage')"
                            class="rounded-xl border border-slate-200 px-6 py-3 font-semibold text-slate-900 transition hover:bg-slate-50"
                        >
                            Cancel
                        </Link>
                    </div>

                    <div v-if="form.errors.image_url || form.errors.image_file" class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                        {{ form.errors.image_url || form.errors.image_file }}
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
