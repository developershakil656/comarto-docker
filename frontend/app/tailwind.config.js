/** @type {import('tailwindcss').Config} */
export default {
    content: [
      './index.html',
      './src/**/*.{vue,js,ts,jsx,tsx,svg}',
    ],
    theme: {
      extend: {
        colors: {
          primary: '#0B845C',
          'primary-dark': '#0A6B4A',
          'primary-50': '#f0fdf4',
          'primary-100': '#dcfce7',
          'primary-200': '#bbf7d0',
          'primary-300': '#86efac',
          'primary-400': '#4ade80',
          'primary-500': '#22c55e',
          'primary-600': '#16a34a',
          'primary-700': '#15803d',
          'primary-800': '#166534',
          'primary-900': '#14532d',
        },
        fontFamily: {
          poppins: ['Poppins', 'sans-serif'],
          sans: ['Poppins', 'ui-sans-serif', 'system-ui', 'sans-serif'],
        }
      },
    },
    plugins: []
  }