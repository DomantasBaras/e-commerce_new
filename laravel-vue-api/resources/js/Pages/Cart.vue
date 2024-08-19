<template>
  <main-layout>
    <template v-slot:default>
      <div v-if="loading">Loading...</div>
      <div v-else>
        <div v-if="cart && cart.items && cart.items.length > 0" class="cart">
          <h2>Shopping Cart</h2>
          <ul>
            <li v-for="item in cart.items" :key="item.product_id" class="cart-item">
              <img :src="getImageUrl(item.product.image)" alt="Product Image" class="cart-item-image" />
              <div class="cart-item-info">
                <h3>{{ item.product.name }}</h3>
                <p>{{ item.product.description }}</p>
                <p>Quantity: {{ item.quantity }}</p>
                <p>Price: ${{ item.product.price }}</p>
                <p>Total: ${{ item.quantity * item.product.price }}</p>
              </div>
            </li>
          </ul>
          <primary-button @click="placeOrder">Place Order</primary-button>
        </div>
        <div v-else>
          <p>Your cart is empty.</p>
        </div>
      </div>
    </template>
  </main-layout>
</template>

<script>
import MainLayout from '@/Layouts/MainLayout.vue';
import axios from 'axios';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

export default {
  components: {
    MainLayout,
    PrimaryButton,
  },
  setup() {
    const cart = ref(null);
    const loading = ref(true);
    const { props } = usePage();
    const userId = ref(props.auth?.user?.id || null);

    const fetchCart = async () => {
      try {
        console.log('Fetching cart for user:', userId.value);
        const response = await axios.get(`/api/carts/${userId.value ?? 'session'}`);
        console.log('API response:', response.data);

        if (userId.value) {
          cart.value = response.data;
        } else {
          const cartItems = await Promise.all(
            Object.entries(response.data).map(async ([productId, item]) => {
              const productResponse = await axios.get(`/api/products/${productId}`);
              console.log(`Fetched product for ID ${productId}:`, productResponse.data);
              return {
                product: productResponse.data,
                quantity: item.quantity,
                product_id: productId,
              };
            })
          );
          cart.value = { items: cartItems };
        }

        console.log('Cart data:', cart.value);
      } catch (error) {
        console.error('Error fetching cart:', error);
      } finally {
        loading.value = false;
      }
    };

    const placeOrder = async () => {
      if (userId.value) {
        try {
          const response = await axios.post(`/api/orders/${userId.value}`);
          alert('Order placed successfully!');
          window.location.href = route('order.show', { id: response.data.id });
        } catch (error) {
          console.error('Error placing order:', error);
        }
      } else {
        alert('Please log in to place an order.');
      }
    };

    const getImageUrl = (imagePath) => {
      return imagePath ? `/storage/${imagePath}` : '/storage/no-image-icon.png';
    };

    onMounted(() => {
      fetchCart();
    });

    return {
      cart,
      loading,
      placeOrder,
      getImageUrl,
    };
  },
};
</script>

<style scoped>
.cart {
  padding: 20px;
}
.cart-item {
  display: flex;
  align-items: center;
  margin-bottom: 20px;
}
.cart-item-image {
  width: 100px;
  height: 100px;
  object-fit: cover;
  margin-right: 20px;
}
.cart-item-info {
  flex: 1;
}
</style>
