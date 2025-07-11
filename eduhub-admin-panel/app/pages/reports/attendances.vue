<script setup lang="ts">
import { Bar, Line } from "vue-chartjs";
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  LineElement,
  CategoryScale,
  LinearScale,
  PointElement,
} from "chart.js";
import DashboardHeader from "~/components/reports/DashboardHeader.vue";
definePageMeta({
  permission: "read-attendancereport",
});

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  BarElement,
  LineElement,
  CategoryScale,
  LinearScale,
  PointElement
);

const authStore = useAuthStore();
const dashboardStore = useDashboardStore();
const group_id = ref(null);
const student_id = ref(null);
const range = shallowRef({
  start: null,
  end: null,
});
const resetSignal = ref(false);
const hasFilter = computed(
  () =>
    range.value.start || range.value.end || group_id.value || student_id.value
);

function resetFilters() {
  resetSignal.value = !resetSignal.value;
  range.value.start = null;
  range.value.end = null;
  group_id.value = null;
  student_id.value = null;
}

const isLoading = ref(false);
const attendanceOverallStudentCommitment = ref({ labels: [], datasets: [] });
const attendanceCommitmentOverTime = ref({ labels: [], datasets: [] });
const attendanceCompareGroups = ref({ labels: [], datasets: [] });
const attendanceRankStudents = ref({ labels: [], datasets: [] });

async function getDashboardReports(params = {}) {
  isLoading.value = true; // ⏳ بداية التحميل

  try {
    await Promise.all([
      dashboardStore.fetchAttendanceoverallStudentCommitment(params),
      dashboardStore.fetchAttendanceCommitmentOverTime(params),
      dashboardStore.fetchAttendanceCompareGroups(params),
      dashboardStore.fetchAttendanceRankStudents(params),
    ]);

    attendanceOverallStudentCommitment.value =
      dashboardStore.attendanceOverallStudentCommitment;
    attendanceCommitmentOverTime.value =
      dashboardStore.attendanceCommitmentOverTime;
    attendanceCompareGroups.value = dashboardStore.attendanceCompareGroups;
    attendanceRankStudents.value = dashboardStore.attendanceRankStudents;
  } finally {
    isLoading.value = false; // ✅ التحميل انتهى في كل الحالات
  }
}

const hasPermission = ref(false);

onMounted(async () => {
  await getDashboardReports();

  hasPermission.value = authStore.hasPermission("read-attendancereport");
});

watch(
  [range, group_id, student_id],
  async ([newRange, newGroupId, newStudentId]) => {
    const params = {
      start: newRange.start?.toISOString() || "",
      end: newRange.end?.toISOString() || "",
      group_id: newGroupId || "",
      student_id: newStudentId || "",
    };
    await getDashboardReports(params);
  }
);

const colorMode = useColorMode();
const baseOptions = {
  responsive: true,
  plugins: {
    legend: {
      position: "bottom",
    },
    datalabels: {
      display: false,
      color: colorMode.value == "dark" ? "#fff" : "#000",
      font: {
        weight: "bold",
        size: 12,
      },
      align: "center", // ← لضبط النص داخل العنصر
      anchor: "center", // ← لضبط موقع النص نسبةً للعُنصر
    },
    customLabels: {
      display: false,
      color: colorMode.value == "dark" ? "#fff" : "#000",
      font: {
        weight: "bold",
        size: 10,
      },
    },
  },
};

ChartJS.register({
  id: "customLabels",
  afterDatasetDraw(chart, args, options) {
    const meta = chart.getDatasetMeta(0);

    // if (meta.type === "bar") return;
    // if (meta.type === "pie") return;

    const {
      ctx,
      chartArea: { width, height },
    } = chart;

    meta.data.forEach((arc, index) => {
      const { x, y } = arc.tooltipPosition();
      const label = chart?.data?.labels?.[index] || "";
      const value = chart?.data?.datasets[0]?.data[index];
      ctx.save();
      ctx.fillStyle = options.color || "#000";
      ctx.font = `${options.font?.weight || "bold"} ${
        options.font?.size || 14
      }px sans-serif`;
      ctx.textAlign = "center";
      ctx.textBaseline = "middle";
      // ctx.fillText(`${label}: ${value}`, x, y);
      ctx.fillText(`${value}`, x, y);
      ctx.restore();
    });
  },
});
</script>

<template>
  <UDashboardPanel id="home">
    <template #header>
      <DashboardHeader
        title="تقارير الحضور و الغياب"
        :reset-signal="resetSignal"
        :range="range"
        :group_id="group_id"
        :student_id="student_id"
        :is-loading="isLoading"
        :has-filter="hasFilter"
        :has-permission="hasPermission"
        @update:range="(val) => (range = val)"
        @update:group_id="(val) => (group_id = val)"
        @update:student_id="(val) => (student_id = val)"
        @reset="resetFilters"
      />
    </template>

    <template #body>
      <div
        v-if="isLoading"
        class="hidden lg:flex flex-col items-center justify-center flex-1 gap-4 text-center p-8"
      >
        <span
          class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-gray-900 dark:border-gray-100"
        ></span>
        <p class="text-gray-700 dark:text-gray-300 text-sm">
          جاري تحميل البيانات...
        </p>
      </div>
      <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full p-4">
        <div class="w-full">
          <h3 class="text-xl font-bold mb-2">1. تقرير الالتزام العام للطالب</h3>
          <p class="text-sm text-gray-600 mb-4">
            يوضح نسبة حضور كل طالب من إجمالي عدد الجلسات المحجوزة له، مما يساعد
            في تحديد الطلاب الذين يحتاجون إلى دعم إضافي.
          </p>
          <Bar
            :data="attendanceOverallStudentCommitment"
            :options="baseOptions"
          />
        </div>

        <div class="w-full">
          <h3 class="text-xl font-bold mb-2">2. تطور الالتزام بمرور الوقت</h3>
          <p class="text-sm text-gray-600 mb-4">
            يعرض الحضور الفعلي والدروس المحجوزة لكل طالب على مدار الأشهر، مما
            يساعد في تتبع التقدم.
          </p>
          <Line :data="attendanceCommitmentOverTime" :options="baseOptions" />
        </div>

        <div class="w-full">
          <h3 class="text-xl font-bold mb-2">3. مقارنة الحضور حسب المجموعة</h3>
          <p class="text-sm text-gray-600 mb-4">
            يقارن بين الحضور الفعلي والدروس المحجوزة لكل مجموعة، مما يساعد في
            تحديد المجموعات التي تحتاج إلى تحسين.
          </p>
          <Bar :data="attendanceCompareGroups" :options="baseOptions" />
        </div>

        <div class="w-full">
          <h3 class="text-xl font-bold mb-2">4. ترتيب الطلاب حسب الالتزام</h3>
          <p class="text-sm text-gray-600 mb-4">
            يعرض ترتيب الطلاب بناءً على نسبة الحضور، مما يساعد في تحديد الطلاب
            الأكثر التزامًا.
          </p>
          <Bar :data="attendanceRankStudents" :options="baseOptions" />
        </div>
      </div>
    </template>
  </UDashboardPanel>
</template>
