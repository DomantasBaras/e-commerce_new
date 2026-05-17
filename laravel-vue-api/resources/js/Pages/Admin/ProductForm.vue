<template>
  <MainLayout>
    <template v-slot:default>
      <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">{{ product.id ? 'Edit' : 'Create' }} Product</h1>

        <form @submit.prevent="saveProduct" class="bg-white p-6 rounded-lg shadow-md">
          <!-- Product Name -->
          <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Product Name</label>
            <input v-model="product.name" id="name" type="text" required
              class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"/>
          </div>

          <!-- Product Price -->
          <div class="mb-4">
            <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
            <input v-model="product.price" id="price" type="number" required
              class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"/>
          </div>

          <div class="mb-4">
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea v-model="product.description" id="description" type="text" required
              class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"/>
          </div>
          <div class="mb-4">
            <label for="stock" class="block text-sm font-medium text-gray-700">Stock</label>
            <input v-model="product.stock" id="stock" type="number" required
              class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"/>
          </div>          
          <!-- Product Image -->
          <div class="mb-4">
            <label for="image" class="block text-sm font-medium text-gray-700">Image</label>
            
            <!-- Image Preview -->
            <div v-if="product.image" class="mb-2">
              <img :src="getImageUrl(product.image)" alt="Product Image" class="w-32 h-32 object-cover rounded-md" />
            </div>

            <input @change="handleFileUpload" type="file" accept="image/png, image/jpeg"
              class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"/>
          </div>

          <!-- Submit Button -->
          <div>
            <PrimaryButton type="submit" class="w-full">
              {{ product.id ? 'Update' : 'Create' }} Product
            </PrimaryButton>
          </div>
        </form>
      </div>
    </template>
  </MainLayout>
</template>

<script>
import axios from "axios";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import MainLayout from "@/Layouts/MainLayout.vue";
import { usePage } from "@inertiajs/vue3";
import {computed} from 'vue';
    const page = usePage()
    const userName =  computed(() => page?.props?.auth?.user?.name || null);

export default {
  components: {
    PrimaryButton,
    MainLayout,
  },
  props: {
    product: {
      type: Object,
      default: () => ({ name: "", price: 0, description: "", stock: 0, image: null }),
    },
  },
  data() {
    return {
      selectedFile: null,
    };
  },
  methods: {
    getImageUrl(imagePath) {
      return imagePath ? `/storage/${imagePath}` : "/storage/no-image-icon.png";
    },

    handleFileUpload(event) {
      const file = event.target.files[0];
      if (file && (file.type === "image/png" || file.type === "image/jpeg")) {
        this.selectedFile = file;
      } else {
        alert("Please upload a valid image file (PNG, JPG, or JPEG)");
        event.target.value = ""; // Reset the input if invalid
      }
    },

    async saveProduct() {
      try {
        const productData = {
          name: this.product.name,
          description: this.product.description,
          price: this.product.price,
          stock: this.product.stock,
        };

        const method = this.product.id ? "put" : "post";
        const url = this.product.id ? `/api/products/${this.product.id}` : "/api/products";

        const { data } = await axios({
          method,
          url,
          data: productData,
          headers: {
            userName: userName.value
          }
        });
        if (this.selectedFile) {
          await this.uploadProductImage(data.id);
        }
      } catch (error) {
        console.error("Error saving product:", error.response?.data || error.message);
      }
    },

    async uploadProductImage(productId) {
      try {
        const formData = new FormData();
        formData.append("image", this.selectedFile);

        const { data } = await axios.post(`/api/products/${productId}/image`, formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        });
      } catch (error) {
        console.error("Error uploading image:", error.response?.data || error.message);
      }
    },
  },
};
</script>



<style scoped>
.container {
  max-width: 800px; /* Limit form width */
}

form {
  background-color: #fff;
  border-radius: 10px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

label {
  font-weight: 600;
}

input {
  padding-left: 12px;
  padding-right: 12px;
  font-size: 1rem;
  border-radius: 5px;
  transition: all 0.3s;
}

input:focus {
  border-color: #6b7280;
  outline: none;
}

PrimaryButton {
  background-color: #007bff;
  color: white;
}
</style>
