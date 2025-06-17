// middleware/auth.global.ts
export default defineNuxtRouteMiddleware(async (to) => {
  const publicRoutes = ["/login", "/register", "/forgot-password"];
  const authStore = useAuthStore();

  // If it's client-side, wait for user data to be loaded from cookies
  if (process.client) {
    // Wait for user data to be loaded
    await authStore.loadUserData();

    // Check if user is authenticated
    if (!authStore.isAuthenticated) {
      // If user is not authenticated and trying to access a protected route
      if (!publicRoutes.includes(to.path)) {
        return navigateTo("/login");
      }
    }
  }

  // Server-side logic (SSR) to check if user is authenticated
  // If the authStore is not initialized yet and we are on SSR, check cookies directly
  if (!authStore.isAuthenticated && !publicRoutes.includes(to.path)) {
    return navigateTo("/login");  // Redirect to login page if not authenticated
  }
});
