export default defineNuxtConfig({
  modules: [
    "@nuxt/eslint",
    "@nuxt/ui-pro",
    "@vueuse/nuxt",
    "@pinia/nuxt", // Pinia module automatically handles Pinia instantiation
  ],
  ui: {
    global: true,
  },
  devtools: {
    enabled: true,
  },
  plugins: ["~/plugins/piniaPersistedState.js"], // Make sure you don't recreate Pinia here
  css: ["~/assets/css/main.css"],

  routeRules: {
    "/api/**": {
      cors: true,
    },
  },

  future: {
    compatibilityVersion: 4,
  },

  compatibilityDate: "2024-07-11",

  eslint: {
    config: {
      stylistic: {
        commaDangle: "never",
        braceStyle: "1tbs",
      },
    },
  },

  runtimeConfig: {
    public: {
      REVERB_APP_ID: process.env.NUXT_PUBLIC_REVERB_APP_ID,
      REVERB_APP_KEY: process.env.NUXT_PUBLIC_REVERB_APP_KEY,
      REVERB_APP_SECRET: process.env.NUXT_PUBLIC_REVERB_APP_SECRET,
      REVERB_HOST: process.env.NUXT_PUBLIC_REVERB_HOST,
      REVERB_PORT: process.env.NUXT_PUBLIC_REVERB_PORT,
      REVERB_SCHEME: process.env.NUXT_PUBLIC_REVERB_SCHEME,
      API_BASE_URL: process.env.VITE_API_BASE_URL,
      NUXT_PUBLIC_SITE_URL: process.env.NUXT_PUBLIC_SITE_URL,
    },
  },
});
