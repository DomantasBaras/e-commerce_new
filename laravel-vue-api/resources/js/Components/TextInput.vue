<script setup>
import { onMounted, ref } from 'vue';

const model = defineProps({
  modelValue: String,
  type: {
    type: String,
    default: 'text',
  },
  placeholder: {
    type: String,
    default: '',
  }
});

const emit = defineEmits(['update:modelValue']);

const input = ref(null);

const updateValue = (event) => {
  emit('update:modelValue', event.target.value);
};

onMounted(() => {
  if (input.value.hasAttribute('autofocus')) {
    input.value.focus();
  }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
  <input
    :type="type"
    :placeholder="placeholder"
    :value="modelValue"
    @input="updateValue"
    class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
    ref="input"
  />
</template>
