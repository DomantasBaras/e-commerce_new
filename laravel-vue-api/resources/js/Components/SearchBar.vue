<template>
  <div class="search-bar">
    <!-- Input: Always active on desktop, collapses on mobile unless clicked -->
    <input 
      v-model="query" 
      @input="onInputDebounced" 
      @keydown.enter="search" 
      type="text" 
      class="search-input" 
      :class="{ active: isActive || isDesktop }"
      placeholder="Search..." 
      @blur="deactivateSearch"
    />

    <!-- Search Button: Uses FontAwesome search icon instead of text -->
    <button v-if="!isDesktop" @click="activateSearch" class="search-button">
      <i class="fas fa-search"></i> <!-- Search icon from FontAwesome -->
    </button>

    <!-- Suggestions Dropdown -->
    <div v-if="suggestions.length > 0" class="suggestions-dropdown">
      <ul>
        <li 
          v-for="suggestion in suggestions" 
          :key="suggestion.id" 
          @click="selectSuggestion(suggestion)">
          {{ suggestion.title }}
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { debounce } from 'lodash';
import axios from 'axios';
import { router } from '@inertiajs/vue3';

const query = ref('');
const suggestions = ref([]);
const isActive = ref(false);
const isDesktop = ref(window.innerWidth > 768);

// Detect window resize for desktop/mobile state
const handleResize = () => {
  isDesktop.value = window.innerWidth > 768;
};

// Add event listener to handle resizing
onMounted(() => {
  window.addEventListener('resize', handleResize);
});

// Debounced function to delay search suggestions
const onInputDebounced = debounce(async () => {
  if (query.value.length > 2) {
    try {
      const response = await axios.get('/api/search', { params: { query: query.value } });
      suggestions.value = response.data.suggestions;
    } catch (error) {
      console.error('Error fetching suggestions:', error);
      suggestions.value = [];
    }
  } else {
    suggestions.value = [];
  }
}, 500);

// Function to perform search using Inertia
const search = () => {
  if (query.value.length > 2) {
    router.get(route('search.results'), { query: query.value });
  }
};

// Function to handle selection of suggestion
const selectSuggestion = (suggestion) => {
  query.value = suggestion.title;
  suggestions.value = [];
  search(); // Navigate to the results page with the selected suggestion
};

// Activate the search input field
const activateSearch = () => {
  isActive.value = true;
  setTimeout(() => {
    document.querySelector('.search-input').focus();
  }, 100);
};

// Deactivate the search input when it loses focus
const deactivateSearch = () => {
  if (query.value.length === 0) {
    isActive.value = false;
  }
};
</script>


<style scoped>
.search-bar {
  position: relative;
  display: flex;
  align-items: center;
  width: 100%; /* Full width of its container */
  max-width: 1000px; /* Max-width for larger screens */
}

.search-input {
  width: 0;
  height: 40px;
  padding: 0;
  border: 1px solid transparent;
  border-radius: 4px 4px 4px 4px;
  background-color: transparent;
  color: #333;
  transition: width 0.5s ease, padding 0.5s ease, border-color 0.3s ease;
  outline: none;
  overflow: hidden;
}

.search-input.active {
  width: 60%; /* Adjust this for the input size when active */
  padding: 8px 12px;
  border: 1px solid #ddd;
  background-color: #f5f5f5;
  color: #333;
}

.search-input:focus {
  border-color:#999; 
}

/* Always active search input on desktop */
@media (min-width: 768px) {
  .search-input {
    width: 60%; /* Default expanded size on desktop */
    padding: 8px 12px;
    border: 1px solid #ddd;
    background-color: #f5f5f5;
    color: #333;
  }
}

.search-button {
  height: 40px;
  padding: 8px 16px;
  border: 1px solid #ddd;
  background-color: #4a4a4a;
  color: white;
  cursor: pointer;
  border-radius: 4px;
  transition: background-color 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.search-button i {
  font-size: 16px;
}

/* Hide the search button on desktop */
@media (min-width: 768px) {
  .search-button {
    display: none;
  }
}

/* Suggestions Dropdown */
.suggestions-dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  border: 1px solid #ddd;
  background: white;
  z-index: 1000;
  width: 60% ;
  max-height: 200px;
  overflow-y: auto;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  border-radius: 4px;
  color: #000;
}

.suggestions-dropdown ul {
  list-style-type: none;
  padding: 0;
  margin: 0;
}

.suggestions-dropdown li {
  padding: 8px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.suggestions-dropdown li:hover {
  background: #999; 
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .search-input.active {
    width: 80%;
  }
}

@media (max-width: 480px) {
  .search-input.active {
    width: 100%;
  }
}


</style>


