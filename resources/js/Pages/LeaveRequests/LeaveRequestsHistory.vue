<template>
  <AuthenticatedLayoutE>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <h1 class="title">Historique des demandes </h1><br>
    <table class="request-table">
      <thead>
        <tr>
          <th>N°</th>
          <th>Date de début</th>
          <th>Date de fin</th>
          <th>Jours demandés </th>
          <th>Statut</th>
          <th>Note</th>
          <th>Voir</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(request, index) in requests.data" :key="request.id">
          <td>{{ index + 1 }}</td>
          <td>{{ request.start_date }}</td>
          <td>{{ request.end_date }}</td>
          <td class="days-requested-column">{{ request.days_requested }}</td>
          <td>{{ request.status }}</td>
          <td class="note-column">{{ request.note }}</td>
          <td>
            <button class="icon-button fa fa-eye" @click="viewRequest(request.id)"></button>
          </td>
        </tr>
      </tbody>
    </table>
    <nav>
      <ul class="pagination">
        <li v-if="requests.prev_page_url">
          <a href="#" @click.prevent="changePage(requests.prev_page_url)">Précédent</a>
        </li>
        <li v-if="requests.next_page_url">
          <a href="#" @click.prevent="changePage(requests.next_page_url)">Suivant</a>
        </li>
      </ul>
    </nav>
  </AuthenticatedLayoutE>
</template>

<script setup>
import { defineProps } from 'vue';
import AuthenticatedLayoutE from '@/Layouts/AuthenticatedLayoutE.vue';
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
  requests: Object
});

function changePage(url) {
  Inertia.visit(url);
}

function viewRequest(id) {
  Inertia.visit(`/demande-de-conge/${id}`);
}
</script>

<style scoped>
.request-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
  background-color: white;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  border-radius: 0.5rem;
  overflow: hidden;
}

.request-table th,
.request-table td {
  padding: 0.5rem 1rem;
  border-bottom: 1px solid #e5e7eb;
}

.request-table th {
  background-color: #e2e8f0;
}

.request-table tr:nth-child(even) {
  background-color: #f9f9f9;
}

.status-select {
  padding: 8px;
  border-radius: 5px;
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

.title {
  font-size: 30px;
  color: #000;
}

.note-column {
  max-width: 350px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.days-requested-column {
  max-width: 80px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
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
</style>
