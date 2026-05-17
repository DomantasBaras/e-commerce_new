<template>
  <MainLayout>
    <template v-slot:default>
      <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-6">Admin Dashboard</h1>
                <!-- Navigation Buttons -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-5 mb-6">
          <PrimaryButton
            v-for="(nav, index) in navigation"
            :key="index"
            :user="user"
            @click="navigateTo(nav.page) "
          >
            {{ nav.label }}
          </PrimaryButton>
        </div>
        <!-- Dashboard Widgets -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
          <div
            v-for="(widget, index) in widgets"
            :key="index"
            class="card p-6 rounded shadow hover:shadow-lg transition-shadow flex items-center"
          >
            <div :class="['icon-wrapper', widget.iconClass]">
              <i :class="['fas', widget.icon]"></i>
            </div>
            <div>
              <h5 class="text-2xl font-bold mb-1">{{ widget.value }}</h5>
              <h6 class="text-lg">{{ widget.label }}</h6>
            </div>
          </div>
        </div>
        <!-- Last Orders and Popular Products -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Last Orders Section -->
            <div class="col-span-2">
            <div class="h-100 mb-6">
                <div class="card-header flex justify-between items-center bg-gray-100 border-b px-6 py-4">
                <div>
                    <h4 class="text-xl font-bold">Last Orders</h4>
                    <small class="text-gray-500">Quick management of the last 10 orders</small>
                </div>
                <div>
                    <ul class="flex space-x-2">
                    <li>
                        <button class="py-1 px-3 rounded bg-gray-200 hover:bg-gray-300 text-gray-700">All</button>
                    </li>
                    <li>
                        <button class="py-1 px-3 rounded bg-gray-200 hover:bg-gray-300 text-gray-700">Waiting</button>
                    </li>
                    <li>
                        <button class="py-1 px-3 rounded bg-blue-500 text-white">Completed</button>
                    </li>
                    </ul>
                </div>
                </div>
                <div class="card-body p-6">
                <div class="overflow-y-auto" style="max-height: 375px;">
                    <table class="table-auto w-full">
                    <thead>
                        <tr class="bg-gray-200">
                        <th class="text-left py-2 px-4">Order Info</th>
                        <th class="text-right py-2 px-4">Amount</th>
                        <th class="text-right py-2 px-4">Payment</th>
                        <th class="text-right py-2 px-4">Status</th>
                        <th class="text-right py-2 px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(order, index) in lastOrders" :key="index" class="border-b hover:bg-gray-50">
                        <td class="py-2 px-4">
                            <a href="#" class="font-bold text-blue-600 hover:underline">{{ order.orderId }}</a>
                            <div class="text-sm text-gray-600">
                            <span class="font-bold">Receiver:</span>
                            <a href="#" class="hover:underline">{{ order.receiver }}</a>
                            </div>
                        </td>
                        <td class="text-right py-2 px-4">
                            <span class="font-bold text-gray-800">{{ order.amount }}</span>
                            <div class="text-sm text-gray-500">{{ order.paymentStatus }}</div>
                        </td>
                        <td class="text-right py-2 px-4 text-gray-600">{{ order.paymentMethod }}</td>
                        <td class="text-right py-2 px-4">
                            <span :class="order.statusClass">{{ order.status }}</span>
                        </td>
                        <td class="text-right py-2 px-4 space-x-2">
                            <button class="text-red-500 hover:bg-gray-100 p-1 rounded">
                            <i class="fas fa-trash"></i>
                            </button>
                            <button class="text-blue-500 hover:bg-gray-100 p-1 rounded">
                            <i class="fas fa-eye"></i>
                            </button>
                        </td>
                        </tr>
                    </tbody>
                    </table>
                </div>
                </div>
            </div>
            </div>

            <!-- Popular Products Section -->
            <div>
            <div class="card h-100">
                <div class="card-header flex justify-between items-center bg-gray-100 border-b px-6 py-4">
                <h4 class="text-xl font-bold">Popular Products</h4>
                <div class="smart-badge smart-badge-primary smart-badge-pill bg-blue-100 text-blue-600 px-2 py-1 rounded">Today</div>
                </div>
                <div class="card-body py-2">
                <div class="feeds">
                    <div
                    v-for="(product, index) in popularProducts"
                    :key="index"
                    class="row row-mx-5 hover:text-blue-600 border-b py-3 flex items-center"
                    >
                    <div class="col-auto mr-4">
                        <div class="img-as-background rounded-circle w-12 h-12 border overflow-hidden">
                        <img :src="getImageUrl(product.image)" alt="" class="w-full h-full object-cover" />
                        </div>
                    </div>
                    <div class="flex-grow">
                        <div class="font-bold">{{ product.name }}</div>
                        <small class="text-muted">{{ product.category }}</small>
                    </div>
                    <div class="col-auto text-right text-gray-600">
                        <small>{{ product.visits }} visits</small>
                    </div>
                    </div>
                </div>
                </div>
            </div>
            </div>
        </div>
      </div>
    </template>
  </MainLayout>
</template>

<script>
import MainLayout from "@/Layouts/MainLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import "@fortawesome/fontawesome-free/css/all.min.css";
// import { ref } from 'vue';
// import { usePage } from '@inertiajs/vue3';
// const { props } = usePage();
// const user = ref(props.auth?.user || null);
export default {
  components: {
    MainLayout,
    PrimaryButton,
  },
  data() {
    return {
      widgets: [
        {
          label: "Sales this Week",
          value: "744",
          icon: "fa-shopping-cart",
          iconClass: "text-blue-500 bg-blue-100",
        },
        {
          label: "Visitors this Week",
          value: "52,138",
          icon: "fa-chart-line",
          iconClass: "text-yellow-500 bg-yellow-100",
        },
        {
          label: "Earnings this Week",
          value: "€409,783",
          icon: "fa-dollar-sign",
          iconClass: "text-green-500 bg-green-100",
        },
        {
          label: "Pending Orders",
          value: "153",
          icon: "fa-cart-plus",
          iconClass: "text-indigo-500 bg-indigo-100",
        },
        {
          label: "Total Revenue",
          value: "€8,126,002",
          icon: "fa-money-bill-wave",
          iconClass: "text-red-500 bg-red-100",
        },
        {
          label: "Active Users",
          value: "1,234",
          icon: "fa-users",
          iconClass: "text-purple-500 bg-purple-100",
        },
        {
          label: "Low Stock Products",
          value: "12",
          icon: "fa-exclamation-triangle",
          iconClass: "text-orange-500 bg-orange-100",
        },
      ],
            lastOrders: [
        {
          orderId: "Order #12312",
          receiver: "John Doe",
          amount: "$2,320",
          paymentStatus: "Paid",
          paymentMethod: "PayPal",
          status: "Approved",
          statusClass: "bg-blue-100 text-blue-600 px-2 py-1 rounded text-sm",
        },
        {
          orderId: "Order #12313",
          receiver: "Teresa Clark",
          amount: "$1,500",
          paymentStatus: "Unpaid",
          paymentMethod: "Pay on Delivery",
          status: "Waiting",
          statusClass: "bg-yellow-100 text-yellow-600 px-2 py-1 rounded text-sm",
        },
        {
          orderId: "Order #12314",
          receiver: "Elijah Smith",
          amount: "$338",
          paymentStatus: "Refund",
          paymentMethod: "Sofort",
          status: "Canceled",
          statusClass: "bg-red-100 text-red-600 px-2 py-1 rounded text-sm",
        },
      ],
      navigation: [
        { label: "User Management", page: "user-management" },
        { label: "Product Management", page: "product-management" },
        { label: "Order Management", page: "order-management" },
        { label: "Role Management", page: "role-management" },
        { label: "View Logs", page: "logs" },
      ],
      popularProducts: [
        { name: "Black Luxury Lamp", category: "Home accessories", visits: 1452, image: "" },
        { name: "White chair", category: "Furnitures", visits: 1322, image: "" },
        { name: "Fresh Flower", category: "Home coziness", visits: 1113, image: "" },
        ],
    };
  },
  methods: {
    navigateTo(page) {
      this.$inertia.get(route(`admin.${page}`));
    },
        // Helper functions
     getImageUrl(imagePath){
      return imagePath ? `/storage/${imagePath}` : `/storage/no-image-icon.png`;
    }

  },
  
};
</script>

<style scoped>
/* Utility Classes for Last Orders */
.table-auto {
  width: 100%;
  border-collapse: collapse;
}
th,
td {
  border-bottom: 1px solid #e5e7eb;
}

.container {
  max-width: 1200px;
}

/* Card Styles */
.card {
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  display: flex;
  align-items: center;
}
.card:hover {
  transform: translateY(-3px);
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Icon Wrapper */
.icon-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 60px;
  height: 60px;
  border-radius: 50%;
  margin-right: 1rem;
  font-size: 24px;
}

.text-blue-500 {
  color: #3b82f6;
}
.bg-blue-100 {
  background-color: #e0f2fe;
}

.text-yellow-500 {
  color: #f59e0b;
}
.bg-yellow-100 {
  background-color: #fef3c7;
}

.text-green-500 {
  color: #10b981;
}
.bg-green-100 {
  background-color: #d1fae5;
}

.text-indigo-500 {
  color: #6366f1;
}
.bg-indigo-100 {
  background-color: #e0e7ff;
}

.text-red-500 {
  color: #ef4444;
}
.bg-red-100 {
  background-color: #fee2e2;
}

.text-purple-500 {
  color: #8b5cf6;
}
.bg-purple-100 {
  background-color: #f3e8ff;
}

.text-orange-500 {
  color: #f97316;
}
.bg-orange-100 {
  background-color: #ffedd5;
}
</style>
