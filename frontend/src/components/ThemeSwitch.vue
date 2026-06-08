<template>
  <button
    @click="toggleTheme"
    class="relative select-none flex items-center justify-between w-[116px] h-10 p-1 rounded-full cursor-pointer focus:outline-none transition-all duration-300 neumorphic-switch"
    :class="isDark ? 'switch-dark' : 'switch-light'"
    role="switch"
    :aria-checked="isDark"
    title="Ganti Tema"
  >
    <!-- Text Label -->
    <span
      class="absolute text-[8px] font-black tracking-widest transition-all duration-300 font-sans theme-text select-none"
      :class="isDark ? 'text-dark-active' : 'text-light-active'"
    >
      {{ isDark ? 'DARK MODE' : 'LIGHT MODE' }}
    </span>

    <!-- Sliding Knob -->
    <span
      class="absolute top-1 w-8 h-8 rounded-full flex items-center justify-center transition-all duration-300 ease-out neumorphic-knob"
      :class="isDark ? 'knob-dark' : 'knob-light'"
    >
      <!-- Sun Icon for Light Mode -->
      <svg
        v-if="!isDark"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2.5"
        stroke-linecap="round"
        stroke-linejoin="round"
        class="w-4 h-4 text-[#8a94a6]"
      >
        <circle cx="12" cy="12" r="4" />
        <path d="M12 3v2" />
        <path d="M12 19v2" />
        <path d="M5 12H3" />
        <path d="M21 12h-2" />
        <path d="M18.36 5.64l-1.42 1.42" />
        <path d="M7.05 16.95l-1.42 1.42" />
        <path d="M18.36 18.36l-1.42-1.42" />
        <path d="M7.05 7.05L5.64 5.64" />
      </svg>

      <!-- Moon & Stars Icon for Dark Mode -->
      <svg
        v-else
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2.5"
        stroke-linecap="round"
        stroke-linejoin="round"
        class="w-4 h-4 text-[#a2aebf]"
      >
        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" />
        <path d="M19 3.5l.4.8.8.4-.8.4-.4.8-.4-.8-.8-.4.4-.8.4-.8z" fill="currentColor" stroke="none" />
        <path d="M15 7.5l.3.6.6.3-.6.3-.3.6-.3-.6-.6-.3.6-.3.3-.6z" fill="currentColor" stroke="none" />
      </svg>
    </span>
  </button>
</template>

<script setup lang="ts">
import { useTheme } from '@/utils/theme'

const { isDark, toggleTheme } = useTheme()
</script>

<style scoped>
.neumorphic-switch {
  border: none;
  box-sizing: border-box;
}

/* Light Mode Switch Styles */
.switch-light {
  background-color: #eef0f3;
  box-shadow: inset 3px 3px 6px #d1d9e6, inset -3px -3px 6px #ffffff;
  border: 1px solid rgba(255, 255, 255, 0.4);
}

.text-light-active {
  color: #8a94a6;
  right: 14px;
  top: 50%;
  transform: translateY(-50%);
}

.knob-light {
  background-color: #eef0f3;
  box-shadow: 3px 3px 6px #d1d9e6, -3px -3px 6px #ffffff;
  left: 4px;
  transform: translateX(0);
}

/* Dark Mode Switch Styles */
.switch-dark {
  background-color: #252932;
  box-shadow: inset 3px 3px 6px #15171d, inset -3px -3px 6px #353b47;
  border: 1px solid rgba(255, 255, 255, 0.05);
}

.text-dark-active {
  color: #4f5769;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
}

.knob-dark {
  background-color: #4b5263;
  box-shadow: 2px 2px 5px #15171d, -2px -2px 5px #353b47;
  left: 4px;
  transform: translateX(76px); /* 116px - 8px - 32px = 76px */
}

/* Transitions and interactions */
.neumorphic-switch {
  transition: all 0.3s ease;
}
.neumorphic-switch:hover {
  transform: translateY(-1px);
}
.neumorphic-switch:active {
  transform: translateY(0) scale(0.98);
}
.neumorphic-knob {
  transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.theme-text {
  transition: all 0.3s ease;
}
</style>
