import { defineStore } from "pinia";

export const useAuthStore = defineStore(
  "auth",
  () => {
    const api = useApi();
    const router = useRouter();

    // State initialization with ref and computed
    const user = ref(null);
    const token = useCookie<string | null>("auth_token", {
      sameSite: "strict",
      secure: process.env.NODE_ENV === "production",
      maxAge: 60 * 60 * 24, // 1 day
    });
    const roles = ref([]);
    const permissions = ref([]);
    const isAuthenticated = computed(() => !!token.value);

    // Helper to load user data from cookies on initialization
    const loadUserData = () => {
      if (process.client) {
        const userCookie = useCookie("auth_user").value;
        const rolesCookie = useCookie("auth_roles").value;
        const permissionsCookie = useCookie("auth_permissions").value;

        user.value = userCookie || null;
        roles.value = rolesCookie || [];
        permissions.value = permissionsCookie || [];

        // console.log(permissions.value);
      }
    };

    // Helper function to set all auth cookies
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

    // Helper function to clear all auth cookies
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

    // API request error handler
    const handleApiError = async (response: Response) => {
      const errorData = await response.json();
      throw new Error(errorData.message || "An error occurred");
    };

    // Login action
    const login = async (credentials: {
      email: string;
      password: string;
      type: string;
    }) => {
      try {
        const response = await api("auth/login", {
          method: "POST",
          body: JSON.stringify(credentials),
        });

        if (!response.ok) {
          await handleApiError(response);
        }

        const { data } = await response.json();

        // Update state and set cookies
        user.value = data.user;
        roles.value = data.roles || [];
        permissions.value = data.permissions || [];
        token.value = data.token;

        setAuthCookies({
          user: data.user,
          roles: data.roles,
          permissions: data.permissions,
          token: data.token,
        });

        // Redirect after successful login
        await router.push("/");
      } catch (error) {
        resetAuthState();
        // throw error;
        throw new Error("خطأ في بيانات الدخول");
      }
    };

    // Logout action
    const logout = async () => {
      try {
        // Optional: Call logout API if necessary
        await api("auth/logout", { method: "POST" });
      } finally {
        resetAuthState();
        clearAuthCookies();
        await router.push("/login");
      }
    };

    const changePassword = async (credentials = {}) => {
      try {
        // Optional: Call logout API if necessary
        await api("auth/change-password", {
          method: "POST",
          body: JSON.stringify(credentials),
        });
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

    // Permission checks
    const hasPermission = (permission: Permission) =>
      permissions.value.includes(permission);
    const hasAnyPermission = (requiredPermissions: Permission[]) =>
      requiredPermissions.some(hasPermission);
    const hasAllPermissions = (requiredPermissions: Permission[]) =>
      requiredPermissions.every(hasPermission);

    // Role checks
    const hasRole = (role: Role) => roles.value.includes(role);
    const hasAnyRole = (requiredRoles: Role[]) => requiredRoles.some(hasRole);

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
      changePassword,
      checkAuth,
      hasPermission,
      hasAnyPermission,
      hasAllPermissions,
      hasRole,
      hasAnyRole,
      resetAuthState,
    };
  },
  {
    persist: true, // Enable persisted state for this store
  }
);
