<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import Footer from '@/Components/Footer.vue';

defineOptions({
    layout: false,
});

const faqs = [
    {
        question: "How does OfficeBnB work?",
        answer: "OfficeBnB connects businesses with flexible office spaces. Browse available offices, select your dates, and book instantly. Office owners list their spaces, manage bookings, and receive payments securely through our platform."
    },
    {
        question: "What types of office spaces are available?",
        answer: "We offer various workspace types including private offices, shared workspaces, coworking spaces, meeting rooms, and entire office floors. Each listing specifies capacity, amenities, and workspace type."
    },
    {
        question: "How do I book an office space?",
        answer: "Simply browse our office listings, select the space that fits your needs, choose your dates, and complete the booking. You'll receive instant confirmation and can manage your booking from your dashboard."
    },
    {
        question: "What is your cancellation policy?",
        answer: "Cancellation policies vary by office owner. Most offer flexible cancellation up to 24-48 hours before the booking date. Check the specific cancellation policy on each office listing before booking."
    },
    {
        question: "How much does it cost to rent an office space?",
        answer: "Pricing varies by location, size, and amenities. Rates start from as low as $20/day for shared workspaces to several hundred dollars per day for private office suites. All prices are clearly displayed on each listing."
    },
    {
        question: "Can I book office space for just one day?",
        answer: "Yes! OfficeBnB specializes in flexible bookings. You can rent office space by the day, week, or month depending on your needs and the availability of the specific office."
    },
    {
        question: "What amenities are typically included?",
        answer: "Common amenities include high-speed WiFi, furniture, air conditioning, printing facilities, kitchen access, and meeting rooms. Each listing details the specific amenities available at that location."
    },
    {
        question: "How do I list my office space on OfficeBnB?",
        answer: "Sign up for a free account, upgrade to a landlord account, and create your office listing. Add photos, describe your space, set your pricing, and start receiving booking requests. We handle payments and provide calendar management tools."
    },
    {
        question: "Is my payment secure?",
        answer: "Yes, all payments are processed through secure payment gateways (Stripe). We never store your full payment details, and all transactions are encrypted and PCI-compliant."
    },
    {
        question: "Can I view the office before booking?",
        answer: "While instant booking is available, we encourage reaching out to office owners for virtual tours or in-person viewings when possible. Contact information is available on each listing."
    },
    {
        question: "What happens if there's an issue with my booking?",
        answer: "Our support team is available to help resolve any issues. Contact us at support@systimo.org and we'll work with you and the office owner to find a solution."
    },
    {
        question: "Are utilities included in the price?",
        answer: "Most office spaces include utilities (electricity, water, internet) in the daily rate. However, some may have additional charges. Check the listing details for specifics."
    },
    {
        question: "Can I bring my team to a shared workspace?",
        answer: "Yes! Many listings accommodate teams. Check the capacity listed for each space, and ensure you book for the correct number of people."
    },
    {
        question: "How do reviews work?",
        answer: "After your booking ends, you can leave a review rating the office space, amenities, and host. Reviews help other users make informed decisions and maintain quality standards on our platform."
    },
    {
        question: "Do you offer long-term rentals?",
        answer: "Yes, many office owners offer discounted rates for weekly or monthly bookings. Contact the office owner directly or check their listing for long-term pricing options."
    }
];

const faqSchema = {
    '@context': 'https://schema.org',
    '@type': 'FAQPage',
    'mainEntity': faqs.map(faq => ({
        '@type': 'Question',
        'name': faq.question,
        'acceptedAnswer': {
            '@type': 'Answer',
            'text': faq.answer
        }
    }))
};

onMounted(() => {
    const script = document.createElement('script');
    script.type = 'application/ld+json';
    script.textContent = JSON.stringify(faqSchema);
    document.head.appendChild(script);
});
</script>

<template>
    <div class="min-h-screen bg-slate-50 flex flex-col">
        <Head title="Frequently Asked Questions (FAQ) - OfficeBnB">
            <meta name="description" content="Find answers to common questions about booking office spaces, pricing, amenities, cancellation policies, and more on OfficeBnB." />
            <meta name="og:title" content="FAQ - OfficeBnB" />
            <meta name="og:description" content="Get answers to frequently asked questions about flexible office space rentals on OfficeBnB" />
            <meta name="keywords" content="office rental FAQ, workspace questions, booking help, office space pricing, coworking FAQ" />
            <link rel="canonical" href="https://officebnb.systimo.org/faq" />
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
                <div class="text-center mb-12">
                    <h1 class="text-4xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h1>
                    <p class="text-lg text-gray-600">Find answers to common questions about OfficeBnB</p>
                </div>

                <div class="space-y-6">
                    <div 
                        v-for="(faq, index) in faqs" 
                        :key="index"
                        class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden"
                    >
                        <details class="group">
                            <summary class="flex justify-between items-center cursor-pointer p-6 hover:bg-gray-50 transition">
                                <h2 class="text-lg font-semibold text-gray-900 pr-4">{{ faq.question }}</h2>
                                <svg 
                                    class="w-5 h-5 text-gray-500 transition-transform group-open:rotate-180" 
                                    fill="none" 
                                    stroke="currentColor" 
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </summary>
                            <div class="px-6 pb-6 pt-2 text-gray-600 border-t border-gray-100">
                                {{ faq.answer }}
                            </div>
                        </details>
                    </div>
                </div>

                <!-- Additional Help Section -->
                <div class="mt-16 bg-blue-50 rounded-lg p-8 text-center">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Still have questions?</h2>
                    <p class="text-gray-600 mb-6">Can't find the answer you're looking for? Our support team is here to help.</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <Link 
                            :href="route('contact')" 
                            class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition"
                        >
                            Contact Support
                        </Link>
                        <a 
                            href="mailto:support@systimo.org" 
                            class="inline-flex items-center justify-center px-6 py-3 bg-white text-blue-600 font-semibold rounded-lg border-2 border-blue-600 hover:bg-blue-50 transition"
                        >
                            Email Us
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <Link :href="route('offices.index')" class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition text-center">
                        <h3 class="font-semibold text-gray-900 mb-2">Browse Offices</h3>
                        <p class="text-sm text-gray-600">Explore available workspaces</p>
                    </Link>
                    <Link :href="route('privacy')" class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition text-center">
                        <h3 class="font-semibold text-gray-900 mb-2">Privacy Policy</h3>
                        <p class="text-sm text-gray-600">Learn about data protection</p>
                    </Link>
                    <Link :href="route('terms')" class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition text-center">
                        <h3 class="font-semibold text-gray-900 mb-2">Terms of Service</h3>
                        <p class="text-sm text-gray-600">Read our terms and conditions</p>
                    </Link>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <Footer />
    </div>
</template>
