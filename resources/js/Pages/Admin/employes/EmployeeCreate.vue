<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    first_name: '',
    last_name: '',
    birthdate: '',
    email: '',
    phone_number: '',
    hire_date: '',
});

const submit = () => {
    form.post(route('employees.store'), {
        onSuccess: () => {
            alert("Employé ajouté avec succès");
        },
        onError: (errors) => console.error("Submission errors:", errors)
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <h1 class="title">Créer un nouvel employé</h1>
        <form @submit.prevent="submit">
            <div class="form-group">
                <label for="first_name">Prénom:</label>
                <input type="text" id="first_name" v-model="form.first_name" required>
                <span v-if="form.errors.first_name" class="text-danger">{{ form.errors.first_name }}</span>
            </div>
            <div class="form-group">
                <label for="last_name">Nom:</label>
                <input type="text" id="last_name" v-model="form.last_name" required>
                <span v-if="form.errors.last_name" class="text-danger">{{ form.errors.last_name }}</span>
            </div>
            <div class="form-group">
                <label for="birthdate">Date de naissance:</label>
                <input type="date" id="birthdate" v-model="form.birthdate" required>
                <span v-if="form.errors.birthdate" class="text-danger">{{ form.errors.birthdate }}</span>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" v-model="form.email" required>
                <span v-if="form.errors.email" class="text-danger">{{ form.errors.email }}</span>
            </div>
            <div class="form-group">
                <label for="phone_number">Téléphone:</label>
                <input type="text" id="phone_number" v-model="form.phone_number">
                <span v-if="form.errors.phone_number" class="text-danger">{{ form.errors.phone_number }}</span>
            </div>
            <div class="form-group">
                <label for="hire_date">Date d'embauche:</label>
                <input type="date" id="hire_date" v-model="form.hire_date" required>
                <span v-if="form.errors.hire_date" class="text-danger">{{ form.errors.hire_date }}</span>
            </div>
            
            <button type="submit" class="btn btn-success">Soumettre</button>
        </form>
    </AuthenticatedLayout>
</template>

<style>
.form-group {
  margin-bottom: 15px;
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
.text-danger {
  color: red;
  font-size: 0.875em;
}
.title {
  font-size: 30px;
  color: #000;
}
</style>
