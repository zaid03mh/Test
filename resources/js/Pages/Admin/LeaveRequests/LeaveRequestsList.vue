<script setup>
import { ref, computed } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { defineProps } from 'vue';

const props = defineProps({
  allRequests: Object
});

const filterType = ref('all'); // Use 'processed' or 'unprocessed' to filter

const requests = computed(() => {
  if (filterType.value === 'all') {
    return props.allRequests.data;
  } else if (filterType.value === 'processed') {
    return props.allRequests.data.filter(request => request.processed);
  } else {
    return props.allRequests.data.filter(request => !request.processed);
  }
});

function updateStatus(request) {
  Inertia.patch(`/admin/demandes-de-conge/${request.id}`, { status: request.status });
  if (request.processed_count >= 3) {
    alert("Vous avez atteint la limite de changement de statut");
    return;
  }
}

function viewRequest(id) {
  Inertia.visit(`/admin/demandes-de-conge/${id}`); // Ensure this matches the route definition
}

function changePage(url) {
  Inertia.visit(url);
}
</script>

<template>
  <AuthenticatedLayout>
    <h1 class="title">Liste des demandes</h1>
    <select v-model="filterType">
      <option value="all">Tous</option>
      <option value="processed">Traités</option>
      <option value="unprocessed">Non traités</option>
    </select>
    <nav>
      <ul class="pagination">
        <li v-if="props.allRequests.prev_page_url">
          <a href="#" @click.prevent="changePage(props.allRequests.prev_page_url)">Précédent</a>
        </li>
        <li v-if="props.allRequests.next_page_url">
          <a href="#" @click.prevent="changePage(props.allRequests.next_page_url)">Suivant</a>
        </li>
      </ul>
    </nav>
    <table class="request-table">
      <thead>
        <tr>
          <th>Demandé par</th>
          <th>Date de début</th>
          <th>Date de fin</th>
          <th >Jours demandés</th>
          <th>Statut</th>
          <th>Note</th>
          <th>Voir</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="request in requests" :key="request.id">
          <td>{{ request.user?.name }}</td>
          <td>{{ request.start_date }}</td>
          <td>{{ request.end_date }}</td>
          <td class="days-requested-column">{{ request.days_requested }}</td>
          
          <td>
            <select v-model="request.status" @change="updateStatus(request)" class="status-select">
              <option value="en attente">En attente</option>
              <option value="approuvé">Approuvé</option>
              <option value="refusé">Refusé</option>
            </select>
          </td>
          <td class="note-column">{{ request.note }}</td>
          <td>
            <button class="icon-button fa fa-eye" @click="viewRequest(request.id)"></button>
          </td>
        </tr>
      </tbody>
    </table>
  </AuthenticatedLayout>
</template>

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
  padding: 0.5rem 1rem;
  border-radius: 0.25rem;
  font-size: 0.8em;
  display: block;
  width: 100%;
  box-sizing: border-box;
  margin: auto;
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

.pagination a.is-active {
  font-weight: bold;
  text-decoration: underline;
}

.title {
  font-size: 30px;
  color: #000;
}

.note-column {
  max-width: 350px; /* Limite la largeur de la colonne Note */
  /* Affiche des points de suspension si le texte est trop long */
}

.days-requested-column {
  max-width: 80px; /* Limite la largeur de la colonne Jours demandés */
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis; /* Affiche des points de suspension si le texte est trop long */
}

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
</style>
