import { defineStore } from "pinia";
import { ref } from "vue";

export const useAuthStore = defineStore("auth", () => {
  const api = useApi();

  const user = ref(null);
  const token = ref("");
  const roles = ref(null);
  const permissions = ref(null);

  async function login(par) {
    const res = await api(`auth/login`, {
      method: "POST",
      body: JSON.stringify(par),
    });

    const data = await res.json();

    if (!res.ok) throw new Error(data.message || "Login failed");

    user.value = data?.data?.user;
    token.value = data?.data?.token;
    roles.value = data?.data?.roles;
    permissions.value = data?.data?.permissions;

    localStorage.setItem("auth_token", token.value);
    localStorage.setItem("auth_user", JSON.stringify(user.value));
    localStorage.setItem("auth_roles", JSON.stringify(roles.value));
    localStorage.setItem("auth_permissions", JSON.stringify(permissions.value));
  }

  async function logout() {
    user.value = null;
    token.value = "";
    localStorage.removeItem("auth_token");
    localStorage.removeItem("auth_user");
  }

  // Lazy load the permissions only when needed
  const loadPermissions = () => {
    if (process.client) {
      // Check if permissions are already loaded
      const storedPermissions = localStorage.getItem("auth_permissions");
      permissions.value = storedPermissions
        ? JSON.parse(storedPermissions)
        : [];
    }
  };

  if(process.client) loadPermissions();

  return { user, token, roles, permissions, loadPermissions, login, logout };
});
