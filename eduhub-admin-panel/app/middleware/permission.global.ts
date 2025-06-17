export default defineNuxtRouteMiddleware(async (to) => {
  // const authStore = useAuthStore();
  // let requiredPermission: string | null = null; // Initialize it to null until we get permission from route
  // let userPermissions: string[] = [];

  // const publicRoutes = ["/unauthorized", "/login", "/register"];

  // // Check if we're on the client-side to load permissions
  // if (process.client) {
  //   // Ensure the authStore is loaded
  //   await authStore.loadUserData(); // Load user data and permissions if needed
    
  //   // Get the required permission from the route's meta
  //   requiredPermission = to.meta.permission;

  //   // Get user permissions from the auth store
  //   userPermissions = authStore.permissions;

  //   // Log requiredPermission for debugging
  //   console.log('Required Permission:', requiredPermission);
  //   console.log('User Permissions:', userPermissions);
  // }

  // // Skip permission check for public routes
  // if (publicRoutes.includes(to.path)) {
  //   return; // No need to check permissions if it's a public route
  // }

  // // Check if the route requires permission and if the user has it
  // if (requiredPermission && !userPermissions.includes(requiredPermission)) {
  //   return navigateTo("/unauthorized"); // Redirect to the unauthorized page
  // }
});
