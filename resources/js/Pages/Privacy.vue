<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import Footer from '@/Components/Footer.vue';

defineOptions({
    layout: false,
});

const jsonLd = {
    '@context': 'https://schema.org',
    '@type': 'WebPage',
    'name': 'Privacy Policy - OfficeBnB',
    'description': 'Read OfficeBnB privacy policy to understand how we collect, use, and protect your personal data.',
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
        <Head title="Privacy Policy - OfficeBnB">
            <meta name="description" content="OfficeBnB Privacy Policy - Learn how we collect, use, and protect your personal data and privacy." />
            <meta name="og:title" content="Privacy Policy - OfficeBnB" />
            <meta name="og:description" content="Read our comprehensive privacy policy to understand data protection practices." />
            <meta name="robots" content="index, follow" />
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
                    <h1>Privacy Policy</h1>
                    <p class="text-gray-600"><strong>Last updated:</strong> February 25, 2026</p>

                    <h2>Introduction</h2>
                    <p>OfficeBnB ("we", "our", or "us") operates the OfficeBnB website and platform. This page informs you of our policies regarding the collection, use, and disclosure of personal data when you use our service and the choices you have associated with that data.</p>

                    <h2>Information Collection and Use</h2>
                    <p>We collect several different types of information for various purposes to provide and improve our service to you.</p>

                    <h3>Types of Data Collected:</h3>
                    <ul>
                        <li><strong>Personal Data:</strong> While using our service, we may ask you to provide us with certain personally identifiable information that can be used to contact or identify you ("Personal Data"). This may include, but is not limited to:
                            <ul>
                                <li>Email address</li>
                                <li>First name and last name</li>
                                <li>Phone number</li>
                                <li>Address, State, Province, ZIP/Postal code, City</li>
                                <li>Cookies and Usage Data</li>
                            </ul>
                        </li>
                        <li><strong>Usage Data:</strong> We may also collect information on how the service is accessed and used ("Usage Data"). This may include information such as your computer's Internet Protocol address, browser type, browser version, the pages you visit, the time and date of your visit, the time spent on those pages, and other diagnostic data.</li>
                    </ul>

                    <h2>Use of Data</h2>
                    <p>OfficeBnB uses the collected data for various purposes:</p>
                    <ul>
                        <li>To provide and maintain our service</li>
                        <li>To notify you about changes to our service</li>
                        <li>To provide customer support</li>
                        <li>To gather analysis or valuable information so that we can improve our service</li>
                        <li>To monitor the usage of our service</li>
                        <li>To detect, prevent and address technical issues</li>
                    </ul>

                    <h2>Security of Data</h2>
                    <p>The security of your data is important to us, but remember that no method of transmission over the Internet or method of electronic storage is 100% secure. While we strive to use commercially acceptable means to protect your Personal Data, we cannot guarantee its absolute security.</p>

                    <h2>Changes to This Privacy Policy</h2>
                    <p>We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "effective date" at the top of this Privacy Policy.</p>

                    <h2>Contact Us</h2>
                    <p>If you have any questions about this Privacy Policy, please contact us at <a href="mailto:privacy@systimo.org">privacy@systimo.org</a></p>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <Footer />
    </div>
</template>
