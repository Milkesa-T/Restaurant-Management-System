import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                "body-md": ["Inter", ...defaultTheme.fontFamily.sans],
                "headline-lg-mobile": ["Inter", ...defaultTheme.fontFamily.sans],
                "display-lg": ["Inter", ...defaultTheme.fontFamily.sans],
                "label-caps": ["Inter", ...defaultTheme.fontFamily.sans],
                "price-lg": ["JetBrains Mono", ...defaultTheme.fontFamily.mono],
                "price-sm": ["JetBrains Mono", ...defaultTheme.fontFamily.mono],
                "headline-lg": ["Inter", ...defaultTheme.fontFamily.sans],
                "title-md": ["Inter", ...defaultTheme.fontFamily.sans]
                sans: ['Inter', 'Figtree', ...defaultTheme.fontFamily.sans],
                outfit: ['Outfit', 'sans-serif'],
            },
            colors: {
                "tertiary-fixed-dim": "#fcba66",
                "on-tertiary-container": "#ffcb8f",
                "on-tertiary-fixed-variant": "#653e00",
                "on-secondary": "#ffffff",
                "tertiary-fixed": "#ffddb8",
                "primary-container": "#006948",
                "on-primary-container": "#91e5bc",
                "secondary-fixed": "#cde5ff",
                "on-tertiary-fixed": "#2a1700",
                "on-secondary-fixed": "#001d32",
                "on-error": "#ffffff",
                "primary-fixed": "#9ff4ca",
                "secondary-container": "#81c4ff",
                "inverse-primary": "#83d7ae",
                "on-primary": "#ffffff",
                "surface-tint": "#076c4b",
                "surface-dim": "#dfd7e3",
                "error": "#ba1a1a",
                "on-secondary-fixed-variant": "#004b74",
                "background": "#fef7ff",
                "surface-container-lowest": "#ffffff",
                "inverse-on-surface": "#f6eefa",
                "surface-container": "#f3ebf7",
                "on-background": "#1d1a22",
                "surface-variant": "#e7e0eb",
                "on-primary-fixed": "#002114",
                "surface-container-low": "#f9f1fd",
                "on-primary-fixed-variant": "#005237",
                "outline-variant": "#bec9c1",
                "on-tertiary": "#ffffff",
                "primary-fixed-dim": "#83d7ae",
                "inverse-surface": "#322f37",
                "on-surface-variant": "#3f4943",
                "on-surface": "#1d1a22",
                "on-secondary-container": "#00517e",
                "error-container": "#ffdad6",
                "tertiary": "#623c00",
                "surface": "#fef7ff",
                "surface-bright": "#fef7ff",
                "outline": "#6f7a72",
                "tertiary-container": "#825100",
                "surface-container-highest": "#e7e0eb",
                "surface-container-high": "#ede6f1",
                "secondary-fixed-dim": "#94ccff",
                "primary": "#004f35",
                "secondary": "#016398",
                "on-error-container": "#93000a"
            },
            borderRadius: {
                "DEFAULT": "0.125rem",
                "lg": "0.25rem",
                "xl": "0.5rem",
                "full": "0.75rem"
            },
            spacing: {
                "gutter": "24px",
                "base": "4px",
                "md": "16px",
                "sm": "8px",
                "margin-mobile": "16px",
                "xl": "40px",
                "lg": "24px",
                "xs": "4px",
                "margin-desktop": "64px"
            },
            fontSize: {
                "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                "headline-lg-mobile": ["28px", {"lineHeight": "36px", "fontWeight": "600"}],
                "display-lg": ["57px", {"lineHeight": "64px", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                "label-caps": ["12px", {"lineHeight": "16px", "letterSpacing": "0.05em", "fontWeight": "700"}],
                "price-lg": ["20px", {"lineHeight": "28px", "fontWeight": "500"}],
                "price-sm": ["14px", {"lineHeight": "20px", "fontWeight": "500"}],
                "headline-lg": ["32px", {"lineHeight": "40px", "letterSpacing": "-0.01em", "fontWeight": "600"}],
                "title-md": ["16px", {"lineHeight": "24px", "fontWeight": "600"}]
            }
        },
    },

    plugins: [forms],
};
