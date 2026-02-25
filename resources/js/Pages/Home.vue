<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import OfficeLogo from '@/Components/OfficeLogo.vue';
import Footer from '@/Components/Footer.vue';
import SeoHead from '@/Components/SeoHead.vue';

const props = defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    premiumAdvertisements: {
        type: Array,
        default: () => [],
    },
    offices: {
        type: Array,
        default: () => [],
    },
});

const jsonLd = {
    '@context': 'https://schema.org',
    '@type': 'WebApplication',
    'name': 'OfficeBnB',
    'description': 'Find and book flexible office spaces by the day, week, or month. Premium workspaces for teams that move fast.',
    'url': window.location.origin,
    'applicationCategory': 'BusinessApplication',
    'offers': {
        '@type': 'AggregateOffer',
        'priceCurrency': 'USD',
        'description': 'Flexible office space rentals',
    },
    'aggregateRating': {
        '@type': 'AggregateRating',
        'ratingValue': '4.8',
        'bestRating': '5',
        'worstRating': '1',
        'ratingCount': '324',
    },
};

const organizationSchema = {
    '@context': 'https://schema.org',
    '@type': 'Organization',
    'name': 'OfficeBnB',
    'url': 'https://officebnb.systimo.org',
    'logo': 'https://officebnb.systimo.org/web/icon-512.png',
    'description': 'Premium office spaces on demand. Find and book flexible workspaces by the day, week, or month.',
    'email': 'hello@systimo.org',
    'foundingDate': '2026',
    'sameAs': [
        'https://twitter.com/officebnb',
        'https://linkedin.com/company/officebnb',
        'https://facebook.com/officebnb'
    ],
    'address': {
        '@type': 'PostalAddress',
        'addressCountry': 'US'
    }
};

const searchLocation = ref('');
const searchTeamSize = ref('');
const currentAdIndex = ref(0);

onMounted(() => {
    const appScript = document.createElement('script');
    appScript.type = 'application/ld+json';
    appScript.textContent = JSON.stringify(jsonLd);
    document.head.appendChild(appScript);

    const orgScript = document.createElement('script');
    orgScript.type = 'application/ld+json';
    orgScript.textContent = JSON.stringify(organizationSchema);
    document.head.appendChild(orgScript);
});
const defaultHeroImage = 'https://images.unsplash.com/photo-1465447142348-e9952c393450?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80';
const heroSlide = {
    id: 'hero',
    type: 'hero',
    image_url: defaultHeroImage,
};

const displayedAds = computed(() => {
    return [heroSlide, ...props.premiumAdvertisements];
});

const currentAd = computed(() => {
    return displayedAds.value[currentAdIndex.value] || null;
});

const heroBackground = computed(() => {
    const imageUrl = currentAd.value?.image_url || defaultHeroImage;
    return {
        backgroundImage: `linear-gradient(135deg, rgba(15, 27, 45, 0.85) 0%, rgba(15, 27, 45, 0.6) 100%), url('${imageUrl}')`,
    };
});

const handleSearch = () => {
    router.get(route('offices.index'), {
        location: searchLocation.value,
        team_size: searchTeamSize.value,
    });
};

const goToListings = (sort) => {
    router.get(route('offices.index'), { sort });
};

const nextAd = () => {
    if (displayedAds.value.length > 0) {
        currentAdIndex.value = (currentAdIndex.value + 1) % displayedAds.value.length;
    }
};

const prevAd = () => {
    if (displayedAds.value.length > 0) {
        currentAdIndex.value = (currentAdIndex.value - 1 + displayedAds.value.length) % displayedAds.value.length;
    }
};

const goToAd = (index) => {
    currentAdIndex.value = index;
};

let autoplayInterval = null;

onMounted(() => {
    if (displayedAds.value.length > 1) {
        autoplayInterval = setInterval(() => {
            nextAd();
        }, 5000);
    }
});

onBeforeUnmount(() => {
    if (autoplayInterval) {
        clearInterval(autoplayInterval);
    }
});
</script>

<template>
    <Head title="Find Premium Office Spaces - Book Daily, Weekly, or Monthly">
        <meta name="description" content="Discover and book flexible office spaces for your team. Premium workspaces available by day, week, or month. Find the perfect office space at OfficeBnB." />
        <meta name="og:title" content="OfficeBnB - Premium Office Spaces On Demand" />
        <meta name="og:description" content="Find and book flexible office spaces by the day, week, or month. Premium workspaces for teams that move fast." />
    </Head>

    <div class="min-h-screen bg-slate-50 text-slate-900">
        <!-- Hero Section -->
        <div class="relative min-h-[600px] bg-cover bg-center" :style="heroBackground">
            <div class="absolute inset-0 -z-10">
                <div class="absolute -top-24 right-0 h-72 w-72 rounded-full bg-indigo-100/70 blur-3xl"></div>
                <div class="absolute bottom-0 left-0 h-80 w-80 rounded-full bg-sky-100/60 blur-3xl"></div>
            </div>

            <div class="mx-auto flex min-h-[600px] max-w-6xl flex-col justify-center px-4 pb-20 pt-4 sm:px-6 lg:px-8">
                <header class="mb-12 flex items-center justify-between">
                    <OfficeLogo :dark="false" />

                    <nav v-if="props.canLogin" class="flex items-center gap-2 text-xs sm:text-sm">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="rounded-full border border-white/30 px-3 py-1.5 font-semibold text-white transition hover:border-white/60"
                        >
                            Dashboard
                        </Link>

                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="rounded-full border border-transparent px-3 py-1.5 font-semibold text-white transition hover:border-white/30"
                            >
                                Log in
                            </Link>
                            <Link
                                v-if="props.canRegister"
                                :href="route('register')"
                                class="rounded-full bg-indigo-600 px-3.5 py-1.5 text-xs font-semibold text-white shadow-lg shadow-indigo-600/40 transition hover:bg-indigo-700 sm:text-sm"
                            >
                                Create account
                            </Link>
                        </template>
                    </nav>
                </header>

                <div class="space-y-8 text-white">
                    <div class="space-y-3">
                        <p class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-[0.7rem] font-semibold uppercase tracking-[0.16em] text-slate-200 backdrop-blur">
                            Office stays, reimagined
                        </p>
                        <h1 class="text-balance font-['Space_Grotesk'] text-4xl font-semibold tracking-tight sm:text-5xl">
                            Professional offices for teams that move fast.
                        </h1>
                        <p class="text-base leading-relaxed text-slate-300 sm:max-w-2xl">
                            OfficeBnB helps teams book flexible workspaces by the day, week, or month. Curated hosts, transparent pricing, and seamless experience.
                        </p>
                    </div>

                    <div v-if="currentAd && currentAd.type === 'premium'" class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <p class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-amber-200">Sponsored office</p>
                                <p class="mt-1 text-lg font-semibold text-white">{{ currentAd.office_title }}</p>
                                <p class="text-sm text-slate-300">{{ currentAd.office_city }}, {{ currentAd.office_country }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[0.7rem] uppercase tracking-[0.2em] text-slate-300">From</p>
                                <p class="text-xl font-semibold text-white">${{ currentAd.daily_rate.toLocaleString() }} / day</p>
                            </div>
                        </div>
                        <div class="mt-3">
                            <Link
                                :href="route('offices.show', currentAd.office_slug)"
                                class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-1.5 text-xs font-semibold text-white transition hover:bg-white/25"
                            >
                                View sponsored office
                            </Link>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-white/20 bg-white/10 p-4 shadow-[0_18px_40px_rgba(0,0,0,0.3)] backdrop-blur-md">
                        <form class="grid gap-3 sm:grid-cols-[1.5fr_1fr_auto] sm:items-end" @submit.prevent="handleSearch">
                            <div class="space-y-1.5">
                                <label class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-200">
                                    Location
                                </label>
                                <input
                                    v-model="searchLocation"
                                    type="text"
                                    placeholder="City or district"
                                    class="w-full rounded-2xl border border-white/20 bg-white/5 px-3 py-2 text-sm text-white placeholder:text-slate-400 backdrop-blur focus:border-white/40 focus:outline-none focus:ring-1 focus:ring-white/40"
                                />
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-200">
                                    Team size
                                </label>
                                <input
                                    v-model="searchTeamSize"
                                    type="number"
                                    min="1"
                                    placeholder="4"
                                    class="w-full rounded-2xl border border-white/20 bg-white/5 px-3 py-2 text-sm text-white placeholder:text-slate-400 backdrop-blur focus:border-white/40 focus:outline-none focus:ring-1 focus:ring-white/40"
                                />
                            </div>
                            <button
                                type="submit"
                                class="inline-flex w-full items-center justify-center rounded-2xl bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700 sm:w-auto"
                            >
                                Search
                            </button>
                        </form>
                        <p class="mt-3 text-[0.7rem] text-slate-300 sm:text-xs">
                            Join now to unlock bookings, favourites, and landlord tools.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-3 text-xs text-slate-200">
                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-white/30 transition hover:border-white/60"
                                @click="prevAd"
                                aria-label="Previous slide"
                            >
                                ‹
                            </button>
                            <button
                                type="button"
                                class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-white/30 transition hover:border-white/60"
                                @click="nextAd"
                                aria-label="Next slide"
                            >
                                ›
                            </button>
                        </div>
                        <div class="flex items-center gap-2">
                            <button
                                v-for="(ad, index) in displayedAds"
                                :key="ad.id"
                                type="button"
                                class="h-2.5 w-2.5 rounded-full border border-white/60 transition"
                                :class="index === currentAdIndex ? 'bg-white' : 'bg-transparent'"
                                @click="goToAd(index)"
                                :aria-label="`Go to slide ${index + 1}`"
                            ></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <main class="mx-auto flex max-w-6xl flex-col px-4 pb-20 sm:px-6 lg:px-8">

                <section class="space-y-4">
                    <div class="flex items-baseline justify-between gap-2">
                        <div>
                            <h2 class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-500">
                                Featured offices
                            </h2>
                            <p class="mt-1 text-xs text-slate-400">
                                Curated workspaces from premium hosts.
                            </p>
                        </div>
                        <Link class="text-xs font-semibold text-[#0F1B2D]" :href="route('offices.index')">Explore all</Link>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <button
                            type="button"
                            class="rounded-full border border-slate-200 px-3 py-1 text-[0.7rem] font-semibold text-slate-600 transition hover:border-slate-300"
                            @click="goToListings('recommended')"
                        >
                            Recommended
                        </button>
                        <button
                            type="button"
                            class="rounded-full border border-slate-200 px-3 py-1 text-[0.7rem] font-semibold text-slate-600 transition hover:border-slate-300"
                            @click="goToListings('premium')"
                        >
                            Premium ads
                        </button>
                        <button
                            type="button"
                            class="rounded-full border border-slate-200 px-3 py-1 text-[0.7rem] font-semibold text-slate-600 transition hover:border-slate-300"
                            @click="goToListings('basic')"
                        >
                            Sponsored
                        </button>
                        <button
                            type="button"
                            class="rounded-full border border-slate-200 px-3 py-1 text-[0.7rem] font-semibold text-slate-600 transition hover:border-slate-300"
                            @click="goToListings('best_reviewed')"
                        >
                            Best reviewed
                        </button>
                    </div>

                    <div v-if="!props.offices.length" class="rounded-2xl border border-dashed border-slate-200 bg-white px-4 py-8 text-center text-sm text-slate-500">
                        No offices have been listed yet. Create an account to be the first landlord on OfficeBnB.
                    </div>

                    <div
                        v-else
                        class="-mx-2 flex gap-4 overflow-x-auto pb-2 pt-1 sm:mx-0 sm:grid sm:grid-cols-2 sm:gap-4 lg:grid-cols-3 lg:gap-5 sm:overflow-visible"
                    >
                        <article
                            v-for="office in props.offices"
                            :key="office.id"
                            class="snap-start shrink-0 basis-72 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5"
                        >
                            <div class="h-36 bg-slate-100">
                                <img
                                    v-if="office.image_urls?.length"
                                    :src="office.image_urls[0]"
                                    :alt="office.title"
                                    class="h-full w-full object-cover"
                                />
                            </div>
                            <div class="space-y-3 p-4">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-slate-400">
                                            {{ office.workspace_type || 'Office' }}
                                        </p>
                                        <span
                                            v-if="office.advertisement_type === 'premium'"
                                            class="rounded-full bg-amber-100 px-2 py-1 text-[0.65rem] font-semibold text-amber-700"
                                        >
                                            Premium ad
                                        </span>
                                        <span
                                            v-else-if="office.advertisement_type === 'basic'"
                                            class="rounded-full bg-slate-100 px-2 py-1 text-[0.65rem] font-semibold text-slate-600"
                                        >
                                            Sponsored
                                        </span>
                                    </div>
                                    <div class="rounded-full bg-slate-100 px-2 py-1 text-[0.65rem] text-slate-500">
                                        Up to {{ office.capacity }}
                                    </div>
                                </div>
                                <div>
                                    <h3 class="truncate text-sm font-semibold text-slate-900">
                                        {{ office.title }}
                                    </h3>
                                    <p class="mt-1 text-[0.7rem] text-slate-500">
                                        {{ office.city }}, {{ office.country }}
                                    </p>
                                </div>
                                <p class="line-clamp-2 text-[0.75rem] text-slate-500">
                                    {{ office.description }}
                                </p>
                                <div class="flex items-center justify-between text-xs">
                                    <div class="text-[#0F1B2D]">
                                        <span class="text-sm font-semibold">
                                            ${{ office.daily_rate.toLocaleString() }}
                                        </span>
                                        <span class="text-[0.7rem] text-slate-400">/ day</span>
                                    </div>
                                    <div class="flex items-center gap-1 text-[0.7rem] text-slate-500">
                                        <span class="text-amber-400">★</span>
                                        <span>{{ office.average_rating ? office.average_rating.toFixed(1) : 'New' }}</span>
                                    </div>
                                    <Link class="text-[0.7rem] font-semibold text-[#0F1B2D]" :href="route('offices.show', office.slug)">
                                        View
                                    </Link>
                                </div>
                            </div>
                        </article>
                    </div>
                </section>

                <section class="grid gap-4 pb-4 sm:grid-cols-3">
                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                        <p class="text-sm font-semibold text-slate-900">Enterprise-ready</p>
                        <p class="mt-2 text-xs text-slate-500">
                            Flexible procurement with vetted hosts and clear invoices.
                        </p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                        <p class="text-sm font-semibold text-slate-900">Team-first layout</p>
                        <p class="mt-2 text-xs text-slate-500">
                            Offices designed for collaboration, workshops, and focus.
                        </p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                        <p class="text-sm font-semibold text-slate-900">Trusted hosts</p>
                        <p class="mt-2 text-xs text-slate-500">
                            Dedicated support and quality control for every stay.
                        </p>
                    </div>
                </section>

            <Footer />
        </main>

        <div class="fixed bottom-4 left-0 right-0 z-30 flex justify-center px-4 sm:hidden">
            <Link
                :href="route('offices.index')"
                class="flex w-full max-w-md items-center justify-between rounded-2xl bg-[#0F1B2D] px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-slate-400/40"
            >
                Explore offices
                <span class="rounded-full bg-white/15 px-2 py-1 text-[0.65rem]">Browse</span>
            </Link>
        </div>
    </div>
</template>

