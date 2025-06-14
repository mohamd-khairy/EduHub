// composables/useApi.ts
export function useApi() {
  // Prefer env from runtimeConfig if Nuxt 3, or fallback
  const BASE_URL =
    import.meta.env.NUXT_API_BASE_URL ||
    "http://localhost/EduHub/eduhub-backend/public/api";

  return async (endpoint: string, options: RequestInit = {}) => {
    const token = process.client ? localStorage.getItem("auth_token") : null;

    const url = `${BASE_URL}${
      endpoint.startsWith("/") ? endpoint : "/" + endpoint
    }`;

    return await fetch(url, {
      ...options,
      headers: {
        ...(options.headers || {}),
        "Accept": "application/json",
        "Content-Type": "application/json",
        Authorization: `Bearer ${token}`,
      },
    });
  };
}
