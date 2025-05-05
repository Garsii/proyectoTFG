// tailwind.config.js
import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

export default {
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
  ],
  theme: {
    extend: {
      colors: {
        'gymblue-start': '#3B82F6',  // blue‑500
        'gymblue-end':   '#93C5FD',  // blue‑300
        gymorange:       '#F59E0B',
      },
      backgroundImage: {
        'gym-navbar': 'linear-gradient(to right, #3B82F6, #93C5FD)',
      },
      fontFamily: {
        sans: ['Figtree', ...defaultTheme.fontFamily.sans],
      },
    },
  },
  plugins: [forms],
};
