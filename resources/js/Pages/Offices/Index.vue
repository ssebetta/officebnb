<script setup>
import { reactive, computed, onMounted } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';

const page = usePage();
const Layout = computed(() => page.props.auth.user ? AppLayout : GuestLayout);

const props = defineProps({
    offices: Object,
    filters: Object,
});

const jsonLd = {
    '@context': 'https://schema.org',
    '@type': 'CollectionPage',
    'name': 'Office Spaces for Rent - OfficeBnB',
    'description': 'Browse and book flexible office spaces. Find premium workspaces by the day, week, or month.',
    'url': window.location.href,
};

onMounted(() => {
    const script = document.createElement('script');
    script.type = 'application/ld+json';
    script.textContent = JSON.stringify(jsonLd);
    document.head.appendChild(script);
});

const filters = reactive({
    search: props.filters?.search || '',
    min_rate: props.filters?.min_rate || '',
    max_rate: props.filters?.max_rate || '',
    capacity: props.filters?.capacity || '',
    sort: props.filters?.sort || 'recommended',
});

const submitFilters = () => {
    router.get(route('offices.index'), filters, { preserveState: true, replace: true });
};

const setSort = (value) => {
    filters.sort = value;
    submitFilters();
};
</script>

<template>
    <component :is="Layout" title="Explore offices">
        <Head title="Browse Office Spaces - Find Your Perfect Workspace at OfficeBnB">
            <meta name="description" content="Browse premium office spaces available for daily, weekly, and monthly rental. Find flexible workspaces in your city with OfficeBnB." />
            <meta name="og:title" content="Office Spaces for Rent - OfficeBnB" />
            <meta name="og:description" content="Discover flexible office spaces perfect for your team. Browse premium workspaces by the day, week, or month." />
            <meta name="og:type" content="website" />
            <meta name="keywords" content="office space, office rental, flexible workspace, coworking space, day office, weekly office, monthly office, office for rent" />
            <link rel="canonical" :href="route('offices.index')" />
        </Head>
        <template #header>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Explore offices</h2>
                    <p class="text-sm text-gray-500">Find flexible workspaces built for modern teams.</p>
                </div>
                <Link class="text-sm font-semibold text-indigo-600" :href="route('offices.index')">Reset filters</Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto space-y-6 sm:px-6 lg:px-8">
                <div class="flex flex-wrap gap-2">
                    <button
                        type="button"
                        class="rounded-full border px-3 py-1 text-xs font-semibold transition"
                        :class="filters.sort === 'recommended' ? 'border-indigo-600 bg-indigo-50 text-indigo-700' : 'border-gray-200 text-gray-600 hover:border-gray-300'"
                        @click="setSort('recommended')"
                    >
                        Recommended
                    </button>
                    <button
                        type="button"
                        class="rounded-full border px-3 py-1 text-xs font-semibold transition"
                        :class="filters.sort === 'premium' ? 'border-indigo-600 bg-indigo-50 text-indigo-700' : 'border-gray-200 text-gray-600 hover:border-gray-300'"
                        @click="setSort('premium')"
                    >
                        Premium ads
                    </button>
                    <button
                        type="button"
                        class="rounded-full border px-3 py-1 text-xs font-semibold transition"
                        :class="filters.sort === 'basic' ? 'border-indigo-600 bg-indigo-50 text-indigo-700' : 'border-gray-200 text-gray-600 hover:border-gray-300'"
                        @click="setSort('basic')"
                    >
                        Sponsored
                    </button>
                    <button
                        type="button"
                        class="rounded-full border px-3 py-1 text-xs font-semibold transition"
                        :class="filters.sort === 'best_reviewed' ? 'border-indigo-600 bg-indigo-50 text-indigo-700' : 'border-gray-200 text-gray-600 hover:border-gray-300'"
                        @click="setSort('best_reviewed')"
                    >
                        Best reviewed
                    </button>
                </div>

                <form class="grid gap-3 rounded-2xl bg-white p-5 shadow-sm md:grid-cols-4" @submit.prevent="submitFilters">
                    <div class="md:col-span-2">
                        <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Location</label>
                        <input
                            v-model="filters.search"
                            type="text"
                            placeholder="City, landmark, or address"
                            class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm"
                        />
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Capacity</label>
                        <input
                            v-model="filters.capacity"
                            type="number"
                            min="1"
                            placeholder="Any"
                            class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm"
                        />
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Max daily rate</label>
                        <input
                            v-model="filters.max_rate"
                            type="number"
                            min="20"
                            placeholder="$"
                            class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm"
                        />
                    </div>
                    <button
                        type="submit"
                        class="md:col-span-4 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white"
                    >
                        Apply filters
                    </button>
                </form>

                <div v-if="!props.offices?.data?.length" class="rounded-2xl bg-white p-6 text-sm text-gray-500 shadow-sm">
                    No offices match those filters yet.
                </div>

                <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <article
                        v-for="office in props.offices.data"
                        :key="office.id"
                        class="overflow-hidden rounded-2xl bg-white shadow-sm transition hover:-translate-y-0.5"
                    >
                        <div class="h-40 bg-gray-100">
                            <img
                                v-if="office.image_urls?.length"
                                :src="office.image_urls[0]"
                                :alt="office.title"
                                class="h-full w-full object-cover"
                            />
                        </div>
                        <div class="space-y-2 p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ office.workspace_type || 'Workspace' }}</p>
                                    <span
                                        v-if="office.advertisement_type === 'premium'"
                                        class="rounded-full bg-amber-100 px-2 py-0.5 text-[0.65rem] font-semibold text-amber-700"
                                    >
                                        Premium ad
                                    </span>
                                    <span
                                        v-else-if="office.advertisement_type === 'basic'"
                                        class="rounded-full bg-gray-100 px-2 py-0.5 text-[0.65rem] font-semibold text-gray-600"
                                    >
                                        Sponsored
                                    </span>
                                </div>
                                <span class="rounded-full bg-indigo-50 px-2 py-1 text-xs font-semibold text-indigo-600">
                                    Up to {{ office.capacity }}
                                </span>
                            </div>
                            <h3 class="text-base font-semibold text-gray-900">{{ office.title }}</h3>
                            <p class="text-xs text-gray-500">{{ office.city }}, {{ office.country }}</p>
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-semibold text-gray-900">${{ office.daily_rate.toLocaleString() }}<span class="text-xs text-gray-400"> / day</span></p>
                                <div class="flex items-center gap-1 text-xs text-gray-500">
                                    <span class="text-amber-400">★</span>
                                    <span>{{ office.average_rating ? office.average_rating.toFixed(1) : 'New' }}</span>
                                </div>
                                <Link class="text-sm font-semibold text-indigo-600" :href="route('offices.show', office.slug)">View</Link>
                            </div>
                        </div>
                    </article>
                </div>

                <div v-if="props.offices?.links?.length" class="flex flex-wrap gap-2">
                    <Link
                        v-for="link in props.offices.links"
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
    </component>
</template>
