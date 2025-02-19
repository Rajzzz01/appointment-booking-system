<script setup>
import { ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

const form = ref({
    title: '',
    description: '',
    datetime: '',
    guests: [],
    reminder_time: 30,
    timezone: userTimezone
});

const newGuest = ref('');
const error = ref('');
const success = ref(false);
const loading = ref(false);

const getMinDateTime = () => {
    const now = new Date();
    return new Date(now.getTime() - now.getTimezoneOffset() * 60000)
        .toISOString()
        .slice(0, 16);
};
const minDateTime = getMinDateTime();

const validateDateTime = () => {
    const date = new Date(form.value.datetime);
    const day = date.getDay();

    if (day === 0 || day === 6) {
        error.value = 'Appointments can only be booked on weekdays (Monday to Friday).';
        form.value.datetime = '';
        return false;
    }

    error.value = '';
    return true;
};

const validateEmail = (email) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);

const addGuest = () => {
    if (!newGuest.value) return;

    if (!validateEmail(newGuest.value)) {
        error.value = 'Please enter a valid email address.';
        return;
    }

    if (!form.value.guests.includes(newGuest.value)) {
        form.value.guests.push(newGuest.value);
        newGuest.value = '';
        error.value = '';
    }
};


const removeGuest = (email) => {
    form.value.guests = form.value.guests.filter(guest => guest !== email);
};

const handleSubmit = async () => {
    error.value = '';

    if (!validateDateTime()) {
        return;
    }

    loading.value = true;

    try {

        const appointmentData = {
            title: form.value.title,
            description: form.value.description,
            date_time: form.value.datetime,
            guests: form.value.guests,
            reminder_time: form.value.reminder_time,
            timezone: form.value.timezone
        };

        console.log("===> Sending appointment data:", appointmentData);
        await router.post('/appointment-booking', appointmentData);

        success.value = true;
        setTimeout(() => {
            success.value = false;
        }, 3000);


        form.value = {
            title: '',
            description: '',
            datetime: '',
            guests: [],
            reminder_time: 30,
            timezone: userTimezone
        };
    } catch (err) {
        console.error("Error booking appointment:", err);
        error.value = err.response?.data?.message || 'Failed to book appointment. Please try again.';
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <Head title="Book Appointment" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Book an Appointment
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow-md">
                <!-- Back Button -->
                <div class="mb-4">
                    <Link :href="route('appointments.index')" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                        ← Back to Appointments
                    </Link>
                </div>

                <h2 class="text-2xl font-bold mb-6">Book an Appointment</h2>

                <form @submit.prevent="handleSubmit" class="space-y-6">
                    <!-- Title -->
                    <div>
                        <label for="title" class="block font-medium">Title</label>
                        <input id="title" v-model="form.title" type="text"
                            class="w-full p-2 border rounded-md focus:ring focus:ring-blue-300"
                            placeholder="Enter appointment title" required />
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block font-medium">Description</label>
                        <textarea id="description" v-model="form.description"
                            class="w-full p-2 border rounded-md min-h-[100px] focus:ring focus:ring-blue-300"
                            placeholder="Enter appointment description" required></textarea>
                    </div>

                    <!-- Date & Time -->
                    <div>
                        <label for="datetime" class="block font-medium">Date & Time</label>
                        <input id="datetime" v-model="form.datetime" type="datetime-local"
                            class="w-full p-2 border rounded-md focus:ring focus:ring-blue-300"
                            :min="minDateTime" required @change="validateDateTime" />
                        <p class="text-sm text-gray-600">
                            Appointments available Monday-Friday only. Times are shown in your local timezone.
                        </p>
                    </div>

                    <!-- Reminder Time -->
                    <div>
                        <label for="reminder" class="block font-medium">Reminder Time</label>
                        <select id="reminder" v-model="form.reminder_time"
                            class="w-full p-2 border rounded-md focus:ring focus:ring-blue-300">
                            <option :value="5">5 Minutes Before</option>
                            <option :value="10">10 Minutes Before</option>
                            <option :value="15">15 Minutes Before</option>
                            <option :value="30">30 Minutes Before (Default)</option>
                            <option :value="60">1 Hour Before</option>
                        </select>
                    </div>

                    <!-- Guest Invitations -->
                    <div>
                        <label class="block font-medium">Invite Guests</label>
                        <div class="flex gap-2">
                            <input v-model="newGuest" type="email"
                                class="flex-1 p-2 border rounded-md focus:ring focus:ring-blue-300"
                                placeholder="Enter guest email" />
                            <button type="button" @click="addGuest"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Add Guest
                            </button>
                        </div>

                        <!-- Guest List -->
                        <div v-if="form.guests.length" class="mt-4">
                            <h3 class="font-medium mb-2">Guest List:</h3>
                            <div class="flex flex-wrap gap-2">
                                <div v-for="guest in form.guests" :key="guest"
                                    class="flex items-center gap-2 bg-gray-100 px-3 py-1 rounded-md">
                                    <span>{{ guest }}</span>
                                    <button type="button" @click="removeGuest(guest)"
                                        class="text-gray-500 hover:text-gray-700">
                                        ×
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div v-if="error" class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-md">
                        {{ error }}
                    </div>

                    <!-- Success Message -->
                    <div v-if="success" class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-md">
                        Appointment booked successfully! Email notifications have been sent.
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full py-2 px-4 bg-blue-600 text-white rounded-md hover:bg-blue-700 flex justify-center items-center"
                        :disabled="loading">
                        <span v-if="loading" class="animate-spin border-2 border-white border-t-transparent rounded-full w-5 h-5 mr-2"></span>
                        {{ loading ? 'Booking...' : 'Book Appointment' }}
                    </button>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
