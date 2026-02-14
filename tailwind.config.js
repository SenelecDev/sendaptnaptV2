/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./vendor/livewire/livewire/dist/livewire.js",
  ],
  theme: {
    extend: {
      colors: {
        // Palette officielle SENELEC
        senelec: {
          // Bleu Principal - Reflex Blue C
          blue: {
            DEFAULT: '#0D1CB0',
            dark: '#0A1580',
            light: '#1A2DD0',
          },
          // Teal/Cyan - Pantone 7474 C
          teal: {
            DEFAULT: '#0A91A3',
            dark: '#077A8A',
            light: '#0CB0C5',
          },
          // Violet Foncé - Pantone 2695 C
          purple: {
            DEFAULT: '#2B1444',
            dark: '#1E0E30',
            light: '#3D1E5C',
          },
          // Orange - Pantone 021 C
          orange: {
            DEFAULT: '#E87400',
            dark: '#C56200',
            light: '#FF8C1A',
          },
          // Magenta - Pantone 234 C
          magenta: {
            DEFAULT: '#B3006C',
            dark: '#8F0056',
            light: '#D41A82',
          },
          // Jaune - Pantone 109 C
          yellow: {
            DEFAULT: '#FFD100',
            dark: '#D4AF00',
            light: '#FFE033',
          },
        },
        // Alias pour faciliter l'utilisation
        primary: {
          DEFAULT: '#0D1CB0',
          dark: '#0A1580',
          light: '#1A2DD0',
        },
        secondary: {
          DEFAULT: '#0A91A3',
          dark: '#077A8A',
          light: '#0CB0C5',
        },
        accent: {
          DEFAULT: '#B3006C',
          dark: '#8F0056',
          light: '#D41A82',
        },
        success: {
          DEFAULT: '#0A91A3',
          dark: '#077A8A',
          light: '#0CB0C5',
        },
        warning: {
          DEFAULT: '#E87400',
          dark: '#C56200',
          light: '#FF8C1A',
        },
        danger: {
          DEFAULT: '#B3006C',
          dark: '#8F0056',
          light: '#D41A82',
        },
        info: {
          DEFAULT: '#0D1CB0',
          dark: '#0A1580',
          light: '#1A2DD0',
        },
      },
      fontFamily: {
        'conthrax': ['Conthrax', 'Rajdhani', 'Open Sans', 'system-ui', 'sans-serif'],
        'title': ['Rajdhani', 'Open Sans', 'system-ui', 'sans-serif'],
        'body': ['Open Sans', 'system-ui', 'sans-serif'],
      },
      backgroundImage: {
        'gradient-senelec': 'linear-gradient(135deg, #2B1444 0%, #B3006C 100%)',
        'gradient-senelec-light': 'linear-gradient(90deg, #FFD100 0%, #E87400 25%, #B3006C 60%, #0A91A3 100%)',
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
