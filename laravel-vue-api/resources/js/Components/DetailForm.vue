<template>
  <div class="form-container">
    <div class="form-overlay" @click="closeForm"></div>
    <div class="form-content">
      <button class="close-button" @click="closeForm">X</button>
      <img v-if="product" :src="product.image" alt="Product Image" class="form-image" />
      <form v-if="product" @submit.prevent="submitForm">
        <div class="form-group">
          <label for="name">Name:</label>
          <input type="text" id="name" :value="product.name" disabled />
        </div>
        <div class="form-group">
          <label for="description">Description:</label>
          <textarea id="description" :value="product.description" disabled></textarea>
        </div>
        <div class="form-group">
          <label for="price">Price:</label>
          <input type="text" id="price" :value="product.price" disabled />
        </div>
        <div class="form-group">
          <label for="stock">Stock:</label>
          <input type="text" id="stock" :value="product.stock" disabled />
        </div>
        <primary-button type="button" @click="closeForm">Close</primary-button>
      </form>
      <p v-else>Loading...</p>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import PrimaryButton from './PrimaryButton.vue';

export default {
  props: {
    productId: {
      type: Number,
      required: true,
    },
  },
  components: {
    PrimaryButton,
  },
  data() {
    return {
      product: null,
    };
  },
  mounted() {
    this.fetchProduct();
  },
  methods: {
    async fetchProduct() {
      console.log(`Fetching product with ID: ${this.productId}`);
      try {
        const response = await axios.get(`/api/products/${this.productId}`);
        console.log('Product data:', response.data);
        this.product = response.data;
      } catch (error) {
        console.error('Error fetching product:', error);
      }
    },
    closeForm() {
      this.$emit('close');
    },
    submitForm() {
      // Handle form submission logic if needed
      this.closeForm();
    },
  },
};
</script>

<style scoped>
.form-container {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.form-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
}

.form-content {
  background: #fff;
  padding: 20px;
  border-radius: 8px;
  position: relative;
  z-index: 1001;
  width: 90%;
  max-width: 500px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.form-image {
  max-width: 100%;
  height: auto;
  margin-bottom: 20px;
}

.close-button {
  position: absolute;
  top: 10px;
  right: 10px;
  background: none;
  border: none;
  font-size: 16px;
  cursor: pointer;
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
}

.form-group input,
.form-group textarea {
  width: 100%;
  padding: 8px;
  box-sizing: border-box;
}

.primary-button {
  display: block;
  width: 100%;
  text-align: center;
}
</style>
