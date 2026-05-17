<template>
  <MainLayout>
    <template v-slot:default>
      <div class="container">
        <h1>Export/Import Users</h1>

        <!-- Export Users -->
        <div class="mb-4">
          <a :href="route('users.export')" class="btn btn-primary">Export Users</a>
        </div>

        <!-- Import Users -->
        <form @submit.prevent="importUsers">
          <div class="mb-4">
            <input type="file" ref="file" accept=".csv" />
          </div>
          <button type="submit" class="btn btn-success">Import Users</button>
        </form>
      </div>
      <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Manage Users</h1>

        <!-- Add User Button -->
        <PrimaryButton @click="openCreateForm()" class="mb-4">
          Add New User
        </PrimaryButton>

        <!-- User Table -->
        <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
          <thead class="bg-gray-100">
            <tr>
              <th class="py-3 px-6 text-left">Name</th>
              <th class="py-3 px-6 text-left">Email</th>
              <th class="py-3 px-6 text-left">Role</th>
              <th class="py-3 px-6 text-left">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in users" :key="user.id" class="border-t">
              <td class="py-3 px-6">{{ user.name }}</td>
              <td class="py-3 px-6">{{ user.email }}</td>
              <td class="py-3 px-6">{{ user.role }}</td>
              <td class="py-3 px-6">
                <PrimaryButton @click="openEditForm(user)" class="text-indigo-600 hover:text-indigo-800 mr-4">
                  Edit
                </PrimaryButton>
                <PrimaryButton @click="deleteUser(user.id)" class="text-red-600 hover:text-red-800">
                  Delete
                </PrimaryButton>
              </td>
            </tr>
          </tbody>
        </table>
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
  data() {
    return {
      users: [],
    };
  },
  async created() {
    const response = await fetch('/api/users', {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
    });
    this.users = await response.json();
  },
  methods: {
    openEditForm(user) {
      this.$inertia.get(route('admin.users.edit', user));
    },
    openCreateForm() {
      this.$inertia.get(route('admin.users.create'));
    },
    async deleteUser(id) {
      await fetch(`/api/users/${id}`, {
        method: 'DELETE',
        headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
      });
      this.users = this.users.filter(user => user.id !== id);
    },
    async importUsers() {
      const file = this.$refs.file.files[0];
      if (!file) return alert('Please select a file.');

      const formData = new FormData();
      formData.append('file', file);

      await fetch(route('users.import'), {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: formData,
      }).then(() => alert('Import successful!'));
    },
  }
};
</script>

<style scoped>
.container {
  max-width: 1000px;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th,
td {
  padding: 12px;
  text-align: left;
}

th {
  background-color: #f4f4f4;
}
</style>
