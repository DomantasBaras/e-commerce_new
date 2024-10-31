// cartStore.js (using Pinia)
import { defineStore } from 'pinia';
import axios from 'axios';

export const useCartStore = defineStore('cartStore', {
  state: () => ({
    cart: {
      items: [],
    },
    loading: false,
  }),
  getters: {
    cartItemsCount: (state) => {
      return (state.cart.items ?? []).reduce((total, item) => total + item.quantity, 0);
    },
    isEmpty: (state) => {
      return (state.cart.items ?? []).length === 0;
    },
    cartTotal: (state) => {
      return state.cart.items.reduce((total, item) => total + (item.quantity * item.product.price), 0).toFixed(2);
    }
  },
  actions: {
    async fetchCart(userId) {
      this.loading = true;
      try {
        const response = await axios.get(`/api/carts/${userId ?? 'session'}`);

        if (userId) {
          this.cart = response.data;
        } else {
          const cartItems = await Promise.all(
            Object.entries(response.data).map(async ([productId, item]) => {
              const productResponse = await axios.get(`/api/products/${productId}`);
              return {
                product: productResponse.data,
                quantity: item.quantity,
                product_id: productId,
                showMore: false, // Initialize showMore to false
              };
            })
          );
          this.cart = { items: cartItems };
        }

        console.log('Cart fetched successfully:', this.cart);
      } catch (error) {
        console.error('Error fetching cart:', error);
      } finally {
        this.loading = false;
      }
    },

    async addItemToCart(userId, productId, newQuantity) {
      try {
        const existingItem = this.cart.items.find(item => item.product_id === productId);
        const currentQuantity = existingItem ? existingItem.quantity : 0;

        // Calculate the difference to adjust quantity by the correct amount
        const quantityDifference = newQuantity - currentQuantity;

        // Avoid making an API call if quantity has not changed
        if (quantityDifference === 0) return;

        const response = await axios.post(`/api/carts/${userId ?? 'session'}/items`, {
          product_id: productId,
          quantity: quantityDifference, // send only the difference
        });

        await this.fetchCart(userId); // Refresh cart to get accurate data
        console.log('Updated cart:', this.cart);
      } catch (error) {
        console.error('Error adding item to cart:', error);
      }
    },


    async removeItemFromCart(userId, cartItemId) {
      try {
        await axios.delete(`/api/carts/${userId ?? 'session'}/items?product_id=${cartItemId}`);
        await this.fetchCart(userId); // Refresh cart after item removal
      } catch (error) {
        console.error('Error removing item from cart:', error);
      }
    },

    // Place order action
    async placeOrder(userId) {
      const router = useRouter(); // Use vue-router for navigation

      if (userId) {
        try {
          const response = await axios.post(`/api/orders/${userId}`);
          alert('Order placed successfully!');
          
          // Redirect user to order details page
          router.push({ name: 'order.show', params: { id: response.data.id } });
        } catch (error) {
          console.error('Error placing order:', error);
        }
      } else {
        alert('Please log in to place an order.');
      }
    }
  },
});
