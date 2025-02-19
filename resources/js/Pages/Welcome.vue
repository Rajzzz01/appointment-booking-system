<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});
</script>

<template>
    <Head title="Appointment Booking System" />

    <div class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-white min-h-screen flex flex-col items-center justify-center">
        <!-- Hero Section -->
        <div class="text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4 text-blue-600 dark:text-blue-400">
                Appointment Booking System
            </h1>
            <p class="text-lg text-gray-700 dark:text-gray-300 mb-6">
                Book and manage appointments effortlessly.
            </p>

            <!-- Buttons -->
            <div class="flex justify-center space-x-4">
                <Link v-if="$page.props.auth.user"
                    :href="route('dashboard')"
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition">
                    Go to Dashboard
                </Link>
                <Link v-else
                    :href="route('login')"
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition">
                    Get Started
                </Link>
            </div>
        </div>

        <!-- Footer -->
        <footer class="absolute bottom-5 text-sm text-gray-600 dark:text-gray-400">
            Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }})
        </footer>
    </div>
</template>
