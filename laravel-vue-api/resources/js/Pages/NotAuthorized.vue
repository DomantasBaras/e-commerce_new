<template>
    <main-layout>
        <div class="container mx-auto p-6">
            <h1 class="text-2xl font-bold mb-4">Access Denied</h1>
            <p>You do not have permission to view this page.</p>
            <PrimaryButton @click="returnHome()" class="text-blue-600">Return to Homepage</PrimaryButton>
        </div>
  </main-layout>
</template>
<script>

import MainLayout from '@/Layouts/MainLayout.vue';
import PrimaryButton from '../Components/PrimaryButton.vue';

export default {
  components: {
    MainLayout,
    PrimaryButton
  },
  methods: {
    returnHome() {
        this.$inertia.get(`/`);
    }
  },
};
</script>
