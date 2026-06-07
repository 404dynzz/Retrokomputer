/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'class',
  content: ['./index.html', './src/**/*.{vue,js,ts,jsx,tsx}'],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter', 'Segoe UI', 'Helvetica Neue', 'system-ui', '-apple-system', 'sans-serif'],
        display: ['Poppins', 'Inter', 'system-ui', '-apple-system', 'sans-serif'],
        mono: ['JetBrains Mono', 'Fira Code', 'Courier New', 'monospace'],
      },
      colors: {
        'retro-orange': '#FF7A00',
        'retro-orange-dark': '#E05300',
        'retro-blue': '#1D4ED8', // Rich royal blue from logo swirl/outline
        'retro-blue-deep': '#0A19D1', // Deep cosmic blue from logo swirl
        'retro-dark': '#0B0F19', // Deep premium navy-slate screen background
        'retro-dark-card': '#131926', // Lighter panel/card background
        'retro-yellow': '#FFC857', // Vibrant yellow/gold from R gradient
        'retro-yellow-glow': '#FFE600',
        'retro-cyan': '#00D1FF',
        'retro-purple': '#6D28D9',
      },
    },
  },
  plugins: [],
}
