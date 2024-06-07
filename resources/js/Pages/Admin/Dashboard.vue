<script setup>
import { defineProps } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

// Define props
const props = defineProps({
    totalRequests: Number,
    processedRequests: Number,
    pendingRequests: Number,
    employees: Array
});

</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div>
            <h1 class="text-xl font-bold">Demandes</h1>
            <div class="container mx-auto p-4">
                <!-- Status Cards -->
                <div class="status-cards flex justify-around mb-8">
                    <div class="status-card total bg-yellow-400 text-white text-center p-4 rounded-md shadow-md w-1/4">
                        <h2 class="text-xl font-bold">Total</h2>
                        <p class="text-4xl">{{ props.totalRequests }}</p>
                    </div>
                    <div class="status-card processed bg-blue-400 text-white text-center p-4 rounded-md shadow-md w-1/4">
                        <h2 class="text-xl font-bold">Traités</h2>
                        <p class="text-4xl">{{ props.processedRequests }}</p>
                    </div>
                    <div class="status-card pending bg-red-400 text-white text-center p-4 rounded-md shadow-md w-1/4">
                        <h2 class="text-xl font-bold">Non traités</h2>
                        <p class="text-4xl">{{ props.pendingRequests }}</p>
                    </div>
                </div>

                <!-- Employee List -->
                <div>
                    <h2 class="text-xl font-bold mb-4">Liste des employés</h2>
                    <table class="employee-table min-w-full bg-white shadow-md rounded-md overflow-hidden">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 bg-gray-200">Prénom</th>
                                <th class="py-2 px-4 bg-gray-200">Nom</th>
                                <th class="py-2 px-4 bg-gray-200">Solde congé</th>
                                <th class="py-2 px-4 bg-gray-200">Détails</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="employee in props.employees" :key="employee.id" class="border-b">
                                <td class="py-2 px-4">{{ employee.first_name }}</td>
                                <td class="py-2 px-4">{{ employee.last_name }}</td>
                                <td class="py-2 px-4">{{ employee.total_accumulated_leave }}</td>
                                <td class="py-2 px-4">
                                    <button class="btn-show-details bg-green-200 text-green-800 px-2 py-1 rounded" @click="showEmployee(employee.id)">Afficher</button>
                                </td>
                            </tr>
                            <!-- Empty Rows for spacing if needed -->
                            <tr class="border-b bg-gray-100"><td colspan="4" class="py-2 px-4"></td></tr>
                            <tr class="border-b bg-gray-100"><td colspan="4" class="py-2 px-4"></td></tr>
                            <tr class="border-b bg-gray-100"><td colspan="4" class="py-2 px-4"></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
<script>
export default {
  methods: {
    showEmployee(id) {
      this.$inertia.visit(`/employees/${id}/show`);
    }
  }};
</script>
<style scoped>
.status-cards {
    display: flex;
    justify-content: space-around;
    margin-bottom: 2rem;
}

.status-card {
    padding: 1rem;
    border-radius: 0.5rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    width: 25%;
    text-align: center;
}

.status-card h2 {
    font-size: 1.25rem;
    font-weight: bold;
}

.status-card p {
    font-size: 2.5rem;
}

.employee-table {
    background-color: white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border-radius: 0.5rem;
    overflow: hidden;
}

.employee-table th {
    padding: 0.5rem 1rem;
    background-color: #e2e8f0;
}

.employee-table td {
    padding: 0.5rem 1rem;
    border-bottom: 1px solid #e5e7eb;
}

.btn-show-details {
    background-color: #bbf7d0;
    color: #065f46;
    padding: 0.5rem 1rem;
    border-radius: 0.25rem;
}
</style>
