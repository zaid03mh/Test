<script setup>
import { defineProps } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    employee: Object,
    annualLeaveByYear: Object
});
</script>

<template>
    <AuthenticatedLayout>
        <div class="container mx-auto p-6 bg-gray-100 rounded-lg shadow-md">
            <h1 class="text-3xl font-bold mb-6">{{ employee.first_name }} {{ employee.last_name }}</h1>
            
            <div class="mb-6">
                <h2 class="text-2xl font-semibold mb-2">Details Employé </h2>
                <ul class="list-disc pl-5 bg-white p-4 rounded-lg shadow">
                    <li><strong>Email:</strong> {{ employee.email }}</li>
                    <li><strong>Numero de télephone:</strong> {{ employee.phone_number }}</li>
                    <li><strong>Date de Naissance:</strong> {{ new Date(employee.birthdate).toLocaleDateString() }}</li>
                    <li><strong>Date d'ambauche:</strong> {{ new Date(employee.hire_date).toLocaleDateString() }}</li>
                    <li><strong>Age:</strong> {{ employee.age }} ans</li>
                    <li><strong>Solde congés (Accumulé):</strong> {{ employee.total_accumulated_leave }}</li>
                </ul>
            </div>

            <div>
                <h2 class="text-2xl font-semibold mb-2">Solde Congé annuel (5 dernières années)</h2>
                <div class="overflow-x-auto">
                    <table class="employee-table">
                        <thead>
                            <tr>
                                <th class="py-2 px-4">Année</th>
                                <th v-for="(balance, year) in annualLeaveByYear" :key="year" class="py-2 px-4">{{ year }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="py-2 px-4">Solde congé Annuel</td>
                                <td v-for="(balance, year) in annualLeaveByYear" :key="year" class="py-2 px-4">{{ balance }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.container {
    max-width: 1200px;
}

.employee-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background-color: white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border-radius: 0.5rem;
    overflow: hidden;
}

.employee-table th,
.employee-table td {
    padding: 0.5rem 1rem;
    border-bottom: 1px solid #e5e7eb;
}

.employee-table th {
    background-color: #e2e8f0;
}

.btn-show-details {
    background-color: #bbf7d0;
    color: #065f46;
    padding: 0.5rem 1rem;
    border-radius: 0.25rem;
}

.title {
    font-size: 30px;
    color: #000;
}
</style>
