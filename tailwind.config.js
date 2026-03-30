import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        primary: '#B61F24',
        'primary-hover': '#8F171B',
        mahogany: '#1A1614',
        'background-light': '#F7F1E6',
        'pastel-beige': '#EFE4D2',
        'surface-light': '#FFFFFF',
        'surface-dark': '#1F1F1F',
        'text-main': '#111111',
        'text-secondary': '#5A534B',
        'input-bg': '#FBF8F2',
      },
      fontFamily: {
        sans: ['Inter', ...defaultTheme.fontFamily.sans],
        serif: ['Playfair Display', ...defaultTheme.fontFamily.serif],
      },
      boxShadow: {
        soft: '0 14px 42px rgba(17,17,17,0.08)',
      },
    },
  },
  plugins: [forms],
};
