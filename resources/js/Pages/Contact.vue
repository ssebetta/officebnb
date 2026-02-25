<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Footer from '@/Components/Footer.vue';

defineOptions({
    layout: false,
});

const jsonLd = {
    '@context': 'https://schema.org',
    '@type': 'ContactPage',
    'name': 'Contact OfficeBnB',
    'description': 'Get in touch with OfficeBnB for inquiries, support, and partnerships.',
    'url': window.location.href,
};

onMounted(() => {
    const script = document.createElement('script');
    script.type = 'application/ld+json';
    script.textContent = JSON.stringify(jsonLd);
    document.head.appendChild(script);
});
</script>

<template>
    <div class="min-h-screen bg-slate-50 flex flex-col">
        <Head title="Contact Us - OfficeBnB">
            <meta name="description" content="Contact OfficeBnB for general inquiries, support, or partnership opportunities. Email us at info@systimo.org or use our contact form." />
            <meta name="og:title" content="Contact OfficeBnB" />
            <meta name="og:description" content="Get in touch with OfficeBnB for inquiries, support, and partnerships." />
        </Head>

        <!-- Navigation -->
        <nav class="bg-white border-b border-gray-200 sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <Link :href="route('home')" class="flex items-center">
                        <img src="/web/icon-512.png" alt="OfficeBnB Logo" class="h-10 w-10 rounded-lg" />
                        <span class="ml-2 font-semibold text-gray-900">OfficeBnB</span>
                    </Link>
                    <div class="flex items-center gap-4">
                        <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="text-gray-700 hover:text-gray-900">Dashboard</Link>
                        <Link v-else :href="route('login')" class="text-gray-700 hover:text-gray-900">Login</Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Content -->
        <main class="flex-1">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="prose prose-sm sm:prose lg:prose-lg max-w-none">
                    <h1>Contact Us</h1>
                    <p>We'd love to hear from you. Get in touch with our team for any questions, feedback, or support.</p>

                    <h2>Email</h2>
                    <p>
                        For general inquiries: <a href="mailto:info@systimo.org">info@systimo.org</a>
                    </p>
                    <p>
                        For support: <a href="mailto:support@systimo.org">support@systimo.org</a>
                    </p>
                    <p>
                        For partnerships: <a href="mailto:partnerships@systimo.org">partnerships@systimo.org</a>
                    </p>

                    <h2>Contact Form</h2>
                    <form class="space-y-6 py-8">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-2">
                                    First Name
                                </label>
                                <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-2">
                                    Last Name
                                </label>
                                <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">
                                Email
                            </label>
                            <input type="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">
                                Subject
                            </label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">
                                Message
                            </label>
                            <textarea rows="6" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>

                        <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-2 rounded-lg hover:bg-blue-700 transition">
                            Send Message
                        </button>
                    </form>

                    <h2>Response Time</h2>
                    <p>We typically respond to emails within 24 hours during business days.</p>

                    <h2>Office Hours</h2>
                    <p>Our team is available Monday through Friday, 9 AM to 6 PM EAT.</p>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <Footer />
    </div>
</template>
