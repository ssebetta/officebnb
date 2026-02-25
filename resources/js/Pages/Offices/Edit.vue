<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    office: Object,
    amenityOptions: Array,
    countryOptions: Array,
    officeTypeOptions: Array,
    timezoneOptions: Array,
});

const form = useForm({
    title: props.office.title,
    city: props.office.city,
    country: props.office.country,
    address: props.office.address,
    description: props.office.description,
    workspace_type: props.office.workspace_type,
    size_sqft: props.office.size_sqft,
    meeting_rooms: props.office.meeting_rooms,
    desks: props.office.desks,
    timezone: props.office.timezone,
    capacity: props.office.capacity,
    daily_rate: props.office.daily_rate,
    amenities: props.office.amenities || [],
    image_urls: props.office.image_urls?.length ? [...props.office.image_urls] : ['', '', ''],
    image_files: [],
    is_active: props.office.is_active,
});

const addImageField = () => {
    if (form.image_urls.length < 5) {
        form.image_urls.push('');
    }
};

const handleFileUpload = (event) => {
    const files = Array.from(event.target.files);
    const totalImages = form.image_urls.filter(url => url.trim()).length + form.image_files.length + files.length;
    
    if (totalImages > 5) {
        alert('Maximum 5 images allowed');
        event.target.value = '';
        return;
    }
    
    form.image_files = [...form.image_files, ...files].slice(0, 5 - form.image_urls.filter(url => url.trim()).length);
};

const removeFile = (index) => {
    form.image_files.splice(index, 1);
};

const submit = () => {
    const data = form.data();
    
    // Clean up numeric fields - convert empty strings to null
    if (!data.size_sqft || data.size_sqft === '') {
        data.size_sqft = null;
    } else {
        data.size_sqft = parseInt(data.size_sqft);
    }
    
    if (!data.meeting_rooms || data.meeting_rooms === '') {
        data.meeting_rooms = null;
    } else {
        data.meeting_rooms = parseInt(data.meeting_rooms);
    }
    
    if (!data.desks || data.desks === '') {
        data.desks = null;
    } else {
        data.desks = parseInt(data.desks);
    }
    
    // Convert capacity to integer
    data.capacity = parseInt(data.capacity) || 1;
    
    // Convert daily_rate to float
    data.daily_rate = parseFloat(data.daily_rate) || 0;
    
    // Filter out empty image URLs
    data.image_urls = data.image_urls.filter(url => url && url.trim());
    
    form.transform(() => data).put(route('listings.update', props.office.slug), {
        onError: (errors) => {
            console.error('Validation errors:', errors);
        },
    });
};
</script>

<template>
    <AppLayout title="Edit listing">
        <template #header>
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Edit listing</h2>
                <p class="text-sm text-gray-500">Keep your space details accurate and up to date.</p>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <form class="space-y-6" @submit.prevent="submit">
                    <div class="rounded-2xl bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900">Basics</h3>
                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Title</label>
                                <input v-model="form.title" type="text" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
                                <p v-if="form.errors.title" class="mt-1 text-xs text-red-600">{{ form.errors.title }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Workspace type</label>
                                <select v-model="form.workspace_type" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm">
                                    <option value="">Select a type</option>
                                    <option v-for="type in props.officeTypeOptions" :key="type" :value="type">{{ type }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">City</label>
                                <input v-model="form.city" type="text" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
                                <p v-if="form.errors.city" class="mt-1 text-xs text-red-600">{{ form.errors.city }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Country</label>
                                <select v-model="form.country" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm">
                                    <option value="">Select a country</option>
                                    <option v-for="country in props.countryOptions" :key="country" :value="country">{{ country }}</option>
                                </select>
                                <p v-if="form.errors.country" class="mt-1 text-xs text-red-600">{{ form.errors.country }}</p>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Address</label>
                                <input v-model="form.address" type="text" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900">Details</h3>
                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Size (sq ft)</label>
                                <input v-model="form.size_sqft" type="number" min="100" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Timezone</label>
                                <select v-model="form.timezone" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm">
                                    <option value="">Select a timezone</option>
                                    <option v-for="tz in props.timezoneOptions" :key="tz" :value="tz">{{ tz }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Desks</label>
                                <input v-model="form.desks" type="number" min="0" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Meeting rooms</label>
                                <input v-model="form.meeting_rooms" type="number" min="0" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Capacity</label>
                                <input v-model="form.capacity" type="number" min="1" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
                                <p v-if="form.errors.capacity" class="mt-1 text-xs text-red-600">{{ form.errors.capacity }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Daily rate (USD)</label>
                                <input v-model="form.daily_rate" type="number" min="20" step="0.01" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
                                <p v-if="form.errors.daily_rate" class="mt-1 text-xs text-red-600">{{ form.errors.daily_rate }}</p>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Description</label>
                                <textarea v-model="form.description" rows="4" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900">Amenities</h3>
                        <div class="mt-4 grid gap-3 sm:grid-cols-2">
                            <label
                                v-for="amenity in props.amenityOptions"
                                :key="amenity"
                                class="flex items-center gap-2 rounded-xl border border-gray-100 px-3 py-2 text-sm text-gray-700"
                            >
                                <input v-model="form.amenities" :value="amenity" type="checkbox" class="rounded border-gray-300 text-indigo-600" />
                                {{ amenity }}
                            </label>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900">Images</h3>
                        <p class="mt-1 text-xs text-gray-500">Add up to 5 images (URLs or file uploads)</p>
                        
                        <div class="mt-4 space-y-3">
                            <div v-for="(url, index) in form.image_urls" :key="'url-' + index">
                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Image URL {{ index + 1 }}</label>
                                <input v-model="form.image_urls[index]" type="text" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" placeholder="https://example.com/image.jpg" />
                            </div>
                            <button v-if="form.image_urls.length < 5" type="button" class="text-sm font-semibold text-indigo-600" @click="addImageField">Add URL field</button>
                        </div>
                        
                        <div class="mt-6">
                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">Or upload new images</label>
                            <input 
                                type="file" 
                                accept="image/*" 
                                multiple 
                                class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" 
                                @change="handleFileUpload"
                            />
                            <p class="mt-1 text-xs text-gray-500">Max 5MB per image</p>
                        </div>
                        
                        <div v-if="form.image_files.length" class="mt-4 space-y-2">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Selected files:</p>
                            <div v-for="(file, index) in form.image_files" :key="'file-' + index" class="flex items-center justify-between rounded-lg border border-gray-200 px-3 py-2">
                                <span class="text-sm text-gray-700">{{ file.name }}</span>
                                <button type="button" class="text-xs font-semibold text-red-600" @click="removeFile(index)">Remove</button>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white" :disabled="form.processing">
                            Save changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
