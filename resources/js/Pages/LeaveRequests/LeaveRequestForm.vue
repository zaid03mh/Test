<template>
  <AuthenticatedLayoutE>
    <h1 class="title">Demander un Congé</h1>
    <form @submit.prevent="checkBalanceAndSubmit" class="form-group">
      <div>
        <label for="start_date">Date de début:</label>
        <input type="date" id="start_date" v-model="form.start_date" @change="validateDates" required class="input">
      </div>
      <div>
        <label for="end_date">Date de fin:</label>
        <input type="date" id="end_date" v-model="form.end_date" @change="validateDates" required class="input">
      </div>
      <div>
        <label for="days_requested">Jours demandés:</label>
        <input type="text" id="days_requested" :value="daysRequested" disabled class="input">
      </div>
    
      <div>
        <label for="description">Description:</label>
        <textarea id="description" v-model="form.description" required class="input"></textarea>
      </div>
      <button type="submit" class="btn btn-success">Soumettre la demande</button>
    </form>
    <div v-if="errorMessage" class="error-message">{{ errorMessage }}</div>
  </AuthenticatedLayoutE>
</template>

<script setup>
import AuthenticatedLayoutE from '@/Layouts/AuthenticatedLayoutE.vue';
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { differenceInDays, parseISO, isBefore, isAfter } from 'date-fns';
import axios from 'axios';

const form = useForm({
    start_date: '',
    end_date: '',
    description: ''
});

const daysRequested = ref(0);
const errorMessage = ref('');

const validateDates = () => {
    const today = new Date();
    const startDate = form.start_date ? parseISO(form.start_date) : null;
    const endDate = form.end_date ? parseISO(form.end_date) : null;

    if (startDate && isBefore(startDate, today)) {
        errorMessage.value = 'La date de début ne peut pas être antérieure à la date d\'aujourd\'hui.';
    } else if (startDate && endDate && isAfter(startDate, endDate)) {
        errorMessage.value = 'La date de début ne peut pas être postérieure à la date de fin.';
    } else {
        errorMessage.value = '';
        calculateDaysRequested();
    }
};

const calculateDaysRequested = () => {
    if (form.start_date && form.end_date) {
        const startDate = parseISO(form.start_date);
        const endDate = parseISO(form.end_date);
        daysRequested.value = differenceInDays(endDate, startDate);
    } else {
        daysRequested.value = 0;
    }
};

const getLeaveBalance = async () => {
    try {
        const response = await axios.get('/leave-balance');
        return response.data.balance;
    } catch (error) {
        console.error('Error fetching leave balance:', error);
        return 0;
    }
};

const checkBalanceAndSubmit = async () => {
    if (errorMessage.value) {
        return;
    }

    const balance = await getLeaveBalance();
    if (balance < daysRequested.value) {
        errorMessage.value = 'Votre solde n\'est pas suffisant.';
    } else {
        form.post(route('leave-requests.store'), {
            onSuccess: () => {
                console.log("Form submitted successfully!");
                alert('Votre demande a été soumise');
                errorMessage.value = '';
            },
            onError: (errors) => console.error("Submission errors:", errors)
        });
    }
};
</script>

<style scoped>
.form-group {
  margin-bottom: 15px;
  justify-content: center;
}
.form-group label {
  display: block;
}
.input, .btn {
  width: 100%;
  padding: 8px;
  margin-top: 5px;
}
.btn-success {
  color: white;
  background-color: green;
  border: none;
  padding: 10px 15px;
  cursor: pointer;
}
.title {
  font-size: 30px;
  color: #000;
}
.error-message {
  color: red;
  margin-top: 10px;
}
</style>
