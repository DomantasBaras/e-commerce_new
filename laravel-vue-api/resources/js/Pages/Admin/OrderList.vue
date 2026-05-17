<template>
  <MainLayout>
    <template v-slot:default>
      <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Manage Orders</h1>

        <!-- Orders Table -->
        <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
          <thead class="bg-gray-100">
            <tr>
              <th class="py-3 px-6 text-left">Order ID</th>
              <th class="py-3 px-6 text-left">Customer</th>
              <th class="py-3 px-6 text-left">Total</th>
              <th class="py-3 px-6 text-left">Status</th>
              <th class="py-3 px-6 text-left">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in orders" :key="order.id" class="border-t">
              <td class="py-3 px-6">{{ order.id }}</td>

              <td class="py-3 px-6">€{{ order.total }}</td>
              <td class="py-3 px-6">
                <span
                  :class="{
                    'text-green-600': order.status === 'Completed',
                    'text-yellow-600': order.status === 'Pending',
                    'text-red-600': order.status === 'Cancelled',
                  }"
                >
                  {{ order.status }}
                </span>
              </td>
              <td class="py-3 px-6">
                <PrimaryButton @click="viewOrder(order)" class="text-indigo-600 hover:text-indigo-800 mr-4">
                  View
                </PrimaryButton>
                <PrimaryButton v-if="order.status !== 'Completed'" @click="markAsCompleted(order.id)" class="text-green-600 hover:text-green-800">
                  Mark as Completed
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
      orders: [],
    };
  },
  async created() {
    const response = await fetch('/api/orders', {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` },
    });
    this.orders = await response.json();
  },
  methods: {
    viewOrder(order) {
      this.$inertia.get(route('admin.orders.show', order));
    },
    async markAsCompleted(orderId) {
      await fetch(`/api/orders/${orderId}/complete`, {
        method: 'PATCH',
        headers: { Authorization: `Bearer ${localStorage.getItem('token')}` },
      });
      this.orders = this.orders.map((order) =>
        order.id === orderId ? { ...order, status: 'Completed' } : order
      );
    },
  },
};
</script>

<style scoped>
/* Styling for the order management table */
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
