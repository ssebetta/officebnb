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
    'name': 'Terms of Service - OfficeBnB',
    'description': 'OfficeBnB Terms of Service - Legal agreement governing the use of our platform.',
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
        <Head title="Terms of Service - OfficeBnB">
            <meta name="description" content="OfficeBnB Terms of Service - Legal agreement and conditions for using our office rental platform." />
            <meta name="og:title" content="Terms of Service - OfficeBnB" />
            <meta name="og:description" content="Read the legal terms and conditions governing the use of OfficeBnB platform." />
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
                    <h1>Terms of Service</h1>
                    <p class="text-gray-600"><strong>Last updated:</strong> February 25, 2026</p>

                    <h2>1. Agreement to Terms</h2>
                    <p>By accessing and using the OfficeBnB website and platform, you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to abide by the above, please do not use this service.</p>

                    <h2>2. Use License</h2>
                    <p>Permission is granted to temporarily download one copy of the materials (information or software) on OfficeBnB's website for personal, non-commercial transitory viewing only. This is the grant of a license, not a transfer of title, and under this license you may not:</p>
                    <ul>
                        <li>Modifying or copying the materials</li>
                        <li>Using the materials for any commercial purpose or for any public display</li>
                        <li>Attempting to decompile, reverse engineer, or disassemble any software contained on the website</li>
                        <li>Removing any copyright or other proprietary notations from the materials</li>
                        <li>Transferring the materials to another person or "mirroring" the materials on any other server</li>
                    </ul>

                    <h2>3. Disclaimer</h2>
                    <p>The materials on OfficeBnB's website are provided on an 'as is' basis. OfficeBnB makes no warranties, expressed or implied, and hereby disclaims and negates all other warranties including, without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights.</p>

                    <h2>4. Limitations</h2>
                    <p>In no event shall OfficeBnB or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption) arising out of the use or inability to use the materials on OfficeBnB's website.</p>

                    <h2>5. Accuracy of Materials</h2>
                    <p>The materials appearing on OfficeBnB's website could include technical, typographical, or photographic errors. OfficeBnB does not warrant that any of the materials on its website are accurate, complete, or current. OfficeBnB may make changes to the materials contained on its website at any time without notice.</p>

                    <h2>6. Links</h2>
                    <p>OfficeBnB has not reviewed all of the sites linked to its website and is not responsible for the contents of any other site. The inclusion of any link does not imply endorsement by OfficeBnB of the site. Use of any such linked website is at the user's own risk.</p>

                    <h2>7. Modifications</h2>
                    <p>OfficeBnB may revise these terms of service for its website at any time without notice. By using this website, you are agreeing to be bound by the then current version of these terms of service.</p>

                    <h2>8. Governing Law</h2>
                    <p>These terms and conditions are governed by and construed in accordance with the laws of the jurisdiction in which OfficeBnB operates, and you irrevocably submit to the exclusive jurisdiction of the courts in that location.</p>

                    <h2>9. Contact Information</h2>
                    <p>If you have questions about these Terms of Service, please contact us at <a href="mailto:legal@systimo.org">legal@systimo.org</a></p>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <Footer />
    </div>
</template>
