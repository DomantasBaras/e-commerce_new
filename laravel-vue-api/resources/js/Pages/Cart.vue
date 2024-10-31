<template>
  <main-layout>
    <template v-slot:default>
      <!-- Loading spinner or text when cart data is being fetched -->
      <div v-if="loading" class="loading-text">Loading...</div>

      <!-- Cart contents if available -->
      <div v-else>
        <!-- Check if there are items in the cart -->
        <div v-if="Object.values(cart).length > 0" class="cart-container">
          <h2>Shopping Cart</h2>
          <table class="order-table">
            <thead>
              <tr>
                <th colspan="2">Product</th>
                <th class="white-space_nowrap">Unit price</th>
                <th class="tac">Quantity</th>
                <th class="tar">Total</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <!-- Loop through cart items -->
              <tr v-for="item in cart.items" :key="item.product_id" class="cart-item">
                <td data-label="Product" class="image_cell">
                  <img :src="getImageUrl(item.product.image)" alt="Product Image" class="cart-item-image" />
                </td>
                <td class="info_cell">
                  <h3 class="product-name">{{ item.product.name }}</h3>
                  <div class="description-wrapper">
                    <p>
                      {{ item.showMore ? item.product.description : truncatedDescription(item.product.description) }}
                      <button v-if="item.product.description && item.product.description.length > 150" 
                              @click="toggleDescription(item)" class="show-more-btn">
                        {{ item.showMore ? 'Show Less' : 'Show More' }}
                      </button>
                    </p>
                  </div>
                </td>
                <td data-label="Unit Price" class="price_cell">
                  <span class="price">{{ item.product.price }} €</span>
                </td>
                <td data-label="Quantity" class="quant_cell">
                  <div class="inc-dec-input">
                    <button 
                      @click="decrementQuantity(item)" 
                      :disabled="item.quantity === 1" 
                      class="dec-button"
                    >-</button>
                    <input 
                      type="number" 
                      v-model.number="item.quantity" 
                      @change="updateCart(item)" 
                      min="1" 
                      class="input-small"
                    />
                    <button @click="incrementQuantity(item)" class="inc-button">+</button>
                  </div>
                </td>
                <td data-label="Total" class="total_cell">
                  <span class="price">{{ (item.quantity * item.product.price).toFixed(2) }} €</span>
                </td>
                <td data-label="Action" class="controls_cell">
                  <div class="remove-item" @click="removeFromCart(item)">
                    <i class="fas fa-trash"></i>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>

          <!-- Cart total summary -->
          <div class="cart-summary">
            <h3>Total: {{cartTotal}} €</h3>
          </div>
          
          <!-- Place order button -->
          <primary-button @click="placeOrder">Place Order</primary-button>
        </div>

        <!-- Message if the cart is empty -->
        <div v-else class="empty-cart-message">
          <p>Your cart is empty.</p>
        </div>
      </div>
    </template>
  </main-layout>
</template>

<script>
import MainLayout from '@/Layouts/MainLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { computed, onMounted } from 'vue';
import { useCartStore } from '@/Stores/cartStore';
import { usePage } from '@inertiajs/vue3';

export default {
  components: {
    MainLayout,
    PrimaryButton,
  },
  setup() {
    // Get user information from Inertia's page props
    const { props } = usePage();
    const userId = computed(() => props.auth?.user?.id || null);

    // Access the cart store
    const cartStore = useCartStore();
    
    // Destructure cart and loading state from the store
    const cart = computed(() => cartStore.cart );
    const loading = computed(() => cartStore.loading);

    // Fetch cart data when the component is mounted
    onMounted(() => {
      cartStore.fetchCart(userId.value);
      console.log("Cart from store:", cart.value); // Add this line for debuggin
    });

    // Compute the cart total
    const cartTotal = computed(() => {
      return cart.value.items.reduce((total, item) => total + (item.quantity * item.product.price), 0).toFixed(2);
    });

    // Helper functions
    const getImageUrl = (imagePath) => {
      return imagePath ? `/storage/${imagePath}` : `/storage/no-image-icon.png`;
    };

    const truncatedDescription = (description) => {
      return description.length > 150 ? description.substring(0, 150) + '...' : description;
    };

    const toggleDescription = (item) => {
      item.showMore = !item.showMore;
    };

    // Increment quantity by 1
    const incrementQuantity = (item) => {
      cartStore.addItemToCart(userId.value, item.product_id, item.quantity + 1); // Increase by 1
    };

    // Decrement quantity by 1
    const decrementQuantity = (item) => {
      if (item.quantity > 1) {
        cartStore.addItemToCart(userId.value, item.product_id, item.quantity - 1); // Decrease by 1
      }
    };

    // const incrementQuantity = (item) => {
    //   item.quantity++;
    //   updateCart(item);
    // };

    // const decrementQuantity = (item) => {
    //   if (item.quantity > 1) {
    //     item.quantity--;
    //     updateCart(item);
    //   }
    // };

    // Remove item from the cart
    const removeFromCart = async (item) => {
      await cartStore.removeItemFromCart(userId.value, item.product_id);
    };

    // Update cart when quantity input changes
    const updateCart = (item) => {
      cartStore.addItemToCart(userId.value, item.product_id, item.quantity);
    };

    // Place order function
    const placeOrder = async () => {
      if (userId.value) {
        try {
          const response = await cartStore.placeOrder(userId.value);
          alert('Order placed successfully!');
          window.location.href = route('order.show', { id: response.data.id });
        } catch (error) {
          console.error('Error placing order:', error);
        }
      } else {
        alert('Please log in to place an order.');
      }
    };

    return {
      cart,
      loading,
      cartTotal,
      getImageUrl,
      incrementQuantity,
      decrementQuantity,
      removeFromCart,
      updateCart,
      truncatedDescription,
      toggleDescription,
      placeOrder,
    };
  },
};
</script>

<style scoped>
.cart-container {
  padding: 20px;
}
.product-name {
  font-size: 1.5rem;
  text-align: start;
  margin-top: 0;
}
.order-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 20px;
}
.order-table th, .order-table td {
  border: 1px solid #ddd;
  padding: 8px;
  text-align: left;
}
.cart-item-image {
  width: 100px;
  height: 100px;
  object-fit: cover;
}

.image_cell {
  width: 120px;
  text-align: right;
  margin-right: 1em;
}

.cart-item-image {
  width: 100px; /* Set a fixed width */
  height: auto;
  object-fit: cover;
  margin-left: auto; /* Aligns image to the right within its cell */
}

.inc-dec-input {
  display: flex;
  align-items: center;
}
.dec-button, .inc-button {
  width: 30px;
  height: 30px;
  border: 1px solid #ddd;
  background-color: #f0f0f0;
  cursor: pointer;
}
.input-small {
  width: 50px;
  text-align: center;
  margin: 0 5px;
}
.cart-summary {
  text-align: right;
  font-size: 1.5rem;
  margin-top: 20px;
}
.show-more-btn {
  background: none;
  border: none;
  color: blue;
  cursor: pointer;
  text-decoration: underline;
  font-size: 12px;
}
.remove-item {
  cursor: pointer;
}
.empty-cart-message {
  text-align: center;
  font-size: 1.2rem;
  margin-top: 20px;
}

.description-wrapper {
  max-width: 600px; /* Set a maximum width to prevent horizontal overflow */
  word-wrap: break-word; /* Ensure text breaks and wraps within the container */
}

.description-wrapper p {
  max-height: 4.5em; /* Limit the height to show a truncated version */
  overflow: hidden;
  line-height: 1.5em; /* Ensure consistent line height */
  transition: max-height 0.3s ease;
}

.description-wrapper p.expanded {
  max-height: none; /* Allow expansion when 'Show More' is clicked */
}
/* Mobile styling */
@media (max-width: 960px) {
  .order-table,
  .order-table thead,
  .order-table tr {
    display: block;
  }

  .order-table thead {
    display: none; /* Hide table headers */
  }
  .cart-item-image{
    margin-right: 1em;
  }
  .order-table tbody {
    display: block;
    width: 100%;
  }

  .order-table tr {
    display: flex;
    flex-direction: column;
    border: 1px solid #ddd;
    margin-bottom: 15px;
    padding: 10px;
    border-radius: 8px;
  }

  .order-table td {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    text-align: right;
    border: none;
  }

  /* Labels for mobile */
  .order-table td[data-label]::before {
    content: attr(data-label);
    font-weight: bold;
    text-align: left;
    width: 50%;
    display: inline-block;
  }
  .cart-item-image {
    width: 80px; /* Reduce image size for mobile view */
    height: auto;
  }

  .image_cell {
    width: auto;
    display: flex;
    justify-content: flex-end;
    padding-right: 8px;
  }

  /* Updated description truncation and alignment for mobile */
  .description-wrapper {
    max-width: 100%; /* Allows description to use full width on mobile */
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.5em;
    max-height: 3em; /* Shows only two lines of text initially */
   -webkit-box-orient: vertical;
  }

  .description-wrapper p.expanded {
    white-space: normal;
    max-height: none; /* Remove truncation when expanded */
  }
}

</style>
