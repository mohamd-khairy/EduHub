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
});
