/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/react/**/*.{js,jsx,ts,tsx}',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Poppins', 'Inter', 'ui-sans-serif', 'system-ui'],
            },
            colors: {
                brand: {
                    50:  '#fdf8f0',
                    100: '#faefd8',
                    200: '#f5d9a8',
                    300: '#efbc6e',
                    400: '#e8993a',
                    500: '#c97d20', // Koriro Coffee amber
                    600: '#a6620f',
                    700: '#854c0f',
                    800: '#6c3d13',
                    900: '#5a3313',
                    950: '#311805',
                },
                coffee: {
                    50:  '#f7f3f0',
                    100: '#ede3d8',
                    200: '#d9c3aa',
                    300: '#bf9b74',
                    400: '#a97a4c',
                    500: '#8d6035',
                    600: '#7a4f2c',
                    700: '#644127',
                    800: '#543826',
                    900: '#473122',
                    950: '#251710',
                },
                dark: {
                    800: '#1a1210',
                    900: '#0f0b09',
                    950: '#080503',
                },
            },
        },
    },
    plugins: [],
};
