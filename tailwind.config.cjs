module.exports = {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.{js,ts,vue,jsx,tsx}'
    ],
    theme: {
        extend: {},
    },
    plugins: [
        require('@tailwindcss/forms')
    ],
};
