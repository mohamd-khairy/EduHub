import { defineNuxtRouteMiddleware, navigateTo } from '#app';

export default defineNuxtRouteMiddleware(async (to) => {
  const publicRoutes = ["/login", "/register", "/forgot-password"];
  const authStore = useAuthStore();

  // If it's client-side, wait for user data to be loaded from cookies and persisted state
  if (process.client) {
    // Wait for user data to be loaded (from Pinia store with persisted state)
    await authStore.loadUserData();

    console.log(!authStore.isAuthenticated && !publicRoutes.includes(to.path));
    
    // Check if the user is authenticated
    if (!authStore.isAuthenticated) {
      // If the user is not authenticated and trying to access a protected route
      if (!publicRoutes.includes(to.path)) {
        return navigateTo("/login");  // Redirect to login page
      }
    }
  }

  // Server-side logic (SSR) to check if user is authenticated
  if (!authStore.isAuthenticated && !publicRoutes.includes(to.path)) {
    return navigateTo("/login");  // Redirect to login page if not authenticated
  }
});
