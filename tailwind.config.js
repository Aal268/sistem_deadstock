import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Bricolage Grotesque", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: "#13505B",
                secondary: "#0C7489",
                tertiary: "#119DA4",
                accent: "#4B6858",
                info: "#D7D9CE",
            },
        },
    },

    plugins: [forms],
};
