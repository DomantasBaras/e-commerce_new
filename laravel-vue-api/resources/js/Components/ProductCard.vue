<template>
  <div :class="['card', { 'out-of-stock': product.stock === 0 }]" @click="openForm">
    <div class="image-container">
      <img :src="getImageUrl(product.image)" alt="Product Image" />
    </div>
    <div class="card-hover-content">
      <h3>{{ product.name }}</h3>
      <p>Price: ${{ product.price }}</p>
      <add-to-cart @click.stop="addToCart" :disabled="product.stock === 0">
        {{ product.stock === 0 ? 'Out of Stock' : 'Add to Cart' }}
      </add-to-cart>
    </div>
  </div>
</template>

<script>
import AddToCart from './AddToCart.vue';
import { usePage } from '@inertiajs/vue3';
import { useCartStore } from '../Stores/cartStore';

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
    const { auth } = usePage().props;
    const cartStore = useCartStore();  
    const userId = auth.user ? auth.user.id : null;


    const addToCart = async () => {
      await cartStore.addItemToCart(userId,props.product.id, 1 );
            // Trigger the dropdown visibility after adding to cart
      const navbarInstance = document.querySelector('.navbar');
      if (navbarInstance && navbarInstance.__vue__) {
        navbarInstance.__vue__.showCartDropdown();
      }
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
      return imagePath ? `/storage/${imagePath}` : '/storage/no-image-icon.png';
    },
  },
};
</script>

<style scoped>
.card {
  width: 200px;
  height: 320px;
  position: relative;
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  border: 1px solid #e0e0e0;
  border-radius: 10px;
  overflow: hidden;
  background-color: #fff;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  cursor: pointer;
  transition: box-shadow 0.3s ease;
  margin: 10px;
}

.card:hover {
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.image-container {
  width: 100%;
  height: 100%;
  overflow: hidden;
  flex: 1;
}

.image-container img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.card-hover-content {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  background-color: rgba(0, 0, 0, 0.7); /* Semi-transparent overlay on hover */
  color: white;
  padding: 10px;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.card:hover .card-hover-content {
  opacity: 1;
}

.card-hover-content h3 {
  font-size: 18px;
  margin-bottom: 10px;
}

.card-hover-content p {
  font-size: 16px;
  margin-bottom: 10px;
}

.card-hover-content add-to-cart {
  font-size: 14px;
}

.out-of-stock {
  background-color: #d3d3d3;
  cursor: not-allowed;
}
</style>
