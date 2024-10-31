<template>
  <main-layout>
    <template #default>
      <div class="container">
        <h1>Search Results for "{{ query }}"</h1>

        <div v-if="results.length > 0">
          <div class="product-grid">
            <product-card 
              v-for="product in results" 
              :key="product.id" 
              :product="product"
            />
          </div>
        </div>
        <div v-else>
          <p>No products found matching your query.</p>
        </div>
      </div>
    </template>
  </main-layout>
</template>

<script setup>
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';

const { props } = usePage(); // Get props passed from the server
const results = ref(props.results); // Get the search results from Inertia props
const query = ref(props.query); // Get the search query from Inertia props
</script>

<style scoped>
.container {
  padding: 20px;
}

.product-grid {
  display: flex;
  flex-wrap: wrap;
  justify-content: flex-start;
}

.product-grid > * {
  width: calc(100% / 3 - 25px);
  box-sizing: border-box;
}

@media (max-width: 1200px) {
  .product-grid > * {
    width: calc(100% / 2 - 20px);
  }
}

@media (max-width: 768px) {
  .product-grid > * {
    width: 100%;
  }
}
</style>
