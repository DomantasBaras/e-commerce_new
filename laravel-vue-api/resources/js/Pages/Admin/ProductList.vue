<template>
  <MainLayout>
    <template v-slot:default>
      <div class="container">
        <h1>Export/Import products</h1>

        <!-- Export products -->
        <div class="mb-4">
          <PrimaryButton @click="downloadProducts()">Export products</PrimaryButton>
        </div>

        <!-- Import products -->
        <form @submit.prevent="importProducts">
          <div class="mb-4">
            <input type="file" ref="file" accept=".csv" />
          </div>
          <button type="submit" class="btn btn-success">Import products</button>
        </form>
      </div>
      <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Manage Products</h1>

        <!-- Add Product Button -->

        <PrimaryButton @click="openCreateForm()" class="mb-4">
          Add New Product
        </PrimaryButton>


        <!-- Product Table -->
        <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
          <thead class="bg-gray-100">
            <tr>
              <th class="py-3 px-6 text-left">Name</th>
              <th class="py-3 px-6 text-left">Price</th>
              <th class="py-3 px-6 text-left">Stock</th>
              <th class="py-3 px-6 text-left">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="product in products" :key="product.id" class="border-t">
              <td class="py-3 px-6">{{ product.name }}</td>
              <td class="py-3 px-6">€{{ product.price }}</td>
              <td class="py-3 px-6">{{ product.stock }}</td>
              <td class="py-3 px-6">
                <PrimaryButton @click="openEditForm(product)"  class="text-indigo-600 hover:text-indigo-800 mr-4">
                  Edit
                </PrimaryButton>
                <PrimaryButton @click="deleteProduct(product.id)" class="text-red-600 hover:text-red-800">
                  Delete
                </PrimaryButton>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </template>
  </MainLayout>
</template>

<script>
import DropdownLink from '@/Components/DropdownLink.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import axios from 'axios';
export default {
  components: {
    PrimaryButton,
    MainLayout,
    DropdownLink,
  },
  data() {
    return {
      products: [],
    };
  },
  async created() {
    const response = await fetch('/api/products', {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
    });
    this.products = await response.json();
  },
  methods: {
    openEditForm(product) {
      this.$inertia.get(route('admin.products.edit', product));
    },
    downloadProducts() {
      axios.get(route('products.export'));
    },
    openCreateForm(){
      this.$inertia.get(route('admin.products.create'));
    },
    async deleteProduct(id) {
      await fetch(`/api/products/${id}`, {
        method: 'DELETE',
        headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
      });
      this.products = this.products.filter(product => product.id !== id);
    },
    async importProducts() {
      const file = this.$refs.file.files[0];
      if (!file) return alert('Please select a file.');

      const formData = new FormData();
      formData.append('file', file);

      await fetch(route('products.import'), {
        method: 'POST',
        body: formData,
      }).then(() => alert('Import successful!'));
    },
  }
};
</script>

<style scoped>
/* Styling for the product list table */
.container {
  max-width: 1000px;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th,
td {
  padding: 12px;
  text-align: left;
}

th {
  background-color: #f4f4f4;
}


</style>
