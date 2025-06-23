import { defineStore } from "pinia";
import { ref, computed } from "vue";

export const useDashboardStore = defineStore("dashboard", () => {
  const api = useApi(); // Assuming useApi is a composable for API calls
  const items = ref([]);
  const studentPerformancePerGroup = ref([]);
  const studentPerformanceOverTime = ref([]);
  const studentPerformancePerExam = ref([]);
  const studentAttendanceSummary = ref([]);
  const groupAverageScores = ref([]);
  const groupActiveStudents = ref([]);
  const groupAttendancePercentage = ref([]);
  const groupLatePercentage = ref([]);
  const groupAbsentPercentage = ref([]);
  const attendanceOverallStudentCommitment = ref([]);
  const attendanceCommitmentOverTime = ref([]);
  const attendanceCompareGroups = ref([]);
  const attendanceRankStudents = ref([]);
  const paymentPerStudent = ref([]);
  const paymentMonthlyRevenue = ref([]);
  const paymentOverdueStudentPayments = ref([]);
  const paymentPerGroups = ref([]);

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

  async function fetchStudentPerformancePerExam(params = {}) {
    try {
      const query = new URLSearchParams(params).toString();
      const url = `/dashboard/student-performance-per-exam${
        query ? `?${query}` : ""
      }`;
      const res = await api(url, { method: "GET" });

      // Assume `api` returns a standard fetch-like response
      const json = await res.json();

      if (json?.data) {
        studentPerformancePerExam.value = json.data;
      }
    } catch (error) {
      console.error("Error fetching student performance per exam data:", error);
    }
  }

  async function fetchStudentAttendanceSummary(params = {}) {
    try {
      const query = new URLSearchParams(params).toString();
      const url = `/dashboard/student-attendance-summary${
        query ? `?${query}` : ""
      }`;
      const res = await api(url, { method: "GET" });

      // Assume `api` returns a standard fetch-like response
      const json = await res.json();

      if (json?.data) {
        studentAttendanceSummary.value = json.data;
      }
    } catch (error) {
      console.error("Error fetching student attendance summary:", error);
    }
  }

  async function fetchGroupAverageScores(params = {}) {
    try {
      const query = new URLSearchParams(params).toString();
      const url = `/dashboard/group-average-scores${query ? `?${query}` : ""}`;
      const res = await api(url, { method: "GET" });

      // Assume `api` returns a standard fetch-like response
      const json = await res.json();

      if (json?.data) {
        groupAverageScores.value = json.data;
      }
    } catch (error) {
      console.error("Error fetching group average scores:", error);
    }
  }

  async function fetchGroupActiveStudents(params = {}) {
    try {
      const query = new URLSearchParams(params).toString();
      const url = `/dashboard/group-active-students${query ? `?${query}` : ""}`;
      const res = await api(url, { method: "GET" });

      // Assume `api` returns a standard fetch-like response
      const json = await res.json();

      if (json?.data) {
        groupActiveStudents.value = json.data;
      }
    } catch (error) {
      console.error("Error fetching group active students:", error);
    }
  }

  async function fetchGroupAttendancePercentage(params = {}) {
    try {
      const query = new URLSearchParams(params).toString();
      const url = `/dashboard/group-attendance-percentage${
        query ? `?${query}` : ""
      }`;
      const res = await api(url, { method: "GET" });

      // Assume `api` returns a standard fetch-like response
      const json = await res.json();

      if (json?.data) {
        groupAttendancePercentage.value = json.data;
      }
    } catch (error) {
      console.error("Error fetching group attendance percentage:", error);
    }
  }

  async function fetchGroupAbsentPercentage(params = {}) {
    try {
      const query = new URLSearchParams(params).toString();
      const url = `/dashboard/group-absent-percentage${
        query ? `?${query}` : ""
      }`;
      const res = await api(url, { method: "GET" });

      // Assume `api` returns a standard fetch-like response
      const json = await res.json();

      if (json?.data) {
        groupAbsentPercentage.value = json.data;
      }
    } catch (error) {
      console.error("Error fetching group absent percentage:", error);
    }
  }

  async function fetchGroupLatePercentage(params = {}) {
    try {
      const query = new URLSearchParams(params).toString();
      const url = `/dashboard/group-late-percentage${query ? `?${query}` : ""}`;
      const res = await api(url, { method: "GET" });

      // Assume `api` returns a standard fetch-like response
      const json = await res.json();

      if (json?.data) {
        groupLatePercentage.value = json.data;
      }
    } catch (error) {
      console.error("Error fetching group late percentage:", error);
    }
  }

  async function fetchAttendanceoverallStudentCommitment(params = {}) {
    try {
      const query = new URLSearchParams(params).toString();
      const url = `/dashboard/attendance-overall-student-commitment${
        query ? `?${query}` : ""
      }`;
      const res = await api(url, { method: "GET" });

      // Assume `api` returns a standard fetch-like response
      const json = await res.json();

      if (json?.data) {
        attendanceOverallStudentCommitment.value = json.data;
      }
    } catch (error) {
      console.error("Error fetching overall student commitment:", error);
    }
  }

  async function fetchAttendanceCommitmentOverTime(params = {}) {
    try {
      const query = new URLSearchParams(params).toString();
      const url = `/dashboard/attendance-commitment-over-time${
        query ? `?${query}` : ""
      }`;
      const res = await api(url, { method: "GET" });

      // Assume `api` returns a standard fetch-like response
      const json = await res.json();

      if (json?.data) {
        attendanceCommitmentOverTime.value = json.data;
      }
    } catch (error) {
      console.error("Error fetching attendance commitment over time:", error);
    }
  }

  async function fetchAttendanceCompareGroups(params = {}) {
    try {
      const query = new URLSearchParams(params).toString();
      const url = `/dashboard/attendance-compare-groups${
        query ? `?${query}` : ""
      }`;
      const res = await api(url, { method: "GET" });

      // Assume `api` returns a standard fetch-like response
      const json = await res.json();

      if (json?.data) {
        attendanceCompareGroups.value = json.data;
      }
    } catch (error) {
      console.error("Error fetching attendance compare groups:", error);
    }
  }

  async function fetchAttendanceRankStudents(params = {}) {
    try {
      const query = new URLSearchParams(params).toString();
      const url = `/dashboard/attendance-rank-students${
        query ? `?${query}` : ""
      }`;
      const res = await api(url, { method: "GET" });

      // Assume `api` returns a standard fetch-like response
      const json = await res.json();

      if (json?.data) {
        attendanceRankStudents.value = json.data;
      }
    } catch (error) {
      console.error("Error fetching attendance rank students:", error);
    }
  }

  async function fetchPaymentPerStudent(params = {}) {
    try {
      const query = new URLSearchParams(params).toString();
      const url = `/dashboard/payment-per-students${query ? `?${query}` : ""}`;
      const res = await api(url, { method: "GET" });

      // Assume `api` returns a standard fetch-like response
      const json = await res.json();

      if (json?.data) {
        paymentPerStudent.value = json.data;
      }
    } catch (error) {
      console.error("Error fetching payment per student data:", error);
    }
  }

  async function fetchPaymentMonthlyRevenue(params = {}) {
    try {
      const query = new URLSearchParams(params).toString();
      const url = `/dashboard/payment-monthly-revenue${query ? `?${query}` : ""}`;
      const res = await api(url, { method: "GET" });

      // Assume `api` returns a standard fetch-like response
      const json = await res.json();

      if (json?.data) {
        paymentMonthlyRevenue.value = json.data;
      }
    } catch (error) {
      console.error("Error fetching payment monthly revenue data:", error);
    }
  }

  async function fetchPaymentOverdueStudentPayments(params = {}) {
    try {
      const query = new URLSearchParams(params).toString();
      const url = `/dashboard/payment-overdue-student-payment${
        query ? `?${query}` : ""
      }`;
      const res = await api(url, { method: "GET" });

      // Assume `api` returns a standard fetch-like response
      const json = await res.json();

      if (json?.data) {
        paymentOverdueStudentPayments.value = json.data;
      }
    } catch (error) {
      console.error("Error fetching overdue student payments:", error);
    }
  }

  async function fetchPaymentPerGroup(params = {}) {
    try {
      const query = new URLSearchParams(params).toString();
      const url = `/dashboard/payment-per-groups${query ? `?${query}` : ""}`;
      const res = await api(url, { method: "GET" });

      // Assume `api` returns a standard fetch-like response
      const json = await res.json();

      if (json?.data) {
        paymentPerGroups.value = json.data;
      }
    } catch (error) {
      console.error("Error fetching payment per group data:", error);
    }
  }

  return {
    items,
    studentPerformancePerGroup,
    studentPerformanceOverTime,
    studentPerformancePerExam,
    studentAttendanceSummary,
    groupAverageScores,
    groupActiveStudents,
    groupAttendancePercentage,
    groupLatePercentage,
    groupAbsentPercentage,
    attendanceOverallStudentCommitment,
    attendanceCommitmentOverTime,
    attendanceCompareGroups,
    attendanceRankStudents,
    paymentPerStudent,
    paymentMonthlyRevenue,
    paymentOverdueStudentPayments,
    paymentPerGroups,
    fetchDashboardData,
    fetchStudentPerformancePerGroup,
    fetchStudentOverTimePerformance,
    fetchStudentPerformancePerExam,
    fetchStudentAttendanceSummary,
    fetchGroupAverageScores,
    fetchGroupActiveStudents,
    fetchGroupAttendancePercentage,
    fetchGroupAbsentPercentage,
    fetchGroupLatePercentage,
    fetchAttendanceoverallStudentCommitment,
    fetchAttendanceCommitmentOverTime,
    fetchAttendanceCompareGroups,
    fetchAttendanceRankStudents,
    fetchPaymentPerStudent,
    fetchPaymentMonthlyRevenue,
    fetchPaymentOverdueStudentPayments,
    fetchPaymentPerGroup,
  };
});
