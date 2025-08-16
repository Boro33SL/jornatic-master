// tailwind.config.js
console.log('tailwind.config.js cargando...');
module.exports = {
    content: [
        "./templates/**/*.php",
        "./webroot/**/*.js",
    ],
    safelist: [
        { pattern: /^(bg|text|border|ring|outline)-primary-(50|100|200|300|400|500|600|700|800|900)$/ },
    ],
    theme: {
    },
    corePlugins: {
        preflight: true, // asegura que no estás desactivando nada
    },
    plugins: [],
};