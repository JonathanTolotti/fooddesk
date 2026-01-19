import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    safelist: [
        // Existing
        'bg-indigo-600', 'bg-indigo-700', 'bg-green-600', 'bg-red-600',
        'bg-purple-600', 'bg-sky-600', 'bg-amber-600', 'bg-gray-600',
        'bg-purple-500', 'bg-sky-500', 'bg-amber-500', 'bg-gray-500',
        'text-white', 'text-indigo-600', 'text-purple-700', 'text-sky-700', 'text-amber-700',
        'border-purple-600', 'border-sky-600', 'border-amber-600', 'border-gray-600',
        'border-purple-500', 'border-sky-500', 'border-amber-500', 'border-gray-500',
        'hover:bg-indigo-700', 'hover:bg-red-700',
        'peer-checked:bg-purple-600', 'peer-checked:bg-sky-600', 'peer-checked:bg-amber-600', 'peer-checked:bg-gray-600',
        'peer-checked:bg-purple-500', 'peer-checked:bg-sky-500', 'peer-checked:bg-amber-500', 'peer-checked:bg-gray-500',
        'peer-checked:text-white', 'peer-checked:border-purple-600', 'peer-checked:border-sky-600',
        'peer-checked:border-amber-600', 'peer-checked:border-gray-600',
        'peer-checked:border-purple-500', 'peer-checked:border-sky-500',
        'peer-checked:border-amber-500', 'peer-checked:border-gray-500',
        // Users list
        'bg-blue-600', 'hover:bg-blue-700', 'bg-blue-400', 'to-blue-600', 'from-blue-400',
        'bg-green-50', 'bg-green-100', 'text-green-500', 'text-green-700', 'text-green-800', 'border-green-200',
        'bg-purple-100', 'text-purple-800',
        'bg-orange-100', 'text-orange-800',
        'bg-red-100', 'text-red-800', 'text-red-600', 'hover:text-red-900',
        'text-blue-600', 'hover:text-blue-900',
        'bg-gray-50', 'bg-gray-100', 'bg-gray-200', 'hover:bg-gray-50', 'hover:bg-gray-200',
        'text-gray-500', 'text-gray-700', 'text-gray-800', 'text-gray-900',
        'border-gray-200', 'border-gray-300',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
