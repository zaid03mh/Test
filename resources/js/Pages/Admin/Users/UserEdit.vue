<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  user: Object
});

const form = useForm({
  name: props.user.name,
  email: props.user.email,
  password: '',  // Password initially empty
  password_confirmation: '',  // Include password confirmation for updates
  role_id: props.user.role_id || '' // Ensure the role ID is included and defaults if necessary
});

const submit = () => {
  console.log("Updating user", form);
  form.put(route('users.update', props.user.id), {
    preserveScroll: true,
    onSuccess: () => {
      console.log("User updated successfully!");
      alert('Utilisateur mis à jour avec succès');
    },
    onError: errors => console.error("Update errors:", errors)
  });
};
</script>

<template>
  <AuthenticatedLayout>
    <h1 class="title">Modifier Utilisateur</h1>
    <form @submit.prevent="submit">
      <div class="form-group">
        <label for="name">Nom</label>
        <input type="text" id="name" v-model="form.name" required>
      </div>
      <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" id="email" v-model="form.email" required>
      </div>
      <div class="form-group">
        <label for="password">Nouveau Mot de Passe</label>
        <input type="password" id="password" v-model="form.password" placeholder="Enter new password">
      </div>
      <div class="form-group">
        <label for="password_confirmation">Confirmer le Mot de Passe</label>
        <input type="password" id="password_confirmation" v-model="form.password_confirmation" placeholder="Confirm new password">
      </div>
      <div class="form-group">
        <label for="role">Role:</label>
        <select id="role" v-model="form.role_id" required>
          <option value="">Sélectionner un Role</option>
          <option value="1">Admin</option> <!-- Assuming '1' is the ID for Admin -->
          <option value="2">User</option> <!-- Assuming '2' is the ID for User -->
        </select>
      </div>
      <button type="submit" class="btn btn-success">Mettre à jour</button>
    </form>
  </AuthenticatedLayout>
</template>

<style>
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
  font-size: 30px; /* Adjust size as needed */
  color: #000;
}
</style>
