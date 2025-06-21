<script setup lang="ts">
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale,
  RadialLinearScale,
  ArcElement,
  Filler,
} from "chart.js";
import ChartDataLabels from "chartjs-plugin-datalabels";

import { Bar, Line, Pie, Doughnut, PolarArea } from "vue-chartjs";

// Register all chart elements + datalabels plugin
ChartJS.register(
  Title,
  Tooltip,
  Legend,
  BarElement,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale,
  RadialLinearScale,
  ArcElement,
  Filler,
  ChartDataLabels
);

const { isNotificationsSlideoverOpen } = useDashboard();
const authStore = useAuthStore();
const dashboardStore = useDashboardStore();
const group_id = ref(null);
const student_id = ref(null);
const range = shallowRef<Range>({
  start: null,
  end: null,
});
const studentPerformancePerGroup = ref({ labels: [], datasets: [] });
const studentPerformanceOverTime = ref({ labels: [], datasets: [] });

onMounted(async () => {
  await dashboardStore.fetchStudentPerformancePerGroup();
  studentPerformancePerGroup.value = dashboardStore.studentPerformancePerGroup;

  await dashboardStore.fetchStudentOverTimePerformance();
  studentPerformanceOverTime.value = dashboardStore.studentPerformanceOverTime;
});

watch(
  [range, group_id, student_id],
  async ([newRange, newGroupId, newStudentId]) => {
    await dashboardStore.fetchStudentPerformancePerGroup({
      start: newRange.start?.toISOString() || "",
      end: newRange.end?.toISOString() || "",
      group_id: newGroupId || "",
      student_id: newStudentId || "",
    });
    studentPerformancePerGroup.value =
      dashboardStore.studentPerformancePerGroup;

    await dashboardStore.fetchStudentOverTimePerformance({
      start: newRange.start?.toISOString() || "",
      end: newRange.end?.toISOString() || "",
      group_id: newGroupId || "",
      student_id: newStudentId || "",
    });
    studentPerformanceOverTime.value =
      dashboardStore.studentPerformanceOverTime;
  }
);

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

// Theme colors
function getCSSVariableColor(variable: string): string {
  if (typeof window === "undefined") return "";
  return getComputedStyle(document.documentElement)
    .getPropertyValue(variable)
    .trim();
}
const primary =
  getCSSVariableColor("--color-primary-500") || "rgba(59,130,246,0.7)";
const secondary =
  getCSSVariableColor("--color-secondary-500") || "rgba(234,88,12,0.7)";
const info = getCSSVariableColor("--color-info-500") || "rgba(6,182,212,0.7)";
const muted =
  getCSSVariableColor("--color-muted-500") || "rgba(148,163,184,0.7)";

const baseOptions = {
  responsive: true,
  plugins: {
    legend: {
      position: "bottom",
    },
    datalabels: {
      display: false,
    },
    customLabels: {
      display: false,
      color: "#000",
      font: {
        weight: "bold",
        size: 14,
      },
    },
  },
};

ChartJS.register({
  id: "customLabels",
  afterDatasetDraw(chart, args, options) {
    const meta = chart.getDatasetMeta(0);

    if (meta.type === "bar") return;

    const {
      ctx,
      chartArea: { width, height },
    } = chart;

    meta.data.forEach((arc, index) => {
      const { x, y } = arc.tooltipPosition();
      const label = chart.data.labels?.[index] || "";
      const value = chart.data.datasets[0].data[index];
      ctx.save();
      ctx.fillStyle = options.color || "#000";
      ctx.font = `${options.font?.weight || "bold"} ${
        options.font?.size || 14
      }px sans-serif`;
      ctx.textAlign = "center";
      ctx.textBaseline = "middle";
      ctx.fillText(`${label}: ${value}`, x, y);
      ctx.restore();
    });
  },
});

const students = ["أحمد", "سارة", "خالد", "ريم", "فهد"];
const studentAverages = [88, 72, 80, 90, 65];
const classAverage = Array(students.length).fill(75);

// const performanceOverTime = {
//   labels: ["الشهر 1", "الشهر 2", "الشهر 3", "الشهر 4"],
//   datasets: [
//     {
//       label: "أداء الطالب",
//       data: [70, 75, 78, 85],
//       borderColor: primary,
//       tension: 0.4,
//       fill: false,
//     },
//   ],
// };

const examLabels = ["اختبار 1", "اختبار 2", "اختبار 3", "اختبار 4", "اختبار 5"];
const examScores = [78, 82, 69, 88, 74];
const examPerformance = {
  labels: examLabels,
  datasets: [
    {
      label: "درجة الطالب",
      data: examScores,
      backgroundColor: ["#FF6384", "#36A2EB", "#FFCE56", "#4BC0C0", "#9966FF"],
    },
  ],
};

const attendanceChart = {
  labels: ["يناير", "فبراير", "مارس", "أبريل"],
  datasets: [
    {
      label: "حضور",
      data: [18, 15, 20, 17],
      backgroundColor: primary,
    },
    {
      label: "غياب",
      data: [2, 5, 0, 3],
      backgroundColor: secondary,
    },
  ],
};
</script>

<template>
  <UDashboardPanel id="home">
    <template #header>
      <UDashboardNavbar title="الصفحة الرئيسية" :ui="{ right: 'gap-3' }">
        <template #leading><UDashboardSidebarCollapse /></template>
        <template #right v-if="authStore.hasPermission('read-notification')">
          <UTooltip text="Notifications" :shortcuts="['N']">
            <UButton
              color="neutral"
              variant="ghost"
              square
              @click="isNotificationsSlideoverOpen = true"
            >
              <UChip color="error" inset
                ><UIcon name="i-lucide-bell" class="size-5 shrink-0"
              /></UChip>
            </UButton>
          </UTooltip>
        </template>
      </UDashboardNavbar>

      <UDashboardToolbar>
        <template #left>
          <HomeDateRangePicker
            :reset-signal="resetSignal"
            v-model="range"
            class="-ms-1"
          />
          <HomeStudentSelect v-model="student_id" :range="range" />
          <HomeGroupSelect v-model="group_id" :range="range" />
          <UButton
            v-if="hasFilter"
            icon="i-lucide-x"
            color="gray"
            size="sm"
            @click="resetFilters()"
            class="hover:bg-gray-200"
          />
        </template>
      </UDashboardToolbar>
    </template>

    <template #body>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full p-4">
        <div class="w-full" v-if="studentPerformancePerGroup">
          <h2 class="text-xl font-bold mb-2">أداء الطالب في كل وحدة دراسية</h2>
          <p class="text-sm text-gray-600 mb-4">
            يوضح درجات الطالب في كل وحدة على حدة، لتحديد نقاط القوة والضعف.
          </p>
          <Bar :data="studentPerformancePerGroup" :options="baseOptions" />
        </div>

        <div class="w-full">
          <h2 class="text-xl font-bold mb-2">أداء طالب بمرور الوقت</h2>
          <p class="text-sm text-gray-600 mb-4">
            يعكس هذا المخطط كيف تحسن أداء الطالب أو تراجع خلال عدة أشهر.
          </p>
          <Line :data="studentPerformanceOverTime" :options="baseOptions" />
        </div>

        <div class="w-full">
          <h2 class="text-xl font-bold mb-2">أداء الطالب في الحضور والغياب</h2>
          <p class="text-sm text-gray-600 mb-4">
            يعرض هذا الرسم عدد الأيام التي حضرها الطالب مقابل الأيام التي تغيب
            فيها لكل شهر، مما يساعد على تقييم التزامه.
          </p>
          <Bar :data="attendanceChart" :options="baseOptions" />
        </div>

        <div class="w-full">
          <h2 class="text-xl font-bold mb-2">أداء الطالب في الاختبارات</h2>
          <p class="text-sm text-gray-600 mb-4">
            يقدم هذا الرسم نظرة على نتائج الطالب في الاختبارات المختلفة خلال
            الفترة.
          </p>
          <Pie
            :data="examPerformance"
            :options="baseOptions"
            style="max-width: 400px; margin: auto"
          />
        </div>
      </div>
    </template>
  </UDashboardPanel>
</template>

<style scoped>
canvas {
  width: 100% !important;
  height: auto !important;
  min-width: 0;
}
</style>
