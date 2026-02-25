<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    amenityOptions: Array,
    countryOptions: Array,
    officeTypeOptions: Array,
    timezoneOptions: Array,
});

const form = useForm({
    title: '',
    city: '',
    country: '',
    address: '',
    description: '',
    workspace_type: '',
    size_sqft: '',
    meeting_rooms: '',
    desks: '',
    timezone: '',
    capacity: '',
    daily_rate: '',
    amenities: [],
    image_urls: [''],
    owner_email: '',
});

const addImageUrl = () => {
    form.image_urls.push('');
};

const removeImageUrl = (index) => {
    form.image_urls.splice(index, 1);
};

const submit = () => {
    form.post(route('super-admin.offices.store'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout title="Create office listing for user">
        <template #header>
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Create office for user</h2>
                <p class="text-sm text-gray-500">Add a new office and assign an owner</p>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <form class="space-y-6" @submit.prevent="submit">
                    <div class="rounded-2xl bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900">Owner information</h3>
                        <div class="mt-4 space-y-4">
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Owner email *</label>
                                <input v-model="form.owner_email" type="email" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" required />
                                <p class="mt-1 text-xs text-gray-500">If user doesn't exist, they'll receive an invitation to register</p>
                                <p v-if="form.errors.owner_email" class="mt-1 text-xs text-red-600">{{ form.errors.owner_email }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900">Basic information</h3>
                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Title *</label>
                                <input v-model="form.title" type="text" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" required />
                                <p v-if="form.errors.title" class="mt-1 text-xs text-red-600">{{ form.errors.title }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">City *</label>
                                <input v-model="form.city" type="text" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" required />
                                <p v-if="form.errors.city" class="mt-1 text-xs text-red-600">{{ form.errors.city }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Country *</label>
                                <select v-model="form.country" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" required>
                                    <option value="">Select a country</option>
                                    <option v-for="country in props.countryOptions" :key="country" :value="country">{{ country }}</option>
                                </select>
                                <p v-if="form.errors.country" class="mt-1 text-xs text-red-600">{{ form.errors.country }}</p>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Address</label>
                                <input v-model="form.address" type="text" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
                            </div>
                            <div class="sm:col-span-2">
                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Description</label>
                                <textarea v-model="form.description" rows="4" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900">Details</h3>
                        <div class="mt-4 grid gap-4 sm:grid-cols-3">
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Capacity *</label>
                                <input v-model="form.capacity" type="number" min="1" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" required />
                                <p v-if="form.errors.capacity" class="mt-1 text-xs text-red-600">{{ form.errors.capacity }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Daily rate ($) *</label>
                                <input v-model="form.daily_rate" type="number" min="20" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" required />
                                <p v-if="form.errors.daily_rate" class="mt-1 text-xs text-red-600">{{ form.errors.daily_rate }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Workspace type</label>
                                <select v-model="form.workspace_type" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm">
                                    <option value="">Select a type</option>
                                    <option v-for="type in props.officeTypeOptions" :key="type" :value="type">{{ type }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Size (sqft)</label>
                                <input v-model="form.size_sqft" type="number" min="100" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Desks</label>
                                <input v-model="form.desks" type="number" min="0" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Meeting rooms</label>
                                <input v-model="form.meeting_rooms" type="number" min="0" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
                            </div>
                            <div class="sm:col-span-3">
                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Timezone</label>
                                <select v-model="form.timezone" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm">
                                    <option value="">Select a timezone</option>
                                    <option v-for="tz in props.timezoneOptions" :key="tz" :value="tz">{{ tz }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900">Amenities</h3>
                        <div class="mt-4 grid gap-3 sm:grid-cols-2">
                            <label v-for="amenity in props.amenityOptions" :key="amenity" class="flex items-center gap-2">
                                <input v-model="form.amenities" type="checkbox" :value="amenity" class="rounded border-gray-300" />
                                <span class="text-sm text-gray-700">{{ amenity }}</span>
                            </label>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900">Images</h3>
                        <div class="mt-4 space-y-3">
                            <div v-for="(url, index) in form.image_urls" :key="index" class="flex items-center gap-2">
                                <input
                                    v-model="form.image_urls[index]"
                                    type="url"
                                    placeholder="https://example.com/image.jpg"
                                    class="flex-1 rounded-xl border border-gray-200 px-3 py-2 text-sm"
                                />
                                <button v-if="form.image_urls.length > 1" type="button" class="rounded-lg border border-red-100 px-3 py-2 text-xs font-semibold text-red-600" @click="removeImageUrl(index)">
                                    Remove
                                </button>
                            </div>
                            <button type="button" class="rounded-lg border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-600" @click="addImageUrl">
                                + Add image
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-600" @click="$inertia.visit(route('super-admin.offices'))">
                            Cancel
                        </button>
                        <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white" :disabled="form.processing">
                            Create listing
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
