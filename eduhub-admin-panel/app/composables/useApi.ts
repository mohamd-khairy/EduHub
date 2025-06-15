// composables/useApi.ts
export function useApi() {
  const BASE_URL =
    import.meta.env.NUXT_API_BASE_URL ||
    "http://localhost/EduHub/eduhub-backend/public/api";

  const router = useRouter();

  return async (endpoint: string, options: RequestInit = {}) => {
    const token = process.client ? localStorage.getItem("auth_token") : null;

    const url = `${BASE_URL}${
      endpoint.startsWith("/") ? endpoint : "/" + endpoint
    }`;

    // Check if body is FormData
    const isFormData = options.body instanceof FormData;

    const headers: HeadersInit = {
      ...(options.headers || {}),
      Accept: "application/json",
      ...(token ? { Authorization: `Bearer ${token}` } : {}),
    };

    // Only set Content-Type if not sending FormData
    if (!isFormData) {
      headers["Content-Type"] = "application/json";
    }

    const response = await fetch(url, {
      ...options,
      headers,
    });

    if (response.status === 401 && process.client) {
      localStorage.removeItem("auth_token");
      localStorage.removeItem("auth_user");
      router.push("/login");
      return;
    }

    return response;
  };
}
