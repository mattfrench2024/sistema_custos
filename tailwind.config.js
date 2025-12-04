import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";
import typography from "@tailwindcss/typography";
import plugin from "tailwindcss/plugin";

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: "class", // Suporte total a Dark Mode controlado via classe

    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.js",
        "./resources/js/**/*.vue",
        "./resources/js/**/*.jsx",
    ],

    theme: {
        extend: {
            /* -----------------------------------------
               TIPOGRAFIA PREMIUM
            ------------------------------------------*/
            fontFamily: {
                sans: ["Inter", ...defaultTheme.fontFamily.sans],
                display: ["Inter", ...defaultTheme.fontFamily.sans],
            },

            /* -----------------------------------------
               DESIGN TOKENS — Cores Premium do Sistema
            ------------------------------------------*/
            colors: {
                brand: {
                    DEFAULT: "#F9821A",
                    light: "#FF9E34",
                    dark: "#C86915",
                    gradientFrom: "#F9821A",
                    gradientTo: "#FC940D",
                },

                // Glassmorphism base
                glass: {
                    light: "rgba(255,255,255,0.6)",
                    dark: "rgba(255,255,255,0.08)",
                }
            },

            /* -----------------------------------------
               SOMBRAS PREMIUM (Apple / Linear Style)
            ------------------------------------------*/
            boxShadow: {
                soft: "0 8px 24px rgba(0,0,0,0.08)",
                medium: "0 12px 32px rgba(0,0,0,0.12)",
                strong: "0 16px 48px rgba(0,0,0,0.18)",
                card: "0 12px 40px -8px rgba(0,0,0,0.16)",
            },

            /* -----------------------------------------
               DESFOQUE — Glassmorphism real
            ------------------------------------------*/
            backdropBlur: {
                xs: "2px",
                sm: "4px",
                md: "8px",
                lg: "16px",
                xl: "24px",
                "2xl": "40px",
            },

            /* -----------------------------------------
               ANIMAÇÕES PREMIUM
            ------------------------------------------*/
            keyframes: {
                "fade-in": {
                    "0%": { opacity: 0 },
                    "100%": { opacity: 1 },
                },
                "fade-in-up": {
                    "0%": { opacity: 0, transform: "translateY(10px)" },
                    "100%": { opacity: 1, transform: "translateY(0)" },
                },
                "scale-in": {
                    "0%": { opacity: 0, transform: "scale(0.96)" },
                    "100%": { opacity: 1, transform: "scale(1)" },
                },
                "blur-in": {
                    "0%": { opacity: 0, filter: "blur(12px)" },
                    "100%": { opacity: 1, filter: "blur(0)" },
                },
            },

            animation: {
                "fade-in": "fade-in 0.4s ease-out forwards",
                "fade-in-up": "fade-in-up 0.5s ease-out forwards",
                "scale-in": "scale-in 0.45s cubic-bezier(0.32, 0.72, 0, 1)",
                "blur-in": "blur-in 0.6s ease-out forwards",
            },

            /* -----------------------------------------
                RADIUS PREMIUM
            ------------------------------------------*/
            borderRadius: {
                xl2: "18px",
                xl3: "22px",
                smooth: "14px",
            },
        },
    },

    /* -----------------------------------------
       PLUGINS OFICIAIS + MICROINTERAÇÕES
    ------------------------------------------*/
    plugins: [
        forms,
        typography,

        // Plugin: animações de hover premium em botões e cards
        plugin(function ({ addUtilities }) {
            addUtilities({
                ".tap-highlight-none": {
                    "-webkit-tap-highlight-color": "transparent",
                },
                ".smooth-hover": {
                    transition: "all 0.25s cubic-bezier(0.4, 0.0, 0.2, 1)",
                },
            });
        }),
    ],
};
