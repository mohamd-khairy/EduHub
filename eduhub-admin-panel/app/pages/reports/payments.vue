<script setup lang="ts">
import { Bar, Line, Doughnut } from "vue-chartjs";
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
  ArcElement,
} from "chart.js";

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  BarElement,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale,
  ArcElement
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

const paymentsPerStudent = {
  labels: ["أحمد", "سارة", "خالد", "ريم"],
  datasets: [
    {
      label: "إجمالي المدفوعات",
      data: [5000, 4000, 2500, 6000],
      backgroundColor: "#36A2EB",
    },
  ],
};

const monthlyRevenue = {
  labels: ["يناير", "فبراير", "مارس", "أبريل"],
  datasets: [
    {
      label: "الإيرادات (ر.س)",
      data: [10000, 12000, 11000, 15000],
      borderColor: "#4BC0C0",
      fill: false,
      tension: 0.3,
    },
  ],
};

const overduePayments = {
  labels: ["أحمد", "خالد", "سارة"],
  datasets: [
    {
      label: "المتأخرات",
      data: [1000, 2000, 500],
      backgroundColor: "#FF6384",
    },
  ],
};

const paymentsByGroup = {
  labels: ["المجموعة أ", "المجموعة ب", "المجموعة ج"],
  datasets: [
    {
      label: "مدفوعات المجموعة",
      data: [10000, 8000, 6000],
      backgroundColor: ["#FFCE56", "#36A2EB", "#9966FF"],
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
        <div class="w-full">
          <h2 class="text-xl font-bold mb-2">تقرير المدفوعات حسب الطالب</h2>
          <Bar :data="paymentsPerStudent" :options="baseOptions" />
        </div>

        <!-- تقرير الإيرادات الشهرية -->
        <div class="w-full">
          <h2 class="text-xl font-bold mb-2">تقرير الإيرادات الشهرية</h2>
          <Line :data="monthlyRevenue" :options="baseOptions" />
        </div>

        <!-- تقرير الدفعات المتأخرة -->
        <div class="w-full">
          <h2 class="text-xl font-bold mb-2">تقرير الدفعات المتأخرة</h2>
          <Bar :data="overduePayments" :options="baseOptions" />
        </div>

        <!-- تقرير المدفوعات حسب المجموعة أو المستوى -->
        <div class="w-full">
          <h2 class="text-xl font-bold mb-2">
            تقرير المدفوعات حسب المجموعة أو المستوى
          </h2>
          <Doughnut
            :data="paymentsByGroup"
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
