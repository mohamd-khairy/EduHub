import { defineStore } from "pinia";
import { ref, computed } from "vue";

export const useDashboardStore = defineStore("dashboard", () => {
  const items = ref([]);
  const studentPerformancePerGroup = ref([]);
  const studentPerformanceOverTime = ref([]);
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

  async function fetchStudentPerformancePerGroup(params = {}) {
    try {
      const query = new URLSearchParams(params).toString();
      const url = `/dashboard/student-performance-per-group${
        query ? `?${query}` : ""
      }`;
      const res = await api(url, { method: "GET" });

      // Assume `api` returns a standard fetch-like response
      const json = await res.json();

      if (json?.data) {
        studentPerformancePerGroup.value = json.data;
      }
    } catch (error) {
      console.error("Error fetching student performance data:", error);
    }
  }

  async function fetchStudentOverTimePerformance(params = {}) {
    try {
      const query = new URLSearchParams(params).toString();
      const url = `/dashboard/student-performance-over-time${
        query ? `?${query}` : ""
      }`;
      const res = await api(url, { method: "GET" });

      // Assume `api` returns a standard fetch-like response
      const json = await res.json();

      if (json?.data) {
        studentPerformanceOverTime.value = json.data;
      }
    } catch (error) {
      console.error("Error fetching student overtime performance data:", error);
    }
  }

  return {
    items,
    studentPerformancePerGroup,
    studentPerformanceOverTime,
    fetchDashboardData,
    fetchStudentPerformancePerGroup,
    fetchStudentOverTimePerformance,
  };
});
