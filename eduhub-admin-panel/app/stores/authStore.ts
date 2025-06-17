// ~/stores/auth.ts
import { defineStore } from "pinia";
import type { User, Role, Permission } from "~/types/auth"; // Define these types

export const useAuthStore = defineStore("auth", () => {
  const api = useApi();
  const router = useRouter();

  // State
  const user = ref<User | null>(null);
  const token = useCookie<string | null>("auth_token", {
    sameSite: "strict",
    secure: process.env.NODE_ENV === "production",
    maxAge: 60 * 60 * 24, // 1 day
  });
  const roles = ref<Role[]>([]);
  const permissions = ref<Permission[]>([]);
  const isAuthenticated = computed(() => !!token.value);

  // Initialize store from cookies
  const loadUserData = () => {
    if (process.client) {
      const userCookie = useCookie<User | null>("auth_user").value;
      const rolesCookie = useCookie<Role[]>("auth_roles").value;
      const permissionsCookie =
        useCookie<Permission[]>("auth_permissions").value;

      user.value = userCookie;
      roles.value = rolesCookie || [];
      permissions.value = permissionsCookie || [];      
    }
  };

  // Login action
  const login = async (credentials: { email: string; password: string }) => {
    try {
      const response = await api("auth/login", {
        method: "POST",
        body: JSON.stringify(credentials),
      });

      if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.message || "Login failed");
      }

      const { data } = await response.json();

      // Update state
      user.value = data.user;
      roles.value = data.roles || [];
      permissions.value = data.permissions || [];
      token.value = data.token;

      // Set secure cookies
      setAuthCookies({
        user: data.user,
        roles: data.roles,
        permissions: data.permissions,
        token: data.token,
      });

      // Redirect after login
      await router.push("/");
    } catch (error) {
      // Clear state on error
      resetAuthState();
      throw error;
    }
  };

  // Logout action
  const logout = async () => {
    try {
      // Optional: Call logout API if needed
      await api("auth/logout", { method: "POST" });
    } finally {
      resetAuthState();
      clearAuthCookies();
      await router.push("/login");
    }
  };

  // Check auth status (useful for page reloads)
  const checkAuth = async () => {
    if (!token.value) return false;

    try {
      const response = await api("auth/me", {
        headers: {
          Authorization: `Bearer ${token.value}`,
        },
      });

      if (!response.ok) throw new Error("Invalid session");

      const { data } = await response.json();

      // Update state with fresh data
      user.value = data.user;
      roles.value = data.roles || [];
      permissions.value = data.permissions || [];

      // Update cookies
      setAuthCookies({
        user: data.user,
        roles: data.roles,
        permissions: data.permissions,
        token: token.value, // Keep existing token
      });

      return true;
    } catch (error) {
      resetAuthState();
      clearAuthCookies();
      return false;
    }
  };

  // Helper to set all auth cookies
  const setAuthCookies = (data: {
    user: User | null;
    roles: Role[];
    permissions: Permission[];
    token: string | null;
  }) => {
    const cookieOptions = {
      maxAge: 60 * 60 * 24, // 1 day
      sameSite: "strict" as const,
      secure: process.env.NODE_ENV === "production",
    };

    useCookie("auth_token").value = data.token;
    useCookie("auth_user", cookieOptions).value = JSON.stringify(data.user);
    useCookie("auth_roles", cookieOptions).value = JSON.stringify(data.roles);
    useCookie("auth_permissions", cookieOptions).value = JSON.stringify(
      data.permissions
    );
  };

  // Helper to clear all auth cookies
  const clearAuthCookies = () => {
    useCookie("auth_token").value = null;
    useCookie("auth_user").value = null;
    useCookie("auth_roles").value = null;
    useCookie("auth_permissions").value = null;
  };

  // Reset all state
  const resetAuthState = () => {
    user.value = null;
    roles.value = [];
    permissions.value = [];
    token.value = null;
  };

  // Permission checks
  const hasPermission = (permission: Permission) => {
    return permissions.value.includes(permission);
  };

  const hasAnyPermission = (requiredPermissions: Permission[]) => {
    return requiredPermissions.some((p) => hasPermission(p));
  };

  const hasAllPermissions = (requiredPermissions: Permission[]) => {
    return requiredPermissions.every((p) => hasPermission(p));
  };

  // Role checks
  const hasRole = (role: Role) => {
    return roles.value.includes(role);
  };

  const hasAnyRole = (requiredRoles: Role[]) => {
    return requiredRoles.some((r) => hasRole(r));
  };

  // Initialize on store creation
  loadUserData();

  return {
    user,
    token,
    roles,
    permissions,
    isAuthenticated,
    loadUserData,
    login,
    logout,
    checkAuth,
    hasPermission,
    hasAnyPermission,
    hasAllPermissions,
    hasRole,
    hasAnyRole,
    resetAuthState,
  };
});
