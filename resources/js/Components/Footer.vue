<template>
    <footer class="border-t border-gray-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 gap-8 sm:grid-cols-4">
                <!-- Company -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">Company</h3>
                    <ul class="mt-4 space-y-2">
                        <li>
                            <Link href="/" class="text-sm text-gray-600 hover:text-gray-900 transition">
                                Home
                            </Link>
                        </li>
                        <li>
                            <Link :href="route('offices.index')" class="text-sm text-gray-600 hover:text-gray-900 transition">
                                Explore
                            </Link>
                        </li>
                        <li>
                            <Link :href="route('contact')" class="text-sm text-gray-600 hover:text-gray-900 transition">
                                Contact
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- Product -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">Product</h3>
                    <ul class="mt-4 space-y-2">
                        <li>
                            <Link :href="route('dashboard')" class="text-sm text-gray-600 hover:text-gray-900 transition">
                                Dashboard
                            </Link>
                        </li>
                        <li>
                            <Link :href="route('bookings.index')" class="text-sm text-gray-600 hover:text-gray-900 transition">
                                My Bookings
                            </Link>
                        </li>
                        <li>
                            <a href="#" class="text-sm text-gray-600 hover:text-gray-900 transition">
                                Features
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Support -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">Support</h3>
                    <ul class="mt-4 space-y-2">
                        <li>
                            <a href="mailto:support@systimo.org" class="text-sm text-gray-600 hover:text-gray-900 transition">
                                Email Support
                            </a>
                        </li>
                        <li>
                            <Link :href="route('faq')" class="text-sm text-gray-600 hover:text-gray-900 transition">
                                FAQ
                            </Link>
                        </li>
                        <li>
                            <a href="#" class="text-sm text-gray-600 hover:text-gray-900 transition">
                                Documentation
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Legal -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">Legal</h3>
                    <ul class="mt-4 space-y-2">
                        <li>
                            <Link :href="route('privacy')" class="text-sm text-gray-600 hover:text-gray-900 transition">
                                Privacy Policy
                            </Link>
                        </li>
                        <li>
                            <Link :href="route('terms')" class="text-sm text-gray-600 hover:text-gray-900 transition">
                                Terms of Service
                            </Link>
                        </li>
                        <li>
                            <a href="#" class="text-sm text-gray-600 hover:text-gray-900 transition">
                                Cookies
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="mt-8 border-t border-gray-200 pt-8">
                <div class="flex items-center justify-between">
                    <OfficeLogo :dark="true" />
                    <p class="text-sm text-gray-500">
                        &copy; 2026 OfficeBnB. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </footer>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import OfficeLogo from '@/Components/OfficeLogo.vue';
</script>
