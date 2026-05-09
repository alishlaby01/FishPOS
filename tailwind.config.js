/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./app/Livewire/**/*.php",
  ],
  theme: {
    extend: {
      colors: {
        "surface": "#131313",
        "on-surface": "#e5e2e1",
        "accent": "#DFFF00",
        "secondary": "#d1beef",
        "stone-950": "#0a0a0a",
      },
      fontFamily: {
        "cairo": ["Cairo", "sans-serif"],
        "jakarta": ["Plus Jakarta Sans", "sans-serif"],
      },
    },
  },
  plugins: [],
}