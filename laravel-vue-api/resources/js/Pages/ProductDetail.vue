<template>
  <main-layout>
    <template v-slot:default>
      <div v-if="product" class="product-detail-page">
        <!-- Left: Large Product Image -->
        <div class="product-image-section">
          <img :src="productImage" alt="Product Image" class="product-image" />
        </div>

        <!-- Center: Product Details -->
        <div class="product-info-section">
          <h2 class="product-title">{{ product.name }}</h2>
          <div class="description-wrapper">
            <p>
              {{ showFullDescription ? product.description : truncatedDescription }}
              <button v-if="isDescriptionLong" @click="toggleDescription" class="show-more-btn">
                {{ showFullDescription ? 'Show Less' : 'Show More' }}
              </button>
            </p>
          </div>
          <p class="product-price">Price: €{{ product.price }}</p>

          <div class="product-specifications">
            <h3>Specifications:</h3>
            <table>
              <tr v-for="(value, key) in product.specifications" :key="key">
                <td>{{ key }}:</td>
                <td>{{ value }}</td>
              </tr>
            </table>
          </div>
        </div>

        <!-- Right: Action Buttons -->
        <div class="product-action-section">
          <div class="quantity-selector">
            <label for="quantity">Quantity:</label>
            <input type="number" v-model="quantity" min="1" :max="product.stock" />
          </div>

          <primary-button class="primary-button" @click="addToCart">
            Add to Cart
          </primary-button>

          <primary-button class="secondary-button" @click="notifyPriceDrop">
            Notify when price drops
          </primary-button>

          <primary-button class="tertiary-button" @click="askQuestion">
            Ask a Question
          </primary-button>
        </div>
      </div>
      <div v-else>
        <p>Loading...</p>
      </div>
    </template>
  </main-layout>
</template>

<script>
import MainLayout from "@/Layouts/MainLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

export default {
  props: ["id"],
  components: {
    MainLayout,
    PrimaryButton,
  },
  data() {
    return {
      product: null,
      quantity: 1,
      showFullDescription: false,
    };
  },
  computed: {
    productImage() {
      return this.product && this.product.image ? `/storage/${this.product.image}` : '/storage/no-image-icon.png';
    },
    truncatedDescription() {
      return this.product.description.length > 150
        ? this.product.description.substring(0, 150) + "..."
        : this.product.description;
    },
    isDescriptionLong() {
      return this.product.description.length > 150;
    },
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
        console.error("Failed to fetch product:", error);
      }
    },
    addToCart() {
      console.log(`Adding ${this.quantity} of product ID ${this.product.id} to cart.`);
    },
    notifyPriceDrop() {
      console.log("User subscribed to price drop notifications for this product.");
    },
    askQuestion() {
      console.log("User wants to ask a question about this product.");
    },
    toggleDescription() {
      this.showFullDescription = !this.showFullDescription;
    },
  },
};
</script>

<style scoped>
.product-detail-page {
  display: flex;
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
  gap: 20px;
}

.product-image-section {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
}

.product-image {
  max-width: 100%;
  height: auto;
  border-radius: 8px;
}

.product-info-section {
  flex: 2;
  padding: 20px;
}

.product-title {
  font-size: 1.8em;
  font-weight: bold;
  margin-bottom: 0.5em;
}

.product-price {
  font-size: 1.5em;
  color: #4a90e2;
  margin: 1em 0;
}

.description-wrapper {
  max-width: 600px;
  word-wrap: break-word;
}

.show-more-btn {
  background: none;
  border: none;
  color: blue;
  cursor: pointer;
  text-decoration: underline;
  font-size: 12px;
}

.product-specifications table {
  width: 100%;
  margin-top: 0.5em;
  border-collapse: collapse;
}

.product-specifications td {
  padding: 5px;
  border-bottom: 1px solid #ddd;
}

.product-action-section {
  flex: 1;
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.primary-button,
.secondary-button,
.tertiary-button {
  padding: 12px;
  font-size: 1em;
  text-align: center;
  cursor: pointer;
}

.primary-button {
  background-color: #007bff;
  color: #fff;
  border: none;
}

.secondary-button {
  background-color: #4a90e2;
  color: #fff;
  border: none;
}

.tertiary-button {
  background-color: #f5f5f5;
  color: #333;
  border: 1px solid #ddd;
}

/* Mobile styling */
@media (max-width: 960px) {
  .product-detail-page {
    flex-direction: column;
    align-items: center;
  }
  .product-image {
    width: 80%;
    max-width: 400px;
  }
  .description-wrapper {
    max-width: 100%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.5em;
    max-height: 3em;
  }
  .description-wrapper p.expanded {
    white-space: normal;
    max-height: none;
  }
}
</style>
