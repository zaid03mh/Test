<script setup>
import { defineProps } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
  employees: Array
})
</script>

<template>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  <AuthenticatedLayout>
    <h1 class="title">Employés</h1><br>
    <div class="create-button-container">
      <button @click="createEmployee()" class="btn btn-primary">Créer un nouvel employé</button>
    </div>
    <nav>
      <ul class="pagination">
        <li v-if="employees.prev_page_url">
          <a href="#" @click="changePage(employees.prev_page_url)">Précédent</a>
        </li>
        <li v-if="employees.next_page_url">
          <a href="#" @click="changePage(employees.next_page_url)">Suivant</a>
        </li>
      </ul>
    </nav>
    
    <table class="employee-table">
      <thead>
        <tr>
          <th>Nom</th>
          <th>Prénom</th>
          <th>Âge</th>
          <th>Email</th>
          <th>Téléphone</th>
          <th>Date d'embauche</th>
          <th>Solde de congés</th>
          <th>Voir</th>
          <th>Modifier</th>
          <th>Supprimer</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="employee in employees.data" :key="employee.id">
          <td>{{ employee.first_name }}</td>
          <td>{{ employee.last_name }}</td>
          <td>{{ employee.age }} ans</td>
          <td>{{ employee.email }}</td>
          <td>{{ employee.phone_number }}</td>
          <td>{{ employee.hire_date }}</td>
          <td>{{ employee.total_accumulated_leave }}</td>
          <td><button class="icon-button fa fa-eye" @click="showEmployee(employee.id)"></button></td>
          <td><button class="icon-button fa fa-pen" @click="editEmployee(employee.id)"></button></td>
          <td><button class="icon-button fa fa-trash" @click="confirmDeleteEmployee(employee.id)"></button></td>
        </tr>
      </tbody>
    </table>
  </AuthenticatedLayout>
</template>

<script>
export default {
  methods: {
    createEmployee() {
      this.$inertia.visit('/employees/create');
    },
    showEmployee(id) {
      this.$inertia.visit(`/employees/${id}/show`);
    },
    editEmployee(id) {
      this.$inertia.visit(`/employees/${id}/edit`);
    },
    confirmDeleteEmployee(id) {
      if (confirm("Êtes-vous sûr de vouloir supprimer cet employé ?")) {
        this.deleteEmployee(id);
      }
    },
    deleteEmployee(id) {
      this.$inertia.delete(`/employees/${id}`);
    },
    changePage(url) {
      this.$inertia.visit(url);
    }
  }
};
</script>

<style scoped>
/* Status Cards */
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

/* Employee Table */
.employee-table {
    background-color: white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border-radius: 0.5rem;
    overflow: hidden;
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.employee-table th {
    padding: 0.5rem 1rem;
    background-color: #e2e8f0;
}

.employee-table td {
    padding: 0.5rem 1rem;
    border-bottom: 1px solid #e5e7eb;
}

td .icon-button {
    font-size: 16px;
    color: #000;
    background: none;
    border: none;
    cursor: pointer;
    display: inline-block;
    width: 100%;
    height: 100%;
    align-items: center;
    justify-content: center;
    padding: 10px;
}

td .fa-pen {
    color: green;
}

td .fa-trash {
    color: red;
}

.btn-primary {
    color: white;
    background-color: blue;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
}

.title {
    font-size: 30px;
    color: #000;
}

.pagination {
    list-style: none;
    display: flex;
    justify-content: center;
}

.pagination li {
    margin: 0 10px;
}

.pagination a {
    color: blue;
    text-decoration: none;
    cursor: pointer;
}
</style>
