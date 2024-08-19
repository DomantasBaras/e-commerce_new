<template>
  <button
    :class="['cart-button', { 'is-pressed': isPressed }]"
    :disabled="disabled"
  >
    <i class="fas fa-shopping-cart"></i>
    <span><slot></slot></span>
  </button>
</template>

<script>
export default {
  props: {
    disabled: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      isPressed: false,
    };
  },
};
</script>

<style scoped>
.cart-button {
  padding: 10px 20px;
  border: none;
  background-color: #555; /* Grey color */
  color: white;
  border-radius: 5px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform 0.1s ease, background-color 0.3s ease; /* Add transition for smooth animation */
}
.cart-button i {
  margin-right: 5px; /* Add some space between the icon and text */
}
.cart-button.is-pressed {
  transform: scale(0.95); /* Slightly shrink the button to indicate press */
  background-color: #333; /* Change color to indicate press */
}
.cart-button[disabled] {
  background-color: #a9a9a9;
  cursor: not-allowed;
}
</style>
