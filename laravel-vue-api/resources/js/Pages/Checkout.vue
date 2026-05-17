<template>
  <main-layout>
    <template v-slot:default>
      <div class="checkout-container">
        <h2>Checkout</h2>

        <div v-if="loading" class="loading-text">Loading cart...</div>

        <div v-else-if="isEmpty" class="empty-cart-message">
          <p>Your cart is empty. <a :href="route('home')">Continue shopping</a></p>
        </div>

        <div v-else class="checkout-layout">
          <!-- Delivery & Payment form -->
          <div class="checkout-form-section">
            <h3>Delivery Details</h3>
            <form @submit.prevent="submitOrder">
              <div class="form-group">
                <label for="address">Delivery Address</label>
                <textarea
                  id="address"
                  v-model="form.address"
                  rows="3"
                  placeholder="Street, City, Postal Code, Country"
                  class="form-input"
                  :class="{ 'input-error': errors.address }"
                ></textarea>
                <span v-if="errors.address" class="error-msg">{{ errors.address }}</span>
              </div>

              <h3>Payment Method</h3>
              <div class="payment-options">
                <label
                  v-for="option in paymentOptions"
                  :key="option.value"
                  class="payment-option"
                  :class="{ selected: form.paymentMethod === option.value }"
                >
                  <input
                    type="radio"
                    :value="option.value"
                    v-model="form.paymentMethod"
                  />
                  <span class="payment-label">{{ option.label }}</span>
                </label>
                <span v-if="errors.paymentMethod" class="error-msg">{{ errors.paymentMethod }}</span>
              </div>

              <primary-button type="submit" :disabled="submitting" class="place-order-btn">
                {{ submitting ? 'Placing order...' : 'Place Order' }}
              </primary-button>

              <p v-if="serverError" class="error-msg server-error">{{ serverError }}</p>
            </form>
          </div>

          <!-- Order summary sidebar -->
          <div class="order-summary-section">
            <h3>Order Summary</h3>
            <ul class="summary-items">
              <li v-for="item in cart.items" :key="item.product_id" class="summary-item">
                <span class="summary-name">{{ item.product.name }} &times; {{ item.quantity }}</span>
                <span class="summary-price">{{ (item.product.price * item.quantity).toFixed(2) }} €</span>
              </li>
            </ul>
            <div class="summary-total">
              <strong>Total</strong>
              <strong>{{ cartTotal }} €</strong>
            </div>
          </div>
        </div>
      </div>
    </template>
  </main-layout>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useCartStore } from '@/Stores/cartStore';

export default {
  components: { MainLayout, PrimaryButton },

  setup() {
    const { props } = usePage();
    const userId = computed(() => props.auth?.user?.id || null);

    const cartStore = useCartStore();
    const cart = computed(() => cartStore.cart);
    const loading = computed(() => cartStore.loading);
    const isEmpty = computed(() => cartStore.isEmpty);
    const cartTotal = computed(() => cartStore.cartTotal);

    const form = ref({
      address: '',
      paymentMethod: 'credit_card',
    });

    const errors = ref({});
    const submitting = ref(false);
    const serverError = ref('');

    const paymentOptions = [
      { value: 'credit_card', label: 'Credit Card' },
      { value: 'paypal', label: 'PayPal' },
      { value: 'cash_on_delivery', label: 'Cash on Delivery' },
    ];

    onMounted(() => {
      cartStore.fetchCart(userId.value);
    });

    const validate = () => {
      errors.value = {};
      if (!form.value.address.trim()) {
        errors.value.address = 'Delivery address is required.';
      }
      if (!form.value.paymentMethod) {
        errors.value.paymentMethod = 'Please select a payment method.';
      }
      return Object.keys(errors.value).length === 0;
    };

    const submitOrder = async () => {
      serverError.value = '';
      if (!validate()) return;

      submitting.value = true;
      try {
        const order = await cartStore.placeOrder(
          userId.value,
          form.value.address,
          form.value.paymentMethod,
        );
        router.visit(route('order.show', { id: order.id }));
      } catch (error) {
        serverError.value = error.response?.data?.error || 'Failed to place order. Please try again.';
      } finally {
        submitting.value = false;
      }
    };

    return {
      cart,
      loading,
      isEmpty,
      cartTotal,
      form,
      errors,
      submitting,
      serverError,
      paymentOptions,
      submitOrder,
      route,
    };
  },
};
</script>

<style scoped>
.checkout-container {
  max-width: 1100px;
  margin: 0 auto;
  padding: 24px 16px;
}

.checkout-container h2 {
  font-size: 1.8rem;
  margin-bottom: 24px;
}

.checkout-layout {
  display: flex;
  gap: 40px;
  align-items: flex-start;
}

.checkout-form-section {
  flex: 1;
}

.checkout-form-section h3,
.order-summary-section h3 {
  font-size: 1.2rem;
  margin-bottom: 16px;
  padding-bottom: 8px;
  border-bottom: 1px solid #ddd;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  font-weight: 600;
  margin-bottom: 6px;
}

.form-input {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 0.95rem;
  resize: vertical;
  box-sizing: border-box;
}

.form-input:focus {
  outline: none;
  border-color: #666;
}

.input-error {
  border-color: #e53e3e;
}

.error-msg {
  color: #e53e3e;
  font-size: 0.85rem;
  margin-top: 4px;
  display: block;
}

.server-error {
  margin-top: 12px;
  font-size: 0.95rem;
}

.payment-options {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-bottom: 24px;
}

.payment-option {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 16px;
  border: 1px solid #ccc;
  border-radius: 8px;
  cursor: pointer;
  transition: border-color 0.2s, background-color 0.2s;
}

.payment-option.selected {
  border-color: #333;
  background-color: #f9f9f9;
}

.payment-option input[type="radio"] {
  accent-color: #333;
}

.payment-label {
  font-size: 0.95rem;
}

.place-order-btn {
  width: 100%;
  margin-top: 8px;
}

/* Order summary sidebar */
.order-summary-section {
  width: 320px;
  flex-shrink: 0;
  background: #f9f9f9;
  border: 1px solid #ddd;
  border-radius: 10px;
  padding: 20px;
}

.summary-items {
  list-style: none;
  padding: 0;
  margin: 0 0 16px 0;
}

.summary-item {
  display: flex;
  justify-content: space-between;
  padding: 8px 0;
  border-bottom: 1px solid #eee;
  font-size: 0.9rem;
}

.summary-name {
  color: #444;
}

.summary-price {
  font-weight: 600;
}

.summary-total {
  display: flex;
  justify-content: space-between;
  font-size: 1.05rem;
  margin-top: 12px;
}

.empty-cart-message {
  text-align: center;
  font-size: 1.1rem;
  margin-top: 40px;
}

.empty-cart-message a {
  color: #333;
  text-decoration: underline;
}

.loading-text {
  text-align: center;
  margin-top: 40px;
  font-size: 1rem;
  color: #666;
}

@media (max-width: 768px) {
  .checkout-layout {
    flex-direction: column-reverse;
  }

  .order-summary-section {
    width: 100%;
    box-sizing: border-box;
  }
}
</style>
