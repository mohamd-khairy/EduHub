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

const isLoading = ref(false);
const paymentPerStudent = ref({ labels: [], datasets: [] });
const paymentMonthlyRevenue = ref({ labels: [], datasets: [] });
const paymentOverdueStudentPayments = ref({ labels: [], datasets: [] });
const paymentPerGroups = ref({ labels: [], datasets: [] });

async function getDashboardReports(params = {}) {
  isLoading.value = true; // ⏳ بداية التحميل

  try {
    await Promise.all([
      dashboardStore.fetchPaymentPerStudent(params),
      dashboardStore.fetchPaymentMonthlyRevenue(params),
      dashboardStore.fetchPaymentOverdueStudentPayments(params),
      dashboardStore.fetchPaymentPerGroup(params),
    ]);

    paymentPerStudent.value = dashboardStore.paymentPerStudent;
    paymentMonthlyRevenue.value = dashboardStore.paymentMonthlyRevenue;
    paymentOverdueStudentPayments.value = dashboardStore.paymentOverdueStudentPayments;
    paymentPerGroups.value = dashboardStore.paymentPerGroups;
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
          <HomeDateRangePicker :reset-signal="resetSignal" v-model="range" class="-ms-1" :disabled="isLoading" />
          <HomeStudentSelect v-model="student_id" :range="range" :disabled="isLoading" />
          <HomeGroupSelect v-model="group_id" :range="range" :disabled="isLoading" />
          <UButton v-if="hasFilter" icon="i-lucide-x" color="gray" size="sm" @click="resetFilters()"
            class="hover:bg-gray-200" />
        </template>
      </UDashboardToolbar>
    </template>

    <template #body>
      <div v-if="isLoading" class="hidden lg:flex flex-col items-center justify-center flex-1 gap-4 text-center p-8">
        <span
          class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-gray-900 dark:border-gray-100"></span>
        <p class="text-gray-700 dark:text-gray-300 text-sm">جاري تحميل البيانات...</p>
      </div>
      <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full p-4">
        <!-- 1. تقرير المدفوعات حسب الطالب -->
        <div class="w-full">
          <h2 class="text-xl font-bold mb-2">تقرير المدفوعات حسب الطالب</h2>
          <p class="text-sm text-muted mb-4">
            يوضح إجمالي المدفوعات التي قام بها كل طالب خلال الفترة المحددة. يساعد على تتبع الالتزام المالي لكل طالب.
          </p>
          <Bar :data="paymentPerStudent" :options="baseOptions" />
        </div>

        <!-- 2. تقرير الإيرادات الشهرية -->
        <div class="w-full">
          <h2 class="text-xl font-bold mb-2">تقرير الإيرادات الشهرية</h2>
          <p class="text-sm text-muted mb-4">
            يعرض تطور الإيرادات شهريًا، مما يساعد في تقييم الأداء المالي وتحديد أشهر النشاط الأعلى أو الأدنى.
          </p>
          <Line :data="paymentMonthlyRevenue" :options="baseOptions" />
        </div>

        <!-- 3. تقرير الدفعات المتأخرة -->
        <div class="w-full">
          <h2 class="text-xl font-bold mb-2">تقرير الدفعات المتأخرة</h2>
          <p class="text-sm text-muted mb-4">
            يعرض الطلاب الذين لديهم دفعات مستحقة لم تُسدَّد بعد، مما يساعد على تحسين التحصيل المالي.
          </p>
          <Bar :data="paymentOverdueStudentPayments" :options="baseOptions" />
        </div>

        <!-- 4. تقرير المدفوعات حسب المجموعة أو المستوى -->
        <div class="w-full">
          <h2 class="text-xl font-bold mb-2">تقرير المدفوعات حسب المجموعة أو المستوى</h2>
          <p class="text-sm text-muted mb-4">
            يوضح توزيع إجمالي المدفوعات على المجموعات الدراسية أو المستويات المختلفة، مما يساعد في تحليل أداء كل مجموعة
            ماليًا.
          </p>
          <Doughnut :data="paymentPerGroups" :options="baseOptions" style="max-width: 400px; margin: auto" />
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
