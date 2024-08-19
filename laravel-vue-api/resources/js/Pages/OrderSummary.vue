<template>
  <main-layout>
    <template #default>
      <div class="order-summary-container">
        <h1>Order Summary</h1>
        <div v-if="loading">Loading...</div>
        <div v-else-if="order">
          <h2>Order #{{ order.id }}</h2>
          <p>Status: {{ order.status }}</p>
          <p>Total: ${{ order.total }}</p>

          <h3>Items:</h3>
          <ul>
            <li v-for="item in order.items" :key="item.id">
              <p>{{ item.product.name }} - {{ item.quantity }} x ${{ item.price }}</p>
            </li>
          </ul>
        </div>
        <div v-else>
          <p>No order found.</p>
        </div>
      </div>
    </template>
  </main-layout>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import MainLayout from '@/Layouts/MainLayout.vue';

export default {
  props: ['id'],
  components: {
    MainLayout,
  },
  setup() {
    const order = ref(null);
    const loading = ref(true);
    const orderId = 1; // Get this dynamically, for example from route params

    onMounted(() => {
      axios.get(`/api/orders/${orderId}`)
        .then(response => {
          order.value = response.data;
          loading.value = false;
        })
        .catch(error => {
          console.error('Error fetching order', error);
          loading.value = false;
        });
    });

    return {
      order,
      loading,
    };
  },
};
</script>

<style scoped>
.order-summary-container {
  padding: 20px;
}

.order-summary-container h1 {
  margin-bottom: 20px;
}

.order-summary-container ul {
  list-style: none;
  padding: 0;
}

.order-summary-container li {
  margin-bottom: 10px;
}
</style>
