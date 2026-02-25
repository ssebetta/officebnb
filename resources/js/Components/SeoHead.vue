<script setup>
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        required: true,
    },
    image: {
        type: String,
        default: () => '',
    },
    url: {
        type: String,
        default: () => '',
    },
    type: {
        type: String,
        default: 'website',
    },
});

const fullTitle = computed(() => `${props.title} | OfficeBnB`);
</script>

<template>
    <Head>
        <title>{{ fullTitle }}</title>
        <meta name="description" :content="description" />
        <meta name="og:title" :content="fullTitle" />
        <meta name="og:description" :content="description" />
        <meta name="og:type" :content="type" />
        <meta v-if="image" name="og:image" :content="image" />
        <meta v-if="url" name="og:url" :content="url" />
        <meta name="twitter:title" :content="fullTitle" />
        <meta name="twitter:description" :content="description" />
        <meta v-if="image" name="twitter:image" :content="image" />
    </Head>
    <slot />
</template>
