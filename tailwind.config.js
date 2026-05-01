/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./app/Livewire/**/*.php", // Tambahan: Biar class di dalam file PHP juga terbaca
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}