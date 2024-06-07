<template>
  <AuthenticatedLayoutE>
      <div class="container mx-auto p-6 bg-gray-100 rounded-lg shadow-md">
          <h1 class="text-3xl font-bold mb-6">Détails de la Demande</h1>
          
          <div class="mb-6">
              <ul class="list-disc pl-5 bg-white p-4 rounded-lg shadow">
                  <li><strong>Demandé par:</strong> {{ leaveRequest.user.name }}</li>
                  <li><strong>Date de début:</strong> {{ formatDate(leaveRequest.start_date) }}</li>
                  <li><strong>Date de fin:</strong> {{ formatDate(leaveRequest.end_date) }}</li>
                  <li><strong>Nombre de jours demandés:</strong> {{ leaveRequest.days_requested }}</li>
                  <li><strong>Description:</strong> {{ leaveRequest.description }}</li>
                  <li><strong>Statut:</strong> {{ leaveRequest.status }}</li>
                  <li><strong>Traité:</strong> {{ leaveRequest.processed ? 'Oui' : 'Non' }}</li>
                  <li><strong>Note:</strong> {{ leaveRequest.note }}</li>
              </ul>
          </div>
      </div>
  </AuthenticatedLayoutE>
</template>

<script setup>
import { defineProps } from 'vue';
import AuthenticatedLayoutE from '@/Layouts/AuthenticatedLayoutE.vue';

const props = defineProps({
  leaveRequest: {
      type: Object,
      required: true
  }
});

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString('fr-FR', options);
};
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

.pl-5 {
padding-left: 1.25rem;
}
</style>
