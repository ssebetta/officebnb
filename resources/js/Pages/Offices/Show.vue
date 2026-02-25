<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { computed, ref, onMounted } from 'vue';

const props = defineProps({
    office: Object,
});

const page = usePage();
const Layout = computed(() => page.props.auth.user ? AppLayout : GuestLayout);
const userReview = computed(() => props.office.reviews?.find(r => r.user.id === page.props.auth.user?.id));

const jsonLd = computed(() => ({
    '@context': 'https://schema.org',
    '@type': 'Place',
    'name': props.office.title,
    'description': props.office.description,
    'address': {
        '@type': 'PostalAddress',
        'streetAddress': props.office.address,
        'addressLocality': props.office.city,
        'addressCountry': props.office.country,
    },
    'image': props.office.image_urls?.[0] || '',
    'priceRange': `$${props.office.daily_rate}`,
    'aggregateRating': {
        '@type': 'AggregateRating',
        'ratingValue': props.office.average_rating || 5,
        'bestRating': '5',
        'worstRating': '1',
        'ratingCount': props.office.reviews?.length || 0,
    },
    'amenities': props.office.features || [],
}));

const breadcrumbSchema = computed(() => ({
    '@context': 'https://schema.org',
    '@type': 'BreadcrumbList',
    'itemListElement': [
        {
            '@type': 'ListItem',
            'position': 1,
            'name': 'Home',
            'item': window.location.origin
        },
        {
            '@type': 'ListItem',
            'position': 2,
            'name': 'Offices',
            'item': `${window.location.origin}/offices`
        },
        {
            '@type': 'ListItem',
            'position': 3,
            'name': props.office.title,
            'item': `${window.location.origin}/offices/${props.office.slug}`
        }
    ]
}));

onMounted(() => {
    const placeScript = document.createElement('script');
    placeScript.type = 'application/ld+json';
    placeScript.textContent = JSON.stringify(jsonLd.value);
    document.head.appendChild(placeScript);

    const breadcrumbScript = document.createElement('script');
    breadcrumbScript.type = 'application/ld+json';
    breadcrumbScript.textContent = JSON.stringify(breadcrumbSchema.value);
    document.head.appendChild(breadcrumbScript);
});

const form = useForm({
    start_date: '',
    end_date: '',
    guests: 1,
    notes: '',
});

const reviewForm = useForm({
    rating: userReview.value?.rating || 5,
    comment: userReview.value?.comment || '',
});

const submitBooking = () => {
    form.post(route('bookings.store', props.office.slug), {
        preserveScroll: true,
    });
};

const submitReview = () => {
    reviewForm.post(route('reviews.store', props.office.slug), {
        preserveScroll: true,
    });
};

const deleteReview = (reviewId) => {
    if (confirm('Are you sure you want to delete this review?')) {
        usePage().remember = false;
        
        const deleteForm = useForm({});
        deleteForm.delete(route('reviews.delete', reviewId), {
            preserveScroll: true,
        });
    }
};

const renderStars = (rating) => {
    return '★'.repeat(Math.round(rating)) + '☆'.repeat(5 - Math.round(rating));
};
</script>

<template>
    <component :is="Layout" :title="office.title">
        <Head :title="`${office.title} - Book Office Space at OfficeBnB`">
            <meta name="description" :content="`Book ${office.title} - A ${office.workspace_type} office in ${office.city}, ${office.country}. From $${office.daily_rate}/day. ${office.reviews?.length || 0} reviews, ${office.average_rating || 5}★ rating.`" />
            <meta name="og:title" :content="`${office.title} - OfficeBnB`" />
            <meta name="og:description" :content="office.description" />
            <meta v-if="office.image_urls?.[0]" name="og:image" :content="office.image_urls[0]" />
            <meta name="og:type" content="property" />
            <meta name="twitter:card" content="summary_large_image" />
            <meta name="twitter:title" :content="`${office.title} - OfficeBnB`" />
            <meta name="twitter:description" :content="office.description" />
            <meta v-if="office.image_urls?.[0]" name="twitter:image" :content="office.image_urls[0]" />
            <meta name="keywords" :content="`office space, workspace, ${office.workspace_type}, ${office.city}, ${office.country}, office rental, coworking`" />
            <link rel="canonical" :href="`${window.location.origin}/offices/${office.slug}`" />
        </Head>
        <template #header>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ office.workspace_type || 'Workspace' }}</p>
                    <h2 class="text-xl font-semibold text-gray-900">{{ office.title }}</h2>
                    <p class="text-sm text-gray-500">{{ office.city }}, {{ office.country }}</p>
                </div>
                <div class="text-left sm:text-right">
                    <p class="text-sm text-gray-500">Daily rate</p>
                    <p class="text-2xl font-semibold text-gray-900">${{ office.daily_rate.toLocaleString() }}</p>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-6xl mx-auto space-y-6 sm:px-6 lg:px-8">
                <div class="grid gap-4 lg:grid-cols-[2fr_1fr]">
                    <div class="space-y-4">
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="overflow-hidden rounded-2xl bg-gray-100">
                                <img
                                    v-if="office.image_urls?.length"
                                    :src="office.image_urls[0]"
                                    :alt="office.title"
                                    class="h-52 w-full object-cover"
                                />
                            </div>
                            <div class="grid gap-3">
                                <div class="overflow-hidden rounded-2xl bg-gray-100">
                                    <img
                                        v-if="office.image_urls?.length > 1"
                                        :src="office.image_urls[1]"
                                        :alt="office.title"
                                        class="h-24 w-full object-cover"
                                    />
                                </div>
                                <div class="overflow-hidden rounded-2xl bg-gray-100">
                                    <img
                                        v-if="office.image_urls?.length > 2"
                                        :src="office.image_urls[2]"
                                        :alt="office.title"
                                        class="h-24 w-full object-cover"
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl bg-white p-6 shadow-sm">
                            <h3 class="text-lg font-semibold text-gray-900">About this office</h3>
                            <p class="mt-3 text-sm leading-relaxed text-gray-600">{{ office.description }}</p>
                            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                <div class="rounded-xl border border-gray-100 p-3 text-sm text-gray-600">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Capacity</p>
                                    <p class="mt-1 text-sm font-semibold text-gray-900">Up to {{ office.capacity }} people</p>
                                </div>
                                <div class="rounded-xl border border-gray-100 p-3 text-sm text-gray-600">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Desks</p>
                                    <p class="mt-1 text-sm font-semibold text-gray-900">{{ office.desks ?? 'Flexible' }}</p>
                                </div>
                                <div class="rounded-xl border border-gray-100 p-3 text-sm text-gray-600">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Meeting rooms</p>
                                    <p class="mt-1 text-sm font-semibold text-gray-900">{{ office.meeting_rooms ?? 0 }}</p>
                                </div>
                                <div class="rounded-xl border border-gray-100 p-3 text-sm text-gray-600">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Timezone</p>
                                    <p class="mt-1 text-sm font-semibold text-gray-900">{{ office.timezone || 'Local' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl bg-white p-6 shadow-sm">
                            <h3 class="text-lg font-semibold text-gray-900">Amenities</h3>
                            <div class="mt-3 flex flex-wrap gap-2">
                                <span
                                    v-for="amenity in office.amenities"
                                    :key="amenity"
                                    class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600"
                                >
                                    {{ amenity }}
                                </span>
                            </div>
                        </div>

                        <div class="rounded-2xl bg-white p-6 shadow-sm">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900">Reviews</h3>
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl font-semibold text-gray-900">{{ office.averageRating.toFixed(1) }}</span>
                                    <span class="text-xl text-yellow-400">{{ renderStars(office.averageRating) }}</span>
                                    <span class="text-sm text-gray-500">({{ office.reviewCount }})</span>
                                </div>
                            </div>

                            <div v-if="page.props.auth.user" class="mt-6 border-t border-gray-200 pt-6">
                                <h4 class="font-semibold text-gray-900">{{ userReview ? 'Edit your review' : 'Leave a review' }}</h4>
                                <form class="mt-4 space-y-3" @submit.prevent="submitReview">
                                    <div>
                                        <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Rating</label>
                                        <div class="mt-2 flex gap-2">
                                            <button
                                                v-for="n in 5"
                                                :key="n"
                                                type="button"
                                                class="text-3xl transition"
                                                :class="n <= reviewForm.rating ? 'text-yellow-400' : 'text-gray-300'"
                                                @click="reviewForm.rating = n"
                                            >
                                                ★
                                            </button>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Comment</label>
                                        <textarea
                                            v-model="reviewForm.comment"
                                            rows="3"
                                            placeholder="Share your experience..."
                                            class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm"
                                        />
                                    </div>
                                    <button
                                        type="submit"
                                        class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white"
                                        :disabled="reviewForm.processing"
                                    >
                                        {{ userReview ? 'Update review' : 'Post review' }}
                                    </button>
                                </form>
                            </div>
                            <div v-else class="mt-6 border-t border-gray-200 pt-6 text-center text-sm text-gray-500">
                                <Link :href="route('login')" class="font-semibold text-indigo-600 hover:underline">Sign in</Link>
                                to leave a review
                            </div>

                            <div v-if="office.reviews?.length" class="mt-6 space-y-4 border-t border-gray-200 pt-6">
                                <div
                                    v-for="review in office.reviews"
                                    :key="review.id"
                                    class="rounded-xl border border-gray-100 p-4"
                                >
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ review.user.name }}</p>
                                            <p class="text-sm text-yellow-400">{{ renderStars(review.rating) }}</p>
                                            <p class="mt-1 text-xs text-gray-500">{{ review.created_at }}</p>
                                        </div>
                                        <button
                                            v-if="page.props.auth.user?.id === review.user.id || page.props.auth.user?.role === 'super_admin'"
                                            type="button"
                                            class="text-xs font-semibold text-red-600 hover:text-red-800"
                                            @click="deleteReview(review.id)"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                    <p v-if="review.comment" class="mt-2 text-sm text-gray-600">{{ review.comment }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <aside class="space-y-4">
                        <div class="rounded-2xl bg-white p-6 shadow-sm">
                            <h3 class="text-lg font-semibold text-gray-900">Request to book</h3>
                            <p class="mt-2 text-sm text-gray-500">Send a request and pay once approved.</p>

                            <div v-if="!page.props.auth.user" class="mt-4">
                                <Link
                                    class="block rounded-xl bg-indigo-600 px-4 py-2.5 text-center text-sm font-semibold text-white"
                                    :href="route('login')"
                                >
                                    Sign in to request
                                </Link>
                            </div>

                            <form v-else class="mt-4 space-y-3" @submit.prevent="submitBooking">
                                <div>
                                    <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Start date</label>
                                    <input v-model="form.start_date" type="date" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
                                    <p v-if="form.errors.start_date" class="mt-1 text-xs text-red-600">{{ form.errors.start_date }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">End date</label>
                                    <input v-model="form.end_date" type="date" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
                                    <p v-if="form.errors.end_date" class="mt-1 text-xs text-red-600">{{ form.errors.end_date }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Guests</label>
                                    <input v-model="form.guests" type="number" min="1" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
                                </div>
                                <div>
                                    <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Notes</label>
                                    <textarea v-model="form.notes" rows="3" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
                                </div>
                                <button type="submit" class="w-full rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white" :disabled="form.processing">
                                    Request booking
                                </button>
                            </form>
                        </div>

                        <div class="rounded-2xl bg-white p-6 shadow-sm">
                            <h3 class="text-lg font-semibold text-gray-900">Host</h3>
                            <p class="mt-2 text-sm text-gray-500">Managed by {{ office.owner?.name || 'OfficeBnB team' }}.</p>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </component>
</template>
