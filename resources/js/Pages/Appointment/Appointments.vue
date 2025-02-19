<script setup>
    import { ref } from 'vue';
    import { router, Link } from '@inertiajs/vue3';
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

    const props = defineProps({
        appointments: Array,
        sortBy: String
    });

    const selectedSort = ref(props.sortBy || 'created_at');
    const sortAppointments = () => {
        router.get(route('appointments.index'), { sort: selectedSort.value }, { preserveState: true });
    };

    const formatDate = (date, timezone) => {
        return new Date(date).toLocaleString('en-US', { timeZone: timezone });
    };

    const canCancel = (appointment) => {
        if (appointment.is_canceled) return false;

        const appointmentDate = new Date(appointment.date_time);
        const now = new Date();
        return (appointmentDate - now) > 30 * 60 * 1000;
    };

    const cancelAppointment = (id) => {
        if (confirm("Are you sure you want to cancel this appointment?")) {
            router.post(route('appointments.cancel', { id }), {}, { preserveState: true });
        }
    };
</script>
<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">My Appointments</h2>
        </template>

        <div class="py-12">
            <div class="max-w-6xl mx-auto p-6 bg-white shadow rounded-lg">

                <!-- Success Message -->
                <div v-if="$page.props.flash && $page.props.flash.success"
                    class="p-4 mb-4 bg-green-50 border border-green-200 text-green-700 rounded-md">
                    {{ $page.props.flash.success }}
                </div>

                <div class="mb-4 flex justify-between items-center">
                    <div class="flex gap-2">
                        <label class="font-medium">Sort By:</label>
                        <select v-model="selectedSort" @change="sortAppointments"
                            class="border px-3 py-2 rounded-md">
                            <option value="created_at">Created Date</option>
                            <option value="date_time">Upcoming</option>
                        </select>
                    </div>
                    <Link :href="route('appointment.form')" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        + Book Appointment
                    </Link>
                </div>

                <div v-if="appointments.length">
                    <table class="w-full border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="border p-2">Title</th>
                                <th class="border p-2">Description</th>
                                <th class="border p-2">Date & Time</th>
                                <th class="border p-2">Reminder Time</th>
                                <th class="border p-2">Guests</th>
                                <th class="border p-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="appointment in appointments" :key="appointment.id" class="hover:bg-gray-100">
                                <td class="border p-2">{{ appointment.title }}</td>
                                <td class="border p-2">{{ appointment.description }}</td>
                                <td class="border p-2">{{ formatDate(appointment.date_time, appointment.timezone) }}</td>
                                <td class="border p-2">{{ appointment.reminder_time }} minutes before</td>
                                <td class="border p-2">
                                    <ul>
                                        <li v-for="guest in appointment.guests" :key="guest">{{ guest }}</li>
                                    </ul>
                                </td>
                                <td class="border p-2">
                                    <button v-if="canCancel(appointment)"
                                        @click="cancelAppointment(appointment.id)"
                                        class="px-3 py-1 bg-red-600 text-white rounded-md hover:bg-red-700">
                                        Cancel
                                    </button>
                                    <span v-else class="text-gray-500 text-sm font-bold">
                                        {{ appointment.is_canceled === 1 ? "Cancelled" : "Cannot cancel" }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p v-else class="text-gray-500 text-center mt-4">No appointments found.</p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
