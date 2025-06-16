export default defineNuxtRouteMiddleware((to) => {
  const authStore = useAuthStore();
  // const linksStore = useLinksStore();
  // linksStore.filterLinks();

  // const links = linksStore.links;
  let userPermissions: string[] = [];

  if (process.client) {
    authStore.loadPermissions();
    userPermissions = authStore.permissions;
  }

  const publicRoutes = ["/unauthorized", "/login", "/register"];

  // Skip permission check for public routes
  if (publicRoutes.includes(to.path)) {
    return;
  }

  const requiredPermission = to.meta.permission;

  // Check if the route requires permission and the user has it
  if (requiredPermission && !userPermissions.includes(requiredPermission)) {
    return navigateTo("/unauthorized"); // Redirect to the unauthorized page
  }
});
