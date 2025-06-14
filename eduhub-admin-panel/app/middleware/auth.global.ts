// middleware/auth.global.ts
export default defineNuxtRouteMiddleware((to, from) => {
  const isAuthenticated = useCookie("auth_token").value; // حسب نظام المصادقة المستخدم
  const publicRoutes = ["/login", "/register", "/forgot-password"];
  if (!isAuthenticated && !publicRoutes.includes(to.path)) {
    return navigateTo("/login");
  }
});
