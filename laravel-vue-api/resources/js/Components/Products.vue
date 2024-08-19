<template>
  <div>
    <div v-if="loading">Loading...</div>
    <div v-else>
      <ul>
        <li v-for="product in products" :key="product.id">
          <h2>{{ product.name }}</h2>
          <p>{{ product.description }}</p>
          <p>Price: ${{ product.price }}</p>
          <p>Stock: {{ product.stock }}</p>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      products: [],
      loading: true
    };
  },
  mounted() {
    axios.get('http://127.0.0.1:8000/api/products')
      .then(response => {
        this.products = response.data;
        this.loading = false;
      })
      .catch(error => {
        console.error(error);
        this.loading = false;
      });
  }
}
</script>

<style scoped>
/* Add some styling for your component if needed */
</style>
