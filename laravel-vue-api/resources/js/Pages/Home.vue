<template>
  <main-layout>
    <template #default>
      <div class="container">
        <h1>Products</h1>
        <div v-if="loading">Loading...</div>
        <div v-else>
          <div class="product-grid">
            <product-card 
              v-for="product in products" 
              :key="product.id" 
              :product="product" 
              :userId="userId" 
            />
          </div>
        </div>
      </div>
    </template>
  </main-layout>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import MainLayout from '@/Layouts/MainLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';
import { usePage } from '@inertiajs/vue3';
import SearchBar from '@/Components/SearchBar.vue';

export default {
  components: {
    MainLayout,
    ProductCard,
    SearchBar
  },
  setup() {
    const products = ref([]);
    const loading = ref(true);
    const { props } = usePage();
    const userId = ref(props.auth?.user?.id || null);
    
    onMounted(() => {
      axios.get('/api/products')
        .then(response => {
          products.value = response.data;
          loading.value = false;
        })
        .catch(error => {
          console.error('Error fetching products', error);
          loading.value = false;
        });
    });

    return {
      products,
      loading,
      userId,
    };
  },
};
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
