import { defineStore } from "pinia";
import { ref, computed } from "vue";

export const useDashboardStore = defineStore("dashboard", () => {
  const items = ref([[], []]);
  const api = useApi(); // Assuming useApi is a composable for API calls

  async function fetchDashboardData(params = {}) {
    try {
      const query = new URLSearchParams(params).toString();
      const url = `/dashboard${query ? `?${query}` : ""}`;

      const res = await api(url, { method: "GET" });

      // Assume `api` returns a standard fetch-like response
      const json = await res.json();

      if (json?.data) {
        items.value = json.data;
      }
    } catch (error) {
      console.error("Error fetching dashboard data:", error);
    }
  }

  return { items, fetchDashboardData };
});
