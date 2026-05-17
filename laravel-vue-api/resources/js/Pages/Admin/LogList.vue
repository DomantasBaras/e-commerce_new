<template>
  <MainLayout>
    <template v-slot:default>
      <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">System Logs</h1>

        <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
          <thead class="bg-gray-100">
            <tr>
              <th class="py-3 px-6 text-left">User ID</th>
              <th class="py-3 px-6 text-left">Action</th>
              <th class="py-3 px-6 text-left">Details</th>
              <th class="py-3 px-6 text-left">Timestamp</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="log in logs" :key="log.id" class="border-t">
              <td class="py-3 px-6">{{ log.user_id }}</td>
              <td class="py-3 px-6">{{ log.action }}</td>
              <td style="white-space: pre-line;" class="py-3 px-6">{{ log.details }}</td>
              <td class="py-3 px-6">{{ log.created_at }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </template>
  </MainLayout>
</template>

<script>
import MainLayout from '@/Layouts/MainLayout.vue';

export default {
  components: {
    MainLayout,
  },
  data() {
    return {
      logs: [],
    };
  },
  async created() {
    const response = await fetch('/api/logs', {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
    });
    this.logs = await response.json();
  },
};
</script>
