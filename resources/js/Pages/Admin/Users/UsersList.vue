<script setup>
import { defineProps } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
const props = defineProps({
  users: Array
})
</script>

<template>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <AuthenticatedLayout>
      <h1 class="title">Users</h1><br>
      
      <nav>
        <ul class="pagination">
          <li v-if="users.prev_page_url">
            <a href="#" @click="changePage(users.prev_page_url)">Précédent</a>
          </li>
          <li v-if="users.next_page_url">
            <a href="#" @click="changePage(users.next_page_url)">Suivant</a>
          </li>
        </ul>
      </nav>
      <table class="user-table">
        <thead>
          <tr>
            <th>Nom</th>
            <th>Email</th>
            <th>Role</th>
            <th>Modifier</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users.data" :key="user.id">
            <td>{{ user.name }}</td>
            <td>{{ user.email }}</td>
            <td>{{ user.role ? user.role.name : 'N\'a pas de role' }}</td>
            <td><button class="icon-button fa fa-pen" @click="editUser(user.id)"></button></td>
          </tr>
        </tbody>
      </table>
    </AuthenticatedLayout>
</template>

<script>
export default {
  methods: {
    editUser(id) {
      this.$inertia.visit(`/users/${id}/edit`);
    },
    changePage(url) {
      this.$inertia.visit(url);
    }
  }
};
</script>

<style scoped>
.user-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
  background-color: white;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  border-radius: 0.5rem;
  overflow: hidden;
}

.user-table th,
.user-table td {
  padding: 0.5rem 1rem;
  border-bottom: 1px solid #e5e7eb;
}

.user-table th {
  background-color: #e2e8f0;
}

.user-table tr:nth-child(even) {
  background-color: #f9f9f9;
}

/* General styles for table cells containing icons */
td .icon-button {
  font-size: 16px; /* Adjust size as needed */
  color: #000; /* Default color */
  background: none; /* Remove any button background */
  border: none; /* Remove border */
  cursor: pointer; /* Change cursor to pointer to indicate it's clickable */
  display: inline-block; /* Helps with centering */
  width: 100%; /* Full width to help centering */
  height: 100%; /* Full height to help centering */
  align-items: center; /* Center align for flex items */
  justify-content: center; /* Center justify for flex items */
  padding: 10px; /* Padding for clickable area */
}

/* Specific styles for edit icon */
td .fa-pen {
  color: green; /* Change to desired color for edit */
}

/* Specific styles for delete icon */
td .fa-trash {
  color: red; /* Change to desired color for delete */
}

.btn-primary {
  color: white;
  background-color: blue;
  padding: 10px 20px;
  border-radius: 5px;
  cursor: pointer;
}

.title {
  font-size: 30px; /* Adjust size as needed */
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
