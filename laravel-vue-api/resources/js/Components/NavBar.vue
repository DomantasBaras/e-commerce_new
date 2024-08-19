<template>
  <nav class="navbar">
    <div class="left">
      <ApplicationLogo />
      <ul>
        <li><NavLink :href="route('home')">Home</NavLink></li>
        <li><NavLink :href="route('products')">Products</NavLink></li>
        <li><NavLink :href="route('cart')">Cart</NavLink></li>
      </ul>
    </div>

    <div class="right">
      <!-- Login Form -->
      <div v-if="!isLoggedIn">
        <form @submit.prevent="login" class="login-form">
          <TextInput v-model="form.email" type="email" placeholder="Email" />
          <TextInput v-model="form.password" type="password" placeholder="Password" />
          <Checkbox v-model:checked="form.remember" />
          <label>Remember me</label>
          <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Login</PrimaryButton>
        </form>
      </div>

      <!-- Conditional Links -->
      <div v-if="!isLoggedIn">
        <NavLink :href="route('register')">Register</NavLink>
      </div>
      <div v-else>
        <NavLink :href="route('profile.edit')">{{ props.auth.user.name }}</NavLink>
        <a href="#" @click.prevent="logout">Logout</a>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, computed, defineEmits } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import ApplicationLogo from './ApplicationLogo.vue';
import TextInput from './TextInput.vue';
import Checkbox from './Checkbox.vue';
import PrimaryButton from './PrimaryButton.vue';
import NavLink from './NavLink.vue';

const emit = defineEmits(['auth-changed']);

// Fetch user authentication status
const { props } = usePage();
const isLoggedIn = computed(() => !!props.auth?.user);

// Define form and methods
const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const login = () => {
  form.post(route('login'), {
    onFinish: () => {
      form.reset('password');
      emit('auth-changed'); // Notify parent component of auth change
      console.log('Login finished, isLoggedIn:', isLoggedIn.value, 'props.auth?.user:', props.auth?.user);
    },
    onError: (errors) => {
      console.error('Login error:', errors);
    }
  });
};

const logout = async () => {
  try {
    await form.post(route('logout'), {
      onFinish: () => {
        emit('auth-changed'); // Notify parent component of auth change
        console.log('Logout finished, isLoggedIn:', isLoggedIn.value, 'props.auth?.user:', props.auth?.user);
      },
    });
  } catch (error) {
    console.error('Logout error:', error);
  }
};
</script>

<style scoped>
.navbar {
  background-color: #4a4a4a;
  padding: 1rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.left {
  display: flex;
  align-items: center;
}

.left ul {
  list-style: none;
  display: flex;
  gap: 1rem;
}

.right {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.navbar a {
  color: white;
  text-decoration: none;
}

.login-form {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
</style>
