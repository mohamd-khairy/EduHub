import { defineNuxtPlugin } from '#app';
import piniaPersistedState from 'pinia-plugin-persistedstate';

export default defineNuxtPlugin((nuxtApp) => {
  if (process.client) {
    const pinia = nuxtApp.$pinia; // Use the Pinia instance already created by @pinia/nuxt
    pinia.use(piniaPersistedState); // Only apply the plugin on the client-side
  }
});
