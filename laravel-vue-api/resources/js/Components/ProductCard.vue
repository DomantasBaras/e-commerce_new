<template>
  <div :class="['card', { 'out-of-stock': product.stock === 0 }]" @click="openForm">
    <div class="image-container">
      <img :src="getImageUrl(product.image)" alt="Product Image" />
    </div>
    <div class="card-body">
      <h3>{{ product.name }}</h3>
      <p>Price: ${{ product.price }}</p>
    </div>
    <div class="card-footer">
      <add-to-cart @click.stop="addToCart" :disabled="product.stock === 0">
        {{ product.stock === 0 ? 'Out of Stock' : 'Add to Cart' }}
      </add-to-cart>
    </div>
  </div>
</template>

<script>
import AddToCart from './AddToCart.vue';
import axios from 'axios';
import { usePage } from '@inertiajs/vue3';

export default {
  props: {
    product: {
      type: Object,
      required: true,
    },
  },
  components: {
    AddToCart,
  },
  setup(props) {
    const { auth, sessionId } = usePage().props;

  const addToCart = () => {
    const userId = auth.user ? auth.user.id : null;

    // Disable the button (or add a loading state)
    const button = document.querySelector('.add-to-cart-button');
    if (button) button.disabled = true;

    axios.post(`/api/carts/${userId ?? 'session'}/items`, {
      product_id: props.product.id,
      quantity: 1,
    })
    .then(response => {
      console.log('Product added to cart:', response.data);
    })
    .catch(error => {
      console.error('Error adding product to cart:', error);
    })
    .finally(() => {
      if (button) button.disabled = false;
    });
  };

    return {
      addToCart,
    };
  },
  methods: {
    openForm() {
      this.$inertia.get(`/products/${this.product.id}`);
    },
    getImageUrl(imagePath) {
      if (imagePath) {
        return `/storage/${imagePath}`;
      } else {
        return '/storage/no-image-icon.png'; // Path to your default image
      }
    }
  }
};
</script>

<style scoped>
.card {
  width: 200px;
  height: 320px;
  display: flex;
  flex-direction: column;
  border: 1px solid #e0e0e0;
  border-radius: 10px;
  overflow: hidden;
  background-color: #fff;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  transition: box-shadow 0.3s ease, background-color 0.3s ease;
  cursor: pointer;
  margin: 10px;
}
.card:hover {
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}
.out-of-stock {
  background-color: #d3d3d3;
  cursor: not-allowed;
}
.image-container {
  flex: 0 0 60%;
  width: 100%;
  overflow: hidden;
}
.image-container img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.card-body {
  padding: 5px;
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
}
.card-body h3 {
  margin: 0 0 5px;
  font-size: 16px;
}
.card-body p {
  margin: 0;
  font-size: 14px;
}
.card-footer {
  padding: 5px;
  display: flex;
  justify-content: center;
}
</style>
