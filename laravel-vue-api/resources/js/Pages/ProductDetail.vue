<template>
  <main-layout>
    <template v-slot:default>
      <div v-if="product" class="product-detail">
        <h2 class="product-title">{{ product.name }}</h2>
        <img class="product-image" :src="productImage" alt="Product Image" />
        <div class="product-info">
          <p>{{ product.description }}</p>
          <p class="product-price">Price: ${{ product.price }}</p>
          <p class="product-stock">Stock: {{ product.stock }}</p>
        </div>
        <form @submit.prevent="updateProductData" enctype="multipart/form-data">
          <label>
            Name:
            <input v-model="product.name" type="text" />
          </label>
          <label>
            Description:
            <input v-model="product.description" type="text" />
          </label>
          <label>
            Price:
            <input v-model="product.price" type="number" />
          </label>
          <label>
            Stock:
            <input v-model="product.stock" type="number" />
          </label>
          <label>
            Image:
            <input @change="handleFileUpload" type="file" />
          </label>
          <button type="submit">Update Product</button>
        </form>
      </div>
      <div v-else>
        <p>Loading...</p>
      </div>
    </template>
  </main-layout>
</template>

<script>
import MainLayout from '@/Layouts/MainLayout.vue';
import axios from 'axios';
export default {
  props: ['id'],
  components: {
    MainLayout,
  },
  data() {
    return {
      product: null,
      selectedFile: null,
      errorMessage: null
    };
  },
  computed: {
    productImage() {
      return this.product && this.product.image ? `/storage/${this.product.image}` : '';
    }
  },
  created() {
    this.fetchProduct();
  },
  methods: {
    async fetchProduct() {
      try {
        const response = await fetch(`/api/products/${this.id}`);
        if (!response.ok) throw new Error(`Failed to fetch product: ${response.statusText}`);

        this.product = await response.json();
      } catch (error) {
        console.error('Failed to fetch product:', error);
      }
    },
    handleFileUpload(event) {
      this.selectedFile = event.target.files[0];
    },
   
    async updateProductData() {
        try {
            // Prepare the product data
            const productData = {
                name: this.product.name,
                description: this.product.description,
                price: this.product.price,
                stock: this.product.stock
            };

            // Send the PUT request with JSON data
            const response = await fetch(`/api/products/${this.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(productData)
            });

            const data = await response.json();
            console.log('Product data updated successfully:', data);

            // If there is a selected file, proceed to update the image
            if (this.selectedFile) {
                await this.updateProductImage();
            }
        } catch (error) {
            console.error('Error updating product data:', error);
        }
    },
    async updateProductImage() {
    try {
        // Create a FormData object to handle file upload
        const formData = new FormData();
        formData.append('image', this.selectedFile);
        console.log('formData values:', formData);
        // Send the POST request to update the product image
        const response = await fetch(`/api/products/${this.id}/image`, {
            method: 'POST',
            body: formData
        });

        const data = await response.json();
        console.log('Product image updated successfully:', data);
    } catch (error) {
        console.error('Error updating product image:', error);
    }
}


  }
};
</script>


<style scoped>
/* Your styles remain the same */
.product-detail {
  padding: 20px;
  background-color: #ffffff;
  border: 1px solid #e0e0e0;
  border-radius: 10px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  transition: box-shadow 0.3s ease;
  max-width: 800px;
  margin: 20px auto;
}

.product-detail:hover {
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.product-title {
  margin: 0 0 20px;
  font-size: 2em;
  font-weight: bold;
  text-align: center;
}

.product-image {
  width: 100%;
  height: auto;
  display: block;
  margin: 0 auto 20px;
  border-radius: 10px;
}

.product-info {
  text-align: center;
}

.product-info p {
  margin: 10px 0;
}

.product-price {
  font-size: 1.5em;
  color: #4a90e2;
}

.product-stock {
  font-size: 1.2em;
  color: #7b7b7b;
}
</style>
