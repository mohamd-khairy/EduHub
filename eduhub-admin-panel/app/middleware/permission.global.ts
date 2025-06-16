export default defineNuxtRouteMiddleware((to) => {

  const publicRoutes = ["/unauthorized", "/login", "/register"];

  // If the route is public, skip permission check
  if (publicRoutes.includes(to.path)) {
    return;
  }

  let userPermissions: string[] = [];

  // Ensure we're on the client before accessing localStorage
  if (process.client) {
    const stored = localStorage.getItem("auth_permissions");

    userPermissions = stored ? JSON.parse(stored) : [];

    // Check if the route has a required permission
    const requiredPermission = to.meta.permission;

    // Ensure there's a permission to check, and the user has it
    if (requiredPermission && !userPermissions.includes(requiredPermission)) {
      return navigateTo("/unauthorized");
    }
  }
});
