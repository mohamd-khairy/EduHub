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

const attendanceOverallStudentCommitment = ref({ labels: [], datasets: [] });
const attendanceCommitmentOverTime = ref({ labels: [], datasets: [] });

async function getDashboardReports(params = {}) {
  await dashboardStore.fetchAttendanceoverallStudentCommitment(params);
  attendanceOverallStudentCommitment.value = dashboardStore.attendanceOverallStudentCommitment;

  await dashboardStore.fetchAttendanceCommitmentOverTime(params);
  attendanceCommitmentOverTime.value = dashboardStore.attendanceCommitmentOverTime;
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


const commitmentOverTime = {
  labels: ["يناير", "فبراير", "مارس", "أبريل"],
  datasets: [
    {
      label: "الحضور الفعلي",
      data: [40, 50, 45, 60],
      borderColor: "#36A2EB",
      fill: false,
      tension: 0.4,
    },
    {
      label: "الدروس المحجوزة",
      data: [50, 55, 50, 65],
      borderColor: "#FF6384",
      fill: false,
      tension: 0.4,
    },
  ],
};

const groupComparison = {
  labels: ["المجموعة أ", "المجموعة ب", "المجموعة ج"],
  datasets: [
    {
      label: "محجوز",
      data: [60, 45, 70],
      backgroundColor: "#36A2EB",
    },
    {
      label: "حضور فعلي",
      data: [55, 40, 65],
      backgroundColor: "#FF6384",
    },
  ],
};

const topBottomStudents = {
  labels: ["ريم", "أحمد", "خالد", "سارة", "فهد"],
  datasets: [
    {
      label: "نسبة الحضور",
      data: [95, 90, 80, 75, 60],
      backgroundColor: "#4BC0C0",
    },
  ],
};

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
      <UDashboardNavbar title="الصفحة الرئيسية" :ui="{ right: 'gap-3' }">
        <template #leading>
          <UDashboardSidebarCollapse />
        </template>
        <template #right v-if="authStore.hasPermission('read-notification')">
          <UTooltip text="Notifications" :shortcuts="['N']">
            <UButton color="neutral" variant="ghost" square @click="isNotificationsSlideoverOpen = true">
              <UChip color="error" inset>
                <UIcon name="i-lucide-bell" class="size-5 shrink-0" />
              </UChip>
            </UButton>
          </UTooltip>
        </template>
      </UDashboardNavbar>

      <UDashboardToolbar>
        <template #left>
          <HomeDateRangePicker :reset-signal="resetSignal" v-model="range" class="-ms-1" />
          <HomeStudentSelect v-model="student_id" :range="range" />
          <HomeGroupSelect v-model="group_id" :range="range" />
          <UButton v-if="hasFilter" icon="i-lucide-x" color="gray" size="sm" @click="resetFilters()"
            class="hover:bg-gray-200" />
        </template>
      </UDashboardToolbar>
    </template>

    <template #body>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full p-4">
        <div class="w-full">
          <h3 class="text-xl font-bold mb-2">1. تقرير الالتزام العام للطالب</h3>
          <p class="text-sm text-gray-600 mb-4">
            يوضح نسبة حضور كل طالب من إجمالي عدد الجلسات المحجوزة له، مما يساعد في تحديد الطلاب الذين يحتاجون
            إلى دعم إضافي.
          </p>
          <Bar :data="attendanceOverallStudentCommitment" :options="baseOptions" />
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
          <Bar :data="groupComparison" :options="baseOptions" />
        </div>

        <div class="w-full">
          <h3 class="text-xl font-bold mb-2">4. ترتيب الطلاب حسب الالتزام</h3>
          <p class="text-sm text-gray-600 mb-4">
            يعرض ترتيب الطلاب بناءً على نسبة الحضور، مما يساعد في تحديد الطلاب
            الأكثر التزامًا.
          </p>
          <Bar :data="topBottomStudents" :options="baseOptions" />
        </div>
      </div>
    </template>
  </UDashboardPanel>
</template>
