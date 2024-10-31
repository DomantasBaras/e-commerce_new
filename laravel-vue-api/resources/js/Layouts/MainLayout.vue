<template>
  <div class="layout-container">
    <NavBar :isLoggedIn="isLoggedIn" @auth-changed="handleAuthChanged" :key="authKey" />
    <main class="content">
      <slot :userId="userId" />
    </main>
    <Footer />
  </div>
</template>

<script setup>
import { ref, watch, provide } from 'vue';
import { usePage } from '@inertiajs/vue3';
import NavBar from '../Components/NavBar.vue';
import Footer from '../Components/Footer.vue';

const { props } = usePage();
const isLoggedIn = ref(!!props.auth?.user);
const userId = ref(props.auth?.user?.id || null);
const authKey = ref(Date.now());

// Provide userId to child components
provide('userId', userId);

// Watch for changes in authentication status
watch(
  () => props.auth?.user,
  (newValue) => {
    isLoggedIn.value = !!newValue;
    userId.value = newValue?.id || null;
    authKey.value = Date.now();
  }
);

const handleAuthChanged = () => {
  isLoggedIn.value = !!props.auth?.user;
  userId.value = props.auth?.user?.id || null;
  authKey.value = Date.now();
};
</script>

<style scoped>
html, body {
  height: 100%;
  margin: 0;
}

.layout-container {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  position: relative;
}

.content {
  flex: 1;
  padding: 20px;
  background-color: #f0f0f0;
  padding-bottom: 5%;
}

footer {
  text-align: center;
  padding: 10px;
  position: absolute;
}
/* Adjust padding for smaller screens */
@media (max-width: 768px) {
  .content {
    padding: 10px;
  }
}
/* Additional responsive styles for footer */
@media (max-width: 768px) {
  footer {
    padding: 15px;
  }
}

/* Navbar responsiveness */
nav {
  display: flex;
  justify-content: space-between;
  padding: 20px;
  background-color: #333;
  color: white;
}

@media (max-width: 768px) {
  nav {
    flex-direction: column;
    align-items: center;
  }
}
</style>
