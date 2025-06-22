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

const groupAverageScores = ref({ labels: [], datasets: [] });
const groupActiveStudents = ref({ labels: [], datasets: [] });
const groupAttendancePercentage = ref({ labels: [], datasets: [] });
const groupAbsentPercentage = ref({ labels: [], datasets: [] });
const groupLatePercentage = ref({ labels: [], datasets: [] });

async function getDashboardReports(params = {}) {
  await dashboardStore.fetchGroupAverageScores(params);
  groupAverageScores.value = dashboardStore.groupAverageScores;

  await dashboardStore.fetchGroupActiveStudents(params);
  groupActiveStudents.value = dashboardStore.groupActiveStudents;

  await dashboardStore.fetchGroupAttendancePercentage(params);
  groupAttendancePercentage.value = dashboardStore.groupAttendancePercentage;

  await dashboardStore.fetchGroupAbsentPercentage(params);
  groupAbsentPercentage.value = dashboardStore.groupAbsentPercentage;

  await dashboardStore.fetchGroupLatePercentage(params);
  groupLatePercentage.value = dashboardStore.groupLatePercentage;
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
        <!-- تقرير المدفوعات حسب الطالب -->
        <div>
          <h2 class="text-xl font-bold mb-2">
            1. متوسط درجات الطلاب في كل مجموعة
          </h2>
          <!-- Description: This chart shows the average scores of students in each group -->
          <p class="text-sm text-gray-600 mb-2">
            يعرض هذا الرسم البياني متوسط درجات الطلاب في كل مجموعة. يمكنك من معرفة مدى أداء الطلاب في مجموعاتهم
            المختلفة.
          </p>
          <Bar :data="groupAverageScores" :options="baseOptions" />
        </div>

        <!-- 4. الطلاب النشطين وغير النشطين في كل مجموعة -->
        <div>
          <h2 class="text-xl font-bold mb-2">
            2. عدد الطلاب النشطين وغير النشطين في كل مجموعة
          </h2>
          <!-- Description: This chart displays the number of active and inactive students in each group -->
          <p class="text-sm text-gray-600 mb-2">
            يعرض هذا الرسم البياني عدد الطلاب النشطين وغير النشطين في كل مجموعة، مما يساعد في تقييم تفاعل الطلاب مع
            المواد الدراسية.
          </p>
          <Bar :data="groupActiveStudents" :options="baseOptions" />
        </div>


        <!-- 2. نسبة الحضور لكل مجموعة -->
        <div>
          <h2 class="text-xl font-bold mb-2">
            4. نسبة الحضور لكل مجموعة
          </h2>
          <!-- Description: This doughnut chart shows the attendance rate for each group -->
          <p class="text-sm text-gray-600 mb-2">
            يعرض هذا الرسم البياني نسبة الحضور لكل مجموعة، مما يساعد في مراقبة التزام الطلاب بالحضور.
          </p>
          <Doughnut :data="groupAttendancePercentage" :options="baseOptions" style="max-width: 400px; margin: auto" />
        </div>

        <!-- 2. نسبة الحضور لكل مجموعة -->
        <div>
          <h2 class="text-xl font-bold mb-2">
            4. نسبة الغياب لكل مجموعة
          </h2>
          <!-- Description: This doughnut chart shows the attendance rate for each group -->
          <p class="text-sm text-gray-600 mb-2">
            يعرض هذا الرسم البياني نسبة الغياب لكل مجموعة، مما يساعد في مراقبة التزام الطلاب بالغياب.
          </p>
          <Doughnut :data="groupAbsentPercentage" :options="baseOptions" style="max-width: 400px; margin: auto" />
        </div>


        <!-- 2. نسبة الحضور لكل مجموعة -->
        <div>
          <h2 class="text-xl font-bold mb-2">
            4. نسبة التأخير لكل مجموعة
          </h2>
          <!-- Description: This doughnut chart shows the attendance rate for each group -->
          <p class="text-sm text-gray-600 mb-2">
            يعرض هذا الرسم البياني نسبة التأخير لكل مجموعة، مما يساعد في مراقبة التزام الطلاب بالتأخير.
          </p>
          <Doughnut :data="groupLatePercentage" :options="baseOptions" style="max-width: 400px; margin: auto" />
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
