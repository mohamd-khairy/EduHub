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
import DashboardHeader from "~/components/reports/DashboardHeader.vue";

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
const studentPerformancePerGroup = ref({ labels: [], datasets: [] });
const studentPerformanceOverTime = ref({ labels: [], datasets: [] });
const studentPerformancePerExam = ref({ labels: [], datasets: [] });
const studentAttendanceSummary = ref({ labels: [], datasets: [] });

async function getDashboardReports(params = {}) {
  isLoading.value = true; // ⏳ بداية التحميل

  try {
    await Promise.all([
      dashboardStore.fetchStudentPerformancePerGroup(params),
      dashboardStore.fetchStudentOverTimePerformance(params),
      dashboardStore.fetchStudentAttendanceSummary(params),
      dashboardStore.fetchStudentPerformancePerExam(params),
    ]);
    studentPerformancePerGroup.value = dashboardStore.studentPerformancePerGroup;
    studentPerformanceOverTime.value = dashboardStore.studentPerformanceOverTime;
    studentAttendanceSummary.value = dashboardStore.studentAttendanceSummary;
    studentPerformancePerExam.value = dashboardStore.studentPerformancePerExam;
  } finally {
    isLoading.value = false; // ✅ التحميل انتهى في كل الحالات
  }
}

onMounted(async () => {
  await getDashboardReports();
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


const baseOptions = {
  responsive: true,
  plugins: {
    legend: {
      position: "bottom",
    },
    datalabels: {
      display: false,
      color: "#000",
      font: {
        weight: "bold",
        size: 12,
      },
      align: "center", // ← لضبط النص داخل العنصر
      anchor: "center", // ← لضبط موقع النص نسبةً للعُنصر
    },
    customLabels: {
      display: false,
      color: "#000",
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
      const label = chart.data.labels?.[index] || "";
      const value = chart.data.datasets[0].data[index];
      ctx.save();
      ctx.fillStyle = options.color || "#000";
      ctx.font = `${options.font?.weight || "bold"} ${options.font?.size || 14
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
      <DashboardHeader title="تقارير أداء الطلاب" :reset-signal="resetSignal" :range="range" :group_id="group_id"
        :student_id="student_id" :is-loading="isLoading" :has-filter="hasFilter" @update:range="val => range = val"
        @update:group_id="val => group_id = val" @update:student_id="val => student_id = val" @reset="resetFilters" />
    </template>

    <template #body>
      <div v-if="isLoading" class="hidden lg:flex flex-col items-center justify-center flex-1 gap-4 text-center p-8">
        <span
          class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-gray-900 dark:border-gray-100"></span>
        <p class="text-gray-700 dark:text-gray-300 text-sm">جاري تحميل البيانات...</p>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full p-4">
        <div class="w-full" v-if="studentPerformancePerGroup">
          <h2 class="text-xl font-bold mb-2">1. أداء الطالب في كل وحدة دراسية</h2>
          <p class="text-sm text-gray-600 mb-4">
            يوضح درجات الطالب في كل وحدة على حدة، لتحديد نقاط القوة والضعف.
          </p>
          <Bar :data="studentPerformancePerGroup" :options="baseOptions" />
        </div>

        <div class="w-full">
          <h2 class="text-xl font-bold mb-2">2. أداء طالب بمرور الوقت</h2>
          <p class="text-sm text-gray-600 mb-4">
            يعكس هذا المخطط كيف تحسن أداء الطالب أو تراجع خلال عدة أشهر.
          </p>
          <Line :data="studentPerformanceOverTime" :options="baseOptions" />
        </div>

        <div class="w-full">
          <h2 class="text-xl font-bold mb-2">3. أداء الطالب في الحضور والغياب</h2>
          <p class="text-sm text-gray-600 mb-4">
            يعرض هذا الرسم عدد الأيام التي حضرها الطالب مقابل الأيام التي تغيب
            فيها لكل شهر، مما يساعد على تقييم التزامه.
          </p>
          <Bar :data="studentAttendanceSummary" :options="baseOptions" />
        </div>

        <div class="w-full">
          <h2 class="text-xl font-bold mb-2">4. أداء الطالب في الاختبارات</h2>
          <p class="text-sm text-gray-600 mb-4">
            يقدم هذا الرسم نظرة على نتائج الطالب في الاختبارات المختلفة خلال
            الفترة.
          </p>
          <Pie :data="studentPerformancePerExam" :options="baseOptions" />
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
