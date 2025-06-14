import { defineStore } from "pinia";
import { ref } from "vue";

export const useAuthStore = defineStore("auth", () => {
  const api = useApi();

  const user = ref(null);
  const token = ref("");
  const loaded = ref(false);

  async function login(par) {
    const res = await api(`auth/login`, {
      method: "POST",
      body: JSON.stringify(par),
    });

    const data = await res.json();

    if (!res.ok) throw new Error(data.message || "Login failed");

    user.value = data.data.user;
    token.value = data.data.token;

    localStorage.setItem("auth_token", token.value);
    localStorage.setItem("auth_user", JSON.stringify(user.value));
  }

  async function logout() {
    user.value = null;
    token.value = "";
    localStorage.removeItem("auth_token");
    localStorage.removeItem("auth_user");
  }

  return { user, token, loaded, login, logout };
});
