<template>
  <MainLayout>
    <template v-slot:default>
      <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">{{ user.id ? 'Edit' : 'Create' }} User</h1>

        <form @submit.prevent="saveUser" class="bg-white p-6 rounded-lg shadow-md">
          <!-- User Name -->
          <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">User Name</label>
            <input v-model="user.name" id="name" type="text" required
              class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"/>
          </div>

          <!-- User Email -->
          <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input v-model="user.email" id="email" type="email" required
              class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"/>
          </div>

          <!-- Password -->
          <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input v-model="user.password" id="password" type="password" :required="!user.id"
              class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"/>
            <small v-if="user.id" class="text-gray-500">Leave blank to keep the current password</small>
          </div>

          <!-- User Role -->
          <div class="mb-4">
            <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
            <select v-model="user.role" id="role" required
              class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
              <option value="user">User</option>
              <option value="admin">Admin</option>
            </select>
          </div>

          <!-- Submit Button -->
          <div>
            <PrimaryButton type="submit" class="w-full">
              {{ user.id ? 'Update' : 'Create' }} User
            </PrimaryButton>
          </div>
        </form>
      </div>
    </template>
  </MainLayout>
</template>

<script>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import MainLayout from '@/Layouts/MainLayout.vue';

export default {
  components: {
    PrimaryButton,
    MainLayout,
  },
  props: {
    user: Object, // Receive the user object if editing
  },
  data() {
    return {
      user: this.user || { name: '', email: '', password: '', role: 'user' },
    };
  },
  methods: {
    async saveUser() {
      try {
        const userData = {
          name: this.user.name,
          email: this.user.email,
          password: this.user.password || undefined, // Leave undefined to avoid sending empty password
          role: this.user.role,
        };

        const endpoint = this.user.id ? `/api/users/${this.user.id}` : '/api/users';
        const method = this.user.id ? 'PUT' : 'POST';

        const response = await fetch(endpoint, {
          method,
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
          },
          body: JSON.stringify(userData),
        });

        const data = await response.json();
        console.log('User data saved successfully:', data);
      } catch (error) {
        console.error('Error saving user data:', error);
      }
    },
  },
};
</script>

<style scoped>
.container {
  max-width: 800px; /* Limit form width */
}

form {
  background-color: #fff;
  border-radius: 10px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

label {
  font-weight: 600;
}

input, select {
  padding-left: 12px;
  padding-right: 12px;
  font-size: 1rem;
  border-radius: 5px;
  transition: all 0.3s;
}

input:focus, select:focus {
  border-color: #6b7280;
  outline: none;
}

PrimaryButton {
  background-color: #007bff;
  color: white;
}
</style>
