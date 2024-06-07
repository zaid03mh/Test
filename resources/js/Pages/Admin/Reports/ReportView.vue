<template>
  <AuthenticatedLayout>
    <div class="report-container">
      <h1>Générer des Rapports</h1>
      <form @submit.prevent="generateReport" class="report-form">
        <div class="form-group">
          <label for="user">Utilisateur:</label>
          <select v-model="filters.user_id" class="form-control">
            <option value="">Tous</option>
            <option v-for="user in users" :key="user.id" :value="user.id">
              {{ user.name }}
            </option>
          </select>
        </div>
        <div class="form-group">
          <label for="month">Mois:</label>
          <select v-model="filters.month" class="form-control">
            <option value="">Tous</option>
            <option v-for="(month, index) in months" :key="index" :value="index + 1">
              {{ month }}
            </option>
          </select>
        </div>
        <div class="form-group">
          <label for="year">Année:</label>
          <select v-model="filters.year" class="form-control">
            <option value="">Tous</option>
            <option v-for="year in years" :key="year" :value="year">
              {{ year }}
            </option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary">Générer le rapport</button>
      </form>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const { props } = usePage();
const users = ref(props.users);

const filters = ref({
  user_id: '',
  month: '',
  year: ''
});

const months = ref([
  'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 
  'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
]);

const years = ref([]);
const currentYear = new Date().getFullYear();
for (let year = currentYear; year >= 2020; year--) {
  years.value.push(year);
}

const generateReport = () => {
  Inertia.get('/admin/reports/another', filters.value);
};
</script>

<style scoped>
.report-container {
  max-width: 600px;
  margin: 0 auto;
  padding: 20px;
  background-color: #fff;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  border-radius: 8px;
}

.report-container h1 {
  text-align: center;
  color: #333;
  margin-bottom: 20px;
}

.report-form {
  display: flex;
  flex-direction: column;
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
}

.form-control {
  width: 100%;
  padding: 10px;
  font-size: 16px;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.form-control:focus {
  border-color: #4CAF50;
  outline: none;
  box-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
}

.btn {
  display: inline-block;
  padding: 10px 20px;
  font-size: 16px;
  font-weight: bold;
  text-align: center;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.btn-primary {
  background-color: #4CAF50;
  color: #fff;
}

.btn-primary:hover {
  background-color: #45a049;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

th, td {
  border: 1px solid #ddd;
  padding: 8px;
}

th {
  background-color: #f2f2f2;
}
</style>
