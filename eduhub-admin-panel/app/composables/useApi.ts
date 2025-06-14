// composables/useApi.ts
export function useApi() {
  const BASE_URL =
    import.meta.env.NUXT_API_BASE_URL ||
    "http://localhost/EduHub/eduhub-backend/public/api";

  const router = useRouter(); // Nuxt 3 composable to programmatically navigate

  return async (endpoint: string, options: RequestInit = {}) => {
    const token = process.client ? localStorage.getItem("auth_token") : null;

    const url = `${BASE_URL}${
      endpoint.startsWith("/") ? endpoint : "/" + endpoint
    }`;

    const response = await fetch(url, {
      ...options,
      headers: {
        ...(options.headers || {}),
        Accept: "application/json",
        "Content-Type": "application/json",
        ...(token ? { Authorization: `Bearer ${token}` } : {}),
      },
    });

    if (response.status === 401 && process.client) {
      // Optional: clear token and redirect
      localStorage.removeItem("auth_token");
      localStorage.removeItem("auth_user");

      router.push("/login");
      return;
    }

    return response;
  };
}
