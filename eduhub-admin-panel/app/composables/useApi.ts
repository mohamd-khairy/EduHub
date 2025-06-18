import { useRouter } from '#app';

export function useApi() {
  const BASE_URL =
    import.meta.env.NUXT_API_BASE_URL || "http://localhost/EduHub/eduhub-backend/public/api";
  
  const router = useRouter();
  const authStore = useAuthStore(); // Access the authStore to get the token

  // Helper function to get the token from Pinia store
  const getToken = () => {
    return authStore.token; // Get the token from the Pinia store
  };

  return async (endpoint: string, options: RequestInit = {}): Promise<Response | void> => {
    const token = getToken();

    const url = `${BASE_URL}${endpoint.startsWith("/") ? endpoint : "/" + endpoint}`;

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

    try {
      const response = await fetch(url, {
        ...options,
        headers,
      });

      // Handle 401 Unauthorized responses
      if (response.status === 401 && process.client) {
        // Clear the token and user data from Pinia store
        authStore.resetAuthState();  // Assuming `resetAuthState` is a function in your authStore that resets the user, token, etc.
        router.push("/login");
        return;
      }

      // If the response is an error, throw to handle it in the calling code
      if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.message || "An error occurred during the request");
      }

      return response;
    } catch (error) {
      console.error("API request failed:", error);
      throw error; // Propagate error for higher-level handling
    }
  };
}
