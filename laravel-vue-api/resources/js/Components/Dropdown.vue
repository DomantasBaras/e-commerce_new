<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  align: {
    type: String,
    default: 'right',
  },
  width: {
    type: String,
    default: '48',
  },
  contentClasses: {
    type: String,
    default: 'py-1 bg-white dark:bg-gray-700',
  },
});

const open = ref(false);
let closeTimeout = null;

const showDropdown = () => {
  clearCloseTimeout();
  open.value = true;
  window.dispatchEvent(new CustomEvent('dropdown-opened', { detail: { dropdownId: props.dropdownId } }));
};

const hideDropdown = () => {
  closeTimeout = setTimeout(() => {
    open.value = false;
    window.dispatchEvent(new CustomEvent('dropdown-closed', { detail: { dropdownId: props.dropdownId } }));
  }, 150);
};

const cancelHide = () => {
  clearCloseTimeout();
};

const clearCloseTimeout = () => {
  if (closeTimeout) {
    clearTimeout(closeTimeout);
  }
};

const widthClass = computed(() => {
  return {
    48: 'w-48',
  }[props.width.toString()];
});

const alignmentClasses = computed(() => {
  if (props.align === 'left') {
    return 'ltr:origin-top-left rtl:origin-top-right start-0';
  } else if (props.align === 'right') {
    return 'ltr:origin-top-right rtl:origin-top-left end-0';
  } else {
    return 'origin-top';
  }
});

const handleDropdownClosed = (event) => {
  if (event.detail.dropdownId !== props.dropdownId) {
    open.value = false;
  }
};

onMounted(() => {
  window.addEventListener('dropdown-closed', handleDropdownClosed);
});

onUnmounted(() => {
  window.removeEventListener('dropdown-closed', handleDropdownClosed);
});
</script>

<template>
  <div class="relative menu-item" @mouseenter="showDropdown" @mouseleave="hideDropdown">
    <!-- Trigger slot for the button or icon -->
    <div>
      <slot name="trigger" />
    </div>

    <!-- Background overlay -->
    <div v-if="open" class="fixed inset-0 z-40" @click="hideDropdown"></div>

    <!-- Dropdown content -->
    <Transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition ease-in duration-75"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-95"
    >
      <div
        v-show="open"
        class="absolute z-50 mt-2 rounded-md shadow-lg sub-menu"
        :class="[widthClass, alignmentClasses]"
        @mouseenter="cancelHide"
        @mouseleave="hideDropdown"
      >
        <div class="rounded-md ring-1 ring-black ring-opacity-5" :class="contentClasses">
          <slot name="content" />
        </div>
      </div>
    </Transition>
  </div>
</template>

<style scoped lang="scss">
$black: #2A363B;
$red: #F67280;
$red-dark: #C06C84;
$orange: #F8B195;
$yellow: #FECEAB;
$white: #FFF;

.menu {
  position: relative;

  &-item {
    line-height: 2.5rem;
    text-align: center;

    &:hover .sub-menu {
      display: block;
    }

    &:after {
      content: '';
      position: absolute;
      width: 4px;
      height: 4px;
      border-radius: 50%;
      bottom: 5px;
      left: calc(50% - 2px);
      background: $yellow;
      transform: scale(0);
      transition: transform 0.2s ease;
    }

    &:hover:after {
      transform: scale(1);
    }

    a {
      display: block;
      color: $white;
      text-transform: uppercase;
      padding: 0.75rem 0;
    }
  }

  .sub-menu {
    position: absolute;
    width: 100%;
    top: 100%;
    left: 0;
    display: none;
    z-index: 1;
    background: $red;
    border-radius: 0 0 5px 5px;
    opacity: 0;
    transform-origin: bottom;
    animation: enter 0.2s ease forwards;

    a {
      color: $white;
      padding: 0.75rem;
    }

    @for $n from 1 through 3 {
      &:nth-child(#{$n}) {
        animation-duration: 0.2s + 0.1s * ($n - 1);
        animation-delay: 0.1s * ($n - 1);
      }
    }

    &:hover {
      background: $orange;
    }
  }

  @keyframes enter {
    from {
      opacity: 0;
      transform: scaleY(0.98) translateY(10px);
    }
    to {
      opacity: 1;
      transform: none;
    }
  }

  @media (max-width: 600px) {
    flex-direction: column;

    .sub-menu {
      width: 100vw;
      left: -2rem;
      top: 50%;
      transform: translateY(-50%);
    }
  }
}
</style>
