// middleware/auth.global.ts
export default defineNuxtRouteMiddleware((to) => {
  const publicRoutes = ["/login", "/register", "/forgot-password"];

  if (process.client) {
    const authStore = useAuthStore();

    // Sync token and user from localStorage
    authStore.token = localStorage.getItem("auth_token");

    const userStr = localStorage.getItem("auth_user");
    authStore.user = userStr ? JSON.parse(userStr) : null;

    const rolesStr = localStorage.getItem("auth_roles");
    authStore.roles = rolesStr ? JSON.parse(rolesStr) : null;

    const permissionsStr = localStorage.getItem("auth_permissions");
    authStore.permissions = permissionsStr ? JSON.parse(permissionsStr) : null;

    // If not authenticated and trying to access a protected route
    if (!authStore.token && !publicRoutes.includes(to.path)) {
      return navigateTo("/login");
    }

    // If authenticated and trying to access a public route (optional)
    if (authStore.token && publicRoutes.includes(to.path)) {
      return navigateTo("/");
    }
  }
});
