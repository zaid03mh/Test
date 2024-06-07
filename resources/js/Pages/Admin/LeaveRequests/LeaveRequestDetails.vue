<template>
  <AuthenticatedLayout>
      <div class="container mx-auto p-6 bg-gray-100 rounded-lg shadow-md">
          <h1 class="text-3xl font-bold mb-6">Détails de demande</h1>
          
          <div class="mb-6">
              <ul class="list-disc pl-5 bg-white p-4 rounded-lg shadow">
                  <li><strong>Demandé par:</strong> {{ leaveRequest.user.name }}</li>
                  <li><strong>Date de début:</strong> {{ new Date(leaveRequest.start_date).toLocaleDateString() }}</li>
                  <li><strong>Date de fin:</strong> {{ new Date(leaveRequest.end_date).toLocaleDateString() }}</li>
                  <li><strong>Nombre de jours demandés:</strong> {{ leaveRequest.days_requested }}</li>
                  <li><strong>Description:</strong> {{ leaveRequest.description }}</li>
                  <li><strong>Traité:</strong> {{ leaveRequest.processed ? 'Oui' : 'Non' }}</li>
                  <li><strong>Note:</strong> {{ leaveRequest.note }}</li>
                  <li>
                      <strong>Statut:</strong>
                      <select v-model="selectedStatus" @change="updateStatus" class="status-select">
                          <option value="en attente">En attente</option>
                          <option value="approuvé">Approuvé</option>
                          <option value="refusé">Refusé</option>
                      </select>
                  </li>
              </ul>
          </div>
      </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { defineProps, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
  leaveRequest: Object
});

const selectedStatus = ref(props.leaveRequest.status);

function updateStatus() {
 

  Inertia.patch(`/admin/demandes-de-conge/${props.leaveRequest.id}`, {
      status: selectedStatus.value
  });
  if (props.leaveRequest.processed_count >= 3) {
      alert("Vous avez atteint la limite de changement de statut");
      return;
  }
}
</script>

<style scoped>
.container {
max-width: 800px;
}

.list-disc {
list-style-type: disc;
}

.bg-white {
background-color: white;
}

.p-4 {
padding: 1rem;
}

.rounded-lg {
border-radius: 0.5rem;
}

.shadow {
box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.text-3xl {
font-size: 1.875rem;
}

.font-bold {
font-weight: 700;
}

.mb-6 {
margin-bottom: 1.5rem;
}

.text-2xl {
font-size: 1.5rem;
}

.font-semibold {
font-weight: 600;
}

.pl-5 {
padding-left: 1.25rem;
}

.status-select {
padding: 12px;
border-radius: 5px;
font-size: 1.2em;
display: block;
width: 40%;
box-sizing: border-box;
margin: auto;
}
</style>
