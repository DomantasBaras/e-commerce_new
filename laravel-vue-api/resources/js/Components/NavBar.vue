<template>
  <nav class="navbar">

    <!-- Hamburger menu for mobile -->
    <div class="mobile-hamburger" @click="toggleMobileMenu">
      <i class="fas fa-bars"></i>
    </div>

    <div class="left">
      <ApplicationLogo hidden />
      <ul :class="['mobile-menu', { 'is-active': isMobileMenuOpen }]">
        <li><ResponsiveNavLink :href="route('home')"><i class="fas fa-home"></i> Home</ResponsiveNavLink></li>
        <li><ResponsiveNavLink :href="route('home')"><i class="fas fa-shopping-bag"></i> Shop</ResponsiveNavLink></li>
        <li><ResponsiveNavLink :href="route('home')"><i class="fas fa-info-circle"></i> About</ResponsiveNavLink></li>
        <li><ResponsiveNavLink :href="route('home')"><i class="fas fa-envelope"></i> Contact</ResponsiveNavLink></li>
      </ul>
    </div>

    <!-- SearchBar Component -->
    <div class="center">
      <SearchBar @search="handleSearch" />
    </div>

    <div class="right">
      <!-- Cart Dropdown -->
      <div class="dropdown cart-dropdown">
        <a class="cart-icon">
          <i class="fas fa-shopping-cart"></i>
          <!-- Use cartItemsCount for the total quantity -->
          <span class="cart-counter">{{ cartItemsCount }}</span>
        </a>
        <div class="dropdown-content"   >
          <div v-if="cartItems.length > 0" class="cart-items">
            <!-- Loop through cart items -->
            <div v-for="item in cartItems" :key="item.product_id" class="cart-item">
              <!-- Product Image -->
              <div class="cart-item__media" @click="openProductPage(item.product_id)">
                <img :src="getImageUrl(item.product.image)" alt="Product Image" />
              </div>

              <!-- Product Name with Quantity -->
              <a :href="`/products/${item.product_id}`" class="cart-item__name">
                {{ item.product.name }} (x{{ item.quantity }}) <!-- Use item.quantity -->
              </a>

              <!-- Price -->
              <div class="cart-item__controls">
                <div class="cart-item__price">
                  {{ Math.floor(item.product.price) }}<sup>{{ (item.product.price % 1).toFixed(2).split('.')[1] }}</sup> <small>€</small>
                </div>
                <!-- Remove Item Button -->
                <i class="cart-item__remove fas fa-times" @click="removeItem(item.product_id)" title="Remove"></i>
              </div>
            </div>

            <!-- View Cart Button -->
            <a :href="route('cart')" class="view-cart-link">View Cart</a>
          </div>
          <div v-else class="empty-cart">
            <p>Your cart is empty.</p>
          </div>
        </div>
      </div>

      <!-- Profile/Login Dropdown -->
      <div class="dropdown profile-dropdown">
        <a class="profile-icon">
          <i class="fas fa-user"></i>
        </a>
        <div class="dropdown-content">
          <div v-if="!isLoggedIn" class="login-section">
            <div class="clearfix">
              <a href="/auth/facebook" class="user-block-dropdown__social-button user-block-dropdown__social-button--fb">Facebook</a>
              <a href="/auth/google_oauth2" class="user-block-dropdown__social-button user-block-dropdown__social-button--gp">Google</a>
            </div>
            <a :href="route('login')" class="login-link">Login</a>
            <a :href="route('register')" class="register-link">Register</a>
          </div>
          <div v-else class="user-menu">
            <a :href="route('profile.edit')"><i class="fas fa-user-circle"></i> {{ props.auth.user.name }}</a>
            <a href="#" @click.prevent="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, computed, defineEmits, onMounted } from 'vue';
import { usePage, useForm } from '@inertiajs/vue3';
import ApplicationLogo from './ApplicationLogo.vue';
import ResponsiveNavLink from './ResponsiveNavLink.vue';
import SearchBar from './SearchBar.vue';
import { useCartStore } from '../Stores/cartStore';

const cartStore = useCartStore();
const { props } = usePage();
const emit = defineEmits(['auth-changed']);
const userId = ref(usePage().props.auth?.user?.id || null);

// Computed properties for cart
const cartItems = computed(() => cartStore.cart.items || []);
const cartItemsCount = computed(() => cartStore.cartItemsCount); // Use cartItemsCount getter

const isLoggedIn = computed(() => !!usePage().props.auth?.user);

// Method to get image URL
const getImageUrl = (imagePath) => {
  return imagePath ? `/storage/${imagePath}` : '/storage/no-image-icon.png';
};

// Fetch cart on mount
const fetchCart = async () => {
  await cartStore.fetchCart(userId.value);
};

const removeItem = async (cartItemId) => {
  try {
    await cartStore.removeItemFromCart(userId.value, cartItemId);
  } catch (error) {
    console.error('Error removing item:', error);
  }
};

// Handle logout
const logout = async () => {
  try {
    await form.post(route('logout'), {
      onFinish: () => {
        emit('auth-changed');
      },
    });
  } catch (error) {
    console.error('Logout error:', error);
  }
};

const handleSearch = (query) => {
  console.log('Search query:', query);
};

// Mobile menu state
const isMobileMenuOpen = ref(false);

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value;
};


// Reactive state for controlling cart dropdown visibility
const isCartDropdownVisible = ref(false);

// Function to show cart dropdown and hide it after a delay
const showCartDropdown = () => {
  isCartDropdownVisible.value = true;

  // Hide the cart dropdown after 3 seconds (adjust as needed)
  setTimeout(() => {
    isCartDropdownVisible.value = false;
  }, 3000);
};
// Fetch cart on component mount
onMounted(() => {
  fetchCart();
});
</script>

<style scoped>

/* Hamburger Icon - Visible only on mobile */
.mobile-hamburger {
  display: none;
  color: white;
  font-size: 2rem;
  cursor: pointer;
}
/* Navbar and Dropdown Styling */
.navbar {
  background-color: #4a4a4a;
  padding: 1rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}


.left ul {
  list-style: none;
  display: flex;
  gap: 1rem;
}

.left {
  display: flex;
  align-items: center;
}

.center {
  flex: 1;
  display: flex;
  justify-content: center;
}

.right {
  display: flex;
  align-items: center;
  gap: 2rem; /* Adjust the gap */
}

.cart-icon, .profile-icon {
  color: white;
  font-size: 1.5rem;
  cursor: pointer;
  position: relative;
}

.cart-counter {
  position: absolute;
  top: -10px;
  right: -10px;
  background-color: red;
  color: white;
  border-radius: 50%;
  padding: 0.2rem 0.5rem;
  font-size: 0.8rem;
}

/* Dropdown Styles */
.dropdown {
  position: relative;
  display: inline-block;
}

.dropdown-content {
  display: none;
  position: absolute;
  background-color: #fff;
  min-width: 200px;
  box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
  padding: 1rem;
  z-index: 1;
  border-radius: 5px;
}

.dropdown:hover .dropdown-content {
  display: block;
}

.cart-dropdown .dropdown-content {
  right: 0;
  background-color: #4a4a4a;
}

.profile-dropdown .dropdown-content {
  right: 0;
  background-color: #fff;
}

.cart-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 10px;
}

.cart-item__media img {
  width: 40px;
  height: 40px;
  object-fit: cover;
  margin-right: 10px;
}

.cart-item__name {
  flex-grow: 1;
  color: #000;
  text-decoration: none;
  font-size: 0.9rem;
}

.cart-item__controls {
  display: flex;
  align-items: center;
}

.cart-item__price {
  font-size: 1rem;
  margin-right: 10px;
}

.cart-item__price sup {
  font-size: 0.7rem;
}

.cart-item__remove {
  cursor: pointer;
  color: #ff0000;
  font-size: 1.2rem;
}

.view-cart-link {
  display: block;
  margin-top: 10px;
  text-align: center;
  background-color: #2375d8;
  color: white;
  padding: 5px 10px;
  border-radius: 5px;
  text-decoration: none;
}

.view-cart-link:hover {
  background-color: #1e63b8;
}


.empty-cart {
  text-align: center;
  color: #888;
}

/* User Login Styles */
.login-section {
  padding: 1rem;
}
.user-menu {
  padding: 1rem;
  color: #000;
}

.login-link, .register-link {
  display: block;
  padding: 0.5rem;
  color: #2375D8;
  text-align: center;
}

.login-link:hover, .register-link:hover {
  text-decoration: underline;
}

.user-block-dropdown__social-button {
  padding: 0.5rem;
  color: white;
  text-align: center;
  display: block;
  border-radius: 4px;
  margin-bottom: 0.5rem;
}

.user-block-dropdown__social-button--fb {
  background-color: #3b5998;
}

.user-block-dropdown__social-button--gp {
  background-color: #db4437;
}

/* Media Query for Mobile Screens */
@media screen and (max-width: 768px) {
  .navbar {
    flex-direction: row;
    padding: 0.5rem;
  }

  .left ul {
    display: none;
  }
  /* Ensure list items on mobile take full width */
  .left li {
    margin: -2px 0 0 -2px;
    width: calc(100% - 20px);
    line-height: 1.7;
    padding: 0.5rem;
    cursor: pointer;
}
  /* Show hamburger on mobile */
  .mobile-hamburger {
    display: block;
    padding: 0.5rem;
  }

  .center input {
    width: 100%;  /* Ensure full-width search bar on mobile */
  }
  .center {
    order: 2;
    width: 100%;
    justify-content: center;
  }
  .left {
    order: 1;
    justify-content: left;
  }
  .right {
    padding: 0.5rem;
    order:3;
    justify-content: end;
  }
  /* Mobile menu visibility */
  .mobile-menu {
    display: block;
  }

  .mobile-menu.is-active {
    position: absolute;
    background-color: #fff;
    min-width: 200px;
    box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
    padding: 1rem;
    z-index: 1;
    border-radius: 5px;
    display:block;
    margin-top: 2%;
    top: 60px;
    left: 0;
    overflow-y: auto;
  }
}
</style>
