<template>
  <div class="button-group">
          <button @click="printReport" class="button print-button">Imprimer</button>
        </div>
  <div>
    <header class="report-header">
      <img src="/build/assets/images/logo1.png" alt="Company Logo" class="company-logo"/>
      <div class="company-info">
        <p><strong>Adresse :</strong> 77, Rue Mohamed Smiha ETG 10 NR 57 CASABLANCA</p>
        <p><strong>Téléphone :</strong> 0621094673</p>
        <p><strong>Email :</strong> m.bouhali@infragest.ma</p>
      </div>
    </header>
    <div>
      <h1>Rapport Des congés</h1>
      <div v-if="reports.length > 0">
        <table class="results-table">
          <thead>
            <tr>
              <th>Utilisateur</th>
              <th>Date de début</th>
              <th>Date de fin</th>
              <th>Jour Demandés</th>
              <th>Note</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="report in reports" :key="report.id">
              <td>{{ report.user.name }}</td>
              <td>{{ report.start_date }}</td>
              <td>{{ report.end_date }}</td>
              <td>{{ report.days_requested }}</td>
              <td>{{ report.note }}</td>
            </tr>
          </tbody>
        </table>
        
      </div>
      <div v-else>
        <p>Aucun rapport trouvé.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';

const { props } = usePage();
const reports = ref(props.reports || []);



const printReport = () => {
  window.print();
};
</script>

<style scoped>
body {
  font-family: 'Arial', sans-serif;
  background-color: #f4f4f9;
  color: #333;
}

.report-header {
  display: flex;
  align-items: center;
  margin-bottom: 20px;
  padding: 10px;
  background-color: #fff;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.company-logo {
  width: 80px;
  height: auto;
  margin-right: 20px;
}

.company-info p {
  margin: 0;
  line-height: 1.6;
}

h1 {
  font-size: 24px;
  color: #000000;
  text-align: center;
  margin: 20px 0;
}

.results-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
  background-color: #fff;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.results-table th, .results-table td {
  border: 1px solid #ddd;
  padding: 10px;
  text-align: left;
}

.results-table th {
  background-color: #4CAF50;
  color: #fff;
}

.results-table tr:nth-child(even) {
  background-color: #f9f9f9;
}

.button-group {
  display: flex;
  justify-content: center;
  margin: 20px 0;
}

.button {
  padding: 10px 20px;
  margin: 0 10px;
  border: none;
  cursor: pointer;
  font-size: 16px;
  border-radius: 4px;
  transition: background-color 0.3s ease;
}

.download-button {
  background-color: #4CAF50;
  color: #fff;
}

.download-button:hover {
  background-color: #45a049;
}

.print-button {
  background-color: #f44336;
  color: #fff;
}

.print-button:hover {
  background-color: #e53935;
}

@media print {
  body {
    background-color: #fff;
    color: #000;
  }

  .report-header {
    background-color: #fff;
    box-shadow: none;
  }

  .results-table th {
    background-color: #4CAF50 !important;
    color: #fff !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }

  .results-table tr:nth-child(even) {
    background-color: #f9f9f9 !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }

  .print-button, .download-button {
    display: none;
  }
}
</style>
