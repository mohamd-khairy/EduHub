<script setup>
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
  ArcElement,
  RadialLinearScale,
  PointElement,
  LineElement,
} from "chart.js";
import { Bar, Doughnut, Radar } from "vue-chartjs";

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
  ArcElement,
  RadialLinearScale,
  PointElement,
  LineElement
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

const averageScoreData = {
  labels: ["المجموعة أ", "المجموعة ب", "المجموعة ج"],
  datasets: [
    {
      label: "متوسط الدرجات",
      data: [82.5, 74.3, 88.1],
      backgroundColor: "#1E93C8",
    },
  ],
};

const attendanceRateData = {
  labels: ["المجموعة أ", "المجموعة ب", "المجموعة ج"],
  datasets: [
    {
      label: "نسبة الحضور (%)",
      data: [91, 76, 84],
      backgroundColor: ["#48BC7E", "#FF6384", "#36A2EB"],
    },
  ],
};

const mathComparisonData = {
  labels: ["المجموعة أ", "المجموعة ب", "المجموعة ج"],
  datasets: [
    {
      label: "الدرجة في الرياضيات",
      data: [85, 70, 90],
      backgroundColor: "rgba(54, 162, 235, 0.2)",
      borderColor: "rgba(54, 162, 235, 1)",
      borderWidth: 2,
    },
  ],
};

const activityData = {
  labels: ["المجموعة أ", "المجموعة ب", "المجموعة ج"],
  datasets: [
    {
      label: "نشط",
      data: [25, 18, 22],
      backgroundColor: "#4BC0C0",
    },
    {
      label: "غير نشط",
      data: [5, 7, 3],
      backgroundColor: "#FF9F40",
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
        <!-- تقرير المدفوعات حسب الطالب -->
        <div>
          <h2 class="text-xl font-bold mb-2">
            متوسط درجات الطلاب في كل مجموعة
          </h2>
          <Bar :data="averageScoreData" :options="baseOptions" />
        </div>
        <!-- 4. الطلاب النشطين وغير النشطين في كل مجموعة -->
        <div>
          <h2 class="text-xl font-bold mb-2">
            عدد الطلاب النشطين وغير النشطين في كل مجموعة
          </h2>
          <Bar :data="activityData" :options="baseOptions" />
        </div>

        <!-- 3. مقارنة أداء المجموعات في مادة الرياضيات -->
        <div>
          <h2 class="text-xl font-bold mb-2">
            مقارنة أداء المجموعات في مادة الرياضيات
          </h2>
          <Radar :data="mathComparisonData" :options="baseOptions" />
        </div>

        <!-- 2. نسبة الحضور لكل مجموعة -->
        <div>
          <h2 class="text-xl font-bold mb-2">نسبة الحضور لكل مجموعة</h2>
          <Doughnut
            :data="attendanceRateData"
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
}
</style>
